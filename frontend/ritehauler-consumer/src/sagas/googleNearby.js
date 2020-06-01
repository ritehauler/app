import { put, call, take, fork, takeLatest } from "redux-saga/effects";

import { success as recentLocationSuccess } from "../actions/RecentLocationActions";
import {
  success,
  failure,
  googlePlacesSuccess,
  googlePlacesFailure
  // googlePlaceDetailSuccess,
  // googlePlaceDetailFailure
} from "../actions/GoogleNearby";
import {
  GOOGLE_NEARBY,
  GOOGLE_PLACES,
  GOOGLE_PLACE_DETAIL
} from "../actions/ActionTypes";

import GoogleApiSauce from "../util/GoogleApiSauce";
import ApiSauce from "../util/ApiSauce";
import {
  NEARBY_GOOGLE_API,
  PLACES_GOOGLE_API,
  PLACE_DETAIL_GOOGLE_API,
  API_RECENT_LOCATION_CITY
} from "../config/WebService";

import Util from "../util";

function callRequest(url, data) {
  return GoogleApiSauce.get(url, data);
}

function callRequestRecent(data) {
  return ApiSauce.get(API_RECENT_LOCATION_CITY, data);
}

function* watchNearByRequestRequest() {
  while (true) {
    const { payload } = yield take(GOOGLE_NEARBY.REQUEST);
    try {
      const response = yield call(callRequest, NEARBY_GOOGLE_API, payload);

      /*
      if (payload.city_id !== -1) {
        const payLoadRecentLocation = {
          city_id: payload.city_id,
          customer_id: payload.customer_id
        };
        yield put(recentLocationRequest(payLoadRecentLocation));
      }
      */
      if (payload.city_id !== -1) {
        const payLoadRecentLocation = {
          city_id: payload.city_id,
          customer_id: payload.customer_id
        };
        const responseRecent = yield call(
          callRequestRecent,
          payLoadRecentLocation
        );
        yield put(recentLocationSuccess(responseRecent.data.location || []));
      }
      yield put(success(response.results));
    } catch (err) {
      yield put(failure(err.message));
      //Util.alert(err.message);
    }
  }
}

function* watchPlacesRequest(action) {
  const { payload } = action;
  try {
    const response = yield call(callRequest, PLACES_GOOGLE_API, payload);
    yield put(googlePlacesSuccess(response.predictions));
  } catch (err) {
    yield put(googlePlacesFailure(err.message));
    //Util.alert(err.message);
  }
}

function* watchPlaceDetailRequest() {
  while (true) {
    const { payload, callback } = yield take(GOOGLE_PLACE_DETAIL);
    try {
      const response = yield call(
        callRequest,
        PLACE_DETAIL_GOOGLE_API,
        payload
      );
      callback(response.result);
      //yield put(googlePlaceDetailSuccess(response.predictions));
    } catch (err) {
      //yield put(googlePlaceDetailFailure(err));
      Util.alert(err.message);
    }
  }
}

export default function* root() {
  yield fork(watchNearByRequestRequest);
  yield takeLatest(GOOGLE_PLACES.REQUEST, watchPlacesRequest);
  yield fork(watchPlaceDetailRequest);
}

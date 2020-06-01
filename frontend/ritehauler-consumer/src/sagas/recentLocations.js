import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";

import { success, failure } from "../actions/RecentLocationActions";
import { RECENT_LOCATIONS } from "../actions/ActionTypes";
import { API_RECENT_LOCATION_CITY } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_RECENT_LOCATION_CITY, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(RECENT_LOCATIONS.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(success(response.data.location || []));
    } catch (err) {
      yield put(failure(err));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

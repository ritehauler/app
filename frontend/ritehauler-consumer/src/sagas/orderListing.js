import { put, call, takeLatest } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";

import { success, failure } from "../actions/OrderListingActions";
import { request as generalSettingsRequest } from "../actions/GeneralSettingsActions";
import { ORDER_LISTING } from "../actions/ActionTypes";
import { API_ORDER_LISTING } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_ORDER_LISTING, data);
}

function* watchRequest(action) {
  const { payload, reset } = action;
  try {
    // update general settings
    yield put(generalSettingsRequest());

    const response = yield call(callRequest, payload);
    yield put(
      success(
        response.data.order || [],
        response.data.page || {},
        response.message,
        reset
      )
    );
  } catch (err) {
    yield put(failure(err.message));
  }
}

export default function* root() {
  yield takeLatest(ORDER_LISTING.REQUEST, watchRequest);
}

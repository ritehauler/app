import { put, call, take, fork, takeEvery } from "redux-saga/effects";

import ApiSauce from "../services/ApiSauce";
import { success, failure } from "../actions/VerifyVolumeActions";
import { ORDER_DRIVER_TRACKING } from "../actions/ActionTypes";
import { API_ORDER_DRIVER_TRACKING } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.post(API_ORDER_DRIVER_TRACKING, data);
}

function* watchTrackingRequest(action) {
  try {
    const response = yield call(callRequest, action.payload);
    //yield put(success(response));
  } catch (err) {
    //yield put(failure(err.message));
  }
}

export default function* root() {
  yield takeEvery(ORDER_DRIVER_TRACKING.REQUEST, watchTrackingRequest);
}

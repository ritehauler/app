import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";

import { success, failure } from "../actions/DriverProfileActions";
import { DRIVER_PROFILE } from "../actions/ActionTypes";
import { API_DRIVER_PROFILE } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_DRIVER_PROFILE, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(DRIVER_PROFILE.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(success(response.data.driver || {}, response.message));
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

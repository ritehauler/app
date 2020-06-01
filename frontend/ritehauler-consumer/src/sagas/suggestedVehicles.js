import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";

import { success, failure } from "../actions/SuggestedVehiclesActions";
import { SUGGESTED_VEHICLES } from "../actions/ActionTypes";
import { API_SUGGESTED_VEHICLES } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_SUGGESTED_VEHICLES, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(SUGGESTED_VEHICLES.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(
        success(
          response.data.truck || [],
          payload,
          response.data.truck_suggested_id || "",
          response.message
        )
      );
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

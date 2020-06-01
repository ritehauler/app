import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";

import { success, failure } from "../actions/DeliveryProfessionalsActions";
import { DELIVERY_PROFESSIONALS } from "../actions/ActionTypes";
import { API_DELIVERY_PROFESSIONALS } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_DELIVERY_PROFESSIONALS, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(DELIVERY_PROFESSIONALS.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(
        success(response.data.delivery_professional || [], response.message)
      );
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

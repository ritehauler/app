import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../services/ApiSauce";

import { success, failure } from "../actions/PendingOrdersActions";
import { PENDING_ORDERS } from "../actions/ActionTypes";
import { API_PENDING_ORDERS } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_PENDING_ORDERS, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(PENDING_ORDERS.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(success(response.data));
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

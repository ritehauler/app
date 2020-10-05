import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../services/ApiSauce";

import { success, failure } from "../actions/MyOrders";
import { MY_ORDERS } from "../actions/ActionTypes";
import { API_ORDER_HISTORY } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_ORDER_HISTORY, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(MY_ORDERS.REQUEST);
    let newPayload = {};
    for (const key in payload) {
      if (payload[key]) {
        newPayload[key] = payload[key];
      }
    }

    try {
      const response = yield call(callRequest, newPayload);
      yield put(
        success(
          response.data.order || [],
          response.data.page.next_offset,
          payload.isConcat
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

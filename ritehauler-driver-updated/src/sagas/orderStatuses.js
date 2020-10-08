import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../services/ApiSauce";
import { success, failure } from "../actions/OrderStatusesActions";
import { ORDER_STATUSES, ORDER_DETAILS } from "../actions/ActionTypes";
import { API_ORDER_STATUSES } from "../config/WebService";
function callRequest(data) {
  return ApiSauce.get(API_ORDER_STATUSES, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(ORDER_STATUSES.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(success(response.data || []));
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

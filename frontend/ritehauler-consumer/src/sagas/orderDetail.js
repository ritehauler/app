import { put, call, takeLatest } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";

import { success, failure } from "../actions/OrderDetailActions";
import { ORDER_DETAIL } from "../actions/ActionTypes";
import { API_ORDER_DETAIL } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_ORDER_DETAIL, data);
}

function* watchRequest(action) {
  const { payload } = action;
  try {
    const response = yield call(callRequest, payload);
    yield put(success(response.data.order, response.message));
  } catch (err) {
    yield put(failure(err.message));
  }
}

export default function* root() {
  yield takeLatest(ORDER_DETAIL.REQUEST, watchRequest);
}

import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../services/ApiSauce";

import { success, failure } from "../actions/TodaysOrdersActions";
import { TODAYS_ORDERS } from "../actions/ActionTypes";
import { API_TODAYS_ORDERS } from "../config/WebService";
import { focusMap } from "../actions/HandleMapActions";
function callRequest(data) {
  return ApiSauce.get(API_TODAYS_ORDERS, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(TODAYS_ORDERS.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(success(response.data.order || []));
      yield put(focusMap(true));
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

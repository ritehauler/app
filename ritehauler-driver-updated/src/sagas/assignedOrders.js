import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../services/ApiSauce";
import { Actions } from "react-native-router-flux";
import { success, failure } from "../actions/AssignedOrders";
import { ASSIGNED_ORDERS } from "../actions/ActionTypes";
import { API_ASSIGNED_ORDERS } from "../config/WebService";
import { focusMap } from "../actions/HandleMapActions";
import Utils from "../util";

function callRequest(data) {
  return ApiSauce.get(API_ASSIGNED_ORDERS, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(ASSIGNED_ORDERS.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(success(response.data.order || []));
      if (response.data.order.length) {
        Actions.OrderAssignment();
      } else {
        Utils.showMessage("No new order assigned to you", false);
      }
    } catch (err) {
      Utils.showMessage("Failed to fetch assigned orders.");
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

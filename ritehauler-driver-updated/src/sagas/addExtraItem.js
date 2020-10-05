import { put, call, take, fork, select } from "redux-saga/effects";

import ApiSauce from "../services/ApiSauce";
import { Actions } from "react-native-router-flux";
import { getOnTheWayPayload } from "../reducers/selectors";
import { success, failure } from "../actions/AddExtraItemsAction";
import { ADD_EXTRA_ITEM } from "../actions/ActionTypes";
import { API_ADD_EXTRA_ITEM } from "../config/WebService";
import { resetOrderInfo } from "../actions/OrderActions";
import { focusMap } from "../actions/HandleMapActions";

import { request as updateAssignedOrderStatusRequest } from "../actions/UpdateAssignedOrderStatus";

function callRequest(data) {
  return ApiSauce.post(API_ADD_EXTRA_ITEM, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(ADD_EXTRA_ITEM.REQUEST);
    const delay = ms => new Promise(res => setTimeout(res, ms));
    try {
      const response = yield call(callRequest, payload);
      const onTheWayPayload = yield select(getOnTheWayPayload);
      yield put(updateAssignedOrderStatusRequest(onTheWayPayload));
      yield put(focusMap(true));

      yield delay(10000);
      yield put(success(response));
      // deletes the data of local order items reducer
      yield put(resetOrderInfo());
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

import { put, call, take, fork } from "redux-saga/effects";
import { Actions } from "react-native-router-flux";

import ApiSauce from "../util/ApiSauce";
import Util from "../util";

import { success, failure } from "../actions/OrderCompleteActions";
import { setNotificationInfo } from "../actions/NotificationListingActions";
import { ORDER_COMPLETE } from "../actions/ActionTypes";
import { API_ORDER_COMPLETE } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.post(API_ORDER_COMPLETE, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(ORDER_COMPLETE.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(success(response.data || {}, response.message));

      yield put(
        setNotificationInfo(
          payload.order_id,
          payload.order_number,
          new Date().getTime()
        )
      );
      Actions.rateDriver({ orderId: payload.order_id });
    } catch (err) {
      yield put(failure(err.message));
      Util.alert(err.message);
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

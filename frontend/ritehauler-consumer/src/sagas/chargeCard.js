import { put, call, take, fork } from "redux-saga/effects";
import { Actions } from "react-native-router-flux";

import ApiSauce from "../util/ApiSauce";
import { Strings } from "../theme";
import Util from "../util";

import { success, failure } from "../actions/ChargeCardActions";
import { setNotificationInfo } from "../actions/NotificationListingActions";
import { CHARGE_CARD } from "../actions/ActionTypes";
import {
  API_CHARGE_CARD,
  ORDER_STATUS_DRIVER_CANCELLED
} from "../config/WebService";

function callRequest(data) {
  return ApiSauce.post(API_CHARGE_CARD, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(CHARGE_CARD.REQUEST);
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
      if (payload.orderStatus === ORDER_STATUS_DRIVER_CANCELLED) {
        Actions.popTo("consumerLocation");
        Util.alert(Strings.paymentSuccessFullMessage, "success");
      } else {
        Actions.rateDriver({ orderId: payload.order_id });
      }
    } catch (err) {
      yield put(failure(err.message));
      Util.alert(err.message);
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

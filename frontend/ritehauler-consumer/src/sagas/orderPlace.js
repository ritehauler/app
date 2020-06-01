import { put, call, take, fork } from "redux-saga/effects";
import { Actions } from "react-native-router-flux";

import ApiSauce from "../util/ApiSauce";
import Util from "../util";
import DataHandler from "../util/DataHandler";
import { Strings } from "../theme";

import { success, failure } from "../actions/OrderPlaceActions";
import { resetOrderInfo } from "../actions/OrderActions";
import { ORDER_PLACE } from "../actions/ActionTypes";
import { API_ORDER_PLACE } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.post(API_ORDER_PLACE, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(ORDER_PLACE.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(success(response.data.order || [], response.message));
      yield put(resetOrderInfo());
      Actions.popTo("consumerLocation");
      DataHandler.callBackOrderPlace();
      Util.alert(Strings.orderPlaceMessage, "success");
    } catch (err) {
      yield put(failure(err.message));
      Util.alert(err.message);
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

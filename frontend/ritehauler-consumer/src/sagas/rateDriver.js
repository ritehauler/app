import { put, call, take, fork } from "redux-saga/effects";
import { Actions } from "react-native-router-flux";

import ApiSauce from "../util/ApiSauce";
import Util from "../util";
import { Strings } from "../theme";

import { success, failure } from "../actions/RateDriverActions";
import { RATE_DRIVER } from "../actions/ActionTypes";
import { API_RATE_DRIVER } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.post(API_RATE_DRIVER, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(RATE_DRIVER.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(success(response.data.rate || {}, response.message));
      Actions.popTo("consumerLocation");
      Util.alert(Strings.driverRateMessage, "success");
    } catch (err) {
      yield put(failure(err.message));
      Util.alert(err.message);
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

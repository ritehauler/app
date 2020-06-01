import { put, call, takeLatest } from "redux-saga/effects";
import { Actions } from "react-native-router-flux";

import ApiSauce from "../util/ApiSauce";
import Util from "../util";

import { success, failure } from "../actions/LogoutActions";
import { removeAllNotifications } from "../util/NotificationListener";
import { LOGOUT } from "../actions/ActionTypes";
import { API_LOGOUT } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.post(API_LOGOUT, data);
}

function* watchRequest(action) {
  const { payload, message } = action;
  try {
    const response = yield call(callRequest, payload);
    Actions.login();
    yield put(success(response.message));
    removeAllNotifications();
    if (message !== "") {
      Util.alert(message);
    }
  } catch (err) {
    yield put(failure(err.message));
    Util.alert(err.message);
  }
}

export default function* root() {
  yield takeLatest(LOGOUT.REQUEST, watchRequest);
}

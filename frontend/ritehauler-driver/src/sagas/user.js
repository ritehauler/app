import { put, call, take, fork, all } from "redux-saga/effects";
import { Actions } from "react-native-router-flux";
import { API_ENTITY_AUTH_LOGOUT } from "../config/WebService";
import ApiSauce from "../services/ApiSauce";
import Utils from "../util";
import { success, failure, logoutSuccess } from "../actions/UserActions";
import { USER, LOGOUT } from "../actions/ActionTypes";
import {
  API_ENTITY_AUTH_EMAIL_LOGIN,
  API_ENTITY_AUTH_FORGOT_PASS,
  API_ENTITY_AUTH_CHANGE_PASS
} from "../config/WebService";
import { getToken } from "../services/NotificationListener";

function callRequest(url, data) {
  return ApiSauce.post(url, data);
}

function* watchUserRequest() {
  while (true) {
    const { url, payload, callback } = yield take(USER.REQUEST);
    try {
      const token = yield getToken();
      payload.device_token = token;
      const response = yield call(callRequest, url, payload);
      if (response && response.data && response.data.driver) {
        if (url === API_ENTITY_AUTH_EMAIL_LOGIN) {
          yield put(success(response.data.driver));
          setTimeout(() => Actions.home({ type: "reset" }), 500);
        } else if (url === API_ENTITY_AUTH_FORGOT_PASS) {
          yield put(success(response.data.driver));
          Utils.showMessage("Check your email for instructions.", "success");
          setTimeout(() => Actions.pop(), 500);
        } else if (url === API_ENTITY_AUTH_CHANGE_PASS) {
          yield put(success(response.data.driver));
          setTimeout(() => Actions.pop(), 500);
        }
      }
    } catch (err) {
      yield put(failure(err.message));
      if (err && err.message) Utils.showMessage(err.message, "error");
    }
  }
}

function* watchLogoutRequest() {
  while (true) {
    const { payload } = yield take(LOGOUT.REQUEST);
    try {
      const response = yield call(callRequest, API_ENTITY_AUTH_LOGOUT, payload);
      Actions.login({ type: "reset" });
      yield put(logoutSuccess());
    } catch (err) {
      if (err && err.message) Utils.showMessage(err.message, "error");
    }
  }
}

export default function* root() {
  yield all([fork(watchUserRequest), fork(watchLogoutRequest)]);
}

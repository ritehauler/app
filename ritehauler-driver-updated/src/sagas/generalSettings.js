import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../services/ApiSauce";
import { success, failure } from "../actions/GeneralSettingsActions";
import { GENERAL_SETTINGS } from "../actions/ActionTypes";
import { API_GENERAL_SETTINGS } from "../config/WebService";
function callRequest(data) {
  return ApiSauce.get(API_GENERAL_SETTINGS, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(GENERAL_SETTINGS.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      if (response.data && response.data.setting) {
        yield put(success(response.data.setting));
      }
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

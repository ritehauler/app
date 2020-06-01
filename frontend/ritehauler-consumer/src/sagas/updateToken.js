import { put, call, takeLatest } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";

import { success, failure } from "../actions/UpdateTokenActions";
import { UPDATE_TOKEN } from "../actions/ActionTypes";
import { API_UPDATE_TOKEN } from "../config/WebService";
import { getToken } from "../util/NotificationListener";

function callRequest(data) {
  return ApiSauce.post(API_UPDATE_TOKEN, data);
}

function* watchRequest(action) {
  const { payload } = action;
  try {
    // get token and set payload for device token
    const token = yield getToken();
    if (token !== "") {
      payload.device_token = token;
      const response = yield call(callRequest, payload);
      yield put(success(response.data.customer));
    } else {
      yield put(failure("failure"));
    }
  } catch (err) {
    yield put(failure(err.message));
  }
}

export default function* root() {
  yield takeLatest(UPDATE_TOKEN.REQUEST, watchRequest);
}

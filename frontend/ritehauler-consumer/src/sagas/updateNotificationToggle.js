import { put, call, takeLatest } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";

import { success } from "../actions/UpdateNotificationToggleActions";
import { UPDATE_NOTIFICATION_TOGGLE } from "../actions/ActionTypes";
import { API_UPDATE_NOTIFICATION_TOGGLE } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.post(API_UPDATE_NOTIFICATION_TOGGLE, data);
}

function* watchRequest(action) {
  const { payload } = action;
  try {
    const response = yield call(callRequest, payload);
    yield put(success(response.data.customer));
  } catch (err) {}
}

export default function* root() {
  yield takeLatest(UPDATE_NOTIFICATION_TOGGLE.REQUEST, watchRequest);
}

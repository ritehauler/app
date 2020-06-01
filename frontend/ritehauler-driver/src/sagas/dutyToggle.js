import { put, call, take, fork, select, takeLatest } from "redux-saga/effects";

import ApiSauce from "../services/ApiSauce";
import { success } from "../actions/UserActions";
import { DUTY_TOGGLE } from "../actions/ActionTypes";
import { API_DUTY_TOGGLE } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.post(API_DUTY_TOGGLE, data);
}

function* watchUserRequest(action) {
  const { payload } = action;
  try {
    const response = yield call(callRequest, payload);
    if (response.data.driver) {
      yield put(success(response.data.driver));
    }
  } catch (err) {}
}

export default function* root() {
  yield takeLatest(DUTY_TOGGLE, watchUserRequest);
}

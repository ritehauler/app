import { put, call, takeLatest } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";

import { success, failure } from "../actions/TrackOrderActions";
import { TRACK_ORDER } from "../actions/ActionTypes";
import { API_TRACK_ORDER } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_TRACK_ORDER, data);
}

function* watchRequest(action) {
  const { payload } = action;
  try {
    const response = yield call(callRequest, payload);
    yield put(success(response.data.tracking || {}, response.message));
  } catch (err) {
    yield put(failure(err.message));
  }
}

export default function* root() {
  yield takeLatest(TRACK_ORDER.REQUEST, watchRequest);
}

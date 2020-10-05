import { put, call, takeLatest } from "redux-saga/effects";

import ApiSauce from "../services/ApiSauce";

import { success, failure } from "../actions/NotificationListingActions";
import { request as generalSettingsRequest } from "../actions/GeneralSettingsActions";
import { NOTIFICATION_LISTING } from "../actions/ActionTypes";
import { API_NOTIFICATION_LISTING } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_NOTIFICATION_LISTING, data);
}

function* watchRequest(action) {
  const { payload, reset } = action;
  try {
    const response = yield call(callRequest, payload);
    yield put(
      success(
        response.data.notification_list || [],
        response.data.page || {},
        reset
      )
    );
  } catch (err) {
    yield put(failure(err.message));
  }
}

export default function* root() {
  yield takeLatest(NOTIFICATION_LISTING.REQUEST, watchRequest);
}

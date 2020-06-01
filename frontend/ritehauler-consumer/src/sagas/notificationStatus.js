import { put, call, takeEvery } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";

import { request as generalSettingsRequest } from "../actions/GeneralSettingsActions";
import { updateNotificationList } from "../actions/NotificationListingActions";
import { UPDATE_NOTIFICATION_STATUS } from "../actions/ActionTypes";
import { API_UPDATE_NOTIFICATION_READ_STATUS } from "../config/WebService";

function callRequestUpdateNotification(data) {
  return ApiSauce.post(API_UPDATE_NOTIFICATION_READ_STATUS, data);
}

function* watchRequest(action) {
  const { historyId, orderId } = action;
  try {
    const payloadUpdateNotification = {
      entity_history_id: historyId
    };
    yield call(callRequestUpdateNotification, payloadUpdateNotification);
    yield put(generalSettingsRequest());
    yield put(updateNotificationList(orderId));
  } catch (err) {}
}

export default function* root() {
  yield takeEvery(UPDATE_NOTIFICATION_STATUS, watchRequest);
}

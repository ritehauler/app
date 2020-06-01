import { put, call, takeLatest, select } from "redux-saga/effects";
import firebase from "react-native-firebase";

import ApiSauce from "../util/ApiSauce";

import { success, failure } from "../actions/GeneralSettingsActions";
import { GENERAL_SETTINGS } from "../actions/ActionTypes";
import { API_GENERAL_SETTINGS } from "../config/WebService";
import { getUser } from "../reducers/selectors";
import Util from "../util";

function callRequest(data) {
  return ApiSauce.get(API_GENERAL_SETTINGS, data);
}

function* watchRequest(action) {
  try {
    const { data } = yield select(getUser);
    if (data.entity_id) {
      const payload = { entity_id: data.entity_id, mobile_json: 1 };
      const response = yield call(callRequest, payload);
      if (response.data.setting) {
        Util.setSettings(response.data.setting);
        if (Util.isPlatformIOS()) {
          firebase
            .notifications()
            .setBadge(response.data.setting.unread_notification || 0);
        }
      }
      yield put(success(response.data.setting || {}, response.message));
    }
  } catch (err) {
    yield put(failure(err.message));
  }
}

export default function* root() {
  yield takeLatest(GENERAL_SETTINGS.REQUEST, watchRequest);
}

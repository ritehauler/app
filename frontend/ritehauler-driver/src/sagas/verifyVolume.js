import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../services/ApiSauce";
import { Actions } from "react-native-router-flux";
import { success, failure } from "../actions/VerifyVolumeActions";
import { VERIFY_VOLUME } from "../actions/ActionTypes";
import { API_VERIFY_EXTRA_ITEM } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.post(API_VERIFY_EXTRA_ITEM, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(VERIFY_VOLUME.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(success(response));
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

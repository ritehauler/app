import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";

import { success, failure } from "../actions/ItemNameActions";
import { ITEM_NAME } from "../actions/ActionTypes";
import { API_ITEM_NAMES_LIST } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_ITEM_NAMES_LIST, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(ITEM_NAME.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(success(response.data.item || [], response.message));
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

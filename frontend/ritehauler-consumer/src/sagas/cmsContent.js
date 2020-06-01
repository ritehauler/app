import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";

import { success, failure } from "../actions/CmsContentActions";
import { CMS_CONTENT } from "../actions/ActionTypes";
import { API_CMS_CONTENT } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_CMS_CONTENT, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(CMS_CONTENT.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      const data =
        response.data.cms && response.data.cms.length > 0
          ? response.data.cms[0]
          : {};
      yield put(success(data, response.message));
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

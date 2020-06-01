import { put, call, take, fork } from "redux-saga/effects";
import { success, failure } from "../actions/StateCity";
import { STATE_CITY } from "../actions/ActionTypes";
import { API_STATE_CITY } from "../config/WebService";
import ApiSauce from "../util/ApiSauce";
import Util from "../util";

function callRequest(url, data) {
  return ApiSauce.get(API_STATE_CITY, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(STATE_CITY.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      yield put(success(response.data.state));
    } catch (err) {
      yield put(failure(err));
      Util.alert(err.message);
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

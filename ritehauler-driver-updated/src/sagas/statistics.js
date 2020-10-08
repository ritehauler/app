import { put, call, take, fork, takeLatest } from "redux-saga/effects";
import ApiSauce from "../services/ApiSauce";
import { success, failure } from "../actions/StatisticsActions";
import { STATISTICS } from "../actions/ActionTypes";
import { API_STATISTICS } from "../config/WebService";
function callRequest(data) {
  return ApiSauce.get(API_STATISTICS, data);
}

function* watchUserRequest(action) {
  const { payload } = action;
  try {
    const response = yield call(callRequest, payload);
    yield put(success(response.data || {}));
  } catch (err) {
    yield put(failure(err.message));
  }
}

export default function* root() {
  yield takeLatest(STATISTICS.REQUEST, watchUserRequest);
}

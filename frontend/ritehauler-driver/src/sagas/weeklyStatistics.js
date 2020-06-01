import { put, call, take, fork, takeLatest } from "redux-saga/effects";
import ApiSauce from "../services/ApiSauce";
import { success, failure } from "../actions/WeeklyStatisticsActions";
import { WEEKLY_STATISTICS } from "../actions/ActionTypes";
import { API_WEEKLY_STATS } from "../config/WebService";
function callRequest(data) {
  return ApiSauce.get(API_WEEKLY_STATS, data);
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
  yield takeLatest(WEEKLY_STATISTICS.REQUEST, watchUserRequest);
}

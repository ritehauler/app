import { take, put, call, fork } from "redux-saga/effects";
import GoogleApiSauce from "../services/GoogleApiSauce";
import { MAP_ROUTE } from "../actions/ActionTypes";
import { success, failure } from "../actions/MapRouteActions";

function callRequest(url, data) {
  return GoogleApiSauce.get(url, data, {});
}

function* watchRequest() {
  while (true) {
    const { url, payload } = yield take(MAP_ROUTE.REQUEST);
    try {
      const response = yield call(callRequest, url, payload);
      yield put(success(response, url));
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchRequest);
}

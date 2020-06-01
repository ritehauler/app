import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../services/ApiSauce";

import { success, failure } from "../actions/SubmitRatingsActions";
import { SUBMIT_RATINGS } from "../actions/ActionTypes";
import { API_RATE_CUSTOMER } from "../config/WebService";
import { Actions } from "../../node_modules/react-native-router-flux";

function callRequest(data) {
  return ApiSauce.post(API_RATE_CUSTOMER, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(SUBMIT_RATINGS.REQUEST);

    try {
      const response = yield call(callRequest, payload);
      yield put(
        success(response.data && response.data.rate ? response.data.rate : {})
      );
      Actions.reset("home");
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

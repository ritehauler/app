import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";

import { success, failure } from "../actions/RatingOptionActions";
import { RATING_OPTIONS } from "../actions/ActionTypes";
import { API_RATING_LIST } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_RATING_LIST, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(RATING_OPTIONS.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      const data =
        response.data.attribute_option_listing &&
        response.data.attribute_option_listing.driver_review &&
        response.data.attribute_option_listing.driver_review.options
          ? response.data.attribute_option_listing.driver_review.options
          : [];
      yield put(success(data, response.message));
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

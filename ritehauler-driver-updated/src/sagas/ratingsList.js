import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../services/ApiSauce";

import { success, failure } from "../actions/RatingsListActions";
import { RATINGS_LIST } from "../actions/ActionTypes";
import { API_RATINGS_LIST } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_RATINGS_LIST, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(RATINGS_LIST.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      if (
        response.data.attribute_option_listing &&
        response.data.attribute_option_listing.customer_review &&
        response.data.attribute_option_listing.customer_review.options
      ) {
        const data =
          response.data.attribute_option_listing.customer_review.options;
        /*
          data.push({
          title: "others",
          value: "others"
        });
        */
        yield put(success(data));
      } else {
        yield put(success([]));
      }
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

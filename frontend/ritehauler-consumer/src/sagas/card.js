import { put, call, take, fork } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";
import Util from "../util";

import {
  listingSuccess,
  listingFailure,
  addCardSuccess,
  addCardFailure,
  deleteCardSuccess,
  deleteCardFailure
} from "../actions/CardActions";
import { CARD_LISTING, ADD_CARD, DELETE_CARD } from "../actions/ActionTypes";
import {
  API_ADD_CARD,
  API_CARD_LISTING,
  API_DELETE_CARD
} from "../config/WebService";

function callRequestPost(url, data) {
  return ApiSauce.post(url, data);
}

function* watchCardListingRequest() {
  while (true) {
    const { payload, reset } = yield take(CARD_LISTING.REQUEST);
    try {
      const response = yield call(callRequestPost, API_CARD_LISTING, payload);
      yield put(
        listingSuccess(response.data.cards || [], response.message, reset)
      );
    } catch (err) {
      yield put(listingFailure(err.message));
    }
  }
}

function* watchAddCardRequest() {
  while (true) {
    const { payload } = yield take(ADD_CARD.REQUEST);
    try {
      const response = yield call(callRequestPost, API_ADD_CARD, payload);
      yield put(addCardSuccess(response.data.card, response.message));
    } catch (err) {
      yield put(addCardFailure(err.message));
      Util.alert(err.message);
    }
  }
}

function* watchDeleteCardRequest() {
  while (true) {
    const { payload, index } = yield take(DELETE_CARD.REQUEST);
    try {
      const response = yield call(callRequestPost, API_DELETE_CARD, payload);
      yield put(deleteCardSuccess(response.data, response.message, index));
    } catch (err) {
      yield put(deleteCardFailure(err.message));
      Util.alert(err.message);
    }
  }
}

export default function* root() {
  yield fork(watchCardListingRequest);
  yield fork(watchAddCardRequest);
  yield fork(watchDeleteCardRequest);
}

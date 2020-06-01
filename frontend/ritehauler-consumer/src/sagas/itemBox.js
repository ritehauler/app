import { put, call, takeLatest } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";
import Util from "../util";

import { success, failure } from "../actions/ItemBoxActions";
import { ITEM_BOX } from "../actions/ActionTypes";
import { API_ITEM_BOX } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.get(API_ITEM_BOX, data);
}

function* watchRequest(action) {
  const { payload } = action;
  try {
    const response = yield call(callRequest, payload);
    yield put(success(response.data.item_box, response.message));
  } catch (err) {
    yield put(failure(err.message));
    //Util.alert(err.message);
  }
}

export default function* root() {
  yield takeLatest(ITEM_BOX.REQUEST, watchRequest);
}

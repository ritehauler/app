import { put, call, take, fork } from "redux-saga/effects";
import { Actions } from "react-native-router-flux";

import ApiSauce from "../util/ApiSauce";
import Util from "../util";

import { success, failure } from "../actions/SaveProfessionalActions";
import { saveDeliveryProfessionalOrder } from "../actions/OrderActions";
import { SAVE_PROFESSIONAL } from "../actions/ActionTypes";
import { API_SAVE_PROFESSIONAL } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.post(API_SAVE_PROFESSIONAL, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload, data } = yield take(SAVE_PROFESSIONAL.REQUEST);
    try {
      const response = yield call(callRequest, payload);

      // send success
      yield put(success(payload, response.message));

      // save delivery professional in order info reducer
      yield put(
        saveDeliveryProfessionalOrder(data, {
          professional_id: payload.professional_id
        })
      );

      // send to order summary
      Actions.paymentMethod();
    } catch (err) {
      yield put(failure(err.message));
      Util.alert(err.message);
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

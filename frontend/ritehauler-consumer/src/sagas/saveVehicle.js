import { put, call, take, fork } from "redux-saga/effects";
import { Actions } from "react-native-router-flux";

import ApiSauce from "../util/ApiSauce";
import Util from "../util";

import { success, failure } from "../actions/SaveVehicleActions";
import { saveVehicleOrder } from "../actions/OrderActions";
import { SAVE_VEHICLE } from "../actions/ActionTypes";
import { API_SAVE_VEHICLE } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.post(API_SAVE_VEHICLE, data);
}

function* watchUserRequest() {
  while (true) {
    const { payload, data } = yield take(SAVE_VEHICLE.REQUEST);
    try {
      const response = yield call(callRequest, payload);
      const { truck_selected_id } = response.data;

      // send success
      yield put(success(truck_selected_id, payload, response.message));

      // save vehicle in order info reducer
      yield put(
        saveVehicleOrder(data, {
          truck_selected_id,
          truck_id: payload.truck_id,
          weight: payload.weight,
          volume: payload.volume
        })
      );

      // send to order summary
      Actions.deliveryProfessional({
        truckSelectedId: response.data.truck_selected_id
      });
    } catch (err) {
      yield put(failure(err.message));
      Util.alert(err.message);
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}

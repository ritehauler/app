// @flow
import { SAVE_VEHICLE } from "./ActionTypes";

export function request(payload: Object, data: Object) {
  return {
    payload,
    data,
    type: SAVE_VEHICLE.REQUEST
  };
}

export function success(
  truckSelectedId: String,
  payLoad: Object,
  successMessage: String
) {
  return {
    truckSelectedId,
    payLoad,
    successMessage,
    type: SAVE_VEHICLE.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: SAVE_VEHICLE.FAILURE
  };
}

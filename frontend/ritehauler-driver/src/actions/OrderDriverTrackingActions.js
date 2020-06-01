// @flow
import { ORDER_DRIVER_TRACKING } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: ORDER_DRIVER_TRACKING.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: ORDER_DRIVER_TRACKING.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: ORDER_DRIVER_TRACKING.FAILURE
  };
}

// @flow
import { ORDER_PLACE } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: ORDER_PLACE.REQUEST
  };
}

export function success(data: Object, successMessage: String) {
  return {
    data,
    successMessage,
    type: ORDER_PLACE.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: ORDER_PLACE.FAILURE
  };
}

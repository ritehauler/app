// @flow
import { ORDER_COMPLETE } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: ORDER_COMPLETE.REQUEST
  };
}

export function success(data: Object, successMessage: String) {
  return {
    data,
    successMessage,
    type: ORDER_COMPLETE.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: ORDER_COMPLETE.FAILURE
  };
}

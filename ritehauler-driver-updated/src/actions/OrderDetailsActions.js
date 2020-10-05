// @flow
import { ORDER_DETAILS } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: ORDER_DETAILS.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: ORDER_DETAILS.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: ORDER_DETAILS.FAILURE
  };
}

// @flow
import { PENDING_ORDERS } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: PENDING_ORDERS.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: PENDING_ORDERS.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: PENDING_ORDERS.FAILURE
  };
}

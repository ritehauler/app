// @flow
import { MY_ORDERS } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: MY_ORDERS.REQUEST
  };
}

export function success(data: Object, nextOffset, isConcat) {
  return {
    data,
    nextOffset,
    isConcat,
    type: MY_ORDERS.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: MY_ORDERS.FAILURE
  };
}

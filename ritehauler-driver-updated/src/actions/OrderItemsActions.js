// @flow
import { ORDER_ITEMS } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: ORDER_ITEMS.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: ORDER_ITEMS.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: ORDER_ITEMS.FAILURE
  };
}

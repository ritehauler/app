// @flow
import { ORDER_STATUSES } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: ORDER_STATUSES.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: ORDER_STATUSES.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: ORDER_STATUSES.FAILURE
  };
}

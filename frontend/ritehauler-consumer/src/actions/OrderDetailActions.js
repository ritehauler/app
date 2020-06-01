// @flow
import { ORDER_DETAIL, RESET_ORDER_DETAIL } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: ORDER_DETAIL.REQUEST
  };
}

export function success(data: Object, successMessage: String) {
  return {
    data,
    successMessage,
    type: ORDER_DETAIL.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: ORDER_DETAIL.FAILURE
  };
}

export function resetOrderDetail() {
  return {
    type: RESET_ORDER_DETAIL
  };
}

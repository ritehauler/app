// @flow
import { DELIVERY_PROFESSIONALS } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: DELIVERY_PROFESSIONALS.REQUEST
  };
}

export function success(data: Object, successMessage: String) {
  return {
    data,
    successMessage,
    type: DELIVERY_PROFESSIONALS.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: DELIVERY_PROFESSIONALS.FAILURE
  };
}

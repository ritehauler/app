// @flow
import { RATE_DRIVER } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: RATE_DRIVER.REQUEST
  };
}

export function success(data: Object, successMessage: String) {
  return {
    data,
    successMessage,
    type: RATE_DRIVER.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: RATE_DRIVER.FAILURE
  };
}

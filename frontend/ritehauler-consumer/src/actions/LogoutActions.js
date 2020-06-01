// @flow

import { LOGOUT } from "./ActionTypes";

export function request(payload: ?Object = undefined, message: String) {
  return {
    payload,
    message,
    type: LOGOUT.REQUEST
  };
}

export function success(successMessage: String) {
  return {
    successMessage,
    type: LOGOUT.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: LOGOUT.FAILURE
  };
}

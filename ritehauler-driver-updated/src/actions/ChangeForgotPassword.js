// @flow
import { CHANGE_FORGOT_PASSWORD } from "./ActionTypes";

export function request(payload: Object, url: string) {
  return {
    payload,
    url,
    type: CHANGE_FORGOT_PASSWORD.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: CHANGE_FORGOT_PASSWORD.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: CHANGE_FORGOT_PASSWORD.FAILURE
  };
}

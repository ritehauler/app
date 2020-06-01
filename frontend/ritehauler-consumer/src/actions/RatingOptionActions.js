// @flow
import { RATING_OPTIONS } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: RATING_OPTIONS.REQUEST
  };
}

export function success(data: Object, successMessage: String) {
  return {
    data,
    successMessage,
    type: RATING_OPTIONS.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: RATING_OPTIONS.FAILURE
  };
}

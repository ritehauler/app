// @flow
import { SUBMIT_RATINGS } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: SUBMIT_RATINGS.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: SUBMIT_RATINGS.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: SUBMIT_RATINGS.FAILURE
  };
}

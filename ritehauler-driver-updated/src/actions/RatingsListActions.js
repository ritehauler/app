// @flow
import { RATINGS_LIST } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: RATINGS_LIST.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: RATINGS_LIST.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: RATINGS_LIST.FAILURE
  };
}

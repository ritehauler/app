// @flow
import { STATISTICS } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: STATISTICS.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: STATISTICS.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: STATISTICS.FAILURE
  };
}

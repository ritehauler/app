// @flow
import { WEEKLY_STATISTICS } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: WEEKLY_STATISTICS.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: WEEKLY_STATISTICS.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: WEEKLY_STATISTICS.FAILURE
  };
}

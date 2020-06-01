// @flow
import { TRACK_ORDER, RESET_TRACK_ORDER } from "./ActionTypes";

export function request(payload: Object, reset: boolean = false) {
  return {
    payload,
    reset,
    type: TRACK_ORDER.REQUEST
  };
}

export function success(data: Object, successMessage: String) {
  return {
    data,
    successMessage,
    type: TRACK_ORDER.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: TRACK_ORDER.FAILURE
  };
}

export function resetTrackOrder() {
  return {
    type: RESET_TRACK_ORDER
  };
}

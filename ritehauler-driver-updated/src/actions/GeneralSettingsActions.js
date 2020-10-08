// @flow
import { GENERAL_SETTINGS } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: GENERAL_SETTINGS.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: GENERAL_SETTINGS.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: GENERAL_SETTINGS.FAILURE
  };
}

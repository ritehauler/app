// @flow
import { SAVE_PROFESSIONAL } from "./ActionTypes";

export function request(payload: Object, data: Object) {
  return {
    payload,
    data,
    type: SAVE_PROFESSIONAL.REQUEST
  };
}

export function success(payLoad: Object, successMessage: String) {
  return {
    payLoad,
    successMessage,
    type: SAVE_PROFESSIONAL.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: SAVE_PROFESSIONAL.FAILURE
  };
}

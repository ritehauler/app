// @flow
import { CHARGE_CARD } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: CHARGE_CARD.REQUEST
  };
}

export function success(data: Object, successMessage: String) {
  return {
    data,
    successMessage,
    type: CHARGE_CARD.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: CHARGE_CARD.FAILURE
  };
}

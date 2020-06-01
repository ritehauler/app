// @flow
import { ITEM_BOX, RESET_ITEM_BOX } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: ITEM_BOX.REQUEST
  };
}

export function success(data: Object, successMessage: String) {
  return {
    data,
    successMessage,
    type: ITEM_BOX.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: ITEM_BOX.FAILURE
  };
}

export function resetItemBox() {
  return {
    type: RESET_ITEM_BOX
  };
}

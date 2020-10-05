// @flow
import { ITEM_NAME } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: ITEM_NAME.REQUEST
  };
}

export function success(data: Object, successMessage: String) {
  return {
    data,
    successMessage,
    type: ITEM_NAME.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: ITEM_NAME.FAILURE
  };
}

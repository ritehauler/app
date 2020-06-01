// @flow
import { ADD_EXTRA_ITEM } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: ADD_EXTRA_ITEM.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: ADD_EXTRA_ITEM.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: ADD_EXTRA_ITEM.FAILURE
  };
}

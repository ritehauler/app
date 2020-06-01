// @flow

import { UPDATE_TOKEN } from "./ActionTypes";

export function request(payload: ?Object = undefined) {
  return {
    payload,
    type: UPDATE_TOKEN.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: UPDATE_TOKEN.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: UPDATE_TOKEN.FAILURE
  };
}

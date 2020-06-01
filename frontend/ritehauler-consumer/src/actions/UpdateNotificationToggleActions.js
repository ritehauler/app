// @flow
import { UPDATE_NOTIFICATION_TOGGLE } from "./ActionTypes";

export function request(payload: ?Object = undefined) {
  return {
    payload,
    type: UPDATE_NOTIFICATION_TOGGLE.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: UPDATE_NOTIFICATION_TOGGLE.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: UPDATE_NOTIFICATION_TOGGLE.FAILURE
  };
}

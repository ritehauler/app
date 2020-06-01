// @flow
import { UPDATE_ASSIGNED_ORDER_STATUS } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: UPDATE_ASSIGNED_ORDER_STATUS.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: UPDATE_ASSIGNED_ORDER_STATUS.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: UPDATE_ASSIGNED_ORDER_STATUS.FAILURE
  };
}

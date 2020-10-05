// @flow
import { ASSIGNED_ORDERS, CLEAR_ASSIGNED_ORDERS } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: ASSIGNED_ORDERS.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: ASSIGNED_ORDERS.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: ASSIGNED_ORDERS.FAILURE
  };
}

export function clearAssignedOrders() {
  return {
    type: CLEAR_ASSIGNED_ORDERS
  };
}

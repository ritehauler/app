// @flow
import {
  TODAYS_ORDERS,
  LOCALLY_UPDATE_ASSIGNED_ORDER_STATUS
} from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: TODAYS_ORDERS.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: TODAYS_ORDERS.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: TODAYS_ORDERS.FAILURE
  };
}

export function locallyUpdateOrderStatus() {
  return {
    type: LOCALLY_UPDATE_ASSIGNED_ORDER_STATUS
  };
}

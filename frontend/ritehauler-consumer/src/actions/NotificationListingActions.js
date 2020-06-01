// @flow
import {
  NOTIFICATION_LISTING,
  RESET_NOTIFICATION_LISTING,
  SET_NOTIFICATION_INFO,
  UPDATE_NOTIFICATION_LIST,
  UPDATE_NOTIFICATION_STATUS
} from "./ActionTypes";

export function request(payload: Object, reset: boolean, clearList: boolean) {
  return {
    payload,
    reset,
    clearList,
    type: NOTIFICATION_LISTING.REQUEST
  };
}

export function success(
  data: Object,
  page: Object,
  successMessage: String,
  reset: boolean
) {
  return {
    data,
    page,
    successMessage,
    reset,
    type: NOTIFICATION_LISTING.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: NOTIFICATION_LISTING.FAILURE
  };
}

export function resetNotificationListing() {
  return {
    type: RESET_NOTIFICATION_LISTING
  };
}

export function setNotificationInfo(orderId: any, orderNumber: any, time: any) {
  return {
    orderId,
    orderNumber,
    time,
    type: SET_NOTIFICATION_INFO
  };
}

export function updateNotificationList(orderId: number) {
  return {
    orderId,
    type: UPDATE_NOTIFICATION_LIST
  };
}

export function updateNotificationStatus(historyId: number, orderId: number) {
  return {
    historyId,
    orderId,
    type: UPDATE_NOTIFICATION_STATUS
  };
}

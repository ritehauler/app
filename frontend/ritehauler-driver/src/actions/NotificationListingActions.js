// @flow
import {
  NOTIFICATION_LISTING,
  RESET_NOTIFICATION_LISTING
} from "./ActionTypes";

export function request(payload: Object, reset: boolean) {
  return {
    payload,
    reset,
    type: NOTIFICATION_LISTING.REQUEST
  };
}

export function success(data: Object, page: Object, reset: boolean) {
  return {
    data,
    page,
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

export function clearNotificationListing() {
  return {
    type: RESET_NOTIFICATION_LISTING
  };
}

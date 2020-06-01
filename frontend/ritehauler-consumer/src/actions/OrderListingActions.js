// @flow
import { ORDER_LISTING, RESET_ORDER_LISTING } from "./ActionTypes";

export function request(payload: Object, reset: boolean, clearList: boolean) {
  return {
    payload,
    reset,
    clearList,
    type: ORDER_LISTING.REQUEST
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
    type: ORDER_LISTING.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: ORDER_LISTING.FAILURE
  };
}

export function resetOrderListing() {
  return {
    type: RESET_ORDER_LISTING
  };
}

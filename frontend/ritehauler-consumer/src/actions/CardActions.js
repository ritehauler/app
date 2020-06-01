// @flow
import {
  CARD_LISTING,
  ADD_CARD,
  DELETE_CARD,
  RESET_CARD_LIST
} from "./ActionTypes";

// card listing
export function listingRequest(payload: Object, reset: boolean) {
  return {
    payload,
    reset,
    type: CARD_LISTING.REQUEST
  };
}

export function listingSuccess(
  data: Object,
  successMessage: String,
  reset: boolean
) {
  return {
    data,
    successMessage,
    reset,
    type: CARD_LISTING.SUCCESS
  };
}

export function listingFailure(errorMessage: String) {
  return {
    errorMessage,
    type: CARD_LISTING.FAILURE
  };
}

// add card
export function addCardRequest(payload: Object) {
  return {
    payload,
    type: ADD_CARD.REQUEST
  };
}

export function addCardSuccess(data: Object, successMessage: String) {
  return {
    data,
    successMessage,
    type: ADD_CARD.SUCCESS
  };
}

export function addCardFailure(errorMessage: String) {
  return {
    errorMessage,
    type: ADD_CARD.FAILURE
  };
}

// delete card
export function deleteCardRequest(payload: Object, index: Number) {
  return {
    payload,
    index,
    type: DELETE_CARD.REQUEST
  };
}

export function deleteCardSuccess(
  data: Object,
  successMessage: String,
  index: Number
) {
  return {
    data,
    successMessage,
    index,
    type: DELETE_CARD.SUCCESS
  };
}

export function deleteCardFailure(errorMessage: String) {
  return {
    errorMessage,
    type: DELETE_CARD.FAILURE
  };
}

export function resetCardListing() {
  return {
    type: RESET_CARD_LIST
  };
}

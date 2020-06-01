import {
  GOOGLE_NEARBY,
  GOOGLE_PLACES,
  GOOGLE_PLACE_DETAIL
} from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: GOOGLE_NEARBY.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: GOOGLE_NEARBY.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: GOOGLE_NEARBY.FAILURE
  };
}

export function googlePlacesRequest(payload: Object) {
  return {
    payload,
    type: GOOGLE_PLACES.REQUEST
  };
}

export function googlePlacesSuccess(data: Object) {
  return {
    data,
    type: GOOGLE_PLACES.SUCCESS
  };
}

export function googlePlacesFailure(errorMessage: String) {
  return {
    errorMessage,
    type: GOOGLE_PLACES.FAILURE
  };
}

export function googlePlaceDetailRequest(payload: Object, callback: Function) {
  return {
    payload,
    callback,
    type: GOOGLE_PLACE_DETAIL
  };
}

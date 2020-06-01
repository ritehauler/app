import { RECENT_LOCATIONS } from "../actions/ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: RECENT_LOCATIONS.REQUEST
  };
}
export function success(data: Object) {
  return {
    data,
    type: RECENT_LOCATIONS.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: RECENT_LOCATIONS.FAILURE
  };
}

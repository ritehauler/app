import { STATE_CITY } from "../actions/ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: STATE_CITY.REQUEST
  };
}
export function success(data: Object) {
  return {
    data,
    type: STATE_CITY.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: STATE_CITY.FAILURE
  };
}

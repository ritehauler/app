import { GENERAL_SETTINGS } from "../actions/ActionTypes";

export function request() {
  return {
    type: GENERAL_SETTINGS.REQUEST
  };
}
export function success(data: Object) {
  return {
    data,
    type: GENERAL_SETTINGS.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: GENERAL_SETTINGS.FAILURE
  };
}

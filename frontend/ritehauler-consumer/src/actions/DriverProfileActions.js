// @flow
import { DRIVER_PROFILE, RESET_DRIVER_PROFILE } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: DRIVER_PROFILE.REQUEST
  };
}

export function success(data: Object, successMessage: String) {
  return {
    data,
    successMessage,
    type: DRIVER_PROFILE.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: DRIVER_PROFILE.FAILURE
  };
}

export function resetDriverProfile() {
  return {
    type: RESET_DRIVER_PROFILE
  };
}

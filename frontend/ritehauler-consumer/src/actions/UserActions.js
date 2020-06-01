// @flow
import { USER, USER_EDIT, CHANGE_PASSWORD } from "./ActionTypes";

export function request(payload: Object, url: String, callback) {
  return {
    payload,
    url,
    callback,
    type: USER.REQUEST
  };
}

// EDIT PROFILE
export function userEditRequest(payload: Object, url: String) {
  return {
    payload,
    url,
    type: USER_EDIT.REQUEST
  };
}

// CHANGE PASSWORD
export function changePasswordRequest(payload: Object, url: String) {
  return {
    payload,
    url,
    type: CHANGE_PASSWORD.REQUEST
  };
}

export function success(data: Object, successMessage: String, url: String) {
  return {
    data,
    successMessage,
    url,
    type: USER.SUCCESS
  };
}

export function failure(errorMessage: String, url: String) {
  return {
    errorMessage,
    url,
    type: USER.FAILURE
  };
}

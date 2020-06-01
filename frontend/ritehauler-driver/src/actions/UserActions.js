// @flow

import { USER, LOGOUT, CURRENT_LOCATION } from "./ActionTypes";

export function request(url: string, payload: Object) {
  return {
    url,
    payload,
    type: USER.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: USER.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: USER.FAILURE
  };
}

export function logout(payload: Object) {
  return {
    payload,
    type: LOGOUT.REQUEST
  };
}

export function logoutSuccess() {
  return {
    type: LOGOUT.SUCCESS
  };
}

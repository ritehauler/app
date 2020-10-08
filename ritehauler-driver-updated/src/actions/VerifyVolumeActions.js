// @flow
import {
  VERIFY_VOLUME,
  CLEAR_VERIFY_VOLUME,
  SET_RE_RENDER_KEY
} from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: VERIFY_VOLUME.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: VERIFY_VOLUME.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: VERIFY_VOLUME.FAILURE
  };
}

export function clearVerifyVolume() {
  return {
    type: CLEAR_VERIFY_VOLUME
  };
}

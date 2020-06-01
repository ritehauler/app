// @flow
import { CMS_CONTENT, RESET_CMS_CONTENT } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: CMS_CONTENT.REQUEST
  };
}

export function success(data: Object, successMessage: String) {
  return {
    data,
    successMessage,
    type: CMS_CONTENT.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: CMS_CONTENT.FAILURE
  };
}

export function resetCmsContent() {
  return {
    type: RESET_CMS_CONTENT
  };
}

// @flow
import { ATTACHMENT_REQUEST, ATTACHMENT_UPDATE } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: ATTACHMENT_REQUEST
  };
}

export function updateAttachment(
  itemId: String,
  failure: boolean,
  progress: Number,
  attachment: Object
) {
  return {
    itemId,
    failure,
    progress,
    attachment,
    type: ATTACHMENT_UPDATE
  };
}

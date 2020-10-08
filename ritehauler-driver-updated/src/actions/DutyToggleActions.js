// @flow
import { DUTY_TOGGLE } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: DUTY_TOGGLE
  };
}

// @flow
import { SHOW_CARD, HIDE_CARD } from "./ActionTypes";

export function showCard() {
  return {
    type: SHOW_CARD
  };
}

export function hideCard() {
  return {
    type: HIDE_CARD
  };
}

import {
  INCREMENT_PENDING_ORDER_COUNT,
  DECREMENT_PENDING_ORDER_COUNT
} from "./ActionTypes";

export function incrementPendingOrderCount() {
  return {
    type: INCREMENT_PENDING_ORDER_COUNT
  };
}

export function decrementPendingOrderCount() {
  return {
    type: DECREMENT_PENDING_ORDER_COUNT
  };
}

// @flow
import Immutable from "seamless-immutable";

import { SET_NOTIFICATION_INFO } from "../actions/ActionTypes";

const initialState = Immutable({
  orderId: undefined,
  orderNumber: undefined,
  time: undefined
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case SET_NOTIFICATION_INFO: {
      return Immutable.merge(state, {
        orderId: action.orderId,
        orderNumber: action.orderNumber,
        time: action.time
      });
    }
    default:
      return state;
  }
};

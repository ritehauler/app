// @flow
import Immutable from "seamless-immutable";
import _ from "lodash";

import {
  TODAYS_ORDERS,
  LOCALLY_UPDATE_ASSIGNED_ORDER_STATUS
} from "../actions/ActionTypes";

import { ABOUT_TO_REACH } from "../constant";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case TODAYS_ORDERS.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }
    case TODAYS_ORDERS.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });
    }
    case TODAYS_ORDERS.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });

    case LOCALLY_UPDATE_ASSIGNED_ORDER_STATUS: {
      let orders = _.cloneDeep(state.data);
      orders[0].order_status.value = ABOUT_TO_REACH;
      return Immutable.merge(state, {
        data: orders
      });
    }
    default:
      return state;
  }
};

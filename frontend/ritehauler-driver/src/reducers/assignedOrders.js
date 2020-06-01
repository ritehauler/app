// @flow
import Immutable from "seamless-immutable";

import { ASSIGNED_ORDERS, CLEAR_ASSIGNED_ORDERS } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case ASSIGNED_ORDERS.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }

    case ASSIGNED_ORDERS.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });
    }

    case ASSIGNED_ORDERS.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });

    case CLEAR_ASSIGNED_ORDERS:
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: []
      });

    default:
      return state;
  }
};

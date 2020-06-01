// @flow
import Immutable from "seamless-immutable";

import { ORDER_DETAIL, RESET_ORDER_DETAIL } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  data: {}
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case ORDER_DETAIL.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true,
        failure: false,
        data: {}
      });
    }
    case ORDER_DETAIL.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        data: action.data
      });
    }
    case ORDER_DETAIL.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        successMessage: "",
        data: {},
        errorMessage: action.errorMessage
      });
    case RESET_ORDER_DETAIL: {
      return initialState;
    }
    default:
      return state;
  }
};

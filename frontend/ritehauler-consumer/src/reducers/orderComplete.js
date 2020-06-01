// @flow
import Immutable from "seamless-immutable";

import { ORDER_COMPLETE } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  data: {}
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case ORDER_COMPLETE.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true,
        failure: false,
        data: {}
      });
    }
    case ORDER_COMPLETE.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        data: action.data
      });
    }
    case ORDER_COMPLETE.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        successMessage: "",
        data: {},
        errorMessage: action.errorMessage
      });
    default:
      return state;
  }
};

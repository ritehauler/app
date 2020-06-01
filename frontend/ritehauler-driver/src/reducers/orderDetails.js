// @flow
import Immutable from "seamless-immutable";

import { ORDER_DETAILS } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case ORDER_DETAILS.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }
    case ORDER_DETAILS.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });
    }
    case ORDER_DETAILS.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });
    default:
      return state;
  }
};

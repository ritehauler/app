// @flow
import Immutable from "seamless-immutable";

import { ORDER_PLACE } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  data: {}
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case ORDER_PLACE.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }
    case ORDER_PLACE.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        data: action.data
      });
    }
    case ORDER_PLACE.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        successMessage: "",
        errorMessage: action.errorMessage
      });
    default:
      return state;
  }
};

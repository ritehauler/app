// @flow
import Immutable from "seamless-immutable";

import { RATE_DRIVER } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  data: {}
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case RATE_DRIVER.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true,
        failure: false,
        data: {}
      });
    }
    case RATE_DRIVER.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        data: action.data
      });
    }
    case RATE_DRIVER.FAILURE:
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

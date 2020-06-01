// @flow
import Immutable from "seamless-immutable";

import { DRIVER_PROFILE, RESET_DRIVER_PROFILE } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  data: {}
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case DRIVER_PROFILE.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true,
        failure: false,
        data: {}
      });
    }
    case DRIVER_PROFILE.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        data: action.data
      });
    }
    case DRIVER_PROFILE.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        successMessage: "",
        data: {},
        errorMessage: action.errorMessage
      });
    case RESET_DRIVER_PROFILE: {
      return initialState;
    }
    default:
      return state;
  }
};

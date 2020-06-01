// @flow
import Immutable from "seamless-immutable";

import { TRACK_ORDER, RESET_TRACK_ORDER } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  data: {}
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case TRACK_ORDER.REQUEST: {
      const updateObject = {
        isFetching: true,
        failure: false
      };
      if (action.reset) {
        updateObject.data = {};
      }
      return Immutable.merge(state, updateObject);
    }
    case TRACK_ORDER.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        data: action.data
      });
    }
    case TRACK_ORDER.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        successMessage: "",
        errorMessage: action.errorMessage
      });
    case RESET_TRACK_ORDER: {
      return initialState;
    }
    default:
      return state;
  }
};

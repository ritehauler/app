// @flow
import Immutable from "seamless-immutable";

import { RATING_OPTIONS } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case RATING_OPTIONS.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }
    case RATING_OPTIONS.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        data: action.data
      });
    }
    case RATING_OPTIONS.FAILURE:
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

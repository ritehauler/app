// @flow
import Immutable from "seamless-immutable";

import { SUBMIT_RATINGS } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: {}
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case SUBMIT_RATINGS.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }
    case SUBMIT_RATINGS.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });
    }
    case SUBMIT_RATINGS.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });
    default:
      return state;
  }
};

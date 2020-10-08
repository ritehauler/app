// @flow
import Immutable from "seamless-immutable";

import { STATISTICS } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case STATISTICS.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }

    case STATISTICS.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });
    }

    case STATISTICS.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });

    default:
      return state;
  }
};

// @flow
import Immutable from "seamless-immutable";

import { WEEKLY_STATISTICS } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case WEEKLY_STATISTICS.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }

    case WEEKLY_STATISTICS.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });
    }

    case WEEKLY_STATISTICS.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });

    default:
      return state;
  }
};

// @flow
import Immutable from "seamless-immutable";

import { RATINGS_LIST } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case RATINGS_LIST.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }
    case RATINGS_LIST.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });
    }
    case RATINGS_LIST.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });
    default:
      return state;
  }
};

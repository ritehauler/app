// @flow
import Immutable from "seamless-immutable";

import { ADD_EXTRA_ITEM } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case ADD_EXTRA_ITEM.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }
    case ADD_EXTRA_ITEM.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });
    }
    case ADD_EXTRA_ITEM.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });
    default:
      return state;
  }
};

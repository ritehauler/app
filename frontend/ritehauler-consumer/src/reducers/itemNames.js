// @flow
import Immutable from "seamless-immutable";

import { ITEM_NAME } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case ITEM_NAME.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }
    case ITEM_NAME.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        data: action.data
      });
    }
    case ITEM_NAME.FAILURE:
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

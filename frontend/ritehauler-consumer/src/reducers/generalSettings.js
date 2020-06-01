// @flow
import Immutable from "seamless-immutable";

import { GENERAL_SETTINGS } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  data: {}
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case GENERAL_SETTINGS.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true,
        failure: false,
        data: {}
      });
    }
    case GENERAL_SETTINGS.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        data: action.data
      });
    }
    case GENERAL_SETTINGS.FAILURE:
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

// @flow
import Immutable from "seamless-immutable";

import { SAVE_PROFESSIONAL } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  payLoad: {}
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case SAVE_PROFESSIONAL.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true,
        payLoad: {}
      });
    }
    case SAVE_PROFESSIONAL.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        payLoad: action.payLoad
      });
    }
    case SAVE_PROFESSIONAL.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        successMessage: "",
        errorMessage: action.errorMessage,
        payLoad: {}
      });
    default:
      return state;
  }
};

// @flow
import Immutable from "seamless-immutable";

import { DELIVERY_PROFESSIONALS } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case DELIVERY_PROFESSIONALS.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }
    case DELIVERY_PROFESSIONALS.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        data: action.data
      });
    }
    case DELIVERY_PROFESSIONALS.FAILURE:
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

// @flow
import Immutable from "seamless-immutable";

import { UPDATE_ASSIGNED_ORDER_STATUS } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: {}
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case UPDATE_ASSIGNED_ORDER_STATUS.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }
    case UPDATE_ASSIGNED_ORDER_STATUS.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });
    }
    case UPDATE_ASSIGNED_ORDER_STATUS.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });
    default:
      return state;
  }
};

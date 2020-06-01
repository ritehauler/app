// @flow
import Immutable from "seamless-immutable";

import { SUGGESTED_VEHICLES } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  data: [],
  payLoad: {},
  truckSuggestedId: ""
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case SUGGESTED_VEHICLES.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true,
        payLoad: {},
        data: []
      });
    }
    case SUGGESTED_VEHICLES.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        data: action.data,
        payLoad: action.payLoad,
        truckSuggestedId: action.truckSuggestedId
      });
    }
    case SUGGESTED_VEHICLES.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        successMessage: "",
        payLoad: {},
        data: [],
        errorMessage: action.errorMessage
      });
    default:
      return state;
  }
};

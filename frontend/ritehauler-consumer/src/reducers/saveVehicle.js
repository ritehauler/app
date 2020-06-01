// @flow
import Immutable from "seamless-immutable";

import { SAVE_VEHICLE } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  payLoad: {},
  truckSelectedId: ""
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case SAVE_VEHICLE.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true,
        payLoad: {},
        truckSelectedId: ""
      });
    }
    case SAVE_VEHICLE.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        truckSelectedId: action.truckSelectedId,
        payLoad: action.payLoad
      });
    }
    case SAVE_VEHICLE.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        successMessage: "",
        payLoad: {},
        truckSelectedId: "",
        errorMessage: action.errorMessage
      });
    default:
      return state;
  }
};

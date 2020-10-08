// @flow
import Immutable from "seamless-immutable";
import _ from "lodash";
import {
  GENERAL_SETTINGS,
  INCREMENT_PENDING_ORDER_COUNT,
  DECREMENT_PENDING_ORDER_COUNT
} from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: {}
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case GENERAL_SETTINGS.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }

    case GENERAL_SETTINGS.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });
    }

    case GENERAL_SETTINGS.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });

    case INCREMENT_PENDING_ORDER_COUNT:
      let incrementedState = _.cloneDeep(state);
      incrementedState.pending_order = state.pending_order + 1;
      return Immutable.merge(state, {
        data: incrementedState
      });

    case DECREMENT_PENDING_ORDER_COUNT:
      let decrementedState = _.cloneDeep(state);
      decrementedState.pending_order = state.pending_order
        ? state.pending_order - 1
        : 0;
      return Immutable.merge(state, {
        data: decrementedState
      });

    default:
      return state;
  }
};

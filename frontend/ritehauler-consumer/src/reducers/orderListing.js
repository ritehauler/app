// @flow
import _ from "lodash";
import Immutable from "seamless-immutable";

import { ORDER_LISTING, RESET_ORDER_LISTING } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  isPullToRefresh: false,
  errorMessage: "",
  successMessage: "",
  page: {},
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case ORDER_LISTING.REQUEST: {
      const updateObject = {
        isPullToRefresh: action.reset,
        failure: false,
        isFetching: true
      };
      if (action.clearList) {
        updateObject.data = [];
        updateObject.page = {};
      }
      return Immutable.merge(state, updateObject);
    }
    case ORDER_LISTING.SUCCESS: {
      const newData = action.reset
        ? action.data
        : _.concat(state.data, action.data);
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        isPullToRefresh: false,
        errorMessage: "",
        successMessage: action.successMessage,
        data: newData,
        page: action.page
      });
    }

    case ORDER_LISTING.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        isPullToRefresh: false,
        successMessage: "",
        errorMessage: action.errorMessage
      });

    case RESET_ORDER_LISTING: {
      return initialState;
    }

    default:
      return state;
  }
};

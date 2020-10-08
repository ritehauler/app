// @flow
import _ from "lodash";
import Immutable from "seamless-immutable";

import {
  NOTIFICATION_LISTING,
  RESET_NOTIFICATION_LISTING
} from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  page: {},
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case NOTIFICATION_LISTING.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }
    case NOTIFICATION_LISTING.SUCCESS: {
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

    case NOTIFICATION_LISTING.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        isPullToRefresh: false,
        successMessage: "",
        errorMessage: action.errorMessage
      });

    case RESET_NOTIFICATION_LISTING: {
      return initialState;
    }

    default:
      return state;
  }
};

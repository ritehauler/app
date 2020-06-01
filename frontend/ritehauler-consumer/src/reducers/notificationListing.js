// @flow
import _ from "lodash";
import Immutable from "seamless-immutable";

import {
  NOTIFICATION_LISTING,
  RESET_NOTIFICATION_LISTING,
  UPDATE_NOTIFICATION_LIST
} from "../actions/ActionTypes";

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
    case NOTIFICATION_LISTING.REQUEST: {
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

    case UPDATE_NOTIFICATION_LIST: {
      const newData = _.cloneDeep(state.data);
      const index = _.findIndex(newData, { order_id: action.orderId });
      if (index !== -1) {
        newData[index].is_read = 1;
      }
      return Immutable.merge(state, {
        data: newData
      });
    }

    case RESET_NOTIFICATION_LISTING: {
      return initialState;
    }

    default:
      return state;
  }
};

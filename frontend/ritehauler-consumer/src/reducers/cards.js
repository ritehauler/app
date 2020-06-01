// @flow
import _ from "lodash";
import Immutable from "seamless-immutable";

import {
  CARD_LISTING,
  ADD_CARD,
  DELETE_CARD,
  RESET_CARD_LIST
} from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  failureAddDelete: false,
  isPullToRefresh: false,
  loading: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case ADD_CARD.REQUEST:
    case DELETE_CARD.REQUEST: {
      return Immutable.merge(state, {
        loading: true,
        failureAddDelete: false
      });
    }
    case CARD_LISTING.REQUEST: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: true,
        isPullToRefresh: action.reset
      });
    }

    case ADD_CARD.SUCCESS: {
      const newData = _.cloneDeep(state.data);
      newData.unshift(action.data);
      return Immutable.merge(state, {
        loading: false,
        data: newData,
        failureAddDelete: false
      });
    }
    case DELETE_CARD.SUCCESS: {
      const newData = _.cloneDeep(state.data);
      newData.splice(action.index, 1);
      return Immutable.merge(state, {
        loading: false,
        data: newData,
        failureAddDelete: false
      });
    }
    case CARD_LISTING.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        isPullToRefresh: false,
        successMessage: action.successMessage,
        data: action.data
      });
    }
    case CARD_LISTING.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        isPullToRefresh: false,
        successMessage: "",
        errorMessage: action.errorMessage
      });
    case ADD_CARD.FAILURE:
    case DELETE_CARD.FAILURE: {
      return Immutable.merge(state, {
        loading: false,
        failureAddDelete: true
      });
    }
    case RESET_CARD_LIST: {
      return initialState;
    }
    default:
      return state;
  }
};

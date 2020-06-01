// @flow
import Immutable from "seamless-immutable";
import {
  USER,
  LOGOUT,
  USER_EDIT,
  CHANGE_PASSWORD,
  UPDATE_NOTIFICATION_TOGGLE,
  UPDATE_TOKEN
} from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  successMessage: "",
  url: "",
  data: {}
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case CHANGE_PASSWORD.REQUEST:
    case USER.REQUEST:
    case USER_EDIT.REQUEST:
      return Immutable.merge(state, {
        isFetching: true,
        url: action.url
      });
    case UPDATE_TOKEN.SUCCESS: {
      return Immutable.merge(state, {
        data: action.data
      });
    }
    case USER.SUCCESS:
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage,
        data: action.data,
        url: action.url
      });
    case USER.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage,
        url: action.url
      });
    case UPDATE_NOTIFICATION_TOGGLE.SUCCESS: {
      return Immutable.merge(state, {
        data: action.data
      });
    }

    case LOGOUT.SUCCESS: {
      return initialState;
    }
    default:
      return state;
  }
};

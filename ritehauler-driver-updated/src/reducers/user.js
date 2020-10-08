// @flow
import Immutable from "seamless-immutable";
import * as types from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: {},
  tutorialFlag: false
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case types.USER.REQUEST:
      return Immutable.merge(state, {
        isFetching: true
      });
    case types.USER.SUCCESS:
      return Immutable.merge(state, {
        isFetching: false,
        failure: false,
        data: action.data,
        errorMessage: ""
      });
    case types.USER.FAILURE:
      return Immutable.merge(state, {
        isFetching: false,
        failure: true
      });
    case types.LOGOUT:
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: {}
      });
    default:
      return state;
  }
};

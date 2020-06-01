// @flow
import Immutable from "seamless-immutable";
import { LOGOUT } from "../actions/ActionTypes";

const initialState = Immutable({
  errorMessage: "",
  successMessage: "",
  isFetching: false
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case LOGOUT.REQUEST:
      return Immutable.merge(state, {
        isFetching: true
      });
    case LOGOUT.SUCCESS:
      return Immutable.merge(state, {
        isFetching: false,
        errorMessage: "",
        successMessage: action.successMessage
      });
    case LOGOUT.FAILURE:
      return Immutable.merge(state, {
        isFetching: false,
        successMessage: "",
        errorMessage: action.errorMessage
      });
    default:
      return state;
  }
};

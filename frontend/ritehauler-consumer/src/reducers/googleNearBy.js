// @flow
import Immutable from "seamless-immutable";
import { GOOGLE_NEARBY } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  previousLocation: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case GOOGLE_NEARBY.REQUEST:
      return Immutable.merge(state, {
        previousLocation: action.payload.location,
        isFetching: true
      });
    case GOOGLE_NEARBY.SUCCESS:
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });
    case GOOGLE_NEARBY.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });

    default:
      return state;
  }
};

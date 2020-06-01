// @flow
import Immutable from "seamless-immutable";
import { GOOGLE_PLACES } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case GOOGLE_PLACES.REQUEST:
      return Immutable.merge(state, {
        isFetching: true
      });

    case GOOGLE_PLACES.SUCCESS:
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });

    case GOOGLE_PLACES.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });

    default:
      return state;
  }
};

import Immutable from "seamless-immutable";
import { RECENT_LOCATIONS } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  errorMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case RECENT_LOCATIONS.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        data: action.data
      });
    }
    case RECENT_LOCATIONS.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        data: [],
        errorMessage: action.errorMessage
      });

    default:
      return state;
  }
};

// @flow
import Immutable from "seamless-immutable";
import { FOCUS_MAP } from "../actions/ActionTypes";
const initialState = Immutable({
  focusMap: false
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case FOCUS_MAP: {
      return Immutable.merge(state, {
        focusMap: action.data
      });
    }
    default:
      return state;
  }
};

// @flow
import Immutable from "seamless-immutable";
import * as types from "../actions/ActionTypes";
const initialState = Immutable({
  coords: {},
  tracking: false,
  backgroundLocation: false
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case types.CURRENT_LOCATION:
      return Immutable.merge(state, {
        coords: action.location
      });

    case types.TRACKING:
      return Immutable.merge(state, {
        tracking: action.status
      });

    case types.START_BACKGROUND_LOCATION:
      return Immutable.merge(state, {
        backgroundLocation: true
      });

    case types.STOP_BACKGROUND_LOCATION:
      return Immutable.merge(state, {
        backgroundLocation: false
      });

    default:
      return state;
  }
};

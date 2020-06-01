// @flow
import Immutable from "seamless-immutable";
import { SHOW_CARD, HIDE_CARD } from "../actions/ActionTypes";
const initialState = Immutable({
  showCard: false
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case SHOW_CARD: {
      return Immutable.merge(state, {
        showCard: true
      });
    }
    case HIDE_CARD: {
      return Immutable.merge(state, {
        showCard: false
      });
    }
    default:
      return state;
  }
};

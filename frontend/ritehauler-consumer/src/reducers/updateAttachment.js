// @flow
import Immutable from "seamless-immutable";

import { ATTACHMENT_UPDATE } from "../actions/ActionTypes";

const initialState = Immutable({
  itemId: "",
  failure: false,
  progress: 0,
  attachment: undefined
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case ATTACHMENT_UPDATE: {
      return Immutable.merge(state, {
        itemId: action.itemId,
        failure: action.failure,
        progress: action.progress,
        attachment: action.attachment
      });
    }
    default:
      return state;
  }
};

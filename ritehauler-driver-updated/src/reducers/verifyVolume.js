// @flow
import Immutable from "seamless-immutable";

import { VERIFY_VOLUME, CLEAR_VERIFY_VOLUME } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: {},
  reRender_key: ""
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case VERIFY_VOLUME.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }
    case VERIFY_VOLUME.SUCCESS: {
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });
    }
    case VERIFY_VOLUME.FAILURE: {
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage,
        data: {}
      });
    }
    case CLEAR_VERIFY_VOLUME:
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

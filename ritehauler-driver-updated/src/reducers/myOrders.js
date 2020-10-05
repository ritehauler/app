// @flow
import Immutable from "seamless-immutable";
import _ from "lodash";
import { MY_ORDERS } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: [],
  nextOffset: undefined
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case MY_ORDERS.REQUEST: {
      return Immutable.merge(state, {
        isFetching: true
      });
    }
    case MY_ORDERS.SUCCESS: {
      if (action.isConcat) {
        return Immutable.merge(state, {
          failure: false,
          isFetching: false,
          errorMessage: "",
          data: _.concat(state.data, action.data),
          nextOffset: action.nextOffset
        });
      }
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data,
        nextOffset: action.nextOffset
      });
    }
    case MY_ORDERS.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });
    default:
      return state;
  }
};

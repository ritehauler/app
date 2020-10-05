import _ from "lodash";
import Immutable from "seamless-immutable";
import { MAP_ROUTE, MAP_ROUTE_RESET } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: {}
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case MAP_ROUTE.REQUEST:
      return Immutable.merge(state, {
        isFetching: true
      });

    case MAP_ROUTE.SUCCESS:
      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data: action.data
      });

    case MAP_ROUTE.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });

    case MAP_ROUTE_RESET:
      return Immutable.merge(state, {
        data: action.data
      });

    default:
      return state;
  }
};

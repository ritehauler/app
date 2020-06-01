import Immutable from "seamless-immutable";
import { STATE_CITY } from "../actions/ActionTypes";

const initialState = Immutable({
  failure: false,
  isFetching: false,
  errorMessage: "",
  data: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case STATE_CITY.REQUEST:
      return Immutable.merge(state, {
        isFetching: true
      });
    case STATE_CITY.SUCCESS: {
      const { data } = action;
      for (let i = 0; i < data.length; i += 1) {
        if (!data[i].data) {
          data[i].data = data[i].city;
          delete data[i].city;
        }
      }

      return Immutable.merge(state, {
        failure: false,
        isFetching: false,
        errorMessage: "",
        data
      });
    }
    case STATE_CITY.FAILURE:
      return Immutable.merge(state, {
        failure: true,
        isFetching: false,
        errorMessage: action.errorMessage
      });

    default:
      return state;
  }
};

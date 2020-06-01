// @flow
import _ from "lodash";
import Immutable from "seamless-immutable";

import {
  ADD_ITEM,
  UPDATE_ITEM,
  DELETE_ITEM,
  UPDATE_ORDER_INFO,
  SAVE_VEHICLE_ORDER,
  SAVE_DELIVERY_PROFESSIONAL_ORDER,
  RESET_ORDER_INFO
} from "../actions/ActionTypes";

const initialState = Immutable({
  items: []
});

export default (state: Object = initialState, action: Object) => {
  switch (action.type) {
    case ADD_ITEM: {
      const newData = _.cloneDeep(state.items);
      newData.push(action.data);
      return Immutable.merge(state, {
        items: newData
      });
    }
    case UPDATE_ITEM: {
      const newData = _.cloneDeep(state.items);
      newData[action.index] = action.data;

      return Immutable.merge(state, {
        items: newData
      });
    }
    case DELETE_ITEM: {
      const newData = _.cloneDeep(state.items);
      newData.splice(action.index, 1);
      return Immutable.merge(state, {
        items: newData
      });
    }
    case UPDATE_ORDER_INFO: {
      return Immutable.merge(state, {
        info: { ...state.info, ...action.data }
      });
    }
    case SAVE_VEHICLE_ORDER: {
      return Immutable.merge(state, {
        info: { ...state.info, ...action.orderInfo },
        vehicle: action.vehicle
      });
    }
    case SAVE_DELIVERY_PROFESSIONAL_ORDER: {
      return Immutable.merge(state, {
        info: { ...state.info, ...action.orderInfo },
        deliveryProfessional: action.deliveryProfessional
      });
    }
    case RESET_ORDER_INFO: {
      return initialState;
    }
    default:
      return state;
  }
};

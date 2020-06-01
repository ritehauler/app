// @flow
import {
  ADD_ITEM,
  UPDATE_ITEM,
  DELETE_ITEM,
  UPDATE_ORDER_INFO,
  SAVE_VEHICLE_ORDER,
  SAVE_DELIVERY_PROFESSIONAL_ORDER,
  RESET_ORDER_INFO
} from "./ActionTypes";

export function addItem(data: Object) {
  return {
    data,
    type: ADD_ITEM
  };
}

export function updateItem(data: Object, index: Number) {
  return {
    data,
    index,
    type: UPDATE_ITEM
  };
}

export function deleteItem(index: Number) {
  return {
    index,
    type: DELETE_ITEM
  };
}

export function updateOrderInfo(data: Object) {
  return {
    data,
    type: UPDATE_ORDER_INFO
  };
}

export function resetOrderInfo() {
  return {
    type: RESET_ORDER_INFO
  };
}

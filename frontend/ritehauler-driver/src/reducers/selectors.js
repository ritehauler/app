import {
  ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
  STATUS_ON_THE_WAY,
  USER_ENTITY_TYPE_ID
} from "../constant";
// Define state selectors here
export const getUser = state => state.user;
export const getTodaysOrders = state => state.todaysOrders.data;
export const getAssignedOrders = state => state.assignedOrders.data;

export const getOnTheWayPayload = state => {
  return {
    entity_type_id: ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
    order_status: STATUS_ON_THE_WAY,
    order_id: state.todaysOrders.data[0].entity_id,
    driver_id: state.user.data.entity_id,
    login_entity_id: state.user.data.entity_id,
    login_entity_type_id: USER_ENTITY_TYPE_ID,
    home: true
  };
};

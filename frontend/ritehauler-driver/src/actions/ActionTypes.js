// @flow
const REQUEST = "REQUEST";
const SUCCESS = "SUCCESS";
const FAILURE = "FAILURE";
const CANCEL = "CANCEL";

function createRequestTypes(base) {
  const res = {};
  [REQUEST, SUCCESS, FAILURE, CANCEL].forEach(type => {
    res[type] = `${base}_${type}`;
  });
  return res;
}

// Network info
export const NETWORK_INFO = "NETWORK_INFO";

// user action types
export const USER = createRequestTypes("USER");
export const CHANGE_FORGOT_PASSWORD = createRequestTypes(
  "CHANGE_FORGOT_PASSWORD"
);
export const LOGOUT = createRequestTypes("LOGOUT");
export const EMPTY = createRequestTypes("EMPTY");

// location action types
export const CURRENT_LOCATION = "CURRENT_LOCATION";
export const TRACKING = "TRACKING";
export const START_BACKGROUND_LOCATION = "START_BACKGROUND_LOCATION";
export const STOP_BACKGROUND_LOCATION = "STOP_BACKGROUND_LOCATION";
export const ORDER_DRIVER_TRACKING = createRequestTypes(
  "ORDER_DRIVER_TRACKING"
);

// upload image
export const ATTACHMENT_REQUEST = "ATTACHMENT_REQUEST";
export const ATTACHMENT_UPDATE = "ATTACHMENT_UPDATE";

// add item
export const ITEM_NAME = createRequestTypes("ITEM_NAME");
export const ITEM_BOX = createRequestTypes("ITEM_BOX");
export const RESET_ITEM_BOX = "RESET_ITEM_BOX";
export const ADD_ITEM = "ADD_ITEM";
export const UPDATE_ITEM = "UPDATE_ITEM";
export const DELETE_ITEM = "DELETE_ITEM";
export const UPDATE_ORDER_INFO = "UPDATE_ORDER_INFO";
export const RESET_ORDER_INFO = "RESET_ORDER_INFO";
export const ADD_EXTRA_ITEM = createRequestTypes("ADD_EXTRA_ITEM");
export const VERIFY_VOLUME = createRequestTypes("VERIFY_VOLUME");
export const CLEAR_VERIFY_VOLUME = "CLEAR_VERIFY_VOLUME";
export const ORDER_ITEMS = createRequestTypes("ORDER_ITEMS");

// orders
export const MY_ORDERS = createRequestTypes("MY_ORDERS");
export const ORDER_DETAILS = createRequestTypes("ORDER_DETAILS");
export const ASSIGNED_ORDERS = createRequestTypes("ASSIGNED_ORDERS");
export const CLEAR_ASSIGNED_ORDERS = createRequestTypes(
  "CLEAR_ASSIGNED_ORDERS"
);
export const UPDATE_ASSIGNED_ORDER_STATUS = createRequestTypes(
  "UPDATE_ASSIGNED_ORDER_STATUS"
);
export const LOCALLY_UPDATE_ASSIGNED_ORDER_STATUS =
  "LOCALLY_UPDATE_ASSIGNED_ORDER_STATUS";
export const TODAYS_ORDERS = createRequestTypes("TODAYS_ORDERS");
export const PENDING_ORDERS = createRequestTypes("PENDING_ORDERS");

// ratings
export const RATINGS_LIST = createRequestTypes("RATINGS_LIST");
export const SUBMIT_RATINGS = createRequestTypes("SUBMIT_RATINGS");

// detail card
export const SHOW_CARD = "SHOW_CARD";
export const HIDE_CARD = "HIDE_CARD";

// map actions
export const FOCUS_MAP = "FOCUS_MAP";
export const MAP_ROUTE = createRequestTypes("MAP_ROUTE");
export const MAP_ROUTE_RESET = "MAP_ROUTE_RESET";

// statistics actions
export const STATISTICS = createRequestTypes("STATISTICS");
export const WEEKLY_STATISTICS = createRequestTypes("WEEKLY_STATISTICS");
export const ORDER_STATUSES = createRequestTypes("ORDER_STATUSES");

// duty toggle actions
export const DUTY_TOGGLE = "DUTY_TOGGLE";
export const GENERAL_SETTINGS = createRequestTypes("GENERAL_SETTINGS");
export const INCREMENT_PENDING_ORDER_COUNT = "INCREMENT_PENDING_ORDER_COUNT";
export const DECREMENT_PENDING_ORDER_COUNT = "DECREMENT_PENDING_ORDER_COUNT";

// CONTENT
export const CMS_CONTENT = createRequestTypes("CMS_CONTENT");
export const RESET_CMS_CONTENT = "RESET_CMS_CONTENT";

// NOTIFICATIONS LISTING
export const NOTIFICATION_LISTING = createRequestTypes("NOTIFICATION_LISTING");
export const RESET_NOTIFICATION_LISTING = "RESET_NOTIFICATION_LISTING";

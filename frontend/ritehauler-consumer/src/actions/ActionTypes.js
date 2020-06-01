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

// User authentication
export const USER = createRequestTypes("USER");
export const USER_EDIT = createRequestTypes("USER_EDIT");
export const CHANGE_PASSWORD = createRequestTypes("CHANGE_PASSWORD");
export const LOGOUT = createRequestTypes("LOGOUT");
export const UPDATE_TOKEN = createRequestTypes("UPDATE_TOKEN");

// ORDER PLACE
export const ITEM_NAME = createRequestTypes("ITEM_NAME");
export const ATTACHMENT_REQUEST = "ATTACHMENT_REQUEST";
export const ATTACHMENT_UPDATE = "ATTACHMENT_UPDATE";
export const ITEM_BOX = createRequestTypes("ITEM_BOX");
export const RESET_ITEM_BOX = "RESET_ITEM_BOX";
export const ADD_ITEM = "ADD_ITEM";
export const UPDATE_ITEM = "UPDATE_ITEM";
export const DELETE_ITEM = "DELETE_ITEM";
export const UPDATE_ORDER_INFO = "UPDATE_ORDER_INFO";
export const RESET_ORDER_INFO = "RESET_ORDER_INFO";
export const SUGGESTED_VEHICLES = createRequestTypes("SUGGESTED_VEHICLES");
export const DELIVERY_PROFESSIONALS = createRequestTypes(
  "DELIVERY_PROFESSIONALS"
);
export const SAVE_VEHICLE = createRequestTypes("SAVE_VEHICLE");
export const SAVE_PROFESSIONAL = createRequestTypes("SAVE_PROFESSIONAL");
export const SAVE_VEHICLE_ORDER = createRequestTypes("SAVE_VEHICLE_ORDER");
export const SAVE_DELIVERY_PROFESSIONAL_ORDER = createRequestTypes(
  "SAVE_DELIVERY_PROFESSIONAL_ORDER"
);
export const ORDER_PLACE = createRequestTypes("ORDER_PLACE");

// ORDER LISTING
export const ORDER_LISTING = createRequestTypes("ORDER_LISTING");
export const RESET_ORDER_LISTING = "RESET_ORDER_LISTING";
export const ORDER_DETAIL = createRequestTypes("ORDER_DETAIL");
export const RESET_ORDER_DETAIL = "RESET_ORDER_DETAIL";

// SET STATUS ORDER COMPLETE
export const ORDER_COMPLETE = createRequestTypes("ORDER_COMPLETE");

// SET STATUS ORDER COMPLETE
export const TRACK_ORDER = createRequestTypes("TRACK_ORDER");
export const RESET_TRACK_ORDER = "RESET_TRACK_ORDER";

// ORDER RATING
export const RATING_OPTIONS = createRequestTypes("RATING_OPTIONS");
export const RATE_DRIVER = createRequestTypes("RATE_DRIVER");

// DRIVER PROFILE
export const DRIVER_PROFILE = createRequestTypes("DRIVER_PROFILE");
export const RESET_DRIVER_PROFILE = "RESET_DRIVER_PROFILE";

// NOTIFICATIONS LISTING
export const NOTIFICATION_LISTING = createRequestTypes("NOTIFICATION_LISTING");
export const UPDATE_NOTIFICATION_TOGGLE = createRequestTypes(
  "UPDATE_NOTIFICATION_TOGGLE"
);
export const RESET_NOTIFICATION_LISTING = "RESET_NOTIFICATION_LISTING";
export const SET_NOTIFICATION_INFO = "SET_NOTIFICATION_INFO";
export const UPDATE_NOTIFICATION_LIST = "UPDATE_NOTIFICATION_LIST";
export const UPDATE_NOTIFICATION_STATUS = "UPDATE_NOTIFICATION_STATUS";

// GENERAL SETTINGS
export const GENERAL_SETTINGS = createRequestTypes("GENERAL_SETTINGS");

// CONTENT
export const CMS_CONTENT = createRequestTypes("CMS_CONTENT");
export const RESET_CMS_CONTENT = "RESET_CMS_CONTENT";

// CARD ACTIONS
export const CARD_LISTING = createRequestTypes("CARD_LISTING");
export const ADD_CARD = createRequestTypes("ADD_CARD");
export const DELETE_CARD = createRequestTypes("DELETE_CARD");
export const RESET_CARD_LIST = "RESET_CARD_LIST";

// CHARGE CARD
export const CHARGE_CARD = createRequestTypes("CHARGE_CARD");

// LOCATIONS
export const STATE_CITY = createRequestTypes("STATE_CITY");
export const RECENT_LOCATIONS = createRequestTypes("RECENT_LOCATIONS");

// GOOGLE API ACTION
export const GOOGLE_NEARBY = createRequestTypes("GOOGLE_NEARBY");
export const GOOGLE_PLACES = createRequestTypes("GOOGLE_PLACES");
export const GOOGLE_PLACE_DETAIL = "GOOGLE_PLACE_DETAIL";

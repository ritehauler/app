import { Metrics } from "../theme";
import Utils from "../util";
export const BACK_SCENES = "login|_home";
export const ORDER_STATUS = {
  PROCESSING: "processing",
  ASSIGNED: "assigned",
  PICK_UP: "pick_up",
  PICKED: "picked",
  STARTED: "started",
  DELIVERED: "delivered",
  ENDED: "ended"
};
export const RIDE_MODE = {
  COMBINED: "combined",
  DRIVING: "driving",
  WALKING: "walking"
};
export const PAYMENT_METHOD = {
  BITCOIN: "bitcoin",
  CREDIT_CARD: "creditCard",
  COD: "COD"
};
export const PRICE_RANGE = {
  MIN_VALUE: 5,
  MAX_VALUE: 3000,
  STEP_VALUE: 5,
  FACTOR_VALUE: 500
};
export const PERFORMANCE_TYPE = {
  WEEKLY: "weekly",
  DAILY: "daily"
};
export const PERFORMANCE_VALUE = {
  WEEKLY: 7,
  DAILY: 1
};
export const PERFORMANCE_KEY = {
  ORDERS_COMPLETED: "deliveries_completed",
  ORDERS_CANCELED: "deliveries_canceled",
  HOURS_ONLINE: "attendance_seconds",
  COMMISSION_EARNED: "commission_earned",
  RATING: "deliveries_rating",
  DELIVERIES_PERCENTAGE: "completed_percentage"
};
export const PERFORMANCE_KEY_VALUE = {
  ORDERS_COMPLETED: "Orders",
  ORDERS_CANCELED: "Drive Cancel",
  HOURS_ONLINE: "Hours Online",
  COMMISSION_EARNED: "Earned",
  RATING: "Rating",
  DELIVERIES_PERCENTAGE: "Deliveries"
};
export const ORDER_HISTORY_STATUS = {
  COMPLETED: "completed"
};
export const MENU_TYPE = {
  STATS: "stats",
  ORDER_HISTORY: "order_history",
  WALLET: "wallet",
  DUTY: "duty",
  ABOUT: "about",
  CUSTOMER_SERVICE: "customer_service",
  LOGOUT: "Logout"
};

export const TIME = {
  HOURS: 60 * 60,
  MINUTES: 60
};

export const UNIT_METER = {
  MILES: 1609.34,
  KM: 1000
};

export const STUCK_ASSIGNMENT_MODE = {
  AUTOMATIC: "automatic",
  MANUAL: "manual"
};

export const WORKER_TYPE = {
  AGENT: "agent",
  DRIVER: "driver"
};

export const MAP_DELTAS = {
  latitudeDelta: 0.0272457629830285,
  longitudeDelta: 0.030134618282318115
};

export const MAX_ZOOM_LEVEL = 18;

export const EDGE_PADDING = {
  top: Metrics.ratio(20),
  right: Metrics.ratio(20),
  bottom: Utils.isPlatformAndroid()
    ? Metrics.screenHeight / 1.2
    : Metrics.screenHeight / 1.5,
  left: Metrics.ratio(20)
};

// FLAT_LIST
export const PAGE_SIZE = 20;
export const FLAT_LIST_ON_END_REACHED_THRESHOLD = 0.2;
export const INITIAL_NUMBER_TO_RENDER = 4;

// EXPENSIVE ITEM
export const EXPENSIVE_ITEM_MINIMUM_PRICE = 100;

// IMAGE
export const IMAGE_QUALITY = 1;
export const IMAGE_MAX_WIDTH = 720;
export const IMAGE_MAX_HEIGHT = 480;
export const IMAGE_COMPRESS_MAX_WIDTH = 720;
export const IMAGE_COMPRESS_MAX_HEIGHT = 480;
export const IMAGE_COMPRESS_FORMAT = "JPEG";

// GRADIENT
export const GRADIENT_START = { x: 0.0, y: 0.0 };
export const GRADIENT_END = { x: 0.5, y: 0.0 };

// entity type ids
export const USER_ENTITY_TYPE_ID = 3;
//
export const IS_AUTH_EXISTS=0;
export const IS_TEMP_PASSWORD=0;
export const ENTITY_TYPE_ID_ITEM = 14;
export const ENTITY_TYPE_ID_MY_ORDERS = 15;
export const ENTITY_TYPE_ID_UPDATE_ORDER_STATUS = 68;
export const ENTITY_TYPE_ORDER_ITEM = 16;
export const ENTITY_TYPE_ID_CMS = 24;

// order status these are what we receive on response
export const DRIVER_ASSIGNED = "Driver Assigned";
export const DRIVER_ACCEPTED = "Driver Accepted";
export const DRIVER_ARRIVED = "Driver Arrived";
export const ON_THE_WAY = "On the way";
export const COMPLETED = "Completed";
export const DRIVER_REACHED = "Driver Reached";
export const DRIVER_CANCELLED = "Driver Cancelled";
export const DRIVER_DECLINED = "Driver Declined";

// dummy status for transition between on the way to reach
export const ABOUT_TO_REACH = "ABOUT_TO_REACH";

// these statuses are what we send to api when updating status
export const STATUS_ACCEPTED = "accepted";
export const STATUS_ON_THE_WAY = "on_the_way";
export const STATUS_ARRIVED = "arrived";
export const STATUS_REACHED = "reached";
export const STATUS_DECLINED = "declined";

// DATE FORMAT
export const DATE_FORMAT = "YYYY-MM-DD";
export const DISPLAY_DATE_FORMAT = "D MMMM YYYY HH:mm:ss";
export const SHORT_DISPLAY_DATE_FORMAT = "D MMM YYYY";
export const GMT_TIME_FORMAT = "YYYY-MM-DD HH:mm:ss";
export const SUMMARY_DATE_FORMAT = "D MMMM YYYY, HH:mm:ss";

// TIMER
export const AUTO_DECLINE_PERIOD = 5; // in minutes
export const LOCATION_COLLECTION_PERIOD = 3000; // in milliseconds

// CUSTOMER LOGIN PLATFORM TYPE
export const PLATFORM_TYPE_CUSTOM = "custom";

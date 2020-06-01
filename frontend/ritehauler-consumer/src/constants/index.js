// BACK SCENES
export const BACK_SCENES = "login|consumerLocation";

// TOOLBAR HEIGHT
export const TOOLBAR_HEIGHT = 76;

// IMAGE
export const IMAGE_QUALITY = 1;
export const IMAGE_MAX_WIDTH = 720;
export const IMAGE_MAX_HEIGHT = 480;
export const IMAGE_COMPRESS_MAX_WIDTH = 720;
export const IMAGE_COMPRESS_MAX_HEIGHT = 480;
export const IMAGE_COMPRESS_FORMAT = "JPEG";

// DATE TIME
export const DATE_FORMAT = "YYYY-MM-DD";
export const DATE_TIME_FORMAT = "YYYY-MM-DD HH:mm:ss";
export const DISPLAY_DATE_FORMAT = "MMM DD, YYYY";
export const DISPLAY_DATE_FORMAT_ORDER = "D MMMM YYYY";
export const DISPLAY_DATE_ORDER_FORMAT = "DD MMM";
export const TIME_FORMAT = "HH:mm";
export const TIME_FORMAT_FULL = "HH:mm:ss";
export const DISPLAY_TIME_FORMAT = "hh:mm a";
export const DISPLAY_DATE_TIME_FORMAT = "dddd, D MMM YYYY";
export const DISPLAY_DATE_TIME_FORMAT_ORDER = "D MMMM YYYY, HH:mm:ss";
export const DISPLAY_DATE_TIME_FORMAT_ORDER_ITEM = "DD MMM, hh:mm a";
export const DEFAULT_TIME = "00:00";

// FILTERS
export const MINIMUM_PRICE_FILTER = 1;
export const MAXIMUM_PRICE_FILTER = 1000;
export const PRICE_STEP_FILTER = 1;

// CONSUMER LOCATION
export const ADD_PICKUP_STATUS = "add_pickup";
export const CONFIRM_PICKUP_STATUS = "confirm_pickup";
export const ADD_DROPOFF_STATUS = "add_dropoff";
export const CONFIRM_DROPOFF_STATUS = "confirm_dropoff";
export const BOOK_STATUS = "book";
export const INITIAL_REGION_MAP = {
  latitude: 39.14772147905898,
  longitude: -101.37749075889587,
  latitudeDelta: 17.071557403153175,
  longitudeDelta: 16.685355082154274
};

// TRACK UPDATE TIME
export const TRACK_UPDATE_TIME = 10000;

// GRADIENT
export const GRADIENT_START = { x: 0.0, y: 0.0 };
export const GRADIENT_END = { x: 0.5, y: 0.0 };

export const LOCATION_TIME_OUT = 10000;
export const LOCATION_MAX_AGE = 1000;
export const LOCATION_DISTANCE_FILTER = 10;
export const LOCATION_HIGH_ACCURACY = true;

// GOOGLE API KEY
export const GOOGLE_API_KEY = "AIzaSyBQKtTe8YwcJQeYKfRdYHrEEzMmQX4vNS4";
// export const STRIPE_PUBLISHABLE_KEY = "pk_test_ECb7molrbIqIFvOvuSPIpsn6"; // debug
export const STRIPE_PUBLISHABLE_KEY = "pk_live_OGuFtPE2yTsqVJAbZXHRcuXE"; // live
export const GOOGLE_RADIUS_NEAR = 10000;
export const GOOGLE_RADIUS_CITY = 25000;

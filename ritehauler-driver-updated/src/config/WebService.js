// Live bas url
export const BASE_URL = "https://admin.ritehauler.com";

// QA base url
// export const BASE_URL = "https://cubixsource.com/web/ritehauler";

// Staging base url
// export const BASE_URL = "https://cubixsource.com/staging/ritehauler";

export const GOOGLE_BASE_URL = "https://maps.googleapis.com";

export const GOOGLE_API_KEY = "AIzaSyApwqnHeqmr46j7i5YM3Ph8podnyDA2nIo";

// GOOGLE API ROUTES
export const API_GOOGLE_DIRECTION = "/maps/api/directions/json";

export const API_USER_NAME = "cubixapiuser";
export const API_PASSWORD = "apipass123";
export const API_TIMEOUT = 60000;

export const API = "/api";

// API ORDER/DRIVER TRACKING
export const API_ORDER_DRIVER_TRACKING = `${API}/system/driver/update_location`;

// API USER ROUTES
export const API_ENTITY_AUTH = `${API}/entity_auth`;
export const API_ENTITY_AUTH_EMAIL_LOGIN = `${API_ENTITY_AUTH}/signin`;
export const API_ENTITY_AUTH_LOGOUT = `${API_ENTITY_AUTH}/logout`;
export const API_ENTITY_AUTH_FORGOT_PASS = `${API_ENTITY_AUTH}/forgot_request`;
export const API_ENTITY_AUTH_CHANGE_PASS = `${API_ENTITY_AUTH}/change_password`;

//sign up 
export const API_ENTITY_AUTH_EMAIL_SIGNUP= `${API_ENTITY_AUTH}/signup_new`;

// API UPLOAD ATTACHMENT
export const API_UPLOAD_IMAGE = `${API}/system/item/uplaod_image`;

// ADD ITEM API
export const API_ENTITY_LISTING = `${API}/system/entities/listing`;
export const API_ITEM_BOX = `${API}/system/item_box`;
export const API_ADD_EXTRA_ITEM = `${API}/system/order/add_extra_item`;
export const API_VERIFY_EXTRA_ITEM = `${API}/system/order/verify_extra_item`;

// ORDERS
export const API_ASSIGNED_ORDERS = `${API}/system/order/assigned`;
export const API_UPDATE_ASSIGNED_ORDER_STATUS = `${API}/system/entities`;
export const API_TODAYS_ORDERS = `${API}/system/order/current`;
export const API_PENDING_ORDERS = `${API}/system/order/pending`;

// API RATING
export const API_RATINGS_LIST = `${API}/system/attribute_option/listing`;
export const API_RATE_CUSTOMER = `${API}/extension/social/package/rate`;

// API STATISTICS
export const API_STATISTICS = `${API}/system/driver/stats`;
export const API_WEEKLY_STATS = `${API}/system/driver/weekly_stats`;

// API MY ORDERS HISTORY
export const API_ORDER_HISTORY = `${API}/system/driver/order_search`;
export const API_ORDER_STATUSES = `${API}/system/driver/order_status`;

// API DUTY TOGGLE
export const API_DUTY_TOGGLE = `${API}/system/entities/update`;

// API GENERAL SETTINGS
export const API_GENERAL_SETTINGS = `${API}/system/driver/general_setting`;

// API CMS CONTENT
export const API_CMS_CONTENT = `${API}/system/entities/listing`;

// CMS SLUGS
export const CMS_SLUG_ABOUT = "about";
export const CMS_PRIVACY_POLICY = "privacy_policy";
export const CMS_TERMS_OF_SERVICES = "terms_of_services";

export const API_NOTIFICATION_LISTING = `${API}/system/notification/list`;

export const API_LOG = false;

export const ERROR_SOMETHING_WENT_WRONG = {
  message: "errorSomethingWentWrong",
  error: 1
};
export const ERROR_NETWORK_NOT_AVAILABLE = {
  message: "networkNotAvailable",
  error: 1
};
//signup 
export const ERROR_KICK_USER = {
  message: "Error",
  error: 1
};

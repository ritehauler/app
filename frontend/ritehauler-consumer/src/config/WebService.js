import { Strings } from "../theme";

// Base url
//export const BASE_URL = "http://cubixsource.com/staging/ritehauler";
//export const BASE_URL = "http://cubixsource.com/web/ritehauler";
export const BASE_URL = "https://admin.ritehauler.com";

// api credentials
export const API_USER_NAME = "cubixapiuser";
export const API_PASSWORD = "apipass123";
export const API_TIMEOUT = 30000;

export const API = "/api/";

// entity ids
export const ENTITY_TYPE_ID_CUSTOMER = "11";
export const ROLL_ID = 3;
export const ENTITY_TYPE_ID_ITEM = "14";
export const ENTITY_TYPE_ID_DELIVERY_PROFESSIONAL = "58";
export const ENTITY_TYPE_ID_ORDER = "15";
export const ENTITY_TYPE_ID_ORDER_ITEM = "16";
export const ENTITY_TYPE_ID_CMS = "24";
export const ENTITY_TYPE_REVIEW_LIST = "driver_review";

// API USER ROUTES
export const API_USER_SIGN_IN = `${API}entity_auth/signin`;
export const API_USER_SIGN_UP = `${API}entity_auth`;
export const API_VERIFY_NUMBER = `${API}entity_auth/verify_phone`;
export const API_RESEND_CODE = `${API}entity_auth/resend_code`;
export const API_FORGOT_PASSWORD = `${API}entity_auth/forgot_request`;
export const API_SOCIAL_LOGIN = `${API}entity_auth/social_login`;
export const API_UPDATE_PHONE_NUMBER = `${API}entity_auth/change_id_request`;
export const API_LOGOUT = `${API}entity_auth/logout`;
export const API_EDIT_PROFILE = `${API}system/entities/update`;
export const API_CHANGE_PASSWORD = `${API}entity_auth/change_password`;
export const API_UPDATE_TOKEN = `${API}entity_auth/save_token`;

// ORDER PLACE API'S
export const API_ITEM_BOX = `${API}system/item_box`;
export const API_ITEM_NAMES_LIST = `${API}system/entities/listing`;
export const API_UPLOAD_IMAGE = `${API}system/item/uplaod_image`;
export const API_SUGGESTED_VEHICLES = `${API}system/truck/suggest`;
export const API_DELIVERY_PROFESSIONALS = `${API}system/entities/listing`;
export const API_SAVE_VEHICLE = `${API}system/truck/save_selected`;
export const API_SAVE_PROFESSIONAL = `${API}system/truck/save_professional`;
export const API_ORDER_PLACE = `${API}system/entities`;

// ORDER LISTING
//export const API_ORDER_LISTING = `${API}system/entities/listing`;
export const API_ORDER_LISTING = `${API}system/order/search`;
export const API_ORDER_DETAIL = `${API}system/entities`;

// RATING
export const API_RATING_LIST = `${API}system/attribute_option/listing`;
export const API_RATE_DRIVER = `${API}extension/social/package/rate`;

// DRIVER PROFILE
export const API_DRIVER_PROFILE = `${API}system/driver/profile`;

// NOTIFICATION LISTING
export const API_NOTIFICATION_LISTING = `${API}system/notification/list`;
export const API_UPDATE_NOTIFICATION_READ_STATUS = `${API}system/notification/update-status`;
export const API_UPDATE_NOTIFICATION_TOGGLE = `${API}system/entities/update`;

// GENERAL SETTINGS
export const API_GENERAL_SETTINGS = `${API}system/customer/general_setting`;

// API VERIFICATION MODE
export const API_VERIFICATION_MODE_SIGN_UP = "signup";
export const API_VERIFICATION_MODE_CHANGE_NUMBER = "change_mobile_no";

// API CMS CONTENT
export const API_CMS_CONTENT = `${API}system/entities/listing`;

// API ORDER COMPLETE
export const API_ORDER_COMPLETE = `${API}system/order/complete`;

// API TRACK ORDER
export const API_TRACK_ORDER = `${API}system/order/track_driver`;

//  API CARDS
export const API_CARD_LISTING = `${API}system/payments/ListCards`;
export const API_ADD_CARD = `${API}system/payments/addCard`;
export const API_DELETE_CARD = `${API}system/payments/deleteCard`;
export const API_CHARGE_CARD = `${API}system/payments/chargePayment`;

// ORDER STATUS
export const ORDER_STATUS_PENDING = "pending";
export const ORDER_STATUS_ON_THE_WAY = "on_the_way";
export const ORDER_STATUS_CONFIRMED = "confirmed";
export const ORDER_STATUS_CANCELLED = "cancelled";
export const ORDER_STATUS_DRIVER_CANCELLED = "driver_cancelled";
export const ORDER_STATUS_PAYMENT_REQUIRED = "reached";
export const ORDER_STATUS_COMPLETE = "completed";
export const ORDER_STATUS_DRIVER_ARRIVED = "arrived";

// CARD TYPES
export const CARD_TYPE_VISA = "Visa";
export const CARD_TYPE_JCB = "JCB";
export const CARD_TYPE_DinersClub = "Diners Club";
export const CARD_TYPE_Discover = "Discover";
export const CARD_TYPE_AmericanExpress = "American Express";
export const CARD_TYPE_MasterCard = "MasterCard";

// NOTIFICATION IDENTIFIER CUSTOM
export const NOTIFICATION_IDENTIFIER_CUSTOM = "custom_notification";
export const NOTIFICATION_IDENTIFIER_ORDER_DETAIL = "order_detail";

// CMS
export const CMS_SLUG_ABOUT = "about";
export const CMS_PRIVACY_POLICY = "privacy_policy";
export const CMS_TERMS_OF_SERVICES = "terms_of_services";

// STATE CITIES
export const API_STATE_CITY = `${API}system/city/listing`;
export const API_RECENT_LOCATION_CITY = `${API}system/city/recent`;

// GOOGLE API
export const NEARBY_GOOGLE_API = "/maps/api/place/nearbysearch/json";
export const PLACES_GOOGLE_API = "/maps/api/place/autocomplete/json";
export const PLACE_DETAIL_GOOGLE_API = "/maps/api/place/details/json";

// PLATFORM TYPE
export const PLATFORM_TYPE_FACEBOOK = "facebook";
export const PLATFORM_TYPE_CUSTOM = "custom";

// api show logs boolean
export const API_LOG = false;

// Error messages
export const ERROR_SOMETHING_WENT_WRONG = {
  error: 1,
  message: Strings.errorMessageSomethingWentWrong
};
export const ERROR_NETWORK_NOT_AVAILABLE = {
  error: 1,
  message: Strings.noInternetMessage
};
export const ERROR_KICK_USER = {
  error: 1,
  message: Strings.errorMessageAccountSuspended
};

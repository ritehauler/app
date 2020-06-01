<?php
/*
 * ---------------------------------------------------------------
 *  Custom Constants
 * ---------------------------------------------------------------
 */
set_time_limit(0);
// App constants
define('APP_NAME', 'Rite Hauler'); // app name
// HTTP Protocol
define('HTTP_TYPE', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://');

// Master Database Constants
if (preg_match('/localhost/', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : exec("hostname"))) {
    // app dir details
    define('APP_ALIAS', ''); // app dir name
    define('ADD_PATH', 'ritehaluer/'); // preceding path to app dir
    //  for saving cookies
    define('APP_DOMAIN', preg_match('/Chrome\/|MSIE/', @$_SERVER["HTTP_USER_AGENT"]) ? '' : $_SERVER['HTTP_HOST']); // chrome/IE cookie fix on local host

    // db details
    define('MASTER_DB_HOST', 'localhost');
    define('MASTER_DB_USER', 'root');
    define('MASTER_DB_PASS', '');
    define('MASTER_DB_NAME', 'ritehauler_db');
    define('MASTER_DB_PREFIX', '');
} else {
    // app dir details
    define('APP_ALIAS', ''); // app dir name
    define('ADD_PATH', '/'); // preceding path to app dir
    define('APP_DOMAIN', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : exec("hostname")); //  for saving cookies
    // db details
    define('MASTER_DB_HOST', 'localhost');
    define('MASTER_DB_USER', 'dbuser_ritehauler');
    define('MASTER_DB_PASS', 'do0xo6NU8rite18&');
    define('MASTER_DB_NAME', 'db_ritehauler');
    define('MASTER_DB_PREFIX', '');
}
// Slave Database Constants
define('SLAVE_DB_HOST', MASTER_DB_HOST);
define('MYSQL_PORT', 3306);

// Cache constants
define('CACHE_ON', FALSE); // bool : TRUE = on | FALSE = off
define('CACHE_DRIVER', 'file'); // memcache, memcached, file, apc, dummy
define('CACHE_LIFE', (60 * 24)); // one day (in minutes)

// Memcache Constants (if enabled)
define('MEMCACHE_HOST', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : exec("hostname"));
define('MEMCACHE_PORT', 11211);
define('MEM_KEY', strtoupper(str_replace(array(" ", "."), '-', APP_NAME)) . '-');

// Session Constants
define('ADMIN_SESS_KEY', strtolower(MEM_KEY) . 'admin-');
define('REMEMBER_COOKIE_TIME', (3600 * 24 * 30)); // 30 days;
define('USER_SESS_KEY', strtolower(MEM_KEY) . 'user-');
define('BRAND_SESS_KEY', strtolower(MEM_KEY) . 'brand-');
define('API_SESS_KEY', strtolower(MEM_KEY) . 'api-');
define('API_USER_SESS_KEY', strtolower(MEM_KEY) . 'api_user-');


// Salt for password security
define('ADMIN_SALT', 'Xx@#_Ww-Oo');
define('API_USER_SALT', 'Xx@#_Aa-Oo');
define('API_SALT', 'Aas--dD-');
define('BRAND_SALT', 'Xi@#_xw-Oo');
//define('CLIENT_TOKEN_SALT', 'cLt_t0k3n-xXwOo');
define('USER_SALT', 'Xx@#_Ww-Ox');
define('GROUP_SALT', 'Xx@#_Ww-Oi');
define('FORGOT_PASS_HASH_LENGTH', 20);

// directory constants
define('DIR_ADMIN', 'administrator/');
define('DIR_BRAND', 'brand/');
define('DIR_BRAND_PANEL', 'brand_panel/');
define('DIR_API', 'api/');
define('DIR_IMG', 'public/images/');
define('DIR_FILES', 'public/files/');
define('DIR_RAW_FILES', 'public/raw_files/');
define('DIR_PROFILE_IMG', DIR_FILES . 'profile/');
define('DIR_CATEGORY_IMG', DIR_FILES . 'category/');
define('DIR_BANNER_IMG', DIR_FILES . 'banner/');
define('DIR_USER_IMG', DIR_FILES . 'user/');
define('DIR_DISH', DIR_FILES . 'media/');
define('DIR_WATERMARK', DIR_FILES . 'watermark/');
define("DIR_SYSTEM","system/");
define('DIR_CRON', 'cron/');

// Paging constants
define("PAGE_LIMIT_ADMIN", 25);
define("PAGE_LIMIT_API", 10);
define("PAGE_LIMIT", 10);

// date formats
define('DATE_TIME_FORMAT_DB', 'Y-m-d H:i:s');
define('DATE_FORMAT_DB', 'Y-m-d');

define('DATE_FORMAT_ADMIN', 'd M Y');
define('DATE_GRAPH', date("Y-m"));
define('DATE_TIME_FORMAT_ADMIN', 'd M Y g:i A');
//define('TIME_FORMAT_ADMIN', 'g:i A');
define('TIME_FORMAT_ADMIN', 'H:i:s');

// other constants
ini_set('session.use_trans_sid', 1);
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');


// mail server configuration
/*define('MAIL_DRIVER', 'mail'); // "smtp", "mail", "sendmail", "mailgun", "mandrill", "log"
define('MAIL_HOST', '');
define('MAIL_PORT', NULL);
define('MAIL_USERNAME', "");
define('MAIL_PASSWORD', "");*/

define('MAIL_DRIVER', 'smtp'); // "smtp", "mail", "sendmail", "mailgun", "mandrill", "log"
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 465);
define('MAIL_USERNAME', "testsmtp@cubixlabs.com");
define('MAIL_PASSWORD', "smtp@123");




// html5 webkit support for IE
header('X-UA-Compatible: IE=edge,chrome=1');

// version control for cache
define('VERSION', 0);

define('DO_URL_REWRITE', TRUE); // bool : TRUE = on | FALSE = off
define('SOFT_DELETE', TRUE);

// NodeJS configuration
define('NODEJS_PORT', 3002);

// push notification
//define('PN_IOS_FILE', "ck.pem");
//define('PN_IOS_URL', "ssl://gateway.push.apple.com:2195");
define('MASS_PN_CODE', 99); // code for sending mass notification
//define('PN_IOS_FILE', "dev.pem");
//define('PN_IOS_URL', "ssl://gateway.sandbox.push.apple.com:2195");

// APNS vars
define('APNS_SANDBOX_FILE', "cert/dev.pem");
define('APNS_PRODUCTION_FILE', "cert/ck.pem");
define('APNS_SANDBOX_URL', "ssl://gateway.sandbox.push.apple.com:2195");
define('APNS_PRODUCTION_URL', "ssl://gateway.push.apple.com:2195");
//define('ANDROID_PUSH_URL', "https://android.googleapis.com/gcm/send"); // GCM
define('ANDROID_PUSH_URL', "https://fcm.googleapis.com/fcm/send"); // FCM


// API Access
//define('API_ACCESS_URL', "http://198.20.103.178/api/"); //
//define('API_ACCESS_URL', "http://cubixcube.com/".ADD_PATH."api");
//define('API_ACCESS_URL', "http://r4outdoors.com/api/");
define('API_ACCESS_EMAIL', "salman.khimani@cubixlabs.com");
define('API_ACCESS_PASS', "apipass123");
define('API_ACCESS_USER', "cubixapiuser");
define('API_BASIC_AUTH_ACTIVE', false);


define('APP_DEBUG', true);
define('APP_KEY', sha1(MEM_KEY));
// define('APP_TIMEZONE', 'GMT');
define('APP_TIMEZONE', 'GMT+0');

//define('DATE_GRAPH', date("Y-m"));

define('EXCLUDE_CSRF_ROUTES', DIR_API."*,facebook,facebook/login_redirect,facebook/set_token");

// Entity Constants
define("DIR_BACKEND","backend/");

// other
define("MOBILE_NO_PATTREN", '@^([0-9]+(-)[0-9]+)$@');

define("BASE_PATH","/ritehaluer/panel/");

define("DIR_DASHBOARD","dashboard/");
define("DIR_PANEL","panel/");
define('KANBAN_URL', 'http://192.168.12.74:3001/?');

/*ReCAPTCHA*/
define("IS_CAPTCHA",0);
define("CAPTCHA_VERIFY_URL","https://www.google.com/recaptcha/api/siteverify");
define("CAPTCHA_SECRET_KEY","'6Le07iwUAAAAAP3w20_kPjEr7FD2uZAyC-mCzPwS");
define("CAPTCHA_SITE_KEY","6Le07iwUAAAAACrzrOjD-LjQ2wkAnmsClaWCmHqV");
define("CAPTCHA_RESPONSE_FIELD","g-recaptcha-response");

define("IS_DEVELOPMENT",1);
define('HTTP_URL', 'http://localhost/ritehaluer/');

if (APP_DEBUG === TRUE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

?>

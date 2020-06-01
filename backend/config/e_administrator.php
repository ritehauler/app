<?php

return [
    "DIR_PANEL" => "administrator/",
    "SESS_KEY" => strtoupper("administrator") . "-",
    "DIR_IMG" => "public/files/administrator_img/",
    "HASH_PATTERN" => 'Xi@#_xw-Oo',
    "REMEMBER_COOKIE_TIME" => (3600 * 24 * 30),  // 30 days;
    "FORGOT_PASS_TOKEN_LENGTH" => 20,
    "SIGNUP_TOKEN_LENGTH" => 50,
    "SMS_TOKEN_LENGTH" => 6,
    "DATE_FORMAT" => "Y-m-d",
    // switches
    "SMS_AUTH_ENABLED" => true,
    "SMS_SIGNUP_ENABLED" => true,
    "EMAIL_AUTH_ENABLED" => true,
    "EMAIL_SIGNUP_ENABLED" => true,
];

<?php

return [
    //"DIR_PANEL" => "panel/",
    "DIR" => "panel/",
    "SESS_KEY" => strtoupper("panel_auth") . "-",
    "DIR_IMG" => "public/files/user_img/",
    "DIR_PANEL_RESOURCE" => "resources/assets/panel/",
    "SALT" => 'Xi@#_xw-Oo',
    "REMEMBER_COOKIE_TIME" => (3600 * 24 * 30),  // 30 days;
    "FORGOT_PASS_TOKEN_LENGTH" => 20,
    "SIGNUP_TOKEN_LENGTH" => 50,
    "SMS_TOKEN_LENGTH" => 6,
    'STATUSES' => array(
        0 => 'Inactive',
        1 => 'Active',
        2 => 'Ban'
    ),
    "DATE_FORMAT" => "Y-m-d",
    "SMS_AUTH_ENABLED" => true,
    "SMS_SIGNUP_ENABLED" => true,
    "EMAIL_AUTH_ENABLED" => true,
    "EMAIL_SIGNUP_ENABLED" => true,
    "SMS_SANDBOX_MODE" => false,
    'UNAUTH_PAGES' => array('logout' , 'login' , 'confirm_forgot' , 'forgot_thankyou' , 'confirm_signup' , 'signup_thankyou' , 'change_password', 'stripe' , 'stripe_post' , 'paypal' , 'getCheckout','getoptions','getRoleOptions','update_profile'),

];

<?php

return [
    'APP_NAME' => APP_NAME,
    'APP_ADMIN_NAME' => APP_NAME . ' :: Administrator :: ',
    'IMG_URL' => '',
    'CSS_URL' => '',
    'JS_URL' => 'resources/assets/js/',
    'FONTS_URL' => '',
    'ADMIN_IMG_URL' => 'resources/assets/' . DIR_ADMIN . 'img/',
    'ADMIN_IMG_URL' => 'resources/assets/' . DIR_ADMIN . 'img/',
    'ADMIN_CSS_URL' => 'resources/assets/' . DIR_ADMIN . 'css/',
    'ADMIN_JS_URL' => 'resources/assets/' . DIR_ADMIN . 'js/',
    'ADMIN_FONTS_URL' => 'resources/assets/' . DIR_ADMIN . 'fonts/',
    'BULK_UPLOAD_SAMPLE' => 'resources/assets/' . DIR_ADMIN . 'sample/',
    'PROFILE_IMAGE_URL' => 'public/profile/',
    'PROFILE_IMAGES_PATH' => 'public/profile/',
    'RESOURCES_PATH' => 'resources/',
    'ASSETS_PATH' => 'resources/assets/',
    'LOGO_PATH' => 'public/files/logo/',
    'BANNER_AD_PATH' => 'public/files/banner_ad/',
    'VIRTUAL_ITEM_PATH' => 'public/files/virtual_item/',
    'CSV_PATH' => 'public/files/csv/',
    'DIR_ADMIN' => DIR_ADMIN,
    'RAW_PATH' => 'public/files/raw/',
    'MASTER_ADMIN_ROUTES' => array(
        DIR_ADMIN . 'admin_group',
        DIR_ADMIN . 'admin',
        DIR_ADMIN . 'setting',
        DIR_ADMIN . 'admin_widget',
    ),
    'MOBILE_AD_TYPES' => array(
        'none' => 'None',
        'admob' => 'Admob',
        'custom' => 'Custom',
    ),
    'ADMIN_WIDGET_TYPES' => array(
        'chart' => 'Chart',
        'tile' => 'Tile',
        'map_chart' => 'Map Chart',
        'data_grid' => 'Data Grid',
        'bar_chart' => 'Bar Chart',
        'pie_chart' => 'Pie Chart',
        'donut_chart' => 'Donut Chart',
        'flot_chart' => 'Flot Chart',
        'stack_chart' => 'Stack Chart',
        'line_chart' => 'Line Chart',
        'radar_chart' => 'Radar Chart',
    ),
    'VIRTUAL_ITEM_TYPES' => array(
        'inapp' => 'In-app',
        'exchange' => 'Exchange',
    ),
    'ASSET_MANAGEMENT_PATH' => 'public/files/asset/',
    'ASSET_TYPES' => array(
        'image' => 'Image',
        'audio' => 'Audio',
        'video' => 'Video'
    ),
    'ADMIN_STATUSES' => array(
        0 => 'Inactive',
        1 => 'Active',
        2 => 'Ban'
    ),
    'LEVEL_TYPES' => array(
        'simulation' => 'Simulation',
        'runner' => 'Runner',
        'qa' => 'Q/A',
    ),
    'ACHIEVEMENT_TYPES' => array(
        'simulation' => 'Simulation',
        'runner' => 'Runner',
        'qa' => 'Q/A',
    ),
    'PRE_CHECK_TYPES' => array(
        'sql' => 'SQL Query',
        'levels' => 'Levels',
        'achievements' => 'Achievements',
    ),
    'POST_CHECK_TYPES' => array(
        'sql' => 'SQL Query',
        'levels' => 'Levels',
        'achievements' => 'Achievements',
    ),
    'ASSET_TYPES' => array(
        'image' => 'Image',
        'audio' => 'Audio',
        'video' => 'Video'
    ),

    'LOBBY_LIB_PATH' => 'app/Libraries/lobby/',
    'PLIST_LIB_PATH' => 'app/Libraries/CFPropertyList/',
    'SE_RESOURCE_TYPES' => array('text', 'color', 'constant', 'font_style', 'custom'),
    'SE_DATA_TYPES' => array('integer', 'string', 'float', 'bool'),
    'SE_PLATFORM_TYPES' => array('ios', 'android'),
    'SE_RESOURCE_PLIST_PATH' => 'public/files/se_resource/plist/',
    'SE_RESOURCE_XML_PATH' => 'public/files/se_resource/xml/',
    'DEF_LANGUAGE_IDENTIFIER' => "en", // default language identifier,
    'DIR_PLUGIN' => "public/plugin/",
    'EF_TABLE_SQL_TYPES' => array(
        "none" => "NONE",
        "basic" => "Basic",
        "auth" => "Authorization",
        "post" => "Post"
    ),
    // branding constants
    'ALLOWED_CUSTOM_BRANDING' => false,
    'POWERED_BY_CO' => "Cubix.co",
    'POWERED_BY_LINK' => "http://cubix.co",
    // allowed options
    "DEVICE_TYPES" => "none,android,ios",
    "SOCIAL_PLATFORM_TYPES" => "facebook,twitter,gplus",
    "ALLOWED_ENTITY_TYPES" => "user", // csv
    "ALLOWED_GENDERS" => "male,female,none", // csv
    "IMAGES_UPLOADS" => "uploads/",
    'IMAGES_UPLOAD_PATH' => 'public/files/uploads/',
    'DIR_ATTACHMENT'  => 'public/files/',
    'DIR_IMPORT'  => 'public/import/',
    'DIR_COMPRESSED'  => 'public/files/compressed/',
    'DIR_MOBILE'  => 'public/files/mobile/',
    'THUMB_PREFIX' => 'thumb-',
    'COMPRESS_PREFIX' => 'compress-',
    'MOBILE_FILE_PREFIX' => 'mobile-',
    'DIR_EXTENSION' => "public/extension/",

    'DIR_ATTACHMENT_SYS'  => 'public/system/',
    'DIR_COMPRESSED_SYS'  => 'public/system/compressed/',
    'DIR_MOBILE_SYS'  => 'public/system/mobile/',

    'UNAUTH_PAGES' => array('logout' , 'login' , 'confirm_forgot' , 'forgot_thankyou' , 'confirm_signup' , 'signup_thankyou' , 'change_password' ,
                        'stripe' , 'stripe_post' , 'paypal' , 'getCheckout','getoptions','getRoleOptions'),
    'WEEK_DAYS' => array(
        '1' => 'Monday',
        '2' => 'Tuesday',
        '3' => 'Wednesday',
        '4' => 'Thursday',
        '5' => 'Friday',
        '6' => 'Saturday',
        '7' => 'Sunday',
    ),
    'EXTERNAL_CALL_DETAIL' => array(
        'url' => 'http://cubixsource.com/staging/delivery/public/todaytoday/',
        'headers' => [
            'api_key' => 'private_key',
            'api_secret' => 'private_secret'
        ],
        'DELIVERY_PLATFORM_QUEUE' =>[
            'AMAZON' => [
                //'receive_url' => 'https://sqs.us-west-2.amazonaws.com/?Action=ReceiveMessage&QueueUrl=https://sqs.us-west-2.amazonaws.com/792804774122/cubix&Version=2012-11-05&AttributeName=All&MaxNumberOfMessages=10&MessageAttributeName=All',
                'receive_url' => 'https://sqs.us-west-2.amazonaws.com/792804774122/cubix',
                'delete_url' => 'https://sqs.us-west-2.amazonaws.com/792804774122/cubix'
            ]
        ]
    ),
    'KANBAN_DETAIL' => array(
        'url' => KANBAN_URL
        //'url' => 'http://192.168.1.72:3000/?'
    ),

    //images constants
    'MIN_IMAGE_WIDTH' => 300,
    'MIN_IMAGE_HEIGHT' => 200,
    'MAX_IMAGE_SIZE' => 1024 * 1024 * 1,
    'MAX_MOBILE_IMAGE_SIZE' => 960,

    //Word limit
    'TITLE_LIMIT' => 45,
    'DESC_LIMIT' => 360,

    'GOOGLE_API_DISTANCE' => 'https://maps.googleapis.com/maps/api/distancematrix/json',
    'AMOUNT_KG_TO_POUND' => 2.2046,
    'ORDER_SERIES' => 'RH',
    'DEFAULT_ESTIMATED_MIN' => 5,
    'ADD_MIN_IN_AUTO_DECLINE' => 1,
    'CITY_TEST' => 0,
    'ALLOWED_COUNTRY_ID' => 187,
    'WEIGHT_UNIT' => 'pound',
    'VOLUME_UNIT' => 'in&#179;',
    'LANGUAGE_PATH' => "resources/lang/",
    'TRANSLATION_FILE_NAME' => 'system.php',
    'TRANSLATION_VALIDATION_FILE' => 'validation.php',
];

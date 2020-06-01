<?php

return [
    'API_BASE_ROUTE' => DIR_API.'ec/',
    'API_CTRL_PATH' => str_replace('/', '\\', ucfirst(DIR_API)).'EC\\',

    // file system
    'DIR_JSON_STUB' => 'public/ec_stub/magento2/',
    'DIR_LIBRARY' => 'Magento2\\',

    // hooks
    'HOOK_PATH' => 'Magento2\\',

    // API credentials + URL (MMA Keys)
    'API_BASE_URL' => 'https://mma.cubix.co/index.php/rest/V1', // (or store REST url)
    'CONSUMER_KEY' => '6teljq646jn493p1kmshmc0s4jjucdnd',
    'CONSUMER_SECRET' => 'pv9a39s7gsxh9erytbav32ussxy062qj',
    'ACCESS_TOKEN' => 'hyrwim31stqrc7phhl1ayea7q221oiky',
    'ACCESS_TOKEN_SECRET' => '2pkjc7hxps21kqpnr3wpjdxq3mtkr2ys',

    // objects
    'CONF_CATEGORY' => array(
        'IDENTIFIER' => 'category',
        'CLASS_NAME' => 'Category',
        // API endpoints
        'API_ENDPOINT_GET' => 'categories'
    ),

    /*// request urls
    'GET_CATEGORIES_URL' => '',
    // data set identifiers
    'IDENTIFIER_PRODUCT' => 'product',*/
];

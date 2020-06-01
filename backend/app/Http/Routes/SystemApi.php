<?php
$routes_prefix = DIR_API.DIR_SYSTEM;
$api_routes_prefix = DIR_API;
$ctrl_dir = ucwords(DIR_API).ucwords(DIR_SYSTEM);

Route::group(['middleware' => ['api.auth'], "prefix" => $api_routes_prefix], function() use ($ctrl_dir,$api_routes_prefix) {


    $ctrl_dir = str_replace("/",'\\',$ctrl_dir);
    $uri_mask_response = \App\Http\Models\SYSEntityType::getUriMask();
    if(!empty($uri_mask_response['uri'])){
        $type = $uri_mask_response['type'];
        if($uri_mask_response['is_external']){
            Route::$type($uri_mask_response['uri'], $ctrl_dir . $uri_mask_response['controller']);
        }else {
            Route::$type($uri_mask_response['uri'], $ctrl_dir . $uri_mask_response['controller']);
        }
    }
});

Route::group(['middleware' => ['api.auth'], "prefix" => $routes_prefix], function() use ($ctrl_dir,$routes_prefix) {
    //else{

        $ctrl_dir = str_replace("/",'\\',$ctrl_dir);
        $uri_response = \App\Http\Models\SYSEntityType::getUri();
        foreach($uri_response as $uri){
            $type = $uri->type;
            $uri->uri = str_replace(DIR_SYSTEM, '',$uri->uri);
            if($uri->is_external){
                Route::$type($uri->uri, $ctrl_dir . $uri->method);
            }else {
                Route::$type($uri->uri, $ctrl_dir . $uri->method);
            }
        }
    //}

/*
    elseif(\App\Http\Models\SYSEntityType::isEntityExternalCall()){
        // Options
        Route::post('options', $ctrl_dir . 'EntityExternalController@index');
        Route::get('options', $ctrl_dir . 'EntityExternalController@index');
        Route::get('options/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('options/update', $ctrl_dir . 'EntityExternalController@index');
        Route::post('options/delete', $ctrl_dir . 'EntityExternalController@index');
        // Status
        Route::post('status', $ctrl_dir . 'EntityExternalController@index');
        Route::get('status', $ctrl_dir . 'EntityExternalController@index');
        Route::get('status/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('status/update', $ctrl_dir . 'EntityExternalController@index');
        Route::post('status/delete', $ctrl_dir . 'EntityExternalController@index');
        // Modules
        Route::post('modules', $ctrl_dir . 'EntityExternalController@index');
        Route::get('modules', $ctrl_dir . 'EntityExternalController@index');
        Route::get('modules/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('modules/update', $ctrl_dir . 'EntityExternalController@index');
        Route::post('modules/delete', $ctrl_dir . 'EntityExternalController@index');
        // Roles
        Route::post('role', $ctrl_dir . 'EntityExternalController@index');
        Route::get('role', $ctrl_dir . 'EntityExternalController@index');
        Route::get('role/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('role/update', $ctrl_dir . 'EntityExternalController@index');
        Route::post('role/delete', $ctrl_dir . 'EntityExternalController@index');
        // Permissions
        Route::post('permissions', $ctrl_dir . 'EntityExternalController@index');
        Route::get('permissions', $ctrl_dir . 'EntityExternalController@index');
        Route::get('permissions/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('permissions/update', $ctrl_dir . 'EntityExternalController@index');
        Route::post('permissions/delete', $ctrl_dir . 'EntityExternalController@index');
        // Permissions
        Route::post('role_permission', $ctrl_dir . 'EntityExternalController@index');
        Route::get('role_permission', $ctrl_dir . 'EntityExternalController@index');
        Route::get('role_permission/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('role_permission/update', $ctrl_dir . 'EntityExternalController@index');
        Route::post('role_permission/delete', $ctrl_dir . 'EntityExternalController@index');
        // Attribute
        Route::post('attribute', $ctrl_dir . 'EntityExternalController@index');
        Route::get('attribute', $ctrl_dir . 'EntityExternalController@index');
        Route::get('attribute/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('attribute/update', $ctrl_dir . 'EntityExternalController@index');
        Route::post('attribute/delete', $ctrl_dir . 'EntityExternalController@index');
        // Attribute option
        Route::post('attribute_option', $ctrl_dir . 'EntityExternalController@index');
        Route::get('attribute_option', $ctrl_dir . 'EntityExternalController@index');
        Route::get('attribute_option/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('attribute_option/update', $ctrl_dir . 'EntityExternalController@index');
        Route::post('attribute_option/delete', $ctrl_dir . 'EntityExternalController@index');
        // Attribute Set
        Route::post('attribute_set', $ctrl_dir . 'EntityExternalController@index');
        Route::get('attribute_set', $ctrl_dir . 'EntityExternalController@index');
        Route::get('attribute_set/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('attribute_set/update', $ctrl_dir . 'EntityExternalController@index');
        Route::post('attribute_set/delete', $ctrl_dir . 'EntityExternalController@index');
        // Attribute
        Route::post('attribute_set', $ctrl_dir . 'EntityExternalController@index');
        Route::get('attribute_set', $ctrl_dir . 'EntityExternalController@index');
        Route::get('attribute_set/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('attribute_set/update', $ctrl_dir . 'EntityExternalController@index');
        Route::post('attribute_set/delete', $ctrl_dir . 'EntityExternalController@index');
        //Entity Attribute
        Route::post('entity_attribute', $ctrl_dir . 'EntityExternalController@index');
        Route::get('entity_attribute', $ctrl_dir . 'EntityExternalController@index');
        Route::get('entity_attribute/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('entity_attribute/update', $ctrl_dir . 'EntityExternalController@index');
        Route::post('entity_attribute/delete', $ctrl_dir . 'EntityExternalController@index');
        //Entity Type
        Route::post('entity_type', $ctrl_dir . 'EntityExternalController@index');
        Route::get('entity_type', $ctrl_dir . 'EntityExternalController@index');
        Route::get('entity_type/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('entity_type/update', $ctrl_dir . 'EntityExternalController@index');
        Route::post('entity_type/delete', $ctrl_dir . 'EntityExternalController@index');
        // Entities
        Route::post('entities', $ctrl_dir . 'EntityExternalController@index');
        Route::get('entities', $ctrl_dir . 'EntityExternalController@index');
        Route::get('entities/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('entities/delete', $ctrl_dir . 'EntityExternalController@index');
        Route::post('entities/update', $ctrl_dir . 'EntityExternalController@index');

        // Entity Entity Map
        Route::post('entity_entity_map', $ctrl_dir . 'EntityExternalController@index');
        Route::get('entity_entity_map', $ctrl_dir . 'EntityExternalController@index');
        Route::get('entity_entity_map/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('entity_entity_map/delete', $ctrl_dir . 'EntityExternalController@index');

        // Entity relations
        Route::post('entity_relation', $ctrl_dir . 'EntityExternalController@index');
        Route::get('entity_relation', $ctrl_dir . 'EntityExternalController@index');
        Route::get('entity_relation/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('entity_relation/delete', $ctrl_dir . 'EntityExternalController@index');
        Route::post('entity_relation/update', $ctrl_dir . 'EntityExternalController@index');


        // User
        Route::post('users', $ctrl_dir . 'EntityExternalController@index');
        Route::get('users/listing', $ctrl_dir . 'EntityExternalController@index');


        Route::get('permissions', $ctrl_dir . 'EntityExternalController@index');


        // attachments
        Route::any('attachment/types', $ctrl_dir . 'EntityExternalController@index');
        Route::any('attachment/save', $ctrl_dir . 'EntityExternalController@index');
        Route::any('attachment/delete', $ctrl_dir . 'EntityExternalController@index');


        // Payment
        Route::any('payments/stripePayment', $ctrl_dir . 'EntityExternalController@index');
        Route::any('payments/stripeAdd', $ctrl_dir . 'EntityExternalController@index');
        Route::any('payments/paypalPay', $ctrl_dir . 'EntityExternalController@index');
        Route::any('payments/stripeAdd', $ctrl_dir . 'EntityExternalController@index');
        Route::any('payments/stripeListing', $ctrl_dir . 'EntityExternalController@index');
        Route::any('payments/stripeUpdate', $ctrl_dir . 'EntityExternalController@index');
        Route::any('payments/stripeDelete', $ctrl_dir . 'EntityExternalController@index');
        Route::any('payments/stripeCharge', $ctrl_dir . 'EntityExternalController@index');
        Route::any('payments/paymentHistory', $ctrl_dir . 'EntityExternalController@index');

        // Chating
        Route::any('chating/addChat', $ctrl_dir . 'EntityExternalController@index');
        Route::any('chating/getList', $ctrl_dir . 'EntityExternalController@index');
        Route::any('chating/delete', $ctrl_dir . 'EntityExternalController@index');
        Route::any('chating/sendMessage', $ctrl_dir . 'EntityExternalController@index');
        Route::any('chating/singleChat', $ctrl_dir . 'EntityExternalController@index');

        Route::any('notification/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::any('notification/read', $ctrl_dir . 'EntityExternalController@index');
        Route::any('notification/counts', $ctrl_dir . 'EntityExternalController@index');

        // Roles
        Route::post('category', $ctrl_dir . 'EntityExternalController@index');
        Route::get('category', $ctrl_dir . 'EntityExternalController@index');
        Route::get('category/listing', $ctrl_dir . 'EntityExternalController@index');
        Route::post('category/update', $ctrl_dir . 'EntityExternalController@index');
        Route::post('category/delete', $ctrl_dir . 'EntityExternalController@index');
    }else {
        // Options
        Route::post('options', $ctrl_dir . 'OptionController@post');
        Route::get('options', $ctrl_dir . 'OptionController@get');
        Route::get('options/listing', $ctrl_dir . 'OptionController@listing');
        Route::post('options/update', $ctrl_dir . 'OptionController@update');
        Route::post('options/delete', $ctrl_dir . 'OptionController@delete');
        // Status
        Route::post('status', $ctrl_dir . 'StatusController@post');
        Route::get('status', $ctrl_dir . 'StatusController@get');
        Route::get('status/listing', $ctrl_dir . 'StatusController@listing');
        Route::post('status/update', $ctrl_dir . 'StatusController@update');
        Route::post('status/delete', $ctrl_dir . 'StatusController@delete');
        // Modules
        Route::post('modules', $ctrl_dir . 'ModuleController@post');
        Route::get('modules', $ctrl_dir . 'ModuleController@get');
        Route::get('modules/listing', $ctrl_dir . 'ModuleController@listing');
        Route::post('modules/update', $ctrl_dir . 'ModuleController@update');
        Route::post('modules/delete', $ctrl_dir . 'ModuleController@delete');
        // Roles
        Route::post('role', $ctrl_dir . 'RoleController@post');
        Route::get('role', $ctrl_dir . 'RoleController@get');
        Route::get('role/listing', $ctrl_dir . 'RoleController@listing');
        Route::post('role/update', $ctrl_dir . 'RoleController@update');
        Route::post('role/delete', $ctrl_dir . 'RoleController@delete');
        // Permissions
        Route::post('permissions', $ctrl_dir . 'PermissionController@post');
        Route::get('permissions', $ctrl_dir . 'PermissionController@get');
        Route::get('permissions/listing', $ctrl_dir . 'PermissionController@listing');
        Route::post('permissions/update', $ctrl_dir . 'PermissionController@update');
        Route::post('permissions/delete', $ctrl_dir . 'PermissionController@delete');
        // Permissions
        Route::post('role_permission', $ctrl_dir . 'RolePermissionController@post');
        Route::get('role_permission', $ctrl_dir . 'RolePermissionController@get');
        Route::get('role_permission/listing', $ctrl_dir . 'RolePermissionController@listing');
        Route::post('role_permission/update', $ctrl_dir . 'RolePermissionController@update');
        Route::post('role_permission/delete', $ctrl_dir . 'RolePermissionController@delete');
        // Attribute
        Route::post('attribute', $ctrl_dir . 'AttributeController@post');
        Route::get('attribute', $ctrl_dir . 'AttributeController@get');
        Route::get('attribute/listing', $ctrl_dir . 'AttributeController@listing');
        Route::post('attribute/update', $ctrl_dir . 'AttributeController@update');
        Route::post('attribute/delete', $ctrl_dir . 'AttributeController@delete');
        // Attribute option
        Route::post('attribute_option', $ctrl_dir . 'AttributeOptionController@post');
        Route::get('attribute_option', $ctrl_dir . 'AttributeOptionController@get');
        Route::get('attribute_option/listing', $ctrl_dir . 'AttributeOptionController@listing');
        Route::post('attribute_option/update', $ctrl_dir . 'AttributeOptionController@update');
        Route::post('attribute_option/delete', $ctrl_dir . 'AttributeOptionController@delete');
        // Attribute Set
        Route::post('attribute_set', $ctrl_dir . 'AttributeSetController@post');
        Route::get('attribute_set', $ctrl_dir . 'AttributeSetController@get');
        Route::get('attribute_set/listing', $ctrl_dir . 'AttributeSetController@listing');
        Route::post('attribute_set/update', $ctrl_dir . 'AttributeSetController@update');
        Route::post('attribute_set/delete', $ctrl_dir . 'AttributeSetController@delete');
        // Attribute
        Route::post('attribute_set', $ctrl_dir . 'AttributeSetController@post');
        Route::get('attribute_set', $ctrl_dir . 'AttributeSetController@get');
        Route::get('attribute_set/listing', $ctrl_dir . 'AttributeSetController@listing');
        Route::post('attribute_set/update', $ctrl_dir . 'AttributeSetController@update');
        Route::post('attribute_set/delete', $ctrl_dir . 'AttributeSetController@delete');
        //Entity Attribute
        Route::post('entity_attribute', $ctrl_dir . 'EntityAttributeController@post');
        Route::get('entity_attribute', $ctrl_dir . 'EntityAttributeController@get');
        Route::get('entity_attribute/listing', $ctrl_dir . 'EntityAttributeController@listing');
        Route::post('entity_attribute/update', $ctrl_dir . 'EntityAttributeController@update');
        Route::post('entity_attribute/delete', $ctrl_dir . 'EntityAttributeController@delete');
        //Entity Type
        Route::post('entity_type', $ctrl_dir . 'EntityTypeController@post');
        Route::get('entity_type', $ctrl_dir . 'EntityTypeController@get');
        Route::get('entity_type/listing', $ctrl_dir . 'EntityTypeController@listing');
        Route::post('entity_type/update', $ctrl_dir . 'EntityTypeController@update');
        Route::post('entity_type/delete', $ctrl_dir . 'EntityTypeController@delete');
        // Entities
        Route::post('entities', $ctrl_dir . 'EntityController@post');
        Route::get('entities', $ctrl_dir . 'EntityController@get');
        Route::get('entities/listing', $ctrl_dir . 'EntityController@listing');
        Route::post('entities/delete', $ctrl_dir . 'EntityController@delete');
        Route::post('entities/update', $ctrl_dir . 'EntityController@save');

        // Entity Entity Map
        Route::post('entity_entity_map', $ctrl_dir . 'EntityEntityMapController@post');
        Route::get('entity_entity_map', $ctrl_dir . 'EntityEntityMapController@get');
        Route::get('entity_entity_map/listing', $ctrl_dir . 'EntityEntityMapController@listing');
        Route::post('entity_entity_map/delete', $ctrl_dir . 'EntityEntityMapController@delete');

        // Entity relations
        Route::post('entity_relation', $ctrl_dir . 'EntityTypeRelationController@post');
        Route::get('entity_relation', $ctrl_dir . 'EntityTypeRelationController@get');
        Route::get('entity_relation/listing', $ctrl_dir . 'EntityTypeRelationController@listing');
        Route::post('entity_relation/delete', $ctrl_dir . 'EntityTypeRelationController@delete');
        Route::post('entity_relation/update', $ctrl_dir . 'EntityTypeRelationController@save');


        // User
        Route::post('users', $ctrl_dir . 'UserController@index');
        Route::get('users/listing', $ctrl_dir . 'UserController@listing');


        Route::get('permissions', $ctrl_dir . 'PermissionController@listing');


        // attachments
        Route::any('attachment/types', $ctrl_dir . 'AttachmentController@attachmentTypes');
        Route::any('attachment/save', $ctrl_dir . 'AttachmentController@saveAttachment');
        Route::any('attachment/delete', $ctrl_dir . 'AttachmentController@deleteAttachment');


        // Payment
        Route::any('payments/stripePayment', $ctrl_dir . 'PaymentController@StripePaymentForm');
        Route::any('payments/stripeAdd', $ctrl_dir . 'PaymentController@index');
        Route::any('payments/paypalPay', $ctrl_dir . 'PaymentController@paypalPay');
        Route::any('payments/stripeAdd', $ctrl_dir . 'PaymentsController@addStripe');
        Route::any('payments/stripeListing', $ctrl_dir . 'PaymentsController@listingStripe');
        Route::any('payments/stripeUpdate', $ctrl_dir . 'PaymentsController@update');
        Route::any('payments/stripeDelete', $ctrl_dir . 'PaymentsController@delete');
        Route::any('payments/stripeCharge', $ctrl_dir . 'PaymentsController@stripCharge');
        Route::any('payments/paymentHistory', $ctrl_dir . 'PaymentsController@paymentHistory');

        // Chating
        Route::any('chating/addChat', $ctrl_dir . 'ChatingController@addChat');
        Route::any('chating/getList', $ctrl_dir . 'ChatingController@chatList');
        Route::any('chating/delete', $ctrl_dir . 'ChatingController@removeChat');
        Route::any('chating/sendMessage', $ctrl_dir . 'ChatingController@sendMessage');
        Route::any('chating/singleChat', $ctrl_dir . 'ChatingController@history');

        Route::any('notification/listing', $ctrl_dir . 'NotificationController@listing');
        Route::any('notification/read', $ctrl_dir . 'NotificationController@read');
        Route::any('notification/counts', $ctrl_dir . 'NotificationController@counts');

        // Roles
        Route::post('category', $ctrl_dir . 'CategoryController@post');
        Route::get('category', $ctrl_dir . 'CategoryController@get');
        Route::get('category/listing', $ctrl_dir . 'CategoryController@listing');
        Route::post('category/update', $ctrl_dir . 'CategoryController@update');
        Route::post('category/delete', $ctrl_dir . 'CategoryController@delete');
    }

*/
});
//});
<?php
// Panel : Routes
$routes_prefix = config("panel.DIR");
$ctrl_dir = ucwords($routes_prefix);
$ctrl_dir = str_replace("/", '\\', $ctrl_dir);

Route::group(["prefix" => $routes_prefix."{department}"], function() use ($ctrl_dir) {
    // passing department name to controller function
    Route::get('/', $ctrl_dir.'EntityAuthController@index');
    Route::any('login/', $ctrl_dir.'EntityAuthController@login');
    Route::any('notification', $ctrl_dir . 'EntityNotificationController@listing');
    Route::any('notification/ajaxListing', $ctrl_dir . 'EntityNotificationController@ajaxListing');
});


Route::group([
    "prefix" => $routes_prefix."{department}/",
    'middleware' => ['panel.auth','entity.auth']
], function() use ($ctrl_dir){
    // routes
    //page controller
    Route::any('page/', $ctrl_dir . 'PageController@index');
    Route::any('page/ajax/listing', $ctrl_dir . 'PageController@ajaxListing');
    Route::any('page/add', $ctrl_dir . 'PageController@add');
    Route::any('page/update/{id}', $ctrl_dir . 'PageController@update');
    Route::any('attribute', $ctrl_dir . 'AttributeController@index');
    Route::any('attribute/ajax/listing', $ctrl_dir . 'AttributeController@ajaxListing');
    Route::any('attribute/add', $ctrl_dir . 'AttributeController@add');
    Route::any('attribute/update/{id}', $ctrl_dir . 'AttributeController@update');

    Route::any('attribute_option', $ctrl_dir . 'AttributeoOptionController@index');
    Route::any('attribute_option/ajax/listing', $ctrl_dir . 'AttributeoOptionController@ajaxListing');
    Route::any('attribute_option/add', $ctrl_dir . 'AttributeoOptionController@add');
    Route::any('attribute_option/update/{id}', $ctrl_dir . 'AttributeoOptionController@update');


    Route::any('attribute_set', $ctrl_dir . 'AttributesetController@index');
    Route::any('attribute_set/ajax/listing', $ctrl_dir . 'AttributesetController@ajaxListing');
    Route::any('attribute_set/add', $ctrl_dir . 'AttributesetController@add');
    Route::any('attribute_set/update/{id}', $ctrl_dir . 'AttributesetController@update');



    //Entity_Type
    Route::any('entity_type', $ctrl_dir . 'EntityTypeController@index');
    Route::any('entity_type/ajax/listing', $ctrl_dir . 'EntityTypeController@ajaxListing');
    Route::any('entity_type/add', $ctrl_dir . 'EntityTypeController@add');
    Route::any('entity_type/update/{id}', $ctrl_dir . 'EntityTypeController@update');

    Route::any('entities/{any}/{all?}', $ctrl_dir . 'EntityBackController@index');
    Route::any('entities/{any}/update/{id}', $ctrl_dir . 'EntityBackController@index');
    Route::any('entities/{any}/view/{id}', $ctrl_dir . 'EntityBackController@update');
    Route::any('entities/{any}/import', $ctrl_dir . 'EntityBackController@import');
    //Route::any('entities/ajax/listing', $ctrl_dir . 'EntityBackController@ajaxListing');
    //Route::any('entities/add', $ctrl_dir . 'EntityBackController@add');
    //Route::any('entities/update/{id}', $ctrl_dir . 'EntityBackController@update');

    Route::any('entity_attribute', $ctrl_dir . 'EntityAttributeController@index');
    Route::any('entity_attribute/ajax/listing', $ctrl_dir . 'EntityAttributeController@ajaxListing');
    Route::any('entity_attribute/add', $ctrl_dir . 'EntityAttributeController@add');
    Route::any('entity_attribute/update/{id}', $ctrl_dir . 'EntityAttributeController@update');


    // Administrator : Admin
    //Route::any('/', $ctrl_dir.'EntityAuthController@login');
    Route::any('logout/', $ctrl_dir.'EntityAuthController@logout');
    Route::get('dashboard/', $ctrl_dir.'EntityAuthController@dashboard');
    Route::any('confirm_forgot/', $ctrl_dir . 'EntityAuthController@confirmForgot');
    Route::any('forgot_thankyou', $ctrl_dir . 'EntityAuthController@forgotThankyou');
    Route::any('confirm_signup', $ctrl_dir . 'EntityAuthController@confirmSignup');
    Route::any('signup_thankyou', $ctrl_dir . 'EntityAuthController@signupThankyou');
    Route::any('change_password', $ctrl_dir . 'EntityAuthController@changePassword');
    Route::any('update_profile', $ctrl_dir.'EntityAuthController@updateProfile');

    // Role
    Route::any('role', $ctrl_dir . 'RoleController@index');
    Route::any('role/ajax/listing', $ctrl_dir . 'RoleController@ajaxListing');
    Route::any('role/moduleslisting', $ctrl_dir . 'RoleController@roleAjaxListing');
    Route::any('role/modulesupdate', $ctrl_dir . 'RoleController@updateRole');
    Route::any('role/add', $ctrl_dir . 'RoleController@add');
    Route::any('role/update/{id}', $ctrl_dir . 'RoleController@update');
    Route::any('role/{id}', $ctrl_dir . 'RoleController@assign_role');
    Route::any('role/view/{id}', $ctrl_dir . 'RoleController@update');

    // User
    Route::any('users', $ctrl_dir . 'UserController@index');
    Route::any('users/ajax/listing', $ctrl_dir . 'UserController@ajaxListing');
    Route::any('users/add', $ctrl_dir . 'UserController@add');
    Route::any('users/update/{id}', $ctrl_dir . 'UserController@update');



    Route::any('stripe', $ctrl_dir . 'StripeController@stripeForm');
    Route::any('stripe_post', $ctrl_dir . 'StripeController@stripePost');


    Route::any('paypal', $ctrl_dir . 'paymentController@paypalForm');

    Route::any('getCheckout', $ctrl_dir . 'paymentController@getCheckout');
    Route::any('getoptions', $ctrl_dir . 'EntityBackController@getoptions');

    //Category
    Route::any('category', $ctrl_dir . 'CategoryController@index');
    Route::any('category/ajax/listing', $ctrl_dir . 'CategoryController@ajaxListing');;
    Route::any('category/add', $ctrl_dir . 'CategoryController@add');
    Route::any('category/update/{id}', $ctrl_dir . 'CategoryController@update');
    Route::any('category/view/{id}', $ctrl_dir . 'CategoryController@view');

    //Groups
    Route::any('group', $ctrl_dir . 'GroupController@index');
    Route::any('group/ajax/listing', $ctrl_dir . 'GroupController@ajaxListing');;
    Route::any('group/add', $ctrl_dir . 'GroupController@add');
    Route::any('group/update/{id}', $ctrl_dir . 'GroupController@update');
    Route::any('group/view/{id}', $ctrl_dir . 'GroupController@update');


    //custom routes
    Route::any('entities/order/assign', $ctrl_dir . 'EntityBackController@import');
    Route::any('entities/order/order-history/{id}', $ctrl_dir . 'EntityBackController@orderHistory');
    Route::any('rating',$ctrl_dir . 'EntityBackController@ratingListing');
    Route::any('rating/ajaxListing',$ctrl_dir . 'EntityBackController@ratingAjaxListing');
    Route::any('entities/rating/rating-detail/{id}',$ctrl_dir . 'EntityBackController@ratingDetail');
    Route::any('order/calendar', $ctrl_dir . 'EntityBackController@calendar');
});

Route::group([
    "prefix" => $routes_prefix."{department}/",
 //   'middleware' => ['panel.auth','entity.auth']
], function() use ($ctrl_dir) {
//Kanban - Order Management
    Route::any('kanban', $ctrl_dir . 'KanbanManagementController@index');
    Route::any('kanban/getMatrix', $ctrl_dir . 'KanbanManagementController@getMatrix');
    Route::any('kanban/getMatrixData', $ctrl_dir . 'KanbanManagementController@getMatrixData');
    Route::any('kanban/assignUser', $ctrl_dir . 'KanbanManagementController@assignUser');
    Route::any('kanban/updateUser/state', $ctrl_dir . 'KanbanManagementController@updateUser');
    Route::any('kanban/comment/add', $ctrl_dir . 'KanbanManagementController@addComment');
    Route::any('kanban/comment/get', $ctrl_dir . 'KanbanManagementController@getComment');

    Route::any('orderRating', $ctrl_dir . 'EntityPanelController@orderRating');
    Route::any('orderRating/ajaxListing', $ctrl_dir . 'EntityPanelController@orderRatingListing');

    Route::any('report', $ctrl_dir . 'EntityPanelController@report');
    Route::any('report/ajaxListing', $ctrl_dir . 'EntityPanelController@reportListing');



});

Route::any('getEntity',$ctrl_dir . 'EntityBackController@getEntityData');
Route::any('getTruckClass',$ctrl_dir . 'EntityBackController@getTruckClass');
Route::any('send-notification','Cron\CustomNotificationController@sendNotification');
Route::any('send-remainder-notification','Cron\CustomNotificationController@sendRemainderNotification');
Route::any('driver/stats', $ctrl_dir . 'EntityAjaxController@getOrderStats');
Route::any('available_vehicles', $ctrl_dir . 'EntityAjaxController@getAvailableVehicles');
<?php
/**
 * un-grouped routes
 */
//Route::any('/', 'IndexController@index');
Route::any('signup', 'IndexController@signup');
Route::any('login', 'IndexController@login');
Route::any('logout', 'IndexController@logout');
Route::get('forgot_thankyou', 'IndexController@forgotThankyou');
//Route::any('confirm_forgot', 'IndexController@xconfirmForgot');
Route::any('confirm_signup', 'EntityAuthController@confirmSignup');
Route::get('signup_thankyou', 'EntityAuthController@signupThankyou');
Route::any('confirm_forgot', 'EntityAuthController@confirmForgot');
Route::any('forgot_thankyou', 'EntityAuthController@forgotThankyou');
Route::any('reset_id', 'EntityAuthController@resetID');





/**
 * API routes
 */
Route::group(['prefix' => DIR_API."entity_auth/"], function () {
    /**
     * Users
     */
    Route::post('/', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@post');
    Route::get('/', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@get');
    Route::put('/', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@put');
    Route::delete('/', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@delete');
    Route::post('social_login', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@socialLogin');
    Route::post('verify_signup', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@verifySignup');
    Route::post('signin', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@login');
    Route::post('forgot_request', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@forgotPasswordRequest');
    Route::post('reset_password', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@resetPassword');
    Route::post('verify_forgot', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@verifyForgot');
    Route::get('listing', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@listing');
    Route::post('change_password', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@changePassword');
    Route::post('edit_profile', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@editProfile');
    Route::post('save_token', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@saveToken');
    Route::post('verify_phone', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@verifyPhone');
    Route::post('resend_code', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@resendCode');
    Route::post('delete', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@delete');
    Route::post('confirm_signup', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@confirmSignup');
    //Route::post('confirm_forgot', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@xconfirmForgot');
    Route::post('change_id_request', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@changeIDRequest');
    Route::post('reset_id', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@resetID');
    Route::post('logout', rtrim(ucfirst(DIR_API), '/') . '\EntityAuthController@logout');
    Route::post('verify_forgot_code', rtrim(ucfirst(DIR_API), '/').'\EntityAuthController@verifyForgotCode');


    /**
     * Company
     */
    /*Route::post('companies', rtrim(ucfirst(DIR_API), '/').'\CompanyController@post');
    Route::get('companies/listing', rtrim(ucfirst(DIR_API), '/').'\CompanyController@listing');*/

    /**
     * Product
     */
    /*Route::post('products', rtrim(ucfirst(DIR_API), '/').'\ProductController@post');
    Route::get('products/listing', rtrim(ucfirst(DIR_API), '/').'\ProductController@listing');
    Route::post('products/assets', rtrim(ucfirst(DIR_API), '/').'\ProductController@postAssets');*/
});

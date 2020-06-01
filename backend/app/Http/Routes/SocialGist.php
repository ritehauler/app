<?php
/**
 * un-grouped routes
 */
//Route::any('/', 'IndexController@index');
//Route::any('signup', 'IndexController@signup');
//Route::any('login', 'IndexController@login');
//Route::any('logout', 'IndexController@logout');
//Route::get('forgot_thankyou', 'IndexController@forgotThankyou');
//Route::any('confirm_forgot', 'IndexController@confirmForgot');
//Route::any('confirm_signup', 'SocialGistUserController@confirmSignup');
//Route::any('signup_thankyou', 'SocialGistUserController@signupThankyou');
//Route::any('confirm_forgot', 'SocialGistUserController@confirmForgot');
//Route::any('forgot_thankyou', 'SocialGistUserController@forgotThankyou');
//Route::any('reset_id', 'SocialGistUserController@resetID');


/**
 * API routes (for reference only)
 */
Route::group(['prefix' => DIR_API . "social_gist/users/"], function () {
    /**
     * Users
     */
//    Route::post('/', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@post');
//    Route::get('/', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@get');
//    Route::put('/', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@put');
//    Route::delete('/', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@delete');
//    Route::post('social_login', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@socialLogin');
//    Route::post('verify_signup', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@verifySignup');

//    Route::post('signin', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@signin');
//    Route::post('forgot_request', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@forgotPasswordRequest');
//    Route::post('verify_forgot', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@verifyForgot');
//    Route::get('listing', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@listing');
//    Route::post('change_password', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@changePassword');
//    Route::post('edit_profile', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@editProfile');
//    Route::post('save_token', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@saveToken');
//    Route::post('logout', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@logout');
//    Route::post('resend_code', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@resendCode');
//    Route::post('is_registered', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@isRegistered');
    //Route::post('verify_phone', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@verifyPhone');
    //Route::post('resend_code', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@resendCode');
    //Route::post('delete', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@delete');
    //Route::post('confirm_signup', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@confirmSignup');
    //Route::post('confirm_forgot', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@confirmForgot');
    //Route::post('change_id_request', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@changeIDRequest');
    //Route::post('reset_id', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@resetID');
    //Route::post('logout', rtrim(ucfirst(DIR_API), '/') . '\SocialGistUserController@logout');


});

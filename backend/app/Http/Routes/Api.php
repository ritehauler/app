<?php
// API Routes
Route::get('api', rtrim(ucfirst(DIR_API), '/').'\IndexController@index');
Route::match(array('get', 'post'),DIR_API.'load_params', rtrim(ucfirst(DIR_API), '/').'\IndexController@load_params');
// API methods : User
Route::any(DIR_API.'user/login', rtrim(ucfirst(DIR_API), '/').'\UserController@login');
// API methods : Game
Route::any(DIR_API.'game/configurations', rtrim(ucfirst(DIR_API), '/').'\GameController@configurations');
Route::any(DIR_API.'game/virtual_items', rtrim(ucfirst(DIR_API), '/').'\GameController@virtualItems');
Route::any(DIR_API.'game/levels', rtrim(ucfirst(DIR_API), '/').'\GameController@levels');
Route::any(DIR_API.'game/achievements', rtrim(ucfirst(DIR_API), '/').'\GameController@achievements');
Route::any(DIR_API.'qa/get_questions', rtrim(ucfirst(DIR_API), '/').'\QAController@getQuestions');
Route::any(DIR_API.'qa/submit_answers', rtrim(ucfirst(DIR_API), '/').'\QAController@submitAnswers');
// API methods : Asset

Route::group(['middleware' => 'api.auth'], function () {
    Route::any(DIR_API . 'asset/get_all', rtrim(ucfirst(DIR_API), '/') . '\AssetController@getAll');
    Route::any(DIR_API.'se/get_all', rtrim(ucfirst(DIR_API), '/').'\SEController@getAll');
});

// API methods : Page
Route::any(DIR_API.'page/get_by_slug', rtrim(ucfirst(DIR_API), '/').'\PageController@getBySlug');

// API methods : oAuth
Route::any(DIR_API.'oauth/get_token', rtrim(ucfirst(DIR_API), '/').'\OAuthController@getToken');
Route::any(DIR_API.'oauth/refresh_token', rtrim(ucfirst(DIR_API), '/').'\OAuthController@refreshToken');

// Users
Route::any(DIR_API.'users/listing', rtrim(ucfirst(DIR_API), '/').'\UserController@listing');
Route::post(DIR_API.'users', rtrim(ucfirst(DIR_API), '/').'\UserController@post');
Route::get(DIR_API.'users', rtrim(ucfirst(DIR_API), '/').'\UserController@get');
Route::post(DIR_API.'users/update', rtrim(ucfirst(DIR_API), '/').'\UserController@update');
Route::post(DIR_API.'users/delete', rtrim(ucfirst(DIR_API), '/').'\UserController@delete');


Route::any(DIR_API.'entity_auth/verify_user', rtrim(ucfirst(DIR_API), '/').'\EntityAuthUserController@verifyPhone');
//Route::any(DIR_API.'entity_auth/verify_forgot_code', rtrim(ucfirst(DIR_API), '/').'\EntityAuthUserController@verifyForgotCode');
Route::any(DIR_API.'entity_auth/forgot_reset_password', rtrim(ucfirst(DIR_API), '/').'\EntityAuthController@ForgotResetPassword');

//Route::post(DIR_API.'users', rtrim(ucfirst(DIR_API), '/').'\UserController@post');
//Route::get(DIR_API.'users', rtrim(ucfirst(DIR_API), '/').'\UserController@get');
//Route::get(DIR_API.'users/listing', rtrim(ucfirst(DIR_API), '/').'\UserController@listing');
//Route::post(DIR_API.'users/update', rtrim(ucfirst(DIR_API), '/').'\UserController@update');
//Route::post(DIR_API.'users/delete', rtrim(ucfirst(DIR_API), '/').'\UserController@delete');

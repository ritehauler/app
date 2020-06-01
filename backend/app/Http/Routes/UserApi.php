<?php
// API methods : User
Route::any(DIR_API.'entity_auth/get', rtrim(ucfirst(DIR_API), '/').'\UserController@get');
Route::any(DIR_API.'entity_auth/mobilelogin', rtrim(ucfirst(DIR_API), '/').'\UserController@mobileLogin');
Route::any(DIR_API.'entity_auth/sociallogin', rtrim(ucfirst(DIR_API), '/').'\UserController@socialLogin');
Route::any(DIR_API.'entity_auth/verify_users', rtrim(ucfirst(DIR_API), '/').'\EntityAuthUserController@verifyPhone');
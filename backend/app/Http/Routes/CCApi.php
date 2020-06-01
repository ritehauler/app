<?php
// API methods : CC : User
Route::any(DIR_API.'cc_user/signup', rtrim(ucfirst(DIR_API), '/').'\CCUserController@signup');
Route::any(DIR_API.'cc_user/login', rtrim(ucfirst(DIR_API), '/').'\CCUserController@login');
//Route::any(DIR_API.'cc_contact/create', rtrim(ucfirst(DIR_API), '/').'\CCContactController@create');
// API methods : CC : Network
Route::any(DIR_API.'cc_network/types', rtrim(ucfirst(DIR_API), '/').'\CCNetworkController@types');
Route::any(DIR_API.'cc_network/add', rtrim(ucfirst(DIR_API), '/').'\CCNetworkController@add');
Route::any(DIR_API.'cc_network/update', rtrim(ucfirst(DIR_API), '/').'\CCNetworkController@update');
// API methods : CC : Group/Space
Route::any(DIR_API.'cc_space/{type}/add', rtrim(ucfirst(DIR_API), '/').'\CCSpaceController@add');
Route::any(DIR_API.'cc_space/{type}/update', rtrim(ucfirst(DIR_API), '/').'\CCSpaceController@update');
// API Methods : CC : General
Route::any(DIR_API.'cc_general/space_permissions', rtrim(ucfirst(DIR_API), '/').'\CCGeneralController@spacePermissions');


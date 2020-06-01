<?php
// API Routes
//Route::get('api', rtrim(ucfirst(DIR_API), '/').'\IndexController@index');
Route::match(array('get', 'post'),DIR_API.'load_params', rtrim(ucfirst(DIR_API), '/').'\IndexController@load_params');
// API methods : Post
Route::any(DIR_API.'post/add', rtrim(ucfirst(DIR_API), '/').'\PostController@addPost');
Route::any(DIR_API.'post/update', rtrim(ucfirst(DIR_API), '/').'\PostController@updatePost');
Route::any(DIR_API.'post/delete', rtrim(ucfirst(DIR_API), '/').'\PostController@deletePost');
Route::any(DIR_API.'post/get', rtrim(ucfirst(DIR_API), '/').'\PostController@getPost');
Route::any(DIR_API.'post/get_all', rtrim(ucfirst(DIR_API), '/').'\PostController@getAllPost');

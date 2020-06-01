<?php
/**
 * API routes
 */

$routes_prefix = DIR_API . "extension/social/package/";
// upper-case all url params for directory structure
$ctrl_dir = ucwords(DIR_API) . implode('/', array_map('ucwords', explode('/', 'extension/social/package/')));

Route::group(["prefix" => $routes_prefix], function () use ($ctrl_dir) {
    $ctrl_dir = str_replace("/", '\\', $ctrl_dir);

    // install / uninstall
    Route::post('core/install', $ctrl_dir . 'CoreController@install');
    Route::post('core/uninstall', $ctrl_dir . 'CoreController@unInstall');

    // Like
    Route::post('like/', $ctrl_dir . 'LikeController@post');
    Route::get('like/get', $ctrl_dir . 'LikeController@get');
    Route::get('like/listing', $ctrl_dir . 'LikeController@listing');

    // Rate
    Route::post('rate/', $ctrl_dir . 'RateController@post');
    Route::get('rate/get', $ctrl_dir . 'RateController@get');
    Route::get('rate/listing', $ctrl_dir . 'RateController@listing');
    Route::post('rate/update', $ctrl_dir . 'RateController@update');
    Route::post('rate/delete', $ctrl_dir . 'RateController@delete');

    // Comment
    Route::post('comment/', $ctrl_dir . 'CommentController@post');
    Route::get('comment/get', $ctrl_dir . 'CommentController@get');
    Route::get('comment/listing', $ctrl_dir . 'CommentController@listing');
    Route::post('comment/update', $ctrl_dir . 'CommentController@update');
    Route::post('comment/delete', $ctrl_dir . 'CommentController@delete');

});
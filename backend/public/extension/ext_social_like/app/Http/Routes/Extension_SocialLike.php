<?php
/**
 * API routes
 */

$routes_prefix = DIR_API."{api_route_base}";
$ctrl_dir = ucwords(DIR_API).ucwords("{api_route_base}");

Route::group(["prefix" => $routes_prefix], function() use ($ctrl_dir) {
    $ctrl_dir = str_replace("/", '\\', $ctrl_dir);

    // Options
    Route::post('/', $ctrl_dir . 'LikeController@post');
    Route::get('get', $ctrl_dir . 'LikeController@get');
    Route::get('listing', $ctrl_dir . 'LikeController@listing');
    Route::post('update', $ctrl_dir . 'LikeController@update');
    Route::post('delete', $ctrl_dir . 'LikeController@delete');

});

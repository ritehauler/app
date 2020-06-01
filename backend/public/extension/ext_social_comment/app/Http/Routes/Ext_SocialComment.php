<?php
/**
 * API routes
 */

$routes_prefix = DIR_API . "{api_base_route}/";
// upper-case all url params for directory structure
$ctrl_dir = ucwords(DIR_API) . implode('/', array_map('ucwords', explode('/', '{api_base_route}/')));

Route::group(["prefix" => $routes_prefix], function () use ($ctrl_dir) {
    $ctrl_dir = str_replace("/", '\\', $ctrl_dir);

    // Options
    Route::post('/', $ctrl_dir . 'IndexController@post');
    Route::get('get', $ctrl_dir . 'IndexController@get');
    Route::get('listing', $ctrl_dir . 'IndexController@listing');
    Route::post('update', $ctrl_dir . 'IndexController@update');
    Route::post('delete', $ctrl_dir . 'IndexController@delete');

});
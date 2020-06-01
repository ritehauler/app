<?php
/**
 * API routes
 */

$routes_prefix = DIR_API . "{api_base_route}/";
// upper-case all url params for directory structure
$ctrl_dir = ucwords(DIR_API) . implode('/', array_map('ucwords', explode('/', '{api_base_route}/')));

Route::group(["prefix" => $routes_prefix], function () use ($ctrl_dir) {
    $ctrl_dir = str_replace("/", '\\', $ctrl_dir);

    // install / uninstall
    Route::post('core/install', $ctrl_dir . 'CoreController@install');
    Route::post('core/uninstall', $ctrl_dir . 'CoreController@unInstall');

    // Options
    Route::post('/', $ctrl_dir . 'IndexController@post');
    Route::post('accept', $ctrl_dir . 'IndexController@accept');
    Route::post('reject', $ctrl_dir . 'IndexController@reject');
    Route::post('cancel', $ctrl_dir . 'IndexController@cancel');
    Route::post('get', $ctrl_dir . 'IndexController@get');
    Route::post('delete', $ctrl_dir . 'IndexController@delete');
    Route::get('listing', $ctrl_dir . 'IndexController@listing');

});
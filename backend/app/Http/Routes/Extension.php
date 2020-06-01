<?php
/**
 * API routes
 */

$routes_prefix = DIR_API . "system/extension/";
$ctrl_dir = ucwords(DIR_API) . ucwords("system/");

Route::group(["prefix" => $routes_prefix], function () use ($ctrl_dir) {
    $ctrl_dir = str_replace("/", '\\', $ctrl_dir);

    // Options
    //Route::post('/', $ctrl_dir . 'SocialController@post');
    Route::post('assign', $ctrl_dir . 'ExtensionController@assign');
    Route::post('unassign', $ctrl_dir . 'ExtensionController@unAssign');
    Route::post('install', $ctrl_dir . 'ExtensionController@install');
    Route::post('uninstall', $ctrl_dir . 'ExtensionController@unInstall');

});

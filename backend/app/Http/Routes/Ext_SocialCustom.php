<?php
/**
 * API routes
 */

$routes_prefix = DIR_API . "extension/social/custom/";
// upper-case all url params for directory structure
$ctrl_dir = ucwords(DIR_API) . implode('/', array_map('ucwords', explode('/', 'extension/social/custom/')));

Route::group(["prefix" => $routes_prefix], function () use ($ctrl_dir) {
    $ctrl_dir = str_replace("/", '\\', $ctrl_dir);

    //Custom like listing
    Route::get('like/listing', $ctrl_dir . 'EntityLikeController@listing');

});
<?php
/**
 * API routes
 */

$routes_prefix = DIR_API . "extension/post/";
// upper-case all url params for directory structure
$ctrl_dir = ucwords(DIR_API) . implode('/', array_map('ucwords', explode('/', 'extension/post/')));

Route::group(["prefix" => $routes_prefix], function () use ($ctrl_dir) {
    $ctrl_dir = str_replace("/", '\\', $ctrl_dir);

    // install / uninstall
    Route::post('core/install', $ctrl_dir . 'CoreController@install');
    Route::post('core/uninstall', $ctrl_dir . 'CoreController@unInstall');

    // types
    Route::get('type/listing', $ctrl_dir . 'PostTypeController@listing');

    // tags
    Route::get('tag/listing', $ctrl_dir . 'PostTagController@listing');
    Route::get('tag', $ctrl_dir . 'PostTagController@get');
    Route::post('tag', $ctrl_dir . 'PostTagController@post');
    Route::post('tag/update', $ctrl_dir . 'PostTagController@update');
    Route::post('tag/delete', $ctrl_dir . 'PostTagController@delete');
    Route::post('tag/post_or_get', $ctrl_dir . 'PostTagController@postOrGet');


    // Post Fields
    Route::post('post', $ctrl_dir . 'PostController@post');
    /*Route::get('form/field/listing', $ctrl_dir . 'FormFieldController@listing');
    Route::get('rate/get', $ctrl_dir . 'RateController@get');
    Route::get('rate/listing', $ctrl_dir . 'RateController@listing');
    Route::post('rate/update', $ctrl_dir . 'RateController@update');
    Route::post('rate/delete', $ctrl_dir . 'RateController@delete');*/


});
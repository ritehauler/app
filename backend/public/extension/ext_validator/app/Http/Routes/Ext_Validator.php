<?php
/**
 * API routes
 */

$routes_prefix = DIR_API . "extension/validator/";
// upper-case all url params for directory structure
$ctrl_dir = ucwords(DIR_API) . implode('/', array_map('ucwords', explode('/', 'extension/validator/')));

Route::group(["prefix" => $routes_prefix], function () use ($ctrl_dir) {
    $ctrl_dir = str_replace("/", '\\', $ctrl_dir);

    // install / uninstall
    //Route::post('core/install', $ctrl_dir . 'CoreController@install');
    //Route::post('core/uninstall', $ctrl_dir . 'CoreController@unInstall');

    // Field Type
    Route::get('field_type/listing', $ctrl_dir . 'FieldTypeController@listing');

    // Form
    Route::post('form', $ctrl_dir . 'EntityTypeFormController@post');
    Route::get('form/listing', $ctrl_dir . 'EntityTypeFormController@listing');

    // Form Fields
    Route::post('form/field', $ctrl_dir . 'FormFieldController@post');
    Route::get('form/field/listing', $ctrl_dir . 'FormFieldController@listing');
    /*Route::get('rate/get', $ctrl_dir . 'RateController@get');
    Route::get('rate/listing', $ctrl_dir . 'RateController@listing');
    Route::post('rate/update', $ctrl_dir . 'RateController@update');
    Route::post('rate/delete', $ctrl_dir . 'RateController@delete');*/

    // Comment
/*    Route::post('comment/', $ctrl_dir . 'CommentController@post');
    Route::get('comment/get', $ctrl_dir . 'CommentController@get');
    Route::get('comment/listing', $ctrl_dir . 'CommentController@listing');
    Route::post('comment/update', $ctrl_dir . 'CommentController@update');
    Route::post('comment/delete', $ctrl_dir . 'CommentController@delete');*/

});
<?php

$routes_prefix = config("frontend.DIR");
$ctrl_dir = ucwords($routes_prefix);

Route::group(["prefix" => $routes_prefix], function() use ($ctrl_dir) {
    $ctrl_dir = str_replace("/",'\\',$ctrl_dir);
    // routes
    Route::any("/", $ctrl_dir.'IndexController@index');
	Route::any("/postData", $ctrl_dir.'IndexController@load_params');
});

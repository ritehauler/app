<?php

Route::group(['middleware' => 'api.auth'], function () {
    // Stub APIs
    Route::post(DIR_API.'stub', rtrim(ucfirst(DIR_API), '/').'\StubController@post');
    Route::get(DIR_API.'stub', rtrim(ucfirst(DIR_API), '/').'\StubController@get');
    Route::post(DIR_API.'stub/update', rtrim(ucfirst(DIR_API), '/').'\StubController@update');
    Route::post(DIR_API.'stub/delete', rtrim(ucfirst(DIR_API), '/').'\StubController@delete');
});

// stub api paths
Route::get(DIR_API.'stub_api/{endpoint_uri?}',[
    'uses' => rtrim(ucfirst(DIR_API), '/').'\StubApiController@get'
])->where('endpoint_uri', '.+');

// stub api paths
Route::post(DIR_API.'stub_api/{endpoint_uri?}',[
    'uses' => rtrim(ucfirst(DIR_API), '/').'\StubApiController@post'
])->where('endpoint_uri', '.+');

// Stub console
Route::get('stub_console', 'StubController@index');
Route::post('stub_console/load_params', 'StubController@loadParams');
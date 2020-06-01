<?php
// Vendor : Core
Route::get("vendor_panel", 'Vendor_Panel\DashboardController@index');
Route::any('vendor_panel/login/', 'Vendor_Panel\VendorController@login');
Route::any('vendor_panel/logout/', 'Vendor_Panel\VendorController@logout');
Route::any('vendor_panel/forgot_password/', 'Vendor_Panel\VendorController@forgotPassword');
Route::any('vendor_panel/confirm_forgot/', 'Vendor_Panel\VendorController@confirmForgot');
Route::any('vendor_panel/change_password/', 'Vendor_Panel\VendorController@changePassword');

// Vendor : Dashboard
Route::get('vendor_panel/dashboard/', 'Vendor_Panel\DashboardController@index');

// Vendor : Setting
Route::any('vendor_panel/setting/', 'Vendor_Panel\SettingController@index');
Route::any('vendor_panel/setting/ajax/listing', 'Vendor_Panel\SettingController@ajaxListing');
Route::any('vendor_panel/setting/update/{id}', 'Vendor_Panel\SettingController@update');
Route::any('vendor_panel/setting/logo_browser', 'Vendor_Panel\SettingController@logoBrowser');
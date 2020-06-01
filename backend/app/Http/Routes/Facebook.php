<?php
/*
* Facebook redirect url state fix
* reference : http://stackoverflow.com/a/32029752
*/
if (!session_id()) {
    session_start();
}
/*
 * base facebook routes
 */
Route::any('facebook/set_token', 'FacebookBaseController@setToken');
Route::any('facebook/login_redirect', 'FacebookBaseController@loginRedirect');

/*
 * extended public facebook routes
 */
Route::any('facebook/', 'FacebookController@index');

<?php
// Methods : Workflow
$routes_prefix = DIR_API.'wfs/';
//$ctrl_dir = ucwords(DIR_API).'Wfs/';
$ctrl_dir = 'Api\Wfs\\';

//Route::group(['middleware' => ['api.auth'], "prefix" => $routes_prefix], function() use ($ctrl_dir,$routes_prefix) {
Route::group(["prefix" => $routes_prefix], function() use ($ctrl_dir,$routes_prefix) {


    Route::any('getMatrix', $ctrl_dir .'WorkflowController@getMatrix');
    Route::any('getMatrixData', $ctrl_dir .'WorkflowController@getMatrixData');
    Route::any('user/assign', $ctrl_dir . 'WorkflowController@assignUserUpdate');
    Route::any('user/update', $ctrl_dir . 'WorkflowController@wftUserUpdate');

    /*Route::any('workflow/login', $ctrl_dir .'WorkflowController@login');

    Route::any('workflow/about', $ctrl_dir .'WorkflowController@about');
    Route::any('workflow/list', $ctrl_dir .'WorkflowController@wfList');
    Route::any('workflow/task/user/selection',$ctrl_dir . 'WorkflowController@userTaskSelection');
    Route::post('workflow/task/user/execution', $ctrl_dir .'WorkflowController@postUserTaskExecution');
    Route::any('workflow/task/user/screen', $ctrl_dir .'UIBController@index');
    Route::any('workflow/task/user/screen/{tt_id}', $ctrl_dir .'UIBController@getTaskScreen');
    Route::any('workflow/instance/list/{wft_id}', $ctrl_dir .'WorkflowController@wfInstanceList');
    Route::post('workflow/workflowfile', $ctrl_dir .'WorkflowController@postFile');
    Route::post('workflow/wfsjson', $ctrl_dir .'WorkflowController@postJson');
    Route::post('workflow/uibfile', $ctrl_dir .'UIBController@postFile');
    Route::get('workflow/instance/generate/{id}', $ctrl_dir .'WorkflowController@wfGenerateInstance');
    Route::any('workflow/instance/{wfi_id}', $ctrl_dir . 'WorkflowController@wfInstanceDetail');
    Route::get('workflow/task/instance/{ti_id}/{pti_id}', $ctrl_dir . 'WorkflowController@getTIDetail');
    Route::get('workflow/task/instance/update/state/{wfi_id}/{state_id}', $ctrl_dir . 'WorkflowController@updateTIStatus');
    Route::get('workflow/{id}', $ctrl_dir . 'WorkflowController@wfDetail');


    Route::get('workflow/tmp/driver', $ctrl_dir . 'WorkflowController@wftDriverDetail');
    Route::get('workflow/tmp/driver/update/{ti_id}/{status}', $ctrl_dir . 'WorkflowController@wftDriverUpdate');
    Route::get('workflow/tmp/user', $ctrl_dir . 'WorkflowController@wftUserDetail');
    Route::get('workflow/tmp/user/update/{ti_id}/{status}', $ctrl_dir . 'WorkflowController@wftUserUpdate');
    Route::any('workflow/instance/transaction/{wfi_id}', $ctrl_dir . 'WorkflowController@wfTransactionDetail');
    */
});
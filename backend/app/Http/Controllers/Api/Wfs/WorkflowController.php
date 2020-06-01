<?php
namespace App\Http\Controllers\Api\Wfs;


use App\Http\Controllers\Controller;
use App\Http\Models\WFSWorkflowInstance;
use App\Libraries\OrderHelper;
use App\Libraries\Wfs\WorkflowGenerator;
use App\Libraries\Wfs\WFSTaskExecution;
use App\Libraries\Wfs\UIBTileGenerator;
use App\Libraries\WfsEntityWrapper;
use Illuminate\Http\Request;

use View;
use Cache;
use Input;
use Validator;
//use Request;

// load models
use App\Http\Models\WFSWorkflowTemplate;
use App\Http\Models\WFSTaskTemplate;
use App\Http\Models\WFSWFTTTRelation;
use App\Http\Models\WFSWFTAccess;
use App\Http\Models\WFSTTAccess;
use App\Http\Models\UIBScreen;
use App\Http\Models\UsrProfile;

class WorkflowController extends Controller {

	private $_assignData = array(
		'pDir' => '',
		'dir' => 'workflow/'
	);
	private $_users = array('admin' => 1,'user' => 2);
	private $_footerData = array();
	private $_layout = "";

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->_apiData['error'] = 0;
		$this->_apiData['message'] = "error";
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return "Hello World";		
	}
	
	/**
	 * All Workflow list
	 *
	 * @return HTML
	*/
	public function wfList()
	{
		$rows = WFSWorkflowTemplate::selectWFList();
		//print_r($this->__models);exit;
		//$this->_layout .= view($this->_assignData["dir"]."/list", $this->_assignData)->with($this->__models);
		$this->_layout .= view($this->_assignData["dir"]."/list")->with('data',$rows);
		return $this->_layout;
	}

	/**
	 * All Workflow list
	 *
	 * @return HTML
	*/
	public function wfInstanceList($wft_id)
	{
		$rows = WFSWorkflowTemplate::selectWFInstanceListByWFTId($wft_id);
		$this->_layout .= view($this->_assignData["dir"]."/instancelist")->with('data',$rows);
		return $this->_layout;
	}

	/**
	 * All Workflow list
	 *
	 * @return HTML
	*/
	public function wfDetail($wft_id)
	{
		$rows = WFSWorkflowTemplate::selectWFDetail($wft_id);
		$obj_wfg = new WorkflowGenerator();
		$obj_wfg->generateJSON($rows);
		print_r($rows);exit;
		$this->_layout .= view($this->_assignData["dir"]."/list")->with('data',$rows);
		return $this->_layout;
	}

	/**
	 * All Workflow list
	 *
	 * @return HTML
	*/

	public function wftDriverDetail(Request $request)
	{
		// now i am a driver department
		// have to get work flow tasks
		$url = 'system/entities/listing';
		$method = 'get';
		$params['entity_type_id'] = 28;
		//$params['wf_ti_id'] = ;
		$params['assign_id'] = 27;
		$params['mobile_json'] = 1;
		//$params['assign_type'] = 'department';
		$resp = $this->__internalCall($request, $url, $method, $params,false);
		$wf_ti_ids = array();
		foreach($resp['data']['WF_Entity_Relation'] as $row){
			if(!empty($row['wf_ti_id']))
				$wf_ti_ids[] = $row['wf_ti_id'];
		}
		//print_r($wf_ti_ids);
		$ti_data = WFSTaskTemplate::getTIDetails($wf_ti_ids);
		//print_r($ti_data);
		$html = '';
		foreach($ti_data as $ti){
			//$ti->title;
			$html .= '<tr role="row">
		<div><td>['.$ti->ti_id.']</td>
		<td>'.$ti->wfi_title.']>'.$ti->ti_title.'</td>
		<td></td>';
			if(!empty($ti->successor)) {
				if($ti->wfs_state_id == 1) {
					$html .= '<td>
			[<a type="button" href="http://localhost/ntrust/workflow/tmp/driver/update/' . $ti->ti_id . '/2" title="Yes">Approved</a>]
			<span>|</span>
			[<a type="button" href="http://localhost/ntrust/workflow/tmp/driver/update/' . $ti->ti_id . '/3" title="No">Declined</a>]
		</td>';
				}else{
					$state = ($ti->wfs_state_id == 2)? 'Approved':'Declined';
					$html .= '<td>
			[<a type="button" href="#" title="Yes">'.$state.'</a>]
		</td>';
				}
			}
			$html .= '</div>
		</tr>';
		}
		return $html;
	}

	public function wftUserDetail(Request $request)
	{
		// now i am a driver department
		// have to get work flow tasks
		$url = 'system/entities/listing';
		$method = 'get';
		$params['entity_type_id'] = 28;
		//$params['wf_ti_id'] = ;
		$params['assign_id'] = 23;
		$params['mobile_json'] = 1;
		//$params['assign_type'] = 'department';
		$resp = $this->__internalCall($request, $url, $method, $params,false);
		$wf_ti_ids = array();
		foreach($resp['data']['WF_Entity_Relation'] as $row){
			if(!empty($row['wf_ti_id']))
				$wf_ti_ids[] = $row['wf_ti_id'];
		}
		//print_r($wf_ti_ids);
		$ti_data = WFSTaskTemplate::getTIDetails($wf_ti_ids);
		//print_r($ti_data);exit;
		$html = '';
		foreach($ti_data as $ti){
			//$ti->title;
			$html .= '<tr role="row">
		<div><td>['.$ti->ti_id.']</td>
		<td>'.$ti->wfi_title.']>'.$ti->ti_title.'</td>
		<td></td>';
		if(!empty($ti->successor)) {
			if($ti->wfs_state_id == 1) {
				$html .= '<td>
			[<a type="button" href="http://localhost/ntrust/workflow/tmp/user/update/' . $ti->ti_id . '/2" title="Yes">Approved</a>]
			<span>|</span>
			[<a type="button" href="http://localhost/ntrust/workflow/tmp/user/update/' . $ti->ti_id . '/3" title="No">Declined</a>]
		</td>';
			}else{
				$state = ($ti->wfs_state_id == 2)? 'Approved':'Declined';
				$html .= '<td>
			[<a type="button" href="#" title="Yes">'.$state.'</a>]
		</td>';
			}
		}else{
			$state = 'Finished';
			$html .= '<td>[<a type="button" href="#" title="Finished">Finished</a>]</td>';
		}
		$html .= '</div>
		</tr>';
		}
		print $html;
	}

	public function wftDriverUpdate(Request $request, $ti_id, $state_id)
	{
		//print_r($ti_id);
		//print_r($state_id);
		// have to update work flow task status
		// change status of the task
		$result = WFSTaskTemplate::getTIDetail($ti_id);
		$result_u = WFSTaskTemplate::updateTIState($ti_id,$state_id,$result);
		if(empty($result_u['next_node'])) {
			print 'Already state has been updated';
			exit;
		}

		if(!empty($ti_id)){
			// have to work on the implementation of TI attemps ??????????
			//WFSTaskTemplate::updateTIAttemps($ti_id);
		}

		$result[0]->state = strtolower($result[0]->state);
		$result[0]->is_expired = 0;
		//dd($result[0]->successor);
		$objTaskExec = new WFSTaskExecution($result);
		//if(!empty($objTaskExec->data[0]->successor))
		//dd($objTaskExec->objDic);
		if(!empty($objTaskExec->objDic->successor->getValue()))
			$objTaskExec->successors = WFSTaskTemplate::getTISuccessorList($objTaskExec->objDic->wfi_id->getValue(), $objTaskExec->objDic->successor->getValue());
		$objTaskExec->execute();
		$ti_id = $objTaskExec->generateNextNode();
		if(!empty($ti_id)) {
			$ti_detail = WFSTaskTemplate::getTIDetail($ti_id);
			$this->__assignWFSTask($request, $ti_detail, $ti_id);
		}
		//print_r($objTaskExec->data);

		// now have to generate next node task if node is not empty.
		print 'Thanks for completing your task.';
		exit;

		$response = $this->_wfGenerateInstance($result['node_id']);
		$wfi_id = $response['wfi_id'];
		$start_ti_id = reset($response['ti_ids']);
		//return redirect("workflow/instance/$wfi_id");
		return redirect("workflow/task/instance/$start_ti_id/0");

		// and initiate the next task and also assign it to them.

	}

	public function wftUserUpdate(Request $request)
	{
		$ti_id = $request->ti_id;
		$state_id = $request->state_id;
		$user_id = $request->user_id;
		$department_id = $request->department_id;
		$group_id = $request->role_id;
		$is_admin = $request->is_admin;

		// have to update work flow task status
		// change status of the task
		$result = WFSTaskTemplate::getTIDetail($ti_id);
		if(count($result) <= 0){
			$this->_apiData['error'] = 1;
			$this->_apiData['message'] = "No task is found to update.";
			return $this->_apiData;
		}
		$assign_coll = explode('.',$result[0]->assign_to);
		if((!$is_admin)) {
			if ($assign_coll[0] != '*' && $assign_coll[0] != $department_id) {
				$this->_apiData['error'] = 1;
				$this->_apiData['message'] = "Task is not belong to your department";
				return $this->_apiData;
			}
			if ($assign_coll[1] != '*' && $assign_coll[1] != $group_id) {
				$this->_apiData['error'] = 1;
				$this->_apiData['message'] = "Task is not belong to your group";
				return $this->_apiData;
			}
		}
		if($assign_coll[2] == '*'){
			$this->_apiData['error'] = 1;
			$this->_apiData['message'] = "Task is not yet assigned";
			return $this->_apiData;
		}
		if($assign_coll[2] != $user_id){
			$this->_apiData['error'] = 1;
			$this->_apiData['message'] = "Task is not belong to you.";
			return $this->_apiData;
		}

		$result_u = WFSTaskTemplate::updateTIState($ti_id,$state_id,$result);

		if(empty($result_u['next_node']) && strtolower($result_u['state']->state) != 'approved') {
			//$this->_apiData['error'] = 1;
			//$this->_apiData['message'] = "Task state already has been updated";
			//return $this->_apiData;
		}

		if(!empty($ti_id)){
			// working on the implementation of TI attemps
			//WFSTaskTemplate::updateTIAttemps($ti_id);
		}

		$result[0]->state = strtolower($result_u['state']->state);
		$result[0]->wfs_state_id = $state_id;
		$result[0]->is_expired = 0;
		//dd($result[0]->successor);
		$objTaskExec = new WFSTaskExecution($result);
		//if(!empty($objTaskExec->data[0]->successor))
		//dd($objTaskExec->objDic);
		if(!empty($objTaskExec->objDic->successor->getValue()))
			$objTaskExec->successors = WFSTaskTemplate::getTISuccessorList($objTaskExec->objDic->wfi_id->getValue(), $objTaskExec->objDic->successor->getValue());
		$objTaskExec->execute();
		$ti_id = $objTaskExec->generateNextNode();

		$this->_apiData['message'] = "Success";
		$order_state = ($state_id == 2) ? 'pending': 'canceled';

		// Call to verify external call on generation of new node

		if(!empty($objTaskExec->data[0]->next_node_external_url)){

			$order_helper = new OrderHelper();

			$order_department = str_replace(' ', '_', strtolower($objTaskExec->data[0]->ti_title))."_approved";
			$approved_order_department_id = $order_helper->getOrderStatusIdByKeyword($order_department);

			$order_department = str_replace(' ', '_', strtolower($objTaskExec->data[0]->next_node))."_$order_state";
			$order_department_id = $order_helper->getOrderStatusIdByKeyword($order_department);

			$objTaskExec->data[0]->user_id = $user_id;
			$objTaskExec->data[0]->department_id = $department_id;
			$objTaskExec->data[0]->role_id = $group_id;
			$objTaskExec->data[0]->order_status = $approved_order_department_id;


			WfsEntityWrapper::entityTrigger($objTaskExec->data[0]); //$approved_order_department_id

			$objTaskExec->data[0]->order_status = $order_department_id;
			WfsEntityWrapper::entityTrigger($objTaskExec->data[0]);

		}elseif(!empty($objTaskExec->data[0]->external_url)){
			$order_state = ($state_id == 2) ? 'approved': 'canceled';

			$order_helper = new OrderHelper();

			$order_department = str_replace(' ', '_', strtolower($objTaskExec->data[0]->ti_title))."_$order_state";
			$order_department_id = $order_helper->getOrderStatusIdByKeyword($order_department);

			//print "$order_department--$order_department_id";

			$objTaskExec->data[0]->user_id = $user_id;
			$objTaskExec->data[0]->department_id = $department_id;
			$objTaskExec->data[0]->role_id = $group_id;
			$objTaskExec->data[0]->order_status = $order_department_id;
			$objTaskExec->data[0]->next_node_instance_id = $objTaskExec->data[0]->ti_id;
			$objTaskExec->data[0]->next_node = $objTaskExec->data[0]->ti_title;
			$objTaskExec->data[0]->next_node_external_url = $objTaskExec->data[0]->external_url;

			WfsEntityWrapper::entityTrigger($objTaskExec->data[0]);
		}

		$this->_apiData['data'] = $objTaskExec->data[0];
		return $this->_apiData;

		if(!empty($ti_id)) {
			$ti_detail = WFSTaskTemplate::getTIDetail($ti_id);
			//$this->__assignWFSTask($request, $ti_detail, $ti_id);
		}
		//print_r($objTaskExec->data);

		// now have to generate next node task if node is not empty.
		//print 'Thanks for completing your task.';
		//exit;

		$response = $this->_wfGenerateInstance($result[0]->next_node_id);
		$wfi_id = $response['wfi_id'];
		$start_ti_id = reset($response['ti_ids']);



		//return redirect("workflow/instance/$wfi_id");
		//return redirect("workflow/task/instance/$start_ti_id/0");

		// and initiate the next task and also assign it to them.

	}

	public function assignUserUpdate(Request $request)
	{
		/*print_r($request->all());
		exit;*/
		$ti_id = $request->ti_id;
		$user_id = $request->user_id;
		$department_id = $request->department_id;
		$group_id = $request->role_id;
		$is_admin = $request->is_admin;
		// have to update work flow task status
		$result = WFSTaskTemplate::getTIDetail($ti_id);
		if(count($result) <= 0){
			$this->_apiData['error'] = 1;
			$this->_apiData['message'] = "No task is found.";
			return $this->_apiData;
		}
		$assign_coll = explode('.',$result[0]->assign_to);
		//$department_id = 3;
		//$group_id = 18;
		if((!$is_admin)) {
			if ($assign_coll[0] != '*' && $assign_coll[0] != $department_id) {
				$this->_apiData['error'] = 1;
				$this->_apiData['message'] = "Task is not belong to your department";
				return $this->_apiData;
			}
			if ($assign_coll[1] != '*' && $assign_coll[1] != $group_id) {
				$this->_apiData['error'] = 1;
				$this->_apiData['message'] = "Task is not belong to your group";
				return $this->_apiData;
			}
		}
		if($assign_coll[2] != '*'){
			$this->_apiData['error'] = 1;
			$this->_apiData['message'] = "Task already assigned";
			return $this->_apiData;
		}

		$assign_coll[2] = $user_id;
		$assign_to = implode('.',$assign_coll);
		WFSTaskTemplate::assignTI($ti_id, $assign_to);

		//print_r($result);
		//exit;

		/*$order_helper = new OrderHelper();

		$order_department = str_replace(' ', '_', strtolower($result[0]->ti_title))."_approved";
		$order_department_id = $order_helper->getOrderStatusIdByKeyword($order_department);

		//print "$order_department--$order_department_id";

		$resp = new \stdClass();
		$resp->user_id = $user_id;
		$resp->department_id = $department_id;
		$resp->role_id = $group_id;
		$resp->order_status = $order_department_id;
		$resp->next_node_instance_id = $result[0]->ti_id;
		$resp->next_node = $result[0]->ti_title;
		$resp->assign_entity_type_id = $result[0]->assign_entity_type_id;
		$resp->assign_entity_id = $result[0]->assign_entity_id;
		$resp->next_node_external_url = 'api/system/entities/update';

		WfsEntityWrapper::entityTrigger($resp);*/

		$this->_apiData['message'] = "Success";
		return $this->_apiData;

	}

	/**
	 * Make workflow instance
	 *
	 * @return HTML
	*/
	public function wfGenerateInstance($wft_id)
	{
		/*$start_date = date('Y-m-d H:m:s');
		$end_date = date('Y-m-d 23:59:59');
		$assign_entity_id = 1;
		$assign_entity_type_id = 1;
		$assign_by_id = 1;
		$activation_state = 1;

		$wfi_id = WFSWorkflowTemplate::insertInstance($wft_id,$start_date,$end_date,$activation_state);
		$ti_ids = WFSTaskTemplate::insertWFSInstance($wft_id,$start_date,$end_date,$assign_entity_id,$assign_entity_type_id,$assign_by_id,$activation_state);
		WFSWFTTTRelation::insertWFSInstance($wfi_id,$ti_ids);*/
		$this->_wfGenerateInstance($wft_id);
		return redirect("workflow/instance/list/$wft_id");
	}

	/**
	 * Make workflow instance
	 *
	 * @return HTML
	*/
	private function _wfGenerateInstance($wft_id)
	{
		$start_date = date('Y-m-d H:m:s');
		$end_date = date('Y-m-d 23:59:59');
		$assign_entity_id = 1;
		$assign_entity_type_id = 1;
		$assign_by_id = 1;
		$activation_state = 1;

		$wfi_id = WFSWorkflowTemplate::insertInstance($wft_id,$start_date,$end_date,$activation_state);
		$ti_ids = WFSTaskTemplate::insertWFSInstance($wft_id,$start_date,$end_date,$assign_entity_id,$assign_entity_type_id,$assign_by_id,$activation_state);
		WFSWFTTTRelation::insertWFSInstance($wfi_id,$ti_ids);

		$response['wft_id'] = $wft_id;
		$response['wfi_id'] = $wfi_id;
		$response['ti_ids'] = $ti_ids;

		return $response;
	}

	/**
	 * View workflow instance detail
	 *
	 * @return HTML
	*/
	public function wfInstanceDetail($wfi_id)
	{
		$result = WFSWorkflowTemplate::getDetailWFInstanceListByWFIId($wfi_id);
		$this->_layout .= view($this->_assignData["dir"]."/wfi_detail")->with('data',$result);
		return $this->_layout;

	}

	/**
	 * View workflow instance detail
	 *
	 * @return HTML
	*/
	public function wfTransactionDetail($wfi_id)
	{
		$result = WFSWorkflowInstance::getWITransaction($wfi_id);
		$this->_layout .= view($this->_assignData["dir"]."/ti_screen_transaction_detail")->with('data',$result);
		return $this->_layout;

	}

	/**
	 * View workflow instance detail
	 *
	 * @return HTML
	*/
	public function getTIDetail($ti_id,$pti_id = 0)
	{
		if(!empty($pti_id)){
			WFSTaskTemplate::updateTIAttemps($pti_id);
		}
		$result = WFSTaskTemplate::getTIDetail($ti_id);

		$result[0]->state = strtolower($result[0]->state);
		$result[0]->is_expired = 0;
        //dd($result[0]->successor);
		$objTaskExec = new WFSTaskExecution($result);
		//if(!empty($objTaskExec->data[0]->successor))
		//dd($objTaskExec->objDic);
		if(!empty($objTaskExec->objDic->successor->getValue()))
			$objTaskExec->successors = WFSTaskTemplate::getTISuccessorList($objTaskExec->objDic->wfi_id->getValue(), $objTaskExec->objDic->successor->getValue());
		$objTaskExec->execute();

		/*
		 * get data and successors of the node
		 * have to give the option of the selection of state or node may be on yes or no
		 * have to create next task instance on selection of yes or no ..
		 *
		 * have to save multiple assignees on single node, and also comments and also save wf_task_instance_id and also wft_id
		 * */


		//--- end here ----
		/*if(($objTaskExec->decisonConstraints['next_node'] == 'Declined' && $objTaskExec->data[0]->state == 'pending') || ($objTaskExec->decisonConstraints['is_expired'] && $objTaskExec->data[0]->state == 'pending'))
			$this->updateTIStatus($objTaskExec->data[0]->wfi_id,3);*/
		//dd($objTaskExec);
		/*

		if($objTaskExec->decisonConstraints['is_redirection'] && !empty($objTaskExec->decisonConstraints['redirect_url']))
			return redirect($objTaskExec->decisonConstraints['redirect_url']);
*/
		//$user_data = UsrProfile::getUserProfileById($objTaskExec->data[0]->assign_entity_id);
		//dd($objTaskExec->data[0]);
		//print $objTaskExec->data[0]->tt_id;exit;
		$screen_data = UIBScreen::getTaskScreen($objTaskExec->data[0]->tt_id);
        $tile_g_data = array();
        //$tile_g_data['user_data'] = $user_data;
        $tile_g_data['screen_data'] = $screen_data;
        $tile_g_data['task_data'] = $objTaskExec->data[0];
		//dd($tile_g_data);
        $obj_tile_generator = new UIBTileGenerator($tile_g_data);
        $tile_process_data = $obj_tile_generator->getProcessedData();
        $view_Data['task_data'] = $objTaskExec->data;
        $view_Data['screen_data'] = $tile_process_data;
        //print_r($view_Data);exit;
		$this->_layout .= view($this->_assignData["dir"]."/ti_screen_detail")->with('data',$view_Data);
		return $this->_layout;
	}

	public function getTIDetail_bkp($ti_id,$pti_id = 0)
	{
		if(!empty($pti_id)){
			WFSTaskTemplate::updateTIAttemps($pti_id);
		}
		$result = WFSTaskTemplate::getTIDetail($ti_id);

		$result[0]->state = strtolower($result[0]->state);
		$result[0]->is_expired = 0;
        //dd($result[0]->successor);
		$objTaskExec = new WFSTaskExecution($result);
		//if(!empty($objTaskExec->data[0]->successor))
		//dd($objTaskExec->objDic);
		if(!empty($objTaskExec->objDic->successor->getValue()))
			$objTaskExec->successors = WFSTaskTemplate::getTISuccessorList($objTaskExec->objDic->wfi_id->getValue(), $objTaskExec->objDic->successor->getValue());
		$objTaskExec->execute();
		if(($objTaskExec->decisonConstraints['next_node'] == 'Declined' && $objTaskExec->data[0]->state == 'pending') || ($objTaskExec->decisonConstraints['is_expired'] && $objTaskExec->data[0]->state == 'pending'))
			$this->updateTIStatus($objTaskExec->data[0]->wfi_id,3);

		if($objTaskExec->decisonConstraints['is_redirection'] && !empty($objTaskExec->decisonConstraints['redirect_url']))
			return redirect($objTaskExec->decisonConstraints['redirect_url']);

		$user_data = UsrProfile::getUserProfileById($objTaskExec->data[0]->assign_entity_id);
		//print $objTaskExec->data[0]->tt_id;exit;
		$screen_data = UIBScreen::getTaskScreen($objTaskExec->data[0]->tt_id);
        $tile_g_data = array();
        $tile_g_data['user_data'] = $user_data;
        $tile_g_data['screen_data'] = $screen_data;
        $tile_g_data['task_data'] = $objTaskExec->data[0];
		//dd($tile_g_data);
        $obj_tile_generator = new UIBTileGenerator($tile_g_data);
        $tile_process_data = $obj_tile_generator->getProcessedData();
        $view_Data['task_data'] = $objTaskExec->data;
        $view_Data['screen_data'] = $tile_process_data;
        //print_r($view_Data);exit;
		$this->_layout .= view($this->_assignData["dir"]."/ti_screen_detail")->with('data',$view_Data);
		return $this->_layout;
	}

	public function updateTIStatus($wfi_id,$state_id)
	{
		$result = WFSTaskTemplate::updateTIState($wfi_id,$state_id);
		if(empty($result['next_node'])) {
			$start_ti_id = $result['node_id'];
			return redirect("workflow/task/instance/$start_ti_id/0");
		}
		$response = $this->_wfGenerateInstance($result['node_id']);
		$wfi_id = $response['wfi_id'];
		$start_ti_id = reset($response['ti_ids']);
		//return redirect("workflow/instance/$wfi_id");
		return redirect("workflow/task/instance/$start_ti_id/0");
	}

	/*
	 * User task selection
	 *
	 * @return HTML
	*/
	public function userTaskSelection()
	{
		$request_type = WFSWorkflowTemplate::selectWFList();
		$this->_layout .= view($this->_assignData["dir"]."/multi_user")->with('data',$request_type);
		return $this->_layout;
	}

	/*
	 * User task Execution
	 *
	 * @return HTML
	*/
	public function postUserTaskExecution()
	{
		//print_r($_POST);exit;
		$wft_id =  $_POST['request_type_id'];
		$start_date = date('Y-m-d H:m:s');
		$end_date = date('Y-m-d 23:59:59');
		$assign_entity_id = $_POST['user_type'];
		$admin_id = 1;
		$assign_entity_type_id = 1;
		$assign_by_id = 1;
		$activation_state = 1;

        $wfi_id = WFSWorkflowTemplate::insertInstance($wft_id,$start_date,$end_date,$activation_state);
		$ti_ids = WFSTaskTemplate::insertWFSInstance($wft_id,$start_date,$end_date,$assign_entity_id,$assign_entity_type_id,$assign_by_id,$activation_state,$admin_id);
		WFSWFTTTRelation::insertWFSInstance($wfi_id,$ti_ids);
		$wfi_row = WFSWorkflowTemplate::getDetailWFInstanceListByWFIId($wfi_id);
		return redirect('workflow/task/instance/'.$wfi_row[0]->ti_id.'/0');
	}

	/**
	 * About us
	 *
	 * @return HTML
	*/
	public function about()
	{
		print 'This is a workflow module. Wfs';
		exit;
	}

	/**
	 * get Matrix X and Y axis of Workflow
	 *
	 * @return HTML
	*/
	public function getMatrix(Request $request)
	{
		$response = WFSWorkflowTemplate::getMatrix($request->type);
		if(count($response) <= 0){
			$this->_apiData['error'] = 1;
			$this->_apiData['message'] = "No matrix found against ". $request->type;
			return $this->_apiData;
		}
		$this->_apiData['message'] = "Success";
		$this->_apiData['data'] = $response;
		return $this->_apiData;
	}

	/**
	 * get Matrix X and Y axis of Workflow
	 *
	 * @return HTML
	*/
	public function getMatrixData(Request $request)
	{
		$param['user_id'] = $request->user_id;
		$param['role_id'] = $request->role_id;
		$param['department_id'] = $request->department_id;
		$param['is_admin'] = $request->is_admin;

		$param['delivery_date'] = $request->delivery_date;
		$param['delivery_start_time'] = $request->delivery_start_time;
		$param['delivery_end_time'] = $request->delivery_end_time;
		$param['session_department'] = $request->session_department;

		$response = WFSWorkflowTemplate::getMatrixData($request->type, $param);
		if(count($response) <= 0){
			/*$this->_apiData['error'] = 1;
			$this->_apiData['message'] = "No matrix data found against ". $request->type;*/
			$response = [];
			//return $this->_apiData;
		}
		$this->_apiData['message'] = "Success";
		$this->_apiData['data'] = $response;
		return $this->_apiData;
	}

	/**
	 * About us
	 *
	 * @return HTML
	*/
	public function login()
	{
		$this->_layout .= view($this->_assignData["dir"]."/".__FUNCTION__, $this->_assignData)->with($this->__models);
		return $this->_layout;
	}

	/**
	 * postWorkFlowFile
	 *
	 * @return HTML
	*/
	public function postFile()
	{

		$path = $this->_manageUploadFile('fileToUpload');
		$workflow_content = $this->_getFileContent($path);

        $this->_wfsArrayParsing($workflow_content);

		return redirect('workflow/list');
	}

    private function _wfsArrayParsing($workflow_content)
    {
        $workflow_container = array();
        $task_container = array();
        $workflow_task_successor_container = array();
        $inserted_workflow_container = array();
        $inserted_workflow_start_end_task_container = array();
        $counter = 0;
        $wft_id = 0;
        foreach($workflow_content as $row) {
            if($counter == 0 || count($row) < 35){
                $counter++;
                continue;
            }

            if(!array_key_exists ( $row[0] , $workflow_container )) {
                $result = $this->_insertWFT($row);
                $wft_id = $result['id'];
                $workflow_container[$row[0]] = $result['id'];
                $inserted_workflow_container[] = $result;
                $inserted_workflow_start_end_task_container[$wft_id] = array('start_task' => $row[9],'end_task' => $row[10]);
            }
            if(empty($row[16])){ //Task Title
                continue;
            }
            $result = $this->_insertTT($row);
            $row['wft_id'] = $wft_id;
            $row['tt_id'] = $result['id'];
            $task_container[$row[16]] = $result['id'];

            if(!empty($row[19])) // verifying successor
                $workflow_task_successor_container[$result['id']] = array('wft_id' => $wft_id,'successor' => $row[19]);

            if(!empty($row[0]) && !empty($result['id'])) // linking when task and WF ids available
                $this->_insertWFSTTRelation($row);
            $counter++;
        }

        $this->_updateWFTTTRelationSuccessor($task_container, $workflow_task_successor_container);
        $this->_updateWFTStartEndTask($task_container, $inserted_workflow_start_end_task_container);

        return;
    }

	/**
	 * postWorkFlowFile
	 *
	 * @return HTML
	*/
	public function postJson()
	{
        $obj_wfg = new WorkflowGenerator();
        $workflow_content = $obj_wfg->parseJSON(json_decode($_POST['txtJSON'],true));
        $this->_wfsArrayParsing($workflow_content);
        return redirect('workflow/list');
	}

	private function _insertWFT($row)
	{
		$title = $row[0];
		$type = $row[1];
		$wfs_state_id = $row[2];
		$created_by_id = $row[3];
		$status_id = $row[4];
		$description = $row[5];
		$pre_condition = $row[6];
		$post_condition = $row[7];
		$successor_wft_id = $row[8];
		$start_tt_id = $row[9];
		$end_tt_id = $row[10];
		$access_arr = (empty($row[11]))? array() : explode(',',$row[11]);
		$access_type = $row[12];
		$can_copy = $row[13];
		$can_delete = $row[14];
		$can_start = $row[15];

		$result = WFSWorkflowTemplate::insert( $title, $type, $wfs_state_id, $created_by_id, $status_id, $description, $pre_condition, $post_condition, $successor_wft_id, $start_tt_id, $end_tt_id);
        //print_r($row);print_r($access_arr);exit;
		foreach($access_arr as $access) {
			WFSWFTAccess::insert($result, $this->_users[$access], $access_type, $can_copy, $can_delete, $can_start);
		}
		return array('id' => $result, 'title' => $title);
	}

	private function _insertTT($row)
	{
		$title = $row[16];
		$task_type_id = $row[17];
		$wfs_state_id = $row[18];
		$successor_tt_id = $row[19];
		$pre_conditions = $row[20];
		$post_conditions = $row[21];
		$actions = $row[22];
		$params = $row[23];
		$retry_limit = $row[24];
		$expiry_duration = $row[25];
		$expiry_type = $row[26];
		$created_by_id = $row[27];
		$status_id = $row[28];
		$description = $row[29];
		$access_arr = (empty($row[30]))? array() : explode(',',$row[30]);
		$access_type = $row[31];
		$can_copy = $row[32];
		$can_delete = $row[33];
		$can_start = $row[34];
		$assign_to = $row[35];

		$result = WFSTaskTemplate::insert( $title, $task_type_id, $wfs_state_id, $pre_conditions, $post_conditions, $actions, $params, $retry_limit, $expiry_duration, $expiry_type, $created_by_id, $status_id, $description,$assign_to);
		foreach($access_arr as $access) {
			WFSTTAccess::insert($result, $this->_users[$access], $access_type, $can_copy, $can_delete, $can_start);
		}
		return array('id' => $result, 'title' => $title);
	}

	private function _insertWFSTTRelation($row)
	{
		$wft_id = $row['wft_id'];
		$tt_id = $row['tt_id'];
		$successor = $row[19];
		//$successor_tt_id = $row[19];
		$pre_conditions = $row[20];
		$post_conditions = $row[21];
		$actions = $row[22];
		$params = $row[23];
		$retry_limit = $row[24];
		$expiry_duration = $row[25];
		$expiry_type = $row[26];

		$result = WFSWFTTTRelation::insert( $wft_id, $tt_id, $successor, $pre_conditions, $post_conditions, $actions, $params, $retry_limit, $expiry_duration, $expiry_type);
		return $result;
	}

	private function _updateWFTTTRelationSuccessor($task_container, $successor_container)
	{
		foreach($successor_container as $tt_id => $successor_data){
			$successor_ids = [];
			$successors = explode(',',$successor_data['successor']);

			foreach($successors as $successor) {
				if(isset($task_container[$successor]))
					$successor_ids[] = $task_container[$successor];
			}

			WFSWFTTTRelation::updateData( $successor_data['wft_id'], $tt_id, array('successor' => implode(',',$successor_ids)));
		}
		return;
	}

	private function _updateWFTStartEndTask($task_container, $workflow_start_end_task_container)
	{
		foreach($workflow_start_end_task_container as $wft_id => $workflow_start_end_task){

			$start_task = $task_container[$workflow_start_end_task['start_task']];
			$end_task_ids = [];
			$end_tasks = explode(',',$workflow_start_end_task['end_task']);

			foreach($end_tasks as $end_task) {
				if(isset($task_container[$end_task]))
					$end_task_ids[] = $task_container[$end_task];
			}

			WFSWorkflowTemplate::updateData( $wft_id, array('start_tt_id' => $start_task,'end_tt_id' => implode(',',$end_task_ids)));
		}
		return;
	}

	private function _manageUploadFile($field_path,$storage_folder = 'uploads')
	{
		$destinationPath = storage_path($storage_folder); // upload path
		$extension = Input::file($field_path)->getClientOriginalExtension(); // getting file extension
		$fileName = Input::file($field_path)->getClientOriginalName(); // getting file extension
		$fileName = rand(11111,99999) . '_' . $fileName; // renameing file

		$upload_success = Input::file($field_path)->move($destinationPath, $fileName); // uploading file to given path

		return  storage_path("$storage_folder/$fileName");
	}

	private function _getFileContent($file_path)
	{
		$workflow_content = array();
		$file_resource = fopen($file_path,"r");

		while(!feof($file_resource))
		{
			$row = array();
			$tmp = fgetcsv($file_resource);
			for($i=0; $i<count($tmp); $i++){
				$row[] = trim($tmp[$i]);
			}
			$workflow_content[] = $row;
		}

		fclose($file_resource);
		return $workflow_content;
	}

}

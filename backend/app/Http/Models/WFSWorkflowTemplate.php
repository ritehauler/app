<?php namespace App\Http\Models;

use App\Libraries\CustomHelper;
use App\Libraries\OrderHelper;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use Illuminate\Http\Request;
// init models
//use App\Http\Models\Conf;

class WFSWorkflowTemplate extends Base {
	
	use SoftDeletes;
    protected $table = 'wfs_work_flow_template';
    protected static $static_table = 'wfs_work_flow_template';
    public $timestamps = false;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
         $this->__fields   = array($this->primaryKey, 'title', 'type', 'wfs_state_id', 'created_by_id', 'status_id', 'description', 'pre_condition', 'post_condition', 'successor_wft_id', 'start_tt_id', 'end_tt_id', 'created_at');
	}


	/**
	 *
	 */
	public static function insert( $title, $type, $wfs_state_id, $created_by_id, $status_id, $description, $pre_condition, $post_condition, $successor_wft_id, $start_tt_id, $end_tt_id){

		$obj = WFSWorkflowTemplate::create();

		$obj->title				= $title;
		$obj->type				= $type;
		$obj->wfs_state_id		= $wfs_state_id;
		$obj->created_by_id		= $created_by_id;
		$obj->status_id			= $status_id;
		$obj->description		= $description;
		$obj->pre_condition		= $pre_condition;
		$obj->post_condition	= $post_condition;
		$obj->successor_wft_id	= $successor_wft_id;
		$obj->start_tt_id		= $start_tt_id;
		$obj->end_tt_id			= $end_tt_id;
		$obj->created_at		= $obj->freshTimestampString();

		$obj->save();

		if($obj->save()){
			return $obj->id;
		}
		return false;
	}

	/**
	 *
	 */
	public static function updateData($id, $data){

		$save = array();
		DB::table(self::$static_table)
			->where('id', $id)
			->update($data);
		return;
	}

	/**
	 * List of All Workflows
	 */
	public static function selectWFList(){

		$save = array();
		$result = DB::select("SELECT id, title FROM wfs_work_flow_template WHERE type != 'sequential' ORDER BY ID DESC");
		return $result;
	}

	/**
	 * List of All Workflows By WFT_ID
	 */
	public static function selectWFInstanceListByWFTId($wft_id){

		$result = DB::select("SELECT id, title FROM wfs_work_flow_instance WHERE wft_id = $wft_id ORDER BY ID DESC");
		return $result;
	}

	/**
	 * List of All Workflows By WFI_ID
	 */
	public static function getDetailWFInstanceListByWFIId($wfi_id){

		$result = DB::select("SELECT wfs_work_flow_instance.id, wfs_work_flow_instance.title, wfs_work_flow_instance.start_tt_id, wfs_work_flow_instance.end_tt_id,
wfs_work_flow_instance.description, wfs_task_instance.id AS ti_id
FROM wfs_work_flow_instance
LEFT JOIN wfs_wfi_ti_relation ON  wfs_wfi_ti_relation.wfi_id = wfs_work_flow_instance.id
LEFT JOIN wfs_task_instance ON  wfs_task_instance.id = wfs_wfi_ti_relation.ti_id
WHERE wfs_work_flow_instance.id = $wfi_id AND wfs_task_instance.tt_id = wfs_work_flow_instance.start_tt_id
 ORDER BY wfs_work_flow_instance.id DESC");
		return $result;
	}

	/**
	 * View detail of Workflow
	 */
	public static function selectWFDetail($wft_id){

		$save = array();
		$result = DB::select("SELECT wfs_work_flow_template.id AS wft_id, wfs_work_flow_template.title AS wft_title,
wfs_task_template.id AS tt_id, wfs_task_template.title AS tt_title, wfs_work_flow_template.start_tt_id, wfs_work_flow_template.end_tt_id,
wfs_wft_tt_relation.actions, wfs_wft_tt_relation.expiry_duration, wfs_wft_tt_relation.expiry_type,
wfs_wft_tt_relation.params, wfs_wft_tt_relation.post_conditions, wfs_wft_tt_relation.pre_conditions, wfs_wft_tt_relation.retry_limit,
wfs_wft_tt_relation.successor, wfs_wft_tt_relation.created_at FROM wfs_work_flow_template
LEFT JOIN wfs_wft_tt_relation ON  wfs_wft_tt_relation.wft_id = wfs_work_flow_template.id
LEFT JOIN wfs_task_template ON  wfs_task_template.id = wfs_wft_tt_relation.tt_id
WHERE wfs_work_flow_template.id = $wft_id");
		return $result;
	}

	/**
	 * Insert new instance of Workflow
	 */
	public static function insertInstance($wft_id,$start_date,$end_date, $initiator_id = 1){

		 DB::statement("INSERT INTO wfs_work_flow_instance ( wft_id,title,type,wfs_state_id,created_by_id,initiator_id,
        status_id,description,pre_condition,post_condition, successor_wft_id,start_tt_id,end_tt_id,start_date,end_date,created_at)
		(SELECT id,title,type,wfs_state_id,created_by_id,$initiator_id,status_id,description,pre_condition,post_condition,
		successor_wft_id,start_tt_id,end_tt_id,'$start_date','$end_date', NOW() AS created_at FROM wfs_work_flow_template
		 WHERE id = $wft_id)");

        $last_id = DB::getPdo()->lastInsertId();
		return $last_id;
	}

	public static function getFirstTemplateTaskById($wft_id){
		 $result = DB::select("SELECT wfs_work_flow_template.id AS wft_id, wfs_work_flow_template.title AS wft_title,
wfs_task_template.assign_to, wfs_task_template.id AS tt_id, wfs_task_template.title AS tt_title, wfs_work_flow_template.start_tt_id, wfs_work_flow_template.end_tt_id,
wfs_wft_tt_relation.actions, wfs_wft_tt_relation.expiry_duration, wfs_wft_tt_relation.expiry_type,
wfs_wft_tt_relation.params, wfs_wft_tt_relation.post_conditions, wfs_wft_tt_relation.pre_conditions, wfs_wft_tt_relation.retry_limit,
wfs_wft_tt_relation.successor, wfs_wft_tt_relation.created_at FROM wfs_work_flow_template
LEFT JOIN wfs_wft_tt_relation ON  wfs_wft_tt_relation.tt_id = wfs_work_flow_template.start_tt_id
LEFT JOIN wfs_task_template ON  wfs_task_template.id = wfs_work_flow_template.start_tt_id
WHERE wfs_work_flow_template.id = $wft_id");

		return (isset($result[0]))? $result[0] : [];
	}

	public static function getMatrix($title)
	{
		$result = DB::Select("SELECT * FROM kb_kanban WHERE title = '$title';");
		$response = [];
		foreach($result as $row){
			$response['title'] = $row->title;
			$response['x_axis'] = json_decode($row->x_axis)->as;
			$response['x_axis_detail'] = json_decode($row->x_axis);
			$response['y_axis'] = json_decode($row->y_axis)->as;
			$response['y_axis_detail'] = json_decode($row->y_axis);
		}

		return $response;
	}

	public static function getMatrixData($title, $data)
	{
		$axis_response = self::getMatrix($title);

		/*
		 * have to think on x-axis and y-axis implementation
		 * that they can communicate directly what is thr x-axis and y-axis.
		 * */

		$result = DB::Select("SELECT wfs_task_instance.id AS task_id, wfs_task_instance.title AS task_title, wfs_task_instance.assign_to,
wfs_task_instance.assign_entity_id, wfs_task_instance.assign_entity_type_id
FROM wfs_work_flow_instance
INNER JOIN wfs_wfi_ti_relation ON wfs_wfi_ti_relation.wfi_id = wfs_work_flow_instance.id
AND wfs_wfi_ti_relation.ti_id = (SELECT MAX(ti_id) FROM wfs_wfi_ti_relation WHERE wfs_wfi_ti_relation.wfi_id = wfs_work_flow_instance.id)
INNER JOIN wfs_task_instance ON wfs_task_instance.id = wfs_wfi_ti_relation.ti_id
WHERE wfs_work_flow_instance.title = '$title'  AND wfs_task_instance.wfs_state_id = 1
 order by task_id ");

		$response = [];
		$order_id_collector = [];
		$order_collector = [];
		$counter = 0;
		$url = 'api/system/entities';
		//$url = 'api/system/entities/listing';
		$request = Request();

		foreach($result as $row){
			$order_id_collector[$row->assign_entity_id] = $row->assign_entity_id;
			$order_collector[$row->assign_entity_id] = $row;
		}

		// get order detail
		$ordrer_result = DB::Select('SELECT of.*, saf.first_name, saf.last_name, saf.longitude, saf.latitude, saf.email,
						saf.street, saf.telephone, saf.customer_id
							FROM order_flat of
						#LEFT JOIN customer_flat cf ON cf.entity_id = of.customer_id
						LEFT JOIN shipping_address_flat saf ON saf.entity_id = of.shipping_address
						#LEFT JOIN billing_address_flat baf ON baf.entity_id = of.billing_address
						#LEFT JOIN order_discussion_flat odf on odf.order_id = of.entity_id
						 WHERE of.entity_id IN ('.implode(',',$order_id_collector) . ')
						 AND user_delivery_date = "'.$data['delivery_date'].'"
						 AND user_delivery_time >= "'.$data['delivery_start_time'].'"
						 AND user_delivery_time_end <= "'.$data['delivery_end_time'].'"'); //delivery_end_time

		$order_comments = []; //OrderHelper::getOrderDiscussion($order_id_collector);


		foreach($ordrer_result as $order_detail) {

			$row = $order_collector[$order_detail->entity_id];

			$fn_x = $axis_response['x_axis_detail']->condition . 'Kanban';
			$fn_y = $axis_response['y_axis_detail']->condition . 'Kanban';

			$x_axis = CustomHelper::$fn_x($axis_response['x_axis_detail'], $row);
			$y_axis = CustomHelper::$fn_y($axis_response['y_axis_detail'], $row);
			$response[$counter]['id'] = $row->task_id; // on department id movement
			$response[$counter]['callback_uri'] = CustomHelper::getKanbanCallBackUri($row, $data); // on department id movement
			$response[$counter]['callback_comment_uri'] = CustomHelper::getKanbanCommentCallBackUri($row, $data); // on department id movement
			$response[$counter]['is_assigned'] = $y_axis; // on department id movement
			$response[$counter]['position']['x_axis'] = $x_axis; // on department id movement
			$response[$counter]['position']['y_axis'] = $y_axis;
			$response[$counter]['matrix_cell'] = $row;

			$order_detail->payment_method_type = strtoupper($order_detail->payment_method_type);

			$timestamp = strtotime($order_detail->user_delivery_date);

			$day = date('l', $timestamp);
			$order_detail->delivery_time_slot = "$day -  " . date('H:i' ,strtotime($order_detail->user_delivery_time)) . " - " .date('H:i' ,strtotime($order_detail->user_delivery_time_end));
			$order_detail->discussion = [];

			/*if(isset($order_comments[$order_detail->entity_id]))
				$order_detail->discussion = $order_comments[$order_detail->entity_id];*/

			$response[$counter]['matrix_entity'] = $order_detail;

			$response[$counter]['cell_matrix_id'] = $order_detail->entity_id; // show cell detail
			$response[$counter]['callback_detail_cell_url'] = CustomHelper::getEntityCallBackUri($data['session_department']) . $order_detail->entity_id; // show cell detail

			$counter++;
		}
		return $response;
	}
}
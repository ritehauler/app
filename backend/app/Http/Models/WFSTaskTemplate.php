<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class WFSTaskTemplate extends Base {
	
	use SoftDeletes;
    protected $table = 'wfs_task_template';
    protected static $static_table = 'wfs_task_template';
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
         $this->__fields   = array($this->primaryKey, 'title', 'task_type_id', 'wfs_state_id', 'status_id', 'description',
			 				'pre_conditions', 'post_conditions', 'actions', 'params', 'retry_limit', 'created_by_id', 'expiry_duration',
			 				'assign_to', 'expiry_type', 'created_at');
	}


	/**
	 *
	 */
	public static function insert( $title, $task_type_id, $wfs_state_id, $pre_conditions, $post_conditions, $actions, $params, $retry_limit, $expiry_duration, $expiry_type, $created_by_id, $status_id, $description,$assign_to){

		$obj = WFSTaskTemplate::create();

		$obj->title				= $title;
		$obj->task_type_id		= $task_type_id;
		$obj->wfs_state_id		= $wfs_state_id;
		$obj->pre_conditions	= $pre_conditions;
		$obj->post_conditions	= $post_conditions;
		$obj->actions			= $actions;
		$obj->params			= $params;
		$obj->retry_limit		= $retry_limit;
		$obj->expiry_duration	= $expiry_duration;
		$obj->expiry_type		= $expiry_type;
		$obj->created_by_id		= $created_by_id;
		$obj->status_id			= $status_id;
		$obj->assign_to			= $assign_to;
		$obj->description 		= $description;
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
	 * Create instance
	 */
	public static function insertWFSInstance($wft_id,$start_date,$end_date,$assign_entity_id,$assign_entity_type_id,
										  $assign_by_id,$activation_state = 1,$admin_id = 1,$task_assign_to){
		$last_ids =array();
		$current_date = date('Y-m-d H:i:s');

		$tt_id_result = DB::select("SELECT wft_id,tt_id FROM wfs_wft_tt_relation
					INNER JOIN wfs_work_flow_template ON wfs_work_flow_template.start_tt_id = wfs_wft_tt_relation.tt_id
					WHERE wft_id = $wft_id");

		foreach($tt_id_result as $row) {
			$tt_id = $row->tt_id;

			DB::statement("INSERT INTO wfs_task_instance ( tt_id,wfs_state_id,title,task_type_id,pre_conditions,post_conditions,
			actions,activation_state,params,retry_limit,expiry_duration,expiry_type,assign_to,assign_entity_id,assign_entity_type_id,priority,
			assign_by_id,created_by_id,start_date,end_date,status_id,description,retry_attemps,successor,is_external,external_url,created_at)
		(SELECT wfs_wft_tt_relation.tt_id,wfs_task_template.wfs_state_id,wfs_task_template.title,wfs_task_template.task_type_id,
			wfs_wft_tt_relation.pre_conditions,wfs_wft_tt_relation.post_conditions,wfs_wft_tt_relation.actions,$activation_state,
			wfs_wft_tt_relation.params,wfs_wft_tt_relation.retry_limit,wfs_wft_tt_relation.expiry_duration,
			wfs_wft_tt_relation.expiry_type,'$task_assign_to',IF(wfs_task_template.assign_to = 'admin', $admin_id, $assign_entity_id),$assign_entity_type_id,'default',$assign_by_id,
			wfs_task_template.created_by_id,'$start_date','$end_date',wfs_task_template.status_id,wfs_task_template.description,0,
			wfs_wft_tt_relation.successor,wfs_wft_tt_relation.is_external,wfs_wft_tt_relation.external_url, '$current_date' as created_at
			FROM wfs_task_template LEFT JOIN wfs_wft_tt_relation ON wfs_wft_tt_relation.tt_id = wfs_task_template.id
		 WHERE wfs_task_template.id = $tt_id)");

			$last_ids[$tt_id] = DB::getPdo()->lastInsertId();
		}
		//print "SELECT id,tt_id, successor FROM wfs_task_instance WHERE id in (".implode(',',$last_ids).')';exit;
		/*$ti_id_result = DB::select("SELECT id,tt_id, successor FROM wfs_task_instance WHERE id in (".implode(',',$last_ids).') AND successor != ""');
		//print_r($ti_id_result);exit;
		foreach($ti_id_result as $row){
			$tmp = array();
			$successors = explode(',',$row->successor);
			foreach($successors as $successor) {
				if(isset($last_ids[$successor]))
					$tmp[] = $last_ids[$successor];
			}
			DB::statement('Update wfs_task_instance SET successor = "'.implode(',',$tmp).'" WHERE id = '.$row->id);
		}*/
		return $last_ids;
	}

	/**
	 * View detail of Workflow
	 */
	public static function getTIDetail($ti_id){

		$save = array();
		$result = DB::select("SELECT wfs_work_flow_instance.id AS wfi_id, wfs_work_flow_instance.title AS wfi_title,
wfs_task_instance.id AS ti_id, wfs_task_instance.tt_id, wfs_task_instance.title AS ti_title, wfs_work_flow_instance.start_tt_id,
wfs_work_flow_instance.end_tt_id,wfs_task_instance.actions, wfs_task_instance.assign_to, wfs_task_instance.expiry_duration, wfs_task_instance.expiry_type,
wfs_task_instance.params, wfs_task_instance.post_conditions, wfs_task_instance.pre_conditions, wfs_task_instance.retry_limit,
wfs_task_instance.retry_attemps, wfs_task_instance.successor, wfs_task_instance.start_date, wfs_task_instance.end_date,
wfs_task_instance.wfs_state_id, wfs_state.state, wfs_task_instance.description, wfs_task_instance.assign_entity_id, wfs_task_instance.created_at,
wfs_work_flow_instance.wft_id, wfs_task_instance.assign_entity_type_id,wfs_task_instance.is_external,wfs_task_instance.external_url
FROM wfs_task_instance
LEFT JOIN wfs_wfi_ti_relation ON  wfs_wfi_ti_relation.ti_id = wfs_task_instance.id
LEFT JOIN wfs_state ON  wfs_state.id = wfs_task_instance.wfs_state_id
LEFT JOIN wfs_work_flow_instance ON  wfs_work_flow_instance.id = wfs_wfi_ti_relation.wfi_id
WHERE wfs_task_instance.id = $ti_id");
		return $result;
	}

	/**
	 * View detail of Workflow
	 */
	public static function getTT($tt_id)
	{
		$result = DB::select("SELECT wfs_task_template.*, wfs_wft_tt_relation.external_url, wfs_wft_tt_relation.is_external
 							  FROM wfs_task_template
 							  left join wfs_wft_tt_relation on wfs_wft_tt_relation.tt_id = wfs_task_template.id
 							  WHERE wfs_task_template.id = $tt_id");
		return $result[0];
	}

	/**
	 * View detail of Workflow
	 */
	public static function getTIDetails($ti_ids){

		$save = array();
		$result = DB::select("SELECT wfs_work_flow_instance.id AS wfi_id, wfs_work_flow_instance.title AS wfi_title,
wfs_task_instance.id AS ti_id, wfs_task_instance.tt_id, wfs_task_instance.title AS ti_title, wfs_work_flow_instance.start_tt_id,
wfs_work_flow_instance.end_tt_id,wfs_task_instance.actions, wfs_task_instance.assign_to, wfs_task_instance.expiry_duration, wfs_task_instance.expiry_type,
wfs_task_instance.params, wfs_task_instance.post_conditions, wfs_task_instance.pre_conditions, wfs_task_instance.retry_limit,
wfs_task_instance.retry_attemps, wfs_task_instance.successor, wfs_task_instance.start_date, wfs_task_instance.end_date,
wfs_task_instance.wfs_state_id, wfs_state.state, wfs_task_instance.description, wfs_task_instance.assign_entity_id, wfs_task_instance.created_at
FROM wfs_task_instance
LEFT JOIN wfs_wfi_ti_relation ON  wfs_wfi_ti_relation.ti_id = wfs_task_instance.id
LEFT JOIN wfs_state ON  wfs_state.id = wfs_task_instance.wfs_state_id
LEFT JOIN wfs_work_flow_instance ON  wfs_work_flow_instance.id = wfs_wfi_ti_relation.wfi_id
WHERE wfs_task_instance.id in (".implode(',',$ti_ids).')');
		return $result;
	}

	/**
	 * View detail of Workflow
	 */
	public static function getTISuccessorList($wfi_id, $successor_ids){

		$save = array();
		/*$result = DB::select("SELECT wfs_work_flow_instance.id AS wfi_id, wfs_work_flow_instance.title AS wfi_title,
wfs_task_instance.id AS ti_id, wfs_task_instance.title AS tt_title, wfs_work_flow_instance.start_tt_id,
wfs_work_flow_instance.end_tt_id,wfs_task_instance.actions, wfs_task_instance.expiry_duration, wfs_task_instance.expiry_type,
wfs_task_instance.params, wfs_task_instance.post_conditions, wfs_task_instance.pre_conditions, wfs_task_instance.retry_limit,
wfs_task_instance.retry_attemps, wfs_task_instance.successor, wfs_task_instance.created_at
FROM wfs_task_instance
LEFT JOIN wfs_wfi_ti_relation ON  wfs_wfi_ti_relation.ti_id = wfs_task_instance.id
LEFT JOIN wfs_work_flow_instance ON  wfs_work_flow_instance.id = wfs_wfi_ti_relation.wfi_id
WHERE wfs_task_instance.id IN ($successor_ids)");*/
		$result = DB::select("SELECT $wfi_id AS wfi_id, wfs_work_flow_template.id AS wft_id, wfs_work_flow_template.title AS wfi_title,
wfs_task_template.id AS ti_id, wfs_task_template.title AS tt_title, wfs_work_flow_template.start_tt_id,
wfs_work_flow_template.end_tt_id,wfs_task_template.actions, wfs_task_template.expiry_duration, wfs_task_template.expiry_type,
wfs_task_template.params, wfs_task_template.post_conditions, wfs_task_template.pre_conditions, wfs_task_template.retry_limit,
0 AS retry_attemps, wfs_wft_tt_relation.successor, wfs_task_template.created_at
FROM wfs_task_template
LEFT JOIN wfs_wft_tt_relation ON  wfs_wft_tt_relation.tt_id = wfs_task_template.id
LEFT JOIN wfs_work_flow_template ON  wfs_work_flow_template.id = wfs_wft_tt_relation.wft_id
WHERE wfs_task_template.id IN ($successor_ids)");
		return $result;
	}

	public static function updateTIAttemps($pti_id){
		DB::statement("UPDATE wfs_task_instance SET retry_attemps = retry_attemps+1 WHERE id = $pti_id");
		return;
	}

	public static function updateTIStates($wfi_id,$state_id){
		$result_ti_ids = DB::select(" SELECT wfs_task_instance.id,wfs_work_flow_instance.start_tt_id, wfs_task_instance.tt_id
  		,wfs_work_flow_instance.post_condition  FROM wfs_task_instance
		LEFT JOIN wfs_wfi_ti_relation ON  wfs_wfi_ti_relation.ti_id = wfs_task_instance.id
		LEFT JOIN wfs_work_flow_instance ON  wfs_work_flow_instance.id = wfs_wfi_ti_relation.wfi_id
		WHERE wfs_work_flow_instance.id = $wfi_id");
		$ti_ids = array();
		$start_ti_id = 0;
		$post_condition = '';

		foreach($result_ti_ids as $result_ti_id){
			$ti_ids[] = $result_ti_id->id;
			$post_condition = $result_ti_id->post_condition;
			if($result_ti_id->start_tt_id == $result_ti_id->tt_id)
				$start_ti_id = $result_ti_id->id;
		}
		$response['node_id'] = $start_ti_id;
		$post_condition = json_decode($post_condition,true);
		DB::statement("UPDATE wfs_task_instance SET wfs_state_id = $state_id WHERE id IN (".implode(',',$ti_ids).")");
		$state_result = DB::select("SELECT state FROM wfs_state WHERE id = $state_id");
		$next_node = '';
		$retry_limit = 0;
		$retry_attemps = 0;
		$state = strtolower($state_result[0]->state);
		eval($post_condition['php']);
		if(!empty($next_node)){
			$wfs_result = DB::select("SELECT id,title FROM wfs_work_flow_template WHERE title = '$next_node' ORDER by id desc limit 1");
			$wft_id = $wfs_result[0]->id;
			$response['node_id'] = $wft_id;
		}
		$response['next_node'] = $next_node;

		return $response;
	}

	public static function updateTIState($ti_id,$state_id, $result_ti_ids){
		/*$result_ti_ids = DB::select(" SELECT *  FROM wfs_task_instance
		WHERE id = $ti_id");*/
		$ti_ids = array();
		$response = array();
		$response['next_node'] = '';
		$start_ti_id = 0;
		$wfs_state_id = $result_ti_ids[0]->wfs_state_id;
		$post_condition = $result_ti_ids[0]->post_conditions;
		//print_r($result_ti_ids[0]->post_conditions);exit;
		/*foreach($result_ti_ids as $result_ti_id){
			$ti_ids[] = $result_ti_id->id;
			$post_condition = $result_ti_id->post_condition;
			if($result_ti_id->start_tt_id == $result_ti_id->tt_id)
				$start_ti_id = $result_ti_id->id;
		}
		$response['node_id'] = $start_ti_id;*/
		//if($wfs_state_id == 1) {
			$post_condition = json_decode($post_condition,true);
			DB::statement("UPDATE wfs_task_instance SET wfs_state_id = $state_id, end_date = NOW() WHERE id = $ti_id");

			$state_result = DB::select("SELECT state FROM wfs_state WHERE id = $state_id");
			$next_node = '';
			$retry_limit = 0;
			$retry_attemps = 0;
			$is_expired = 0;
			$state = strtolower($state_result[0]->state);
			eval($post_condition['php']);
			/*if(!empty($next_node)){
                $wfs_result = DB::select("SELECT id,title FROM wfs_work_flow_template WHERE title = '$next_node' ORDER by id desc limit 1");
                $wft_id = $wfs_result[0]->id;
                $response['node_id'] = $wft_id;
            }*/

			$response['next_node'] = $next_node;
			$response['state'] = $state_result[0];
		//}
		return $response;
	}

	public static function assignTI($ti_id,$user_id)
	{
		DB::statement("UPDATE wfs_task_instance SET assign_to = '$user_id', assign_date = NOW() WHERE id = $ti_id");
		return;
	}

	public static function getTiByEntityid($assign_entity_id){
		return DB::select("SELECT id, wfs_state_id, title, assign_entity_type_id, assign_to  FROM wfs_task_instance WHERE assign_entity_id = $assign_entity_id ORDER BY 1 DESC LIMIT 1");
	}
}
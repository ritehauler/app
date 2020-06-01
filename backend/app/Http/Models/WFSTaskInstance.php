<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class WFSTaskInstance extends Base {
	
	use SoftDeletes;
    protected $table = 'wfs_task_instance';
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
         $this->__fields   = array($this->primaryKey, 'tt_id', 'title', 'task_type_id', 'wfs_state_id', 'status_id', 'description', 'pre_condition', 'post_condition', 'actions', 'activation_state', 'params', 'retry_limit', 'created_by_id', 'expiry_duration', 'expiry_type',  'assign_entity_id', 'assign_entity_type_id', 'priority', 'assign_by_id', 'start_date', 'end_date', 'created_at');
	}


	/**
	 * Create instance
	 */
	public static function insertWFSInstance($tt_id,$start_date,$end_date,$assign_entity_id,$assign_entity_type_id,
											 $assign_by_id,$activation_state = 1,$admin_id = 1, $assign_to)
	{
		$current_date = date('Y-m-d H:i:s');

		DB::statement("INSERT INTO wfs_task_instance ( tt_id,wfs_state_id,title,task_type_id,pre_conditions,post_conditions,
			actions,activation_state,params,retry_limit,expiry_duration,expiry_type,assign_to,assign_entity_id,assign_entity_type_id,priority,
			assign_by_id,created_by_id,start_date,end_date,status_id,description,retry_attemps,successor,is_external,external_url,created_at)
		(SELECT wfs_wft_tt_relation.tt_id,wfs_task_template.wfs_state_id,wfs_task_template.title,wfs_task_template.task_type_id,
			wfs_wft_tt_relation.pre_conditions,wfs_wft_tt_relation.post_conditions,wfs_wft_tt_relation.actions,$activation_state,
			wfs_wft_tt_relation.params,wfs_wft_tt_relation.retry_limit,wfs_wft_tt_relation.expiry_duration,
			wfs_wft_tt_relation.expiry_type,'$assign_to',IF(wfs_task_template.assign_to = 'admin', $admin_id, $assign_entity_id),$assign_entity_type_id,'default',$assign_by_id,
			wfs_task_template.created_by_id,'$start_date','$end_date',wfs_task_template.status_id,wfs_task_template.description,0,
			wfs_wft_tt_relation.successor,wfs_wft_tt_relation.is_external,wfs_wft_tt_relation.external_url, '$current_date' as created_at
			FROM wfs_task_template LEFT JOIN wfs_wft_tt_relation ON wfs_wft_tt_relation.tt_id = wfs_task_template.id
		 WHERE wfs_task_template.id = $tt_id)");

		$last_ids = DB::getPdo()->lastInsertId();

		return $last_ids;
	}

	/**
	 * @param $entity_type_id
	 * @param $entity_id
	 * @return bool
	 */
	public function getDataByEntityId($entity_type_id,$entity_id)
	{
		/*$row = $this->where('assign_entity_type_id', $entity_type_id)
			->where('assign_entity_id', $entity_id)
			->whereNull("deleted_at")
			->get();*/

		$row = $this->select($this->__table.'.*','wfs_state.state')
			->where($this->__table.'.assign_entity_type_id', $entity_type_id)
			->where($this->__table.".assign_entity_id","=",$entity_id)
			->join('wfs_state', 'wfs_state.id', '=', $this->__table.".wfs_state_id")
			//->whereNull($this->__table.".deleted_at")
			->get();

		return isset($row[0]) ? $row : false;
	}


}
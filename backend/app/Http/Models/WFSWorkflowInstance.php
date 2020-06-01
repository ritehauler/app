<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class WFSWorkflowInstance extends Base {
	
	use SoftDeletes;
    protected $table = 'wfs_work_flow_instance';
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
         $this->__fields   = array($this->primaryKey, 'wft_id', 'title', 'type', 'wfs_state_id', 'created_by_id', 'initiator_id', 'status_id', 'description', 'pre_condition', 'post_condition', 'successor_wft_id', 'start_tt_id', 'end_tt_id', 'start_date', 'end_date', 'created_at');
	}

	public static function getWITransaction($wfi_id)
	{
		$result = DB::select("SELECT wfs_task_instance.id AS wfi_id, wfs_work_flow_instance.title, wfs_task_instance.title AS wfi_title,
wfs_task_instance.assign_to, wfs_work_flow_instance.created_at AS wf_originated_on, wfs_task_instance.created_at AS task_initialized_on,
wfs_task_instance.end_date AS task_complete_date, wfs_state.state AS `status`,
IF(wfs_task_instance.tt_id = wfs_work_flow_instance.start_tt_id,'First', IF(wfs_task_instance.successor = '','Last','Middle')) AS task_position,
wfs_task_instance.expiry_duration AS due_days, DATE_ADD(wfs_task_instance.created_at,
INTERVAL wfs_task_instance.expiry_duration DAY)  AS due_date, '' AS slack, wfs_task_instance.expiry_type
FROM wfs_work_flow_instance
LEFT JOIN wfs_wfi_ti_relation ON wfs_wfi_ti_relation.wfi_id = wfs_work_flow_instance.id
LEFT JOIN wfs_task_instance ON wfs_task_instance.id = wfs_wfi_ti_relation.ti_id
LEFT JOIN wfs_state ON wfs_state.id = wfs_task_instance.wfs_state_id
WHERE wfs_work_flow_instance.id = $wfi_id");

		$expiry_types = array('hours' => 'hour', 'days' => 'day', 'weeks' => 'week', 'month' => 'month', 'year' => 'year');
		//$row->expiry_type
		foreach ($result as $row){
			$row->expiry_type = isset($expiry_types[$row->expiry_type]) ? $expiry_types[$row->expiry_type] : 'day';
			$row->wfi_id;
			$row->title;
			$row->wfi_title;
			$row->assign_to;
			$row->wf_originated_on;
			$row->task_initialized_on;
			$row->task_complete_date;
			$row->status;
			$row->task_position;

			$completed_date = date('Y-m-d h:i:s', strtotime("+{$row->due_days} {$row->expiry_type}", strtotime($row->task_complete_date)));
			$interval = date_diff(date_create($completed_date),date_create($row->task_complete_date));
			//$row->slack = $interval->format('%R%a');
			if($row->expiry_type == 'hour')
				$row->slack = round(abs(strtotime($completed_date)-strtotime($row->task_complete_date))/3600) . ' hours'; // hours
			else
				$row->slack = round(abs(strtotime($completed_date)-strtotime($row->task_complete_date))/86400) . ' days'; //days

			$row->due_days = $row->due_days . ' ' . $row->expiry_type;
			//print_r($interval);exit;
		}
		return $result;
	}
}
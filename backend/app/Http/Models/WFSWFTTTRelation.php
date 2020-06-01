<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Facades\DB;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class WFSWFTTTRelation extends Base {
	
	use SoftDeletes;
    protected $table = 'wfs_wft_tt_relation';
    protected static $static_table = 'wfs_wft_tt_relation';
    public $timestamps = false;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'wft_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
         $this->__fields   = array($this->primaryKey, 'tt_id', 'successor', 'pre_conditions', 'post_conditions', 'actions', 'params',
			 				'retry_limit', 'expiry_duration', 'expiry_type', 'created_at');
	}

	/**
	 *
	 */
	public static function insert( $wft_id, $tt_id, $successor, $pre_conditions, $post_conditions, $actions, $params, $retry_limit, $expiry_duration, $expiry_type){

		$obj = WFSWFTTTRelation::create();

		$obj->wft_id	= $wft_id;
		$obj->tt_id		= $tt_id;
		$obj->successor	= $successor;
		$obj->pre_conditions	= $pre_conditions;
		$obj->post_conditions	= $post_conditions;
		$obj->actions			= $actions;
		$obj->params			= $params;
		$obj->retry_limit		= $retry_limit;
		$obj->expiry_duration	= $expiry_duration;
		$obj->expiry_type		= $expiry_type;
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
	public static function updateData($wft_id, $tt_id, $data){

		$save = array();
		DB::table(self::$static_table)
			->where('wft_id', $wft_id)
			->where('tt_id', $tt_id)
			->update($data);
		return;
	}


	/**
	 *
	 */
	public static function insertWFSInstance($wft_id, $tt_ids){
		$values = "($wft_id,NOW(),". implode("),($wft_id,NOW(),",$tt_ids).')';
		DB::statement("INSERT INTO wfs_wfi_ti_relation (wfi_id, created_at, ti_id) VALUES $values");
		return;
	}

}
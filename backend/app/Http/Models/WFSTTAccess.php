<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Facades\DB;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class WFSTTAccess extends Base {
	
	use SoftDeletes;
    protected $table = 'wfs_tt_access';
    protected static $static_table = 'wfs_tt_access';
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
		$this->__fields = array($this->primaryKey, 'tt_id', 'entiy_id', 'entity_type_id', 'can_copy', 'can_delete', 'can_start');
	}

	/**
	 *
	 */
	public static function insert( $tt_id, $entiy_id, $entity_type_id, $can_copy, $can_delete, $can_start){

		$obj = WFSTTAccess::create();

		$obj->tt_id		= $tt_id;
		$obj->entiy_id	= $entiy_id;
		$obj->entity_type_id	= $entity_type_id;
		$obj->can_copy	= $can_copy;
		$obj->can_delete	= $can_delete;
		$obj->can_start			= $can_start;

		$obj->save();

		if($obj->save()){
			return $obj->id;
		}
		return false;
	}

	/**
	 *
	 */
	public static function updateData($wft_id, $data){

		$save = array();
		DB::table(self::$static_table)
			->where('wft_id', $wft_id)
			->update($data);
		return;
	}
}
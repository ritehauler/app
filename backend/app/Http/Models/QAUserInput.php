<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class QAUserInput extends Base {
	
	use SoftDeletes;
    public $table = 'qa_user_input';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'user_input_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'user_id', 'pacakge_id', 'container_id', 'level_tag_id', 'input', 'input_time', 'created_at', 'updated_at', 'deleted_at');
	}	
	
	
	/**
	 * removeUserRecords
	 *
	 * @return Query
	*/
	public function removeUserRecords($user_id = 0, $level_ids = "") {
		// query records
		$sql = "UPDATE `".$this->table."`
		SET deleted_at = '".date("Y-m-d H:i:s")."'
		WHERE user_id = '".$user_id."'";
		
		// if level_ids given
		if($level_ids !== "") {
			$sql .= " AND `level_tag_id` IN (
				SELECT `level_tag_id` FROM `qa_level_tag`
				WHERE `level_id` IN (".$level_ids.")
			)";
		}
		
		\DB::statement($sql);		
		return;
	}
	
}
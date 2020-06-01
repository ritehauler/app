<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\User;

class UserBlock extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'user_block';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'user_id', 'target_user_id', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
     * check
     * @param integer $user_id
	 * @param integer $target_user_id
     * @return integer user_report_id
     */
    function check($user_id, $target_user_id) {		
		// fetch
		$query = $this->select(array($this->primaryKey));
		$query->whereRaw("((user_id = '".$user_id."' AND target_user_id = '".$target_user_id."') OR (user_id = '".$target_user_id."' AND target_user_id = '".$user_id."'))");
		$query->whereNull("deleted_at");
		$raw_records = $query->get();
		
        // return
        return isset($raw_records[0]) ? $raw_records[0]->{$this->primaryKey} : 0;
    }
	
	
}
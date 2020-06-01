<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\User;

class Group extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'group';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'group_behavior_id', 'title', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	
}
<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\User;

class QAContainerBehavior extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'qa_container_behavior';
		$this->primaryKey = 'container_behavior_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'identifier', 'title', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	
}
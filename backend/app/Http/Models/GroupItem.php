<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\User;

class GroupItem extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'group_item';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'group_id', 'item_id', 'item_type_id', 'from_level', 'to_level','worth', 'order', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	
}
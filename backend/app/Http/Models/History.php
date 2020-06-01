<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\User;

class History extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'history';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'identifier', "plugin_identifier", 'notification_type', 'notify_user', 'notify_target_user', 'is_user_viewable', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
}
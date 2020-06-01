<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
use App\Http\Models\User;

class MessageTrash extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'message_trash';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'user_id', 'message_id', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
}
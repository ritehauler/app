<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ApiMethod extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'api_method';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
		$this->__fields   = array($this->primaryKey, 'type', 'name', 'uri', 'schema', 'description', 'plugin_identifier', 'order', 'type_id', 'is_active', 'is_token_required', 'created_at', 'updated_at', 'deleted_at');

	}
	
}

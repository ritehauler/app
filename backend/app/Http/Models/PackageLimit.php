<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\UserPackage;

class PackageLimit extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'package_limit';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'package_id', 'limit', 'key', 'type', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	
	
}
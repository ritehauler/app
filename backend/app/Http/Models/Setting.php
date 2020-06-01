<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'setting';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'key', 'value', 'description');
	}
	
}
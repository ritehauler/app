<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\User;

class QAContentType extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'qa_content_type';
		$this->primaryKey = 'content_type_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'identifier', 'title', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	
}
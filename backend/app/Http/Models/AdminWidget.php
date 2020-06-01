<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
//use App\Http\Models\UserDisability;

class AdminWidget extends Base {
	
	use SoftDeletes;
    public $table = 'admin_widget';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
         $this->__fields   = array($this->primaryKey, 'identifier', 'title', 'type', 'sql_query', 'context_type', 'context', 'wildcards', 'replacers', 'target_element', 'insertion_type', 'is_active', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	
}
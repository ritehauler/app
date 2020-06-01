<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
//use App\Http\Models\UserDisability;

class AdminGroupWidget extends Base {
	
	use SoftDeletes;
    public $table = 'admin_group_widget';
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
         $this->__fields   = array($this->primaryKey, 'admin_group_id', 'admin_widget_id', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
}
<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

class AdminModule extends Base {

    use SoftDeletes;
    public $table = 'admin_module';
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
        $this->__fields   = array($this->primaryKey, 'parent_id', 'name', 'class_name', 'icon_class', 'is_active', 'show_in_menu', 'order', 'created_at', 'updated_at', 'deleted_at');
	}
    
}
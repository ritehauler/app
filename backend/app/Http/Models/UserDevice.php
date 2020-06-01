<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
//use App\Http\Models\UserDisability;

class UserDevice extends Base {
	
	use SoftDeletes;
    public $table = 'user_device';
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
         $this->__fields   = array($this->primaryKey, 'user_id', 'type', 'brand', 'model', 'manufacturer', 'os_version', 'api_level', 'build_id', 'app_version', 'created_at', 'updated_at', 'deleted_at');
	}
	
}
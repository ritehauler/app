<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class CCSpace extends Base {
	
	use SoftDeletes;
    public $table = 'cc_space';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'space_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'parent_id', "user_id", "network_id", 'type', 'title', 'logo_image', 'space_email', "view_access", "post_access", "is_required_manager_approval", 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
     * Get Data
     * @param integer $pk
     * @return Object
     */
    public function getData($id = 0) {
		// init target
        $data = $this->get($id);
		// got data
		if($data) {
			// set image dir
			$data->logo_image = config('cc_constants.DIR_GROUP').$data->logo_image;
			// unset unrequired
			unset($data->deleted_at, $data->updated_at);
			
		}
        return $data;
    }
	
}
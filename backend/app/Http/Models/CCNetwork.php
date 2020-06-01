<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class CCNetwork extends Base {
	
	use SoftDeletes;
    public $table = 'cc_network';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'network_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'network_type_id', 'title', 'network_email', 'logo_image', 'created_at', 'updated_at', 'deleted_at');
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
			$data->logo_image = config('cc_constants.DIR_NETWORK').$data->logo_image;
			// unset unrequired
			unset($data->deleted_at, $data->updated_at);
			
		}
        return $data;
    }
}
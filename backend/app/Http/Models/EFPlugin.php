<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class EFPlugin extends Base {
	
	use SoftDeletes;
    public $table = 'ef_plugin';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'plugin_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
         $this->__fields   = array($this->primaryKey, 'title', 'identifier', 'is_active', 'schema', 'version', 'has_related_entity', 'created_at', 'updated_at', 'deleted_at');
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
			// decode schema
			$data->schema = $data->schema !== NULL ? json_decode(trim($data->schema)) : (object)array();
			// unset unrequired
			unset($data->deleted_at);
			
		}
        return $data;
    }
	
	
	
}
<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class EFPluginFeature extends Base {
	
	use SoftDeletes;
    public $table = 'ef_plugin_feature';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'plugin_feature_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
         $this->__fields   = array($this->primaryKey, 'plugin_id', 'title', 'identifier', 'schema', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	
}
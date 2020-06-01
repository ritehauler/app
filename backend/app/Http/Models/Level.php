<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class Level extends Base {
	
	use SoftDeletes;
    public $table = 'level';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'level';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'level_type', 'title', 'schema', 'from_xp', 'to_xp', 'pre_check_type', 'pre_check', 'successor_type', 'successor_check', 'created_at', 'updated_at', 'deleted_at');
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
    
    /**
    * 
    */
    public function insertLevel($level_id, $level_type, $title, $schema, $successcor_type, $successcor_check){
         
        $level = Level::create();
        $level->level_id = $level_id;
        $level->level_type = $level_type;
        $level->title = $title;
        $level->schema = $schema;
        $level->successor_type = $successcor_type;
        $level->successor_check = $successcor_check;
        $level->save();
        
        if($level->save()) {
            return $level->level_id;
        }
        return false;
    }     
	
}
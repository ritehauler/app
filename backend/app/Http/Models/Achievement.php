<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Achievement;

class Achievement extends Base {
	
	use SoftDeletes;
    public $table = 'achievement';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'achievement';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'achievement_type', 'title', 'schema', 'from_xp', 'to_xp', 'pre_check_type', 'pre_check', 'successor_type', 'successor_check', 'created_at', 'updated_at', 'deleted_at');
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
    public function insertAchievement($achievement_id, $achievement_type, $title, $schema, $successcor_type, $successcor_check){
         
        $achievement = Achievement::create();
        $achievement->achievement_id = $achievement_id;
        $achievement->achievement_type = $achievement_type;
        $achievement->title = $title;
        $achievement->schema = $schema;
        $achievement->successor_type = $successcor_type;
        $achievement->successor_check = $successcor_check;
        $achievement->save();
        
        if($achievement->save()) {
            return $achievement->achievement_id;
        }
        return false;
    }     
	
}
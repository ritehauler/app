<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class QAContainer extends Base {
	
	use SoftDeletes;
    public $table = 'qa_container';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'container_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'container_behavior_id', 'title', 'worth', 'max_level', 'min_level', 'max_score', 'min_score', 'condition_type', 'condition', 'multiplier_effect', 'min_time', 'max_time', 'min_pace', 'max_pace', 'order', 'created_at', 'updated_at', 'deleted_at');
	}
    
    public function insertContainer($con_behavior, $title, $order){
        
        $qa_container = QAContainer::create();
        $qa_container->container_behavior_type_id = $con_behavior;
        $qa_container->title = $title;
        $qa_container->order = $order;
        $qa_container->save();
        
        if($qa_container->save()) {
            return $qa_container->container_id;
        }
        return false;
    }	
}
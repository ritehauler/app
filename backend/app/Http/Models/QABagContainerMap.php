<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\User;

class QABagContainerMap extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'qa_bag_container_map';
		$this->primaryKey = 'bag_container_map_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'bag_id', 'tag_id', 'container_id', 'title', 'created_at', 'updated_at', 'deleted_at');
	}
	
	public function insertBagConMap($bag_id, $tag_id, $container_id, $language_id){

        $qa_bag_container_map = QABagContainerMap::create();
        $qa_bag_container_map->bag_id = $bag_id;
        $qa_bag_container_map->tag_id = $tag_id;
        $qa_bag_container_map->container_id = $container_id;
        $qa_bag_container_map->language_id = $language_id;
        $qa_bag_container_map->save();
        
        if($qa_bag_container_map->save()) {
            return true;
        }
        return false;

	}
	
}
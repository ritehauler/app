<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\User;

class QAAchievementTag extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'qa_achievement_tag';
		$this->primaryKey = 'achievement_tag_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'achievement_id', 'tag_id', 'bag_id', 'successor_type', 'successor', 'schema', 'created_at', 'updated_at', 'deleted_at');
	}
    
    /**
     * 
     */
     public function insertAchievementTag($level_id, $tag_id, $bag_id){
        
        $qa_level_tag = QAAchievementTag::create();
        $qa_level_tag->achievement_id = $achievement_id;
        $qa_level_tag->tag_id = $tag_id;
        $qa_level_tag->bag_id = $bag_id;
        $qa_level_tag->save();
        
        if($qa_level_tag->save()) {
            return true;
        }
        return false;
        
     }
    
}
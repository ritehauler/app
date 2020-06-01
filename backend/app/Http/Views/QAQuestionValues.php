<?php namespace App\Http\Views;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
use App\Http\Models\Base;

class QAQuestionValues extends Base {
	
	use SoftDeletes;
    protected $table = 'qa-questions';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'record_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'level_id', 'bag_id', 'tag_id', 'container_id', 'question_id', 'answer1_id', 'answer1_worth', 'answer2_id', 'answer2_worth', 'answer3_id', 'answer3_worth', 'answer4_id', 'answer4_worth', 'created_at', 'updated_at', 'deleted_at');
	}
    
}
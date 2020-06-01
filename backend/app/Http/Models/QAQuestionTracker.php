<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class QAQuestionTracker extends Base {
	
	use SoftDeletes;
    public $table = 'qa_question_tracker';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'question_tracker_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
         $this->__fields   = array($this->primaryKey, 'level_id', 'bag_id', 'tag_id', "language_id", "container_id", 'question_id', 'answer1_id', 'answer1_worth', 'answer2_id', 'answer2_worth', 'answer3_id', 'answer3_worth', 'answer4_id', 'answer4_worth', 'created_at', 'updated_at', 'deleted_at');
	}
    
	
	/**
     * create QA
     * @param array $row_data
	 * @param array $loaded_models
	 * @param datetime $created_at
     * @return id
     */
    public function createQA($row_data, $loaded_models, $created_at = NULL) {
		// defaults vars

		$expected_answers_count = 4;
		$content_type = $loaded_models["qa_content_type_model"]->getBy("identifier","text");
		$container_behavior = $loaded_models["qa_container_behavior_model"]->getBy("identifier","single-choice-question");
		$package_type_question = $loaded_models["qa_package_type_model"]->getBy("identifier","question");
		$package_type_answer = $loaded_models["qa_package_type_model"]->getBy("identifier","answer");
		
		// add container
		$save = array(
			"container_behavior_id" => $container_behavior->container_behavior_id,
			"title" => "Container ".$row_data["record_id"],
			"order" => $row_data["record_id"],
			"created_at" => $created_at
		);
		$save["container_id"] = $loaded_models["qa_container_model"]->put($save);
		$saved_data["container"] = $save;
		unset($save);
		
		
		// add contents
		// -- add question
		$save = array(
			"language_id" => $row_data["language_id"],
			"content_type_id" => $content_type->content_type_id,
			"title" => $package_type_question->title." : ".$row_data["record_id"],
			"content" => $row_data["question"],
			"created_at" => $created_at
		);
		// -- save and get insert_id
		$save["content_id"] = $loaded_models["qa_content_model"]->put($save);
		$saved_data["question"] = $save;
		unset($save);
		// -- add package
		$package_title = "Package : ".$row_data["record_id"];
		$sequence = 1;
		// -- get id
		$package_id = $this->_addPackage($loaded_models, $row_data, $package_title, (object)$saved_data["question"], 0, $sequence, $created_at);
		// -- add mapping (package > container)
		$save = array(
			"container_id" => $saved_data["container"]["container_id"],
			"package_id" => $package_id,
			"package_id" => $package_type_question->package_type_id,
			"title" => $saved_data["container"]["title"]." : ".$package_type_question->title,
			"created_at" => $created_at
		);
		$loaded_models["qa_package_container_map_model"]->put($save);
		unset($save);
		
		// save answers
		for($i=1; $i<=$expected_answers_count; $i++) {
			// -- add question
			$save = array(
				"language_id" => $row_data["language_id"],
				"content_type_id" => $content_type->content_type_id,
				"title" => $package_type_answer->title." : ".$i,
				"content" => $row_data["answer".$i],
				"created_at" => $created_at
			);
			// -- save and get insert_id
			$save["content_id"] = $loaded_models["qa_content_model"]->put($save);
			$saved_data["answer".$i] = $save;
			unset($save);
			// -- add package
			$package_title = "Package : ".$row_data["record_id"]." : ".$i;
			$sequence = 1;
			// -- get id
			$package_id = $this->_addPackage($loaded_models, $row_data, $package_title, (object)$saved_data["answer".$i], $row_data["answer".$i."_worth"], $sequence, $created_at);
			// -- add mapping (package > container)
			$save = array(
				"container_id" => $saved_data["container"]["container_id"],
				"package_id" => $package_id,
                "package_type_id" => $package_type_answer->package_type_id,
				"title" => $package_type_question->title." : ".$row_data["record_id"],
				"created_at" => $created_at
			);
			$loaded_models["qa_package_container_map_model"]->put($save);
			unset($save);			
		}

		// add bag container
		$save = array(
			"bag_id" => $row_data["bag_id"],
			"tag_id" => $row_data["tag_id"],
			"language_id" => $row_data["language_id"],
			"container_id" => $saved_data["container"]["container_id"],
			"title" => "Container Map ".$row_data["record_id"],
			"created_at" => $created_at
		);
		$loaded_models["qa_bag_container_map_model"]->put($save);
		unset($save);
		
		// prepare save data
		$save = array(
			$this->primaryKey => $row_data["record_id"],
			"level_id" => $row_data["level_id"],
			"bag_id" => $row_data["bag_id"],
			"tag_id" => $row_data["tag_id"],
			"language_id" => $row_data["language_id"],
			"container_id" => $saved_data["container"]["container_id"],
			"question_id" => $saved_data["question"]["content_id"],
			"created_at" => $created_at
		);
		// expected answer loop
		for($i=1; $i <= $expected_answers_count; $i++) {
			$save["answer".$i."_id"] = $saved_data["answer".$i]["content_id"];
			$save["answer".$i."_worth"] = $row_data["answer".$i."_worth"];
		}
		// save data
		return $this->put($save);
    }
	
	/**
     * removve QA
     * @param array $row_data
	 * @param array $loaded_models
	 * @param datetime $created_at
     * @return id
     */
    public function removeQA($record, $remove_related_contents = 0) {
		if($record !== FALSE) {
			// remove related content if asked to
			if($remove_related_contents == 1) {
				// -- remove packages
				\DB::table('qa_package')
				->whereRaw("package_id IN (SELECT package_id FROM `qa_package_container_map`
					WHERE `container_id` = '".$record->container_id."'
				)")
				->delete();
				// -- remove content
				\DB::table('qa_content')
				->whereRaw("content_id IN (SELECT content_id FROM `qa_package_content_map`
					WHERE package_id IN (
						SELECT package_id FROM `qa_package_container_map`
						WHERE `container_id` = '".$record->container_id."'
					)
				)")
				->delete();
			}
			
			// remove relations
			// -- find and remove bag container relation
			\DB::table('qa_bag_container_map')
				->where('bag_id', '=', $record->bag_id)
				->where('tag_id', '=', $record->tag_id)
				->where('language_id', '=', $record->language_id)
				->where('container_id', '=', $record->container_id)
				->delete();
			// -- find and remove package_content relation
			\DB::table('qa_package_content_map')
				->whereRaw("package_id IN (SELECT package_id FROM `qa_package_container_map`
					WHERE `container_id` = '".$record->container_id."'
				)")
				->delete();
			// -- find and remove package_container relation
			\DB::table('qa_package_container_map')
				->where('container_id', '=', $record->container_id)
				->delete();
			// remove from mandatory tables
			// -- find and remove from container
			\DB::table('qa_container')
				->where('container_id', '=', $record->container_id)
				->delete();
			// -- find and remove from user input
			\DB::table('qa_user_input')
				->where('container_id', '=', $record->container_id)
				->delete();
			// -- find and remove from question tracker (i.e: current model record)
			$this->hardRemove($record->{$this->primaryKey});
		}
	}
	
	
	/**
     * add package
	 * @param array $loaded_models
     * @param array $row
	 * @param int $row_id
	 * @param bool $validation_required
     * @return null
     */
	private function _addPackage($loaded_models, $row_data, $package_title, $content, $worth = 0, $sequence = 1, $created_at = NULL) {
		// set default
		$created_at = $created_at === NULL ? date("Y-m-d H:i:s") : $created_at;
		
		// prepare data
		$save = array(
			"title" => $package_title,
			"order" => $row_data["record_id"],
			"created_at" => $created_at
		);
		// save and get insert_id
		$id = $loaded_models["qa_package_model"]->put($save);
		// unset array
		unset($save);
		// add package mapping
		$save = array(
			"package_id" => $id,
			"content_id" => $content->content_id,
			"language_id" => $content->language_id,
			"worth" => $worth,
			"order" => $sequence,
			"created_at" => $created_at
		);
		// save
        $loaded_models["qa_package_content_map_model"]->put($save);
		return $save["package_id"];
	}
   
}
<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
// models
use App\Http\Models\Preference;

class UserPreference extends Base {

    public function __construct() {
        // set tables and keys
        $this->__table = $this->table = 'user_preference';
        $this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'user_id', 'preference_id', 'value', 'created_at', 'updated_at', 'deleted_at');
    }
	
	/**
     * Save user preferences
     *
     * @return NULL
     */
    function putUserPreferences($user_id) {
		// init models
		$preference_model = new Preference;
		
		// get preferences
		// find records
		$query = $preference_model->select(array("preference_id","default_value"));
		$query->orderBy("preference_id", "ASC");
		$raw_records = $query->get();
		if(isset($raw_records[0])) {
			// default value
			$data["user_id"] = $user_id;
			
			// loop through all
			foreach($raw_records as $raw_record) {
				//$preference = $preference_model->get($raw_record->preference_id);
				
				// save
				$data["preference_id"] = $raw_record->preference_id;
				$data["value"] = $raw_record->default_value;
				$save_data["created_at"] = date("Y-m-d H:i:s");
				$this->put($data);
			}
		}
		
        // return
        return;
    }
	
	
	/**
     * get User Preferences
     *
     * @return NULL
     */
    function getUserPreferences($user_id, $type = "") {
		// init models
		$preference_model = new Preference;
		
		// init data
		$data = array();
		
		// validate type
		$type = trim($type);
		
		// fetch
		$query = $this->select(array("p.preference_id","p.key","up.value"));
		if($type != "") {
			$query->where("p.type", $type);
		}
		$query->from("user_preference AS up");
		$query->join("preference AS p","p.preference_id","=","up.preference_id");
		$query->where("up.user_id", $user_id);
		$raw_records = $query->get();
		
		if(isset($raw_records[0])) {
			foreach($raw_records as $raw_record) {
				//$record = $preference_model->get($raw_record->preference_id);
				//if($record !== FALSE) {
					//$data[] = $record;
					$data[$raw_record->{'key'}] = $raw_record->value;
				//}
			}
		}

        // return
        return $data;
    }

}

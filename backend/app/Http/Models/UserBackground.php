<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\User;

class UserBackground extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'user_background';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'user_id', 'type', 'name', 'is_selected', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	
	/**
     * Save user background
     *
     * @return NULL
     */
    function putUserBackground($user_id, $key = "", $data_str = "", $selections_str = "") {
		
		
		$data = array();
		$selections = array();
		if($data_str != "") {
			$data = explode("#@#",$data_str);
			$selections = explode(",",$selections_str);
		}
		
		if($key != "" && isset($data[0])) {
			// default
			$save_data["user_id"] = $user_id;
			$itrator = 0;
			// loop through data
			foreach($data as $record) {
				//$active_data = explode("-@-",$record);
				//$is_selected = isset($record[1]) ? intval(trim($record[1])) : 0;
				//$is_selected = $is_selected > 1 ? 1 : 0;
				//$is_selected = $itrator == 0 ? 1 : 0;
				
				//$save_data["name"] = trim($record[0]);
				$save_data["name"] = trim($record);
				$save_data["type"] = $key;
				$save_data["is_selected"] = isset($selections[$itrator]) ? $selections[$itrator] : 0;
				$save_data["created_at"] = date("Y-m-d H:i:s");
				
				// save
				$this->put($save_data);
				
				$itrator++;
			}
		}

        // return
        return;
    }
	
	
	/**
     * get User Disabilities
     *
     * @return NULL
     */
    function getUserBackground($user_id, $type = "") {
		// init data
		$data = array();
		
		// validate type
		$type = trim($type);
		
		// fetch
		$query = $this->select(array($this->primaryKey));
		if($type != "") {
			$query->where("type", $type);
		}
		$query->where("user_id", $user_id);
		$query->orderBy("is_selected", "DESC");
		$query->whereNull("deleted_at");
		$raw_records = $query->get();
		
		if(isset($raw_records[0])) {
			foreach($raw_records as $raw_record) {
				$record = $this->getData($raw_record->{$this->primaryKey});
				if($record !== FALSE) {
					$data[] = $record;
				}
			}
		}

        // return
        return $data;
    }
	
	
	/**
     * Remove user disabilities
     *
     * @return NULL
     */
    function removeUserBackground($user_id, $type = "") {
		// validate type
		$type = trim($type);
		
		// fetch
		$query = $this->select(array($this->primaryKey));
		$query->where("user_id", $user_id);
		if($type != "") {
			$query->where("type", $type);
		}
		$query->whereNull("deleted_at");
		$raw_records = $query->get();
		
		if(isset($raw_records[0])) {
			foreach($raw_records as $raw_record) {
				$this->remove(($raw_record->{$this->primaryKey}));
			}
		}
        // return
        return;
    }
	
	
}
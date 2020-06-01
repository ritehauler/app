<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
//use Mail;
// models
use App\Http\Models\Package;
use App\Http\Models\PackageLimit;
use App\Http\Models\UserPreference;

class UserPackage extends Base {

    public function __construct() {
        // set tables and keys
        $this->__table = $this->table = 'user_package';
        $this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'user_id', 'package_id', 'valid_until', 'is_expired', 'created_at', 'updated_at', 'deleted_at');
    }
	
	/**
     * Get Data
     * @param integer $pk_id primary id
     * @return Object
     */
    public function getData($pk_id = 0) {
		// init data
        $data = $this->get($pk_id);
		
        if ($data !== FALSE) {
			// unset unrequired
			unset($data->updated_at,$data->deleted_at);
		}

        return $data;
    }
	
	/**
     * Save user package
     *
     * @return NULL
     */
    function putUserPackage($user_id, $key = "free") {
		// init models
		$package_model = new Package;
		$package_limit_model = new PackageLimit;
		$user_preference_model = new UserPreference;
		$user_history_model = new UserHistory;
		
		// validate key
		$key = in_array($key, array("free","monthly","yearly")) ? $key : "";
		$key_data = $package_model->getBy("key", $key);
		if($key_data !== FALSE) {
			$save_data["user_id"] = $user_id;
			$save_data["package_id"] = $key_data->package_id;
			$save_data["valid_until"] = date("Y-m-d", strtotime("+".$key_data->expiry_duration." ".$key_data->expiry_unit));
			// add 24 hours
			$save_data["valid_until"] = $save_data["valid_until"]." 23:59:59";
			$save_data["is_expired"] = 0;
			$save_data["created_at"] = date("Y-m-d H:i:s");
			// save
			$record_id = $this->put($save_data);
			
			// if not free package, put history
			if($key_data->{"key"} != "free") {
				// init model
				$user_history_model = new UserHistory;
				
				// save history
				$reference_data = array(
					"reference_module" => "package",
					"reference_id" => $key_data->package_id
				);
				$user_history_model->putUserHistory($user_id,"upgrade",$reference_data);
			}
		}

        // return
        return;
    }
	
	/**
     * get User Package
     *
     * @return NULL
     */
    function getUserPackage($user_id) {
		// init models
		$package_limit_model = new PackageLimit;
		
		// init data
		$data = array();
		
		// prepare query
		/*$where = "`package_id` = (SELECT `package_id`
			FROM `".$this->__table."`
			WHERE `user_id` = '".$user_id."'
			AND `is_expired` = 0
			LIMIT 1
			ORDER BY `created_at` DESC
		)";*/
		// get last valid package
		$where = "`package_id` = (SELECT `package_id`
			FROM `".$this->__table."`
			WHERE `user_id` = '".$user_id."'
			AND valid_until > '".date("Y-m-d H:i:s")."'
			AND `deleted_at` IS NULL
			ORDER BY `created_at` DESC
			LIMIT 1
		)";
		// fetch
		$query = $package_limit_model->select(array("package_limit_id"));
		$query->whereRaw($where);
		$query->orderBy("key", "ASC");
		$raw_records = $query->get();
		
		if(isset($raw_records[0])) {
			$itrator = 0;
			foreach($raw_records as $raw_record) {
				$record = $package_limit_model->getData($raw_record->package_limit_id);
				if($record !== FALSE) {
					$data[$record->{'key'}] = $record->limit;
					
					// get package info
					if($itrator == 0) {
						$data["is_expired"] = 0;
						$data["package_id"] = $record->package_id;
					}
					
					$itrator++;
				}
			}
		}

        // return
        return $data;
    }
	
	
	/**
     * check
     * @param integer $user_id
	 * @param integer $target_user_id
     * @return integer user_report_id
     */
    function check($user_id, $package_id, $is_expired = "") {		
		// fetch
		$query = $this->select(array($this->primaryKey));
		$query->where("user_id", $user_id);
		$query->where("package_id", $package_id);
		if($is_expired != "") {
			$query->where("is_expired", $is_expired);
		}
		$query->whereNull("deleted_at");
		$raw_records = $query->get();
		
        // return
        return isset($raw_records[0]) ? $raw_records[0]->{$this->primaryKey} : 0;
    }
	
	
	/**
     * remove Package
     * @param integer $user_id
	 * @param integer $package_id
     * @return NULL
     */
    function removePackage($user_id = 0, $package_id = 0) {		
		// get friend record
		$record_id = $this->check($user_id, $package_id, 0);
		$record_data = $this->get($record_id);
		
		if($record_data !== FALSE) {
			// init model
			$history_model = new History;
			$user_history_model = new UserHistory;
			
			// remove friend record
			$record_data->is_expired = 1;
			$record_data->deleted_at = date("Y-m-d H:i:s");
			$this->set($record_data->{$this->primaryKey},(array)$record_data);
			
			// find history // (upgrade)
			$history = $history_model->getBy("key","upgrade");
			// get history data
			$query = $user_history_model->select(array("user_history_id"));
			$query->where("history_id", $history->history_id);
			$query->where("user_id", $user_id);
			$query->where("reference_module", "package");
			$query->where("reference_id", $package_id);
			$query_data = $query->get();
			
			// fetch query
			if(isset($query_data[0])) {
				$user_history = $user_history_model->get($query_data[0]->user_history_id);
				// valid record
				if($user_history !== FALSE) {
					$user_history_model->remove($user_history->user_history_id);
				}
			}
		}
		
		
        // return
        return NULL;
    }	

}

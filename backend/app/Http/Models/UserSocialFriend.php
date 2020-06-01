<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\User;

class UserSocialFriend extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'user_social_friend';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'user_id', 'network', 'uid', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
     * get Friends
     * @param integer $user_id
	 * @param string $network (facebook/instagram/twitter)
     * @return array
     */
    function getFriends($user_id, $network = "") {
		// init data
		$data = array();
		
		// validate type
		$network = trim($network);
		
		// fetch
		$query = $this->select(array($this->primaryKey));
		if($network != "") {
			$query->where("network", $network);
		}
		$query->where("user_id", $user_id);
		$query->whereNull("deleted_at");
		$query->orderBy($this->primaryKey, "ASC");
		$raw_records = $query->get();
		
		if(isset($raw_records[0])) {
			foreach($raw_records as $raw_record) {
				$record = $this->getData($raw_record->{$this->primaryKey});
				if($record !== FALSE) {
					if($network != "") {
						unset($record->network);
					}
					// unset unrequired
					unset($record->user_id);
					
					$data[] = $record;
				}
			}
		}

        // return
        return $data;
    }
	
	
	/**
     * get Friends
     * @param integer $user_id
	 * @param string $network (facebook/instagram/twitter)
	 * @param string $uids comma seperated uids
     * @return NULL
     */
    function putFriends($user_id, $network = "", $uids) {
		// validate type
		$network = trim($network);
		
		$uids_data = explode(",",$uids);
		
		if(isset($uids_data[0])) {
			// default value
			$save_data["user_id"] = $user_id;
			$save_data["network"] = $network;
			
			foreach($uids_data as $uid) {
				if($network != "") {
					// save
					$save_data["uid"] = $uid;
					$save_data["created_at"] = date("Y-m-d H:i:s");
					$this->put($save_data);
				}
			}
		}
		
        // return
        return;
    }
	
	
	/**
     * Remove
     * @param integer $user_id
	 * @param string $network (facebook/instagram/twitter)
     * @return NULL
     */
    function removeFriends($user_id, $network = "") {
		// validate type
		$network = trim($network);
		
		// fetch
		$query = $this->select(array($this->primaryKey));
		$query->where("user_id", $user_id);
		if($network != "") {
			$query->where("network", $network);
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
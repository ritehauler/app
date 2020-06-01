<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
use App\Http\Models\User;

class UserSocialInfo extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'user_social_info';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'user_id', 'network', 'uid', 'access_token', 'profile_url', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
     * get User Disabilities
     *
     * @return NULL
     */
    function getUserInfo($user_id, $network = "") {
		// init data
		$data = array();
		
		// validate type
		$network = trim($network);
		
		// fetch
		$query = $this->select(array($this->primaryKey));
		if($network != "") {
			$query->where("network", $network);
		}
		$query->whereNull("deleted_at");
		$query->where("user_id", $user_id);
		$query->orderBy("network", "ASC");
		$raw_records = $query->get();
		
		if(isset($raw_records[0])) {
			foreach($raw_records as $raw_record) {
				$record = $this->getData($raw_record->{$this->primaryKey});
				if($record !== FALSE) {
					if($network != "") {
						$data[$network] = $record;
					} else {
						$data[] = $record;
					}
				}
			}
		}

        // return
        return $data;
    }
	
	
}
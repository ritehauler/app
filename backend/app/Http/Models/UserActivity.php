<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
use App\Http\Models\User;

class UserActivity extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'user_activity';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'activity_id', 'user_id', 'reference_id', 'target_user_id', 'is_read', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
	 * Check
	 *
	 * @return ID
	*/
	function check($user_id = 0, $activity_id = 0, $reference_id = 0)
    {
		$query = $this->select($this->__fields[0]);
		$query->where('user_id', '=', $user_id);
		$query->where('activity_id', '=', $activity_id);
		$query->where('reference_id', '=', $reference_id);
		$row = $query->get();
		//var_dump($query->toSql());
		return isset($row[0]) ? $row[0]->{$this->__fields[0]} : 0;
    }
	
	
	/**
	 * Mark as Read
	 *
	 * @return Query
	*/
	public function mark_read($user_activity_ids) {
		// init models
		$user_model = new User;
		
		if(isset($user_activity_ids[0])) {
			// query records
			$query = $this
				->whereIn("user_activity_id", $user_activity_ids);
			$raw_records = $query->get();
			
			// set records
			if(isset($raw_records[0])) {
				foreach($raw_records as $raw_record) {
					$user_activity = $this->get($raw_record->user_activity_id);
					$user_activity->is_read = 1;
					// update
					$this->set($user_activity->user_activity_id,(array)$user_activity);
				}
				
				// reset user notification counter
				$target_user = $user_model->get($user_activity->target_user_id);
				if($target_user !== FALSE) {
					$target_user->count_notification = $target_user->count_notification > 0 ? $target_user->count_notification - 1 : 0;
					$user_model->set($target_user->user_id,(array)$target_user);
				}
			}
		}
		return;
	}
	
}
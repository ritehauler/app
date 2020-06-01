<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
use App\Http\Models\UserHistory;
use App\Http\Models\Friend;

class UserView extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'user_view';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'user_id', 'target_user_id', 'view_action_id', 'is_match', 'tracking_id', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
	 * Remove View Action
	 * @param integer $user_id
	 * @param integer $target_user_id
	 * @param string $action_key
	 * @return Query
	*/
	public function removeAction($user_id = 0, $target_user_id = 0, $action_key = "") {
		// set key
		$action_key = trim($action_key);
		$query = $this->where("user_id","=",$user_id);
		$query->where("target_user_id","=",$target_user_id);
		$query->whereNull("deleted_at");
		if($action_key != "") {
			$query->whereRaw("view_action_id = (
				SELECT view_action_id FROM view_action
				WHERE `key` = '".$action_key."'
				LIMIT 1
			)");
		}
		$query->update(array("deleted_at" => date("Y-m-d H:i:s")));		
		return;
	}
	
	
	/**
	 * Revvert user action
	 * @param integer $user_id
	 * @param integer $target_user_id
	 * @param integer $user_view_id
	 * @param integer $tracking_id
	 * @return Query
	*/
	public function revertAction($user_id = 0, $target_user_id = 0, $user_view_id = 0, $tracking_id = 0) {
		// init model
		$user_history_model = new UserHistory;
		$friend_model = new Friend;
		
		// remove user_view action
		$query = $this->where("user_view_id","=",$user_view_id);
		$query->whereNull("deleted_at");
		$query->update(array("deleted_at" => date("Y-m-d H:i:s")));
		// remove view_action history
		$query = $user_history_model->where("reference_module","=","view_action");
		$query->where("user_id","=",$user_id);
		$query->where("against","=","user");
		$query->where("against_id","=",$target_user_id);
		$query->where("tracking_id","=",$tracking_id);
		$query->whereNull("deleted_at");
		$query->update(array("deleted_at" => date("Y-m-d H:i:s")));	
		// remove match
		$query = $friend_model->where("user_id","=",$user_id);
		$query->where("target_user_id","=",$target_user_id);
		$query->where("tracking_id","=",$tracking_id);
		$query->whereNull("deleted_at");
		$query->update(array("deleted_at" => date("Y-m-d H:i:s")));
		// remove match history
		$query = $user_history_model->where("reference_module","=","friend");
		$query->where("user_id","=",$user_id);
		$query->where("against","=","user");
		$query->where("against_id","=",$target_user_id);
		$query->where("tracking_id","=",$tracking_id);
		$query->whereNull("deleted_at");
		$query->update(array("deleted_at" => date("Y-m-d H:i:s")));	
		
		return;
	}
	
	
}
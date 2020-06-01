<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
// models
use App\Http\Models\History;
use App\Http\Models\UserHistory;
use App\Http\Models\UserView;
use App\Http\Models\Message;


class Friend extends Base {

    public function __construct() {
        // set tables and keys
        $this->__table = $this->table = 'friend';
        $this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'user_id', 'target_user_id', 'status', 'tracking_id', 'created_at', 'updated_at', 'deleted_at');
    }
	
	/**
     * check
     * @param integer $user_id
	 * @param integer $target_user_id
	 * @param string $status (optional status 0/1 or empty)
     * @return integer user_report_id
     */
    function check($user_id, $target_user_id, $status = "") {		
		// fetch
		$query = $this->select(array($this->primaryKey));
		$query->whereRaw("((user_id = '".$user_id."' AND target_user_id = '".$target_user_id."') OR (user_id = '".$target_user_id."' AND target_user_id = '".$user_id."'))");
		if($status != "") {
			$query->where("status", $status);
		}
		$query->whereNull("deleted_at");
		$raw_records = $query->get();
		
        // return
        return isset($raw_records[0]) ? $raw_records[0]->{$this->primaryKey} : 0;
    }
	
	
	/**
     * remove
     * @param integer $user_id
	 * @param integer $target_user_id
     * @return NULL
     */
    function remove($user_id = 0, $target_user_id = 0) {		
		// get friend record
		$record_id = $this->check($user_id, $target_user_id, 1);
		$record_data = $this->get($record_id);
		
		if($record_data !== FALSE) {
			// init model
			$history_model = new History;
			$user_history_model = new UserHistory;
			$user_view_model = new UserView;
			$message_model = new Message;
			
			// remove friend record
			//$record_data->status = 0;
			$record_data->deleted_at = date("Y-m-d H:i:s");
			$this->set($record_data->{$this->primaryKey},(array)$record_data);
			
			// find history // (remove_friend)
			$history = $history_model->getBy("key","remove_friend");
			if($history !== FALSE) {
				// init save data
				$save["history_id"] = $history->history_id;
				$save["user_id"] = $user_id;
				$save["reference_module"] = "friend";
				$save["reference_id"] = $record_data->{$this->primaryKey};
				$save["against"] = "user";
				$save["against_id"] = $target_user_id;
				// save data
				$user_history_model->put($save);
			}
			// get liked/superliked action
			/*$query = $user_view_model->select(array("user_view_id"));
			$query->whereRaw("view_action_id IN (
				SELECT view_action_id
				FROM view_action
				WHERE `key` != 'pass'
				AND `user_id` = '".$user_id."'
				AND target_user_id = '".$target_user_id."'
			)");
			$query->whereNull("deleted_at");
			$query_record = $query->get();
			
			// if action record found, do delete
			if(isset($query_record[0])) {
				$user_view = $user_view_model->get($query_record[0]->user_view_id);
				// if record found
				if($user_view !== FALSE) {
					$user_view->deleted_at = date("Y-m-d H:i:s");
					// update
					$user_view_model->set($user_view->user_view_id,(array)$user_view);
				}
			}*/
			
			// remove view actions
			$user_view_model->removeAction($user_id, $target_user_id, "like");
			$user_view_model->removeAction($user_id, $target_user_id, "super_like");
			$user_view_model->removeAction($target_user_id, $user_id, "like");
			$user_view_model->removeAction($target_user_id, $user_id, "super_like");
			
			// remove chat list
			$message_model->removeChatMessages($user_id, $target_user_id);
			
		}
		
		// if another record
		$record_id = $this->check($user_id, $target_user_id, 1);
		if($record_id > 0) {
			$this->remove($user_id, $target_user_id);
		}
		
        // return
        return NULL;
    }	

}

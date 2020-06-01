<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
use App\Http\Models\User;
use App\Http\Models\UserHistory;

class Message extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'message';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'sender_id', 'receiver_id', 'message', 'is_unread', 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
	 * Remove Chat
	 *
	 * @return Query
	*/
	public function removeChat($user_id = 0, $target_user_id = 0) {
		// query records
		$sql = "INSERT INTO message_trash (user_id, message_id, created_at)
		SELECT ".$user_id.", message_id, '".date("Y-m-d H:i:s")."'
		FROM message
		WHERE ((receiver_id = '".$user_id."' AND sender_id = '".$target_user_id."')
		OR (sender_id = '".$user_id."' AND receiver_id = '".$target_user_id."'))
		AND message_id NOT IN (
			SELECT message_id FROM message_trash
			WHERE user_id = '".$user_id."'
		)";
		\DB::statement($sql);
		// save into history
		$user_history_model = new UserHistory;
		// save history
		$reference_data = array(
			"reference_module" => "message",
			"reference_id" => ($user_id > $target_user_id) ? $target_user_id."-".$user_id : $user_id."-".$target_user_id,
			"against" => "user",
			"against_id" => $target_user_id
		);
		$user_history_model->putUserHistory($user_id,"message_chat_remove",$reference_data);
		
		return;
	}
	
	/**
	 * Mark as Read
	 *
	 * @return Query
	*/
	public function markRead($receiver_id = 0, $sender_id = 0) {
		// init models
		$user_model = new User;
		
		// query records
		$query = $this->select(array($this->primaryKey))
			->where("receiver_id", $receiver_id)
			->where("sender_id", $sender_id)
			->where("is_unread", 1);
		$raw_records = $query->get();
		
		// set records
		if(isset($raw_records[0])) {
			foreach($raw_records as $raw_record) {
				$message = $this->get($raw_record->{$this->primaryKey});
				$message->is_unread = 0;
				$message->updated_at = date("Y-m-d H:i:s");
				// update
				$this->set($message->{$this->primaryKey},(array)$message);
			}
			
			/*// reset user notification counter
			$user = $user_model->get($receiver_id);
			if($user !== FALSE) {
				$user->count_notification = $user->count_notification > 0 ? $user->count_notification - 1 : 0;
				$user_model->set($user->user_id,(array)$user);
			}*/
		}
		return;
	}
	
	
	/**
	 * Remove Chat
	 *
	 * @return Query
	*/
	public function removeChatMessages($user_id = 0, $target_user_id = 0) {
		
		$query = $this->select(array($this->primaryKey));
		$query->whereRaw("(
			(sender_id = '".$user_id."' AND receiver_id = '".$target_user_id."')
			OR (sender_id = '".$target_user_id."' AND receiver_id = '".$user_id."')
		)");
		$query->whereNull("deleted_at");
		$query_records = $query->get();
		
		// records found
		if(isset($query_records[0])) {
			foreach($query_records as $query_record) {
				/*$record = $this->get($query_record->{$this->primaryKey});
				$record->deleted_at = date("Y-m-d H:i:s");
				$this->set($query_record->{$this->primaryKey}, (array)$record);*/
				//$this->remove($query_record->{$this->primaryKey});
				$this->hardRemove($query_record->{$this->primaryKey});
			}			
		}
		
		return;
	}
	/**
	 * Count Unread Messages
	 *
	 * @return Query
	 */
	public function messageCount($user_id = 0, $target_user_id = 0){
		$query = $this->select(array($this->primaryKey));
		$query->whereRaw("(
			(sender_id = '".$user_id."' AND receiver_id = '".$target_user_id."')
			OR (sender_id = '".$target_user_id."' AND receiver_id = '".$user_id."')
		)");
 
		$query->join("message_trash","message_trash.message_id", "=", "message.message_id");
		$query->where("message_trash.message_id" , "=" , "message.message_id");
 
		//$query->join("message_trash","message_trash.message_id", "=", "message.message_id");
		//$query->where("message_trash.message_id" , "=" , "message.message_id");
 
		$query_records = $query->count();
		return $query_records;
	}

}
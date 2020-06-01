<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
use App\Http\Models\History;
use App\Http\Models\HistoryNotification;
use App\Http\Models\ViewAction;

class UserHistory extends Base {
	
	private $_model_path = "\App\Http\Models\\";
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'user_history';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'history_id', 'user_id', 'reference_module', 'reference_id', 'against', 'against_id', 'tracking_id', 'navigation_type', 'navigation_item_id', 'is_read' , 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
     * Save user History
     * @param integer $user_id
	 * @param string $identifier
	 * @param array $other_data
     * @return integer insert_id
     */
    function putUserHistory($user_id, $identifier = "", $other_data) {
		// init models
		$history_model = new History;
		
		// default
		$insert_id = FALSE;
		
		// validate key
		$identifier_data = $history_model->getBy("identifier", $identifier);
		
		if($identifier_data !== FALSE && isset($other_data["reference_module"])) {
			// init models
			$history_notification_model = new HistoryNotification;
			
			// set defaults
			$other_data["reference_id"] = isset($other_data["reference_id"]) ? $other_data["reference_id"] : 0;
			$other_data["against"] = isset($other_data["against"]) ? $other_data["against"] : "";
			$other_data["against_id"] = isset($other_data["against_id"]) ? $other_data["against_id"] : 0;
			$other_data["tracking_id"] = isset($other_data["tracking_id"]) ? $other_data["tracking_id"] : 0;
			$other_data["navigation_type"] = isset($other_data["navigation_type"]) ? $other_data["navigation_type"] : '';
			$other_data["navigation_item_id"] = isset($other_data["navigation_item_id"]) ? $other_data["navigation_item_id"] : 0;
			
			// save
			$save_data["user_id"] = $user_id;
			$save_data["history_id"] = $identifier_data->history_id;
			$save_data["reference_module"] = $other_data["reference_module"];
			$save_data["reference_id"] = $other_data["reference_id"];
			$save_data["against"] = $other_data["against"];
			$save_data["against_id"] = $other_data["against_id"];
			$save_data["tracking_id"] = $other_data["tracking_id"];
			$save_data["navigation_type"] = $other_data["navigation_type"];
			$save_data["navigation_item_id"] = $other_data["navigation_item_id"];
			
			
			$save_data["created_at"] = date("Y-m-d H:i:s");
			// save
			$insert_id = $this->put($save_data);
			
			// defaults
			$target_user_id = 0;
			$send_notifications = 1;
			
			// get target user
			if($save_data["against"] == "user") {
				$target_user_id = $save_data["against_id"];
			}
			
			// EXCLUSION starts
			// If history is regarding User View's (pass/like/super_like), exclude notifications other than super_like
			/*if($save_data["reference_module"] == "post") {
				// init models
				$view_action_model = new ViewAction;
				// data
				$view_action = $view_action_model->get($save_data["reference_id"]);
				// dont process notification if it is not regarding super_like
				if($view_action->identifier != "super_like") {
					$send_notifications = 0;
				}
			}*/
			
			if($save_data["reference_module"] == "post") {
				$send_notifications = 0;
			}
			
			// If history is regarding user, ignore push notification
			if($save_data["reference_module"] == "user") {
				$send_notifications = 0;
			}
			// EXCLUSION ends
			
			// process notification
			if($send_notifications > 0) {
				// send push notification
				if($identifier_data->notification_type == "push") {					
					$history_notification_model->sendPush($identifier_data,$user_id,$target_user_id, $insert_id);
				}
				
				// send email
				if($identifier_data->notification_type == "email") {
					$history_notification_model->sendEmail($identifier_data,$user_id,$target_user_id);
				}
			}
			
		}

        // return
        return $insert_id;
    }
	
	
		
	/**
     * Get Data
     * @param integer $pk
     * @return Object
     */
    public function getData($id = 0) {
		// load models
		$user_model = $this->_model_path."User";
		$user_model = new $user_model;	
		
		$history_model = $this->_model_path."History";
		$history_model = new $history_model;	
		
		// get notification_type
		$notification_type = \config("constants.NOTIFICATION_TYPES");
			
		// init target
        $data = $this->get($id);
		
		// get history by history_id
		$history = $history_model->get($data->history_id);
				
		
		// got data
		if($data) {				
			// sender data
			$data->sender = $user_model->getMiniData($data->user_id);
			
			// receiver data
			$data->receiver = $user_model->getMiniData($data->against_id);
			
			// set message
			$data->message = $data->sender['name'] . $notification_type[$history->identifier];
			
			// unset unrequired			
			unset($data->history_id);
			unset($data->user_id);
			unset($data->against);
			unset($data->against_id);
			unset($data->reference_module);
			unset($data->reference_id);
			unset($data->tracking_id);			
			unset($data->updated_at);
			unset($data->deleted_at);

		}
        return $data;
    }
	
}
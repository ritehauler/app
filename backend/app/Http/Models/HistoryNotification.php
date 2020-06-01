<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
use App\Http\Models\User;
use App\Http\Models\Notification;
use App\Http\Models\UserPreference;
use App\Libraries\ApiCurl;

class HistoryNotification extends Base {


	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'history_notification';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		$this->_entity_search_url = \URL::to(DIR_API) . '/system/entities/listing';
		$this->_history_notify_identifier = "history_notification";
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'history_identifier', "plugin_identifier", 'type', 'for', 'title', 'body', 'key_code', 'hint', 'wildcards', 'replacers', 'created_at', 'updated_at', 'deleted_at');
	}


	/**
	 * Send Push Notification
	 * @param $history
	 * @param $entity_type_id
	 * @param $entity_id
	 * @param int $target_entity_type_id
	 * @param int $target_entity_id
	 * @param int $entity_history_id
	 */
    function sendPush($history, $entity_type_id,$entity_id, $target_entity_type_id = 0, $target_entity_id = 0, $entity_history_id = 0) {
		// if history exists
		if(is_object($history) && isset($history->entity_id)) {
			// init models
			$notification_model = new Notification;
			
			// defaults
			$user = array();
			$target_user = array();
			$ApiCurl = new ApiCurl();

			//Getting History notification by identifier
			$parameter['entity_type_id'] = $this->_history_notify_identifier; // entity type id (history)
			$parameter['history_id'] = $history->entity_id;
			$parameter['history_notification_type'] = 1; // notification type is "push"
			$parameter['notification_for'] = 1; // notification for is "to_entity"
			$history_notification_data = $ApiCurl->apiPostRequest($this->_entity_search_url, 'GET', $parameter);

			if (isset($history_notification_data->data->page->total_records) && $history_notification_data->data->page->total_records>0) {

				$history_notification_entity = $history_notification_data->data->entity_listing[0];
				 $history_notification = $history_notification_entity->attributes;

				// - get user data
				//Get Entity data
				$entity_data = $ApiCurl->apiPostRequest($this->_entity_search_url, 'GET', array('entity_type_id'=>$entity_type_id,'entity_id'=>$entity_id));

				if (isset($entity_data->data->page->total_records) && $entity_data->data->page->total_records>0) {
					$user = $entity_data->data->entity_listing[0];

				}

				//Get Target entity data
				if($target_entity_id > 0){
					$target_entity_data = $ApiCurl->apiPostRequest($this->_entity_search_url, 'GET', array('entity_type_id'=>$target_entity_type_id,'entity_id'=>$target_entity_id));

					if (isset($target_entity_data->data->page->total_records) && $target_entity_data->data->page->total_records>0) {
						$target_user = $target_entity_data->data->entity_listing[0];
					}
				}


				// - get user device type & tokens
				if(isset($user->device_token) && in_array($user->device_type,array("ios","android")) && $user->device_token != "") {
					// set body
					if($history_notification->wildcards != "") {
						$wildcards = explode(",",$history_notification->wildcards);
						$replacers = explode(",",$history_notification->replacers);
						// replace title
						$history_notification->title = str_replace($wildcards,$replacers,$history_notification->title);
						eval("\$history_notification->title = \"$history_notification->title\";");
						// replace body
						$history_notification->body = str_replace($wildcards,$replacers,$history_notification->body);
						eval("\$history_notification->body = \"$history_notification->body\";");
					}
					// prepare notification data
					$notification_data = array(
						"title" => $history_notification->title,
						"body" => $history_notification->body,
						"key_code" => intval($history_notification->key_code),
						"sound" => isset($user->sound) ? $user->sound : "",
						"badge" => isset($user->count_notification) ? $user->count_notification : "",
						//"user" => $user ? $user : array(),
						//"target_user" => $target_user ? $target_user : array(),
						"user_id" => $user->entity_id,
						"target_user_id" => isset($target_user->entity_id) ? $target_user->entity_id : "",
						"user_name" => isset($user->name) ? $user->name : "",
						"target_user_name" => isset($target_user->name) ? $target_user->name : "",
					);
					// send
					if($user->device_type == "android") {
						$ret = $notification_model->pn_android($user->device_token,$notification_data);
						//echo "<pre>"; print_r($ret); exit;
					} else {
						$notification_model->pn_ios($user->device_token,$notification_data);
					}
				}
			}
			
			
			
			// check if record exists for : to_target_user
			//Getting History notification by identifier
			if($target_entity_id > 0) {
				$parameter['entity_type_id'] = $this->_history_notify_identifier; // entity type id (history)
				$parameter['history_id'] = $history->entity_id;
				$parameter['history_notification_type'] = 1; // notification type is "push"
				$parameter['notification_for'] = 2; // notification for is "to_target_entity"
				$history_notification_data = $ApiCurl->apiPostRequest($this->_entity_search_url, 'GET', $parameter);

				if (isset($history_notification_data->data->page->total_records) && $history_notification_data->data->page->total_records > 0) {

					$history_notification = $history_notification_data->data->entity_listing[0];

					// - get user data // - taken above
					//Get Entity data
					$entity_data = $ApiCurl->apiPostRequest($this->_entity_search_url, 'GET', array('entity_type_id' => $entity_type_id, 'entity_id' => $entity_id));

					if (isset($entity_data->data->page->total_records) && $entity_data->data->page->total_records > 0) {
						$user = $entity_data->data->entity_listing[0];
					}

					//Get Target entity data
					$target_entity_data = $ApiCurl->apiPostRequest($this->_entity_search_url, 'GET', array('entity_type_id' => $target_entity_type_id, 'entity_id' => $target_entity_id));

					if (isset($target_entity_data->data->page->total_records) && $target_entity_data->data->page->total_records > 0) {
						$target_user = $target_entity_data->data->entity_listing[0];
					}

					// default
					$send_notification = 1;

					// check histories
					// - super_like filter (for view_action) already checked in parent module (i.e: UserHistory)
					/*	$check_histories = array("view_action", "made_match", "message_send");
                        $preference_keys = array(
                            "view_action" => "new_super_like",
                            "made_match" => "new_match",
                            "message_send" => "new_message"
                        );
                        // if specific histories (new message, new super_like, new match)
                        if(in_array($history->{"key"},$check_histories)) {

                            // check if in preferences
                            if(isset($preference_keys[$history->{"key"}])) {
                                $pref_key = $preference_keys[$history->{"key"}];
                                $send_notification = $target_user->setting_notification[$pref_key];
                                // if preference is set
                                /*if($target_user->setting_notification[$pref_key] == 0) {
                                    $send_notification = 0;
                                }*/

					/*	}
                    }*/

					// if sending true
					if ($send_notification > 0) {
						// - get user device type & tokens
						if (isset($target_user->device_token) && in_array($target_user->device_type, array("ios", "android")) && $target_user->device_token != "") {
							// set body
							if ($history_notification->wildcards != "") {
								$wildcards = explode(",", $history_notification->wildcards);
								$replacers = explode(",", $history_notification->replacers);
								// replace title
								$history_notification->title = str_replace($wildcards, $replacers, $history_notification->title);
								eval("\$history_notification->title = \"$history_notification->title\";");
								// replace body
								$history_notification->body = str_replace($wildcards, $replacers, $history_notification->body);
								eval("\$history_notification->body = \"$history_notification->body\";");
							}
							// prepare notification data
							$notification_data = array(
								"title" => $history_notification->title,
								"body" => $history_notification->body,
								"key_code" => intval($history_notification->key_code),
								"sound" => isset($user->sound) ? $user->sound : "",
								"badge" => isset($user->count_notification) ? $user->count_notification : "",
								//"user" => $target_user ? $target_user : array(), // opposite user
								//"target_user" => $user ? $user : array(), // opposite
								"user_id" => $user->entity_id,
								"target_user_id" => $target_user->entity_id,
								"user_name" => isset($user->name) ? $user->name : "",
								"target_user_name" => isset($target_user->name) ? $target_user->name : "",
							);
							$t = array("notification_status" => array(), "params" => array());
							$t["params"] = $notification_data;
							$t["timestamp"] = date("c");
							//$t["target_user"] = $target_user;
							// send
							if ($target_user->device_type == "android") {
								$t["notification_status"] = $notification_model->pn_android($target_user->device_token, $notification_data);
							} else {
								$t["notification_status"] = $notification_model->pn_ios($target_user->device_token, $notification_data);
							}

							@file_put_contents(getcwd() . "/test_errors.log", json_encode($t) . "\n\n");
						}
					}

				}
			}
			
		}

        // return
        return;
    }
	
	
	/**
     * Send Email Notification
     * @param object $history
	 * @param integer $user_id
	 * @param integer $target_user_id
	 * @param integer $entity_history_id
     * @return void
     */
    function sendEmail($history, $user_id, $target_user_id = 0, $entity_history_id = 0) {
		// init models
		$user_model = new User;
		
		if(is_object($history) && isset($history->history_id)) {
			
		}

        // return
        return;
    }
	
}
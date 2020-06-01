<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

// models
//use App\Http\Models\History;
use App\Http\Models\HistoryNotification;
use App\Http\Models\ViewAction;
use App\Libraries\ApiCurl;

class EntityHistory extends Base {
	
	private $_model_path = "\App\Http\Models\\";
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'entity_history';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		$this->_entity_search_url = \URL::to(DIR_API) . '/system/entities/listing';
		$this->_history_identifier = "history_notification";
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'history_notification_id','entity_type_id','entity_id','is_archive','actor_entity_type_id', "actor_entity_id", 'against_entity_type_id', 'against_entity_id', 'tracking_id', 'is_read' , 'created_at', 'updated_at', 'deleted_at');
	}
	
	
	/**
     * Save entity History
     * @param string $entity_type
	 * @param integer $entity_id
	 * @param string $identifier
	 * @param array $other_data
     * @return integer insert_id
     */
    function putEntityHistory($history_identifier,$entity_type_id,$entity_id,$actor_entity_type_id, $actor_entity_id, $against_entity_type_id = 0,$against_entity_id=0,$isArchive=true) {
		// init models
		//$history_model = new History;

		// default
		$insert_id = FALSE;
		$history_model = new HistoryNotification();
		// validate key
		//$identifier_data = $history_model->getBy("history_identifier", $history_identifier);
		$query = $history_model->where("history_identifier", "=", $history_identifier)->whereNull("deleted_at");
		 
	    $identifier_data = $query->first();
		//Getting History by identifier
		//echo "<pre>"; print_r($data); exit;
				if($isArchive){
					$archive = $this
					->where("entity_type_id", "=", $entity_type_id)
					->where("entity_id", "=", $entity_id)
					->where("is_archive", "=", '0');
					if($archive->count()>0){
						$this->where("entity_type_id", "=", $entity_type_id)
						->where("entity_id", "=", $entity_id)
						->where("is_archive", "=", '0')
						->update(array('is_archive'=>'1'));
					}
				}
				if ($identifier_data) { 	
						// save
						$save_data["history_notification_id"] = $identifier_data->history_notification_id;
				}
				$save_data["entity_type_id"] = $entity_type_id;
				$save_data["entity_id"] = $entity_id;
				$save_data["actor_entity_type_id"] = $actor_entity_type_id;
				$save_data["actor_entity_id"] = $actor_entity_id;
				$save_data["against_entity_type_id"] = $against_entity_type_id;
				$save_data["against_entity_id"] = $against_entity_id;

				$save_data["created_at"] = date("Y-m-d H:i:s");
				// save
				$insert_id = $this->put($save_data);

				/*// defaults
				$target_user_id = 0;
				$send_notifications = 1;

				// get target user
				/*if ($save_data["against"] == "user") {
					$target_user_id = $save_data["against_id"];
				}
				$target_entity_type_id = $save_data["against_entity_type_id"];
				$target_entity_id = $save_data["against_entity_id"];*/
				
				// EXCLUSION starts
			
				/*
				// process notification
				if ($send_notifications > 0) {
					// send push notification
					if ($identifier_data->attributes->notification_type->value == 2) { // if push
						$history_notification_model->sendPush($identifier_data, $entity_type_id,$entity_id, $target_entity_type_id,$target_entity_id, $insert_id);

					}

					// send email
					if ($identifier_data->attributes->notification_type->value == 3) { //if email
						$history_notification_model->sendEmail($identifier_data, $entity_id, $target_user_id, $insert_id);
					}
				}*/

        // return
        return $insert_id;
    }
	

	public function get_count($entity_id,$is_read,$sorting,$offset=0){
		$query = $this->where("against_entity_id", "=", $entity_id)->where("is_archive", "=", '0');
		if ($offset > 0) {
			$operator = strtolower($sorting) == "asc" ? ">" : "<";
			$query = $query->where("entity_history_id", $operator, $offset);
		}

		if(isset($is_read) && is_numeric($is_read)){
			$query = $query->where("is_read","=",$is_read);
		}

			
			 
        return $query->count();
			
	} 
		
	/**
     * Get Data
     * @param integer $pk
     * @return Object
     */
    public function getNotificationData($entity_id,$request) {
		// load models
		
		$data[$this->_history_identifier] = array();
		
		$user_model = $this->_model_path."SYSEntityAuth";
		$user_model = new $user_model;	

		$SYSEntity = $this->_model_path."SYSEntity";
		$SYSEntity = new $SYSEntity;	

		$allowed_sorting = "asc,desc";
		$limit    = (int)trim(strip_tags($request->input('limit', 0)));
        $limit    = $limit == "" ? PAGE_LIMIT_API : $limit;
		 
		$offset = (int)trim(strip_tags($request->input('offset', 0)));
		
		if(!is_numeric($offset)) $offset = 0;

    	$offset = $offset < 0 ? 0 : $offset;
   		$next_offset = $offset; // - new pagination flow
	
		//$order_by = ($request->input('order_by', "") == "") ? explode(",", $allowed_ordering)[0] : $order_by;
    	$sorting = ($request->input('sorting', "") == "") ? explode(",", $allowed_sorting)[0] :$request->input('sorting');
		
		$total_records = $this->get_count($entity_id,'',$sorting,$offset);
		$total_unread = $this->get_count($entity_id,0,$sorting,$offset);
				 
		$notificationData = $this
		//->select($this->primaryKey,'eh.*','hn.type','hn.for','hn.title','hn.body','hn.wildcards','hn.viewable')
		//->join('history_notification AS hn', 'hn.history_notification_id', '=', 'eh.history_notification_id')
		//->from('entity_history AS eh')
		->where("against_entity_id", "=", $entity_id)->whereNull("deleted_at")->where("is_archive", "=", '0');
		if(isset($request->is_read) && is_numeric($request->is_read)){
			$notificationData = $notificationData->where("is_read","=",$request->is_read);
		}

		$notificationData = $notificationData->orderBy("entity_history_id", strtoupper($sorting));
		
		$notificationData = $notificationData->take($limit);
		if ($offset > 0) {
			$operator = strtolower($sorting) == "asc" ? ">" : "<";
			$notificationData->where("entity_history_id", $operator, $offset);
		}
		
		$notificationData = $notificationData->get();
 	   // got data
		if($notificationData) {	
			foreach($notificationData as $key=>$notification){	
				$data[$this->_history_identifier][$key] = $notification ;		
				// receiver
				$data[$this->_history_identifier][$key]->identifier = 'order';
				$entityData = $SYSEntity->getListData($notification->entity_id,$notification->entity_type_id,$request);
				
				if(isset($entityData['order'][0])){
					$data[$this->_history_identifier][$key]->order = $entityData['order'][0];
				}
				/*$queryWhere = "entity_id=$notification->actor_entity_id AND (deleted_at IS NULL)";
				$_flatTable = 'order_flat';
			 
				$attrs = \DB::select("SELECT entity_id FROM $_flatTable WHERE $queryWhere");
				if (isset($attrs[0])) {
					$notificationData[$key]->actor_entity_id = $attrs[0];
				}*/
				$next_offset = $notification->entity_history_id; // new pagination flow
			}
		} 
		
		// set pagination response
		$data["page"] = array(
			
			"limit" => $limit,
			//"current" => $page_no,
			"total_records" => $total_records,
			"unread" => $total_unread,
			//"next" => $page_no >= $total_pages ? 0 : $page_no + 1,
			//"prev" => $page_no <= 1 ? 0 : $page_no - 1
			"next_offset" => $next_offset,
			"prev_offset" => $offset
		);
			
		
        return $data;
    }
	
}
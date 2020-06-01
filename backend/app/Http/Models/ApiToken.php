<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class ApiToken extends Base {
	
	use SoftDeletes;
    public $table = 'api_token';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	private $_entityTypeModel = "SYSEntityType";
	private $_entityModel = "SYSEntity";

	// other vars
	private $_salt_pattren, $_token_expiry_time, $_entity_types, $_entity_models, $_entity_pks;
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = $this->table.'_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
		$this->__fields   = array($this->primaryKey, 'entity_type', 'entity_id', 'token', 'count_requests', "request_headers", 'expire_at', 'created_at', 'updated_at', 'deleted_at');
		
		// set other values
		$this->_salt_pattren = config('api_oauth.TOKEN_PATTREN');
		$this->_entity_types = config('api_oauth.ENTITY_TYPES');
		$this->_entity_models = config('api_oauth.ENTITY_MODELS');
		$this->_entity_pks = config('api_oauth.ENTITY_PKS');
		$this->_token_expiry_time = config('api_oauth.TOKEN_EXPIRY_TIME');

		// init models
		$this->_entityTypeModel = $this->__modelPath.$this->_entityTypeModel;
		$this->_entityTypeModel = new $this->_entityTypeModel;

		$this->_entityModel = $this->__modelPath.$this->_entityModel;
		$this->_entityModel = new $this->_entityModel;
	}
	
	/**
     * getDataByToken
	 * @param string $token
	 * @param integer $do_update_data
     * @param string $entity_type
	 * @param integer $entity_id
     * @return Object
     */
    public function getDataByToken($token, $do_update_data = 0, $entity_type = NULL, $entity_id = NULL) {
		// set data types
		$entity_type = $entity_type == "none" ? NULL : $entity_type;
		$entity_id = intval($entity_id) == 0 ? NULL : $entity_id;
	    // set timestamps
	    $current_timestamp = strtotime(date("Y-m-d H:i:s"));
	    $expiry_timestamp = $current_timestamp + $this->_token_expiry_time;
		
		// request headers
		$request_headers = apache_request_headers(); 
		// perform query
		$raw_query = $this->select($this->primaryKey)
			//->whereRaw("token = '$token'")
			//->whereRaw("expire_at > '".date("Y-m-d H:i:s",$current_timestamp)."'")
			->where("token","=",$token)
			->where("expire_at",">",date("Y-m-d H:i:s",$current_timestamp))
			->whereNull("deleted_at");
		/*if($entity_type !== NULL) {
			//$raw_query->whereRaw("entity_type = '$entity_type'");
			//$raw_query->whereRaw("entity_id = '$entity_id'");
			$raw_query->where("entity_type","=",$entity_type);
			$raw_query->where("entity_id","=",$entity_id);
		} else {
			$raw_query->whereNull("entity_type");
			$raw_query->whereNull("entity_id");
		}*/
		$raw_record = $raw_query->get();
		// init target
        $data = $this->get((isset($raw_record[0]) ? $raw_record[0]->{$this->primaryKey} : 0));
		// got data
		if($data !== FALSE) {
			// update data if requested
			if($do_update_data > 0) {

				// check if token is related to entity
				if($data->entity_type !== NULL && $data->entity_id !== NULL) {
					/*
					// - COMMENTED : We do not "have last_seen_at" in entity main table
					// entity data
					$entity = $this->_entityModel->get($data->{$this->_entityModel->primaryKey});
					// if found
					if($entity !== FALSE) {
						// assign last seen and update
						//$entity->last_seen_at = date("Y-m-d H:i:s",$current_timestamp);
						//$this->_entityModel->set($entity->{$entity_pk},(array)$entity);
					}*/
				}
				

				// set values
				$data->updated_at = date("Y-m-d H:i:s",$current_timestamp);
				$data->expire_at = date("Y-m-d H:i:s",$expiry_timestamp);
				$data->count_requests = intval($data->count_requests) + 1;
				$data->request_headers = json_encode($request_headers);
				// set record
				$this->set($data->{$this->primaryKey},(array)$data);
			}
		}
        return $data;
    }
	
	/**
     * extendExpiry
	 * @param string $token
     * @param string $entity_type
	 * @param integer $entity_id
     * @return Object
     */
    public function extendExpiry($token, $entity_type = NULL, $entity_id = NULL) {
	    // set timestamps
	    $current_timestamp = strtotime(date("Y-m-d H:i:s"));
	    $expiry_timestamp = $current_timestamp + $this->_token_expiry_time;

		// perform query
		$raw_query = $this->select($this->primaryKey)
			->where("token","=",$token)
			->whereNull("deleted_at");
		if($entity_type !== NULL) {
			$raw_query->where("entity_type","=",$entity_type);
			$raw_query->where("entity_id","=",$entity_id);
		}
		$raw_record = $raw_query->get();
		// init target
        $data = $this->get((isset($raw_record[0]) ? $raw_record[0]->{$this->primaryKey} : 0));
		// got data
		if($data !== FALSE) {

			// set values
			$data->updated_at = date("Y-m-d H:i:s",$current_timestamp);
			$data->expire_at = date("Y-m-d H:i:s",$expiry_timestamp);
			// set record
			$this->set($data->{$this->primaryKey},(array)$data);
		}
        return $data;
    }
	
	/**
     * generateCode
     * @param string $entity_type
	 * @param integer $entity_id
     * @return string
     */
	 function generateCode($entity_type = NULL, $entity_id = NULL) {
		 
		 $rand = microtime().":".mt_rand();
		 // set init token
		 $token = $this->_salt_pattren . md5(md5($this->_salt_pattren . sha1($this->_salt_pattren . $rand)));
		 // check if entity
		 if($entity_type !== NULL && $entity_id !== NULL) {
			 // generate entity token
			 $token = $entity_type.":".$entity_id."-".$token;
			 // hash again
			 $token = $this->_salt_pattren . md5(md5($this->_salt_pattren . sha1($this->_salt_pattren . $token)));
		 }
		 else {
			 $token = $this->_salt_pattren . md5(md5($this->_salt_pattren . sha1($this->_salt_pattren . $rand)));
		 }
		 
		 return $token;
	 }
	 
	
	/**
     * generate
     * @param string $entity_type
	 * @param integer $entity_id
     * @return string
     */
    function generate($entity_type = NULL, $entity_id = NULL) {
		$token = NULL;

		// set data types
		$entity_type = $entity_type == "none" ? NULL : $entity_type;
		$entity_id = intval($entity_id) == 0 ? NULL : $entity_id;
		
		// check valid entity
		if($entity_type !== NULL && $entity_id !== NULL) {
		
			// get entity record
			$entity_record = $this->_entityModel->get($entity_id);
            /*var_dump($entity_id);
            var_dump($entity_record);
            exit;*/
			// if valid entity
			if($entity_record !== FALSE) {
				// if not deleted
				if($entity_record->deleted_at === NULL) {
					// generate entity token
					$token = $this->generateCode($entity_type, $entity_id);
					// assign token
					$this->_assignToken($token, $entity_type, $entity_id);
				}
			} /*else {
				$this->_assignToken($token, "none");
			}*/
			
		} elseif($entity_type === NULL && $entity_id === NULL) {
			// set init token code
			$token = $this->generateCode($entity_type, $entity_id);
			// assign token
			$this->_assignToken($token, $entity_type, $entity_id);
		} 
		
		return $token;
    }
	
	/**
     * _assignToken
     * @param string $token
     * @return string
     */
    private function _assignToken($token, $entity_type = "none", $entity_id = NULL) {
		// request headers
		$request_headers = apache_request_headers();
		
		// set timestamps
		$current_timestamp = strtotime(date("Y-m-d H:i:s"));
		$expiry_timestamp = $current_timestamp + $this->_token_expiry_time;
		
		// set data
		$save["token"] = $token;
		$save["entity_type"] = $entity_type;
		$save["entity_id"] = $entity_type != "none" ? $entity_id : 0;
		$save["request_headers"] = json_encode($request_headers);
		$save["expire_at"] = date("Y-m-d H:i:s",$expiry_timestamp);
		$save["created_at"] = date("Y-m-d H:i:s", $current_timestamp);		
		$this->put($save);
		// insert
		return $token;
    }
	
	
	/**
     * refreshToken
     * @param string $old_token
	 * @param string $entity_type
	 * @param integer $entity_id
     * @return string
     */
    function refreshToken($old_token, $entity_type = NULL, $entity_id = NULL) {
	    // set timestamps
	    $current_timestamp = strtotime(date("Y-m-d H:i:s"));
	    $expiry_timestamp = $current_timestamp + $this->_token_expiry_time;

		// set data types
		$entity_type = $entity_type == "none" ? NULL : $entity_type;
		$entity_id = intval($entity_id) == 0 ? NULL : $entity_id;
		
		// request headers
		$request_headers = apache_request_headers();
		// defaults
		$new_token = FALSE;
		
		// old_token not null
		if($old_token !== FALSE) {
			// query record
			$query = $this->select($this->primaryKey);
			$query->where("token","=",trim($old_token));
			if($entity_type === NULL) {
				$query->whereNull("entity_type");
				$query->whereNull("entity_id");
			} else {
				$query->where("entity_type","=",$entity_type);
				$query->where("entity_id","=",$entity_id);
			}
			$query->whereNull("deleted_at");
			$raw_record = $query->get();
			$raw_id = isset($raw_record[0]->{$this->primaryKey}) ? $raw_record[0]->{$this->primaryKey} : 0;
			$data = $this->get($raw_id);
			// valid token record
			if($data !== FALSE) {
				
				// set new token
				$new_token = $this->generateCode($entity_type, $entity_id);
				// set data
				$data->token = $new_token;
				$data->request_headers = json_encode($request_headers);
				$data->updated_at = date("Y-m-d H:i:s", $current_timestamp);
				$data->expire_at = date("Y-m-d H:i:s", $expiry_timestamp);
				// set
				$this->set($data->{$this->primaryKey},(array)$data);
			}
			
		}
		
		return $new_token;
    }
	
}
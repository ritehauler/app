<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;
use Session;
use Validator;
use Image;
use Mail;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\EFEntityPlugin;
use App\Http\Models\Conf;
//use Twilio;

class CCUserController extends Controller {

    private $_assignData = array(
        'pDir' => '',
        'dir' => DIR_API
    );
    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
	private $_model_path = "\App\Http\Models\\";	
	private $_entity_identifier = "user";
	private $_entity_pk = "user_id";
	private $_entity_ucfirst = "CCUser";
	//private $_entity_id = "1";
	//private $_plugin_identifier = "ef_auth";
	private $_plugin_config = array();
	

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {
		// load entity model
		// get all webservices data
		$this->__models['entity_plugin_model'] = new EFEntityPlugin;		
		$this->_entity_model = $this->_model_path.$this->_entity_ucfirst;
		$this->_entity_model = new $this->_entity_model;
		
        // init models
        $this->__models['api_method_model'] = new ApiMethod;
        $this->__models['api_user_model'] = new ApiUser;
		$this->__models['conf_model'] = new Conf;
		$this->__models[$this->_entity_identifier.'_model'] = $this->_entity_model;

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

        // check access
        $this->__models['api_user_model']->checkAccess($request);
		// plugin config
		//$this->_plugin_config = $this->__models['entity_plugin_model']->getPluginSchema($this->_entity_id, $this->_plugin_identifier);
		// set defaults
		//$this->_plugin_config = isset($this->_plugin_config->webservices) ? $this->_plugin_config->webservices : array();
		//$this->_plugin_config["webservices"] = $this->_plugin_config;
		
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index() {
        
    }
	
	
	/**
     * User Custom Register
     *
     * @return Response
     */
    public function signup(Request $request) {
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
        // get params
        //$type = trim(strip_tags($request->input('platform_type', '')));
        //$type = in_array($type, array("facebook", "twitter", "gplus","custom")) ? $type : ""; // set default value
		
		//$device_type = trim(strip_tags($request->input('device_type', '')));
        //$device_type = in_array($device_type, array("android", "ios")) ? $device_type : ""; // set default value
        //$device_token = trim(strip_tags($request->input('device_token', '')));
        //$name = trim(strip_tags($request->input('name', '')));
		$first_name = trim(strip_tags($request->input('first_name', '')));
		$last_name = trim(strip_tags($request->input('last_name', '')));
        //$gender = trim(strip_tags($request->input('gender', '')));
		//$gender = in_array($gender, array("male", "female")) ? $gender : "male"; // set default value
		//$dob = trim(strip_tags($request->input('dob', '')));
		//$dob = $dob == "" ? "0000-00-00" : date("Y-m-d",strtotime($dob)); //set default value
		$email = trim(str_replace(" ","",strip_tags($request->input('email', ''))));
		/*$password = trim(str_replace(" ","",strip_tags($request->input('password', ''))));
		$mobile_no = trim(strip_tags($request->input('mobile_no', '')));
		$mobile_no = str_replace("+", "", $mobile_no);
		
		$country_code = trim(strip_tags($request->input('country_code', '')));
		$country_code = str_replace("+", "", $country_code);
		
		$image = trim(strip_tags($request->input('image_file', '')));
		$device_type = trim(strip_tags($request->input('device_type', '')));
		$device_type = in_array($gender, array("ios", "android")) ? $device_type : "ios"; // set default value*/
		//$thumb_url = trim(strip_tags($request->input('thumb_url', '')));
		//$location = trim(strip_tags($request->input('location', '')));		
        
        // validations
		$validator = Validator::make($request->all(), array(
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required|email',
			'password' => 'required|min:6',
			//'network' => 'required'
		));
		
        $valid_email = Validator::make(array('email' => $email), array('email' => 'email'));
        $row_type_exists = $this->__models[$this->_entity_identifier.'_model']
			->where('email', '=', $email)
			->whereNull("deleted_at")		
			->get(array("user_id"));
		
		$exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->user_id : 0;
		/*		
		// check mobile no. 
		$row_mobile_exists = $this->__models[$this->_entity_identifier.'_model']
			->where('country_code', '=', $country_code)
			->where('mobile_no', '=', $mobile_no)			
			->get(array("user_id"));
		
		$mobile_no_exists = isset($row_mobile_exists[0]) ? $row_mobile_exists[0]->user_id : 0;		
		// get user data
		$entity = $this->__models[$this->_entity_identifier.'_model']->get($exists_id);		
		*/
        // validations		
		/*if(!in_array("user/register", $this->_plugin_config["webservices"])){
			$this->_apiData['message'] = 'You are not authorized to access this service.';			
		} else if ($name == "") {
             $this->_apiData['message'] = 'Please provide User name';
        } else if ($email == "") {
             $this->_apiData['message'] = 'Please provide Email';
        } else if ($password == "") {
             $this->_apiData['message'] = 'Please provide Password';
        } else if ($country_code == "") {
             $this->_apiData['message'] = 'Please provide Country Code.';
        } else if ($mobile_no == "") {
             $this->_apiData['message'] = 'Please provide Mobile no.';
        } else if ($type == "") {
             $this->_apiData['message'] = 'Please provide Platform Type';
        } else if ($device_type == "") {
            $this->_apiData['message'] = 'Please provide Device Type';
        } else if ($exists_id) {
            $this->_apiData['message'] = 'Email address is already exists.';
		} else if ($mobile_no_exists) {
            $this->_apiData['message'] = 'Mobile no. is already exists.';
        }else if ($first_name == '') {
             $this->_apiData['message'] = 'Please enter First Name';
         } else if ($last_name == '') {
             $this->_apiData['message'] = 'Please enter Last Name';
         } else if ($entity === FALSE && $dob == "") {
             $this->_apiData['message'] = "Please provide Date of Birth";
         } else if ($email == "") {
             $this->_apiData['message'] = "Please enter Email";
         } else if ($valid_email->fails()) {
             $this->_apiData['message'] = 'Please enter valid Email';
         
		}*/
		if($validator->fails()) {
			 $this->_apiData['message'] = $validator->errors()->first();
		} else if($exists_id > 0) {
			 $this->_apiData['message'] = trans('api_errors.email_already_exists');
		} else {
			// success response
            $this->_apiData['response'] = "success";
			
            // init output data array
            $this->_apiData['data'] = $data = array();	
			
			// init/load model
			$this->__models['network_model'] = $this->_model_path."CCNetwork";
			$this->__models['network_model'] = new $this->__models['network_model'];		
			
							
			// set data
			$entity['first_name'] 	 = $request->input("first_name","");
			$entity['last_name']  	 = $request->input("last_name","");
			$entity['name']		  	 = $entity['first_name']." ".$entity['last_name'];
			$entity['email'] 	  	 = $request->input("email");
			$entity['password']   	 = $this->__models[$this->_entity_identifier.'_model']->saltPassword($request->input("password"));
			/*$entity['mobile_no']  	 = $mobile_no;
			$entity['country_code']  = $country_code;				
			$entity['gender'] 	  	 = $gender;
			$entity['dob'] 			 = $dob;
			$entity['platform_type'] = $type;
			$entity['device_type']   = $device_type;	
			$entity['is_mobile_verified'] = 1;*/
			$entity['status']   	 =  0;	
			
			$entity_id = $this->__models[$this->_entity_identifier.'_model']->signup($entity);			
			/*					
			// save user image
			// filesize
			$file_size = isset($_FILES["image_file"]["size"]) ? $_FILES["image_file"]["size"] : 0;	
				
			if ($file_size > 0) {
				//file type
				$file_type = $_FILES['image_file']['type'];
				$file_type = explode('/', $file_type);
				// content type
				$content_type = $file_type[0];           
				//file name
				$file_name = $_FILES['image_file']['name'];
				$file_name = 'dp-'.$entity_id.'.'.$request->image_file->getClientOriginalExtension();				
				//move file
				$destination_path = base_path() .'/'.config("pl_user.DIR_IMG"); // upload path		   
				move_uploaded_file($_FILES["image_file"]["tmp_name"],$destination_path.$file_name);
				
				// set thumb name
				$user_thumb = "thumb-" . $entity_id . ".jpg";
				
				// get
				$thumb_data = @file_get_contents(url() .'/'."thumb/user_img/".config("thumb.USER_THUMB_SIZE")."/" . $file_name);				
				// put
				@file_put_contents(getcwd() . "/" . config("pl_user.DIR_IMG") . $user_thumb, $thumb_data);	
				
				// set image and thumb
				$entity['image'] = $file_name;						
				$entity['thumb'] = $user_thumb;				
			}
			
			// create Open Fire User			
			$user_roster = $this->__models['conf_model']->_createUser((object) $entity);		
			*/
			
			
			// response data
            $data['user'] = $this->__models[$this->_entity_identifier.'_model']->getData($entity_id);					
            
			$this->_apiData['message'] = trans('api_errors.check_email_for_confirmation');
			
			// assign to output
			$this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	/**
     * CustomLogin
     *
     * @return Response
     */
    public function customLogin(Request $request) {
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
		// load model
		$api_token_model = $this->_model_path."ApiToken";
		$api_token_model = new $api_token_model;
		
		$conf_model = $this->_model_path."Conf";
		$conf_model = new $conf_model;
		
		// param validations
		$validator = Validator::make($request->all(), array(
			'email' => 'required',
			'password' => 'required',
			'platform_type' => 'required',
			'device_type' => 'required|in:android,ios'
		));
		
		// check is email
		if(preg_match('/@/', $request->email)){
			$raw_exists = $this->__models[$this->_entity_identifier.'_model']->check_login($request->email, $request->password);		
			$exists_id = isset($raw_exists) ? $raw_exists : 0;
		}		
		// or mobile no
		else{
			$raw_exists = $this->__models[$this->_entity_identifier.'_model']->check_mobile_login($request->email, $request->password);	
			$exists_id = isset($raw_exists) ? $raw_exists : 0;
		}		
		// get data
		$entity = $this->__models[$this->_entity_identifier.'_model']->get($exists_id);
		
        // validations		
		if(!in_array("user/custom_login", $this->_plugin_config["webservices"])){
			$this->_apiData['message'] = 'You are not authorized to access this service.';			
		} else if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif($exists_id == 0 ){
			$this->_apiData['message'] = 'Invalid login credentials';
		} elseif($exists_id != 0 && $entity->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($exists_id != 0 && $entity->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
			// success response
            $this->_apiData['response'] = "success";
			
            // init output data array
            $this->_apiData['data'] = $data = array();
			
            // update last login as current timestamp
            $entity->last_login_at = date('Y-m-d H:i:s');
			$entity->last_seen_at = date('Y-m-d H:i:s');			
			
			// device data
            if ($request->device_token != "" && $request->device_type != "") {
				$entity->device_type = $request->device_type;
                $entity->device_token = $request->device_token;
                // remove this device token from other user ids
                $this->__models[$this->_entity_identifier.'_model']->replaceToken($entity->{$this->_entity_pk}, $entity->device_token);
            }	
					
			// update user data
            $this->__models[$this->_entity_identifier.'_model']->set($entity->{$this->_entity_pk}, (array) $entity);   
					
			// create Open Fire User			
			$user_roster = $this->__models['conf_model']->_createUser((object) $entity);				
			         
            // response data
            $data['user_data'] = $this->__models[$this->_entity_identifier.'_model']->getData($entity->{$this->_entity_pk});
			
			// get OF Config
			$of_config = $conf_model->where("key", "=", "of_config")->get();
			$data['of_config'] = json_decode($of_config[0]->value);
			
			// generate token
			$data['token'] = $api_token_model->generate("user", $entity->{$this->_entity_pk});
						
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	/**
     * User Social Login
     *
     * @return Response
     */
    public function socialLogin(Request $request) {
		
		// load model
		$conf_model = $this->_model_path."Conf";
		$conf_model = new $conf_model;
		
        // get params
        $type = trim(strip_tags($request->input('platform_type', '')));
        $type = in_array($type, array("facebook", "twitter", "gplus")) ? $type : ""; // set default value
		$uid = trim(strip_tags($request->input('platform_id', 0)));
        $uid = $uid == "" ? 0 : $uid; // set default value
		$device_type = trim(strip_tags($request->input('device_type', '')));
        $device_type = in_array($device_type, array("android", "ios")) ? $device_type : ""; // set default value
        $device_token = trim(strip_tags($request->input('device_token', '')));
        $name = trim(strip_tags($request->input('name', '')));
		$first_name = trim(strip_tags($request->input('first_name', '')));
		$last_name = trim(strip_tags($request->input('last_name', '')));
        $gender = trim(strip_tags($request->input('gender', '')));
		$gender = in_array($gender, array("male", "female")) ? $gender : "male"; // set default value
		$dob = trim(strip_tags($request->input('dob', '')));
		$dob = $dob == "" ? "0000-00-00" : date("Y-m-d",strtotime($dob)); //set default value
		$email = trim(str_replace(" ","",strip_tags($request->input('email', ''))));
		$mobile_no = trim(strip_tags($request->input('mobile_no', '')));
		$mobile_no = str_replace("+", "", $mobile_no);
		
		$country_code = trim(strip_tags($request->input('country_code', '')));
		$country_code = str_replace("+", "", $country_code);
		
		$image = trim(strip_tags($request->input('image', '')));
		$thumb = trim(strip_tags($request->input('thumb', '')));
		//$location = trim(strip_tags($request->input('location', '')));
		
        
        // validations
        $valid_email = Validator::make(array('email' => $email), array('email' => 'email'));
        $row_type_exists = $this->__models[$this->_entity_identifier.'_model']
			->where('platform_type', '=', $type)
			->where('platform_id', '=', $uid)
			->get(array("user_id"));
		
		$exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->user_id : 0;
		// check mobile no. 
		$row_mobile_exists = $this->__models[$this->_entity_identifier.'_model']
			->where('country_code', '=', $country_code)	
			->where('mobile_no', '=', $mobile_no)						
			->get(array("user_id"));
		
		$mobile_no_exists = isset($row_mobile_exists[0]) ? $row_mobile_exists[0]->user_id : 0;	
		// get user data
		$entity = $this->__models[$this->_entity_identifier.'_model']->get($exists_id);		
		
       // validations		
		if(!in_array("user/social_login", $this->_plugin_config["webservices"])){
			$this->_apiData['message'] = 'You are not authorized to access this service.';			
		} else if ($name == '') {
            $this->_apiData['message'] = 'Please enter Name';
		} else if ($email == '') {
            $this->_apiData['message'] = 'Please enter Email';
		} /*else if ($mobile_no == '') {
            $this->_apiData['message'] = 'Please enter Mobile No.';
		} */else if ($type == "") {
             $this->_apiData['message'] = 'Please provide Platform Type';
        } else if ($uid == 0) {
            $this->_apiData['message'] = 'Please provide Platform ID';
        } else if ($device_type == "") {
            $this->_apiData['message'] = 'Please provide Device Type';        
  		} else if($entity !== FALSE && $entity->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($entity !== FALSE && $entity->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} /* else if ($mobile_no_exists) {
            $this->_apiData['message'] = 'Mobile no. is already exists.';
		}  */else {
            // init output data array
            $this->_apiData['data'] = $data = array();			

            // create account if not exists
            if ($entity === FALSE) {			
				if ($mobile_no == '') {
					$this->_apiData['error'] = 1; 
					$this->_apiData['message'] = 'Please enter Mobile No.';
					return $this->__ApiResponse($request, $this->_apiData);
				} else if ($mobile_no_exists) {
					$this->_apiData['error'] = 1; 
           			$this->_apiData['message'] = 'Mobile no. is already exists.';
					return $this->__ApiResponse($request, $this->_apiData);
				}else{
					// set data
					$entity['name'] = $name;
					$entity['first_name'] = $first_name;
					$entity['last_name'] = $last_name;
					$entity['gender'] = $gender;
					$entity['dob'] = $dob;
					$entity['email'] = $email;
					$entity['mobile_no'] = $mobile_no;
					$entity['country_code'] = $country_code;
					$entity['image'] = $image;
					$entity['status'] =  1;
					
					if(preg_match("/http/",$image)){
						$image_data = explode('width=', $image);
						$entity['thumb'] = $image_data[0].'width=200';
					}
					
					$entity['platform_type'] = $type;
					$entity['platform_id'] = $uid;	
					$entity['is_mobile_verified'] = 1;
					$entity['status']      = 0;
					//$entity['location'] = $location;
					
					$entity_id = $this->__models[$this->_entity_identifier.'_model']->registerUser($entity);	
					
					// unset user array
					unset($entity);
					// get user data
					$entity = $this->__models[$this->_entity_identifier.'_model']->get($entity_id);
					$entity->xmpp_user = $entity_id.'_user';
					$entity->xmpp_password = substr(uniqid(),1,8);
				}
            }

            // update last login as current timestamp
            $entity->name = $name;
			$entity->first_name = $first_name;
			$entity->last_name = $last_name;
			$entity->email = $email;
           // $entity->last_login_at = date('Y-m-d H:i:s');
			// device data
            if ($device_token != "" && $device_type != "") {
                $entity->device_type = $device_type;
                $entity->device_token = $device_token;
                // remove this device token from other user ids
                $this->__models[$this->_entity_identifier.'_model']->replaceToken($entity->{$this->_entity_pk}, $entity->device_token);
            }
			// if user came back, activate account
			if($entity->deleted_at !== NULL) {
				$entity->deleted_at = NULL;
				// set current location to true
				//$entity->use_current_location = 1;
			}

            // update user data
            $this->__models[$this->_entity_identifier.'_model']->set($entity->{$this->_entity_pk}, (array) $entity);
			
			$user_data = $this->__models[$this->_entity_identifier.'_model']->get($entity->{$this->_entity_pk});
			
			
			
			// create Open Fire User			
			$user_roster = $this->__models['conf_model']->_createUser((object) $entity);		
			
			
			            
            // response data
            $data['user_data'] = $this->__models[$this->_entity_identifier.'_model']->getData($entity->{$this->_entity_pk});
			
			// get OF Config
			$of_config = $conf_model->where("key", "=", "of_config")->get();
			$data['of_config'] = json_decode($of_config[0]->value);
			
			// success response
            $this->_apiData['response'] = "success";
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	/**
     * is_social_registered
     *
     * @return Response
     */
    public function isSocialRegistered(Request $request) {
        // get params
        $type = trim(strip_tags($request->input('platform_type', '')));
        $type = in_array($type, array("facebook", "twitter", "gplus")) ? $type : ""; // set default value
        $uid = trim(strip_tags($request->input('platform_id', '')));
        $uid = $uid == "" ? '' : $uid; // set default value
        $device_type = trim(strip_tags($request->input('device_type', '')));
        $device_type = in_array($device_type, array("android", "ios")) ? $device_type : ""; // set default value
        $device_token = trim(strip_tags($request->input('device_token', '')));

        $row_type_exists = $this->__models[$this->_entity_identifier.'_model']
                ->where('platform_type', '=', $type)
                ->where('platform_id', '=', $uid)
				->whereNull('deleted_at')
                ->get(array("user_id")
        );
		
        $entity_id = isset($row_type_exists[0]) ? $row_type_exists[0]->user_id : 0;

        // get user data
        $entity = $this->__models[$this->_entity_identifier.'_model']->get($entity_id);
		// validations		
		if(!in_array("user/is_social_registered", $this->_plugin_config["webservices"])){
			$this->_apiData['message'] = 'You are not authorized to access this service.';			
		} else if ($type == "") {
            $this->_apiData['message'] = 'Please provide Social Login Type';
        } else if ($uid == "") {
            $this->_apiData['message'] = 'Please provide UID';
        } else if ($device_type == "") {
            $this->_apiData['message'] = 'Please provide Device Type';
        } else if ($entity === FALSE) {
            $this->_apiData['message'] = 'User does not exist';
        } elseif($entity !== FALSE && $entity->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($entity !== FALSE && $entity->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
            // success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();

            // update last login as current timestamp
            $entity->last_login_at = date('Y-m-d H:i:s');
			
            if ($device_token != "" && $device_type != "") {
                $entity->device_type = $device_type;
                $entity->device_token = $device_token;
            }
            $this->__models[$this->_entity_identifier.'_model']->set($entity->{$this->_entity_pk}, (array) $entity);

            $data['user_data'] = $this->__models[$this->_entity_identifier.'_model']->getData($entity->{$this->_entity_pk});
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	/**
     * User data
     *
     * @return Response
     */
    public function get(Request $request) {
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
        // get params
        $entity_id = intval($request->{$this->_entity_pk});
        $device_type = $request->device_type;
        $device_type = in_array($device_type, array("android", "ios")) ? $device_type : "android"; // set default value
        $device_token = $request->device_token;

        // get data
        $entity = $this->__models[$this->_entity_identifier.'_model']->get($entity_id);
		// validations		
		if(!in_array("user/get", $this->_plugin_config["webservices"])){
			$this->_apiData['message'] = 'You are not authorized to access this service.';			
		} else if ($entity_id == 0) {
            $this->_apiData['message'] = 'Please enter '.$this->_entity_ucfirst.' ID';
        } else if ($entity === FALSE) {
            $this->_apiData['message'] = $this->_entity_ucfirst.' does not exist';
        } elseif($entity->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($entity->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";
			
			// init output data array
            $this->_apiData['data'] = $data = array();

			//check device token
            if ($device_token != "" && $device_type != "") {
                $entity->device_type = $device_type;
                $entity->device_token = $device_token;
            }			
			
			$entity->last_seen_at = date('Y-m-d H:i:s');
			
			// update user data
            $this->__models[$this->_entity_identifier.'_model']->set($entity->{$this->_entity_pk}, (array) $entity);
			
            // get user data
            $data['user_data'] = $this->__models[$this->_entity_identifier.'_model']->getData($entity->{$this->_entity_pk});
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	/**
     * Verify Twilio Account 
     *
     * @return Response
     */
    public function generateSms(Request $request) {
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
				
        // get params        
        $country_code = str_replace("+", "", $request->country_code);
		$mobile_no = str_replace("+", "", $request->mobile_no);
		$email = $request->email;
		
		// check email
		$email_exist = $this->__models[$this->_entity_identifier.'_model']->where('email', '=', $email)->get(array("user_id"));
		
		// check mobile no.
		$mobile_exist = $this->__models[$this->_entity_identifier.'_model']->where('country_code', '=', $country_code)->where('mobile_no', '=', $mobile_no)->get(array("user_id"));		
		        
		// validations		
		if ($country_code == '') {
            $this->_apiData['message'] = 'Please enter Country code.';
        } if ($mobile_no == '') {
            $this->_apiData['message'] = 'Please enter Mobile no.';
        } else if ($email == '') {
            $this->_apiData['message'] = 'Please enter Email.';
        } else if (count($email_exist) > 0) {
            $this->_apiData['message'] = 'Email is already exists.';
        } else if (count($mobile_exist) > 0) {
            $this->_apiData['message'] = 'Mobile no. is already exists.';
        } else {            
			// init output data array
            $this->_apiData['data'] = $data = array();	
						
			// TWILIO CODE			
			$verification_code = rand(500,10000);		
			/*try{
				$response = \Twilio::message("+".$country_code.$mobile_no, "Your verification code $verification_code");							
			}catch(\Services_Twilio_RestException $e){					
				$this->_apiData['message'] =  $e->getMessage(); 
				return $this->__ApiResponse($request, $this->_apiData);			
			}*/
			// TWILIO CODE
							
            $data['code'] = $verification_code;			
			// success response
            $this->_apiData['response'] = "success";
            // assign to output
            $this->_apiData['data'] = $data;
        }
        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	/**
     * Update Profile
     *
     * @return Response
     */
    public function updateProfile(Request $request) {		
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
        // get params
        $entity_id = intval($request->{$this->_entity_pk});
		$name = $request->name;
		$dob = $request->dob;
		$gender = $request->gender;
        $gender = in_array($gender, array("male", "female")) ? $gender : "male"; // set default value
        $device_type = $request->device_type;
        $device_type = in_array($device_type, array("android", "ios")) ? $device_type : "android"; // set default value
        $device_token = $request->device_token;
		
        // get data
        $entity = $this->__models[$this->_entity_identifier.'_model']->get($entity_id);	
		
        // validations
		if ($entity_id == "") {
            $this->_apiData['message'] = 'Please enter User ID';
        } else if ($entity === FALSE) {
            $this->_apiData['message'] = 'Invalid user request';
        } elseif($entity !== FALSE && $entity->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($entity !== FALSE && $entity->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {			
				// success response
				$this->_apiData['response'] = "success";
				// init output data array
				$this->_apiData['data'] = $data = array();
				// update last login as current timestamp
				
				// filesize
				$file_size = isset($_FILES["image_file"]["size"]) ? $_FILES["image_file"]["size"] : 0;	
					
				if ($file_size > 0) {
					//file type
					$file_type = $_FILES['image_file']['type'];
					$file_type = explode('/', $file_type);
					// content type
					$content_type = $file_type[0];           
					//file name
					$file_name = $_FILES['image_file']['name'];
					$file_name = 'dp-'.$entity_id.'-'.time().'.'.$request->image_file->getClientOriginalExtension();				
					//move file
					$destination_path = base_path() .'/'.config("pl_user.DIR_IMG"); // upload path		   
					move_uploaded_file($_FILES["image_file"]["tmp_name"],$destination_path.$file_name);
					
					// set thumb name
					$user_thumb = "thumb-" . $entity_id . '-' .time(). ".jpg";
					
					// get
					$thumb_data = @file_get_contents(url() .'/'."thumb/user_img/".config("thumb.USER_THUMB_SIZE")."/" . $file_name);				
					// put
                    @file_put_contents(getcwd() . "/" . config("pl_user.DIR_IMG") . $user_thumb, $thumb_data);	
					
					// set image and thumb
					$entity->image = $file_name;						
					$entity->thumb = $user_thumb;	
					
      		    }
			
			// COVER 
			// filesize
			$file_size = isset($_FILES["cover_file"]["size"]) ? $_FILES["cover_file"]["size"] : 0;	
				
       	    if ($file_size > 0) {
				//file type
				$file_type = $_FILES['cover_file']['type'];
				$file_type = explode('/', $file_type);
				// content type
				$content_type = $file_type[0];           
				//file name
				$file_name = $_FILES['cover_file']['name'];
				$file_name = 'cover-'.$entity_id.'-'.time().'.'.$request->cover_file->getClientOriginalExtension();				
				//move file
				$destination_path = base_path() .'/'.config("pl_user.DIR_IMG"); // upload path		   
				move_uploaded_file($_FILES["cover_file"]["tmp_name"],$destination_path.$file_name);				
				// set cover
				$entity->cover = $file_name;
      		}
			
			
			$entity->name = isset($name)? $name : $entity->name;
			$entity->dob = isset($dob)? $dob : $entity->dob;
			$entity->gender = isset($gender)? $gender : $entity->gender;
			$entity->device_type = isset($device_type)? $device_type : $entity->device_type;			
            $entity->updated_at = date('Y-m-d H:i:s');
			$entity->last_seen_at = date('Y-m-d H:i:s');
			// update user data
            $this->__models['user_model']->set($entity->user_id, (array) $entity);			
			
			// ACTIVITY
			$entity_user_history_model = $this->_model_path."UserHistory";
			$entity_user_history_model = new $entity_user_history_model;
			// put history : edit_user
			$reference_data = array(
					"reference_module" => "user",
					"reference_id" => $entity->user_id,
					"against" => "",
					"against_id" => "",
					"tracking_id" => "",
			);
			$user_history_id = $entity_user_history_model->putUserHistory($request->user_id,"edit_user",$reference_data);
					
	         // response data
			$data['user_data'] = $this->__models[$this->_entity_identifier.'_model']->getData($entity->{$this->_entity_pk});			
			// message
			$this->_apiData['message'] = "Successfully updated profile";
            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	/**
     * Get User Activity
     *
     * @return Response
     */
    public function notification(Request $request){	
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
        // get params
        $entity_id = intval($request->{$this->_entity_pk});
        // get data
        $entity = $this->__models[$this->_entity_identifier.'_model']->get($entity_id);
		// validations		
		if(!in_array("user/get", $this->_plugin_config["webservices"])){
			$this->_apiData['message'] = 'You are not authorized to access this service.';			
		} else if ($entity_id == 0) {
            $this->_apiData['message'] = 'Please enter '.$this->_entity_ucfirst.' ID';
        } else if ($entity === FALSE) {
            $this->_apiData['message'] = $this->_entity_ucfirst.' does not exist';
        } elseif($entity->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($entity->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
			// load model
			$entity_user_notification_model = $this->_model_path."UserNotification";
			$entity_user_notification_model = new $entity_user_notification_model;
			
            // success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			$this->_apiData['message'] = "Success";
			          
            // get user activity				
			$page_no = ((int)$request->page_no)? (int)$request->page_no: '1' ;
			
			// get counter unread notification
			$counter_unread = $entity_user_notification_model->where("target_user_id", "=", $entity->{$this->_entity_pk})->where("is_unread", "=", 1)->count();	
			
			$query = $entity_user_notification_model->selectRaw("un.user_notification_id");	
			$query->from("user_notification AS un");
			$query->join("user AS u1", "u1.user_id", '=', "un.target_user_id");
			$query->join("user AS u2", "u2.user_id", '=', "un.user_id");
			$query->where("un.target_user_id", "=", $entity->{$this->_entity_pk});
			$query->where("u1.status", "=", "1");
			$query->whereNull("un.deleted_at");
			$notification_item = $query->get();			
			$total_records = $query->count();
			
			$total_pages = ceil($total_records / 20);
			$page_no = $page_no >= $total_pages ? $total_pages : $page_no;
			$page_no = $page_no <= 1 ? 1 : $page_no;
			$offset = 20 * ($page_no - 1);			
			
			$notification_item = array();			
			// get post_comment
			$query = $entity_user_notification_model->from("user_notification AS un");
			$query->selectRaw("un.user_notification_id, un.user_id, un.target_user_id, u1.name AS receiver_name, u1.image AS receiver_image, u1.thumb AS recevier_thumb, u2.name AS sender_name, u2.image AS sender_image, u2.thumb AS sender_thumb, entity, entity_id, un.is_unread, un.created_at");
			$query->join("user AS u1", "u1.user_id", '=', "un.target_user_id");
			$query->join("user AS u2", "u2.user_id", '=', "un.user_id");
			$query->where("un.target_user_id", "=", $entity->{$this->_entity_pk});
			$query->where("u1.status", "=", "1");
			$query->whereNull("un.deleted_at");
			$notification_item = $query->get();			
			$query->take(20);
			$query->skip($offset);
			$query->orderBy("un.user_notification_id", "DESC");
			$notification_item = $query->get();
			
			$notification_type = \config("constants.NOTIFICATION_TYPES");
			
			if(isset($notification_item[0])){
				 foreach($notification_item as $key => $notification){	 	
				 	// set message
					$notification_item[$key]->message = $notification->sender_name . $notification_type[$notification->entity];
				 	// set image
					if(preg_match('/^dp/',$notification->receiver_image)){
						$notification_item[$key]->receiver_image =  "public/files/user/".$notification->receiver_image;
						
						if(!empty($notification->receiver_thumb))
						$notification_item[$key]->receiver_thumb =  "thumb/user/".\config("thumb.USER_THUMB_SIZE")."/" . $notification->receiver_thumb;
					}
					// set image
					if(preg_match('/^dp/',$notification->sender_image)){
						$notification_item[$key]->sender_image =  "public/files/user/".$notification->sender_image;
						
						if(!empty($notification->sender_thumb))
						$notification_item[$key]->sender_thumb = "thumb/user/".\config("thumb.USER_THUMB_SIZE")."/" . $notification->sender_thumb;
					}					
				 }
			}
			// set data
			$data['counter_unread']= $counter_unread;
			$data['notification'] = $notification_item;
			
			// set pagination response
			$data["page"] = array(
				"current" => $page_no,
				"total" => $total_pages,
				"next" => $page_no >= $total_pages ? 0 : $page_no + 1,
				"prev" => $page_no <= 1 ? 0 : $page_no - 1
			);
			
			// update last_seen_at
			$save_user['last_seen_at'] = date('Y-m-d H:i:s');
			$this->__models[$this->_entity_identifier.'_model']->set($entity_id, $save_user);
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    
	}
	
	
	/**
     * User data
     *
     * @return Response
     */
    public function mark_read_notification(Request $request) {
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
        // get params
        $entity_id = intval($request->{$this->_entity_pk});
        $user_notification_id = $request->user_notification_id;

        // get data
        $entity = $this->__models[$this->_entity_identifier.'_model']->get($entity_id);
		// validations		
		if(!in_array("user/get", $this->_plugin_config["webservices"])){
			$this->_apiData['message'] = 'You are not authorized to access this service.';			
		} else if ($entity_id == 0) {
            $this->_apiData['message'] = 'Please enter '.$this->_entity_ucfirst.' ID';
        } else if ($user_notification_id == '') {
            $this->_apiData['message'] = 'Please enter User Notification ID';
        } else if ($entity === FALSE) {
            $this->_apiData['message'] = $this->_entity_ucfirst.' does not exist';
        } elseif($entity->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($entity->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			$this->_apiData['message'] = "Success";
			          
			// load model
			$entity_user_notification_model = $this->_model_path."UserNotification";
			$entity_user_notification_model = new $entity_user_notification_model;
					  
            // update user data
			$save['is_unread'] = 0;
            $entity_user_notification_model->set($user_notification_id, (array) $save);
			
			
			// update last_seen_at
			$save_user['last_seen_at'] = date('Y-m-d H:i:s');
			$this->__models[$this->_entity_identifier.'_model']->set($entity_id, $save_user);
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }

	
	
	
	/**
     * Support Form
     *
     * @return Response
     */
    public function support(Request $request){	
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
        // get params
        $entity_id = intval($request->{$this->_entity_pk});
        // get data
        $entity = $this->__models[$this->_entity_identifier.'_model']->get($entity_id);
		// validations		
		if ($entity_id == 0) {
            $this->_apiData['message'] = 'Please enter '.$this->_entity_ucfirst.' ID';
        } else if ($request->name == '') {
            $this->_apiData['message'] = 'Please enter Name';
        } else if ($request->email == '') {
            $this->_apiData['message'] = 'Please enter Email';
        } else if ($request->subject == '') {
            $this->_apiData['message'] = 'Please enter Subject';
        } else if ($request->description == '') {
            $this->_apiData['message'] = 'Please enter Description';
        } else if ($entity === FALSE) {
            $this->_apiData['message'] = $this->_entity_ucfirst.' does not exist';
        } elseif($entity->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($entity->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
            // success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			$this->_apiData['message'] = "Success";
			
			// support				

			$headers  = "From: $request->name <$request->email>" . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";		 
			$headers .= "Content-type: text/html\r\n";
			$body     = "Name: $request->name<br/><br/>Email: $request->email<br/><br/>Subject: $request->subject<br/><br/>Description: $request->description<br/><br/>";
			 // test email	
			 mail('mehwish@cubixlabs.com',$request->subject, $body, $headers);				
			}
        return $this->__ApiResponse($request, $this->_apiData);
	}
	
	
	
	/**
     * Logout
     *
     * @return Response
     */
    public function logout() {
        // get params
        $entity_id = intval(trim(strip_tags($request->input($this->_entity_id, 0))));

        // get data
        $entity = $this->__models[$this->_entity_identifier.'_model']->get($entity_id);

        if ($entity === FALSE) {
            $this->_apiData['message'] = "Invalid account request";
        } else {

            // success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();

            // set data
            $entity->device_token = "";
			$entity->last_seen_at = date('Y-m-d H:i:s');

            // update user data
            $this->__models[$this->_entity_identifier.'_model']->set($entity->{$this->_entity_pk}, (array) $entity);

            // set message
            $this->_apiData['message'] = "Token successfully cleared";
            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	/**
     * Upgrade
     *
     * @return Response
     */
    public function upgrade(Request $request) {
		// load model
		$package_model = $this->_model_path."Package";
		$package_model = new $package_model;
		
		$user_package_model = $this->_model_path."UserPackage";
		$user_package_model = new $user_package_model;
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));		
        // get params
        $entity_id = intval($request->{$this->_entity_pk});
		
        // get data
        $entity = $this->__models[$this->_entity_identifier.'_model']->get($entity_id);
		
		// get package
		$package = $package_model->get($request->package_id);
		
		// get user package
		$user_package = $user_package_model->where("package_id", "=", $request->package_id)->where("user_id","=", $request->user_id)->first();
		
		$package_id = (isset($user_package->package_id))? $user_package->package_id: '';
				// validations		
		if(!in_array("user/get", $this->_plugin_config["webservices"])){
			$this->_apiData['message'] = 'You are not authorized to access this service.';			
		} else if ($entity_id == 0) {
            $this->_apiData['message'] = 'Please enter '.$this->_entity_ucfirst.' ID';
        } else if ($entity === FALSE) {
            $this->_apiData['message'] = $this->_entity_ucfirst.' does not exist';
        } elseif($entity->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($entity->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else if ($package === FALSE) {
            $this->_apiData['message'] = 'Invalid package request';
        } else if ($package->package_id == 1) {
            $this->_apiData['message'] = 'The free package is by default for every user';
        } else if ($package_id == $package->package_id) {
            $this->_apiData['message'] = 'You are already subscribed to this package';
        } else {
			// success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();
			
			// insert
			$user_package_model->putUserPackage($request->user_id, $package->{"key"});
			
			
			// update last_seen_at
			$save_user['last_seen_at'] = date('Y-m-d H:i:s');
			$this->__models[$this->_entity_identifier.'_model']->set($entity_id, $save_user);
			
			
            // response data
            $data['user'] = $this->__models[$this->_entity_identifier.'_model']->getUserData($request->user_id);
			
			// message
			$this->_apiData['message'] = "User successfully upgraded.";

            // assign to output
            $this->_apiData['data'] = $data;
		}	

        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	
	
	/**
     * Update Notification Settings
     *
     * @return Response
     */
    public function updateNotificationSettings(Request $request) {
		// init models
		//$this->__models["preference_model"] = new Preference;
		
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));		
        // get params
        $entity_id = intval($request->{$this->_entity_pk});
        // get data
        $entity = $this->__models[$this->_entity_identifier.'_model']->get($entity_id);
		
        if ($entity_id == 0) {
            $this->_apiData['message'] = 'Please enter '.$this->_entity_ucfirst.' ID';
        } else if ($entity === FALSE) {
            $this->_apiData['message'] = $this->_entity_ucfirst.' does not exist';
        } elseif($entity->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($entity->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else if ($request->keys == "") {
            $this->_apiData['message'] = "Please provide keys";
        } else if ($request->switches == "") {
            $this->_apiData['message'] = "Please provide switches values";
        } else {
			// init models
			$preference_model = $this->_model_path."Preference";
			$preference_model = new $preference_model;
			
			$user_preference_model = $this->_model_path."UserPreference";
			$user_preference_model = new $user_preference_model;

            // success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			$keys_data = explode(",",$request->keys);
			$switches_data = explode(",",$request->switches);
			
			if(isset($keys_data[0])) {
				$i=0; // itrator for other data
				foreach($keys_data as $key) {
					$key_data = $preference_model->getBy("key",$key);
					// if valid key
					if($key_data !== FALSE) {
						// get user preference record
						$raw_existance = $user_preference_model
							->where('preference_id', '=', $key_data->preference_id)
							->where('user_id', '=', $request->user_id)
							->get(array("user_preference_id"));
						$record_id = isset($raw_existance[0]) ? $raw_existance[0]->user_preference_id : 0;
						$record = $user_preference_model->get($record_id);
						// if record exists
						if($record !== FALSE) {
							$key_value = isset($switches_data[$i]) ? intval($switches_data[$i]) : 1;
							$record->value = $key_value;
							// update
							$user_preference_model->set($record_id, (array)$record);
						}
					}
					// increment itrator
					$i++;
				}
			}
			
			// output data
			$data["user"] = $this->__models[$this->_entity_identifier.'_model']->getUserData($request->user_id);
			
            // set message
            $this->_apiData['message'] = "Settings successfully updated";
            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	 /**
     * Forgot Password
     * @param string email	 
     * @return Response
     */
    public function forgotPassword(Request $request) {
		
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));	
			
        // check is email
		if(preg_match('/@/', $request->email)){
			$raw_exists = $this->__models[$this->_entity_identifier.'_model']
										->where("email", "=", $request->email)
										->where("platform_type", "=", "custom")->first();		
			$exists_id = isset($raw_exists) ? $raw_exists->user_id : 0;
		}		
		// or mobile no
		else{
			$raw_exists = $this->__models[$this->_entity_identifier.'_model']
										->where("mobile_no", "=", $request->email)
										->where("platform_type", "=", "custom")->first();	
			$exists_id = isset($raw_exists) ? $raw_exists->user_id : 0;
		}		
		// get data
		$entity = $this->__models[$this->_entity_identifier.'_model']->get($exists_id);
		
        if ($request->email == '') {
            $this->_apiData['message'] = 'Please enter email or mobile no.';
        } else if ($entity === FALSE) {
            $this->_apiData['message'] = 'Email or mobile no. is invalid';
        } elseif($entity->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($entity->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
			
			if(preg_match('/@/', $request->email)){
				$this->__models[$this->_entity_identifier.'_model']->forgotPassword($entity);
				// response message
           		$this->_apiData['message'] = "Please check your email for password retrieval instructions";
			}		
			// or mobile no
			else{
				// $this->sendTwilioMessage($entity);
				// response message
          		$this->_apiData['message'] = "Please check your mobile sms for password retrieval instructions";
			}
            // success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();
            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	/**
     * Create Roster
     *
     * @return Response
     */
    public function createRoster(Request $request) {
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
        // get data
        $user = $this->__models[$this->_entity_identifier.'_model']->getData($request->user_id);
		$target_user = $this->__models[$this->_entity_identifier.'_model']->getData($request->target_user_id);		
		
		// validations		
		if(!in_array("user/get", $this->_plugin_config["webservices"])){
			$this->_apiData['message'] = 'You are not authorized to access this service.';			
		} else if ($request->user_id == 0) {
            $this->_apiData['message'] = 'Please enter '.$this->_entity_ucfirst.' ID';
        } else if ($user === FALSE) {
            $this->_apiData['message'] = $this->_entity_ucfirst.' does not exist';
        } else if ($target_user === FALSE) {
            $this->_apiData['message'] = 'Target User does not exist';
        } elseif($user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
              // success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			// create Roster for user				
			$user_roster = $this->__models['conf_model']->_createRoster($user, $target_user);	
			  
			// create Roster for target user				
			$target_roster = $this->__models['conf_model']->_createRoster($target_user, $user);	

			// update last_seen_at
			$save_user['last_seen_at'] = date('Y-m-d H:i:s');
			$this->__models[$this->_entity_identifier.'_model']->set($request->user_id, $save_user);
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	
	 /**
     * Twilio send Message - forgot password
     * @param string email-mobile no	 
     * @return Response
     */
	public function sendTwilioMessage($data){
		// get code		
		$password = str_random(config('pl_user.GENERATE_PASS')); 
		
		// TWILIO CODE				
		try{
			$response = \Twilio::message("+".$data->mobile_no, "Social Flock: Your password has been reset and new password is $password");		
			// update
			$this->__models[$this->_entity_identifier.'_model']->setPassword($data->user_id, $password);	
							
		}catch(\Services_Twilio_RestException $e){					
			$this->_apiData['message'] =  $e->getMessage(); 
			return $this->__ApiResponse($request, $this->_apiData);			
		}
		// TWILIO CODE	
	
	}
	
	
	/**
     * Profile
     *
     * @return Response
     */
    public function profile() {
        // get params
        $entity_id = trim(strip_tags($request->input($this->_entity_id, 0)));
        $entity_id = $entity_id == "" ? 0 : $entity_id; // set default value
		$target_user_id = intval(trim(strip_tags($request->input('target_user_id', 0))));
        $device_type = trim(strip_tags($request->input('device_type', '')));
        $device_type = in_array($device_type, array("android", "ios")) ? $device_type : "android"; // set default value
        $device_token = trim(strip_tags($request->input('device_token', '')));

        // get data
        $entity = $this->__models[$this->_entity_identifier.'_model']->get($entity_id);
		$target_user = $this->__models[$this->_entity_identifier.'_model']->get($target_user_id);
		$profile = $this->__models[$this->_entity_identifier.'_model']->getData($target_user->user_id,$entity->{$this->_entity_pk});
		

        if ($entity_id == 0) {
            $this->_apiData['message'] = 'Please enter User ID';
        } else if ($entity === FALSE) {
            $this->_apiData['message'] = 'Invalid user request';
        } else if ($target_user === FALSE) {
            $this->_apiData['message'] = 'Invalid profile user request';
        } else if ($target_user->deleted_at !== NULL) {
            $this->_apiData['message'] = 'Target profile removed';
        } elseif($entity->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($entity->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else if ($profile->is_user_blocked > 0) {
            $this->_apiData['message'] = 'Sorry ! You cannot access this profile';
        } else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();

            // get user data
            //$data['profile'] = $this->__models[$this->_entity_identifier.'_model']->getData($target_user->user_id,$entity->{$this->_entity_pk});
			$data['profile'] = $profile;
			
			// message
			$this->_apiData['message'] = "Success";
			
            // assign to output
            $this->_apiData['data'] = $data;
        }
        return $this->__ApiResponse($request, $this->_apiData);
    }

	
	/**
     * Delete user
     *
     * @return Response
     */
    public function delete() {
        // get params
		$entity_id = intval(trim(strip_tags($request->input($this->_entity_id, 0))));
		
		// get user data
		$entity = $this->__models[$this->_entity_identifier.'_model']->get($entity_id);
		
		// validations
        if ($entity === FALSE) {
            $this->_apiData['message'] = 'Invalid user request';
        } elseif($entity !== FALSE && $entity->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($entity !== FALSE && $entity->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
			// init models
			$this->__models['user_social_friend_model'] = new UserSocialFriend;
			
			
			// success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();
			
			// put deleted date
			//$entity->deleted_at = date("Y-m-d H:i:s");
			//$this->__models[$this->_entity_identifier.'_model']->set($entity->{$this->_entity_pk},(array)$entity);
			// remove user account and related tasks
			$this->__models[$this->_entity_identifier.'_model']->remove($entity->{$this->_entity_pk});

            // response data
            $data['user'] = $this->__models[$this->_entity_identifier.'_model']->getData($entity->{$this->_entity_pk});
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }	

}
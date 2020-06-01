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
use App\Http\Models\CCUser;

class CCNetworkController extends Controller {

    private $_assignData = array(
        'pDir' => '',
        'dir' => DIR_API
    );
    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
	private $_model_path = "\App\Http\Models\\";	
	private $_entity_identifier = "network";
	private $_entity_pk = "network_id";
	private $_entity_ucfirst = "CCNetwork";
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
     * Types
     *
     * @return Response
     */
    public function types(Request $request) {
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
		$allowed_order = array("title","created_at","updated_at");
		$allowed_sorting = array("ASC","DESC");
		
		// validate
		$request->order = in_array($request->order,$allowed_order) ? $request->order : "title";
		$request->sorting = in_array(strtoupper($request->sorting),$allowed_sorting) ? strtoupper($request->sorting) : "ASC";
		
		// init/load model
		$this->__models['network_type_model'] = $this->_model_path."CCNetworkType";
		$this->__models['network_type_model'] = new $this->__models['network_type_model'];
		
		// success response
		$this->_apiData['response'] = "success";
		
		// init output data array
		$this->_apiData['data'] = $data = array();
		
		
		// fetch ids
		$query  = $this->__models['network_type_model']->select("network_type_id");
		$query->whereNull("deleted_at");
		$query->orderBy($request->order,$request->sorting);
		$raw_ids = $query->get();
		
		// fetch records
		if(isset($raw_ids[0])) {
			foreach($raw_ids as $raw_id) {
				$record = $this->__models['network_type_model']->getData($raw_id->network_type_id);				
				$data[] = $record;
			}
		}
		
		// assign to output
		$this->_apiData['data'] = $data;

        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	
	/**
     * add
     *
     * @return Response
     */
    public function add(Request $request) {
		// load models
		$this->__models['network_type_model'] = $this->_model_path."CCNetworkType";
		$this->__models['network_type_model'] = new $this->__models['network_type_model'];
		$this->__models['user_model'] = new CCUser;
		
		// load configurations
		$cc_conf = $this->__models['conf_model']->getBy("key","cc");
		$cc_conf_value = json_decode($cc_conf->value);
		
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));	
        
        // validations
		$validator = Validator::make($request->all(), array(
			'user_id' => 'required',
			'title' => 'required',
			'network_type_id' => "required",
			'network_email' => 'required|min:6|max:30'
		));
		
		// user
		$user = $this->__models['user_model']->get($request->user_id);
		// network_email
		$network_email = $request->network_email.$cc_conf_value->network_email_domain;
		// network type exists ?
		$network_type_exists = $this->__models["network_type_model"]
			->where("network_type_id","=",$request->network_type_id)
			->whereNull("deleted_at")
			->count();
		// network email exists ?
        $email_exists = $this->__models["network_model"]
			->where('network_email', '=', $network_email)
			->whereNull("deleted_at")		
			->count();
		
		
		if($validator->fails()) {
			 $this->_apiData['message'] = $validator->errors()->first();
		} elseif($user === FALSE){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = trans('api_errors.invalid_user_account');
		} elseif($user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = trans('api_errors.account_is_inactive');
		} elseif($user->deleted_at !== NULL || $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = trans('api_errors.user_deleted_or_baned');
		} else if($network_type_exists == 0) {
			 $this->_apiData['message'] = trans('cc_errors.invalid_network_type');
		} else if($email_exists > 0) {
			 $this->_apiData['message'] = trans('cc_errors.network_email_already_exists');
		} else {
			// success response
            $this->_apiData['response'] = "success";
			// no message
			$this->_apiData['message'] = "";
            // init output data array
            $this->_apiData['data'] = $data = array();
			
			// save
			$save["user_id"] = $request->user_id;
			$save["network_type_id"] = $request->network_type_id;
			$save["title"] = $request->title;
			$save["network_email"] = $network_email;
			$save["created_at"] = date("Y-m-d H:i:s");
			$insert_id = $this->_entity_model->put($save);
			// unset
			unset($save);
			
			
			// response data
            $data['network'] = $this->_entity_model->getData($insert_id);			
            $data['user'] = $this->__models["user_model"]->getData($user->user_id);
			
			
			// assign to output
			$this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	
	/**
     * update
     *
     * @return Response
     */
    public function update(Request $request) {
		// load models
		//$this->__models['network_type_model'] = $this->_model_path."CCNetworkType";
		//$this->__models['network_type_model'] = new $this->__models['network_type_model'];
		$this->__models['user_model'] = new CCUser;
		
		// load configurations
		$cc_conf = $this->__models['conf_model']->getBy("key","cc");
		$cc_conf_value = json_decode($cc_conf->value);
		
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));	
		
		// file index
		$file_index = "logo_image";
        
        // validations
		$validator = Validator::make($request->all(), array(
			$this->_entity_pk => 'required|unique:'.$this->_entity_model->table.','.$this->_entity_pk.','.$request->{$this->_entity_pk}.','.$this->_entity_pk,
			'user_id' => 'required',
			'title' => 'required',
			//'network_type_id' => "required",
		));
		
		// user
		$user = $this->__models['user_model']->get($request->user_id);
		
		// network type exists ?
		/*$network_type_exists = $this->__models["network_type_model"]
			->where("network_type_id","=",$request->network_type_id)
			->whereNull("deleted_at")
			->count();*/
		// record exists?
		$record_exists = $this->_entity_model
			->where($this->_entity_pk,"=",$request->{$this->_entity_pk})
			->whereNull("deleted_at")
			->count();
		
		// validations
		if($validator->fails()) {
			 $this->_apiData['message'] = $validator->errors()->first();
		} elseif($user === FALSE){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = trans('api_errors.invalid_user_account');
		} elseif($user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = trans('api_errors.account_is_inactive');
		} elseif($user->deleted_at !== NULL || $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = trans('api_errors.user_deleted_or_baned');
		} /*else if($network_type_exists == 0) {
			 $this->_apiData['message'] = trans('cc_errors.invalid_network_type');
		}*/ else if($record_exists == 0) {
			 $this->_apiData['message'] = trans('cc_errors.invalid_record');
		} else if($request->hasFile($file_index) === TRUE && !in_array($request->file($file_index)->getClientOriginalExtension(),explode(",",config('cc_constants.EXT_NETWORK_LOGO')))) {
			 $this->_apiData['message'] = trans('cc_errors.file_type_not_allowed');
		} else {
			// success response
            $this->_apiData['response'] = "success";
			// no message
			$this->_apiData['message'] = "";
            // init output data array
            $this->_apiData['data'] = $data = array();
			
			// record
			$save = (array)$this->_entity_model->get($request->{$this->_entity_pk});
			
			// move logo file
			if($request->hasFile($file_index) === TRUE) {
				$filename = $file_index."-".$request->user_id."-".$request->{$this->_entity_pk}."-".time();
				$filename = $filename.".".$request->file($file_index)->getClientOriginalExtension();
				$t = $request->file($file_index)->move(config('cc_constants.DIR_NETWORK'),$filename);
				// set name in db
				$save[$file_index] = $filename;
			}
			
			// save
			$save["user_id"] = $request->user_id;
			//$save["network_type_id"] = $request->network_type_id;
			$save["title"] = $request->title;
			$save["updated_at"] = date("Y-m-d H:i:s");
			$this->_entity_model->set($request->{$this->_entity_pk}, $save);
			// unset
			unset($save);
			
			// response data
            $data['network'] = $this->_entity_model->getData($save[$this->_entity_pk]);					
            $data['user'] = $this->__models["user_model"]->getData($user->user_id);
			
			// assign to output
			$this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }
	
	
	

}
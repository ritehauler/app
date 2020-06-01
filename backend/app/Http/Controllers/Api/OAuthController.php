<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;
use Validator;
// load models
#use App\Http\Models\Category as Category;
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;

class OAuthController extends Controller {

	private $_assignData = array(
		'pDir' => '',
		'dir' => DIR_API
	);
	private $_headerData = array();
	private $_footerData = array();
	private $_layout = "";
	private $_jsonData = array();
	private $_model_path = "\App\Http\Models\\";

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(Request $request)
	{
		// init models
        $this->__models['api_method_model'] = new ApiMethod;
        $this->__models['api_user_model'] = new ApiUser;
		
		// error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";
		
		// check access
        $this->__models['api_user_model']->checkAccess($request);
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		
		
	}
	
	
	/**
     * getToken
     *
     * @return Response
     */
    public function getToken(Request $request) {		
		// load models
		$api_token_model = $this->_model_path."ApiToken";
		$api_token_model = new $api_token_model;	
		
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
		// defaults
		$entity_types = config("api_oauth.ENTITY_TYPES");
		$request_entity_models = config("api_oauth.ENTITY_MODELS");
		$request->entity_type = $request->entity_type != "" ? $request->entity_type : NULL;
		$request->entity_id = intval($request->entity_id);
		
		// data
		$request_entity_data = FALSE;
		// if type not none
		if($request->entity_type !== NULL) {
			if(isset($request_entity_models[$request->entity_type])) {
				// request entity models
				$request_entity_models = config("api_oauth.ENTITY_MODELS");
				// try load model
				$request_entity_model = $this->_model_path.($request_entity_models[$request->entity_type]);
				$request_entity_model = new $request_entity_model;
				// data
				$request_entity_data = $request_entity_model->get($request->entity_id);
			}
		}
		
		// validations
        if ($request->entity_type !== NULL && @!isset($entity_types[$request->entity_type])) {
            $this->_apiData['message'] = 'Requested Entity type not allowed';
        } else if($request->entity_id > 0 && $request_entity_data === FALSE){
			$this->_apiData['message'] = 'Invalid Entity Request';
		} else if($request->entity_id > 0 && $request_entity_data->deleted_at !== NULL){
			$this->_apiData['message'] = 'Requested Entity has been removed from system';
		} else if($request->entity_id > 0 && $request_entity_data->status !== 1){
			$this->_apiData['message'] = 'Requested Entity is Inactive';
		} else{
			// success response
			$this->_apiData['response'] = "success";
			
			// generate and assign new token
	        $data["client_token"] = $api_token_model->generate($request->entity_type, $request->entity_id);
			
			// assign to output
			$this->_apiData['data'] = $data;
			
			// messagge
			$this->_apiData['message'] = "Token successfully generated";
        }    
        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * refreshToken
     *
     * @return Response
     */
    public function refreshToken(Request $request) {		
		// load models
		$api_token_model = $this->_model_path."ApiToken";
		$api_token_model = new $api_token_model;	
		
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
		// defaults
		$entity_types = config("api_oauth.ENTITY_TYPES");
		$request_entity_models = config("api_oauth.ENTITY_MODELS");
				
		$request->entity_type = $request->entity_type != "" ? $request->entity_type : NULL;
		$request->entity_id = intval($request->entity_id);
		
		// data
		$request_entity_data = FALSE;
	    $token_data = $api_token_model->getDataByToken($request->old_client_token, 0, $request->entity_type, $request->entity_id);
		
		// if type not none
		if($request->entity_type != "none") {
			if(isset($request_entity_models[$request->entity_type])) {
				// request entity models
				//$request_entity_models = config("api_oauth.ENTITY_MODELS");
				// try load model
				$request_entity_model = $this->_model_path.($request_entity_models[$request->entity_type]);
				$request_entity_model = new $request_entity_model;
				// data
				$request_entity_data = $request_entity_model->get($request->entity_id);
			}
		}
		
		// param validations
		$validator = Validator::make($request->all(), array(
			'old_client_token' => 'required',
		));
		
		
		// validations
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif ($token_data === FALSE) {
	        $this->_apiData['message'] = trans('api_oauth_errors.invalid_entity_provided', array("entity" => "token"));
        } elseif ($request->entity_type && @!isset($entity_types[$request->entity_type])) {
	        $this->_apiData['message'] = trans('api_oauth_errors.entity_not_allowed', array("entity" => "Entity type"));
        } else if ($request->entity_id > 0 && !$request_entity_data) {
	        $this->_apiData['message'] = trans('api_oauth_errors.invalid_entity_request', array("entity" => "Entity"));
		} else if($request->entity_id > 0 && $request_entity_data->deleted_at !== NULL){
	        $this->_apiData['message'] = trans('api_oauth_errors.entity_removed_from_system', array("entity" => "Requested Entity"));
		} else if($request->entity_id > 0 && $request_entity_data->status !== 1){
	        $this->_apiData['message'] = trans('api_oauth_errors.entity_is_inactive', array("entity" => "Requested Entity"));
		} else{
			// success response
	        $this->_apiData['response'] = trans('api_errors.success');
			
			// generate and assign new token
	        $data["client_token"] = $api_token_model->refreshToken($request->old_client_token, $request->entity_type, $request->entity_id);
			
			// assign to output
			$this->_apiData['data'] = $data;
			
			// messagge
	        $this->_apiData['message'] = trans('api_oauth_errors.entity_refresh_success', array("entity" => "Token"));;
        }    
        return $this->__ApiResponse($request,$this->_apiData);
    }

}

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

class CCGeneralController extends Controller {

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
    public function spacePermissions(Request $request) {
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
		
        // validate param
		$type = strtolower(trim(strip_tags($request->input('type', ''))));
		
		
		if($type != "" && !in_array($type,config("cc_constants.SPACE_PERMISSION_TYPES"))) {
			 $this->_apiData['message'] = trans('api_errors.invalid_permission_type');
		} else {
			// success response
			$this->_apiData['response'] = "success";
			
			// init output data array
			$this->_apiData['data'] = $data = array();
			
			// load models
			$this->__models["space_permission_model"] = $this->_model_path."CCSpacePermission";
			$this->__models["space_permission_model"] = new $this->__models["space_permission_model"];
			
			// init array
			$data["permissions"] = array();
			
			
			$query = $this->__models["space_permission_model"]->select("space_permission_id");
			// if type selected
			if($type != "") {
				$query->where("type","=",$type);
			}
			$query->whereNull("deleted_at");
			$query->orderBy("type","ASC");
			$query->orderBy("is_selected","DESC");
			$raw_permissions = $query->get();
			
			// got records ?
			if(isset($raw_permissions[0])) {
				foreach($raw_permissions as $raw_permission) {
					// loop through
					$data["permissions"][] = $this->__models["space_permission_model"]->getData($raw_permission->space_permission_id);
				}
			}
			
		}
		
		// assign to output
		$this->_apiData['data'] = $data;

        return $this->__ApiResponse($request, $this->_apiData);
    }
	

}
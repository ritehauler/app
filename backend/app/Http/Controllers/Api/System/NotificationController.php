<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\EFEntityPlugin;
use App\Http\Models\Conf;

//use Twilio;

class NotificationController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_model_path = "\App\Http\Models\\";
   
    private $_plugin_config = array();
    private $_entityModel = "EntityHistory";
    
    private $_mobile_json = false;
    private $_objectIdentifier = "notification";


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {

        // load entity model
        $this->_entityModel = $this->_model_path . $this->_entityModel;
        $this->_entityModel = new $this->_entityModel;
 
        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

    }

    public function read(Request $request){
		if(isset($request->entity_history_id)){
			$this->_apiData['response']= 'success';
			$this->_entityModel->where("entity_history_id","=",$request->entity_history_id)->update(array("is_read"=>1));
		}
		return $this->__apiResponse($request,$this->_apiData);
	}
			 
    public function counts(Request $request){
		if(isset($request->user_id) && is_numeric($request->user_id)){
			$this->_apiData['response']= 'success';
			$this->_apiData['data']['total_records'] = $this->_entityModel->get_count($request->user_id,'','',0);
			$this->_apiData['data']['unread'] = $this->_entityModel->get_count($request->user_id,0,'',0);
			return $this->__apiResponse($request,$this->_apiData);	
		}
		return $this->__apiResponse($request,$this->_apiData);
	}
	
    public function listing(Request $request)
    {
		if(isset($request->user_id)){
			$this->_apiData['response']= 'success';
			$data = $this->_entityModel->getNotificationData($request->user_id,$request);
			$this->_apiData['data'] = $data;
			return $this->__apiResponse($request,$this->_apiData);
		}
		return $this->__apiResponse($request,$this->_apiData);
    }
	
    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index(Request $request)
    {

    }
 



}
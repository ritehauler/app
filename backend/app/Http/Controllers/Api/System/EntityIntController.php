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

class EntityIntController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_model_path = "\App\Http\Models\\";
    private $_object_identifier = "sys_entity_int";
    private $_sys_entity_int_identifier = "system_entity_int"; // usually routes path
    private $_sys_entity_int_pk = "sys_entity_int_id";
    private $_sys_entity_int_ucfirst = "EntityInt";
    private $_sys_entity_int_model = "SYSEntityInt";
    private $_plugin_config = array();


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load sys_entity_int model
        $this->_sys_entity_int_model = $this->_model_path . $this->_sys_entity_int_model;
        $this->_sys_entity_int_model = new $this->_sys_entity_int_model;

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index(Request $request)
    {

    }


    /**
     * Create
     *
     * @return Response
     */
    public function post(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // validations
        $rules = array(
            'entity_varchar_id' => 'required|integer',
            'data_type_id' => 'required|string',
            'backend_table' => 'required|string',
            'attribute_code' => 'required|unique:'.$this->_sys_entity_int_model->table.',sys_entity_int_code',
            "frontend_input" => "required|string",
            "frontend_label" => "required|string",
            "frontend_class" => "required|string",
            "is_user_defined" => "required|integer",
            'is_required' => 'required|integer',
            'default_value' => 'string',
            'is_unique' => 'required|integer',

        );
        $validator = Validator::make($request->all(), $rules);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // init sys_entity_int
            $sys_entity_int = array();
            // map data
            foreach($rules as $key => $val) {
                $sys_entity_int[$key] = $request->input($key,"");
            }

            // other data
            $sys_entity_int["created_at"] = date("Y-m-d H:i:s");


            $sys_entity_int_id = $this->_sys_entity_int_model->put($sys_entity_int);

            // response data
            $data[$this->_object_identifier] = $this->_sys_entity_int_model->getData($sys_entity_int_id);

            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * check Existance
     *
     * @return Response
     */
    public function checkExist(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        $identity = trim(str_replace(" ", "", strip_tags($request->input('email', ''))));

        // if found "@" sign
        if (config('pl_' . $this->_entity_identifier . '.SMS_SIGNUP_ENABLED') && !preg_match("/@/", $identity)) {
            // validations
            $validator = Validator::make($request->all(), array(
                'email' => "required"
            ));
            // check exists
            $check_exists = $this->_entity_model
                ->select($this->_entity_pk)
                ->where("mobile_no", "=", $request->email)
                ->whereNull("deleted_at")
                ->get();
            // get entity
            $entity = isset($check_exists[0]) ? $this->_entity_model->getData($check_exists[0]->{$this->_entity_pk}) : FALSE;

        } else {
            // validations
            $validator = Validator::make($request->all(), array(
                'email' => "required|email"
            ));
            // check exists
            $check_exists = $this->_entity_model
                ->select($this->_entity_pk)
                ->where("email", "=", $request->email)
                ->whereNull("deleted_at")
                ->get();
            // get entity
            $entity = isset($check_exists[0]) ? $this->_entity_model->getData($check_exists[0]->{$this->_entity_pk}) : FALSE;
        }


        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif (!$entity) {
            // message
            $this->_apiData['message'] = trans('api_errors.invalid_entity_request', array("entity" => $this->_object_identifier));
        } else {
            // success response
            $this->_apiData['response'] = "success";
            $this->_apiData['message'] = trans('api_errors.success');

            // init output data array
            $this->_apiData['data'] = $data = array();

            $data[$this->_object_identifier] = $entity;


            // overrite message
            $this->_apiData['message'] = trans('api_errors.entity_already_exists',array("entity" => $this->_object_identifier));

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
    public function get(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // default method required param
        $request->{$this->_sys_entity_int_pk} = intval($request->input($this->_sys_entity_int_pk, 0));

        // get data
        $sys_entity_int = $this->_sys_entity_int_model->get($request->{$this->_sys_entity_int_pk});
        // validations
        /*if (!in_array("user/get", $this->_plugin_config["webservices"])) {
            $this->_apiData['message'] = 'You are not authorized to access this service.';
        } else*/
        if ($request->{$this->_sys_entity_int_pk} == 0) {
            $this->_apiData['message'] = trans('api_errors.pls_enter_sys_entity_int_id', array("sys_entity_int" => $this->_object_identifier));
        } else if ($sys_entity_int === FALSE) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } elseif ($sys_entity_int->status == 0) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive');
        } elseif ($sys_entity_int->status > 1) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
        } else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            /*//check device token
            if ($device_token != "" && $device_type != "") {
                $sys_entity_int->device_type = $device_type;
                $sys_entity_int->device_token = $device_token;
            }*/

            $sys_entity_int->last_seen_at = date('Y-m-d H:i:s');

            // update user data
            $this->_sys_entity_int_model->set($sys_entity_int->{$this->_sys_entity_int_pk}, (array)$sys_entity_int);

            // get user data
            $data[$this->_object_identifier] = $this->_sys_entity_int_model->getData($sys_entity_int->{$this->_sys_entity_int_pk}, true);

            // message
            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Delete
     *
     * @return Response
     */
    public function delete(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        $rules = array(
            $this->_sys_entity_int_pk => 'required|integer|exists:' . $this->_sys_entity_int_model->table . "," . $this->_sys_entity_int_model->primaryKey . ",deleted_at,NULL"
        );

        $validator = Validator::make($request->all(), $rules);

        // get data
        $sys_entity_int = $this->_sys_entity_int_model
            ->where($this->_sys_entity_int_pk, "=", $request->{$this->_sys_entity_int_pk})
            ->whereNull("deleted_at")
            ->first();

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (!$sys_entity_int) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // get
            $sys_entity_int = json_decode(json_encode($sys_entity_int), true);

            // to-do
            // delete dependencies first
            $this->_sys_entity_int_model->delete($sys_entity_int[$this->_sys_entity_int_pk]);

            // response data
            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


}
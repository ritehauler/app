<?php
namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\CustomHelper;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\EFEntityPlugin;
use App\Http\Models\Conf;
use App\Http\Models\SYSModule;

//use Twilio;

class EntitySearchController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_model_path = "\App\Http\Models\\";
    private $_object_identifier = "sys_entity_type";
    private $_sys_entity_type_identifier = "sys_entity_type"; // usually routes path
    private $_sys_entity_type_pk = "entity_type_id";
    private $_sys_entity_type_ucfirst = "EntityType";
    private $_sys_entity_type_model = "SYSEntityType";
    private $_sys_role_model = "SYSRole";
    private $_sys_role_entity_map_model = "SYSEntityRoleMap";
    private $_sys_entity_attribute_model = "SYSEntityAttribute";
    private $_sys_attribute = "SYSAttribute";
    private $_plugin_config = array();


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load sys_entity_type model
        $this->_sys_entity_type_model = $this->_model_path . $this->_sys_entity_type_model;
        $this->_sys_entity_type_model = new $this->_sys_entity_type_model;

        $this->_sys_role_model = $this->_model_path . $this->_sys_role_model;
        $this->_sys_role_model = new $this->_sys_role_model;

        $this->_sys_attribute = $this->_model_path . $this->_sys_attribute;
        $this->_sys_attribute = new $this->_sys_attribute;

        $this->_sys_entity_attribute_model = $this->_modelPath . $this->_sys_entity_attribute_model;
        $this->_sys_entity_attribute_model = new $this->_sys_entity_attribute_model;
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
     * User data
     *
     * @return Response
     */
    public function get(Request $request)
    {

        // default method required param
        $request->{$this->_sys_entity_type_pk} = intval($request->input($this->_sys_entity_type_pk, 0));
        $api = array();
        // get data
        $sys_entity_type = $this->_sys_entity_type_model->get($request->{$this->_sys_entity_type_pk});
        // validations
        /*if (!in_array("user/get", $this->_plugin_config["webservices"])) {
            $this->_apiData['message'] = 'You are not authorized to access this service.';
        } else*/
        if ($request->{$this->_sys_entity_type_pk} == 0) {
            $this->_apiData['message'] = trans('api_errors.pls_enter_sys_entity_type_id', array("sys_entity_type" => $this->_object_identifier));
        } else if ($sys_entity_type === FALSE) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();


            // update user data
            $this->_sys_entity_type_model->set($sys_entity_type->{$this->_sys_entity_type_pk}, (array)$sys_entity_type);
            $d =array();
            // get user data
            $data[$this->_object_identifier] = $this->_sys_entity_type_model->getData($sys_entity_type->{$this->_sys_entity_type_pk}, true);
            $listOfAttributeToBeValidate = $this->_sys_entity_attribute_model->getEntityAttributeValidationList($sys_entity_type->{$this->_sys_entity_type_pk});
            foreach($listOfAttributeToBeValidate as $fields){
                $d =array();
                if($fields->show_in_search){
                    $attribute = $this->_sys_attribute->getData($fields->attribute_id);
                    if($attribute->use_entity_type){
                        $entity_type = $this->_sys_entity_type_model->getData($attribute->entity_type_id, true);
                        if($entity_type)
                        if(!$this->_sys_entity_type_model->is_table_exist($entity_type->identifier.'_flat')) {
                            $entity_value = \DB::table($entity_type->identifier . '_flat')->select('*')->get();
                            foreach ($entity_value as $entities) {
                                if (isset($entities->title)) {
                                    $d[] = array('id' => $entities->entity_id, 'value' => $entities->title);
                                }
                            }

                            $api[$fields->attribute_code] = $d;
                            unset($d);
                        }


                    }elseif($attribute->backend_table){
                        $api[$fields->attribute_code] = \DB::select("SELECT * From $attribute->backend_table ");

                    }else{
                        $entity_value = \DB::table('sys_attribute_option')->select('*')
                            ->where('attribute_id', $fields->attribute_id)
                            ->get();
                        if(count($entity_value)>0)
                        foreach($entity_value as $entities){
                            if(isset($entities->option)){
                                $d[] = array('id'=>$entities->value,'value'=>$entities->option);
                            }
                        }
                        $api[$fields->attribute_code] = $d;
                        unset($d);

                    }

                }

            }

            // message
            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            //$this->_apiData['data'] = $data;
            $this->_apiData['data']['attribute'] = $api;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }



}
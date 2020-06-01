<?php
namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Libraries\CustomHelper;
use App\Libraries\EntityTrigger;
use Illuminate\Http\Request;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\SYSAttributeOption;
use App\Http\Models\SYSEntityAttribute;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\EFEntityPlugin;
use App\Http\Models\PLAttachment;
use App\Http\Models\SYSEntityAuth;
use App\Http\Models\Conf;
use App\Libraries\ApiCurl;
use App\Libraries\EntityTypeMask;

//use Twilio;

class EntityControllerOld extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_objectIdentifier = "entity";
    private $_entityIdentifier = "system_entity"; // usually routes path
    private $_entityPk = "entity_id";
    private $_entityUcfirst = "Entity";
    private $_entityModel = "SYSEntity";
    private $_pluginConfig = array();
    public $validation = array();
    private $_entity_api_route;
    private $_mobile_json = false;
    private $_entityTypeModel = "SYSEntityType";
    private $_SYSEntityAuth = "SYSEntityAuth";
    private $_SYSEntityAttributeModel = "SYSEntityAttribute";
    private $_PLAttachment = "PLAttachment";
    private $_SYSAttributeOption = "SYSAttributeOption";
    private $_EntityHistory = "SYSEntityHistory";
    private $_entityTypeData = [];
    protected $_panelPath = "";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        // load entity model
        $this->_entityModel = $this->_modelPath . $this->_entityModel;
        $this->_entityModel = new $this->_entityModel;

        $this->_entityTypeModel = $this->_modelPath . $this->_entityTypeModel;
        $this->_entityTypeModel = new $this->_entityTypeModel;

        $this->_PLAttachment = $this->_modelPath . $this->_PLAttachment;
        $this->_PLAttachment = new $this->_PLAttachment;

        $this->_entityAttributeModel = $this->_modelPath . $this->_SYSEntityAttributeModel;
        $this->_entityAttributeModel = new $this->_entityAttributeModel;

        $this->_SYSAttributeOption = $this->_modelPath . $this->_SYSAttributeOption;
        $this->_SYSAttributeOption = new $this->_SYSAttributeOption;

        $this->_SYSEntityAuth = $this->_modelPath . $this->_SYSEntityAuth;
        $this->_SYSEntityAuth = new $this->_SYSEntityAuth;


        $this->_EntityHistory = $this->_modelPath . $this->_EntityHistory;
        $this->_EntityHistory = new $this->_EntityHistory;

        $this->__models['api_method_model'] = new ApiMethod;
        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";
        $this->_mobile_json = intval($request->input('mobile_json', 0)) > 0 ? true : false;

        $this->_entityModel->_mobile_json = $this->_mobile_json;

        $this->_entityTypeData = $entityTypeData = array();
        if (isset($request->entity_type_id) && is_numeric(trim($request->entity_type_id))) {
            $entityTypeData = $this->_entityTypeModel->getEntityTypeById($request->entity_type_id);
        } elseif (isset($request->entity_type_id)) {
            $entityTypeData = $this->_entityTypeModel->getEntityTypeByName($request->entity_type_id);
            if ($entityTypeData) {
                $request->replace(array_merge($request->all(), array('entity_type_id' => $entityTypeData->entity_type_id)));
            }
        }

        if ($this->_mobile_json && $entityTypeData) {
            $this->_objectIdentifier = $entityTypeData->identifier;
        }
		
		

        $this->_panelPath = $this->__getPanelPath();
        $this->_assignData['panel_path'] = $this->_panelPath;
        $this->entity_type_data = $this->_entityTypeData = $entityTypeData;
        //echo '<pre>';print_r($this->entityTypeData);die;
        // plugin config
        //$this->_pluginConfig = $this->__models['entity_plugin_model']->getPluginSchema($this->_entity_id, $this->_plugin_identifier);
        // set defaults
        //$this->_pluginConfig = isset($this->_pluginConfig->webservices) ? $this->_pluginConfig->webservices : array();
        //$this->_pluginConfig["webservices"] = $this->_pluginConfig;
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
        // extra models
        $depend_entity = [];
        $SYSEntityAuthModel = $this->_modelPath . "SYSEntityAuth";
        $SYSEntityAuthModel = new $SYSEntityAuthModel;
        $request_params = $request->all();

        if(isset($request_params['depend_entity'])) {
            $depend_entity = $request_params['depend_entity'];
            unset($request_params['depend_entity']);
            $request->replace($request_params);
        }
        $obj = new EntityTrigger();
        $request->entity_auth_id = isset($request->entity_auth_id)?$request->entity_auth_id:0;
        $listOfAttributeToBeValidate = $listOfAttributeToBeInserted = array();
        if (isset($request->entity_type_id) && is_numeric($request->entity_type_id)) {
            $listOfAttributeToBeValidate = $this->_entityAttributeModel->getEntityAttributeValidationList($request->entity_type_id,'');
            $listOfAttributeToBeInserted = $this->_entityAttributeModel->getEntityAttributeFields($request->entity_type_id);
            $response_validator = $this->_postValidator($request_params, $listOfAttributeToBeValidate);
            if($response_validator)
                return $this->__ApiResponse($request, $this->_apiData);

            $func = $this->__convertToCamel($this->_entityTypeData->identifier.'_verify_trigger');
            if(method_exists($obj,"$func")) {
                $verify_response = $obj->$func($request);
                if($verify_response['error'] == true){
                    $this->_apiData['message'] = $verify_response['message'];
                    return $this->__ApiResponse($request, $this->_apiData);
                }
            }
        }

        $listOfDependentAttributeToBeValidate = $listOfDependentfAttributeToBeInserted = array();
        if(isset($this->_entityTypeData->depend_entity_type) && !empty($this->_entityTypeData->depend_entity_type)) {
            $listOfDependentAttributeToBeValidate = $this->_entityAttributeModel->getEntityAttributeValidationList($this->_entityTypeData->depend_entity_type,'');
            $listOfDependentfAttributeToBeInserted = $this->_entityAttributeModel->getEntityAttributeFields($this->_entityTypeData->depend_entity_type);
        }

        // validation
        if(isset($this->_entityTypeData->depend_entity_type) && !empty($this->_entityTypeData->depend_entity_type)) {
            $this->dependent_entity_type_data = $this->entity_type_data = $this->_getEntityTypeById($this->_entityTypeData->depend_entity_type);
            $func = $this->__convertToCamel($this->dependent_entity_type_data->identifier.'_verify_trigger');
            foreach($depend_entity as $depend_entity_row) {
				$depend_entity_row['entity_type_id'] = $this->dependent_entity_type_data->entity_type_id;
                $response_validator = $this->_postValidator($depend_entity_row, $listOfDependentAttributeToBeValidate);
                if ($response_validator)
                    return $this->__ApiResponse($request, $this->_apiData);
                if(method_exists($obj,"$func")) {
                    $verify_response = $obj->$func($request);
                    if($verify_response['error'] == true){
                        $this->_apiData['message'] = $verify_response['message'];
                        return $this->__ApiResponse($request, $this->_apiData);
                    }
                }
            }
            $this->entity_type_data = $this->_entityTypeData;
            $func_dependent = $this->__convertToCamel($this->_entityTypeData->identifier.'_dependent_verify_trigger');
            if(method_exists($obj,"$func_dependent")) {
                $request_params = $request->all();
                $request_params['depend_entity'] = $depend_entity;
                $request->replace($request_params);
                $verify_response = $obj->$func_dependent($request);
                if ($verify_response['error'] == true) {
                    $this->_apiData['message'] = $verify_response['message'];
                    return $this->__ApiResponse($request, $this->_apiData);
                }
                $depend_entity = $request_params['depend_entity'];
                unset($request_params['depend_entity']);
                $request->replace($request_params);
            }
        }

        $response_post = $this->_post($request, $listOfAttributeToBeValidate, $listOfAttributeToBeInserted);

        if($this->_entityTypeData->wft_id) {
            $func_wfs = $this->__convertToCamel($this->_entityTypeData->identifier.'_wfs_trigger');
            $obj->$func_wfs($request, $response_post, $this->_entityTypeData->wft_id);
        }
        // extra implementation to supportive fields or tables.
        $func = $this->__convertToCamel($this->_entityTypeData->identifier.'_add_trigger');
        if(method_exists($obj,"$func"))
            $obj->$func($request);

        if(isset($this->_entityTypeData->depend_entity_type) && !empty($this->_entityTypeData->depend_entity_type))
            $this->_postDependentEntity($request, $depend_entity, $listOfDependentAttributeToBeValidate, $listOfDependentfAttributeToBeInserted);

        $request_params['depend_entity'] = $depend_entity;
        $request->replace($request_params);

        $func = $this->__convertToCamel($this->_entityTypeData->identifier.'_dependent_add_trigger');
        if(method_exists($obj,"$func"))
            $obj->$func($request);

        return $response_post;
    }

    private function _postDependentEntity(Request $request, $depend_entity, $listOfDependentAttributeToBeValidate, $listOfDependentfAttributeToBeInserted)
    {
        $identifier = $this->_entityTypeData->identifier.'_id';

        if(isset($this->_apiData['data']->entity))
            $entity_id = $this->_apiData['data']->entity->entity_id;
        else
            $entity_id = $this->_apiData['data']->{$this->_entityTypeData->identifier}->entity_id;
        $this->entity_type_data = $this->dependent_entity_type_data;
        foreach($depend_entity as $depend_entity_row) {
            $depend_entity_row[$identifier] = $entity_id;
			$depend_entity_row['entity_type_id'] = $this->dependent_entity_type_data->entity_type_id;
            $request->replace($depend_entity_row);
            $response_dependent_post = $this->_post($request, $listOfDependentAttributeToBeValidate, $listOfDependentfAttributeToBeInserted);
            $func = $this->__convertToCamel($this->dependent_entity_type_data->identifier.'_add_trigger');
            $obj = new EntityTrigger();
            if(method_exists($obj,"$func"))
                $obj->$func($request, $depend_entity_row);
        }
    }

    private function _postValidator($request_params, $listOfAttributeToBeValidate)
    {
        $is_error = false;
        $rules = array(
            'entity_type_id' => 'required|integer|exists:' . $this->_entityTypeModel->table . "," . $this->_entityTypeModel->primaryKey,
            // 'identifier' => 'string', //required|unique:' . $this->_entityModel->table . ',identifier
            //  $SYSEntityAuthModel->primarKey => 'integer|exists:' . $SYSEntityAuthModel->table . "," . $SYSEntityAuthModel->primaryKey . ",deleted_at,NULL",
        );

        $attributes_error = 1;
        if(isset($listOfAttributeToBeValidate[0])) {
            foreach ($listOfAttributeToBeValidate as $result) {
                $rules[$result->attribute_code] = $result->validation;
            }
            $attributes_error = 0;
        }



        $validator = Validator::make($request_params, $rules);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
            $is_error = true;
        } else if($attributes_error > 0) {
            $this->_apiData['message'] = "No attributes defined";
            $is_error = true;
        }
        return $is_error;
    }

    private function _post(Request $request, $listOfAttributeToBeValidate, $listOfAttributeToBeInserted)
    {
        $listOfAttributeToBeInserted = $this->_entityAttributeModel->getEntityAttributeFields($request->entity_type_id);
        {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();
            $identifier = $this->_objectIdentifier;
            if (is_numeric($request->entity_type_id)) {
                $entityTypeData  = $this->entity_type_data;
                if ($this->_mobile_json) $identifier = $entityTypeData->identifier;
            }

            $entity = array();
           
            $entity["created_at"] = date("Y-m-d H:i:s");

            //Map values to attribute field
            foreach ($listOfAttributeToBeInserted as $key => $result) {
                if (isset($result->model) && ($result->model == 'sys_entity')) {
                    $entity[$result->attribute_code] = $request->{$result->attribute_code};
                }
            }
            $entity["entity_auth_id"] = isset($request->entity_auth_id) ? $request->entity_auth_id : 0;

            $entity_id = $this->_entityModel->put($entity);
            //print_r($listOfAttributeToBeInserted); exit;
            //Map values to attribute field
            foreach ($listOfAttributeToBeInserted as $key => $result) {
                if (isset($result->attribute_code))
                if (array_key_exists($result->attribute_code, $request->all()))
                    if ($result->model != 'sys_entity') {
                        $table = $result->model;

                        if(strpos($request->{$result->attribute_code}, ',') > 0 && $result->php_data_type == 'comma_separated' )
                        {
                            foreach(explode(',',$request->{$result->attribute_code}) as $temp){
                                $listOfAttributeToBeInserted[$key]->entity_id = $entity_id;
                                $listOfAttributeToBeInserted[$key]->value = $temp;
                                unset($result->attribute_code,$result->model,$result->attribute_set_id,$result->php_data_type);
                                \DB::table($table)->insert((array)$listOfAttributeToBeInserted[$key]);
                            }

                        }else
                        {
                            //print $result->attribute_code."\n";
                            $listOfAttributeToBeInserted[$key]->entity_id = $entity_id;
                            $listOfAttributeToBeInserted[$key]->value = $request->{$result->attribute_code};
                            unset($result->attribute_code,$result->model,$result->attribute_set_id,$result->php_data_type);
                            \DB::table($table)->insert((array)$listOfAttributeToBeInserted[$key]);
                        }

                    }
            }


            //This variable is use to get attribute having list of values like Gender( male/ female )
            $dataTypesHavingSelectedValues = \DB::table('sys_attribute')->selectRaw('group_concat(attribute_code) as listOfAttr')->whereIn('data_type_id', [5,  9, 11, 12])->first();


            $entity_type = $this->_entityTypeModel->getData($entity['entity_type_id']);
            if ($entity_type->use_flat_table == "1") {
                $flat_entity = array('entity_id' => $entity_id);
                $flat_fields = array('entity_id');
                foreach ($listOfAttributeToBeValidate as $field) {
                    $flat_fields[] = $field->attribute_code;
                    if ($request->{$field->attribute_code})
                        if (in_array($field->attribute_code, array_unique (explode(',', $dataTypesHavingSelectedValues->listOfAttr))))
                            if ($field->use_entity_type) {


                                if(strpos($request->{$result->attribute_code}, ',')=== false)
                                {   $string = '';
                                    foreach(array_unique(explode(',',$request->{$field->attribute_code})) as $temp){
                                        //$record = $this->_entityModel->getLinkedEntityAttributeValue($field->attribute_id, $temp,$request->_lang);
                                        //if (count($record)) {
                                            $string .= $temp.',';
                                        //}
                                    }
                                    $flat_entity[$field->attribute_code] = rtrim($string, ',');

                                }else{
                                    //$record = $this->_entityModel->getLinkedEntityAttributeValue($field->attribute_id, $request->{$field->attribute_code},$request->_lang);
                                    //if (count($record)) {
                                       $flat_entity[$field->attribute_code] = $request->{$field->attribute_code};
                                    //}
                                }

                            }elseif($field->use_entity_type == 0 && $field->linked_entity_type_id !=0){
                                $flat_entity[$field->attribute_code] = $request->{$field->attribute_code};
                            } else {
                                $entity_value = \DB::table('sys_attribute_option')->select('option')
                                    ->where('value', $request->{$field->attribute_code})
                                    ->where('attribute_id', $field->attribute_id)
                                    ->first();
                                if($entity_value){
                                    $flat_entity[$field->attribute_code] = $entity_value->option;
                                }
                                else{
                                    $flat_entity[$field->attribute_code] = '';
                                }


                            }

                        else
                            $flat_entity[$field->attribute_code] = $request->{$field->attribute_code};

                }
                $SYSTableFlatModel = $this->_modelPath . "SYSTableFlat";
                $SYSTableFlatModel = new $SYSTableFlatModel($entity_type->identifier);
                $SYSTableFlatModel->__fields = $flat_fields;
                $SYSTableFlatModel->put($flat_entity);
            }

            // response data
            //$data[$this->_objectIdentifier] = $this->_entityModel->getData($entity_id);

            $request->entity_id = $entity_id;
            $record = $this->_entityModel->getData($entity_id);
            $record->entity_type_identifier = $identifier;
            // init attributes
         
            if (isset($entityTypeData) && $entityTypeData->show_gallery == "1") {
                if (isset($request->gallery_items) && !empty($request->gallery_items)) {
                    $attachments = $request->gallery_items;
                    if (!is_array($attachments)) $attachments = explode(",", $attachments);
                    $gallery_featured_item = 0;
                    if (isset($request->gallery_featured_item) && !empty($request->gallery_featured_item)) $gallery_featured_item = $request->gallery_featured_item;
                    $this->_PLAttachment->updateAttachmentByEntityID($entity_id, $attachments, $gallery_featured_item);
                }
                $record->gallery = array();
                $record->gallery = $this->_PLAttachment->getAttachmentByEntityID($entity_id);
            }

            // init attributes
            //$data[$identifier] = $record;
            $data = $this->_entityModel->getEntityData($request);
            if($data){
                $data2 = $data;
                unset($data);
                $data = (object)array($identifier => $data2);
            }else{
                $data = (object)array($identifier => $data);  
            }

            if($entity_type->entity_type_id == 20){
                $func = 'calculateCustomerCart';//$this->__convertToCamel($this->dependent_entity_type_data->identifier.'_add_trigger');
                $obj = new EntityTrigger();
                if(method_exists($obj,"$func"))
                    $obj->$func();

            }
            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $data->identifier = $identifier; // attach identifier
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    private function _getEntityTypeById($entity_type_id)
    {
        return $this->_entityTypeModel->getEntityTypeById($entity_type_id);
    }

    /**
     * Update
     *
     * @return Response
     */
    public function save(Request $request)
    { // print_r($_POST); exit;
        $request_params = $request->all();
        $depend_entity = [];
        if (isset($request_params['depend_entity'])) {
            $depend_entity = $request_params['depend_entity'];
            unset($request_params['depend_entity']);
            $request->replace($request_params);
        }
        if (isset($request->entity_type_id) && is_numeric($request->entity_type_id)) {
            $listOfAttributeToBeValidate = $this->_entityAttributeModel->getEntityAttributeValidationListForUpdate($request->entity_type_id,$request->entity_id);
            $listOfAttributeToBeInserted = $this->_entityAttributeModel->getEntityAttributeFields($request->entity_type_id);
        }

        // validations


        //check if entity type is auth
        if (is_numeric($request->entity_type_id)) {
            $entityTypeData = $this->entity_type_data = $this->_getEntityTypeById($request->entity_type_id);
            if ($this->_mobile_json) $identifier = $entityTypeData->identifier;
        }

        // validate
        if (!$this->_saveValidator($request, $listOfAttributeToBeValidate))
            return $this->_apiData;

        $obj = new EntityTrigger();
        $func = $this->__convertToCamel($this->_entityTypeData->identifier . '_verify_trigger');

        if (method_exists($obj, "$func")) {
            $verify_response = $obj->$func($request);
            if ($verify_response['error'] == true) {
                $this->_apiData['message'] = $verify_response['message'];
                return $this->_apiData;
            }
        }
        // success response
        $this->_apiData['response'] = "success";

        // init output data array
        $this->_apiData['data'] = $data = array();
        $identifier = $this->_objectIdentifier;

        // init entity
        $entity = array();

        $entity["updated_at"] = date("Y-m-d H:i:s");
        //Map values to attribute field
        foreach ($listOfAttributeToBeInserted as $key => $result) {
            if ($result->model == 'sys_entity') {
                $entity[$result->attribute_code] = $request->{$result->attribute_code};
            }
        }

        //if user management then first get entity auth id then update entity
        if( $entityTypeData->allow_auth == 1 && $entityTypeData->allow_backend_auth == 1){
            $entity_auth = $this->_SYSEntityAuth->entityQuery($request->entity_type_id)
                ->where("entity.".$this->_entityPk, "=", $request->{$this->_entityPk})
                ->select("auth.".$this->_SYSEntityAuth->primaryKey)
                ->first();

           if(isset($entity_auth->{$this->_SYSEntityAuth->primaryKey})){
               $entity[$this->_SYSEntityAuth->primaryKey] = $entity_auth->{$this->_SYSEntityAuth->primaryKey};
           }
        }



        $entity_id = $this->_entityModel->set($request->entity_id, $entity);

        $entity_id = $request->entity_id;
        //Map values to attribute field

        $func = $this->__convertToCamel($this->_entityTypeData->identifier . '_update_alternate');
        if (method_exists($obj, "$func")) {
            $obj->$func($request, $depend_entity);
        } else {
            $dataTypesHavingSelectedValues = \DB::table('sys_attribute')->selectRaw('group_concat(attribute_code) as listOfAttr')->whereIn('data_type_id', [5, 9, 11, 12])->first();
            foreach ($listOfAttributeToBeInserted as $result) {
                if (isset($request->{$result->attribute_code}) && $request->{$result->attribute_code} !== '' && $result->model != 'sys_entity') {
                    //DeleteExtraCode

                    if ( $result->php_data_type == 'comma_separated') {
                        if (in_array($result->attribute_code, explode(',', $dataTypesHavingSelectedValues->listOfAttr))) {
                            $table = $result->model;
                            $string = '';
                            //Delete entity attribute data
                            $this->_entityModel->deleteAttribute($entity_id,$result->attribute_id,$table);

                            foreach (array_unique(explode(',', $request->{$result->attribute_code})) as $temp) {

                                $dataToBeInserted = array("entity_id" => $entity_id,
                                    "value" => $temp,
                                    'entity_type_id' => $request->entity_type_id,
                                    'attribute_id' => $result->attribute_id);
                                \DB::table($table)->insert($dataToBeInserted);

                            }
                        }
                    } else {
                        $table = $result->model;
                        $dataToBeInserted = array("entity_id" => $entity_id,
                            "value" => $request->{$result->attribute_code},
                            'entity_type_id' => $request->entity_type_id,
                            'attribute_id' => $result->attribute_id);

                        unset($result->attribute_code);
                        unset($result->model);
                        $entity_count = \DB::table($table)
                            ->where('entity_id', $entity_id)
                            ->where('entity_type_id', $request->entity_type_id)
                            ->where('attribute_id', $result->attribute_id)
                            ->limit(1)
                            ->count();
                        if ($entity_count == 1) {
                            \DB::table($table)
                                ->where('entity_id', $entity_id)
                                ->where('entity_type_id', $request->entity_type_id)
                                ->where('attribute_id', $result->attribute_id)
                                ->limit(1)
                                ->update($dataToBeInserted);

                        } else {
                            \DB::table($table)->insert($dataToBeInserted);
                        }
                    }

                }
            }

            $entity_type = $this->_entityTypeModel->getData($entity['entity_type_id']);
            if ($entity_type->use_flat_table == "1") {
                $flat_entity = array('entity_id' => $entity_id, "updated_at" => date("Y-m-d H:i:s"));
                $flat_fields = array('entity_id');
                foreach ($listOfAttributeToBeValidate as $field) {
                    if (isset($request->{$field->attribute_code}) && $request->{$field->attribute_code} != '') {
                        $flat_fields[] = $field->attribute_code;
                        $flat_entity[$field->attribute_code] = $request->{$field->attribute_code};
                    }
                }

                $SYSTableFlatModel = $this->_modelPath . "SYSTableFlat";
                $SYSTableFlatModel = new $SYSTableFlatModel($entity_type->identifier);
                $SYSTableFlatModel->__fields = $flat_fields;

                $SYSTableFlatModel->where("entity_id", "=", $entity_id)
                    ->whereNull("deleted_at")
                    ->update($flat_entity);
            }
            $func = $this->__convertToCamel($this->_entityTypeData->identifier . '_update_trigger');
            if (method_exists($obj, "$func"))
                $obj->$func($request);

            //if entity type is user management then update entity role
            if( $entityTypeData->allow_auth == 1 && $entityTypeData->allow_backend_auth == 1){
                //update role_id
                if(isset($request->role_id)){
                    $role_map_model = $this->_modelPath . "SYSEntityRoleMap";
                    $role_map_model = new $role_map_model;

                    $update_param = array();
                    $update_param['role_id'] = $request->role_id;
                    $update_param['entity_id'] = $request->entity_id;
                    $role_map_model->where('entity_id','=',$entity_id)->update($update_param);

                }
            }


        }

        //Gallery images work for update
        if (isset($entityTypeData) && $entityTypeData->show_gallery == "1") {
            if (isset($request->gallery_items) && !empty($request->gallery_items)) {
                $attachments = $request->gallery_items;
                if (!is_array($attachments)) $attachments = explode(",", $attachments);
                $gallery_featured_item = 0;
                if (isset($request->gallery_featured_item) && !empty($request->gallery_featured_item)) $gallery_featured_item = $request->gallery_featured_item;
                //First delete previous image then upload new one
                $this->_PLAttachment->deleteAttachmentByEntityID($entity_id);
                $this->_PLAttachment->updateAttachmentByEntityID($entity_id, $attachments, $gallery_featured_item);
            }
            //$record->gallery = array();
            //  $record->gallery = $this->_PLAttachment->getAttachmentByEntityID($entity_id);
        }

        //$data[$this->_objectIdentifier] = $this->_entityModel->getData($entity_id);
        $data = $this->_entityModel->getData($entity_id);
        // message
        $this->_apiData['message'] = trans('api_errors.success');

        if($data){
            $data2 = $data;
            unset($data);
            $data = (object)array($identifier => $data2);
        }else{
            $data = (object)array($identifier => $data);
        }
        // assign to output
        $data->identifier = $identifier; // attach identifier
        $this->_apiData['data'] = $data;


        return $this->__ApiResponse($request, $this->_apiData);
    }

    private function _saveValidator(Request $request, $listOfAttributeToBeValidate)
    {
        $this->_apiData['error'] = 0;
        $rules = array(
            'entity_type_id' => 'integer|exists:' . $this->_entityTypeModel->table . "," . $this->_entityTypeModel->primaryKey . ",deleted_at,NULL",
            'entity_id' => 'integer|exists:' . $this->_entityModel->table . "," . $this->_entityPk . ",deleted_at,NULL"
        );

        if($this->entity_type_data->allow_auth == 1 && $this->entity_type_data->allow_backend_auth == 1){
            if($this->entity_type_data->identifier != "customer"){
                $rules['role_id'] = 'required';
            }

        }

        foreach ($listOfAttributeToBeValidate as $result) {
            if($result->attribute_code != "") {
                $rules[trim($result->attribute_code)] = $result->validation;
            }
        }

        $validator = Validator::make($request->all(), $rules);
        // validator 2 for verifying correct entity_type with entity_id
        $validator2 = Validator::make($request->all(), array(
            'entity_id' => 'required|integer|exists:' . $this->_entityModel->table . ',' . $this->_entityPk . ',entity_type_id,' . $request->input('entity_type_id', 0) . ',deleted_at,NULL'
        ));
        if ($request->input('entity_type_id', 0) > 0 && $validator2->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
            $this->_apiData['error'] = 1;
            return false;
        } else if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
            $this->_apiData['error'] = 1;
            return false;
        }
        return true;
    }

        /**
     * Delete
     *
     * @return Response
     */
    public function delete(Request $request)
    {
        // trim/escape all
        /*$request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/

        $rules = array(
            'entity_id' => 'integer|exists:' . $this->_entityModel->table . "," . $this->_entityPk . ",deleted_at,NULL"
        );

        $validator = Validator::make($request->all(), $rules);
        // validator 2 for verifying correct entity_type with entity_id
        $validator2 = Validator::make($request->all(), array(
            'entity_id' => 'required|integer|exists:' . $this->_entityModel->table . ',' . $this->_entityPk . ',entity_type_id,' . $request->input('entity_type_id', 0) . ',deleted_at,NULL'
        ));

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif ($request->input('entity_type_id', 0) > 0 && $validator2->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();
            // get
            $record = $this->_entityModel->get($request->{$this->_entityPk});

            $this->_entityModel->remove($request->{$this->_entityPk});

            if ($record) {
                $entity_type = $this->_entityTypeModel->getData($record->entity_type_id);

                if(isset($entity_type->depend_entity_type) && !empty($entity_type->depend_entity_type)) {
                    $entity_type_dependent = $this->_entityTypeModel->getData($entity_type->depend_entity_type);
                    $temp = \DB::table($entity_type_dependent->identifier . '_flat')->where($entity_type->identifier.'_id','=',$record->entity_id)->get();
                    foreach($temp as $entities){
                        $data['entity_id'] = $entities->entity_id;

                        $response =  CustomHelper::internalCall($request,"api/system/entities/delete", 'POST', $data,false);

                    }
                }

                if ($entity_type->use_flat_table == "1") {
                    // remove from flat
                    if (\Schema::hasTable($entity_type->identifier . '_flat')) {
                       // remove from flat table

                        \DB::table($entity_type->identifier . '_flat')->
                            where('entity_id','=',$record->entity_id)
                            ->delete();
                    }
                }
            }

            $this->_apiData['message'] = trans('api_errors.entity_delete_success');
            // response data
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
        // default method required param
        $request->{$this->_entityPk} = intval($request->input($this->_entityPk, 0));

        // validations
        $rules = array(
            'entity_id' => 'required|integer|exists:' . $this->_entityModel->table . "," . $this->_entityPk . ",deleted_at,NULL",
            'entity_type_id' => 'integer|exists:' . $this->_entityTypeModel->table . "," . $this->_entityTypeModel->primaryKey . ",deleted_at,NULL",
        );

        $validator = Validator::make($request->all(), $rules);
        // validator 2 for verifying correct entity_type with entity_id
        $validator2 = Validator::make($request->all(), array(
            'entity_id' => 'required|integer|exists:' . $this->_entityModel->table . ',' . $this->_entityPk . ',entity_type_id,' . $request->input('entity_type_id', 0) . ',deleted_at,NULL'
        ));

        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif ($request->input('entity_type_id', 0) > 0 && $validator2->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } /* if (!in_array("user/get", $this->_pluginConfig["webservices"])) {
          $this->_apiData['message'] = 'You are not authorized to access this service.';
          } else */
        elseif ($request->{$this->_entityPk} == 0) {
            $this->_apiData['message'] = trans('api_errors.pls_enter_entity_id', array("entity" => $this->_objectIdentifier));
        } else {
            $this->_apiData['response'] = "success";

            $data = $this->_entityModel->getData($request->{$this->_entityPk});

            $entityTypeData = $this->_entityTypeModel->getEntityTypeById($data->entity_type_id);

            if ($request->mobile_json)
                    $identifier = $entityTypeData->identifier;
                else
                    $identifier = 'entity';

            // message
            $this->_apiData['message'] = trans('api_errors.success');
            if(isset($request->hook)){
                $hooks = explode(',', $request->hook);
                $temp =array();
                foreach ($hooks as $hook)
                {
                    if(\Schema::hasTable($hook.'_flat')){
                        $entity_values = \DB::table($hook.'_flat')->select('*')
                            ->where($entityTypeData->identifier.'_id', $request->{$this->_entityPk})
                            ->get();
                        if(count($entity_values))
                        foreach($entity_values as $entity_value){
                            $temp[] = $this->_entityModel->getData($entity_value->entity_id);
                        }

                        $data->{$hook} = $temp;
                    }
                }

            }
            // assign to output
            $this->_apiData['data']['identifier'] = $identifier; // attach identifier
            $this->_apiData['data'][$identifier] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * mapAttributes
     *
     * @return Response
     */
    public function mapAttributes($attributes, $ext_attributes)
    {
        $merge_attributes = array();
        foreach ($ext_attributes as $ext_attribute) {
            foreach ($attributes as $attribute)
                if ($ext_attribute->attribute_id == $attribute->attribute_id) {
                    $merge_attributes[] = (object)array_merge((array)$ext_attribute, (array)$attribute);
                    break;
                }
        }
        return $merge_attributes;
    }

    /**
     * Listin / Search
     *
     * @return Response
     */
    public function entityCounts($entity_type_id, $fields, $returnID = false)
    {
        $entityCount = 0;

        $entityTypeData = $this->_entityTypeModel->getEntityTypeById($entity_type_id);

        if ($entityTypeData->use_flat_table == '11') {
            $attributes_search = $this->_entityModel->getEntityIDsByFlat($entityTypeData, $fields);
        } else {
            $attributes_search = $this->_entityModel->getEntityIDsBySearch($entity_type_id, $request);
        }

        if (count($attributes_search) > 0) {
            if ($attributes_search[0] != 0) {

                $query = $this->_entityModel->select($this->_entityModel->primaryKey);
                $query->whereNull("deleted_at"); // exclude deleted
                $query->where("entity_type_id", "=", $entity_type_id);
                $query->whereIn("entity_id", $attributes_search);
                $likes = $query->get();

                if ($returnID && $likes->count() > 0) {
                    $entityCount = $likes[0]->entity_id;
                } else {
                    $entityCount = $likes->count();
                }
            }
        }
        return $entityCount;

    }

    /**
     * Listin / Search
     *
     * @return Response
     */
    public function listing(Request $request)
    {


        if (isset($request->entity_type_id) && is_numeric($request->entity_type_id)) {
            if (is_numeric($request->entity_type_id)) {
                $params['entityTypeData'] = $this->_entityTypeModel->getEntityTypeById($request->entity_type_id);
                $identifier = $params['entityTypeData']->identifier;
            }
        }
        // validations
        $rules = array(
            'entity_type_id' => 'required|int',
            'identifier' => 'string',
        );

        if (!$this->_mobile_json) {
            // override object identifier
            $identifier = $this->_objectIdentifier . "_" . strtolower(__FUNCTION__);
        }

        $validator = Validator::make($request->all(), $rules);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // sorting defaults
            $params['allowed_ordering'] = $params['allowed_searching'] = "identifier,created_at,entity_id";
            $params['allowed_sorting'] = "asc,desc";
            $params['order_by'] = $request->input("order_by", "") == "" ? explode(",", $params['allowed_ordering'])[0] : $request->order_by;
            $params['sorting'] = $request->input("sorting", "") == "" ? explode(",", $params['allowed_sorting'])[0] : $request->sorting;
            $params['limit'] = $request->input("limit", "") == "" ? PAGE_LIMIT_API : intval($request->input("limit", ""));
            $params['offset'] = intval($request->input("offset", 0));
            $params['show_in_list'] = 1;

            $response = $this->_entityModel->getListData($request->entity_id, $request->entity_type_id, $request, $params);

            $response['data'] = isset($response['data']) ? $response['data'] : "";
            $this->_apiData['data'][$identifier] = $response['data'];
            if ($this->_mobile_json) {
                $this->_apiData['data']["page"] = array(
                    //"offset" => $offset,
                    "limit" => $params['limit'],
                    "total_records" => $response['total_records'],
                    //"next_offset" => ($offset + $limit), // new pagination flow
                    "next_offset" => $response['next_offset'],
                    //"prev_offset" => $offset > 0 ? ($offset - $limit) : $offset, // new pagination flow,
                    "prev_offset" => $params['offset']
                );
            } else {
                $this->_apiData['data']["page"] = array(
                    "offset" => $params['offset'],
                    "limit" => $params['limit'],
                    "total_records" => $response['total_records'],
                    "next_offset" => ($params['offset'] + $params['limit']),
                    "prev_offset" => $params['offset'] > 0 ? ($params['offset'] - $params['limit']) : $params['offset'],
                );
            }
            $this->_apiData['message'] = trans('api_errors.success');
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }
    /**
     * Listin / Search
     *
     * @return Response
     */
    public function updateFlatTable(Request $request)
    {

        if (isset($request->entity_type_id) && is_numeric($request->entity_type_id)) {
            if (is_numeric($request->entity_type_id)) {
                $params['entityTypeData'] = $this->_entityTypeModel->getEntityTypeById($request->entity_type_id);
                $identifier = $params['entityTypeData']->identifier;
            }
        }
        // validations
        $rules = array(
            'entity_type_id' => 'required|int',
            'identifier' => 'string',
        );

        if (!$this->_mobile_json) {
            // override object identifier
            $identifier = $this->_objectIdentifier . "_" . strtolower(__FUNCTION__);
        }

        $validator = Validator::make($request->all(), $rules);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // sorting defaults
            $params['allowed_ordering'] = $params['allowed_searching'] = "identifier,created_at,entity_id";
            $params['allowed_sorting'] = "asc,desc";
            $params['order_by'] = $request->input("order_by", "") == "" ? explode(",", $params['allowed_ordering'])[0] : $request->order_by;
            $params['sorting'] = $request->input("sorting", "") == "" ? explode(",", $params['allowed_sorting'])[0] : $request->sorting;
            $params['limit'] = $request->input("limit", "") == "" ? PAGE_LIMIT_API : intval($request->input("limit", ""));
            $params['offset'] = intval($request->input("offset", 0));
            $params['show_in_list'] = 1;

            $response = $this->_entityModel->getListData($request->entity_id, $request->entity_type_id, $request, $params);

            print_r($response);exit;

            $this->_apiData['message'] = trans('api_errors.success');
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }
    /**
     * Search
     * @param $query query
     * @return query
     */
    private function _search($request, $query, $allowed_searching = "")
    {
        // fix indexes
        $fix_indexes = array($this->_entityPk, "target_entity_id", "created_by");
        // search
        foreach (explode(",", $allowed_searching) as $field) {
            // if in fix indexes
            if (in_array($field, $fix_indexes)) {
                // all fix searches
                if (isset($request->{$field}) && $request->{$field} != "") {
                    $q = trim(strtolower($request->{$field}));
                    $query->where($field, '=', "$q");
                }
            } else {
                // all LIKE searches
                if (isset($request->{$field}) && $request->{$field} != "") {
                    $q = trim(strtolower($request->{$field}));
                    $query->where($field, 'like', "%$q%");
                }
            }
        }
        return $query;
    }

    /**
     * load param
     * @param uri
     * return
     */
    public function load_params($uri, $type)
    {
        // load model
        $this->__models['api_method_field_model'] = new ApiMethodField;

        $this->_assignData['api_method'] = $this->__models['api_method_model']
            ->where("type", "=", $type)
            ->where("uri", "=", $uri)
            ->where("is_active", "=", 1)
            ->whereNull("deleted_at")
            ->first();
        //echo '<pre>';print_r( $this->_assignData ); die;
        if ($this->_assignData['api_method'] !== FALSE) {
            // fetch
            $query = $this->__models['api_method_field_model']
                ->where('is_active', '=', 1)
                ->whereNull("deleted_at")
                ->where("request_type", "=", $type)
                ->where('method_uri', '=', $uri);
            $query->orderBy("order", "ASC");


            $this->_assignData['records'] = $query->get();
            //echo '<pre>';print_r( $this->_assignData); die;
            // target element
            //$this->_jsonData['targetElem'] = 'div[id=parameters]';
            // html into string
            //$this->_jsonData['html'] = View::make($this->_assignData["dir"] . "/" . __FUNCTION__, $this->_assignData)->with($this->__models)->__toString();

            return $this->_assignData;
        }
    }
	
	
	public function listWishlist(Request $request) 
	{
		$customer_id = $request->input('customer_id'); 
		$data = array(); 
		$data['actor_entity_id'] 			= $customer_id;
		$data['target_entity_type_id']		= 14;
		$data['actor_entity_type_id']		= 11;
		$data['type']						= "private";
		$data['mobile_json']	= 1;
					
		$response = json_encode(CustomHelper::internalCall($request,"api/extension/social/package/like/listing", 'GET',$data,false));
		$json 	  = json_decode($response,true);
		
		if($json['error']==0) 
		{
			$wishlist = isset($json["data"]["like_listing"])? $json["data"]["like_listing"] : null;
			foreach($wishlist as $products ) 
			{
				if(isset($products['product']['entity_id']))
					$product_id[]	= $products['product']['entity_id'];
			}
			$product_id = implode (", ", $product_id);
			$data = array() ; 
			$data['products'] = $product_id;
			$this->_apiData['data'] = $data;
			
			$json = $this->__ApiResponse($request, $this->_apiData); 
			
			if(isset($json['jsonEditor']))
			{
				$jsonEditor = json_decode($json['jsonEditor'],true);
				$jsonEditor['error'] = 0; 
				$response = array(); 
				$response['targetElem'] = 'pre[id=response]';
				$response['jsonEditor'] = json_encode($jsonEditor); 	
				return $response;
			}
			else
			{
				$json['error'] = 0;
				return json_encode($json); 
			}
			
		}
		else 
		{
			$data = array() ; 
			$data['products'] = "";
			$this->_apiData['data'] = $data;
			$json = $this->__ApiResponse($request, $this->_apiData); 
			if(isset($json['jsonEditor']))
			{
				$jsonEditor = json_decode($json['jsonEditor'],true);
				$jsonEditor['error'] = 1; 
				$response = array(); 
				$response['targetElem'] = 'pre[id=response]';
				$response['jsonEditor'] = json_encode($jsonEditor); 	
				return $response;
			}
			else
			{
				$json['error'] = 1;
				return json_encode($json); 
			}
		}
		
	}
    
}

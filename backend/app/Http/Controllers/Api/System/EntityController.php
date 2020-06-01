<?php
namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\Conf;
use App\Http\Models\EmailTemplate;
use App\Http\Models\PLAttachment;
use App\Http\Models\SYSAttributeOption;
use App\Http\Models\SYSEntity;
use App\Http\Models\SYSEntityAttribute;
use App\Http\Models\SYSEntityAuth;
use App\Http\Models\SYSEntityHistory;
use App\Http\Models\SYSEntityType;
use App\Libraries\CouponLib;
use App\Libraries\CustomHelper;
use App\Libraries\EntityTrigger;
use App\Libraries\OrderCart;
use App\Libraries\Datetime;
use App\Libraries\System\Entity;
use Illuminate\Http\Request;
use Validator;
use View;

// load models


//use Twilio;

class EntityController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_objectIdentifier = "entity";
    private $_entityIdentifier = "system_entity"; // usually routes path
    private $_entityPk = "entity_id";
    private $_entityUcfirst = "Entity";
    private $_pluginConfig = array();
    public $validation = array();
    private $_entity_api_route;
    private $_mobile_json = FALSE;
    private $_entityTypeData = [];
    protected $_panelPath = "";

    /**
     * Model set
     */
    private

        /**
         * Entity
         *
         * @var SYSEntity
         */
        $_entityModel,

        /**
         * Entity Type
         *
         * @var SYSEntityType
         */
        $_entityTypeModel,

        /**
         * Attachment
         *
         * @var PLAttachment
         */
        $_attachmentModel,

        /**
         * Entity Attribute
         *
         * @var SYSEntityAttribute
         */
        $_entityAttributeModel,

        /**
         * Attribute Option
         *
         * @var SYSAttributeOption
         */
        $_SYSAttributeOption,

        /**
         * Entity Auth
         *
         * @var SYSEntityAuth
         */
        $_SYSEntityAuth,

        /**
         * Entity History
         *
         * @var SYSEntityHistory
         */
        $_entityHistory;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        // load entity model
        $this->_entityModel = new SYSEntity();
        $this->_entityTypeModel = new SYSEntityType();
        $this->_attachmentModel = new PLAttachment();
        $this->_entityAttributeModel = new SYSEntityAttribute();
        $this->_SYSAttributeOption = new SYSAttributeOption();
        $this->_SYSEntityAuth = new SYSEntityAuth();
        $this->_entityHistory = new SYSEntityHistory();

        $this->__models['api_method_model'] = new ApiMethod;
        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";
        $this->_mobile_json = intval($request->input('mobile_json', 0)) > 0 ? TRUE : FALSE;

        $this->_entityModel->_mobile_json = $this->_mobile_json;


        // get entity type data (set entity type params)
        $this->_entityTypeData = $this->_setEntityTypeParams($request);

        if ($this->_mobile_json && $this->_entityTypeData) {
            $this->_objectIdentifier = $this->_entityTypeData->identifier;
        }

        $this->_panelPath = $this->__getPanelPath();
        $this->_assignData['panel_path'] = $this->_panelPath;
        $this->entity_type_data = $this->_entityTypeData = $this->_entityTypeData;
        //echo '<pre>';print_r($this->entityTypeData);die;
        // plugin config
        //$this->_pluginConfig = $this->__models['entity_plugin_model']->getPluginSchema($this->_entity_id, $this->_plugin_identifier);
        // set defaults
        //$this->_pluginConfig = isset($this->_pluginConfig->webservices) ? $this->_pluginConfig->webservices : array();
        //$this->_pluginConfig["webservices"] = $this->_pluginConfig;

        $this->_pLib = new Entity();
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

        //$request['pickup_time']='';
       // exit($request['pickup_time']);
        // dd($request->all());
        // init output data array

        //$request['pickup_time'] = Datetime::convertUTCToLocal($request['pickup_time'],Datetime::getTimeZone());

        $this->_apiData['data'] = $data = array();

        //$data =  $this->_entityModel->post($request);
        $data = $this->_pLib->apiPost($request->all());

        if ($data['response'] == 'error') {
            return $this->__apiResponse($request, $data);
        }

        $this->_apiData['response'] = 'success';
        // init attributes
       // $data = (object)array($data['data']->object_key => $data['data']);

        $this->_apiData['message'] = trans('system.success');

        // assign to output
        $this->_apiData['data'] =  (object)$data['data'];

        return $this->__apiResponse($request, $this->_apiData);
    }


    /**
     * Create
     *
     * @return Response
     */
    public function postOld(Request $request)
    {
        // extra models
        $depend_entity = [];
        $SYSEntityAuthModel = $this->_modelPath . "SYSEntityAuth";
        $SYSEntityAuthModel = new $SYSEntityAuthModel;
        $request_params = $request->all();

        $obj = new EntityTrigger();
        $obj->entityRequest = $request_params;

        if (isset($request_params['depend_entity'])) {
            $depend_entity = $request_params['depend_entity'];
            unset($request_params['depend_entity']);
            $request->replace($request_params);
        }

        $request->entity_auth_id = isset($request->entity_auth_id) ? $request->entity_auth_id : 0;
        $listOfAttributeToBeValidate = $listOfAttributeToBeInserted = array();

        if (isset($request->entity_type_id) && is_numeric($request->entity_type_id)) {

            //Trigger for entity type before post and validate
            $func = $this->__convertToCamel($this->_entityTypeData->identifier . '_before_post_trigger');
            if (method_exists($obj, "$func")) {
                $before_add_trigger_data = $obj->$func($request->all());
                if ($before_add_trigger_data) {
                    $request->request->add($before_add_trigger_data);
                    $request_params = array_merge($request_params, $before_add_trigger_data);
                }

            }

            $listOfAttributeToBeValidate = $this->_entityAttributeModel->getEntityAttributeValidationList($request->entity_type_id, '');
            $listOfAttributeToBeInserted = $this->_entityAttributeModel->getEntityAttributeFields($request->entity_type_id);
            $response_validator = $this->_postValidator($request_params, $listOfAttributeToBeValidate);
            if ($response_validator)
                return $this->__ApiResponse($request, $this->_apiData);

            $func = $this->__convertToCamel($this->_entityTypeData->identifier . '_verify_trigger');
            if (method_exists($obj, "$func")) {
                $verify_response = $obj->$func($request->all());
                if ($verify_response['error'] == TRUE) {
                    $this->_apiData['message'] = $verify_response['message'];

                    return $this->__ApiResponse($request, $this->_apiData);
                }
            }
        }

        $listOfDependentAttributeToBeValidate = $listOfDependentfAttributeToBeInserted = array();
        if (isset($this->_entityTypeData->depend_entity_type) && !empty($this->_entityTypeData->depend_entity_type)) {
            $listOfDependentAttributeToBeValidate = $this->_entityAttributeModel->getEntityAttributeValidationList($this->_entityTypeData->depend_entity_type, '');
            $listOfDependentfAttributeToBeInserted = $this->_entityAttributeModel->getEntityAttributeFields($this->_entityTypeData->depend_entity_type);
        }

        // validation
        if (isset($this->_entityTypeData->depend_entity_type) && !empty($this->_entityTypeData->depend_entity_type)) {
            $this->dependent_entity_type_data = $this->entity_type_data = $this->_getEntityTypeById($this->_entityTypeData->depend_entity_type);
            $func = $this->__convertToCamel($this->dependent_entity_type_data->identifier . '_verify_trigger');
            foreach ($depend_entity as $depend_entity_row) {
                $depend_entity_row['entity_type_id'] = $this->dependent_entity_type_data->entity_type_id;
                $response_validator = $this->_postValidator($depend_entity_row, $listOfDependentAttributeToBeValidate);
                if ($response_validator)
                    return $this->__ApiResponse($request, $this->_apiData);
                if (method_exists($obj, "$func")) {
                    $verify_response = $obj->$func($request->all());
                    if ($verify_response['error'] == TRUE) {
                        $this->_apiData['message'] = $verify_response['message'];

                        return $this->__ApiResponse($request, $this->_apiData);
                    }
                }
            }
            $this->entity_type_data = $this->_entityTypeData;
            $func_dependent = $this->__convertToCamel($this->_entityTypeData->identifier . '_dependent_verify_trigger');
            if (method_exists($obj, "$func_dependent")) {
                $request_params = $request->all();
                $request_params['depend_entity'] = $depend_entity;
                $request->replace($request_params);
                $verify_response = $obj->$func_dependent($request);
                if ($verify_response['error'] == TRUE) {
                    $this->_apiData['message'] = $verify_response['message'];

                    return $this->__ApiResponse($request, $this->_apiData);
                }
                $depend_entity = $request_params['depend_entity'];
                unset($request_params['depend_entity']);
                $request->replace($request_params);
            }
        }

        $response_post = $this->_post($request, $listOfAttributeToBeValidate, $listOfAttributeToBeInserted);

        if (isset($this->_entityTypeData->wft_id)) {
            $func_wfs = $this->__convertToCamel($this->_entityTypeData->identifier . '_wfs_trigger');
            $obj->$func_wfs($request->all(), $response_post, $this->_entityTypeData->wft_id);
        }
        // extra implementation to supportive fields or tables.
        $func = $this->__convertToCamel($this->_entityTypeData->identifier . '_add_trigger');
        if (method_exists($obj, "$func"))
            $obj->$func($request, $response_post);

        if (isset($this->_entityTypeData->depend_entity_type) && !empty($this->_entityTypeData->depend_entity_type))
            $this->_postDependentEntity($request, $depend_entity, $listOfDependentAttributeToBeValidate, $listOfDependentfAttributeToBeInserted);

        $request_params['depend_entity'] = $depend_entity;
        $request->replace($request_params);

        $func = $this->__convertToCamel($this->_entityTypeData->identifier . '_dependent_add_trigger');
        if (method_exists($obj, "$func"))
            $obj->$func($request);

        return $response_post;
    }


    /**
     * Post dependent entity (private)
     *
     * @param Request $request
     * @param $depend_entity
     * @param $listOfDependentAttributeToBeValidate
     * @param $listOfDependentfAttributeToBeInserted
     */
    private function _postDependentEntity(Request $request, $depend_entity, $listOfDependentAttributeToBeValidate, $listOfDependentfAttributeToBeInserted)
    {
        $obj = new EntityTrigger();
        $obj->entityRequest = $request->all();
        $identifier = $this->_entityTypeData->identifier . '_id';

        if (isset($this->_apiData['data']->entity))
            $entity_id = $this->_apiData['data']->entity->entity_id;
        else
            $entity_id = $this->_apiData['data']->{$this->_entityTypeData->identifier}->entity_id;
        $this->entity_type_data = $this->dependent_entity_type_data;
        foreach ($depend_entity as $depend_entity_row) {
            $depend_entity_row[ $identifier ] = $entity_id;
            $depend_entity_row['entity_type_id'] = $this->dependent_entity_type_data->entity_type_id;
            $request->replace($depend_entity_row);
            $response_dependent_post = $this->_post($request, $listOfDependentAttributeToBeValidate, $listOfDependentfAttributeToBeInserted);
            $func = $this->__convertToCamel($this->dependent_entity_type_data->identifier . '_add_trigger');

            if (method_exists($obj, "$func"))
                $obj->$func($request, $depend_entity_row);
        }
    }

    /**
     * Post validator (private)
     *
     * @param $request_params
     * @param $listOfAttributeToBeValidate
     * @return bool
     */
    private function _postValidator($request_params, $listOfAttributeToBeValidate)
    {
        $is_error = FALSE;
        $rules = array(
            'entity_type_id' => 'required|integer|exists:' . $this->_entityTypeModel->table . "," . $this->_entityTypeModel->primaryKey,
            // 'identifier' => 'string', //required|unique:' . $this->_entityModel->table . ',identifier
            //  $SYSEntityAuthModel->primarKey => 'integer|exists:' . $SYSEntityAuthModel->table . "," . $SYSEntityAuthModel->primaryKey . ",deleted_at,NULL",
        );

        $attributes_error = 1;
        if (isset($listOfAttributeToBeValidate[0])) {
            foreach ($listOfAttributeToBeValidate as $result) {

                //Combine validation of entity attribute with other validation
                if (!empty($result->js_validation_tags)) {
                    if (!empty($result->validation))
                        $result->validation .= '|' . $result->js_validation_tags;
                    else
                        $result->validation = $result->js_validation_tags;
                }

                if (isset($request_params[ $result->attribute_code ])) {
                    if ($result->php_data_type != 'comma_separated') {
                        $rules[ $result->attribute_code ] = $result->validation;
                    } else {
                        $temp = explode('|in', $result->validation);
                        $rules[ $result->attribute_code ] = $temp[0];
                    }
                }


            }

            $attributes_error = 0;
        }


        $validator = Validator::make($request_params, $rules);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
            $is_error = TRUE;
        } else if ($attributes_error > 0) {
            $this->_apiData['message'] = "No attributes defined";
            $is_error = TRUE;
        }

        return $is_error;
    }

    /**
     * Post (private)
     *
     * @param Request $request
     * @param $listOfAttributeToBeValidate
     * @param $listOfAttributeToBeInserted
     * @return \App\Http\Controllers\Response
     */
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
                $entityTypeData = $this->entity_type_data;
                if ($this->_mobile_json) $identifier = $entityTypeData->identifier;
            }

            $entity = array();

            $entity["created_at"] = date("Y-m-d H:i:s");

            //Map values to attribute field
            foreach ($listOfAttributeToBeInserted as $key => $result) {
                if (isset($result->model) && ($result->model == 'sys_entity')) {
                    $entity[ $result->attribute_code ] = $request->{$result->attribute_code};
                }
            }
            $entity["entity_auth_id"] = isset($request->entity_auth_id) ? $request->entity_auth_id : 0;

            $entity_id = $this->_entityModel->put($entity);
            //print_r($listOfAttributeToBeInserted); exit;
            //Map values to attribute field
            foreach ($listOfAttributeToBeInserted as $key => $result) {

                if (isset($result->attribute_code)) {
                    if (array_key_exists($result->attribute_code, $request->all()))
                        if ($result->model != 'sys_entity') {
                            $table = $result->model;

                            if ($result->php_data_type == 'comma_separated') {
                                foreach (explode(',', $request->{$result->attribute_code}) as $temp) {
                                    $listOfAttributeToBeInserted[ $key ]->entity_id = $entity_id;
                                    $listOfAttributeToBeInserted[ $key ]->value = $temp;
                                    unset($result->attribute_code, $result->model, $result->attribute_set_id, $result->php_data_type, $result->default_value);
                                    \DB::table($table)->insert((array)$listOfAttributeToBeInserted[ $key ]);
                                }

                            } else {
                                //print $result->attribute_code."\n";
                                $listOfAttributeToBeInserted[ $key ]->entity_id = $entity_id;
                                $listOfAttributeToBeInserted[ $key ]->value = $request->{$result->attribute_code};
                                unset($result->attribute_code, $result->model, $result->attribute_set_id, $result->php_data_type, $result->default_value);
                                \DB::table($table)->insert((array)$listOfAttributeToBeInserted[ $key ]);
                            }

                        }
                } else //Values having value null
                {
                    if ($result->model != 'sys_entity') {
                        $table = $result->model;
                        $listOfAttributeToBeInserted[ $key ]->entity_id = $entity_id;
                        $listOfAttributeToBeInserted[ $key ]->value = '';
                        unset($result->attribute_code, $result->model, $result->attribute_set_id, $result->php_data_type);
                        \DB::table($table)->insert((array)$listOfAttributeToBeInserted[ $key ]);
                    }
                }
            }


            //This variable is use to get attribute having list of values like Gender( male/ female )
            $dataTypesHavingSelectedValues = \DB::table('sys_attribute')->selectRaw('group_concat(attribute_code) as listOfAttr')->whereIn('data_type_id', [5, 9, 11, 12])->first();


            $entity_type = $this->_entityTypeModel->getData($entity['entity_type_id']);
            if ($entity_type->use_flat_table == "1") {
                $flat_entity = array('entity_id' => $entity_id);
                $flat_fields = array('entity_id');
                foreach ($listOfAttributeToBeValidate as $field) {
                    $flat_fields[] = $field->attribute_code;

                    if (!$request->{$field->attribute_code}) {
                        if ($field->default_value != "" && !empty($field->default_value))
                            $flat_entity[ $field->attribute_code ] = $field->default_value;
                    } else if ($request->{$field->attribute_code})
                        if (in_array($field->attribute_code, array_unique(explode(',', $dataTypesHavingSelectedValues->listOfAttr))))

                            if (strpos($request->{$result->attribute_code}, ',') === FALSE) {

                                if (strpos($request->{$result->attribute_code}, ',') === FALSE) {
                                    $string = '';
                                    foreach (array_unique(explode(',', $request->{$field->attribute_code})) as $temp) {
                                        $string .= $temp . ',';
                                    }
                                    $flat_entity[ $field->attribute_code ] = rtrim($string, ',');

                                }
                            } elseif ($field->use_entity_type == 1 && strpos($request->{$result->attribute_code}, ',') === TRUE) {
                                $flat_entity[ $field->attribute_code ] = $request->{$field->attribute_code};
                            } elseif ($field->use_entity_type == 0 && $field->linked_entity_type_id != 0) {
                                $flat_entity[ $field->attribute_code ] = $request->{$field->attribute_code};
                            } else {
                                $entity_value = \DB::table('sys_attribute_option')->select('option')
                                    ->where('value', $request->{$field->attribute_code})
                                    ->where('attribute_id', $field->attribute_id)
                                    ->first();
                                if ($entity_value) {
                                    $flat_entity[ $field->attribute_code ] = $entity_value->option;
                                } else {
                                    $flat_entity[ $field->attribute_code ] = '';
                                }

                            }

                        else
                            $flat_entity[ $field->attribute_code ] = $request->{$field->attribute_code};

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
                    $this->_attachmentModel->updateAttachmentByEntityID($entity_id, $attachments, $gallery_featured_item);
                }
                $record->gallery = array();
                $record->gallery = $this->_attachmentModel->getAttachmentByEntityID($entity_id);
            }

            // init attributes
            //$data[$identifier] = $record;
            $data = $this->_entityModel->getEntityData($request);
            if ($data) {
                $data2 = $data;
                unset($data);
                $data = (object)array($identifier => $data2);
            } else {
                $data = (object)array($identifier => $data);
            }

            if ($entity_type->entity_type_id == 20) {
                $func = 'calculateCustomerCart';//$this->__convertToCamel($this->dependent_entity_type_data->identifier.'_add_trigger');
                $obj = new EntityTrigger();
                if (method_exists($obj, "$func"))
                    $obj->$func();

            }
            $this->_apiData['message'] = trans('system.success');

            // assign to output
            $data->identifier = $identifier; // attach identifier
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * @param $entity_type_id
     * @return bool
     */
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
    {
        // init output data array
        $this->_apiData['data'] = $data = array();

        //$data =  $this->_entityModel->post($request);
        $data = $this->_pLib->apiUpdate($request->all());

        if ($data['response'] == 'error') {
            return $this->__apiResponse($request, $data);
        }

        $this->_apiData['response'] = 'success';
        // init attributes
        $data = (object)array($data['data']->object_key => $data['data']);

        $this->_apiData['message'] = trans('system.success');

        // assign to output
        $this->_apiData['data'] = $data;

        return $this->__apiResponse($request, $this->_apiData);
    }


    /**
     * Update
     *
     * @return Response
     */
    public function saveOld(Request $request)
    {
        $request_params = $request->all();
        $depend_entity = [];
        if (isset($request_params['depend_entity'])) {
            $depend_entity = $request_params['depend_entity'];
            unset($request_params['depend_entity']);
            $request->replace($request_params);
        }

        $obj = new EntityTrigger();

        if (isset($request->entity_type_id) && is_numeric($request->entity_type_id)) {

            //Trigger for entity type before post and validate
            $func = $this->__convertToCamel($this->_entityTypeData->identifier . '_before_save_trigger');
            if (method_exists($obj, "$func")) {
                $before_save_trigger_data = $obj->$func($request->all());
                if ($before_save_trigger_data) {
                    $request->request->add($before_save_trigger_data);
                    $request_params = array_merge($request_params, $before_save_trigger_data);
                }
            }

            $listOfAttributeToBeValidate = $this->_entityAttributeModel->getEntityAttributeValidationListForUpdate($request->entity_type_id, $request->entity_id);
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

        $func = $this->__convertToCamel($this->_entityTypeData->identifier . '_verify_trigger');

        if (method_exists($obj, "$func")) {
            $verify_response = $obj->$func($request->all());
            if ($verify_response['error'] == TRUE) {
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
                $entity[ $result->attribute_code ] = $request->{$result->attribute_code};
            }
        }

        //if user management then first get entity auth id then update entity
        if ($entityTypeData->allow_auth == 1 && $entityTypeData->allow_backend_auth == 1) {
            $entity_auth = $this->_SYSEntityAuth->entityQuery($request->entity_type_id)
                ->where("entity." . $this->_entityPk, "=", $request->{$this->_entityPk})
                ->select("auth." . $this->_SYSEntityAuth->primaryKey)
                ->first();

            if (isset($entity_auth->{$this->_SYSEntityAuth->primaryKey})) {
                $entity[ $this->_SYSEntityAuth->primaryKey ] = $entity_auth->{$this->_SYSEntityAuth->primaryKey};
            }
        }


        $entity_id = $this->_entityModel->set($request->entity_id, $entity);

        $entity_id = $request->entity_id;

        //Get Entity Data before update
        $obj->_entityData = $this->_entityModel->getData($entity_id);

        //Map values to attribute field

        $func = $this->__convertToCamel($this->_entityTypeData->identifier . '_update_alternate');
        if (method_exists($obj, "$func")) {
            $obj->$func($request->all(), $depend_entity);
        } else {
            $dataTypesHavingSelectedValues = \DB::table('sys_attribute')->selectRaw('group_concat(attribute_code) as listOfAttr')->whereIn('data_type_id', [5, 9, 11, 12])->first();
            foreach ($listOfAttributeToBeInserted as $result) {
                if (isset($request->{$result->attribute_code}) && $result->model != 'sys_entity') {
                    //DeleteExtraCode

                    if ($result->php_data_type == 'comma_separated') {
                        if (in_array($result->attribute_code, explode(',', $dataTypesHavingSelectedValues->listOfAttr))) {
                            $table = $result->model;
                            $string = '';
                            //Delete entity attribute data
                            $this->_entityModel->deleteAttribute($entity_id, $result->attribute_id, $table);

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
                    if (isset($request->{$field->attribute_code})) {
                        $flat_fields[] = $field->attribute_code;
                        $flat_entity[ $field->attribute_code ] = $request->{$field->attribute_code};
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
                $obj->$func($request->all());

            //if entity type is user management then update entity role
            if ($entityTypeData->allow_auth == 1 && $entityTypeData->allow_backend_auth == 1) {
                //update role_id
                if (isset($request->role_id)) {
                    $role_map_model = $this->_modelPath . "SYSEntityRoleMap";
                    $role_map_model = new $role_map_model;

                    $update_param = array();
                    $update_param['role_id'] = $request->role_id;
                    $update_param['entity_id'] = $request->entity_id;
                    $role_map_model->where('entity_id', '=', $entity_id)->update($update_param);

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
                $this->_attachmentModel->deleteAttachmentByEntityID($entity_id);
                $this->_attachmentModel->updateAttachmentByEntityID($entity_id, $attachments, $gallery_featured_item);
            }
            //$record->gallery = array();
            //  $record->gallery = $this->_attachmentModel->getAttachmentByEntityID($entity_id);
        }

        //$data[$this->_objectIdentifier] = $this->_entityModel->getData($entity_id);
        $data = $this->_entityModel->getData($entity_id);
        // message
        $this->_apiData['message'] = trans('system.success');

        if ($data) {
            $data2 = $data;
            unset($data);
            $data = (object)array($identifier => $data2);
        } else {
            $data = (object)array($identifier => $data);
        }
        // assign to output
        $data->identifier = $identifier; // attach identifier
        $this->_apiData['data'] = $data;


        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Save validator  (private)
     *
     * @param Request $request
     * @param $listOfAttributeToBeValidate
     * @return bool
     */
    private function _saveValidator(Request $request, $listOfAttributeToBeValidate)
    {
        $this->_apiData['error'] = 0;
        $rules = array(
            'entity_type_id' => 'integer|exists:' . $this->_entityTypeModel->table . "," . $this->_entityTypeModel->primaryKey . ",deleted_at,NULL",
            'entity_id' => 'integer|exists:' . $this->_entityModel->table . "," . $this->_entityPk . ",deleted_at,NULL"
        );

        if ($this->entity_type_data->allow_auth == 1 && $this->entity_type_data->allow_backend_auth == 1) {
            if ($this->entity_type_data->identifier != "customer" && !(isset($request->is_profile_update))) {
                $rules['role_id'] = 'required';
            }

        }

        $request_params = $request->all();
        foreach ($listOfAttributeToBeValidate as $result) {

            if (isset($request_params[ $result->attribute_code ])) {
                if (strpos($request_params[ $result->attribute_code ], ',') === TRUE) {
                    $rules[ trim($result->attribute_code) ] = $result->validation;
                } else {
                    $temp = explode('|in', $result->validation);
                    $rules[ $result->attribute_code ] = $temp[0];
                }
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

            return FALSE;
        } else if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
            $this->_apiData['error'] = 1;

            return FALSE;
        }

        return TRUE;
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
            $this->_entityModel->deleteEntityData($request->{$this->_entityPk});

            if ($record) {
                $entity_type = $this->_entityTypeModel->getData($record->entity_type_id);

                if($entity_type->allow_auth == 1 || $entity_type->allow_backend_auth == 1){
                    if(isset($record->entity_auth_id) && $record->entity_auth_id > 0)
                    $this->_SYSEntityAuth->remove($record->entity_auth_id);
                }

                if (isset($entity_type->depend_entity_type) && !empty($entity_type->depend_entity_type)) {
                    $entity_type_dependent = $this->_entityTypeModel->getData($entity_type->depend_entity_type);
                    $temp = \DB::table($entity_type_dependent->identifier . '_flat')->where($entity_type->identifier . '_id', '=', $record->entity_id)->get();
                    foreach ($temp as $entities) {
                        $data['entity_id'] = $entities->entity_id;

                        $response = CustomHelper::internalCall($request, "api/system/entities/delete", 'POST', $data, FALSE);

                    }
                }

                if ($entity_type->use_flat_table == "1") {
                    // remove from flat
                    if (\Schema::hasTable($entity_type->identifier . '_flat')) {
                        // remove from flat table

                        \DB::table($entity_type->identifier . '_flat')->
                        where('entity_id', '=', $record->entity_id)
                            ->delete();
                    }
                }
            }

            $this->_apiData['message'] = trans('system.entity_delete_success');
            // response data
            // assign to output
            $this->_apiData['data'] = $data;

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Get
     *
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function get(Request $request)
    {
        // init output data array
        $this->_apiData['data'] = $data = array();

        //$data =  $this->_entityModel->post($request);
        $data = $this->_pLib->apiGet($request->all());

        if ($data['response'] == 'error') {
            return $this->__apiResponse($request, $data);
        }

        $this->_apiData['response'] = 'success';
        // init attributes
      // $data = (object)array($data['data']->object_key => $data['data'], 'identifier' => $data['data']->object_key);

        $this->_apiData['message'] = trans('system.success');

        // assign to output
        $this->_apiData['data'] = $data['data'];

        return $this->__apiResponse($request, $this->_apiData);
    }

    /**
     * User data
     *
     * @return Response
     */
    public function getOld(Request $request)
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
            $this->_apiData['message'] = trans('system.pls_enter_entity_id', array("entity" => $this->_objectIdentifier));
        } else {
            $this->_apiData['response'] = "success";

            $data = $this->_entityModel->getData($request->{$this->_entityPk});

            $entityTypeData = $this->_entityTypeModel->getEntityTypeById($data->entity_type_id);

            if ($request->mobile_json)
                $identifier = $entityTypeData->identifier;
            else
                $identifier = 'entity';

            // message
            $this->_apiData['message'] = trans('system.success');
            if (isset($request->hook)) {
                $hooks = explode(',', $request->hook);
                $temp = array();
                foreach ($hooks as $hook) {
                    if (\Schema::hasTable($hook . '_flat')) {
                        $entity_values = \DB::table($hook . '_flat')->select('*')
                            ->where($entityTypeData->identifier . '_id', $request->{$this->_entityPk})
                            ->get();
                        if (count($entity_values))
                            foreach ($entity_values as $entity_value) {
                                $temp[] = $this->_entityModel->getData($entity_value->entity_id);
                            }

                        $data->{$hook} = $temp;
                    }
                }

            }


            // extra implementation to supportive fields or tables.
            if (isset($entityTypeData->identifier)) {

                $obj = new EntityTrigger();
                $func = $this->__convertToCamel($entityTypeData->identifier . '_get_trigger');

                if (method_exists($obj, "$func"))
                    $listing_trigger = $obj->$func($request->all());

                if (isset($request->entity_id)) {
                    if (isset($listing_trigger['key']) && isset($listing_trigger['data'])) {
                        $data->{$listing_trigger['key']} = $listing_trigger['data'];
                    }
                }

            }

            // assign to output
            $this->_apiData['data']['identifier'] = $identifier; // attach identifier
            $this->_apiData['data'][ $identifier ] = $data;
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
    public function entityCounts($entity_type_id, $fields, $returnID = FALSE)
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

        // init output data array
        $this->_apiData['data'] = $data = array();

        //$data =  $this->_entityModel->post($request);
        $data = $this->_pLib->apiList($request->all());

        if ($data['response'] == 'error') {
            return $this->__apiResponse($request, $data);
        }

        $this->_apiData['response'] = 'success';
        // init attributes
        $data = $data['data'];

        $this->_apiData['message'] = trans('system.success');

        // assign to output
        $this->_apiData['data'] = $data;

        return $this->__apiResponse($request, $this->_apiData);
    }

    /**
     * Listin / Search
     *
     * @return Response
     */
    public function listingOld(Request $request)
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
            // echo "<pre>"; print_r($response);exit;
            $response['data'] = isset($response['data']) ? $response['data'] : "";

            // extra implementation to supportive fields or tables.
            if (isset($this->_entityTypeData->identifier)) {

                $obj = new EntityTrigger();
                $func = $this->__convertToCamel($this->_entityTypeData->identifier . '_listing_trigger');

                if (method_exists($obj, "$func"))
                    $listing_trigger = $obj->$func($request->all());

                if (isset($request->entity_id)) {
                    if (isset($listing_trigger['key']) && isset($listing_trigger['data'])) {
                        $response['data'][0]->{$listing_trigger['key']} = $listing_trigger['data'];
                    }
                }

            }

            $data[ $identifier ] = $response['data'];
            if ($this->_mobile_json) {
                $data["page"] = array(
                    //"offset" => $offset,
                    "limit" => $params['limit'],
                    "total_records" => $response['total_records'],
                    //"next_offset" => ($offset + $limit), // new pagination flow
                    "next_offset" => $response['next_offset'],
                    //"prev_offset" => $offset > 0 ? ($offset - $limit) : $offset, // new pagination flow,
                    "prev_offset" => $params['offset']
                );
            } else {
                $data["page"] = array(
                    "offset" => $params['offset'],
                    "limit" => $params['limit'],
                    "total_records" => $response['total_records'],
                    "next_offset" => ($params['offset'] + $params['limit']),
                    "prev_offset" => $params['offset'] > 0 ? ($params['offset'] - $params['limit']) : $params['offset'],
                );
            }

            $return = $data;

            //list data if hooks are entity type
            if (isset($request->list_hook)) {
                $hooks = explode(',', $request->list_hook);
                if (count($hooks) > 0) {

                    unset($return);
                    $return[ $identifier ] = $data;
                    $limit = isset($request->list_hook_limit) ? $request->list_hook_limit : 5;

                    foreach ($hooks as $hook) {
                        $return[ $hook ] = $this->_entityModel->listHookData($request, $hook, $limit);
                    }

                }
            }


            $this->_apiData['data'] = $return;
            $this->_apiData['message'] = trans('system.success');
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

            //  print_r($response);exit;

            $this->_apiData['message'] = trans('system.success');
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Search
     *
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
     *
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


    /**
     * Get wishlist
     *
     * @param Request $request
     * @return array|string
     */
    public function listWishlist(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $data = array();
        $data['actor_entity_id'] = $customer_id;
        $data['target_entity_type_id'] = 14;
        $data['actor_entity_type_id'] = 11;
        $data['type'] = "private";
        $data['mobile_json'] = 1;

        $response = json_encode(CustomHelper::internalCall($request, "api/extension/social/package/like/listing", 'GET', $data, FALSE));
        $json = json_decode($response, TRUE);

        if ($json['error'] == 0) {
            $wishlist = isset($json["data"]["like_listing"]) ? $json["data"]["like_listing"] : NULL;
            $product_id = array();
            foreach ($wishlist as $products) {
				
                if (isset($products['product']['entity_id']))
					if( ($products['product']['status']['value']==1) && ($products['product']['availability']['value']==1))
					{
							$product_id[] = $products['product']['entity_id'];
					}
					
            }
            $product_id = implode(", ", $product_id);
            $data = array();
            $data['products'] = $product_id;
            $this->_apiData['data'] = $data;

            $json = $this->__ApiResponse($request, $this->_apiData);

            if (isset($json['jsonEditor'])) {
                $jsonEditor = json_decode($json['jsonEditor'], TRUE);
                $jsonEditor['error'] = 0;
                $response = array();
                $response['targetElem'] = 'pre[id=response]';
                $response['jsonEditor'] = json_encode($jsonEditor);

                return $response;
            } else {
                $json['error'] = 0;

                return json_encode($json);
            }

        } else {
            $data = array();
            $data['products'] = "";
            $this->_apiData['data'] = $data;
            $json = $this->__ApiResponse($request, $this->_apiData);
            if (isset($json['jsonEditor'])) {
                $jsonEditor = json_decode($json['jsonEditor'], TRUE);
                $jsonEditor['error'] = 1;
                $response = array();
                $response['targetElem'] = 'pre[id=response]';
                $response['jsonEditor'] = json_encode($jsonEditor);

                return $response;
            } else {
                $json['error'] = 1;

                return json_encode($json);
            }
        }

    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function sendReferCode(Request $request)
    {
        // validations
        $error_messages = array(
          'email.unique' => "Sorry registered email can not be refer"
        );

        $validator = Validator::make($request->all(), array(
            'entity_id' => 'required|integer|exists:' . $this->_entityModel->table . "," . $this->_entityPk . ",deleted_at,NULL",
            'email' => 'required|email|unique:' .  $this->_SYSEntityAuth->table . ',email,NULL,deleted_at,is_verified,1',
        ),$error_messages);

        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $entity = $this->_entityModel->getData($request->{$this->_entityModel->primaryKey});
            // print_r($entity); exit;
            if (!$entity) {
                $this->_apiData['message'] = trans('system.entity_is_invalid', array("entity" => "Customer"));
            } else if ((!isset($entity->refer_friend_code)) || (isset($entity->refer_friend_code) && empty($entity->refer_friend_code))) {
                $this->_apiData['message'] = trans('system.refer_friend_code_not_found', array("entity" => "Customer"));
            } else {
                // success response
                $this->_apiData['message'] = $this->_apiData['response'] = "success";

                $conf_model = new Conf;
                // $setting_model = new Setting;
                $email_template_model = new EmailTemplate;

                // configuration
                $conf = $conf_model->getBy('key', 'site');
                $conf = json_decode($conf->value);
                // send email

                # load email template
                $query = $email_template_model
                    ->where("key", "=", 'send_refer_code')
                    ->whereNull("deleted_at")
                    ->whereNull("plugin_identifier");

                $email_template = $query->first();

                // dir_path
                $web_link = url('/');
                $app_download_link = url('/');

                $name = $entity->first_name;
                if (isset($entity->last_name)) {
                    if (!empty($entity->last_name)) {
                        $name .= '';
                        $name .= $entity->last_name;
                    }
                }

                if (isset($entity->full_name)) {
                    if (!empty($entity->full_name)) {
                        $name = $entity->full_name;
                    }
                }

                # prepare wildcards [APP_NAME],[WEB_URL],[APP_DOWNLOAD_LINK],[REFER_CODE],[USERNAME_REFERRAL]
                $wildcard['key'] = explode(',', $email_template->wildcards);
                $wildcard['replace'] = array(
                    $conf->site_name,
                    $web_link,
                    $app_download_link,
                    $entity->refer_friend_code,
                    $name
                );

                # subject
                $entity->subject = str_replace($wildcard['key'], $wildcard['replace'], $email_template->subject);
                # body
                $body = str_replace($wildcard['key'], $wildcard['replace'], $email_template->body);

                # send email
                $this->_entityModel->sendMail(
                    array($request->email, $entity->first_name),
                    $body,
                    (array)$entity
                );

            } //end of success block

        } //end of validation

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Get Customer current balance
     *
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function getCurrentBalance(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), array(
            'entity_id' => 'required|integer|exists:' . $this->_entityModel->table . "," . $this->_entityPk . ",deleted_at,NULL",
        ));

        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['message'] = $this->_apiData['response'] = "success";

            $wallet_transaction = new WalletTransaction();
            $current_balance = $wallet_transaction->getCurrentBalance($request->entity_id);

            $data = new \StdClass();
            $data->entity_id = $request->entity_id;
            $data->balance = $current_balance;

            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function validateCoupon(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), array(
           'coupon_code' => 'required|exists:coupon_flat,coupon_code,deleted_at,NULL',
            'customer_id' => 'required|integer|exists:customer_flat,' . $this->_entityPk . ',deleted_at,NULL',
            'order_amount' => 'required',
        ));

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            $coupon_lib = new CouponLib();
            $return = $coupon_lib->validateCoupon($request->all());

            //print_r($return);
            if($return['error'] == 1){
                $this->_apiData['error'] = 1;
                $this->_apiData['message'] = $return['message'];
            }
            else{
                $this->_apiData['response'] = "success";
                $this->_apiData['data'] = $return['data'];
            }
        }

            return $this->__ApiResponse($request, $this->_apiData);
        }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
        public function updateCart(Request $request)
        {
            $this->_apiData['error'] = 0;
            // validations
            $validator = Validator::make($request->all(), array(
                'customer_id' => 'required|integer|exists:customer_flat,' . $this->_entityPk . ',deleted_at,NULL',
            ));


            if ($validator->fails()) {
                $this->_apiData['error'] = 1;
                $this->_apiData['message'] = $validator->errors()->first();
            } else {
                $order_cart_lib = new OrderCart();
                $return = $order_cart_lib->saveCart($request->customer_id,$request->cart_item);

                //print_r($return);
                if($return['error'] == 1){
                    $this->_apiData['error'] = 1;
                    $this->_apiData['message'] = $return['message'];
                }
                else{

                    $return = $order_cart_lib->getOrderCart($request->customer_id);

                    $this->_apiData['response'] = "success";
                    $this->_apiData['data'] = $return['data'];
                }
            }

            return $this->__ApiResponse($request, $this->_apiData);

        }

        public function getCart(Request $request)
        {
            $this->_apiData['error'] = 0;
            // validations
            $validator = Validator::make($request->all(), array(
                'customer_id' => 'required|integer|exists:customer_flat,' . $this->_entityPk . ',deleted_at,NULL',
            ));


            if ($validator->fails()) {
                $this->_apiData['error'] = 1;
                $this->_apiData['message'] = $validator->errors()->first();
            } else {

                $params = $request->all();
                $params['entity_type_id'] = 'order_cart';

                $return = $this->_pLib->apiList($params);

               //print_r($return); exit;
                if($return['error'] == 1){
                    $this->_apiData['error'] = 1;
                    $this->_apiData['message'] = $return['message'];
                }
                else{

                    //print_r($return['data']['order_cart']); exit;
                    if(isset($return['data']['order_cart'][0]) && !empty($return['data']['order_cart'][0])){

                       // echo "<pre>"; print_r($return['data']['order_cart'][0]); exit;
                        $update_item = array();
                        if(isset($return['data']['order_cart'][0]->cart_item)) {

                            $cart_items = json_decode($return['data']['order_cart'][0]->cart_item);
                            if (count($cart_items) > 0) {

                                $entity_lib = new Entity();

                                foreach ($cart_items as $item) {

                                    $params = [
                                        'entity_type_id' => 14,
                                        'entity_id' => $item->product_id,
                                        'status' => 1,
                                        'availability' => 1,
                                        'mobile_json' => 1,
                                    ];
                                    // echo "<pre>"; print_r($params); exit;
                                    $product_information = $entity_lib->apiGet($params);

                                    if (isset($product_information['data']['product'])) {
                                        $item->detail = $product_information['data']['product'];
                                    } else {
                                        $item->detail = new \StdClass();
                                    }

                                    if (count($item->detail) > 0)
                                        $update_item[] = $item;
                                    unset($item);

                                }

                            }
                        }

                        $return['data']['order_cart'][0]->cart_item = $update_item;
                    }


                    $this->_apiData['response'] = "success";
                    $this->_apiData['data'] = $return['data'];
                }
            }

            return $this->__ApiResponse($request, $this->_apiData);
        }

}

<?php
/**
 * Summary : Entity Library
 * Created by PhpStorm.
 * User: Salman
 * Date: 12/28/2017
 * Time: 5:42 PM
 */

namespace App\Libraries\System;

use App\Http\Models\ApiMethodField;
use App\Http\Models\PLAttachment;
use App\Http\Models\SYSAttribute;
use App\Http\Models\SYSAttributeOption;
use App\Http\Models\SYSEntityAttribute;
use App\Http\Models\SYSEntityAuth;
use App\Http\Models\SYSEntityHistory;
use App\Http\Models\SYSTableFlat;
use App\Libraries\CustomHelper;
use App\Libraries\EntityHelper;
use App\Libraries\EntityTrigger;
use Illuminate\Support\Facades\Validator;

/**
 * Class Post
 */
Class Entity extends Base
{

    /**
     * Model set
     */
    private
        /**
         * Entity Auth Model
         */
        $_entityAuthModel,

        /**
         * Entity History Model
         */
        $_eHistoryModel,

        /**
         * Attachment Model
         */
        $_attachmentModel,

        /**
         * Attribute Option Model
         */
        $_attributeModel,

        /**
         * Attribute Option Model
         */
        $_attributeOptionModel,

        /**
         * Attribute Option Model
         */
        $_entityAttributeModel,

        $_attachmentTable = 'pl_attachment';

    /**
     * @var string
     */
    private $_objectIdentifier = 'entity';

    private $_eTypeData = NULL;

    /**
     * database
     */
    private $_dbLib;

    /**
     * @var array
     */
    protected $_apiData = ['response' => 'error'];


    private $_langIdentifier = 'system';
    /**
     * Constructor
     */
    public function __construct()
    {
        // init parent
        parent::__construct();

        // load models
        $this->_entityAuthModel = new SYSEntityAuth();
        $this->_eHistoryModel = new SYSEntityHistory();
        $this->_attachmentModel = new PLAttachment();
        $this->_attributeModel = new SYSAttribute();
        $this->_attributeOptionModel = new SYSAttributeOption();
        $this->_entityAttributeModel = new SYSEntityAttribute();

        // load libs
        $this->_dbLib = new Database();
    }


    public function getData($id = 0, $request_params = FALSE,$entityType = false,$attrs = false,$record = false)
    {
        $request_params = ($request_params && is_array($request_params)) ? (object)$request_params : $request_params;
        $is_mobile_request = isset($request_params->mobile_json) ? $request_params->mobile_json : FALSE;
        $lang = isset($request_params->_lang) ? $request_params->_lang : 'en';


        //for mobile services update in_Detail to 0 by default for sub entities
        $default_detail = ($is_mobile_request) ? 0 : 1;
        $requested_detail = $in_detail = (isset($request_params->in_detail)) ? $request_params->in_detail : $default_detail;
        $detail_key = (isset($request_params->detail_key) && !empty($request_params->detail_key)) ? explode(',',$request_params->detail_key) : false;

        if ($request_params && isset($request_params->request_parameter))
            $request_parameter = explode(',', $request_params->request_parameter);
        else
            $request_parameter = array();

        // row data
        $data = $this->_entityModel->get($id);

        if ($data) {

            // attach auth data (if exists)
            if ($data->{$this->_entityAuthModel->primaryKey} > 0) {

                $data->auth = $this->_entityAuthModel->getData(
                    $data->{$this->_entityAuthModel->primaryKey},
                    $data->{$this->_eTypeModel->primaryKey});

            }

           // if(!$is_mobile_request || (isset($request_params->gallery) && $request_params->gallery == 1)){
                $is_gallery = true;
           /* }else{
                $is_gallery = false;
            }*/

            if(!$entityType){
                $entityType = $this->_eTypeModel->get($data->{$this->_eTypeModel->primaryKey});
            }

            // get entity gallery (if attached)
            if ((in_array('gallery', $request_parameter) && !empty($request_parameter)) || empty($request_parameter)) {
                if ($entityType->show_gallery == '1') {
                    $data->gallery = [];
                    if($is_mobile_request){
                        $data->gallery = $this->_attachmentModel->getAttachmentByEntity($data->{$this->_entityModel->primaryKey});
                    }else{
                        $data->gallery = $this->_attachmentModel->getAttachmentByEntityID($data->{$this->_entityModel->primaryKey});
                    }

                }
            }

            //f attributes are not then fetch entity attributes
            if(!$attrs){
                $api_column = $is_mobile_request ? true : false;
                $api_fields = new ApiMethodField();
                $attrs = $api_fields->getEntityAttributeList($entityType->entity_type_id, false,$api_column);

            }

            //if record not then fetch record from flat table
            if(!$record){
                $flat_table_model = new SYSTableFlat($entityType->identifier);
                $where_condition = ' (deleted_at IS NULL AND entity_id = '.$id.')';
                $flat_raw = $flat_table_model->getDataByWhere($where_condition);
                if(isset($flat_raw[0])){
                    $record = $flat_raw[0];
                }

            }

            // if flat
            if($attrs && $record){

                foreach ($attrs as $field_action) {

                    //echo $field_action->attribute_code;
                     $field_action->name =  $field_action->attribute_code;

                    $value = "";
                     if(isset($record->{$field_action->attribute_code})){
                         $value = $record->{$field_action->attribute_code};
                     }

                    $field_action->attribute = $field_action->json_data = $value;

                     //check if in detail = 0 get detail of only requested attributes
                     if(!$in_detail){
                         if($detail_key && in_array($field_action->attribute_code,$detail_key)){
                             $in_detail = 1;
                         }
                     }

                    if ((in_array($field_action->name, $request_parameter) && !empty($request_parameter)) || empty($request_parameter))
                        if ($field_action->attribute_id) {
                            /*$attributeData = $this->_dbLib->query->table('sys_attribute')->select('*')
                                ->where('attribute_id', $field_action->attribute_id)
                                ->first();*/

                          //  echo "<pre>"; print_r( $field_action->attribute); continue;
                            if ($field_action->data_type_id == 20) {

                                if ($field_action->attribute)
                                    if (!$is_mobile_request)
                                        $data->attributes[ $field_action->name ] = $this->getData($field_action->attribute, array('mobile_json' => $is_mobile_request));
                                    else
                                        $data->{$field_action->name} = $this->getData($field_action->attribute, array('mobile_json' => $is_mobile_request));

                            }
                            else if ($field_action->linked_entity_type_id && $field_action->linked_attribute_id) {

                                $temp = array();
                                if ($field_action->data_type_id == 9 || $field_action->data_type_id == 5) {
                                    foreach (explode(",", $field_action->json_data) as $tempList) {
                                        $linkedAttribute = $this->getLinkedEntityAttributeValue($field_action->attribute_id, $tempList, $lang);
                                        if (count($linkedAttribute)) {
                                            if ($in_detail) $showDetail = $this->getData($linkedAttribute[0]->entity_id, array('mobile_json' => $is_mobile_request)); else $showDetail = '';
                                            $temp[] = array('id' => $linkedAttribute[0]->entity_id, 'value' => $linkedAttribute[0]->attribute, 'detail' => $showDetail);
                                        }
                                    }
                                } else {
                                    if(!empty($field_action->attribute)){

                                        $linkedAttribute = $this->getLinkedEntityAttributeValue($field_action->attribute_id, $field_action->attribute, $lang);
                                        if (count($linkedAttribute)) {
                                            if ($in_detail) $showDetail = $this->getData($linkedAttribute[0]->entity_id, array('mobile_json' => $is_mobile_request)); else $showDetail = '';
                                            $temp = array('id' => $linkedAttribute[0]->entity_id, 'value' => $linkedAttribute[0]->attribute, 'detail' => $showDetail);
                                        }
                                    }
                                }

                                if (!$is_mobile_request) {
                                    $data->attributes[ $field_action->name ] = $temp;
                                }else{
                                    $data->{$field_action->name} = $temp;
                                }

                               if (!$is_mobile_request) {
                                    //$tempp = $temp;
                                    //if entity type is product then also add recipe items
                                   /* if (isset($linkedAttribute[0]->entity_id)) {
                                        $attribute_data = $this->_getRequestedAttributeData($entityType->identifier, $field_action->name, $linkedAttribute[0]->entity_id);
                                        if ($attribute_data) {
                                            // print_r($attribute_data); exit;
                                            $tempp["$attribute_data->key"] = $attribute_data->value;
                                        }

                                    }*/

                                    $data->attributes[ $field_action->name ] = $temp;

                                } else {
                                    $tempp = $temp;
                                    //if entity type is product then also add recipe items
                                 /*   if (isset($linkedAttribute[0]->entity_id)) {
                                        $attribute_data = $this->_getRequestedAttributeData($entityType->identifier, $field_action->name, $linkedAttribute[0]->entity_id);
                                        if ($attribute_data) {
                                            // print_r($attribute_data); exit;
                                            $tempp["$attribute_data->key"] = $attribute_data->value;
                                        }

                                    }*/
                                    $data->{$field_action->name} = $tempp;
                                    //$data->{str_replace('_id', '', $field_action->name)} = $temp;
                                }

                            }
                            else if (isset($field_action->backend_table) && $field_action->backend_table != '') {

                                // category or role attached to any entity type sys_category, sys_role
                                $temp = explode("_", $field_action->backend_table);
                                $DymamicModel = $this->_modelPath . strtoupper($temp[0]);
                                if(isset($temp[1])){
                                    $DymamicModel .=   ucfirst($temp[1]);
                                }

                                if(class_exists($DymamicModel)){
                                    $model = new $DymamicModel;
                                }
                                else{
                                    $DymamicModel = $this->_modelPath . ucfirst($temp[0]);
                                    if(isset($temp[1])){
                                        $DymamicModel .=   ucfirst($temp[1]);
                                    }
                                    $model = new $DymamicModel;
                                }

                                unset($temp);

                                if ($field_action->data_type_id == 9 || $field_action->data_type_id == 5) {
                                    foreach (explode(",", $field_action->json_data) as $tempList) {

                                        if($field_action->backend_table == $this->_attachmentTable && $is_mobile_request){
                                            $temp[] = $model->getAttachmentGallery($tempList);
                                        }else{
                                            $temp[] = $model->getData($tempList);
                                        }

                                    }

                                    if (!$is_mobile_request)
                                        $data->attributes[ $field_action->name ] = $temp;
                                    else
                                        $data->{$field_action->name} = $temp;
                                } else {
                                    //print_r($field_action);
                                    if ($field_action->data_type_id == '6' || $field_action->data_type_id == '11' || $field_action->data_type_id == '4')

                                        if($field_action->backend_table == $this->_attachmentTable && $is_mobile_request){
                                          $backend_table_data  = $model->getAttachmentGallery($field_action->attribute);
                                        }
                                        else{
                                            $backend_table_data = $model->getData($field_action->attribute);
                                        }

                                        if (!$is_mobile_request)
                                            $data->attributes[ $field_action->name ] = $backend_table_data;
                                        else
                                            $data->{$field_action->name} = $backend_table_data;
                                }
                            }
                            else {
                                if ($field_action->linked_entity_type_id == 0 && $field_action->backend_table == '' && $field_action->data_type_id == 5) {

                                    $temp = array();

                                    //if you have multiple entity ids for different entity type like it may be customer, driver etc
                                    //then first get the entity type and attribute code
                                    //then get its value from flat table if there is detail then re call get Data
                                   if($field_action->use_entity_type == 1 && !empty($field_action->json_data)){

                                       foreach (explode(",", $field_action->json_data) as $tempList) {

                                           //get entity type and attribute
                                           $linked_entity_attr = $this->_entityAttributeModel->getEntityTypeAndDefaultAttribute($tempList);

                                           if(isset($linked_entity_attr->entity_type_id) && $linked_entity_attr->attribute_code){

                                               $flat_table = new SYSTableFlat($linked_entity_attr->entity_type_identifier);
                                               $linkedAttribute = $flat_table->getColumnByWhere(' entity_id = '.$tempList,$linked_entity_attr->attribute_code);

                                              //  $linkedAttribute = $this->getLinkedEntityAttributeValue($linked_entity_attr->attribute_id, $tempList, $lang);

                                               if (count($linked_entity_attr)) {
                                                   if ($in_detail) $showDetail = $this->getData($tempList, array('mobile_json' => $is_mobile_request)); else $showDetail = '';
                                                   //print_r($showDetail); exit;
                                                   $temp[] = array('id' => $tempList, 'value' => $linkedAttribute->{$linked_entity_attr->attribute_code}, 'detail' => $showDetail);
                                               }
                                           }

                                       }

                                   }else{
                                       //otherwise get from attribute options
                                       foreach (explode(",", $field_action->json_data) as $tempList) {
                                           $temp[] = $this->_attributeOptionModel->getAttributeById($field_action->attribute_id, $tempList);
                                       }
                                   }


                                    if (!$is_mobile_request)
                                        $data->attributes[ $field_action->name ] = $temp;
                                    else
                                        $data->{$field_action->name} = $temp;

                                } else if ($field_action->data_type_id == '6' || $field_action->data_type_id == '11' || $field_action->data_type_id == '4' || $field_action->data_type_id == '12') {

                                    if (!$is_mobile_request)
                                        $data->attributes[ $field_action->name ] = $this->_attributeOptionModel->getAttributeById($field_action->attribute_id, $field_action->attribute);
                                    else
                                        $data->{$field_action->name} = $this->_attributeOptionModel->getAttributeById($field_action->attribute_id, $field_action->attribute);
                                } else {
                                    if (!$is_mobile_request)
                                        $data->attributes[ $field_action->name ] = $field_action->attribute;
                                    else
                                        $data->{$field_action->name} = $field_action->attribute;
                                }

                            }

                        }

                        $in_detail = $requested_detail;
                }
            }

            // get identifier as per request
            if ($is_mobile_request > 0)
                $identifier = $entityType->identifier;
            else
                $identifier = 'entity';

            // object key
            $data->object_key = $identifier;
            // $data->identifier = $entityType->identifier;
            $data = json_decode(json_encode($data));

            if (isset($request_params->hook)) {
                $hooks = explode(',', $request_params->hook);

                foreach ($hooks as $hook) {
                    if (\Schema::hasTable($hook . '_flat')) {
                        $entity_values = $this->_dbLib->query->table($hook . '_flat')->select('*')
                            ->where($entityType->identifier . '_id', $id)
                            ->whereNull('deleted_at')
                            ->get();

                        if (count($entity_values)) {
                            //set condition for attributes detail
                            $detail_key = '';
                            $hook_detail_key = $hook . '_detail_key';
                            if (isset($request_params->{$hook_detail_key}) && !empty($request_params->{$hook_detail_key})) {
                                $detail_key = $request_params->{$hook_detail_key};
                            }

                            $request_param = ['mobile_json' => $is_mobile_request, 'in_detail' => $in_detail, 'detail_key' => $detail_key];

                            $hook_params = $hook . '_param';
                            if (isset($request_params->{$hook_params}) && !empty($request_params->{$hook_params})) {
                                $request_param['request_parameter'] = $request_params->{$hook_params};
                            }

                            //Get Entity Type
                            $entity_type = $this->_eTypeModel->getByIdentifier($hook);

                            //Get hook attributes
                            $api_column = $is_mobile_request ? TRUE : FALSE;
                            $api_fields = new ApiMethodField();
                            $hook_attrs = $api_fields->getEntityAttributeList($entity_type->entity_type_id, FALSE, $api_column);

                            $temp = [];
                            foreach ($entity_values as $entity_value) {
                              // $temp[] = $this->getData($entity_value->entity_id, ['mobile_json' => $is_mobile_request, 'in_detail' => $in_detail]);
                                $temp[] = $this->getData($entity_value->entity_id, $request_param, $entity_type, $hook_attrs, $entity_value);

                            }
                        }

                        if(isset($temp)){
                            $data->{$hook} = $temp;
                            unset($temp);
                        }

                    }
                }

            }


            // extra implementation to supportive fields or tables.
            if (isset($entityType->identifier)) {

                $obj = new EntityTrigger();
                $func = CustomHelper::convertToCamel($entityType->identifier . '_get_trigger');

                if (method_exists($obj, "$func"))
                    $listing_trigger = $obj->$func($request_params,$data);

                if (isset($request_params->entity_id) || !empty($id)) {
                    if (isset($listing_trigger['key']) && isset($listing_trigger['data'])) {
                        $data->{$listing_trigger['key']} = $listing_trigger['data'];

                    }
                }

            }

            // assign to output
            //$return['identifier'] = $identifier; // attach identifier
            // $return['data'][$identifier] = $data;
        }

        return $data;
    }


    /**
     * Get linked entity attribute value
     *
     * @param $attribute_id
     * @param $entity_id
     * @param $lang
     * @return array
     */
    function getLinkedEntityAttributeValue($attribute_id, $entity_id, $lang)
    {
        if(empty($entity_id) || empty($attribute_id))
            return NUll;

        // init data
        $data = [];
        $q = "SELECT
					REPLACE(
					REPLACE(
						REPLACE(
						  REPLACE(
							REPLACE(
							  REPLACE(
								sdt.`query`, 'ATTRIBUTE_ID_VALUE', ssa.`attribute_id`
							  ), 'ENTITY_ID_VALUE', " . $entity_id . "
							), 'ATTRIBUTE_CODE', CONCAT(\"'\", ssa.`attribute_code`, \"'\")
						  ), 'LINKED_ATTRIBUTE', CONCAT(
							\"'\", ssa.`linked_attribute_id`, \"'\", ''
						  )
						), 'LINKED_ENTITY_TYPE', CONCAT(
						  \"'\", ssa.linked_entity_type_id, \"'\", ''
						)
					  ), 'LANG_ID', '" . $lang . "') AS `query`
				FROM
				  `sys_attribute` sa
				  LEFT JOIN `sys_attribute` ssa
					ON ssa.`attribute_id` = sa.`linked_attribute_id`
				  LEFT JOIN sys_data_type sdt
					ON sdt.`data_type_id` = ssa.`data_type_id`
				WHERE sa.`attribute_id` = " . $attribute_id;
        //exit;
        $data = $this->_dbLib->query->select($q);
        if ($data[0]->query) {
            $data = \DB::select($data[0]->query);
        }

        // return
        return $data;
    }


    /**
     * Post validator
     *
     * @param $request_params
     * @return bool
     */
    private function _postValidator($request_params)
    {
        $request_params = is_array($request_params) ? (object)$request_params : $request_params;

        $listOfAttributeToBeValidate = $this->_entityAttributeModel->getEntityAttributeValidationList($request_params->entity_type_id, '');

        $is_error = FALSE;
        $rules = [
            'entity_type_id' => 'required|integer|exists:' . $this->_eTypeModel->table . "," . $this->_eTypeModel->primaryKey];

        $attributes_error = 1;
        $error_messages = array();
        if (isset($listOfAttributeToBeValidate[0])) {
            foreach ($listOfAttributeToBeValidate as $result) {

                //Combine validation of entity attribute with other validation
                if (!empty($result->js_validation_tags)) {
                    if (!empty($result->validation))
                        $result->validation .= '|' . $result->js_validation_tags;
                    else
                        $result->validation = $result->js_validation_tags;
                }

                if (isset($request_params->{$result->attribute_code})) {
                    if ($result->php_data_type != 'comma_separated') {
                            $rules[ $result->attribute_code ] = $result->validation;
                    } else {
                        $temp = explode('|in', $result->validation);
                            $rules[ $result->attribute_code ] = $temp[0];
                    }

                    //if service call from backend then update error Messages b/c error mesages are displaying attribute code
                    //but in message label should be display
                    if(!isset($request_params->mobile_json) || $request_params->mobile_json == 0){
                        $error_message = $this->_attributeErrorMessages($result,$rules);
                        $error_messages =  array_merge($error_messages,$error_message);
                    }

                }

            }

            $attributes_error = 0;
        }
      // echo "<pre>"; print_r( $rules);
      // echo "<pre>"; print_r( $error_messages);

        if(!isset($request_params->mobile_json) || $request_params->mobile_json == 0)
            $validator = Validator::make((array)$request_params, $rules,$error_messages);
        else
            $validator = Validator::make((array)$request_params, $rules);


        // validate
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
            //$this->_apiData['message'] = $validator->errors()->first();
            $is_error = TRUE;
        } else if ($attributes_error > 0) {
            throw new \Exception(trans('system.no_attribute_defined'));
            //$this->_apiData['message'] = "No attributes defined";
            $is_error = TRUE;
        }

        return $is_error;
    }


    /**
     * Post API response
     *
     * @param $request
     * @return array
     */
    public function apiPost($request)
    {
        try {
            $id = $this->doPost($request);
            $data = $this->getData($id, $request);
            $this->_apiData['data'] = array($data->object_key => $data, 'identifier' => $data->object_key);
            $this->_apiData['response'] = $this->_apiData['data'] ? trans($this->_langIdentifier.".success") : $this->_apiData['data'];
            $this->_apiData['message'] = trans($this->_langIdentifier.".success");

        } catch (\Exception $e) {
            //  echo $e->getTraceAsString(); exit;
            $this->_apiData['message'] = $e->getMessage();
          $this->_apiData['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine() . " : Stack " . $e->getTraceAsString();
        }

        // fix for error flags
        $this->_apiData['error'] = $this->_apiData['response'] == trans($this->_langIdentifier.".success") ? 0 : 1;

        return $this->_apiData;
    }


    /**
     * Post
     *
     * @param integer entity_type_id
     * @return NULL
     */
    public function doPost($request)
    {
        $request = is_object($request) ? $request : (object)$request;
        $request_params = (array)$request;
        $depend_entity = isset($request->depend_entity) ? ($request->depend_entity) : [];

        $obj = new EntityTrigger();
        $obj->entityRequest = (array)$request;

        if (isset($request_params['depend_entity'])) {
            $depend_entity = $request_params['depend_entity'];
            // fix depend entity type
            $depend_entity = is_string($depend_entity) && json_decode($depend_entity) != NULL ?
                json_decode($depend_entity, TRUE) : $depend_entity;

            unset($request_params['depend_entity']);
            //$request->replace($request_params);
            $request = (object)array_merge(
                (array)$request,
                $request_params
            );
        }

       //echo "<pre>"; print_r($depend_entity); exit;
        //print_r($request);
        //  print_r($depend_entity); exit;
        $request->entity_auth_id = isset($request->entity_auth_id) ? $request->entity_auth_id : 0;
        $listOfAttributeToBeValidate = $listOfAttributeToBeInserted = [];

        $validator = Validator::make($request_params, [
            $this->_eTypeModel->primaryKey => 'required|exists:'
                . $this->_eTypeModel->table.','
                . $this->_eTypeModel->primaryKey . ',deleted_at,NULL'
        ]);

        // validator
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }


        if (isset($request->entity_type_id) && is_numeric(trim($request->entity_type_id))) {
            $this->_eTypeData = $this->_eTypeModel->get($request->entity_type_id);
        } elseif (isset($request->entity_type_id)) {
            $this->_eTypeData = $this->_eTypeModel->getBy('identifier', $request->entity_type_id);
            if ($this->_eTypeData) {
                //$request->replace(array_merge($request->all(), array('entity_type_id' => $this->_eTypeData->entity_type_id)));
            }
        }


        if (isset($request->entity_type_id) && is_numeric($request->entity_type_id)) {
            //Trigger for entity type before post and validate
            $func = CustomHelper::convertToCamel($this->_eTypeData->identifier . '_before_post_trigger');
            if (method_exists($obj, "$func")) {
                $before_add_trigger_data = $obj->$func($request);
                if ($before_add_trigger_data) {
                    //$request->request->add($before_add_trigger_data);
                    $request = (array)$request;
                    // array_push($request, $before_add_trigger_data);
                    //$request_params = array_merge($request_params, $before_add_trigger_data);
                    $request_params = array_merge(
                        $request_params,
                        $before_add_trigger_data
                    );

                    $request = array_merge(
                        $request,
                        $before_add_trigger_data
                    );
                    // beck to object
                    $request = (object)$request;
                }

            }
          // echo "<pre>"; print_r($request); exit;
            $listOfAttributeToBeValidate = $this->_entityAttributeModel->getEntityAttributeValidationList($request->entity_type_id, '');
            $listOfAttributeToBeInserted = $this->_entityAttributeModel->getEntityAttributeFields($request->entity_type_id);
            $response_validator = $this->_postValidator($request_params, $listOfAttributeToBeValidate);

            $func = CustomHelper::convertToCamel($this->_eTypeData->identifier . '_verify_trigger');
            if (method_exists($obj, "$func")) {
                $verify_response = $obj->$func((array)$request);
                if ($verify_response['error'] == TRUE) {

                    if(isset($verify_response['kick_user'])){
                        $this->_apiData['kick_user'] = $verify_response['kick_user'];
                    }
                    $this->_apiData['message'] = $verify_response['message'];
                    throw new \Exception($this->_apiData['message']);
                }
            }
        }


        $listOfDependentAttributeToBeValidate = $listOfDependentfAttributeToBeInserted = [];
        if (isset($this->_eTypeData->depend_entity_type) && !empty($this->_eTypeData->depend_entity_type)) {
            $listOfDependentAttributeToBeValidate = $this->_entityAttributeModel->getEntityAttributeValidationList($this->_eTypeData->depend_entity_type, '');
            $listOfDependentfAttributeToBeInserted = $this->_entityAttributeModel->getEntityAttributeFields($this->_eTypeData->depend_entity_type);
        }


        // validation
        if (isset($this->_eTypeData->depend_entity_type) && !empty($this->_eTypeData->depend_entity_type)) {

            $this->dependent_entity_type_data = $this->_eTypeModel->get($this->_eTypeData->depend_entity_type);
            $func = CustomHelper::convertToCamel($this->dependent_entity_type_data->identifier . '_verify_trigger');

            if (isset($depend_entity[0])) {
                foreach ($depend_entity as $depend_entity_row) {
                    $depend_entity_row['entity_type_id'] = $this->dependent_entity_type_data->entity_type_id;
                    $response_validator = $this->_postValidator($depend_entity_row, $listOfDependentAttributeToBeValidate);

                    if (method_exists($obj, "$func")) {
                        $verify_response = $obj->$func((array)$request);
                        if ($verify_response['error'] == TRUE) {
                            $this->_apiData['message'] = $verify_response['message'];
                            throw new \Exception($this->_apiData['message']);
                        }
                    }
                }
            }

            $func_dependent = CustomHelper::convertToCamel($this->_eTypeData->identifier . '_dependent_verify_trigger');
            if (method_exists($obj, "$func_dependent")) {
                $request_params = (array)$request;
                $request_params['depend_entity'] = $depend_entity;
                //$request->replace($request_params);
                $request = (object)array_merge(
                    (array)$request,
                    $request_params
                );
                $verify_response = $obj->$func_dependent($request);
                if ($verify_response['error'] == TRUE) {
                    $this->_apiData['message'] = $verify_response['message'];
                    throw new \Exception($this->_apiData['message']);
                }
                $depend_entity = $request_params['depend_entity'];
                unset($request_params['depend_entity']);
                //$request->replace($request_params);
                $request = (object)array_merge(
                    (array)$request,
                    $request_params
                );
            }
        }

        $response_post = $this->_post($request, $listOfAttributeToBeValidate, $listOfAttributeToBeInserted);

        $entity_id = $response_post->entity->entity_id;
        // print_r($this->_eTypeData);
        if (isset($this->_eTypeData->wft_id)) {
            $func_wfs = CustomHelper::convertToCamel($this->_eTypeData->identifier . '_wfs_trigger');
            if (method_exists($obj, "$func_wfs"))
                $obj->$func_wfs($request, $response_post, $this->_eTypeData->wft_id);
        }

        // extra implementation to supportive fields or tables.
        $func = CustomHelper::convertToCamel($this->_eTypeData->identifier . '_add_trigger');
        if (method_exists($obj, "$func"))
            $obj->$func($request, $entity_id);


        if (isset($this->_eTypeData->depend_entity_type) && !empty($this->_eTypeData->depend_entity_type))
            $this->_postDependentEntity(
                $request,
                $depend_entity,
                $listOfDependentAttributeToBeValidate,
                $listOfDependentfAttributeToBeInserted,
                $entity_id
            );

        $request_params['depend_entity'] = $depend_entity;
        //$request->replace($request_params);
        $request = (object)array_merge(
            (array)$request,
            $request_params
        );

        $func = CustomHelper::convertToCamel($this->_eTypeData->identifier . '_dependent_add_trigger');
        if (method_exists($obj, "$func"))
            $obj->$func($request);

        //Log History and save system notification
        $sys_history = new SYSEntityHistory();
        $other_data['entity_type_id'] = $request->entity_type_id;
        $other_data['extension_ref_table'] = $this->_eTypeData->identifier.'_flat';
        $other_data['extension_ref_id'] = $entity_id;
        $timestamp = date("Y-m-d H:i:s");
        $target_entity_id = false;
        $sys_history->logHistory('entity_add', $entity_id, $target_entity_id, $other_data, $timestamp, $request);

        return $entity_id;
    }

    /**
     * Post functionality divided
     *
     * @param Request $request
     * @param $listOfAttributeToBeValidate
     * @param $listOfAttributeToBeInserted
     * @return mixed
     */
    private function _post($request, $listOfAttributeToBeValidate, $listOfAttributeToBeInserted)
    {
        $request = is_array($request) ? (object)$request : $request;
        $listOfAttributeToBeInserted = $this->_entityAttributeModel->getEntityAttributeFields(
            $request->entity_type_id
        );

        /* if($request->entity_type_id == 16){
             print_r($request); exit;
         }*/
        // init output data array
        $data = [];
        $identifier = $this->_objectIdentifier;
        /*if ($this->_eTypeData) {
            if ($this->_mobile_json) $identifier = $this->_eTypeData->identifier;
        }*/

        $entity = [];

        $entity["created_at"] = date("Y-m-d H:i:s");

        //Map values to attribute field
        foreach ($listOfAttributeToBeInserted as $key => $result) {
            if (isset($result->model) && ($result->model == 'sys_entity')) {
                if (isset($request->{$result->attribute_code}))
                    $entity[ $result->attribute_code ] = $request->{$result->attribute_code};
            }
        }
        $entity["entity_auth_id"] = isset($request->entity_auth_id) ? $request->entity_auth_id : 0;

        $entity_id = $this->_entityModel->put($entity);
        //print_r($listOfAttributeToBeInserted); exit;
        //Map values to attribute field
        foreach ($listOfAttributeToBeInserted as $key => $result) {

            if (isset($result->attribute_code)) {
                if (array_key_exists($result->attribute_code, (array)$request))
                    if ($result->model != 'sys_entity') {
                        $table = $result->model;

                        $listOfAttributeToBeInserted[ $key ]->created_at =  $entity["created_at"];

                        if ($result->php_data_type == 'comma_separated') {
                            foreach (explode(',', $request->{$result->attribute_code}) as $temp) {
                                $listOfAttributeToBeInserted[ $key ]->entity_id = $entity_id;
                                $listOfAttributeToBeInserted[ $key ]->value = $temp;
                                unset($result->attribute_code, $result->model, $result->attribute_set_id, $result->php_data_type, $result->default_value);
                                $this->_dbLib->query->table($table)->insert((array)$listOfAttributeToBeInserted[ $key ]);
                            }

                        } else {

                            //print $result->attribute_code."\n";
                            $listOfAttributeToBeInserted[ $key ]->entity_id = $entity_id;
                            $listOfAttributeToBeInserted[ $key ]->value = $request->{$result->attribute_code};
                            unset($result->attribute_code, $result->model, $result->attribute_set_id, $result->php_data_type, $result->default_value);
                            //var_dump($table);
                            // var_dump($listOfAttributeToBeInserted[$key]);

                            try {
                                $this->_dbLib->query->table($table)->insert((array)$listOfAttributeToBeInserted[ $key ]);
                            } catch (\Exception $e) {
                                //var_dump($table);
                                // var_dump($listOfAttributeToBeInserted[$key]);
                                // var_dump($e->getMessage());
                                // exit;
                                throw new \Exception($e->getMessage());
                            }
                        }

                    }

            } else //Values having value null
            {
                if ($result->model != 'sys_entity') {
                    $table = $result->model;
                    $listOfAttributeToBeInserted[ $key ]->entity_id = $entity_id;
                    $listOfAttributeToBeInserted[ $key ]->value = '';
                    unset($result->attribute_code, $result->model, $result->attribute_set_id, $result->php_data_type);
                    $this->_dbLib->query->table($table)->insert((array)$listOfAttributeToBeInserted[ $key ]);
                }
            }
        }


        //This variable is use to get attribute having list of values like Gender( male/ female )
        $dataTypesHavingSelectedValues = $this->_dbLib->query->table('sys_attribute')->selectRaw('group_concat(attribute_code) as listOfAttr')->whereIn('data_type_id', [5, 9, 11, 12])->first();


        $entity_type = $this->_eTypeModel->getData($entity['entity_type_id']);
        if ($entity_type->use_flat_table == "1") {
            $flat_entity = ['entity_id' => $entity_id];
            foreach ($listOfAttributeToBeValidate as $field) {
                $flat_fields[] = $field->attribute_code;

                if (isset($request->{$field->attribute_code})) {
                    if (in_array(
                        $field->attribute_code,
                        array_unique(explode(',', $dataTypesHavingSelectedValues->listOfAttr))
                    )) {

                        if (isset($request->{$result->attribute_code}) && strpos($request->{$result->attribute_code}, ',') === FALSE) {

                            if (strpos($request->{$result->attribute_code}, ',') === FALSE) {
                                $string = '';
                                foreach (array_unique(explode(',', $request->{$field->attribute_code})) as $temp) {
                                    $string .= $temp . ',';
                                }
                                $flat_entity[ $field->attribute_code ] = rtrim($string, ',');

                            }
                        } elseif ($field->use_entity_type == 1
                            && (isset($request->{$result->attribute_code}) && strpos($request->{$result->attribute_code}, ',') === TRUE)
                        ) {
                            $flat_entity[ $field->attribute_code ] = $request->{$field->attribute_code};
                        } elseif ($field->use_entity_type == 0 && $field->linked_entity_type_id != 0) {
                            $flat_entity[ $field->attribute_code ] = $request->{$field->attribute_code};
                        } else {

                            $entity_value = $this->_dbLib->query->table('sys_attribute_option')->select('value')
                                ->where('value', $request->{$field->attribute_code})
                                ->where('attribute_id', $field->attribute_id)
                                ->first();
                            $entity_value = json_decode(json_encode($entity_value));

                            if ($entity_value) {
                                $flat_entity[ $field->attribute_code ] = $entity_value->value;
                            } else {
                                //$flat_entity[ $field->attribute_code ] = '';
                                // fix for dependent entity attribute values (was missing in flat)
                                $flat_entity[ $field->attribute_code ] = $request->{$field->attribute_code};
                            }

                        }
                    } else {
                        $flat_entity[ $field->attribute_code ] = $request->{$field->attribute_code};
                    }

                } else {
                    if ($field->default_value != "" && !empty($field->default_value))
                        $flat_entity[ $field->attribute_code ] = $field->default_value;
                }

            }

            $SYSTableFlatModel = $this->_modelPath . "SYSTableFlat";
            $SYSTableFlatModel = new $SYSTableFlatModel($entity_type->identifier);
            $SYSTableFlatModel->__fields = $flat_fields;
            $flat_entity['created_at'] = date('Y-m-d H:i:s');
            $SYSTableFlatModel->put($flat_entity);
        }

        // response data
        //$data[$this->_objectIdentifier] = $this->_entityModel->getData($entity_id);

        $request->entity_id = $entity_id;
        $record = $this->_entityModel->getData($entity_id);
        $record->entity_type_identifier = $identifier;
        // init attributes

        if (isset($this->_eTypeData) && $this->_eTypeData->show_gallery == "1") {
            if (isset($request->gallery_items) && !empty($request->gallery_items)) {
                $attachments = $request->gallery_items;
                if (!is_array($attachments)) $attachments = explode(",", $attachments);
                $gallery_featured_item = 0;
                if (isset($request->gallery_featured_item) && !empty($request->gallery_featured_item)) $gallery_featured_item = $request->gallery_featured_item;
                $this->_attachmentModel->updateAttachmentByEntityID($entity_id, $attachments, $gallery_featured_item);
            }
            $record->gallery = [];
            $record->gallery = $this->_attachmentModel->getAttachmentByEntityID($entity_id);
        }

        // init attributes
        //$data[$identifier] = $record;
        $data = $this->_entityModel->getEntityData($request);
        if ($data) {
            $data2 = $data;
            unset($data);
            $data = (object)[$identifier => $data2];
        } else {
            $data = (object)[$identifier => $data];
        }

        if ($entity_type->entity_type_id == 20) {
            $func = 'calculateCustomerCart';
            //CustomHelper::convertToCamel($this->dependent_entity_type_data->identifier.'_add_trigger');
            $obj = new EntityTrigger();
            if (method_exists($obj, "$func"))
                $obj->$func();

        }

        // assign to output
        $data->identifier = $identifier; // attach identifier

        return $data;
    }

    /**
     * Post
     *
     * @param integer entity_type_id
     * @return NULL
     */
    public function xdoPost($request)
    {
        $request = is_object($request) ? $request : (object)$request;
        $depend_entity = isset($request->depend_entity) ? isset($request->depend_entity) : [];

        $obj = new EntityTrigger();
        $request->entity_auth_id = isset($request->entity_auth_id) ? $request->entity_auth_id : 0;

        if (isset($request->entity_type_id) && is_numeric(trim($request->entity_type_id))) {
            $this->_eTypeData = $this->_eTypeModel->get($request->entity_type_id);
        } elseif (isset($request->entity_type_id)) {
            $this->_eTypeData = $this->_eTypeModel->get($request->entity_type_id);
            if ($this->_eTypeData) {
                //$request->replace(array_merge($request->all(), array('entity_type_id' => $this->_eTypeData->entity_type_id)));
            }
        }

        if (isset($request->entity_type_id) && is_numeric($request->entity_type_id)) {
            $response_validator = $this->_postValidator($request);

            if ($response_validator)
                throw new \Exception($this->_apiData['message']);


            $func = CustomHelper::convertToCamel($this->_eTypeData->identifier . '_verify_trigger');
            if (method_exists($obj, "$func")) {
                $verify_response = $obj->$func($request);
                if ($verify_response['error'] == TRUE) {
                    $this->_apiData['message'] = $verify_response['message'];
                    throw new \Exception($this->_apiData['message']);
                }
            }
        }

        // validation
        if (isset($this->_eTypeData->depend_entity_type) && !empty($this->_eTypeData->depend_entity_type)) {

            $this->dependent_entity_type_data = $this->_eTypeData = $this->_eTypeModel->getEntityTypeById($this->_eTypeData->depend_entity_type);
            $func = CustomHelper::convertToCamel($this->dependent_entity_type_data->identifier . '_verify_trigger');

            foreach ($depend_entity as $depend_entity_row) {
                $depend_entity_row['entity_type_id'] = $this->dependent_entity_type_data->entity_type_id;
                $response_validator = $this->_postValidator($depend_entity_row);
                if ($response_validator)
                    throw new \Exception($this->_apiData['message']);
                if (method_exists($obj, "$func")) {
                    $verify_response = $obj->$func($request);
                    if ($verify_response['error'] == TRUE) {
                        $this->_apiData['message'] = $verify_response['message'];
                        throw new \Exception($this->_apiData['message']);
                    }
                }
            }

            $this->_eTypeData = $this->_eTypeData;
            $func_dependent = CustomHelper::convertToCamel($this->_eTypeData->identifier . '_dependent_verify_trigger');
            if (method_exists($obj, "$func_dependent")) {
                //$request_params = $request->all();
                $request->depend_entity = $depend_entity;

                $verify_response = $obj->$func_dependent($request);

                if ($verify_response['error'] == TRUE) {
                    $this->_apiData['message'] = $verify_response['message'];
                    throw new \Exception($this->_apiData['message']);
                }
                $depend_entity = $request->depend_entity;
                unset($request->depend_entity);
            }
        }

        $entity_type = $this->_eTypeModel->getData($request->entity_type_id);
        $listOfAttributeToBeInserted = $this->_entityAttributeModel->getEntityAttributeFields($request->entity_type_id);
        $listOfAttributeToBeValidate = $this->_entityAttributeModel->getEntityAttributeValidationList($request->entity_type_id, '');

        $entity = [];
        $entity["created_at"] = date("Y-m-d H:i:s");

        //Map values to attribute field
        foreach ($listOfAttributeToBeInserted as $key => $result) {
            if (isset($result->model) && ($result->model == 'sys_entity')) {
                if (isset($request->{$result->attribute_code}))
                    $entity[ $result->attribute_code ] = $request->{$result->attribute_code};
            }
        }

        $entity["entity_auth_id"] = isset($request->entity_auth_id) ? $request->entity_auth_id : 0;

        $entity_id = $this->_entityModel->put($entity);//print_r($listOfAttributeToBeInserted); //exit;

        //Map values to attribute field
        foreach ($listOfAttributeToBeInserted as $key => $result) {
            if (isset($result->attribute_code))
                if (array_key_exists($result->attribute_code, $request))
                    if ($result->model != 'sys_entity') {
                        $table = $result->model;
                        if (strpos($request->{$result->attribute_code}, ',') > 0 && $result->php_data_type == 'comma_separated') {
                            foreach (explode(',', $request->{$result->attribute_code}) as $temp) {
                                $listOfAttributeToBeInserted[ $key ]->entity_id = $entity_id;
                                $listOfAttributeToBeInserted[ $key ]->value = $temp;
                                unset($result->attribute_code, $result->model, $result->attribute_set_id, $result->php_data_type);

                                $this->_dbLib->query->insert($table, (array)$listOfAttributeToBeInserted[ $key ]);
                            }
                        } else {
                            //print $result->attribute_code."\n";
                            $listOfAttributeToBeInserted[ $key ]->entity_id = $entity_id;
                            $listOfAttributeToBeInserted[ $key ]->value = $request->{$result->attribute_code};
                            unset($result->attribute_code, $result->model, $result->attribute_set_id, $result->php_data_type);
                            $this->_dbLib->query->insert($table, (array)$listOfAttributeToBeInserted[ $key ]);
                        }

                    }
        }


        //This variable is use to get attribute having list of values like Gender( male/ female )
        $dataTypesHavingSelectedValues = $this->_attributeModel
            ->getAttributeIdsByDataType([5, 9, 11, 12]);


        if ($entity_type->use_flat_table == "1") {
            $flat_entity = ['entity_id' => $entity_id];
            $flat_fields = ['entity_id'];

            foreach ($listOfAttributeToBeValidate as $field) {
                $flat_fields[] = $field->attribute_code;
                if ($request->{$field->attribute_code})
                    if (in_array($field->attribute_code, array_unique(explode(',', $dataTypesHavingSelectedValues->listOfAttr))))
                        if ($field->use_entity_type) {
                            if (strpos($request->{$result->attribute_code}, ',') === FALSE) {
                                $string = '';
                                foreach (array_unique(explode(',', $request->{$field->attribute_code})) as $temp) {
                                    $record = $this->getLinkedEntityAttributeValue($field->attribute_id, $temp, $request->_lang);
                                    if (count($record)) {
                                        $string .= $record[0]->attribute . ',';
                                    }
                                }
                                $flat_entity[ $field->attribute_code ] = rtrim($string, ',');

                            } else {
                                $record = $this->getLinkedEntityAttributeValue($field->attribute_id, $request->{$field->attribute_code}, $request->_lang);
                                if (count($record)) {
                                    $flat_entity[ $field->attribute_code ] = $record[0]->attribute;
                                }
                            }

                        } elseif ($field->use_entity_type == 0 && $field->linked_entity_type_id != 0) {
                            $flat_entity[ $field->attribute_code ] = $request->{$field->attribute_code};
                        } else {
                            $entity_value = $this->_attributeOptionModel->select('option')
                                ->where('value', $request->{$field->attribute_code})
                                ->where('attribute_id', $field->attribute_id)
                                ->first();
                            $flat_entity[ $field->attribute_code ] = $entity_value->option;

                        }

                    else
                        $flat_entity[ $field->attribute_code ] = $request->{$field->attribute_code};

            }
            $SYSTableFlatModel = $this->_modelPath . "SYSTableFlat";
            $SYSTableFlatModel = new $SYSTableFlatModel($entity_type->identifier);
            $SYSTableFlatModel->__fields = $flat_fields;
            $SYSTableFlatModel->put($flat_entity);
        }


        //Add gallery ids
        if (isset($entity_type) && $entity_type->show_gallery == "1") {
            if (isset($request->gallery_items) && !empty($request->gallery_items)) {
                $attachments = $request->gallery_items;
                if (!is_array($attachments)) $attachments = explode(",", $attachments);
                $gallery_featured_item = 0;
                if (isset($request->gallery_featured_item) && !empty($request->gallery_featured_item)) $gallery_featured_item = $request->gallery_featured_item;
                $this->_attachmentModel->updateAttachmentByEntityID($entity_id, $attachments, $gallery_featured_item);
            }
        }

        if ($this->_eTypeData->wft_id) {
            $func_wfs = CustomHelper::convertToCamel($this->_eTypeData->identifier . '_wfs_trigger');
            $obj->$func_wfs($request, $request, $this->_eTypeData->wft_id);
        }
        // extra implementation to supportive fields or tables.
        $func = CustomHelper::convertToCamel($this->_eTypeData->identifier . '_add_trigger');
        if (method_exists($obj, "$func"))
            $obj->$func($request);

        if (isset($this->_eTypeData->depend_entity_type) && !empty($this->_eTypeData->depend_entity_type))
            $this->_postDependentEntity($request, $depend_entity, $this->_eTypeData);

        $request->depend_entity = $depend_entity;
        //$request->replace($request_params);

        $func = CustomHelper::convertToCamel($this->_eTypeData->identifier . '_dependent_add_trigger');
        if (method_exists($obj, "$func"))
            $obj->$func($request);

        return $entity_id;
    }


    /**
     * Entity API response
     *
     * @param $request
     * @return array
     */
    public function apiUpdate($request)
    {
        try {
            $id = $this->doUpdate($request);

            $this->_apiData['data'] = $this->getData($id, $request);
            $this->_apiData['response'] = $this->_apiData['data'] ? trans($this->_langIdentifier.".success") : $this->_apiData['data'];
            $this->_apiData['message'] = trans($this->_langIdentifier.".success");

        } catch (\Exception $e) {
           // echo $e->getTraceAsString(); exit;
            $this->_apiData['message'] = $e->getMessage();
            $this->_apiData['debug'] = $e->getMessage() . ', ' . $e->getFile() . ', ' . $e->getLine();
        }

        // fix for error flags
        $this->_apiData['error'] = $this->_apiData['response'] == trans($this->_langIdentifier.".success") ? 0 : 1;

        return $this->_apiData;
    }


    /**
     * Update
     *
     * @param $request
     * @return mixed
     * @throws \Exception
     */
    public function doUpdate($request)
    {
        $request = is_object($request) ? $request : (object)$request;
        $depend_entity = [];
        $request_params = (array)$request;
        // get entity type data from request
        $entityTypeData = $this->_getEntityTypeDataFromRequest($request);

        if (isset($request_params['depend_entity'])) {
            $depend_entity = $request_params['depend_entity'];
            unset($request_params['depend_entity']);
            //$request->replace($request_params);
            $request = (object)array_merge(
                (array)$request,
                $request_params
            );
        }

        $obj = new EntityTrigger();

        if (isset($request->entity_type_id) && is_numeric($request->entity_type_id)) {
            //Trigger for entity type before post and validate
            $func = CustomHelper::convertToCamel($entityTypeData->identifier . '_before_save_trigger');
            if (method_exists($obj, "$func")) {
                $before_save_trigger_data = $obj->$func((array)$request);
                if ($before_save_trigger_data) {
                    //$request->request->add($before_save_trigger_data);
                    $request_params = array_merge($request_params, $before_save_trigger_data);
                    $request = (object)array_merge(
                        (array)$request,
                        $request_params
                    );
                }
            }

            $listOfAttributeToBeValidate = $this->_entityAttributeModel->getEntityAttributeValidationListForUpdate($request->entity_type_id, $request->entity_id);
            $listOfAttributeToBeInserted = $this->_entityAttributeModel->getEntityAttributeFields($request->entity_type_id);
        }

        // validations
        //check if entity type is auth
        /*if (is_numeric($request->entity_type_id)) {
            $entityTypeData = $this->entity_type_data = $this->_getEntityTypeById($request->entity_type_id);
            if ($this->_mobile_json) $identifier = $entityTypeData->identifier;
        }*/

        // validate
        if (!$this->_saveValidator($request, $listOfAttributeToBeValidate))
            throw new \Exception($this->_apiData['message']);
        //return $this->_apiData;

        $func = CustomHelper::convertToCamel($entityTypeData->identifier . '_update_verify_trigger');

        if (method_exists($obj, "$func")) {
            $verify_response = $obj->$func((array)$request);
            if ($verify_response['error'] == TRUE) {
                throw new \Exception($verify_response['message']);
            }
        }

        // init entity
        $entity = array();

        $entity["updated_at"] = date("Y-m-d H:i:s");
        //Map values to attribute field
        foreach ($listOfAttributeToBeInserted as $key => $result) {
            if ($result->model == 'sys_entity') {
                if (isset($request->{$result->attribute_code}))
                    $entity[ $result->attribute_code ] = $request->{$result->attribute_code};
            }
        }


        //if user management then first get entity auth id then update entity
        if ($entityTypeData->allow_auth == 1 || $entityTypeData->allow_backend_auth == 1) {

            if(isset($request->email) || isset($request->mobile_no)){
                // load sys entity auth
                $entityAuthModel = new SYSEntityAuth();
                $entity_auth = $entityAuthModel->entityQuery($request->entity_type_id)
                    ->where("entity." . $this->_entityModel->primaryKey, "=", $request->{$this->_entityModel->primaryKey})
                    ->select("auth." . $entityAuthModel->primaryKey)
                    ->first();

                if (isset($entity_auth->{$entityAuthModel->primaryKey})) {
                    $entity[ $entityAuthModel->primaryKey ] = $entity_auth->{$entityAuthModel->primaryKey};
                    //if entity type is customer or business user then also update mobile no
                    // if(in_array($entityTypeData->identifier,array('customer','super_admin','business_user'))){

                    $entity_auth_raw = $this->_entityAuthModel->get($entity_auth->{$entityAuthModel->primaryKey});
                    if(isset($request->mobile_no)){

                        $request->mobile_no = str_replace("+", "", $request->mobile_no);
                        $entity_auth_raw->mobile_no = $request->mobile_no;

                    }
                    if(isset($request->email)){
                        $entity_auth_raw->email = $request->email;
                    }

                    $this->_entityAuthModel->set($entity_auth->{$entityAuthModel->primaryKey}, (array)$entity_auth_raw);
                    // }
                }
            }

        }


        $this->_entityModel->set($request->entity_id, $entity);
        $entity_id = $request->entity_id;

        //Get Entity Data before update
        $obj->_entityData = $this->_entityModel->getData($entity_id);

        //Map values to attribute field

        $func = CustomHelper::convertToCamel($entityTypeData->identifier . '_update_alternate');
        if (method_exists($obj, "$func")) {
            $obj->$func((array)$request, $depend_entity);
        } else {
            $dataTypesHavingSelectedValues = $this->_dbLib->query->table('sys_attribute')->selectRaw('group_concat(attribute_code) as listOfAttr')->whereIn('data_type_id', [5, 9, 11, 12])->first();
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
                                $this->_dbLib->query->table($table)->insert($dataToBeInserted);

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
                        $entity_count = $this->_dbLib->query->table($table)
                            ->where('entity_id', $entity_id)
                            ->where('entity_type_id', $request->entity_type_id)
                            ->where('attribute_id', $result->attribute_id)
                            ->limit(1)
                            ->count();
                        if ($entity_count == 1) {
                            $this->_dbLib->query->table($table)
                                ->where('entity_id', $entity_id)
                                ->where('entity_type_id', $request->entity_type_id)
                                ->where('attribute_id', $result->attribute_id)
                                ->limit(1)
                                ->update($dataToBeInserted);

                        } else {
                            $this->_dbLib->query->table($table)->insert($dataToBeInserted);
                        }
                    }

                }
            }

            $entity_type = $this->_eTypeModel->getData($entity['entity_type_id']);
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
            $func = CustomHelper::convertToCamel($entityTypeData->identifier . '_update_trigger');
            if (method_exists($obj, "$func"))
                $obj->$func((array)$request,$depend_entity);

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

        //Log History and save system notification
        $sys_history = new SYSEntityHistory();
        $other_data['entity_type_id'] = $request->entity_type_id;
        $other_data['extension_ref_table'] = $entityTypeData->identifier.'_flat';
        $other_data['extension_ref_id'] = $entity_id;
        $timestamp = date("Y-m-d H:i:s");
        $target_entity_id = false;
        $sys_history->logHistory('entity_update', $entity_id, $target_entity_id, $other_data, $timestamp, $request);

        return $entity_id;
    }


    /**
     * Save validator  (private)
     * @param $request
     * @param $listOfAttributeToBeValidate
     * @return bool
     * @throws \Exception
     */
    private function _saveValidator($request, $listOfAttributeToBeValidate)
    {
        $request = is_object($request) ? $request : (object)$request;
        $request->entity_type_id = isset($request->entity_type_id) ? $request->entity_type_id : 0;

        $rules = array(
            'entity_type_id' => 'integer|exists:' . $this->_eTypeModel->table . "," . $this->_eTypeModel->primaryKey . ",deleted_at,NULL",
            'entity_id' => 'integer|exists:' . $this->_entityModel->table . "," . $this->_entityModel->primaryKey . ",deleted_at,NULL"
        );

        $entity_type_data = $this->_getEntityTypeDataFromRequest($request);

        if ($entity_type_data->allow_auth == 1 && $entity_type_data->allow_backend_auth == 1) {
            if ($entity_type_data->identifier == "business_user" && !(isset($request->is_profile_update))) {
                $rules['role_id'] = 'required';
            }

            //if entity type is customer or business user then validate mobile no
            if(in_array($entity_type_data->identifier,array('customer','business_user','super_admin','driver'))){

               if(isset($request->entity_id) && (isset($request->mobile_no) ||  isset($request->email))){

                  $entity =  $this->_entityModel->get($request->entity_id);

                   if($entity->entity_auth_id){

                       if(isset($request->email))
                       $rules['email'] = 'required|email|unique:' . $this->_entityAuthModel->table . ',email,'.$entity->entity_auth_id.','. $this->_entityAuthModel->primaryKey .',deleted_at,NULL';

                       if(isset($request->mobile_no)){
                           $request->mobile_no = str_replace("+", "", $request->mobile_no);
                           $rules['mobile_no'] = 'required|mobile|unique:' . $this->_entityAuthModel->table . ',mobile_no,'.$entity->entity_auth_id.','. $this->_entityAuthModel->primaryKey .',deleted_at,NULL';
                       }

                   }

               }
            }

        }

        $request_params = (array)$request;
        $error_messages = array();
        foreach ($listOfAttributeToBeValidate as $result) {

            //Combine validation of entity attribute with other validation
            if (!empty($result->js_validation_tags)) {
                if (!empty($result->validation))
                    $result->validation .= '|' . $result->js_validation_tags;
                else
                    $result->validation = $result->js_validation_tags;
            }

            if (isset($request_params[ $result->attribute_code ])) {
                if (strpos($request_params[ $result->attribute_code ], ',') === TRUE) {
                    $rules[ trim($result->attribute_code) ] = $result->validation;
                } else {
                    $temp = explode('|in', $result->validation);
                    $rules[ $result->attribute_code ] = $temp[0];
                }

                //if backend then update error Messages b/c error mesages are displaying attribute code
                //but in message label should be display
                if(!isset($request_params->mobile_json) || $request_params->mobile_json == 0){
                    $error_message = $this->_attributeErrorMessages($result,$rules);
                    $error_messages =  array_merge($error_messages,$error_message);
                }

            }

        }

         // echo "<pre>"; print_r( $rules);
         //echo "<pre>"; print_r( $error_messages);


        if(!isset($request_params->mobile_json) || $request_params->mobile_json == 0)
            $validator = Validator::make((array)$request, $rules,$error_messages);
            else
            $validator = Validator::make((array)$request, $rules);

        // validator 2 for verifying correct entity_type with entity_id
        /*$validator2 = Validator::make((array)$request, array(
            'entity_id' => 'required|integer|exists:' . $this->_entityModel->table . ',' . $this->_entityModel->primaryKey . ',entity_type_id,' . $request->entity_type_id . ',deleted_at,NULL'
        ));
        if (request('entity_type_id', 0) > 0 && $validator2->fails()) {
            throw new \Exception($validator2->errors()->first());
        } else if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }*/

        return TRUE;
    }


    /**
     * Delete API response
     *
     * @param $request
     * @return array
     */
    public function apiDelete($request)
    {
        try {
            $id = $this->doDelete($request);

            $this->_apiData['data'] = $this->getData($id, $request['mobile_json'], $request['_lang']);
            $this->_apiData['response'] = $this->_apiData['data'] ? trans($this->_langIdentifier.".success") : $this->_apiData['data'];

        } catch (\Exception $e) {
            $this->_apiData['message'] = $e->getMessage();
            $this->_apiData['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine();
        }

        // fix for error flags
        $this->_apiData['error'] = $this->_apiData['response'] == trans($this->_langIdentifier.".success") ? 0 : 1;

        return $this->_apiData;
    }


    /**
     * Delete
     *
     * @param integer entity_type_id
     * @return NULL
     */
    public function doDelete($request)
    {
        $request = is_object($request) ? $request : (object)$request;

        // init output data array
        $this->_apiData['data'] = $data = [];

        $rules = [
            //'entity_id' => 'integer|exists:' . $this->_entityModel->table . "," . $this->_entityModel->primaryKey . ",deleted_at,NULL"
            'entity_id' => 'integer|exists:' . $this->_entityModel->table . "," . $this->_entityModel->primaryKey . ",deleted_at,NULL"
        ];

        $validator = Validator::make((array)$request, $rules);
        // validator 2 for verifying correct entity_type with entity_id
        $validator2 = Validator::make((array)$request, [
            'entity_id' => 'required|integer|exists:' . $this->_entityModel->table . ',' . $this->_entityModel->primaryKey . ',entity_type_id,' . intval(@$request->entity_type_id) . ',deleted_at,NULL'
        ]);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
            throw new \Exception($validator->errors()->first());
        } elseif (intval(@$request->entity_type_id) > 0 && $validator2->fails()) {
            throw new \Exception($validator2->errors()->first());
        } else {

            // success response
            $this->_apiData['response'] = trans($this->_langIdentifier.".success");

            // get
            $record = $this->_entityModel->get($request->{$this->_entityModel->primaryKey});
            // delete data to sys_entity_auth table
            if (isset($record->entity_auth_id) && $record->entity_auth_id > 0) {
                $this->_entityAuthModel->remove($record->{$this->_entityModel->primaryKey});
            }
            // delete data to sys_entity table
            $this->_entityModel->remove($request->{$this->_entityModel->primaryKey});
            $this->_entityModel->deleteEntityData($request->entity_id);

            if ($record) {
                $entity_type = $this->_eTypeModel->getData($record->entity_type_id);
                if ($entity_type->use_flat_table == "1") {
                    // remove from flat
                    if (\Schema::hasTable($entity_type->identifier . '_flat')) {
                        // remove from flat table
                        //$this->_entityModel->table($entity_type->identifier . '_flat')->remove($record->entity_id);
                        $flat_obj = new SYSTableFlat($entity_type->identifier);
                        // delete data to flat table
                        //  $id = $flat_obj->remove($record->entity_id);
                        if (SOFT_DELETE === TRUE) {
                            $flat_obj->where($this->_entityModel->primaryKey, $record->entity_id)
                                ->update(array('deleted_at' => date("Y-m-d H:i:s")));
                        } else {
                            $flat_obj->where($this->_entityModel->primaryKey, $record->entity_id)
                                ->delete();
                        }
                    }
                }
            }

        }

        return $data;
    }


    /**
     * get API response
     *
     * @param $request
     * @return array
     */
    public function apiGet($request)
    {
        try {
            $request['mobile_json'] = isset($request['mobile_json']) ? $request['mobile_json'] : FALSE;
            $request['_lang'] = isset($request['_lang']) ? $request['_lang'] : 'en';

            $data = $this->doGet($request);
            //$this->_apiData['data'] = $this->getData($id, $request['mobile_json'], $request['_lang']);
            // $data =  array($data->object_key => $data, 'identifier' => $data->object_key);
            $this->_apiData['data'] = array($data->object_key => $data, 'identifier' => $data->object_key);
            $this->_apiData['response'] = $this->_apiData['data'] ? trans($this->_langIdentifier.".success") : $this->_apiData['data'];

        } catch (\Exception $e) {
            $this->_apiData['message'] = $e->getMessage();
            $this->_apiData['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine();
        }
        // fix for error flags
        $this->_apiData['error'] = $this->_apiData['response'] == trans($this->_langIdentifier.".success") ? 0 : 1;

        return $this->_apiData;
    }

    /**
     * Get
     *
     * @param $request
     * @param bool $is_mobile_request
     * @param string $lang
     * @return array|mixed
     * @throws \Exception
     */
    public function doGet($request)
    {
        $request = is_object($request) ? $request : (object)$request;

        // init data
        $data = [];

        // default method required param
        $request->{$this->_entityModel->primaryKey} = intval(@$request->{$this->_entityModel->primaryKey});

        //if entity type is identifier then get entity type id
        if(!is_numeric(trim($request->{$this->_eTypeModel->primaryKey}))) {
            $request->{$this->_eTypeModel->primaryKey} = $this->_eTypeModel->getIdByIdentifier($request->{$this->_eTypeModel->primaryKey});
        }

        // validations
        $rules = [
            'entity_id' => 'required|integer|exists:' . $this->_entityModel->table . "," . $this->_entityModel->primaryKey . ",deleted_at,NULL",
            'entity_type_id' => 'integer|exists:' . $this->_eTypeModel->table . "," . $this->_eTypeModel->primaryKey . ",deleted_at,NULL",
        ];

        $validator = Validator::make((array)$request, $rules);
        // validator 2 for verifying correct entity_type with entity_id
        $validator2 = Validator::make((array)$request, [
            'entity_id' => 'required|integer|exists:' . $this->_entityModel->table . ',' . $this->_entityModel->primaryKey . ',entity_type_id,' . intval(@$request->entity_type_id) . ',deleted_at,NULL'
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        } elseif (intval(@$request->entity_type_id) > 0 && $validator2->fails()) {
            throw new \Exception($validator2->errors()->first());
        } elseif ($request->{$this->_entityModel->primaryKey} == 0) {
            throw new \Exception(trans('system.pls_enter_entity_id', ["entity" => $this->_objectIdentifier]));
        } else {
            $data = $this->getData($request->{$this->_entityModel->primaryKey}, $request);
            // $data =  array($data_response->object_key => $data_response, 'identifier' => $data_response->object_key);
        }

        return $data;

    }

    /**
     * List API Response
     *
     * @param $request
     * @return array
     */
    public function apiList($request)
    {
        try {
            $request['mobile_json'] = isset($request['mobile_json']) ? $request['mobile_json'] : FALSE;
            $request['_lang'] = isset($request['_lang']) ? $request['_lang'] : 'en';

            $data = $this->doList($request);

            //$this->_apiData['data'] = $this->getData($id, $request['mobile_json'], $request['_lang']);
            $this->_apiData['data'] = $data;
            $this->_apiData['response'] = $this->_apiData['data'] ? trans($this->_langIdentifier.".success") : $this->_apiData['data'];

        } catch (\Exception $e) {
            $this->_apiData['message'] = $e->getMessage();
            $this->_apiData['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine();
        }

        // fix for error flags
        $this->_apiData['error'] = $this->_apiData['response'] == trans($this->_langIdentifier.".success") ? 0 : 1;

        return $this->_apiData;
    }

    /**
     * List Entity Data
     *
     * @param $request
     * @param bool $is_mobile_request
     * @param string $lang
     * @return array
     * @throws \Exception
     */
    public function doList($request)
    {
        $request = is_object($request) ? $request : (object)$request;
        $request->entity_id = isset($request->entity_id) ? $request->entity_id : "";

        if (isset($request->entity_type_id) && !is_numeric($request->entity_type_id)) {
            $entity_type_id = $this->_eTypeModel->getIdByIdentifier($request->entity_type_id);
            $request->entity_type_id = ($entity_type_id) ? $entity_type_id : $request->entity_type_id;
        }
        // init data
        $data = [];
        // validations
        $rules = [
            // 'entity_id' => 'required|integer|exists:' . $this->_entityModel->table . "," . $this->_entityModel->primaryKey . ",deleted_at,NULL",
            'entity_type_id' => 'integer|exists:' . $this->_eTypeModel->table . ","
                . $this->_eTypeModel->primaryKey
                . ",deleted_at,NULL",
        ];

        $validator = Validator::make((array)$request, $rules);
        // validator 2 for verifying correct entity_type with entity_id

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        } else {

            $params = [];

            // get entity type data
            if (isset($request->entity_type_id)) {
                $params['entityTypeData'] = $this->_getEntityTypeDataFromRequest($request);


                //Trigger for entity type before list and validate
                $obj = new EntityTrigger();
                $func = CustomHelper::convertToCamel($params['entityTypeData']->identifier . '_before_listing_trigger');
                if (method_exists($obj, "$func")) {
                    $before_add_trigger_data = $obj->$func($request);
                    if ($before_add_trigger_data) {
                        $request = (array)$request;
                        $request = array_merge(
                            $request,
                            $before_add_trigger_data
                        );
                        // beck to object
                        $request = (object)$request;
                    }
                }
            }
           // echo "<pre>"; print_r($request); exit;
            // override object identifier
            $identifier = ($request->mobile_json == 1 && isset($params['entityTypeData'])) ?
                $params['entityTypeData']->identifier :
                $this->_objectIdentifier . "_listing";

            // set order / sorting defaults
            $request->order_by = isset($request->order_by) ? $request->order_by : '';
            $request->sorting = isset($request->sorting) ? $request->sorting : '';
            $request->limit = isset($request->limit) ? $request->limit : '';
            $request->offset = isset($request->offset) ? $request->offset : 0;
            
            

            $params['allowed_ordering'] = $params['allowed_searching'] = "entity_id,created_at";
            $params['allowed_sorting'] = "desc,asc";
            $params['order_by'] = $request->order_by == "" ? explode(",", $params['allowed_ordering'])[0] : $request->order_by;
            $params['sorting'] = $request->sorting == "" ? explode(",", $params['allowed_sorting'])[0] : $request->sorting;

            if ($request->limit == "" || $request->limit > 0) {
                $params['limit'] = ($request->limit == "") ? PAGE_LIMIT_API : intval($request->limit);
                $params['offset'] = intval($request->offset);
            } else {
                $params['limit'] = $request->limit;
                $params['offset'] = $request->offset;
            }

            if(isset($request->multi_order_by) && !empty($request->multi_order_by)){
                $params['multi_order_by'] = $request->multi_order_by;
            }

            $params['show_in_list'] = 1;

            $response = $this->_entityModel->getListData($request->entity_id, $request->entity_type_id, $request, $params);
            $response['data'] = isset($response['data']) ? $response['data'] : "";

            // extra implementation to supportive fields or tables.
            if (isset($params['entityTypeData']->identifier)) {

                $obj = new EntityTrigger();
                $func = CustomHelper::convertToCamel($params['entityTypeData']->identifier . '_listing_trigger');

                if (method_exists($obj, "$func")){
                    $listing_trigger = $obj->$func($request,$response);

                    if (isset($request->entity_id) && $listing_trigger) {
                        if (isset($listing_trigger['key']) && isset($listing_trigger['data'])) {
                            if(count($listing_trigger['data'])>0)
                            $response['data'][0]->{$listing_trigger['key']} = $listing_trigger['data'];
                        }
                    }
                }
            }

            $data[ $identifier ] = $response['data'];
            if (isset($request->mobile_json) && intval($request->mobile_json) > 0) {
                $data["page"] = [
                    //"offset" => $offset,
                    "limit" => $params['limit'],
                    "total_records" => $response['total_records'],
                    //"next_offset" => ($offset + $limit), // new pagination flow
                    "next_offset" => $response['next_offset'],
                    //"prev_offset" => $offset > 0 ? ($offset - $limit) : $offset, // new pagination flow,
                    "prev_offset" => $params['offset']
                ];
            } else {
                $data["page"] = [
                    "offset" => $params['offset'],
                    "limit" => $params['limit'],
                    "total_records" => $response['total_records'],
                    "next_offset" => ($params['offset'] + $params['limit']),
                    "prev_offset" => $params['offset'] > 0 ? ($params['offset'] - $params['limit']) : $params['offset'],
                ];
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
        }

        return $return;
    }


    /**
     * Post dependenti entity (private)
     *
     * @param $request
     * @param $depend_entity
     * @param $listOfDependentAttributeToBeValidate
     * @param $listOfDependentfAttributeToBeInserted
     * @param $entity_id
     * @throws \Exception
     */
    private function _postDependentEntity(
        $request,
        $depend_entity,
        $listOfDependentAttributeToBeValidate,
        $listOfDependentfAttributeToBeInserted,
        $entity_id
    )
    {
        $request = is_array($request) ? (object)$request : $request;
        $obj = new EntityTrigger();
        $obj->entityRequest = (array)$request;
        $identifier = $this->_eTypeData->identifier . '_id';

        //  if (isset($this->_apiData['data']->entity))
        //  $entity_id = $this->_apiData['data']->entity->entity_id;
        //  else
        //  $entity_id = $this->_apiData['data']->{$this->_eTypeData->identifier}->entity_id;
        // $this->entity_type_data = $this->dependent_entity_type_data;
        foreach ($depend_entity as $depend_entity_row) {
            $depend_entity_row[ $identifier ] = $entity_id;
            $depend_entity_row['entity_type_id'] = $depend_entity_row['entity_type_id'] ?
                $depend_entity_row['entity_type_id'] :
                $this->dependent_entity_type_data->entity_type_id;

            //  $request->replace($depend_entity_row);

            $func = CustomHelper::convertToCamel($this->dependent_entity_type_data->identifier . '_before_post_trigger');
            if (method_exists($obj, "$func")){
                $before_post_trigger_data = $obj->$func($request, $depend_entity_row);
                $depend_entity_row = array_merge($depend_entity_row,$before_post_trigger_data);
            }

            $request = (object)array_merge(
                (array)$request,
                $depend_entity_row
            );

            /*print_r($listOfDependentAttributeToBeValidate);
             print_r($listOfDependentfAttributeToBeInserted);
             print_r($depend_entity_row); */

            $response_dependent_post = $this->_post(
                $depend_entity_row,
                $listOfDependentAttributeToBeValidate,
                $listOfDependentfAttributeToBeInserted
            );

            $func = CustomHelper::convertToCamel($this->dependent_entity_type_data->identifier . '_add_trigger');
            if (method_exists($obj, "$func")) $obj->$func($request, $depend_entity_row,$response_dependent_post);
            //$obj->$func($request, $depend_entity_row);

            if (isset( $this->dependent_entity_type_data) &&  $this->dependent_entity_type_data->show_gallery == "1") {

                if (isset($depend_entity_row['gallery_items']) && !empty($depend_entity_row['gallery_items'])) {
                    $attachments = $depend_entity_row['gallery_items'];
                    if (!is_array($attachments)) $attachments = explode(",", $attachments);
                    $gallery_featured_item = 0;
                    $this->_attachmentModel->updateAttachmentByEntityID($response_dependent_post->entity->entity_id, $attachments,$gallery_featured_item);
                }
            }

        }


    }


    /**
     * Get entity type data from object
     *
     * @param $request
     * @return bool|null|object
     */
    private function _getEntityTypeDataFromRequest($request)
    {
        $data = NULL;

        if (isset($request->{$this->_eTypeModel->primaryKey})) {
            // get data from id
            $data = $this->_eTypeModel->get($request->{$this->_eTypeModel->primaryKey});

            // get from identifier
            if (!is_numeric(trim($request->{$this->_eTypeModel->primaryKey}))) {
                $data = $this->_eTypeModel->getBy(
                    'identifier',
                    trim($request->{$this->_eTypeModel->primaryKey})
                );
            }
        }

        return $data;
    }

    /**
     * @param $identifier
     * @param $field_name
     * @param $entity_id
     * @return bool|object
     */
    private function _getRequestedAttributeData($identifier, $field_name, $entity_id)
    {
        $return = FALSE;
        $entity_helper_lib = new EntityHelper();
        $entity_attribute_detail = $entity_helper_lib->getAttributeData($identifier, $field_name, $entity_id);
        if (count($entity_attribute_detail) > 0) {
            if (isset($entity_attribute_detail['value'])) {
                $ret = array();
                $ret['key'] = $entity_attribute_detail['key'];
                $ret['value'] = $entity_attribute_detail['value'];
                $return = (object)$ret;
            }
        }

        return $return;
    }

    /**
     * update error Messages
     * b/c error mesages are displaying attribute code
     * but in message label should be display
     * @param $result
     * @param $rules
     * @return array
     */
    private function _attributeErrorMessages($result,$rules)
    {
        $error_message = array();

        //set field title if entity label is set
        if((isset($result->entity_frontend_label) & !empty($result->entity_frontend_label)))
            $field_title = $result->entity_frontend_label;
        elseif(isset($result->frontend_label) & !empty($result->frontend_label))
            $field_title = $result->frontend_label;
        else
            $field_title = $result->attribute_code;

        //add message for each rule in a attribute
        foreach(explode('|',$rules[ $result->attribute_code ]) as $apply_rule){
            //trim values if validation is exist/in etc
            if(strpos($apply_rule, ':') !== false){
                $check_rule = explode(':',$apply_rule);
                $rule = $check_rule[0];
            }
            else{
                $rule = $apply_rule;
            }

            $error_message[$result->attribute_code.".".$rule] = trans('validation.'.$rule, array('attribute' => $field_title));
        }

        return $error_message;
    }

    public function postValidator($request)
    {
        return $this->_postValidator($request);
    }
}
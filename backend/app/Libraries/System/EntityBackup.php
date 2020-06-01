<?php
/**
 * Summary : Entity Library
 * Created by PhpStorm.
 * User: Salman
 * Date: 12/28/2017
 * Time: 5:42 PM
 */

namespace App\Libraries\System;

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
Class EntityBackup extends Base
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
        $_entityAttributeModel;

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

    /**
     * Get entity data
     *
     * @param int $id
     * @param bool $is_mobile_request
     * @param string $lang
     * @return mixed
     */
    public function getData($id = 0, $request_params = FALSE)
    {
        $request_params = ($request_params && is_array($request_params)) ? (object)$request_params : $request_params;

        $is_mobile_request = isset($request_params->mobile_json) ? $request_params->mobile_json : FALSE;
        $lang = isset($request_params->_lang) ? $request_params->_lang : 'en';
        $in_detail = (isset($request_params->in_detail)) ? $request_params->in_detail : 1;


        if ($request_params && isset($request_params->request_parameter))
            $request_parameter = explode(',', $request_params->request_parameter);
        else
            $request_parameter = array();

        // row data
        $data = $this->_entityModel->get($id);

        if ($data) {

            // get entity type data
            $entityType = $this->_eTypeModel->get($data->{$this->_eTypeModel->primaryKey});

            // attach auth data (if exists)
            if ($data->{$this->_entityAuthModel->primaryKey} > 0) {
                $data->auth = $this->_entityAuthModel->getData(
                    $data->{$this->_entityAuthModel->primaryKey},
                    $data->{$this->_eTypeModel->primaryKey});
            }

            // get entity gallery (if attached)
            if ($entityType->show_gallery == '1') {
                $data->gallery = [];
                $data->gallery = $this->_attachmentModel->getAttachmentByEntityID($data->{$this->_entityModel->primaryKey});
            }


            // if flat
            $q = $this->_entityModel->getEntityAttributeValues($data->entity_type_id, $data->{$this->_entityModel->primaryKey}, $lang);
            // if has attributes
            if (isset($q[0]) && $q[0]->value !== NULL) {
                $attrs = \DB::select($q[0]->value);
                foreach ($attrs as $field_action) {
                    if ((in_array($field_action->name, $request_parameter) && !empty($request_parameter)) || empty($request_parameter))
                        if ($field_action->attribute_id) {
                            $attributeData = $this->_dbLib->query->table('sys_attribute')->select('*')
                                ->where('attribute_id', $field_action->attribute_id)
                                ->first();
                            if ($field_action->data_type_id == 20) {

                                if ($field_action->attribute)
                                    if (!$is_mobile_request)
                                        $data->attributes[ $field_action->name ] = $this->getData($field_action->attribute, array('mobile_json' => $is_mobile_request));
                                    else
                                        $data->{$field_action->name} = $this->getData($field_action->attribute, array('mobile_json' => $is_mobile_request));

                            } else if ($field_action->linked_entity_type_id && $field_action->linked_attribute_id) {

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
                                    $linkedAttribute = $this->getLinkedEntityAttributeValue($field_action->attribute_id, $field_action->attribute, $lang);
                                    if (count($linkedAttribute)) {
                                        if ($in_detail) $showDetail = $this->getData($linkedAttribute[0]->entity_id, array('mobile_json' => $is_mobile_request)); else $showDetail = '';
                                        $temp = array('id' => $linkedAttribute[0]->entity_id, 'value' => $linkedAttribute[0]->attribute, 'detail' => $showDetail);
                                    }
                                }
                                if (!$is_mobile_request) {
                                    $tempp = $temp;
                                    //if entity type is product then also add recipe items
                                    if (isset($linkedAttribute[0]->entity_id)) {
                                        $attribute_data = $this->_getRequestedAttributeData($entityType->identifier, $field_action->name, $linkedAttribute[0]->entity_id);
                                        if ($attribute_data) {
                                            // print_r($attribute_data); exit;
                                            $tempp["$attribute_data->key"] = $attribute_data->value;
                                        }

                                    }

                                    $data->attributes[ $field_action->name ] = $temp;

                                } else {
                                    $tempp = $temp;
                                    //if entity type is product then also add recipe items
                                    if (isset($linkedAttribute[0]->entity_id)) {
                                        $attribute_data = $this->_getRequestedAttributeData($entityType->identifier, $field_action->name, $linkedAttribute[0]->entity_id);
                                        if ($attribute_data) {
                                            // print_r($attribute_data); exit;
                                            $tempp["$attribute_data->key"] = $attribute_data->value;
                                        }

                                    }
                                    $data->{$field_action->name} = $tempp;
                                    //$data->{str_replace('_id', '', $field_action->name)} = $temp;

                                }

                            } else if (isset($attributeData->backend_table) && $attributeData->backend_table != '') {

                                // category or role attached to any entity type
                                $temp = explode("_", $attributeData->backend_table);
                                $DymamicModel = $this->_modelPath . strtoupper($temp[0]) . ucfirst($temp[1]);
                                unset($temp);
                                $model = new $DymamicModel;
                                if ($field_action->data_type_id == 9 || $field_action->data_type_id == 5) {
                                    foreach (explode(",", $field_action->json_data) as $tempList) {
                                        $temp[] = $model->getData($tempList);
                                    }

                                    if (!$is_mobile_request)
                                        $data->attributes[ $field_action->name ] = $temp;
                                    else
                                        $data->{$field_action->name} = $temp;
                                } else {
                                    //print_r($field_action);
                                    if ($field_action->data_type_id == '6' || $field_action->data_type_id == '11' || $field_action->data_type_id == '4')
                                        if (!$is_mobile_request)
                                            $data->attributes[ $field_action->name ] = $model->getData($field_action->attribute);
                                        else
                                            $data->{$field_action->name} = $model->getData($field_action->attribute);
                                }
                            } else {
                                if ($field_action->linked_entity_type_id == 0 && $attributeData->backend_table == '' && $field_action->data_type_id == 5) {
                                    $temp = array();
                                    foreach (explode(",", $field_action->json_data) as $tempList) {
                                        $temp[] = $this->_attributeOptionModel->getAttributeById($field_action->attribute_id, $tempList);
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
                $temp = array();
                foreach ($hooks as $hook) {
                    if (\Schema::hasTable($hook . '_flat')) {
                        $entity_values = $this->_dbLib->query->table($hook . '_flat')->select('*')
                            ->where($entityType->identifier . '_id', $id)
                            ->get();
                        if (count($entity_values))
                            foreach ($entity_values as $entity_value) {
                                $temp[] = $this->getData($entity_value->entity_id, array('mobile_json' => $is_mobile_request, 'in_detail' => $in_detail));
                            }

                        $data->{$hook} = $temp;
                    }
                }

            }


            // extra implementation to supportive fields or tables.
            if (isset($entityType->identifier)) {

                $obj = new EntityTrigger();
                $func = CustomHelper::convertToCamel($entityType->identifier . '_get_trigger');

                if (method_exists($obj, "$func"))
                    $listing_trigger = $obj->$func($request_params);

                if (isset($request_params->entity_id)) {
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
        if (isset($listOfAttributeToBeValidate[0])) {
            foreach ($listOfAttributeToBeValidate as $result) {
                $rules[ $result->attribute_code ] = $result->validation;
            }
            $attributes_error = 0;
        }

        $validator = Validator::make((array)$request_params, $rules);

        // validate
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
            //$this->_apiData['message'] = $validator->errors()->first();
            $is_error = TRUE;
        } else if ($attributes_error > 0) {
            throw new \Exception('No attributes defined');
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

            $this->_apiData['data'] = $this->getData($id, $request);
            $this->_apiData['response'] = $this->_apiData['data'] ? 'success' : $this->_apiData['data'];

        } catch (\Exception $e) {
            //  echo $e->getTraceAsString(); exit;
            $this->_apiData['message'] = $e->getMessage();
            $this->_apiData['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine() . " : Stack " . $e->getTraceAsString();
        }

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
        $depend_entity = isset($request->depend_entity) ? isset($request->depend_entity) : [];
        // print_r($request);
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
        //print_r($request);
        //  print_r($depend_entity); exit;
        $request->entity_auth_id = isset($request->entity_auth_id) ? $request->entity_auth_id : 0;
        $listOfAttributeToBeValidate = $listOfAttributeToBeInserted = [];

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

            $listOfAttributeToBeValidate = $this->_entityAttributeModel->getEntityAttributeValidationList($request->entity_type_id, '');
            $listOfAttributeToBeInserted = $this->_entityAttributeModel->getEntityAttributeFields($request->entity_type_id);
            $response_validator = $this->_postValidator($request_params, $listOfAttributeToBeValidate);

            $func = CustomHelper::convertToCamel($this->_eTypeData->identifier . '_verify_trigger');
            if (method_exists($obj, "$func")) {
                $verify_response = $obj->$func((array)$request);
                if ($verify_response['error'] == TRUE) {
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
                        $verify_response = $obj->$func($request->all());
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
        $listOfAttributeToBeInserted = $this->_entityAttributeModel->getEntityAttributeFields($request->entity_type_id);

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

                            $entity_value = $this->_dbLib->query->table('sys_attribute_option')->select('option')
                                ->where('value', $request->{$field->attribute_code})
                                ->where('attribute_id', $field->attribute_id)
                                ->first();
                            $entity_value = json_decode(json_encode($entity_value));

                            if ($entity_value) {
                                $flat_entity[ $field->attribute_code ] = $entity_value->option;
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
            $this->_apiData['response'] = $this->_apiData['data'] ? 'success' : $this->_apiData['data'];

        } catch (\Exception $e) {
            $this->_apiData['message'] = $e->getMessage();
            $this->_apiData['debug'] = $e->getMessage() . ', ' . $e->getFile() . ', ' . $e->getLine();
        }

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
        /* if (!$this->_saveValidator($request, $listOfAttributeToBeValidate))
             return $this->_apiData;*/

        $func = CustomHelper::convertToCamel($entityTypeData->identifier . '_verify_trigger');

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
        if ($entityTypeData->allow_auth == 1 && $entityTypeData->allow_backend_auth == 1) {
            // load sys entity auth
            $entityAuthModel = new SYSEntityAuth();
            $entity_auth = $entityAuthModel->entityQuery($request->entity_type_id)
                ->where("entity." . $this->_entityModel->primaryKey, "=", $request->{$this->_entityModel->primaryKey})
                ->select("auth." . $entityAuthModel->primaryKey)
                ->first();

            if (isset($entity_auth->{$entityAuthModel->primaryKey})) {
                $entity[ $entityAuthModel->primaryKey ] = $entity_auth->{$entityAuthModel->primaryKey};
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
                $obj->$func((array)$request);

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

        return $entity_id;
    }


    /**
     * Save validator  (private)
     *
     * @param Request $request
     * @param $listOfAttributeToBeValidate
     * @return bool
     */
    private function _saveValidator($request, $listOfAttributeToBeValidate)
    {
        $request = is_object($request) ? $request : (object)$request;

        $rules = array(
            'entity_type_id' => 'integer|exists:' . $this->_eTypeModel->table . ","
                . $this->_eTypeModel->primaryKey . ",deleted_at,NULL",
            'entity_id' => 'integer|exists:' . $this->_entityModel->table . ","
                . $this->_entityModel->primaryKey . ",deleted_at,NULL"
        );

        $entity_type_data = $this->_getEntityTypeDataFromRequest($request);

        if ($entity_type_data->allow_auth == 1 && $entity_type_data->allow_backend_auth == 1) {
            if ($entity_type_data->identifier != "customer" && !(isset($request->is_profile_update))) {
                $rules['role_id'] = 'required';
            }

        }

        $request_params = (array)$request;
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


        $validator = Validator::make((array)$request, $rules);
        // validator 2 for verifying correct entity_type with entity_id
        $validator2 = Validator::make((array)$request, array(
            'entity_id' => 'required|integer|exists:' . $this->_entityModel->table . ','
                . $this->_entityModel->primaryKey
                . ',entity_type_id,' . request('entity_type_id', 0)
                . ',deleted_at,NULL'
        ));
        if (request('entity_type_id', 0) > 0 && $validator2->fails()) {
            throw new \Exception($validator2->errors()->first());
        } else if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

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
            $this->_apiData['response'] = $this->_apiData['data'] ? 'success' : $this->_apiData['data'];

        } catch (\Exception $e) {
            $this->_apiData['message'] = $e->getMessage();
            $this->_apiData['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine();
        }

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
            $this->_apiData['response'] = "success";

            // get
            $record = $this->_entityModel->get($request->{$this->_entityModel->primaryKey});
            // delete data to sys_entity_auth table
            if (isset($record->entity_auth_id) && $record->entity_auth_id > 0) {
                $this->_entityAuthModel->remove($record->{$this->_entityModel->primaryKey});
            }
            // delete data to sys_entity table
            $this->_entityModel->remove($request->{$this->_entityModel->primaryKey});

            if ($record) {
                $entity_type = $this->_eTypeModel->getData($record->entity_type_id);
                if ($entity_type->use_flat_table == "1") {
                    // remove from flat
                    if (\Schema::hasTable($entity_type->identifier . '_flat')) {
                        // remove from flat table
                        //$this->_entityModel->table($entity_type->identifier . '_flat')->remove($record->entity_id);
                        $flat_obj = new SYSTableFlat($entity_type->identifier, 'id');
                        // delete data to flat table
                        $id = $flat_obj->softDeleteFlat($record->entity_id);
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
            $this->_apiData['data'] = $data;
            $this->_apiData['response'] = $this->_apiData['data'] ? 'success' : $this->_apiData['data'];

        } catch (\Exception $e) {
            $this->_apiData['message'] = $e->getMessage();
            $this->_apiData['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine();
        }

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
            throw new \Exception(trans('api_errors.pls_enter_entity_id', ["entity" => $this->_objectIdentifier]));
        } else {
            $data = $this->getData($request->{$this->_entityModel->primaryKey}, $request);
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
            $this->_apiData['response'] = $this->_apiData['data'] ? 'success' : $this->_apiData['data'];

        } catch (\Exception $e) {
            $this->_apiData['message'] = $e->getMessage();
            $this->_apiData['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine();
        }

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
        // init data
        $data = [];
        // validations
        $rules = [
            // 'entity_id' => 'required|integer|exists:' . $this->_entityModel->table . "," . $this->_entityModel->primaryKey . ",deleted_at,NULL",
            'entity_type_id' => 'integer|exists:' . $this->_eTypeModel->table . "," . $this->_eTypeModel->primaryKey . ",deleted_at,NULL",
        ];

        $validator = Validator::make((array)$request, $rules);
        // validator 2 for verifying correct entity_type with entity_id

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        } else {

            if (isset($request->entity_type_id) && is_numeric($request->entity_type_id)) {
                if (is_numeric($request->entity_type_id)) {
                    $params['entityTypeData'] = $this->_eTypeModel->getEntityTypeById($request->entity_type_id);
                    $identifier = $params['entityTypeData']->identifier;
                }
            }

            $request->order_by = isset($request->order_by) ? $request->order_by : '';
            $request->sorting = isset($request->sorting) ? $request->sorting : '';
            $request->limit = isset($request->limit) ? $request->limit : '';
            $request->offset = isset($request->offset) ? $request->offset : 0;


            $params['allowed_ordering'] = $params['allowed_searching'] = "identifier,created_at,entity_id";
            $params['allowed_sorting'] = "asc,desc";
            $params['order_by'] = $request->order_by == "" ? explode(",", $params['allowed_ordering'])[0] : $request->order_by;
            $params['sorting'] = $request->sorting == "" ? explode(",", $params['allowed_sorting'])[0] : $request->sorting;
            $params['limit'] = ($request->limit == "") ? PAGE_LIMIT_API : intval($request->limit);
            $params['offset'] = intval($request->offset);
            $params['show_in_list'] = 1;
            // echo "<pre>";  print_r($params); exit;
            $response = $this->_entityModel->getListData($request->entity_id, $request->entity_type_id, $request, $params);

            $response['data'] = isset($response['data']) ? $response['data'] : "";

            // extra implementation to supportive fields or tables.
            if (isset($params['entityTypeData']->identifier)) {

                $obj = new EntityTrigger();
                $func = CustomHelper::convertToCamel($params['entityTypeData']->identifier . '_listing_trigger');

                if (method_exists($obj, "$func"))
                    $listing_trigger = $obj->$func($request);

                if (isset($request->entity_id)) {
                    if (isset($listing_trigger['key']) && isset($listing_trigger['data'])) {
                        $response['data'][0]->{$listing_trigger['key']} = $listing_trigger['data'];
                    }
                }

            }

            $data[ $identifier ] = $response['data'];
            if (intval($request->is_mobile_request) > 0) {
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

        return $data;
    }

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
            if (method_exists($obj, "$func")) $obj->$func($request, $depend_entity_row);
            //$obj->$func($request, $depend_entity_row);
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
}
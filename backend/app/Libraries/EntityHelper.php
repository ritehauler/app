<?php namespace App\Libraries;

use App\Http\Controllers\Panel\EntityBackController;
use App\Http\Models\PLAttachment;
use App\Http\Models\SYSAttributeOption;
use App\Http\Models\SYSEntity;
use App\Http\Models\SYSEntityRoleMap;
use App\Http\Models\SYSRole;
use App\Libraries\System\Entity;
use Illuminate\Http\Request;
use App\Http\Models\ApiMethodField;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
/**
 * Class CustomHelper
 */
Class EntityHelper
{
    public static $mobileJson = 0;
    private $_model_path = '';
    private $_apiMethodFieldsObj = "";
    private $_apiUrl = '';
    /**
     * Constructor
     *
     * @param string $url URL
     */
    public function __construct()
    {
        $this->_model_path = config("system.MODEL_PATH");
        $this->_apiMethodFieldsObj = new ApiMethodField();
        $this->_apiUrl = config("system.API_SYSTEM_ENTITIES");
    }


    /**
     * Validate Entity
     * @param $params
     * @param bool $is_update
     * @return mixed
     */
    public function validateEntity($params,$is_update = false)
    {
        $attr_model = $this->_model_path . "SYSEntityAttribute";
        $attr_model = new $attr_model;

        $entity_model = $this->_model_path . "SYSEntity";
        $entity_model = new $entity_model;

        if($is_update){
            $listOfAttributeToBeValidate = $attr_model->getEntityAttributeValidationListForUpdate($params['entity_type_id'],$params['entity_id']);
            $response = $entity_model->updateValidator($params,$listOfAttributeToBeValidate);
        }else{
            $listOfAttributeToBeValidate = $attr_model->getEntityAttributeValidationList($params['entity_type_id'],'');
            $response = $entity_model->addValidator($params,$listOfAttributeToBeValidate);
        }


        return $response;
    }

    /**
     * Sort entity attributes by sort order key
     * @param $entity_attributes
     * @return array
     */
    public function sortEntityAttributes($entity_attributes)
    {
        if(count($entity_attributes)){

            $sorted_entity_attributes = array();
            foreach($entity_attributes as $entity_attr){

                $sort_order = isset($entity_attr->sort_order) ? $entity_attr->sort_order-1 : 0;
                $sorted_entity_attributes[$sort_order] = $entity_attr;
            }

            ksort($sorted_entity_attributes);// echo "<pre>"; print_r($sorted_entity_attributes); exit;
            return $sorted_entity_attributes;
        }

        return $entity_attributes;
    }

    /**
     * Get Depend entity Fields and separate hidden
     * and other fields in different array
     * @param $depend_entity_type_id
     * @return array
     */

    public function getDependEntityFields($depend_entity_type_id)
    {
        $depend_entity_fields = array();
        $depend_entity_hidden_fields = array();

        $depend_entity_attributes = $this->_apiMethodFieldsObj->getEntityAttributeList($depend_entity_type_id);

        if ($depend_entity_attributes) {

            foreach ($depend_entity_attributes as $entity_field) {
                if ($entity_field->data_type_identifier == "hidden") {
                    $entity_field->is_entity_column = 1;
                    $depend_entity_hidden_fields[] = $entity_field;
                } else {
                    $entity_field->is_entity_column = 1;
                    $depend_entity_fields[] = $entity_field;
                }
            }
        }

        return array('hidden_fields' => $depend_entity_hidden_fields, 'entity_fields' => $depend_entity_fields);
    }

    /**
     * Get entity Fields and separate hidden
     * and other fields in different array
     * @param $entity_data
     * @param $request
     * @return array
     */
    public function getEntityFields($entity_data,$request = false)
    {
        $entity_attributes = $this->_apiMethodFieldsObj->getEntityAttributeList($entity_data->entity_type_id);

        $entity_fields = array();
        $entity_hidden_fields = array();
        if ($entity_attributes) {

            foreach ($entity_attributes as $entity_field) {
                if ($entity_field->data_type_identifier == "hidden") {
                    $entity_field->is_entity_column = 1;
                    $entity_hidden_fields[] = $entity_field;
                } else {

                    /* if entity type is product or recipe then do not display product type in form and post as hidden field*/

                    if ($entity_data->identifier == "product" || $entity_data->identifier == "recipe") {

                        if($entity_data->identifier == "product")
                            $type_attribute_code = "product_type";
                        else
                            $type_attribute_code = "recipe_type";

                        if($entity_field->attribute_code == $type_attribute_code){

                            $entity_field->is_entity_column = 1;
                            $entity_field->data_type_identifier = "hidden";
                            $entity_field->entity_attr_default_value = $request->{$type_attribute_code};
                            $entity_hidden_fields[] = $entity_field;
                            continue;
                        }

                    }

                    $entity_field->is_entity_column = 1;
                    $entity_fields[] = $entity_field;
                }
            }
        }

        return array('hidden_fields' => $entity_hidden_fields, 'entity_fields' => $entity_fields);

    }

    public function getEntityAndDependEntityFields($base_data, $request = false)
    {
        if (isset($base_data['entity_data'])) {
            $entity_data = $base_data['entity_data'];

            //Api fields
            if (count($base_data['records']) > 0) {

                $api_hidden_fields = array();
                $api_fields = array();
                foreach ($base_data['records'] as $record) {

                    if ($record->element_type == "hidden") {
                        $record->is_entity_column = 0;
                        $api_hidden_fields[] = $record;
                    } else if ($record->element_type == "query" || $record->data_type == "callback") {
                        continue;
                    } else {

                        /* if entity type is auth then unset auth attributes b/c on update only entity data will be update*/
                        if ($entity_data->allow_auth == 1 && $entity_data->allow_backend_auth == 1) {
                            $check_fields = array('is_auth_exists', 'entity_auth_id', 'password','role_id', 'parent_role_id');

                            if (in_array($record->name, $check_fields)) {
                                continue;
                            }
                        }


                        $record->is_entity_column = 0;
                        $record->sort_order = $record->order;
                        $api_fields[] = $record;
                    }
                }
            }

           // print_r($api_fields); exit;
            //Get Entity fields which are map to entity type
            $get_entity_fields = $this->getEntityFields($entity_data, $request);
            $entity_fields = $get_entity_fields['entity_fields'];
            $entity_hidden_fields = $get_entity_fields['hidden_fields'];

            //Merge entity attributes and api fields
            if (count($entity_fields) > 0 && count($api_fields) > 0) {
                $entity_attributes = array_merge($api_fields, $entity_fields);
            } else if (count($api_fields) > 0) {
                $entity_attributes = $api_fields;
            } else {
                $entity_attributes = $entity_fields;
            }

            //merge hidden fields for entity attributes and api fields
            if (count($api_hidden_fields) > 0 && count($entity_hidden_fields) > 0) {
                $entity_hidden_attributes = array_merge($api_hidden_fields, $entity_hidden_fields);
            } else if (count($api_hidden_fields) > 0) {
                $entity_hidden_attributes = $api_fields;
            } else {
                $entity_hidden_attributes = $entity_hidden_fields;
            }
            //echo "<pre>";  print_r($entity_attributes); exit;
            //Sorting of api fields attributes and entity attributes
            $sorted_entity_attributes = $this->sortEntityAttributes($entity_attributes);

            $base_data['records'] = $sorted_entity_attributes;
            $base_data['hidden_records'] = $entity_hidden_attributes;

            /* Now check and get if depend entity exist then get its attributes*/
            // if ($entity_data->identifier == "discount_promotion") {}

            if (isset($entity_data->depend_entity_type) && isset($base_data['depend_entity_type_data'])) {

                $depend_fields = $this->getDependEntityFields($base_data['depend_entity_type_data']->entity_type_id);
                $base_data['depend_entity_records'] = $depend_fields['entity_fields'];
                $base_data['depend_entity_hidden_records'] = $depend_fields['hidden_fields'];

            }

        }

        return $base_data;
    }

    /**
     * @param $params
     * @param bool $is_update
     * @return mixed
     */
    public function validationEntity($params,$is_update = false)
    {
        $assignData['error'] = 0;
        $assignData['message'] = 'success';

            if(isset($params['depend_entity'])) {
                $depend_params = $params['depend_entity'];
                unset($params['depend_entity']);
            }

            //Validate Order
            $entity_validate = $this->validateEntity($params, $is_update);
            if ($entity_validate['error'] == 1) {
                $assignData['error'] = 1;
                $assignData['message'] = $entity_validate['message'];
                return $assignData;
            } else {

                //Verify Trigger



                //Validate Order Items
                if (count($depend_params) > 0) {

                    foreach ($depend_params as $depend_param) {

                        $entity_depend_validate = $this->validateEntity($depend_param,$is_update);

                        if ($entity_depend_validate['error'] == 1) {
                            $assignData['error'] = 1;
                            $assignData['message'] = $entity_depend_validate['message'];
                            return $assignData;
                        }
                    }

                    //Verify Dependent Trigger


                }
            } //end of validation of depend item
        //}

        return $assignData;
    }

    /**
     * if attribute value is dropdown option then return the value
     * @param $attribute
     * @return mixed
     */
    public static function parseAttributeValue($attribute)
    {
        if(is_object($attribute)){
            if(isset($attribute->id)){
                return $attribute->id;
            }
            else if(isset($attribute->value)){
                return $attribute->value;
            }
            else{
                return $attribute;
            }
        }
        else if (is_array($attribute)) {

            if(count($attribute) > 0) {
                return self::_parseArrayAttribute($attribute);
            }
        }
        return $attribute;
    }

    /**
     * Parse Attribute to display
     * @param $attribute
     * @return mixed
     */
    public static function parseAttributeToDisplay($attribute,$listing_field = false)
    {
        if(is_object($attribute)){

            if(isset($attribute->category_id)){
                return $attribute->title;
            }

            if(isset($attribute->option)){
                return $attribute->option;
            }
           else if(isset($attribute->value)){
                return $attribute->value;
            }
            else{

                //check if value from backend table
                if($listing_field){

                    if($listing_field->backend_table_value != ""){

                       // echo "<pre>"; print_r($listing_field);
                      //  echo "<pre>"; print_r($attribute);
                        if(isset($attribute->{$listing_field->backend_table_value})){
                            return $attribute->{$listing_field->backend_table_option};
                        }
                    }
                }


                return $attribute;
            }
        }
        else if (is_array($attribute)) {
           return self::_parseArrayAttribute($attribute);
        }
        return $attribute;

    }

    /**
     * Parse array value
     * @param $attribute
     * @return bool|string
     */
    public static function _parseArrayAttribute($attribute,$get_value = false)
    {
        if(count($attribute) > 0){

            $cat = array();
            if (isset($attribute[0]->category_id)) {
                if($get_value)
                    $fetch_key = 'category_id';
                else
                    $fetch_key = 'title';
            }
            else{
                if($get_value)
                    $fetch_key = 'id';
                else
                    $fetch_key = 'value';
            }

            if (isset($attribute[0])) {
                foreach ($attribute as $val) {
                    if(isset($val->{$fetch_key}))
                        $cat[] = $val->{$fetch_key};
                }
            }

            if(count($cat) > 0)
                return implode(',', $cat);

        }
        else{
            return false;
        }
    }

    /**
     * Get Extra data for attributes
     * @param $entity_type_identifier
     * @param $attribute_code
     * @param $attribute_code_value
     * @return array
     */
    public function getAttributeData($entity_type_identifier,$attribute_code,$attribute_code_value){

        $return = array();
        if($entity_type_identifier == 'product'){

            if($attribute_code == "product_recipe_id"){

                $entity_lib = new Entity();
                if(isset($attribute_code_value) && !empty($attribute_code_value)){

                    if(\Schema::hasTable('recipe_item_flat')){
                        $entity_values = \DB::table('recipe_item_flat')->select('*')
                            ->where('recipe_id', $attribute_code_value)
                            ->get();
                        if(count($entity_values))
                            foreach($entity_values as $entity_value){
                                $request_params['mobile_json'] = 1;
                                $recipe_item[] = $entity_lib->getData($entity_value->entity_id,$request_params);
                            }

                        if(isset($recipe_item) && count($recipe_item) > 0){
                            $return['key'] = 'recipe_item';
                            $return['value'] = $recipe_item;
                        }

                    }
                }
            }
        }
        return $return;
    }

    /**
     * Get entity list by entity type
     * @param $requested_params
     * @return mixed|object
     */
    public function getDataByEntityType($requested_params,$all_data = true)
    {
        if($all_data){
           $search_columns['limit'] = -1;
        }

        $search_columns['order_by'] = 'created_at';
        $search_columns['sorting'] = 'DESC';
       // $search_columns['mobile_json'] = 1;
        $search_columns['inner_response'] = 1;
        $params = array_merge($requested_params,$search_columns);
        $entity_lib = new Entity();
        $data = (object)$entity_lib->apiList($params);
        $data = json_decode(json_encode($data));
       // echo "<pre>"; print_r( $data); exit;
        return $data;
    }

    /**
     * Get List of Entity
     * @param $entity_type
     * @param $attributes
     * @param $requested_params
     * @return mixed
     */
    public function getEntityList($entity_type,$attributes,$requested_params)
    {
        $data = $this->getDataByEntityType($requested_params);
        //echo "<pre>"; print_r($data); exit;

        $listing = array();

        $allow_auth = $entity_type->allow_auth;

        if (isset($data->data->page->total_records) && $data->data->page->total_records > 0) {
            $entity_data = $data->data->entity_listing;
         //  echo "<pre>"; print_r($entity_data);
            foreach($entity_data as $entity){

                $entity_attr = $entity->attributes;

                    $list = array();
                    foreach ($attributes as $listing_field) {

                        $att_code = $listing_field->attribute_code;

                      // $field_value = empty($entity_attr->{$att_code}) ? '' : $entity_attr->{$att_code};
                        $field_value = ($entity_attr->{$att_code} != '' || $entity_attr->{$att_code} === 0) ? $entity_attr->{$att_code} : '';
                        //Get Attribute value to display

                       // echo "<pre>"; print_r($att_code.'-'.$field_value); continue;
                        $field_value = self::parseAttributeToDisplay($field_value,$listing_field);

                        //check if type is date
                        if(isset($listing_field->data_type_identifier) && $listing_field->data_type_identifier == "date"){
                            $date = (!empty($field_value)) ? date(DATE_FORMAT_ADMIN, strtotime($field_value)) : $field_value;
                            $field_value = $date;
                        }

                        /* check if entity attribute has front label then display it as field title otherwise attribute field*/
                        if(isset($listing_field->entity_attr_frontend_label) && !empty($listing_field->entity_attr_frontend_label)){
                            $field_title = $listing_field->entity_attr_frontend_label;
                        }
                        else{
                            $field_title = $listing_field->frontend_label;
                        }

                        $list[$att_code] = array('title'=> $field_title, 'value' => $field_value);
                    }
                    if($allow_auth == 1){

                        $list['email'] = array(
                            'title' => 'Email',
                            'value' => isset($entity->auth->email) ? $entity->auth->email : ""
                        );
                        $list['phone'] = array(
                            'title' => 'Phone',
                            'value' => isset($entity->auth->mobile_no) ? $entity->auth->mobile_no : ""
                        );
                        //add business user column
                        if($entity->object_key == 'business_user'){
                            $businessUserData = $this->_entityListColumns($entity,$list);
                            $list['parent_role_id'] = array(
                                'title' => 'Department',
                                'value' => $businessUserData['parent_role_id']
                            );
                            $list['role_id'] = array(
                                'title' => 'Designation',
                                'value' => $businessUserData['role_id']
                            );
                        }
                    }

               $listing[] = $list;
                unset($list);
            }
        }
      //  echo "<pre>"; print_r($listing); exit;
        return $listing;
    }

    private function _entityListColumns($entity_data,$list)
    {
        if(isset($entity_data->auth)){
            /* Get role title if user has assigned role*/
            $list["parent_role_id"] = "";
            $list["role_id"] = "";

            //get designation title
            $roleMapModel = new SYSEntityRoleMap();
            $role = $roleMapModel->getRoleInfoByEntity($entity_data->entity_id);
            if ($role) {
                if(isset($role->role_id)){
                    $list["role_id"] = isset($role->title) ? $role->title : "";

                    //get department title
                    $role_model = new SYSRole();
                    $list["parent_role_id"] =  $role_model->getRoleTitleById($role->parent_id);
                }
            }
        }
        return $list;
    }
    /**
     * Get values of dropdown, entity drop down and
     * other data type values from options and attribute id
     * @param $attribute
     * @param $field_value
     * @return bool|string
     */
    public function getFieldValueFromOption($attribute,$field_value)
    {
        if(empty($field_value)) return false;

        $attribute_option_model = new SYSAttributeOption();
        $entity_model = new SYSEntity();

        Switch($attribute->data_type_identifier){
            case 'dropdown':
            case 'yes_no':
            case 'dropdown2':
            case 'radio':
                if (isset($attribute->backend_table) && $attribute->backend_table != '') {
                    //Get value from backend_table
                   return $entity_model->getBackendTableValueByOption($attribute->backend_table,$attribute->backend_table_option,$attribute->backend_table_value,$field_value);
                }
                else{
                    //Get value from attribute options
                    $attribute_option_data = $attribute_option_model->getAttributeOptionValueById($attribute->attribute_id,'',$field_value);
                   // print_r($attribute_option_data); exit;
                    return ($attribute_option_data) ?  $attribute_option_data->value : false;
                }
            case 'entity_select':
                //Get value from entity type linked attribute
                if($attribute->linked_entity_type_id > 0){
                    return $entity_model->getLinkedAttributeEntityID($attribute->linked_entity_type_id,$attribute->linked_attribute_id,$field_value);
                }
            case 'textfield':
                if($attribute->php_data_type == 'comma_separated'){
                    //Get value from entity type linked attribute
                    if($attribute->linked_entity_type_id > 0){

                        $temp_val = array();
                        foreach (explode(",", $field_value) as $field_val) {

                            if(isset($backend_field_val)) unset($backend_field_val);

                            $backend_field_val =  $entity_model->getLinkedAttributeEntityID($attribute->linked_entity_type_id,$attribute->linked_attribute_id,$field_val);

                            if($backend_field_val)
                                $temp_val[] = $backend_field_val;
                        }

                        return (count($temp_val)>0) ? implode(',',$temp_val) : false;

                    }
                    //Get Data from backend table - - Multi Option
                    else if (isset($attribute->backend_table) && $attribute->backend_table != '') {

                        $temp_val = array();
                        foreach (explode(",", $field_value) as $field_val) {

                            if(isset($backend_field_val)) unset($backend_field_val);

                            $backend_field_val = $entity_model->getBackendTableValueByOption($attribute->backend_table,$attribute->backend_table_option,$attribute->backend_table_value,$field_val);
                            if($backend_field_val)
                                $temp_val[] = $backend_field_val;
                        }

                        return (count($temp_val)>0) ? implode(',',$temp_val) : false;

                    }
                    else{
                        //Get value from attribute options - Multi Option
                        $temp_val = array();
                        foreach (explode(",", $field_value) as $field_val) {

                            if(isset($backend_field_val)) unset($backend_field_val);

                            $backend_field_val =  $attribute_option_data = $attribute_option_model->getAttributeOptionValueById($attribute->attribute_id,'',$field_val);
                            if($backend_field_val)
                                $temp_val[] =  $attribute_option_data->value;
                        }

                        return (count($temp_val)>0) ? implode(',',$temp_val) : false;
                    }
                }

               return $field_value;
            default:
            return $field_value;
        }
    }

    /**
     * add status condition in entity listing query for dropdown
     * @param $entity_type_identifier
     * @param $where_condition
     * @return string
     */
    public static function getStatusConditionByEntityType($entity_type_identifier,$where_condition)
    {
        if(in_array($entity_type_identifier,array('super_admin','business_user','customer'))){
            $status_label = 'user_status';
        }
        elseif ($entity_type_identifier == 'item'){
            $status_label = 'item_status';
        }
        else{
            $status_label = 'status';
        }

        if(Schema::hasColumn($entity_type_identifier.'_flat', $status_label))
        {
            if(!empty($where_condition))
                $where_condition .= ' AND `'.$status_label.'` = 1';
            else
                $where_condition .= ' `'.$status_label.'` = 1';
        }
        return $where_condition;
    }

    public static function extractEntityID($raws)
    {
        if($raws){
            foreach($raws as $ids){
                $return[] = $ids->entity_id;
            }
            return $return;
        }

        return false;
    }

}
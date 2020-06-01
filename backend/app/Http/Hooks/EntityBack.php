<?php namespace App\Http\Hooks;

// models
#use App\Http\Models\Achievement;
use App\Http\Models\SYSTableFlat;
use App\Libraries\CustomHelper;
use App\Http\Models\ApiMethodField;
use App\Libraries\Fields;
use App\Libraries\OrderHelper;
use App\Libraries\ProductHelper;
use App\Libraries\ConfigCollection;
use App\Libraries\EntityHelper;
use App\Libraries\System\Entity;

class EntityBack
{
    private $_modelPath = '';
    private $_apiMethodFieldsObj = "";
    private $_entityHelperObj = "";

    public function __construct()
    {
        $this->_apiMethodFieldsObj = new ApiMethodField();
        $this->_entityHelperObj = new EntityHelper();
        $this->_modelPath = config("system.MODEL_PATH");
    }

    /**
     * @param $request
     * @param $base_data
     * @return mixed
     */
    public function add($request, $base_data)
    {
        $entity_data = $base_data['entity_data'];

        if (count($base_data['records']) > 0) {
            $api_hidden_fields = array();
            $api_fields = array();

           // echo "<pre>"; print_r($base_data['records']); exit;
            foreach ($base_data['records'] as $record) {

                if ($record->element_type == "hidden") {
                    $record->is_entity_column = 0;
                    $api_hidden_fields[] = $record;
                } else if ($record->element_type == "query" || $record->data_type == "callback") {
                    continue;
                } else {

                    /* if entity type is auth then few attributes donot need to add on form ignore these fields */
                    if ($entity_data->allow_auth == 1 && $entity_data->allow_backend_auth == 1) {
                        $check_fields = array('is_auth_exists', 'entity_auth_id');

                        if ($entity_data->identifier != "business_user") {
                            $check_fields[] = 'parent_role_id';
                            $check_fields[] = 'role_id';
                        }


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

        //Get list of attributes which are map with entity type
        $get_entity_fields = $this->_entityHelperObj->getEntityFields($entity_data,$request);
        $entity_fields  = $get_entity_fields['entity_fields'];
        $entity_hidden_fields =  $get_entity_fields['hidden_fields'];


        //check count of entity attributes and merge with fields which are coming from api method fields table
        if (count($entity_fields) > 0 && count($api_fields) > 0) {
            $entity_attributes = array_merge($api_fields, $entity_fields);
        } else if (count($api_fields) > 0) {
            $entity_attributes = $api_fields;
        } else {
            $entity_attributes = $entity_fields;
        }

        //check count of hidden entity attributes and merge with hidden fields of api method fields table
        if (count($api_hidden_fields) > 0 && count($entity_hidden_fields) > 0) {
            $entity_hidden_attributes = array_merge($api_hidden_fields, $entity_hidden_fields);
        } else if (count($api_hidden_fields) > 0) {
            $entity_hidden_attributes = $api_fields;
        } else {
            $entity_hidden_attributes = $entity_hidden_fields;
        }

        //Sorting of api fields attributes and entity attributes
        $sorted_entity_attributes = $this->_entityHelperObj->sortEntityAttributes($entity_attributes);

        $base_data['records'] = $sorted_entity_attributes;
        $base_data['hidden_records'] = $entity_hidden_attributes;


        /* Now check and get if depend entity exist then get its attributes*/
        if (isset($entity_data->depend_entity_type) && is_numeric($entity_data->depend_entity_type)) {

            $depend_fields = $this->_entityHelperObj->getDependEntityFields($base_data['depend_entity_type_data']->entity_type_id);
            $base_data['depend_entity_records'] = $depend_fields['entity_fields'];
            $base_data['depend_entity_hidden_records'] =  $depend_fields['hidden_fields'];

        }


        if ($entity_data->identifier == "custom_notification") {
            $conf = new ConfigCollection();
            $placeholders = $conf->getNotifyPlaceHolder();
            $base_data['notify_placeholders'] = $placeholders;
        }

        return $base_data;
    }

    /**
     * Post
     * @param object $request
     * @param array $base_data
     * @return Object
     */
    public function update($request, $base_data)
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
                            $check_ignore_fields = array('is_auth_exists', 'entity_auth_id', 'password');

                            if ($entity_data->identifier != "business_user") { //if customer update then donot show role
                                $check_ignore_fields = array_merge($check_ignore_fields, array('role_id','parent_role_id'));
                            }

                            if (in_array($record->name, $check_ignore_fields)) {
                                continue;
                            }
                        }


                        $record->is_entity_column = 0;
                        $record->sort_order = $record->order;
                        $api_fields[] = $record;
                    }
                }
            }


            //Get Entity fields which are map to entity type
            $get_entity_fields = $this->_entityHelperObj->getEntityFields($entity_data,$request);
            $entity_fields  = $get_entity_fields['entity_fields'];
            $entity_hidden_fields =  $get_entity_fields['hidden_fields'];

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
            $sorted_entity_attributes = $this->_entityHelperObj->sortEntityAttributes($entity_attributes);

            $base_data['records'] = $sorted_entity_attributes;
            $base_data['hidden_records'] = $entity_hidden_attributes;

            /* Now check and get if depend entity exist then get its attributes*/
            // if ($entity_data->identifier == "discount_promotion") {}

            if (isset($entity_data->depend_entity_type) && isset($base_data['depend_entity_type_data'])) {

                $depend_fields = $this->_entityHelperObj->getDependEntityFields($base_data['depend_entity_type_data']->entity_type_id);
                $base_data['depend_entity_records'] = $depend_fields['entity_fields'];
                $base_data['depend_entity_hidden_records'] =  $depend_fields['hidden_fields'];

            }
            // }


            //condition on entity data
            if(isset($base_data['update'])){


            }


        }

        return $base_data;
    }

    /**
     * @param $request
     * @param $base_data
     */
    public function view($request, $base_data)
    {
        if (isset($base_data['entity_data'])) {

            $entity_data = $base_data['entity_data'];

            if ($entity_data->identifier == "product") {

                if(isset($base_data['update_data']['entity_type_id']) && isset($base_data['update_data']['entity_id'])){

                    //Getting the product reviews
                    $search_columns['target_entity_type_id'] = $base_data['update_data']['entity_type_id'];
                    $search_columns['target_entity_id'] = $base_data['update_data']['entity_id'];
                    $search_columns['actor_entity_type_id'] = 11;

                    $reviews_response = CustomHelper::internalCall($request,\URL::to(DIR_API) . '/extension/social/package/rate/listing', 'GET', $search_columns,false);
                    $reviews = array();

                    if(isset($reviews_response->data->rate_listing)){

                        foreach($reviews_response->data->rate_listing as $rating){

                            $review = (object)array();

                            $review->created_at = date(DATE_FORMAT_ADMIN, strtotime($rating->created_at));

                            $review->rating = "";
                            if($rating->rating > 0){

                                for($i=0; $i < $rating->rating; $i++){
                                    $review->rating .= " &bigstar;";
                                }
                            }

                            $review->review = $rating->review;
                            $review->customer_name = "";

                            if(isset($rating->actor_entity->attributes)){

                                if(isset($rating->actor_entity->attributes->full_name)){
                                    $review->customer_name = $rating->actor_entity->attributes->full_name;
                                }
                                else{
                                    if(isset($rating->actor_entity->attributes->last_name)){
                                        $review->customer_name .= $rating->actor_entity->attributes->last_name.", ";
                                    }

                                    if(isset($rating->actor_entity->attributes->first_name)){
                                        $review->customer_name .= $rating->actor_entity->attributes->first_name;
                                    }
                                }
                            }

                            $reviews[] = $review;

                        }
                    }

                    $base_data['reviews'] = $reviews;

                }
            }

            if ($entity_data->identifier == "order") {
                $order_helper = new OrderHelper();
                $order_revisions = $order_helper->getOrderRevision($base_data['update_data']['entity_id']);
                if($order_revisions) $base_data['order_revisions'] = $order_revisions;
            }

        }

        return $base_data;
    }

   
}
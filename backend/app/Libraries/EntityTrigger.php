<?php namespace App\Libraries;

use App\Http\Models\FlatTable;
use App\Http\Models\Notification;
use App\Http\Models\PLAttachment;
use App\Http\Models\SYSEntity;
use App\Http\Models\SYSEntityAuth;
use App\Http\Models\SYSEntityHistory;
use App\Http\Models\SYSEntityType;
use App\Http\Models\SYSTableFlat;
use App\Libraries\System\Entity;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class CustomHelper
 */
Class EntityTrigger
{
    public $entityRequest = [];
    public $_entityData = [];
    private $_apiUrl = '';
    private $_apiUrlListing = '';
    private $_apiUrlUpdate = '';
    private $_tempData = [];
    private $_isUpdateAuth = 0;
    private $_entityLib;

    /**
     * Constructor
     *
     * @param string $url URL
     */
    public function __construct()
    {
        $this->_apiUrl = config("system.API_SYSTEM_ENTITIES");
        $this->_apiUrlListing = $this->_apiUrl . 'listing';
        $this->_apiUrlUpdate = $this->_apiUrl . 'update';
        $this->_entityLib = new Entity();
    }

    /**
     * @param Request $request
     */
    public function customNotificationAddTrigger($request, $response_post = FALSE)
    {
        return;
        $request = is_array($request) ? (object)$request : $request;
        $entity_model = new Entity();
        $notification_model = new Notification;
        $notification_wildcards = new NotificationWildcard();
        
        if (isset($request->customer_ids)) {

            $customer_ids = explode(',', $request->customer_ids);

            if (count($customer_ids) > 0) {

                foreach ($customer_ids as $customer_id) {
                    $entity = $entity_model->getData($customer_id);
                    $entity = json_decode(json_encode($entity));

                    if (isset($entity->auth)) {
                        if (isset($entity->auth->device_type) && isset($entity->auth->device_token)) {

                            if (in_array($entity->auth->device_type, ['android', 'ios'])) {

                                if (isset($entity->attributes->is_notify->value) && $entity->attributes->is_notify->value == 1) {

                                    //Set Customer data to replace wildcards
                                    $replace = new \StdClass();
                                    $replace->user_name = isset($entity->attributes->first_name) ? $entity->attributes->first_name : "";

                                    if (isset($entity->attributes->last_name) && !empty($entity->attributes->last_name)) {
                                        $replace->user_name .= " ";
                                        $replace->user_name .= isset($entity->attributes->last_name) ? $entity->attributes->last_name : "";
                                    }

                                    $replace->email = isset($entity->auth->email) ? $entity->auth->email : "";
                                    $replace->mobile_no = isset($entity->auth->mobile_no) ? $entity->auth->mobile_no : "";

                                    //Replace WildCards
                                    $subject = $notification_wildcards->replaceNotifyText($replace, $request->subject);
                                    $message = $notification_wildcards->replaceNotifyText($replace, $request->message);

                                    // prepare notification data
                                    $notification_data = [
                                        "title" => $subject,
                                        "body" => $message,
                                        "key_code" => '',
                                        "sound" => "",
                                        "badge" => "",
                                        //"user" => $user ? $user : array(),
                                        //"target_user" => $target_user ? $target_user : array(),
                                        // "user_id" => $actor_entity->entity_id,
                                        // "target_user_id" => isset($target_entity->entity_id) ? $target_entity->entity_id : "",
                                        // "user_name" => isset($actor->first_name) ? $actor->first_name : "",
                                        //  "target_user_name" => "",
                                        "my_custom_data" => [
                                            'entity_id' => $customer_id,
                                            "user_name" => $replace->user_name,
                                            'identifier' => 'custom_notification'
                                        ]
                                    ];

                                    // send
                                    //if ($entity->auth->device_type == "android") {
                                    // echo "<pre>"; print_r($notification_data);
                                    $ret = $notification_model->pn_android($entity->auth->device_token, $notification_data);
                                    // echo "<pre>"; print_r($ret); exit;
                                    // } else {
                                    //   $notification_model->pn_ios($entity->auth->device_token, $notification_data);
                                    // }

                                }
                            }

                        }
                    }

                }
            }
        }

        return;
    }

    /**
     * Before post order
     * @param Request $request
     * @return array|bool
     */
    public function orderBeforePostTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        $order_helper = new OrderHelper();

        if (isset($request->order_status)) {

            if (!is_numeric($request->order_status)) {

                if (empty($request->order_status)) {
                    $status_key = 'pending';
                } else {
                    $status_key = $request->order_status;
                }

                $status_data = $order_helper->getOrderStatusData($status_key);
                if ($status_data) {
                    // $request->request->add(['order_status' => $status_data->entity_id]) ;
                    $return = ['order_status' => $status_data->entity_id];
                }
            }
        }

        if(isset($request->truck_selected_id)){

            $order_item_flat = new SYSTableFlat('truck_selected');
            $where_condition = ' entity_id = '.$request->truck_selected_id.' AND is_ordered = 0';
            $rows = $order_item_flat->getDataByWhere($where_condition);

            if(isset($rows[0])){

                $selected_truck = $rows[0];

                $this->_tempData['selected_truck'] = $truck_detail = json_decode($selected_truck->truck_detail);
                //echo "<pre>"; print_r($truck_detail); exit;
               // echo "<pre>"; print_r($selected_truck); exit;
                $return['truck_id'] = $truck_detail->entity_id;
                $return['min_estimated_charges'] = $truck_detail->est_min_charges;
                $return['max_estimated_charges'] = $truck_detail->est_max_charges;
                $return['estimated_distance'] = isset($truck_detail->total_distance) ? "$truck_detail->total_distance" : '';
                $return['estimated_minutes'] = "$truck_detail->total_minutes";

                $loading_price = '';
                if(!empty($selected_truck->loader_detail)){
                    $this->_tempData['selected_loader'] = $loader_detail = json_decode($selected_truck->loader_detail);

                    $return['professional_id'] = $loader_detail->entity_id;
                    $return['number_of_labour'] = $loader_detail->number_of_labour;

                   $loading_price = $loader_detail->price;
                }

                $return['loading_price'] = $loading_price;
                $return['base_fee'] = $truck_detail->base_fee;
                $return['charge_per_minute'] = $truck_detail->charge_per_minute;
                $return['truck_class'] = $truck_detail->truck_class_id->detail->truck_class->value;
                $return['pre_grand_total'] = $loading_price + $truck_detail->est_max_charges;

            }

        }

        //set the estimated delivery date
        if((isset($request->pickup_date) && !empty($request->pickup_date))
            && (isset($request->pickup_time) & !empty($request->pickup_time))){

            $order_helper = new OrderHelper();
            $return['estimated_delivery_date'] = $order_helper->estDeliveryData($request->pickup_date,$request->pickup_time);
            $return['delivery_date'] = $return['estimated_delivery_date'];
        }

      // echo "<pre>"; print_r($return); exit;
        if (isset($return)) {
            return $return;
        }

        return FALSE;
    }

    /**
     * Update order trigger before save
     * @param Request $request
     * @return array|bool
     */
    public function orderBeforeSaveTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        $post_param = (array)$request;
        $order_helper = new OrderHelper();

        if (isset($request->order_status)) {

            if (!is_numeric($request->order_status)) {

                if (!empty($request->order_status)) {
                    $status_key = $request->order_status;

                    $status_data = $order_helper->getOrderStatusData($status_key);
                    if ($status_data) {
                        $return['order_status'] = $status_data->entity_id;
                        // $request->request->add(['order_status' => $status_data->entity_id]);
                    }
                }
            }
        }

        //set the estimated delivery date
        if((isset($request->pickup_date) && !empty($request->pickup_date))
            && (isset($request->pickup_time) & !empty($request->pickup_time))){

            $order_helper = new OrderHelper();
            $return['estimated_delivery_date'] = $order_helper->estDeliveryData($request->pickup_date,$request->pickup_time);
        }

        if (isset($request->entity_id)) {

            //Update Order Number
            if(isset($request->order_number) && empty($request->order_number)){
                $return['order_number'] = config('constants.ORDER_SERIES').$request->entity_id;
            }


                //Save Previous Order in temp
                $pos_arr = [];
                $pos_arr['entity_type_id'] = $request->entity_type_id;
                $pos_arr['entity_id'] = $request->entity_id;
                $pos_arr['hook'] = "order_item";
                $pos_arr['inner_response'] = 1;
                //$pos_arr['mobile_json'] = 1;
               // $pos_arr['in_detail'] = 0;
                $data = (object)$this->_entityLib->apiGet($pos_arr);
                $data = json_decode(json_encode($data));
                //$data = CustomHelper::internalCall($request, $this->_apiUrl, 'GET', $pos_arr, FALSE);
                if (isset($data->error) && $data->error == 0) {

                    if(isset($this->_tempData['previous_order'])){
                        unset($this->_tempData['previous_order']);
                    }
                    $this->_tempData['previous_order'] = $data->data->entity;
                }
              //  $request->replace($post_param);

        }

        if(isset($return)){
            return $return;
        }

        return FALSE;
    }



    /**
     * Customer before post
     * @param Request $request
     * @return array
     */
    public function customerBeforePostTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        //set Full Name
        $return['full_name'] =  CustomHelper::setFullName($request);
        return $return;
    }

    /**
     * Customer before update
     * @param Request $request
     * @return array
     */
    public function customerBeforeSaveTrigger($request)
    {
        $return = array();
        $request = is_array($request) ? (object)$request : $request;

        //set Full Name
        if(isset($request->first_name) && isset($request->last_name)) {
            $return['full_name'] =  CustomHelper::setFullName($request);
        }

        //Update auth status for customer entity
        //if  requested status is different from the status that has saved
        if(isset($request->entity_id) && isset($request->user_status)){
            $this->_checkIfUpdateUserStatus($request);
        }

        if(count($return) > 0) return $return;
    }

    /**
     * Customer post trigger
     * @param Request $request
     */

    public function customerAddTrigger($request, $response_post = FALSE)
    {
         $request = is_array($request) ? (object)$request : $request;
    }

    /**
     * lead order before post trigger
     * @param Request $request
     * @return array
     */
    public function leadOrderBeforePostTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        if (isset($request->customer_id)) {

            if (!empty($request->customer_id)) {
                $transaction_reference = $request->customer_id . '-' . time();

                return ['transaction_reference' => $transaction_reference];
            }

        }

    }

    /**
     * Customer verify trigger
     * @param Request $request
     * @return mixed
     */
    public function customerVerifyTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        $response['error'] = 0;

        return $response;
    }

    /**
     * @param $request
     * @param bool $entity_id
     * @throws \Exception
     */
    public function orderAddTrigger($request, $entity_id = FALSE)
    {
        $request = is_array($request) ? (object)$request : $request;
        $entity_lib = new Entity();

        //Update Order Number
        $order_series = config('constants.ORDER_SERIES').$entity_id;
        $params = [];
        $params['entity_type_id'] = 15;
        $params['entity_id'] = $entity_id;
        $params['order_number'] = "$order_series";

        //Conversion of Pickup GMT to CST
        $order_flat = new SYSTableFlat('order');
        $order_raw = $order_flat->getDataByWhere(' entity_id = '.$entity_id,array('pickup_date','pickup_time'));

        if(isset($order_raw[0])){

            $pickup_date = $order_raw[0]->pickup_date.' '.$order_raw[0]->pickup_time;
            $pickup_date_obj = Carbon::createFromFormat('Y-m-d H:i:s', $pickup_date, APP_TIMEZONE);
            //$pickup_date_obj = Carbon::createFromFormat('Y-m-d H:i:s', $pickup_date, Datetime::getTimeZone());
           // $pickup_date_cst =  $pickup_date_obj->setTimezone(Datetime::getTimeZone());
            $pickup_date_cst =  $pickup_date_obj->setTimezone(APP_TIMEZONE);
            $params['pickup_date_cst'] = $pickup_date_cst;
        }


       // echo "<pre>"; print_r($params);
        $response = $entity_lib->apiUpdate($params);

        //Save Truck Information
        if(isset($this->_tempData['selected_truck'])){

            $selected_truck = $this->_tempData['selected_truck'];
            $post_arr = [];
            $post_arr['entity_type_id'] = 65;
            $post_arr['truck_id'] = $selected_truck->entity_id;
            $post_arr['title'] = $selected_truck->title;
            $post_arr['order_id'] = $entity_id;
            $post_arr['base_fee'] = $selected_truck->base_fee;
            $post_arr['charge_per_minute'] = $selected_truck->charge_per_minute;
            $post_arr['volume'] = $selected_truck->volume;
           // $post_arr['vehicle_code'] = $selected_truck->vehicle_code;
            $post_arr['min_weight'] = $selected_truck->min_weight;
            $post_arr['max_weight'] = $selected_truck->max_weight;
            $post_arr['truck_class_id'] = $selected_truck->truck_class_id->id;

            $response = $entity_lib->doPost($post_arr);  unset($post_arr);


        }

        //Save pickup Location
        $pickup = is_string($request->pickup) && json_decode($request->pickup) != NULL ? json_decode($request->pickup) :$request->pickup;
        $dropoff = is_string($request->dropoff) && json_decode($request->dropoff) != NULL ? json_decode($request->dropoff) :$request->dropoff;

        if(isset($pickup)){
            $post_arr = $this->_getOrderCity(66,$entity_id,$request->customer_id,$pickup);
            $entity_lib->doPost($post_arr);
            unset($post_arr);
        }

        //Save Drop off Location
        if(isset($dropoff)){
            $post_arr = $this->_getOrderCity(67,$entity_id,$request->customer_id,$dropoff);
            $entity_lib->doPost($post_arr);  unset($post_arr);
        }

        //Save Order History
        $post_arr = [];
        $post_arr['entity_type_id'] = 68;
        $post_arr['order_id'] = $entity_id;
        $post_arr['order_status'] = $request->order_status;
        $entity_lib->doPost($post_arr);  unset($post_arr);

        //Update truck selection Temp
        if(isset($request->truck_selected_id) && !empty($request->truck_selected_id)){
            $post_arr = [];
            $post_arr['entity_type_id'] = 62;
            $post_arr['entity_id'] = $request->truck_selected_id;
            $post_arr['is_ordered'] = 1;

            if(!isset($request->professional_id) || (isset($request->professional_id) && empty($request->professional_id))){
                $post_arr['loader_detail'] = "";
            }

            $res = $entity_lib->doUpdate($post_arr);  unset($post_arr);
        }



    }

    /**
     * @param $entity_type_id
     * @param $order_id
     * @param $customer_id
     * @param $address
     * @return array
     * @throws \Exception
     */
    private function _getOrderCity($entity_type_id,$order_id,$customer_id,$address)
    {

       // $address = json_decode($address);
        if(is_array($address) && isset($address[0])){
            $address = (object)$address[0];
        }else{
            $address = (object)$address;
        }
        //$address = is_array($address) ? (object)$address[0] : $address;

        $post_arr = [];
        $post_arr['entity_type_id'] = $entity_type_id;
        $post_arr['order_id'] = $order_id;
        $post_arr['customer_id'] = $customer_id;
        $post_arr['address'] = $address->address;
        $post_arr['latitude'] = $address->latitude;
        $post_arr['longitude'] = $address->longitude;
        $post_arr['city'] = $address->city_id;

        //Get City Information
        $city_arr = array(
            'entity_type_id' => 'city',
            'entity_id' => $address->city_id
        );

        $city_information = $this->_entityLib->doGet($city_arr);
        $city_information = json_decode(json_encode($city_information));

        //Save city more information
        if(isset($city_information->attributes)){

            $city_info = $city_information->attributes;
            $post_arr['city_name'] = $city_info->title;

            if(isset( $city_info->city_id)){
                $post_arr['city_code'] = $city_info->city_id->code;
                $post_arr['city_lat'] = $city_info->city_id->latitude;
                $post_arr['city_long'] = $city_info->city_id->longitude;
            }

            if(isset( $city_info->state_id)){
                $post_arr['state_name'] = $city_info->state_id->name;
                $post_arr['state_code'] = $city_info->state_id->code;
            }
        }

      // echo "<pre>"; print_r($post_arr);
        return $post_arr;
    }

    /**
     * @param $request
     * @param $depend_entity_request
     * @param $depend_entity_response
     */
    public function orderItemAddTrigger($request,$depend_entity_request = false,$depend_entity_response = false)
    {
        $request = is_array($request) ? (object)$request : $request;

        if($depend_entity_request && $depend_entity_response){

            $depend_entity_request = is_array($depend_entity_request) ? (object)$depend_entity_request : $depend_entity_request;
            $depend_entity_response = is_array($depend_entity_response) ? (object)$depend_entity_response : $depend_entity_response;

            $pl_attachment_model = new PLAttachment();
            if(isset($depend_entity_response->entity->entity_id)){

                if(isset($depend_entity_request->item_images) && !empty($depend_entity_request->item_images)){
                    $pl_attachment_model->updateAttachmentByEntity($depend_entity_request->item_images,$depend_entity_response->entity->entity_id);
                }

                if(isset($depend_entity_request->item_receipt) && !empty($depend_entity_request->item_receipt)){
                    $pl_attachment_model->updateAttachmentByEntity($depend_entity_request->item_receipt,$depend_entity_response->entity->entity_id);

                }
            }
        }


    }

    /**
     * @param $request
     * @param $depend_entity_raw
     * @return array
     */
    public function orderItemBeforePostTrigger($request,$depend_entity_raw = false)
    {
        $request            = is_array($request) ? (object)$request : $request;
        $depend_entity_raw  = is_array($depend_entity_raw) ? (object)$depend_entity_raw : $depend_entity_raw;

        if($depend_entity_raw){

            $return['is_extra_item'] = (isset($depend_entity_raw->is_extra_item) && $depend_entity_raw->is_extra_item != '') ? $depend_entity_raw->is_extra_item : 0;
            $return['is_expensive'] = (isset($depend_entity_raw->is_expensive) && $depend_entity_raw->is_expensive != '') ? $depend_entity_raw->is_expensive : 0;

            if($depend_entity_raw->item_id == "" && $depend_entity_raw->item_name != ""){
                //get other item id and update its count
                $item_lib = new ItemLib();
                $return['item_id'] =  $item_lib->getOtherItemByTitle($depend_entity_raw->item_name);
                //return ['item_id' => $other_item_id];
            }

            if($depend_entity_raw->item_id > 0 && $depend_entity_raw->item_name == '')
                $return['item_name'] = ItemLib::getItemName($depend_entity_raw->item_id);


        }
        else{

            if($request->item_id == "" && $request->item_name != ""){
                //get other item id and update its count
                $item_lib = new ItemLib();
                $return['item_id'] =  $item_lib->getOtherItemByTitle($request->item_name);
                //return ['item_id' => $other_item_id];
            }

            if(isset($request->item_id)){
                $return['is_extra_item'] = (isset($request->is_extra_item) && $request->is_extra_item != '') ? $request->is_extra_item : 0;
                $return['is_expensive'] = (isset($request->is_expensive) && $request->is_expensive != '') ? $request->is_expensive : 0;

                if($request->item_id > 0 && $request->item_name == '')
                    $return['item_name'] = ItemLib::getItemName($request->item_id);
            }

           // echo "<pre>"; print_r($return); exit;
        }


        return isset($return) ? $return : array();
    }

    /**
     * @param $request
     * @param $depend_entity
     * @throws \Exception
     */
    public function orderUpdateTrigger($request,$depend_entity)
    {
        $request = is_array($request) ? (object)$request : $request;
        $post_param = (array)$request;

        $previous_order = $this->_tempData['previous_order'];

        if(isset($request->truck_id) && isset($previous_order->attributes->truck_id->id)){

            if(trim($request->truck_id) != trim($previous_order->attributes->truck_id->id))
            {
                $get_arr = [];
                $get_arr['entity_type_id'] = 'truck';
                $get_arr['entity_id'] = $request->truck_id;
                $get_arr['in_detail'] = 1;
                $get_arr['mobile_json'] = 1;
                $selected_truck = $this->_entityLib->doGet($get_arr);
                $selected_truck = json_decode(json_encode($selected_truck));

                //Get Order Truck ID
                $order_item_flat = new SYSTableFlat('order_trucks');
                $where_condition = ' order_id = '.$previous_order->entity_id;
                $order_truck = $order_item_flat->getColumnByWhere($where_condition,'entity_id');

                //Update Order Truck
               // echo "<pre>"; print_r($selected_truck);
                $post_arr = [];
                $post_arr['entity_type_id'] = 65;
                $post_arr['truck_id'] = $selected_truck->entity_id;
                $post_arr['order_id'] = $previous_order->entity_id;
                $post_arr['base_fee'] = $selected_truck->base_fee;
                $post_arr['charge_per_minute'] = $selected_truck->charge_per_minute;
                $post_arr['volume'] = $selected_truck->volume;
              //  $post_arr['vehicle_code'] = $selected_truck->vehicle_code;
                $post_arr['min_weight'] = $selected_truck->min_weight;
                $post_arr['max_weight'] = $selected_truck->max_weight;
                $post_arr['truck_class_id'] = $selected_truck->truck_class_id->id;
               // echo "<pre>"; print_r($post_arr); exit;

                if($order_truck){
                    $post_arr['entity_id'] = $order_truck->entity_id;
                    $response = $this->_entityLib->doUpdate($post_arr);
                }
                // echo "<pre>"; print_r($response); exit;


            }

            if(isset($this->_tempData['order_status_data'])){
                unset($this->_tempData['order_status_data']);
            }

        }


        //Update vehicle and driver

        if(isset($request->truck_vehicle) && !empty($request->truck_vehicle))
        {

            if(isset($previous_order->attributes->vehicle_id->id)
                && $request->truck_vehicle != $previous_order->attributes->vehicle_id->id)
            {
                //Decline Order from previous driver
                $arr = [];
                $arr['entity_type_id'] = 68;
                $arr['order_id'] = $previous_order->entity_id;
                $arr['driver_id'] = $previous_order->attributes->driver_id->id;
                $arr['order_status'] = 'declined';
                $arr_response = $this->_entityLib->apiPost($arr);
                
            }

            //assign vehicle
            $flat = new SYSTableFlat('vehicle');
            $where_condition = ' entity_id = '.$request->truck_vehicle;
            $vehicle_raw = $flat->getColumnByWhere($where_condition,'driver_id');

            $arr = [];
            $arr['entity_type_id'] = 68;
            $arr['order_id'] = $previous_order->entity_id;
            $arr['driver_id'] = $vehicle_raw->driver_id;
            $arr['order_status'] = 'assigned';
            $arr_response = $this->_entityLib->apiPost($arr);

        }

        if(isset($request->order_status) && !empty($request->order_status) && isset($request->customer_id)){
            //notify customer
            $other_data['entity_type_id'] = $request->entity_type_id;
            $other_data['actor_entity_type_id'] = 'customer';
            $other_data['extension_ref_table'] = 'customer_flat';
            $other_data['extension_ref_id'] = $request->customer_id;
            $timestamp = date("Y-m-d H:i:s");
            $sys_history = new SYSEntityHistory();
            $sys_history->logHistory('order_update_customer_notify', $request->entity_id, $request->customer_id, $other_data, $timestamp, $request);
        }


        //Save Order Revision
 /*       if (isset($this->_tempData['previous_order']) &&  (isset($request->add_revision) && $request->add_revision == 1)) {

            $sys_entity_type_model = new SYSEntityType();
            $entity_type_id = $sys_entity_type_model->getIdByIdentifier('order_revision');

            $order_detail = json_encode($previous_order);

            $pos_arr = [];
            $pos_arr['entity_type_id'] = $entity_type_id;
            $pos_arr['order_id'] = $previous_order->entity_id;
            $pos_arr['customer_id'] = isset($previous_order->customer_id->id) ? $previous_order->customer_id->id : '';
            $pos_arr['grand_total'] = "$previous_order->grand_total";
            $pos_arr['order_detail'] = $order_detail;
            $pos_arr['inner_response'] = 1;
            $pos_arr['mobile_json'] = 1;
            $entity_lib = new Entity();
            $entity_lib->apiPost($pos_arr);
        }*/



    }

    /**
     * @param $request
     * @return array
     */
    public function orderItemBeforeSaveTrigger($request)
    {
        $request  = is_array($request) ? (object)$request : $request;

        if(isset($request->is_extra_item)){
            $return['is_extra_item'] = ($request->is_extra_item != '') ? $request->is_extra_item : 0;
        }

        if(isset($request->is_expensive)){
            $return['is_expensive'] = ($request->is_expensive != '') ? $request->is_expensive : 0;
        }

        if($request->item_id > 0 && $request->item_name == '')
            $return['item_name'] = ItemLib::getItemName($request->item_id);

        return isset($return) ? $return : array();
    }

    /**
     * business user post trigger
     * @param $request
     * @return mixed
     */
    public function businessUserBeforePostTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        //set Full Name
        $return['full_name'] = CustomHelper::setFullName($request);
        return $return;
    }

    /**
     * business user update trigger
     * @param $request
     * @return array
     */
    public function businessUserBeforeSaveTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        $return = array();
        //set Full Name
        if(isset($request->first_name) && isset($request->last_name)) {
            $return['full_name'] =  CustomHelper::setFullName($request);
        }

        //Update auth status for customer entity
        //if  requested status is different from the status that has saved
        if(isset($request->entity_id) && isset($request->user_status)){
            $this->_checkIfUpdateUserStatus($request);
        }

        if(count($return)>0) return $return;
    }

    /**
     * Update auth status for customer entity
     * if customer requested status is different from the status that has saved
     * @param $request
     */
    public function customerUpdateTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        if($this->_isUpdateAuth == 1 && (isset($request->user_status) && isset($this->_authID))){
            $this->_updateAuthStatus($this->_authID,$request->user_status);
            $this->_unsetAuthFlags();
        }

    }

    /**
     * Update auth status for customer entity
     * if customer requested status is different from the status that has saved
     * @param $request
     */
    public function businessUserUpdateTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        if($this->_isUpdateAuth == 1 && (isset($request->user_status) && isset($this->_authID))){
            $this->_updateAuthStatus($this->_authID,$request->user_status);
            $this->_unsetAuthFlags();
        }

    }

    /**
     * update entity auth status
     * @param $auth_id
     * @param $status
     */
    private function _updateAuthStatus($auth_id,$status)
    {
        //update auth status
        $auth_model = new SYSEntityAuth();
        $record = $auth_model->get($auth_id);
        $record->status = $status;
        $record->updated_at = date("Y-m-d H:i:s");
        // reset token
        $auth_model->set($record->entity_auth_id, (array)$record);
    }

    /**
     * get user status and check with requested status
     * and set _isUpdateAuth to 1 or 0
     * @param $request
     */
    private function _checkIfUpdateUserStatus($request)
    {
         $sys_entity_model = new Entity();
        $entity_data = $sys_entity_model->getData($request->entity_id);
        //print_r($entity_data->attributes); exit;
         if(isset($entity_data->attributes->user_status)){
           // echo $entity_data->attributes->user_status;
            // echo  $request->user_status; exit;
             if((isset($entity_data->attributes->user_status->value)
                 && $entity_data->attributes->user_status->value != $request->user_status)
             || (!isset($entity_data->attributes->user_status->value))
             ){
                 $this->_isUpdateAuth = 1;
                 $this->_authID = $entity_data->entity_auth_id;
             }
         }
    }

    /**
     * Unset auth flags those are set to update user status
     */
    private function _unsetAuthFlags()
    {
        $this->_isUpdateAuth = 0;
        unset($this->_authID);
    }

    /**
     * coupon verify trigger
     * @param $request
     * @return mixed
     */
    public function couponVerifyTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        $response['error'] = 0;

        if(strtotime($request->start_date) > strtotime($request->coupon_expiry)){
            $response['error'] = TRUE;
            $response['message'] = trans('system.expiry_must_greater_start_date');
            return $response;
        }

    }

    /**
     * Get product max price and send with list
     * @param $request
     * @param $response
     * @return array
     */
    public function productTagsListingTrigger($request,$response)
    {

        if(isset($response)){
            $total_records = false;

                if(isset($response['data'][0]))
                    $total_records = true;

            if($total_records){
                $product_helper_obj = new ProductHelper();
                $max_price = $product_helper_obj->getMaxPriceByProductType('product');
                return ['key' => 'max_price', 'data' => $max_price];
            }
        }
    }


    /**
     * On Order Discussion send notification to customer
     * @param $request
     * @param bool $entity_id
     */
    public function orderDiscussionAddTrigger($request, $entity_id = FALSE)
    {
        $request = is_array($request) ? (object)$request : $request;

        if((isset($request->visible_to_customer) && $request->visible_to_customer == 1)
            && (isset($request->order_id) && !empty($request->order_id))){

            //Get customer info
            $flat_table_model = new FlatTable();
            $customer_raw = $flat_table_model->getCustomerColumnsByOrder($request->order_id);

            if($customer_raw){

                if(isset($customer_raw->customer_id) && (isset($customer_raw->is_notify) && $customer_raw->is_notify == 1)) {
                    $customer_id = $customer_raw->customer_id;

                    $sys_history = new SYSEntityHistory();

                    $other_data['entity_type_id'] = $request->entity_type_id;
                    $other_data['actor_entity_type_id'] = 'customer';
                    $other_data['against_entity_type_id'] = 15;
                    $other_data['against_entity_id'] = $request->order_id;
                    $other_data['extension_ref_table'] = 'order_discussion_flat';
                    $other_data['extension_ref_id'] = $entity_id;
                    $timestamp = date("Y-m-d H:i:s");

                    $sys_history->logHistory('order_discussion_add', $entity_id, $customer_id, $other_data, $timestamp, $request);
                }
            }

        }
    }


    public function cityBeforePostTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        //set title of city

        if(isset($request->city_id) && !empty($request->city_id)){

            $city_name = '';
            $flat_model = new FlatTable();
            $city_name =  $flat_model->getCityByID($request->city_id);

            if($city_name){
                $return['title'] = $city_name;  //echo "<pre>"; print_r($return); exit;
                return $return;
            }

        }
    }

    public function cityBeforeSaveTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        //set title of city

        if(isset($request->city_id) && !empty($request->city_id)){

            $city_name = '';
            $flat_model = new FlatTable();
            $city_name =  $flat_model->getCityByID($request->city_id);

            if($city_name){
                $return['title'] = $city_name;
                return $return;
            }

        }
    }

    /**
     * @param $request
     * @return mixed
     */
    public function truckBeforePostTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        //set title of city
        $return['volume'] = $this->_calculateVolume($request);
        return $return;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function truckBeforeSaveTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        //set title of city
        $return['volume'] = $this->_calculateVolume($request);
        return $return;
    }

    /**
     * Calculate volume
     * @param $request
     * @return float|int|string
     */
    private function _calculateVolume($request)
    {
        $volume = '';
        if(isset($request->width) && !empty($request->width) ||
            isset($request->height) && !empty($request->height) ||
            isset($request->length) && !empty($request->length)
        ){

            if(isset($request->width) && !empty($request->width))
                $volume =  ($volume != '') ? $volume * $request->width :  $request->width;

            if(isset($request->height) && !empty($request->height))
                $volume =  ($volume != '') ? $volume * $request->height :  $request->height;

            if(isset($request->length) && !empty($request->length))
                $volume =  ($volume != '') ? $volume * $request->length :  $request->length;

        }

        return $volume;
    }


    /**
     * @param $request
     * @param $response
     * @return array
     */
    public function truckClassGetTrigger($request,$response)
    {
      if(isset($response) && isset($response->entity_id)){

          if(isset($request->mobile_json) && $request->mobile_json == 1){
              $entity_att = $response;

          }else{
              $entity_att = $response->attributes;
          }

            $min_weight_pound = ''; $max_weight_pound = '';
            if(isset($entity_att->min_weight)){
                $min_weight_pound = CustomHelper::convertPoundToKg($entity_att->min_weight);
            }

            if(isset($entity_att->max_weight)){
                $max_weight_pound = CustomHelper::convertPoundToKg($entity_att->max_weight);
            }

            $data = (object)array(
                'min_weight' => $min_weight_pound,
                'max_weight' => $max_weight_pound
            );

           return ['key' => 'weight_kg', 'data' => $data];
        }
    }

    /**
     * Customer before post
     * @param Request $request
     * @return array
     */
    public function driverBeforePostTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        //set Full Name
        $return['full_name'] =  CustomHelper::setFullName($request);
        return $return;
    }

    /**
     * Customer before update
     * @param Request $request
     * @return array
     */
    public function driverBeforeSaveTrigger($request)
    {
        $return = array();
        $request = is_array($request) ? (object)$request : $request;

        //set Full Name
        if(isset($request->first_name) && isset($request->last_name)) {
            $return['full_name'] =  CustomHelper::setFullName($request);
        }

        //Update auth status for customer entity
        //if  requested status is different from the status that has saved
        if(isset($request->entity_id) && isset($request->user_status)){
            $this->_checkIfUpdateUserStatus($request);
        }

        if(count($return) > 0) return $return;
    }

    /**
     * order verify trigger
     * @param $request
     * @return mixed
     */
    public function orderVerifyTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        $request->pickup = is_string($request->pickup) && json_decode($request->pickup) != NULL ? json_decode($request->pickup) :$request->pickup;
        $request->dropoff = is_string($request->dropoff) && json_decode($request->dropoff) != NULL ? json_decode($request->dropoff) :$request->dropoff;

        $response['error'] = 0;

        $entity_model = new SYSEntity();
        $entity = $entity_model->getData($request->customer_id, 11, TRUE);

        if ($entity && (isset($entity->auth) && $entity->auth->status != 1)) {
            // kick user
            $response['error'] = TRUE;
            $response['kick_user'] = 1;
            $response['message'] = trans('system.your_account_is_baned_removed');
            return $response;
        }

        if(!isset($request->truck_selected_id) || empty($request->truck_selected_id)){
            $response['error'] = TRUE;
            $response['message'] = trans('system.field_required',array('field' => 'truck_selected_id'));
            return $response;
        }

        if(!isset($request->pickup) || empty($request->pickup)){
            $response['error'] = TRUE;
            $response['message'] = trans('system.field_required',array('field' => 'pickup'));
            return $response;
        }

        if(!isset($request->dropoff) || empty($request->dropoff)){
            $response['error'] = TRUE;
            $response['message'] = trans('system.field_required',array('field' => 'dropoff'));
            return $response;
        }


        //Validate order addresses
        $order_address_lib = new OrderAddress();

        if(isset($request->pickup)){

           $address = $request->pickup;
           $validate = $order_address_lib->validateAddress($address);
           if($validate['error'] == 1) return $response;

        }

        if(isset($request->dropoff)){

            $address = $request->dropoff;
            $validate = $order_address_lib->validateAddress($address);
            if($validate['error'] == 1) return $response;

        }

    }

    /**
     * @param $request
     * @throws \Exception
     */
    public function orderHistoryAddTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        //Update Order status / Driver
        $order_status_lib = new OrderStatus();
        $order_response = $order_status_lib->updateOrderStatus($request);

    }

    /**
     * Before post order
     * @param Request $request
     * @return array|bool
     */
    public function orderHistoryBeforePostTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        if (isset($request->order_status)) {

            if (!is_numeric($request->order_status)) {

                $order_helper = new OrderHelper();
                $status_data = $order_helper->getOrderStatusData($request->order_status);
                if ($status_data) {
                    // $request->request->add(['order_status' => $status_data->entity_id]) ;
                    $return = ['order_status' => $status_data->entity_id];
                }
            }else{
                $order_helper = new SYSTableFlat('order_statuses');
                $status_data = $order_helper->getColumnByWhere(' entity_id = '.$request->order_status,'*');

            }

            if(isset($status_data->keyword))
            $return['status_keyword'] = $status_data->keyword;

        }

        if((isset($request->driver_id) && !empty($request->driver_id)) &&
            (!isset($request->vehicle_id) || empty($request->vehicle_id))){
            $flat_model = new SYSTableFlat('vehicle');
            $vehicle = $flat_model->getColumnByWhere(' driver_id = '.$request->driver_id,'entity_id');
            $return['vehicle_id'] = $vehicle->entity_id;
        }

        if (isset($return)) {
            return $return;
        }

        return FALSE;
    }

    /**
     * @param $request
     * @return array|bool
     */
    public function orderBeforeListingTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        if (isset($request->order_status)) {

            if (!is_numeric($request->order_status)) {

                $status_key = $request->order_status;
                $order_helper = new OrderHelper();
                $status_data = $order_helper->getOrderStatusData($status_key);
                if ($status_data) {
                    // $request->request->add(['order_status' => $status_data->entity_id]) ;
                    $return = ['order_status' => $status_data->entity_id];
                }
            }
        }
        if (isset($return)) {
            return $return;
        }

        return FALSE;
    }

    /**
     * check if status is not pending/confirmed then validate
     * @param $request
     * @return mixed
     */
    public function orderHistoryVerifyTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        $response['error'] = 0;

        if(isset($request->order_status) && !empty($request->order_status)){

            $flat_model = new SYSTableFlat('order_statuses');
            $where_condition = ' entity_id = '.$request->order_status;
            $row = $flat_model->getColumnByWhere($where_condition,'keyword');

            if(isset($row->keyword)){
                if(!in_array($row->keyword,array('pending','confirmed','cancelled'))){

                    if(isset($request->is_admin_update) && $request->is_admin_update == 1){
                        if(!isset($request->vehicle_id) || empty($request->vehicle_id)){
                            $response['error'] = TRUE;
                            $response['message'] = trans('system.field_required',array('field' => 'vehicle_id'));
                            return $response;
                        }
                    }else{
                        if(!isset($request->driver_id) || empty($request->driver_id)){
                            $response['error'] = TRUE;
                            $response['message'] = trans('system.field_required',array('field' => 'driver_id'));
                            return $response;
                        }
                    }


                }
            }

        }

    }

    /**
     * @param $request
     * @return mixed
     */
    public function shiftsVerifyTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        $response['error'] = 0;

        if (strtotime($request->shift_to) <= strtotime($request->shift_from)) {
            $response['error'] = TRUE;
            $response['message'] = trans('system.shift_to_greater_shift_from');

            return $response;
        }
    }

    /**
     * @param $request
     * @return bool|int
     */
    public function itemBeforePostTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        if (isset($request->is_other) && empty($request->is_other)) {
               return $return['is_other'] = 0;
        }

        return FALSE;
    }

    /**
     * @param $request
     * @return bool|int
     */
    public function itemBeforeSaveTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        if (isset($request->is_other) && empty($request->is_other)) {
            return $return['is_other'] = 0;
        }

        return FALSE;
    }

    /**
     * @param $request
     * @param $response
     * @return array
     */
    public function orderDriverLocationGetTrigger($request,$response)
    {
        if(isset($response) && isset($response->entity_id))
           // echo "<pre>"; print_r($response); exit;
            if(isset($response->attributes->driver_location) && !empty($response->attributes->driver_location)){
                return ['key' => 'driver_location', 'data' => json_decode($response->attributes->driver_location)];
            }

        if(isset($response->driver_location) && !empty($response->driver_location)){
            return ['key' => 'driver_location', 'data' => json_decode($response->driver_location)];
        }


    }

    /**
     * @param $request
     * @return mixed
     */
    public function vehicleUpdateVerifyTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        $response['error'] = 0;

        if ($request->driver_id && isset($request->entity_id)) {

            $flat_model = new SYSTableFlat('vehicle');
            $check_driver_exist = $flat_model->getDataByWhere(' driver_id = '.$request->driver_id.' AND entity_id <> '.$request->entity_id);

            if($check_driver_exist){

                if(count($check_driver_exist) > 0){

                    $response['error'] = TRUE;
                    //$response['message'] = trans('system.shift_to_greater_shift_from');
                    $response['message'] = trans('system.driver_already_assigned',array('truck_name' => $check_driver_exist[0]->title));
                    return $response;
                }
            }

        }
    }

    /**
     * @param $request
     * @return mixed
     */
    public function vehicleVerifyTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        $response['error'] = 0;

        if (isset($request->driver_id)) {

            $flat_model = new SYSTableFlat('vehicle');
            $check_driver_exist = $flat_model->getDataByWhere(' driver_id = '.$request->driver_id);

            if($check_driver_exist){

                if(count($check_driver_exist) > 0){

                    $response['error'] = TRUE;
                    $response['message'] = trans('system.driver_already_assigned',array('truck_name' => $check_driver_exist[0]->title));
                    return $response;
                }
            }

        }
    }

    /**
     * @param $request
     * @return mixed
     */

    public function customNotificationVerifyTrigger($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        $response['error'] = 0;

        if(in_array(trim($request->target_user_entity_type_id),array("customer","driver"))){

            if(empty($request->target_user_entity_id)){

                $response['error'] = TRUE;
                $response['message'] = trans('system.entity_is_required ',array('entity' => 'Target User'));
                return $response;
            }

        }
    }



}
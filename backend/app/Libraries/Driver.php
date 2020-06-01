<?php

/**
 * Description: this library is related to driver
 * Author: Samreen <samreen.quyyum@cubixlabs.com>
 * Date: 23-July-2018
 * Time: 11:56 PM:
 * Copyright: CubixLabs
 */

namespace App\Libraries;
use App\Http\Models\Extension\Social\ExtPackageRate;
use App\Http\Models\FlatTable;
use App\Http\Models\PLAttachment;
use App\Http\Models\SYSAttributeOption;
use App\Http\Models\SYSTableFlat;
use App\Libraries\System\Entity;

/**
 * Class DeliveryProfessional
 * @package App\Libraries
 */
Class Driver
{
    private $_SYSTableFlatModel = '';
    private $_entityLib;

    /**
     * Truck constructor.
     */
    public function __construct()
    {
        $this->_SYSTableFlatModel = new SYSTableFlat('driver');
        $this->_entityLib = new Entity();
    }

    /**
     * @param $driver_id
     * @param bool $request_params
     * @return mixed
     */
    public function getDriverProfile($driver_id,$request_params = false)
    {
        $return['error'] = 0;
        $return['message'] = trans('api_errors.success');

        $params['entity_type_id'] = 3;
        $params['entity_id'] = $driver_id;

       /* if($request_params){
            $params = array_merge($params,$request_params);

            if(isset($params['limit']))
                unset($params['limit']);
        }*/

        $entity_lib = new Entity();
        $response = $entity_lib->apiGet($params);
        $response = json_decode(json_encode($response));
      //  echo "<pre>"; print_r($response); exit;
        if($response->error == 0){

            if(isset($response->data->entity)){

                $request_params = is_array($request_params) ? (object)$request_params : $request_params;
                $limit = (isset($request_params->limit) && $request_params->limit > 0) ? $request_params->limit : 3;

                $order_helper = new OrderHelper();
                $order_ids = $order_helper->getClientOrders('driver',$driver_id);

                $driver = $response->data->entity->attributes;
                $profile['entity_type_id'] = $response->data->entity->entity_type_id;
                $profile['entity_id'] = $driver_id;
                $profile['first_name'] = $driver->first_name;
                $profile['last_name'] = $driver->last_name;
                $profile['full_name'] = $driver->full_name;

                $pl_attachment = new PLAttachment();
                $gallery = isset($response->data->entity->gallery[0]) ? $response->data->entity->gallery[0] : array();

                $profile['gallery'] = $pl_attachment->getAttachmentBasicInfo($gallery);
                //$return['gallery'] = $response->data->entity->gallery;

                $profile['trips'] = ($order_ids) ? count($order_ids) : 0;
                $profile['rating'] = $driver->ext_average_rating;
                $profile['rating_options'] = $this->getDriverReview($driver->rating_options);

                $profile['reviews'] = $order_helper->getRating($order_ids,$limit);
                $profile['created_at'] = $response->data->entity->created_at;

                $join_date = CustomHelper::getJoiningDays($response->data->entity->created_at);

                $profile['joining_key'] = $join_date['key'];
                $profile['joining_value'] = $join_date['value'];


                $return['data'] = $profile;

            }
            else{
                $return['error'] = 1;
                $return['message'] = trans('api_errors.data_not_found');
            }

        }
        else{
            $return['error'] = 1;
            $return['message'] = $response->message;
        }

        return $return;
        //echo "<pre>"; print_r($return); exit;

    }

    /**
     * @param $rating_option
     * @return array
     */
    public function getDriverReview($rating_option)
    {
        $driver_reviews = array();

        if(!empty($rating_option)){

            $rating_option = json_decode($rating_option,true);

            $sys_attribute_option = new SYSAttributeOption();

            if(count($rating_option) > 0){
                foreach($rating_option as $option => $value){

                    $review_option = $sys_attribute_option->getByAttributeCode('driver_review',$option);

                    if(isset($review_option->attribute_option_id)){
                        $review_option->count = $value;


                        $file = new \StdClass();
                        if($review_option->file != ''){
                            $pl_attachment = new PLAttachment();
                            $file =  $pl_attachment->getAttachmentGallery($review_option->file);
                        }

                        $review_option->file = $file;
                        $driver_reviews[] = $review_option;
                    }


                }
            }
        }
        //echo "<pre>"; print_r($driver_reviews); exit;
        return $driver_reviews;
    }

    /**
     * Update driver location
     * @param $request
     * @return mixed
     */
    public function updateLocation($request)
    {
        $return['error'] = 0;
        $return['message'] = trans('api_errors.success');

        try{
            $request = is_array($request) ? (object)$request : $request;

                //Get order driver location
                if(isset($request->order_id) && !empty($request->order_id)){
                  $response =  $this->_updateOrderLocation($request);

                  if($response->error == 1){
                      $return['error'] = $response->error;
                      $return['message'] = $response->message;
                      $return['debug'] = $response->debug;
                      return $return;
                  }
                }

                //Update Driver
             $response_driver = $this->_updateLocation($request);

            if($response_driver->error == 1){
                $return['error'] = $response->error;
                $return['message'] = $response->message;
                $return['debug'] = $response->debug;
                return $return;
            }

            $return = $response_driver;

        }
        catch (\Exception $e) {
            // echo $e->getTraceAsString(); exit;
            $return['error'] = 1;
            $return['message'] = $e->getMessage();
            $return['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine();

           // echo "<pre>"; print_r($return); exit;
        }

        return $return;
    }

    /**
     *  Update order driver location
     * @param $request
     * @return bool|mixed
     */
    private function _updateOrderLocation($request)
    {
        $flat_table = new SYSTableFlat('order_driver_location');
        $driver_order_location = $flat_table->getColumnByWhere(' order_id = '.$request->order_id.' AND driver_id = '.$request->driver_id,'*');

        $driver_loc = is_array($request->driver_location) ? $request->driver_location : "";

            if($driver_order_location && isset($driver_order_location->entity_id)){

                if(!empty($driver_order_location->driver_location)){

                    $driver_location = json_decode($driver_order_location->driver_location,true);

                   if(count($driver_location) > 0){
                        /*foreach($driver_location as $locations){
                            $location[] = $locations;
                        }*/

                        $location = array_merge($driver_location,$driver_loc);


                    }

                }else{
                    $location = $driver_loc;
                }

                //Update Order Driver location
                $arr = [];
                $arr['entity_type_id'] = 69;
                $arr['entity_id'] = $driver_order_location->entity_id;
              //  $arr['login_entity_id'] =
                 $arr['driver_id'] = $request->driver_id;
                $arr['driver_location'] = (!empty($location)) ? json_encode($location) : "";
                $arr['mobile_json'] = isset($request->mobile_json) ? $request->mobile_json : 0;
               // $arr['login_entity_type_id'] = 'driver';
               // echo "<pre>"; print_r($arr);
                $arr_response =  $this->_entityLib->apiUpdate($arr);
             //  echo "<pre>"; print_r($arr_response); exit;
                return json_decode(json_encode($arr_response));

            }else{
                $location = $driver_loc;

                //Update Order Driver location
                $arr = [];
                $arr['entity_type_id'] = 69;
                $arr['order_id'] = $request->order_id;
              //  $arr['login_entity_id'] =
                $arr['driver_id'] = $request->driver_id;
                $arr['driver_location'] = (!empty($location)) ? json_encode($location) : "";
                $arr['mobile_json'] = isset($request->mobile_json) ? $request->mobile_json : 0;
               // $arr['login_entity_type_id'] = 'driver';

                $arr_response =  $this->_entityLib->apiPost($arr);
             //echo "<pre>"; print_r($arr_response);
                return json_decode(json_encode($arr_response));
            }

           // exit;

        return false;
    }

    /**
     * update driver lcoation
     * @param $request
     * @return mixed
     */
    private function _updateLocation($request)
    {
       // echo "<pre>"; print_r($request->driver_location); exit;
        $driver_location = is_array($request->driver_location) ? $request->driver_location : "";
       // $index = last_key($driver_location);
        $keys = array_keys($driver_location);
        $index = end($keys);


        $location = (object)$driver_location[$index];
       // echo "<pre>"; print_r($location); exit;
        $arr = [];
        $arr['entity_type_id'] = 3;
       // $arr['login_entity_id'] =
        $arr['entity_id'] = $request->driver_id;
        $arr['latitude'] = "$location->latitude";
        $arr['longitude'] = "$location->longitude";


        $arr['driver_location'] = json_encode($location);
        $arr['mobile_json'] = isset($request->mobile_json) ? $request->mobile_json : 0;
      //  $arr['login_entity_type_id'] = 'driver';

       // echo "<pre>"; print_r($arr);
        $arr_response = $this->_entityLib->apiUpdate($arr);

       // echo "<pre>"; print_r($arr_response); exit;
        return json_decode(json_encode($arr_response));
    }

    /**
     * @param $order
     * @return array
     */
    public function availableDrivers($order)
    { //echo "<pre>"; print_r($order); exit;
        //Check Driver Available
        //Check pickup date/time lie in driver shift slot
        $available_drivers = array();

        $truck_id = $order->truck_id;
        $pickup_time = $order->pickup_time;
        $deliver_time = date("H:i:s",strtotime($order->estimated_delivery_date));

        $flat_model = new FlatTable();
        $drivers = $flat_model->getAvailableDrivers($truck_id,$pickup_time,$deliver_time);

        $general_settings = new GeneralSetting();
        $trip_grace_minutes = $general_settings->getColumn('trip_grace_minutes');
        $trip_grace_minutes = ($trip_grace_minutes) ? $trip_grace_minutes : 0;

        //Check available time slots
        if($drivers){

            if(count($drivers) > 0){

                $custom_helper = new CustomHelper();
                foreach($drivers as $driver){

                    //Get Driver busy slots
                    $busy_slots = $flat_model->getDriverBusySlot($driver->entity_id,$order->pickup_date,$trip_grace_minutes);
                    if($busy_slots){

                        //Check busy slots with order slot
                        $is_available =  $custom_helper->isSlotAvailable($pickup_time,$deliver_time,$busy_slots);
                        if($is_available == 1){
                            $available_drivers[] =  $driver;
                            break;
                        }
                    }
                    else{
                        $available_drivers[] =  $driver;
                    }

                }

            }
        }
       // echo "<pre>"; print_r($drivers); exit;
        return $available_drivers;

    }




}
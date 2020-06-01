<?php
namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Http\Models\City;
use App\Http\Models\FlatTable;
use App\Http\Models\SYSEntity;
use App\Http\Models\SYSTableFlat;
use App\Libraries\CustomHelper;
use App\Libraries\DeliveryProfessional;
use App\Libraries\Driver;
use App\Libraries\EntityNotification;
use App\Libraries\GeneralSetting;
use App\Libraries\ItemLib;
use App\Libraries\OrderHelper;
use App\Libraries\OrderStatus;
use App\Libraries\System\Entity;
use App\Libraries\Truck;
use Illuminate\Http\Request;
use Validator;
use View;

Class EntityApiController extends Controller{

    private $_apiData = array();
    private $_mobile_json = FALSE;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";
        $this->_mobile_json = intval($request->input('mobile_json', 0)) > 0 ? TRUE : FALSE;
    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function listCity(Request $request)
    {

        $this->_apiData['error'] = 0;
        $city = new City();
        $states = $city->getStateList();
        $data = $rows = array();

        $this->_apiData['message'] = $this->_apiData['response'] = "success";

        if($states){
            foreach($states as $state){

                $record = new \StdClass();
                $record->state_id =  $state->state_id;
                $record->name =  $state->name;
                $record->code =  $state->code;
                $record->data = $city->getCityByState($state->state_id);


                $rows[] = $record;
            }
        }

        $data['state'] = $rows;
        $this->_apiData['data'] = $data;

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function getItemBox(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), array(
            'width' => 'required',
            'height' => 'required',

        ));

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $volume = CustomHelper::calculateVolume($request->all());

            if($volume > 0){

                $this->_apiData['message'] = $this->_apiData['response'] = "success";

                $item_lib = new ItemLib();
                $item_boxes = $item_lib->getItemBoxByVolume($volume);

                if($item_boxes->error == 1){
                    $this->_apiData['error'] = 1;
                    $this->_apiData['message'] = $item_boxes->message;
                }
                else{
                    if(isset($item_boxes->data->item_box[0])){
                        $data['item_box'] = isset($item_boxes->data->item_box[0]) ? $item_boxes->data->item_box[0] : new \StdClass() ;
                        $data['item_box']->volume = $volume;
                        // echo "<pre>"; print_r($item_boxes); exit;
                        $this->_apiData['data'] = $data;
                    }
                    else{
                        $this->_apiData['error'] = 1;
                        $this->_apiData['message'] = trans('api_errors.no_box_found');
                    }
                }
            }
            else{
                $this->_apiData['error'] = 1;
                $this->_apiData['message'] = trans('api_errors.invalid_dimension');
            }


        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Get suggested trucks and save temp selected list
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function getVehicle(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), array(
            'weight' => 'required',
            'volume' => 'required',
            'pickup_latitude' => 'required',
            'pickup_longitude' => 'required',
            'dropoff_latitude' => 'required',
            'dropoff_longitude' => 'required',

        ));

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['message'] = $this->_apiData['response'] = "success";
            //calculate total time
            $truck_lib = new Truck();
            $data =  $truck_lib->getSuggestedList($request->all());

            //save Truck Suggest List
           $truck_suggested_id = $truck_lib->saveSuggestedList($data);

            $this->_apiData['data']['truck'] = $data;
            $this->_apiData['data']['truck_suggested_id'] = $truck_suggested_id;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Save temp selected truck
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function saveSelectedTruck(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), array(
            'truck_suggested_id' => 'required',
            'truck_id' => 'required',

        ));

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['message'] = $this->_apiData['response'] = "success";
            //calculate total time
            $truck_lib = new Truck();

            //save Truck Suggest List
            $data = $truck_lib->saveSelectedTruck($request->truck_suggested_id,$request->truck_id);
            $data = json_decode(json_encode($data));

            if($data->error == 1){
                $this->_apiData['error'] = 1;
                $this->_apiData['message'] = $data->message;
            }
            else{
                $truck_raw = new \StdClass();
                $truck_raw->truck_selected_id = $data->entity_id;
                $this->_apiData['data'] = $truck_raw;
            }

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function saveDeliveryProfessional(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), array(
            'truck_selected_id' => 'required',
            'professional_id' => 'required',
        ));

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['message'] = $this->_apiData['response'] = "success";
            $prof_lib = new DeliveryProfessional();
           $data = $prof_lib->saveSelectedProfessional($request->truck_selected_id,$request->professional_id);

            $data = json_decode(json_encode($data));

            $this->_apiData['error'] = $data->error;
            $this->_apiData['message'] = $data->message;

        }

        return $this->__ApiResponse($request, $this->_apiData);

    }

    /**
     * Get Customer Recent Locations
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function getRecentLocation(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), array(
            'customer_id' => 'required',
            'city_id' => 'required',
        ));

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['message'] = $this->_apiData['response'] = "success";
            $city_model = new City();
            $data = $city_model->getRecentLocation($request->city_id,$request->customer_id);
            $locations = array();

            if($data){

                foreach($data as $raw){

                    $location = new \StdClass();
                    $location->address = $raw->address;
                    $location->latitude = $raw->latitude;
                    $location->longitude = $raw->longitude;

                    $city = new \StdClass();

                    $city->entity_id = $raw->city;
                    $city->name = $raw->city_name;
                    $city->code = $raw->city_code;
                    $city->latitude = $raw->latitude;
                    $city->longitude = $raw->longitude;

                    $location->city =  $city;

                    $locations[] = $location;
                    unset($location);
                }
            }

            //$data = json_decode(json_encode($data));

            $this->_apiData['error'] = 0;
            $this->_apiData['message'] = trans("api_errors.success");
            $this->_apiData['data']['location'] = $locations;

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function getDriverProfile(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), array(
            'driver_id' => "required|integer|exists:driver_flat,entity_id,deleted_at,NULL",
        ));

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['message'] = $this->_apiData['response'] = "success";
            $request_params = $request->all();

            $driver_lib = new Driver();
            $response = $driver_lib->getDriverProfile($request->driver_id,$request_params);

           // echo "<pre>"; print_r($response); exit;
            $this->_apiData['error'] = $response['error'];
            $this->_apiData['message'] = $response['message'];

            if(isset($response['data'])){
                $this->_apiData['data']['driver'] = $response['data'];
            }

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * getNotificationList
     * @param {object} $request
     */
    public function getNotificationList(Request $request)
    {
        $this->_apiData['error'] = 0;
        // validations
        $validator = Validator::make($request->all(), array(
            'entity_id'      => "required|integer|exists:sys_entity,entity_id,deleted_at,NULL",
            'entity_type_id' => "required|integer|exists:sys_entity_type,entity_type_id,deleted_at,NULL"
        ));
        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            $checkEntity = \DB::table('sys_entity')
                                ->where('entity_type_id',$request->input('entity_type_id'))
                                ->where('entity_id',$request->input('entity_id'))
                                ->first();
            if(count($checkEntity)){
                $this->_apiData['message'] = $this->_apiData['response'] = "success";
                $request_params = $request->all();

                $notification_lib = new EntityNotification();
                $response = $notification_lib->getNotificationList($request->all());

                $this->_apiData['error']   = $response['error'];
                $this->_apiData['message'] = $response['message'];

                if(isset($response['data'])){
                    $this->_apiData['data']['notification_list'] = $response['data']['records'];
                    $this->_apiData['data']['page']              = $response['data']['page'];
                }
            }else{
                $this->_apiData['error'] = 1;
                $this->_apiData['message'] = "Invalid entity id";
            }
        }
        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * update notification
     * @param {object} $request
     */
    public function updateNotification(Request $request)
    {
        $this->_apiData['error'] = 0;
        // validations
        $validator = Validator::make($request->all(), array(
            'entity_id'         => "integer|exists:sys_entity,entity_id,deleted_at,NULL",
            'entity_type_id'    => "integer|exists:sys_entity_type,entity_type_id,deleted_at,NULL",
            'entity_history_id' => "required|integer|exists:sys_entity_history,entity_history_id,deleted_at,NULL",
        ));
        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            $this->_apiData['error']   = 0;
            $this->_apiData['message'] = "success";
            $this->_apiData['data']    = [];
            //update history notification flag
            \DB::table('sys_entity_history')
                ->where('entity_history_id',$request->input('entity_history_id'))
                ->update([
                    'is_read' => 1
                ]);
        }
        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Get Customer Orders
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function orderSearch(Request $request)
    {
        $this->_apiData['error'] = 0;
        $error_messages = array(
            'end_date.date_greater' =>trans('validation.date_greater',array('other'=>'start_date'))
        );
        // validations
        $validator = Validator::make($request->all(), [
            'customer_id' => "required|integer|exists:customer_flat,entity_id,deleted_at,NULL",
            'start_date'    => 'date|date_format:Y-m-d',
            'end_date'      => 'date_format:Y-m-d|date_greater:start_date,'.$request->start_date,
        ],$error_messages);


        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['error'] = 0;
            $order_helper = new OrderHelper();
            $response =  $order_helper->searchOrder($request->all());

            $this->_apiData['error'] = $response->error;
            if($response->error == 1){
                $this->_apiData['message'] = $response->message;
            }else{
                $this->_apiData['message'] = $response->response;
            }

            if(isset($response->data)){
                $this->_apiData['data'] = $response->data;
            }
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * getCustomerGeneralSetting
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function getCustomerGeneralSetting(Request $request)
    {
        $this->_apiData['error'] = 0;
        // validations
        $validator = Validator::make($request->all(), [
            'entity_id'   => "required|integer|exists:sys_entity,entity_id,deleted_at,NULL"
        ]);
        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $entity_model = new SYSEntity();
            $entity = $entity_model->getData($request->entity_id, 11, TRUE);

            if ($entity && (isset($entity->auth) && $entity->auth->status != 1)) {
                // kick user
                 $this->_apiData['kick_user'] = 1;
                // message
                $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
            }else{

                $this->_apiData['error'] = 0;
                //pending order count
                $order_helper    = new OrderHelper();
                $getPendingOrder =  $order_helper->getPendingOrder($request);
                //unread notification
                $notification_lib = new EntityNotification();
                $response = $notification_lib->getNotificationList($request->all(),true);
                //general setting
                $general_setting = new GeneralSetting();
                $getSetting      = $general_setting->getSetting();

                $this->_apiData['error']   = 0;
                $this->_apiData['message'] = "success";
                $this->_apiData['data']['setting']['general_setting'] = $getSetting;
                $this->_apiData['data']['setting']['pending_orders'] = $getPendingOrder;
                $this->_apiData['data']['setting']['unread_notification'] = $response['totalRecord'];
            }

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function orderComplete(Request $request)
    {
        $this->_apiData['error'] = 0;
        // validations
        $validator = Validator::make($request->all(), [
            'order_id' => "required|integer|exists:order_flat,entity_id,deleted_at,NULL",
        ]);
        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['error'] = 0;

            $flat_order = new SYSTableFlat('order');
            $row = $flat_order->getDataByWhere(' entity_id = '.$request->order_id);
            if($row){
                $order = $row[0];
                //  echo "<pre>"; print_r($order); exit;

                $order_status_lib = new OrderStatus();
                $reached_status = $order_status_lib->getIdByKeywords('reached');

                if($order->order_status == $reached_status){

                    $arr = [];
                    $arr['entity_type_id'] = 68;
                    $arr['order_id'] = $request->order_id;
                    $arr['driver_id'] = $order->driver_id;
                    $arr['order_status'] = 'completed';
                    $arr['mobile_json'] = 1;

                    $entity_lib = new Entity();
                    $response = $entity_lib->apiPost($arr);
                    $response = json_decode(json_encode($response));

                    $this->_apiData['error'] = $response->error;
                    $this->_apiData['message'] = $response->message;

                    if(isset($response->data)){
                        $this->_apiData['data']['order'] = $response->data;
                    }
                }
                else{
                    $this->_apiData['error'] = 1;
                    $this->_apiData['message'] = trans('api_errors.order_cannot_complete');

                }

            }
            else{
                $this->_apiData['error'] = 1;
                $this->_apiData['message'] = trans('api_errors.entity_is_invalid',array('entity'=>'order'));

            }

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    public function trackDriver(Request $request)
    {
        $this->_apiData['error'] = 0;
        // validations
        $validator = Validator::make($request->all(), [
            'order_id' => "required|integer|exists:order_flat,entity_id,deleted_at,NULL",
        ]);
        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['error'] = 0;
            $this->_apiData['message'] = trans('api_errors.success');

            $arr = [];
            $arr['entity_type_id'] = 'order';
            $arr['entity_id'] = $request->order_id;
            $arr['hook'] = 'order_pickup,order_dropoff,order_driver_location';
            $arr['mobile_json'] = 1;

            $entity_lib = new Entity();
            $response = $entity_lib->apiGet($arr);
            $response = json_decode(json_encode($response));
           // echo "<pre>"; print_r($response); exit;
            $this->_apiData['error'] = $response->error;

            if(isset($response->message)){
                $this->_apiData['message'] = $response->message;
            }

            if(isset($response->data)){

                $data = new \StdClass();
                $data->order_pickup = $response->data->order->order_pickup;
                $data->order_dropoff = $response->data->order->order_dropoff;
                $data->driver_location = isset($response->data->order->order_driver_location[0]->driver_location) ? $response->data->order->order_driver_location[0]->driver_location : "";

                $this->_apiData['data']['tracking'] = $data;
            }

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

}

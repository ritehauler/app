<?php
namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Http\Models\FlatTable;
use App\Http\Models\SYSTableFlat;
use App\Http\Models\Truck;
use App\Libraries\Driver;
use App\Libraries\GeneralSetting;
use App\Libraries\OrderHelper;
use App\Libraries\OrderItem;
use App\Libraries\OrderProcess;
use App\Libraries\OrderStatus;
use App\Libraries\System\Entity;
use Illuminate\Http\Request;
use Validator;
use View;

Class DriverApiController extends Controller
{

    private $_apiData = [];
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
    public function verifyExtraItem(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), array(
            //'driver_id' => "required|integer|exists:driver_flat,entity_id,deleted_at,NULL",
            'order_id' => "required|integer|exists:order_flat,entity_id,deleted_at,NULL",
           // 'truck_id' => "required|integer|exists:truck_flat,entity_id,deleted_at,NULL",
            'extra_item' => 'required',
        ));

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $request_params = json_decode(json_encode($request->all()));
            //Validate order items
            $order_item_lib = new OrderItem();
            $validate_items = $order_item_lib->validateItems($request_params->extra_item);

            if($validate_items->error == 1){
                $this->_apiData['error'] = 1;
                $this->_apiData['message'] = $validate_items->message;
            }else{

                //calculate Weight and volume of extra items
                $total_load = $order_item_lib->calcTotalLoad($request_params->extra_item);
               // echo "<pre>"; print_r($total_load);
                //add items load in order load
                $order_helper = new OrderProcess();
                $order_load = $order_helper->addExtraLoad($request_params->order_id,$total_load->volume,$total_load->weight);

                //validate truck capability
                $truck_model = new Truck();
                $verify_truck = $truck_model->verifyTruckCapacity($request_params->order_id,$order_load->volume,$order_load->weight);


                if(!$verify_truck){
                    $this->_apiData['error'] = 1;
                    $this->_apiData['message'] = trans('api_errors.truck_not_capable');
                }
                else{
                    $this->_apiData['message'] = 'Success';
                }

            }
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\Response|\StdClass
     */
    public function addExtraItem(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), array(
            'driver_id' => "required|integer|exists:driver_flat,entity_id,deleted_at,NULL",
            'order_id' => "required|integer|exists:order_flat,entity_id,deleted_at,NULL",
            'extra_item' => 'required',
        ));

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $request_params = json_decode(json_encode($request->all()));

            //Validate order items
            $order_item_lib = new OrderItem();
            $validate_items = $order_item_lib->validateItems($request_params->extra_item);

            if($validate_items->error == 1){
                $this->_apiData['error'] = 1;
                $this->_apiData['message'] = $validate_items->message;
            }else{

                $order_process = new OrderProcess();
                $response =  $order_process->addExtraItems($request_params);

                $this->_apiData['error'] = $response->error;
                $this->_apiData['message'] = $response->message;
            }
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Update Final charges
     * @param Request $request
     * @return \App\Http\Controllers\Response|\StdClass
     */
    public function updateFinalOrder(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), [
            'driver_id' => "required|integer|exists:driver_flat,entity_id,deleted_at,NULL",
            'order_id' => "required|integer|exists:order_flat,entity_id,deleted_at,NULL",
            'total_minutes' => 'required',
            'total_distance' => 'required',
        ]);

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $order_process = new OrderProcess();
            $response =  $order_process->updateFinalOrder($request->all());
           // echo "<pre>"; print_r($response); exit;

            $this->_apiData['error'] = $response->error;
            $this->_apiData['message'] = $response->message;

            if(isset($response->data)){
                $this->_apiData['data']['order'] = $response->data;
            }


        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Get Driver Assigned Orders
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function getAssignedOrders(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), [
            'driver_id' => "required|integer|exists:driver_flat,entity_id,deleted_at,NULL",
            'pickup_date' => 'date|date_format:Y-m-d'
        ]);

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

           $pickup_date  = (isset($request->pickup_date)) ? $request->pickup_date : date('Y-m-d');
            $request_params = $request->all();

            if((isset($request->pickup_date))){
                unset($request_params['pickup_date']);
            }

            $params = [];
            $params['entity_type_id'] = 'order';
            $params['driver_id'] = $request->driver_id;
            $params['order_status'] = 'assigned';
           $params['where_condition'] = ' AND pickup_date >= "'.$pickup_date.'"';
            $params['order_by'] = 'pickup_date,pickup_time';
            $params['sorting'] = 'ASC';
            $params['mobile_json'] =  $this->_mobile_json;

            $params = array_merge($params,$request_params);


           // echo "<pre>"; print_r($params); exit;
           $entity_lib = new Entity();
            $response =  $entity_lib->apiList($params);

            $response = json_decode(json_encode($response));

            if(isset($response->data->order) && isset($response->data->order[0])){

                $setting_model = new GeneralSetting();
                $grace_min = $setting_model->getColumn('order_accept_grace_min');

                foreach($response->data->order as $key => $order){

                    $order_flat = new SYSTableFlat('order_history');
                    $history_raw = $order_flat->getDataByWhere(' order_id = '.$order->entity_id.' AND order_status = 996',array('created_at'),'DESC');

                    $date_assigned = (($history_raw) && isset($history_raw[0])) ? $history_raw[0]->created_at : '';
                    $response->data->order[$key]->date_assigned_at = $date_assigned;
                    $response->data->order[$key]->order_accept_grace_min = $grace_min;
                }
            }

           // echo "<pre>"; print_r($response); exit;

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
     * Get Driver Current Orders
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function getCurrentOrders(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), [
            'driver_id' => "required|integer|exists:driver_flat,entity_id,deleted_at,NULL",
           // 'pickup_date' => 'date|date_format:Y-m-d'
        ]);

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $pickup_date  = (isset($request->pickup_date)) ? $request->pickup_date : date('Y-m-d');
            $request_params = $request->all();

            if((isset($request->pickup_date))){
                unset($request_params['pickup_date']);
            }

            $order_status_lib = new OrderStatus();
            $driver_statuses = $order_status_lib->getDriverCurrentStatuses('"accepted","arrived","on_the_way"');
            $driver_statuses = implode(',',$driver_statuses);

            //echo "<pre>"; print_r($driver_statuses); exit;
            $params = [];
            $params['entity_type_id'] = 'order';
            $params['driver_id'] = $request->driver_id;
            $params['where_condition'] = ' AND (pickup_date = "'.$pickup_date.'" AND order_status IN ('.$driver_statuses.'))';
            $params['order_by'] = 'pickup_time';
            $params['sorting'] = 'ASC';
            $params['mobile_json'] =  $this->_mobile_json;

            $params = array_merge($params,$request_params);


            //echo "<pre>"; print_r($params); exit;
            $entity_lib = new Entity();
            $response =  $entity_lib->apiList($params);

            $response = json_decode(json_encode($response));
            // echo "<pre>"; print_r($response); exit;

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
     * Get Driver Current Orders
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function getPendingOrders(Request $request)
    {
        $this->_apiData['error'] = 0;

        // validations
        $validator = Validator::make($request->all(), [
            'driver_id' => "required|integer|exists:driver_flat,entity_id,deleted_at,NULL",
            // 'pickup_date' => 'date|date_format:Y-m-d'
        ]);

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $pickup_date  = (isset($request->pickup_date)) ? $request->pickup_date : date('Y-m-d');
            $request_params = $request->all();

            if((isset($request->pickup_date))){
                unset($request_params['pickup_date']);
            }

            $order_status_lib = new OrderStatus();
            $driver_statuses = $order_status_lib->getDriverCurrentStatuses('"accepted","arrived","on_the_way","assigned"');
            $driver_statuses = implode(',',$driver_statuses);

            //echo "<pre>"; print_r($driver_statuses); exit;
            $params = [];
            $params['entity_type_id'] = 'order';
            $params['driver_id'] = $request->driver_id; //pickup_date = "'.$pickup_date.'" AND
            $params['where_condition'] = ' AND (pickup_date >= "'.$pickup_date.'" AND order_status IN ('.$driver_statuses.'))';
            $params['multi_order_by'] = 'pickup_date ASC,pickup_time ASC';
            $params['limit'] = -1;
            $params['mobile_json'] =  $this->_mobile_json;

            $params = array_merge($params,$request_params);

            //echo "<pre>"; print_r($params); exit;
            $entity_lib = new Entity();
            $response =  $entity_lib->apiList($params);

            $response = json_decode(json_encode($response));
           // echo "<pre>"; print_r($response); exit;

            $this->_apiData['error'] = $response->error;
            if($response->error == 1){
                $this->_apiData['message'] = $response->message;
            }else{
                $this->_apiData['message'] = $response->response;
            }

            if(isset($response->data->order)){

                //echo "<pre>"; print_r($response->data->order); exit;
                $order_helper = new OrderHelper();
                $orders = $order_helper->filterByPickupDate($response->data->order);

                $this->_apiData['data'] = $orders;
            }else{
                $this->_apiData['data'] = $response;
            }

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Driver Stats
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function driverStats(Request $request)
    {
        $this->_apiData['error'] = 0;
        $error_messages = array(
            'end_date.date_greater' =>trans('validation.date_greater',array('other'=>'start_date'))
        );

        // validations
        $validator = Validator::make($request->all(), [
            'driver_id' => "required|integer|exists:driver_flat,entity_id,deleted_at,NULL",
            'start_date'    => 'date|date_format:Y-m-d',
            'end_date'      => 'date_format:Y-m-d|date_greater:start_date,'.$request->start_date,
        ],$error_messages);

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['error'] = 0;
            $this->_apiData['message'] = trans('api_errors.success');

            $request_params = $request->all();
            $request_params['entity_id'] = $request_params['driver_id'];

            $order_helper = new OrderHelper();
            $current = $order_helper->getDriverOrderStats($request_params,false);
            $previous = $order_helper->getDriverPreviousStats($request_params);
            $graph = $order_helper->getOrderGraphStats($request_params);

            $response = new \StdClass();
            $response->current = isset($current->monthly) ? $current->monthly : "";
            $response->previous = isset($previous->monthly) ? $previous->monthly : "";


            $this->_apiData['data']['monthly'] = $response;
            $this->_apiData['data']['graph'] = $graph;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Get Weekly Stats
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function weeklyStats(Request $request)
    {
        $this->_apiData['error'] = 0;
        $error_messages = array(
            'end_date.date_greater' =>trans('validation.date_greater',array('other'=>'start_date'))
        );

        // validations
        $validator = Validator::make($request->all(), [
            'driver_id' => "required|integer|exists:driver_flat,entity_id,deleted_at,NULL",
        ],$error_messages);

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['error'] = 0;
            $this->_apiData['message'] = trans('api_errors.success');

            $request_params = $request->all();
            $request_params['entity_id'] = $request_params['driver_id'];

            $order_helper = new OrderHelper();
            $response = $order_helper->getDriverWeeklyStats($request_params,true);

            $this->_apiData['data']['weekly'] = $response;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Update Driver Locations
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function updateLocations(Request $request)
    {
        $this->_apiData['error'] = 0;
        // validations
        $validator = Validator::make($request->all(), [
            'driver_id' => "required|integer|exists:driver_flat,entity_id,deleted_at,NULL",
            'driver_location' => 'required'
        ]);

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['error'] = 0;

            $helper = new Driver();
            $response = $helper->updateLocation($request->all());

            $response = json_decode(json_encode($response));
         // echo "<pre>"; print_r($response); exit;
            $this->_apiData['error'] = $response->error;
            $this->_apiData['message'] = $response->message;

            if(isset($response->data)){
                $this->_apiData['data'] = $response->data;
            }
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function orderStatuses(Request $request)
    {
        $this->_apiData['error'] = 0;

        $flat_model = new SYSTableFlat('order_statuses');
        $order_statuses = $flat_model->getDataByWhere(' keyword NOT IN ("pending","confirmed","cancelled","assigned")');

        $this->_apiData['data'] = $order_statuses;
        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
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
            'driver_id' => "required|integer|exists:driver_flat,entity_id,deleted_at,NULL",
            'start_date'    => 'date|date_format:Y-m-d',
            'end_date'      => 'date_format:Y-m-d|date_greater:start_date,'.$request->start_date,
        ],$error_messages);


        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['error'] = 0;
            $this->_apiData['message'] = trans('api_errors.success');

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

    public function getGeneralSetting(Request $request)
    {
        $this->_apiData['error'] = 0;
        // validations
        $validator = Validator::make($request->all(), [
            'driver_id' => "required|integer|exists:driver_flat,entity_id,deleted_at,NULL",
        ]);

        if ($validator->fails()) {
            $this->_apiData['error'] = 1;
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            $this->_apiData['error'] = 0;
            $this->_apiData['message'] = trans('api_errors.success');

            $flat_table = new FlatTable();
            $pending_order = $flat_table->getDriverPendingOrder($request->driver_id);

            $general_setting_model = new GeneralSetting();
            $data['setting'] = $general_setting_model->getSetting();
            $data['setting']->pending_order = ($pending_order) ? $pending_order : 0;

            $this->_apiData['data'] = $data;

        }


        return $this->__ApiResponse($request, $this->_apiData);
    }

}



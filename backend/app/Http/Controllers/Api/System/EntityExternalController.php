<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Http\Models\SYSEntityType;
use App\Libraries\CustomHelper;
use App\Libraries\GeneralSetting;
use Illuminate\Http\Request;


class EntityExternalController extends Controller
{

    private $_apiData = array();
    private $_model = array();
    private $_jsonData = array();
    private $_externalConfig = array();

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load entity model
        $this->_model['entity_type'] = new SYSEntityType();

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

        $this->_externalConfig = config('constants.EXTERNAL_CALL_DETAIL');
    }

    /**
     * Call entity external call through curl
     *
     * @return Response
     */


    public function index(Request $request)
    {
        $result = $this->_model['entity_type']->getEntityTypeById($request->entity_type_id);
        $fn_before = $this->__convertToCamel($result->identifier.'_before');
        $fn_after = $this->__convertToCamel($result->identifier.'_after');

        $params = $request->all();
        if(method_exists($this, $fn_before)){
            $params = $this->$fn_before($params);
        }

        $this->_apiData = $this->apiPostRequest($result->external_url, $request->method(), $params, true);

        if(method_exists($this, $fn_after)){
            $this->$fn_after();
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    //orderAssign
    public function deliveryCreate(Request $request)
    {
        $params_internal['entity_type_id'] = $request['entity_type_id'];
        $params_internal['entity_id'] = $request['entity_id'];
        $departmental_status = $request['departmental_status'];
        $departmental_order_status = $request['order_status'];
        $user_id = $request['user_id'];
        $department_id = $request['department_id'];
        $role_id = $request['role_id'];
        $wfs_ti_id = $request['wfs_ti_id'];
        $params_internal['mobile_json'] = 1;
        $params_internal['hook'] = 'order_item';
        $first_request = $request->all();

        $url = 'api/system/entities';
        $internal_response = CustomHelper::internalCall($request, $url,'get',$params_internal,false);
        $response = (isset($internal_response->data->order)) ? $internal_response->data->order : $internal_response->data->entity;

        $items = [];
        $item_count = 0;
        $obj_general_setting = new GeneralSetting();

        foreach($response->order_item as $item){

            $items[$item_count]['item_id'] = $item->entity_id;
            $items[$item_count]['name'] = $item->product_id->value;
            $items[$item_count]['quantity'] = $item->quantity;
            $items[$item_count]['unit_value'] = isset($item->product_id->detail->item_unit->value) ?$item->product_id->detail->item_unit->value : '';
            $items[$item_count]['unit_weight'] = isset($item->product_id->detail->item_unit->option) ? $item->product_id->detail->item_unit->option : '';
            $items[$item_count]['unit_volume'] = 'NULL';
            $items[$item_count]['unit_price'] = $item->price;
            $items[$item_count]['unit_currency'] = $obj_general_setting->getCurrency();
            $items[$item_count]['total_price'] = ($item->quantity * $item->price);
            $items[$item_count]['unit_text'] = $items[$item_count]['unit_currency'].$items[$item_count]['total_price'].'/'.$items[$item_count]['unit_weight'];

            $item_count++;
        }


        $url = $this->_externalConfig['url'].'request/delivery/create';
        $type = 'post';


        $params['order_id'] = $response->entity_id;

        $params['customer_id'] = $response->customer_id->id;
        $params['customer_phone'] = $response->customer_id->detail->auth->mobile_no;
        $params['customer_first_name'] = $response->customer_id->detail->first_name;
        $params['customer_last_name'] = $response->customer_id->detail->last_name;
        $params['customer_place'] = (isset($response->shipping_address->value))? $response->shipping_address->value : 'coming soon!';

        $params['width'] = isset($response->width)? $response->width: 0;
        $params['height'] = isset($response->height)? $response->height: 0;
        $params['len'] = isset($response->lenght)? $response->lenght: 0;
        //$params['volume_unit'] = 'cm';
        $params['weight'] = isset($response->weight)? $response->weight : 0;
        //$params['weight_unit'] = 'kg';

        $params['quantity'] = isset($response->total_items) ? $response->total_items : 0;
        $order_amount = isset($response->subtotal_with_discount)? abs($response->subtotal_with_discount) + 0 : 0;
        $order_grand_amount = isset($response->grand_total)? abs($response->grand_total) + 0 : 0;
        $params['order_cost'] = empty($order_amount) ? $order_grand_amount : $order_amount;

        $params['order_notes'] = isset($response->order_notes)? $response->order_notes: '';;
        $params['commission_for_rider'] = isset($response->commission_for_rider)? $response->commission_for_rider: 0;

        $params['pickup_latitude'] = isset($response->pickup_latitude)? $response->pickup_latitude: '24.86909400';
        $params['pickup_longitude'] = isset($response->pickup_longitude)? $response->pickup_longitude: '67.08574200';

        $params['dropoff_latitude'] = isset($response->shipping_address->detail->latitude)? $response->shipping_address->detail->latitude : '24.86909200';
        $params['dropoff_longitude'] = isset($response->shipping_address->detail->longitude)? $response->shipping_address->detail->longitude : '67.08579900';

        // if delivery date time is not coming, then add +24 hours in date time
        $params['delivery_date'] = (isset($response->user_delivery_date)|| !empty($response->user_delivery_date))? $response->user_delivery_date: '';
        $params['delivery_date'] = (empty($params['delivery_date']))? date("Y-m-d", strtotime('+24 hours')) : date("Y-m-d", strtotime($params['delivery_date']));

        // if delivery date time is not coming, then add +24 hours in date time
        $params['delivery_time'] = (isset($response->user_delivery_time))? $response->user_delivery_time: '';
        $params['delivery_time'] = (empty($params['delivery_time']))? date("H:i", strtotime('+20 hours')) : date("H:i", strtotime($params['delivery_time']));

        // if delivery date time is not coming, then add +24 hours in date time
        $delivery_time_end = strtotime($params['delivery_time']) + 3600*4;
        $params['delivery_time_end'] = (isset($response->user_delivery_time_end))? $response->user_delivery_time_end: '';
        $params['delivery_time_end'] = (empty($params['delivery_time_end']))? date("H:i", $delivery_time_end) : date("H:i", strtotime($params['delivery_time_end']));

        $params['payment_method'] = (isset($response->payment_method_type->value))? $response->payment_method_type->value : 'COD';
        $params['currency'] = '';

        $params['metadata'] = json_encode([
            //'entity_details' => $response,
            'order_details' => ['items' => $items],
            'callback_params' => $first_request,
        ]);

        $params['tags'] = '';
        $params['callback_uri'] = HTTP_URL.DIR_API.'order/driver/update';

        $response = [];
        $response = CustomHelper::guzzleHttpRequest($url, $params, $this->_externalConfig['headers']);

        $response_block = json_decode($response->getContents());

        $params_internal['departmental_status'] = $departmental_status;
        $params_internal['order_status'] = $departmental_order_status;
        $params_internal['wfs_ti_id'] = $wfs_ti_id;

        if($response_block->error == 1) {

            $url1 = 'api/wfs/user/assign';
            $url2 = 'api/wfs/user/update';
            $params_internal_err['ti_id'] = $wfs_ti_id;
            $params_internal_err['user_id'] = $user_id;
            $params_internal_err['role_id'] = $role_id;
            $params_internal_err['department_id'] = $department_id;
            $params_internal_err['state_id'] = 3; // declined
            $params_internal_err['is_admin'] = 1; // admin status

            $internal_response_1 = CustomHelper::internalCall($request, $url1,'post',$params_internal_err,false);
            $internal_response = CustomHelper::internalCall($request, $url2,'post',$params_internal_err,false);

            $params_comment['target_id'] = $user_id;
            $params_comment['target_type'] = "'".$role_id."'";

            $entity_type_model = new SYSEntityType();
            $order_discussion_entity_type_id = $entity_type_model->getIdByIdentifier('order_discussion');

            $params_comment['visible_to_customer'] = 0;
            $params_comment['order_id'] = $params_internal['entity_id'];
            $params_comment['order_message'] = (!empty($response_block->message))? $response_block->message : 'Unable to get driver assignment';
            $params_comment['entity_type_id'] = $order_discussion_entity_type_id; //31; // to add comments

            $ret = $this->__internalCall($request, 'api/system/order/comment/add','POST',$params_comment,false);
        }else{
            $url1 = 'api/wfs/user/assign';
            $url2 = 'api/wfs/user/update';
            $params_internal_err['ti_id'] = $wfs_ti_id;
            $params_internal_err['user_id'] = $user_id;
            $params_internal_err['role_id'] = $role_id;
            $params_internal_err['department_id'] = $department_id;
            $params_internal_err['state_id'] = 2; // accepted
            $params_internal_err['is_admin'] = 1; // admin status

            $internal_response_1 = CustomHelper::internalCall($request, $url1,'post',$params_internal_err,false);
            //$internal_response = CustomHelper::internalCall($request, $url2,'post',$params_internal_err,false);
        }

        $url = 'api/system/entities/update';
        $internal_response = CustomHelper::internalCall($request, $url,'post',$params_internal,false);
        /*
         * call for internal to update external reference id and departmental status
         *
         * */

        return $response;
    }

    public function deliveryUpdate(Request $request)
    {
        // update task instance
        // update order
    }

}
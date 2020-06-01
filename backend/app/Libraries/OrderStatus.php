<?php

/**
 * Description: this library is to related to order statuses
 * Author: Samreen <samreen.quyyum@cubixlabs.com>
 * Date: 04-July-2018
 * Time: 01:00 PM
 * Copyright: CubixLabs
 */
namespace App\Libraries;


use App\Http\Models\FlatTable;
use App\Http\Models\SYSEntityHistory;
use App\Http\Models\SYSTableFlat;
use App\Libraries\System\Entity;

Class OrderStatus
{

    private $_sysTableFlatModel = '';

    /**
     * ProductHelper constructor.
     */
    public function __construct()
    {
        $this->_sysTableFlatModel = new SYSTableFlat('order_statuses');
    }

    /**
     * @param $request
     * @return mixed
     */
    public function updateOrderStatus($request,$update_order_flage = false)
    {
        $return['error'] = 0;
        try{
            //update order
            $order_process = new OrderProcess();
            $order_response = $order_process->updateOrderDriverEnd($request);
            if($order_response->error == 1){
                $return['error'] = 1;
                $return['message'] = $order_response->message;
                return $return;
            }
            //order status
            $order_status = $order_response->data->attributes->order_status->detail->attributes->keyword;
            //driver notification
            $driver_notification_status = ['assigned'];
            if(in_array($order_status,$driver_notification_status)){
                $other_data['entity_type_id'] = $order_response->data->entity_type_id;
                $other_data['actor_entity_type_id'] = 'driver';
                $other_data['extension_ref_table'] = 'order_flat';
                $other_data['extension_ref_id'] = $order_response->data->entity_id;
                $timestamp = date("Y-m-d H:i:s");
                $sys_history = new SYSEntityHistory();
                $sys_history->logHistory('order_driver_notify', $order_response->data->entity_id, $order_response->data->attributes->driver_id->id, $other_data, $timestamp, $request);
            }
            //customer notification
            $customer_notification_status = ['confirmed','cancelled','arrived','reached','on_the_way','driver_cancelled','completed'];
            if(in_array($order_status,$customer_notification_status) || $update_order_flage === true){
                $other_data['entity_type_id'] = $order_response->data->entity_type_id;
                $other_data['actor_entity_type_id'] = 'customer';
                $other_data['extension_ref_table'] = 'customer_flat';
                $other_data['extension_ref_id'] = $order_response->data->attributes->customer_id->id;
                $timestamp = date("Y-m-d H:i:s");
                $sys_history = new SYSEntityHistory();
                $sys_history->logHistory('order_customer_notify', $order_response->data->entity_id, $order_response->data->attributes->customer_id->id, $other_data, $timestamp, $request);
            }
            //response message
            $return['message'] = "Order updated successfully";

        }
        catch (\Exception $e) {
            $return['error'] = 1;
            $return['message'] = $e->getMessage();
            $return['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine() . " : Stack " . $e->getTraceAsString();
        }
       // exit;
        return $return;
    }



    /**
     * @param $status_keys
     * @return array|bool
     */
    public function getDriverCurrentStatuses($status_keys)
    {
        $sys_flat_model = new SYSTableFlat('order_statuses');
        $rows = $sys_flat_model->getDataByWhere(' keyword IN ('.$status_keys.')',array('entity_id'));

        if($rows){
            foreach($rows as $row){
                $status_ids[] = $row->entity_id;
            }
            return $status_ids;
        }
        return false;
    }

    /**
     * Get order statuses by keyword
     * @param $keyword
     * @return array|bool
     */
    public function getIdByKeywords($keyword = false)
    {
        if($keyword){
            $order_helper = new OrderHelper();
            $row =  $order_helper->getOrderStatusData($keyword);
            if(isset($row->entity_id))
           return  $status_ids["$row->keyword"] = $row->entity_id;
        }else{
            $rows = $this->_sysTableFlatModel->getAll();
            if($rows) {
                foreach ($rows as $row) {
                    $status_ids["$row->keyword"] = $row->entity_id;
                }

                return $status_ids;
            }
        }

        return false;
    }






}
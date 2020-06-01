<?php

/**
 * This is written to send notification
 * Class CustomNotificationController
 * Date: 13-07-2018
 * Author: Cubix
 * Copyright: cubix
 */

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Http\Models\Custom\OrderFlat;
use App\Http\Models\SYSTableFlat;
use App\Libraries\Driver;
use App\Libraries\GeneralSetting;
use App\Libraries\OrderStatus;
use App\Libraries\System\Entity;
use Illuminate\Http\Request;

Class OrderController extends Controller
{
    /**
     * auto assign service
     * @param Request $request
     */
    public function assignOrder(Request $request)
    {
        $current_date = date('Y-m-d');

        $order_status_lib = new OrderStatus();
        $order_statuses = $order_status_lib->getIdByKeywords();
        $status_id = $order_statuses["confirmed"];
        $status_id .= ','.$order_statuses["declined"];

        $order_flat = new SYSTableFlat('order');
        $where = ' pickup_date >= "'.trim($current_date).'" AND order_status IN ('.$status_id.')';
        $orders =  $order_flat->getDataByWhere($where);

        echo '<h3>Orders to assign</h3>';
        echo "<pre>"; print_r($orders);

        if($orders){

            $entity_lib = new Entity();

            foreach($orders as $order){

                $driver_lib = new Driver();
                $drivers = $driver_lib->availableDrivers($order);

               // echo "<pre>"; print_r($drivers);

                if(count($drivers) > 0){

                    $driver = $drivers[0];

                    $arr = [];
                    $arr['entity_type_id'] = 68;
                    $arr['order_id'] = $order->entity_id;
                    $arr['driver_id'] = $driver->driver_id;
                    $arr['order_status'] = $order_statuses["assigned"];

                    echo "<pre>"; print_r($arr);
                    $arr_response = $entity_lib->apiPost($arr);
/*
                    //Update Order status / Driver
                     $order_status_lib = new OrderStatus();


                    $arr['order_status'] = $order_statuses["assigned"];
                    $order_response = $order_status_lib->updateOrderStatus((object)$arr);

                    dd($order_response);*/

                  //  echo "<pre>"; print_r($arr_response);

                }
                else{
                    echo '<h3>No Driver available</h3>';
                }
            }

        }
        else{
            echo '<h3>No Orders for today</h3>';
        }

    }

    /**
     * Auto Decline service
     * @param Request $request
     */
    public function autoDecline(Request $request)
    {
        $order_flat = new OrderFlat();
        $orders = $order_flat->assignOrder();

        $setting_model = new GeneralSetting();
        $grace_min = $setting_model->getColumn('order_accept_grace_min');
        $grace_min = $grace_min + config('constants.ADD_MIN_IN_AUTO_DECLINE');

        echo '<h3>Assign Orders</h3>';


        if($orders && count($orders)>0){

            echo "<pre>"; print_r($orders);

            $entity_lib = new Entity();

            foreach($orders as $order){

                //Get order assignment date
                $order_flat = new SYSTableFlat('order_history');
                $history_raw = $order_flat->getDataByWhere(' order_id = '.$order->entity_id.' AND order_status = 996',array('created_at'),'DESC');

                $date_assigned = (($history_raw) && isset($history_raw[0])) ? $history_raw[0]->created_at : '';

                // Create a new \DateTime instance
                $date = \DateTime::createFromFormat('Y-m-d H:i:s',$date_assigned);
                $date->modify('+'.$grace_min.' minutes');
                 $assignment_date = $date->format('Y-m-d H:i:s');

                $current_date = date('Y-m-d H:i:s');
                 if(strtotime($current_date) > strtotime($assignment_date)){

                     //Auto Decline
                     $arr = [];
                     $arr['entity_type_id'] = 68;
                     $arr['order_id'] = $order->entity_id;
                     $arr['driver_id'] = $order->driver_id;
                     $arr['vehicle_id'] = $order->vehicle_id;
                     $arr['order_status'] = 'declined';
                     $arr['comment'] = 'This is Auto decline';

                     echo 'Order auto decline';
                     echo "<pre>"; print_r($arr);
                     $arr_response = $entity_lib->apiPost($arr);

                 }

            }
        }else{
            echo 'No orders found';
        }

    }
}
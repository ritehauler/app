<?php
namespace App\Libraries;

use App\Http\Models\Custom\OrderFlat;
use App\Http\Models\Extension\Social\ExtPackageRate;
use App\Http\Models\FlatTable;
use App\Http\Models\SYSAttributeOption;
use App\Http\Models\SYSTableFlat;
use App\Http\Models\WFSTaskInstance;
use App\Http\Requests\Request;
use App\Libraries\CustomHelper;
use App\Libraries\System\Entity;
use Carbon\Carbon;

/**
 * Class OrderHelper
 */

Class OrderHelper
{

    private $_SYSTableFlatModel = '';

    /**
     * ProductHelper constructor.
     */
    public function __construct()
    {
        $this->_SYSTableFlatModel = new SYSTableFlat('order');
        $this->_orderStatusModel = new SYSTableFlat('order_statuses');
    }


    /**
     * Get Order Display Title
     * @param $keyword
     * @return mixed
     */
    public function getOrderStatusDisplayTitle($keyword)
    {
        return $this->_orderStatusModel->columnValueByWhere('keyword',$keyword,'display_title');
    }

    /**
     * Get order status Data
     * @param $keyword
     * @return mixed
     */

    public function getOrderStatusData($keyword)
    {
        return $this->_orderStatusModel->getColumnsByWhere('keyword',$keyword,array('entity_id','title','display_title','keyword'));
    }


    /**
     * Get order status entity id
     * @param $keyword
     * @return string
     */
    public function getOrderStatusIdByKeyword($keyword)
    {
        $status_data = $this ->getOrderStatusData($keyword);
        return ($status_data) ? $status_data->entity_id : "";
    }

    /**
     * @param $customer_id
     * @return string
     */
    public function createOrderNumber($customer_id = false)
    {
        //time().$customer_id;
        return time();
    }

    /**
     * Get Order Items
     * @param $order_id
     * @return bool
     */
    public function getOrderItems($order_id)
    {
        $order_item_flat = new SYSTableFlat('order_item');
        $where_condition = 'order_id = '.$order_id;
        return $order_item_flat->getDataByWhere($where_condition);
    }

    /**
     * Get order
     * @param $order_id
     * @return bool
     */
    public function getOrder($order_id)
    {
        return $this->_SYSTableFlatModel->getColumnsByWhere('order_id',$order_id);
    }

    /**
     * @param $order_id
     * @return bool
     */
    public function getOrderRevision($order_id)
    {
        $order_revision_flat = new SYSTableFlat('order_revision');
        $where_condition = 'order_id = '.$order_id;
        return $order_revision_flat->getDataByWhere($where_condition);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return bool
     */
    public function totalOrder($start_date,$end_date)
    {
        $where_condition = " created_at >= '$start_date' AND created_at <= '$end_date'";
        $return = $this->_SYSTableFlatModel->getColumnByWhere($where_condition,'COUNT(entity_id) as total_count');
        return $return->total_count;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function totalSale($start_date,$end_date)
    {
        $where_condition = " created_at >= '$start_date' AND created_at <= '$end_date'";
        $return = $this->_SYSTableFlatModel->getColumnByWhere($where_condition,'SUM(grand_total) as total_sale');
        return $return->total_sale;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function getRides($start_date,$end_date,$status = 993)
    {
        $where_condition = " created_at >= '$start_date' AND created_at <= '$end_date' AND order_status IN ($status) AND deleted_at IS NULL ";
        $return = $this->_SYSTableFlatModel->getColumnByWhere($where_condition,'COUNT(id) AS total_rides');
        return $return->total_rides;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function totalSalesByDate($start_date,$end_date)
    {
        $return = array();
        $flat_table_model = new FlatTable();
        $data = $flat_table_model->totalSaleByDate($start_date,$end_date);
        // echo "<pre>"; print_r( $data); exit;

        if($data){
            $general_setting = new GeneralSetting();
            $return['currency'] = $general_setting->getCurrency();
            foreach($data as $row){
                $return['date'][] = $row->created_date;
                $return['total'][] = ($row->total && $row->total > 0) ? $row->total : 0;

            }

            // $return =  CustomHelper::formatAsChart($return);
        }
        return $return;
    }

    /**
     * @param int $product_type
     * @param $start_date
     * @param $end_date
     * @return bool
     */
    public function totalOrderByProductType($product_type=1,$start_date,$end_date)
    {
        $flat_table_model = new FlatTable();
        return $flat_table_model->orderCountByProductType($product_type,$start_date,$end_date);

    }

    /**
     * @return array
     */

    public function topDeliverySlots($start_date,$end_date)
    {
        $return = array();
        $flat_table_model = new FlatTable();
        $slots = false;//$flat_table_model->topDeliverySlots($start_date,$end_date);
        $attribute_option_model = new SYSAttributeOption();
        if($slots){
            foreach($slots as $slot){
                $data = new \StdClass();
                // $data = $slot;
                $data->total_order = $slot->total;
                $data->start_time = $slot->start_time;
                $data->end_time = $slot->end_time;
                $attr_option = $attribute_option_model->getByAttributeCode('day',$slot->day);
                $data->day = ($attr_option && isset($attr_option->option)) ? $attr_option->option: "";

                $data->name = $data->day." ".$data->start_time." - ".$data->end_time;
                $data->data = [$data->total_order];

                $return[] = $data;
            }
        }

        $data = new \StdClass();
        $data->name = "Monday 10:00 - 02:00";
        $data->data = [500];
        $return[] = $data;

        unset($data);
        $data = new \StdClass();
        $data->name = "Tuesday 10:00 - 02:00";
        $data->data = [250];
        $return[] = $data;

        return $return;

    }

    public function topDriverByOrder($start_date,$end_date)
    {
        $flat_table_model = new FlatTable();
        $data = $flat_table_model->getTopDriver($start_date,$end_date);
        $return = array();
        if($data){
            foreach($data as $driver){
                $return[] = array($driver->full_name,($driver->total && $driver->total > 0) ? $driver->total : 0);
            }
        }
        /* $return[] = array('Chef Zakir',25);
         $return[] = array('Chef Sana',10);
         $return[] = array('Chef Sonu',5);*/
        return $return;
    }
    
    public function topCustomerByOrder($start_date,$end_date)
    {
        $flat_table_model = new FlatTable();
        $data = $flat_table_model->getTopCustomer($start_date,$end_date);
        $return = array();
        if($data){
            foreach($data as $customer){
                $return[] = array( $customer->full_name,
                    ($customer->total && $customer->total > 0) ? $customer->total : 0
                            );
            }
        }
        /* $return[] = array('Chef Zakir',25);
         $return[] = array('Chef Sana',10);
         $return[] = array('Chef Sonu',5);*/
        return $return;
    }

    public function topCityOld($start_date,$end_date)
    {
        $return = array();
        $flat_table_model = new FlatTable();
        $top_city = $flat_table_model->getTopArea($start_date,$end_date);

        if($top_city){
            foreach($top_city as $city){
                $data = new \StdClass();
                // $data = $slot;
                // $data->total_order = $city->total_count;
                //$data->name = $city->street;
                // $data->data = [$city->total_count];
                $return['title'][] =$city->street;
                $return['total'][] = ($city->total_count && $city->total_count > 0) ? $city->total_count : 0;
                //  $return[] = $data;
            }
        }

        return $return;
    }

    public function topCity($start_date,$end_date)
    {
        $return = array();
        $flat_table_model = new FlatTable();
        $list = $flat_table_model->getTopCities($start_date,$end_date);

        if($list){
            foreach($list as $data){
                // $data = new \StdClass();
                // $data = $slot;
                // $data->total_order = $city->total_count;
                //$data->name = $city->street;
                // $data->data = [$city->total_count];
                $return['title'][] = $data->title;
                $return['total'][] = ($data->total && $data->total > 0) ? $data->total : 0;
                //  $return[] = $data;
            }
        }

        return $return;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function peakOrdersTime($start_date,$end_date)
    {
        $return = array();
        $flat_table_model = new FlatTable();
        $list = $flat_table_model->getPeakOrderTime($start_date,$end_date);

        $time_slots = CustomHelper::createTimeSlots(12,3);


        if($list){
            $days= array();
            $count = 0;
            $time_slot_rows = array();
            $time_slot_arr = array();
            foreach($list as $row){

                // print_r($row);
                if(!in_array($row->day_name,$days)){
                    $days[] = $row->day_name;
                    $time_slot_arr[$row->day_name] = array();

                    $time_data[$row->day_name] = array();
                    // $count++;
                }

                $day_time = explode(':',$row->day_time);
                $day_hour = $day_time[0];
                $total_count = ($row->total_count && $row->total_count > 0) ? $row->total_count : 0;

                $time_slot_result = CustomHelper::checkHourInTimeSlot($time_slots,$day_hour);

                if($time_slot_result){

                    $time_slot_res = explode('|',$time_slot_result);
                    $time_slot_id = $time_slot_res[0];
                    $time_slot = $time_slot_res[1];

                    $time_slot_key = str_replace(',','_',$time_slot);



                    if(isset($time_slot_arr[$row->day_name][$time_slot_key])){
                        $time_slot_arr[$row->day_name][$time_slot_key] = $time_slot_arr[$row->day_name][$time_slot_key]+$total_count;
                    }
                    else{
                        $time_slot_arr[$row->day_name][$time_slot_key] = $total_count;
                    }

                    echo "<br>";
                    echo $row->day_name.$time_slot_key;
                    if(isset($time_slot_arr[$row->day_name][$time_slot_key])){
                        echo $time_slot_arr[$row->day_name][$time_slot_key];
                        echo "<br>";
                    }

                    $total_counttt = $time_slot_arr[$row->day_name][$time_slot_key];


                    $time_data[$row->day_name][$time_slot_key]['id'] = $time_slot_id;
                    $time_data[$row->day_name][$time_slot_key]['name'] = $time_slot_key;
                    $time_data[$row->day_name][$time_slot_key]['data'] = $total_counttt;

                    unset($total_counttt);

                }



            }

            $return['day'] = $days;
            $return['time_slots'] = $time_slot_rows;
        }
        // echo "<pre>"; print_r( $time_data); exit;

        return $return;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function peakOrderTime($start_date,$end_date)
    {
        $return = array();
        $flat_table_model = new FlatTable();
        $list = $flat_table_model->getPeakOrderTime($start_date,$end_date);

        if($list){
            foreach($list as $data){
                // $data = new \StdClass();
                // $data = $slot;
                // $data->total_order = $city->total_count;
                //$data->name = $city->street;
                // $data->data = [$city->total_count];
                $return['title'][] = $data->day_name;
                $return['total'][] = ($data->total_count && $data->total_count > 0) ? $data->total_count : 0;
                //  $return[] = $data;
            }
        }

        return $return;
    }

    /**
     * @param $order_id array
     * @return array
     */
    public static function getOrderDiscussion($order_id)
    {

        $order_item_flat = new SYSTableFlat('order_discussion');
        $where_condition = 'order_id IN ('.implode(',',$order_id).')';
        $rows = $order_item_flat->getDataByWhere($where_condition);

        $response = [];
        $identifiers = [];

        if($rows) {
            foreach ($rows as $row) {

                $counter = isset($response[$row->order_id]) ? count($response[$row->order_id]) : 0;
                $identifiers[$row->target_id] = !isset($identifiers[$row->target_id]) ? $order_item_flat->getEntityIdentifier($row->target_id) : $identifiers[$row->target_id];

                $is_customer = 0;
                if ($identifiers[$row->target_id] == 'customer')
                    $is_customer = 1;

                $response[$row->order_id][$counter]['message'] = $row->order_message;
                $response[$row->order_id][$counter]['name'] = $row->target_type;
                $response[$row->order_id][$counter]['is_customer'] = $is_customer;
                $response[$row->order_id][$counter]['created_at'] = $row->created_at;
            }
        }

        return $response;
    }

    /**
     * @param $order_id array
     * @return array
     */
    public static function getOrderDeliverySlots()
    {

        $delivery_item_flat = new SYSTableFlat('delivery_slot_item');

        $columns[] = 'day';
        $columns[] = 'start_time';
        $columns[] = 'end_time';

        $join[] = 'INNER JOIN delivery_slot_flat ON delivery_slot_flat.entity_id = delivery_slot_item_flat.delivery_slot_id';

        $where_condition = '';

        $rows = $delivery_item_flat->getJoinDataByWhere($where_condition, $join, $columns);

        $response = [];
        $week_days = config('constants.WEEK_DAYS');

        foreach($rows as $row){
            if(!empty($row->day)) {
                $counter = isset($response[$week_days[$row->day]]) ? count($response[$week_days[$row->day]]) : 0;

                $response[$week_days[$row->day]][$counter]['week_day'] = $row->day;
                $response[$week_days[$row->day]][$counter]['start_time'] = $row->start_time;
                $response[$week_days[$row->day]][$counter]['end_time'] = $row->end_time;
                $response[$week_days[$row->day]][$counter]['time_slot'] = $row->start_time . ' - ' . $row->end_time;
            }
        }

        return $response;
    }

    /**
     * @param $pickup_date
     * @param $pickup_time
     * @param bool $grace_time
     * @return false|string
     */
    public function estDeliveryData($pickup_date,$pickup_time, $grace_time = FALSE)
    {
        $delivery_date = $pickup_date.' '.$pickup_time;

        if(!$grace_time){
            $setting_lib = new GeneralSetting();
            $grace_time = $setting_lib->getColumn('delivery_grace_minutes');
        }

        return date("Y-m-d H:i:s", strtotime("+$grace_time minutes", strtotime($delivery_date)));
    }

    /**
     * Filter order array by dates
     * @param $orders
     * @return array
     */
    public function filterByPickupDate($orders)
    {
        $return = array();
        if($orders){

            foreach($orders as $order) {

                $return[$order->pickup_date][] = $order;
            }
        }
        return $return;
       // echo "<pre>"; print_r($return); exit;
    }

    /**
     * Get Driver orders
     * @param $driver_id
     * @return array|bool
     */
    public function getClientOrders($identifier,$driver_id,$filter = false)
    {
        $sys_flat = new FlatTable();
           $order_raws = $sys_flat->getCompletedOrders($identifier,$driver_id,$filter);
           if($order_raws){
               foreach($order_raws as $order_ids){
                   $return[] = $order_ids->entity_id;
               }
               return $return;
           }

           return false;
    }



    /**
     * Get reviews by order
     * @param $order_ids
     * @return array
     */
    public function getRating($order_ids,$limit = false)
    {
        $reviews = array();
        if(count($order_ids) > 0){
            //   echo "<pre>"; print_r($order_ids); exit;
            $rating_model = new ExtPackageRate();
            $reviews =  $rating_model->getTargetReviews(1,$order_ids,$limit);
        }

        return $reviews;
    }

    /**
     * Search customer Orders
     * @param $request
     * @return array|mixed
     */
    public function searchOrder($request)
    {
        $request_params = $request;
        $request = is_array($request) ? (object)$request : $request;
        $where_condition = '';

        $params = [];
        $params['entity_type_id'] = 'order';
        //$params['mobile_json'] = 1;

        if(isset($request->driver_id) && !empty($request->driver_id)){
            $params['driver_id'] = $request->driver_id;
        }

        if(isset($request->customer_id) && !empty($request->customer_id)){
            $params['customer_id'] = $request->customer_id;
        }

        if(isset($request->order_number) && !empty($request->order_number)){
            //$params['order_number'] = $request->order_number;
            unset($request_params['order_number']);
            $where_condition .= " AND order_number LIKE '%$request->order_number%'";
        }

        if(isset($request->order_status) && !empty($request->order_status)){

            //if order status is confirmed then get the order status ids where display title is confirmed
            if(trim($request->order_status) == 'confirmed' && isset($request->customer_id)){

                unset($request_params['order_status']);

                $flat_table = new SYSTableFlat('order_statuses');
                $raws = $flat_table->getDataByWhere(' display_key = "confirmed"',array('entity_id'));
                $status_ids  = EntityHelper::extractEntityID($raws);
                if($status_ids)
                     $where_condition .= ' AND (order_status IN ('.implode(',',$status_ids).'))';
            }
            elseif(trim($request->order_status) == 'cancelled' && isset($request->customer_id)){

                unset($request_params['order_status']);

                $flat_table = new SYSTableFlat('order_statuses');
                $raws = $flat_table->getDataByWhere(' display_key = "cancelled"',array('entity_id'));
                $status_ids  = EntityHelper::extractEntityID($raws);
                if($status_ids)
                    $where_condition .= ' AND (order_status IN ('.implode(',',$status_ids).'))';
            }
            else{
                $params['order_status'] = $request->order_status;
            }
        }

        $end_date = date('Y-m-d');
        if(isset($request->end_date) && !empty($request->end_date)){
            $end_date = $request->end_date;
        }

        if(isset($request->start_date) && !empty($request->start_date) ){
            $where_condition .= " AND (Date(pickup_date) BETWEEN '$request->start_date' AND '$end_date')";
        }

        if((isset($request->start_amount) && !empty($request->start_amount)) &&
            (isset($request->end_amount) && !empty($request->end_amount))){
            $where_condition .= " AND (( CAST(`pre_grand_total` AS UNSIGNED) BETWEEN '$request->start_amount' AND '$request->end_amount')";
            $where_condition .= " OR ( CAST(`grand_total` AS UNSIGNED) BETWEEN '$request->start_amount' AND '$request->end_amount'))";
        }
        else if((isset($request->start_amount) && !empty($request->start_amount)) &&
            (!isset($request->end_amount) ||
                (isset($request->end_amount) && empty($request->end_amount))
            )){

            $where_condition .= " AND (( CAST(`pre_grand_total` AS UNSIGNED) >= '$request->start_amount')";
            $where_condition .= " OR (CAST(`grand_total` AS UNSIGNED) >= '$request->start_amount'))";
        }

        if(!empty($where_condition)){
            $params['where_condition'] = $where_condition;
        }

        $params = array_merge($params,$request_params);

       // echo "<pre>"; print_r($params);
        $entity_lib = new Entity();
        $response = $entity_lib->apiList($params);
        $response = json_decode(json_encode($response));
        return $response;
       // echo "<pre>"; print_r($response); exit;
    }

    /**
     * Get Driver Stats
     * @param $request
     * @return \stdClass
     */
    public function getDriverOrderStats($request,$weekly = true)
    {
        $request = is_array($request) ? (object)$request : $request;

        $where_condition = '';
        $return = new \stdClass();
        $identifier = 'driver';
        $filter = false;

        if(isset($request->start_date) && !empty($request->start_date)){
            $start_date = $request->start_date;
        }

        $end_date = date('Y-m-d');
        if(isset($request->end_date) && !empty($request->end_date)){
            $end_date = $request->end_date;
        }

        if((isset($request->start_date) && !empty($request->start_date)) &&
            (isset($request->end_date) && !empty($request->end_date))) {
            $where_condition .= " (Date(pickup_date) BETWEEN '$start_date' AND '$end_date')";
        }

        $amount_condition = '';

        if((isset($request->start_amount) && !empty($request->start_amount)) &&
            (isset($request->end_amount) && !empty($request->end_amount))){
            $amount_condition .= " AND (( CAST(`pre_grand_total` AS UNSIGNED) BETWEEN '$request->start_amount' AND '$request->end_amount')";
            $amount_condition .= " OR ( CAST(`grand_total` AS UNSIGNED) BETWEEN '$request->start_amount' AND '$request->end_amount'))";
        }
        else if((isset($request->start_amount) && !empty($request->start_amount)) &&
            (!isset($request->end_amount) ||
                (isset($request->end_amount) && empty($request->end_amount))
            )){

            $amount_condition .= " AND (( CAST(`pre_grand_total` AS UNSIGNED) >= '$request->start_amount')";
            $amount_condition .= " OR (CAST(`grand_total` AS UNSIGNED) >= '$request->start_amount'))";
        }

        $where_condition = $where_condition.$amount_condition;

            $order_status_lib = new OrderStatus();
            $order_statuses = $order_status_lib->getIdByKeywords();

        $return->monthly = $this->monthlyStats($identifier,$request->entity_id,$order_statuses,$where_condition);
        if($weekly){
            $return->weekly = $this->weeklyStats($identifier,$request->entity_id,$order_statuses);
        }

        return $return;
    }

    /**
     * @param $request
     * @return \stdClass
     */
    public function getDriverPreviousStats($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        $where_condition = '';
        $return = new \stdClass();
        $identifier = 'driver';
        $filter = false;

        $start_date = isset($request->start_date) ? $request->start_date : "";
        $end_date = date('Y-m-d');
        if(isset($request->end_date) && !empty($request->end_date)){
            $end_date = $request->end_date;
        }

        if(!empty($start_date) && !empty($end_date)) {

            //Get Difference of 2 dates
            $date1 = new \DateTime($start_date);
            $date2 = new \DateTime($end_date);
            $interval = $date1->diff($date2);
            $diff = $interval->d + 1;

            $start_date_obj = Carbon::parse($start_date);
            $start_date = $start_date_obj->subDays($diff)->format('Y-m-d');

            $end_date_obj = Carbon::parse($end_date);
            $end_date = $end_date_obj->subDays($diff)->format('Y-m-d');

            $where_condition .= " (Date(pickup_date) BETWEEN '$start_date' AND '$end_date')";

            $amount_condition = '';

            if ((isset($request->start_amount) && !empty($request->start_amount)) &&
                (isset($request->end_amount) && !empty($request->end_amount))) {
                $amount_condition .= " AND (( CAST(`pre_grand_total` AS UNSIGNED) BETWEEN '$request->start_amount' AND '$request->end_amount')";
                $amount_condition .= " OR ( CAST(`grand_total` AS UNSIGNED) BETWEEN '$request->start_amount' AND '$request->end_amount'))";
            } else if ((isset($request->start_amount) && !empty($request->start_amount)) &&
                (!isset($request->end_amount) ||
                    (isset($request->end_amount) && empty($request->end_amount))
                )) {

                $amount_condition .= " AND (( CAST(`pre_grand_total` AS UNSIGNED) >= '$request->start_amount')";
                $amount_condition .= " OR (CAST(`grand_total` AS UNSIGNED) >= '$request->start_amount'))";
            }

            $where_condition = $where_condition . $amount_condition;

            $order_status_lib = new OrderStatus();
            $order_statuses = $order_status_lib->getIdByKeywords();

             $where_condition;

            $return->monthly = $this->monthlyStats($identifier, $request->entity_id, $order_statuses, $where_condition);
        }

        return $return;

    }

    /**
     * @param $request
     * @return \stdClass
     */
    public function getCustomerOrderStats($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        $where_condition = '';
        $return = new \stdClass();
        $identifier = 'customer';
        $filter = false;

        $end_date = date('Y-m-d');
        if(isset($request->end_date) && !empty($request->end_date)){
            $end_date = $request->end_date;
        }

        if((isset($request->start_date) && !empty($request->start_date)) &&
            (isset($request->end_date) && !empty($request->end_date))) {
            $where_condition .= " (Date(pickup_date) BETWEEN '$request->start_date' AND '$end_date')";
        }

        $amount_condition = '';

        if((isset($request->start_amount) && !empty($request->start_amount)) &&
            (isset($request->end_amount) && !empty($request->end_amount))){
            $amount_condition .= " AND (( CAST(`pre_grand_total` AS UNSIGNED) BETWEEN '$request->start_amount' AND '$request->end_amount')";
            $amount_condition .= " OR ( CAST(`grand_total` AS UNSIGNED) BETWEEN '$request->start_amount' AND '$request->end_amount'))";
        }
        else if((isset($request->start_amount) && !empty($request->start_amount)) &&
            (!isset($request->end_amount) ||
                (isset($request->end_amount) && empty($request->end_amount))
            )){

            $amount_condition .= " AND (( CAST(`pre_grand_total` AS UNSIGNED) >= '$request->start_amount')";
            $amount_condition .= " OR (CAST(`grand_total` AS UNSIGNED) >= '$request->start_amount'))";
        }

        $where_condition = $where_condition.$amount_condition;

        $order_status_lib = new OrderStatus();
        $order_statuses = $order_status_lib->getIdByKeywords();

        $return->monthly = $this->monthlyStats($identifier,$request->entity_id,$order_statuses,$where_condition);
        $return->weekly = $this->weeklyStats($identifier,$request->entity_id,$order_statuses);
        return $return;
    }

    /**
     * @param $identifier
     * @param $entity_id
     * @param $order_statuses
     * @param string $where_condition
     * @param bool $filter
     * @return array
     */
    public function monthlyStats($identifier,$entity_id,$order_statuses,$where_condition = '')
    {
        $monthly = array();
        //Get total Orders
        $monthly['total_order'] = $this->getTotalOrders($identifier,$entity_id,$where_condition);
        //Get Cancel Orders
        $monthly['cancelled_order'] =  $this->getCancelledOrders($identifier,$entity_id,$where_condition,$order_statuses['driver_cancelled']);

        //Get Completed Orders
        $monthly['completed_order'] =  $this->getCompletedOrders($identifier,$entity_id,$where_condition,$order_statuses['completed']);

        //Get total Earned
        $monthly['total_earned'] =  $this->getTotalEarned($identifier,$entity_id,$where_condition,$order_statuses['completed']);

        //Get rating
        $monthly['rating'] = $this->getClientRating($identifier,$entity_id,$where_condition);

        if($identifier == 'driver'){
            $monthly['decline_order'] = $this->getDeclineOrders($identifier,$entity_id,$where_condition);
        }
        return $monthly;
    }

    /**
     * @param $identifier
     * @param $entity_id
     * @param $order_statuses
     * @return array
     */
    public function weeklyStats($identifier,$entity_id,$order_statuses,$where_condition = '',$previous = false)
    {
      $weekly = array();
        $weekly['monday'] = $this->dailyStats($identifier,$entity_id,$order_statuses,'monday',$where_condition,$previous);
        $weekly['tuesday'] = $this->dailyStats($identifier,$entity_id,$order_statuses,'tuesday',$where_condition,$previous);
        $weekly['wednesday'] = $this->dailyStats($identifier,$entity_id,$order_statuses,'wednesday',$where_condition,$previous);
        $weekly['thursday'] = $this->dailyStats($identifier,$entity_id,$order_statuses,'thursday',$where_condition,$previous);
        $weekly['friday'] = $this->dailyStats($identifier,$entity_id,$order_statuses,'friday',$where_condition,$previous);
        $weekly['saturday'] = $this->dailyStats($identifier,$entity_id,$order_statuses,'saturday',$where_condition,$previous);
        $weekly['sunday'] = $this->dailyStats($identifier,$entity_id,$order_statuses,'sunday',$where_condition,$previous);
        return $weekly;
    }

    /**
     * @param $identifier
     * @param $entity_id
     * @param $order_statuses
     * @param string $day_name
     * @param string $where
     * @return mixed
     */
    public function dailyStats($identifier,$entity_id,$order_statuses,$day_name = 'monday',$where = '',$previous = false)
    {
         $start_date = Carbon::parse("$day_name last week")->format('Y-m-d');
         $end_date = Carbon::parse("sunday last week")->format('Y-m-d');

         if($previous){
             $start_date_obj = Carbon::parse($start_date);
             $start_date =  $start_date_obj->subDays(7)->format('Y-m-d');

             $end_date_obj = Carbon::parse($end_date);
              $end_date =  $end_date_obj->subDays(7)->format('Y-m-d');
         }


       // $where_condition = " (Date(pickup_date) BETWEEN '$start_date' AND '$end_date')";
        $where_condition = " pickup_date = '$start_date'";

        if(!empty($where)){
            $where_condition = $where_condition.$where;
        }

      //  echo $where_condition;
        $return['total_order'] = $this->getTotalOrders($identifier,$entity_id,$where_condition);
        $return['cancelled_order'] =  $this->getCancelledOrders($identifier,$entity_id,$where_condition,$order_statuses['driver_cancelled']);
        $return['completed_order'] =  $this->getCompletedOrders($identifier,$entity_id,$where_condition,$order_statuses['completed']);
        $return['total_earned'] =  $this->getTotalEarned($identifier,$entity_id,$where_condition,$order_statuses['completed']);
        $return['rating'] = $this->getClientRating($identifier,$entity_id,$where_condition);
        if($identifier == 'driver'){
            $return['decline_order'] = $this->getDeclineOrders($identifier,$entity_id,$where_condition);
        }

      //  echo "<pre>"; print_r($return); exit;
        return $return;

    }

    /**
     * Get Cancelled Orders
     * @param $entity_type_identifier
     * @param $entity_id
     * @param string $where_condition
     * @param bool $order_status
     * @return int
     */
    public function getCancelledOrders($entity_type_identifier,$entity_id,$where_condition = '',$order_status = false)
    {
        $where_condition = $this->_entityWhereCondition($entity_type_identifier,$entity_id,$where_condition);

        if($entity_type_identifier == 'driver'){
            if(!$order_status){
                $order_status_lib = new OrderStatus();
                $order_status = $order_statuses = $order_status_lib->getIdByKeywords('driver_cancelled');
            }
        }
        else{
            if(!$order_status){
                $order_status_lib = new OrderStatus();
                $order_statuses = $order_statuses = $order_status_lib->getIdByKeywords();
                $order_status = $order_statuses['driver_cancelled'].','.$order_statuses['cancelled'];
            }
        }

        $where_condition .= " AND order_status IN (".$order_status.")";

        $cancel_order_raw = $this->_SYSTableFlatModel->getColumnByWhere($where_condition,'COUNT(entity_id) AS total_order');
       return isset($cancel_order_raw) ? $cancel_order_raw->total_order : 0;

    }

    /**
     * Get Total Orders
     * @param $entity_type_identifier
     * @param $entity_id
     * @param string $where_condition
     * @return int
     */
    public function getTotalOrders($entity_type_identifier,$entity_id,$where_condition = '')
    {
        $where_condition = $this->_entityWhereCondition($entity_type_identifier,$entity_id,$where_condition);
            //echo $where_condition;
        $total_order_raw = $this->_SYSTableFlatModel->getColumnByWhere($where_condition,'COUNT(entity_id) AS total_order');
        return isset($total_order_raw) ? $total_order_raw->total_order : 0;

    }

    /**
     * Get Completed Orders
     * @param $entity_type_identifier
     * @param $entity_id
     * @param string $where_condition
     * @param bool $order_status
     * @return int
     */
    public function getCompletedOrders($entity_type_identifier,$entity_id,$where_condition = '',$order_status = false)
    {
        $where_condition = $this->_entityWhereCondition($entity_type_identifier,$entity_id,$where_condition);

        if(!$order_status){
            $order_status_lib = new OrderStatus();
            $order_status = $order_statuses = $order_status_lib->getIdByKeywords('completed');
        }
        $where_condition .= " AND order_status = ".$order_status;

        $order_raw = $this->_SYSTableFlatModel->getColumnByWhere($where_condition,'COUNT(entity_id) AS total_order');
        return isset($order_raw->total_order) ? $order_raw->total_order : 0;

    }

    /**
     * @param $entity_type_identifier
     * @param $entity_id
     * @param string $where_condition
     * @param bool $order_status
     * @return int
     */
    public function getTotalEarned($entity_type_identifier,$entity_id,$where_condition = '',$order_status = false)
    {
        $where_condition = $this->_entityWhereCondition($entity_type_identifier,$entity_id,$where_condition);

        if(!$order_status){
            $order_status_lib = new OrderStatus();
            $order_status = $order_statuses = $order_status_lib->getIdByKeywords('completed');
        }
        $where_condition .= " AND order_status = ".$order_status;

        $order_raw = $this->_SYSTableFlatModel->getColumnByWhere($where_condition,'SUM(grand_total) AS total');
        return isset($order_raw->total) ? $order_raw->total : 0;

    }

    /**
     * @param string $user
     * @param $entity_id
     * @return string
     */

    private function _entityWhereCondition($user = 'driver',$entity_id,$where_condition = '')
    {
        if(!empty($where_condition)){
            $where_condition .= " AND";
        }
        if($user == 'driver') {
            $where_condition .= ' driver_id = ' . $entity_id;
        }
        else{
            $where_condition .= ' customer_id = '.$entity_id;
        }
        return $where_condition;
    }

    /**
     * @param $driver_id
     * @param bool $filter
     * @return float|string
     */
    public function getClientRating($identifier,$driver_id,$filter = false)
    {
        $average_rating = 0;
        $order_ids = $this->getClientOrders($identifier,$driver_id,$filter);
        $ratings = $this->getRating($order_ids,false);

        if($ratings){
            $total_raters = count($ratings);
            if(count($ratings) > 0){
                $sum_rating = 0;
                foreach($ratings as $rating){
                    $sum_rating += $rating->rating;
                }

                $average_rating =  floatval(number_format( ($sum_rating /$total_raters), 2, '.', ''));
            }
        }

        return $average_rating;
    }

    /**
     *  This function is used for get pending order
     * @param $request
     * @return mixed
     */
    public function getPendingOrder($request)
    {
        $entity_id = $request->input('entity_id');
        $query = \DB::table('order_flat')
                    ->where('customer_id',$entity_id)
                    ->where('order_status',993)
                    ->whereNull('deleted_at')
                    ->count();
        return $query;
    }

    /**
     * Get Driver weekly stats
     * @param $request
     * @return \StdClass
     */
    public function getDriverWeeklyStats($request,$previous = false)
    {
        $request = is_array($request) ? (object)$request : $request;
        $return = new \StdClass();
        $where_condition = '';
        $identifier = 'driver';

        $order_status_lib = new OrderStatus();
        $order_statuses = $order_status_lib->getIdByKeywords();

       $return->current =  $this->weeklyStats($identifier,$request->entity_id,$order_statuses,$where_condition);
       if($previous)
        $return->previous =  $this->weeklyStats($identifier,$request->entity_id,$order_statuses,$where_condition,true);

        return $return;
    }

    /**
     * @param $request
     * @return array
     */
    public function getOrderGraphStats($request)
    {
        $request = is_array($request) ? (object)$request : $request;
        $where_condition = " AND o.driver_id = ".$request->driver_id;

        if(isset($request->start_date) && !empty($request->start_date)){
            $start_date = $request->start_date;
        }

        $end_date = date('Y-m-d');
        if(isset($request->end_date) && !empty($request->end_date)){
            $end_date = $request->end_date;
        }

        if((isset($request->start_date) && !empty($request->start_date)) &&
            (isset($request->end_date) && !empty($request->end_date))) {
            $where_condition .= " AND (Date(o.pickup_date) BETWEEN '$start_date' AND '$end_date')";
        }

        $amount_condition = '';

        if((isset($request->start_amount) && !empty($request->start_amount)) &&
            (isset($request->end_amount) && !empty($request->end_amount))){
            $amount_condition .= " AND (( CAST(o.`pre_grand_total` AS UNSIGNED) BETWEEN '$request->start_amount' AND '$request->end_amount')";
            $amount_condition .= " OR ( CAST(o.`grand_total` AS UNSIGNED) BETWEEN '$request->start_amount' AND '$request->end_amount'))";
        }
        else if((isset($request->start_amount) && !empty($request->start_amount)) &&
            (!isset($request->end_amount) ||
                (isset($request->end_amount) && empty($request->end_amount))
            )){

            $amount_condition .= " AND (( CAST(o.`pre_grand_total` AS UNSIGNED) >= '$request->start_amount')";
            $amount_condition .= " OR (CAST(o.`grand_total` AS UNSIGNED) >= '$request->start_amount'))";
        }

        $where_condition = $where_condition.$amount_condition;

       // echo $where_condition; exit;
        $return = array();
        $flat_model = new FlatTable();

        if(!empty($start_date) && !empty($end_date)) {

            //Get Difference of 2 dates
            $date1 = new \DateTime($start_date);
            $date2 = new \DateTime($end_date);
            $interval = $date1->diff($date2);
            $diff = $interval->d + 1;

            if ($diff > 60) {
                  $return['filter_by'] = 'month';
                $list = $flat_model->getOrderMonthlyGraph($where_condition);
            } else {
                 $return['filter_by'] = 'week';
                $list = $flat_model->getOrderWeeklyGraph($where_condition);
            }

            if ($list) {
                foreach ($list as $data) {
                    $return['title'][] = $data->title;
                    $return['total'][] = ($data->total && $data->total > 0) ? $data->total : 0;
                    //  $return[] = $data;
                }
            }
        }
        return  $return;
    }

    /**
     * @param $entity_type_identifier
     * @param $entity_id
     * @param string $where_condition
     * @return int
     */
    public function getDeclineOrders($entity_type_identifier,$entity_id,$where_condition = '')
    {
        $total = 0;
        $where_condition = $this->_entityWhereCondition($entity_type_identifier,$entity_id,$where_condition);
        //echo $where_condition;
        $orders = $this->_SYSTableFlatModel->getDataByWhere($where_condition,array('entity_id'));


       if($orders){

           if(count($orders) > 0){
              //

               foreach($orders as $order){
                   $ids[] = $order->entity_id;
               }
              // echo "<pre>"; print_r($ids); exit;
               $iids = implode(',',$ids);

               $flat_model = new OrderFlat();
               return $flat_model->getDeclineOrder($entity_id,$iids);
           }

       }
       return $total;

    }

    public static function checkOrderTimSlot($date,$start_time,$end_time,$orders)
    {
        $order_info = '';
        if($orders){

            if(isset($orders[$date])){
                foreach($orders[$date] as $order){

                    if(strtotime($order->pickup_date) == strtotime($date)){

                        if(strtotime($start_time) >= strtotime($order->start_time) && strtotime($start_time) <= strtotime($order->end_time))
                            $order_info += 1;


                    }
                }
            }

        }

        return $order_info;
    }



}
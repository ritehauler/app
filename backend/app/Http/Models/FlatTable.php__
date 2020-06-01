<?php
/**
 * Flat Table Queries
 */

namespace App\Http\Models;

use App\Libraries\OrderStatus;
use App\Libraries\System\Entity;
use Illuminate\Database\Eloquent\Model;

Class FlatTable extends Base
{
    /**
     * @param $entity_type_identifier
     * @param string $where_condition
     * @return bool
     */
    public function totalAuthCount($entity_type_identifier,$where_condition = "")
    {
        $table = $entity_type_identifier."_flat";
        $row = \DB::select("SELECT COUNT(f.entity_id) AS total_count
            FROM $table f
            LEFT JOIN sys_entity e ON e.entity_id = f.entity_id
            LEFT JOIN sys_entity_auth a ON a.entity_auth_id = e.entity_auth_id
            WHERE f.deleted_at IS NULL
            AND f.user_status = 1
            AND a.status = 1
            AND a.is_verified = 1 $where_condition");

        if($row)
            return isset($row[0]->total_count) ? $row[0]->total_count : false;

        return false;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return bool|string
     */
    public function totalSaleByDate($start_date,$end_date)
    {
        $row = \DB::select("SELECT DATE_FORMAT(o.created_at,'%M-%D') AS created_date,SUM(o.grand_total) AS total
	    	FROM
	    	order_flat o
	    	LEFT JOIN order_statuses_flat os ON o.order_status = os.entity_id
	    	WHERE o.deleted_at IS NULL
	    	AND o.created_at >= '$start_date' AND o.created_at <= '$end_date'
	    	AND os.keyword NOT IN ('cancelled','driver_cancelled')
	    	GROUP BY DATE_FORMAT(o.created_at, '%M-%D')
	    	ORDER BY o.created_at ASC");
        if($row)
            return isset($row[0]) ? $row : false;

        return false;
    }

    /**
     * @param $product_type
     * @param int $limit
     * @return bool
     */
    public function getTopProducts($product_type,$start_date,$end_date,$limit=10)
    {
        $row = \DB::select("SELECT p.title,SUM(oi.quantity) as total
            FROM order_item_flat oi
            LEFT JOIN product_flat p ON oi.product_id = p.entity_id
            WHERE oi.deleted_at IS NULL AND p.product_type = $product_type
             AND oi.created_at >= '$start_date'
                 AND oi.created_at <= '$end_date'
            GROUP BY oi.product_id
            ORDER BY total DESC LIMIT 0,$limit");

        if($row)
            return isset($row[0]) ? $row : false;

        return false;

    }

    /**
     * @param $product_type
     * @param $start_date
     * @param $end_date
     * @return bool
     */
    public function orderCountByProductType($product_type,$start_date,$end_date)
    {
        $row = \DB::select("SELECT SUM(oi.quantity) AS total_count
                  FROM order_item_flat oi
                LEFT JOIN product_flat p ON oi.product_id = p.entity_id
                WHERE p.product_type = $product_type
                AND oi.created_at >= '$start_date'
                 AND oi.created_at <= '$end_date'");

        if($row)
            return isset($row[0]->total_count) ? $row[0]->total_count : false;

        return false;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param int $limit
     * @return bool
     */
    public function activeCoupons($start_date,$end_date,$limit = 5)
    {
        $row = \DB::select("SELECT campaign_name,coupon_expiry FROM coupon_flat
                        WHERE deleted_at IS NULL
                        AND start_date >= '$start_date' AND coupon_expiry <= '$end_date'
                        ORDER BY entity_id DESC LIMIT 0, $limit");

        if($row)
            return isset($row[0]) ? $row : false;

        return false;

    }

    /**
     * @param $start_date
     * @param $end_date
     * @return bool
     */
    public function topDeliverySlots($start_date,$end_date)
    {
        $row = \DB::select("SELECT o.delivery_slot_id,
            COUNT(o.entity_id) AS total,
            dsi.start_time,
            dsi.end_time,
            ds.day
        FROM order_flat o
        LEFT JOIN delivery_slot_flat_item dsi ON dsi.entity_id = o.delivery_slot_id
        LEFT JOIN delivery_slot_flat ds ON ds.entity_id = dsi.delivery_slot_id
        WHERE o.deleted_at IS NULL
         AND o.created_at >= '$start_date'
         AND o.created_at <= '$end_date'
        GROUP BY o.delivery_slot_id
        ORDER BY total DESC
        LIMIT 0,5");

        if($row)
            return isset($row[0]) ? $row : false;

        return false;
    }

    /**
     * @param int $limit
     * @return bool
     */
    public function getTopDriver($start_date,$end_date,$limit=5)
    {
        $row = \DB::select("SELECT 
                                COUNT(`of`.driver_id) AS total, df.full_name
                            FROM
                                order_flat `of`
                                    INNER JOIN
                                driver_flat df ON df.entity_id = `of`.driver_id
                            WHERE `of`.deleted_at IS NULL
                              AND `of`.created_at >= '$start_date'
                              AND `of`.created_at <= '$end_date'
                            GROUP BY `of`.driver_id
                            ORDER BY total DESC LIMIT $limit");

        if($row)
            return isset($row[0]) ? $row : false;

        return false;
    }

    /**
     * @param int $limit
     * @return bool
     */
    public function getTopCustomer($start_date,$end_date,$limit=5)
    {
        $row = \DB::select("SELECT 
                                COUNT(`of`.customer_id) AS total, cf.full_name
                            FROM
                                order_flat `of`
                                    INNER JOIN
                                customer_flat cf ON cf.entity_id = `of`.customer_id
                            WHERE `of`.deleted_at IS NULL
                              AND `of`.created_at >= '$start_date'
                              AND `of`.created_at <= '$end_date'
                            GROUP BY `of`.customer_id
                            ORDER BY total DESC LIMIT $limit");
        if($row)
            return isset($row[0]) ? $row : false;

        return false;
    }


    /**
     * @param $start_date
     * @param $end_date
     * @param int $limit
     * @return bool
     */
    public function topCustomers($start_date,$end_date,$limit =10)
    {
        $row = \DB::select("SELECT c.first_name,c.last_name, COUNT(o.entity_id) AS total_count
                            FROM order_flat o
                            LEFT JOIN customer_flat c ON o.customer_id = c.entity_id
                            WHERE o.deleted_at IS NULL
                            AND o.created_at >= '$start_date'
                            AND o.created_at <= '$end_date'
                            GROUP BY o.customer_id
                            ORDER BY total_count DESC
                            LIMIT 0,$limit");

        if($row)
            return isset($row[0]) ? $row : false;

        return false;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param int $limit
     * @return bool
     */
    public function topDrivers($start_date,$end_date,$limit =10)
    {
        $row = \DB::select("SELECT d.first_name,d.last_name, 
                            COUNT(o.entity_id) AS total_count,
                            d.login_status  
                            FROM order_flat o
                            LEFT JOIN driver_flat d ON o.driver_id = d.entity_id
                            WHERE o.deleted_at IS NULL 
                            AND o.driver_id > 0
                            AND o.created_at >= '$start_date'
                            AND o.created_at <= '$end_date'
                            GROUP BY o.driver_id
                            ORDER BY total_count DESC
                            LIMIT 0,$limit");

        if($row)
            return isset($row[0]) ? $row : false;

        return false;
    }

    /**
     *
     */
    public function topVehicles($start_date,$end_date,$limit = 3)
    {
        $row = \DB::select("SELECT 
                                COUNT(otf.truck_id) AS total,
                                tf.title
                            FROM
                                order_flat `of`
                                    INNER JOIN
                                order_trucks_flat otf ON otf.order_id = `of`.entity_id
                                    INNER JOIN
                                truck_flat tf ON tf.entity_id = otf.truck_id
                            WHERE `of`.deleted_at IS NULL
                                GROUP BY otf.truck_id
                            ORDER BY total DESC
                            LIMIT 3");

        if($row)
            return isset($row[0]) ? $row : false;

        return false;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param int $limit
     * @return bool
     */
    public function activePromotions($start_date,$end_date,$limit = 5)
    {
        $row = \DB::select("SELECT title,end_date
                        FROM promotion_discount_flat
                        WHERE deleted_at IS NULL
                        AND start_date >= '$start_date' AND end_date <= '$end_date'
                        ORDER BY entity_id DESC LIMIT 0,$limit");

        if($row)
            return isset($row[0]) ? $row : false;

        return false;

    }


    /**
     * Get Product where promotion has to apply
     * @param $date
     * @return bool
     */
    public function getPromotionProducts($date)
    {
        $row = \DB::select("SELECT p.entity_id AS promotion_id, p.title AS promotion_name,p.start_date,p.end_date,
         pi.promotion_product_id,pi.coupon_type,pi.discount,prod.price
         FROM promotion_item_flat `pi`
        LEFT JOIN promotion_discount_flat p ON pi.promotion_discount_id = p.entity_id
         LEFT JOIN product_flat prod ON prod.entity_id = pi.promotion_product_id
        WHERE p.`deleted_at` IS NULL AND  p.start_date <= '$date' AND  p.end_date >= '$date'");

        return isset($row[0]) ? $row : false;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param int $limit
     * @return bool
     */
    public function getTopArea($start_date,$end_date,$limit = 10)
    {
        $row = \DB::select("SELECT s.street,s.latitude,s.longitude, COUNT(o.entity_id) AS total_count
                FROM order_flat o
                LEFT JOIN shipping_address_flat s ON s.entity_id = o.shipping_address
                WHERE o.deleted_at IS NULL
                  AND o.created_at >= '$start_date'
                  AND o.created_at <= '$end_date'
                GROUP BY o.shipping_address
                ORDER BY total_count DESC
                LIMIT 0,$limit");

        return isset($row[0]) ? $row : false;
    }

    public function getTopCities($start_date,$end_date,$limit = 10)
    {
        $row = \DB::select("SELECT 
                                COUNT(opf.city) AS total, cf.title
                            FROM
                                order_flat `of`
                                    INNER JOIN
                                order_pickup_flat opf ON opf.order_id = `of`.entity_id
                                    INNER JOIN
                                city_flat cf ON cf.entity_id = opf.city
                            WHERE `of`.deleted_at IS NULL
                              AND `of`.created_at >= '$start_date'
                              AND `of`.created_at <= '$end_date'    
                            GROUP BY opf.city
                            ORDER BY total DESC LIMIT $limit ");

        return isset($row[0]) ? $row : false;
    }

    public function getPeakOrdersTime($start_date,$end_date)
    {
        $row = \DB::select("SELECT DAYNAME(o.created_at) AS day_name,
            TIME_FORMAT(o.created_at,'%H:00') AS day_time,
            COUNT(o.entity_id) AS total_count
            FROM sys_entity o
            WHERE o.deleted_at IS NULL
                  AND o.created_at >= '$start_date'
                  AND o.created_at <= '$end_date'
            GROUP BY DAYNAME(o.created_at),HOUR(o.created_at)
            ORDER BY total_count,day_name DESC");

        return isset($row[0]) ? $row : false;
    }

    public function getPeakOrderTime($start_date,$end_date)
    {
        /* "SELECT DAYNAME(o.created_at) AS day_name,TIME_FORMAT(o.created_at,'%H:00') AS day_time,COUNT(o.entity_id) AS total_count
             FROM order_flat o
             GROUP BY DAYNAME(o.created_at),HOUR(o.created_at)
             ORDER BY total_count DESC";*/
        $row = \DB::select("SELECT DAYNAME(o.pickup_date) AS day_name,COUNT(o.entity_id) AS total_count
            FROM order_flat o
             WHERE o.deleted_at IS NULL
                  AND o.created_at >= '$start_date'
                  AND o.created_at <= '$end_date'
            GROUP BY DAYNAME(o.pickup_date)
            ORDER BY total_count DESC");

        return isset($row[0]) ? $row : false;
    }

    /**
     * Get Max price
     * @param bool $where_column
     * @param bool $where_value
     * @return bool
     */
    public function getMaxPrice($where_column = false,$where_value = false)
    {
        $table_name = 'product_flat';
        if($where_column && $where_value) {
            $row = \DB::select("SELECT MAX(CAST(price AS DECIMAL(10,2))) AS max_price FROM $table_name WHERE `deleted_at` IS NULL AND $where_column = '$where_value'");
        }
        else{
            $row = \DB::select("SELECT MAX(CAST(price AS DECIMAL(10,2))) AS max_price FROM $table_name WHERE `deleted_at` IS NULL");
        }

        return isset($row[0]->max_price) ? $row[0]->max_price : false;
    }

    /**
     * @param $order_id
     * @return bool
     */
    public function getCustomerColumnsByOrder($order_id)
    {
        $row = \DB::select(" SELECT o.customer_id,c.is_notify FROM order_flat o
                 LEFT JOIN customer_flat c ON o.customer_id = c.entity_id
                 WHERE o.entity_id = $order_id");

        return isset($row[0]) ? $row[0] : false;
    }

    /**
     * @param $state_id
     * @return bool
     */
    public function getCityByState($state_id)
    {
        $row = \DB::select(" SELECT city_id,name,state_id FROM city
                 WHERE state_id =  $state_id");

        return isset($row[0]) ? $row : false;
    }

    public function getCityByID($city_id)
    {
        $row = \DB::select(" SELECT `name` FROM city
                 WHERE city_id =  $city_id");
      //  echo "<pre>"; print_r($row); exit;
        return isset($row[0]->name) ? $row[0]->name : false;
    }

    /**
     * @param $volume
     * @param $weight
     * @return bool
     */
    public function getTruckListByVolWeight($volume,$weight,$columns = false)
    {
        $columns =  (!$columns) ?  't.entity_id' : $columns;

        $row = \DB::select("SELECT $columns
                            FROM truck_flat t
                            WHERE ( t.volume > $volume AND t.max_weight > $weight  )
                            ORDER BY CAST(t.max_weight AS UNSIGNED),CAST(t.volume AS UNSIGNED) ASC
                            LIMIT 0,4");

        return isset($row[0]) ? $row : false;
    }
    
    public static function getRemainderNotificationData()
    {
        $currentDate = date('Y-m-d');
        $query = \DB::table('order_flat')
                    ->selectRaw('driver_id, pickup_date, MIN(pickup_time) AS pickup_time,entity_id AS order_id')
                    ->where('order_status',997)
                    ->where('pickup_date',$currentDate)
                    ->whereNotNull('driver_id')
                    ->whereNull('deleted_at')
                    ->groupBy('driver_id')
                    ->orderByRaw('CONCAT(pickup_date," ",pickup_time) ASC')
                    ->get();
        return $query;
    }


    public static function getOrderRating($limit = '',$page_no = '',$searchParams = [])
    {
        $query = \DB::table('ext_package_rate')
                            ->whereNull('deleted_at')
                            ->groupBy('target_entity_id')
                            ->orderBy('created_at','desc');

        if(!empty($searchParams['order_id'])){
            $query = $query->where('target_entity_id',$searchParams['order_id']);
        }
        if(!empty($searchParams['customer_id'])){
            $query = $query->where('actor_entity_id',$searchParams['customer_id']);
        }
        if(!empty($searchParams['driver_id'])){
            $query = $query->where('actor_entity_id',$searchParams['driver_id']);
        }
        if(!empty($searchParams['from_date']) && !empty($searchParams['to_date'])){
            $from_date = $searchParams['from_date'];
            $to_date   = $searchParams['to_date'];
            $query = $query->whereRaw("DATE('created_at') BETWEEN '$from_date' AND '$to_date' ");
        }
        $total_Records = count($query->get());

        if(!empty($limit)){
            $query = $query->take($limit);
        }

        if(!empty($page_no)){
            $query = $query->skip($page_no);
        }
        
        $query = $query->get();
        $data = [];
        if(count($query)){
            foreach($query as $key => $values){
                $getEntityType = \DB::table('ext_package_rate')
                                    ->join('sys_entity','sys_entity.entity_id','=','ext_package_rate.actor_entity_id')
                                    ->select('ext_package_rate.*','sys_entity.entity_type_id')
                                    ->where('target_entity_id',$values->target_entity_id)
                                    ->get();
                foreach($getEntityType as $entityType)
                {
                    if($entityType->entity_type_id == 3) //driver
                    {
                        $driverReview = $entityType;
                    }
                    if($entityType->entity_type_id == 11) //customer
                    {
                        $customerReview = $entityType;
                    }
                }
                $data[$values->target_entity_id] = array(
                    'driver_review' => isset($driverReview) ? $driverReview : [],
                    'customer_review' => isset($customerReview) ? $customerReview : []
                );
            }
        }
        return [
            'data' => $data,
            'total_records' => $total_Records
        ];
    }
    
    public static function getOrderReviewDetail($target_entity_id)
    {
        $data = [];
        $getOrderReview = \DB::table('ext_package_rate')
                            ->where('target_entity_id',$target_entity_id)
                            ->first();
        
        $getEntityType = \DB::table('ext_package_rate')
            ->join('sys_entity','sys_entity.entity_id','=','ext_package_rate.actor_entity_id')
            ->select('ext_package_rate.*','sys_entity.entity_type_id')
            ->where('target_entity_id',$target_entity_id)
            ->where('sys_entity.deleted_at',NUll)
            ->get();
      // echo "<pre>"; print_r($getEntityType);

        if(count($getEntityType) > 0) {

            $driver = $customer = $driverReview = $customerReview = false;

            foreach ($getEntityType as $entityType) {

                if ($entityType->entity_type_id == 3) //driver
                {
                    $driverReview = $entityType;
                    $driver = self::getEnityData('driver', $entityType->actor_entity_id);
                }
                if ($entityType->entity_type_id == 11) //customer
                {
                    $customerReview = $entityType;
                    $customer = self::getEnityData('customer', $entityType->actor_entity_id);
                }
            }

            $data = [
                'order_review' => $getOrderReview,
                'order_detail' => self::getEnityData('order', $target_entity_id, 'order_pickup,order_dropoff'),
                'driver_review' =>  ($driverReview && isset($driverReview)) ? $driverReview : [],
                'customer_review' => ($customerReview && isset($customerReview)) ? $customerReview : [],
                'driver' => ($driver && isset($driver)) ? $driver : [],
                'customer' => ($customer && isset($customer)) ? $customer : []
            ];
        }
      //  echo "<pre>"; print_r($data); exit;
        return $data;
    }

    public static function getEnityData($entity_type,$entity_id,$hook = '')
    {
        $entity_lib = new Entity();
        $params = array(
            'entity_type_id' => $entity_type,
            'entity_id' => $entity_id
        );
        if (!empty($hook)) {
            $params['hook'] = 'order_pickup,order_dropoff';
        }
        $getData = $entity_lib->apiList($params);
        return isset($getData['data']['entity_listing'][0]) ? $getData['data']['entity_listing'][0] : false;
    }
    /**
     * Get Oredr Arrived Info
     * @param $order_id
     * @return mixed
     */
    public function getOrderReachedInfo($order_id)
    {
        $query = \DB::table('order_flat AS o')
            ->select('o.*','oh.created_at AS arrived_date','ol.driver_location')
            ->leftJoin('order_history_flat AS oh', 'o.entity_id', '=', 'oh.order_id')
            ->leftJoin('order_statuses_flat AS os', 'oh.order_status', '=', 'os.entity_id')
            ->leftJoin('order_driver_location_flat AS ol','ol.order_id','=','o.entity_id')
            ->where('o.entity_id',$order_id)
            ->where('os.keyword','arrived')
            ->whereNull('o.deleted_at')
            ->get();
        return $query;
    }

    /**
     * @param $entity_type_identifier
     * @param $entity_id
     * @param bool $where
     * @return bool
     */
    public function getCompletedOrders($entity_type_identifier,$entity_id,$where = false)
    { //echo "<pre>"; print_r($where); exit;
        $order_status = new OrderStatus();
       $status_id = $order_status->getIdByKeywords('completed');

        $query = "SELECT entity_id FROM order_flat 
                  WHERE deleted_at IS NULL";

        if($entity_type_identifier == 'driver'){
            $query .= " AND driver_id = $entity_id";
        }else{
            $query .= " AND customer_id = $entity_id";
        }

        if($status_id){
            $query .= " AND order_status = $status_id";
        }

        if($where){
            $query .= " AND ".$where;
        }

       //echo $query; exit;

        $row = \DB::select($query);

        return isset($row[0]) ? $row : false;
    }

    /**
     * @param $truck_id
     * @param $pickup_time
     * @param $deliver_time
     * @return bool
     */
    public function getAvailableDrivers($truck_id,$pickup_time,$deliver_time)
    {
         $query = "SELECT v.*,d.entity_id as driver_id, d.full_name FROM vehicle_flat v
            INNER JOIN driver_flat d ON v.driver_id = d.entity_id
            INNER JOIN shifts_flat sh ON d.shift = sh.entity_id
            WHERE v.truck_id = $truck_id
            AND v.driver_id > 0
            AND d.login_status = 1
            AND d.on_duty = 1
            AND sh.shift_from <= '$pickup_time' 
            AND sh.shift_to > '$deliver_time'";
        $row = \DB::select($query);

        return isset($row[0]) ? $row : false;
    }

    /**
     * @param $driver_id
     * @param $pickup_date
     * @return bool
     */
    public function getDriverBusySlot($vehicle_id,$pickup_date,$trip_grace_minutes)
    {
        $trip_grace_minutes = "00:$trip_grace_minutes:00";

         $query ="SELECT o.entity_id,
          o.pickup_time as start_time,
         ADDTIME(DATE_FORMAT(o.estimated_delivery_date,'%H:%i:%s'),'".$trip_grace_minutes."') AS end_time
          FROM order_flat o
        LEFT JOIN order_statuses_flat os ON o.order_status = os.entity_id
        WHERE
        o.vehicle_id = $vehicle_id
         AND o.pickup_date = '$pickup_date'
         AND os.keyword IN ('accepted','arrived','on_the_way')
         ORDER BY o.pickup_time,end_time ASC";
        $row = \DB::select($query);

        return isset($row[0]) ? $row : false;
    }

    /**
     * @param $truck_id
     * @return bool
     */
    public function getDrivers($truck_id)
    {
        $query = "SELECT v.*,d.entity_id as driver_id, d.full_name 
                  FROM vehicle_flat v
            INNER JOIN driver_flat d ON v.driver_id = d.entity_id
            WHERE v.truck_id = $truck_id";
        $row = \DB::select($query);

        return isset($row[0]) ? $row : false;
    }

    /**
     * Get driver location
     * @param $order_id
     * @return mixed
     */
    public function getDriverLocation($order_id)
    {
        $query = \DB::table('order_driver_location_flat')
                    ->where('order_id',$order_id)
                    ->whereNull('deleted_at')
                    ->first();
        return $query;
    }

    /**
     * Get mOnthly stats
     * @param $where
     * @return bool
     */
    public function getOrderMonthlyGraph($where)
    {
       $query = "SELECT DATE_FORMAT(o.pickup_date,'%M') AS title,
            SUM(o.grand_total) AS total
	    	FROM
	    	order_flat o
	    	LEFT JOIN order_statuses_flat os ON o.order_status = os.entity_id
	    	WHERE o.deleted_at IS NULL AND os.keyword = 'completed'
	    	$where
	    	GROUP BY MONTH(o.pickup_date)
	    	ORDER BY o.pickup_date ASC";

        $row = \DB::select($query);
        return isset($row[0]) ? $row : false;

    }

    /**
     * Get Weekly Stats
     * @param $where
     * @return bool
     */
    public function getOrderWeeklyGraph($where)
    {
        $query = "SELECT WEEK(o.pickup_date,1) AS title,
            SUM(o.grand_total) AS total
	    	FROM
	    	order_flat o
	    	LEFT JOIN order_statuses_flat os ON o.order_status = os.entity_id
	    	WHERE o.deleted_at IS NULL AND os.keyword = 'completed'
	    	$where
	    	GROUP BY WEEK(o.pickup_date,1)
	    	ORDER BY o.pickup_date ASC";

        $row = \DB::select($query);
        return isset($row[0]) ? $row : false;
    }

    /**
     * @param $driver_id
     * @return bool
     */
    public function getDriverPendingOrder($driver_id)
    {
        $date = date('Y-m-d');

        $query = "SELECT COUNT(o.`entity_id`) as total FROM order_flat o
        LEFT JOIN order_statuses_flat os ON o.`order_status` = os.`entity_id`
        WHERE o.`driver_id` = $driver_id AND o.deleted_at is NULL
        AND os.`keyword` IN ('accepted','arrived','on_the_way')
        AND o.pickup_date >= $date";

        $row = \DB::select($query);
        return isset($row[0]->total) ? $row[0]->total : false;
    }

    
}
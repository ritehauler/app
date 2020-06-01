<?php
/**
 * this model is create to get data from flat table
 */

namespace App\Http\Models\Custom;

use App\Http\Models\Base;
use App\Libraries\GeneralSetting;
use Illuminate\Database\Eloquent\Model;

Class OrderFlat extends Base
{

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table = 'order_flat';

    }

    /**
     * Get assigned orders
     * @return bool
     */
    public function assignOrder()
    {
        $date = date('Y-m-d');

        $orders = $this->selectRaw($this->table.'.*')
        ->leftJoin('order_statuses_flat As os','os.entity_id','=',$this->table.'.order_status')
            ->where('os.keyword','assigned')->get();

        return ($orders) ? $orders : false;
    }

    /**
     * @param $driver_id
     * @return int
     */
    public function getDeclineOrder($driver_id,$order_ids)
    {
        $query = "SELECT oh.*
         FROM order_history_flat oh
         LEFT JOIN order_statuses_flat os ON oh.`order_status`= os.`entity_id`
         WHERE oh.`driver_id` = $driver_id 
         AND os.`keyword` = 'declined'
          AND oh.`order_id` IN ($order_ids)
         GROUP BY oh.`order_id`";

        $row = \DB::select($query);
        return isset($row[0]) ? count($row) : 0;
    }

    /**
     * Get Driver on ride
     * @param $pickup_date
     * @param $order_status
     * @return bool
     */
    public function getDriverOnRide($pickup_date,$order_status)
    {
        $query =  "SELECT o.`order_number`,d.*,
            s.`title` as shift_title,s.`shift_from`,s.`shift_to`,
            p.`address` AS pickup_address, od.`address` AS dropoff_address
             FROM order_flat o
            LEFT JOIN driver_flat d ON d.`entity_id` = o.`driver_id`
            LEFT JOIN shifts_flat s ON d.`shift` = s.`entity_id`
            LEFT JOIN order_pickup_flat p ON p.`order_id` = o.`entity_id`
            LEFT JOIN order_dropoff_flat od ON od.`order_id` = o.`entity_id`
            WHERE 
             o.`pickup_date` = $pickup_date AND
            o.`order_status` IN ($order_status)
            AND o.driver_id <> ''
            AND d.`shift` <> ''
            GROUP BY o.driver_id 
            ORDER BY o.pickup_time ASC";

        $row = \DB::select($query);
        return isset($row[0]) ? $row : false;
    }

    /**
     * Get Driver busy Slots
     * @param $driver_id
     * @param $start_date
     * @param $end_date
     * @return bool
     */
    public function getDriverOrderSlots($driver_id = false,$start_date,$end_date)
    {
        $general_settings = new GeneralSetting();
        $trip_grace_minutes = $general_settings->getColumn('trip_grace_minutes');
        $trip_grace_minutes = ($trip_grace_minutes) ? $trip_grace_minutes : 0;

        $query =  "SELECT o.entity_id,o.`pickup_date`,o.order_number,
          o.pickup_time AS start_time,
         ADDTIME(DATE_FORMAT(o.estimated_delivery_date,'%H:%i:%s'),'".$trip_grace_minutes."') AS end_time
          FROM order_flat o
        LEFT JOIN order_statuses_flat os ON o.order_status = os.entity_id";

        $query .= " WHERE (o.pickup_date BETWEEN '$start_date' AND '$end_date')";

        if($driver_id){
            $query .= " AND o.driver_id = $driver_id";
        }

        $query.= " AND os.keyword IN ('accepted','arrived','on_the_way')
         ORDER BY o.pickup_time,end_time ASC";

        $row = \DB::select($query);

        if(isset($row[0])){

            $orders = array();
            foreach($row as $record){

                $orders[$record->pickup_date][] = $record;
            }

            return $orders;
        }

        return false;
    }

    public function getDriverOrderDetail($driver_id = false,$date,$start_time,$end_time)
    {

        $general_settings = new GeneralSetting();
        $trip_grace_minutes = $general_settings->getColumn('trip_grace_minutes');
        $trip_grace_minutes = ($trip_grace_minutes) ? $trip_grace_minutes : 0;

        $query =  "SELECT o.*,
                d.full_name as driver_name,dauth.mobile_no as driver_mobile,
                 c.full_name as customer_name,cauth.mobile_no as customer_mobile,
                od.address as dropoff,op.address as pickup,
                 ADDTIME(DATE_FORMAT(o.estimated_delivery_date,'%H:%i:%s'),'".$trip_grace_minutes."') AS end_time,
                 ov.title as vehicle_name 
                 
          FROM order_flat o
           LEFT JOIN vehicle_flat ov ON o.vehicle_id = ov.entity_id
          LEFT JOIN order_statuses_flat os ON o.order_status = os.entity_id
          LEFT JOIN driver_flat d ON o.driver_id = d.entity_id
         LEFT JOIN sys_entity de ON de.entity_id = d.entity_id
         LEFT JOIN sys_entity_auth dauth ON dauth.entity_auth_id = de.entity_auth_id
         
           LEFT JOIN customer_flat c ON o.customer_id = c.entity_id
         LEFT JOIN sys_entity ce ON ce.entity_id = c.entity_id
         LEFT JOIN sys_entity_auth cauth ON cauth.entity_auth_id = ce.entity_auth_id
         
        LEFT JOIN order_pickup_flat op ON op.order_id = o.entity_id
        LEFT JOIN order_dropoff_flat od ON od.order_id = o.entity_id";

        $query .= " WHERE o.pickup_date = '$date'";
        $query .= " AND (o.pickup_time >= '$start_time' 
        AND ADDTIME(DATE_FORMAT(o.estimated_delivery_date,'%H:%i:%s'),'".$trip_grace_minutes."') <= '$end_time')";
       // $query .= " AND (o.pickup_time BETWEEN '$start_time' AND '$end_time')";

        if($driver_id){
            $query .= " AND o.driver_id = $driver_id";
        }

        $query.= " AND os.keyword IN ('accepted','arrived','on_the_way')
         ORDER BY o.pickup_time ASC";

        //echo $query;
        $row = \DB::select($query);
        return isset($row[0]) ? $row : false;
    }


}
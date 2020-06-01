<?php


namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

Class Truck extends Base
{

    /**
     * Verify Order Truck capacity
     * @param $order_id
     * @param $volume
     * @param $weight
     * @return bool
     */
    public function verifyTruckCapacity($order_id, $volume, $weight)
    {
        $row = \DB::select("SELECT ot.entity_id
                            FROM order_trucks_flat ot
                            WHERE ot.order_id = $order_id 
                            AND (ot.volume >= $volume 
                            AND ot.max_weight >= $weight)");

        return isset($row[0]) ? $row[0] : FALSE;
    }
}
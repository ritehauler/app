<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Base
{

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table = 'city';
        $this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = [];

        // set fields
        $this->__fields = [$this->primaryKey, 'name', 'code', 'latitude', 'longitude', 'state_id'];
    }

    public function getData($id=0,$status = false)
    {
        $data = $this->get($id);
        return $data;
    }

    /**
     * @return bool
     */
    public function getCityState()
    {
       $row = \DB::select("SELECT 
             c.state_id,s.name,s.code,c.*
            FROM city_flat cf
            LEFT JOIN state s ON cf.state_id = s.state_id
            LEFT JOIN city c ON c.city_id = cf.city_id
            ORDER BY s.name, c.name");

        return isset($row[0]) ? $row : false;
    }

    /**
     * @return bool
     */
    public function getStateList()
    {
        $row = \DB::select("SELECT DISTINCT c.state_id,s.*
            FROM city_flat c
            LEFT JOIN state s ON s.state_id = c.state_id
            ORDER BY s.name");

        return isset($row[0]) ? $row : false;
    }

    /**
     * @param $state_id
     * @return bool
     */
    public function getCityByState($state_id)
    {
        $row = \DB::select("SELECT cf.entity_id,c.name,c.code,c.latitude,c.longitude
            FROM city c
            LEFT JOIN city_flat cf ON c.city_id = cf.city_id
            WHERE cf.state_id = $state_id
            ORDER BY c.name");

        return isset($row[0]) ? $row : false;
    }

    /**
     * @param $city_id
     * @param $customer_id
     * @return bool
     */
    public function getRecentLocation($city_id, $customer_id)
    {
        $row = \DB::select("SELECT  t2.* FROM (SELECT latitude, longitude , address,city,city_name,city_code,state_name,state_code  FROM order_dropoff_flat WHERE city = $city_id AND customer_id = $customer_id
                        UNION 
                        SELECT latitude, longitude , address,city,city_name,city_code,state_name,state_code    FROM order_pickup_flat  WHERE  city = $city_id  AND customer_id = $customer_id
                        ) AS t2 GROUP BY t2.latitude");

        return isset($row[0]) ? $row : false;
    }

}
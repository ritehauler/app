<?php

/**
 * Description: this library is to get truck information
 * Author: Samreen <samreen.quyyum@cubixlabs.com>
 * Date: 04-July-2018
 * Time: 01:00 PM
 * Copyright: CubixLabs
 */
namespace App\Libraries;


use App\Http\Models\SYSTableFlat;
use App\Libraries\System\Entity;

Class OrderHistory
{
    /**
     * @param $request
     * @return NULL
     * @throws \Exception
     */
    public function addHistory($request)
    {
        $params = $this->setHistory($request);

        $entity_lib = new Entity();
        $response = $entity_lib->apiPost($params);
        return json_decode(json_encode($response));
    }

    /**
     * @param $request
     * @return array
     */
    public function setHistory($request)
    {
        $post_arr = [];
        $post_arr['entity_type_id'] = 68;
        $post_arr['order_id'] = $request->order_id;
        $post_arr['order_status'] = $request->order_status;

        if(isset($request->driver_id)){
            $post_arr['driver_id'] = $request->driver_id;
        }

        if(isset($request->vehicle_id)){
            $post_arr['vehicle_id'] = $request->vehicle_id;
        }

        if(isset($request->comment)){
            $post_arr['comment'] = $request->comment;
        }

        if(isset($request->is_admin_update)){
            $post_arr['is_admin_update'] = $request->is_admin_update;
        }
        return $post_arr;
    }
}
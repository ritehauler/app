<?php

/**
 * Description: this library is to get truck information
 * Author: Samreen <samreen.quyyum@cubixlabs.com>
 * Date: 11-July-2018
 * Time: 11:00 PM
 * Copyright: CubixLabs
 */
namespace App\Libraries;


use App\Libraries\System\Entity;

Class OrderItem
{
    /**
     * Validate Order Item
     * @param $items
     * @return \StdClass
     */
    public function validateItems($items)
    {
        $return = new \StdClass();
        $return->error = 0;

        try{
            $entity_lib = new Entity();
            //validate Items
            if(count($items) > 0){
                foreach($items as $extra_item){

                    $response_validate = $entity_lib->postValidator((array)$extra_item);

                    if($response_validate){
                        $return->error = 1;
                        $return->message = $response_validate;
                    }
                }
            }
        }
        catch (\Exception $e) {
            //  echo $e->getTraceAsString(); exit;
            $return->error = 1;
            $return->message = $e->getMessage();
            //$return['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine();
        }

        return $return;

    }

    /**
     * Calculate items total load
     * @param $items
     * @return \StdClass
     */
    public function calcTotalLoad($items)
    {
        $return = new \StdClass();
        $return->weight = $return->volume =  0;

        if(count($items) > 0){
            foreach($items as $order_item){

                $return->volume += $order_item->volume*$order_item->quantity;
                $return->weight +=  $order_item->weight*$order_item->quantity;
            }
        }

        return $return;
    }

    /**
     * @param $order_items
     * @return \StdClass
     */
    public function addOrderItem($order_items)
    {
        $return = new \StdClass();
        $return->error = 0;

        if(count($order_items) > 0){

            foreach($order_items as $item){

                $entity_lib = new Entity();
                $order_response =  $entity_lib->apiPost($item);
                $order_response = json_decode(json_encode($order_response));

                if($order_response->error == 1){

                    $return->error = 1;
                    $return->message = $order_response->message;
                    return $return;
                }


            }
        }
        $return->message = "Success";

        return $return;
    }



}
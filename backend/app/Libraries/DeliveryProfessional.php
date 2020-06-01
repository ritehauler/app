<?php

/**
 * Description: this library is related to delivery professionals
 * Author: Samreen <samreen.quyyum@cubixlabs.com>
 * Date: 20-June-2018
 * Time: 05:12 PM
 * Copyright: CubixLabs
 */

namespace App\Libraries;
use App\Http\Models\SYSTableFlat;
use App\Libraries\System\Entity;

/**
 * Class DeliveryProfessional
 * @package App\Libraries
 */
Class DeliveryProfessional
{
    private $_SYSTableFlatModel = '';

    /**
     * Truck constructor.
     */
    public function __construct()
    {
        $this->_SYSTableFlatModel = new SYSTableFlat('delivery_professional');
    }

    /**
     * @param $truck_selected_id
     * @param $professional_id
     * @return mixed
     */
    public function saveSelectedProfessional($truck_selected_id,$professional_id)
    {
        try {
            //Get delivery Professional
            $flat_model = new SYSTableFlat('delivery_professional');
            $where = ' entity_id = ' . $professional_id;
            $professional = $flat_model->getColumnByWhere($where, 'entity_id,number_of_labour,price');

            if (isset($professional->entity_id)) {
                //Save temp selected professional

                $params = array(
                    'entity_type_id' => 62,
                    'entity_id' => $truck_selected_id,
                    'loader_detail' => json_encode($professional),
                    'mobile_json' => 1
                );

                $entity_lib = new Entity();
                $response =  $entity_lib->apiUpdate($params);
                $response = json_decode(json_encode($response));
                //echo "<pre>"; print_r($response); exit;

                $return['error'] = $response->error;
                $return['message'] = $response->message;

            }
        }

        catch (\Exception $e) {
            //  echo $e->getTraceAsString(); exit;
            $return['error'] = 1;
            $return['message'] = $e->getMessage();
            //$return['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine();
        }

        return $return;
    }



}
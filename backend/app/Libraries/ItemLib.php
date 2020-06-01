<?php

/**
 * Description: this library is to get truck information
 * Author: Samreen <samreen.quyyum@cubixlabs.com>
 * Date: 25-June-2018
 * Time: 04:00 PM
 * Copyright: CubixLabs
 */
namespace App\Libraries;
use App\Http\Models\SYSEntity;
use App\Http\Models\SYSTableFlat;
use App\Libraries\System\Entity;

Class ItemLib
{
    private $_SYSTableFlatModel = '';

    /**
     * Truck constructor.
     */
    public function __construct()
    {
        $this->_SYSTableFlatModel = new SYSTableFlat('item');
    }

    /**
     * Check if other item exist then update count
     * other wise insert item
     * @param $title
     * @return bool
     */
    public function getOtherItemByTitle($title)
    {
        $order_status_flat = new SYSTableFlat('item');
        $where_condition = ' is_other = 1 AND title = "' . trim($title).'"';
        $data = $order_status_flat->getDataByWhere($where_condition);

       // echo "<pre>"; print_r($response); exit;
        if($data && isset($data[0]))
        {
               $data = $data[0];
               //update count  and return item id
              $this->updateOtherItemCount($data->entity_id);
              $entity_id = $data->entity_id;
            }
            else{
                //insert other item and return item id
                $entity_id =  $this->addOtherItem($title);
            }

            return $entity_id;


        return false;
    }

    /**
     * @param $entity_id
     */
    public function updateOtherItemCount($entity_id)
    {
        $entity_model = new SYSEntity();
        $entity_model->updateEntityAttributeValue($entity_id, 'other_item_count', 1, '+', 'item');
    }

    /**
     * @param $title
     * @return bool
     */
    public function addOtherItem($title)
    {
        $entity_lib = new Entity();
        $post_arr = [];
        $post_arr['entity_type_id'] = 14;
        $post_arr['title'] = $title;
        $post_arr['is_other'] = 1;
        $post_arr['other_item_count'] = 1;
        $response = $entity_lib->apiPost($post_arr);
        $response = json_decode(json_encode($response));
     //   echo "<pre>"; print_r($response);
        if($response->error == 0){
            return $response->data->entity->entity_id;
        }
        return false;
    }

    /**
     * @param $volume
     * @return array|mixed
     */
    public function getItemBoxByVolume($volume)
    {
        $entity_lib = new Entity();
        $params = array(
            'entity_type_id' => 'item_box',
            'status' => 1,
            'where_condition' => ' AND (min_volume <= '.$volume. ' AND max_volume >='.$volume.')',
            'order_by' => 'max_volume',
            'sorting' => 'ASC',
            'mobile_json' => 1,
            'limit' => 1
        );

        $item_boxes = $entity_lib->apiList($params);
        $item_boxes = json_decode(json_encode($item_boxes));
        // echo "<pre>"; print_r($item_boxes->data->item_box); exit;
        if(!isset( $item_boxes->data->item_box[0])){

            $params = array(
                'entity_type_id' => 'item_box',
                'status' => 1,
                'where_condition' => ' AND ( max_volume >= '.$volume.')',
                'order_by' => 'CAST(max_volume AS UNSIGNED)',
                'sorting' => 'ASC',
                'mobile_json' => 1,
                'limit' => 1
            );

            $item_boxes = $entity_lib->apiList($params);
            $item_boxes = json_decode(json_encode($item_boxes));

            if(!isset( $item_boxes->data->item_box[0])){
                $params = array(
                    'entity_type_id' => 'item_box',
                    'status' => 1,
                    'order_by' => 'CAST(max_volume AS UNSIGNED)',
                    'sorting' => 'DESC',
                    'mobile_json' => 1,
                    'limit' => 1
                );

                $item_boxes = $entity_lib->apiList($params);
                $item_boxes = json_decode(json_encode($item_boxes));
            }
        }

        return $item_boxes;

    }

    public static function getItemName($item_id)
    {
        $order_item_flat = new SYSTableFlat('item');
        $where_condition = ' entity_id = '.$item_id;
        $item_record = $order_item_flat->getColumnByWhere($where_condition,'title');
       return ($item_record && isset($item_record->title)) ? $item_record->title : "";
    }


}
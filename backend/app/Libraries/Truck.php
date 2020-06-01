<?php

/**
 * Description: this library is to get truck information
 * Author: Samreen <samreen.quyyum@cubixlabs.com>
 * Date: 13-June-2018
 * Time: 03:00 PM
 * Copyright: CubixLabs
 */
namespace App\Libraries;
use App\Http\Models\FlatTable;
use App\Http\Models\SYSTableFlat;
use App\Libraries\System\Entity;

Class Truck
{
    private $_SYSTableFlatModel = '';

    /**
     * Truck constructor.
     */
    public function __construct()
    {
        $this->_SYSTableFlatModel = new SYSTableFlat('truck');
    }

    /**
     * @param $request
     * @return array
     */
    public function getSuggestedList($request)
    {
        $request = is_array($request) ? (object)$request : $request;

        $truck_list = $this->_getListByVolumeWeight($request->volume,$request->weight);

        if(count($truck_list) > 0){
            $distance_time = $this->_totalDriveTime($request->pickup_latitude,$request->pickup_longitude,$request->dropoff_latitude,$request->dropoff_longitude);
            $truck_list = $this->_calculateEstCharges($truck_list,$distance_time);
        }

        return $truck_list;

    }

    /**
     * @param $volume
     * @param $weight
     * @return array
     */
    private function _getListByVolumeWeight($volume,$weight)
    {
        $flat_table_model = new FlatTable();
        $trucks =  $flat_table_model->getTruckListByVolWeight($volume,$weight);
        //echo "<pre>"; print_r($trucks); exit;
        $truck_list = array();


        if($trucks){
            if(count($trucks) > 0){

              $entity_lib = new Entity();
                foreach($trucks as $truck)
                {
                    $params = array(
                        'entity_type_id' => 'truck',
                        'entity_id' => $truck->entity_id,
                        'mobile_json' => 1,
                        'in_detail' => 1
                    );

                    $response =  $entity_lib->apiGet($params);
                    $response = json_decode(json_encode($response));
                    //echo "<pre>"; print_r($response); exit;

                    if($response->error == 0 && isset($response->data->truck)){
                        $truck_list[] = $response->data->truck;
                    }
                }
              /* $entity_ids = $this->_getEntityIDs($trucks);
                $params = array(
                    'entity_type_id' => 'truck',
                    'entity_id' => implode(',',$entity_ids),
                    'mobile_json' => 1,
                    'in_detail' => 1
                );

                $entity_lib = new Entity();
                $response =  $entity_lib->apiList($params);
                $response = json_decode(json_encode($response));
                if($response->error == 0 && isset($response->data->truck[0])){
                    $truck_list = $response->data->truck;
                }*/
            }
        }

        return $truck_list;

    }

    /**
     * @param $pickup_lat
     * @param $pickup_long
     * @param $dropoff_lat
     * @param $dropoff_long
     * @return float|int|string
     */
    private function _totalDriveTime($pickup_lat,$pickup_long,$dropoff_lat,$dropoff_long)
    {
        $total_distance = $total_minutes = '';
        $google_api = new GoogleApi();
        $response =  $google_api->GetDrivingDistance($pickup_lat,$pickup_long,$dropoff_lat,$dropoff_long);
        $response = json_decode(json_encode($response));

      // echo "<pre>"; print_r($response); exit;
       //convert time in minutes as it is in seconds
        if(isset($response->time->value)){
            $total_minutes = abs(round($response->time->value/60));
        }

       // convert disctance in km as it is in meters
        if(isset($response->distance->value)){
            $total_distance = CustomHelper::metersToMiles($response->distance->value);
           // $total_distance = abs(round($response->distance->value/1000));
        }

        $return = new \StdClass();
        $return->total_minutes = $total_minutes;
        $return->total_distance = $total_distance;

        return $return;
    }

    /**
     * @param $listing
     * @param $total_minutes
     * @return array
     */
    private function _calculateEstCharges($listing,$distance_time)
    {
        $rows = array();
        if($listing){

            //Get estimation charges range
            $flat_table_model = new SYSTableFlat('general_setting');
            $est_charges_range = $flat_table_model->getColumn('est_charges_range');
            $est_charges_range = ($est_charges_range) ? $est_charges_range : 0;
            $distance_time->total_minutes = ($distance_time->total_minutes > 0) ? $distance_time->total_minutes : config('constants.DEFAULT_ESTIMATED_MIN');

            foreach($listing as $list){

                //echo "<pre>"; print_r($list); exit;
                $list->total_minutes = $distance_time->total_minutes;
                $list->total_distance = $distance_time->total_distance;
                $est_charges = ($list->charge_per_minute * $distance_time->total_minutes)+$list->base_fee;
                $list->est_min_charges = CustomHelper::roundOffPrice($est_charges);
                $list->est_max_charges =  CustomHelper::roundOffPrice($est_charges + $est_charges_range);
                $rows[] = $list;
            }
        }

        return $rows;
    }

    /**
     * @param $entity_ids
     * @return array
     */
    private function _getEntityIDs($entity_ids)
    {
        $arr = array();
        if(count($entity_ids) >0){
            foreach($entity_ids as $row){
                $arr[] = $row->entity_id;
            }
        }

        return $arr;
    }

    /**
     * @param $listing
     * @return bool
     */
   public function saveSuggestedList($listing)
   {
       if($listing){
           if(count($listing) > 0){
               $trucks = array();
               foreach($listing as $list){
                  // unset($list->gallery);
                   unset($list->status);
                   $trucks[] = $list;
               }

               $params = array(
                   'entity_type_id' => 63,
                   'truck_detail' => json_encode($trucks),
                   'is_ordered' => 0,
                   'mobile_json' => 1
               );

               $entity_lib = new Entity();
              $response =  $entity_lib->apiPost($params);
               $response = json_decode(json_encode($response));

               if(isset($response->data->truck_suggested->entity_id))
                   return $response->data->truck_suggested->entity_id;
               else
                   return false;
              // echo "<pre>"; print_r($data); exit;
           }

       }
   }

    /**
     * @param $suggested_truck_id
     * @param $truck_id
     * @return array
     */
   public function saveSelectedTruck($suggested_truck_id,$truck_id)
   {
       try{
           //Get Suggested Truck
           $truck = $this->_getSelectedTruck($suggested_truck_id,$truck_id);
           if(!$truck){
               return array(
                   'error' => 1,
                   'message' => 'The suggested truck id is already ordered'
               );
           }
           //Save selected Truck
           $selected_truck  = $this->_saveSelectedTruck($suggested_truck_id,$truck);

           if(is_numeric($selected_truck)){
               return array(
                   'error' => 0,
                   'message' => 'Success',
                   'entity_id' => $selected_truck
               );
           }

           if(!$selected_truck){
               return array(
                   'error' => 1,
                   'message' => 'The selected truck couldnot save',
               );
           }

           return array(
               'error' => 1,
               'message' => $selected_truck,
           );
       }
       catch (\Exception $e) {
           //  echo $e->getTraceAsString(); exit;
           $return['error'] = 1;
           $return['message'] = $e->getMessage();
           //$return['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine();
       }

       return $return;
   }

    /**
     * Get Selected Truck
     * @param $suggested_truck_id
     * @param $truck_id
     * @return bool
     */
   private function _getSelectedTruck($suggested_truck_id,$truck_id)
   {
       $flat_model = new SYSTableFlat('truck_suggested');
       $where = ' entity_id = '.$suggested_truck_id.' AND is_ordered = 0';
       $data =  $flat_model->getColumnByWhere($where,'entity_id,truck_detail');

       if(isset($data->entity_id)){
           $truck_detail = json_decode($data->truck_detail);

           if(count($truck_detail > 0)){
               foreach($truck_detail as $truck){

                   if(trim($truck_id) == $truck->entity_id)
                       return $truck;

               }
           }
          // echo "<pre>"; print_r($truck_detail); exit;
       }

       return false;
   }

    /**
     * Save Selected Truck
     * @param $suggested_truck_id
     * @param $truck
     * @return bool
     */
   private function _saveSelectedTruck($suggested_truck_id,$truck)
   {
       $params = array(
           'entity_type_id' => 62,
           'truck_detail' => json_encode($truck),
           'is_ordered' => 0,
           'truck_suggested_id' => $suggested_truck_id,
           'mobile_json' => 1
       );

       $entity_lib = new Entity();
       $response =  $entity_lib->apiPost($params);
       $response = json_decode(json_encode($response));
       //echo "<pre>"; print_r($response); exit;
       if(isset($response->data->truck_selected->entity_id))
           return $response->data->truck_selected->entity_id;
       else
          if($response->error == 1)
            return $response->message;
           return false;
   }

    /**
     * @param $weight
     * @param $volume
     * @param $columns
     * @return bool
     */
   public function getTruckByWeightVol($weight,$volume,$columns = false)
   {
       $flat_table_model = new FlatTable();
      return $flat_table_model->getTruckListByVolWeight($volume,$weight,$columns);
   }

}
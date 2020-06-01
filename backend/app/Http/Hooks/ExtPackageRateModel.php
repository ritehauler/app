<?php namespace App\Http\Hooks;

// models
#use App\Http\Models\Achievement;

use App\Http\Models\SYSAttributeOption;
use App\Http\Models\SYSTableFlat;
use App\Libraries\System\Entity;

class ExtPackageRateModel
{

    protected $__modelPath = "\App\Http\Models\\";

    private $_targetEntityTypId = '';
    private $_targetEntityId = '';
    private $_targetEntityIdentifier = '';
    private $_targetEntityData;

    public function __construct()
    {
            
    }

    /**
     * if entity type is order then get the target entity
     * b/c if customer is rating then target is driver and if driver is
     * rating then target is customer then update their entity for review options
     * @param $request
     * @param $aggregate
     * @return array|bool
     */
    public function saveData($request, $aggregate)
    {
        $request = is_array($request) ? (object)$request : $request;
        //if entity type is order
        if($request->target_entity_type_id == 15){
            //get target entity type data
            $this->_getTargetEntity($request);
            //set other attributes for customer/driver for review options
            if(!empty($this->_targetEntityId)){
                $other_data = $this->_setReviewOptions($request,$this->_targetEntityData);
               //update target entity
                return $this->_updateTargetEntity($request,$aggregate,$other_data);
            }

        }
        return true;
    }

    /**
     * Get the target entity Data
     * @param $request
     */
    private function _getTargetEntity($request)
    {
        $flat_table = new SYSTableFlat('order');
        $get_data = $flat_table->getDataByWhere(' entity_id = '.$request->target_entity_id,array('customer_id','driver_id'));

        if($get_data) {
            $order = $get_data[0];

            Switch($request->actor_entity_type_id){

                case 11:
                    $this->_targetEntityTypId = 3;
                    $this->_targetEntityId = $order->driver_id;
                    $this->_targetEntityIdentifier = 'driver';
                    break;

                case 3:
                    $this->_targetEntityTypId = 11;
                    $this->_targetEntityId = $order->customer_id;
                    $this->_targetEntityIdentifier = 'customer';
                    break;
            }

            if(!empty($this->_targetEntityId)){
                $flat_table = new SYSTableFlat( $this->_targetEntityIdentifier);
                $data = $flat_table->getColumnByWhere(' entity_id = '.$this->_targetEntityId,'rating_options');
                $this->_targetEntityData = $data;
            }

            //echo "<pre>"; print_r( $this->_targetEntityData); exit;
        }
    }

    /**
     * @param $request
     * @param $target_entity_data
     * @return mixed|string
     */
    private function _setReviewOptions($request,$target_entity_data)
    {
        $rating_options = '';

        if((isset($request->json_data) && !empty($request->json_data)) && isset($target_entity_data)) {

            $json_data = (is_array($request->json_data)) ? $request->json_data : explode(',', $request->json_data);
            // $json_data = explode(',',$request->json_data);

            $other_date = sprintf("'%s'", implode("', '", $json_data));
            //echo "<pre>"; print_r($other_date); exit;
            $sys_attribute = new SYSAttributeOption();
            $valid_options = $sys_attribute->checkValidOptions($this->_targetEntityIdentifier . '_review', $other_date);

            if($valid_options){
                //if no rating options are set
                //echo '<pre>'; print_r($target_entity_data); exit;
                if ($target_entity_data->rating_options == '') {

                    foreach ($json_data as $value) {
                        if (in_array($value, $valid_options))
                            $rating_options[ $value ] = 1;
                    }
                } else {

                    //if rating options are not empty
                    $rating_options = json_decode($target_entity_data->rating_options, TRUE);

                    foreach ($json_data as $value) {

                        if (in_array($value, $valid_options)) {

                            if (array_key_exists($value, $rating_options)) {
                                $rating_options[ $value ] = $rating_options[ $value ] + 1;
                            } else {
                                $rating_options[ $value ] = 1;
                            }
                        }

                    }
                }
            }
        }
       //echo "<pre>"; print_r($rating_options); exit;
        return $rating_options;
    }

    /**
     * @param $request
     * @param $aggregate
     * @param $other_data
     * @return array
     */
    private function _updateTargetEntity($request,$aggregate,$other_data)
    {
        if((isset($this->_targetEntityTypId) && !empty($this->_targetEntityTypId))
            && (isset($this->_targetEntityId) && !empty($this->_targetEntityId)))
        {
            // update aggregate to target entity
            $params = array(
                'entity_type_id' => $this->_targetEntityTypId,
                'entity_id' => $this->_targetEntityId,
                // aggregate field
                //    $ext_map_record->aggregate_field => $aggregate->aggregate_value,
                // rating
                'ext_rating' => $request->rating,
                // total_rating
                'ext_total_rating' => $aggregate->json_value->ext_total_rating,
                // total_raters
                'ext_total_raters' => $aggregate->json_value->ext_total_raters,
                // average_rating
                'ext_average_rating' => floatval(number_format(
                    ($aggregate->json_value->ext_total_rating / $aggregate->json_value->ext_total_raters),
                    2, '.', '')),
                'mobile_json' => (isset($request->mobile_json)) ? TRUE : FALSE,
                'login_entity_type_id' => isset($request->actor_entity_type_id) ? $request->actor_entity_type_id : "",
                'login_entity_id' =>isset($request->actor_entity_id) ? $request->actor_entity_id : "",
               );

            if(!empty($other_data)){
                $params['rating_options'] = json_encode($other_data);
            }

            $entity_lib = new Entity();
           $response =  $entity_lib->apiUpdate($params);
           return $response;
        }
    }

}
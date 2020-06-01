<?php namespace App\Http\Models\Extension\Social;

use App\Http\Models\Base;
use App\Http\Models\SYSEntity;
use App\Http\Models\SYSEntityType;
use App\Http\Models\SYSEntityTypeExtensionMap;
use App\Http\Models\SYSExtensionStat;
use App\Libraries\CustomHelper;
use App\Libraries\System\Entity;
use Illuminate\Database\Eloquent\SoftDeletes as SoftDeletes;
use Illuminate\Http\Request;

// init models
//use App\Http\Models\Conf;

class ExtPackageRate extends Base
{

    use SoftDeletes;
    public $table = 'ext_package_rate';
    public $timestamps = TRUE;
    public $primaryKey = 'package_rate_id';
    protected $dates = ['deleted_at'];
    public $objectIdentifier = "rate";
    public $actionIdentifier = "rate";
    private $_pHook = "ExtPackageRateModel"; // extension hook
    public $currentActorID = NULL;

    /**
     * Models set
     */
    private

        /** Entity type
         *
         * @var $_entityTypeModel
         */
        $_entityTypeModel,

        /**
         * Entity
         *
         * @var $_entityModel
         */
        $_entityModel,

        /**
         * Extension stat
         *
         * @var $_extStatModel
         */
        $_extStatModel,

        /**
         * Entity Type Extension Map
         *
         * @var $_eTypeExtMapModel
         */
        $_eTypeExtMapModel;

    /**
     * Entity Library
     *
     * @var $_entityLib
     */
    private $_entityLib;


    /**
     * ExtPackageRate constructor.
     */
    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'activity_type_id', 'entity_type_extension_map_id', 'target_entity_id', 'actor_entity_id', 'data_entity_id', 'review', 'rating', 'json_data', 'created_at', 'updated_at', 'deleted_at');

        //load models
        $this->_entityTypeModel = new SYSEntityType();
        $this->_entityModel = new SYSEntity();
        $this->_extStatModel = new SYSExtensionStat();
        $this->_eTypeExtMapModel = new SYSEntityTypeExtensionMap();
        // load lib
        $this->_entityLib = new Entity();
    }


    /**
     * save
     *
     * @return Response
     */
    public function saveData($ext_map_record, $save_data, $timestamp = NULL,$request_params = null)
    {
        // default vars
        $timestamp = $timestamp == "" ? date("Y-m-d H:i:s") : $timestamp;
        $request = new Request();

        $data_entity_id = 0;

        // if data entity exists, save data entity record
//        if (intval($ext_map_record->{'data_' . $this->_entityTypeModel->primaryKey}) > 0) {
//            $params = array(
//                $this->_entityTypeModel->primaryKey => $ext_map_record->{'data_' . $this->_entityTypeModel->primaryKey},
//                'target_' . $this->_entityModel->primaryKey => $save_data['target_' . $this->_entityModel->primaryKey],
//                'actor_' . $this->_entityModel->primaryKey => $save_data['actor_' . $this->_entityModel->primaryKey],
//                'review' => $save_data['review'],
//                'ext_rating' => $save_data['rating'],
//                'json_data' => $save_data['json_data'],
//            );
//            print_r($params);exit;
//            $ret = CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities', 'POST', $params);
//            print_r($ret);exit;
//            unset($params);
//
//            // set created record id
//            $data_entity_id = $ret->error == 0 ?
//                $ret->data->{$ret->data->identifier}->{$this->_entityModel->primaryKey} : 0;
//        }

        // set data
        $save_data[ $this->_eTypeExtMapModel->primaryKey ] = $ext_map_record->{$this->_eTypeExtMapModel->primaryKey};
        $save_data['data_entity_id'] = $data_entity_id;
        $save_data['created_at'] = isset($save_data['created_at']) ? $save_data['created_at'] : $timestamp;
        // insert
        $id = $this->put($save_data);

        // save stats
        // update json stats (aggregate_field)
        if (trim($ext_map_record->aggregate_field) != "") {
            $this->_extStatModel->setJSON(
                $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
                $save_data[ 'target_' . $this->_entityModel->primaryKey ],
                $ext_map_record->aggregate_field,
                1);
        }

        // update json stats (total_rating)
        $this->_extStatModel->setJSON(
            $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
            $save_data[ 'target_' . $this->_entityModel->primaryKey ],
            'ext_total_rating',
            intval($save_data['rating']));
        // update json stats (total_raters)
        $this->_extStatModel->setJSON(
            $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
            $save_data[ 'target_' . $this->_entityModel->primaryKey ],
            'ext_total_raters',
            1); // increment 1
        // update aggregates stats
        $aggregate = $this->_extStatModel->setAggregate(
            $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
            $save_data[ 'target_' . $this->_entityModel->primaryKey ],
            1);

        // update aggregate to target entity
  /*      $params = array(
            $this->_entityTypeModel->primaryKey => $ext_map_record->{'target_' . $this->_entityTypeModel->primaryKey},
            $this->_entityModel->primaryKey => $save_data[ 'target_' . $this->_entityModel->primaryKey ],
            // aggregate field
            $ext_map_record->aggregate_field => $aggregate->aggregate_value,
            // rating
            'ext_rating' => $save_data['rating'],
            // total_rating
            'ext_total_rating' => $aggregate->json_value->ext_total_rating,
            // total_raters
            'ext_total_raters' => $aggregate->json_value->ext_total_raters,
            // average_rating
            'ext_average_rating' => floatval(number_format(
                ($aggregate->json_value->ext_total_rating / $aggregate->json_value->ext_total_raters),
                2, '.', '')),
           // 'status' => 1, // active
        );
        //$r = CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);
        $this->_entityLib->doUpdate($params);*/

        // call hook

        $this->_apiData = CustomHelper::hookData($this->_pHook, __FUNCTION__, $request_params, $aggregate);

        // get data
        //$data = $this->getData($id);

        return $id;
    }


    /**
     * update
     *
     * @return Response
     */
    public function updateData($ext_map_record, $id, $save_data, $timestamp = NULL)
    {
        // default vars
        $timestamp = $timestamp == "" ? date("Y-m-d H:i:s") : $timestamp;
        $request = new Request();
        $record = $this->get($id);
        $data = NULL;

        // if record exists
        if ($record) {
            $data_entity_id = $record->{'data_' . $this->_entityModel->primaryKey};

            // update in entity record
            if ($data_entity_id > 0) {
                $params = array(
                    $this->_entityModel->primaryKey => $data_entity_id,
                    $this->_entityTypeModel->primaryKey => $ext_map_record->{'data_' . $this->_entityTypeModel->primaryKey},
                    'target_' . $this->_entityModel->primaryKey => $record->{'target_' . $this->_entityModel->primaryKey},
                    'actor_' . $this->_entityModel->primaryKey => $record->{'actor_' . $this->_entityModel->primaryKey},
                    'review' => $save_data['review'],
                    'ext_rating' => $save_data['rating'],
                    'json_data' => $save_data['json_data'],
                );

                $ret = CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);
                unset($params);

                /*$ret->error == 0 ?
                    $ret->data->{$ret->data->identifier}->{$this->_entityModel->primaryKey} : 0;*/
            }

            // set data
            //$save_data[$this->_eTypeExtMapModel->primaryKey] = $ext_map_record->{$this->_eTypeExtMapModel->primaryKey};
            //$save_data['data_entity_id'] = $data_entity_id;
            $save_data['updated_at'] = isset($save_data['updated_at']) ? $save_data['updated_at'] : $timestamp;
            // insert
            $id = $this->set($id, $save_data);

            // we need to re-calculate rating / total_raters / average_rating
            // 1 - get total ratings for target_entity_id
            $results = $this->selectRaw('COALESCE(SUM(rating),0) as total_rating')
                ->where('target_' . $this->_entityModel->primaryKey, $record->{'target_' . $this->_entityModel->primaryKey})
                ->whereNull('deleted_at')
                ->first();

            // 2 - update json stats (total_rating)
            $aggregate = $this->_extStatModel->setJSON(
                $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
                $record->{'target_' . $this->_entityModel->primaryKey},
                'ext_total_rating',
                $results->total_rating);

            // update aggregate to target entity
           $params = array(
                $this->_entityTypeModel->primaryKey => $ext_map_record->{'target_' . $this->_entityTypeModel->primaryKey},
                $this->_entityModel->primaryKey => $record->{'target_' . $this->_entityModel->primaryKey},
                // aggregate field
                $ext_map_record->aggregate_field => $aggregate->aggregate_value,
                // rating
                'ext_rating' => $save_data['rating'],
                // total_rating
                'ext_total_rating' => $results->total_rating,
                // total_raters
                'ext_total_raters' => $aggregate->json_value->ext_total_raters,
                // average_rating
                'ext_average_rating' => floatval(number_format(
                    ($aggregate->json_value->ext_total_rating / $aggregate->json_value->ext_total_raters),
                    2, '.', '')),
                'status' => 1, // active
            );

            //CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);
            $this->_entityLib->doUpdate($params);


            // get data
            $data = $this->getData($id);
        }


        return $data;
    }


    /**
     * remove
     *
     * @return Response
     */
    public function removeData($ext_map_record, $id, $timestamp = NULL)
    {
        // get record
        $record = $this->get($id);
        $is_removed = FALSE;

        // if record exists
        if ($record) {

            // default vars
            $timestamp = $timestamp == "" ? date("Y-m-d H:i:s") : $timestamp;
            $request = new Request();

            // remove
            $this->remove($record->{$this->primaryKey}, $timestamp);

            // save stats
            // update json stats
            if (trim($ext_map_record->aggregate_field) != "") {
                $this->_extStatModel->setJSON(
                    $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
                    $record->{'target_' . $this->_entityModel->primaryKey},
                    $ext_map_record->aggregate_field,
                    -1);
            }

            // update aggregates stats
            $aggregate = $this->_extStatModel->setAggregate(
                $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
                $record->{'target_' . $this->_entityModel->primaryKey},
                -1);

            // we need to re-calculate rating / total_raters / average_rating
            // 1 - get total ratings for target_entity_id
            $results = $this->selectRaw('COALESCE(SUM(rating),0) as total_rating')
                ->where('target_' . $this->_entityModel->primaryKey, $record->{'target_' . $this->_entityModel->primaryKey})
                ->whereNull('deleted_at')
                ->first();
            // 2 - update json stats (total_rating)
            $aggregate = $this->_extStatModel->setJSON(
                $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
                $record->{'target_' . $this->_entityModel->primaryKey},
                'ext_total_rating',
                $results->total_rating);

            // update aggregate to target entity
            $params = array(
                $this->_entityTypeModel->primaryKey => $ext_map_record->{'target_' . $this->_entityTypeModel->primaryKey},
                $this->_entityModel->primaryKey => $record->{'target_' . $this->_entityModel->primaryKey},
                // aggregate field
                $ext_map_record->aggregate_field => $aggregate->aggregate_value,
                // rating
                'ext_rating' => $record->rating,
                // total_rating
                'ext_total_rating' => $results->total_rating,
                // total_raters
                'ext_total_raters' => $aggregate->json_value->ext_total_raters,
                // average_rating
                'ext_average_rating' => floatval(number_format(
                    ($aggregate->json_value->ext_total_rating / $aggregate->json_value->ext_total_raters),
                    2, '.', '')),
                'status' => 1, // active
            );
            //CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);
            $this->_entityLib->doUpdate($params);

            // remove data entity from API
            if (intval($record->{'data_' . $this->_entityModel->primaryKey}) > 0) {
                $params = array(
                    $this->_entityModel->primaryKey => $record->{'data_' . $this->_entityModel->primaryKey}
                );
                //CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/delete', 'POST', $params);
                $this->_entityLib->doDelete($params);
            }

            $is_removed = TRUE;
        }

        return $is_removed;
    }


    /**
     * getData
     *
     * @return Response
     */
    public function getData($pk_id = 0)
    {
        // record
        $record = $this->get($pk_id);
        $ext_map = $this->_eTypeExtMapModel->get($record->{$this->_eTypeExtMapModel->primaryKey});

        // init data
        $data = NULL;

        if ($record) {
            // load models
            $this->_entityTypeModel = $this->__modelPath . 'SYSEntityType';
            $this->_entityTypeModel = new $this->_entityTypeModel;

            // assign record
            $data = json_decode(json_encode($record));

            // attach target entity data
            $data->target_entity = $data->data_entity = $data->actor_entity = NULL;
            $eData = $this->_getEntityDataFromAPI($ext_map->target_entity_type_id, $record->target_entity_id);

            if ($eData) {
                $identifier = $eData->identifier == 'entity' ? 'target_entity' : $eData->identifier; // fix for non-mobile
                $data->{$identifier} = $eData->{$eData->identifier};
            }


            // attach actor entity data
            $eData = $this->_getEntityDataFromAPI($ext_map->actor_entity_type_id, $record->actor_entity_id);
            if ($eData) {
                $identifier = $eData->identifier == 'entity' ? 'actor_entity' : $eData->identifier; // fix for non-mobile
                $data->{$identifier} = $eData->{$eData->identifier};
            }

            // if got data entity id
            if (isset($data->data_entity_id) && $data->data_entity_id > 0) {
                $eData = $this->_getEntityDataFromAPI($ext_map->data_entity_type_id, $record->data_entity_id);
                if ($eData) {
                    $identifier = $eData->identifier == 'entity' ? 'data_entity' : $eData->identifier; // fix for non-mobile
                    $data->{$identifier} = $eData->{$eData->identifier};
                }
            }

            // extra keys
            $data->has_reviewed = ($this->currentActorID == $record->actor_entity_id) ? 1 : 0;

            // unset un-required;
            unset($data->deleted_at);

            // pass via hook
            $data = CustomHelper::hookData($this->_pHook, __FUNCTION__, new Request(), $data);
        }


        return $data;
    }


    /**
     * get Entity data from API
     *
     * @return Response
     */
    private function _getEntityDataFromAPI($entity_type_id, $entity_id = 0)
    {
        /*
        // init data
        $data = NULL;
        // set body/request
        $request = new Request();
        $url = \URL::to(DIR_API) . '/system/entities';
        $params = array(
            'entity_type_id' => $entity_type_id,
            'entity_id' => $entity_id
        );
        // call api
        $ret = CustomHelper::appCall($request, $url, 'GET', $params);

        //$data = $ret->error == 0 ? $ret->data->{$ret->data->identifier} : NULL;
        if (isset($ret->error)) {
            $data = $ret->error == 0 ? (isset($ret->data) ? $ret->data : NULL) : NULL;
        } else {
            $data = NULL;
            exit($ret);
        }


        return $data;*/

        $request = [
            'entity_type_id' => $entity_type_id,
            'mobile_json' => CustomHelper::$mobileJson
        ];
        $data = $this->_entityLib->getData($entity_id, $request);
        if ($data) {
            $data = (object)[
                'identifier' => $data->object_key,
                $data->object_key => $data
            ];
        }

        return $data;
    }

    /**
     * @param $ext_map_id
     * @param $target_entity_id
     * @param int $actor_entity_id
     * @return mixed
     */
    public function check($ext_map_id, $target_entity_id, $actor_entity_id = 0)
    {
        // init data
        $raw_record = $this->select($this->primaryKey)
            ->where($this->_eTypeExtMapModel->primaryKey, $ext_map_id)
            ->where('target_' . $this->_entityModel->primaryKey, $target_entity_id)
            ->where('actor_' . $this->_entityModel->primaryKey, $actor_entity_id)
            ->whereNull('deleted_at')
            ->first();
        $raw_record = json_decode(json_encode($raw_record));

        return $raw_record ? $this->getData($raw_record->{$this->primaryKey}) : NULL;
    }

    /**
     * @param $ext_map_id
     * @param $target_entity_id
     * @param int $limit
     * @return bool
     */
    public function getTargetReviews($ext_map_id,$target_entity_id,$limit = false)
    {
        // init data
        $raw_record =  \DB::table($this->table)->select('package_rate_id','review','rating')
            ->where($this->_eTypeExtMapModel->primaryKey, $ext_map_id)
            ->whereIn('target_' . $this->_entityModel->primaryKey, $target_entity_id)
            ->whereNull('deleted_at');

        if($limit){
            $raw_record->orderBy('created_at','DESC');
           $raw_record->take($limit);
           $raw_record->skip(0);
        }

    // echo $raw_record->toSql(); exit;
        $row = $raw_record->get();
       // $raw_record = json_decode(json_encode($raw_record));
        return isset($row[0]) ? $row : false;

    }

}
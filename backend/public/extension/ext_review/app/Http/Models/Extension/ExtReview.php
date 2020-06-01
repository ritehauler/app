<?php namespace App\Http\Models\Extension;

use App\Http\Models\Base;
use App\Libraries\CustomHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use Illuminate\Http\Request;

// init models
//use App\Http\Models\Conf;

class ExtReview extends Base
{

    use SoftDeletes;
    public $table = '{plugin_identifier}';
    public $timestamps = true;
    public $primaryKey = 'review_id';
    protected $dates = ['deleted_at'];
    public $objectIdentifier = "review";
    public $actionIdentifier = "review";
    private $_pHook = "ExtReviewModel"; // extension hook
    public $currentActorID = NULL;

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'entity_type_extension_map_id', 'target_entity_id', 'actor_entity_id', 'data_entity_id', 'review', 'rating', 'json_data', 'created_at', 'updated_at', 'deleted_at');
    }


    /**
     * save
     *
     * @return Response
     */
    public function saveData($ext_map_record, $save_data, $timestamp = NULL)
    {
        // load models
        $eTypeModel = $this->__modelPath . 'SYSEntityType';
        $eTypeModel = new $eTypeModel;
        $entityModel = $this->__modelPath . 'SYSEntity';
        $entityModel = new $entityModel;
        $extStatModel = $this->__modelPath . 'SYSExtensionStat';
        $extStatModel = new $extStatModel;
        $eTypeExtMapModel = $this->__modelPath . 'SYSEntityTypeExtensionMap';
        $eTypeExtMapModel = new $eTypeExtMapModel;

        // default vars
        $timestamp = $timestamp == "" ? date("Y-m-d H:i:s") : $timestamp;
        $request = new Request();

        $data_entity_id = 0;

        // if data entity exists, save data entity record
        if (intval($ext_map_record->{'data_' . $eTypeModel->primaryKey}) > 0) {
            $params = array(
                $eTypeModel->primaryKey => $ext_map_record->{'data_' . $eTypeModel->primaryKey},
                'target_' . $entityModel->primaryKey => $save_data['target_' . $entityModel->primaryKey],
                'actor_' . $entityModel->primaryKey => $save_data['actor_' . $entityModel->primaryKey],
                'review' => $save_data['review'],
                'rating' => $save_data['rating'],
                'json_data' => $save_data['json_data'],
            );
            $ret = CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities', 'POST', $params);
            unset($params);

            // set created record id
            $data_entity_id = $ret->error == 0 ?
                $ret->data->{$ret->data->identifier}->{$entityModel->primaryKey} : 0;
        }

        // set data
        $save_data[$eTypeExtMapModel->primaryKey] = $ext_map_record->{$eTypeExtMapModel->primaryKey};
        $save_data['data_entity_id'] = $data_entity_id;
        $save_data['created_at'] = isset($save_data['created_at']) ? $save_data['created_at'] : $timestamp;
        // insert
        $id = $this->put($save_data);

        // save stats
        // update json stats (aggregate_field)
        $extStatModel->setJSON(
            $ext_map_record->{$eTypeExtMapModel->primaryKey},
            $save_data['target_' . $entityModel->primaryKey],
            $ext_map_record->aggregate_field,
            1);
        // update json stats (total_rating)
        $extStatModel->setJSON(
            $ext_map_record->{$eTypeExtMapModel->primaryKey},
            $save_data['target_' . $entityModel->primaryKey],
            'total_rating',
            intval($save_data['rating']));
        // update json stats (total_raters)
        $extStatModel->setJSON(
            $ext_map_record->{$eTypeExtMapModel->primaryKey},
            $save_data['target_' . $entityModel->primaryKey],
            'total_raters',
            1); // increment 1
        // update aggregates stats
        $aggregate = $extStatModel->setAggregate(
            $ext_map_record->{$eTypeExtMapModel->primaryKey},
            $save_data['target_' . $entityModel->primaryKey],
            1);

        // update aggregate to target entity
        $params = array(
            $eTypeModel->primaryKey => $ext_map_record->{'target_' . $eTypeModel->primaryKey},
            $entityModel->primaryKey => $save_data['target_' . $entityModel->primaryKey],
            // aggregate field
            $ext_map_record->aggregate_field => $aggregate->aggregate_value,
            // rating
            'rating' => $save_data['rating'],
            // total_rating
            'total_rating' => $aggregate->json_value->total_rating,
            // total_raters
            'total_raters' => $aggregate->json_value->total_raters,
            // average_rating
            'average_rating' => floatval(number_format(
                ($aggregate->json_value->total_rating / $aggregate->json_value->total_raters),
                2, '.', '')),
        );
        CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);

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
        // load models
        $eTypeModel = $this->__modelPath . 'SYSEntityType';
        $eTypeModel = new $eTypeModel;
        $entityModel = $this->__modelPath . 'SYSEntity';
        $entityModel = new $entityModel;
        $extStatModel = $this->__modelPath . 'SYSExtensionStat';
        $extStatModel = new $extStatModel;
        $eTypeExtMapModel = $this->__modelPath . 'SYSEntityTypeExtensionMap';
        $eTypeExtMapModel = new $eTypeExtMapModel;

        // default vars
        $timestamp = $timestamp == "" ? date("Y-m-d H:i:s") : $timestamp;
        $request = new Request();
        $record = $this->get($id);
        $data = null;

        // if record exists
        if ($record) {
            $data_entity_id = $record->{'data_' . $entityModel->primaryKey};

            // update in entity record
            if ($data_entity_id > 0) {
                $params = array(
                    $entityModel->primaryKey => $data_entity_id,
                    $eTypeModel->primaryKey => $ext_map_record->{'data_' . $eTypeModel->primaryKey},
                    'target_' . $entityModel->primaryKey => $record->{'target_' . $entityModel->primaryKey},
                    'actor_' . $entityModel->primaryKey => $record->{'actor_' . $entityModel->primaryKey},
                    'review' => $save_data['review'],
                    'rating' => $save_data['rating'],
                    'json_data' => $save_data['json_data'],
                );

                $ret = CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);
                unset($params);

                /*$ret->error == 0 ?
                    $ret->data->{$ret->data->identifier}->{$entityModel->primaryKey} : 0;*/
            }

            // set data
            //$save_data[$eTypeExtMapModel->primaryKey] = $ext_map_record->{$eTypeExtMapModel->primaryKey};
            //$save_data['data_entity_id'] = $data_entity_id;
            $save_data['updated_at'] = isset($save_data['updated_at']) ? $save_data['updated_at'] : $timestamp;
            // insert
            $id = $this->set($id, $save_data);

            // we need to re-calculate rating / total_raters / average_rating
            // 1 - get total ratings for target_entity_id
            $results = $this->selectRaw('COALESCE(SUM(rating),0) as total_rating')
                ->where('target_' . $entityModel->primaryKey, $record->{'target_' . $entityModel->primaryKey})
                ->whereNull('deleted_at')
                ->first();
            // 2 - update json stats (total_rating)
            $aggregate = $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $record->{'target_' . $entityModel->primaryKey},
                'total_rating',
                $results->total_rating);

            // update aggregate to target entity
            $params = array(
                $eTypeModel->primaryKey => $ext_map_record->{'target_' . $eTypeModel->primaryKey},
                $entityModel->primaryKey => $record->{'target_' . $entityModel->primaryKey},
                // aggregate field
                $ext_map_record->aggregate_field => $aggregate->aggregate_value,
                // rating
                'rating' => $save_data['rating'],
                // total_rating
                'total_rating' => $results->total_rating,
                // total_raters
                'total_raters' => $aggregate->json_value->total_raters,
                // average_rating
                'average_rating' => floatval(number_format(
                    ($aggregate->json_value->total_rating / $aggregate->json_value->total_raters),
                    2, '.', '')),
            );
            CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);

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
            // load models
            $eTypeModel = $this->__modelPath . 'SYSEntityType';
            $eTypeModel = new $eTypeModel;
            $entityModel = $this->__modelPath . 'SYSEntity';
            $entityModel = new $entityModel;
            $extStatModel = $this->__modelPath . 'SYSExtensionStat';
            $extStatModel = new $extStatModel;
            $eTypeExtMapModel = $this->__modelPath . 'SYSEntityTypeExtensionMap';
            $eTypeExtMapModel = new $eTypeExtMapModel;

            // default vars
            $timestamp = $timestamp == "" ? date("Y-m-d H:i:s") : $timestamp;
            $request = new Request();

            // remove
            $this->remove($record->{$this->primaryKey}, $timestamp);

            // save stats
            // update json stats
            $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $record->{'target_' . $entityModel->primaryKey},
                $ext_map_record->aggregate_field,
                -1);
            // update aggregates stats
            $aggregate = $extStatModel->setAggregate(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $record->{'target_' . $entityModel->primaryKey},
                -1);

            // we need to re-calculate rating / total_raters / average_rating
            // 1 - get total ratings for target_entity_id
            $results = $this->selectRaw('COALESCE(SUM(rating),0) as total_rating')
                ->where('target_' . $entityModel->primaryKey, $record->{'target_' . $entityModel->primaryKey})
                ->whereNull('deleted_at')
                ->first();
            // 2 - update json stats (total_rating)
            $aggregate = $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $record->{'target_' . $entityModel->primaryKey},
                'total_rating',
                $results->total_rating);

            // update aggregate to target entity
            $params = array(
                $eTypeModel->primaryKey => $ext_map_record->{'target_' . $eTypeModel->primaryKey},
                $entityModel->primaryKey => $record->{'target_' . $entityModel->primaryKey},
                // aggregate field
                $ext_map_record->aggregate_field => $aggregate->aggregate_value,
                // rating
                'rating' => $record->rating,
                // total_rating
                'total_rating' => $results->total_rating,
                // total_raters
                'total_raters' => $aggregate->json_value->total_raters,
                // average_rating
                'average_rating' => floatval(number_format(
                    ($aggregate->json_value->total_rating / $aggregate->json_value->total_raters),
                    2, '.', '')),
            );
            CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);

            // remove data entity from API
            if (intval($record->{'data_' . $entityModel->primaryKey}) > 0) {
                $params = array(
                    $entityModel->primaryKey => $record->{'data_' . $entityModel->primaryKey}
                );
                CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/delete', 'POST', $params);
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
        // load models
        $eTypeExtMapModel = $this->__modelPath . 'SYSEntityTypeExtensionMap';
        $eTypeExtMapModel = new $eTypeExtMapModel;

        // record
        $record = $this->get($pk_id);
        $ext_map = $eTypeExtMapModel->get($record->{$eTypeExtMapModel->primaryKey});

        // init data
        $data = NULL;

        if ($record) {
            // load models
            $eTypeModel = $this->__modelPath . 'SYSEntityType';
            $eTypeModel = new $eTypeModel;

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
            $data = null;
            exit($ret);
        }


        return $data;
    }

}
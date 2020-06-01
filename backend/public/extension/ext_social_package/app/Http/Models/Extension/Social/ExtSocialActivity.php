<?php namespace App\Http\Models\Extension\Social;

use App\Http\Models\Base;
use App\Libraries\CustomHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use Illuminate\Http\Request;

// init models
//use App\Http\Models\Conf;

class ExtSocialActivity extends Base
{

    use SoftDeletes;
    public $table = 'ext_social_activity';
    public $timestamps = true;
    public $primaryKey = 'social_activity_id';
    protected $dates = ['deleted_at'];
    public $objectIdentifier = "social_activity";
    public $actionIdentifier = "social_activity";
    private $_extModelPath = "Extension\\Social\\";
    private $_pHook = "ExtSocialActivityModel"; // extension hook
    public $currentActorID = NULL;

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // concat extension model path
        $this->_extModelPath = $this->__modelPath . $this->_extModelPath;

        // set fields
        $this->__fields = array($this->primaryKey, 'activity_type_id', 'entity_type_extension_map_id', 'actor_entity_id', 'target_entity_id', 'data_entity_id', 'created_at', 'updated_at', 'deleted_at');
    }


    /**
     * save
     *
     * @return Response
     */
    public function xsaveLike($ext_map_record, $save_data, $timestamp = NULL)
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
                'actor_' . $entityModel->primaryKey => $save_data['actor_' . $entityModel->primaryKey]
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
        // update json stats
        $extStatModel->setJSON(
            $ext_map_record->{$eTypeExtMapModel->primaryKey},
            $save_data['target_' . $entityModel->primaryKey],
            $ext_map_record->aggregate_field,
            1);
        // update aggregates stats
        $aggregate = $extStatModel->setAggregate(
            $ext_map_record->{$eTypeExtMapModel->primaryKey},
            $save_data['target_' . $entityModel->primaryKey],
            1);

        // update aggregate to target entity
        $params = array(
            $eTypeModel->primaryKey => $ext_map_record->{'target_' . $eTypeModel->primaryKey},
            $entityModel->primaryKey => $save_data['target_' . $entityModel->primaryKey],
            $ext_map_record->aggregate_field => $aggregate->aggregate_value
        );
        CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);

        // get data
        //$data = $this->getData($id);

        return $id;
    }


    /**
     * remove
     *
     * @return Response
     */
    public function xremoveLike($ext_map_record, $id, $timestamp = NULL)
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

            // update aggregate to target entity
            $params = array(
                $eTypeModel->primaryKey => $ext_map_record->{'target_' . $eTypeModel->primaryKey},
                $entityModel->primaryKey => $record->{'target_' . $entityModel->primaryKey},
                $ext_map_record->aggregate_field => $aggregate->aggregate_value
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
            $data->has_liked = ($this->currentActorID == $record->actor_entity_id) ? 1 : 0;

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
        $data = $ret->error == 0 ? (isset($ret->data) ? $ret->data : NULL) : NULL;

        return $data;
    }


}
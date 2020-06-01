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

class ExtPackageLike extends Base
{

    use SoftDeletes;
    public $table = 'ext_package_like';
    public $timestamps = TRUE;
    public $primaryKey = 'package_like_id';
    protected $dates = ['deleted_at'];
    public $objectIdentifier = "like";
    public $actionIdentifier = "like";
    private $_pHook = "ExtPackageLikeModel"; // extension hook
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
     * ExtPackageLike constructor.
     */
    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'activity_type_id', 'entity_type_extension_map_id', 'type', 'target_entity_id', 'actor_entity_id', 'data_entity_id', 'created_at', 'updated_at', 'deleted_at');

        //load models
        $this->_entityTypeModel = new SYSEntityType();
        $this->_entityModel = new SYSEntity();
        $this->_extStatModel = new SYSExtensionStat();
        $this->_eTypeExtMapModel = new SYSEntityTypeExtensionMap();

        $this->_entityLib = new Entity();
    }


    /**
     * save
     *
     * @return Response
     */
    public function saveData($ext_map_record, $save_data, $timestamp = NULL)
    {
        // default vars
        $timestamp = $timestamp == "" ? date("Y-m-d H:i:s") : $timestamp;
        $request = new Request();

        $data_entity_id = 0;

        // if data entity exists, save data entity record
        if (intval($ext_map_record->{'data_' . $this->_entityTypeModel->primaryKey}) > 0) {
            $params = array(
                $this->_entityTypeModel->primaryKey => $ext_map_record->{'data_' . $this->_entityTypeModel->primaryKey},
                'target_' . $this->_entityModel->primaryKey => $save_data[ 'target_' . $this->_entityModel->primaryKey ],
                'actor_' . $this->_entityModel->primaryKey => $save_data[ 'actor_' . $this->_entityModel->primaryKey ],
                'status' => 1, // active
            );
            //$ret = CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities', 'POST', $params);
            $ret = (object)$this->_entityLib->doPost($params);
            unset($params);

            // set created record id
            $data_entity_id = $ret->error == 0 ?
                $ret->data->{$ret->data->identifier}->{$this->_entityModel->primaryKey} : 0;
        }

        // set data
        $save_data[ $this->_eTypeExtMapModel->primaryKey ] = $ext_map_record->{$this->_eTypeExtMapModel->primaryKey};
        $save_data['data_entity_id'] = $data_entity_id;
        $save_data['created_at'] = isset($save_data['created_at']) ? $save_data['created_at'] : $timestamp;
        // insert
        $id = $this->put($save_data);

        // save stats
        // update json stats
        if (trim($ext_map_record->aggregate_field) != "") {
            $this->_extStatModel->setJSON(
                $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
                $save_data[ 'target_' . $this->_entityModel->primaryKey ],
                $ext_map_record->aggregate_field,
                1);
        }

        // update aggregates stats
        $aggregate = $this->_extStatModel->setAggregate(
            $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
            $save_data[ 'target_' . $this->_entityModel->primaryKey ],
            1);

        // update aggregate to target entity
        $params = array(
            $this->_entityTypeModel->primaryKey => $ext_map_record->{'target_' . $this->_entityTypeModel->primaryKey},
            $this->_entityModel->primaryKey => $save_data[ 'target_' . $this->_entityModel->primaryKey ],
            $ext_map_record->aggregate_field => $aggregate->aggregate_value,
            'status' => 1, // active
        );
        //CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);

        $this->_entityLib->doUpdate($params);

        // get data
        //$data = $this->getData($id);

        return $id;
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

            // update aggregate to target entity
            $params = array(
                $this->_entityTypeModel->primaryKey => $ext_map_record->{'target_' . $this->_entityTypeModel->primaryKey},
                $this->_entityModel->primaryKey => $record->{'target_' . $this->_entityModel->primaryKey},
                $ext_map_record->aggregate_field => $aggregate->aggregate_value,
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

            $data->has_liked = $data->is_favorite = 0;
            if ($data->type == 'private') {
                $data->is_favorite = ($this->currentActorID == $record->actor_entity_id) ? 1 : 0;
            } else {
                $data->has_liked = ($this->currentActorID == $record->actor_entity_id) ? 1 : 0;
            }

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
        /*// init data
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

}
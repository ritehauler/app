<?php namespace App\Http\Models\Extension\Social;

use App\Http\Models\Base;
use App\Libraries\CustomHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use Illuminate\Http\Request;

// init models
//use App\Http\Models\Conf;

class EXTSocialFriend extends Base
{

    use SoftDeletes;
    public $table = '{plugin_identifier}';
    public $timestamps = true;
    public $primaryKey = 'social_friend_id';
    protected $dates = ['deleted_at'];
    public $objectIdentifier = "friend";
    public $actionIdentifier = "friend";
    private $_pHook = "ExtSocialFriendModel"; // extension hook
    public $currentActorID = NULL;


    /**
     * construct
     *
     */
    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'entity_type_extension_map_id', 'target_entity_id', 'actor_entity_id', 'data_entity_id', 'request_status', 'created_at', 'updated_at', 'deleted_at');
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
                'request_status' => $save_data['request_status']
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

        // save stats (target entity)
        // - update aggregate field if exists
        if (trim($ext_map_record->aggregate_field) != '') {
            // update json stats
            $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $save_data['target_' . $entityModel->primaryKey],
                $ext_map_record->aggregate_field,
                1);
        }
        // - update aggregates stats (aggregates)
        $extStatModel->setAggregate(
            $ext_map_record->{$eTypeExtMapModel->primaryKey},
            $save_data['target_' . $entityModel->primaryKey],
            1);

        // - update json stats (sent request)
        $aggregate = $extStatModel->setJSON(
            $ext_map_record->{$eTypeExtMapModel->primaryKey},
            $save_data['target_' . $entityModel->primaryKey],
            'friend_request_received',
            1);

        // update aggregate to target entity
        $params = array(
            $eTypeModel->primaryKey => $ext_map_record->{'target_' . $eTypeModel->primaryKey},
            $entityModel->primaryKey => $save_data['target_' . $entityModel->primaryKey],
            'friend_request_received' => $aggregate->json_value->friend_request_received
        );
        CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);

        // save stats (actor entity)
        // - update json stats (received request)
        $aggregate = $extStatModel->setJSON(
            $ext_map_record->{$eTypeExtMapModel->primaryKey},
            $save_data['actor_' . $entityModel->primaryKey],
            'friend_request_sent',
            1);

        // update json stats (sent request)
        $params = array(
            $eTypeModel->primaryKey => $ext_map_record->{'actor_' . $eTypeModel->primaryKey},
            $entityModel->primaryKey => $save_data['actor_' . $entityModel->primaryKey],
            'friend_request_sent' => $aggregate->json_value->friend_request_sent
        );
        CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);

        // get data
        //$data = $this->getData($id);

        return $id;
    }


    /**
     * cancel request
     *
     * @return Response
     */
    public function cancelRequest($ext_map_record, $id, $save_data, $timestamp = NULL)
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

        if ($record) {
            $data_entity_id = $record->{'data_' . $entityModel->primaryKey};

            // update in entity record
            if ($data_entity_id > 0) {
                $params = array(
                    $eTypeModel->primaryKey => $ext_map_record->{'data_' . $eTypeModel->primaryKey},
                    $entityModel->primaryKey => $data_entity_id,
                    'target_' . $entityModel->primaryKey => $record->{'target_' . $entityModel->primaryKey},
                    'actor_' . $entityModel->primaryKey => $record->{'actor_' . $entityModel->primaryKey},
                    'request_status' => $save_data['request_status'],
                );
                $ret = CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);
                unset($params);

                //$ret->error == 0 ?
                //$ret->data->{$ret->data->identifier}->{$entityModel->primaryKey} : 0;
            }

            // set data
            $save_data['updated_at'] = isset($save_data['updated_at']) ? $save_data['updated_at'] : $timestamp;
            // update
            $this->set($id, $save_data);

            // save stats (target entity)
            // - update aggregates stats (aggregates)
            $extStatModel->setAggregate(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $save_data['target_' . $entityModel->primaryKey],
                -1);

            // - update json stats (received request)
            $aggregate = $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $save_data['target_' . $entityModel->primaryKey],
                'friend_request_received',
                -1);

            // - update json stats (received request)
            $params = array(
                $eTypeModel->primaryKey => $ext_map_record->{'target_' . $eTypeModel->primaryKey},
                $entityModel->primaryKey => $save_data['target_' . $entityModel->primaryKey],
                'friend_request_received' => $aggregate->json_value->friend_request_received
            );
            CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);

            // save stats (actor entity)
            // - update json stats (sent request)
            $aggregate = $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $save_data['actor_' . $entityModel->primaryKey],
                'friend_request_sent',
                -1);

            // update aggregate to target entity
            $params = array(
                $eTypeModel->primaryKey => $ext_map_record->{'actor_' . $eTypeModel->primaryKey},
                $entityModel->primaryKey => $save_data['actor_' . $entityModel->primaryKey],
                'friend_request_sent' => $aggregate->json_value->friend_request_sent
            );
            CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);

            // get data
            $data = $this->getData($id);
        }


        return $data;
    }


    /**
     * accept request
     *
     * @return Response
     */
    public function acceptRequest($ext_map_record, $id, $save_data, $timestamp = NULL)
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

        if ($record) {
            $data_entity_id = $record->{'data_' . $entityModel->primaryKey};

            // update in entity record
            if ($data_entity_id > 0) {
                $params = array(
                    $eTypeModel->primaryKey => $ext_map_record->{'data_' . $eTypeModel->primaryKey},
                    $entityModel->primaryKey => $data_entity_id,
                    'target_' . $entityModel->primaryKey => $record->{'target_' . $entityModel->primaryKey},
                    'actor_' . $entityModel->primaryKey => $record->{'actor_' . $entityModel->primaryKey},
                    'request_status' => $save_data['request_status'],
                );
                $ret = CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);
                unset($params);

                //$ret->error == 0 ?
                //$ret->data->{$ret->data->identifier}->{$entityModel->primaryKey} : 0;
            }

            // set data
            $save_data['updated_at'] = isset($save_data['updated_at']) ? $save_data['updated_at'] : $timestamp;
            // update
            $this->set($id, $save_data);

            // save stats (target entity)
            // - update aggregates stats (aggregates)
            $extStatModel->setAggregate(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $save_data['target_' . $entityModel->primaryKey],
                -1);

            // - update json stats (received request)
            $aggregate = $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $save_data['target_' . $entityModel->primaryKey],
                'friend_request_received',
                -1);
            // - update json stats (accepted request)
            $aggregate = $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $save_data['target_' . $entityModel->primaryKey],
                'friend_request_accepted',
                1);

            // - update json stats (received request)
            $params = array(
                $eTypeModel->primaryKey => $ext_map_record->{'target_' . $eTypeModel->primaryKey},
                $entityModel->primaryKey => $save_data['target_' . $entityModel->primaryKey],
                'friend_request_received' => $aggregate->json_value->friend_request_received,
                'friend_request_accepted' => $aggregate->json_value->friend_request_accepted
            );
            CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);

            // save stats (actor entity)
            // - update json stats (sent request)
            $aggregate = $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $save_data['actor_' . $entityModel->primaryKey],
                'friend_request_sent',
                -1);

            $aggregate = $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $save_data['actor_' . $entityModel->primaryKey],
                'friend_request_accepted',
                1);

            // update aggregate to target entity
            $params = array(
                $eTypeModel->primaryKey => $ext_map_record->{'actor_' . $eTypeModel->primaryKey},
                $entityModel->primaryKey => $save_data['actor_' . $entityModel->primaryKey],
                'friend_request_sent' => $aggregate->json_value->friend_request_sent,
                'friend_request_accepted' => $aggregate->json_value->friend_request_accepted
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

            // save stats (target entity)
            // - update aggregate field if exists
            if (trim($ext_map_record->aggregate_field) != '') {
                // update json stats
                $extStatModel->setJSON(
                    $ext_map_record->{$eTypeExtMapModel->primaryKey},
                    $record->{'target_' . $entityModel->primaryKey},
                    $ext_map_record->aggregate_field,
                    -1);
            }
            // - update aggregates stats (aggregates)
            $extStatModel->setAggregate(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $record->{'target_' . $entityModel->primaryKey},
                -1);

            // - update json stats (sent request)
            $aggregate = $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $record->{'target_' . $entityModel->primaryKey},
                'friend_request_accepted',
                -1);

            // update aggregate to target entity
            $params = array(
                $eTypeModel->primaryKey => $ext_map_record->{'target_' . $eTypeModel->primaryKey},
                $entityModel->primaryKey => $record->{'target_' . $entityModel->primaryKey},
                'friend_request_accepted' => $aggregate->json_value->friend_request_accepted
            );
            CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);

            // save stats (actor entity)
            // - update json stats (received request)
            $aggregate = $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $record->{'actor_' . $entityModel->primaryKey},
                'friend_request_accepted',
                -1);

            // update json stats (sent request)
            $params = array(
                $eTypeModel->primaryKey => $ext_map_record->{'actor_' . $eTypeModel->primaryKey},
                $entityModel->primaryKey => $record->{'actor_' . $entityModel->primaryKey},
                'friend_request_accepted' => $aggregate->json_value->friend_request_accepted
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
     * reject request
     *
     * @return Response
     */
    public function rejectRequest($ext_map_record, $id, $save_data, $timestamp = NULL)
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

        if ($record) {
            $data_entity_id = $record->{'data_' . $entityModel->primaryKey};

            // update in entity record
            if ($data_entity_id > 0) {
                $params = array(
                    $eTypeModel->primaryKey => $ext_map_record->{'data_' . $eTypeModel->primaryKey},
                    $entityModel->primaryKey => $data_entity_id,
                    'target_' . $entityModel->primaryKey => $record->{'target_' . $entityModel->primaryKey},
                    'actor_' . $entityModel->primaryKey => $record->{'actor_' . $entityModel->primaryKey},
                    'request_status' => $save_data['request_status'],
                );
                $ret = CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);
                unset($params);

                //$ret->error == 0 ?
                //$ret->data->{$ret->data->identifier}->{$entityModel->primaryKey} : 0;
            }

            // set data
            $save_data['updated_at'] = isset($save_data['updated_at']) ? $save_data['updated_at'] : $timestamp;
            // update
            $this->set($id, $save_data);

            // save stats (target entity)
            // - update aggregates stats (aggregates)
            $extStatModel->setAggregate(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $save_data['target_' . $entityModel->primaryKey],
                -1);

            // - update json stats (received request)
            $aggregate = $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $save_data['target_' . $entityModel->primaryKey],
                'friend_request_received',
                -1);

            // - update json stats (received request)
            $params = array(
                $eTypeModel->primaryKey => $ext_map_record->{'target_' . $eTypeModel->primaryKey},
                $entityModel->primaryKey => $save_data['target_' . $entityModel->primaryKey],
                'friend_request_received' => $aggregate->json_value->friend_request_received
            );
            CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);

            // save stats (actor entity)
            // - update json stats (sent request)
            $aggregate = $extStatModel->setJSON(
                $ext_map_record->{$eTypeExtMapModel->primaryKey},
                $save_data['actor_' . $entityModel->primaryKey],
                'friend_request_sent',
                -1);

            // update aggregate to target entity
            $params = array(
                $eTypeModel->primaryKey => $ext_map_record->{'actor_' . $eTypeModel->primaryKey},
                $entityModel->primaryKey => $save_data['actor_' . $entityModel->primaryKey],
                'friend_request_sent' => $aggregate->json_value->friend_request_sent
            );
            CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);

            // get data
            $data = $this->getData($id);
        }


        return $data;
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
        $record = json_decode(json_encode($this->get($pk_id)));
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
                $identifier = $eData->identifier == 'entity' ? 'target_entity' : 'receiver'; // fix for non-mobile
                $data->{$identifier} = $eData->{$eData->identifier};
            }

            // attach actor entity data
            $eData = $this->_getEntityDataFromAPI($ext_map->actor_entity_type_id, $record->actor_entity_id);
            if ($eData) {
                $identifier = $eData->identifier == 'entity' ? 'actor_entity' : 'sender'; // fix for non-mobile
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
            $data->is_friend = 0;
            // check friends
            if ($record->request_status == 'accepted' && $record->deleted_at !== NULL) {
                if ($record->target_entity_id == $this->currentActorID || $record->actor_entity_id == $this->currentActorID) {
                    $data->is_friend = 1;
                }
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
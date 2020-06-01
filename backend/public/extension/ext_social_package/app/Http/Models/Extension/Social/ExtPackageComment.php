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

class ExtPackageComment extends Base
{

    use SoftDeletes;
    public $table = 'ext_package_comment';
    public $timestamps = TRUE;
    public $primaryKey = 'package_comment_id';
    protected $dates = ['deleted_at'];
    public $objectIdentifier = "comment";
    public $actionIdentifier = "comment";
    private $_pHook = "ExtPackageCommentModel"; // extension hook
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
     * ExtPackageComment constructor.
     */
    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'activity_type_id', 'entity_type_extension_map_id', 'parent_id', 'target_entity_id', 'actor_entity_id', 'data_entity_id', 'content', 'json_data', 'created_at', 'updated_at', 'deleted_at');

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
                'content' => $save_data['content'],
                'json_data' => $save_data['json_data'],
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
        $save_data['parent_id'] = isset($save_data['parent_id']) ? intval($save_data['parent_id']) : 0;
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

        // comment / replies
        if (intval($save_data['parent_id']) > 0) {
            // update json stats (total_raters)
            $this->_extStatModel->setJSON(
                $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
                $save_data[ 'target_' . $this->_entityModel->primaryKey ],
                'ext_total_replies',
                1); // increment 1
        } else {
            // update json stats (total_raters)
            $this->_extStatModel->setJSON(
                $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
                $save_data[ 'target_' . $this->_entityModel->primaryKey ],
                'ext_total_comments',
                1); // increment 1
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
            //$ext_map_record->aggregate_field => $aggregate->aggregate_value,
            // total_comments
            'ext_total_comments' => isset($aggregate->json_value->total_comments) ? $aggregate->json_value->total_comments : 0,
            // rating
            'ext_total_replies' => isset($aggregate->json_value->total_replies) ? $aggregate->json_value->total_replies : 0,
            'status' => 1, // active
        );
        //CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);
        $ret = $this->_entityLib->doUpdate($params);

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

        if ($record) {
            $data_entity_id = $record->{'data_' . $this->_entityModel->primaryKey};

            // update in entity record
            if ($data_entity_id > 0) {
                $params = array(
                    $this->_entityTypeModel->primaryKey => $ext_map_record->{'data_' . $this->_entityTypeModel->primaryKey},
                    $this->_entityModel->primaryKey => $data_entity_id,
                    'target_' . $this->_entityModel->primaryKey => $record->{'target_' . $this->_entityModel->primaryKey},
                    'actor_' . $this->_entityModel->primaryKey => $record->{'actor_' . $this->_entityModel->primaryKey},
                    'content' => $save_data['content'],
                    'json_data' => $save_data['json_data'],
                    'status' => 1, // active

                );
                //$ret = CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);
                $ret = $this->_entityLib->doUpdate($params);
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

            /*// save stats
            // update json stats
            $this->_extStatModel->setJSON(
                $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
                $save_data['target_' . $this->_entityModel->primaryKey],
                $ext_map_record->aggregate_field,
                1);
            // update aggregates stats
            $aggregate = $this->_extStatModel->setAggregate(
                $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
                $save_data['target_' . $this->_entityModel->primaryKey],
                1);

            // update aggregate to target entity
            $params = array(
                $this->_entityTypeModel->primaryKey => $ext_map_record->{'target_' . $this->_entityTypeModel->primaryKey},
                $this->_entityModel->primaryKey => $save_data['target_' . $this->_entityModel->primaryKey],
                $ext_map_record->aggregate_field => $aggregate->aggregate_value
            );
            CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);*/

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

            // comment / replies
            if (intval($record->parent_id) > 0) {
                // update json stats (total_raters)
                $this->_extStatModel->setJSON(
                    $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
                    $record->{'target_' . $this->_entityModel->primaryKey},
                    'ext_total_replies',
                    -1); // increment 1
            } else {
                // update json stats (total_raters)
                $this->_extStatModel->setJSON(
                    $ext_map_record->{$this->_eTypeExtMapModel->primaryKey},
                    $record->{'target_' . $this->_entityModel->primaryKey},
                    'ext_total_comments',
                    -1); // increment 1
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
                //$ext_map_record->aggregate_field => $aggregate->aggregate_value,
                // total_comments
                'ext_total_comments' => isset($aggregate->json_value->total_comments) ? $aggregate->json_value->total_comments : 0,
                // rating
                'ext_total_replies' => isset($aggregate->json_value->total_replies) ? $aggregate->json_value->total_replies : 0,
                'status' => 1, // active

            );
            // CustomHelper::appCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $params);
            $ret = $this->_entityLib->doUpdate($params);

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
            if (intval($data->parent_id) > 0) {
                $data->has_replied = ($this->currentActorID == $record->actor_entity_id) ? 1 : 0;
                //$data->has_commented = 0; // mobile team may ask for that
            } else {
                //$data->has_replied = 0; // mobile team may ask for that
                $data->has_commented = ($this->currentActorID == $record->actor_entity_id) ? 1 : 0;
            }

            // parent data
            $data->parent = intval($data->parent_id) > 0 ? $this->getData($data->parent_id) : NULL;

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
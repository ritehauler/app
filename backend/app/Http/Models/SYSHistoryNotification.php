<?php namespace App\Http\Models;

use App\Http\Hooks\EntityNotification;
use App\Libraries\CustomHelper;
use App\Libraries\System\Entity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use Illuminate\Http\Request;

// init models
//use App\Http\Models\Conf;

class SYSHistoryNotification extends Base
{

    use SoftDeletes;
    public $table = 'sys_history_notification';
    public $timestamps = TRUE;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'history_notification_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = [];
        $this->_entity_search_url = \URL::to(DIR_API) . 'entities/listing';

        // set fields
        $this->__fields = [$this->primaryKey, 'history_identifier', "plugin_identifier", 'lang_identifier', 'type', 'for', 'title', 'body', 'body', 'hint', 'wildcards', 'replacers', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @param $identifier
     * @param $type
     * @param $for
     * @return bool
     */
    public function getDataByIdentifierAndType($identifier, $type, $for)
    {
        $row = $this->where('history_identifier', '=', "$identifier")
            ->where('type', '=', "$type")
            ->where('for', '=', "$for")
            ->whereNull("deleted_at")
            ->get();

        // print_r($row); exit;
        return isset($row[0]) ? $row[0] : FALSE;
    }

    /**
     * Send Push Notification
     * @param $history
     */
    function xsendPush($history)
    {
        // if history exists

        if (is_object($history)) {
            $history_data = $history;
        } else {
            $history_model = new SYSHistory();
            $history_data = $history_model->getBy("identifier", $history, TRUE);
        }

        if ($history_data) {

            // init models
            $entity_lib = new Entity();
            $notification_model = new Notification;
            $entity_history_model = new SYSEntityHistory();
            $history_notification_model = new SYSHistoryNotification();

            //Get entity history Data
            $entity_history = $entity_history_model->getBy('history_id', $history_data->history_id, TRUE);
            //print_r($entity_history); exit;

            $actor_entity = FALSE;
            $target_entity = FALSE;

            //Get actor entity Data
            $actor_params['entity_type_id'] = $entity_history->actor_entity_type_id;
            $actor_params['entity_id'] = $entity_history->actor_entity_id;
            //$actor_params['mobile_json'] = 1;
            $actor_entity_data = $entity_lib->apiGet($actor_params);
            $actor_entity_data = json_decode(json_encode($actor_entity_data));

            if ($actor_entity_data->error == 0 && isset($actor_entity_data->data->entity)) {
                $actor_entity = $actor_entity_data->data->entity;
            }

            //Get Target entity Data
            $target_params['entity_type_id'] = $entity_history->entity_type_id;
            $target_params['entity_id'] = $entity_history->entity_id;
            //$target_params['mobile_json'] = 1;
            $target_entity_data = $entity_lib->apiGet($target_params);
            $target_entity_data = json_decode(json_encode($target_entity_data));

            if ($target_entity_data->error == 0 && isset($target_entity_data->data->entity)) {
                $target_entity = $target_entity_data->data->entity;
            }

            //Get notification template by identifier for actor
            if ($history_data->notify_entity > 0) {

                $actor_history_notification = $history_notification_model->getDataByIdentifierAndType($history_data->identifier, 'push', 'to_entity');

                //  print_r($actor_history_notification); exit;
                if ($actor_history_notification) {

                    // - get actor device type & tokens
                    if (isset($actor_entity->auth->device_token) && in_array($actor_entity->auth->device_type, ["ios", "android"]) && $actor_entity->auth->device_token != "") {

                        CustomHelper::hookData(
                            'EntityNotification',
                            'init',
                            new Request(),
                            [
                                'history_data' => $history_data,
                                'template' => $actor_history_notification,
                                'actor_entity' => $actor_entity,
                                'target_entity' => $target_entity
                            ]
                        );

                        /*//check if function exist for notification
                        $entity_notification = new EntityNotification();
                        $func = CustomHelper::convertToCamel($history_data->identifier . '_notification');

                        if (method_exists($entity_notification, "$func")) {
                            $notification_data = $entity_notification->$func($actor_history_notification, $actor_entity, $target_entity);
                           // echo "<pre>"; print_r($notification_data); exit;
                            //send Notification
                            $ret = $notification_model->pn_android($actor_entity->auth->device_token, $notification_data);

                        }*/

                    }
                }
            }

            //Get notification template by identifier for target
            if ($history_data->notify_target_entity > 0) {
                $actor_history_notification = $history_notification_model->getDataByIdentifierAndType($history_data->identitifer, 'push', 'to_target_entity');

                if ($actor_history_notification) {

                    // - get target entity device type & tokens
                    if (isset($target_entity->auth->device_token) && in_array($target_entity->auth->device_type, ["ios", "android"]) && $target_entity->auth->device_token != "") {

                        $entity_notification = new EntityNotification();
                        $func = CustomHelper::convertToCamel($history_data->identitifer . '_notification');

                        if (method_exists($entity_notification, "$func")) {
                            $notification_data = $entity_notification->$func($actor_history_notification, $actor_entity, $target_entity);
                            $ret = $notification_model->pn_android($actor_entity->auth->device_token, $notification_data);
                        }

                    }
                }
            }


        }

        // return
        return;
    }

    /**
     * Send Push Notification
     * @param $history
     */
    function sendPush($history,$history_notification_id)
    {
        // if history exists

        if (is_object($history)) {
            $history_data = $history;
        } else {
            $history_model = new SYSHistory();
            $history_data = $history_model->getBy("identifier", $history, TRUE);
        }

        if ($history_data) {

        }
            // init models
            $entity_history_model = new SYSEntityHistory();

            //Get entity history Data
            $entity_history = $entity_history_model->get($history_notification_id);
            CustomHelper::hookData(
                'EntityNotification',
                'init',
                null,
                [
                    'history_data' => $history_data,
                    'entity_history' => $entity_history
                ]
            );
            // return
            return;
        }


        /**
         * Send Email Notification
         * @param object $history
         * @param integer $user_id
         * @param integer $target_user_id
         * @param integer $entity_history_id
         * @return void
         */
        function sendEmail($history, $user_id, $target_user_id = 0, $entity_history_id = 0)
        {
            // init models
            //$user_model = new User;

            if (is_object($history) && isset($history->history_id)) {

            }

            // return
            return;
        }

    }
<?php namespace App\Http\Models;

use App\Libraries\EntityNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSEntityHistory extends Base
{

    use SoftDeletes;
    public $table = 'sys_entity_history';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'entity_history_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'history_id', "entity_type_id", 'entity_id', 'actor_entity_type_id', 'actor_entity_id', 'against_entity_type_id', 'against_entity_id', 'extension_ref_table', 'extension_ref_id', 'tracking_id', 'is_read', 'is_archive', 'request_params', 'notification_message','created_at', 'updated_at', 'deleted_at');
    }


    /**
     * Save entity History
     * @param string $entity_type
     * @param integer $entity_id
     * @param string $identifier
     * @param array $other_data
     * @return integer insert_id
     */
    function logHistory($history_identifier, $entity_id, $actor_entity_id, $other_data = array(), $timestamp = "", $request_params = null)
    {
        // init models
        $history_model = new SYSHistory();
        //$history_notification_model = $this->__modelPath . "SYSHistoryNotification";
        $history_notification_model = new SYSHistoryNotification();

        // default
        $insert_id = NULL;
        $timestamp = $timestamp == "" ? date("Y-m-d H:i:s") : $timestamp;

        // get history identifier
        $identifier_data = $history_model->getBy("identifier", $history_identifier, true);
        if ($identifier_data) {

            //if actor entity id is empty then check if request if from customer or backend session user
            if(!$actor_entity_id){

                $actor_entity_id = 1;

                if(isset($request_params->login_entity_id) && !empty($request_params->login_entity_id)){
                    $actor_entity_id = $request_params->login_entity_id;
                   // $other_data['actor_entity_type_id'] = 'customer';
                }
                else{
                    $sys_entity_auth_model = new SYSEntityAuth();
                    $entity =  $sys_entity_auth_model->getSessionEntity();
                    if($entity){
                        $actor_entity_id = $entity->entity_id;
                        $other_data['actor_entity_type_id'] = $entity->entity_type_id;
                    }
                }

                if(isset($request_params->login_entity_type_id) && !empty($request_params->login_entity_type_id)){
                    $other_data['actor_entity_type_id'] = $request_params->login_entity_type_id;
                }
            }


            //Get entity type id
            $entity_type_model = new SYSEntityType();

            if(isset($other_data['entity_type_id'])){
                if(!is_numeric($other_data['entity_type_id']))
                    $entity_type_id = $entity_type_model->getIdByIdentifier($other_data['entity_type_id']);
                else
                    $entity_type_id = $other_data['entity_type_id'];
            }

            if(isset($other_data['actor_entity_type_id'])){
                if(!is_numeric($other_data['actor_entity_type_id']))
                    $actor_entity_type_id = $entity_type_model->getIdByIdentifier($other_data['actor_entity_type_id']);
                else
                    $actor_entity_type_id = $other_data['actor_entity_type_id'];
            }

            // save
            $save_data[$history_model->primaryKey] = $identifier_data->{$history_model->primaryKey};
            //$save_data["entity_type_id"] = $entity_type_id;
            $save_data["entity_id"] = $entity_id;
            //$save_data["actor_entity_type_id"] = $actor_entity_type_id;
            $save_data["actor_entity_id"] = $actor_entity_id;
            //$save_data["against_entity_type_id"] = $against_entity_type_id;
            $save_data["against_entity_id"] = isset($other_data["against_entity_id"]) ? $other_data["against_entity_id"] : 0;
            $save_data["against_entity_type_id"] = isset($other_data["against_entity_type_id"]) ? $other_data["against_entity_type_id"] : 0;
            // built-in defaults
            if(isset($other_data["extension_ref_table"])) {
                $save_data["extension_ref_table"] = $other_data["extension_ref_table"];
            }
            if(isset($other_data["extension_ref_id"])) {
                $save_data["extension_ref_id"] = $other_data["extension_ref_id"];
            }
            if(isset($other_data["is_archive"])) {
                $save_data["is_archive"] = $other_data["is_archive"];
            }
            if(isset($entity_type_id)) {
                $save_data["entity_type_id"] = $entity_type_id;
            }

            if(isset($actor_entity_type_id)) {
                $save_data["actor_entity_type_id"] = $actor_entity_type_id;
            }

            $save_data["request_params"] = $request_params ? json_encode($request_params) : NULL;
            $save_data["created_at"] = $timestamp;
            // save

            $insert_id = $this->put($save_data);
            //$insert_id = false;

            // process notification
            $send_notification = $identifier_data->notify_entity > 0 || $identifier_data->notify_target_entity > 0;

            if ($send_notification) {
                // send push notification
                if(preg_match("@push@",$identifier_data->notification_type)) {
                    $history_notification_model->sendPush($identifier_data,$insert_id);
                }
                // send email notification
                if(preg_match("@email@",$identifier_data->notification_type)) {
                    $history_notification_model->sendEmail($identifier_data, $actor_entity_type_id,$actor_entity_id,$entity_id, $actor_entity_id, $insert_id);
                }
                // send push notification
                if(preg_match("@system@",$identifier_data->notification_type)) {
                    $entity_notification_lib = new EntityNotification();
                    $entity_notification_lib->systemNotify($identifier_data,$insert_id);
                }
            }
        }

        // return
        return $insert_id;
    }


}
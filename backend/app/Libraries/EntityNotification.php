<?php

/**
 * Description: this file is to create to send system notification
 * Author: Samreen <samreen.quyyum@cubixlabs.com>
 * Date: 21-03-2018
 * Time: 06:30 PM
 * Copyright: CubixLabs
 */

namespace App\Libraries;

use App\Http\Models\Notification;
use App\Http\Models\SYSEntity;
use App\Http\Models\SYSEntityAuth;
use App\Http\Models\SYSEntityHistory;
use App\Http\Models\SYSEntityNotification;
use App\Http\Models\SYSEntityType;
use App\Http\Models\SYSHistory;
use App\Http\Models\SYSModule;
use App\Http\Models\SYSPermission;
use App\Http\Models\SYSRole;
use App\Http\Models\SYSRolePermission;
use App\Http\Models\SYSTableFlat;
use App\Libraries\System\Entity;

/**
 * Class EntityNotification
 * @package App\Libraries
 */
Class EntityNotification
{
    /**
     * @var SYSEntityNotification|string
     */
    private $_model =  '';

    /**
     * EntityNotification constructor.
     */
    public function __construct()
    {
        $this->_model = new SYSEntityNotification();

    }

    /**
     * Get the entity list whose have rights of module
     * then save system notification to send
     * @param $history_data
     * @param $history_notification_id
     */
    public function systemNotify($history_data,$history_notification_id)
    {
        try {
            if ($history_data) {

                $module = false;
                //Get Entity History
                $entity_history_model = new SYSEntityHistory();
                $entity_history = $entity_history_model->get($history_notification_id);

                //if entity type id is exist
                if($entity_history->entity_type_id > 0){
                    $target_module = $this->_getEntityTypeData($entity_history->entity_type_id);
                    $module = $target_module->identifier;
                }
                else{
                    //if module is other than entity type
                    if(!empty($entity_history->extension_ref_table) && !empty($entity_history->entity_id))
                    $module = $this->_getTargetModule($entity_history->extension_ref_table,$entity_history->entity_id);
                }

                $action = $this->_getAction($history_data->identifier);

                if ($module && $action) {
                    //get module if any entity type has customized slug
                    $module = $this->_getCustomizedModuleSlug($entity_history->entity_id,$module);
                    //Get entity ids to send notification
                    $entity_list = $this->_getAllowedEntities($module, $action,$entity_history->actor_entity_id);

                    //save entity notification
                    if($entity_list && count($entity_list) > 0)
                    $this->_save($entity_history->entity_history_id, $entity_list);
                }

            }
        }
          catch (\Exception $e) {
              // echo $e->getTraceAsString(); exit;
              // $return['message'] = $e->getMessage();
              //  $return['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine(); //" : Stack " . $e->getTraceAsString();
          }

    }


    /**
     * @param $target_entity_type_id
     * @return bool
     */
    private function _getEntityTypeData($target_entity_type_id)
    {
        $entity_type_model = new SYSEntityType();
         $target_entity_type = $entity_type_model->get($target_entity_type_id);
            if($target_entity_type){
                return $target_entity_type;

        }

        return false;
    }


    /**
     * Get entity IDS which has access of module on permission
     * @param $module
     * @param $action
     * @param $actor_entity_id
     * @return bool
     */
    private function _getAllowedEntities($module,$action,$actor_entity_id)
    {
        $role_permission_model = new SYSRolePermission();

        //check if entity type is super admin then get the all list of entities to notify except super admin
        if($actor_entity_id == 1){
            return $role_permission_model->entityListByModulePermission($module,$action,$actor_entity_id);
        }
        else{
            //else first get the all list of entities and then super admin data to notify all except actor entity
            $entity = array();
            $entity_list = $role_permission_model->entityListByModulePermission($module,$action,$actor_entity_id);


            //if module and permission then get the super admin user b/c every time super admin will be notify
            if($module && $action){
                $admin_entity_id = 1;

                $module_model = new SYSModule();
                $module_raw = $module_model->where('slug',$module)->first();

               // echo "<pre>"; print_r($module_raw); exit;
                $permission_model = new SYSPermission();
               $permission_raw = $permission_model->where('identifier','=',$action)->first();

                if(isset($module_raw->module_id) && isset($permission_raw->permission_id)){

                    $entity_model = new SYSEntity();
                    $admin_entity_raw = $entity_model->get($admin_entity_id);

                    if($admin_entity_raw){
                        $admin_entity_raw->module_id = $module_raw->module_id;
                        $admin_entity_raw->permission_id = $permission_raw->permission_id;

                        $entity[] = $admin_entity_raw;
                    }
                }

            }


            if(is_array($entity_list)){
                foreach($entity_list as $list){
                    $entity[] = $list;
                }
            }


            return $entity;
        }

    }

    /**
     * Add entity notification data
     * @param $entity_history_id
     * @param $entity_list
     */
    private function _save($entity_history_id,$entity_list)
    {
        if($entity_list){
            if(count($entity_list) > 0){

                foreach($entity_list as $entity){

                   $params['entity_history_id'] = $entity_history_id;
                    $params['against_entity_type_id'] =  $entity->entity_type_id;
                    $params['against_entity_id'] =  $entity->entity_id;
                    $params['is_read'] = 0;
                    $params['module_id'] =  $entity->module_id;
                    $params['permission_id'] =  $entity->permission_id;
                    $params['created_at'] =  date('Y-m-d h:i:s');
                    $this->_model->put($params);
                }
            }
        }
    }

    /**
     * @param $history_identifier
     * @return mixed
     */
    private function _getAction($history_identifier){

        $action_raw = explode('_',$history_identifier);
         return $action_raw[1];
    }

    /**
     * The module which has different slug to categorize listing
     * so get the slug which is used in module
     * @param $entity_id
     * @param $slug
     * @return string
     */
    private function _getCustomizedModuleSlug($entity_id,$slug)
    {
        //if module is product then get the product, recipe and bundle customized slug
        if($slug == 'product'){

            $where_condition = " entity_id = $entity_id";
            $flat_table_model = new SYSTableFlat($slug);
            $return = $flat_table_model->getColumnByWhere($where_condition,'is_other');

             if(isset($return->is_other)){
                 $product_helper_lib = new ProductHelper();
                 if($module = $product_helper_lib->getRequestedIdentifierByType($return->is_other)){
                     return $module;
                 }
             }
        }

        //if slug is customer then check user status if user status is blocked then
        //get the slug of black list customer
        elseif ($slug == 'customer'){

            $where_condition = " entity_id = $entity_id";
            $flat_table_model = new SYSTableFlat($slug);
            $return = $flat_table_model->getColumnByWhere($where_condition,'user_status');

            if(isset($return->user_status)){
                $customer_helper_lib = new EntityCustomer();
                return $customer_helper_lib->getRequestedIdentifierByStatus($return->user_status);
            }
        }
        return $slug;
    }

    /**
     * Get notification count
     * @return bool
     */
    public function getTotalCount()
    {
        //Get the session user then get the notification count
        $sys_entity_auth_model = new SYSEntityAuth();
        $entity =  $sys_entity_auth_model->getSessionEntity();
        if($entity){
           return $this->_model->getTotalCount($entity->entity_type_id,$entity->entity_id);
        }
    }

    /**
     * Get list of notifications
     * @param $request_params
     * @return array
     */
    public function getList($request_params)
    {
        $request_params = is_object($request_params) ? $request_params : (object)$request_params;

        try{
            //Get Session user to get associative list of notifications
            $sys_entity_auth_model = new SYSEntityAuth();
            $entity =  $sys_entity_auth_model->getSessionEntity();
            if($entity){
                $notification_list = $this->_model->getList($entity->entity_type_id,$entity->entity_id,$request_params);
                return $this->_notifyMessageRecords($notification_list);
            }
        }
        catch (\Exception $e) {
            // echo $e->getTraceAsString(); exit;
            // $return['message'] = $e->getMessage();
            //  $return['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine(); //" : Stack " . $e->getTraceAsString();
        }
    }

    /**
     * Get the data which is needed to display in notification message
     * @param $notification_list
     * @return array
     */
    private function _notifyMessageRecords($notification_list)
    {
        $return = array();
        if($notification_list){
            if(count($notification_list) > 0){


                foreach($notification_list as $notification){

                    //if target entity type is not out of entity module
                    if(!in_array($notification->module,array('role','group','category'))){

                        //Get Target entity data
                        $target_entity_type = $this->_getEntityTypeData($notification->entity_type_id);

                        $target_entity_type_title = $this->_customizeModule($notification->module);
                        $target_entity_type_identifier = $target_entity_type->identifier;

                        //If entity is part of entity type then get the title of entity type
                        if(!$target_entity_type_title){
                            $target_entity_type_title = $target_entity_type->title;
                            $target_entity_type_identifier = ucwords($notification->module);
                        }
                    }
                    else{ //if entity is out of entity part like category, role and group
                        $target_entity_type_identifier = $notification->module;
                        $target_entity_type_title =  ucwords($notification->module);
                    }

                    //Get Actor Entity Data
                    $actor_entity_type = $this->_getEntityTypeData($notification->actor_entity_type_id);

                    //Get actor entity name
                    $flat_table_model = new SYSTableFlat($actor_entity_type->identifier);
                    $where_condition = " entity_id = ".$notification->actor_entity_id;
                    $actor_entity = $flat_table_model->getColumnByWhere($where_condition,'first_name, last_name');

                    $actor_name = "Unknown";
                    if($actor_entity){
                        $actor_name = CustomHelper::setFullName($actor_entity);
                    }

                    $notification->message = "has ".$notification->permission." $target_entity_type_title #".$notification->entity_id;
                    $notification->target_entity_type_identifier = strtolower($target_entity_type_identifier);
                    $notification->target_entity_type_title = $target_entity_type_title;
                    $notification->actor_entity_type_identifier = $actor_entity_type->identifier;
                    $notification->actor_entity_type_title = $actor_name;

                    //get target module data
                    $return[] = $notification;
                }
            }
        }

        return $return;
    }

    /**
     * Get Module name which are customized
     * @param $slug
     * @return bool|string
     */
    private function _customizeModule($slug)
    {
        if($slug == 'product_recipe') return  "Recipe";
        else if($slug == 'product_bundle') return  "Bundle";
        else if($slug == 'blacklist_customer') return  "Customer";
        return false;
    }

    /**
     * List of system notification
     * @param $request
     * @return mixed
     */
    public function listing($request)
    {
        $request = is_object($request) ? $request : (object)$request;

        // sorting defaults
        $request->order_by = $request->order_by;
        $request->sorting = $request->sorting;
        $total_records = $this->getTotalCount();

       // $request->limit = $total_records;
        // if need paging
        // params
        $request->limit = $request->limit == "" ? PAGE_LIMIT_API : intval($request->limit);
        // offfset / limits / valid pages
        $request->offset = isset($request->offset) ? $request->offset : 0;

        $raw_records = $this->getList($request);

        $data["data"]['notification'] = $raw_records;
        // set pagination response
        $data["data"]["page"] = array(
            "offset" => $request->offset,
            "limit" => $request->limit,
            "total_records" => $total_records,
            "next_offset" => ($request->offset + $request->limit),
            "prev_offset" => $request->offset > 0 ? ($request->offset - $request->limit) : $request->offset,
        );

        return $data;

    }

    /**
     * Update notification is read
     * @param $request
     * @return bool
     */
    public function updateNotificationRead($request)
    {
        $request = is_object($request) ? $request : (object)$request;

        try{
            if(isset($request->entity_notification_id) && is_numeric($request->entity_notification_id)){

                $entity_notification = $this->_model->get($request->entity_notification_id);
                if($entity_notification){

                    $entity_notification->is_read = 1;
                    $this->_model->set($request->entity_notification_id,(array)$entity_notification);

                }
            }
        }
        catch (\Exception $e) {
            // echo $e->getTraceAsString(); exit;
            // $return['message'] = $e->getMessage();
            //  $return['debug'] = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine(); //" : Stack " . $e->getTraceAsString();
        }
        return true;
    }

    /**
     * Get Target module
     * @param $table
     * @param $entity_id
     * @return bool|string
     */
    private function _getTargetModule($table, $entity_id)
    {
        Switch($table){

            case 'sys_category':
                return 'category';

            case 'sys_role':
                $role_model = new SYSRole();
                $role = $role_model->get($entity_id);

                if(isset($role->is_group) && $role->is_group == 1)
                    return 'group';
                  else
                      return 'role';

            default:
                return false;
        }
    }

    /**
     * send Notification
     * @param $entity
     * @param $notificationSubject
     * @param $notificationMessage
     * @param bool $identifier
     * @param bool $notify_type
     */
    public function sendNotification($entity,$notificationSubject,$notificationMessage,$identifier = false,$notify_type = false)
    {
        $entity_model = new Entity();
        $notification_model = new Notification;
        $notification_wildcards = new NotificationWildcard();
        if (isset($entity->entity_id)) {
            if (isset($entity->auth)) {
                if (isset($entity->auth->device_type) && isset($entity->auth->device_type)) {
                    if (in_array($entity->auth->device_type, ['android', 'ios'])) {
                        if ( (isset($entity->attributes->is_notify->value) && $entity->attributes->is_notify->value == 1) || $entity->entity_type_id == 3 ) {
                            //Set entity data to replace wildcards
                            $replace = new \StdClass();
                            $replace->user_name = isset($entity->attributes->first_name) ? $entity->attributes->first_name : "";

                            if (isset($entity->attributes->last_name) && !empty($entity->attributes->last_name)) {
                                $replace->user_name .= " ";
                                $replace->user_name .= isset($entity->attributes->last_name) ? $entity->attributes->last_name : "";
                            }

                            $replace->email = isset($entity->auth->email) ? $entity->auth->email : "";
                            $replace->mobile_no = isset($entity->auth->mobile_no) ? $entity->auth->mobile_no : "";

                            //Replace WildCards
                            $subject = $notification_wildcards->replaceNotifyText($replace, $notificationSubject);
                            $message = $notification_wildcards->replaceNotifyText($replace, $notificationMessage);
                            // prepare notification data
                            $notification_data = [
                                "title" => $subject,
                                "body" => $message,
                                "key_code" => '',
                                "sound" => "",
                                "badge" => "",
                                //"user" => $user ? $user : array(),
                                //"target_user" => $target_user ? $target_user : array(),
                                // "user_id" => $actor_entity->entity_id,
                                // "target_user_id" => isset($target_entity->entity_id) ? $target_entity->entity_id : "",
                                // "user_name" => isset($actor->first_name) ? $actor->first_name : "",
                                //  "target_user_name" => "",
                                "my_custom_data" => [
                                    'entity_id' => $entity->entity_id,
                                    "user_name" => $replace->user_name,
                                    'identifier' => ($notify_type) ? $notify_type : 'custom_notification'
                                ]
                            ];
                            // send
                            //if ($entity->auth->device_type == "android") {
                             echo "<pre>"; print_r($notification_data);
                            $ret = $notification_model->pn_android($entity->auth->device_token, $notification_data,$identifier);
                            //echo 'Notitication Status<br />' . $ret; echo '<br />';
                            // echo "<pre>"; print_r($ret);
                            // } else {
                            //   $notification_model->pn_ios($entity->auth->device_token, $notification_data);
                            // }

                        }
                    }

                }
            }
        }
    }

    /**
     * @param $entity
     * @param $order
     * @param $pickupTime
     */
    public function sendReminderNotification($entity,$order,$pickupTime)
    {

        $notificationSubject = 'Notification Remainder - Order #'.$order->attributes->order_number;
        $notificationMessage = 'You have to pickup order '. $order->attributes->order_number .' at ' . $pickupTime;

        $entity_model = new Entity();
        $notification_model = new Notification;
        $notification_wildcards = new NotificationWildcard();
        if (isset($entity->entity_id)) {
            if (isset($entity->auth)) {
                if (isset($entity->auth->device_type) && isset($entity->auth->device_type)) {
                    if (in_array($entity->auth->device_type, ['android', 'ios'])) {
                        if ( (isset($entity->attributes->is_notify->value) && $entity->attributes->is_notify->value == 1) || $entity->entity_type_id == 3 ) {
                            //Set entity data to replace wildcards
                            $replace = new \StdClass();
                            $replace->user_name = isset($entity->attributes->first_name) ? $entity->attributes->first_name : "";

                            if (isset($entity->attributes->last_name) && !empty($entity->attributes->last_name)) {
                                $replace->user_name .= " ";
                                $replace->user_name .= isset($entity->attributes->last_name) ? $entity->attributes->last_name : "";
                            }

                            $replace->email = isset($entity->auth->email) ? $entity->auth->email : "";
                            $replace->mobile_no = isset($entity->auth->mobile_no) ? $entity->auth->mobile_no : "";

                            //Replace WildCards
                            $subject = $notification_wildcards->replaceNotifyText($replace, $notificationSubject);
                            $message = $notification_wildcards->replaceNotifyText($replace, $notificationMessage);
                            // prepare notification data
                            $notification_data = [
                                "title" => $subject,
                                "body" => $message,
                                "key_code" => '',
                                "sound" => "",
                                "badge" => "",
                                "my_custom_data" => [
                                    'entity_id' => $entity->entity_id,
                                    "user_name" => $replace->user_name,
                                    'identifier' => 'order_reminder',
                                    'order_id' => $order->entity_id,
                                ]
                            ];
                            // send
                            //if ($entity->auth->device_type == "android") {
                            echo "<pre>"; print_r($notification_data);
                            $ret = $notification_model->pn_android($entity->auth->device_token, $notification_data,'driver');
                            //echo 'Notitication Status<br />' . $ret; echo '<br />';
                            // echo "<pre>"; print_r($ret);
                            // } else {
                            //   $notification_model->pn_ios($entity->auth->device_token, $notification_data);
                            // }

                        }
                    }

                }
            }
        }
    }

    /**
     * This function is for get entity type notification listing
     * @param {object} $request
     * @param {bool} $is_read
     */
    public function getNotificationList($request,$unread = false, $entity_id = '')
    {

        $request = (is_array($request) && count($request)>0) ? (object)$request : $request;
        if(empty($entity_id)){
            $entity_id = $request->entity_id;
        }
        $offset    = (isset($request->offset) && !empty($request->offset)) ? $request->offset : 0;
        $limit     = (isset($request->limit) && !empty($request->limit)) ? $request->limit : PAGE_LIMIT_API;

        if(isset($request->entity_type_id)  && $request->entity_type_id == 3){

            $query = \DB::table('sys_entity_history AS seh')
                ->join('order_flat AS o','o.entity_id','=','seh.entity_id')
                ->leftJoin('order_statuses_flat As os','o.order_status','=','os.entity_id')
                ->selectRaw("os.keyword as order_status,entity_history_id,history_id,seh.entity_id AS order_id,actor_entity_type_id,actor_entity_id,is_read,notification_message,o.customer_id,seh.created_at,o.order_number")
                ->where('o.driver_id',$entity_id)
                ->where('os.keyword','assigned')
                ->whereRaw("entity_history_id IN (SELECT MAX(entity_history_id) FROM sys_entity_history
                 WHERE `entity_type_id` = 15 AND `history_id` = 16 GROUP BY entity_id) ");

        }
        else{
            $query = \DB::table('sys_entity_history AS seh')
                ->join('order_flat AS o','o.entity_id','=','seh.entity_id')
                ->selectRaw("(SELECT 
                                  osf.display_key
                                FROM
                                    order_history_flat ohf
                                        INNER JOIN
                                    order_statuses_flat osf ON osf.entity_id = ohf.order_status
                                WHERE
                                    ohf.order_id = `o`.`entity_id`
                                ORDER BY ohf.id DESC
                                LIMIT 1) AS order_status,entity_history_id,history_id,seh.entity_id AS order_id,actor_entity_type_id,actor_entity_id,is_read,notification_message,o.customer_id,seh.created_at,o.order_number")
                ->where('o.customer_id',$entity_id)
                ->whereRaw("entity_history_id IN (SELECT MAX(entity_history_id) FROM sys_entity_history WHERE `entity_type_id` = 15 AND `history_id` = 17 GROUP BY entity_id) ");

        }

        //unread notification
        if($unread === true){
            $query = $query->where('seh.is_read',0);
        }

        // total records count
        $total_records = $query->count();

        if(!empty($offset)){
            $query = $query->skip($offset);

        }


        //get record
        $getRecords    = $query->take($limit)->orderBy('entity_history_id','desc')->get();
        $data['records'] = $getRecords;
        //pagination
        $data["page"] = [
            "limit" => $limit,
            "total_records" => $total_records,
            "next_offset" => ($offset + $limit),
            "prev_offset" => $offset
        ];
        //set response
        $response['error']          = 0;
        $response['message']        = "success";
        $response['data']           = $data;
        $response['totalRecord']    = $total_records;
      // echo "<pre>"; print_r($response); exit;
        return $response;
    }

}
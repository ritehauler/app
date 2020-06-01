<?php namespace App\Http\Hooks;

use App\Http\Models\Notification;
use App\Http\Models\SYSHistoryNotification;
use App\Libraries\CustomHelper;
use App\Libraries\System\Entity;
use PhpParser\Node\Stmt\Switch_;

Class EntityNotification
{

    public function __construct()
    {

    }

    /**
     * @param Request $request
     * @param $base_data
     * @return mixed
     */
    function init($request,$base_data)
    {
        if (isset($base_data['history_data']) && isset($base_data['entity_history'])) {
            $func_name = '_' . $base_data['history_data']->identifier;
            $func = CustomHelper::convertToCamel($func_name);
            if (method_exists($this, $func))
                return $this->{$func}(
                    $base_data['history_data'],
                    $base_data['entity_history']
                );

        }
    }

    /**
     * send notification on order update
     * @param $history_data
     * @param $entity_history
     */
    public function orderDriverNotify($history_data,$entity_history)
    {
        if( $history_data->notify_entity > 0) {
            //Get notification template
            $history_notification_model = new SYSHistoryNotification();
            $history_notification = $history_notification_model->getDataByIdentifierAndType($history_data->identifier, 'push', 'to_entity');

            if($history_notification) {

                //Get actor entity Data
                $actor_entity = $this->_getEntityData($entity_history->actor_entity_type_id,$entity_history->actor_entity_id);
                if (isset($actor_entity->attributes)) {
                    $actor = $actor_entity->attributes;
                }
                //Get Target entity Data
                $target_entity = $this->_getEntityData($entity_history->entity_type_id,$entity_history->entity_id);
                if (isset($target_entity->attributes)) {
                    $target = $target_entity->attributes;
                }
                //Replace and set body for message
                $order_status_title = $target_entity->attributes->order_status->detail->attributes->title;
                //Replace and set body for message
                $replacers = [
                    'order_number' => $target_entity->attributes->order_number,
                    'status'       => $target->order_status->detail->attributes->keyword,
                    'content'      => 'Order '.$target_entity->attributes->order_number.' has been updated to '. $order_status_title
                ];

                // set body
                if ($history_notification->wildcards != "") {
                    $wildcards = explode(",", $history_notification->wildcards);
                    // $replacers = explode(",",$history_notification->replacers);
                    // replace title
                    $history_notification->title = str_replace($wildcards, $replacers, $history_notification->title);
                    eval("\$history_notification->title = \"$history_notification->title\";");
                    // replace body
                    $history_notification->body = str_replace($wildcards, $replacers, $history_notification->body);
                    eval("\$history_notification->body = \"$history_notification->body\";");
                }
                $order_status = $target_entity->attributes->order_status->detail->attributes->keyword;

                //prepare notification data
                $notification_data = [
                    "title" => $history_notification->title,
                    "body" => $history_notification->body,
                    "key_code" => intval($history_notification->key_code),
                    "sound" => isset($actor->sound) ? $actor->sound : "default",
                    "badge" => isset($actor->count_notification) ? $actor->count_notification : "",
                    "notId" => (string)$target_entity->entity_id,
                    //"user" => $user ? $user : array(),
                    //"target_user" => $target_user ? $target_user : array(),
                    // "user_id" => $actor_entity->entity_id,
                    // "target_user_id" => isset($target_entity->entity_id) ? $target_entity->entity_id : "",
                    // "user_name" => isset($actor->first_name) ? $actor->first_name : "",
                    //  "target_user_name" => "",
                    "my_custom_data" => array(
                        'entity_history_id' => $entity_history->entity_history_id,
                        'entity_id'=> $target_entity->entity_id,
                        "user_id" => $actor_entity->entity_id,
                        "user_name" => isset($actor->first_name) ? $actor->first_name : "",
                        "order_id"     =>  $target_entity->entity_id,
                        "order_number"  =>  $target_entity->attributes->order_number,
                        "order_status" => $order_status,
                        'identifier' => 'order'
                    )
                ];
                //update notification entity history
                \DB::table('sys_entity_history')
                    ->where('entity_history_id',$entity_history->entity_history_id)
                    ->update([
                        'notification_message' => $history_notification->body
                    ]);
                //send Notification
                $notification_model = new Notification();
                $ret = $notification_model->pn_android($actor_entity->auth->device_token, $notification_data,'driver');
            }

        }
        return;
    }

    /**
     * send notification on order update
     * @param $history_data
     * @param $entity_history
     */
    public function orderCustomerNotify($history_data,$entity_history)
    {
        if( $history_data->notify_entity > 0) {
            //Get notification template
            $history_notification_model = new SYSHistoryNotification();
            $history_notification = $history_notification_model->getDataByIdentifierAndType($history_data->identifier, 'push', 'to_entity');

            if($history_notification) {

                //Get actor entity Data
                $actor_entity = $this->_getEntityData($entity_history->actor_entity_type_id,$entity_history->extension_ref_id);

                if (isset($actor_entity->attributes)) {
                    $actor = $actor_entity->attributes;
                }
                //Get Target entity Data
                $target_entity = $this->_getEntityData($entity_history->entity_type_id,$entity_history->entity_id);
                if (isset($target_entity->attributes)) {
                    $target = $target_entity->attributes;
                }

                $order_status_title = $target_entity->attributes->order_status->detail->attributes->title;
                $content = $this->_orderNotificationMessage($target_entity);

                //Replace and set body for message
                $replacers = [
                    'order_number' => $target_entity->attributes->order_number,
                    'status'       => $target->order_status->detail->attributes->display_title,
                    'content'      => $content
                ];
                // set body
                if ($history_notification->wildcards != "") {
                    $wildcards = explode(",", $history_notification->wildcards);
                    // $replacers = explode(",",$history_notification->replacers);
                    // replace title
                    $history_notification->title = str_replace($wildcards, $replacers, $history_notification->title);
                    eval("\$history_notification->title = \"$history_notification->title\";");
                    // replace body
                    $history_notification->body = str_replace($wildcards, $replacers, $history_notification->body);
                    eval("\$history_notification->body = \"$history_notification->body\";");
                }
                $order_status = $target_entity->attributes->order_status->detail->attributes->display_key;
                //unread notification
                $notification_lib = new \App\Libraries\EntityNotification();
                $unreadBadge = $notification_lib->getNotificationList(array(),true,$actor_entity->entity_id);
                //echo "<pre>"; print_r($unreadBadge); exit;
                // prepare notification data
                $notification_data = [
                    "title" => $history_notification->title,
                    "body" => $history_notification->body,
                    "key_code" => intval($history_notification->key_code),
                    "sound" => isset($actor->sound) ? $actor->sound : "default",
                    "badge" => isset($unreadBadge['totalRecord']) ? $unreadBadge['totalRecord'] : 0,
                    "notId" => (string)$target_entity->entity_id,
                   // 'tag'   => (string)$target_entity->entity_id,
                    //"user" => $user ? $user : array(),
                    //"target_user" => $target_user ? $target_user : array(),
                    // "user_id" => $actor_entity->entity_id,
                    // "target_user_id" => isset($target_entity->entity_id) ? $target_entity->entity_id : "",
                    // "user_name" => isset($actor->first_name) ? $actor->first_name : "",
                    //  "target_user_name" => "",
                    "my_custom_data" => array(
                        'entity_history_id' => $entity_history->entity_history_id,
                        'entity_id'=> $target_entity->entity_id,
                        "user_id" => $actor_entity->entity_id,
                        "user_name" => isset($actor->first_name) ? $actor->first_name : "",
                        "order_id" =>  $target_entity->entity_id,
                        "order_number"  =>  $target_entity->attributes->order_number,
                        "order_status" => $order_status,
                        'identifier' => 'order'
                    )
                ];

              //  echo "<pre>"; print_r($notification_data); exit;
                //update notification entity history
                \DB::table('sys_entity_history')
                    ->where('entity_history_id',$entity_history->entity_history_id)
                    ->update([
                        'notification_message' => $history_notification->body
                    ]);


                //send Notification
                if(isset($actor_entity->attributes->is_notify->value)){
                    if($actor_entity->attributes->is_notify->value == 1){
                        $notification_model = new Notification();
                        $ret = $notification_model->pn_android($actor_entity->auth->device_token, $notification_data,'consumer');
                    }
                }
            }
        }
        return;
    }

    public function orderUpdateCustomerNotify($history_data,$entity_history)
    {
        if( $history_data->notify_entity > 0) {
            //Get notification template
            $history_notification_model = new SYSHistoryNotification();
            $history_notification = $history_notification_model->getDataByIdentifierAndType($history_data->identifier, 'push', 'to_entity');

            if($history_notification) {

                //Get actor entity Data
                $actor_entity = $this->_getEntityData($entity_history->actor_entity_type_id,$entity_history->extension_ref_id);

                if (isset($actor_entity->attributes)) {
                    $actor = $actor_entity->attributes;
                }
                //Get Target entity Data
                $target_entity = $this->_getEntityData($entity_history->entity_type_id,$entity_history->entity_id);
                if (isset($target_entity->attributes)) {
                    $target = $target_entity->attributes;
                }

                $order_status_title = $target_entity->attributes->order_status->detail->attributes->title;
                //Replace and set body for message
                $replacers = [
                    'order_number' => $target_entity->attributes->order_number
                ];
                // set body
                if ($history_notification->wildcards != "") {
                    $wildcards = explode(",", $history_notification->wildcards);
                    // $replacers = explode(",",$history_notification->replacers);
                    // replace title
                    $history_notification->title = str_replace($wildcards, $replacers, $history_notification->title);
                    eval("\$history_notification->title = \"$history_notification->title\";");
                    // replace body
                    $history_notification->body = str_replace($wildcards, $replacers, $history_notification->body);
                    eval("\$history_notification->body = \"$history_notification->body\";");
                }
                $order_status = $target_entity->attributes->order_status->detail->attributes->display_key;
                //unread notification
                //$notification_lib = new \App\Libraries\EntityNotification();
                //$unreadBadge = $notification_lib->getNotificationList('',true,$actor->entity_id);

                // prepare notification data
                $notification_data = [
                    "title" => $history_notification->title,
                    "body" => $history_notification->body,
                    "key_code" => intval($history_notification->key_code),
                    "sound" => isset($actor->sound) ? $actor->sound : "default",
                    "badge" => isset($unreadBadge['totalRecord']) ? 0 : "",
                    "notId" => (string)$target_entity->entity_id,
                    //"user" => $user ? $user : array(),
                    //"target_user" => $target_user ? $target_user : array(),
                    // "user_id" => $actor_entity->entity_id,
                    // "target_user_id" => isset($target_entity->entity_id) ? $target_entity->entity_id : "",
                    // "user_name" => isset($actor->first_name) ? $actor->first_name : "",
                    //  "target_user_name" => "",
                    "my_custom_data" => array(
                        'entity_history_id' => $entity_history->entity_history_id,
                        'entity_id'=> $target_entity->entity_id,
                        "user_id" => $actor_entity->entity_id,
                        "user_name" => isset($actor->first_name) ? $actor->first_name : "",
                        "order_id" =>  $target_entity->entity_id,
                        "order_number"  =>  $target_entity->attributes->order_number,
                        "order_status" => $order_status,
                        'identifier' => 'order'
                    )
                ];
                //update notification entity history
                \DB::table('sys_entity_history')
                    ->where('entity_history_id',$entity_history->entity_history_id)
                    ->update([
                        'notification_message' => $history_notification->body
                    ]);
                //send Notification
                if(isset($actor_entity->attributes->is_notify->value)){
                    if($actor_entity->attributes->is_notify->value == 1){
                        $notification_model = new Notification();
                        $ret = $notification_model->pn_android($actor_entity->auth->device_token, $notification_data,'consumer');
                    }
                }
            }
        }
        return;
    }

    /**
     * send notification on order update
     * @param $history_data
     * @param $entity_history
     */
    public function orderStatusUpdate($history_data,$entity_history)
    {

        if( $history_data->notify_entity > 0) {

            //Get notification template
            $history_notification_model = new SYSHistoryNotification();
            $history_notification = $history_notification_model->getDataByIdentifierAndType($history_data->identifier, 'push', 'to_entity');

            if($history_notification) {

                //Get actor entity Data
                $actor_entity = $this->_getEntityData($entity_history->actor_entity_type_id,$entity_history->actor_entity_id);
                if (isset($actor_entity->attributes)) {
                    $actor = $actor_entity->attributes;
                }

                //Get Target entity Data
                $target_entity = $this->_getEntityData($entity_history->entity_type_id,$entity_history->entity_id);
                if (isset($target_entity->attributes)) {
                    $target = $target_entity->attributes;
                }

                //Replace and set body for message
                $replacers = [
                    'order_number' => $target->order_number,
                    'status' => $target->order_status->detail->attributes->display_title,
                ];

                // set body
                if ($history_notification->wildcards != "") {
                    $wildcards = explode(",", $history_notification->wildcards);
                    // $replacers = explode(",",$history_notification->replacers);
                    // replace title
                    $history_notification->title = str_replace($wildcards, $replacers, $history_notification->title);
                    eval("\$history_notification->title = \"$history_notification->title\";");
                    // replace body
                    $history_notification->body = str_replace($wildcards, $replacers, $history_notification->body);
                    eval("\$history_notification->body = \"$history_notification->body\";");
                }
                // prepare notification data
                $notification_data = [
                    "title"     => $history_notification->title,
                    "body"      => $history_notification->body,
                    "key_code"  => intval($history_notification->key_code),
                     "sound"    => isset($actor->sound) ? $actor->sound : "default",
                     "badge"    => isset($actor->count_notification) ? $actor->count_notification : "",
                   // 'collapse_key' => $target_entity->entity_id,
                   // 'tag'        => "$target_entity->entity_id",
                    //"user" => $user ? $user : array(),
                    //"target_user" => $target_user ? $target_user : array(),
                   // "user_id" => $actor_entity->entity_id,
                   // "target_user_id" => isset($target_entity->entity_id) ? $target_entity->entity_id : "",
                   // "user_name" => isset($actor->first_name) ? $actor->first_name : "",
                  //  "target_user_name" => "",
                    "my_custom_data"    => array(
                        'entity_id'     => $target_entity->entity_id,
                        "user_id"       => $actor_entity->entity_id,
                        "user_name"     => isset($actor->first_name) ? $actor->first_name : "",
                        "order_number"  =>  $target->order_number,
                        'identifier'    => 'order_update',
                    )
                ];

              //  echo "<pre>"; print_r($notification_data);

                //send Notification
                $notification_model = new Notification();
                $ret = $notification_model->pn_android($actor_entity->auth->device_token, $notification_data);
             //  echo "<pre>"; print_r($ret);exit;

            }

        }
        return;
    }

    /**
     * Get entity Data
     * @param $entity_type_id
     * @param $entity_id
     * @return bool
     */
    private function _getEntityData($entity_type_id,$entity_id)
    {
        $entity_lib = new Entity();
        //Get actor entity Data
        $actor_params['entity_type_id'] = $entity_type_id;
        $actor_params['entity_id'] = $entity_id;
        //$actor_params['mobile_json'] = 1;
        $actor_entity_data = $entity_lib->apiGet($actor_params);
        $actor_entity_data = json_decode(json_encode($actor_entity_data));

        if ($actor_entity_data->error == 0 && isset($actor_entity_data->data->entity)) {
            return  $actor_entity_data->data->entity;
        }
        return false;
    }

    /**
     * send notification on order update
     * @param $history_data
     * @param $entity_history
     */
    public function orderDiscussionAdd($history_data,$entity_history)
    {

        if( $history_data->notify_entity > 0) {

            //Get notification template
            $history_notification_model = new SYSHistoryNotification();
            $history_notification = $history_notification_model->getDataByIdentifierAndType($history_data->identifier, 'push', 'to_entity');

            if($history_notification) {

                //Get actor entity Data
                $actor_entity = $this->_getEntityData($entity_history->actor_entity_type_id,$entity_history->actor_entity_id);
                if (isset($actor_entity->attributes)) {
                    $actor = $actor_entity->attributes;
                }

                //Get Target entity Data
                $target_entity = $this->_getEntityData($entity_history->entity_type_id,$entity_history->entity_id);
                if (isset($target_entity->attributes)) {
                    $target = $target_entity->attributes;
                }

                //Replace and set body for message
             //  print_r($target); exit;
                $replacers = [
                    'order_number' => $target->order_id->detail->attributes->order_number,
                    'message' => CustomHelper::limit_text($target->order_message,15),
                ];

                // set body
                if ($history_notification->wildcards != "") {
                    $wildcards = explode(",", $history_notification->wildcards);
                    // $replacers = explode(",",$history_notification->replacers);
                    // replace title
                    $history_notification->title = str_replace($wildcards, $replacers, $history_notification->title);
                    eval("\$history_notification->title = \"$history_notification->title\";");
                    // replace body
                    $history_notification->body = str_replace($wildcards, $replacers, $history_notification->body);
                    eval("\$history_notification->body = \"$history_notification->body\";");
                }
                // prepare notification data
                $notification_data = [
                    "title" => $history_notification->title,
                    "body" => $history_notification->body,
                    "key_code" => intval($history_notification->key_code),
                    "sound" => isset($actor->sound) ? $actor->sound : "default",
                    "badge" => isset($actor->count_notification) ? $actor->count_notification : "",
                    //"user" => $user ? $user : array(),
                    //"target_user" => $target_user ? $target_user : array(),
                    // "user_id" => $actor_entity->entity_id,
                    // "target_user_id" => isset($target_entity->entity_id) ? $target_entity->entity_id : "",
                    // "user_name" => isset($actor->first_name) ? $actor->first_name : "",
                    //  "target_user_name" => "",
                     "my_custom_data" => array(
                        'entity_id'=> $target->order_id->id,
                        "user_id" => $actor_entity->entity_id,
                        "user_name" => isset($actor->first_name) ? $actor->first_name : "",
                        "order_number" =>  $target->order_id->detail->attributes->order_number,
                        'identifier' => 'chat'
                    )
                ];

                //  echo "<pre>"; print_r($notification_data);

                //send Notification
                $notification_model = new Notification();
                $ret = $notification_model->pn_android($actor_entity->auth->device_token, $notification_data);
                //  echo "<pre>"; print_r($ret);exit;

            }

        }
        return;
    }

    /**
     * order notification messages
     * @param $order
     * @return string
     */
    private function _orderNotificationMessage($order)
    {
        $order_number = $order->attributes->order_number;
         $order_status_key  = $order->attributes->order_status->detail->attributes->keyword;

        Switch(trim($order_status_key)){
            case 'confirmed':
                return 'Thanks for using Rite Hauler. Your order '.$order_number.' is confirmed. We will notify you again before pickup.';
            case 'arrived':
                $driver_name = $order->attributes->driver_id->detail->attributes->full_name;
                $driver_phone = $order->attributes->driver_id->detail->auth->mobile_no;
                $vehicle_name = $order->attributes->vehicle_id->value;
                $vehicle_code = $order->attributes->vehicle_id->detail->attributes->vehicle_code;
               return 'Your driver is here for order '.$order_number.'. Driver is '.$driver_name.', '.$driver_phone.', '.$vehicle_name.', '.$vehicle_code.'. Have a pleasant ride!';
               // print_r($cont); exit;
            case 'on_the_way':
                return 'The driver is on the way towards your destination to drop your order '.$order_number.'.';
            case 'reached':
                return 'Your order '.$order_number.' reached the destination. Please make the payment of '.$order->attributes->grand_total.'. Please rate this ride.';
            case 'completed':
                return 'Thanks for using Rite Hauler. Your order '.$order_number.' is completed.';
            case 'driver_cancelled':
                return 'Sorry! The pickup has been declined for order '.$order_number.'. A cancellation fee will be charged.';
            case 'cancelled':
                return 'Sorry! Your order '.$order_number.' has been canceled. Please reschedule your order again.';
            case 'assigned':
                return 'Order '.$order_number.' has been updated to '. $order->order_status->value;
            default:
                return 'Order '.$order_number.' has been updated to '. $order->order_status->value;
        }
    }


}
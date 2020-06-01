<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;


// models
#use App\Http\Models\Setting;
use App\Http\Models\User;
use App\Http\Models\Message;

class Notification extends Base
{

    private $_ios_config;
    private $_android_config;
    //
    private $_ios_stream_ctx; // stream
    private $_ios_fp; // file pointer

    public function __construct()
    {
        // init models
        $setting_model = new Setting;

        // set tables and keys
        $this->__table = $this->table = 'notification';
        $this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // get key from admin
        $this->_ios_config = $setting_model->getBy("key", "ios_pem_pass");
        $this->_ios_apns_config = $setting_model->getBy("key", "apns_sandbox_mode");
        $this->_android_config = $setting_model->getBy("key", "android_gcm_key");

        // set fields
        $this->__fields = array($this->primaryKey, 'user_id', 'target_user_id', 'entity', 'entity_id', 'is_sent', 'created_at', 'updated_at', 'deleted_at');
    }

    /**
     * Push Notification (ios)
     * @param string $device_token
     * @param array $data
     * @return Response
     */
    function pn_ios($device_token, $data)
    {
        // set badge data type
        $data["badge"] = intval($data["badge"]);

        // add alert key for title
        $data["alert"] = $data["title"];
        // unset title
        unset($data["title"]);

        $data["sound"] = "default";

        // add timestamp
        $data["timestamp"] = gmdate("Y-m-d H:i:s");

        $ios_stream_ctx = stream_context_create();

        $apns_file = intval($this->_ios_apns_config->value) > 0 ? APNS_SANDBOX_FILE : APNS_PRODUCTION_FILE;
        $apns_url = intval($this->_ios_apns_config->value) > 0 ? APNS_SANDBOX_URL : APNS_PRODUCTION_URL;

        //stream_context_set_option($ios_stream_ctx, 'ssl', 'local_cert', getcwd()."/".PN_IOS_FILE);
        stream_context_set_option($ios_stream_ctx, 'ssl', 'local_cert', getcwd() . "/" . $apns_file);
        stream_context_set_option($ios_stream_ctx, 'ssl', 'passphrase', $this->_ios_config->value);

        // Open a connection to the APNS server
        //$ios_fp = @stream_socket_client(PN_IOS_URL, $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ios_stream_ctx);
        $ios_fp = stream_socket_client($apns_url, $err,
            $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ios_stream_ctx);

        if (!$ios_fp) {
            //exit("Failed to connect: $err $errstr" . PHP_EOL);
            echo "Failed to connect: $err $errstr" . PHP_EOL . "<br />";
        }
        //echo 'Connected to APNS' . PHP_EOL;


        $body['aps'] = $data;

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . @pack('n', 32) . @pack('H*', $device_token) . @pack('n', strlen($payload)) . $payload;
        //$msg = chr(0) . pack('n', 32) . pack('H*', str_replace(' ', '', sprintf('%u', CRC32($device_token)))) . pack('n', strlen($payload)) . $payload;


        // Send it to the server
        $result = @fwrite($ios_fp, $msg, strlen($msg));

        if (!$result) {
            echo "<br />" . 'Message not delivered' . PHP_EOL;
        } else {
            echo "<br />" . 'Message successfully delivered' . PHP_EOL;
        }
        // Close the connection to the server
        @fclose($ios_fp);

        return $result;
    }


    /**
     * Push Notification (android)
     * @param string $device_token
     * @param array $data
     * @return Response
     */
    function pn_android($device_token, $data, $identifier = '')
    {
        $reg_ids = is_array($device_token) ? $device_token : array($device_token);

        // Set POST variables dont change it, its default
        $url = ANDROID_PUSH_URL;

        // add timestamp
        $data["timestamp"] = gmdate("Y-m-d H:i:s");
        // set badge data type
        $data["badge"] = isset($data["badge"]) ? intval($data["badge"]) : 0;

       /* $fields = array(
            'registration_ids' => $reg_ids,
            'data' => array(
                "message" => $data
            ),
            'collapse_key' => str_replace(array(".", " "), "", microtime())
        );*/


        $fields = array(
            'to' => $device_token,
            'notification' => $data,
            'data' => $data
        );

        $headers = array(
            'Authorization: key=' .  $this->_android_config->value,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // Execute post
        $result = curl_exec($ch);
        // Close connection
        curl_close($ch);
        $fo = fopen("android-push.log", "a+");
        fwrite($fo, date("Y-m-d H:i:s") . " : " . json_encode($result) . "\n");
        fclose($fo);
        return $result;
    }


    /**
     * Updates
     *
     * @return Response
     */
    public function updates($user_id = 0, $datetime = "")
    {
        // init
        $data = array();

        /*// init models
        $user_model = new User;
        $dish_model = new Dish;
        $message_model = new Message;

        $datetime = $datetime == 0 ? date("d-m-Y H:i:s") : $datetime; // set default value

        // user data
        $user = $user_model->get_data($user_id);

        // get new dish count.
        $query = $dish_model
            //->where("datetime", ">", strtotime($datetime));
            ->where("datetime", ">", $datetime)
            ->where("status", "=", 1);
        $count_dish = $query->count();

        $query = $message_model
            ->selectRaw("message_id");
        $query->whereRaw("receiver_id = '".$user->user_id."'
        AND is_unread = 1
        AND message_id NOT IN (
            SELECT message_id FROM message_trash
            WHERE user_id = '".$user->user_id."'
        )
        AND sender_id NOT IN (
            SELECT target_user_id FROM user_block
            WHERE user_id = '".$user->user_id."'
        ) AND sender_id NOT IN (
            SELECT user_id FROM user_block
            WHERE target_user_id = '".$user->user_id."'
        )
        GROUP BY `sender_id`");
        //exit($query->toSql());



        //$count_messages = $query->count();
        $count_messages = count($query->get());
        // last msg date time
        $data["update"]["datetime"] = $datetime;
        $data["update"]["count_dish"] = $count_dish;
        //$data["update"]["is_friendlist_change"] = $user->is_friendlist_change;
        $data["update"]["count_messages"] = $count_messages;
        //$data["update"]["count_request"] = $user->count_request;
        $data["update"]["badge_count"] = $user->count_notification + $count_messages;
        //$data["update"]["count_voted_today"] = $user->count_voted_today;
        */
        return $data;
    }


    /**
     * Open socket
     * @param string $device_token
     * @param array $data
     * @return Response
     */
    function open_ios_socket()
    {
        $this->_ios_stream_ctx = stream_context_create();
        stream_context_set_option($this->_ios_stream_ctx, 'ssl', 'local_cert', getcwd() . "/" . PN_IOS_FILE);
        stream_context_set_option($this->_ios_stream_ctx, 'ssl', 'passphrase', $this->_ios_config->value);

        // Open a connection to the APNS server
        $this->_ios_fp = @stream_socket_client(PN_IOS_URL, $err,
            $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $this->_ios_stream_ctx);

        return $this->_ios_fp;
    }

    /**
     * Push Notification (ios)
     * @param string $device_token
     * @param array $data
     * @return Response
     */
    function ios_multi_send($ios_fp, $device_token, $data)
    {
        // set sound name
        $data["sound"] = $data["sound"] . ".caf";
        // set badge data type
        $data["badge"] = intval($data["badge"]);

        // add alert key for title
        $data["alert"] = $data["title"];
        // unset title
        unset($data["title"]);

        // unset sound on sound0
        if ($data["sound"] == "sound0.caf") {
            unset($data["sound"]);
        }
        $data["sound"] = "default";

        // add timestamp
        $data["timestamp"] = gmdate("Y-m-d H:i:s");


        if (!$ios_fp) {
            //exit("Failed to connect: $err $errstr" . PHP_EOL);
            //echo "Failed to connect: $err $errstr" . PHP_EOL. "<br />";
        }
        //echo 'Connected to APNS' . PHP_EOL;


        $body['aps'] = $data;

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . @pack('n', 32) . @pack('H*', $device_token) . @pack('n', strlen($payload)) . $payload;
        //$msg = chr(0) . pack('n', 32) . pack('H*', str_replace(' ', '', sprintf('%u', CRC32($device_token)))) . pack('n', strlen($payload)) . $payload;


        // Send it to the server
        $result = @fwrite($ios_fp, $msg, strlen($msg));

        if (!$result) {
            //echo 'Message not delivered' . PHP_EOL;
        } else {
            //echo 'Message successfully delivered' . PHP_EOL;
        }

        return $result;
    }


    /**
     * Close socket
     * @param string $device_token
     * @param array $data
     * @return Response
     */
    function close_ios_socket($fp)
    {
        // Close the connection to the server
        @fclose($fp);
    }


}
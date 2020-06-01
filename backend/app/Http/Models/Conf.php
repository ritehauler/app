<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

class Conf extends Base
{

    use SoftDeletes;
    public $table = 'conf';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'key', 'value', 'created_at', 'updated_at', 'deleted_at');
    }

    /**
     * set updated
     * @param string $key
     * @return Object
     */
    public function setUpdated($key = "", $timestamp = "")
    {
        // init target
        $data = $this->getBy("key", $key);
        // got data
        if ($data) {
            $data->updated_at = $timestamp == "" ? date("Y-m-d H:i:s") : $timestamp;
            $this->set($data->{$this->primaryKey}, (array)$data);
        }
        return $data;
    }

    /**
     * get schema
     * @param int $id
     * @return Object
     */
    public function getSchema($id = 0)
    {
        // init data
        $data = $this->get($id);
        $schema = FALSE;

        if ($data !== FALSE) {
            $schema = $data->value !== '' ? json_decode(trim($data->value)) : FALSE;
        }

        return $schema;
    }

    /**
     * get schema by key
     * @param string $key
     * @return Object
     */
    public function getSchemaByKey($key = "")
    {
        // init data
        $data = $this->getBy("key", $key);
        $schema = FALSE;

        if ($data !== FALSE) {
            $schema = $data->value !== '' ? json_decode(trim($data->value)) : FALSE;
        }

        return $schema;
    }


    /**
     * get schema by key
     * @param string $api_endpoint
     * @param string $request_type
     * @param array $post_array
     * @param string $parent_xml_tag
     * @return Array
     */
    public function makeOFRequest($api_endpoint, $request_type = "GET", $post_array = array(), $parent_xml_tag = "")
    {
        // reference for API calls
        // url : https://www.igniterealtime.org/projects/openfire/plugins/userservice/readme.html
        // conf_key : api_secret_userservice
        // url : https://www.igniterealtime.org/projects/openfire/plugins/restapi/readme.html
        // conf_key : api_secret

        /*
        // Example usage in Controller
        // ---------------------------
        // init model
        $conf_model = new \App\Http\Models\Conf;

        // endpoint url
        //$api_endpoint = "userService/users";
        $api_endpoint = "userService/users/eguser111";

        $post_array = array(
            "username" => "eguser",
            "password" => "1234567890",
            "name" => "EG User"
        );

        // make request
        //$response = $conf_model->makeOFRequest($api_endpoint, "post", $post_array, "<user/>");
        $response = $conf_model->makeOFRequest($api_endpoint);
        var_dump($response);
        exit;
        */

        // set vars
        $request_type = strtoupper($request_type);
        $is_post_request = $request_type != "GET" ? TRUE : FALSE;
        $of_config = $this->getBy("key", "of_config");
        $of_config_value = json_decode($of_config->value);
        $api_url = "http://" . $of_config_value->domain . ":" . $of_config_value->api_port . "/plugins/";
        $api_url .= trim($api_endpoint, "/");

        // init models / libs
        $curl_lib = new \App\Libraries\Curl;

        /*$api_url = "http://216.250.125.182:9090/plugins/userService/users";
        $post_params = array(
                "username" => "3_user",
                "password" => "1234567890",
                "name" => "User 3"
        );
        $body = $this->arrayToXML($post_params,"<user/>");
        */
        //$api_url .= "/23_user";
        $curl_lib->create($api_url);
        $curl_lib->httpHeader("Accept: application/json");
        //$curl_lib->httpHeader("Content-Type: application/json");
        $curl_lib->httpHeader("Content-Type: application/xml");
        $curl_lib->httpHeader("Authorization: " . $of_config_value->api_secret_userservice);
        $curl_lib->options(array(
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HEADER => FALSE
        ));

        // post params
        if ($is_post_request) {
            $body = $this->arrayToXML($post_array, $parent_xml_tag);
            $curl_lib->post($body);
        }
        //$api_url = http
        $response = $curl_lib->execute($api_url);
        /*var_dump($response); // false
        var_dump($curl_lib->error_code); // code
        var_dump($curl_lib->error_string);
        var_dump($curl_lib->info);*/
        $return = array(
            "response" => $response,
            "error_code" => $curl_lib->error_code, // NULL === success | FALSE === no record found
            "error_string" => $curl_lib->error_string
        );
        return $return;
    }


}
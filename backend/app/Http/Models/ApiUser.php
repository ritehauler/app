<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use Input;
use Session;
use Illuminate\Http\Request;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiToken;

class ApiUser extends Base
{

    private $_request;

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table = 'api_user';
        $this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, "type", 'name', 'email', 'username', 'password', 'status', 'count_requests', 'count_auth_requests', 'created_at', "updated_at", "deleted_at");
    }


    /**
     * Method    :    check_email
     * Reason    :    check email already exists
     **/
    /* function check_email($email = '')
     {
         $return = 0;
         $opt['count'] = true;
         $opt['conditions']['email'] = $email;
         $return = $this->findAllIDs($opt);
         return $return;
     }*/

    /**
     * Method        :    checkLogin
     * Parameters    :
     * -    email (required)        =    (description)
     * -    password (required)        =    (description)
     * Reason        :    checking user Login
     **/
    function checkLogin($email = "", $password = "")
    {
        $data = false;
        $enc_password = $this->saltPassword($password);

        // fetch
        $row = $this->where('email', '=', $email)
            ->where('password', '=', $enc_password)
            ->whereNull("deleted_at")
            ->get(array($this->__fields[0]));

        return isset($row[0]) ? (int)$row[0]->{$this->__fields[0]} : 0;
    }

    /**
     * Method        :    checkUserLogin
     * Parameters    :
     * -    email (required)        =    (description)
     * -    password (required)        =    (description)
     * Reason        :    checking user Login
     **/
    function checkUserLogin($username = "", $password = "")
    {
        $data = false;
        $enc_password = $this->saltPassword($password);

        // fetch
        $row = $this->where('username', '=', $username)
            ->where('password', '=', $enc_password)
            ->whereNull("deleted_at")
            ->get(array($this->__fields[0]));

        return isset($row[0]) ? (int)$row[0]->{$this->__fields[0]} : 0;
    }

    /**
     * Method        :    saltPassword
     * Reason        :    saltify password
     **/
    function saltPassword($password = "")
    {
        return API_USER_SALT . md5(md5(API_USER_SALT . sha1(API_USER_SALT . $password)));
    }


    /**
     * Method    :    check_access
     * Reason    :    check user authentication
     **/
    function checkAccess($request)
    {
        // load models
        $api_method_model = new ApiMethod;
        $api_token_model = new ApiToken;

        // set response header
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-Type: application/json; charset=utf8');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        if (\Session::token() != $request->input('_token') && (API_BASIC_AUTH_ACTIVE)) {
            $token_i = "client_token";

            // request headers
            $headers = array_change_key_case(apache_request_headers(), CASE_LOWER);

            // basic auth pattren
            $username = @$_SERVER["PHP_AUTH_USER"];
            $pass = @$_SERVER["PHP_AUTH_PW"];
            $token = isset($headers[$token_i]) ? $headers[$token_i] : "";
            // check login
            //$api_user_id = $this->checkLogin($email,$pass);
            $api_user_id = $this->checkUserLogin($username, $pass);
            $headers = apache_request_headers();

            $api_user = $this->get($api_user_id);

            //$request_path = preg_replace("@^(" . DIR_API . ")@", "", rawurldecode($request->path()));
            // FIX : invalid request uri from built-in route
            if(str_replace("/","",ADD_PATH) != "") {
                $_SERVER["REQUEST_URI"] = str_replace(ADD_PATH,"",$_SERVER["REQUEST_URI"]);
            }

            $request_path = preg_replace("@^(" . DIR_API . ")|(/" . DIR_API . ")@", "", rawurldecode($_SERVER["REQUEST_URI"]));

            // FIX : remove query string
            $request_path = isset(parse_url($request_path)["path"]) ? parse_url($request_path)["path"] : $request_path;

            // retrieve method id
            $raw_query = $api_method_model->select("api_method_id")
                //->where("uri","=", preg_replace("@^(".DIR_API.")@","",Input::path()))
                ->where("uri", "=", $request_path)
                ->where("is_active", "=", 1)
                ->whereNull("deleted_at")
                ->get();

            $raw_method_id = isset($raw_query[0]) ? $raw_query[0]->api_method_id : 0;
            $api_method = $api_method_model->get($raw_method_id);

            // get token data
            $token_data = $api_token_model->getDataByToken($token, 0, $request->input('entity_type', NULL), $request->input('entity_id', NULL));

            if ($api_user === FALSE) {
                $api_data['response'] = "error";
                $api_data['kick_user'] = 1; // kick user
                $api_data['message'] = "Invalid API Authorization";
            } elseif ($api_user->status == 0) {
                $api_data['response'] = "error";
                $api_data['kick_user'] = 1; // kick user
                $api_data['message'] = "Your API account is InActive";
            } elseif ($api_user->status == 2) {
                $api_data['response'] = "error";
                $api_data['kick_user'] = 1; // kick user
                $api_data['message'] = "Your API account is Baned by Admin";
            } elseif ($api_method === FALSE) {
                $api_data['response'] = "error";
                $api_data['message'] = "Invalid API Method Request";
            } elseif ($api_method->is_token_required == 1 && $token_data === FALSE) {
                $api_data['response'] = "error";
                $api_data['kick_user'] = 1; // kick user
                $api_data['message'] = "Invalid request Token";
            } else {
                // update auth request count/expiry
                if ($api_method->is_token_required == 1) {
                    $token_data = $api_token_model->getDataByToken($token, 1, $request->input('entity_type', NULL), $request->input('entity_id', NULL));
                }

                // update request count
                $api_user->count_requests = $api_user->count_requests + 1;
                $api_user->count_auth_requests = $api_method->is_token_required > 0 ? ($api_user->count_auth_requests + 1) : $api_user->count_auth_requests;
                $api_user->updated_at = date("Y-m-d H:i:s");
                $this->set($api_user->api_user_id, (array)$api_user);
            }

            if (isset($api_data)) {
                // we need to have bool (0/1) for response
                $api_data["response"] = $api_data["response"] == "success" ? 0 : 1;
                $api_data["error"] = $api_data["response"];
                unset($api_data["response"]);

                echo json_encode($api_data);
                exit;
            }
        }
    }

}

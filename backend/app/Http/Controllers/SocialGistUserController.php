<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use Validator;

// load models
#use App\Http\Models\EFEntityPlugin;
use App\Http\Models\Conf;

//use Twilio;

class SocialGistUserController extends Controller
{

    protected $_assignData = array(
        'p_dir' => '',
        'dir' => "social_gist/",
        "s_title" => "SocialGist",
        "p_title" => "SocialGist",
    );
    protected $_apiData = array();
    protected $_layout = "";
    protected $_models = array();
    protected $_jsonData = array();
    protected $_modelPath = "\App\Http\Models\\";
    protected $_object_identifier;
    protected $_entity_identifier;
    protected $_entity_api_route;
    protected $_entity_pk;
    protected $_entity_ucfirst;
    protected $_entity_model;
    //protected $_entity_id = "1";
    protected $_plugin_identifier = NULL;
    protected $_plugin_config = array();


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        // load entity model
        // get all webservices data
        //$this->__models['entity_plugin_model'] = new EFEntityPlugin;
        $this->_entity_model = $this->_modelPath . $this->_entity_ucfirst;
        $this->_entity_model = new $this->_entity_model;

        $this->_assignData['conf_model'] = $this->_modelPath . "Conf";
        $this->_assignData['conf_model'] = new $this->_assignData['conf_model'];

        // plugin config
        //$this->_plugin_config = $this->__models['entity_plugin_model']->getPluginSchema($this->_entity_id, $this->_plugin_identifier);
        // set defaults
        //$this->_plugin_config = isset($this->_plugin_config->webservices) ? $this->_plugin_config->webservices : array();
        //$this->_plugin_config["webservices"] = $this->_plugin_config;

    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        exit("Welcome...");
    }

    /**
     * confirm signup
     *
     * @return Response
     */
    public function confirmSignup(Request $request)
    {
        // view file
        $view_file = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', __FUNCTION__));
        // title
        $this->_assignData["page_action"] = "Confirm Signup";
        $this->_assignData["p_title"] = $this->_assignData["page_action"];

        // get params
        $email = $request->input("email", "");
        $verification_token = $request->input("verification_token", "");

        // post registration
        if ($request->do_post == 1) {

            // default target element
            $this->_jsonData['targetElem'] = "p[id=error_msg]";
            // default errors class
            $this->_jsonData['removeClass'] = "hide";
            $this->_jsonData['addClass'] = "show";


            $post_arr = array(
                "login_id" => $email,
                "verification_token" => $verification_token,
            );

            // request api
            $response = $this->__internalCall($request, $this->_entity_api_route."confirm_signup","post",$post_arr);
            $response = isset($response["jsonEditor"]) ? json_decode($response["jsonEditor"]) : $response;

            if ($response->error == 1) {
                // msg
                $this->_jsonData['text'] = $response->message;
                $this->_jsonData['targetElem'] = "div[id=error_msg_verification_token]";
            } else {
                $this->_jsonData['removeClass'] = "txt-danger";
                $this->_jsonData['addClass'] = "txt-success";

                // set in session
                \Session::put($this->_entity_identifier."_og_data",$response->data->{$this->_entity_identifier});
                \Session::save();

                // redirect
                $this->_jsonData['redirect'] = \URL::to("signup_thankyou");
            }

            // return
            return $this->_jsonData;
        }

        $this->_assignData["email"] = $email;
        $this->_assignData["verification_token"] = $verification_token;

        // set/show view
        //$this->_layout .= View::make($this->_assignData["p_dir"] . "header", $this->_assignData);
        $this->_layout = View::make($this->_assignData["dir"] . $view_file, $this->_assignData);
        //$this->_layout .= View::make($this->_assignData["p_dir"] . "footer", $this->_assignData);
        //var_dump($this->_layout);
        //$t = view($this->_assignData["dir"] . $view_file, $this->_assignData);
        //return $t;
        //exit;
        return $this->_layout;
    }


    /**
     * confirm signup
     *
     * @return Response
     */
    public function signupThankyou(Request $request)
    {
        // view file
        $view_file = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', __FUNCTION__));
        // assign vars to views
        $this->_assignData["page_action"] = "Thank you";
        $this->_assignData["p_title"] = $this->_assignData["page_action"];

        $this->_assignData['_entity_identifier'] = $this->_entity_identifier;
        $this->_assignData['_entity_pk'] = $this->_entity_pk;

        $data = array(
            "content" => "Your account is successfully confirmed.",
            $this->_entity_identifier => \Session::get($this->_entity_identifier."_og_data",NULL)
        );

        // get from session
        $this->_assignData["data"] = $data;

        // set/show view
        $this->_layout = View::make($this->_assignData["dir"] . $view_file, $this->_assignData);
        return $this->_layout;
    }


    /**
     * confirm forgot
     *
     * @return Response
     */
    public function confirmForgot(Request $request)
    {
        // view file
        $view_file = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', __FUNCTION__));
        // title
        $this->_assignData["page_action"] = "Confirm Forgot";
        $this->_assignData["p_title"] = $this->_assignData["page_action"];

        // page data
        $data = array(
            "content" => "Please follow instructions on your phone...",
            "note" => "Please enter your new password"
        );

        // get params
        //$email = $request->input("email", "");
        $verification_token = $request->input("verification_token", "");


        // post registration
        if ($request->do_post == 1) {

            // trim/escape all
            //$request->merge(array_map('strip_tags', $request->all()));
            $request->merge(array_map('trim', $request->all()));

            // default target element
            $this->_jsonData['targetElem'] = "p[id=error_msg]";
            // default errors class
            $this->_jsonData['removeClass'] = "hide";
            $this->_jsonData['addClass'] = "show";

            // get params
            $password = $request->input("password", "");
            $confirm_password = $request->input("confirm_password", "");


            $post_arr = array(
                //"login_id" => $email,
                "verification_token" => $verification_token,
                "new_password" => $confirm_password,
            );

            if($password == "") {
                $field_name = "password";
                $this->_jsonData['focusElem'] = "input[name=" . $field_name . "]";
                $this->_jsonData['text'] = "Please enter New Password";
                $this->_jsonData['targetElem'] = "div[id=error_msg_" . $field_name . "]";
            } elseif(strlen($password) < 6) {
                $field_name = "password";
                $this->_jsonData['focusElem'] = "input[name=" . $field_name . "]";
                $this->_jsonData['text'] = "Password length must be 6 characters atleast";
                $this->_jsonData['targetElem'] = "div[id=error_msg_" . $field_name . "]";
            } elseif($password != $confirm_password) {
                $field_name = "confirm_password";
                $this->_jsonData['focusElem'] = "input[name=" . $field_name . "]";
                $this->_jsonData['text'] = "Confirm password does not match";
                $this->_jsonData['targetElem'] = "div[id=error_msg_" . $field_name . "]";
            } else {
                // request api
                $response = $this->__internalCall($request, $this->_entity_api_route."reset_password","post",$post_arr);
                $response = isset($response["jsonEditor"]) ? json_decode($response["jsonEditor"]) : $response;

                if ($response->error == 1) {
                    // msg
                    $this->_jsonData['text'] = $response->message;
                    $this->_jsonData['targetElem'] = "div[id=error_msg_confirm_password]";
                } else {
                    $this->_jsonData['removeClass'] = "txt-danger";
                    $this->_jsonData['addClass'] = "txt-success";

                    // set in session
                    \Session::put($this->_entity_identifier."_og_data",$response->data->{$this->_entity_identifier});
                    \Session::save();

                    // redirect
                    $this->_jsonData['redirect'] = \URL::to("forgot_thankyou");
                }
            }

            // return
            return $this->_jsonData;
        }

        //$this->_assignData["email"] = $email;
        $this->_assignData["verification_token"] = $verification_token;
        $this->_assignData["data"] = $data;

        // set/show view
        //$this->_layout .= View::make($this->_assignData["p_dir"] . "header", $this->_assignData);
        $this->_layout = View::make($this->_assignData["dir"] . $view_file, $this->_assignData);
        //$this->_layout .= View::make($this->_assignData["p_dir"] . "footer", $this->_assignData);
        //var_dump($this->_layout);
        //$t = view($this->_assignData["dir"] . $view_file, $this->_assignData);
        //return $t;
        //exit;
        return $this->_layout;
    }


    /**
     * confirm signup
     *
     * @return Response
     */
    public function forgotThankyou(Request $request)
    {
        // view file
        $view_file = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', __FUNCTION__));
        // assign vars to views
        $this->_assignData["page_action"] = "Thank you";
        $this->_assignData["p_title"] = $this->_assignData["page_action"];

        $this->_assignData['_entity_identifier'] = $this->_entity_identifier;
        $this->_assignData['_entity_pk'] = $this->_entity_pk;

        $data = array(
            "content" => "Password retrieved successfully",
            $this->_entity_identifier => \Session::get($this->_entity_identifier."_og_data",NULL)
        );

        // get from session
        $this->_assignData["data"] = $data;

        // set/show view
        $this->_layout = View::make($this->_assignData["dir"] . $view_file, $this->_assignData);
        return $this->_layout;
    }


    /**
     * reset ID
     *
     * @return Response
     */
    public function resetID(Request $request)
    {
        // view file
        $view_file = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', __FUNCTION__));

        // get params
        $email = $request->input("new_login_id", "");
        $verification_token = $request->input("verification_token", "");

        // title
        $this->_assignData["page_action"] = "Confirm new Email";
        $this->_assignData["p_title"] = $this->_assignData["page_action"];


        // set content
        $data = array(
            "content" => "Invalid verification link"
        );

        // device detection library
        $detect = $this->_assignData["detect"] = new \App\Libraries\Mobile_Detect;

        // default mode
        $this->_assignData["mode"] = "web";

        // post params
        $url = $this->_entity_api_route."reset_id";

        $post_arr = array(
            "new_login_id" => $email,
            "verification_token" => $verification_token,
            "method_name" => $url
        );

        // request api
        $response = $this->__internalCall($request, $url, "post", $post_arr);
        $response = json_decode(json_encode($response));
        //$response = isset($response["jsonEditor"]) ? json_decode($response["jsonEditor"]) : $response;

        $this->_assignData['error'] = $response->error;

        // if success
        if ($this->_assignData['error'] == 0) {

            $this->_assignData["_entity_identifier"] = $this->_entity_identifier;
            $this->_assignData["entity"] = $response->data->{$this->_entity_identifier};

            // if device is mobile
            $data["client_token"] = "test"; // -temp

            if($detect->isMobile()) {
                // generate and assign new oAuth Token, remove old tokens
                // load / init models
                $api_token_model = $this->_modelPath . "ApiToken";
                $api_token_model = new $api_token_model;
                $data["client_token"] = $api_token_model->generate($this->_object_identifier, $this->_assignData["entity"]->{$this->_entity_pk}, true);


                $this->_assignData["mode"] = "mobile";

                // content
                $data["content"] = "Please continue on your mobile";

            } else {
                $data["content"] = $response->message;
            }
        }
        else {
            $data["content"] = $response->message;
        }

        // assign data
        $this->_assignData["data"] = $data;

        // set/show view
        $this->_layout = View::make($this->_assignData["dir"] . $view_file, $this->_assignData);
        return $this->_layout;
    }


}
<?php
namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Api\System\EntityController;
use App\Http\Models\SYSEntityType;
use App\Libraries\CustomHelper;
use Illuminate\Http\Request;
use View;
use Validator;

// load models
#use App\Http\Models\EFEntityPlugin;
use App\Http\Models\Conf;
use App\Libraries\EntityHelper;
use App\Libraries\System\Entity;
//use Twilio;

/**
 * @property  _json_data
 */
class EntityAuthController extends EntityController
{

    protected $_assignData = array(
        'p_dir' => 'panel/',
        'dir' => "panel/entity_auth/",
        //"s_title" => "Entity Auth",
       // "p_title" => "Entity Auth"
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
    protected $_entity_type_model;
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
        //set vars
        $this->_object_identifier = "entity_auth";
        $this->_entity_identifier = "entity_auth";
        $this->_entity_pk = "entity_auth_id";
        $this->_entity_ucfirst = "SYSEntityAuth";
        $this->_entity_api_route = "entity_auth/";
        $this->_entity_type_model = "SYSEntityType";

        // load entity model
        // get all webservices data
        //$this->__models['entity_plugin_model'] = new EFEntityPlugin;
        $this->_entity_model = $this->_modelPath . $this->_entity_ucfirst;
        $this->_entity_model = new $this->_entity_model;

        $this->_entity_type_model = $this->_modelPath . $this->_entity_type_model;
        $this->_entity_type_model = new $this->_entity_type_model;
        $this->_entity_session_identifier = config("panel.SESS_KEY");

        $this->_assignData['conf_model'] = $this->_modelPath . "Conf";
        $this->_assignData['conf_model'] = new $this->_assignData['conf_model'];
        $this->_assignData["_meta"] = $this->__meta;
        $this->_assignData["_entity_session_identifier"] = $this->_entity_session_identifier;
        // plugin config
        //$this->_plugin_config = $this->__models['entity_plugin_model']->getPluginSchema($this->_entity_id, $this->_plugin_identifier);
        // set defaults
        //$this->_plugin_config = isset($this->_plugin_config->webservices) ? $this->_plugin_config->webservices : array();
        //$this->_plugin_config["webservices"] = $this->_plugin_config;

    }

    /**
     * index
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $this->_entity_model->checkAuth($request);
        // if logged, redirect to dashboard
        return redirect(CustomHelper::getPanelPath().'dashboard');
    }

    /**
     * confirm signup
     *
     * @return Response
     */
    public function confirmSignup(Request $request)
    {
        // restrict logged-in user access
       /* if($this->_entity_model->checkAccess($request)) {
            return redirect(CustomHelper::getPanelPath().'dashboard');
        }*/
        // view file
        $view_file = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', __FUNCTION__));
        // title
        $this->_assignData["page_action"] = "Confirm Signup";
        $this->_assignData["p_title"] = $this->_assignData["page_action"];


        $this->_entity_model->logout(false);
        // get params
        $email = $request->input("email", "");
        $verification_token = $request->input("verification_token", "");

        // post registration
        if ($request->do_post == 1) {

            $route_params = $request->route()->parameters();

            if(isset($route_params['department'])) {
                $department = $route_params['department'];
                // verify this department exist and login user belongs to that department.
                // save department name in session with its entity_type_id
                $exModel = $this->_modelPath . "SYSEntityType";
                $exModel = new $exModel;
                $entityTypeData = $exModel->getEntityTypeByName($department);
                if ($entityTypeData && isset($entityTypeData->entity_type_id)) {
                    $entity_type_id = $entityTypeData->entity_type_id;
                }
            }

            // default target element
            $this->_jsonData['targetElem'] = "p[id=error_msg]";
            // default errors class
            $this->_jsonData['removeClass'] = "hide";
            $this->_jsonData['addClass'] = "show";


            $post_arr = array(
                "login_id" => $email,
                "verification_token" => $verification_token,
                'entity_type_id' => $entity_type_id
            );

            // request api
            $response = $this->__internalCall($request, \URL::to(DIR_API) .'/'.$this->_entity_api_route . "confirm_signup", "post", $post_arr);

            if ($response->error == 1) {
                // msg
                $this->_jsonData['text'] = $response->message;
                $this->_jsonData['targetElem'] = "div[id=error_msg_verification_token]";
            } else {
                $this->_jsonData['removeClass'] = "txt-danger";
                $this->_jsonData['addClass'] = "txt-success";

                // set in session
                \Session::put($this->_entity_identifier . "_og_data", $response->data->{$this->_entity_identifier});
                \Session::save();

                // redirect
               // $this->_jsonData['redirect'] = \URL::to("backend/signup_thankyou");
                \Session::put(ADMIN_SESS_KEY . 'success_msg', "You have successfully active your account");
                $this->_jsonData['redirect'] = \URL::to($this->__getPanelPath($department) . 'login');
            }

            // return
            return $this->_jsonData;
        }

        $this->_assignData["email"] = $email;
        $this->_assignData["verification_token"] = $verification_token;
        // set/show view
        //$this->_layout .= View::make($this->_assignData["p_dir"] . "header", $this->_assignData);
        $this->_layout = View::make($this->_assignData["dir"]. $view_file, $this->_assignData);
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
            $this->_entity_identifier => \Session::get($this->_entity_identifier . "_og_data", NULL)
        );

        // get from session
        $this->_assignData["data"] = $data;

        // set/show view
        $this->_layout = View::make($this->_assignData["p_dir"] . $view_file, $this->_assignData);
        return $this->_layout;
    }

    /**
     * forgot Password
     *
     * @return Response
     */
    public function changePassword(Request $request)
    {
        // view file
        $view_file = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', __FUNCTION__));
        // assign vars to views
        $this->_assignData["page_action"] = "";
        $this->_assignData["s_title"] = "Change Password";
        $this->_assignData["p_title"] = $this->_assignData["s_title"];
        $this->_assignData['_entity_identifier'] = $this->_entity_identifier;
        $this->_assignData['_entity_pk'] = $this->_entity_pk;

        /*$data = array(
            "content" => "Your account is successfully confirmed.",
            $this->_entity_identifier => \Session::get($this->_entity_identifier."_og_data",NULL)
        );*/

        if ($request->post_change_password == 1) {

            return $this->_changePassword($request);
        }
        // get from session
        //$this->_assignData["data"] = $data;

        // set/show view
        $this->_layout = View::make($this->_assignData["dir"] . $view_file, $this->_assignData);
        return $this->_layout;
        //return $this->_layout;
    }

    /**
     * _change_password
     *
     * @return Response
     */
    public function _changePassword(Request $request)
    {
        // default errors class
        $this->_assignData['removeClass'] = "hide";
        $this->_assignData['addClass'] = "show";
        $_POST['entity_id'] = $data = \Session::get($this->_entity_session_identifier . "auth")->entity_id;;
        $_POST['current password'] = $request->current_password;
        $_POST['new_password'] = $request->new_password;
        //$this->_assignData["data"] = $_POST;


        $this->_assignData["data"] = $this->__internalCall($request, \URL::to(DIR_API) .'/'. 'entity_auth/change_password', 'POST', $_POST,false);
        if ($this->_assignData["data"]->error == "1") {
            $assignData['error'] = 1;
            $assignData['message'] = $this->_assignData["data"]->message;
            return $assignData;
        }
        else{
            $assignData['error'] = 0;
            $assignData['message'] = trans('api_errors.change_password_success');
            $assignData['redirect'] = \URL::current();
            return $assignData;
        }

    }

    /**
     * confirm forgot
     *
     * @return Response
     */
    public function confirmForgot(Request $request)
    {
        $entity_type_id = '';
        // restrict logged-in user access
        if($this->_entity_model->checkAccess($request)) {
            return redirect(CustomHelper::getPanelPath().'dashboard');
        }
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
        $route_params = $request->route()->parameters();

        if(isset($route_params['department'])) {
            $department = $route_params['department'];
            // verify this department exist and login user belongs to that department.
            // save department name in session with its entity_type_id
            $exModel = $this->_modelPath . "SYSEntityType";
            $exModel = new $exModel;
            $entityTypeData = $exModel->getEntityTypeByName($department);
            if ($entityTypeData && isset($entityTypeData->entity_type_id)) {
                $entity_type_id = $entityTypeData->entity_type_id;
            }
        }
        // get params
        $identity = $request->input("login_id", "");
        $verification_token = $request->input("verification_token", "");
        $email = $request->input("email", "");
        $request->email = $email;

        if (preg_match("/@/", $identity)) {
            $verification_mode = "email";
        } else {
            $verification_mode = "mobile_no";
        }

        // post registration
        if ($request->do_post == 1) {
            return $this->_confirmForgot($request);

        }

        $row = $this->_entity_model->getUserByEmailAndToken($email, $verification_token);
        if ($row) {
            \Session::put($this->_entity_session_identifier . 'confirm_forgot_entity_auth_id', $row->entity_auth_id);
          //  \Session::put($this->_entity_session_identifier . 'confirm_forgot_entity_type_id', $row->entity_type_id);
        } else {
            \Session::put(ADMIN_SESS_KEY . 'error_msg', trans('panel.invalid_token_or_email'));
        }
        $this->_assignData["email"] = $email;
        $this->_assignData["verification_token"] = $verification_token;
        $this->_assignData["entity_type_id"] = $entity_type_id;
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
            $this->_entity_identifier => \Session::get($this->_entity_identifier . "_og_data", NULL)
        );

        // get from session
        $this->_assignData["data"] = $data;

        // set/show view
        $this->_layout = View::make($this->_assignData["p_dir"] . $view_file, $this->_assignData);
        return $this->_layout;
    }


    /**
     * reset ID
     *
     * @return Response
     */
    public function resetID(Request $request)
    {
        // restrict logged-in user access
        if($this->_entity_model->checkAccess($request)) {
            return redirect(CustomHelper::getPanelPath().'dashboard');
        }
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
        $url = $this->_entity_api_route . "reset_id";

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

            if ($detect->isMobile()) {
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
        } else {
            $data["content"] = $response->message;
        }

        // assign data
        $this->_assignData["data"] = $data;

        // set/show view
        $this->_layout = View::make($this->_assignData["dir"] . $view_file, $this->_assignData);
        return $this->_layout;
    }

    /**
     * dashboard page
     *
     * @return view
     */
    public function dashboard(Request $request)
    {
        // set title
        $this->_assignData['s_title'] = ucfirst(__FUNCTION__);
        $this->_assignData['p_title'] = $this->_assignData['s_title'];

        $this->_entity_model->checkAuth($request);

        //check if user has permission of widgets otherwise display plain dashbaord
        $check_dashboard_permission = $this->_entity_model->checkDashboardAccess();
        if(!$check_dashboard_permission){
            $view_file = "index";
        }
        else{
            $view_file =  __FUNCTION__;
           // $view_file = "index";
        }

        $view = View::make($this->_assignData["dir"] .$view_file, $this->_assignData);
        return $view;
    }

    /**
     * login
     *
     * @return type Array()
     */
    public function login(Request $request)
    {
        $remember_login_token = isset($_COOKIE[$this->_entity_session_identifier . "remember_login_token"]) ? $_COOKIE[$this->_entity_session_identifier . "remember_login_token"] : FALSE;
        $this->_assignData['remember_login_token'] = $remember_login_token;

        // check remember me
        $this->_entity_model->checkCookieAuth();
        // redirect logged

        //Check if user has access of dashboard then display view of dashboard
        //else display default page
       // $redirect_to = $this->_entity_model->checkDashboardAccess();
        $this->_entity_model->redirectLogged("dashboard");

        // page action
        $this->_assignData["page_action"] = ucfirst(__FUNCTION__);
        $this->_assignData["route_action"] = strtolower(__FUNCTION__);
        // validate post form
        if ($request->post_login == "1") {
            return $this->_login($request);
        } elseif ($request->post_forgot == "1") {

            return $this->_forgotPassword($request);
        }

        $view = View::make($this->_assignData["dir"] . __FUNCTION__, $this->_assignData);
        return $view;
    }

    /**
     * _login (private)
     *
     * @return view
     */
    private function _login(Request $request)
    {
        // load models
        $exModel = $this->_modelPath . "SYSEntityType";
        $exModel = new $exModel;

        // trim/escape all
        //$request->merge(array_map('strip_tags', $request->all())); // - pass may have special chars
        $request->merge(array_map('trim', $request->all()));

        $department = \Route::current()->getParameter('department');

        // verify this department exist and login user belongs to that department.
        // save department name in session with its entity_type_id

        $entityType = $exModel->getBy('identifier', $department, true);


        if($entityType && isset($entityType->entity_type_id)){
          $entity_type_id = $entityType->entity_type_id;
        }
        else{
            \Session::put(ADMIN_SESS_KEY . 'error_msg', "You are trying to hit wrong panel");
            $this->_json_data['redirect'] = \URL::to($this->__getPanelPath($department) . 'login');
            return $this->_json_data;
        }

        // filter params
        $request->email = strip_tags($request->email);
        $request->remember = intval(strip_tags($request->remember));
        $request->password = $request->password;

        // validator
        $validate_map = array(
            'email' => 'required|email|exists:' . $this->_entity_model->table,
            'password' => 'required|min:6');

        $validator = $this->__validateInputParams($request->all(), $validate_map);

        // default errors class
        $this->_json_data['removeClass'] = "hide";
        $this->_json_data['addClass'] = "show";
        $this->_json_data['errorFieldID'] = "error_msg_";
        $this->_json_data['error'] = 0;
        // get all modules

        if ($validator['error']) {
            $this->_json_data['error'] = 1;
            $this->_json_data['message'] = $validator['message'];
            $this->_json_data['fields'] = $validator['fields'];
        } else {

            if (preg_match("/@/", $request->email)) {
                $verification_mode = "email";
            } else {
                $verification_mode = "mobile_no";
            }

            $record = $this->_entity_model->checkLogin($request->email, $request->password, $verification_mode, $entity_type_id);
            if($record){

                //first check if status is not active and first time login then active account and update statuses
                if($record->last_login_at == '' && $record->status != 1){
                    $this->_entity_model->confirmSignupUser($record,$entity_type_id);
                    $record = $this->_entity_model->get($record->entity_auth_id);
                }

            }

            if ($record === FALSE) {
                $field_name = "password";
                $this->_json_data['error'] = 1;
                $this->_json_data['message'] = array("The email or password is incorrect");
                $this->_json_data['fields'] = array($field_name);
            } else if ($record->status == 0) {
                $field_name = "password";
                $this->_json_data['error'] = 1;
                $this->_json_data['message'] = array("Your account is inactive, Please contact Administrator.");
                $this->_json_data['fields'] = array($field_name);
            } else if ($record->status > 1) {
                $field_name = "password";
                $this->_json_data['error'] = 1;
                $this->_json_data['message'] = array("Cannot login. Account is banned by Administrator.");
                $this->_json_data['fields'] = array($field_name);
            } else {

                // set record
                $save = (array)$record;
                $save["last_login_at"] = date("Y-m-d H:i:s");
                $save["remember_login_token"] = NULL;

                // remember me
                if ($request->remember > 0) {
                    $remember_login_token = $this->_entity_model->setRememberToken((object)$save, $entity_type_id);
                    $save["remember_login_token"] = $remember_login_token;
                }

                // save
                $this->_entity_model->set($save[$this->_entity_pk], $save);

                // set login session
                 $redirect_url = $this->_entity_model->setLoginSession((object)$save, $entity_type_id);

                //redirect
                $this->_json_data['redirect'] = $redirect_url;
            }
        }

        // return json
        return $this->_json_data;
    }

    /**
     * Return data to admin listing page
     *
     * @return type redirect
     */
    public function logout(Request $request)
    {
        $this->_entity_model->logout();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function _forgotPassword(Request $request)
    {
        // trim/escape all
       // $request->merge(array_map('trim', $request->all()));

        // validator
        $validate_map = array(
            'email' => 'required|email|exists:' . $this->_entity_model->table,
        );

        $validator = $this->__validateInputParams($request->all(), $validate_map);

        // default errors class
        $this->_json_data['removeClass'] = "hide";
        $this->_json_data['addClass'] = "show";
        $this->_json_data['errorFieldID'] = "error_msg_";
        $this->_json_data['error'] = 0;
        // get all modules

        if ($validator['error']) {
            $this->_json_data['error'] = 1;
            $this->_json_data['message'] = $validator['message'];
            $this->_json_data['fields'] = $validator['fields'];
        } else {

            if (IS_CAPTCHA == 1) {

                if (trim($_POST[CAPTCHA_RESPONSE_FIELD]) == "") {
                    $this->_json_data['error'] = 1;
                    $this->_json_data['message'] = array(trans("backend.captcha_required"));
                    $this->_json_data['fields'] = array('captcha');
                    return $this->_json_data;
                }

                $response_captcha = $this->__validateRecaptcha();
                //  echo "<pre>"; print_r($response_captcha);
                if ($response_captcha['error'] == 1) {
                    $this->_json_data['error'] = 1;
                    $this->_json_data['message'] = array($response_captcha['message']);
                    $this->_json_data['fields'] = array('captcha');
                    return $this->_json_data;
                }
            }
            $route_params = $request->route()->parameters();
            $entity_type_id = 2;

            if(isset($route_params['department'])) {
                $department = $route_params['department'];
                // verify this department exist and login user belongs to that department.
                // save department name in session with its entity_type_id
                $exModel = $this->_modelPath . "SYSEntityType";
                $exModel = new $exModel;
                $entityTypeData = $exModel->getEntityTypeByName($department);
                if($entityTypeData && isset($entityTypeData->entity_type_id)){
                    $entity_type_id = $entityTypeData->entity_type_id;
                }
                else{
                    \Session::put(ADMIN_SESS_KEY . 'error_msg', "You are trying to hit wrong panel");
                    $this->_json_data['redirect'] = \URL::current();
                    return $this->_json_data;
                }

            }

                $post_arr = array(
                    'entity_type_id' => $entity_type_id,
                    'login_id' => $request->email,
                );
                // request api
                $response = $this->__internalCall($request, \URL::to(DIR_API) ."/".$this->_entity_api_route . "forgot_request", "post", $post_arr);

                $this->_assignData['error'] = $response->error;

                // if success
                if ($this->_assignData['error'] == 0) {
                    \Session::put(ADMIN_SESS_KEY . 'success_msg', $response->message);
                    $this->_json_data['redirect'] = \URL::current();
                } else {
                    $this->_json_data['error'] = 1;
                    $this->_json_data['message'] = array($response->message);
                    $this->_json_data['fields'] = array('email');
                }



        }

        // return json
        return $this->_json_data;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function _confirmForgot(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('trim', $request->all()));

        // validator
        $validate_map = array(
            'password' => 'required|min:8',
            'confirm_password' => 'required|min:8|same:password',
        );

        $validator = $this->__validateInputParams($request->all(), $validate_map);

        // default errors class
        $this->_json_data['removeClass'] = "hide";
        $this->_json_data['addClass'] = "show";
        $this->_json_data['errorFieldID'] = "error_msg_";
        $this->_json_data['error'] = 0;
        // get all modules

        if ($validator['error']) {
            $this->_json_data['error'] = 1;
            $this->_json_data['message'] = $validator['message'];
            $this->_json_data['fields'] = $validator['fields'];
        } else {

            if (\Session::has($this->_entity_session_identifier . "confirm_forgot_entity_auth_id")) {

                $entity_auth_id = \Session::get($this->_entity_session_identifier . "confirm_forgot_entity_auth_id");
                $entity_type_id = $request->entity_type_id;

                $post_arr = array(
                    'entity_auth_id' => $entity_auth_id,
                    "new_password" => $request->confirm_password,
                    "entity_type_id" => $request->entity_type_id,
                );
                //  print_r($post_arr);
                // request api
                $response = $this->__internalCall($request, \URL::to(DIR_API) ."/".$this->_entity_api_route . "forgot_reset_password", "post", $post_arr);

                // echo "<pre>"; print_r($response);
                if ($response->error == 0) {

                    //Get entity type data
                   $entity_type_model = new SYSEntityType();
                   $entity_type_data =  $entity_type_model->getEntityTypeById($entity_type_id);

                    // forget session
                    \Session::forget($this->_entity_session_identifier . "confirm_forgot_entity_auth_id");
                    //\Session::forget($this->_entity_session_identifier . "confirm_forgot_entity_id");

                    \Session::put(ADMIN_SESS_KEY . 'success_msg', $response->message);

                    if($entity_type_data->identifier == 'customer'){
                        $this->_json_data['redirect'] =  url('/');

                    }else{
                        $this->_json_data['redirect'] = \URL::to($this->_panelPath . 'login');
                       // $this->_json_data['redirect'] = \URL::current();
                    }

                    //$this->_json_data['redirect'] = \URL::current();
                    /*  $this->_jsonData['removeClass'] = "txt-danger";
                      $this->_jsonData['addClass'] = "txt-success";

                      // set in session
                      \Session::put($this->_entity_identifier."_og_data",$response->data->{$this->_entity_identifier});
                      \Session::save();

                      // redirect
                      $this->_jsonData['redirect'] = \URL::to("backend/forgot_thankyou");*/

                } else {
                    $this->_json_data['error'] = 1;
                    $this->_json_data['message'] = array($response->message);
                    $this->_json_data['fields'] = array('confirm_password');
                }
            }

        }
        // return json
        return $this->_json_data;
    }

    /**
     * @param Request $request
     * @return string
     */
    public function confirmSignupBackend(Request $request)
    {
        // view file
        $view_file = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', __FUNCTION__));
        // title
        $this->_assignData["page_action"] = "Reset Your Password";
        $this->_assignData["p_title"] = $this->_assignData["page_action"];

        // get params
        $identity = $request->input("login_id", "");
        $verification_token = $request->input("verification_token", "");
        $email = $request->input("email", "");
        $request->email = $email;


        // post registration
        if ($request->do_post == 1) {
            return $this->_confirmSignupBackend($request);

        }

        $row = $this->_entity_model->getUserByEmailAndEmailToken($email, $verification_token);
        if ($row) {
            \Session::put($this->_entity_session_identifier . 'confirm_signup_entity_id', $row->entity_id);
            \Session::put($this->_entity_session_identifier . 'confirm_signup_entity_type_id', $row->entity_type_id);
        } else {
            \Session::put(ADMIN_SESS_KEY . 'error_msg', trans('panel.invalid_token_or_email'));
        }

        $this->_assignData["email"] = $email;
        $this->_assignData["verification_token"] = $verification_token;

        // set/show view
        //$this->_layout .= View::make($this->_assignData["p_dir"] . "header", $this->_assignData);
        $this->_layout = View::make($this->_assignData["p_dir"] . $view_file, $this->_assignData);

        return $this->_layout;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function _confirmSignupBackend(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('trim', $request->all()));

        // validator
        $validate_map = array(
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|min:8|same:new_password',
        );

        $validator = $this->__validateInputParams($request->all(), $validate_map);

        // default errors class
        $this->_json_data['removeClass'] = "hide";
        $this->_json_data['addClass'] = "show";
        $this->_json_data['errorFieldID'] = "error_msg_";
        $this->_json_data['error'] = 0;
        // get all modules

        if ($validator['error']) {
            $this->_json_data['error'] = 1;
            $this->_json_data['message'] = $validator['message'];
            $this->_json_data['fields'] = $validator['fields'];
        } else {

            if (\Session::has($this->_entity_session_identifier . "confirm_signup_entity_id")) {

                $entity_type_id = \Session::get($this->_entity_session_identifier . "confirm_signup_entity_type_id");
                $entity_id = \Session::get($this->_entity_session_identifier . "confirm_signup_entity_id");

                $post_arr = array(
                    'entity_id' => $entity_id,
                    'current_password' => $request->current_password,
                    "new_password" => $request->confirm_password,
                );
                //  print_r($post_arr);
                // request api
                $response = $this->__internalCall($request, $this->_entity_api_route . "change_password", "post", $post_arr);
                $response = isset($response["jsonEditor"]) ? json_decode($response["jsonEditor"]) : $response;

                //echo "<pre>"; print_r($response); exit;
                if ($response->error == 0) {

                    // forget session
                    \Session::forget($this->_entity_session_identifier . "confirm_signup_entity_type_id");
                    \Session::forget($this->_entity_session_identifier . "confirm_signup_entity_id");

                    \Session::put(ADMIN_SESS_KEY . 'success_msg', 'Your password is changed successfully');
                    $this->_json_data['redirect'] = \URL::to($this->_panelPath . 'login');

                } else {
                    $this->_json_data['error'] = 1;
                    $this->_json_data['message'] = array($response->message);
                    $this->_json_data['fields'] = array('confirm_password');
                }
            } else {

            }

        }
        // return json
        return $this->_json_data;
    }


    /**
     * Update
     *
     * @return view
     */
    public function updateProfile(Request $request)
    {
        // page action
        $this->_assignData["page_action"] = 'Update profile';
        //$this->_assignData["route_action"] = strtolower(__FUNCTION__);

        // validate post form
        if (isset($request->do_post)) {

            return $this->_updateProfile($request);

        }

        $data_entity = \Session::get($this->_entity_session_identifier . "auth");
        $getData['entity_id'] = $data_entity->entity_id;
        $getData['entity_type_id'] = $data_entity->entity_type_id;
       // echo "<pre>"; print_r( $getData);
       // $entity_data = $this->__internalCall($request,\URL::to(DIR_API) . '/system/entities/listing' , 'GET', $getData);
	   $entity_lib = new Entity();
        $entity_data = (object)$entity_lib->apiList($getData);
        $entity_data = json_decode(json_encode($entity_data));
        //echo "<pre>"; print_r( $entity_data);exit;
        $entity_type_model = new SYSEntityType();
        $this->_entity_controller = $entity_type_model->getEntityTypeById($data_entity->entity_type_id);

        if ($entity_data) {
            $this->_assignData["update"] = isset($entity_data->data->entity_listing[0]) ? $entity_data->data->entity_listing[0] : [];
           if(isset($entity_data->data->entity_listing[0]))
            $this->_assignData["update"]->identifier = $this->_entity_controller->identifier;
        }

        $this->_assignData["dir"] = config("panel.DIR") . $this->_object_identifier . '/';

        $data = $this->load_params('entity_auth', 'post');

        $this->_assignData['records'] = $data['records'];


        $this->_assignData['entity_data'] = (object)$this->_entity_controller['attributes'];
        $this->_assignData['form_template_dir'] = "template/";
        $view_file = $this->_assignData["dir"] . 'profile_update';

        $entity_helper = new EntityHelper();
        $this->_assignData = $entity_helper->getEntityAndDependEntityFields($this->_assignData,$request->all());


        $view = View::make($view_file, $this->_assignData);
        return $view;
    }

    /**
     * Update (private)
     *
     * @return view
     */
    private function _updateProfile(Request $request)
    {
        foreach ($_POST as $key => $value) {
            if (is_array($value) && $key != "depend_entity") {
                $sm_values = "";
                foreach ($value as $_data) {
                    $sm_values .= (($sm_values != "") ? ',' : '') . $_data;
                }
                $_POST[$key] = $sm_values;
            }

        }

        $data_entity = \Session::get($this->_entity_session_identifier . "auth");
        $_POST['entity_id'] = $data_entity->entity_id;
        $_POST['entity_type_id'] = $data_entity->entity_type_id;
        $_POST['is_profile_update'] = 1;

           // $this->_assignData["update"] = $this->__internalCall($request,\URL::to(DIR_API) . '/system/entities/update', 'POST', $_POST);
        $this->_assignData["update"]  = (object)$this->_pLib->apiUpdate($_POST);

        if ($this->_assignData["update"]->error == "1") {

            $assignData['error'] = 1;
            $assignData['message'] = $this->_assignData["update"]->message;
            return $assignData;

        } else {
            \Session::put(ADMIN_SESS_KEY . 'success_msg', $this->_assignData["update"]->message);
            //if custom redirection is defined then move to that page
            if(isset($this->_assignData['custom_redirect'])){
                $redirect = $this->_assignData['custom_redirect'];
            }
            else if(isset($custom_redirect)){
                $redirect = $custom_redirect;
            }
            else{  //move to update page
              // $redirect = 'dashboard';
            }


            $assignData['error'] = 0;
            $assignData['message'] = 'Success';
           // $assignData['redirect'] = $redirect;
            return $assignData;

        }
    }

    public function defaultPage(Request $request)
    {
        // set title
        $this->_assignData['s_title'] = "index";
        $this->_assignData['p_title'] = $this->_assignData['s_title'];


        $view = View::make($this->_assignData["dir"] ."index", $this->_assignData);
        return $view;
    }
}
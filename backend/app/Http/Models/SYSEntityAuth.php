<?php
namespace App\Http\Models;

use App\Libraries\Module;
use App\Libraries\System\Entity;
use Illuminate\Database\Eloquent\Model;
// models
use App\Http\Models\Setting;
use App\Http\Models\Conf;
use App\Http\Models\EmailTemplate;
use App\Libraries\CustomHelper;
use Illuminate\Support\Facades\Session;


class SYSEntityAuth extends Base
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'sys_entity_auth';
    protected $dates = ['deleted_at'];
    public $timestamps = true;
    public $primaryKey = 'entity_auth_id';
    protected $_model_path = "\App\Http\Models\\";
    public $_mobile_json = false;
    public $_config_dir = '';

    // enitity vars
    public $_entity_identifier, $_entity_session_identifier, $_entity_dir, $_entity_pk, $_entity_salt_pattren, $_entity_model, $_plugin_identifier, $_has_separate_panel;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //protected $fillable = ['name', 'email', 'password', 'admin_group_id', 'status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_login_token'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        //$this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();
        // entity vars
        $this->_entity_identifier = 'entity_auth';
        $this->_plugin_identifier = NULL;
        $this->_entity_session_identifier = config("panel.SESS_KEY");
        $this->_entity_dir = config("panel.DIR");
        $this->_entity_pk = $this->primaryKey;
        $this->_entity_salt_pattren = config('panel.SALT');
        $this->_entity_model = $this;
        $this->_has_separate_panel = true;
        $this->_config_dir = "panel";
        // set fields
        $this->__fields = array($this->primaryKey, 'email', 'name', 'password', 'mobile_no', 'new_mobile_no', 'image', 'thumb','account_id','account_number','customer_id', 'country_id', 'state_id', 'city_id', 'status', 'platform_type', 'platform_id', 'device_udid', 'device_type', 'device_token', 'email_verification_token', 'is_email_verified', 'email_verified_at', 'is_verified', 'verification_token', 'verified_at', 'mobile_verification_code', 'is_mobile_verified', 'mobile_verification_token', 'mobile_verified_at', 'is_guest', 'remember_login_token', 'remember_login_token_created_at', 'forgot_password_token', 'forgot_password_token_created_at', 'forgot_password_hash_created_at', 'forgot_password_hash', 'sent_email_verification', 'sent_mobile_verification', 'other_data', 'additional_note', 'has_temp_password', 'last_login_at', 'last_seen_at', 'created_at', 'updated_at', 'deleted_at'
        );
    }


    /**
     * Check master access
     * return bool
     */
    function checkAccess($request)
    {
        $key = $this->_entity_session_identifier . "logged";
        //$return = $request->session()->get($key);
        $return = \Session::get($this->_entity_session_identifier . "auth");
        return $return ? $return : FALSE;
    }

    /**
     * checkAuth
     * return redirect
     */
    function checkAuth($request)
    {
        // check basic login
        if ($this->checkAccess($request) === FALSE) {
            // check cookie login
            $this->checkCookieAuth();

            \Session::put($this->_entity_session_identifier . 'error_msg', 'Please login to continue...');
            \Session::put($this->_entity_session_identifier . 'redirect_url', \URL::current());
            $redirect_url = \URL::current();
            // send to login
            $redirect_url = \URL::to(CustomHelper::getPanelPath(\Route::current()->getParameter('department')) . 'login');
            // save session
            \Session::save();
            header("location:" . $redirect_url);
            exit;
        }
        // get auth record
        $record = $this->get(\Session::get($this->_entity_session_identifier . "auth")->{$this->primaryKey});

        // check for inactive account
        if ($record->status <> 1) {
            // set msg
            $msg = $record->status == 0 ? "Your account is inactive. Please contact Administrator" :
                "Cannot process further. Your account is baned";
            // error msg
            \Session::put($this->_entity_session_identifier . 'error_msg', $msg);
            // forget session
            \Session::forget($this->_entity_session_identifier . "logged");
            \Session::forget($this->_entity_session_identifier . "auth");
            $redirect_url = \URL::current();
            // save session
            \Session::save();
            header("location:" . $redirect_url);
            exit;
        }

    }

    /**
     * redirectLogged
     * return redirect
     */
    function redirectLogged($to_page = "")
    {
        if (\Session::has($this->_entity_session_identifier . "auth")) {
            // get logged record
            $record = $this->get(\Session::get($this->_entity_session_identifier . "auth")->{$this->primaryKey});
            if ($record) {
                $redirect_url = $this->_has_separate_panel ? $this->__getPanelPath() : \URL::to("/");
                if (\Session::has($this->_entity_session_identifier . 'redirect_url')) {
                    $redirect_url = \Session::get($this->_entity_session_identifier . 'redirect_url');
                    \Session::forget($this->_entity_session_identifier . 'redirect_url');
                    // save session
                    \Session::save();
                }
                // redirect to entity home
                header("location:" . $to_page);
                exit;
            }
        }
    }


    /**
     * setLoginSession
     * @param $data
     * return redirect_url
     */
    function setLoginSession($data, $entity_type_id = NULL)
    {
        if ($data) {
            // load model
            $roleMapModel = $this->_model_path . "SYSEntityRoleMap";
            $roleMapModel = new $roleMapModel;
            $entityModel = $this->_model_path . "SYSEntity";
            $entityModel = new $entityModel;
            $entityTypeModel = $this->_model_path . "SYSEntityType";
            $entityTypeModel = new $entityTypeModel;

            // get entity type
          /*  $entity_type_id = \Session::get($this->_entity_session_identifier . $entityTypeModel->primaryKey)
                ? \Session::get($this->_entity_session_identifier . $entityTypeModel->primaryKey) : $entity_type_id;*/
            $entityType = $entityTypeModel->get($entity_type_id);

            // get entity data
            $raw_entity = $this->entityQuery($entity_type_id)
                ->where('auth.' . $this->primaryKey, $data->{$this->primaryKey})
                ->select('entity.' . $entityModel->primaryKey)
                ->first();

            // set in session
            $entity = $entityModel->getData($raw_entity->{$entityModel->primaryKey});
            //Get entity role and its parent id and save with auth session
            $role = $roleMapModel->getRoleInfoByEntity($entity->{$entityModel->primaryKey});
            if ($role) {
                $role_id = $role->role_id;
                \Session::put($this->_entity_session_identifier . 'entity_role_id', $role_id);

                $entity->auth->role_id = $role_id;
                $entity->auth->parent_role_id = $role->parent_id;
            }

            \Session::put($this->_entity_session_identifier . "logged", TRUE);
            \Session::put($this->_entity_session_identifier . "auth", $entity);
            \Session::put($this->_entity_session_identifier . "department", $entityType->identifier);
            \Session::put($this->_entity_session_identifier . $entityTypeModel->primaryKey, $entity_type_id);

            // $role_id = $roleMapModel->getRoleByEntity($entity->{$entityModel->primaryKey});
            // save session
            \Session::save();
        }
        // get redirection url
        $dir_path = $this->_has_separate_panel ? \URL::current() : "/";
        $redirect_url = \Session::has($this->_entity_session_identifier . "redirect_url") ? \Session::get($this->_entity_session_identifier . "redirect_url") : \URL::to($dir_path);
        return $redirect_url;
    }

    /**
     * Check master authentication
     * return redirect
     */
    function logout($redirect = true,$message = false)
    {
        // get logged data
        $data = \Session::get($this->_entity_session_identifier . "auth");
        // forget session

        if(!$message){
            \Session::put(ADMIN_SESS_KEY. 'success_msg', "Successfully logged out");

        }else{
            \Session::put(ADMIN_SESS_KEY . 'error_msg', $message);
        }
        \Session::forget($this->_entity_session_identifier . "logged");
        $panel_dir = $this->__getPanelPath();
        \Session::forget($this->_entity_session_identifier . "auth");
        \Session::forget($this->_entity_session_identifier . "department");
        \Session::forget($this->_entity_session_identifier . "entity_type_id");
        \Session::forget($this->_entity_session_identifier . 'entity_role_id');
        \Session::forget($this->_entity_session_identifier . 'redirect_url');
        //$redirect_url = $this->_has_separate_panel ? \URL::to($panel_dir . 'login/') : \URL::to('/');
        // save session
        \Session::save();
        // forget cookie
        $this->removeRememberToken($data);
        if($redirect){
            $redirect_url = \URL::to($panel_dir . 'login/');
            // redirect
            header("location:" . $redirect_url);
            exit;
        }
        return true;
    }

    /**
     * setRememberToken
     * @param object $data
     * return string remember_login_token
     */
    function setRememberToken($data, $entity_type_id)
    {
        $remember_login_token = $this->saltCookie($data);
        // set cookie
        setcookie($this->_entity_session_identifier . "remember_login_token", $remember_login_token, time() + (config($this->_config_dir . '.REMEMBER_COOKIE_TIME')), "/");
        setcookie($this->_entity_session_identifier . "entity_type_id", $entity_type_id, time() + (config($this->_config_dir . '.REMEMBER_COOKIE_TIME')), "/");
        // return token
        return $remember_login_token;
    }

    /**
     * removeRememberToken
     * @param object $data
     * return void
     */
    function removeRememberToken($data)
    {
        if ($data) {
            $record = $this->get($data->{$this->primaryKey});
            $record->remember_login_token = NULL;
            $record->updated_at = date("Y-m-d H:i:s");
            // reset token
            $this->set($record->{$this->primaryKey}, (array)$record);
            // forget cookie
            setcookie($this->_entity_session_identifier . "remember_login_token", FALSE, time() - (config($this->_config_dir . '.REMEMBER_COOKIE_TIME')), "/");
            setcookie($this->_entity_session_identifier . "entity_type_id", FALSE, time() - (config($this->_config_dir . '.REMEMBER_COOKIE_TIME')), "/");
        }
    }

    /**
     * checkCookieAuth
     * @param $data
     * return redirect_url
     */
    function checkCookieAuth()
    {
        // get cookie
        $remember_login_token = isset($_COOKIE[$this->_entity_session_identifier . "remember_login_token"]) ? $_COOKIE[$this->_entity_session_identifier . "remember_login_token"] : FALSE;
        $entity_type_id = isset($_COOKIE[$this->_entity_session_identifier . "entity_type_id"]) ? $_COOKIE[$this->_entity_session_identifier . "entity_type_id"] : FALSE;

        if (($remember_login_token && $entity_type_id) && !\Session::has($this->_entity_session_identifier . "logged")) {
            $data = $this->getBy("remember_login_token", $remember_login_token);
            if ($data) {
                // remove session error/succcess msgs
                \Session::forget($this->_entity_session_identifier . 'success_msg');
                \Session::forget($this->_entity_session_identifier . 'error_msg');

                $redirect_url = $this->setLoginSession($data, $entity_type_id);
                // redirect user
                header("location:" . $redirect_url);
                exit;
            }
        }
    }

    /**
     * saltPassword
     *
     * @return string
     */
    function saltPassword($password = "")
    {
        return $this->_entity_salt_pattren . md5(md5($this->_entity_salt_pattren . sha1($this->_entity_salt_pattren . $password)));
    }

    /**
     * saltCookie
     * @param object $data
     * @return string
     */
    function saltCookie($data, $get_column_query = 0)
    {
        // prepare hash
        $cookie_value = $this->_entity_salt_pattren;
        $cookie_value .= "-" . $data->{$this->primaryKey}; // add pk
        $cookie_value .= "-" . sha1($data->email); // add email
        // encode
        $cookie_value = $this->_entity_salt_pattren . md5($cookie_value);
        return $cookie_value;
    }


    /**
     * checkLogin
     *
     * @return object
     */

    function checkLogin($identity = "", $password = "", $signin_mode = "email", $entity_type_id)
    {
        $enc_password = $this->saltPassword($password);
        //$type = !$entity_type ? config("constants.ALLOWED_ENTITY_TYPES") : $entity_type;
        if ($signin_mode == "mobile_no") {
            // fetch
            $row = $this->entityQuery($entity_type_id)
                ->where('auth.mobile_no', '=', $identity)
                ->where('auth.password', '=', $enc_password)
                ->orderBy("auth.status", "DESC")
                ->orderBy("entity." . $this->primaryKey, "DESC")
                ->get(array($this->__fields[0]));
        } else {
            // fetch
            $query = $this->entityQuery($entity_type_id)
                ->where('auth.email', '=', $identity)
                ->where('auth.password', '=', $enc_password)
                ->orderBy("auth.status", "DESC")
                ->orderBy("entity." . $this->primaryKey, "DESC");

            $row = $query->get(array($this->__fields[0]));
        }

        return isset($row[0]) ? $this->get($row[0]->{$this->primaryKey}) : FALSE;
    }


    function checkUser($identity = "", $signin_mode = "email", $token = "", $entity_type_id = null)
    {
        //$type = !$entity_type ? config("constants.ALLOWED_ENTITY_TYPES") : $entity_type;
        if ($signin_mode == "mobile_no") {

            // fetch
            $row = $this->where('mobile_no', '=', $identity)
                //->where('entity_type_id', '=', $entity_type_id)
                ->where('verification_token', '=', $token)
                ->orderBy($this->primaryKey, "DESC")
                ->whereNull("deleted_at")
                ->get(array($this->__fields[0]));

        } else {

            // fetch
            $row = $this->where('email', '=', $identity)
                //->where('entity_type_id', '=', $entity_type_id)
                ->where('verification_token', '=', $token)
                ->orderBy($this->primaryKey, "DESC")
                ->whereNull("deleted_at")
                ->get(array($this->__fields[0]));
        }
        return $row;
    }


    /**
     * signup user
     *
     * @return ID
     */
    function signup($data, $entity_type_id = false)
    {
        $data = (object)$data;
        // init models
        $conf_model = new Conf;
        $setting_model = new Setting;
        $email_template_model = new EmailTemplate;

        // configuration
        $conf = $conf_model->getBy('key', 'site');
        $conf = json_decode($conf->value);

        $data->created_at = date('Y-m-d H:i:s');

        if (isset($data->password) && $data->password !== "") {
            // saltify & assign password
            $password = $data->password;
            $data->password = $this->saltPassword($data->password);
        }

        $code = str_random(config($this->_config_dir . '.FORGOT_PASS_TOKEN_LENGTH'));
        $code = trim($code . md5(microtime(true)));
        $data->verification_token = $code;

        $entity_type_identifier = "";
        if (isset($entity_type_id) && is_numeric(trim($entity_type_id))) {

            $entityTypeModel = $this->_model_path . "SYSEntityType";
            $entityTypeModel = new $entityTypeModel;
            $entityTypeData = $entityTypeModel->getEntityTypeById($entity_type_id);
            if ($entityTypeData) {
                $entity_type_identifier = $entityTypeData->identifier;
            }
        }

        // if email signup enabled
        if (config($this->_config_dir . '.EMAIL_SIGNUP_ENABLED') && $data->email != "") {
            // generate hash
            //$data->email_verification_token = str_random(config($this->_config_dir . '.SIGNUP_TOKEN_LENGTH') / 2);
            //$data->verification_token = $data->email_verification_token;
            $data->email_verification_token = $data->verification_token;

            // send email to new admin
            # admin email
            $setting = $setting_model->getBy('key', 'admin_email');
            $data->from = $setting->value;
            # admin email name
            $setting = $setting_model->getBy('key', 'admin_email_name');
            $data->from_name = $setting->value;

            // dir
            //$dir = ($mode == "api") ? config($this->_config_dir . '.DIR') : $this->_entity_dir;
            $dir_path = $this->_has_separate_panel ? $this->_entity_dir . $entity_type_identifier : "/";


            if(trim($entity_type_identifier) == 'customer'){
                $login_link =  '+'.$data->mobile_no;
                $email_template_slug = '_signup_customer';
            }
            elseif(trim($entity_type_identifier) == 'driver'){
                $setting = $setting_model->getBy('key', 'driver_app_url');
                $login_link = $setting->value;
                $email_template_slug = '_signup_driver';
            }else{
                $login_link =  \URL::to($dir_path . "login/");
                $email_template_slug = '_signup_confirmation';
            }

            # load email template
            $query = $email_template_model
                ->where("key", "=", $this->_entity_identifier . $email_template_slug)
                ->whereNull("deleted_at");

            if ($this->_plugin_identifier) {
                $query->where("plugin_identifier", "=", $this->_plugin_identifier);
            } else {
                $query->whereNull("plugin_identifier");
            }
            $email_template = $query->first();

            //this is the requirement that no need to send confirmation link directy send login link and  password
            //when user will first time login if not active account then active account
           // $login_link =  \URL::to($dir_path . "confirm_signup/?email=" . $data->email . "&verification_token=" . $data->verification_token);

            $wildcard['key'] = explode(',', $email_template->wildcards);
            $wildcard['replace'] = array(
                $conf->site_name, // APP_NAME
                \URL::to($dir_path), // APP_LINK
                $data->name, // ENTITY_NAME
               $login_link, // CONFIRMATION_LINK
                $data->email, // EMAIL_ADDRESS
                $password // PASSWORD
            );
            # body
             $body = str_replace($wildcard['key'], $wildcard['replace'], $email_template->body);

            # subject
            $data->subject = str_replace($wildcard['key'], $wildcard['replace'], $email_template->subject);
            # send email
            $this->sendMail(
                array($data->email, $data->name),
                $body,
                (array)$data
            );
            // unset non-column data
            unset($data->from, $data->from_name, $data->subject);
            $data->sent_email_verification = 1;
        }
        // if sms signup enabled
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && $data->mobile_no != "") {
            // generate code
            //$data->mobile_verification_token = str_random(config($this->_config_dir . '.SMS_TOKEN_LENGTH'));
            //$data->verification_token = $data->mobile_verification_token;
            $data->mobile_verification_token = $data->verification_token;

            // send sms code (if not in sandbox mode)
            if (!config($this->_config_dir . '.SMS_SANDBOX_MODE')) {
                $this->sendSMS($data, $data->mobile_verification_token, "signup");
            }


            $data->sent_mobile_verification = 1;

        }
        // insert admin record
        //print_r($data); exit;
        if (isset($data->entity_type_id)) {
            $data = $this->unsetEntityData($data, $data->entity_type_id);
        }
        $id = $this->put((array)$data);

        // load / init models
        $entity_history_model = $this->__modelPath . "EntityHistory";
        $entity_history_model = new $entity_history_model;
        // set data for history
        $actor_entity = $this->_entity_identifier;
        $actor_id = $id;
        $identifier = strtolower(__FUNCTION__);
        $plugin_identifier = NULL;
        // other data
        $other_data["navigation_type"] = strtolower(__FUNCTION__);

        //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

        // get data
        $entity_data = $this->get($id);

        // return new id
        return $entity_data;
    }

    /**
     * welcome User
     *
     * @return ID
     */
    function welcome($data)
    {
        $data = is_object($data) ? $data : (object)$data;
        // init models
        $conf_model = new Conf;
        $setting_model = new Setting;
        $email_template_model = new EmailTemplate;

        // configuration
        $conf = $conf_model->getBy('key', 'site');
        $conf = json_decode($conf->value);

        // default updates
        $data->status = 1;
        $data->updated_at = date('Y-m-d H:i:s');
        $data->is_verified = 1;
        $data->verified_at = $data->updated_at;
        $data->verification_token = NULL;
        // in any case, account is verified, clear token from all fields
        $data->mobile_verification_token = $data->email_verification_token = $data->verification_token;
        $data->sent_email_verification = $data->sent_mobile_verification = 0;

        // if email is verified, send welcome email
        if (config($this->_config_dir . '.EMAIL_SIGNUP_ENABLED') && $data->is_email_verified == 1) {
            $data->email_verified_at = $data->updated_at;

            // send email to new admin
            # admin email
            $setting = $setting_model->getBy('key', 'admin_email');
            $data->from = $setting->value;
            # admin email name
            $setting = $setting_model->getBy('key', 'admin_email_name');
            $data->from_name = $setting->value;

            # load email template
            $query = $email_template_model
                ->where("key", "=", $this->_entity_identifier . '_signup_welcome')
                ->whereNull("deleted_at");
            if ($this->_plugin_identifier) {
                $query->where("plugin_identifier", "=", $this->_plugin_identifier);
            } else {
                $query->whereNull("plugin_identifier");
            }
            $email_template = $query->first();

            // dir_path
            $dir_path = $this->_has_separate_panel ? $this->_entity_dir : "/";

            $wildcard['key'] = explode(',', $email_template->wildcards);
            $wildcard['replace'] = array(
                $conf->site_name, // APP_NAME
                \URL::to($dir_path), // APP_LINK
                $data->name, // ENTITY_NAME
                \URL::to($dir_path . "confirm_signup/?email=" . $data->email . "&verification_token=" . $data->verification_token), // CONFIRMATION_LINK
            );


            $body = str_replace($wildcard['key'], $wildcard['replace'], $email_template->body);
            # subject
            $data->subject = str_replace($wildcard['key'], $wildcard['replace'], $email_template->subject);
            # send email
            $this->sendMail(
                array($data->email, $data->name),
                $body,
                (array)$data
            );
            // unset non-column data
            unset($data->from, $data->from_name, $data->subject);
        }

        // if email is verified, send welcome email
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && $data->is_mobile_verified == 1) {

            // site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            $data->mobile_verified_at = $data->updated_at;

//            $message = "Welcome to " . $conf->site_name;
//
//            // send sms
            // send sms code (if not in sandbox mode)
            if (!config($this->_config_dir . '.SMS_SANDBOX_MODE')) {
//            $this->sendSMS($data, "", "welcome", $message);
            }
        }

        // load / init models
        $entity_history_model = $this->__modelPath . "EntityHistory";
        $entity_history_model = new $entity_history_model;
        // set data for history
        $actor_entity = $this->_entity_identifier;
        $actor_id = $data->{$this->primaryKey};
        $identifier = strtolower(__FUNCTION__);
        // other data
        $other_data["navigation_type"] = strtolower(__FUNCTION__);

        //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);
        // set admin record
        if (isset($data->entity_type_id)) {
            $data = $this->unsetEntityData($data, $data->entity_type_id);
        }

       $this->set($data->{$this->primaryKey}, (array)$data);

        // return new id
        return $data;
    }

    /**
     * Generate new
     *
     * @return Admin ID
     */
    function generateNew($data)
    {
        $data = (object)$data;
        // init models
        $conf_model = new Conf;
        $setting_model = new Setting;
        $email_template_model = new EmailTemplate;

        // configuration
        $conf = $conf_model->getBy('key', 'site');
        $conf = json_decode($conf->value);

        $data->created_at = date('Y-m-d H:i:s');

        // temporary password
        $data->password = str_random(config($this->_config_dir . '.FORGOT_PASS_TOKEN_LENGTH') / 2);
        $temp_password = $data->password;
        // saltify & assign password
        $data->password = $this->saltPassword($data->password);
        $data->status = 1;
        // insert admin record
        if (isset($data->entity_type_id)) {
            $data = $this->unsetEntityData($data, $data->entity_type_id);
        }
        $id = $this->put((array)$data);

        // send email to new admin
        # admin email
        $setting = $setting_model->getBy('key', 'admin_email');
        $data->from = $setting->value;
        # admin email name
        $setting = $setting_model->getBy('key', 'admin_email_name');
        $data->from_name = $setting->value;

        # load email template
        $query = $email_template_model
            ->where("key", "=", $this->_entity_identifier . '_new_account')
            ->whereNull("deleted_at");
        if ($this->_plugin_identifier) {
            $query->where("plugin_identifier", "=", $this->_plugin_identifier);
        } else {
            $query->whereNull("plugin_identifier");
        }
        $email_template = $query->first();

        // dir_path
        $dir_path = $this->_has_separate_panel ? $this->_entity_dir : "/";

        $wildcard['key'] = explode(',', $email_template->wildcards);
        $wildcard['replace'] = array(
            $conf->site_name, // APP_NAME
            \URL::to($dir_path), // APP_LINK
            $data->name, // ENTITY_NAME
            $data->email, // ENTITY_EMAIL
            $temp_password, // ENTITY_PASSWORD
        );
        $body = str_replace($wildcard['key'], $wildcard['replace'], $email_template->body);
        # subject
        $data->subject = str_replace($wildcard['key'], $wildcard['replace'], $email_template->subject);
        # send email
        $this->sendMail(
            array($data->email, $data->name),
            $body,
            (array)$data
        );
        // return new id
        return $id;
    }


    /**
     * Forgot Password Request
     * @param object $data
     * @param $verification_mode (email/mobile_no)
     * @return object $data
     */
    function forgotPasswordRequest_old($data, $id_type = "email")
    {
        // init models
        $conf_model = new Conf;
        $setting_model = new Setting;
        $email_template_model = new EmailTemplate;

        // configuration
        $conf = $conf_model->getBy('key', 'site');
        $conf = json_decode($conf->value);

        // fix data type
        $data = is_object($data) ? $data : (object)$data;

        // get code
        $code = str_random(config($this->_config_dir . '.FORGOT_PASS_TOKEN_LENGTH'));
        $code = trim($code . md5(microtime(true)));
        $data->forgot_password_token = $data->verification_token = $code;
        $data->forgot_password_token_created_at = date("Y-m-d H:i:s");
        $entity = json_decode(json_encode($data));
        //$this->set($entity->{$this->primaryKey}, (array)$entity);

        // if email signup enabled
        if (config($this->_config_dir . '.EMAIL_SIGNUP_ENABLED') && ($data->email != "" && $id_type == "email")) {
            // send email to new admin
            # admin email
            $setting = $setting_model->getBy('key', 'admin_email');
            $data->from = $setting->value;
            # admin email name
            $setting = $setting_model->getBy('key', 'admin_email_name');
            $data->from_name = $setting->value;
            # load email template
            $query = $email_template_model
                ->where("key", "=", $this->_entity_identifier . '_forgot_password_confirmation')
                ->whereNull("deleted_at");
            if ($this->_plugin_identifier) {
                $query->where("plugin_identifier", "=", $this->_plugin_identifier);
            } else {
                $query->whereNull("plugin_identifier");
            }
            $email_template = $query->first();

            // dir_path
            $dir_path = $this->_has_separate_panel ? $this->_entity_dir : "/";

            # prepare wildcards
            $wildcard['key'] = explode(',', $email_template->wildcards);
            $wildcard['replace'] = array(
                $conf->site_name, // APP_NAME
                \URL::to($dir_path), // APP_LINK
                $data->name, // ENTITY_NAME
                \URL::to($dir_path . "confirm_forgot/?verification_token=" . $code . "&login_id=" . $data->email), // CONFIRMATION_LINK
            );
            # subject
            $data->subject = str_replace($wildcard['key'], $wildcard['replace'], $email_template->subject);
            # body
            $body = str_replace($wildcard['key'], $wildcard['replace'], $email_template->body);
            # send email
            $this->sendMail(
                array($data->email, $data->name),
                $body,
                (array)$data
            );

            $entity->sent_email_verification = 1;
            $entity->sent_mobile_verification = 0;
        }

        // if sms signup enabled
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && ($data->mobile_no != "" && $id_type == "mobile_no")) {
            // site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            // get code
            $code = str_random(config($this->_config_dir . '.SMS_TOKEN_LENGTH'));
            $message = "Your reset password code is :" . $code;

            // send email
            // send email to new admin
            # admin email
            $setting = $setting_model->getBy('key', 'admin_email');
            $data->from = $setting->value;
            # admin email name
            $setting = $setting_model->getBy('key', 'admin_email_name');
            $data->from_name = $setting->value;
            # load email template


            $query = $email_template_model
                ->where("key", "=", $this->_entity_identifier . '_forgot_mob_password_confirmation')
                ->whereNull("deleted_at");
            if ($this->_plugin_identifier) {
                $query->where("plugin_identifier", "=", $this->_plugin_identifier);
            } else {
                $query->whereNull("plugin_identifier");
            }

            $email_template = $query->first();

            // dir_path
            $dir_path = $this->_has_separate_panel ? $this->_entity_dir : "/";

            # prepare wildcards
            $wildcard['key'] = explode(',', $email_template->wildcards);
            $wildcard['replace'] = array(
                $conf->site_name, // APP_NAME
                \URL::to($dir_path), // APP_LINK
                $data->name, // ENTITY_NAME
                \URL::to($dir_path . "confirm_forgot/?verification_token=" . $code . "&login_id=" . $data->mobile_no), // CONFIRMATION_LINK,
                $code
            );
            # subject
            $data->subject = str_replace($wildcard['key'], $wildcard['replace'], $email_template->subject);
            # body
            $body = str_replace($wildcard['key'], $wildcard['replace'], $email_template->body);

            # send email
            $this->sendMail(
                array($data->email, $data->name),
                $body,
                (array)$data
            );

            // send sms code
            if (!config($this->_config_dir . '.SMS_SANDBOX_MODE')) {
                $this->sendSMS($data, $code, "forgot", $message);
            }
            $entity->mobile_verification_code = $code;
            $entity->sent_mobile_verification = 1;
            $entity->sent_email_verification = 0;
        }

        // update
        if (isset($entity->entity_type_id)) {
            $entity = $this->unsetEntityData($entity, $entity->entity_type_id);
        }
        $this->set($entity->{$this->primaryKey}, (array)$entity);

        // load / init models
        $entity_history_model = $this->__modelPath . "EntityHistory";
        $entity_history_model = new $entity_history_model;
        // set data for history
        $actor_entity = $this->_entity_identifier;
        $actor_id = $data->{$this->primaryKey};
        $identifier = strtolower(__FUNCTION__);
        $plugin_identifier = NULL;
        // other data
        $other_data["navigation_type"] = "forgot_password_request";

        // $entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);


        $data = $this->getData($entity->{$this->primaryKey});
        $data->verification_token = $code;
        return $data;

    }

    /**
     * Forgot Password Request
     * @param object $data
     * @param $verification_mode (email/mobile_no)
     * @return object $data
     */
    function forgotPasswordRequest($data, $id_type = "email", $entity_type_id)
    {
        // init models
        $conf_model = new Conf;
        $setting_model = new Setting;
        $email_template_model = new EmailTemplate;

        // configuration
        $conf = $conf_model->getBy('key', 'site');
        $conf = json_decode($conf->value);

        // fix data type
        $data = is_object($data) ? $data : (object)$data;

        // get code
        $code = str_random(config($this->_config_dir . '.FORGOT_PASS_TOKEN_LENGTH'));
        $code = trim($code . md5(microtime(true)));
       // $data->forgot_password_token = $data->verification_token = $code;
       // $data->forgot_password_token_created_at = date("Y-m-d H:i:s");
        $entity = json_decode(json_encode($data));
        //$this->set($entity->{$this->primaryKey}, (array)$entity);

        $entity_type_identifier = "";
        if (isset($entity_type_id) && is_numeric(trim($entity_type_id))) {

            $entityTypeModel = $this->_model_path . "SYSEntityType";
            $entityTypeModel = new $entityTypeModel;
            $entityTypeData = $entityTypeModel->getEntityTypeById($entity_type_id);
            if ($entityTypeData) {
                $entity_type_identifier = $entityTypeData->identifier . '/';
            }
        }

           $new_password = str_random(8);
           //$new_password = '12345678';

        // if email signup enabled
        if (config($this->_config_dir . '.EMAIL_SIGNUP_ENABLED') && ($data->email != "" && $id_type == "email")) {
            // send email to new admin
            # admin email
            $setting = $setting_model->getBy('key', 'admin_email');
            $data->from = $setting->value;
            # admin email name
            $setting = $setting_model->getBy('key', 'admin_email_name');
            $data->from_name = $setting->value;
            # load email template
            $query = $email_template_model
                ->where("key", "=", $this->_entity_identifier . '_change_password')
                ->whereNull("deleted_at");
            if ($this->_plugin_identifier) {
                $query->where("plugin_identifier", "=", $this->_plugin_identifier);
            } else {
                $query->whereNull("plugin_identifier");
            }
            $email_template = $query->first();

            // dir_path
            $dir_path = $this->_has_separate_panel ? $this->_entity_dir . $entity_type_identifier : "/";

            $name = $data->name;
            $entity_model = new SYSEntity();
            $entity_raw  = $entity_model->getEntityByAuthId($data->entity_auth_id);
            if($entity_raw->entity_id){
                $name  = CustomHelper::setFullName($entity_raw);
            }

            # prepare wildcards
            $wildcard['key'] = explode(',', $email_template->wildcards);
            $wildcard['replace'] = array(
                $conf->site_name, // APP_NAME
                \URL::to($dir_path), // APP_LINK
                $name, // ENTITY_NAME
                //\URL::to($dir_path . "confirm_forgot/?verification_token=" . $code . "&email=" . $data->email), // CONFIRMATION_LINK
                $data->email,
                $new_password
            );

            # subject
            $data->subject = str_replace($wildcard['key'], $wildcard['replace'], $email_template->subject);
            # body
             $body = str_replace($wildcard['key'], $wildcard['replace'], $email_template->body);
            # send email
            $this->sendMail(
                array($data->email, $name),
                $body,
                (array)$data
            );

            $entity->sent_email_verification = 1;
            $entity->sent_mobile_verification = 0;
            $entity->password = $this->saltPassword($new_password);
        }

        // if sms signup enabled
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && ($data->mobile_no != "" && $id_type == "mobile_no")) {
            // site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            $message = "Your reset password code is :" . $code;
            // send sms code
            if (!config($this->_config_dir . '.SMS_SANDBOX_MODE')) {
                $this->sendSMS($data, $code, "forgot", $message);
            }

            $entity->sent_mobile_verification = 1;
            $entity->sent_email_verification = 0;
        }

        // update
        if (isset($entity->entity_type_id)) {
            $entity = $this->unsetEntityData($entity, $entity->entity_type_id);
        }
        $this->set($entity->{$this->primaryKey}, (array)$entity);

        // load / init models
        $entity_history_model = $this->__modelPath . "EntityHistory";
        $entity_history_model = new $entity_history_model;
        // set data for history
        $actor_entity = $this->_entity_identifier;
        $actor_id = $data->{$this->primaryKey};
        $identifier = strtolower(__FUNCTION__);
        $plugin_identifier = NULL;
        // other data
        $other_data["navigation_type"] = "forgot_password_request";

        //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

        $data = $this->getData($entity->{$this->primaryKey}, $entity_type_id);
       // $data->new_password = $new_password;
        return $data;

    }

    /**
     * change ID Request
     * @param object $data
     * @param $verification_mode (email/mobile_no)
     * @return object $data
     */
    function changeIDRequest_old($data, $new_login_id, $id_type = "email")
    {
        // init models
        $conf_model = new Conf;
        $setting_model = new Setting;
        $email_template_model = new EmailTemplate;

        // configuration
        $conf = $conf_model->getBy('key', 'site');
        $conf = json_decode($conf->value);

        // fix data type
        $data = is_object($data) ? $data : (object)$data;

        // get code
        $code = str_random(config($this->_config_dir . '.FORGOT_PASS_TOKEN_LENGTH'));
        $code = trim($code . md5(microtime(true)));
        $data->forgot_password_token = $data->verification_token = $code;
        $data->forgot_password_token_created_at = date("Y-m-d H:i:s");
        $entity = json_decode(json_encode($data));

        //$this->set($data->{$this->primaryKey}, (array)$data);


        // if email signup enabled
        if (config($this->_config_dir . '.EMAIL_SIGNUP_ENABLED') && $id_type == "email") {
            // send email to new admin
            # admin email
            $setting = $setting_model->getBy('key', 'admin_email');
            $data->from = $setting->value;
            # admin email name
            $setting = $setting_model->getBy('key', 'admin_email_name');
            $data->from_name = $setting->value;
            # load email template
            $query = $email_template_model
                ->where("key", "=", $this->_entity_identifier . '_change_id_confirmation')
                ->whereNull("deleted_at");
            if ($this->_plugin_identifier) {
                $query->where("plugin_identifier", "=", $this->_plugin_identifier);
            } else {
                $query->whereNull("plugin_identifier");
            }
            $email_template = $query->first();

            // dir_path
            $dir_path = $this->_has_separate_panel ? $this->_entity_dir : "/";

            # prepare wildcards
            $wildcard['key'] = explode(',', $email_template->wildcards);
            $wildcard['replace'] = array(
                $conf->site_name, // APP_NAME
                \URL::to($dir_path), // APP_LINK
                $data->name, // ENTITY_NAME
                \URL::to($dir_path . "reset_id/?verification_token=" . $code . "&new_login_id=" . $new_login_id), // CONFIRMATION_LINK
            );
            # subject
            $data->subject = str_replace($wildcard['key'], $wildcard['replace'], $email_template->subject);
            # body
            $body = str_replace($wildcard['key'], $wildcard['replace'], $email_template->body);
            # send email
            $this->sendMail(
            //array($data->email, $data->name),
                array($new_login_id, $data->name),
                $body,
                (array)$data
            );

            $entity->sent_email_verification = 1;
            $entity->sent_mobile_verification = 0;
        }

        // if sms signup enabled
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && $id_type == "mobile_no") {
            // site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            $message = "Your reset password code is :" . $code;
            // send sms code (if not in sandbox mode)
            if (!config($this->_config_dir . '.SMS_SANDBOX_MODE')) {
                $this->sendSMS($data, $code, "change_id", $message, $new_login_id);
            }

            $entity->new_mobile_no = 1;
            $entity->sent_mobile_verification = 1;
            $entity->sent_email_verification = 0;
        }

        // update
        $this->set($entity->{$this->primaryKey}, (array)$entity);

        // load / init models
        $entity_history_model = $this->__modelPath . "EntityHistory";
        $entity_history_model = new $entity_history_model;
        // set data for history
        $actor_entity = $this->_entity_identifier;
        $actor_id = $data->{$this->primaryKey};
        $identifier = strtolower(__FUNCTION__);
        $plugin_identifier = NULL;
        // other data
        $other_data["navigation_type"] = "change_id_request";

        //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

        // get new data
        $data = $this->getData($entity->{$this->primaryKey});

        return $data;

    }

    function unsetEntityData($data, $entity_type_id)
    {
        $api_method_field_model = $this->_model_path . "ApiMethodField";
        $api_method_field_model = new $api_method_field_model;
        $listfields = $api_method_field_model->getEntityAttributeList($entity_type_id);
        if ($listfields) {
            foreach ($listfields as $_field) {
                if (isset($data->{$_field->attribute_code})) unset($data->{$_field->attribute_code});
            }
        }
        return $data;
    }

    function changeIDRequest($data, $new_login_id, $id_type = "email")
    {
        // init models
        $conf_model = new Conf;
        $setting_model = new Setting;
        $email_template_model = new EmailTemplate;

        // configuration
        $conf = $conf_model->getBy('key', 'site');
        $conf = json_decode($conf->value);

        // fix data type
        $data = is_object($data) ? $data : (object)$data;

        // get code
        $code = str_random(config($this->_config_dir . '.FORGOT_PASS_TOKEN_LENGTH'));
        $code = trim($code . md5(microtime(true)));
        $data->forgot_password_token = $data->verification_token = $code;
        $data->forgot_password_token_created_at = date("Y-m-d H:i:s");
        $entity = json_decode(json_encode($data));
        //$this->set($data->{$this->primaryKey}, (array)$data);


        // if email signup enabled
        if (config($this->_config_dir . '.EMAIL_SIGNUP_ENABLED') && $id_type == "email") {
            // send email to new admin
            # admin email
            $setting = $setting_model->getBy('key', 'admin_email');
            $data->from = $setting->value;
            # admin email name
            $setting = $setting_model->getBy('key', 'admin_email_name');
            $data->from_name = $setting->value;
            # load email template
            $query = $email_template_model
                ->where("key", "=", $this->_entity_identifier . '_change_id_confirmation')
                ->whereNull("deleted_at");
            if ($this->_plugin_identifier) {
                $query->where("plugin_identifier", "=", $this->_plugin_identifier);
            } else {
                $query->whereNull("plugin_identifier");
            }
            $email_template = $query->first();

            // dir_path
            $dir_path = $this->_has_separate_panel ? $this->_entity_dir : "/";

            # prepare wildcards
            $wildcard['key'] = explode(',', $email_template->wildcards);
            $wildcard['replace'] = array(
                $conf->site_name, // APP_NAME
                \URL::to($dir_path), // APP_LINK
                $data->name, // ENTITY_NAME
                \URL::to($dir_path . "reset_id/?verification_token=" . $code . "&new_login_id=" . $new_login_id), // CONFIRMATION_LINK
            );
            # subject
            $data->subject = str_replace($wildcard['key'], $wildcard['replace'], $email_template->subject);
            # body
            $body = str_replace($wildcard['key'], $wildcard['replace'], $email_template->body);
            # send email
            $this->sendMail(
            //array($data->email, $data->name),
                array($new_login_id, $data->name),
                $body,
                (array)$data
            );

            $entity->sent_email_verification = 1;
            $entity->sent_mobile_verification = 0;
        }

        // if sms signup enabled
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && $id_type == "mobile_no") {
            // site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            $message = "Your reset password code is :" . $code;
            // send sms code (if not in sandbox mode)
            if (!config($this->_config_dir . '.SMS_SANDBOX_MODE')) {
                $this->sendSMS($data, $code, "change_id", $message, $new_login_id);
            }
            $entity->sent_mobile_verification = 1;
            $entity->sent_email_verification = 0;
        }

        // update
        if (isset($entity->entity_type_id)) {
            //  $entity = $this->unsetEntityData($entity, $entity->entity_type_id);
        }


        $this->set($entity->{$this->primaryKey}, (array)$entity);

        // load / init models
        $entity_history_model = $this->__modelPath . "EntityHistory";
        $entity_history_model = new $entity_history_model;
        // set data for history
        $actor_entity = $this->_entity_identifier;
        $actor_id = $data->{$this->primaryKey};
        $identifier = strtolower(__FUNCTION__);
        $plugin_identifier = NULL;
        // other data
        $other_data["navigation_type"] = "change_id_request";

        //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

        // get new data
        $data = $this->getData($entity->{$this->primaryKey});

        return $data;

    }

    /**
     * Forgot Password Request
     * @param object $data
     * @param $verification_mode (email/mobile_no)
     * @return object $data
     */
    function forgotPasswordVerify($data, $mode = "mobile_no")
    {
        // init models
        $conf_model = new Conf;
        $setting_model = new Setting;
        $email_template_model = new EmailTemplate;

        // configuration
        $conf = $conf_model->getBy('key', 'site');
        $conf = json_decode($conf->value);

        // fix data type
        $data = is_object($data) ? $data : (object)$data;

        // get code
        $code = str_random(config($this->_config_dir . '.FORGOT_PASS_TOKEN_LENGTH'));
        $code = trim($code . md5(microtime(true)));
        $data->forgot_password_token = $data->verification_token = $code;
        $data->forgot_password_token_created_at = date("Y-m-d H:i:s");
        $entity = json_decode(json_encode($data));
        //$this->set($data->{$this->primaryKey}, (array)$data);


        // if email signup enabled
        if (config($this->_config_dir . '.EMAIL_SIGNUP_ENABLED') && $mode = "email") {
            /*// send email to new admin
            # admin email
            $setting = $setting_model->getBy('key', 'admin_email');
            $data->from = $setting->value;
            # admin email name
            $setting = $setting_model->getBy('key', 'admin_email_name');
            $data->from_name = $setting->value;
            # load email template
            $query = $email_template_model
                ->where("key", "=", $this->_entity_identifier . '_forgot_password_confirmation')
                ->whereNull("deleted_at");
            if ($this->_plugin_identifier) {
                $query->where("plugin_identifier", "=", $this->_plugin_identifier);
            } else {
                $query->whereNull("plugin_identifier");
            }
            $email_template = $query->first();

            // dir_path
            $dir_path = $this->_has_separate_panel ? $this->_entity_dir : "/";

            # prepare wildcards
            $wildcard['key'] = explode(',', $email_template->wildcards);
            $wildcard['replace'] = array(
                $conf->site_name, // APP_NAME
                \URL::to($dir_path), // APP_LINK
                $data->name, // ENTITY_NAME
                \URL::to($dir_path . "confirm_forgot/?hash=" . $code . "&email=" . $data->email."&verification_mode=email"), // CONFIRMATION_LINK
            );
            # subject
            $data->subject = str_replace($wildcard['key'], $wildcard['replace'], $email_template->subject);
            # body
            $body = str_replace($wildcard['key'], $wildcard['replace'], $email_template->body);
            # send email
            $this->sendMail(
                array($data->email, $data->name),
                $body,
                (array)$data
            );

            $entity->sent_email_verification = 1;*/
        }

        // if sms signup enabled
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && $mode = "mobile_no") {
            /*// site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            $message = "Your reset password code is :" . $code;
            // send sms code (if not in sandbox mode)
            if (!config($this->_config_dir . '.SMS_SANDBOX_MODE')) {
                $this->sendSMS($data, $code, "forgot", $message);
            }


            $entity->sent_mobile_verification = 1;*/
        }

        // update
        if (isset($entity->entity_type_id)) {
            $entity = $this->unsetEntityData($entity, $entity->entity_type_id);
        }
        $this->set($entity->{$this->primaryKey}, (array)$entity);

        // load / init models
        $entity_history_model = $this->__modelPath . "EntityHistory";
        $entity_history_model = new $entity_history_model;
        // set data for history
        $actor_entity = $this->_entity_identifier;
        $actor_id = $data->{$this->primaryKey};
        $identifier = strtolower(__FUNCTION__);
        $plugin_identifier = NULL;
        // other data
        $other_data["navigation_type"] = "forgot_password_verify";

        //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

        return $data;

    }

    /**
     * Forgot Password Success
     * @param object $data
     * @return object $data
     */
    function forgotPasswordSuccess($data)
    {
        // init models
        $conf_model = new Conf;
        $setting_model = new Setting;
        $email_template_model = new EmailTemplate;

        // configuration
        $conf = $conf_model->getBy('key', 'site');
        $conf = json_decode($conf->value);

        // fix data type
        $data = is_object($data) ? $data : (object)$data;

        // reset code
        $data->forgot_password_token = $data->verification_token = NULL;
        $data->forgot_password_token_created_at = NULL;
        $data->sent_email_verification = $data->sent_mobile_verification = 0;
        // assign new password
        $new_password = str_random(config($this->_config_dir . '.FORGOT_PASS_TOKEN_LENGTH') / 2);
        $data->password = $this->saltPassword($new_password);
        if (isset($data->entity_type_id)) {
            $data = $this->unsetEntityData($data, $data->entity_type_id);
        }
        $this->set($data->{$this->primaryKey}, (array)$data);

        // if email signup enabled
        if (config($this->_config_dir . '.EMAIL_SIGNUP_ENABLED') && $data->email != "") {
            // send email to new admin
            # admin email
            $setting = $setting_model->getBy('key', 'admin_email');
            $data->from = $setting->value;
            # admin email name
            $setting = $setting_model->getBy('key', 'admin_email_name');
            $data->from_name = $setting->value;
            # load email template
            $query = $email_template_model
                ->where("key", "=", $this->_entity_identifier . '_forgot_password_success')
                ->whereNull("deleted_at");
            if ($this->_plugin_identifier) {
                $query->where("plugin_identifier", "=", $this->_plugin_identifier);
            } else {
                $query->whereNull("plugin_identifier");
            }
            $email_template = $query->first();

            // dir_path
            $dir_path = $this->_has_separate_panel ? $this->_entity_dir : "/";

            # prepare wildcards
            $wildcard['key'] = explode(',', $email_template->wildcards);
            $wildcard['replace'] = array(
                $conf->site_name, // APP_NAME
                \URL::to($dir_path), // APP_LINK
                //\URL::to($dir_path."forgot_password"), //ADMIN_FORGOT_LINK
                $data->name, // ENTITY_NAME
                $data->email, // ENTITY_EMAIL
                $new_password, // ENTITY_PASSWORD
            );
            # subject
            $data->subject = str_replace($wildcard['key'], $wildcard['replace'], $email_template->subject);
            # body
            $body = str_replace($wildcard['key'], $wildcard['replace'], $email_template->body);
            # send email
            $this->sendMail(
                array($data->email, $data->name),
                $body,
                (array)$data
            );
        }

        // if sms signup enabled
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && $data->mobile_no != "") {
            // site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            $message = "Your new password is : " . $new_password;
            // send sms code (if not in sandbox mode)
            if (!config($this->_config_dir . '.SMS_SANDBOX_MODE')) {
                $this->sendSMS($data, $new_password, "new_password", $message);
            }

        }

        // load / init models
        $entity_history_model = $this->__modelPath . "EntityHistory";
        $entity_history_model = new $entity_history_model;
        // set data for history
        $actor_entity = $this->_entity_identifier;
        $actor_id = $data->{$this->primaryKey};
        $identifier = strtolower(__FUNCTION__);
        $plugin_identifier = NULL;
        // other data
        $other_data["navigation_type"] = "forgot_password_success";

        //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

        return $new_password;

    }


    /**
     * Change Password
     * @param object $data
     * @return object $data
     */
    function changePassword($data)
    {
        // init models
        $conf_model = new Conf;
        $setting_model = new Setting;
        $email_template_model = new EmailTemplate;

        // configuration
        $conf = $conf_model->getBy('key', 'site');
        $conf = json_decode($conf->value);

        // fix data type
        $data = is_object($data) ? $data : (object)$data;

        // assign new password
        $new_password = $data->password;
        $data->password = $this->saltPassword($data->password);
        $data->has_temp_password = 0;
        if (isset($data->entity_type_id)) {
            $data = $this->unsetEntityData($data, $data->entity_type_id);
        }
        $this->set($data->{$this->primaryKey}, (array)$data);

        // if email signup enabled
        if (config($this->_config_dir . '.EMAIL_SIGNUP_ENABLED') && $data->email != "") {
            // send email to new admin
            # admin email
            $setting = $setting_model->getBy('key', 'admin_email');
            $data->from = $setting->value;
            # admin email name
            $setting = $setting_model->getBy('key', 'admin_email_name');
            $data->from_name = $setting->value;
            # load email template
            $query = $email_template_model
                ->where("key", "=", $this->_entity_identifier . '_change_password')
                ->whereNull("deleted_at");
            if ($this->_plugin_identifier) {
                $query->where("plugin_identifier", "=", $this->_plugin_identifier);
            } else {
                $query->whereNull("plugin_identifier");
            }
            $email_template = $query->first();

            // dir_path
            $dir_path = $this->_has_separate_panel ? $this->_entity_dir : "/";

            $name = $data->name;
            $entity_model = new SYSEntity();
            $entity_raw  = $entity_model->getEntityByAuthId($data->entity_auth_id);
            if($entity_raw->entity_id){
                $name  = CustomHelper::setFullName($entity_raw);
            }


            # prepare wildcards
            $wildcard['key'] = explode(',', $email_template->wildcards);
            $wildcard['replace'] = array(
                $conf->site_name, // APP_NAME
                \URL::to($dir_path), // APP_LINK
                $name, // ENTITY_NAME
                $data->email, // ENTITY_EMAIL
                $new_password, // ENTITY_PASSWORD
            );
            # subject
            $data->subject = str_replace($wildcard['key'], $wildcard['replace'], $email_template->subject);
            # body
            $body = str_replace($wildcard['key'], $wildcard['replace'], $email_template->body);
            # send email
            $this->sendMail(
                array($data->email, $name),
                $body,
                (array)$data
            );
        }

        // if sms signup enabled
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && $data->mobile_no != "") {
            // site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            //$message = "Your new password is : " . $new_password;
            $message = "You password have been changed successfully";
            // send sms code (if not in sandbox mode)
            if (!config($this->_config_dir . '.SMS_SANDBOX_MODE')) {
                $this->sendSMS($data, $new_password, "new_password", $message);
            }

        }

        // load / init models
        $entity_history_model = $this->__modelPath . "EntityHistory";
        $entity_history_model = new $entity_history_model;
        // set data for history
        $actor_entity = $this->_entity_identifier;
        $actor_id = $data->{$this->primaryKey};
        $identifier = strtolower(__FUNCTION__);
        $plugin_identifier = NULL;
        // other data
        $other_data["navigation_type"] = "change_password";

        // $entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

        return $new_password;

    }


    /**
     * Get Age
     * @param string $date format YYYY-mm-dd
     * @return Query
     */
    public function getAge($date = "")
    {
        //replace / with - so strtotime works
        $dob = strtotime(str_replace("/", "-", $date));
        $tdate = time();
        $age = date('Y', $tdate) - date('Y', $dob);
        return $age;
    }

    /**
     * get data
     * @param int id
     * @return Query
     */
    public function getAttributeData($entity_type_id, $entity_id)
    {

        $SYSEntity = $this->_model_path . "SYSEntity";
        $SYSEntity = new $SYSEntity();
        $data = array();
        $q = $SYSEntity->getEntityAttributeValues($entity_type_id, $entity_id, $this->_lang);
        if ($entity_type_id != "0" && $entity_id != "0") {
            if (count($q[0]->value) > 0) {
                // get attributes
                $attrs = \DB::select($q[0]->value);
                // if found
                if (isset($attrs[0])) {
                    foreach ($attrs as $attr) {
                        if (!$this->_mobile_json) {
                            $data['attributes'][$attr->name] = $attr->attribute;
                        } else {
                            $data[$attr->name] = $attr->attribute;
                        }
                    }
                }
            }
        }
        return $data;
    }


    /**
     * get data
     * @param int id
     * @return Query
     */
    public function getData($pk_id = 0, $entity_type_id = 0, $update_last_seen = FALSE)
    {
        // init models
        $exModel = $this->_model_path . "SYSEntity";
        $exModel = new $exModel;

        $data = NULL;

        if ($pk_id > 0) {

            if ($entity_type_id > 0) {
                $raw_entity = $this->entityQuery($entity_type_id)
                    ->select("auth." . $this->primaryKey)
                    ->where('auth.' . $this->primaryKey, '=', $pk_id)
                    ->first();
                $raw_entity = json_decode(json_encode($raw_entity));
                $data = $this->get(
                    isset($raw_entity->{$this->primaryKey}) ? $raw_entity->{$this->primaryKey} : 0
                );
            } else
                $data = $this->get($pk_id);


            if ($update_last_seen) {
                $data->last_seen_at = $update_last_seen === TRUE ? date("Y-m-d H:i:s") : $update_last_seen;
                $this->set($pk_id, (array)$data);
            }

            // attach entity data
            //$data->entity = $exModel->getData($auth->{$exModel->primaryKey});

            //$dir_img = config($this->_config_dir . '.DIR_IMG');

            // set image directories
            /*if ($data->image != "") {
                $data->image = \URL::to($dir_img . $data->image);
            }
            if ($data->thumb != "") {
                $data->thumb = \URL::to($dir_img . $data->thumb);
            }*/

            // mobile_no
            if(isset($data->mobile_no)){
                $data->mobile_no = $data->mobile_no != "" ? "+" . str_replace('+','',$data->mobile_no) : $data->mobile_no;
            }

            if ($data) {
                unset($data->password, $data->mobile_verification_token, $data->remember_login_token, $data->forgot_password_token, $data->forgot_password_token_created_at);
            }

        }
        $data = $this->authData($data);
        return $data;
    }


    /**
     * get minimum data
     * @param int id
     * @return Query
     */


    public function getMiniData($id)
    {
        $data = $this->getData($id);

        if ($data !== FALSE) {
            $data2 = (object)array();
            $data2->{$this->primaryKey} = $data->{$this->primaryKey};
            $data2->name = $data->name;
            $data2->first_name = (isset($data->attributes)) ? $data->attributes['first_name'] : (isset($data->first_name) ? $data->first_name : '');
            $data2->last_name = (isset($data->attributes)) ? $data->attributes['last_name'] : (isset($data->last_name) ? $data->last_name : '');
            $data2->entity_id = $data->entity_id;
            $data2->entity_type_id = $data->entity_type_id;
            $data2->image = $data->image;
            $data2->thumb = $data->thumb;
            unset($data);
            $data = $data2;
        }
        return $data;
    }

    /**
     * send sms to user
     * @param int id
     * @return Query
     */
    public function sendSMS($userdata, $code, $event = "signup", $message = "", $new_number = null)
    {
        // fix data type
        $userdata = is_object($userdata) ? $userdata : (object)$userdata;

        // overrite new number if exists
        $new_number = trim($new_number);
        $mobile_no = isset($userdata->mobile_no) ? $userdata->mobile_no : $new_number;
        if (isset($userdata->mobile_no)) {
            $mobile_no = $new_number != "" ? $new_number : $userdata->mobile_no;
        } else {
            $mobile_no = $new_number;
        }

        if ($mobile_no != "") {
            // init models
            $conf_model = new Conf;

            // twilio configurations
            $config = $conf_model->getBy("key", "twilio_config");
            $twilio = json_decode($config->value);

            //$userdata->mobile_no = !$new_number ? $userdata->mobile_no : $new_number;

            //$number_data = explode("-", $userdata->mobile_no);
            $number_data = explode("-", $mobile_no);
            $country_code = str_replace("+", "", $number_data[0]);
            $mobile_no = str_replace(array("+" . $country_code), "", $number_data[1]);

            //send for signup (use authy)
            //if ($event == "signup") {
            // Twilio Authy starts
            $authy_api = new \Authy\AuthyApi($twilio->api_key);

            $verify = $authy_api->phoneVerificationStart($mobile_no, $country_code, 'sms');

            if (empty($verify->ok())) {
                //$send_sms->message();
            }
            // Twilio Authy ends
            //} else {
            // set constants for twilio sms
//                \Config::set("twilio.twilio.connections.twilio.sid", $twilio->account_sid);
//                \Config::set("twilio.twilio.connections.twilio.token", $twilio->token);
//                \Config::set("twilio.twilio.connections.twilio.from", $twilio->from);
//
//                // Twilio sms starts
//                try {
//                    $response = \Twilio::message("+" . $country_code . $mobile_no, $message);
//                } catch (\Services_Twilio_RestException $e) {
//                    //$e->getMessage();
//                }
            // Twilio sms ends
            //}


            //


        }


    }

    /**
     * verify phone
     * @param int id
     * @return Query
     */
    public function verifyPhone($mobile_no)
    {
        // init models
        $conf_model = new Conf;

        // twilio configurations
        $config = $conf_model->getBy("key", "twilio_config");
        $twilio = json_decode($config->value);

        $number_data = explode("-", $mobile_no);
        $country_code = str_replace("+", "", $number_data[0]);
        $mobile_no = str_replace(array($country_code, "-", "+"), "", $mobile_no);

        // Twilio Authy starts
        $authy_api = new \Authy\AuthyApi($twilio->api_key);

        $verify = $authy_api->phoneVerificationStart($mobile_no, $country_code, 'sms');

        if (empty($verify->ok())) {
            //$send_sms->message();
        }

        //var_dump($verify);


    }


    /**
     * remove unverified accounts with newly confirmed email/mobile_no
     * @param int id
     * @return void
     */
    public function removeUnverified($entity, $new_login_id, $verification_mode = "email")
    {
        $entity = is_object($entity) ? $entity : (object)$entity;

        // find other accounts with this mobile number/email, which are not verified (we assume those are junk accounts now)
        $query = $this->select($this->primaryKey)
            ->where("is_verified", "!=", 1)
            ->where($this->primaryKey, "!=", $entity->{$this->primaryKey})
            ->whereNull("deleted_at");
        if ($verification_mode == "email") {
            $query->where("email", "=", $new_login_id);
        } else {
            $query->where("mobile_no", "=", $new_login_id);
        }
        $raw_ids = $query->get();
        // if found, remove
        if (isset($raw_ids[0])) {
            foreach ($raw_ids as $raw_id) {
                $this->remove($raw_id->{$this->primaryKey});
            }
        }

    }


    /**
     * getDataByEntityID
     *
     * @return object
     */

    function getDataByEntityID($entity_id, $min = false)
    {
        // ex model
        $exModel = $this->_model_path . "SYSEntity";
        $exModel = new $exModel;

        // fetch
        $row = $this->where($exModel->primaryKey, '=', ${$exModel->primaryKey})
            ->whereNull("deleted_at")
            ->get(array($this->primaryKey));

        if (isset($row[0]) && $min)
            return $this->getMiniData($row[0]->{$this->primaryKey});
        elseif (isset($row[0])) return $this->getData($row[0]->{$this->primaryKey});
        return false;
    }


    /**
     * send mobile verification code
     *
     * @return ID
     */
    function sendMobileVerificationCode($data)
    {
        // fix data type
        $data = is_object($data) ? $data : (object)$data;

        // sending twilio verification code
        $code = str_random(config($this->_config_dir . '.FORGOT_PASS_TOKEN_LENGTH'));
        $code = trim($code . md5(microtime(true)));
        $sent_mobile_verification = 0;

        // if sms signup enabled
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED')) {
            // send sms code (if not in sandbox mode)
            if (!config($this->_config_dir . '.SMS_SANDBOX_MODE')) {
                $this->sendSMS($data, $code, "signup");
            }
            $sent_mobile_verification = 1;
        }

        // set data
        $data->is_verified = $data->is_mobile_verified = 0;
        $data->verification_token = $code;
        $data->sent_mobile_verification = $sent_mobile_verification;
        $data->updated_at = date('Y-m-d h:i:s');
        // update
        if (isset($data->entity_type_id)) {
            $data = $this->unsetEntityData($data, $data->entity_type_id);
        }
        $this->set($data->{$this->_entity_pk}, (array)$data);

        // return data
        return $data;
    }


    /**
     * getDataByEntityID
     *
     * @return object
     */

    function entityQuery($entity_type_id)
    {
        // ex model
        $exModel = $this->_model_path . "SYSEntity";
        $exModel = new $exModel;
        $exModel2 = $this->_model_path . "SYSEntityType";
        $exModel2 = new $exModel2;

        return $this->select("auth." . $this->primaryKey)
            ->from($this->table . " AS auth")
            ->join($exModel->table . " AS entity", "entity." . $this->primaryKey, "=", "auth." . $this->primaryKey)
            //->where("entity.".$exModel2->primaryKey,"=",${$exModel2->primaryKey})
            ->where("entity." . $exModel2->primaryKey, "=", $entity_type_id)
            ->whereNull("entity.deleted_at")
            ->whereNull("auth.deleted_at");
    }

    function getUserByEmail($email = "")
    {
        $row = $this->where('email', '=', $email)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0]) ? $row[0] : FALSE;
    }

    /**
     * @param string $email
     * @param $token
     * @return bool
     */
    function getUserByEmailAndToken($email = "", $token = "")
    {
        $row = $this->where('email', '=', $email)
            ->where('verification_token', '=', $token)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0]) ? $row[0] : FALSE;
    }

    /**
     * @param string $entity_id
     * @param string $entity_type_id
     * @return bool
     */
    function getUserByEntityID($entity_id = "", $entity_type_id = "")
    {
        if ($entity_type_id != "") {
            $row = $this->where('entity_id', '=', $entity_id)
                ->where('entity_type_id', '=', $entity_type_id)
                ->whereNull("deleted_at")
                ->get();
        } else {
            $row = $this->where('entity_id', '=', $entity_id)
                ->whereNull("deleted_at")
                ->get();
        }

        return isset($row[0]) ? $row[0] : FALSE;
    }

    function getEntityByAuthAndEntityType($entity_auth_id, $entity_type_id = false)
    {
        if (!$entity_type_id) {
            $entity_type_id = \Session::get($this->_entity_session_identifier . 'entity_type_id');
        }

        if ($entity_type_id) {
            $exModel = $this->_model_path . "SYSEntity";
            $exModel = new $exModel;
            $entity_id = $exModel->getEntityByAuthAndEntityType($entity_auth_id, $entity_type_id);

            return $entity_id;
        }

        return false;
    }

    /**
     * Check Dashboard Widgets access
     * @return bool
     */
    function checkDashboardAccess()
    {
        if(Session::has($this->_entity_session_identifier . "auth")){

            $entity = Session::get($this->_entity_session_identifier . "auth");

            if(isset($entity->auth->role_id)){
                $role_permission_model = new SYSRolePermission();
                //Get dashbaord widget module id
                $module_lib = new Module();
                $module = $module_lib->getModuleBySlug('dashboard_widget');
                if($module){
                    $module_id = $module->module_id;
                    $check_permission = $role_permission_model->checkAccess($module_id, "view", $entity->auth->role_id);
                    if (!$check_permission) {
                        return false;
                    }
                }

            }

        }
        return TRUE;
    }

    /**
     * restrict to user if  already loggedin to panel then donot switch to other department panel
     */
    function checkRequestedDepartment($request)
    {
        $requested_department = \Route::current()->getParameter('department');
        $session_department = Session::get($this->_entity_session_identifier . "department");

        if($requested_department && $session_department){

            if(!in_array($requested_department,array('super_admin','business_user'))){

                \Session::put($this->_entity_session_identifier . 'error_msg', 'You are trying to hit wrong url');
                \Session::save();
                $redirect_url = \URL::to(CustomHelper::getPanelPath($session_department) . 'dashboard');
                header("location:" . $redirect_url);
                exit;
                // return redirect($redirect_url);
            }

            $exModel = new SYSEntityType();
            $entityType = $exModel->getBy('identifier', $requested_department, true);

            if($entityType && isset($entityType->entity_type_id)) {

                if($session_department != $requested_department){
                    //$this->_entity_model->logout(false);
                    \Session::put($this->_entity_session_identifier . 'error_msg', 'You are trying to hit wrong url');
                    \Session::save();
                    $redirect_url = \URL::to(CustomHelper::getPanelPath($session_department) . 'dashboard');
                    header("location:" . $redirect_url);
                    exit;
                }
            }

        }
    }

    function checkActiveSessionUser($request)
    {
        $session_entity = Session::get($this->_entity_session_identifier . "auth");
        if($session_entity){
            $entity_auth =  $this->get($session_entity->entity_auth_id);
            if(!$entity_auth || $entity_auth->deleted_at != '' || $entity_auth->status != 1){

                $message = 'Please contact administrator, Your account is removed or banned.';
                $this->logout(true,$message);
            }
        }
    }

    /**
     * confirm signup user and update entity attribute status
     * @param $entity
     * @param $entity_type_id
     */
    public function confirmSignupUser($entity,$entity_type_id)
    {
        $data = $this->get($entity->{$this->primaryKey});

        if($data && isset($data->{$this->primaryKey})){

            $entity_auth_id =  $data->{$this->primaryKey};

            $data->is_email_verified = 1;
            $data->status = 1;
            $data->updated_at = date('Y-m-d H:i:s');
            $data->is_verified = 1;
            $data->verified_at = $data->updated_at;
            $data->verification_token = NULL;
            // in any case, account is verified, clear token from all fields
            $data->mobile_verification_token = $data->email_verification_token = $data->verification_token;
            $data->sent_email_verification = $data->sent_mobile_verification = 0;
            // if email is verified, send welcome email
            if (config($this->_config_dir . '.EMAIL_SIGNUP_ENABLED') && $data->is_email_verified == 1) {
                $data->email_verified_at = $data->updated_at;
            }
            $this->set($data->{$this->primaryKey}, (array)$data);

            //Get entity id by auth id and entity type
            $entity_id = $this->getEntityByAuthAndEntityType($entity_auth_id, $entity_type_id);

            if ($entity_id) {
                //update entity status
                $entity_lib = new Entity();
                $params['entity_type_id'] = $entity_type_id;
                $params['entity_id'] = $entity_id;
                $params['user_status'] = 1;
                $params['is_profile_update'] = 1;
               $return =  $entity_lib->apiUpdate($params);

               //echo '<pre>'; print_r($return); exit;
            }

        }


    }

    /**
     * @return bool
     */
    public function getSessionEntity()
    {
        if(Session::has($this->_entity_session_identifier . "auth")){
            return Session::get($this->_entity_session_identifier . "auth");
        }
        return false;
    }

    /**
     * @param $entity_type_id
     * @param $email
     * @return bool
     */
    function checkUserInOtherDepartment($entity_type_id,$email)
    {
        $row = $this->select("entity_type.title","entity_type.entity_type_id")
            ->from($this->table . " AS auth")
            ->join("sys_entity as e","e.entity_auth_id",'=',"auth." . $this->primaryKey)
            ->join("sys_entity_type AS entity_type", "e.entity_type_id", "=", "entity_type.entity_type_id")
            ->where("entity_type.entity_type_id", "<>", $entity_type_id)
            ->where('auth.email','=',"$email")
            ->where('auth.is_verified','=',1)
            ->whereNull("e.deleted_at")
            ->whereNull("auth.deleted_at")
            ->get();

        return isset($row[0]) ? $row[0] : FALSE;
    }

    public function authData($auth,$mobile_json = false)
    {
       // if($mobile_json){

            if(isset($auth->entity_auth_id)){

                unset($auth->image);
                unset($auth->thumb);
                unset($auth->country_id);
                unset($auth->state_id);
                unset($auth->city_id);
               // unset($auth->remember_login_token_created_at);
               // unset($auth->forgot_password_hash_created_at);
                //unset($auth->forgot_password_hash);
                unset($auth->other_data);
               unset($auth->additional_note);
                unset($auth->has_temp_password);
               /* unset($auth->last_login_at);
                unset($auth->last_seen_at);
                unset($auth->last_login_at);*/

               // echo '<pre>'; print_r($auth);
            }
        //}

        return $auth;

    }


}
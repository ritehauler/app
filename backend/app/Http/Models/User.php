<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
// models
use App\Http\Models\Setting;
use App\Http\Models\Conf;
use App\Http\Models\EmailTemplate;


class User extends Base
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'user';
    protected $dates = ['deleted_at'];
    public $timestamps = true;
    public $primaryKey = 'user_id';

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
        $this->_entity_identifier = $this->table;
        $this->_plugin_identifier = NULL;
        $this->_entity_session_identifier = config('pl_' . $this->_entity_identifier . '.SESS_KEY');
        $this->_entity_dir = config('pl_' . $this->_entity_identifier . '.DIR_PANEL');
        $this->_entity_pk = "user_id";
        $this->_entity_salt_pattren = config('pl_' . $this->_entity_identifier . '.SALT');
        $this->_entity_model = $this;
        $this->_has_separate_panel = false;

        // set fields
        $this->__fields = array($this->primaryKey, "type", 'name', 'first_name', 'last_name', 'user_name', 'email', 'password', 'dob', 'gender', 'image', 'thumb', 'status', 'platform_type', 'platform_id', 'device_udid', 'device_type', "mobile_no", "city_id", "country_id", "state_id", "zip_code", 'device_type', 'device_token', 'is_verified', 'mobile_verified_at', 'is_email_verified', 'verification_token', 'is_mobile_verified', 'mobile_verification_token', "sent_email_verification", "sent_mobile_verification", 'is_guest', 'additional_note', 'other_data', 'remember_login_token', 'remember_login_token_created_at', 'forgot_password_token', 'forgot_password_token_created_at', "has_temp_password",'last_login_at', 'last_seen_at', 'created_at', 'updated_at', 'deleted_at');
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
            $redirect_url = $this->_has_separate_panel ? \URL::to($this->_entity_dir . 'login/') : \URL::to('login/');

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
            $redirect_url = $this->_has_separate_panel ? \URL::to($this->_entity_dir . 'login/') : \URL::to('login/');
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
    function redirectLogged()
    {
        if (\Session::has($this->_entity_session_identifier . "auth")) {
            // get logged record
            $record = $this->get(\Session::get($this->_entity_session_identifier . "auth")->{$this->primaryKey});
            if ($record) {
                $redirect_url = $this->_has_separate_panel ? \URL::to($this->_entity_dir) : \URL::to("/");
                if (\Session::has($this->_entity_session_identifier . 'redirect_url')) {
                    $redirect_url = \Session::get($this->_entity_session_identifier . 'redirect_url');
                    \Session::forget($this->_entity_session_identifier . 'redirect_url');
                    // save session
                    \Session::save();
                }
                // redirect to entity home
                header("location:" . $redirect_url);
                exit;
            }
        }
    }


    /**
     * setLoginSession
     * @param $data
     * return redirect_url
     */
    function setLoginSession($data)
    {
        if ($data) {
            // set in session
            \Session::put($this->_entity_session_identifier . "logged", TRUE);
            \Session::put($this->_entity_session_identifier . "auth", $this->getData($data->{$this->primaryKey}));
            // save session
            \Session::save();
        }
        // get redirection url
        $dir_path = $this->_has_separate_panel ? $this->_entity_dir : "/";
        $redirect_url = \Session::has($this->_entity_session_identifier . "redirect_url") ? \Session::get($this->_entity_session_identifier . "redirect_url") : \URL::to($dir_path);
        return $redirect_url;
    }

    /**
     * Check master authentication
     * return redirect
     */
    function logout()
    {
        // get logged data
        $data = \Session::get($this->_entity_session_identifier . "auth");
        // forget session
        \Session::put($this->_entity_session_identifier . 'success_msg', "Successfully logged out");
        \Session::forget($this->_entity_session_identifier . "logged");
        \Session::forget($this->_entity_session_identifier . "auth");
        $redirect_url = $this->_has_separate_panel ? \URL::to($this->_entity_dir . 'login/') : \URL::to('/');
        // save session
        \Session::save();
        // forget cookie
        $this->removeRememberToken($data);
        // redirect
        header("location:" . $redirect_url);
        exit;
    }

    /**
     * setRememberToken
     * @param object $data
     * return string remember_login_token
     */
    function setRememberToken($data)
    {
        $remember_login_token = $this->saltCookie($data);
        // set cookie
        setcookie($this->_entity_session_identifier . "remember_login_token", $remember_login_token, time() + (config('pl_' . $this->_entity_identifier . '.REMEMBER_COOKIE_TIME')), "/");
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
            setcookie($this->_entity_session_identifier . "remember_login_token", FALSE, time() - (config('pl_' . $this->_entity_identifier . '.REMEMBER_COOKIE_TIME')), "/");
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
        if ($remember_login_token && !\Session::has($this->_entity_session_identifier . "logged")) {
            $data = $this->getBy("remember_login_token", $remember_login_token);
            if ($data) {
                // remove session error/succcess msgs
                \Session::forget($this->_entity_session_identifier . 'success_msg');
                \Session::forget($this->_entity_session_identifier . 'error_msg');

                $redirect_url = $this->setLoginSession($data);
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
    function checkLogin($identity = "", $password = "", $signin_mode = "email", $entity_type = null)
    {
        $enc_password = $this->saltPassword($password);
        $type = !$entity_type ? config("constants.ALLOWED_ENTITY_TYPES") : $entity_type;
        if ($signin_mode == "mobile_no") {
            // fetch
            $row = $this->where('mobile_no', '=', $identity)
                ->where('type', '=', $type)
                ->where('password', '=', $enc_password)
                ->orderBy($this->primaryKey, "DESC")
                ->whereNull("deleted_at")
                ->get(array($this->__fields[0]));
        } else {
            // fetch
            $row = $this->where('email', '=', $identity)
                ->where('type', '=', $type)
                ->where('password', '=', $enc_password)
                ->orderBy($this->primaryKey, "DESC")
                ->whereNull("deleted_at")
                ->get(array($this->__fields[0]));
        }


        return isset($row[0]) ? $this->get($row[0]->{$this->primaryKey}) : FALSE;
    }


    /**
     * signup user
     *
     * @return ID
     */
    function signup($data, $mode = "normal")
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
            $data->password = $this->saltPassword($data->password);
        }

        $code = str_random(config('pl_' . $this->_entity_identifier . '.FORGOT_PASS_TOKEN_LENGTH'));
        $code = trim($code . md5(microtime(true)));
        $data->verification_token = $code;


        // if email signup enabled
        if (config('pl_' . $this->_entity_identifier . '.EMAIL_SIGNUP_ENABLED') && $data->email != "") {
            // generate hash
            //$data->email_verification_token = str_random(config('pl_' . $this->_entity_identifier . '.SIGNUP_TOKEN_LENGTH') / 2);
            //$data->verification_token = $data->email_verification_token;
            $data->email_verification_token = $data->verification_token;

            // send email to new admin
            # admin email
            $setting = $setting_model->getBy('key', 'admin_email');
            $data->from = $setting->value;
            # admin email name
            $setting = $setting_model->getBy('key', 'admin_email_name');
            $data->from_name = $setting->value;

            # load email template
            $query = $email_template_model
                ->where("key", "=", $this->_entity_identifier . '_signup_confirmation')
                ->whereNull("deleted_at");
            if ($this->_plugin_identifier) {
                $query->where("plugin_identifier", "=", $this->_plugin_identifier);
            } else {
                $query->whereNull("plugin_identifier");
            }
            $email_template = $query->first();

            // dir
            //$dir = ($mode == "api") ? config('pl_' . $this->_entity_identifier . '.DIR') : $this->_entity_dir;
            $dir_path = $this->_has_separate_panel ? $this->_entity_dir : "/";

            $wildcard['key'] = explode(',', $email_template->wildcards);
            $wildcard['replace'] = array(
                $conf->site_name, // APP_NAME
                \URL::to($dir_path), // APP_LINK
                $data->name, // ENTITY_NAME
                \URL::to($dir_path . "confirm_signup/?email=" . $data->email . "&verification_token=" . $data->email_verification_token), // CONFIRMATION_LINK
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
        if (config('pl_' . $this->_entity_identifier . '.SMS_SIGNUP_ENABLED') && $data->mobile_no != "") {
            // generate code
            //$data->mobile_verification_token = str_random(config('pl_' . $this->_entity_identifier . '.SMS_TOKEN_LENGTH'));
            //$data->verification_token = $data->mobile_verification_token;
            $data->mobile_verification_token = $data->verification_token;

            // send sms code (if not in sandbox mode)
            if (!config('pl_' . $this->_entity_identifier . '.SMS_SANDBOX_MODE')) {
                $this->sendSMS($data, $data->mobile_verification_token, "signup");
            }


            $data->sent_mobile_verification = 1;

        }
        // insert admin record
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

        $entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

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
        if (config('pl_' . $this->_entity_identifier . '.EMAIL_SIGNUP_ENABLED') && $data->is_email_verified == 1) {
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
        if (config('pl_' . $this->_entity_identifier . '.SMS_SIGNUP_ENABLED') && $data->is_mobile_verified == 1) {

            // site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            $data->mobile_verified_at = $data->updated_at;

//            $message = "Welcome to " . $conf->site_name;
//
//            // send sms
            // send sms code (if not in sandbox mode)
            if (!config('pl_' . $this->_entity_identifier . '.SMS_SANDBOX_MODE')) {
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

        $entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);
        // set admin record
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
        $data->password = str_random(config('pl_' . $this->_entity_identifier . '.FORGOT_PASS_TOKEN_LENGTH') / 2);
        $temp_password = $data->password;
        // saltify & assign password
        $data->password = $this->saltPassword($data->password);
        $data->status = 1;
        // insert admin record
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
    function forgotPasswordRequest($data, $id_type = "email")
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
        $code = str_random(config('pl_' . $this->_entity_identifier . '.FORGOT_PASS_TOKEN_LENGTH'));
        $code = trim($code . md5(microtime(true)));
        $data->forgot_password_token = $data->verification_token = $code;
        $data->forgot_password_token_created_at = date("Y-m-d H:i:s");
        $entity = json_decode(json_encode($data));
        //$this->set($entity->{$this->primaryKey}, (array)$entity);

        // if email signup enabled
        if (config('pl_' . $this->_entity_identifier . '.EMAIL_SIGNUP_ENABLED') && ($data->email != "" && $id_type == "email")) {
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
                \URL::to($dir_path . "confirm_forgot/?verification_token=" . $code . "&email=" . $data->email), // CONFIRMATION_LINK
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
        if (config('pl_' . $this->_entity_identifier . '.SMS_SIGNUP_ENABLED') && ($data->mobile_no != "" && $id_type == "mobile_no")) {
            // site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            $message = "Your reset password code is :" . $code;
            // send sms code
            if (!config('pl_' . $this->_entity_identifier . '.SMS_SANDBOX_MODE')) {
                $this->sendSMS($data, $code, "forgot", $message);
            }

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
        $other_data["navigation_type"] = "forgot_password_request";

        $entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);


        $data = $this->getData($entity->{$this->primaryKey});

        return $data;

    }

    /**
     * change ID Request
     * @param object $data
     * @param $verification_mode (email/mobile_no)
     * @return object $data
     */
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
        $code = str_random(config('pl_' . $this->_entity_identifier . '.FORGOT_PASS_TOKEN_LENGTH'));
        $code = trim($code . md5(microtime(true)));
        $data->forgot_password_token = $data->verification_token = $code;
        $data->forgot_password_token_created_at = date("Y-m-d H:i:s");
        $entity = json_decode(json_encode($data));
        //$this->set($data->{$this->primaryKey}, (array)$data);


        // if email signup enabled
        if (config('pl_' . $this->_entity_identifier . '.EMAIL_SIGNUP_ENABLED') && $id_type == "email") {
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
        if (config('pl_' . $this->_entity_identifier . '.SMS_SIGNUP_ENABLED') && $id_type == "mobile_no") {
            // site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            $message = "Your reset password code is :" . $code;
            // send sms code (if not in sandbox mode)
            if (!config('pl_' . $this->_entity_identifier . '.SMS_SANDBOX_MODE')) {
                $this->sendSMS($data, $code, "change_id", $message, $new_login_id);
            }


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

        $entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

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
        $code = str_random(config('pl_' . $this->_entity_identifier . '.FORGOT_PASS_TOKEN_LENGTH'));
        $code = trim($code . md5(microtime(true)));
        $data->forgot_password_token = $data->verification_token = $code;
        $data->forgot_password_token_created_at = date("Y-m-d H:i:s");
        $entity = json_decode(json_encode($data));
        //$this->set($data->{$this->primaryKey}, (array)$data);


        // if email signup enabled
        if (config('pl_' . $this->_entity_identifier . '.EMAIL_SIGNUP_ENABLED') && $mode = "email") {
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
        if (config('pl_' . $this->_entity_identifier . '.SMS_SIGNUP_ENABLED') && $mode = "mobile_no") {
            /*// site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            $message = "Your reset password code is :" . $code;
            // send sms code (if not in sandbox mode)
            if (!config('pl_' . $this->_entity_identifier . '.SMS_SANDBOX_MODE')) {
                $this->sendSMS($data, $code, "forgot", $message);
            }


            $entity->sent_mobile_verification = 1;*/
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
        $other_data["navigation_type"] = "forgot_password_verify";

        $entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

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
        $new_password = str_random(config('pl_' . $this->_entity_identifier . '.FORGOT_PASS_TOKEN_LENGTH') / 2);
        $data->password = $this->saltPassword($new_password);
        $this->set($data->{$this->primaryKey}, (array)$data);

        // if email signup enabled
        if (config('pl_' . $this->_entity_identifier . '.EMAIL_SIGNUP_ENABLED') && $data->email != "") {
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
        if (config('pl_' . $this->_entity_identifier . '.SMS_SIGNUP_ENABLED') && $data->mobile_no != "") {
            // site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            $message = "Your new password is : " . $new_password;
            // send sms code (if not in sandbox mode)
            if (!config('pl_' . $this->_entity_identifier . '.SMS_SANDBOX_MODE')) {
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

        $entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

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
        $this->set($data->{$this->primaryKey}, (array)$data);

        // if email signup enabled
        if (config('pl_' . $this->_entity_identifier . '.EMAIL_SIGNUP_ENABLED') && $data->email != "") {
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

            # prepare wildcards
            $wildcard['key'] = explode(',', $email_template->wildcards);
            $wildcard['replace'] = array(
                $conf->site_name, // APP_NAME
                \URL::to($dir_path), // APP_LINK
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
        if (config('pl_' . $this->_entity_identifier . '.SMS_SIGNUP_ENABLED') && $data->mobile_no != "") {
            // site configurations
            $conf = $conf_model->getBy('key', 'site');
            $conf = json_decode($conf->value);

            //$message = "Your new password is : " . $new_password;
            $message = "You password have been changed successfully";
            // send sms code (if not in sandbox mode)
            if (!config('pl_' . $this->_entity_identifier . '.SMS_SANDBOX_MODE')) {
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

        $entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

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
    public function getData($pk_id = 0, $update_last_seen = FALSE)
    {
        $data = $this->get($pk_id);

        if ($update_last_seen) {
            $data->last_seen_at = $update_last_seen === TRUE ? date("Y-m-d H:i:s") : $update_last_seen;
            $this->set($pk_id, (array)$data);
        }

        $dir_img = config('pl_' . $this->_entity_identifier . '.DIR_IMG');

        // set image directories
        if ($data->image != "") {
            $data->image = \URL::to($dir_img . $data->image);
        }
        if ($data->thumb != "") {
            $data->thumb = \URL::to($dir_img . $data->thumb);
        }

        // mobile_no
        //$data->mobile_no = $data->mobile_no != "" ? "+".str_replace("-","",$data->mobile_no) : $data->mobile_no;
        $data->mobile_no = $data->mobile_no != "" ? "+" . $data->mobile_no : $data->mobile_no;

        if ($data !== FALSE) {
            unset($data->password, $data->mobile_verification_token, $data->remember_login_token, $data->forgot_password_token, $data->forgot_password_token_created_at, $data->deleted_at);
        }

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

        if ($userdata->mobile_no != "") {
            // init models
            $conf_model = new Conf;

            // twilio configurations
            $config = $conf_model->getBy("key", "twilio_config");
            $twilio = json_decode($config->value);

            $userdata->mobile_no = !$new_number ? $userdata->mobile_no : $new_number;

            $number_data = explode("-", $userdata->mobile_no);
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

    public function getWin($id)
    {
        // load models
        $pModel = $this->__modelPath . "Raffle";
        $pModel = new $pModel;
        $pModel2 = $this->__modelPath . "RaffleEntry";
        $pModel2 = new $pModel2;
        $pModel3 = $this->__modelPath . "User";
        $pModel3 = new $pModel3;

        $data = $this->selectRaw("COALESCE(COUNT(re.`raffle_entry_id`),0) AS total_win, COALESCE(SUM(r.`jackpot_amount`),0) AS total_amount")
            ->from($pModel2->table . " AS re")
            ->leftJoin($pModel->table . " AS r", "r." . $pModel->primaryKey, "=", "re." . $pModel->primaryKey)
            ->where("re." . $pModel3->primaryKey, "=", $id)
            ->whereNull("re.deleted_at")
            ->first();
        $data = json_decode(json_encode($data));
        return $data;
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


}
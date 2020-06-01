<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
// models
use App\Http\Models\Setting;
use App\Http\Models\Conf;
use App\Http\Models\EmailTemplate;

class Vendor extends Base {

        /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'vendor';
    protected $dates = ['deleted_at'];
    public $timestamps = true;
	public $primaryKey = 'vendor_id';
    
    // enitity vars
	private $_entity_identifier, $_entity_session_identifier, $_entity_dir, $_entity_pk, $_entity_salt_pattren, $_entity_model;

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
	
	 public function __construct() {
        // set tables and keys
        $this->__table = $this->table;
        //$this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();
		// entity vars
		$this->_entity_identifier = "vendor";
		$this->_entity_session_identifier = config('pl_vendor.SESS_KEY');
		$this->_entity_dir = config('pl_vendor.DIR_PANEL');
		$this->_entity_pk = "vendor_id";
		$this->_entity_salt_pattren = config('pl_vendor.SALT');
		$this->_entity_model = $this;

        // set fields
        $this->__fields = array($this->primaryKey,'name','first_name','last_name','user_name','email','password','dob','gender','image','thumb','status','platform_type','device_udid','device_type','device_type','device_token','is_verified','verification_token','is_mobile_verified','mobile_verification_code','is_guest','additional_note','other_data','remember_login_token', 'remember_login_token_created_at', 'forgot_password_hash', 'forgot_password_hash_created_at','last_login_at','created_at','updated_at','deleted_at');
    }
	
	
	/**
     * Check master access
     * return bool
     */
    function checkAccess($request) {
		$key = $this->_entity_session_identifier."logged";
		$return = $request->session()->get($key);
		return $return ? $return : FALSE;
    }
	
	/**
     * checkAuth
     * return redirect
     */
    function checkAuth($request) {
		// check basic login
		if ($this->checkAccess($request) === FALSE) {
			// check cookie login
			$this->checkCookieAuth();
			
            \Session::put($this->_entity_session_identifier.'error_msg', 'Please login to continue...');
			\Session::put($this->_entity_session_identifier.'redirect_url', \URL::current());
            $redirect_url = \URL::to($this->_entity_dir.'login/');
			// save session
			\Session::save();
			header("location:" . $redirect_url);
			exit;
        }
		// get auth record
		$record = $this->get(\Session::get($this->_entity_session_identifier."auth")->{$this->primaryKey});
		
		// check for inactive account
		if($record->status <> 1) {
			// set msg
			$msg = $record->status == 0 ? "Your account is inactive. Please contact Administrator" :
				"Cannot process further. Your account is baned";
			// error msg
			\Session::put($this->_entity_session_identifier.'error_msg', $msg);
			// forget session
			\Session::forget($this->_entity_session_identifier."logged");
			\Session::forget($this->_entity_session_identifier."auth");
			$redirect_url = \URL::to($this->_entity_dir.'login/');
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
    function redirectLogged() {
		if(\Session::has($this->_entity_session_identifier."auth")) {
			// get logged record
			$record = $this->get(\Session::get($this->_entity_session_identifier."auth")->{$this->primaryKey});
			if($record) {
				// redirect to entity home
				header("location:" . \URL::to($this->_entity_dir));
				exit;
			}
		}
    }
	
	
	/**
     * setLoginSession
	 * @param $data
     * return redirect_url
     */
    function setLoginSession($data) {
		if($data) {
			// set in session
			\Session::put($this->_entity_session_identifier."logged", TRUE);
			\Session::put($this->_entity_session_identifier."auth", $this->getData($data->{$this->primaryKey}));
			// save session
			\Session::save();
		}
		// get redirection url
		$redirect_url = \Session::has($this->_entity_session_identifier."redirect_url") ? \Session::get($this->_entity_session_identifier."redirect_url") : \URL::to($this->_entity_dir);
		return $redirect_url;
    }
	
	/**
     * Check master authentication
     * return redirect
     */
    function logout() {
		// get logged data
		$data = \Session::get($this->_entity_session_identifier."auth");
		// forget session
		\Session::put($this->_entity_session_identifier.'success_msg', "Successfully logged out");
		\Session::forget($this->_entity_session_identifier."logged");
		\Session::forget($this->_entity_session_identifier."auth");
		$redirect_url = \URL::to($this->_entity_dir.'login/');
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
    function setRememberToken($data) {
		$remember_login_token = $this->saltCookie($data);
		// set cookie
		setcookie($this->_entity_session_identifier."remember_login_token", $remember_login_token, time() + (config('pl_vendor.REMEMBER_COOKIE_TIME')), "/");
		// return token
		return $remember_login_token;
	}
	
	/**
     * removeRememberToken
	 * @param object $data
     * return void
     */
    function removeRememberToken($data) {
		if($data) {
			$record = $this->get($data->{$this->primaryKey});
			$record->remember_login_token = NULL;
			$record->updated_at = date("Y-m-d H:i:s");
			// reset token
			$this->set($record->{$this->primaryKey},(array)$record);
			// forget cookie
			setcookie($this->_entity_session_identifier."remember_login_token", FALSE, time() - (config('pl_vendor.REMEMBER_COOKIE_TIME')), "/");
		}
	}
	
	/**
     * checkCookieAuth
	 * @param $data
     * return redirect_url
     */
    function checkCookieAuth() {
		// get cookie
		$remember_login_token = isset($_COOKIE[$this->_entity_session_identifier."remember_login_token"]) ? $_COOKIE[$this->_entity_session_identifier."remember_login_token"] : FALSE;
		if($remember_login_token && !\Session::has($this->_entity_session_identifier."logged")) {
			$data = $this->getBy("remember_login_token", $remember_login_token);
			if($data) {
				// remove session error/succcess msgs
				\Session::forget($this->_entity_session_identifier.'success_msg');
				\Session::forget($this->_entity_session_identifier.'error_msg');
				
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
    function saltPassword($password = "") {
		return $this->_entity_salt_pattren . md5(md5($this->_entity_salt_pattren . sha1($this->_entity_salt_pattren . $password)));
    }
	
	/**
     * saltCookie
     * @param object $data
     * @return string
     */
    function saltCookie($data, $get_column_query = 0) {
		// prepare hash
		$cookie_value = $this->_entity_salt_pattren;
		$cookie_value .= "-".$data->{$this->primaryKey}; // add pk
		$cookie_value .= "-".sha1($data->email); // add email
		// encode
		$cookie_value = $this->_entity_salt_pattren.md5($cookie_value);
		return $cookie_value;
    }
	
	
	/**
     * checkLogin
     * 
     * @return object
     */
	function checkLogin($email = "", $password = "") {
        $enc_password = $this->saltPassword($password);
        // fetch
        $row = $this->where('email', '=', $email)
			->where('password', '=', $enc_password)
            ->get(array($this->__fields[0]));

        return isset($row[0]) ? $this->get($row[0]->{$this->__fields[0]}) : FALSE;
    }
	
	
	/**
     * Generate new
     *
     * @return Admin ID
     */
    function generateNew($data) {
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
		$data->password = str_random(config('pl_vendor.FORGOT_PASS_HASH_LENGTH') / 2);
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
		$email_template = $email_template_model->getBy('key', $this->_entity_identifier.'_new_account');
		$wildcard['key'] = explode(',', $email_template->wildcards);
		$wildcard['replace'] = array(
			$conf->site_name, // APP_NAME
			\URL::to($this->_entity_dir), // APP_LINK
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
     * Forgot Password
     * @param object $data
     * @return object $data
     */
    function forgotPassword($data) {
        // init models
        $conf_model = new Conf;
        $setting_model = new Setting;
        $email_template_model = new EmailTemplate;

        // configuration
        $conf = $conf_model->getBy('key', 'site');
        $conf = json_decode($conf->value);
		
		// get code
		$code = str_random(config('pl_vendor.FORGOT_PASS_HASH_LENGTH'));
		$data->forgot_password_hash = $code;
		$data->forgot_password_hash_created_at = date("Y-m-d H:i:s");
		$this->set($data->{$this->primaryKey}, (array)$data);

		// send email to new admin
		# admin email
		$setting = $setting_model->getBy('key', 'admin_email');
		$data->from = $setting->value;
		# admin email name
		$setting = $setting_model->getBy('key', 'admin_email_name');
		$data->from_name = $setting->value;
		# load email template
		$email_template = $email_template_model->getBy('key', $this->_entity_identifier.'_forgot_password_confirmation');
		# prepare wildcards
		$wildcard['key'] = explode(',', $email_template->wildcards);
		$wildcard['replace'] = array(
			$conf->site_name, // APP_NAME
			\URL::to($this->_entity_dir), // APP_LINK
			$data->name, // ENTITY_NAME
			\URL::to($this->_entity_dir."confirm_forgot/?code=".$code."&email=".$data->email), // CONFIRMATION_LINK
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
		
		return $data;

    }
	
	/**
     * Recover Password Success
     * @param object $data
     * @return object $data
     */
    function recoverPasswordSuccess($data, $new_password) {
        // init models
        $conf_model = new Conf;
        $setting_model = new Setting;
        $email_template_model = new EmailTemplate;

        // configuration
        $conf = $conf_model->getBy('key', 'site');
        $conf = json_decode($conf->value);
		
		// reset code
		$data->forgot_password_hash = "";
		$data->forgot_password_hash_created_at = NULL;
		// assign new password
		$data->password = $this->saltPassword($new_password);
		$this->set($data->{$this->primaryKey}, (array)$data);

		// send email to new admin
		# admin email
		$setting = $setting_model->getBy('key', 'admin_email');
		$data->from = $setting->value;
		# admin email name
		$setting = $setting_model->getBy('key', 'admin_email_name');
		$data->from_name = $setting->value;
		# load email template
		$email_template = $email_template_model->getBy('key', $this->_entity_identifier.'_recover_password_success');
		# prepare wildcards
		$wildcard['key'] = explode(',', $email_template->wildcards);
		$wildcard['replace'] = array(
			$conf->site_name, // APP_NAME
			\URL::to($this->_entity_dir), // APP_LINK
			//\URL::to($this->_entity_dir."forgot_password"), //ADMIN_FORGOT_LINK
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
		
		return $data;

    }


}
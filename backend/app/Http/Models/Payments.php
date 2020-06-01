<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
// models
use App\Http\Models\Setting;
use App\Http\Models\Conf;
use App\Http\Models\EmailTemplate;


class Payments extends Base
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
    public $_apiData = array();

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


        $stripe = config("services.stripe");
        $stripe = array(
            "secret_key"      => $stripe['key'],
            "publishable_key" => $stripe['secret']
        );

        \Stripe\Stripe::setApiKey($stripe['secret_key']);
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


    public function PaymentProcess($data){

        \Stripe\Stripe::setApiKey('sk_test_1eP8ZZnOp6HKB5fH0KHGIgk4');
        $response = \Stripe\Token::create(array(
            "card" => array(
                "number"    => $data['card_number'],
                "exp_month" => $data['expiration_month'],
                "exp_year"  => $data['expiration_year'],
                "cvc"       => $data['cvc'],
                "name"      => $data['card_name']
            )));
        try {
            $charge = \Stripe\Charge::create(array(
                    "amount" => 1000,
                    "currency" => "usd",
                    "source" => $response->id,
                    "description" => $data['card_name'])
            );

            $this->_apiData['charge']= $charge->__toArray(true);
        }catch(\Stripe\Error\Card $e){
            $this->_apiData['charge'] =  $e->getMessage();
        }
        return $this->_apiData;
    }



}
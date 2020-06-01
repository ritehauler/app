<?php
namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\EFEntityPlugin;
use App\Http\Models\Conf;
use App\Http\Models\SYSModule;
use Stripe;


//use Twilio;

class PaymentController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_model_path = "\App\Http\Models\\";
    private $_object_identifier = "sys_entity_type";
    private $_sys_entity_type_identifier = "sys_entity_type"; // usually routes path
    private $_sys_entity_type_pk = "entity_type_id";
    private $_sys_entity_type_ucfirst = "EntityType";
    private $_payment_model = "Payments";
    private $_plugin_config = array();
    protected $_target_entity_identifier = "payments";


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load sys_entity_type model
        $this->_payment_model = $this->_model_path . $this->_payment_model;
        $this->_payment_model = new $this->_payment_model;

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index(Request $request)
    {

    }

    /**
     * paymentForm
     *
     * @return Response
     */

    public function StripePaymentForm(Request $request)
    {


        $data = $this->_payment_model->PaymentProcess($request);
        $this->_apiData['message'] = trans('api_errors.success');

        // assign to output
        $this->_apiData['data'] = $data;

        return $this->__ApiResponse($request, $this->_apiData);
    }






}
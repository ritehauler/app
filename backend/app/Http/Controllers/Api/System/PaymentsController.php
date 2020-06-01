<?php
namespace App\Http\Controllers\Api\System;
//namespace App\Libraries\StripeLib;
use App\Http\Controllers\Controller;
use App\Libraries\OrderStatus;
use App\Libraries\System\Entity;
use Illuminate\Http\Request;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\EFEntityPlugin;
use App\Http\Models\SYSEntity;
use App\Http\Models\Conf;
use StripeLib;
use Helper;
use App\Libraries\ApiCurl;


//use Twilio;

class PaymentsController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_model_path = "\App\Http\Models\\";
    private $_object_identifier = "Stored_card";
    private $_entity_model = "StoredCard";
    private $_entity_pk = "stored_card_id";
    private $_StripeLib = "";
    private $_percentage_stripe_first = 2.9;
    private $_percentage_stripe_second = 0.30;
    private $_application_fees_in_perc = 5;
    private $_object_identifier_entity = "entities";
    private $_entityAuth = "";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load sys_entity_gallery model

        // load stored card model
        $this->_entity_model = $this->_model_path . $this->_entity_model;
        $this->_entity_model = new $this->_entity_model;
        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";
        // need uncom 
	    $this->_StripeLib = new \App\Libraries\StripeLib;


        // extra models
        $stripeHistory = $this->_model_path . "PaymentHistory";
        $this->_stripeHistory = new $stripeHistory;

        $this->_entityAuth = $this->_model_path . "SYSEntityAuth";
        $this->_entityAuth = new $this->_entityAuth;

    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index(Request $request)
    {

    }
	
	public function calculateCharges(Request $request){
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));
        // validations
        $rules = array(
            'amount' => 'required'
        );
		
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else{
			$serviceFee = getSetting("service_fee");
			$itemRent = $request->amount;
			$nTrustFee = ($itemRent*$serviceFee)/100;
			$borrower  = $itemRent+($nTrustFee/2);
			$stripeFee = ((($borrower*2.9)/100)+0.3);
			$borrowerToPay = ($borrower+($stripeFee/2));
			$amount = $itemRent+$nTrustFee;
			
			$this->_apiData['data'] = array();
			$this->_apiData['data']['charges'] = array(
				array(
					'title'=>'Subtotal',
					'value'=>round($itemRent,2),
					'key'=>'sub_total'
				),
				array(
					'title'=>'nTrust Fee',
					'value'=>round($nTrustFee,2),
					'key'=>'ntrust_fee'
				),
				array(
					'title'=>'Service Fee',
					'value'=> (round(($nTrustFee/2),2) + round(($stripeFee/2),2)),
					'key'=>'service_fee'
				)
			);
			
			
			$this->_apiData['response'] = "success";
            $this->_apiData['message'] = trans('api_errors.success');
		}
		
		return $this->__ApiResponse($request, $this->_apiData);	
	}

    /**
     * Post Payment History
     *
     * @return Response
     */


    public function paymentHistory(Request $request)
    {

        // extra models
        $ex1Model = $this->_model_path . "PaymentHistory";
        $ex1Model = new $ex1Model;

        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));
        // validations
        $rules = array(
            'order_id' => 'required',
            'product_id' => 'required',
            'lender_id' => 'required',
            'borower_id' => 'required',
            'transaction_id' => 'required',
            'payment_method' => 'required',
            'response' => 'required'

        );


        $Transction_id = $this->_stripeHistory
            ->where("transaction_id", "=", $request->transaction_id)
            //->whereNull("deleted_at")
            ->limit(1)
            ->get();

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif (count($Transction_id) > 0) {
            $this->_apiData['message'] = "Transaction ID already exists";
        } else {


            $HistoryService['order_id'] = $request->order_id;
            $HistoryService['product_id'] = $request->product_id;
            $HistoryService['lender_id'] = $request->lender_id;
            $HistoryService['borower_id'] = $request->borower_id;
            $HistoryService['transaction_id'] = $request->transaction_id;
            $HistoryService['payment_method'] = $request->payment_method;
            $HistoryService['response'] = $request->response;
            $id = $this->_stripeHistory->putStripeHistory($HistoryService);
            if ($id) {
                $this->_apiData['data'] = $this->_stripeHistory->get($id);
                $this->_apiData['success'] = "Payment history successfully added";
            }

        }
        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Post Add Card
     *
     * @return Response
     */

    public function AddCard(Request $request)
    {


        // extra models
        $ex1Model = $this->_model_path . "PaymentHistory";
        $ex1Model = new $ex1Model;

        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));
        // validations
        $rules = array(
            'entity_auth_id' => 'required',
            'card_token' => 'required'
        );
        $this->_entityAuth = $this->_model_path . "SYSEntityAuth";
        $this->_entityAuth = new $this->_entityAuth;
        $authID = $this->_entityAuth
            ->where("entity_auth_id", "=", $request->entity_auth_id)
            ->whereNull("deleted_at")
            ->limit(1)
            ->get();

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();

        } elseif (count($authID) <= 0) {
            $this->_apiData['message'] = "User Not Exists";
        } else {


            //$HistoryService['response'] = $request->response;


            $email = $authID[0]['email'];
            if (isset($authID[0]['customer_id']) && $authID[0]['customer_id'] != "") {

                $customer_id = $authID[0]['customer_id'];

            } else {

                // add customer
                $CustomerPara['entity_auth_id'] = $request->entity_auth_id;
                $CustomerPara['email'] = $email;

                $StripeService['stripe_history'] = $this->_StripeLib->addCustomer($CustomerPara);

                $customer_id = $StripeService['stripe_history']['response']['id'];
                $this->_entityAuth
                    ->where('entity_auth_id', $CustomerPara['entity_auth_id'])// find your user by their email
                    ->limit(1)// optional - to ensure only one record is updated.
                    ->update(array('customer_id' => $customer_id));

                $HistoryService['response'] = $StripeService['stripe_history']['response'];
                //$this->_stripeHistory->putStripeHistory($HistoryService);
            }

            $StripeService['stripe_history'] = $this->_StripeLib->addCard($customer_id, $request->borower_id, $request->card_token);
            if (isset($StripeService['stripe_history']['response']->id)) {

                $CardData['stripe_card_id'] = $StripeService['stripe_history']['response']->id;
                $CardData['entity_auth_id'] = $request->entity_auth_id;
                $CardData['card_number'] = $StripeService['stripe_history']['response']->last4;
                $CardData['card_type'] = $StripeService['stripe_history']['response']->funding;
                $CardData["created_at"] = date("Y-m-d H:i:s");
               // $exModel = $this->_model_path . "StoredCard";
               // $exModel = new $exModel;
               // $exModel->put($CardData);
                $this->_apiData['data']['card'] = $StripeService['stripe_history']['response'];
                $this->_apiData['success'] = "Payment history successfully added";
                $this->_apiData['response'] = "success";

            } else {
                $this->_apiData['error'] = 1;
                $HistoryService['response'] = $StripeService['stripe_history']['response'];
                // $this->_stripeHistory->putStripeHistory($HistoryService);
                $this->_apiData['data']['card'] = $StripeService['stripe_history'];
                $this->_apiData['message'] = $StripeService['stripe_history']['response'];
            }


        }
        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Post List Card
     *
     * @return Response
     */


    public function ListCards(Request $request)
    {


        // extra models
        $ex1Model = $this->_model_path . "PaymentHistory";
        $ex1Model = new $ex1Model;

        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));
        // validations
        $rules = array(
            'entity_auth_id' => 'required',
        );

        $exModel = $this->_model_path . "StoredCard";
        $exModel = new $exModel;

        $this->_entityAuth = $this->_model_path . "SYSEntityAuth";
        $this->_entityAuth = new $this->_entityAuth;
        $authID = $this->_entityAuth
            ->where("entity_auth_id", "=", $request->entity_auth_id)
            ->whereNull("deleted_at")
            ->limit(1)
            ->get();

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();

        } elseif (count($authID) <= 0) {
            $this->_apiData['message'] = "User Not Exists";
        } else {
            if (isset($authID[0]['customer_id']) && $authID[0]['customer_id'] != "") {
                $customer_id = $authID[0]['customer_id'];
            } else {
                // add customer
                $CustomerPara['entity_auth_id'] = $request->entity_auth_id;
                $CustomerPara['email'] = $authID[0]['email'];

                $StripeService['stripe_history'] = $this->_StripeLib->addCustomer($CustomerPara);

                $customer_id = $StripeService['stripe_history']['response']['id'];
                $this->_entityAuth
                    ->where('entity_auth_id', $CustomerPara['entity_auth_id'])// find your user by their email
                    ->limit(1)// optional - to ensure only one record is updated.
                    ->update(array('customer_id' => $customer_id));
                $customr = $this->_entityAuth->select('customer_id')->where('entity_auth_id', $CustomerPara['entity_auth_id'])->get();
                $customer_id = $customr[0]['customer_id'];

            }

            $StripeService['stripe_history'] = $this->_StripeLib->retrieveCards($customer_id, $authID[0]['entity_id']);
            if (isset($StripeService['stripe_history']['response']['data'])) {
                $this->_apiData['data']['cards'] = $StripeService['stripe_history']['response']['data'];
                $this->_apiData['response'] = "success";
                $this->_apiData['message'] = trans('api_errors.success');
            } else {
                $this->_apiData['data'] = $StripeService['stripe_history']['response'];
            }

        }
        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Post Delete Particular Card
     *
     * @return Response
     */


    public function deleteCard(Request $request)
    {


        // extra models
        $ex1Model = $this->_model_path . "PaymentHistory";
        $ex1Model = new $ex1Model;

        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));
        // validations
        $rules = array(
            'card_id' => 'required',
            'entity_auth_id' => 'required'
        );
        $this->_entityAuth = $this->_model_path . "SYSEntityAuth";
        $this->_entityAuth = new $this->_entityAuth;

        /*$exModel = $this->_model_path . "StoredCard";
        $exModel = new $exModel;

        $cards = $exModel
            ->where("stripe_card_id", "=", $request->card_id)
            ->whereNull("deleted_at")
            ->limit(1)
            ->get();*/

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();

        } /*elseif (count($cards) <= 0) {
            $this->_apiData['message'] = "Invalid Card Id";
        } */else {
            $authID = $this->_entityAuth
                ->where("entity_auth_id", "=", $request->entity_auth_id)
                ->whereNull("deleted_at")
                ->limit(1)
                ->get();

            $StripeService['stripe_history'] = $this->_StripeLib->deleteCard($authID[0]->customer_id, $request->card_id);
            if (isset($StripeService['stripe_history']['response']['id'])) {

                $cardDelete["deleted_at"] = date("Y-m-d H:i:s");
               // $entity_id = $this->_entity_model->set($cards[0]['stored_card_id'], $cardDelete);
                $this->_apiData['data']['card'] = $StripeService['stripe_history']['response'];
                $this->_apiData['message'] = "Card deleted successfully";
                $this->_apiData['response'] = "success";
            } else {
                $this->_apiData['data'] = $StripeService['stripe_history']['response'];
            }
        }
        return $this->__ApiResponse($request, $this->_apiData);
    }
    /**
     * Add Debit Card
     *
     * @return Response
     */

    public function AddBankAccountNumber(Request $request)
    {

        // extra models
        $ex1Model = $this->_model_path . "PaymentHistory";
        $ex1Model = new $ex1Model;

        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));
        // validations
        $rules = array(
            'entity_auth_id' => 'required',
         //   'account_number' => 'required'
            // 'token' => 'required'
        );
        $this->_entityAuth = $this->_model_path . "SYSEntityAuth";
        $this->_entityAuth = new $this->_entityAuth;

        $exModel = $this->_model_path . "StoredCard";
        $exModel = new $exModel;

        $user = $this->_entityAuth
            ->where("entity_auth_id", "=", $request->entity_auth_id)
            ->whereNull("deleted_at")
            ->limit(1)
            ->get();

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();

        } elseif (count($user) <= 0) {
            $this->_apiData['message'] = "Invalid user Id";
        } else {
            $this->_entityAuth
                ->where('entity_id', $request->entity_auth_id)// find your user by their email
                ->limit(1)// optional - to ensure only one record is updated.
                ->update(array('account_number' => $request->account_number));

            if ($user[0]['email'] == "") {
                $email = $user[0]['social_email'];
            } else {
                $email = $user[0]['email'];
            }

            if (isset($user[0]['account_id']) && $user[0]['account_id'] != "") {
                $account_id = $user[0]['account_id'];
            } else {

                $StripeService = $this->_StripeLib->addAccount($request->entity_auth_id, $email);

                if (isset($StripeService['response']['id'])) {
                    $account_id = $StripeService['response']['id'];
                    $acountData["account_id"] = $account_id;
                    $this->_entityAuth->set($user[0]['entity_auth_id'], $acountData);
                    $customr = $this->_entityAuth->select('account_id')->where('entity_auth_id', $request->entity_auth_id)->get();
                    $account_id = $customr[0]['account_id'];

                }

            }

            $StripeService = $this->_StripeLib->updateAccount($account_id, $request->all());
            // print_r($StripeService);
            if (isset($StripeService['response']['id'])) {
                //$StripeService = $this->_StripeLib->addBankAccount($account_id, $request->token);
                //print_r($StripeService);
                //die();

                $this->_apiData['response'] = "success";
				
				if(isset($request->items_avaliable) && isset($request->entity_auth_id)){
					$SYSEntity = new SYSEntity();
					$SYSEntity->setItemsAvaliable($request->entity_auth_id);
				}
            }
            if (isset($StripeService['stripe_history']['description'])) {
                $messageData = json_decode($StripeService['stripe_history']['description']);
                $this->_apiData['message'] = $messageData->message;
            }

            //$this->_apiData['data']['card'] = array();
            $this->_apiData['status'] = $StripeService['status'];
            //$this->_apiData['message'] = $StripeService['stripe_history']['description'];

        }
        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Post Charge Card
     *
     * @return Response
     */

    public function stripCharge(Request $request)
    {


        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));
        // validations
        $rules = array(
            'card_token' => 'required',
            'order_id' => 'required',
        );

        $params = array(
            'entity_type_id' => 15,
            'entity_id' => $request->order_id,
            'mobile_json' => 1,
            'detail_key' => 'customer_id'
        );

        $entity_lib = new Entity();
        $order_data =  $entity_lib->apiGet($params);
        $order_data = json_decode(json_encode($order_data));
        //echo "<pre>"; print_r($order_data); exit;


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        }
        elseif ($order_data->error == 1){
            $this->_apiData['message'] = $order_data->message;
        }
        elseif (!isset($order_data->data->order->customer_id->detail->auth->entity_auth_id)){
            $this->_apiData['message'] = 'Customer not exist';
        }
        else {

            $order = $order_data->data->order;
            $customer = $order->customer_id->detail->auth;
            // if user customer id not exists
              if (isset($customer->customer_id) && $customer->customer_id != "") {

            $transferInfo['customer_id'] = $customer->customer_id;

            $transferInfo['description'] = " ";
            $transferInfo['total_amount'] = $order->grand_total;


            $transferInfo['card_id'] = $request->card_token;

            if (isset($transferInfo['card_id'])) {

                $stripeActual = ((($request->total_amount * 2.9) / 100) + 0.3);
                $remaining = ($request->total_amount + $stripeActual);
                $lender_amount = $remaining - $request->ntrust_fee;

                $transferInfo['metadata'] = array(
                  'order_number' =>  $order->order_number,
                    'customer_id' =>  $order->customer_id->id,
                    'customer_name' => $order->customer_id->detail->full_name,
                );

                // add Card
                $StripeService = $this->_StripeLib->chargeCard($transferInfo);
                if (isset($StripeService['stripe_history']['transaction_id'])) {

                    $this->_apiData['response'] = "success";
                    $transferInfo['transaction_id'] = $StripeService['stripe_history']['transaction_id'];
                    $this->_apiData['data']['stripe_history'] = $StripeService['stripe_history'];
                    $this->_apiData['data']['transaction_detail'] = $transferInfo;
                    $transferInfo['payment_method'] = "stripe";
                    $transferInfo['payment_status'] = 1;
                    $transferInfo['card_token'] = $request->card_token;
                    $transferInfo['entity_auth_id'] = $customer->entity_auth_id;
                    $transferInfo['order_id'] = $request->order_id;
                    $transferInfo['description'] = $StripeService['stripe_history']['description'];
                    $transferInfo['transaction_response'] = $StripeService['stripe_history']['transaction_response'];
                    unset($transferInfo['card_id']); unset( $transferInfo['metadata']);
                    $StripeService['stripe_history'] = $transferInfo;

                    $transaction_history_id = $this->_stripeHistory->putStripeHistory($StripeService['stripe_history']);
                   $transaction_response = json_decode($StripeService['stripe_history']['transaction_response']);

                   //if order status is reached then update the order as completed
                    $arr = [];
                   $order_model = new OrderStatus();
                   $order_statuses =  $order_model->getIdByKeywords();

                   if($order->order_status->id == $order_statuses['reached']){
                       $arr['entity_type_id'] = 68;
                       $arr['order_status'] =  $order_statuses['completed'];
                       $arr['order_id'] = $order->entity_id;
                       $arr['driver_id'] = $order->driver_id->id;
                       $arr['vehicle_id'] = $order->vehicle_id->id;
                       $arr['transaction_id'] = $StripeService['stripe_history']['transaction_id'];
                       $arr['card_id'] = $request->card_token;
                       $arr['card_type'] = $transaction_response->source->brand;
                       $arr['card_last_digit'] = $transaction_response->source->last4;
                       $arr_response = $entity_lib->apiPost($arr);
                   }else{
                       $arr['entity_type_id'] = 15;
                       $arr['entity_id'] = $order->entity_id;
                       $arr['transaction_id'] = $StripeService['stripe_history']['transaction_id'];
                       $arr['card_id'] = $request->card_token;
                       $arr['card_type'] = $transaction_response->source->brand;
                       $arr['card_last_digit'] = $transaction_response->source->last4;
                       $arr_response = $entity_lib->apiUpdate($arr);
                   }
                    //  echo "<pre>"; print_r($arr); exit;

                } else {
                    $message = json_decode($StripeService['stripe_history']['description']);
                    $this->_apiData['data']['stripe_history'] = $StripeService['stripe_history'];
                    $this->_apiData['message'] = $message->message;
                    $this->_apiData['data']['stripe_history'] = $StripeService['stripe_history'];
                }


            }
        }
         else{
             $this->_apiData['error'] = 1;
             $this->_apiData['message'] = 'Sorry! Customer not created on stripe';
            }
        }
        return $this->__ApiResponse($request, $this->_apiData);
    }
    /**
     * Add Debit Card
     *
     * @return Response
     */

    public function bankInfor(Request $request)
    {


        $this->_entityAuth = $this->_model_path . "SYSEntityAuth";
        $this->_entityAuth = new $this->_entityAuth;

        $user = $this->_entityAuth
            ->where("entity_id", "=", $request->entity_auth_id)
            ->whereNull("deleted_at")
            ->limit(1)
            ->get();

        $rules = array(
            'entity_auth_id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif (count($user) <= 0 && $user == "") {
            $this->_apiData['message'] = "Invalid User ID";
        } else {
            if (isset($user[0]['account_id']) && $user[0]['account_id'] != "") {
                if (isset($user[0]['account_id']))
                    $StripeService = $this->_StripeLib->getBankAccount($user[0]['account_id']);

                if (count($StripeService['response']->external_accounts['data']) > 0) {
//                    print_r($StripeService['response']);
//                    die();
                    $data['first_name'] = $StripeService['response']->legal_entity['first_name'];
                    $data['last_name'] = $StripeService['response']->legal_entity['last_name'];
                    $data['routing_number'] = $StripeService['response']->external_accounts['data'][0]->routing_number;
                    $data['dob'] = $StripeService['response']->legal_entity['dob']['day'] . "-" . $StripeService['response']->legal_entity['dob']['month'] . "-" . $StripeService['response']->legal_entity['dob']['year'];
                    $data['city'] = $StripeService['response']->legal_entity['address']['city'];
                    $data['state'] = $StripeService['response']->legal_entity['address']['state'];
                    $data['address'] = $StripeService['response']->legal_entity['address']['line1'];
                    $data['postal_code'] = $StripeService['response']->legal_entity['address']['postal_code'];
                    $this->_apiData['response'] = "success";
                    $data['bank_account_number'] = $user[0]['account_number'];
                    $this->_apiData['data']['bank_account'] = $data;

                } else {
                    $this->_apiData['message'] = "No any bank account found";
                }
            }


        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Add Debit Card
     *
     * @return Response
     */
    public function addPaymentMethod(Request $request)
    {
        $rules = array(
            'entity_id' => 'required',
            'token' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        $AccountID = $this->_entityAuth->getUserAccountID($request->entity_id);
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif (!isset($AccountID->account_id) && $AccountID->account_id == "") {
            $this->_apiData['message'] = "Account id not Exists";
        } else {
            $AccountID = $AccountID->account_id;
            $StripeService = $this->_StripeLib->AddPayoutMethod($AccountID, $request->token);
            if (isset($StripeService['response']->external_accounts) && count($StripeService['response']->external_accounts) > 0) {
                $this->_apiData['response'] = "success";
                $this->_apiData ['data'] = $StripeService['response']->external_accounts;
				if(isset($request->items_avaliable) && isset($request->entity_id)){
					$SYSEntity = new SYSEntity();
					$SYSEntity->setItemsAvaliable($request->entity_id);
				}
				//$StripeService = $this->_StripeLib->updateAccount($AccountID, $request->all());
            } else {
                $this->_apiData ['data'] = "";
                $this->_apiData['message'] = $StripeService['response'];
            }
        }
        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Add Amount To debit Card
     *
     * @return Response
     */

    public function addPaymentToDebit(Request $request){
        $rules = array(
            'entity_id' => 'required',
            'amount' => 'required',
            'card_token'=> 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        $AccountID = $this->_entityAuth->getUserAccountID($request->entity_id);
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif (!isset($AccountID->account_id) && $AccountID->account_id == "") {
            $this->_apiData['message'] = "Account id not Exists";
        } else {
            $AccountID = $AccountID->account_id;
            $StripeService = $this->_StripeLib->transverToDebit($AccountID, $request->amount , $request->card_token);

            $this->_apiData = $StripeService;
        }
        return $this->__ApiResponse($request, $this->_apiData);
    }

    public function getPaymentMethods(Request $request){

        $rules = array(
            'entity_id' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        $AccountID = $this->_entityAuth->getUserAccountID($request->entity_id);

        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
			$userData = $this->_entityAuth->getDataByEntityID($request->entity_id);
			if($AccountID){
				if(is_null($AccountID->account_id)){
					if(isset($userData->email) && !empty($userData)){
						$StripeService = $this->_StripeLib->addAccount($request->entity_id, $userData->email);
						if (isset($StripeService['response']['id'])) {
							$AccountID = $StripeService['response']['id'];
							$acountData["account_id"] = $AccountID;
							$this->_entityAuth->set($userData->entity_auth_id, $acountData);
							$AccountID = $this->_entityAuth->getUserAccountID($request->entity_id);
						}
					}
				}
				$StripeService = $this->_StripeLib->getAllPaymentMethods($AccountID->account_id);
				if(isset($StripeService['stripe_history']['accounts']['data'])) {
					
					$accounts = $StripeService['stripe_history']['accounts']['data'];
					foreach($accounts as $account){
						$account->is_default=0;
						if(isset($userData->attributes['default_payout_id'])){
							if($userData->attributes['default_payout_id']==$account->id) $account->is_default=1;
						}
						$this->_apiData['data']['accounts'][] = $account;
					}
					$this->_apiData['response'] = "success";
				}else{
					//$this->_apiData['data'] = [];
				}
			}else{
				$this->_apiData['message'] = "User not exist!";
			}

        }
        return $this->__ApiResponse($request, $this->_apiData);
    }



    public function deletePaymentMethod(Request $request){

        $rules = array(
            'entity_id' => 'required',
            'method_id' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        $AccountID = $this->_entityAuth->getUserAccountID($request->entity_id);

        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            $userData = $this->_entityAuth->getDataByEntityID($request->entity_id);
            if($AccountID){
                if(is_null($AccountID->account_id)){
                    if(isset($userData->email) && !empty($userData)){
                        $StripeService = $this->_StripeLib->addAccount($request->entity_id, $userData->email);
                        if (isset($StripeService['response']['id'])) {
                            $AccountID = $StripeService['response']['id'];
                            $acountData["account_id"] = $AccountID;
                            $this->_entityAuth->set($userData->entity_auth_id, $acountData);
                            $AccountID = $this->_entityAuth->getUserAccountID($request->entity_id);
                        }
                    }
                }
                $this->_StripeLib->deleteExternalAccount($AccountID->account_id, $request->method_id);
                $StripeService = $this->_StripeLib->getAllPaymentMethods($AccountID->account_id);
                if(isset($StripeService['stripe_history']['accounts']['data'])) {

                    $accounts = $StripeService['stripe_history']['accounts']['data'];
                    foreach($accounts as $account){
                        $account->is_default=0;
                        if(isset($userData->attributes['default_payout_id'])){
                            if($userData->attributes['default_payout_id']==$account->id) $account->is_default=1;
                        }
                        $this->_apiData['data']['accounts'][] = $account;
                    }
                    $this->_apiData['response'] = "success";
                }else{
                    //$this->_apiData['data'] = [];
                }
            }else{
                $this->_apiData['message'] = "User not exist!";
            }

        }
        return $this->__ApiResponse($request, $this->_apiData);
    }


    public function updatePaymentMethod(Request $request){

        $rules = array(
            'entity_id' => 'required',
            'method_id' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        $AccountID = $this->_entityAuth->getUserAccountID($request->entity_id);

        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            $userData = $this->_entityAuth->getDataByEntityID($request->entity_id);
            if($AccountID){
                if(is_null($AccountID->account_id)){
                    if(isset($userData->email) && !empty($userData)){
                        $StripeService = $this->_StripeLib->addAccount($request->entity_id, $userData->email);
                        if (isset($StripeService['response']['id'])) {
                            $AccountID = $StripeService['response']['id'];
                            $acountData["account_id"] = $AccountID;
                            $this->_entityAuth->set($userData->entity_auth_id, $acountData);
                            $AccountID = $this->_entityAuth->getUserAccountID($request->entity_id);
                        }
                    }
                }
                $this->_StripeLib->updateExternalAccount($AccountID->account_id, $request->method_id);
                $this->_apiData['response'] = "success";
             
            }else{
                $this->_apiData['message'] = "User not exist!";
            }

        }
        return $this->__ApiResponse($request, $this->_apiData);
    }


}
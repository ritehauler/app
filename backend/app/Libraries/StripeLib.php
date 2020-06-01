<?php
namespace App\Libraries;

use Stripe\Error\Card;
use Stripe\Error\RateLimit;
use Stripe\Error\InvalidRequest;
use Stripe\Error\Authentication;
use Stripe\Error\ApiConnection;
use Stripe\Error\Base;
use Stripe\Stripe;
use Helper;

/**
 * Custom Library For Performing CRUD operations for Stripe API
 *
 * Urls :
 *        https://github.com/stripe/stripe-php
 *        https://stripe.com/docs/ap
 *
 * @category   Libraries
 * @author     Sohaib Rehman
 */
class StripeLib
{

    /**
     * Stripe constructor for loading credentials for api calls.
     */
    public function __construct()
    {
        CustomHelper::getSettings();  // fetching settings from setting table and overiding some indexes in conf/services.php file

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));   // initializing stripe object
    }


    /**
     *
     * Create Customer
     *
     * @param array customerinfo
     *
     * @return array customer
     */

    public function xaddCustomer($customerInfo = array())
    {
        // set records for stripe history
        $stripeHistoryInfo['entity_auth_id'] = $customerInfo['entity_auth_id'];
        $stripeHistoryInfo['user_type'] = $customerInfo['user_type'];
        $stripeHistoryInfo['record_type'] = config('constants.TYPE_ADD_CUSTOMER');

        try {
            $customer = \Stripe\Customer::create([
                'email' => $customerInfo['email'],
            ]);

            $response = ['status' => config('constants.SUCCESS'), 'response' => $customer];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Customer added successfully.']);

        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {

            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;
    }

    public function addCustomer($customerInfo = array())
    {


        // set records for stripe history
        $stripeHistoryInfo['entity_auth_id'] = $customerInfo['entity_auth_id'];
        //$stripeHistoryInfo['user_type'] = $customerInfo['user_type'];
        $stripeHistoryInfo['record_type'] = config('constants.TYPE_ADD_CUSTOMER');

        try {
            $customer = \Stripe\Customer::create([
                'email' => $customerInfo['email'],
            ]);


            $response = ['status' => config('constants.SUCCESS'), 'response' => $customer];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Customer added successfully.']);


        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {

            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }


        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;
    }

    /**
     * Store the credit card info into the stripe for using later.
     *
     * @param int customerId , userId
     * @param array creditCardInfo
     *
     * @return array card
     */

    public function addCard($customerId, $userId, $cardToken)
    {

        // set records for stripe history
        $stripeHistoryInfo['borower_id'] = $userId;
        //$stripeHistoryInfo['user_type'] = config('constants.USER_TYPES.TIPPER');
        $stripeHistoryInfo['record_type'] = config('constants.TYPE_ADD_CARD');

        try {
            $customer = \Stripe\Customer::retrieve($customerId);
            $creditCard = $customer->sources->create(array("source" => $cardToken));

            $response = ['status' => config('constants.SUCCESS'), 'response' => $creditCard];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Card added successfully.']);

        } catch (\Stripe\Error\Card $ex) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {

            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;
    }


    /**
     * Delete Customer's CreditCard Into The Stripe
     *
     * @param int userId, userType, customerId , cardId
     * @param array cardinfo
     *
     * @return array card
     */


    public function deleteCard($customerId, $cardId)
    {

        // set records for stripe history
        $stripeHistoryInfo['record_type'] = config('constants.TYPE_DELETE_CARD');


        try {
            $isDeleted = \Stripe\Customer::retrieve($customerId)
                ->sources->retrieve($cardId)->delete();

            $response = ['status' => config('constants.SUCCESS'), 'response' => $isDeleted];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Card deleted successfully.']);

        } catch (\Stripe\Error\Card $ex) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;

    }

    /**
     * Retrieve All Stored Cards Of Customer Into The Stripe
     *
     * @param string customerId
     *
     * @return array cards
     */

    public function retrieveCards($customerId, $userId)
    {

        // set records for stripe history
        $stripeHistoryInfo['borower_id'] = $userId;
        $stripeHistoryInfo['record_type'] = config('constants.TYPE_RETRIEVE_CARDS');

        try {
            $cards = \Stripe\Customer::retrieve($customerId)->sources->all(array(
                "object" => "card"
            ));

            $response = ['status' => config('constants.SUCCESS'), 'response' => $cards];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Cards retrieved successfully.']);

        } catch (\Stripe\Error\Card $ex) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {

            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;
    }

    /**
     * Retrieve Stored Card Into The Stripe
     *
     * @param int userId
     * @param string customerId , cardId
     *
     * @return array card
     */

    public function retrieveCard($customerId, $userId, $cardId)
    {

        // set records for stripe history
        $stripeHistoryInfo['entity_auth_id'] = $userId;
        $stripeHistoryInfo['record_type'] = config('constants.TYPE_RETRIEVE_CARD');

        try {
            $card = \Stripe\Customer::retrieve($customerId)->sources->retrieve($cardId);

            $response = ['status' => config('constants.SUCCESS'), 'response' => $card];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Card retrieved successfully.']);

        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;
    }


    /**
     * Create Account Into The Stripe
     *
     * @param int userId
     *
     * @return array account
     */

    public function addAccount($userId, $userEmail)
    {

        // set records for stripe history
        $stripeHistoryInfo['entity_auth_id'] = $userId;
        //$stripeHistoryInfo['user_type'] = $userType;
        $stripeHistoryInfo['record_type'] = config('constants.TYPE_ADD_ACCOUNT');

        try {
            $account = \Stripe\Account::create(array(
                "type" => "custom",   // account is managed by our platform.
                "country" => "US",
                "email" => $userEmail
            ));

            $response = ['status' => config('constants.SUCCESS'), 'response' => $account];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Account added successfully.']);

        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;
    }


    /**
     * Update Account Of User Into The Stripe
     *
     * @param int userId
     * @params string userType, accountId
     *
     * @return array account
     */

    public function updateAccount($account_id, $data)
    {
        $request = (object)$data;

        $stripeHistoryInfo['record_type'] = config('constants.TYPE_RETRIEVE_ACCOUNT');
        $d = strtotime("$request->dob");
        $accountInfo = array(

            //'external_account' => 'bank_account_token',
            'legal_entity' => array(
                'first_name' => 'test',
                'last_name' => 'test',
                'ssn_last_4' => $request->ssn_last_4,
                //'personal_id_number' => $request->personal_id_number,
                'type' => 'individual',

//                    'type' => 'company',
                'dob' => array(
                    'day' => date("d", $d),
                    'month' => date("m", $d),
                    'year' => date("Y", $d)
                ),
                'address' => array(
                    'city' => $request->city,
                    'country' => $request->country,

                    'line1' => $request->line1,
                    'postal_code' => $request->postal_code,
                    'state' => $request->state
                ),

                //'default_for_currency' => false,
            ),

            'tos_acceptance' => array(
                'date' => time(),
                'ip' => $_SERVER['REMOTE_ADDR']
            ),

//            'external_account' => array(
//                "object" => "bank_account",
//                "country" => $request->country,
//                "routing_number" => $request->routing_number,
//                "account_number" => $request->account_number,
//                "account_holder_name" => $request->first_name,
//                "account_holder_type" => "individual",
//                "default_for_currency" => false,
//
//            )
           // "external_account" => $request->bank_token,

        );

        // set records for stripe history
        $stripeHistoryInfo['entity_auth_id'] = $request->entity_auth_id;
        $stripeHistoryInfo['record_type'] = config('constants.TYPE_ACCOUNT_VERIFICATION');

        try {

            $accountDetails = \Stripe\Account::update($account_id, $accountInfo);
            $account = \Stripe\Account::retrieve($account_id);

            $account->external_accounts->create(array(
                "external_account" => $request->bank_token,
               // 'default_for_currency' => true,
            ));

            $response = ['status' => config('constants.SUCCESS'), 'response' => $account];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Account retrieved successfully.']);

        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;


    }


    /**
     * Retrieve Stored Account Of User Into The Stripe
     *
     * @param int userId
     * @params string userType, accountId
     *
     * @return array account
     */

    public function retrieveAccount($userId, $userType, $accountId)
    {
        // set records for stripe history
        $stripeHistoryInfo['entity_auth_id'] = $userId;
        //$stripeHistoryInfo['user_type'] = $userType;
        $stripeHistoryInfo['record_type'] = config('constants.TYPE_RETRIEVE_ACCOUNT');

        try {
            $accountDetails = \Stripe\Account::retrieve($accountId);
            $response = ['status' => config('constants.SUCCESS'), 'response' => $accountDetails];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Account retrieved successfully.']);


        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;
    }


    /**
     * Delete Stored Account Into The Stripe
     *
     * @param string accountId
     *
     * @return array Response
     */

    public function deleteAccount($accountId)
    {

        try {
            $accountDetails = \Stripe\Account::retrieve($accountId);
            $isDeleted = $accountDetails->delete();

            //$isDeleted = $this->_stripe->account()->delete($accountId);

            $response = ['status' => config('constants.SUCCESS'), 'response' => $isDeleted];

        } catch (StripeException $ex) {
            // Network communication with Stripe failed
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

        } catch (\Exception $ex) {
            $response = ['status' => config('constants.ERROR'), 'response' => $ex->getMessage()];
        }

        return $response;
    }

    /**
     * Refunds Transaction Into The Stripe
     *
     * @param int userId
     * @param string chargeId / transactionId
     *
     * @return array Response
     */

    public function refund($chargeId, $userId)
    {
        // set records for stripe history
        $stripeHistoryInfo['entity_auth_id'] = $userId;
        $stripeHistoryInfo['record_type'] = config('constants.TYPE_REFUND_CHARGE');

        try {

            $appFees = \Stripe\ApplicationFee::all(array("limit" => 3));
            $fee = \Stripe\ApplicationFee::retrieve($appFees['data'][0]['id']);
            $refund = $fee->refunds->create();


            $response = ['status' => config('constants.SUCCESS'), 'response' => $refund['id']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Transaction refunded successfully.']);

        } catch (StripeException $ex) {
            // Network communication with Stripe failed
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {
            $response = ['status' => config('constants.ERROR'), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;
    }

    /**
     * Make Transaction For Transafering Funds To Stripe Account
     *
     * @param array transferInfo
     *
     * @return array card
     */

    public function xchargeCard($transferInfo)
    {
        // set records for stripe history
        $stripeHistoryInfo['entity_auth_id'] = $transferInfo['entity_auth_id'];
        $stripeHistoryInfo['user_type'] = $transferInfo['user_type'];
        $stripeHistoryInfo['record_type'] = "tip";

        try {
            $token = \Stripe\Token::create(array(
                "customer" => $transferInfo['customer_id'],
                "card" => $transferInfo['card_id'],
            ), array("stripe_account" => $transferInfo['stripe_account_id']));

            if (!isset($token->id)) {
                $response = ['status' => config('constants.ERROR'), 'response' => 'Unable to send tip'];

                // set records for stripe history
                $stripeHistoryInfo['response'] = config('constants.ERROR');
                $stripeHistoryInfo['description'] = json_encode(["message" => "Unable to create token for charge"]);

                $response['stripe_history'] = $stripeHistoryInfo;

                return $response;
            }

            $transfered = \Stripe\Charge::create([
                'currency' => getSetting('transaction_currency'),
                'amount' => $transferInfo['total_amount'] * 100,  // converting dollars to cents
                'description' => $transferInfo['description'],
                'source' => $token->id,
                "application_fee" => $transferInfo['application_fee'] * 100,
            ], ['stripe_account' => $transferInfo['stripe_account_id']]);   // organization's managed account id

            $response = ['status' => config('constants.SUCCESS'), 'response' => $transfered['id']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Funds transfered successfully to organization.']);

        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {

            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;
    }


    public function chargeCard($transferInfo)
    {

        $stripeHistoryInfo['record_type'] = "tip";

        try {

            //here we are not using source param b/c this is use for one time card token
            //but here we are using stored card id
            $params = [
                'currency' => 'USD',
                'amount' => $transferInfo['total_amount'] * 100,  // converting dollars to cents
                'description' => $transferInfo['description'],
               // 'source' => $transferInfo['card_id'],
                'customer' => $transferInfo['customer_id'],
                "card" => $transferInfo['card_id'],
                'metadata' =>  $transferInfo['metadata']
            ];

           // echo "<pre>"; print_r($params);
            $transfered = \Stripe\Charge::create($params);   // organization's managed account id

          // echo "<pre>"; print_r($transfered->__toArray(true)); exit;
            $response = ['status' => config('constants.SUCCESS'), 'response' => $transfered['id']];

            // set records for stripe history
            $stripeHistoryInfo['transaction_id'] = $transfered['id'];
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Funds transfered successfully to organization.']);
            $stripeHistoryInfo['transaction_response'] = json_encode($transfered->__toArray(true));

        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);
            $stripeHistoryInfo['transaction_response'] =json_encode($transfered->__toArray(true));

        } catch (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
            $stripeHistoryInfo['transaction_response'] =json_encode($transfered->__toArray(true));

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
            $stripeHistoryInfo['transaction_response'] =json_encode($transfered->__toArray(true));

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
            $stripeHistoryInfo['transaction_response'] =json_encode($transfered->__toArray(true));

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
            $stripeHistoryInfo['transaction_response'] =json_encode($transfered->__toArray(true));

        } catch (\Exception $ex) {

            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
            $stripeHistoryInfo['transaction_response'] =json_encode($transfered->__toArray(true));
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;
    }

    /**
     * Verifies The Managed Account For Transfers
     *
     * @param string userId, accountId
     * @param array verificationInfo
     *
     * @return array response
     */

    public function addBankAccount($account_id, $bank_token = false)
    {

        try {
            $account = \Stripe\Account::retrieve($account_id);

            $account->external_accounts->create(array(
                "external_account" => $bank_token
            ));
            $response = ['status' => config('constants.SUCCESS'), 'response' => $account];
            $stripeHistoryInfo['data'] = $response;
            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Bank account info updated successfully.']);


        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;

    }


    public function getBankAccount($account_id)
    {

        try {
            $account = \Stripe\Account::retrieve($account_id);
            $response = ['status' => config('constants.SUCCESS'), 'response' => $account];
            $stripeHistoryInfo['data'] = $response;
            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Bank account info updated successfully.']);


        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;

    }

    /**
     * Add Payout Method
     *
     * @param string userId, accountId
     * @param array CardToken
     *
     * @return array response
     */
    public function AddPayoutMethod($account_id, $cardToken)
    {

        try {
            $account = \Stripe\Account::retrieve($account_id);

            $account->external_accounts->create(array(
                "external_account" => $cardToken
            ));
            $response = ['status' => config('constants.SUCCESS'), 'response' => $account];
            $stripeHistoryInfo['data'] = $response;
            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Bank account info updated successfully.']);


        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch
        (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;

    }



    /**
     * Transfer Amount To Debit Card
     *
     * @param string userId, accountId
     * @param array Amount
     *
     * @return array response
     */
    public function transverToDebit($account_id, $amount , $cardToken)
    {

        try {
            $amount = $amount*100;
            $destination = $cardToken;
            $transfer = \Stripe\Transfer::create(
                array(
                    "amount" =>$amount,
                    "currency" => "usd",
                    "method" => "instant",
                    "destination" =>$destination
                ),
                array("stripe_account" => $account_id)
            );


            $response = ['status' => config('constants.SUCCESS'), 'response' => $transfer];
            $stripeHistoryInfo['data'] = $response;
            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(['message' => 'Debit Card Payment Add Scuccessfully .']);


        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch
        (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;

    }


    public function getAllPaymentMethods($account_id){

        try {
            $account = \Stripe\Account::retrieve($account_id);


            if(isset($account->external_accounts)){
                $stripeHistoryInfo['accounts'] = $account->external_accounts;
            }else{
                $stripeHistoryInfo['accounts'] = $account;
            }
            $response = ['status' => 'success', 'response' => $stripeHistoryInfo['accounts'] ];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = 'Funds transfered successfully to organization';


        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch
        (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;


    }


    public function deleteExternalAccount($account_id , $method_id){

        try {
            $account = \Stripe\Account::retrieve($account_id);
            $account->external_accounts->retrieve($method_id)->delete();
            $stripeHistoryInfo['accounts'] = $account;
            $response = ['status' => 'success', 'response' => $stripeHistoryInfo['accounts'] ];
            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = 'Delet Account Successfully';


        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch
        (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;


    }



    public function updateExternalAccount($account_id , $method_id){

        try {
            $account = \Stripe\Account::retrieve($account_id);
            $account = $account->external_accounts->retrieve($method_id);
            $account->default_for_currency  = true;
            $account->save();
            $stripeHistoryInfo['accounts'] = $account;
            $response = ['status' => 'success', 'response' => $stripeHistoryInfo['accounts'] ];
            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = 'Delet Account Successfully';


        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $response = ['status' => $err['code'], 'response' => $err['message']];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $err['message']]);

        } catch
        (\Stripe\Error\InvalidRequest $ex) {
            // Invalid parameters were supplied to Stripe's API
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Authentication $ex) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\ApiConnection $ex) {
            // Network communication with Stripe failed
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Stripe\Error\Base $ex) {
            // Display a very generic error to the user, and maybe send
            $body = $ex->getJsonBody();
            $err = $body['error'];

            $response = ['status' => !empty($ex->getCode()) ? $ex->getCode() : $err['type'], 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);

        } catch (\Exception $ex) {
            $response = ['status' => $ex->getCode(), 'response' => $ex->getMessage()];

            // set records for stripe history
            $stripeHistoryInfo['response'] = $response['status'];
            $stripeHistoryInfo['description'] = json_encode(["message" => $ex->getMessage()]);
        }

        $response['stripe_history'] = $stripeHistoryInfo;

        return $response;


    }

}
<?php

/**
 * @description Billing module MAU implementation
 * @description on all monthly active user
 * @description billing will calculate on daily active users
 * @date: 10-Jan-2018
 * @author: Hammad Haider
 */

namespace App\Libraries\DeliveryPlatformQueue;

use App\Http\Models\SYSTableFlat;
use App\Http\Models\User;
use App\Http\Models\WFSTaskTemplate;
use App\Libraries\CustomHelper;
use App\Libraries\DeliveryPlatformQueue\queueInterface;

//require 'vendor/autoload.php';
use App\Libraries\System\Entity;
use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;


class queueAmazon implements queueInterface
{

    private
        $_externalConfig = [],
        $_queue = [],
        $_receiptHandles = [],
        $_client;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_externalConfig = config('constants.EXTERNAL_CALL_DETAIL.DELIVERY_PLATFORM_QUEUE.AMAZON');
        //$flat_model = new SYSTableFlat('order_statuses');
        //$data = $flat_model->getDataByWhere();
        //print_r($data);
        //exit;
        // get all statuses
    }

    /**
     * @description Compile customer from database for billing
     * @param  void
     * @return void
     */
    public function getOrderQueue(){

        print "Queue Amazon: getting orders from queue\n";

        $queueUrl = $this->_externalConfig['receive_url'];
        $this->_client = new SqsClient([
            'profile' => 'default',
            'region' => 'us-west-2',
            'version' => '2012-11-05'
        ]);

        try {
            $result = $this->_client->receiveMessage(array(
                'AttributeNames' => ['SentTimestamp'],
                'MaxNumberOfMessages' => 1,
                'MessageAttributeNames' => ['All'],
                'QueueUrl' => $queueUrl, // REQUIRED
                'WaitTimeSeconds' => 0,
            ));
            if (count($result->get('Messages')) > 0) {
                $this->_queue = $result->get('Messages');
                /**/
            } else {
                echo "No messages in queue. \n";
            }
        } catch (AwsException $e) {
            // output error message if fails
            error_log($e->getMessage());
        }
    }

    /**
     * @description Compile customer from database for billing
     * @param  void
     * @return void
     */
    public function operationQueue(){

        print "Queue Amazon: initializing operation ..\n";
        foreach($this->_queue as $queue){

            $this->_receiptHandles[] = $queue['ReceiptHandle'];
            $data_body  = base64_decode($queue['Body']);
            $order_detail  = json_decode($data_body, true);

            $orders = isset($order_detail['trips'])? $order_detail['trips'] : [];

            foreach($orders as $order) {
                $order_id = $order['order']['order_id'];
                $order_status = $order['order']['status'];
                print "Queue Amazon: order[$order_id] processing..\n";

                $this->_updateOrder($order_id, $order_status, $order);
            }
        }
        print "Queue Amazon: operation completed..\n";

    }

    /**
     * @description update order with driver information
     * @param  void
     * @return void
     */
    private function _updateOrder($order_id, $order_status, $order_detail){
        $data = WFSTaskTemplate::getTiByEntityid($order_id);

        if(count($data) > 0) {
            // to update order on picked it must be at Dispatch
            print "Queue Amazon: updating order[$order_id] status ..\n";

            if (strtolower($data[0]->title) == 'dispatch' && strtolower($order_status) == 'picked') {
                // if it is in dispatch then curl to accept then next task will be initiated

                $params = [];
                $params['ti_id'] = $data[0]->id;
                $params['user_id'] = 1;        // admin id
                $params['role_id'] = 10;      // delivery depart role id
                $params['department_id'] = 6; // delivery department id
                $params['state_id'] = 2; // accepted
                $params['is_admin'] = 1; // admin status

                $url = HTTP_URL .'api/wfs/user/update';
                $response = CustomHelper::guzzleHttpRequest($url, $params);

                // call function to update order for driver_response
                $this->_orderApiUpdate($data, $order_id, $order_detail);
            }

            if (strtolower($data[0]->title) == 'delivered' && strtolower($order_status) == 'started') {

                $assign_to = str_replace('*', 1, $data[0]->assign_to);
                WFSTaskTemplate::assignTI($data[0]->id, $assign_to);

                // call function to update order for driver_response
                $this->_orderApiUpdate($data, $order_id, $order_detail);
            }

            // to update order on delivered it must be at Delivered and status is pending
            if (strtolower($data[0]->title) == 'delivered' && strtolower($order_status) == 'delivered') {

                $assign_to = str_replace('*', 1, $data[0]->assign_to);
                WFSTaskTemplate::assignTI($data[0]->id, $assign_to);

                // if it is in Delivered then curl to accept and close the order cycle
                $params = [];
                $params['ti_id'] = $data[0]->id;
                $params['user_id'] = 1;        // admin id
                $params['role_id'] = 11;      // delivery depart role id
                $params['department_id'] = 7; // delivery department id
                $params['state_id'] = 2; // accepted
                $params['is_admin'] = 1; // admin status

                $url = HTTP_URL .'api/wfs/user/update';
                $response = CustomHelper::guzzleHttpRequest($url, $params);

                // call function to update order for driver_response
                $this->_orderApiUpdate($data, $order_id, $order_detail);
            }

            if (strtolower($data[0]->title) == 'delivered' && strtolower($order_status) == 'ended') {

                $transaction_type = $order_detail['payments']['transaction_type'];
                $balance = $order_detail['payments']['balance'];
                $customer_id = $order_detail['order']['customer_id'];

                $params = [];

                $entity_type_model = new SYSEntityType();
                $wallet_transaction_entity_type_id = $entity_type_model->getIdByIdentifier('wallet_transaction');
                $params['entity_type_id'] = $wallet_transaction_entity_type_id;

                $params['customer_id'] = $customer_id;
                $params['order_id'] = $order_id;
                $params['credit'] = '';
                $params['debit'] = '';

                if(!empty($transaction_type) && $balance > 0){
                    if($transaction_type == 'credit'){
                        // add $balance amount in current user balance
                        $params['transaction_type'] = 'credit';
                        $params['credit'] = $balance;
                        $this->_orderBalanceUpdate($params);
                    }elseif($transaction_type == 'debit'){
                        // subtract $balance amount in current user ammount
                        $params['transaction_type'] = 'debit';
                        $params['debit'] = $balance;
                        $this->_orderBalanceUpdate($params);
                    }
                }
            }

        }

    }

    /**
     * @description Compile customer from database for billing
     * @param  void
     * @return void
     */
    public function removeQueueItem(){

        print "Queue Amazon: removing queue items\n";
        $queueUrl = $this->_externalConfig['receive_url'];

        foreach($this->_receiptHandles as $handle) {
            try {
                $result = $this->_client->deleteMessage([
                    'QueueUrl' => $queueUrl, // REQUIRED
                    'ReceiptHandle' => $handle // REQUIRED
                ]);
            } catch (AwsException $e) {
                // output error message if fails
                error_log($e->getMessage());
            }
        }

    }

    private function _parseResponse ($response) {
        //return new \SimpleXMLElement($response->getContents());
        //return new \SimpleXMLElement($response);
        //return new \GreenCape\Xml\Converter($response);
        return json_decode(json_encode(simplexml_load_string($response)), true);
    }

    private function _orderApiUpdate($data, $order_id, $order_detail) {
        // call function to update order for driver_response
        $entity_lib = new Entity();
        $params = [];
        $params['entity_type_id'] = $data[0]->assign_entity_type_id;
        $params['entity_id'] = $order_id;
        $params['driver_response'] = json_encode($order_detail);
        $params['is_profile_update'] = 1;

        $return = $entity_lib->apiUpdate($params);
    }

    private function _orderBalanceUpdate($params) {
        // call function to update order for driver_response
        $entity_lib = new Entity();
        $return = $entity_lib->apiPost($params);
    }

}
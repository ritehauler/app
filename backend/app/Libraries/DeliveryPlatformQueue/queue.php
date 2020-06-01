<?php

/**
 * @description Billing module factory implementation
 * @description Call its initiated provider and call all its methods
 * @date: 10-Jan-2018
 * @author: Hammad Haider
 */

namespace App\Libraries\DeliveryPlatformQueue;

use App\Libraries\DeliveryPlatformQueue\queueAmazon;


class queue
{
    private $_providers = array('amazon' => 'queueAmazon');
    protected $__response = array();
    private $_provider_obj, $service, $_mode;

    /**
     * @description set default or recieve provider
     * @param  string (provider name)
     * @return void
     */
    public function __construct($service = 'amazon')
    {
        $this->_mode = '';
        $service = ucfirst($service);
        $fn = "init{$service}Queue";

        if (method_exists($this, $fn))
            return $this->$fn($service);

        throw new \Exception('Queue services not found.');

    }

    /**
     * @description initiate MAU provider object
     * @param  string (provider name)
     * @return void
     */
    public function initAmazonQueue($service = 'amazon')
    {

        print "Queue Amazon: initiated\n";

        $provider_obj = 'queueAmazon';
        $this->_provider_obj = new queueAmazon();

    }

    /**
     * @description magic function call undefine functions
     * through provider set object, it calls all provider function
     * @param  string (calling function names)
     * @param array (passed arguments to function)
     * @return array (calling function response)
     */
    public function __call($name, $args)
    {

        if (method_exists($this->_provider_obj, $name)) {

            return call_user_func_array(array($this->_provider_obj, $name), $args);

        } else {

            $this->__response['code'] = '404';
            $this->__response['message'] = $name . ' is not defined';
            $this->__response['data']['methods'] = get_class_methods($this->_provider_obj);

            return $this->__response;
        }
    }

}
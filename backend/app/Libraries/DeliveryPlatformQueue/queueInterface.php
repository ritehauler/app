<?php

/**
 * @description Billing module interface will implement according to business
 * @date: 10-Jan-2018
 * @author: Hammad Haider
 */
namespace App\Libraries\DeliveryPlatformQueue;


interface queueInterface
{

    //@description  Compile customer from database for billing
    public function getOrderQueue();

    //@description  implement billing business on every compile customer
    public function operationQueue();

    //@description  dispatch billing to customer after preparing
    public function removeQueueItem();

}
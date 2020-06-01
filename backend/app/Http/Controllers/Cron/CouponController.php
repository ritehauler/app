<?php

/**
 * This is written to execute to read coupons
 * and will update expired coupons
 * Class CouponController
 * Date: 10-01-2018
 * Author: Cubix
 * Copyright: cubix
 */

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Libraries\CouponLib;
use Illuminate\Http\Request;
use App\Libraries\CustomHelper;
use Validator;
use Illuminate\Http\Exception;

Class CouponController extends Controller {

    private $_couponLibObject = '';

    public function __construct(Request $request)
    {
        $this->_couponLibObject = new CouponLib();
    }

    /**
     * update coupons as inactive those have expired
     * @param Request $request
     */
    public function clearCoupon(Request $request)
    {
        try {
            echo "---START";

            $date = date('Y-m-d');
            $coupons_log = $this->_couponLibObject->inactiveExpiredCoupon($date);

            //if debug then echo required data
            if(isset($request->is_debug)){
                if($coupons_log)
                echo json_encode($coupons_log);
                else
                   echo "<br>There is no expired coupon<br>";
            }

            echo "---END";
        }
        catch (Exception $e) {
           // return $e;
        }
    }

}
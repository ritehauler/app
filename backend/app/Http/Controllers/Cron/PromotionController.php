<?php

/**
 * This is written to execute to read discount promotions and
 * will update the product data
 * Class PromotionController
 * Date: 10-01-2018
 * Author: Cubix
 * Copyright: cubix
 */

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Libraries\ProductHelper;
use App\Libraries\PromotionDiscount;
use Illuminate\Http\Request;
use App\Libraries\CustomHelper;
use Validator;
use Illuminate\Http\Exception;

Class PromotionController extends Controller {

    private $_promotionLibObject = '';

    public function __construct(Request $request)
    {
        $this->_promotionLibObject = new PromotionDiscount();
    }

    public function apply(Request $request)
    {
        try {
            echo "---START";
            //Get Promotion Products which end date is expired
            $this->_promotionLibObject->updateExpiredPromotionProduct($request);
            $this->_promotionLibObject->applyPromotion($request);
            echo "---END";
        }
        catch (Exception $e) {
           // return $e;
        }
    }

}
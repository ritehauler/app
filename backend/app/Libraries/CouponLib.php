<?php

/**
 * Class CouponLib
 */
namespace App\Libraries;

use App\Http\Models\SYSEntityType;
use App\Http\Models\SYSTableFlat;
use App\Libraries\System\Entity;
use Illuminate\Http\Exception;
use App\Http\Models\SYSPromotionCron;

Class CouponLib
{
    private $_sysTableFlatModel = '';

    /**
     * CouponLib constructor.
     */
    public function __construct()
    {
        $this->_sysTableFlatModel = new SYSTableFlat('coupon');

    }

    /**
     * Get expired coupons and update their statuses
     * and also log request and response when updating
     * @param $date
     * @return array|bool
     */
    public function inactiveExpiredCoupon($date)
    {
        //Get the coupons those have expired and has active statuses
        $where_condition = " coupon_expiry < '$date' AND `coupon_status` = 1";
        $expiry_coupons = $this->_sysTableFlatModel->getDataByWhere($where_condition);
        //echo "<pre>";print_r($expiry_coupons); exit;
        //Get Entity type
        $entity_type_model = new SYSEntityType();
        $entity_type_id = $entity_type_model->getIdByIdentifier('coupon');

        if ($expiry_coupons) {
            if (count($expiry_coupons) > 0) {
                //update expired coupons statuses
                $coupons_log = $this->updateExpiredCoupons($expiry_coupons, $entity_type_id);
                if (count($coupons_log) > 0) {
                    //Create Coupon log
                    //insert in promotion cron
                    $cron_model = new SYSPromotionCron();
                    $cron_model->addExpiredCoupon($coupons_log);

                    return $coupons_log;
                }
            }

        }

        return FALSE;
    }

    /**
     * Update expired coupons statuses
     * @param $expiry_coupons
     * @param $entity_type_id
     * @return array
     */
    public function updateExpiredCoupons($expiry_coupons, $entity_type_id)
    {
        $coupons_log = [];
        if ($expiry_coupons) {

            $entity_lib = new Entity();
            foreach ($expiry_coupons as $expiry_coupon) {

                $coupon_log = $params = [];
                $params['entity_type_id'] = $entity_type_id;
                $coupon_log['coupon_id'] = $params['entity_id'] = $expiry_coupon->entity_id;
                $params['coupon_status'] = 2;
                $params['mobile_json'] = 1;
                $params['inner_response'] = 1;

                $coupon_log['request'] = $params;
                $response = $entity_lib->apiUpdate($params);
                $coupon_log['response'] = $response;

                $coupons_log[ $expiry_coupon->entity_id ] = $coupon_log;
            }
        }

        return $coupons_log;
    }

    public function validateCoupon($data)
    {
        $return = [];
        $data = is_array($data) ? (object)$data : $data;
        $date = date('Y-m-d');
        $return['error'] = 0;

        if (isset($data->coupon_code)) {
            //check if coupon code is expired
            $where_condition = " `coupon_code` = '$data->coupon_code' AND `coupon_expiry` >= '$date' AND `coupon_status` = 1";
            $coupon_raw = $this->_sysTableFlatModel->getDataByWhere($where_condition);
            $coupon = $coupon_raw[0];

            if (!$coupon) {
                $return['error'] = 1;
                $return['message'] = trans('api_errors.coupon_expired');

                return $return;
            } else {
                //check if coupon is special or not for customer sepcial type
                if (isset($data->customer_id)) {
                    //get Customer data
                    $customer_flat = new SYSTableFlat('customer');
                    $where_condition = " entity_id = " . $data->customer_id;
                    $customer_raw = $customer_flat->getDataByWhere($where_condition);
                    $customer = $customer_raw[0];

                    if ($coupon->offer_to == 2 && $customer->special != 1) {
                        $return['error'] = 1;
                        $return['message'] = trans('api_errors.coupon_valid_for_special');

                        return $return;
                    }
                }
                //check order amount
                if ($data->order_amount < $coupon->minimum_order) {
                    $return['error'] = 1;
                    $return['message'] = trans('api_errors.coupon_minimum_order', ['min_order' => $coupon->minimum_order]);

                    return $return;
                }

                $entity_helper = new EntityHelper();
                $request_params['entity_type_id'] = "coupon";
                $request_params['coupon_code'] = $data->coupon_code;
                $coupon_data = $entity_helper->getDataByEntityType($request_params, FALSE);

                $return['error'] = 0;
                $return['data'] = $coupon_data->data;
                $return['message'] = trans('api_errors.success');

                return $return;
            }

        }

    }

}
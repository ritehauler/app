<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSPromotionCron extends Base
{

    use SoftDeletes;
    public $table = 'sys_promotion_cron';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];



    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'method','requested_params', "return_response", 'return_message', "requested_url", 'created_at', 'updated_at', 'deleted_at');
    }

    /**
     * Log remove promotion data
     * @param $params
     */
    public function addRemovePromotion($params)
    {
        $record = array(
            'method'=> 'removeDiscount',
            'requested_params' => json_encode($params),
            'return_message' => 'success',
            'requested_url' => url('/').'/applyPromotion',
           'created_at' => date("Y-m-d H:i:s")
        );

        $this->put($record);
    }

    /**
     * Log apply promotion data
     * @param $params
     */
    public function addApplyPromotion($params)
    {
        $record = array(
            'method'=> 'applyDiscount',
            'requested_params' => json_encode($params),
            'return_message' => 'success',
            'requested_url' => url('/').'/applyPromotion',
            'created_at' => date("Y-m-d H:i:s")
        );

        $this->put($record);
    }

    /**
     * Log Expired Coupon
     * @param $params
     */
    public function addExpiredCoupon($params)
    {
        $record = array(
            'method'=> 'updateCouponExpiry',
            'requested_params' => json_encode($params),
            'return_message' => 'success',
            'requested_url' => url('/').'/clearCoupon',
            'created_at' => date("Y-m-d H:i:s")
        );

        $this->put($record);
    }

}
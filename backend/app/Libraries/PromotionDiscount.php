<?php

/**
 * Class PromotionDiscount
 */
namespace App\Libraries;
use App\Http\Models\SYSPromotionCron;
use App\Http\Models\SYSEntityType;
use App\Http\Models\SYSTableFlat;
use App\Libraries\System\Entity;
use Illuminate\Http\Request;
use App\Libraries\CustomHelper;
use Illuminate\Http\Exception;

Class PromotionDiscount
{
    private $_sysTableFlatModel = '';
    private $_entityTypeModel = '';
    /**
     * PromotionDiscount constructor.
     */
    public function __construct()
    {
        $this->_sysTableFlatModel = new SYSTableFlat('promotion_discount');
        $this->_apiUrl = config("system.API_SYSTEM_ENTITIES");
        $this->_entityTypeModel = new SYSEntityType();

    }

    /**
     * Update products promotion columns which has expired promotions
     * @param Request $request
     * @return bool
     */
    public function updateExpiredPromotionProduct(Request $request, $date=false)
    {
        try{
            $date = (!$date) ? date('Y-m-d') : $date;
            $product_helper = new ProductHelper();
            $promotion_products = $product_helper->getExpiredPromotionProducts($date,array('entity_id,product_promotion_id'));
            //echo "<pre>"; print_r( $promotion_products); exit;

            if($promotion_products){

                $promotions_log = array();
                $entity_type_id = $this->_entityTypeModel->getIdByIdentifier('product');

                foreach($promotion_products as $product){
                    //update Entity
                    $post_params = array(
                        'entity_type_id' => $entity_type_id,
                        'entity_id' => $product->entity_id,
                        'inner_response' => 1,
                    );

                    $promotion_attributes = $this->_getProductPromotionAttributes();
                    foreach($promotion_attributes as $attribute){
                        $post_params[$attribute] = '';
                    }

                    $promotion_log = array();
                    $promotion_log['promotion_id'] = $product->product_promotion_id;
                    $promotion_log['request'] = $post_params;

                    $request_params = $request->all();
                    $data = CustomHelper::internalCall($request,$this->_apiUrl.'update','POST',$post_params,false);

                    $promotion_log['response'] = (isset($data->error)) ? array('error'=>$data->error,'message'=>$data->message) : array();
                    $promotions_log[$product->product_promotion_id][] =  $promotion_log;

                    $request->replace($request_params);
                    // echo '<pre>'; print_r($data);
                }

                if(count($promotions_log)>0){
                    //insert in promotion cron
                    $cron_model = new SYSPromotionCron();
                    $cron_model->addRemovePromotion($promotions_log);

                   if(isset($request->is_debug)) echo '<pre>'; print_r($promotions_log);
                }
            }

            //  $request->replace($request_params);
            return true;
        }
        catch (Exception $e) {
           // return $e;
        }
    }

    /**
     * Get Products and apply promotions
     * @param Request $request
     * @param bool $date
     */
    public function applyPromotion(Request $request, $date = false)
    {
        try {
            $product_helper = new ProductHelper();
            $promotion_products = $product_helper->getPromotionProducts($date);
            // echo "<pre>"; print_r($promotion_products);
            if ($promotion_products) {

                $promotions_log = array();
                $entity_type_id = $this->_entityTypeModel->getIdByIdentifier('product');

                $entity_lib = new Entity();

                foreach ($promotion_products as $product) {
                    //update Entity
                    if ($product->price > 0) {

                        $post_params = array(
                            'entity_type_id' => $entity_type_id,
                            'entity_id' => $product->promotion_product_id,
                            'inner_response' => 1,
                        );

                        $post_params['product_promotion_id'] = "$product->promotion_id";
                        $post_params['product_promotion_name'] = "$product->promotion_name";
                        $post_params['promotion_start_date'] = $product->start_date;
                        $post_params['promotion_end_date'] = $product->end_date;
                        $post_params['promotion_discount_type'] = "$product->coupon_type";
                        $post_params['promotion_discount'] = "$product->discount";

                        if ($product->coupon_type == 'percent')
                            $product_price = $product->price - (($product->discount / 100) * $product->price);
                        else
                            $product_price = $product->price - $product->discount;

                        $post_params['promotion_discount_amount'] = "$product_price";

                        $promotion_log = array();
                        $promotion_log['promotion_id'] = $product->promotion_id;
                        $promotion_log['request'] = $post_params;

                        // echo "<pre>"; print_r($post_params); exit;
                        $request_params = $request->all();
                        //$data = CustomHelper::internalCall($request, $this->_apiUrl . 'update', 'POST', $post_params, false);
                        $data = $entity_lib->apiUpdate($post_params);
                        $data = json_decode(json_encode($data));
                       // echo "<pre>"; print_r($data->message); exit;

                        $promotion_log['response'] = (isset($data->error)) ? array('error'=>$data->error,'message'=>$data->message) : array();
                        $promotions_log[$product->promotion_id][] = $promotion_log;

                        $request->replace($request_params);
                    }

                }

                if(count($promotions_log)>0){
                    //insert in promotion cron
                    $cron_model = new SYSPromotionCron();
                    $cron_model->addApplyPromotion($promotions_log);

                   if(isset($request->is_debug)) echo '<pre>'; print_r($promotions_log);
                }
            }

        } catch (Exception $e) {
            // return $e;
        }

    }


    /**
     * Product Promotion attributes
     * @return array
     */
    private function _getProductPromotionAttributes()
    {
        return array('product_promotion_id',
                    'product_promotion_name',
                    'promotion_start_date',
                    'promotion_end_date',
                    'promotion_discount_type',
                    'promotion_discount',
                    'promotion_discount_amount');
    }

}
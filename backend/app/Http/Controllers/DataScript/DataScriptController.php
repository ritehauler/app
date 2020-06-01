<?php

/**
 * This is written to clear data from database
 * Class DataScriptController
 * Date: 25-01-2018
 * Author: Cubix
 * Copyright: cubix
 */

namespace App\Http\Controllers\DataScript;

use App\Http\Controllers\Controller;
use App\Http\Models\PLAttachment;
use App\Http\Models\SYSCategory;
use App\Http\Models\SYSTableFlat;
use App\Libraries\CategoryHelper;
use App\Libraries\ProductHelper;
use App\Libraries\PromotionDiscount;
use App\Libraries\System\Entity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Libraries\CustomHelper;
use Illuminate\Support\Facades\Schema;
use Intervention\Image\File;
use Validator;
use Illuminate\Http\Exception;
use Illuminate\Database\Eloquent\Model;


Class DataScriptController extends Controller
{


    public function clearData(Request $request)
    {
        \DB::statement("CALL hard_delete_entity_data('business_user')");
        \DB::statement("CALL hard_delete_entity_data('customer')");
        \DB::statement("CALL hard_delete_entity_data('chef')");
        \DB::statement("CALL hard_delete_entity_data('vendor')");
        \DB::statement("CALL hard_delete_entity_data('vendor_category')");
        \DB::statement("CALL hard_delete_entity_data('item')");
        \DB::statement("CALL hard_delete_entity_data('recipe')");
        \DB::statement("CALL hard_delete_entity_data('recipe_item')");
        \DB::statement("CALL hard_delete_entity_data('inventory')");
        \DB::statement("CALL hard_delete_entity_data('inventory_item_relation')");
        \DB::statement("CALL hard_delete_entity_data('product')");
        \DB::statement("CALL hard_delete_entity_data('billing_address')");
        \DB::statement("CALL hard_delete_entity_data('shipping_address')");
        \DB::statement("CALL hard_delete_entity_data('cart')");
        \DB::statement("CALL hard_delete_entity_data('cart_item')");
        \DB::statement("CALL hard_delete_entity_data('coupon')");
        \DB::statement("CALL hard_delete_entity_data('promotion_discount')");
        \DB::statement("CALL hard_delete_entity_data('promotion_item')");
        // \DB::statement("CALL hard_delete_entity_data('cms')");
        //  \DB::statement("CALL hard_delete_entity_data('faq')");
        \DB::statement("CALL hard_delete_entity_data('customer_loyalty_points')");
        \DB::statement("CALL hard_delete_entity_data('order_discussion')");
        \DB::statement("CALL hard_delete_entity_data('wishlist')");
        \DB::statement("CALL hard_delete_entity_data('delivery_slot')");
        \DB::statement("CALL hard_delete_entity_data('delivery_slot_item')");
        \DB::statement("CALL hard_delete_entity_data('testimonials')");
        // \DB::statement("CALL hard_delete_entity_data('tutorial')");
        \DB::statement("CALL hard_delete_entity_data('about_business')");
        \DB::statement("CALL hard_delete_entity_data('about_business_items')");
        //\DB::statement("CALL hard_delete_entity_data('general_setting')");
        \DB::statement("CALL hard_delete_entity_data('payment_transaction')");
        //\DB::statement("CALL hard_delete_entity_data('tags')");
        \DB::statement("CALL hard_delete_entity_data('product_tags')");
        \DB::statement("CALL hard_delete_entity_data('recipe_tags')");
        \DB::statement("CALL hard_delete_entity_data('custom_notification')");
        // \DB::statement("CALL hard_delete_entity_data('order_statuses')");
        //  \DB::statement("CALL hard_delete_entity_data('payment_config')");
        \DB::statement("CALL hard_delete_entity_data('order_item')");
        \DB::statement("CALL hard_delete_entity_data('order_transaction')");
        \DB::statement("CALL hard_delete_entity_data('lead_order')");
        \DB::statement("CALL hard_delete_entity_data('wallet_transaction')");
        \DB::statement("CALL hard_delete_entity_data('order_revision')");
        \DB::statement("CALL hard_delete_entity_data('order')");
        \DB::statement("CALL hard_delete_entity_data('order_shipping_address')");
        \DB::statement("CALL hard_delete_entity_data('order_cart')");
        //\DB::statement("CALL hard_delete_entity_data('package')");
        // \DB::statement("CALL hard_delete_entity_data('inventory_category')");
        \DB::statement("CALL hard_delete_entity_data('order_cart')");
        \DB::statement("CALL hard_delete_entity_data('driver')");
        \DB::statement("CALL hard_delete_entity_data('agent')");

        \DB::select("DELETE  FROM sys_category");
        \DB::select("DELETE FROM sys_role WHERE role_id > 11");
        \DB::select("DELETE  FROM sys_role_permission_map where role_id <= 11");
        \DB::select("DELETE FROM sys_entity_auth WHERE entity_auth_id <> 1");
        \DB::select("DELETE FROM pl_attachment");
        \DB::select("DELETE FROM sys_entity_history");
        \DB::select("DELETE FROM sys_entity_notification");
        \DB::select("DELETE FROM wfs_work_flow_instance");
        \DB::select("DELETE FROM wfs_task_instance");
        \DB::select("DELETE FROM wfs_wfi_ti_relation");

        echo "<h3>Deleted Successfully</h3>";
    }

    /**
     * Update product count in category
     */
    public function updateProductCount()
    {
        $product_model = new SYSTableFlat('product');
        $where_condition = " `status` = 1 AND availability = 1";
        $products = $product_model->getDataByWhere($where_condition);
        $category_helper = new CategoryHelper();
        if($products){

            \DB::select("update sys_category set product_count = 0");

            foreach($products as $product){

                // $categories = $product->category_id;
                if(!empty( $product->category_id)){

                    echo "<pre>"; print_r( $product);
                    $p_categories = explode(',', $product->category_id);
                    echo "<pre>"; print_r( $p_categories);
                    $category_helper->adjustProductCategoryParentCount($p_categories);

                }

            }
        }

    }

    /**
     * Compress Image
     */
    public function compressImages()
    {
        $product_model = new SYSTableFlat('product');
        // $where_condition = " `status` = 1 AND availability = 1";
        $products = $product_model->getDataByWhere();

        echo '<h3>Products</h3>';

        if($products){
            foreach($products as $product){

                if(!empty($product->entity_id))
                    $this->createCompressFile($product->entity_id);
            }
        }


        $promotion_discount_model = new SYSTableFlat('promotion_discount');
        $promotion_discount = $promotion_discount_model->getDataByWhere();
        /* echo "<pre>";
         print_r($promotion_discount);*/
        echo '<h3>Promotions</h3>';

        if($promotion_discount){
            foreach($promotion_discount as $discount){

                if(!empty($discount->entity_id))
                    $this->createCompressFile($discount->entity_id);
            }
        }

        echo '<h3>Categories</h3>';
        $category_model = new SYSCategory();
        $categories = $category_model->all();
        /* echo "<pre>";
         print_r($categories);*/
        if($categories){
            foreach($categories as $category){

                if(!empty($category->category_id))
                    $this->createCompressFile($category->category_id);
            }
        }

        $chef_model = new SYSTableFlat('chef');
        $chefs = $chef_model->getDataByWhere();
        /* echo "<pre>";
         print_r($promotion_discount);*/
        echo '<h3>Chef</h3>';

        if($chefs){
            foreach($chefs as $chef){

                if(!empty($chef->entity_id))
                    $this->createCompressFile($chef->entity_id);
            }
        }

    }

    private function createCompressFile($entity_id)
    {
        $pl_attachment_model = new PLAttachment();
        $attachment = $pl_attachment_model->getBy('entity_id',$entity_id);

        if(isset($attachment->attachment_id)){

            echo public_path(str_replace('public/files/','','files/'.$attachment->file));
            if(!file_exists(public_path(str_replace('public/files/','','files/'.$attachment->file)))){
                return false;
            }


            $dir_path = config("constants.DIR_ATTACHMENT");
            $filename = str_replace('public/files/', '', $attachment->file);

            if($attachment->attachment_type_id == 8 &&  empty($attachment->compressed_file)){
                // echo "<pre>"; print_r($attachment);

                $compress_image_dir = config("constants.DIR_COMPRESSED");
                $compress_prefix = config("constants.COMPRESS_PREFIX");

                // echo \URL::to($attachment->file);
                //if (file_exists(\URL::to($attachment->file))) {

                $attachment->compressed_file = $pl_attachment_model->compressImage($dir_path, $filename, $compress_image_dir, $compress_prefix);


                // if (!empty($attachment->compressed_file)) {

                $attachment->updated_at = date('Y-m-d H:i:s');
                $pl_attachment_model->set($attachment->attachment_id, (array)$attachment);
                // }

                /* echo "<pre>";
                   print_r($attachment);*/
                //  }

            }

            if($attachment->attachment_type_id == 8 &&  empty($attachment->mobile_file)){

                //create image for mobile app
                $mobile_image_dir = config("constants.DIR_MOBILE");
                $mobile_prefix = config("constants.MOBILE_FILE_PREFIX");
                $attachment->mobile_file =   $pl_attachment_model->createMobileImage($dir_path, $filename,$mobile_image_dir,$mobile_prefix);

                $attachment->updated_at = date('Y-m-d H:i:s');
                $pl_attachment_model->set($attachment->attachment_id, (array)$attachment);


            }

            echo "<pre>";
            print_r($attachment);
        }
    }

    public function updateName()
    {
        $product_model = new SYSTableFlat('customer');
        $customers = $product_model->getDataByWhere();

        echo '<h3>Customers</h3>';

        $entity_lib = new Entity();

        if($customers) {
            foreach ($customers as $customer) {

                $post_params = [
                    'entity_type_id' => 11,
                    'entity_id' => $customer->entity_id,
                    'full_name' => CustomHelper::setFullName($customer),
                ];
                echo '<pre>';
                print_r($post_params);

                $data = $entity_lib->apiUpdate($post_params);
            }
        }


        $product_model = new SYSTableFlat('business_user');
        $customers = $product_model->getDataByWhere();

        echo '<h3>Business User</h3>';

        $entity_lib = new Entity();

        if($customers) {
            foreach ($customers as $customer) {

                $post_params = [
                    'entity_type_id' => 11,
                    'entity_id' => $customer->entity_id,
                    'full_name' => CustomHelper::setFullName($customer),
                ];
                echo '<pre>';
                print_r($post_params);

                $data = $entity_lib->apiUpdate($post_params);
            }
        }
    }

    public function clearSystemEntity()
    {
        $delete_entity = array('vendor','vendor_category',
            'inventory',
            'recipe',
            'item',
            'product_tags',
            'recipe_tags',
            'inventory_item_relation',
            'recipe',
            'recipe_item',
            'chef',
            'vendor',
            'vendor_category',
            'delivery_slot',
            'delivery_slot_item',
            'testimonials',
            'about_business',
            'about_business_items',
            //'testimonials',
            'coupon',
            'promotion_discount',
            'promotion_item',
            'payment_transaction',
            'wishlist',
            'recipe_rating',
            'bundle_tags',
            //'lead_order'

        );

        $delete_e = implode(',',$delete_entity);
        //  echo "select * from sys_entity_type where identifier IN ($delete_e)"; exit;
        // $entity_types = \DB::select("select * from sys_entity_type where identifier IN ($delete_e)");

        foreach($delete_entity as $entity_type){

            // echo "select * from sys_entity_type where identifier = '$entity_type'"; die;
            $entity_db = \DB::select("select * from sys_entity_type where identifier = '$entity_type'");


            // echo '<pre>'; print_r($entity); exit;
            //die($entity->entity_type_id);
            if(isset($entity_db[0]->entity_type_id)){
                $entity = $entity_db[0];
                \DB::select("Delete from sys_entity_attribute where entity_type_id = $entity->entity_type_id ");

                \DB::select("Delete from sys_attribute where linked_entity_type_id = $entity->entity_type_id ");

                \DB::select("Delete from sys_module where entity_type_id = $entity->entity_type_id ");

                \DB::select("Delete from sys_entity_type where entity_type_id = $entity->entity_type_id ");
                 Schema::dropIfExists($entity_type.'_flat');

            }

            \DB::select("DELETE FROM sys_entity_attribute WHERE attribute_id NOT IN (SELECT attribute_id FROM sys_attribute)");
            \DB::select("DELETE FROM sys_attribute_option WHERE attribute_id NOT IN (SELECT attribute_id FROM sys_attribute)");

            \DB::select("DELETE FROM sys_entity_role WHERE entity_id NOT IN (SELECT entity_id FROM sys_entity)");

        }


    }

    public function updateDate()
    {
        $product_model = new SYSTableFlat('order');
        $customers = $product_model->getDataByWhere();

        echo '<h3>Orders</h3>';

        $entity_lib = new Entity();

        if($customers) {
            foreach ($customers as $customer) {

                $pickup_date = $customer->pickup_date.' '.$customer->pickup_time;
                $pickup_date_obj = Carbon::createFromFormat('Y-m-d H:i:s', $pickup_date, APP_TIMEZONE);
                $pickup_date_cst =  $pickup_date_obj->setTimezone('EST');

                $post_params = [
                    'entity_type_id' => 15,
                    'entity_id' => $customer->entity_id,
                    'pickup_date_cst' => $pickup_date_cst,
                ];

                echo '<pre>';
                print_r($post_params);

                $data = $entity_lib->apiUpdate($post_params);

                echo '<pre>';
                print_r($data);
            }
        }
    }
}
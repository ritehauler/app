<?php namespace App\Libraries;
use App\Http\Models\SYSAttribute;
use App\Http\Models\SYSAttributeOption;
use App\Http\Models\ApiMethodField;
use App\Libraries\ApiCurl;

/**
 * Simple Fields Library
 *
 *
 * @category   Libraries
 * @package    Fields
 * @subpackage Libraries

 */
class EntityTypeMask
{
	 
    /**
     * Constructor
     *
     * @param string $url URL
     */
    public function __construct(){
		
	}
 
    
	public function productSystemAttributes($ec,$product,$entityTypeData,$request){
		$product->product_orders = $product->your_last_order = (object)array();
	    if(isset($request->actor_user_id) && is_numeric($request->actor_user_id)){
			$s_col['product_id'] = $product->entity_id; 
			$s_col['mobile_json'] = 1; 
			$s_col['entity_type_id'] = '15'; 
			$s_col['borrower_id'] =$request->actor_user_id; 
			$orderData = $ec->getListData('',$s_col['entity_type_id'],(object)$s_col,'1',0,'entity_id','DESC');
		  
			if($orderData && isset($orderData['data']['order'][0])){								
				$product->your_last_order = $orderData['data']['order'][0];
			}
		}
		$order_status = "(order_status=2 OR order_status=5 OR order_status=6  OR order_status=7)";
		$productData = \DB::select("SELECT entity_id,order_status,start_date,end_date,pickup_time FROM order_flat WHERE $order_status AND product_id=$product->entity_id AND deleted_at IS NULL ORDER BY entity_id DESC LIMIT 30");
		if (isset($productData[0])) {
			$product->product_orders = $productData;
		}
		
		return $product;
	}
	
}
 
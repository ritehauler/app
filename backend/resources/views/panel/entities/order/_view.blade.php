@include(config('panel.DIR').'header')
<?php
use App\Http\Models\PLAttachment;
		use App\Libraries\Fields;
		use App\Libraries\EntityHelper;
		use App\Libraries\GeneralSetting;
		use App\Libraries\CustomHelper;

$fields = new Fields();
$entity_helper = "App\Libraries\EntityHelper";

$pl_atatchment = new PLAttachment();
$general_setting = new GeneralSetting();

if($update){
	//order information
	$order_id = isset($update->entity_id) ? $update->entity_id : "";
	$order_date = isset($update->created_at) ? CustomHelper::displayDateTime($update->created_at) : "";

	$order = $update->attributes;
	$order_status = isset($order->order_status->value) ? $order->order_status->value : "";

	//customer information
	$customer = $update->attributes->customer_id;
	$email = isset($customer->detail->auth->email) ? $customer->detail->auth->email : "";
	$first_name = isset($customer->detail->attributes->first_name) ?  $customer->detail->attributes->first_name : "";
	$last_name = isset($customer->detail->attributes->last_name) ?  $customer->detail->attributes->last_name : "";

	if(isset($customer->detail->attributes->full_name) && !empty($customer->detail->attributes->full_name)){
		$customer_name = $customer->detail->attributes->full_name;
	}
	else{
        $customer_name = CustomHelper::setFullName($customer->detail->attributes);
	}


	$star_rating = $entity_helper::parseAttributeToDisplay(isset($customer->detail->attributes->star_rating) ?  $customer->detail->attributes->star_rating : "");
	$reviews = isset($customer->detail->attributes->reviews) ?  $customer->detail->attributes->reviews : "No Reviews";

}
?>
<!-- Begin: Content -->
<section id="content_wrapper" class="content">
    <section id="content">
		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-theme panel-border top mb25">
					<div class="panel-heading">
						<span class="panel-title">Order # {!! isset($order->order_number) ? $order->order_number : "" !!}</span>
					</div>
					<div class="panel-body p20 pb10">
						<table width="100%" class="orderPrevDetail">
							<tr>
								<td>Order Date</td>
								<td><b>{!! $order_date !!}</b></td>
							</tr>
							<tr>
								<td>Order Status</td>
								<td><b>{!! $order_status !!}</b></td>
							</tr>
							{{--<tr>
								<td>Purchased Form</td>
								<td><b>Purchased</b></td>
							</tr>--}}
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-theme panel-border top mb25">
					<div class="panel-heading">
						<span class="panel-title">Account Information</span>
					</div>
					<div class="panel-body p20 pb10">
						<table width="100%" class="orderPrevDetail">
							<tr>
								<td>Customer Name</td>
								<td><b>{!! $customer_name !!}</b></td>
							</tr>
							<tr>
								<td>Email</td>
								<td><b>{!! $email !!}</b></td>
							</tr>
							{{--<tr>
								<td>Customer Group</td>
								<td><b>General</b></td>
							</tr>--}}
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-theme panel-border top mb25">
					<div class="panel-heading">
						<span class="panel-title">Billing Detail</span>
					</div>
					<div class="panel-body p20 pb10">
						<table width="100%" class="orderPrevDetail">
							<tr>
								<td>Address</td>
								<td><b>
									<?php if(isset($order->billing_address)){
											if(isset($order->billing_address->detail->attributes)){
												$billing_address = $order->billing_address->detail->attributes
											?>
										@if(isset($billing_address->street) && !empty(trim($billing_address->street))){!! $billing_address->street !!}<br>@endif
											@if(isset($billing_address->company) && !empty(trim($billing_address->company))){!! $billing_address->company !!}@endif
										@if(isset($billing_address->telephone) && !empty(trim($billing_address->telephone))) {!! $billing_address->telephone !!}@endif
										@if(isset($billing_address->city) && !empty(trim($billing_address->city)))<br>{!! $billing_address->city !!},@endif
										@if(isset($billing_address->region) && !empty(trim($billing_address->region))){!! $billing_address->region !!}, @endif
										@if(isset($billing_address->postcode) && !empty(trim($billing_address->postcode))){!! $billing_address->postcode !!} @endif
										@if(isset($billing_address->country) && !empty(trim($billing_address->country)))<br>@endif
											<?php }
												else{
													echo "No Address Found";
												}
											}else{
											echo "No Address Found";
										}	?>
								</b></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-theme panel-border top mb25">
					<div class="panel-heading">
						<span class="panel-title">Shipping Detail</span>
					</div>
					<div class="panel-body p20 pb10">
						<table width="100%" class="orderPrevDetail">
							<tr>
								<td>Address</td>
								<td><b>
										<?php if(isset($order->shipping_address)){
										if(isset($order->shipping_address->detail->attributes)){
											$shipping_address = $order->shipping_address->detail->attributes;

                                        //echo "<pre>"; print_r( $shipping_address);exit;

                                        ?>
											@if(isset($shipping_address->street) && !empty(trim($shipping_address->street))) {!! $shipping_address->street !!}<br> @endif
											@if(isset($shipping_address->company) && !empty(trim($shipping_address->company))) {!! $shipping_address->company !!} @endif
											@if(isset($shipping_address->telephone) && !empty(trim($shipping_address->telephone))) {!! $shipping_address->telephone !!}@endif
											@if(isset($shipping_address->city) && !empty(trim($shipping_address->city))) <br>{!! $shipping_address->city !!}, @endif
											@if(isset($shipping_address->region) && !empty(trim($shipping_address->region))) {!! $shipping_address->region !!}, @endif
											@if(isset($shipping_address->postcode) && !empty(trim($shipping_address->postcode))) {!! $shipping_address->postcode !!} @endif
											@if(isset($shipping_address->country) && !empty(trim($shipping_address->country))) <br> @endif
										<?php }
											else{
												echo "No Address Found";
											}
											}else{
												echo "No Address Found";
											}	?>
								</b></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-theme panel-border top mb25">
					<div class="panel-heading">
						<span class="panel-title">Order Total</span>
					</div>
					<div class="panel-body p20 pb10">
						<table width="100%" class="orderPrevDetail">
							<tr>
								<td>Sub Total</td>
								<?php
								$discount_amount = (!empty($order->discount_amount)) ? $order->discount_amount : 0;
								$subtotal = (!empty($order->subtotal)) ? $order->subtotal : 0;
								//$subtotal =  $order->subtotal_with_discount - $discount_amount;
								?>
								<td><b>{!! $general_setting->getPrettyPrice($subtotal) !!}</b></td>
							</tr>
							<tr>
								<td>Discount</td>
								<td><b>{!! $general_setting->getPrettyPrice($discount_amount) !!}</b></td>
							</tr>

							<tr>
								<td>Delivery Charges</td>
								<td><b>{!!  $general_setting->getPrettyPrice((!empty($order->delivery_charge)) ? $order->delivery_charge : 0) !!}</b></td>
							</tr>

							<tr>
								<td>Grand Total</td>
								<td><b>{!! $general_setting->getPrettyPrice(isset($order->grand_total) ? $order->grand_total : 0) !!}</b></td>
							</tr>
							<tr>
								<td>Wallet</td>
								<td><b>{!!  $general_setting->getPrettyPrice((!empty($order->wallet)) ? $order->wallet : 0) !!}</b></td>
							</tr>
							<tr>
								<td>Total Paid</td>
								<td><b>{!! $general_setting->getPrettyPrice(isset($order->grand_total) ? $order->grand_total : 0) !!}</b></td>
							</tr>
							{{--<tr>
								<td>Total Refunded</td>
								<td><b>$0.00</b></td>
							</tr>
							<tr>
								<td>Total Due</td>
								<td><b>$0.00</b></td>
							</tr>--}}
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-theme panel-border top mb25">
					<div class="panel-heading">
						<span class="panel-title">Payment Information</span>
					</div>
					<div class="panel-body p20 pb10">
						<table width="100%" class="orderPrevDetail">
							<tr>
							<td>Payment Type</td>
							<td><b>Cash On Delivery</b></td>
							</tr>
							{{--<tr>
								<td>Credit Card</td>
								<td><b></b></td>
							</tr>
							<tr>
								<td>Credit Card Type</td>
								<td><b>Visa</b></td>
							</tr>
							<tr>
								<td>Credit Card Number</td>
								<td><b>411111333307</b></td>
							</tr>
							<tr>
								<td>Name on the Card</td>
								<td><b>Tassen</b></td>
							</tr>
							<tr>
								<td>Expiration Date</td>
								<td><b>12/2017</b></td>
							</tr>--}}
						</table>
					</div>
				</div>
			</div>


			<div class="col-md-6">
				<div class="panel panel-theme panel-border top mb25">
					<div class="panel-heading">
						<span class="panel-title">Commission</span>
					</div>
					<div class="panel-body p20 pb10">
						<table width="100%" class="orderPrevDetail">
							<tr>
								<td>Delivery Commission</td>
								<td><b>{!!  $general_setting->getPrettyPrice((!empty($order->commission_for_rider)) ? $order->commission_for_rider : 0) !!}</b></td>
							</tr>
						</table>
					</div>
				</div>
			</div>

			<div class="col-md-12">
				<div class="panel panel-theme panel-border top mb25">
					<div class="panel-heading">
						<span class="panel-title">Rate & Reviews</span>
					</div>
					<div class="panel-body p20 pb10">
						<table width="100%" class="orderPrevDetail">
							<tr>
								<td>Rating</td>
								<td><b>{!! $star_rating !!}</b></td>
							</tr>
							<tr>
								<td>Reviews</td>
								<td><b>{!! $reviews !!}</b></td>
							</tr>
						</table>
					</div>
				</div>
			</div>

			<div class="col-md-12">
				<div class="panel panel-theme panel-border top mb25">
					<div class="panel-heading">
						<span class="panel-title">Item Ordered</span>
					</div>
					<div class="panel-body pn">
						<table width="100%" class="table table-striped" >
							<tr>
								<th width="10%">Product</th>
								<th>Name</th>
								<th>Qty</th>
								<th>Price</th>
							</tr>

							<?php
							$no_item = 0;
							if(isset($update->order_item[0])){?>

							<?php foreach($update->order_item as $order_item){

								if(isset($order_item->attributes->product_id->detail->attributes)){

								$order_item->attributes->product_id->detail->gallery;
								$file = $fields::getGalleryImageFile($order_item->attributes->product_id->detail->gallery,'product');
							?>
							<tr>
								<td><img src="{!!$file !!}" class="mw30 mr15 border bw2 border-alert"></td>
								<td>{!! $order_item->attributes->product_id->value !!}</td>
								<td>{!! isset($order_item->attributes->quantity) ? $order_item->attributes->quantity : "" !!}</td>
								<td>{!! $general_setting->getPrettyPrice($order_item->attributes->price) !!}</td>
							</tr>
							<?php
									$no_item++;
									}
								}
							}


							if($no_item == 0){
							?>
							<tr><td colspan="4">No Item Found</td></tr>
							<?php }
							?>

						</table>
					</div>
				</div>
			</div>
			@include(config('panel.DIR') . 'entities/order/order_history')

		</div>
        </div>
    </section>
    <!-- End: Content -->
    <!-- Begin: Page Footer -->
    @include(config('panel.DIR') . 'footer_bottom')
</section>
<!-- End: Page Footer -->

@include(config('panel.DIR').'footer')
@include(config('panel.DIR').'header')

<section id="content_wrapper" class="content dubble_side">
	<div id="content" class="table-layout animated fadeIn">
		<div class="tray tray-center p25 va-t posr">
			<div class="panel panel-theme panel-border top">
				<div class="panel-heading">
					<span class="panel-title">ORDER</span>
				</div>
				<div class="panel-body">
					<table width="100%" class="table orderPrevDetail">
						<tbody>
							<tr>
								<td>Order ID</td>
								<td>
									<a href="../../order/view/{{ $orderReviewDetail['order_review']->target_entity_id }}">
										<b>{{ count($orderReviewDetail['order_review']) ? $orderReviewDetail['order_review']->target_entity_id : '' }}</b>
									</a>
								</td>
							</tr>
							<tr>
								<td>Pickup Location</td>
								<td><b>{{ $orderReviewDetail['order_detail']->order_pickup[0]->attributes->address }}</b></td>
							</tr>
							<tr>
								<td>Dropoff Location</td>
								<td><b>{{ $orderReviewDetail['order_detail']->order_dropoff[0]->attributes->address }}</b></td>
							</tr>
							<tr>
								<td>DateTime</td>
								<td><b>
										{{ $orderReviewDetail['order_detail']->attributes->pickup_date . ' - ' . date('h:i a',strtotime($orderReviewDetail['order_detail']->attributes->pickup_time)) }}
									</b>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
	<div id="content" class="table-layout animated fadeIn">
		<div class="tray tray-center p25 va-t posr">
			<div class="panel panel-theme panel-border top">
				<div class="panel-heading">
					<span class="panel-title">CUSTOMER</span>
				</div>
				<div class="panel-body">
					<table width="100%" class="table orderPrevDetail">
						<tbody>
							<tr>
								<td>Customer Name</td>
								<td><b>{{ count($orderReviewDetail['customer']) ? $orderReviewDetail['customer']->attributes->full_name : '' }}</b></td>
							</tr>
							<tr>
								<td>Rate</td>
								<td><b>{{ count($orderReviewDetail['customer_review']) ? $orderReviewDetail['customer_review']->rating : '' }}</b></td>
							</tr>
							<tr>
								<td>FeedBack</td>
								<td><b>{{ count($orderReviewDetail['customer_review']) ? $orderReviewDetail['customer_review']->json_data : '' }}</b></td>
							</tr>
							<tr>
								<td>Comment</td>
								<td><b>{{ count($orderReviewDetail['customer_review']) ? $orderReviewDetail['customer_review']->review : '' }}</b></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
	<div id="content" class="table-layout animated fadeIn">
		<div class="tray tray-center p25 va-t posr">
			<div class="panel panel-theme panel-border top">
				<div class="panel-heading">
					<span class="panel-title">DRIVER</span>
				</div>
				<div class="panel-body">
					<table width="100%" class="table orderPrevDetail">
						<tbody>
							<tr>
								<td>Driver Name</td>
								<td><b>{{ count($orderReviewDetail['driver']) ? $orderReviewDetail['driver']->attributes->full_name : '' }}</b></td>
							</tr>
							<tr>
								<td>Rate</td>
								<td><b>{{ count($orderReviewDetail['driver_review']) ? $orderReviewDetail['driver_review']->rating : '' }}</b></td>
							</tr>
							<tr>
								<td>FeedBack</td>
								<td><b>{{ count($orderReviewDetail['driver_review']) ? $orderReviewDetail['driver_review']->json_data : '' }}</b></td>
							</tr>
							<tr>
								<td>Comment</td>
								<td><b>{{ count($orderReviewDetail['driver_review']) ? $orderReviewDetail['driver_review']->review : '' }}</b></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
</section>

{{--<!-- Start: Content-Wrapper -->
<section id="content_wrapper" class="content">
	@if(Session::has('message'))
		<p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('message') }}</p>
	@endif
	<div id="seaction-header"> @include(config('panel.DIR').'flash_message')
		<section id="content" class="pn">
			<div class="panel panel-theme panel-border top mb25">
				<div class="panel-heading">
					<span class="panel-title">Order Rating Detail</span>
				</div>
				<div class="panel-body pn" style="margin-top: 50px;">
					<div class="row">
						<div class="col-md-6">
							<div class="panel panel-theme top mb25">
								<div class="panel-heading">
									<span class="panel-title">Customer Review</span>
								</div>
								<div class="panel-body p20 pb10">
									<table width="100%" class="orderPrevDetail">
										<tbody>
											<tr>
												<td>Customer Name</td>
												<td><b>{{ count($orderReviewDetail['customer']) ? $orderReviewDetail['customer']->attributes->full_name : '' }}</b></td>
											</tr>
											<tr>
												<td>Rate</td>
												<td><b>{{ count($orderReviewDetail['customer_review']) ? $orderReviewDetail['customer_review']->rating : '' }}</b></td>
											</tr>
											<tr>
												<td>FeedBack</td>
												<td><b>{{ count($orderReviewDetail['customer_review']) ? $orderReviewDetail['customer_review']->json_data : '' }}</b></td>
											</tr>
											<tr>
												<td>Comment</td>
												<td><b>{{ count($orderReviewDetail['customer_review']) ? $orderReviewDetail['customer_review']->review : '' }}</b></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="panel panel-theme  top mb25">
								<div class="panel-heading">
									<span class="panel-title">Driver Review</span>
								</div>
								<div class="panel-body p20 pb10">
									<table width="100%" class="orderPrevDetail">
										<tbody>
										<tr>
											<td>Driver Name</td>
											<td><b>{{ count($orderReviewDetail['driver']) ? $orderReviewDetail['driver']->attributes->full_name : '' }}</b></td>
										</tr>
										<tr>
											<td>Rate</td>
											<td><b>{{ count($orderReviewDetail['driver_review']) ? $orderReviewDetail['driver_review']->rating : '' }}</b></td>
										</tr>
										<tr>
											<td>FeedBack</td>
											<td><b>{{ count($orderReviewDetail['driver_review']) ? $orderReviewDetail['driver_review']->json_data : '' }}</b></td>
										</tr>
										<tr>
											<td>Comment</td>
											<td><b>{{ count($orderReviewDetail['driver_review']) ? $orderReviewDetail['driver_review']->review : '' }}</b></td>
										</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<div class="panel panel-theme top mb25">
								<div class="panel-heading">
									<span class="panel-title">Order Detail</span>
								</div>
								<div class="panel-body p20 pb10">
									<table width="100%" class="orderPrevDetail">
										<tbody>
										<tr>
											<td>Order ID</td>
											<td>
												<a href="../../order/view/{{ $orderReviewDetail['order_review']->target_entity_id }}">
													<b>{{ count($orderReviewDetail['order_review']) ? $orderReviewDetail['order_review']->target_entity_id : '' }}</b>
												</a>
											</td>
										</tr>
										<tr>
											<td>Pickup Location</td>
											<td><b>{{ $orderReviewDetail['order_detail']->order_pickup[0]->attributes->address }}</b></td>
										</tr>
										<tr>
											<td>Dropoff Location</td>
											<td><b>{{ $orderReviewDetail['order_detail']->order_dropoff[0]->attributes->address }}</b></td>
										</tr>
										<tr>
											<td>DateTime</td>
											<td><b>
													{{ $orderReviewDetail['order_detail']->attributes->pickup_date . ' - ' . date('h:i a',strtotime($orderReviewDetail['order_detail']->attributes->pickup_time)) }}
												</b>
											</td>
										</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- End: Content -->
		<!-- Begin: Page Footer -->
	@include(config('panel.DIR') . 'footer_bottom')
	<!-- End: Page Footer -->
		<!-- Modal -->
</section>--}}
<!-- End: Content-Wrapper -->

<!-- Required Plugin CSS -->
<link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datepicker/css/bootstrap-datetimepicker.css' ) !!}">
<link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/daterange/daterangepicker.css' ) !!}">
<!-- Page Plugins via CDN -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/moment/moment.min.js' ) !!}"></script>
<!-- Datatables -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datatables/media/js/datatables.min.js' ) !!}"></script>
<!-- ckeditor -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/ckeditor/ckeditor.js' ) !!}"></script>
<!-- Page Plugins -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/xeditable/js/bootstrap-editable.js' ) !!}"></script>
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/daterange/daterangepicker.js' ) !!}"></script>
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datepicker/js/bootstrap-datetimepicker.js' ) !!}"></script>
<script type="text/javascript">
	$(document).ready(function() {

		// Init Boostrap Multiselect
		$('#multiselect2').multiselect({
			includeSelectAllOption: true
		});
		// Init Boostrap Multiselect
		$('#multiselect3').multiselect({
			includeSelectAllOption: true
		});
		// Init daterange plugin
		$('#daterangepicker1').daterangepicker();
		// Init datetimepicker - fields
		$('#datetimepicker1').datetimepicker();
		// Select Status
		if ($('a').hasClass('admin-status')){
			$('.admin-status').editable({
				showbuttons: false,
				source: [
					{value: 1, text: 'Active'},
					{value: 2, text: 'Inactive'},
				]
			});
		}
	});
</script>
@include(config('panel.DIR').'footer')
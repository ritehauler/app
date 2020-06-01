@include(config('panel.DIR').'header')
<style>
	.star-rating {
		line-height:32px;
		font-size:1.25em;
	}

	.star-rating .fa-star{color: #ecca09;}
</style>
<!-- Start: Content-Wrapper -->
<section id="content_wrapper" class="content">
	@if(Session::has('message'))
		<p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('message') }}</p>
	@endif
	<div id="seaction-header">
		@include(config('panel.DIR').'flash_message')
		@include(config('panel.DIR') . 'entities/report/advance-search')
		<section id="content" class="pn" style="margin-top: 60px;">
			<div class="panel panel-theme panel-border top mb25">
				<div class="panel-heading">
					<span class="panel-title">Listing</span>
				</div>
				<div class="panel-body pn">
						<section id="content" class="pn table-layout">
							<div class="tray pn">
								<div class="panel">
									<div class="table-responsive">
										<table class="table table-hover responsive" id="mydatatable" cellspacing="0" width="100%">
											<thead>
											<tr>
												<th>Order ID</th>
												<th>Driver Name</th>
												<th>Driver Rating</th>
												<th>Pickup City</th>
												<th>dropOff City</th>
												<th>Customer Name</th>
												<th>Customer Rating</th>
												<th>Customer Email</th>
												<th>Customer Phone</th>
												<th>Order Amount</th>
												<th>Date</th>
												<th>Order Status</th>
											</tr>
											</thead>
										</table>
									</div>
								</div>
							</div>
						</section>
						<input type="hidden" name="_token" value="{!! csrf_token() !!}" />
					</form>
				</div>
			</div>
		</section>

		<!-- End: Content -->
		<!-- Begin: Page Footer -->
	@include(config('panel.DIR') . 'footer_bottom')
	<!-- End: Page Footer -->

		<!-- Modal -->

</section>
<!-- Detailed View Popup -->
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

		$('#mydatatable').DataTable().destroy();
		var dg = $("#mydatatable").DataTable({
			processing: true,
			serverSide: true,
			//paging: false,
			searching: false,
			"oLanguage":{
				"sProcessing": loaderHtml(),
			},
			ajax: {
				url: "{{ Request::url() . '/ajaxListing?_token=' . csrf_token() }}", // ajax source
				type: "POST",
				data : function(d) {
					for (var attrname in dg_ajax_params) { d[attrname] = dg_ajax_params[attrname]; }
				}
			},
			drawCallback: function (settings) {

			},
			lengthMenu: [
				[10, 20, 50, 100, - 1],
				[10, 20, 50, 100, "All"] // change per page values here
			],
			pageLength: 10, // default record count per page
			columnDefs : [
				{
					data: "ids",
					orderable: false,
					className: 'select-checkbox',
					targets: 0
				}, {
					data: "driver_name",
					orderable: false,
					targets: 1
				}, {
					data: "driver_rating",
					orderable: false,
					targets: 2
				},{
					data: "pickup_city",
					orderable: false,
					targets: 3
				}, {
					data: "dropoff_city",
					orderable: false,
					targets: 4
				},{
					data: "customer_name",
					orderable: false,
					targets: 5
				},{
					data: "customer_rating",
					orderable: false,
					targets: 6
				}, {
					data: "customer_email",
					orderable: false,
					targets: 7
				}, {
					data: "customer_phone",
					orderable: false,
					targets: 8
				}, {
					data: "order_amount",
					orderable: false,
					targets: 9
				},{
					data: "date",
					orderable: false,
					targets: 10
				},{
					data: "order_status",
					orderable: false,
					targets: 11
				}
			]
		});

		// add search to datatable
		dgSearch(dg);
		// add select actions to datatable
		dgSelectActions(dg);


		$('.export_rating').on('click',function(e){
			e.preventDefault()
			$('input[name="do_export"]').val('1');
			$('#searchEntity').submit();
		})

	});
</script>
@include(config('panel.DIR').'footer')
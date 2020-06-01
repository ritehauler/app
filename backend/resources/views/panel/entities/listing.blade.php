@include(config('panel.DIR').'header')
		<!-- Start: Content-Wrapper -->
<section id="content_wrapper" class="content">

	@if(Session::has('message'))
		<p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('message') }}</p>
	@endif
	<div id="seaction-header"> @include(config('panel.DIR').'flash_message')
		<div class="adv-search">
			<div class="topbar-left">
				<span class="panel-controls">
					@if(!empty($delete_permission) && $delete_permission == 1)
						<a class="select_action" title="delete">
							<button type="button" class="btn-default btn-sm link-unstyled ib" ><span class="icon mdi mdi-delete pr5 fs15"></span> Delete</button>
						</a>
					@endif
					@if(in_array($entity_data->identifier,$allow_export))
					<a class="export_entity hide" title="export">
						<button type="button" class="btn-default btn-sm link-unstyled ib" ><span class="icon mdi mdi-export pr5 fs15"></span>Export to Excel</button>
					</a>
						@endif
						@if(in_array($entity_data->identifier,$allow_import))
							<a class="" title="Import" href="{!! URL::to($panel_path.$module.'/import') !!}">
								<button type="button" class="btn-default btn-sm link-unstyled ib" ><span class="icon mdi mdi-export pr5 fs15"></span>Import</button>
							</a>
						@endif
					</span>
			</div>
			@if(!empty($add_permission) && $add_permission == 1)

				<div class="topbar-right text-right">
					<div class="pull-right adv-search-bar">

						<?php if($entity_data->identifier == "recipe"){ ?>
							<button type="button" class="accordion-toggle mr5 btn-default btn-sm accordion-icon link-unstyled collapsed ib" data-toggle="collapse" data-parent="#accordion" href="#adv-search"><span class="icon mdi mdi-menu"></span></button>
							<a href="{!! URL::to($panel_path.$module.'/add?recipe_type=1') !!}" class="btn-default btn-sm add-new-btn link-unstyled ib"><span class="icon mdi mdi-plus pr5 fs15"></span>{!! trans('panel.create')." ".trans('panel.product') !!}</a>

							<a href="{!! URL::to($panel_path.$module.'/add?recipe_type=2') !!}" class="btn-default btn-sm add-new-btn link-unstyled ib"><span class="icon mdi mdi-plus pr5 fs15"></span>{!! trans('panel.create')." ".trans('panel.recipe') !!}</a>

							<a href="{!! URL::to($panel_path.$module.'/add?recipe_type=3') !!}" class="btn-default btn-sm add-new-btn link-unstyled ib"><span class="icon mdi mdi-plus pr5 fs15"></span>{!! trans('panel.create')." ".trans('panel.bundle') !!}</a>

							<?php } else if($entity_data->identifier == "inventory"){ ?>

							<button type="button" class="accordion-toggle mr5 btn-default btn-sm accordion-icon link-unstyled collapsed ib" data-toggle="collapse" data-parent="#accordion" href="#adv-search"><span class="icon mdi mdi-menu"></span></button>
							<a href="{!! URL::to($panel_path.$module.'/add') !!}" class="btn-default btn-sm add-new-btn link-unstyled ib"><span class="icon mdi mdi-plus pr5 fs15"></span> Add {!! $module_identifier !!}</a>
							{{--<a href="{!! URL::to($panel_path.$module.'/add?inv_action=1') !!}" class="btn-default btn-sm add-new-btn link-unstyled ib"><span class="icon mdi mdi-plus pr5 fs15"></span> Adjust/Refund New {!! $module_identifier !!}</a>
							--}}<a href="{!! URL::to($panel_path.$module.'/add?inv_action=2') !!}" class="btn-default btn-sm add-new-btn link-unstyled ib"><span class="icon mdi mdi-plus pr5 fs15"></span> Adjust/Refund Existing {!! $module_identifier !!}</a>

						<?php } else { ?>

							<button type="button" class="accordion-toggle mr5 btn-default btn-sm accordion-icon link-unstyled collapsed ib" data-toggle="collapse" data-parent="#accordion" href="#adv-search"><span class="icon mdi mdi-menu"></span></button>
							<a href="{!! URL::to($panel_path.$module.'/add') !!}" class="btn-default btn-sm add-new-btn link-unstyled ib"><span class="icon mdi mdi-plus pr5 fs15"></span> Add {!! $module_identifier !!}</a>

							<?php }  ?>
					</div>
				</div>
			@endif
		</div>
		@include(config('panel.DIR') . 'entities/advance/advance-search')

	<section id="content" class="pn">
		<div class="panel panel-theme panel-border top mb25">
			<div class="panel-heading">
				<span class="panel-title">Listing</span>
			</div>
			<div class="panel-body pn">
				<form name="listing_form" method="post">
					<!-- End: Topbar -->
					<!-- Begin: Content -->
					<section id="content" class="pn table-layout">
						<div class="tray pn">


							<div class="panel">
								<div class="table-responsive">
									<table class="table table-hover responsive" id="mydatatable" cellspacing="0" width="100%">
										<thead>
										<tr>
											<?php
												foreach ($columns as $key => $column_field) {
													?>
											<th><?= $column_field ?></th>
											<?php }  ?>
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
		<div class="modal fade" id="orderModal" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Order Status</h4>
					</div>
					<div class="modal-body" >
						<div class="alert-message"></div>
						<div id="orderContent">
							Loading...
						</div>


					</div>
					<div class="modal-footer">
						{{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
						<button type="button" class="btn ladda-button btn-theme btn-wide mt10 order-update-btn" data-style="zoom-in" >Update</button>
					</div>
				</div>
			</div>
		</div>
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


		// data grid generation
		$('#mydatatable').DataTable().destroy();
		var dg = $("#mydatatable").DataTable({
			processing: true,
			serverSide: true,
			//paging: false,
			searching: false,
			"aaSorting": [],
			"oLanguage":{
				"sProcessing": loaderHtml(),
			},
			//bStateSave: true, // save datatable state(pagination, sort, etc) in cookie.

			ajax: {
				url: "{!! URL::to($panel_path.$module.'/ajaxListing') !!}?_token=<?php echo csrf_token(); ?>&entity_type_id=<?=$entity_data->entity_type_id?>", // ajax source
				type: "POST",
				data : function(d) {
					/*for (var attrname in dg_ajax_params) { d[attrname] = dg_ajax_params[attrname]; }
					d.search_columns = $('.search_columns').val();*/
					d.search_columns = dg_ajax_params;
					if ("checked_ids" in d.search_columns) d.checked_ids = d.search_columns['checked_ids']; delete d.search_columns['checked_ids'];
					if ("select_action" in d.search_columns) d.select_action = d.search_columns['select_action']; delete d.search_columns['select_action'];
				}
			},
			drawCallback: function (settings) {
				//check if records are greater than zero then show export button
				if($('.export_entity').length >0){
					var record_count = this.fnSettings().fnRecordsTotal();
					if(record_count>0) $('.export_entity').removeClass('hide'); else $('.export_entity').addClass('hide');
					console.log(record_count);
				}
			},
			pageLength: 10, // default record count per page
			columnDefs : [<?php
				$index = 0;
				foreach ($columns as $index_key => $column_field) { ?>
			{
				data: "<?= $index_key ?>",
				orderable: false,
				targets: <?= $index ?>
			},
				<?php $index++;
				} ?>
			],
			"createdRow": function ( row, data, index ) {
				$('td:last', row).addClass('hv-btns');
			},
		});

		// add search to datatable
		dgSearch(dg);
		// add select actions to datatable
		dgSelectActions(dg);


        // Form Skin Switcher
        $(document).on('click', '#view_popup',function() {

            $("#orderModal").modal();
             console.log($(this).data('order-id'));
            $("#orderContent").text('Loading...');
            $(".order-update-btn").attr("disabled","disabled");

            $.ajax({
                type: "GET",
                url: "<?php echo url('getOrderStatus'); ?>",
                dataType: "json",
                data: {"order_id": $(this).data('order-id'), "driver_id": ''},
                success: function (data) {
					//if(data.html){
					//onsole.log(data.data.html);
                    $('#orderModal .alert-message').html('');
                        $("#orderContent").html(data.data.html);
                    	$(".order-update-btn").removeAttr("disabled");
					//}

                    if(data.data.vehicle_id != ""){
                        driverVehicleInfo(data.data.vehicle_id);
                    }
                }
            });

        });




	});
</script>
@include(config('panel.DIR').'footer')
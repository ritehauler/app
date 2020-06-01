@include(config('panel.DIR').'header')
		<!-- Start: Content-Wrapper -->
<!-- New Design Start --->
<section id="content_wrapper" class="content dubble_side">
	<section id="content" class="table-layout animated fadeIn">

		<!-- begin: .tray-center -->
		<div class="tray tray-center p25 va-t posr">
			<!-- create new order panel -->
			@if(!empty($add_permission) && $add_permission == 1)
			@if ($entity_data->show_gallery)
			@include(config('panel.DIR').'entities.advance.add')
			@else
			@include(config('panel.DIR').'entities.advance.add_basic')
			@endif
			@endif
					<!-- recent orders table -->
			<div class="panel panel-theme panel-border top mb25">
				<div class="panel-heading">
					<span class="panel-title">Listing</span>
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

				<div class="panel-body pn">
					<form name="listing_form" method="post">
						<section id="content" class="pn table-layout">
							<div class="tray pn">
								<div class="panel">
									<div class="table-responsive">

										<table class="table table-hover responsive fs13 smallTable smallImgTable" id="mydatatable" cellspacing="0" width="100%">
											<thead>
											<tr>
												<?php  foreach ($columns as $column_field) { ?>
												<th><?=  $column_field ?></th>
												<?php } ?>
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


		</div>
		<!-- end: tray-center -->


	</section>
	<!-- Begin: Page Footer -->

	<?php if(in_array($entity_data->identifier,array('business_user','customer','driver'))){ ?>
			@include(config('panel.DIR') . 'entities/advance/advance-search-user')
	<?php }else{
	    ?>
		@include(config('panel.DIR') . 'entities/advance/advance-search')
	<?php } ?>
			<!-- End: Page Footer -->
</section>
		@include(config('panel.DIR') . 'footer_bottom')
			<!-- New Design End --->
			<div id="modal-panel" class="popup-basic bg-none mfp-with-anim mfp-hide">
				<div class="panel">

					<div class="panel-heading">
						<span class="panel-title"> View</span>
					</div>
					<div class="panel-body">
						<table class="view_popup_table">
							<?php
							if (isset($update)) {
							foreach ($update->attributes as $key => $value) {

							if(is_object($value)){
								$value = isset($value->option) ? $value->option : "";
							}
							$column_label = isset($columns["$key"]) ? $columns["$key"] : $key;
							//echo '<h1>'.$column_label. ': </h1>' ; echo $value; echo '<br>';
							?>
							<tr>
								<td><h4>{!! $column_label !!}</h4></td>
								<td><span>{!! $value !!}</span></td>
							</tr>
							<?php
							}
							}
							?>
						</table>
					</div>
				</div>
			</div>

<!-- Modal -->
<div class="modal fade" id="driverModal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Order Statistics</h4>
			</div>
			<div class="modal-body" >
				<div class="alert-message"></div>
				<div id="orderContent">
					Loading...
				</div>


			</div>
			<div class="modal-footer">
				{{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
			</div>
		</div>
	</div>
</div>


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
<script type="text/javascript" src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/holder/holder.min.js' ) !!}"></script>
<!-- Page Plugins -->
<script type="text/javascript" src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/magnific/jquery.magnific-popup.js' ) !!}"></script>


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

				var extra_params = "";

            <?php if(isset($is_other)){?>
        var extra_params = "&is_other={!! $is_other !!}";
console.log(extra_params);
        <?php }  ?>


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
							url: "{!! URL::to($panel_path.$module.'/ajaxListing') !!}?_token=<?php echo csrf_token(); ?>&entity_type_id=<?=$entity_data->entity_type_id?>"+extra_params, // ajax source
							type: "POST",
							data : function(d) {
								/*for (var attrname in dg_ajax_params) {
									d[attrname] = dg_ajax_params[attrname];
								}*/


								d.search_columns = dg_ajax_params;
								if ("checked_ids" in d.search_columns) d.checked_ids = d.search_columns['checked_ids']; delete d.search_columns['checked_ids'];
								if ("select_action" in d.search_columns) d.select_action = d.search_columns['select_action']; delete d.search_columns['select_action'];

							},
							/*"success" : function(data){
								//do stuff here
								console.log(data.recordsTotal);
							}*/
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

	});

	$(document).ready(function() {

		var modalContent = $('#modal-content');
		modalContent.on('click', '.holder-style', function(e) {
			e.preventDefault();
			modalContent.find('.holder-style').removeClass('holder-active');
			$(this).addClass('holder-active');
		});

		// Form Skin Switcher
		$(document).on('click', '#view_popup',function() {

			// Inline Admin-Form example
			$.magnificPopup.open({
				removalDelay: 500, //delay removal by X to allow out-animation,
				items: {
					src: "#modal-panel"
				},
				// overflowY: 'hidden', //
				callbacks: {
					beforeOpen: function(e) {
						var Animation = "mfp-slideDown";
						this.st.mainClass = Animation;
					}
				},
				midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
			});

		});

		// Update Content show
		$(document).on('click',".updateBtn ",function(){
			$(".updateWrap ").fadeIn(1500)
		});

		$(document).on('click',".updateBtn ",function(){
			$('html, body').animate({
				scrollTop: $(".updateWrap ").offset().top
			}, 1000);
		});

	});


	$(function () {

		$('form[name="data_form"]').submit(function(e) { //alert('sam'); return false;
			e.preventDefault();
			setFullName();
			Common.jsonValidation('<?=Request::url()?>', this,'',"<?php echo $entity_data->identifier ?>");
		});



	@if($entity_data->identifier != 'customer' && (isset($entity_data->show_gallery) && $entity_data->show_gallery == 1))


		var baseUrl = "";
		var token = "{{ Session::getToken() }}";
		Dropzone.autoDiscover = false;
		var myDropzone = new Dropzone("div#dropzoneFileUpload", {
			url: baseUrl + "{!! URL::to('/api/system/attachment/save') !!}",
			addRemoveLinks: true,
			maxFiles:1,
            thumbnailWidth: parseInt(minThumbWidth),
            thumbnailHeight: parseInt(minThumbHeight),
			dictRemoveFileConfirmation:  "Are you sure you want to remove?",
			params: {
				_token: token,
				attachment_type_id: 8,
				entity_type_id:0
			}
		});
		var numItems = 0;
		var attch_id = '';
		myDropzone.on("success", function(file,responseText) {
           $('.alert-message').html('');
			 var image_response = $.parseJSON(responseText.jsonEditor);
			 if(image_response.error == 1){
                // $('.dz-preview').remove();
                 showAlert(image_response.message);
                 this.removeFile(file);
			 }
			 else{
                 attch_id = image_response.data.attachment.attachment_id;
			 }

			//console.log($.parseJSON(responseText.jsonEditor).data.attachment.attachment_id);
			//$('.dz-complete').eq(numItems).append('<input type="checkbox" onClick="isfeatured()">');
			//numItems++;
			myDropzone.processQueue();
		});

		myDropzone.on("complete", function() {


			$('.dz-complete').append('<div class="pull-right"><input type="hidden" name="gallery_items[]" value="'+attch_id+'"></div>');
			$('.dz-remove').attr('data-attachment-id',attch_id);
			/*setTimeout(function(){
			 $('.dz-complete').eq(numItems).append('<input type="radio" name="gallery_featured_item" value="'+attch_id+'">');

			 numItems++;

			 }, 3000);*/


		});


		myDropzone.on("removedfile", function(file) {
			// console.log(file);
			//console.log(file._removeLink.attributes['data-attachment-id']);
			var file_id = '';
			if($('#dropzoneFileUpload').find('a.dz-remove').length > 0){

                var attach_attr = file._removeLink.attributes['data-attachment-id'];
                var file_id = attach_attr.nodeValue;
			}

			if(file_id != ""){
				$.ajax({
					url: "<?php echo url('/api/system/attachment/delete'); ?>",
					dataType: "json",
					data: {"attachment_id": file_id},
					beforeSend: function () {
					}
				}).done(function (data) {

				});
			}

		});



		$('#removeGallery').click(function(){
			alert($('#removeGallery').val());
		});


				@endif

		var entity_type_id = "{!! $entity_data->entity_type_id !!}";

		<?php if($entity_data->identifier == 'business_user'){ ?>

		if($('.password_field').length>0){
			$('.password_field').addClass('hide');
		}

		if( $('#role_id').length > 0 && entity_type_id != ""){

			$('#parent_role_id').empty();
			var field_title = $(".parent_role_id_field label.field-label").data("original-title");
			$('#parent_role_id').append('<option value="">-- Select '+field_title+' --</option>');
			//$('#role_id').append('<option value="">Select '+$('.role_id_field label:first').text()+'</option>');
			$.ajax({
				url: "<?php echo url('getRoleOptions'); ?>",
				dataType: "json",
				data: {"entity_type_id": entity_type_id , "is_group":1},
				beforeSend: function () {
				}
			}).done(function (data) {
				$(data).each(function (index, ele) {
					$('#parent_role_id').append('<option value="' + ele.role_id + '">' + ele.title + '</option>');
				});
			});


			$('#role_id').empty();
			var field_title = $(".role_id_field label.field-label").data("original-title");
			$('#role_id').append('<option value="">-- Select '+field_title+' --</option>');
			//$('#role_id').append('<option value="">Select '+$('.role_id_field label:first').text()+'</option>');
			/*	$.ajax({
			 url: "<?php //echo url('getRoleOptions'); ?>",
			 dataType: "json",
			 data: {"entity_type_id": entity_type_id},
			 beforeSend: function () {
			 // $('#' + chosen_id).empty();
			 }
			 }).done(function (data) {
			 $(data).each(function (index, ele) {
			 $('#role_id').append('<option value="' + ele.role_id + '">' + ele.title + '</option>');
			 });
			 });*/



			//on change user type get departments
			$("#parent_role_id").on("change",function(){
				console.log($(this).val())
				if( $(this).val() != ""){
					$('#role_id').empty();
					var field_title = $(".role_id_field label.field-label").data("original-title");
					$('#role_id').append('<option value="">-- Select '+field_title+' --</option>');
					$.ajax({
						url: "<?php echo url('getRoleOptions'); ?>",
						dataType: "json",
						data: {"entity_type_id": entity_type_id , "parent_id": $(this).val()},
						beforeSend: function () {
						}
					}).done(function (data) {
						$(data).each(function (index, ele) {
							$('#role_id').append('<option value="' + ele.role_id + '">' + ele.title + '</option>');
						});
					});
				}
			});

		}

		<?php } ?>

	@if($entity_data->identifier == 'custom_notification')

		$(document).on('change','select[name="notify_to"]',function(){
			var city_ids = ''
			var data = {id: '', text: ''};
			var newOption = new Option(data.text, data.id, false, false);
			$('.target_user_entity_id_field').find('select').html(newOption).trigger('change');

			var notify_to = $(this).val();
			if(notify_to == "all_customer" || notify_to == "all_driver"){
				$('.target_user_entity_id_field').hide();
			}else{
				$('.target_user_entity_id_field').show();
			}

			if(notify_to == "all_driver" || notify_to == "driver" || notify_to == "all_customer"){
				$('.target_city_field').hide()
			}else{
				$('.target_city_field').show()
			}
			//get entity record
			$.ajax({
				type:'POST',
				data: {notify_to:notify_to ,city_ids:city_ids, _token:'{{ csrf_token() }}'},
				url: '{{ url('getEntity') }}',
				success : function(data){
					var obj = JSON.parse(data);
					var target_entity_user_html = '';
					if(obj.length > 0){
						target_entity_user_html += '<option value=""></option>';
						obj.forEach(function(value,index){
							target_entity_user_html += '<option value="'+ value.entity_id +'">'+  value.full_name+'</option>';
							$('#target_user_entity_type_id').val(value.entity_type_id);
						})
						$('.target_user_entity_id_field').find('select').html(target_entity_user_html);
					}
				}
			});
		});

		$(document).on('change','#target_city_select2',function(){
			var notify_to = $('select[name="notify_to"]').val();
			if(notify_to == 'customer')
			{
				var city_ids = $(this).val();
				$.ajax({
					type:'POST',
					data: {notify_to:notify_to ,city_ids:city_ids, _token:'{{ csrf_token() }}'},
					url: '{{ url('getEntity') }}',
					success : function(data){
						var obj = JSON.parse(data);
						var target_entity_user_html = '';
						if(obj.length > 0){
							target_entity_user_html += '<option value=""></option>';
							obj.forEach(function(value,index){
								target_entity_user_html += '<option value="'+ value.entity_id +'">'+  value.full_name+'</option>';
								$('#target_user_entity_type_id').val(value.entity_type_id);
							})
							$('.target_user_entity_id_field').find('select').html(target_entity_user_html);
						}
					}
				});
			}
		})

	@endif


	<?php if($entity_data->identifier == "wallet_transaction"){ ?>

		$('.debit_field').hide();

		$("#transaction_type").on("change",function(){
			if($(this).val() == 'debit'){
				$('.credit_field').hide();
				$('.debit_field').show();
			}else{
				$('.credit_field').show();
				$('.debit_field').hide();
			}
		});
		<?php } ?>


        <?php if($entity_data->identifier == "city"){ ?>

        //get product list respective to product type
        $( document ).on( "change", "#state_id", function() {
            var id = $(this).val();

            if(id != ""){

                $('#city_id').empty();

                $.ajax({
                    url: "<?php echo url('getCityByState'); ?>",
                    dataType: "json",
                    data: {"state_id": $(this).val()},
                    beforeSend: function () {
                    }
                }).done(function (data) {
                    //   console.log( data.data);
                    var products = data.data;
                    if(products.length >0){
                        $('#city_id').append("<option value=''>-- Select City --</option>");

                        $.each(products,function(k,v){
                            // console.log(v.entity_id);
                            $('#city_id').append("<option value='"+v.city_id+"'>"+v.name+"</option>");
                            // $('.blah').val(key); // if you want it to be automatically selected
                            $('#city_id').trigger("chosen:updated");
                        })

                    }

                });
            }
        });

        <?php } ?>

		$(document).on('click','.move_item',function(){
			var item_id = $(this).attr('data-order-id');
			var msg 	  = confirm('Are you sure you want to continue?');
			if(msg){
				$.ajax({
					type:"POST",
					url:"{{ Request::url().'/updateOtherItem' }}",
					data:{item_id:item_id, _token:'{{ csrf_token() }}' },
					success: function(data){
						location.reload(true);
					}
				});
			}else{
				return false;
			}
		})

		@if($entity_data->identifier == "truck")
			$('select[name="truck_class_id"]').on('change',function(){
				var truck_class_id = $(this).val();
				if(truck_class_id != ''){
					$.ajax({
						type:"POST",
						url:"{{ url('getTruckClass') }}",
						data:{truck_class_id:truck_class_id, _token:'{{ csrf_token() }}' },
						success: function(data){
							var obj = JSON.parse(data);
							if(obj.length > 0){

								var truck_class_html = '<p>Truck Class : '+ obj[0].attributes.truck_class.option +'</p>';
								truck_class_html += '<p>Min Weight : '+ obj[0].attributes.min_weight +'</p>';
								truck_class_html += '<p>Max Weight : '+ obj[0].attributes.max_weight +'</p>';
								$('.ajax_content').html(truck_class_html);
								$('#min_weight').val(obj[0].attributes.min_weight);
                                $('#max_weight').val(obj[0].attributes.max_weight);

							}
						}
					});
				}
			})
		@endif

        <?php if(in_array($entity_data->identifier,array('driver','customer'))){ ?>
        // Form Skin Switcher
        $(document).on('click', '.view_stats',function() {

            $("#driverModal").modal();
            $("#orderContent").text('Loading...');
            $(".order-update-btn").attr("disabled","disabled");

            $.ajax({
                type: "GET",
                url: "{{ url('driver/stats') }}",
                dataType: "json",
                data: { "entity_id": $(this).data('driver-id'),"identifier" : "{{ $entity_data->identifier }}"},
                success: function (data) {
                    //if(data.html){
                    //onsole.log(data.data.html);

                    $("#orderContent").html(data.data.html);
                    //}
                }
            });

        });
        <?php } ?>
    });
</script>

@include(config('panel.DIR').'footer')
@include(config('panel.DIR').'header')
<!-- Start: Content-Wrapper -->
<?php 
    $admin_statuses = config("constants.ADMIN_STATUSES");
?>
<section id="content_wrapper" class="content">

  <div id="seaction-header">
    @include(config('panel.DIR').'flash_message')
    <div class="adv-search">
      <div class="topbar-left">
        <div class="table-tools">
          @if($perm_del)
          <a class="select_action" title="delete">
            <button type="button" class="btn-default btn-sm link-unstyled ib" ><span class="icon mdi mdi-delete pr5 fs15"></span> Delete</button>
          </a>
          @endif
          <button type="button" class="btn-default btn-sm link-unstyled ib" href="#" data-toggle="dropdown"><span class="icon mdi mdi-chevron-down fs15"></span></button>
          <ul class="dropdown-menu list-group dropdown-persist w200" role="menu">
           @foreach($admin_statuses as $key=>$val)
              <li class="list-group-item"> <a class="select_action" title="{!! $val !!}">{!! $val !!}</a> </li>
           @endforeach
          </ul>
        </div>
      </div>
      <div class="topbar-right text-right">
        <div class="pull-right adv-search-bar">
          <button type="button" class="accordion-toggle mr5 btn-default btn-sm accordion-icon link-unstyled collapsed ib" data-toggle="collapse" data-parent="#accordion" href="#adv-search"><span class="icon mdi mdi-menu"></span></button>
          @if($perm_add)
          <a href="{!! URL::to(DIR_ADMIN.$module.'/add') !!}" class="btn-default btn-sm add-new-btn link-unstyled ib"><span class="icon mdi mdi-plus pr5 fs15"></span> Add {!! $p_title !!}</a>
          @endif
        </div>
      </div>
    </div>
    <div id="adv-search" class="panel-collapse collapse" style="height: auto;">
      <div class="panel-body bg-light lighter br-h-n br-t-n search-filters">
        <div class="row">
          <div class="col-md-8">
            <div class="row">
              <div class="col-md-6 multiSelect mb20">
                <label>Filter Column</label>
                <select class="search_columns" id="multiselect2" multiple="multiple" >
                  <option value="admin_group">Admin Group</option>
                  <option value="username">User</option>
                  <option value="email">Email</option>
                  <option value="status">Status</option>
                </select>
				<i class="arrow"></i>
              </div>
              <div class="col-md-6 mb5">
                <label>Status</label>
                <input type="text" name="keyword" class="form-control" placeholder="Keyword">
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="mt25 clearfix">
              <a type="button" id="grid_reset" class="btn btn-default btn-md col-sm-6 mr5 btn-clear">Clear</a>
              <a type="button" id="grid_search" class="btn btn-theme btn-md col-sm-6 btn-search">Search</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <form name="listing_form" method="post">
  <!-- End: Topbar -->
  <!-- Begin: Content -->
  <section id="content" class="pn">
    <div class="panel panel-visible" >
      <div class="table-responsive">
        <table class="table table-hover" id="mydatatable" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th width="5%" class="nosort">
                 <div class="checkbox-t">
                     <input type="checkbox" id="check_all" name="check_all" />
                     <label for="check_all"></label>
                 </div>
               </th>
              <th>Admin Group</th>
              <th>User</th>
              <th>Email</th>
              <th>Status</th>
              <th>Created</th>
              <th>Options</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </section>
  <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
  </form>
  <!-- End: Content -->
  <!-- Begin: Page Footer -->
   
  <!-- End: Page Footer -->
</section>
<!-- Detailed View Popup -->
<!-- End: Content-Wrapper -->
<!-- Required Plugin CSS -->
<link rel="stylesheet" type="text/css" href="{!! URL::to(config('constants.ADMIN_ASSET_URL') . 'vendor/plugins/datepicker/css/bootstrap-datetimepicker.css' ) !!}">
<link rel="stylesheet" type="text/css" href="{!! URL::to(config('constants.ADMIN_ASSET_URL') . 'vendor/plugins/daterange/daterangepicker.css' ) !!}">
<!-- Page Plugins via CDN -->
<script src="{!! URL::to(config('constants.ADMIN_ASSET_URL') . 'vendor/plugins/moment/moment.min.js' ) !!}"></script>
<!-- Datatables -->
<script src="{!! URL::to(config('constants.ADMIN_ASSET_URL') . 'vendor/plugins/datatables/media/js/datatables.min.js' ) !!}"></script>
<!-- ckeditor -->
<script src="{!! URL::to(config('constants.ADMIN_ASSET_URL') . 'vendor/plugins/ckeditor/ckeditor.js' ) !!}"></script>
<!-- Page Plugins -->
<script src="{!! URL::to(config('constants.ADMIN_ASSET_URL') . 'vendor/plugins/xeditable/js/bootstrap-editable.js' ) !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_ASSET_URL') . 'vendor/plugins/daterange/daterangepicker.js' ) !!}"></script>
<script src="{!! URL::to(config('constants.ADMIN_ASSET_URL') . 'vendor/plugins/datepicker/js/bootstrap-datetimepicker.js' ) !!}"></script>
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
		if($('a').hasClass('admin-status')){
			$('.admin-status').editable({
				showbuttons: false,
				source: [
					{value: 1, text: 'Active'},
					{value: 2, text: 'Inactive'},
					
				]
			});
		}
		
		// No Sorting
		$('#datatable2').DataTable({
			"bLengthChange":   false,
			"bInfo": false,
			"searching": false,
			"bPaginate": false, 
			"order": [],	
			"columnDefs": [ {
				"targets"  : 'nosort',
				  "orderable": false,
			} ]
		});

		// data grid generation
        	$('#mydatatable').DataTable().destroy();
        	var dg = $("#mydatatable").DataTable({
        		processing: true,
        		serverSide: true,
        		//paging: false,
        		searching: false,
        		//bStateSave: true, // save datatable state(pagination, sort, etc) in cookie.
        		ajax: {
        			url: "{!! $module !!}/ajax/listing?_token=<?php echo csrf_token(); ?>", // ajax source
        			type: "POST",
        			data : function(d) {
        				for (var attrname in dg_ajax_params) { d[attrname] = dg_ajax_params[attrname]; }
        				d.search_columns = $('.search_columns').val();

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
        				targets: [0]
        			}, {
        				data: "admin_group",
        				orderable: true,
        				targets: [1]
        			}, {
        				data: "a|username",
        				orderable: true,
        				targets: [2]
        			}, {
        				data: "a|email",
        				orderable: true,
        				targets: [3]
        			},{
        				data: "a|status",
        				orderable: true,
        				targets: [4]
        			}, {
        				data: "a|created_at",
        				orderable: true,
        				targets: [5]
        			}, {
        				data: "options",
        				orderable: false,
        				targets: [6]
        			}
        		],
        		order: [
        			[5, "desc"]
        		]
        	});
        	// add search to datatable
        	dgSearch(dg);
        	// add select actions to datatable
        	dgSelectActions(dg);
	});
</script>
@include(config('panel.DIR').'footer')
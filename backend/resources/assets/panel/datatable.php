<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","Admin Role",$buffer);
    echo $buffer;
?>

	
	
<!-- Start: Content-Wrapper -->
    <section id="content_wrapper">
    <!-- Start: Topbar-Dropdown -->
    <div id="topbar-dropmenu">
        <div class="topbar-menu row">
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="glyphicon glyphicon-inbox"></span>
              <span class="metro-title">Messages</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="glyphicon glyphicon-user"></span>
              <span class="metro-title">Users</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="glyphicon glyphicon-headphones"></span>
              <span class="metro-title">Support</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="fa fa-gears"></span>
              <span class="metro-title">Settings</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="glyphicon glyphicon-facetime-video"></span>
              <span class="metro-title">Videos</span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-2">
            <a href="#" class="metro-tile">
              <span class="glyphicon glyphicon-picture"></span>
              <span class="metro-title">Pictures</span>
            </a>
          </div>
        </div>
      </div>
    <!-- End: Topbar-Dropdown -->

    <!-- Start: Topbar -->
    <div id="seaction-header">
        <div class="adv-search">
            <div class="topbar-left">
                <div class="sec-title">
                    <span>Showing</span>
                    <span class="p-list-count op6">(12)</span>
                </div>
            </div>
            <div class="page-nav-tab tabnav-holder">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#profile" data-toggle="tab" aria-expanded="false">Users</a></li>
                    <li><a href="#profile" data-toggle="tab" aria-expanded="false">Roles</a></li>
                    <li><a href="#profile" data-toggle="tab" aria-expanded="false">Widgets</a></li>
                </ul>
            </div>
            <div class="topbar-right">
                <div class="pull-right">
                    <button type="button" class="accordion-toggle btn-default btn-sm accordion-icon link-unstyled collapsed ib" data-toggle="collapse" data-parent="#accordion" href="#adv-search"><span class="icon mdi mdi-menu"></span></button>
                    <button type="button" class="btn-default btn-sm accordion-icon link-unstyled ib" href="#"><span class="icon mdi mdi-plus pr5 fs15"></span> Add Admin Role</button>
                </div>
            </div>
        </div>    
        <div id="adv-search" class="panel-collapse collapse" style="height: auto;">
            <div class="panel-body bg-light lighter br-h-n br-t-n search-filters">
                <div class="row mb15">
                    <div class="col-md-4">
                        <label>Organization Name</label>
                        <input type="text" id="" class="form-control" placeholder="Name">
                    </div>
                    <div class="col-md-4">
                        <label for="daterangepicker1">Expiry</label>
                        <input type="text" class="form-control pull-right" name="daterange" id="daterangepicker1">
                    </div>
                    <div class="col-md-4">
                        <label for="datetimepicker1">Purchase Date</label>
                        <input type="text" class="form-control" id="datetimepicker1">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 multiSelect">
                                <label>Organization Name</label>
                                <select id="multiselect2" multiple="multiple" >
                                    <option value="cheese">Cheese</option>
                                    <option value="tomatoes">Tomatoes</option>
                                    <option value="mozarella">Mozzarella</option>
                                    <option value="mushrooms">Mushrooms</option>
                                    <option value="pepperoni">Pepperoni</option>
                                    <option value="onions">Onions</option>
                                </select>
                            </div>
                            <div class="col-md-6 multiSelect">
                                <label>Status</label>
                                <select id="multiselect3" multiple="multiple" >
                                    <option value="approved">Approved</option>
                                    <option value="disapproved">Disapproved</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt25 clearfix">
                            <button type="button" class="btn btn-default btn-md col-sm-6 mr5 btn-clear">Clear</button>
                            <button type="button" class="btn btn-theme btn-md col-sm-6 btn-search">Search</button>
                        </div>	
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End: Topbar -->

    <!-- Begin: Content -->
    <section id="content" class="pn">
		<div class="panel panel-visible">
			<div class="panel-heading">
				<div class="panel-title hidden-xs">
					<span class="glyphicon glyphicon-tasks"></span>
					Search Bar Filtering
				</div>
			</div>
			<table class="datatable" id="table1" data-datatable>
				<thead>
					<!-- For column sorting -->
					<tr class="text-nowrap">
						<th>Table Heading <a href="#" class="datatable-sort"></a></th>
						<th>Table Heading <a href="#" class="datatable-sort"></a></th>
						<th>Table Heading <a href="#" class="datatable-sort"></a></th>
					</tr>
				</thead>
				<tbody>
					...
				</tbody>
			</table>

			<!-- To display table/page info -->
			<div data-datatable-info></div>

			<!-- Previous page button -->
			<a href="#" data-datatable-previous="{table:'#table1'}">Previous</a>

			<!-- Pagination -->
			<div class="datatable-pagination"></div>

			<!-- Next page button -->
			<a href="#" data-datatable-next="{table:'#table1'}">Next</a>       
		</div>
    </section>
    <!-- End: Content -->

    <!-- Begin: Page Footer -->
    <footer id="content-footer">
    <div class="row">
      <div class="col-md-6">
        <span class="footer-legal">© 2017 Cubix Panel</span>
      </div>
      <div class="col-md-6 text-right">
        <span class="footer-meta">10GB of <b>250GB</b> Free</span>
        <a href="#content" class="footer-return-top">
          <span class="fa fa-arrow-up"></span>
        </a>
      </div>
    </div>
    </footer>
    <!-- End: Page Footer -->

    </section>
    <!-- End: Content-Wrapper -->

<?php include_once("footer.php"); ?>


<!-- Required Plugin CSS -->
<link rel="stylesheet" type="text/css" href="vendor/plugins/datepicker/css/bootstrap-datetimepicker.css">
<link rel="stylesheet" type="text/css" href="vendor/plugins/daterange/daterangepicker.css">

<!-- Page Plugins via CDN -->
<script src="vendor/plugins/moment/moment.min.js"></script>

<!-- Datatables -->
<script src="./vendor/plugins/datatables/media/js/datatables.min.js"></script>

<!-- Datatables Addons -->
<script src="./vendor/plugins/datatables/media/js/dataTables.bootstrap.js"></script>
<script src="./vendor/plugins/datatables/media/js/jquery.dataTables.js"></script>

<!-- Page Plugins -->
<script src="vendor/plugins/daterange/daterangepicker.js"></script>
<script src="vendor/plugins/datepicker/js/bootstrap-datetimepicker.js"></script>


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
        
		/* $('#datatable2').dataTable({
            // dom: "Bfrtip",
            // dom: "rtip",
            dom: '<"top"fl>rt<"bottom"ip>'
            // select: true
        }); */
		
		// No Sorting
		var table = $('#datatable2').DataTable({
		   'aoColumnDefs': [{
				'bSortable': false,
				'aTargets': ['nosort']
			}]
		});
		
	});
</script>
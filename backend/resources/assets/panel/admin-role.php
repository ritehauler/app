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
    <section id="content_wrapper" class="content">
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
            <div class="topbar-right">
                <div class="pull-right">
					<form class="admin-form ib va-m mr5 search">
						<div class="smart-widget sm-right">
							<label class="field mbn">
							  <input type="text" name="search" id="jquery-search-sample" class="input-sm mnw200" placeholder="Search">
							</label>
							<button type="submit" class="button btn btn-sm h-30 ph10 lh20 ">
							  <i class="fa fa-search"></i>
							</button>
						</div>
					</form>   
                    <button type="button" class="accordion-toggle btn-default mr5 btn-sm accordion-icon link-unstyled collapsed ib" data-toggle="collapse" data-parent="#accordion" href="#adv-search"><span class="icon mdi mdi-menu"></span></button>
                    <button type="button" class="btn-default btn-sm add-new-btn link-unstyled ib" href="add-new-admin-role.php"><span class="icon mdi mdi-plus pr5 fs15"></span> Add Admin Role</button>
                </div>
            </div>
        </div>    
        <div id="adv-search" class="panel-collapse collapse" style="height: auto;">
            <div class="panel-body bg-light lighter br-h-n br-t-n search-filters"> 
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
                            <div class="col-md-6">
                                <label>Status</label>
                                <input type="text" id="" class="form-control" placeholder="Name">
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
			<table class="table table-hover" id="datatable2" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="5%" class="nosort">Name</th>
						<th width="40%"></th>
						<th width="20%">Created Date</th>
						<th width="20%">Updated Date</th>
						<th width="15%" class="nosort"></th>
						<th width="5%" class="text-right nosort"><button type="button" class="btn-default btn-sm accordion-icon link-unstyled" href="#"><span class="icon mdi mdi-refresh-alt fs15"></span></button></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<span class="ticon"><i class="icon mdi mdi-accounts-list"></i></span>
						</td>
						<td>
							Master Admin Master Admin Master Admin
						</td>
						<td>
							<span class="cell-tittle">April 22, 2016</span>
							<span class="cell-description">14:45</span>
						</td>
						<td>
							<span class="cell-tittle">April 22, 2016</span>
							<span class="cell-description">14:45</span>
						</td>
						<td class="hv-btns text-right">
							<button type="button" class="btn btn-default btn-sm" href="#" title="View" data-toggle="modal" data-target=".view-popup"><span class="icon mdi mdi-eye fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
						</td>
						<td style="width:5%;" class="text-right">
							<span class="mdi mdi-chevron-right fs20"></span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="ticon"><i class="icon mdi mdi-accounts-list"></i></span>
						</td>
						<td>
							Master Admin 
						</td>
						<td>
							<span class="cell-tittle">April 22, 2016</span>
							<span class="cell-description">14:45</span>
						</td>
						<td>
							<span class="cell-tittle">April 22, 2016</span>
							<span class="cell-description">14:45</span>
						</td>
						<td class="hv-btns text-right">
							<button type="button" class="btn btn-default btn-sm" href="#" title="View" data-toggle="modal" data-target="#"><span class="icon mdi mdi-eye fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
						</td>
						<td style="width:5%;" class="text-right">
							<span class="mdi mdi-chevron-right fs20"></span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="ticon"><i class="icon mdi mdi-accounts-list"></i></span>
						</td>
						<td>
							Master Admin Master Admin Master Admin
						</td>
						<td>
							<span class="cell-tittle">April 22, 2016</span>
							<span class="cell-description">14:45</span>
						</td>
						<td>
							<span class="cell-tittle">April 22, 2016</span>
							<span class="cell-description">14:45</span>
						</td>
						<td class="hv-btns text-right">
							<button type="button" class="btn btn-default btn-sm" href="#" title="View" data-toggle="modal" data-target="#"><span class="icon mdi mdi-eye fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
						</td>
						<td style="width:5%;" class="text-right">
							<span class="mdi mdi-chevron-right fs20"></span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="ticon"><i class="icon mdi mdi-accounts-list"></i></span>
						</td>
						<td>
							Master Admin Master Admin
						<td>
							<span class="cell-tittle">April 22, 2016</span>
							<span class="cell-description">14:45</span>
						</td>
						<td>
							<span class="cell-tittle">April 22, 2016</span>
							<span class="cell-description">14:45</span>
						</td>
						<td class="hv-btns text-right">
							<button type="button" class="btn btn-default btn-sm" href="#" title="View" data-toggle="modal" data-target="#"><span class="icon mdi mdi-eye fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
						</td>
						<td style="width:5%;" class="text-right">
							<span class="mdi mdi-chevron-right fs20"></span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="ticon"><i class="icon mdi mdi-accounts-list"></i></span>
						</td>
						<td>
							Master Admin
						</td>
						<td>
							<span class="cell-tittle">April 22, 2016</span>
							<span class="cell-description">14:45</span>
						</td>
						<td>
							<span class="cell-tittle">April 22, 2016</span>
							<span class="cell-description">14:45</span>
						</td>
						<td class="hv-btns text-right">
							<button type="button" class="btn btn-default btn-sm" href="#" title="View" data-toggle="modal" data-target="#"><span class="icon mdi mdi-eye fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
							<button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
						</td>
						<td style="width:5%;" class="text-right">
							<span class="mdi mdi-chevron-right fs20"></span>
						</td>
					</tr>
				</tbody>
			</table>             
		</div>
    </section>
    <!-- End: Content -->

    <!-- Begin: Page Footer -->
    <footer id="content-footer">
        <div class="row">
          <div class="col-md-12 text-center">
            <span class="footer-legal">Cubix Panel 2.0.1</span>
          </div>
        </div>
    </footer>
    <!-- End: Page Footer -->

    </section>
    <!-- End: Content-Wrapper -->
	
	<div class="modal fade view-popup">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h4 class="modal-title">Detailed View</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body detail-view">
			<table width="100%" >
				<tr>
					<th width="30%">Name</th>
					<td width="70%">Master Admin Master Admin Master Admin</td>
				</tr>
				<tr>
					<th>Created Date</th>
					<td>
						<span>April 22, 2016</span>
						<span class="cell-description">14:45</span>
					</td>
				</tr>
				<tr>
					<th>Updated Date</th>
					<td>
						<span>April 22, 2016</span>
						<span class="cell-description">14:45</span>
					</td>
				</tr>
			</table>
		  </div>
		</div>
	  </div>
	</div>

<?php include_once("footer.php"); ?>


<!-- Required Plugin CSS -->
<link rel="stylesheet" type="text/css" href="vendor/plugins/datepicker/css/bootstrap-datetimepicker.css">
<link rel="stylesheet" type="text/css" href="vendor/plugins/daterange/daterangepicker.css">

<!-- Page Plugins via CDN -->
<script src="vendor/plugins/moment/moment.min.js"></script>

<!-- Datatables -->
<script src="./vendor/plugins/datatables/media/js/datatables.min.js"></script>
<script src="./vendor/plugins/datatables/ellipsis.js"></script>

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
		
		
        
		// Ellipsis Tool tip
		/* $('.text-ellipsis').each(function(){
			  var lengthText = 30;
			  var text = $(this).text();
			  if(text.length > 30){
				  var shortText = $.trim(text).substring(0, lengthText).split(" ").slice(0, -1).join(" ") + "...";
				  $(this).prop("title", text);
				  $(this).text(shortText);
				  $(this).tooltip();
			  }	  
		}); */
		
		// Page Load Ajax
		$('button.add-new-btn').on('click', function(e){
			e.preventDefault();
			var pageRef = $(this).attr('href');
			callPage(pageRef)

		});
		function callPage(pageRefInput){
			// Using the core $.ajax() method
			$.ajax({
				url: pageRefInput,
				type: "GET",
				dataType : 'text',
				success: function( response ) {
				  console.log('the page was loaded', response);
				  $('.content').html(response);
				},
			 
				error: function( error ) {
				  console.log('the page was NOT loaded', error);
				},
			 
				complete: function( xhr, status ) {
				  console.log("The request is complete!");
				}
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
				"targets": 1,
                "render": $.fn.dataTable.render.ellipsis( 30 )
			},
			{
				"targets"  : 'nosort',
				  "orderable": false,
			}]
		});
        
        $('.ellipsis').tooltip()
        
	});
</script>
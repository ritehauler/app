<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","Email Templates",$buffer);
    echo $buffer;
?>
	
<!-- Start: Content-Wrapper -->
    <section id="content_wrapper" class="collapse-sidebar">
        
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
            <div id="topbar" >
                <div class="topbar-left">
                    <div class="sec-title">
                        <span>Showing</span>
                        <span class="p-list-count op6">(12)</span>
                    </div>
                </div>
                <div class="topbar-right">
					<div class="pull-right">
						<form class="admin-form ib va-m search">
							<div class="smart-widget sm-right">
								<label class="field mbn">
								  <input type="text" name="search" id="jquery-search-sample" class="input-sm mnw200" placeholder="Search">
								</label>
								<button type="submit" class="button btn btn-sm h-30 ph10 lh20 ">
								  <i class="fa fa-search"></i>
								</button>
							</div>
						</form>
					</div>
                </div>
            </div>
        </div>    
        <!-- End: Topbar -->
	  
        <!-- Begin: Content -->
        <section id="content" class="pn table-layout">
            <div class="tray tray-center pn">
                <div class="panel">
                    <table class="table table-hover" id="datatable2" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="nosort" style="width:5%;"></th>
                                <th class="nosort" style="width:5%;">Subject</th>
                                <th style="width:30%;"></th>
                                <th style="width:30%;">Key</th>
                                <th style="width:15%;">Created Date</th>
                                <th class="nosort"  style="width:12%;"></th>
                                <th class="text-right nosort"><button type="button" class="btn-default btn-sm accordion-icon link-unstyled" href="#"><span class="icon mdi mdi-refresh-alt fs15"></span></button></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>           
                                <td><span data-toggle="tooltip" title="Template for Admin Password Recovery email confirmation"><i class="icon mdi mdi-help mt2 fs18 text-system"></i></span></td>
                                <td>
									<span class="ticon"><i class="icon mdi mdi-email mt2"></i></span>
								</td>
								<td>
                                    Password Confirmation Password Confirmation 
                                </td>
                                <td>
                                    admin_forgot_password_confirmation  
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
                                <td><span data-toggle="tooltip" title="Template for new Admin accounts that are created"><i class="icon mdi mdi-help mt2 fs18 text-system"></i></span></td>
                                 <td>
									<span class="ticon"><i class="icon mdi mdi-email mt2"></i></span>
								</td>
								<td>
                                    Welcome to [APP_NAME]
                                </td>
                                <td>
                                    admin_new_account
                                </td>
                                <td>
                                    <span class="cell-tittle">April 22, 2016</span>
                                    <span class="cell-description">14:45</span>
                                </td>
                                <td class="hv-btns text-right">
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
                                </td>
                                <td style="width:5%;" class="text-right">
                                    <span class="mdi mdi-chevron-right fs20"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span data-toggle="tooltip" title="Template for sending new password after successfull Admin email confirmation"><i class="icon mdi mdi-help mt2 fs18 text-system"></i></span></td>
                                 <td>
									<span class="ticon"><i class="icon mdi mdi-email mt2"></i></span>
								</td>
								<td>
                                    Your new Password
                                </td>
                                <td>
                                    admin_new_password
                                </td>
                                <td>
                                    <span class="cell-tittle">April 22, 2016</span>
                                    <span class="cell-description">14:45</span>
                                </td>
                                <td class="hv-btns text-right">
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
                                </td>
                                <td style="width:5%;" class="text-right">
                                    <span class="mdi mdi-chevron-right fs20"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span data-toggle="tooltip" title="Template for sending email when admin changes password"><i class="icon mdi mdi-help mt2 fs18 text-system"></i></span></td>
                                 <td>
									<span class="ticon"><i class="icon mdi mdi-email mt2"></i></span>
								</td>
								<td>
                                    Password Changed Successfully
                                </td>
                                <td>
                                    admin_password_change_self
                                </td>
                                <td>
                                    <span class="cell-tittle">April 22, 2016</span>
                                    <span class="cell-description">14:45</span>
                                </td>
                                <td class="hv-btns text-right">
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
                                </td>
                                <td style="width:5%;" class="text-right">
                                    <span class="mdi mdi-chevron-right fs20"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span data-toggle="tooltip" title="Template for sending new password to other admins when master admin resets password for them"><i class="icon mdi mdi-help mt2 fs18 text-system"></i></span></td>
								<td>
									<span class="ticon"><i class="icon mdi mdi-email mt2"></i></span>
								</td>
  							    <td>
                                    [APP_NAME] new Password
                                </td>
                                <td>
                                    admin_reset_password
                                </td>
                                <td>
                                    <span class="cell-tittle">April 22, 2016</span>
                                    <span class="cell-description">14:45</span>
                                </td>
                                <td class="hv-btns text-right">
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
                                </td>
                                <td style="width:5%;" class="text-right">
                                    <span class="mdi mdi-chevron-right fs20"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span data-toggle="tooltip" title="Template for sending email when any changes made to API user account"><i class="icon mdi mdi-help mt2 fs18 text-system"></i></span></td>
							    <td>
									<span class="ticon"><i class="icon mdi mdi-email mt2"></i></span>
								</td>
								<td>
                                    API account updated
                                </td>
                                <td>
                                    api_user_account_changes
                                </td>
                                <td>
                                    <span class="cell-tittle">April 22, 2016</span>
                                    <span class="cell-description">14:45</span>
                                </td>
                                <td class="hv-btns text-right">
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
                                </td>
                                <td style="width:5%;" class="text-right">
                                    <span class="mdi mdi-chevron-right fs20"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span data-toggle="tooltip" title="Template for new API user accounts that are created"><i class="icon mdi mdi-help mt2 fs18 text-system"></i></span></td>
							    <td>
									<span class="ticon"><i class="icon mdi mdi-email mt2"></i></span>
								</td>
								<td>
                                    Welcome to [APP_NAME] API
                                </td>
                                <td>
                                    api_user_new_account
                                </td>
                                <td>
                                    <span class="cell-tittle">April 22, 2016</span>
                                    <span class="cell-description">14:45</span>
                                </td>
                                <td class="hv-btns text-right">
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
                                </td>
                                <td style="width:5%;" class="text-right">
                                    <span class="mdi mdi-chevron-right fs20"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span data-toggle="tooltip" title="Template for sending new password to API users generated by Admin"><i class="icon mdi mdi-help mt2 fs18 text-system"></i></span></td>
							    <td>
									<span class="ticon"><i class="icon mdi mdi-email mt2"></i></span>
								</td>
								<td>
                                    Your new Password
                                </td>
                                <td>
                                    api_user_new_password
                                </td>
                                <td>
                                    <span class="cell-tittle">April 22, 2016</span>
                                    <span class="cell-description">14:45</span>
                                </td>
                                <td class="hv-btns text-right">
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
                                </td>
                                <td style="width:5%;" class="text-right">
                                    <span class="mdi mdi-chevron-right fs20"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span data-toggle="tooltip" title="Template to Welcome new User Signed Up from Facebook"><i class="icon mdi mdi-help mt2 fs18 text-system"></i></span></td>
							    <td>
									<span class="ticon"><i class="icon mdi mdi-email mt2"></i></span>
								</td>
								<td>
                                    Welcome to [APP_NAME]
                                </td>
                                <td>
                                    user_facebook_signup_welcome
                                </td>
                                <td>
                                    <span class="cell-tittle">April 22, 2016</span>
                                    <span class="cell-description">14:45</span>
                                </td>
                                <td class="hv-btns text-right">
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit"><span class="icon mdi mdi-edit fs13"></span></button>
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Delete"><span class="icon mdi mdi-delete fs13"></span></button>
                                </td>
                                <td style="width:5%;" class="text-right">
                                    <span class="mdi mdi-chevron-right fs20"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span data-toggle="tooltip" title="Template for Email after password recovery"><i class="icon mdi mdi-help mt2 fs18 text-system"></i></span></td>
                                <td>
									<span class="ticon"><i class="icon mdi mdi-email mt2"></i></span>
								</td>
 							    <td>
                                    [APP_NAME] Password Changed
                                </td>
                                <td>
                                    user_forgot_password_change
                                </td>
                                <td>
                                    <span class="cell-tittle">April 22, 2016</span>
                                    <span class="cell-description">14:45</span>
                                </td>
                                <td class="hv-btns text-right">
                                    <button type="button" class="btn btn-default btn-sm" href="#" title="Edit" data-toggle="modal" data-target="#myModal"><span class="icon mdi mdi-eye fs13"></span></button>
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

    <!-- Detailed View Popup -->
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
					<th width="30%">Subject</th>
					<td width="70%">
						<span class="cell-tittle">Password Confirmation</span>
					</td>
				</tr>
				<tr>
					<th width="30%">Key</th>
					<td width="70%">admin_forgot_password_confirmation</td>
				</tr>
				<tr>
					<th>Created Date</th>
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
		
		// No Sorting
		$('#datatable2').DataTable({
			"bLengthChange":   false,
			"bInfo": false,
			"searching": false,
			"bPaginate": false,
			"responsive": true,			
			"order": [],	
			"columnDefs": [{
				"targets": [ 2, 3 ],
                "render": $.fn.dataTable.render.ellipsis( 30 )
				
			},
			{
				"targets"  : 'nosort',
				"orderable": false,
			}]
			
		});
        
        $('.ellipsis').tooltip()

	});

	// Init daterange plugin
	$('#daterangepicker1').daterangepicker();
	
	// Init datetimepicker - fields
	$('#datetimepicker1').datetimepicker();
	
</script>
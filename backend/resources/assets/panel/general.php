<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","General Settings",$buffer);
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

    <!-- Begin: Content -->
    <section id="content" class="pn">
		<div class="col-md-8 col-md-offset-2 p30">
			<div class="row">
				<div class="col-md-12 mt0 mb25">
					<h3>Information</h3>
					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</p>
				</div>
			</div>
			<form action="#" method="">
				<div class="main admin-form ">
					<div class="row">
						<div class="col-md-6">
							<div class="section mb20">
								<label class="field-label cus-lbl">App/Site Name</label>
								<label for="" class="field">
									<input type="text" name="names" id="" class="gui-input" placeholder="CubixPanel 2.5.1">
								</label>
							</div>
						</div>			
						<div class="col-md-6">
							<div class="section mb20">
								<label class="field-label cus-lbl">Site/App Slogan</label>
								<label for="" class="field">
									<input type="text" name="names" id="" class="gui-input" placeholder="questions answers">
								</label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="section mb20">
								<label class="field-label cus-lbl">App Description</label>
								<label for="" class="field">
									<textarea class="gui-textarea" id="" name="" placeholder="Lorem ipsum dolar sit amit test description "></textarea>
								</label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="section mb20">
								<label class="field-label cus-lbl">Meta Keyword (for web)</label>
								<label for="" class="field">
									<textarea class="gui-textarea" id="" name="" placeholder="Lorem ipsum dolar sit amit test text"></textarea>
								</label>
							</div>
						</div>
						<div class="col-md-12">
							<div class="section mb20">
								<label class="field-label cus-lbl">Meta Description (for web)</label>
								<label for="" class="field">
									<textarea class="gui-textarea" id="" name="" placeholder="Lorem ipsum dolar sit amit test text"></textarea>
								</label>
							</div>
						</div>
						
					</div>	
					
					<div class="pull-right">
						<button type="button" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in">
							<span class="ladda-label">Submit</span>
						</button>
					</div>
				</div>
			</form>
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

<!-- Ladda Loading Button JS -->
<script src="vendor/plugins/ladda/ladda.min.js"></script>

<!-- Page Plugins -->
<script src="vendor/plugins/daterange/daterangepicker.js"></script>
<script src="vendor/plugins/datepicker/js/bootstrap-datetimepicker.js"></script>

<script src="assets/js/ellipsis.js"></script>

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
		
		// Init Ladda Plugin on buttons
		Ladda.bind('.ladda-button', {
		  timeout: 2000
		});

		// Bind progress buttons and simulate loading progress. Note: Button still requires ".ladda-button" class.
		Ladda.bind('.progress-button', {
		  callback: function(instance) {
			var progress = 0;
			var interval = setInterval(function() {
			  progress = Math.min(progress + Math.random() * 0.1, 1);
			  instance.setProgress(progress);

			  if (progress === 1) {
				instance.stop();
				clearInterval(interval);
			  }
			}, 200);
		  }
		});
		
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
	});
</script>
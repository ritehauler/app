<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","Ads Management",$buffer);
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
            <div id="topbar" >
                <div class="topbar-left">
                    <div class="sec-title">
                        <span>Showing</span>
                        <span class="p-list-count op6">(12)</span>
                    </div>
                </div>
                <div class="tabnav-holder">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#ads-managment-1" data-toggle="tab" aria-expanded="false">Advertisement</a></li>
                        <li><a href="#ads-managment-2" data-toggle="tab" aria-expanded="false">Configuration</a></li>
                    </ul>
                </div>
                <div class="topbar-right">
                    <div class="pull-right">
                        <button type="button" class="btn-default btn-sm add-new-btn link-unstyled ib" href="add-advertisement.php"><span class="icon mdi mdi-plus pr5 fs15"></span> Add Advertisement</button>
                    </div>
                </div>
            </div>
        </div>    
        <!-- End: Topbar -->
	  
        <!-- Begin: Content -->
        <section id="content">
			<div class="tab-content">
				<div id="ads-managment-1" class="tab-pane active">
					<div class="row mb15 col-container">
						<div class="col col-sm-4 col-md-3">
							<div class="panel panel-tile text-center br-a br-light ads-box">
								<div class="panel-tools">
									<button type="button" class="btn-default btn-sm edit-bttn">
										<span class="icon mdi mdi-edit"></span>
									</button>
								</div>
								<div class="panel-body bg-light light d-v-table pv30"> 
									<div class="d-v-cell">
										<span class="cubix-bannerads text-system fs80">
											<span class="path1"></span><span class="path2"></span>
										</span>
										<h2 class="mbn mt10 fs20">Advertisement 01</h2>
									</div>    
								</div>
								<div class="panel-footer bg-white br-t br-light p14">
									<span class="fs13">
										<span class="icon mdi mdi-check-circle fs13 text-system"></span> Created
										<b>9 Days Ago</b>
									</span>
								</div>   
							</div>
						</div> 
						<div class="col col-sm-4 col-md-3">
							<div class="panel panel-tile text-center br-a br-light ads-box">
								<div class="panel-tools">
									<button type="button" class="btn-default btn-sm edit-bttn">
										<span class="icon mdi mdi-edit"></span>
									</button>
								</div>
								<div class="panel-body bg-light light d-v-table pv30">
									<div class="d-v-cell">
										<span class="cubix-bannerads text-system fs80">
											<span class="path1"></span><span class="path2"></span>
										</span>
										<h2 class="mbn mt10 fs20">Advertisement 02</h2>
									</div>    
								</div>
								<div class="panel-footer bg-white br-t br-light p14">
								  <span class="fs13">
									<span class="icon mdi mdi-check-circle fs13 text-system"></span> Created
									<b>9 Days Ago</b>
								  </span>
								</div>
							</div>
						</div>
						<div class="col col-sm-4 col-md-3">
							<div class="panel panel-tile text-center br-a br-light ads-box">
								<div class="panel-tools">
									<button type="button" class="btn-default btn-sm edit-bttn">
										<span class="icon mdi mdi-edit"></span>
									</button>
								</div>
								<div class="panel-body bg-light light d-v-table pv30">
									<div class="d-v-cell">
										<span class="cubix-bannerads text-system fs80">
											<span class="path1"></span><span class="path2"></span>
										</span>
										<h2 class="mbn mt10 fs20">Advertisement 03</h2>
									</div>    
								</div>
								<div class="panel-footer bg-white br-t br-light p14">
								  <span class="fs13">
									<span class="icon mdi mdi-check-circle fs13 text-system"></span> Created
									<b>9 Days Ago</b>
								  </span>
								</div>
							</div>
						</div>
						<div class="col col-sm-4 col-md-3">
							<div class="panel panel-tile text-center br-a br-light ads-box">
								<div class="panel-tools">
									<button type="button" class="btn-default btn-sm edit-bttn">
										<span class="icon mdi mdi-edit"></span>
									</button>
								</div>
								<div class="panel-body bg-light light d-v-table pv30">
									<div class="d-v-cell">
										<span class="cubix-bannerads text-system fs80">
											<span class="path1"></span><span class="path2"></span>
										</span>
										<h2 class="mbn mt10 fs20">Advertisement 04</h2>
									</div>    
								</div>
								<div class="panel-footer bg-white br-t br-light p14">
								  <span class="fs13">
									<span class="icon mdi mdi-check-circle fs13 text-system"></span> Created
									<b>9 Days Ago</b>
								  </span>
								</div>
							</div>
						</div>
						<div class="col col-sm-4 col-md-3">
							<div class="panel panel-tile text-center br-a br-light ads-box">
								<div class="panel-tools">
									<button type="button" class="btn-default btn-sm edit-bttn">
										<span class="icon mdi mdi-edit"></span>
									</button>
								</div>
								<div class="panel-body bg-light light d-v-table pv30">
									<div class="d-v-cell">
										<span class="cubix-bannerads text-system fs80">
											<span class="path1"></span><span class="path2"></span>
										</span>
										<h2 class="mbn mt10 fs20">Advertisement 05</h2>
									</div>    
								</div>
								<div class="panel-footer bg-white br-t br-light p14">
									<span class="fs13">
										<span class="icon mdi mdi-check-circle fs13 text-system"></span> Created
										<b>9 Days Ago</b>
									</span>
								</div>
							</div>
						</div> 
						<div class="col col-sm-4 col-md-3">
							<div class="panel panel-tile text-center br-a br-light widget-box">
								<div class="panel-tools">
									<button type="button" class="btn-default btn-sm edit-bttn">
										<span class="icon mdi mdi-edit"></span>
									</button>
								</div>
								<div class="panel-body bg-light light d-v-table pv30">
									<div class="d-v-cell">
										<span class="cubix-bannerads text-system fs80">
											<span class="path1"></span><span class="path2"></span>
										</span>
										<h2 class="mbn mt10 fs20">Advertisement 06</h2>
									</div>    
								</div>
								<div class="panel-footer bg-white br-t br-light p14">
								  <span class="fs13">
									<span class="icon mdi mdi-check-circle fs13 text-system"></span> Created
									<b>9 Days Ago</b>
								  </span>
								</div>
							</div>
						</div>
						<div class="col col-sm-4 col-md-3">
							<div class="panel panel-tile text-center br-a br-light ads-box">
								<div class="panel-tools">
									<button type="button" class="btn-default btn-sm edit-bttn">
										<span class="icon mdi mdi-edit"></span>
									</button>
								</div>
								<div class="panel-body bg-light light d-v-table pv30">
									<div class="d-v-cell">
										<span class="cubix-bannerads text-system fs80">
											<span class="path1"></span><span class="path2"></span>
										</span>
										<h2 class="mbn mt10 fs20">Advertisement 07</h2>
									</div>    
								</div>
								<div class="panel-footer bg-white br-t br-light p14">
								  <span class="fs13">
									<span class="icon mdi mdi-check-circle fs13 text-system"></span> Created
									<b>9 Days Ago</b>
								  </span>
								</div>
							</div>
						</div>
						<div class="col col-sm-4 col-md-3">
							<div class="panel panel-tile text-center br-a br-light ads-box">
								<div class="panel-tools">
									<button type="button" class="btn-default btn-sm pull-left edit-bttn">
										<span class="icon mdi mdi-edit"></span>
									</button>
								</div>
								<div class="panel-body bg-light light d-v-table pv30">
									<div class="d-v-cell">
										<span class="cubix-bannerads text-system fs80">
											<span class="path1"></span><span class="path2"></span>
										</span>
										<h2 class="mbn mt10 fs20">Advertisement 08</h2>
									</div>    
								</div>
								<div class="panel-footer bg-white br-t br-light p14">
								  <span class="fs13">
									<span class="icon mdi mdi-check-circle fs13 text-system"></span> Created
									<b>9 Days Ago</b>
								  </span>
								</div>
							</div>
						</div>
					</div>    
				</div>    
				<div id="ads-managment-2" class="tab-pane">
					<div class="col-md-8 col-md-offset-2 p30 mt20">
						<div class="row">
							<div class="col-md-12 mt0 mb25">
								<h3>Information</h3>
								<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</p>
							</div>
						</div>
						<form action="#" method="">
							<div class="main admin-form">
								<div class="row">
									<div class="col-md-12">
										<div class="section mb20">
											<label class="field-label cus-lbl">Ad Type</label>
											<label class="field select">
												<select id="" name="">
													<option value="None">None</option>
													<option value="admob">Admob</option>
													<option value="custom">Custom</option>
												</select>
												<i class="arrow"></i>
											</label>
										</div>
									</div>
									<div class="col-md-12">
										<div class="section mb20">
											<label class="field-label cus-lbl">Ad Refresh Seconds</label>
											<label for="" class="field">
												<input type="number" name="number" id="" class="gui-input" value="1000" >
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
<?php include_once("footer.php"); ?>
<!-- Style Css -->

<!-- Required Plugin CSS -->
<link rel="stylesheet" type="text/css" href="vendor/plugins/datepicker/css/bootstrap-datetimepicker.css">
<link rel="stylesheet" type="text/css" href="vendor/plugins/daterange/daterangepicker.css">

<!-- Page Plugins via CDN -->
<script src="vendor/plugins/moment/moment.min.js"></script>

<!-- ckeditor -->
<script src="vendor/plugins/ckeditor/ckeditor.js"></script>

<!-- Ladda Loading Button JS -->
<script src="vendor/plugins/ladda/ladda.min.js"></script>

<!-- Page Plugins -->
<script src="vendor/plugins/xeditable/js/bootstrap-editable.js"></script>
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
        
        // Ads Box Match Height
        $('.ads-box').matchHeight();
		
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
		
	});
	
	
	
</script>
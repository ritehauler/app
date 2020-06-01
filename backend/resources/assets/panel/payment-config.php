<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","Payment Config",$buffer);
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
                <div class="topbar-right">
                    <div class="pull-right">
                        <button type="button" class="btn-default btn-sm add-new-btn link-unstyled ib" href="add-payment.php"><span class="icon mdi mdi-plus pr5 fs15"></span> Add Payment</button>
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
						<div class="col col-sm-4 col-md-4">
							<div class="panel panel-tile text-center br-a br-light payment-box">
								<div class="active-overlay"></div>
                                <div class="panel-tools">
									<button type="button" class="btn-default btn-sm edit-bttn">
										<span class="icon mdi mdi-edit"></span>
                                    </button>
                                    <div class="switch switch-success switch-xs pull-right">
                                        <input id="exampleCheckboxSwitch1" type="checkbox" >
                                        <label for="exampleCheckboxSwitch1"></label>
                                    </div>
								</div>
								<div class="panel-body bg-light light pn">
                                    <div class="panel-img stripe-bg" style="background-color: #f9f9f9;">
                                        <div class="d-v-table">
                                            <div class="d-v-cell">
                                                <img src="assets/img/dummy/payment/paypalpro-logo.png" class="pmethod-logo" title="stripe-logo" width="50%"/>
                                            </div>    
                                        </div>    
                                    </div>
                                    <div class="payment-cont">
                                        <h2 class="mtn fs20">Pay Pal Pro</h2>
                                        <p class="mtn">Merchants and developers can use Website Payments Pro to accept credit cards.</p>
                                    </div>
								</div>
							</div>
						</div>
						<div class="col col-sm-4 col-md-4">
							<div class="panel panel-tile text-center br-a br-light payment-box">
                                <div class="active-overlay"></div>
								<div class="panel-tools">
									<button type="button" class="btn-default btn-sm edit-bttn">
										<span class="icon mdi mdi-edit"></span>
                                    </button>
                                    <div class="switch switch-success switch-xs pull-right">
                                        <input id="exampleCheckboxSwitch2" type="checkbox" checked >
                                        <label for="exampleCheckboxSwitch2"></label>
                                    </div>
								</div>
								<div class="panel-body bg-light light pn">
                                    <div class="panel-img stripe-bg" style="background-color:#6772E5;">
                                        <div class="d-v-table">
                                            <div class="d-v-cell">
                                                <img src="assets/img/dummy/payment/stripe-logo.png" class="pmethod-logo" title="stripe-logo" width="40%"/>
                                            </div>    
                                        </div>    
                                    </div>
                                    <div class="payment-cont">
                                        <h2 class="mtn fs20">Stripe</h2>
                                        <p class="mtn">Stripe is a US technology company, operating in over 25 countries, that allows.</p>
                                    </div>
								</div>
							</div>
						</div>
					</div>    
				</div>    
				<div id="ads-managment-2" class="tab-pane">
					<div class="panel col-md-8 col-md-offset-2 p30 mt20">
						<form action="#" method="">
							<div class="main admin-form">
								<div class="row">
									<div class="col-md-6">
										<div class="section">
											<label class="field-label">Ad Type</label>
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
									<div class="col-md-6">
										<div class="section">
											<label class="field-label">Ad Refresh Seconds</label>
											<label for="" class="field">
												<input type="number" name="number" id="" class="gui-input" value="1000" >
											</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6 pull-right">
										<button type="button" class="btn ladda-button btn-theme btn-block" data-style="zoom-out">
											<span class="ladda-label">Submit</span>
										<span class="ladda-spinner"></span><div class="ladda-progress" style="width: 0px;"></div></button>
									</div>
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
          <div class="col-md-6">
            <span class="footer-legal">� 2017 Cubix Panel</span>
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
<!-- Style Css -->

<!-- Required Plugin CSS -->
<link rel="stylesheet" type="text/css" href="vendor/plugins/datepicker/css/bootstrap-datetimepicker.css">
<link rel="stylesheet" type="text/css" href="vendor/plugins/daterange/daterangepicker.css">

<!-- Page Plugins via CDN -->
<script src="vendor/plugins/moment/moment.min.js"></script>

<!-- ckeditor -->
<script src="vendor/plugins/ckeditor/ckeditor.js"></script>

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
        $('.payment-config').matchHeight();
		
		// Page Load Ajax
		$('button.add-new-btn').on('click', function(e){
			e.preventDefault();
			var pageRef = $(this).attr('href');
			callPage(pageRef)

		});
        
        // Select Status
		if($('a').hasClass('active-widget')){
			$('.active-widget').editable({
				showbuttons: false,
				source: [
					{value: 1, text: 'Yes'},
					{value: 2, text: 'No'},
					
				]
			});
		}

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
		
	});
	
	
	
</script>
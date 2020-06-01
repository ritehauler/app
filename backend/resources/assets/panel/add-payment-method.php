<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","Add Payment Method",$buffer);
    echo $buffer;
?>

<!-- Start: Content-Wrapper --> 

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
                        <a href=""><span class="icon mdi mdi-arrow-left pr5 fs15"> Go Back</a>
                    </div>
				</div>	
				<div class="topbar-right"></div>
            </div>   
        </div>
        <!-- End: Topbar -->

        <!-- Begin: Content -->
        <section id="content_wrapper" class="content">
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
									<label class="field-label cus-lbl prepend-icon">Method Name</label>
									<label for="payment-method" class="field prepend-icon">
                                        <input type="text" id="payment-method" name="" class="gui-input" placeholder="">
                                        <label class="field-icon">
                                            <i class="mdi mdi-card fs17"></i>
                                        </label>
                                    </label>
								</div>
							</div>
							<div class="col-md-12">
								<div class="section mb20">
									<label class="field-label cus-lbl">Method Logo</label>
									<label class="field file">
										<span class="button btn-theme">Choose File</span>
										<input type="file" class="gui-file" name="file2" id="file2" onchange="document.getElementById('uploader2').value = this.value;">
										<input type="text" class="gui-input" id="uploader2" placeholder="Please Select A File">
										
									</label>
								</div>
							</div>
                            <div class="col-md-12">
                                <div class="section mb20">
                                    <label class="field-label cus-lbl">Logo BG Color</label>
                                    <label class="field sfcolor">
                                      <input type="text" name="colorpicker" id="colorpicker" class="gui-input" placeholder="">
                                    </label>
                                </div>
                            </div>    
                            <div class="col-md-12">
								<div class="section mb20">
                                    <label class="field-label cus-lbl">Method Description</label>
                                    <label class="field">
                                        <textarea class="gui-textarea" id="comment" name="comment" placeholder=""></textarea>
                                    </label>
								</div>
							</div>
						</div>
						<div class=" pull-right">
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
<?php include_once("footer.php"); ?>

<!-- Page Plugins via CDN -->
<script src="vendor/plugins/moment/moment.min.js"></script>

<!-- Color Plugins -->            
<script src="assets/admin-tools/admin-forms/js/jquery.spectrum.min.js"></script>
<script src="assets/admin-tools/admin-forms/js/jquery.stepper.min.js"></script>

<!-- Ladda Loading Button JS -->
<script src="vendor/plugins/ladda/ladda.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {	
        
		/* @color picker
		------------------------------------------------------------------ */
		var cPicker = $("#colorpicker");
		var cContainer = cPicker.parents('.sfcolor').parent();
		$(cContainer).addClass('posr');
		$("#colorpicker").spectrum({
			preferredFormat: "hex",
			color: bgInfo,
			appendTo: cContainer,
			containerClassName: 'sp-left'
		});
		$("#colorpicker, .inline-cp").show();

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
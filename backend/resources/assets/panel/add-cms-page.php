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
						<a class="goback" href="#"><span class="icon mdi mdi-arrow-left pr5 fs15"></span> Go Back</a>
					</div>
				</div>	
				<div class="topbar-right"></div>
            </div>   
        </div>
        <!-- End: Topbar -->

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
							<div class="col-md-12">
								<div class="section mb20">
									<label class="field-label cus-lbl">Title</label>
									<label for="" class="field">
										<input type="text" name="names" id="" class="gui-input">
									</label>
								</div>
							</div>
							<div class="col-md-12">
								<div class="section mb20">
									<label class="field-label cus-lbl">Slug</label>
									<label for="" class="field">
										<input type="text" name="names" id="" class="gui-input">
									</label>
								</div>
							</div>
							<div class="col-md-12">
								<div class="section mb20">
									<label class="field-label cus-lbl">Meta Keywords</label>
									<label for="" class="field">
										<textarea class="gui-textarea" id="" name=""></textarea>
									</label>
								</div>
							</div>
							<div class="col-md-12">
								<div class="section mb20">
									<label class="field-label cus-lbl">Meta Description</label>
									<label for="" class="field">
										<textarea class="gui-textarea" id="" name=""></textarea>
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


<!-- Ladda Loading Button JS -->
<script src="vendor/plugins/ladda/ladda.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {	
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
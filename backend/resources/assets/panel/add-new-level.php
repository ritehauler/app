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
				<div class="tabnav-holder">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#ads-level-1" data-toggle="tab" aria-expanded="false">Manuall</a></li>
                        <li><a href="#ads-level-2" data-toggle="tab" aria-expanded="false">Import</a></li>
                    </ul>
                </div>
				<div class="topbar-right"></div>
            </div>   
        </div>
        <!-- End: Topbar -->

        <!-- Begin: Content -->
        <section id="content" class="pn">
			<div class="tab-content">
				<div id="ads-level-1" class="tab-pane active">
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
									<div class="col-md-6">
										<div class="section mb20">	
											<label class="field-label cus-lbl">Title</label>
											<label for="" class="field">
												<input type="text" name="names" id="" class="gui-input">
											</label>
										</div>
									</div>
									<div class="col-md-6">
										<div class="section mb20">
											<label class="field-label cus-lbl">Level Type</label>
											<label class="field select">
												<select id="" name="">
													<option value="none">Select One</option>
													<option value="simulation">Simulation</option>
													<option value="runner">Runner</option>
													<option value="qa">Q/A</option>
												</select>
												<i class="arrow"></i>
											</label>
										</div>
									</div>
									<div class="col-md-6">
										<div class="section mb20">
											<label class="field-label cus-lbl">XP Range<small> (From)</small></label>
											<label for="" class="field">
												<input type="number" name="number" id="" class="gui-input" value="1000" >
											</label>
										</div>
									</div>
									<div class="col-md-6">
										<div class="section mb20">
											<label class="field-label cus-lbl">XP Range<small> (To)</small></label>
											<label for="" class="field">
												<input type="number" name="number" id="" class="gui-input" value="1000" >
											</label>
										</div>
									</div>
									<div class="col-md-12">
										<div class="section mb20">
											<label class="field-label cus-lbl">Post Check Type</label>
											<label class="field select">
												<select id="" name="">
													<option value="none">None</option>
													<option value="sql-query">SQL Query</option>
													<option value="levels">Levels</option>
													<option value="achievements">Achievements</option>
												</select>
												<i class="arrow"></i>
											</label>
										</div>
									</div>	
									<div class="col-md-12 mb20">
										<div id='editor_holder'></div>
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
				<div id="ads-level-2" class="tab-pane">
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
											<label class="field-label cus-lbl">Upload Image</label>
											<label class="field file">
												<span class="button btn-theme">Choose File</span>
												<input type="file" class="gui-file" name="file2" id="file2" onchange="document.getElementById('uploader2').value = this.value;">
												<input type="text" class="gui-input" id="uploader2" placeholder="Please Select A File">
												
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


<!-- JSON Editor -->
<link rel="stylesheet" type="text/css" href="vendor/plugins/json_editor/jsoneditor.min.css">
<script src="vendor/plugins/json_editor/jsoneditor.js"></script>

<!-- Ladda Loading Button JS -->
<script src="vendor/plugins/ladda/ladda.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {	
        
        // create the editor
        var container = document.getElementById('editor_holder');
        var options = {
            mode: 'tree',
            modes: ['code', 'text', 'tree'], // allowed modes
            onError: function (err) {
                alert(err.toString());
            },
            onModeChange: function (newMode, oldMode) {
                //console.log('Mode switched from', oldMode, 'to', newMode);
            }
        };
        var editor = new JSONEditor(container, options);
        editor.set({"title":"CubixPanel","default_level_id":1,"speed":10,"test":[{"aa":1,"b":222}],"BanTime":1});

        // default form submit/validate (config)
        $('form[name="config"]').submit(function(e) {
            e.preventDefault();
            // hide all errors
            $("div[id^=error_msg_]").removeClass("show").addClass("hide");
            $('input[name="value"]').val(JSON.stringify(editor.get()));
            // validate form
            return jsonValidate('game_config',$(this), "#loading");
        });
		
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

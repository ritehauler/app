<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","Configurations",$buffer);
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

        <!-- Begin: Content -->
        <section id="content" class="pn">
			<div id='editor_holder'></div>
            <div class="code-footer">
                <div class="code-left">
                    <button type="button" class="btn btn-default btn-wide">Clear</button>
                </div>
                <div class="code-right">
                   <button type="button" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in">
						<span class="ladda-label">Submit</span>
					</button>
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

<!-- JSON Editor -->
<link rel="stylesheet" type="text/css" href="vendor/plugins/json_editor/jsoneditor.min.css">
<script src="vendor/plugins/json_editor/jsoneditor.js"></script>

<!-- Page Plugins via CDN -->
<script src="vendor/plugins/moment/moment.min.js"></script>

<!-- Ladda Loading Button JS -->
<script src="vendor/plugins/ladda/ladda.min.js"></script>

<!-- ckeditor -->
<script src="vendor/plugins/ckeditor/ckeditor.js"></script>

<!-- Page Plugins -->
<script src="vendor/plugins/xeditable/js/bootstrap-editable.js"></script>
<script src="vendor/plugins/daterange/daterangepicker.js"></script>
<script src="vendor/plugins/datepicker/js/bootstrap-datetimepicker.js"></script>



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
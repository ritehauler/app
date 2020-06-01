<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","Appearance",$buffer);
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
    <section id="content" class="ptn prn pln clearfix">
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
								<label class="field-label cus-lbl">Logo</label>
								<label class="field file">
									<span class="button btn-theme">Choose File</span>
									<input type="file" class="gui-file" name="file2" id="file2" onchange="document.getElementById('uploader2').value = this.value;">
									<input type="text" class="gui-input" id="uploader2" placeholder="Please Select A File">
								</label>
							</div>
						</div>	
						<div class="col-md-12">
							<div class="section mb20">
								<label class="field-label cus-lbl">Favicon</label>
								<label class="field file">
									<span class="button btn-theme">Choose File</span>
									<input type="file" class="gui-file" name="file3" id="file3" onchange="document.getElementById('uploader3').value = this.value;">
									<input type="text" class="gui-input" id="uploader3" placeholder="Please Select A File">
								</label>
							</div>
						</div>	
						
						<div class="col-md-6">
							<div class="section mb20">
								<label class="field-label cus-lbl">Color 1</label>
								<label class="field sfcolor">
								  <input type="text" name="colorpicker" id="colorpicker1" class="gui-input" placeholder="">
								</label>
							</div>
						</div>  
						<div class="col-md-6">
							<div class="section mb20">
								<label class="field-label cus-lbl">Color 2</label>
								<label class="field sfcolor">
								  <input type="text" name="colorpicker" id="colorpicker2" class="gui-input" placeholder="">
								</label>
							</div>
						</div> 
						<div class="col-md-6">
							<div class="section mb20">
								<label class="field-label cus-lbl">Color 3</label>
								<label class="field sfcolor">
								  <input type="text" name="colorpicker" id="colorpicker3" class="gui-input" placeholder="">
								</label>
							</div>
						</div> 
						<div class="col-md-6">
							<div class="section mb20">
								<label class="field-label cus-lbl">Color 4</label>
								<label class="field sfcolor">
								  <input type="text" name="colorpicker" id="colorpicker4" class="gui-input " placeholder="">
								</label>
							</div>
						</div> 
						<div class="col-md-12">
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
		  <div class="modal-header bg-theme">
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

<!-- Page Plugins via CDN -->
<script src="vendor/plugins/moment/moment.min.js"></script>

<!-- Color Plugins -->            
<script src="assets/admin-tools/admin-forms/js/jquery.spectrum.min.js"></script>
<script src="assets/admin-tools/admin-forms/js/jquery.stepper.min.js"></script>

<!-- Ladda Loading Button JS -->
<script src="vendor/plugins/ladda/ladda.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {	
		/* @color picker 01
		------------------------------------------------------------------ */

		var cPicker1 = $("#colorpicker1"),
			cPicker2 = $("#colorpicker2"),
			cPicker3 = $("#colorpicker3"),
			cPicker4 = $("#colorpicker4");

		var cContainer1 = cPicker1.parents('.sfcolor').parent(),
			cContainer2 = cPicker2.parents('.sfcolor').parent(),
			cContainer3 = cPicker3.parents('.sfcolor').parent(),
			cContainer4 = cPicker4.parents('.sfcolor').parent();

		$(cContainer1).add(cContainer2).add(cContainer3).add(cContainer4).addClass('posr');

		$("#colorpicker1").spectrum({
			preferredFormat: "hex",
			color: bgInfo,
			appendTo: cContainer1,
			containerClassName: 'sp-left'
		});
		
		$("#colorpicker2").spectrum({
			preferredFormat: "hex",
			color: bgInfo,
			appendTo: cContainer2,
			containerClassName: 'sp-left'
		});
		
		$("#colorpicker3").spectrum({
			preferredFormat: "hex",
			color: bgInfo,
			appendTo: cContainer3,
			containerClassName: 'sp-left'
		});
		
		$("#colorpicker4").spectrum({
			preferredFormat: "hex",
			color: bgInfo,
			appendTo: cContainer4,
			containerClassName: 'sp-left'
		});

		$("#colorpicker1, #colorpicker2, #colorpicker3, #colorpicker4, .inline-cp").show();
		
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
<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","Flurry Analytics",$buffer);
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

    <div class="adv-search-panel">
        <!-- Start: Topbar -->
        <div id="seaction-header">
            <div id="topbar" >
                <div class="topbar-left">
                    <div class="sec-title">
                        <span>Flurry Analytics</span>
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
    </div>
	  
      <!-- Begin: Content -->
      <section id="content">
		<div class="panel">
			<!-- Content Goes Here Start -->
				
			<!-- Content Goes Here End -->
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
<link rel="stylesheet" type="text/css" href="vendor/plugins/datepicker/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" type="text/css" href="vendor/plugins/daterange/daterangepicker.css">

<!-- Page Plugins via CDN -->
<script src="vendor/plugins/moment/moment.min.js"></script>

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
	});
	
	// Init daterange plugin
	$('#daterangepicker1').daterangepicker();
	
	// Init datetimepicker - fields
	$('#datetimepicker1').datetimepicker();
	
</script>
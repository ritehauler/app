<?php
    ob_start();
    include("header.php");
    headerDefault();
    $buffer=ob_get_contents();
    ob_end_clean();

    $buffer=str_replace("%TITLE%","Widgets",$buffer);
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
						<form class="admin-form ib va-m mr5 search">
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
        <section id="content">
            <div class="row mb15">
                <div class="col-sm-4 col-md-3">
                    <div class="panel panel-tile text-center br-a br-light widget-box mbn">
                        <div class="active-overlay"></div>
                        <div class="panel-tools">
                            <button type="button" class="btn-default btn-sm edit-bttn">
                                <span class="icon mdi mdi-edit"></span>
                            </button>
                            <div class="switch switch-success switch-xs pull-right">
                                <input id="exampleCheckboxSwitch1" type="checkbox" checked="">
                                <label for="exampleCheckboxSwitch1"></label>
                            </div>
                        </div>
                        <div class="panel-body bg-light light">
                            <span class="icon mdi mdi-delete fs60 text-system"></span>    
                            <h2 class="mbn mt10">Deleted</h2>
                            <h6 class="text-system">deleted_users</h6>
                        </div>
                        <div class="panel-footer bg-white br-t br-light p14">
                            <span class="fs13">
                                <span class="icon mdi mdi-refresh-alt fs13 text-system"></span> Updated At
                                <b>9 Days Ago</b>
                            </span>
                        </div>
                    </div>
                </div> 
                <div class="col-sm-4 col-md-3">
                    <div class="panel panel-tile text-center br-a br-light widget-box mbn">
                        <div class="active-overlay"></div>
                        <div class="panel-tools">
                            <button type="button" class="btn-default btn-sm edit-bttn">
                                <span class="icon mdi mdi-edit"></span>
                            </button>
                            <div class="switch switch-success switch-xs pull-right">
                                <input id="exampleCheckboxSwitch2" type="checkbox">
                                <label for="exampleCheckboxSwitch2"></label>
                            </div>
                        </div>
                        <div class="panel-body bg-light light">
                            <span class="icon mdi mdi-map fs60 text-system"></span>   
                            <h2 class="mbn mt10">Map</h2>
                            <h6 class="text-system">map</h6>
                        </div>
                        <div class="panel-footer bg-white br-t br-light p14">
                          <span class="fs13">
                            <span class="icon mdi mdi-refresh-alt fs13 text-system"></span> Updated At
                            <b>9 Days Ago</b>
                          </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-3">
                    <div class="panel panel-tile text-center br-a br-light widget-box mbn">
                        <div class="active-overlay"></div>
                        <div class="panel-tools">
                            <button type="button" class="btn-default btn-sm edit-bttn">
                                <span class="icon mdi mdi-edit"></span>
                            </button>
                            <div class="switch switch-success switch-xs pull-right">
                                <input id="exampleCheckboxSwitch3" type="checkbox" checked="">
                                <label for="exampleCheckboxSwitch3"></label>
                            </div>
                        </div>
                        <div class="panel-body bg-light light">
                            <span class="icon mdi mdi-account fs60 text-system"></span>
                            <h2 class="mbn mt10">Users</h2>
                            <h6 class="text-system">total_users</h6>
                        </div>
                        <div class="panel-footer bg-white br-t br-light p14">
                          <span class="fs13">
                            <span class="icon mdi mdi-refresh-alt fs13 text-system"></span> Updated At
                            <b>9 Days Ago</b>
                          </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-3">
                    <div class="panel panel-tile text-center br-a br-light widget-box mbn">
                        <div class="active-overlay"></div>
                        <div class="panel-tools">
                            <button type="button" class="btn-default btn-sm edit-bttn">
                                <span class="icon mdi mdi-edit"></span>
                            </button>
                            <div class="switch switch-success switch-xs pull-right">
                                <input id="exampleCheckboxSwitch4" type="checkbox" >
                                <label for="exampleCheckboxSwitch4"></label>
                            </div>
                        </div>
                        <div class="panel-body bg-light light">
                            <span class="icon mdi mdi-chart-donut fs60 text-system"></span>
                            <h2 class="mbn mt10">Pie Chart User</h2>
                            <h6 class="text-system">total_users</h6>
                        </div>
                        <div class="panel-footer bg-white br-t br-light p14">
                          <span class="fs13">
                            <span class="icon mdi mdi-refresh-alt fs13 text-system"></span> Updated At
                            <b>9 Days Ago</b>
                          </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-sm-4 col-md-3">
                    <div class="panel panel-tile text-center br-a br-light widget-box mbn">
                        <div class="active-overlay"></div>
                        <div class="panel-tools">
                            <button type="button" class="btn-default btn-sm edit-bttn">
                                <span class="icon mdi mdi-edit"></span>
                            </button>
                            <div class="switch switch-success switch-xs pull-right">
                                <input id="exampleCheckboxSwitch5" type="checkbox" checked="">
                                <label for="exampleCheckboxSwitch5"></label>
                            </div>
                        </div>
                        <div class="panel-body bg-light light">
                            <span class="icon mdi mdi-delete fs60 text-system"></span>    
                            <h2 class="mbn mt10">Deleted</h2>
                            <h6 class="text-system">deleted_users</h6>
                        </div>
                        <div class="panel-footer bg-white br-t br-light p14">
                            <span class="fs13">
                                <span class="icon mdi mdi-refresh-alt fs13 text-system"></span> Updated At
                                <b>9 Days Ago</b>
                            </span>
                        </div>
                    </div>
                </div> 
                <div class="col-sm-4 col-md-3">
                    <div class="panel panel-tile text-center br-a br-light widget-box mbn">
                        <div class="active-overlay"></div>
                        <div class="panel-tools">
                            <button type="button" class="btn-default btn-sm edit-bttn">
                                <span class="icon mdi mdi-edit"></span>
                            </button>
                            <div class="switch switch-success switch-xs pull-right">
                                <input id="exampleCheckboxSwitch6" type="checkbox">
                                <label for="exampleCheckboxSwitch6"></label>
                            </div>
                        </div>
                        <div class="panel-body bg-light light">
                            <span class="icon mdi mdi-map fs60 text-system"></span>   
                            <h2 class="mbn mt10">Map</h2>
                            <h6 class="text-system">map</h6>
                        </div>
                        <div class="panel-footer bg-white br-t br-light p14">
                          <span class="fs13">
                            <span class="icon mdi mdi-refresh-alt fs13 text-system"></span> Updated At
                            <b>9 Days Ago</b>
                          </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-3">
                    <div class="panel panel-tile text-center br-a br-light widget-box mbn">
                        <div class="active-overlay"></div>
                        <div class="panel-tools">
                            <button type="button" class="btn-default btn-sm edit-bttn">
                                <span class="icon mdi mdi-edit"></span>
                            </button>
                            <div class="switch switch-success switch-xs pull-right">
                                <input id="exampleCheckboxSwitch7" type="checkbox" checked="">
                                <label for="exampleCheckboxSwitch7"></label>
                            </div>
                        </div>
                        <div class="panel-body bg-light light">
                            <span class="icon mdi mdi-account fs60 text-system"></span>
                            <h2 class="mbn mt10">Users</h2>
                            <h6 class="text-system">total_users</h6>
                        </div>
                        <div class="panel-footer bg-white br-t br-light p14">
                          <span class="fs13">
                            <span class="icon mdi mdi-refresh-alt fs13 text-system"></span> Updated At
                            <b>9 Days Ago</b>
                          </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-3">
                    <div class="panel panel-tile text-center br-a br-light widget-box mbn">
                        <div class="active-overlay"></div>
                        <div class="panel-tools">
                            <button type="button" class="btn-default btn-sm edit-bttn">
                                <span class="icon mdi mdi-edit"></span>
                            </button>
                            <div class="switch switch-success switch-xs pull-right">
                                <input id="exampleCheckboxSwitch8" type="checkbox" >
                                <label for="exampleCheckboxSwitch8"></label>
                            </div>
                        </div>
                        <div class="panel-body bg-light light">
                            <span class="icon mdi mdi-chart-donut fs60 text-system"></span>
                            <h2 class="mbn mt10">Pie Chart User</h2>
                            <h6 class="text-system">total_users</h6>
                        </div>
                        <div class="panel-footer bg-white br-t br-light p14">
                          <span class="fs13">
                            <span class="icon mdi mdi-refresh-alt fs13 text-system"></span> Updated At
                            <b>9 Days Ago</b>
                          </span>
                        </div>
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
        
        // Init daterange plugin
        $('#daterangepicker1').daterangepicker();

        // Init datetimepicker - fields
        $('#datetimepicker1').datetimepicker();
		
	});
	
	
	
</script>
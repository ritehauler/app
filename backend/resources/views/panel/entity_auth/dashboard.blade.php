@include(config('panel.DIR').'header')
<style>
    .text-primary, .panel-menu .nav li a, #sidebar_left.sidebar-light .sidebar-menu > li.active > a > span:nth-child(1), .pagination > li > a, .pagination > li > span, .sb-l-m .navbar-right li a span.mdi-settings, .navbar[class*='bg-'] .nav > li.open > a span.mdi-notifications, .text-system, ul.side-nav-tab li.active a {
        color: #4a89dc !important;
    }

    #seaction-header{
        margin-top:60px;
    }
</style>
        <!-- Start: Content-Wrapper -->
<section id="content_wrapper" class="dashboardWrap">
    <div id="seaction-header" style="margin-top: 60px;">
        @include(config('panel.DIR').'flash_message')
        <div class="alert-message"></div>
        <div class="adv-search">
            <div class="topbar-left">
				<span class="panel-controls">
					<input type="hidden" name="filter_by" id="filter_by" value="week" />
					<a class="filter-stats" title="Today" data-filter="today">
						<button type="button" class="btn-default btn-sm link-unstyled ib"><span
									class="icon mdi mdi-export pr5 fs15"></span>Today
						</button>
					</a>
					 <a class="filter-stats" title="7 Days" data-filter="week">
						 <button type="button" class="btn-default btn-sm link-unstyled ib"><span
									 class="icon mdi mdi-export pr5 fs15"></span>7 Days
						 </button>
					 </a>
					 <a class="filter-stats" title="30 Days" data-filter="month">
						 <button type="button" class="btn-default btn-sm link-unstyled ib"><span
									 class="icon mdi mdi-export pr5 fs15"></span>30 Days
						 </button>
					 </a>
				</span>
			</div>
			<div class="topbar-right text-right">
				<div class="admin-form">
					<label for="start_date" class="field prepend-icon">
						<input type="text" name="start_date" id="start_date" class="gui-input field_date start_date" placeholder="Start Date">
						<label class="field-icon"><i class="fa fa-calendar-o"></i>
						</label>
					</label>
					<label for="end_date" class="field prepend-icon">
						<input type="text" id="end_date" name="end_date" class="gui-input field_date end_date" placeholder="End Date">
						<label class="field-icon"><i class="fa fa-calendar-o"></i>
						</label>
					</label>
					<button type="button" data-filter="date" class="btn ladda-button btn-theme btn-sm filter-stats" data-style="zoom-in"> <span class="ladda-label fs12">Search</span></button>
				</div>
						
			</div>
        </div>
    </div>
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
    <section id="content" class="animated fadeIn">
        <!-- Dashboard Tiles -->
		 <div class="row mb10">
            <div class="col-sm-6 col-md-3">
                <div class="panel light of-h mb10 br-darkgrey-a">
                    <div class="pn pl20 p15">
                        <div class="icon-bg">
                            <span class="icon-ttp-sale">
								<span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span>
							</span>
                        </div>
                        <h2 class="mt15 lh15 total_sales">
                            <b>0</b>
                        </h2>
                        <h5 class="text-muted">Total Sales</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="panel light of-h mb10 br-darkgrey-a">
                    <div class="pn pl20 p15">
                        <div class="icon-bg">
                            <span class="icon-ttp-order"></span>
                        </div>
                        <h2 class="mt15 lh15 total_order">
                            <b>0</b>
                        </h2>
                        <h5 class="text-muted">Total Orders</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="panel light of-h mb10 br-darkgrey-a">
                    <div class="pn pl20 p15">
                        <div class="icon-bg">
                            <span class="icon-ttp-customer"></span>
                        </div>
                        <h2 class="mt15 lh15 total_customer">
                            <b>0</b>
                        </h2>
                        <h5 class="text-muted">Total Customers</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="panel light of-h mb10 br-darkgrey-a">
                    <div class="pn pl20 p15">
                        <div class="icon-bg">
                            <span class="icon-ttp-product"></span>
                        </div>
                        <h2 class="mt15 lh15 total_driver">
                            <b>0</b>
                        </h2>
                        <h5 class="text-muted">Total Drivers</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb10">
            <div class="col-sm-6 col-md-3">
                <div class="panel light of-h mb10 br-darkgrey-a">
                    <div class="pn pl20 p15">
                        <div class="icon-bg">
                            <span class="icon-ttp-sale">
								<span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span>
							</span>
                        </div>
                        <h2 class="mt15 lh15 new_rides">
                            <b>0</b>
                        </h2>
                        <h5 class="text-muted">New Rides</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="panel light of-h mb10 br-darkgrey-a">
                    <div class="pn pl20 p15">
                        <div class="icon-bg">
                            <span class="icon-ttp-order"></span>
                        </div>
                        <h2 class="mt15 lh15 on_going_rides">
                            <b>0</b>
                        </h2>
                        <h5 class="text-muted">On Going Rides</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="panel light of-h mb10 br-darkgrey-a">
                    <div class="pn pl20 p15">
                        <div class="icon-bg">
                            <span class="icon-ttp-customer"></span>
                        </div>
                        <h2 class="mt15 lh15 cancelled_rides">
                            <b>0</b>
                        </h2>
                        <h5 class="text-muted">Cancelled Rides</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="panel light of-h mb10 br-darkgrey-a">
                    <div class="pn pl20 p15">
                        <div class="icon-bg">
                            <span class="icon-ttp-product"></span>
                        </div>
                        <h2 class="mt15 lh15 completed_rides">
                            <b>0</b>
                        </h2>
                        <h5 class="text-muted">Completed Rides</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin-panels -->
        <div class="admin-panels fade-onload dashboard_widgets">
            <div class="row">
                <!-- Three Pane Widget -->
                <div class="col-md-12 admin-grid" id="grid-0">
					<div class="panel sort-disable mb20" id="p0">
                        <div class="panel-heading">
                            <span class="panel-title">Total Sales</span>
                        </div>
                        <div class="panel-body mnw700 of-a">
                            <div class="row">
								<div id="order_sale_chart" style="width: 100%; height: 210px; margin: 0 auto"></div>
							</div>
                        </div>
                    </div>
                </div>
                <!-- end: .col-md-12.admin-grid -->
            </div>
            <!-- end: .row -->

            <div class="row">
                <div class="col-md-6">
                    <!-- Pie Chart -->
                    <div class="panel mb20" id="p12.2">
                        <div class="panel-heading">
                            <span class="panel-title">Top Driver</span>
                        </div>
                        <div class="panel-body pn">
                            <div id="top_driver" style="width: 100%; height: 250px; margin: 0 auto"></div>
                        </div>
                    </div>
                </div>
                    <div class="col-md-6">
                    <!-- Pie Chart -->
                    <div class="panel mb20" id="p12.1">
                        <div class="panel-heading">
                            <span class="panel-title">Top Customer</span>
                        </div>
                        <div class="panel-body pn">
                            <div id="top_customer" style="width: 100%; height: 250px; margin: 0 auto"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row d-flex">
                <div class="col-md-6 d-flex ">
                    <div class="panel mb20 wfull" id="p14.1">
                        <div class="panel-heading">
                            <span class="panel-title">Peak Order Time</span>
                        </div>
                        <div class="panel-body pn">
                            <div id="peak_time" style="width: 100%; height: 300px; margin: 0 auto"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 d-flex">
                    <div class="panel mb20 wfull" id="p13.1">
                        <div class="panel-heading">
                            <span class="panel-title">Top City</span>
                        </div>
                        <div class="panel-body pn">
                            <div id="top_city" style="width: 100%; height: 210px; margin: 0 auto"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row d-flex">

                <div class="col-md-4 d-flex ">

                    <div class="panel mb20 wfull" id="p5">
                        <div class="panel-heading">
                            <span class="panel-title">Total Vehicle</span>
                        </div>
                        <div class="panel-body">
                            <div class="mb20 text-right">
								<span class="fs11 text-muted ml10">
								  <i class="fa fa-circle text-primary fs12 pr5"></i><span id="c1_title"></span></span>
								<span class="fs11 text-muted ml10">
								  <i class="fa fa-circle text-info fs12 pr5"></i><span id="c2_title"></span></span>
								<span class="fs11 text-muted ml10">
								  <i class="fa fa-circle text-warning fs12 pr5"></i><span id="c3_title"></span></span>
                            </div>
                            <div class="row">
                                <div class="col-xs-4 text-center">
                                    <div class="info-circle" id="c1" data-circle-color="primary"></div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="info-circle" id="c2" data-circle-color="info"></div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="info-circle" id="c3" data-circle-color="warning"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-md-4 d-flex ">
                    <div class="panel mb20 wfull" id="p19.1">
                        <div class="panel-heading">
                            <span class="panel-title">Customer List</span>
                        </div>
                        <div class="panel-body pn">
                            <table class="table mbn tc-med-1 tc-bold-2">
                                <thead>
                                <tr class="hidden">
                                    <th>#</th>
                                    <th>First Name</th>
                                </tr>
                                </thead>
                                <tbody id="customer_list">
                                <tr>
                                    <td>
                                        <span class="fa fa-circle text-warning fs14 mr10"></span>No Record Found</td>
                                    <td>&nbsp;</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-flex ">

                    <div class="panel mb20 wfull" id="p19.2">
                        <div class="panel-heading">
                            <span class="panel-title">Driver List</span>
                        </div>
                        <div class="panel-body pn">
                            <table class="table mbn tc-med-1 tc-bold-2">
                                <thead>
                                <tr class="hidden">
                                    <th>#</th>
                                    <th>First Name</th>
                                </tr>
                                </thead>
                                <tbody id="driver_list">
                                <tr>
                                    <td>
                                        <span class="fa fa-circle text-warning fs14 mr10"></span>No Record Found</td>
                                    <td>&nbsp;</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end: .row -->
        </div>

        <div class="row dashboardForm">
            <div class="col-md-12">
                <div class="panel mb20">
                    <div class="panel-heading">
                        <span class="panel-title">Map</span>
                        <span class="panel-controls">
                            <form class="admin-form">
                                <label class="field select">
                                    <select class="field_dropdown2 form-control" id="driver_status" name="driver_status">
                                        <option value="" selected="selected">All Drive</option>
                                        <option value="1">Online</option>
                                        <option value="0">Offline</option>
                                        <option value="2">On Ride</option>

                                    </select><i class="arrow"></i>
                                </label>
                            </form>
                        </span>
                    </div>
                    <div class="panel-body" id="mapWrap">
                        <div style="width:100%; height:400px;" id="map_canvas" class="mapping"></div>

                     {{--   <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2996.0339928370745!2d69.31817291507974!3d41.32987417926943!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x38aef4ec32242aa1%3A0x1d7ab8fa6515bea9!2sSayram+Blind+Alley%2C+Tashkent%2C+Uzbekistan!5e0!3m2!1sen!2s!4v1535459950308" width="100%" height="400px" frameborder="0" style="border:0" allowfullscreen></iframe>
--}}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
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

<!-- Start: Right Sidebar -->
<aside id="sidebar_right" class="nano affix">
    <!-- Start: Sidebar Right Content -->
    <div class="sidebar-right-content nano-content p15">
        <h5 class="title-divider text-muted mb20"> Server Statistics
        <span class="pull-right"> 2013
          <i class="fa fa-caret-down ml5"></i>
        </span>
        </h5>
        <div class="progress mh5">
            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 44%">
                <span class="fs11">DB Request</span>
            </div>
        </div>
        <div class="progress mh5">
            <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 84%">
                <span class="fs11 text-left">Server Load</span>
            </div>
        </div>
        <div class="progress mh5">
            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 61%">
                <span class="fs11 text-left">Server Connections</span>
            </div>
        </div>
        <h5 class="title-divider text-muted mt30 mb10">Traffic Margins</h5>
        <div class="row">
            <div class="col-xs-5">
                <h3 class="text-primary mn pl5">132</h3>
            </div>
            <div class="col-xs-7 text-right">
                <h3 class="text-success-dark mn">
                    <i class="fa fa-caret-up"></i> 13.2% </h3>
            </div>
        </div>
        <h5 class="title-divider text-muted mt25 mb10">Database Request</h5>
        <div class="row">
            <div class="col-xs-5">
                <h3 class="text-primary mn pl5">212</h3>
            </div>
            <div class="col-xs-7 text-right">
                <h3 class="text-success-dark mn">
                    <i class="fa fa-caret-up"></i> 25.6% </h3>
            </div>
        </div>
        <h5 class="title-divider text-muted mt25 mb10">Server Response</h5>
        <div class="row">
            <div class="col-xs-5">
                <h3 class="text-primary mn pl5">82.5</h3>
            </div>
            <div class="col-xs-7 text-right">
                <h3 class="text-danger mn">
                    <i class="fa fa-caret-down"></i> 17.9% </h3>
            </div>
        </div>
        <h5 class="title-divider text-muted mt40 mb20"> Server Statistics
            <span class="pull-right text-primary fw600">USA</span>
        </h5>
    </div>
</aside>
<!-- End: Right Sidebar -->

@include(config('panel.DIR') . 'footer_bottom')
@include(config('panel.DIR').'footer')

<!-- HighChart CSS -->
<link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/highlight/styles/github.css' ) !!}" />

<!-- HighCharts Plugin -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/highcharts/highcharts.js' ) !!}"></script>
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/highlight/highlight.pack.js' ) !!}"></script>
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/sparkline/jquery.sparkline.min.js' ) !!}"></script>

<!-- Simple Circles Plugin -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/circles/circles.js' ) !!}"></script>

<!-- JvectorMap Plugin + US Map (more maps in plugin/assets folder) -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/jvectormap/jquery.jvectormap.min.js' ) !!}"></script>
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/jvectormap/assets/jquery-jvectormap-world-mill-en.js' ) !!}"></script>

<!-- Widget Javascript -->
<script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/js/demo/widgets.js' ) !!}"></script>


<script type="text/javascript">
    jQuery(document).ready(function() {

        // Define chart color patterns
        var highColors = [bgWarning, bgPrimary, bgInfo, bgAlert,
            bgDanger, bgSuccess, bgSystem, bgDark
        ];
        //demoHighCharts.init();
        var filter_by = $('#filter_by').val();
        var start_date = '';
        var end_date = '';
        var product_type = 1;
        dashboardWidgets(filter_by,start_date,end_date,highColors,product_type);



        $('.filter-stats').on('click',function(){
            var product_type = 1;
            var filter_by = $(this).data('filter');
            $('#filter_by').val(filter_by);
          //  console.log(filter_by);
            var start_date = '';
            var end_date = '';
            if(filter_by == "date"){
                start_date = $('.start_date').val();
                end_date = $('.end_date').val();
            }
            $(".alert-danger").remove();
            dashboardWidgets(filter_by,start_date,end_date,highColors,product_type);
        });


        // Because we are using Admin Panels we use the OnFinish
        // callback to activate the demoWidgets. It's smoother if
        // we let the panels be moved and organized before
        // filling them with content from various plugins

        // Init plugins used on this page
        // HighCharts, JvectorMap, Admin Panels

        // Init Admin Panels on widgets inside the ".admin-panels" container
        $('.admin-panels').adminpanel({
            grid: '.admin-grid',
            draggable: false,
            preserveGrid: true,
            mobile: false,
            onStart: function() {
                // Do something before AdminPanels runs
            },
            onFinish: function() {
                $('.admin-panels').addClass('animated fadeIn').removeClass('fade-onload');

                // Init the rest of the plugins now that the panels
                // have had a chance to be moved and organized.
                // It's less taxing to organize empty panels
               // demoHighCharts.init();
              //  runVectorMaps(); // function below
            },
            onSave: function() {
                $(window).trigger('resize');
            }
        });

        //top customer pie chart
        $.ajax({
            url: "<?php echo url('topCustomerChart'); ?>",
            dataType: "json",
            data: {"filter_type": filter_by,"start_date":start_date,"end_date":end_date},
            beforeSend: function () {
                // $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            var response = data.data;
            var column1 = $('#top_customer');

            if ($('#top_customer').length) {
                // Pie Chart1
                $('#top_customer').highcharts({
                    credits: false,
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    title: {
                        text: null
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            center: ['30%', '50%'],
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false
                            },
                            showInLegend: true
                        }
                    },
                    colors: highColors,
                    legend: {
                        x: 90,
                        floating: true,
                        verticalAlign: "middle",
                        layout: "vertical",
                        itemMarginTop: 10
                    },
                    series: [{
                        type: 'pie',
                        name: 'Total Order',
                        data: response

                    }]
                });
            }
        });

        driverLocations('');
        $('#driver_status').on('change',function(){
            //console.log($(this).find("option:selected").text());
           driverLocations($(this).val());
        });


    });

    function dashboardWidgets(filter_by,start_date,end_date,highColors,product_type)
    {
       // $(".alert-danger").remove();
        if(filter_by == "date" && (start_date == "" || end_date == "")){
            message = 'Start Date and End Date is required';
            $('.alert-message').append('<div class="alert alert-danger fade in"> <a href="#" class="close" data-dismiss="alert">&times;</a>'+message+'</div>');

            return false;
        }

        getTotalStats(filter_by,start_date,end_date);
        totalSales(filter_by,start_date,end_date,highColors);
        listWidgets(filter_by,start_date,end_date);
        topDriver(filter_by,start_date,end_date,highColors);
        topCustomerList(filter_by,start_date,end_date,highColors);
        topDriverList(filter_by,start_date,end_date,highColors);
        topCity(filter_by,start_date,end_date);
        peakOrderTime(filter_by,start_date,end_date);
        topVehiclesByOrder(filter_by,start_date,end_date);
    }

    function getTotalStats(filter_by,start_date,end_date)
    {
       // console.log(start_date); console.log(end_date);
        $.ajax({
            url: "<?php echo url('totalCountStats'); ?>",
            dataType: "json",
            data: {"filter_type": filter_by,"start_date":start_date,"end_date":end_date},
            beforeSend: function () {
                // $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            if(data.error == 0){
                var resposne = data.data;
                $('.total_sales b').text(resposne.total_sales);
                $('.total_order b').text(resposne.total_order);
                $('.total_customer b').text(resposne.total_customer);
                $('.total_driver b').text(resposne.total_driver);
                $('.new_rides b').text(resposne.new_rides);
                $('.on_going_rides b').text(resposne.on_going_rides);
                $('.cancelled_rides b').text(resposne.cancelled_rides);
                $('.completed_rides b').text(resposne.completed_rides);
            }
        });
    }

    function totalSales(filter_by,start_date,end_date,highColors)
    {
        $.ajax({
            url: "<?php echo url('totalSalesChart'); ?>",
            dataType: "json",
            data: {"filter_type": filter_by,"start_date":start_date,"end_date":end_date},
            beforeSend: function () {
                // $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            if(data.error == 0){
                var response = data.data;
                //response.date
                //response.total
                $('#order_sale_chart').highcharts({
                    credits: false,
                    colors: highColors,
                    chart: {
                        backgroundColor: '#f9f9f9',
                        className: 'br-r',
                        type: 'line',
                        zoomType: 'x',
                        panning: true,
                        panKey: 'shift',
                        marginTop: 25,
                        marginRight: 1,
                    },
                    title: {
                        text: null
                    },
                    xAxis: {
                        gridLineColor: '#EEE',
                        lineColor: '#EEE',
                        tickColor: '#EEE',
                        categories: response.date,
                    },
                    yAxis: {
                        min: 0,
                        tickInterval: 500,
                        gridLineColor: '#EEE',
                        title: {
                            text: null,
                        }
                    },
                    tooltip: {
                        formatter: function() {
                            return '<span style="font-size:10px">'+this.x+'</span><br><table>' +
                                    '<tr><td style="color:{series.color};padding:0">'+this.series.name+': </td>' +
                                    '<td style="padding:0"><b>'+response.currency+this.y+'</b></td></tr></table>';
                        }
                    },
                    plotOptions: {
                        spline: {
                            lineWidth: 3,
                        },
                        area: {
                            fillOpacity: 0.2
                        }
                    },
                    legend: {
                        enabled: false,
                    },
                    series: [{
                        name: 'Total Sale',
                        data: response.total
                    }]
                });
            }
        });
    }


    function listWidgets(filter_by,start_date,end_date)
    {
       // console.log(start_date); console.log(end_date);
        $.ajax({
            url: "<?php echo url('getListWidgets'); ?>",
            dataType: "json",
            data: {"filter_type": filter_by,"start_date":start_date,"end_date":end_date},
            beforeSend: function () {
                // $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            if(data.error == 0){
                var response = data.data;

                $('#tags_list').html('');
                $('#tags_list').html(response.tags_list);
            }
        });
    }

    function topCustomerList(filter_by,start_date,end_date)
    {
        //console.log(start_date); console.log(end_date);
        $.ajax({
            url: "<?php echo url('topCustomerList'); ?>",
            dataType: "json",
            data: {"filter_type": filter_by,"start_date":start_date,"end_date":end_date},
            beforeSend: function () {
                // $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            if(data.error == 0){
                var response = data.data;
                $('#customer_list').html('');
                $('#customer_list').html(response.coupon_list);

            }
        });
    }

    function topDriverList(filter_by,start_date,end_date)
    {
        //console.log(start_date); console.log(end_date);
        $.ajax({
            url: "<?php echo url('topDriverList'); ?>",
            dataType: "json",
            data: {"filter_type": filter_by,"start_date":start_date,"end_date":end_date},
            beforeSend: function () {
                // $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            if(data.error == 0){
                var response = data.data;
                $('#driver_list').html('');
                $('#driver_list').html(response.coupon_list);

            }
        });
    }

    function topDriver(filter_by,start_date,end_date,highColors)
    {
        $.ajax({
            url: "<?php echo url('topDriver'); ?>",
            dataType: "json",
            data: {"filter_type": filter_by,"start_date":start_date,"end_date":end_date},
            beforeSend: function () {
                // $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            var response = data.data;
                if ($('#top_driver').length) {
                    // Pie Chart1
                    $('#top_driver').highcharts({
                        credits: false,
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false
                        },
                        title: {
                            text: null
                        },
                        tooltip: {
                            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                        },
                        plotOptions: {
                            pie: {
                                center: ['30%', '50%'],
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: false
                                },
                                showInLegend: true
                            }
                        },
                        colors: highColors,
                        legend: {
                            x: 90,
                            floating: true,
                            verticalAlign: "middle",
                            layout: "vertical",
                            itemMarginTop: 10
                        },
                        series: [{
                            type: 'pie',
                            name: 'Total Order',
                            data: response

                        }]
                    });
                }
           // End High Pie Charts Demo
        });
    }

    function topCustomer(filter_by,start_date,end_date,highColors)
    {

    }

    function peakOrderTime(filter_by,start_date,end_date,highColors)
    {
       // console.log(start_date); console.log(end_date);
        $.ajax({
            url: "<?php echo url('peakOrderTime'); ?>",
            dataType: "json",
            data: {"filter_type": filter_by,"start_date":start_date,"end_date":end_date},
            beforeSend: function () {
                // $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            if(data.error == 0){
                var response = data.data;

                $('#peak_time').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: ''
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        categories: response.title,
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Order Count'
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y}</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: 'Total Order',
                        data: response.total

                    }]
                });


            }
        });
    }

    function topCity(filter_by,start_date,end_date,highColors)
    {
        // console.log(start_date); console.log(end_date);
        $.ajax({
            url: "<?php echo url('topCity'); ?>",
            dataType: "json",
            data: {"filter_type": filter_by,"start_date":start_date,"end_date":end_date},
            beforeSend: function () {
                // $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            if(data.error == 0){
                var response = data.data;

                $('#top_city').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: ''
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        categories: response.title,
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Order Count'
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y}</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: 'Total Order',
                        data: response.total

                    }]
                });


            }
        });
    }

    function topVehiclesByOrder(filter_by,start_date,end_date)
    {
        // console.log(start_date); console.log(end_date);
        $.ajax({
            url: "<?php echo url('getTopVehicles') ?>",
            dataType: "json",
            data: {"filter_type": filter_by,"start_date":start_date,"end_date":end_date},
            beforeSend: function () {
                // $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            if(data.error == 0){
                var response = data.data;
                if(response.length > 0)
                {
                    $('#c1_title').text(response[0].title);
                    $('#c2_title').text(response[1].title);
                    $('#c3_title').text(response[2].title);

                    $('#c1').attr('value',response[0].total);
                    $('#c2').attr('value',response[1].total);
                    $('#c3').attr('value',response[2].total);

                    //  console.log('info-circle');
                    var infoCircle = $('.info-circle');
                    if (infoCircle.length) {
                        // Color Library we used to grab a random color
                        var colors = {
                            "primary": [bgPrimary, bgPrimaryLr,
                                bgPrimaryDr
                            ],
                            "info": [bgInfo, bgInfoLr, bgInfoDr],
                            "warning": [bgWarning, bgWarningLr,
                                bgWarningDr
                            ],
                            "success": [bgSuccess, bgSuccessLr,
                                bgSuccessDr
                            ],
                            "alert": [bgAlert, bgAlertLr, bgAlertDr]
                        };
                        // Store all circles
                        var circles = [];
                        infoCircle.each(function(i, e) {
                            // Define default color
                            var color = ['#DDD', bgPrimary];
                            // Modify color if user has defined one
                            var targetColor = $(e).data(
                                    'circle-color');
                            if (targetColor) {
                                var color = ['#DDD', colors[
                                        targetColor][0]]
                            }
                            // Create all circles
                            var circle = Circles.create({
                                id: $(e).attr('id'),
                                value: $(e).attr('value'),
                                radius: $(e).width() / 2,
                                width: 14,
                                colors: color,
                                text: function(value) {
                                    var title = $(e).attr('title');
                                    if (title) {
                                        return '<h2 class="circle-text-value">' + value + '</h2><p>' + title + '</p>'
                                    }
                                    else {
                                        return '<h2 class="circle-text-value mb5">' + value + '</h2>'
                                    }
                                }
                            });
                            circles.push(circle);
                        });

                        // Add debounced responsive functionality
                        var rescale = function() {
                            infoCircle.each(function(i, e) {
                                var getWidth = $(e).width() / 2;
                                circles[i].updateRadius(
                                        getWidth);
                            });
                            setTimeout(function() {
                                // Add responsive font sizing functionality
                                $('.info-circle').find('.circle-text-value').fitText(0.4);
                            },50);
                        }
                        var lazyLayout = _.debounce(rescale, 300);
                        $(window).resize(lazyLayout);

                    }
                }
            }
        });
    }



    function initialize(locations) {
        var map;
        var bounds = new google.maps.LatLngBounds();
        var mapOptions = {
            mapTypeId: 'roadmap'
        };

        // Display a map on the page
        map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
        map.setTilt(45);

        // Multiple Markers

        // Display multiple markers on a map
        var infoWindow = new google.maps.InfoWindow(), marker, i;

        // Loop through our array of markers & place each one on the map
        for( i = 0; i < locations.length; i++ ) {

          //  console.log(locations[i]['latitude']);
          //  console.log(locations[i]['longitude']);
            var position = new google.maps.LatLng(locations[i]['latitude'], locations[i]['longitude']);
            bounds.extend(position);
            marker = new google.maps.Marker({
                position: position,
                map: map,
                title: locations[i]['full_name']
            });

            // Allow each marker to have an info window
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infoWindow.setContent(locations[i]['desc']);
                    infoWindow.open(map, marker);
                }
            })(marker, i));

            // Automatically center the map fitting all markers on the screen
            map.fitBounds(bounds);
        }

        // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
        var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
            this.setZoom(14);
            google.maps.event.removeListener(boundsListener);
        });

    }

   // initialize();
    function displayMap() {
        var map;
        var bounds = new google.maps.LatLngBounds();
        var mapOptions = {
            mapTypeId: 'roadmap'
        };

        // Display a map on the page
        map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
        map.setTilt(45);

        // Multiple Markers
        var locations = [
            ['California', 37.419857, -122.078827],
        ];


        // Display multiple markers on a map
        var infoWindow = new google.maps.InfoWindow(), marker, i;

        // Loop through our array of markers & place each one on the map
        for( i = 0; i < locations.length; i++ ) {

            var position = new google.maps.LatLng(locations[i][1], locations[i][2]);
            bounds.extend(position);
            marker = new google.maps.Marker({
                position: position,
                map: map,
                title: 'California'
            });

            // Allow each marker to have an info window
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infoWindow.setContent(locations[i][0]);
                    infoWindow.open(map, marker);
                }
            })(marker, i));

            // Automatically center the map fitting all markers on the screen
            map.fitBounds(bounds);
        }

        // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
        var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
            this.setZoom(14);
            google.maps.event.removeListener(boundsListener);
        });

    }


    function driverLocations(driver_status)
    {
        $('#mapWrap .alert-danger').remove();
        $.ajax({
            url: "<?php echo url('getDriverLocation'); ?>",
            dataType: "json",
            data: {"driver_status": driver_status },
            beforeSend: function () {
                // $('#' + chosen_id).empty();
            }
        }).done(function (data) {
            if(data.error == 0){
                var response = data.data;
                initialize(response);

            }
            else{
                var tt = $('#driver_status').find("option:selected").text();
                var msg = 'There is no driver '+tt;
                $('#map_canvas').before('<div class="alert alert-danger">'+msg+'</div>');
                displayMap();
            }
        });
    }



</script>
<!DOCTYPE html>
<html>

<head>
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <title>Cubix Panel</title>
    <meta name="description" content="Cubix Panel">
    <meta name="author" content="Cubix">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font CSS (Via CDN) -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
    
    <!-- Admin Forms CSS -->
    <link rel="stylesheet" type="text/css" href="assets/admin-tools/admin-forms/css/admin-forms.css">
	
	<!-- Dropzone CSS -->
	<link rel="stylesheet" href="vendor/plugins/dropzone/css/dropzone.css">

    <!-- Theme CSS -->
    <link rel="stylesheet" type="text/css" href="assets/skin/default_skin/css/theme.css">
	
	<!-- Popup CSS -->
    <link rel="stylesheet" type="text/css" href="vendor/plugins/magnific/magnific-popup.css">
	
	<!-- Datatables Core + Addons + Editor CSS -->
    <link rel="stylesheet" type="text/css" href="./vendor/plugins/datatables/media/css/dataTables.bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="./vendor/plugins/datatables/media/css/dataTables.plugins.css" />
    <link rel="stylesheet" type="text/css" href="./vendor/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css" />
	
    <!-- Marterial CSS -->
    <link rel="stylesheet" type="text/css" href="assets/fonts/material-design-icons/css/material-design-iconic-font.min.css"/><!--[if lt IE 9]> --> 


    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/img/apple-touch-icon-114x114.png"> 

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    
    <!-- Dynamic CSS -->
    <?php require_once("assets/skin/color.php"); ?>
    
</head>
    
<body class="sb-l-o sb-r-c mb70">

  <!-- Start: Main -->
<div id="main">
    <!--  Header Default   -->  
    <?php function headerDefault(){ ?>
        <!-- Start: Header -->
        <header class="navbar navbar-fixed-top bg-dark dark">
            <div class="navbar-branding">
                <a class="navbar-brand" href="dashboard.php">Cubix Panel</a>
                <span id="toggle_sidemenu_l" class="icon mdi mdi-menu fs20"></span>
            </div>  
            <ul class="nav navbar-nav navbar-left">
                <span class="nav-title">%TITLE%</span>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <span class="icon mdi mdi-notifications fs24"></span>
                    <span class="indicator"></span>
                  </a>
                    <div class="dropdown-menu media-list w300 animated animated-shorter fadeIn" role="menu">
                        <div class="dropdown-header">
                            <span class="dropdown-title"> Notifications</span>
                            <span class="label label-warning">12</span>
                        </div>
                        <div class="panel-scroller scroller-overlay scroller-pn notification-list">
                            <ul>
                                <li class="media">
                                  <a class="media-left" href="#"> <span class="mw40 w40 h-40 br64 ib text-center bg-theme"><span class="icon mdi mdi-notifications fs20 ib lh40 fa-inverse"></span></span> </a>
                                  <div class="media-body">
                                    <h5 class="media-heading">Article
                                    </h5> Last Updated 36 days ago by
                                    <a class="text-system" href="#"> Max </a>
                                    <small class="text-muted">2 Min Ago</small>  
                                  </div>
                                </li>
                                <li class="media">
                                  <a class="media-left" href="#"> <span class="mw40 w40 h-40 br64 ib text-center bg-theme"><span class="icon mdi mdi-notifications fs20 ib lh40 fa-inverse"></span></span> </a>
                                  <div class="media-body">
                                    <h5 class="media-heading">Article
                                    </h5>
                                    Last Updated 36 days ago by
                                    <a class="text-system" href="#"> Max </a>
                                    <small class="text-muted">2 Min Ago</small>  
                                  </div>
                                </li>
                                <li class="media">
                                  <a class="media-left" href="#"> <span class="mw40 w40 h-40 br64 ib text-center bg-theme"><span class="icon mdi mdi-notifications fs20 ib lh40 fa-inverse"></span></span> </a>
                                  <div class="media-body">
                                    <h5 class="media-heading">Article
                                    </h5> Last Updated 36 days ago by
                                    <a class="text-system" href="#"> Max </a>
                                    <small class="text-muted">2 Min Ago</small>    
                                  </div>
                                </li>
                                <li class="media">
                                  <a class="media-left" href="#"> <span class="mw40 w40 h-40 br64 ib text-center bg-theme"><span class="icon mdi mdi-notifications fs20 ib lh40 fa-inverse"></span></span> </a>
                                  <div class="media-body">
                                    <h5 class="media-heading">Article
                                    </h5> Last Updated 36 days ago by
                                    <a class="text-system" href="#"> Max </a>
                                    <small class="text-muted">2 Min Ago</small>    
                                  </div>
                                </li>
                            </ul>
                        </div>    
                    </div>
                </li> 
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle fw600 p15" data-toggle="dropdown"> <img src="assets/img/avatars/1.jpg" alt="avatar" class="mw30 br64">
                  </a>
                  <ul class="dropdown-menu list-group dropdown-persist w250" role="menu">
                    <li class="list-group-item">
                        <a href="account-setting.php" class="animated animated-short fadeInUp">
                            <span class="icon mdi mdi-account fs17 pr5"></span> Account   
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="general.php" class="animated animated-short fadeInUp">
                            <span class="icon mdi mdi-settings fs17 pr5"></span> Settings
                        </a>
                    </li>
                    <li class="dropdown-footer">
                      <a href="#" class="">
                      <span class="fa fa-power-off pr5"></span> Logout </a>
                    </li>
                  </ul>
                </li>
            </ul>
        </header>
        <!-- End: Header -->

        <!-- Start: Sidebar Left -->
        <aside id="sidebar_left" class="nano sidebar-light light affix">

          <!-- Start: Sidebar Left Content -->
          <div class="sidebar-left-content nano-content">

            <!-- Start: Sidebar Header -->
            <header class="sidebar-header">
                <!-- Sidebar Widget - Author  -->
                <div class="sidebar-widget author-widget">
                    <div class="media">
                        <a class="media-left" href="#">
                            <img src="assets/client/glimmer-logo.png" class="img-responsive">
                        </a>
                        <div class="media-body">
                            <div class="media-author">Glimmer App</div>
                            <div class="media-links">
                                <a href="pages_login(alt).html"><span class="fa fa-power-off pr5"></span>Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- End: Sidebar Header -->

            <!-- Start: Sidebar Left Menu -->
            <ul class="nav sidebar-menu">
				<li>
                    <a class="accordion-toggle" href="#" data-toggle="tooltip" title="Settings">
					  <span class="icon mdi mdi-settings fs17"></span>
                      <span class="sidebar-title">System</span>
					  <span class="caret"></span>
                    </a>
					<ul class="nav sub-nav">
                      <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'add-admin-role.php') !== false){ echo ' active'; }?>">
                        <a href="general.php" data-toggle="tooltip" title="General Setting">
                         General Setting</a>
                      </li>
					  <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'add-admin-role.php') !== false){ echo ' active'; }?>">
                        <a href="app-setting.php" data-toggle="tooltip" title="App Setting">
                         App Setting</a>
                      </li>
					  <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'add-admin-role.php') !== false){ echo ' active'; }?>">
                        <a href="appearance.php" data-toggle="tooltip" title="Appearance">
                         Appearance</a>
                      </li>
					  <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'layout_navbar-static.php') !== false){ echo ' active'; }?>">
                        <a href="add-payment-method.php" data-toggle="tooltip" title="Add Payment Method">
                          Add Payment Method</a>
                      </li>
					</ul>
                </li>
				<li class="sidebar-label pt20">Menu</li>
				<li>
                    <a href="dashboard.php" data-toggle="tooltip" title="Dashboard">
                       <span class="icon mdi mdi-apps fs18"></span>
                      <span class="sidebar-title">Dashboard</span>
                    </a>
                </li>
				<li>
                    <a class="accordion-toggle" href="#" data-toggle="tooltip" title="Administration">
					  <span class="icon mdi mdi-account fs18"></span>
                      <span class="sidebar-title">Administration</span>
					  <span class="caret"></span>
                    </a>
					<ul class="nav sub-nav">
                      <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'add-admin-role.php') !== false){ echo ' active'; }?>">
                        <a href="admin-role.php" data-toggle="tooltip" title="Admin Roles">
                         Admin Roles</a>
                      </li>
					  <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'add-admin.php') !== false){ echo ' active'; }?>">
                        <a href="admin.php" data-toggle="tooltip" title="Admin">
                         Admin</a>
                      </li>
					  <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'widgets.php') !== false){ echo ' active'; }?>">
                        <a href="widgets.php" data-toggle="tooltip" title="Widgets">
                         Widgets</a>
                      </li>
					</ul>
                </li>
                <li>
                    <a href="all-pages.php" data-toggle="tooltip" title="CMS Pages">
                      <span class="icon mdi mdi-view-web fs17"></span>
                      <span class="sidebar-title">CMS Pages</span>
                    </a>
                </li>
                <li>
                    <a href="query-interface.php" data-toggle="tooltip" title="Query Interface">
                      <span class="icon mdi mdi-code fs18"></span>
                      <span class="sidebar-title">Query Interface</span>
                    </a>
				</li>
				<li>
                    <a href="email-template.php" data-toggle="tooltip" title="Email Template">
                      <span class="icon mdi mdi-email fs17"></span>
                      <span class="sidebar-title">Email Template</span>
                    </a>
				</li>
				<li>
                    <a href="ads-management.php" data-toggle="tooltip" title="Ads Management">
                      <span class="icon mdi mdi-view-agenda fs17"></span>
                      <span class="sidebar-title">Ads Management</span>
                    </a>
				</li>
				<li>
                    <a href="flurry-analytic.php" data-toggle="tooltip" title="Flurry Analytic">
                      <span class="icon mdi mdi-code fs18"></span>
                      <span class="sidebar-title">Flurry Analytic</span>
                    </a>
				</li>
				<li>
                    <a class="accordion-toggle" href="#" data-toggle="tooltip" title="Game">
                      <span class="fa fa-gamepad fs17"></span>
                      <span class="sidebar-title">Game</span>
					   <span class="caret"></span>
                    </a>
					<ul class="nav sub-nav">
                      <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'layout_navbar-static.php') !== false){ echo ' active'; }?>">
                        <a href="configurations.php" data-toggle="tooltip" title="Configurations">
                          Configurations</a>
                      </li>
					  <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'layout_navbar-static.php') !== false){ echo ' active'; }?>">
                        <a href="levels.php" data-toggle="tooltip" title="Levels">
                          Levels</a>
                      </li>
					  <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'layout_navbar-static.php') !== false){ echo ' active'; }?>">
                        <a href="achievements.php" data-toggle="tooltip" title="Achievements">
                          Achievements</a>
                      </li>
					</ul>
                </li>
				<li>
                    <a class="accordion-toggle" href="#" data-toggle="tooltip" title="Q&A">
                      <span class="icon mdi mdi-font fs17"></span>
                      <span class="sidebar-title">Q&amp;A</span>
					   <span class="caret"></span>
                    </a>
					<ul class="nav sub-nav">
                      <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'layout_navbar-static.php') !== false){ echo ' active'; }?>">
                        <a href="qa-content.php" data-toggle="tooltip" title="Content">
                          Content</a>
                      </li>
					  <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'layout_navbar-static.php') !== false){ echo ' active'; }?>">
                        <a href="question.php" data-toggle="tooltip" title="Question">
                          Question</a>
                      </li>
					 
					</ul>
                </li>
				<li>
                    <a href="assetes-manager.php" data-toggle="tooltip" title="Assets Manager">
                      <span class="fa fa-file fs15"></span>
                      <span class="sidebar-title">Assets Manager</span>
                    </a>
                </li>
				<li>
                    <a href="sync-engine.php" data-toggle="tooltip" title="Sync Engine<">
                      <span class="icon mdi mdi-refresh-sync fs18"></span>
                      <span class="sidebar-title">Sync Engine</span>
                    </a>
                </li>
				<li>
                    <a href="entity-framework.php" data-toggle="tooltip" title="Entity Framework">
                      <span class="fa fa-database fs15"></span>
                      <span class="sidebar-title">Entity Framework</span>
                    </a>
                </li>
                <li>
                    <a class="accordion-toggle" href="#" data-toggle="tooltip" title="Payment">
                      <span class="fa fa-money fs16"></span>
                      <span class="sidebar-title">Payment</span>
					   <span class="caret"></span>
                    </a>
					<ul class="nav sub-nav">
                      <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'layout_navbar-static.php') !== false){ echo ' active'; }?>">
                        <a href="payment-config.php" data-toggle="tooltip" title="Payment Methods">
                          Payment Methods</a>
                      </li>
					  
					</ul>
                </li>
            </ul>
            <!-- End: Sidebar Menu -->

          </div>
          <!-- End: Sidebar Left Content -->

        </aside>
        <!-- End: Sidebar Left -->
    <?php } ?>  
      
    <!--  Header visual-editor.php   -->
    <?php function headerVisualEditor(){ ?>
        <!-- Start: Header -->
        <header class="navbar navbar-fixed-top bg-dark dark visual-editor-page">
            <div class="navbar-branding">
                <a class="navbar-brand" href="dashboard.php">Cubix Panel</a>
            </div>  
            <ul class="nav navbar-nav navbar-left">
                <li>
                    <select class="select2-dark light form-control" id="select2-dark-two">
                        <option value="CA">Version 01 - 14 Jan 2017</option>
                        <option value="AL">Version 02 - 12 Jan 2017</option>
                        <option value="WY">Version 03 - 12 Jan 2017</option>
                        <option value="WY">Version 04 - 11 Jan 2017</option>
                        <option value="WY">Version 05 - 09 Jan 2017</option>
                    </select>
                </li>
                <li>
                    <button type="button" class="btn bg-dark light btn-block">New Revision</button>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <div class="vc-tools">
                        <ul>
                            <li class="vc-edit-tools">
                                
                            </li>
                            <li>
                                <button type="button" class="btn btn-success btn-block btn-wide">Save Template</button>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle fw600 p15" data-toggle="dropdown"> <img src="assets/img/avatars/1.jpg" alt="avatar" class="mw30 br64">
                  </a>
                  <ul class="dropdown-menu list-group dropdown-persist w250" role="menu">
                    <li class="list-group-item">
                      <a href="#" class="animated animated-short fadeInUp">
                        <span class="icon mdi mdi-account fs17 pr5"></span> Account Settings
                        <span class="label label-warning">2</span>
                      </a>
                    </li>
                    <li class="list-group-item">
                      <a href="#" class="animated animated-short fadeInUp">
                        <span class="icon mdi mdi-settings fs17 pr5"></span> System Settings
                        <span class="label label-warning">6</span>
                      </a>
                    </li>
                    <li class="dropdown-footer">
                      <a href="#" class="">
                      <span class="fa fa-power-off pr5"></span> Logout </a>
                    </li>
                  </ul>
                </li>
            </ul>
        </header>
        <!-- End: Header --> 
    <?php } ?>  
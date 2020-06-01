<?php
$moduleLib = "App\Libraries\Module";
$module = new $moduleLib;

$setting_model = new \App\Http\Models\Setting();
$google_key = $setting_model->getBy('key','google_api_key');

$google_api_key = (isset($google_key->value)) ? $google_key->value : "";
?>
<!DOCTYPE html>
<html>
    <head>
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <title>{!! $_meta->admin_panel !!}</title>
        <meta name="keywords" content="{!! $_meta->site_meta_keywords !!}"/>
        <meta name="description" content="{!! $_meta->site_meta_keywords !!}">
        <meta name="author" content="{!! $_meta->site_author !!}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Font CSS (Via CDN) -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
        <!-- Admin Forms CSS -->
        <!-- Font CSS (Via CDN) -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
        <!-- Admin Forms CSS -->
        <link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/admin-tools/admin-forms/css/admin-forms.css' ) !!}">
        <!-- Dropzone CSS -->
        <link rel="stylesheet" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/dropzone/css/dropzone.css' ) !!}">
        <!-- Theme CSS -->
        <link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/skin/default_skin/css/theme.css' ) !!}">
        <!-- Popup CSS -->
        <link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/magnific/magnific-popup.css' ) !!}">
        <!-- Datatables Core + Addons + Editor CSS -->
        <link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datatables/media/css/dataTables.bootstrap.css' ) !!}" />
        <link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datatables/media/css/dataTables.plugins.css' ) !!}" />
        <link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css' ) !!}" />
        <!-- Marterial CSS -->
        <link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/fonts/material-design-icons/css/material-design-iconic-font.min.css' ) !!}"/><!--[if lt IE 9]> -->
        <!-- Favicon -->
        <link rel="shortcut icon" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/img/favicon.ico?v=0.0' ) !!}" type="image/x-icon" />
        <link rel="apple-touch-icon" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/img/apple-touch-icon.png' ) !!}">
        <link rel="apple-touch-icon" sizes="72x72" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/img/apple-touch-icon-72x72.png' ) !!}">
        <link rel="apple-touch-icon" sizes="114x114" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/img/apple-touch-icon-114x114.png' ) !!}">
        

        <link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datepicker/css/bootstrap-datetimepicker.css' ) !!}">
        <link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/daterange/daterangepicker.css' ) !!}">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">

        <!-- Select2 Plugin CSS  -->
           <link rel="stylesheet" type="text/css" href="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/select2/css/core.css' ) !!}">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9] -->

        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/jquery/jquery-1.11.1.min.js' ) !!}"></script>
        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/jquery/jquery_ui/jquery-ui.min.js' ) !!}"></script>
        <script src="{!! URL::to(config('constants.ADMIN_JS_URL').'plugins/jquery-ui/jquery-ui.js') !!}"></script>
        <!-- Theme Javascript -->

        <script src="https://maps.googleapis.com/maps/api/js?key={!! $google_api_key !!}&libraries=places"></script>
        <script src=" {!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/js/jquery.geocomplete.js' ) !!}"></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/dropzone.js"></script>
        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/js/utility/utility.js' ) !!}"></script>
        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/js/main.js' ) !!}"></script>
        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'assets/js/bootbox.js') !!}"></script>
        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'assets/js/custom.js') !!}"></script>
        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE').'assets/js/core.js') !!}"></script>
        <!-- Page Plugins via CDN -->
        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/moment/moment.min.js' ) !!}"></script>
        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/daterange/daterangepicker.js' ) !!}"></script>
        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/datepicker/js/bootstrap-datetimepicker.js' ) !!}"></script>

        <!-- ckeditor -->
        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/ckeditor/ckeditor.js' ) !!}"></script>
        <script type="text/javascript" src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/ckeditor/adapters/jquery.js' ) !!}"></script>

        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/admin-tools/admin-forms/js/jquery-ui-monthpicker.min.js' ) !!}"></script>
        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/admin-tools/admin-forms/js/jquery-ui-datepicker.min.js' ) !!}"></script>

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.jquery.min.js"></script>


        <script src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'vendor/plugins/select2/select2.min.js' ) !!}"></script>



        <!-- END: PAGE SCRIPTS -->
        <script type="text/javascript">
            jQuery(document).ready(function() {
            "use strict";
            // Init Theme Core
            Core.init();
            // Init Demo JS
            Demo.init();

            });
        </script>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <!-- Elfinder -->
        @include(config('panel.DIR').'elfinder')

        <!-- Dynamic CSS -->
        <?php include(config("panel.DIR_PANEL_RESOURCE") . "assets/skin/color.blade.php");?>

    </head>
    <style>
    .btn-file {
         position: relative;
         overflow: hidden;
     }

     .btn-file input[type=file] {
         position: absolute;
         top: 0;
         right: 0;
         min-width: 100%;
         min-height: 100%;
         font-size: 100px;
         text-align: right;
         filter: alpha(opacity=0);
         opacity: 0;
         outline: none;
         background: white;
         cursor: inherit;
         display: block;
     }

     .img-zone {
         background-color: #F2FFF9;
         border: 5px dashed #4cae4c;
         border-radius: 5px;
         padding: 20px;
     }

     .img-zone h2 {
         margin-top: 0;
     }

     .progress, #img-preview {
         margin-top: 15px;
     }
    </style>
    <script>

    </script>
    <body class="sb-l-o sb-r-c ">
        <!-- Start: Main -->
        <div id="main">
            <!--  Header Default   -->

            <!-- Start: Header -->
            <header class="navbar navbar-fixed-top bg-dark dark">
                <div class="navbar-branding"> <a class="navbar-brand" href="{!! URL::to($panel_path) !!}">{!! $_meta->admin_panel !!}</a> {{--<span id="toggle_sidemenu_l" class="icon mdi mdi-menu fs20"></span> --}}</div>
                <ul class="nav navbar-nav navbar-left">
                    <span class="nav-title">{!! isset($s_title) ? ucfirst($s_title) : '' !!}</span>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown" id="notify_icon"> <a class="dropdown-toggle"  href="#"> <span class="icon mdi mdi-notifications fs24"></span> <span class="indicator" id="notify_count"></span> </a>
                        <div class="dropdown-menu media-list w300 animated animated-shorter fadeIn" role="menu">
                            <div class="dropdown-header"> <span class="dropdown-title"> Notifications</span> {{--<span class="label label-warning">12</span> --}}</div>
                            <div class="panel-scroller scroller-overlay scroller-pn">
                                <ul class="notification-list">
                                    <li class="media">Loading...</li>
                                </ul>
                            </div>
                        </div>
                    </li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle fw600 p15" data-toggle="dropdown">
							<img src="{!! URL::to(config('panel.DIR_PANEL_RESOURCE') . 'assets/img/avatars/1.jpg' ) !!}" alt="avatar" class="mw30 br64 mr15">
						</a>
						<ul class="dropdown-menu dropdown-persist pn w250 bg-white" role="menu">
							<li class="br-t of-h">
								<a href="{!! URL::to($panel_path.'update_profile') !!}" class="fw600 p12 animated animated-short ">
								<span class="fa fa-gear pr5"></span> Profile Edit </a>
							</li>
							<li class="br-t of-h">
								<a href="{!! URL::to($panel_path.'change_password') !!}" class="fw600 p12 animated animated-short">
									<span class="fa fa-user pr5"></span> Change Password
								</a>
							</li>
							<li class="br-t of-h">
								<a href="{!! URL::to($panel_path.'logout') !!}" class="fw600 p12 animated animated-short">
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
							<a href="{!! \URL::to($panel_path .'dashboard') !!}">
								<img src="{!! \URL::to("resources/assets/".config("panel.DIR")."assets/img/logos/admin-logo.png") !!}" class="img-responsive">
							</a>
                            <!--<div class="media"> 
								<a class="media-left" href="#">
									<img src="{!! \URL::to("resources/assets/".config("panel.DIR")."assets/img/logos/$_meta->site_logo") !!}" class="img-responsive">
								</a>
								 <div class="media-body">
                                    <div class="media-author">{!! $_meta->admin_panel !!}</div>
                                    <div class="media-links"> <a href="{!! URL::to($panel_path.'logout') !!}"><span class="fa fa-power-off pr5"></span>Logout</a> </div>
                                </div>
                            </div> -->
                        </div>
                    </header>
                    <!-- End: Sidebar Header -->
                    <!-- Start: Sidebar Left Menu -->
                    <ul class="nav sidebar-menu">
                        <li class="sidebar-label pt20">Menu</li>
                        <li>
                            <a class="" href="<?=\URL::to($panel_path .'dashboard')?>" data-toggle="tooltip" title="Dashboard">
                                <span class="icon mdi mdi-home fs18"></span>
                                <span class="sidebar-title">Dashboard</span>
                            </a>
                        </li>
                    <?php
						$sys_menus = $module->getRoleMenus(0);
						if($sys_menus){
							foreach($sys_menus as $sys_menu){
								if($sys_menu->entity_type_id!="0") $sys_menu->slug = "entities/".$sys_menu->slug;
							$sub_menus = $module->getRoleMenus($sys_menu->module_id);

                            if(($sys_menu->module_id == 1 && APP_DEBUG === FALSE) || $sys_menu->slug == 'dashboard_widget' || $sys_menu->slug == 'dashboard'){
                                continue;
                            }
                        ?>
							<li>
                                <a class="<?=(($sub_menus))?'accordion-toggle':''?>" href="<?=\URL::to($panel_path .$sys_menu->slug)?>" data-toggle="tooltip" title="<?=$sys_menu->title?>">
                                    <span class="icon mdi <?=$sys_menu->icon?>"></span>
                                    <span class="sidebar-title"><?=$sys_menu->title?></span>
                                    <?php if($sub_menus){?>
                                    <span class="caret"></span>
                                    <?php }?>
                                </a>
                                <?php
                                if($sub_menus){ ?>
                                	<ul class="nav sub-nav">
								<?php	foreach($sub_menus as $sub_menu){ 
												if($sub_menu->entity_type_id!="0") $sub_menu->slug = "entities/".$sub_menu->slug;?>
                                            	<li>
													<a href="<?=\URL::to($panel_path .$sub_menu->slug)?>" data-toggle="tooltip" title="<?=$sub_menu->title?>"><?=$sub_menu->title?></a>
                                				</li>
								<?php 	} ?>
                                	</ul>
								<?php } ?>
                            </li>
					<?php 	}
						} ?>

                    </ul>
                    <!-- End: Sidebar Menu -->
                </div>
                <!-- End: Sidebar Left Content -->
            </aside>
            <!-- End: Sidebar Left -->

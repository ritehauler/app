<!-- Sidebar -->
<nav id="sidebar">
    <!-- Sidebar Scroll Container -->
    <div id="sidebar-scroll">
        <!-- Sidebar Content -->
        <!-- Adding .sidebar-mini-hide to an element will hide it when the sidebar is in mini mode -->
        <div class="sidebar-content">
            <!-- Side Header -->
            <?php /*
      <div class="side-header side-content bg-white-op">
        <!-- Layout API, functionality initialized in App() -> uiLayoutApi() -->
        <button class="btn btn-link text-gray pull-right hidden-md hidden-lg" type="button" data-toggle="layout" data-action="sidebar_close"> <i class="fa fa-times"></i> </button>
        <!-- Themes functionality initialized in App() -> uiHandleTheme() -->
        <div class="btn-group pull-right">
          <!--<button class="btn btn-link text-gray dropdown-toggle" data-toggle="dropdown" type="button">
                                  <i class="si si-drop"></i>
                              </button>-->
          <ul class="dropdown-menu dropdown-menu-right font-s13 sidebar-mini-hide">
            <li> <a data-toggle="theme" data-theme="default" tabindex="-1" href="javascript:void(0)"> <i class="fa fa-circle text-default pull-right"></i> <span class="font-w600">Default</span> </a> </li>
            <li> <a data-toggle="theme" data-theme="{!! URL::to(config('constants.ADMIN_CSS_URL').'themes/amethyst.min.css') !!}" tabindex="-1" href="javascript:void(0)"> <i class="fa fa-circle text-amethyst pull-right"></i> <span class="font-w600">Amethyst</span> </a> </li>
            <li> <a data-toggle="theme" data-theme="{!! URL::to(config('constants.ADMIN_CSS_URL').'themes/city.min.css')!!}" tabindex="-1" href="javascript:void(0)"> <i class="fa fa-circle text-city pull-right"></i> <span class="font-w600">City</span> </a> </li>
            <li> <a data-toggle="theme" data-theme="{!! URL::to(config('constants.ADMIN_CSS_URL').'themes/flat.min.css')!!}" tabindex="-1" href="javascript:void(0)"> <i class="fa fa-circle text-flat pull-right"></i> <span class="font-w600">Flat</span> </a> </li>
            <li> <a data-toggle="theme" data-theme="{!! URL::to(config('constants.ADMIN_CSS_URL').'themes/modern.min.css')!!}" tabindex="-1" href="javascript:void(0)"> <i class="fa fa-circle text-modern pull-right"></i> <span class="font-w600">Modern</span> </a> </li>
            <li> <a data-toggle="theme" data-theme="{!! URL::to(config('constants.ADMIN_CSS_URL').'themes/smooth.min.css')!!}" tabindex="-1" href="javascript:void(0)"> <i class="fa fa-circle text-smooth pull-right"></i> <span class="font-w600">Smooth</span> </a> </li>
          </ul>
        </div>
        <a class="h5 text-white" href="{!! URL::to($_dir.'dashboard/') !!}"> <span class="h4 font-w600 sidebar-mini-hide"><img src="{!! URL::to(config('constants.ADMIN_IMG_URL').'site-logo.png')!!}" width="132" height="45" alt="-"></span> </a> </div>
		*/ ?>
            <div class="side-header side-content bg-white-op custom-bg-logo">
                <!-- Layout API, functionality initialized in App() -> uiLayoutApi() -->
                <button class="btn btn-link text-gray pull-right hidden-md hidden-lg" type="button" data-toggle="layout"
                        data-action="sidebar_close"><i class="fa fa-times"></i></button>
                <!-- Themes functionality initialized in App() -> uiHandleTheme() -->
                <div class="btn-group pull-right">
                    <!--<button class="btn btn-link text-gray dropdown-toggle" data-toggle="dropdown" type="button">
                                            <i class="si si-drop"></i>
                                        </button>-->

                </div>
                <a class="h5 text-white logo-custom" href="{!! URL::to($_dir.'dashboard') !!}"> <span
                            class="h4 font-w600 sidebar-mini-hide"><img
                                src="{!! URL::to(config('constants.LOGO_PATH').$_meta->site_logo) !!}" alt="-"/></span>
                </a></div>
            <!-- END Side Header -->

            <!-- Side Content -->
            <div class="side-content">
                <ul class="nav-main" id="admin_nav_set">
                    <li id="dashboard"><a class="active" href="{!! URL::to($_dir.'dashboard/') !!}"><i
                                    class="si si-speedometer"></i><span class="sidebar-mini-hide">Dashboard </span></a>
                    </li>

                    {{--* @if($admin_group_id == 1)
                        <li id="administration"><a class="nav-submenu" data-toggle="nav-submenu" href="javascript:;"><i
                                    class="si si-user"></i><span class="sidebar-mini-hide">Administration</span></a>
                          <ul>
                            <li id="administration-admin_group"><a href="{!! URL::to($_dir.'admin_group/') !!}">Admin Roles</a></li>
                            <li id="administration-admin"><a href="{!! URL::to($_dir.'admin/') !!}">Admin</a></li>
                            <li id="administration-admin_widget"><a href="{!! URL::to($_dir.'admin_widget/') !!}">Widgets</a></li>
                          </ul>
                        </li>
                  @endif
                    *--}}

                    @foreach($final_array as $module_id)
                        @if($module_id['has_child'] > 0)
                            <li id="{!! $module_id['class_name'] !!}">
                                <a class="nav-submenu" data-toggle="nav-submenu" href="javascript:;"><i
                                            class="{!! $module_id['icon_class'] !!}"></i><span
                                            class="sidebar-mini-hide">{!! $module_id['name'] !!}</span></a>
                                @if(isset($module_id['children']))
                                    <ul>
                                        @foreach($module_id['children'] as $c_module_id)
                                            <li id="{!! $module_id['class_name']."-".$c_module_id['class_name'] !!}">
                                                <a href="{!! URL::to($_dir.$c_module_id['class_name'].'/') !!}"><i
                                                            class="{!! $c_module_id['icon_class'] !!}"></i><span
                                                            class="sidebar-mini-hide">{!! $c_module_id['name'] !!}</span></a>
                                            </li>

                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @else
                            <li id="{!! $module_id['class_name'] !!}">
                                <a href="{!! URL::to($_dir.$module_id['class_name'].'/') !!}"><i
                                            class="{!! $module_id['icon_class'] !!}"></i><span
                                            class="sidebar-mini-hide">{!! $module_id['name'] !!}</span></a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <!-- END Side Content -->
        </div>
        <!-- Sidebar Content -->
    </div>
    <!-- END Sidebar Scroll Container -->
</nav>
<!-- END Sidebar -->
{{--*/

// extra models
$admin_module_model = $model_path."AdminModule";
$admin_module_model = new $admin_module_model;

// get modules that are view-able
$query = $admin_module_model->select("am.admin_module_id", "am.parent_id", "am.order", "am.name", "amp.admin_group_id", "amp.view", "am.is_active", "am.show_in_menu")
	->from("admin_module AS am")
	->join("admin_module_permission AS amp", "amp.admin_module_id", "=", "am.admin_module_id")
    ->whereRaw("am.is_active = '1'")
    ->whereRaw("am.show_in_menu = '1'")
    ->whereRaw("am.admin_module_id  > 1")
    ->whereRaw("amp.view = '1'")
    ->whereNull("am.deleted_at")
    ->whereNull("amp.deleted_at");
// basic module sql as per alloted permissions
$modules_sql = $query->toSql();

$query = $admin_module_model->select("admin_module_id")
	->from(\DB::raw("(".$modules_sql.") AS vtable"))
    ->where("admin_group_id","=",\Session::get(ADMIN_SESS_KEY."auth")->admin_group_id)
    ->where("parent_id","=", 0)
    ->orderBy("order", "ASC");
$module_ids = $query->get();
/*--}}
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
        <a class="h5 text-white" href="{!! URL::to(DIR_ADMIN.'dashboard/') !!}"> <span class="h4 font-w600 sidebar-mini-hide"><img src="{!! URL::to(config('constants.ADMIN_IMG_URL').'site-logo.png')!!}" width="132" height="45" alt="-"></span> </a> </div>
		*/ ?>
      <div class="side-header side-content bg-white-op custom-bg-logo">
        <!-- Layout API, functionality initialized in App() -> uiLayoutApi() -->
        <button class="btn btn-link text-gray pull-right hidden-md hidden-lg" type="button" data-toggle="layout" data-action="sidebar_close"> <i class="fa fa-times"></i> </button>
        <!-- Themes functionality initialized in App() -> uiHandleTheme() -->
        <div class="btn-group pull-right"> 
          <!--<button class="btn btn-link text-gray dropdown-toggle" data-toggle="dropdown" type="button">
                                  <i class="si si-drop"></i>
                              </button>-->
          
        </div>
        <a class="h5 text-white logo-custom" href="{!! URL::to(DIR_ADMIN.'dashboard') !!}"> <span class="h4 font-w600 sidebar-mini-hide"><img src="{!! URL::to(config('constants.LOGO_PATH').$_meta->site_logo) !!}" alt="-" /></span> </a> </div>
      <!-- END Side Header --> 
      
      <!-- Side Content -->
      <div class="side-content">

        <ul class="nav-main" id="admin_nav_set">
          <li id="dashboard"> <a class="active" href="{!! URL::to(DIR_ADMIN.'dashboard/') !!}"><i class="si si-speedometer"></i><span class="sidebar-mini-hide">Dashboard </span></a> </li>
          @if(\Session::get(ADMIN_SESS_KEY."auth")->admin_group_id == 1)
          <li id="administration"> <a class="nav-submenu" data-toggle="nav-submenu" href="javascript:;"><i class="si si-user"></i><span class="sidebar-mini-hide">Administration</span></a>
            <ul>
              <li id="administration-admin_group"> <a href="{!! URL::to(DIR_ADMIN.'admin_group/') !!}">Admin Roles</a> </li>
              <li id="administration-admin"> <a href="{!! URL::to(DIR_ADMIN.'admin/') !!}">Admin</a> </li>
              <li id="administration-admin_widget"> <a href="{!! URL::to(DIR_ADMIN.'admin_widget/') !!}">Widgets</a> </li>
            </ul>
          </li>
          @endif
          <!-- modules via db -->
          @if(isset($module_ids[0]))
          <!-- child container opened -->
          {{--*/ $child_cont_opened = 0; /*--}}
          @foreach($module_ids as $module_id)
          <!-- get record -->
          {{--*/ $record = $admin_module_model->get($module_id->admin_module_id); /*--}}
          <!-- has child --> 
          {{--*/ $has_child = $admin_module_model->select("admin_module_id")->where("parent_id","=",$record->admin_module_id)->count(); /*--}}
          <!-- parent record --> 
          {{--*/ $p_record = $admin_module_model->get($record->parent_id); /*--}}
          <!-- class_name --> 
          {{--*/ $class_name = $record->class_name; /*--}}
          	<!-- if has child -->
            @if($has_child > 0)
            <li id="{!! $class_name !!}">
            <a class="nav-submenu" data-toggle="nav-submenu" href="javascript:;"><i class="{!! $record->icon_class !!}"></i><span class="sidebar-mini-hide">{!! $record->name !!}</span></a>
            {{--*/
            $query = $admin_module_model->select("admin_module_id")
                ->from(\DB::raw("(".$modules_sql.") AS vtable"))
                ->where("admin_group_id","=",\Session::get(ADMIN_SESS_KEY."auth")->admin_group_id)
                ->where("parent_id","=", $record->admin_module_id)
                ->where("is_active","=", 1)
                ->orderBy("order", "ASC");
            $c_module_ids = $query->get(); /*--}}
            @if(isset($c_module_ids[0]))
            	<ul>
            	@foreach($c_module_ids as $c_module_id)
                <!-- get record -->
                {{--*/ $c_record = $admin_module_model->get($c_module_id->admin_module_id); /*--}}
                {{--*/ $class_name = $record->class_name."-".$c_record->class_name; /*--}}
                <li id="{!! $class_name !!}">
                <a href="{!! URL::to(DIR_ADMIN.$c_record->class_name.'/') !!}"><i class="{!! $c_record->icon_class !!}"></i><span class="sidebar-mini-hide">{!! $c_record->name !!}</span></a></li>
                @endforeach
                </ul>
            @endif
            
            </li>
            @else
            <li id="{!! $class_name !!}">
            <a href="{!! URL::to(DIR_ADMIN.$record->class_name.'/') !!}"><i class="{!! $record->icon_class !!}"></i><span class="sidebar-mini-hide">{!! $record->name !!}</span></a>
            </li>
            @endif
          
          @endforeach
          @endif
        </ul>
      </div>
      <!-- END Side Content --> 
    </div>
    <!-- Sidebar Content --> 
  </div>
  <!-- END Sidebar Scroll Container --> 
</nav>
<!-- END Sidebar -->
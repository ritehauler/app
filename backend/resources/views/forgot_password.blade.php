{{--*/
// extra models
// - temp (will be removed after custom login/forgot/recover
$conf_model = "App\Http\Models\Conf";
$conf_model = new $conf_model;
$_meta = isset($_meta) ? $_meta : json_decode($conf_model->getBy('key','site')->value);
/*--}}
@include(DIR_ADMIN.'header') 
<!-- Reminder Content -->
<div class="content overflow-hidden">
  <div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
    <!--@if (count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif
      
      @if (session('status'))
      <div class="alert alert-success"> {{ session('status') }} </div>
      @endif -->
      <!-- Session Messages --> 
      @include(DIR_ADMIN.'flash_message')
      <!-- Reminder Block -->
      <div class="block block-themed animated fadeIn">
        <div class="block-header bg-success">
          <ul class="block-options">
            <li> <a href="{!! URL::to(DIR_ADMIN."login") !!}" data-toggle="tooltip" data-placement="left" title="Log In"><i class="si si-login"></i></a> </li>
          </ul>
          <h3 class="block-title">{!! $page_action !!}</h3>
        </div>
        <div class="block-content block-content-full block-content-narrow"> 
          <!-- Reminder Title -->
          <h1 class="h2 font-w600 push-30-t push-15 text-center"><img src="{!! URL::to(config('constants.LOGO_PATH').$_meta->site_logo) !!}" class="logo-custom" alt="-"></h1>
          <p>Please provide your account’s email and we will send you your password.</p>
          <!-- END Reminder Title --> 
          
          <!-- Reminder Form --> 
          <!-- jQuery Validation (.js-validation-reminder class is initialized in js/pages/base_pages_reminder.js) --> 
          <!-- For more examples you can check out https://github.com/jzaefferer/jquery-validation -->
          <form name="data_form" class="js-validation-reminder form-horizontal push-30-t push-50" action="" method="post">
            <div class="form-group">
              <div class="col-xs-12">
                <div class="form-material form-material-primary floating">
                  <input class="form-control" type="email" id="login-username" name="email">
                  <label for="email"><span class="text-danger">*</span> Email</label>
                  <div id="error_msg_email" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-6 col-md-5">
                <button class="btn btn-block btn-success" type="submit"><i class="si si-envelope-open pull-right"></i> Send Mail</button>
              </div>
            </div>
            <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
            <input type="hidden" name="do_post" value="1" />
          </form>
          <!-- END Reminder Form --> 
        </div>
      </div>
      <!-- END Reminder Block --> 
    </div>
  </div>
</div>
<!-- END Reminder Content --> 

<!-- Reminder Footer -->
<?php /*?><div class="push-5-t text-center animated fadeInUp"> <small class="text-muted font-w600"><span class="js-year-copy"></span> &copy; {!! APP_NAME !!}</small> </div><?php */?>
<script>
// default form submit/validate
$('form[name="data_form"]').submit(function(e) {
	e.preventDefault();
	// hide all errors
	$("div[id^=error_msg_]").removeClass("show").addClass("hide");
	// validate form
	return jsonValidate('{!! $route_action !!}',$(this));
});
</script>
<!-- END Reminder Footer --> 
@include(DIR_ADMIN.'footer')
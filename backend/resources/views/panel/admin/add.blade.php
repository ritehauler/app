@include(DIR_ADMIN.'header')
<!-- Begin: Content -->
<section id="content_wrapper" class="content">
  <section id="content" class="pn">
    <div class="col-md-8 col-md-offset-2 p30">
      <div class="row">
        <div class="col-md-12 mt0 mb25">
          <h3>{!! $page_action !!} {!! $s_title !!}</h3>
        </div>
      </div>
      <form  name="data_form" method="post">
        <div class="main admin-form ">
          @include(DIR_ADMIN.'flash_message') 
          <div class="row">
            <div class="col-md-12">
              <div class="section mb20">
                <label class="field-label cus-lbl">Username</label>
                <label for="" class="field">
                <input type="text" name="username" id="username" class="gui-input">
                </label>
                <div id="error_msg_username" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="section mb20">
                <label class="field-label cus-lbl">Email</label>
                <label for="" class="field">
                <input type="text" name="email" id="email" class="gui-input">
                </label>
                <div id="error_msg_email" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="section mb20">
                <label class="field-label cus-lbl">Role</label>
                <label class="field select">
                    <select id="admin_group_id" name="admin_group_id">
                        @if (isset($admin_groups[0]))
                              @foreach($admin_groups as $record)
                                <option value="{!! $record->admin_group_id !!}">{!! $record->name !!}</option>
                              @endforeach
                         @endif
                    </select>
                    <i class="arrow"></i>
                </label>
                <div id="error_msg_admin_group_id" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="section mb20">
                <label class="field-label cus-lbl">Password</label>
                <label for="" class="field">
                <input type="password" class="gui-input" id="password" name="password" />
                </label>
                <div id="error_msg_password" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
              </div>
            </div>
          </div>
          <div class="pull-right">
            <button type="submit" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in"> <span class="ladda-label">Submit</span> </button>
          </div>
        </div>
         <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
         <input type="hidden" name="do_post" value="1" />
      </form>
    </div>
  </section>
  <!-- End: Content -->
  <!-- Begin: Page Footer -->
  @include(DIR_ADMIN . 'footer_bottom') 
</section>
<!-- End: Page Footer -->
<script type="text/javascript">
$(function() {
	// default form submit/validate
	$('form[name="data_form"]').submit(function(e) {
		e.preventDefault();
		// hide all errors
		$("div[id^=error_msg_]").removeClass("show").addClass("hide");
		// validate form
		return jsonValidate('<?=Request::url()?>',$(this));
	});
	
});

</script> 
@include(DIR_ADMIN.'footer') 
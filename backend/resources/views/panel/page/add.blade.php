@include(config('panel.DIR').'header')
<!-- Begin: Content -->
<section id="content_wrapper" class="content">
  <section id="content" class="pn">
    <div class="col-md-8 col-md-offset-2 p30">
      <div class="row">
        <div class="col-md-12 mt0 mb25">
          <h3>{!! $page_action !!} {!! $module !!}</h3>
        </div>
      </div>
      <form  name="data_form" method="post">
        <div class="main admin-form ">
          @include(config('panel.DIR').'flash_message')
          <div class="row">
            <div class="col-md-12">
              <div class="section mb20">
                <label class="field-label cus-lbl">Title</label>
                <label for="" class="field">
                <input type="text" name="title" id="title" class="gui-input">
                </label>
                <div id="error_msg_title" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="section mb20">
                <label class="field-label cus-lbl">Meta Keyword</label>
                <label for="" class="field">
                <textarea name="meta_keywords" id="meta_keywords" class="gui-input"></textarea>
                </label>
                <div id="error_msg_meta_keywords" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="section mb20">
                <label class="field-label cus-lbl">Meta Description</label>
                <label for="" class="field">
                <textarea class="gui-input" id="meta_description" name="meta_description"></textarea>
                </label>
                <div id="error_msg_meta_description" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="section mb20">
                <label class="field-label cus-lbl">Content</label>
                <label for="" class="field">
                <textarea class="gui-input" id="content" name="content"></textarea>
                </label>
                <div id="error_msg_content" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
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
  @include(config('panel.DIR') . 'footer_bottom')
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
@include(config('panel.DIR').'footer')
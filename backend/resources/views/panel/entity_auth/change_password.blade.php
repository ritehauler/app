@include(config('panel.DIR').'header')
        <!-- Begin: Content -->
<section id="content_wrapper" class="content">
    <section id="content" class="pn">
            <div  class="tray tray-center p25 va-t posr" style="height: 712px;">
				<div class="panel panel-theme panel-border top mb25">
					<div class="panel-heading">
						<span class="panel-title">{!! $p_title !!}</span>
					</div>
					<div class="panel-body p20 pb10">
						<form name="data_form" method="post" id="data_form">
							<div class="main admin-form ">
								@include(config('panel.DIR').'flash_message')
								@if (Session::has('message'))
									<div class="alert alert-info">{{ Session::get('message') }}</div>
								@endif
								<div class="alert-message"></div>
								<div class="row">


									<div class="col-md-6">
										<div class="section mb20"><label title="" class="field-label cus-lbl field-label cus-lbl"
																		 data-toggle="tooltip" data-original-title="Is User Exists">
												Current Password*</label>
											<label class=" field select ">
												<input type="password" name="current_password" id="password" class="gui-input"
													   placeholder="" autocomplete="off">
											</label>
											<div id="error_msg_current_password"
												 class="help-block text-right animated fadeInDown hide" style="color:red"></div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="section mb20"><label title="" class="field-label cus-lbl field-label cus-lbl"
																		 data-toggle="tooltip" data-original-title="Is User Exists">
												New Password*</label>
											<label class=" field select ">
												<input type="password" name="new_password" id="password" class="gui-input"
													   placeholder="" autocomplete="off">
											</label>
											<div id="error_msg_new_password" class="help-block text-right animated fadeInDown hide"
												 style="color:red"></div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="section mb20"><label title="" class="field-label cus-lbl field-label cus-lbl"
																		 data-toggle="tooltip" data-original-title="Is User Exists">
												Confirm Password*</label>
											<label class=" field select ">
												<input type="password" name="confirm_password" id="confirm_password" class="gui-input"
													   placeholder="" autocomplete="off">
											</label>
											<div id="error_msg_confirm_password" class="help-block text-right animated fadeInDown hide"
												 style="color:red"></div>
										</div>
									</div>
									{{--<input type="hidden" name="entity_type" value=""/>--}}
									<input type="hidden" name="post_change_password" value="1"/>
									<input type="hidden" name="_token" value="{!! csrf_token() !!}"/>
								</div>


								<div class="pull-right">
									<button type="submit" class="btn ladda-button btn-theme btn-wide" data-style="zoom-in"><span
												class="ladda-label">Submit</span></button>
								</div>
							</div>

						</form>
					</div>
				</div>
			</div>
    </section>
    <!-- End: Content -->
    <!-- Begin: Page Footer -->
    @include(config('panel.DIR') . 'footer_bottom')
</section>
<!-- End: Page Footer -->
<script type="text/javascript">

    $(function () {

        

        // Init Common JS
        Common.init();

        $('form[name="data_form"]').submit(function (e) {
            e.preventDefault();
          //  Common.jsonValidate('<?=Request::url()?>', this);
			Common.jsonValidation('<?=Request::url()?>', $(this),'',"auth");
        });
    });
</script>
@include(config('panel.DIR').'footer')

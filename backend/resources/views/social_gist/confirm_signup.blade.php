{{--*/
// extra models
$conf_model = $model_path."Conf";
$conf_model = new $conf_model;

$_meta = isset($_meta) ? $_meta : json_decode($conf_model->getBy('key','site')->value);
/*--}}
@include($dir.'header')

        <!-- Reminder Content -->
<div class="content overflow-hidden">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">

            <!-- Reminder Block -->
            <div class="block block-themed animated fadeIn">
                <div class="block-header bg-success">
                    <h3 class="block-title">{!! $page_action !!}</h3>
                </div>
                <div class="block-content block-content-full block-content-narrow">

                    <form name="data_form" class="js-validation-reminder form-horizontal push-30-t push-50"
                          action="" method="post">
                        <div class="form-group">
                            <div class="col-xs-12">
                                <div class="form-material form-material-primary floating">
                                    <input class="form-control" type="email" name="email" value="{!! $email !!}">
                                    <label for="email"><span class="text-danger">*</span> Email</label>
                                    <div id="error_msg_email" class="help-block text-right animated fadeInDown hide"
                                         style="color:red"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <div class="form-material form-material-primary floating">
                                    <input class="form-control" type="text" name="verification_token"
                                           value="{!! $verification_token !!}">
                                    <label for="verification_token"><span class="text-danger">*</span> Verification Code</label>
                                    <div id="error_msg_verification_token"
                                         class="help-block text-right animated fadeInDown hide"
                                         style="color:red"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6 col-sm-offset-3">
                                <button class="btn btn-block btn-success" type="submit">Submit</button>
                            </div>
                        </div>
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}"/>
                        <input type="hidden" name="do_post" value="1"/>
                    </form>
                    <!-- END Reminder Form --></div>
            </div>
            <!-- END Reminder Block -->
        </div>
    </div>
</div>
<!-- END Reminder Content -->

<!-- Reminder Footer -->
<script>
    // default form submit/validate
    $('form[name="data_form"]').submit(function (e) {
        e.preventDefault();
        // hide all errors
        $("div[id^=error_msg_]").removeClass("show").addClass("hide");
        // validate form
        return jsonValidate("", $(this));
    });

    $(function() {
        $('form[name="data_form"]').submit();
    });

</script>
<!-- END Reminder Footer -->
@include($dir.'footer')
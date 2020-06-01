{{--*/
// extra models
$conf_model = $model_path."Conf";
$conf_model = new $conf_model;

$_meta = isset($_meta) ? $_meta : json_decode($conf_model->getBy('key','site')->value);

// get entity var
$setting_model = $model_path."Setting";
$setting_model = new $setting_model;

// Opengraph Requisites Starts
// ios_app_url
$setting = $setting_model->getBy("key","ios_app_url");
$ios_app_url = $setting ? $setting->value : FALSE;
// android_app_url
$setting = $setting_model->getBy("key","android_app_url");
$android_app_url = $setting ? $setting->value : FALSE;
// ios_store_id
$setting = $setting_model->getBy("key","ios_store_id");
$ios_store_id = $setting ? $setting->value : FALSE;
// android_store_id
$setting = $setting_model->getBy("key","android_store_id");
$android_store_id = $setting ? $setting->value : FALSE;
// og_schema_share
$setting = $setting_model->getBy("key","og_schema_signup_thankyou");
$og_schema_forgot = $setting ? $setting->value : FALSE;

// stores url
$appstore_url = $ios_app_url ? $ios_app_url : "";
$appstore_id = $ios_store_id ? $ios_store_id : "";
$appstore_url2 = "itms-apps://itunes.apple.com/app/id".$appstore_id;

$playstore_url = $android_app_url ? $android_app_url : "";
$playstore_keystore = $android_store_id ? $android_store_id : "";
$playstore_url2 = "market://details?id=".$playstore_keystore;



// mobile schema
$schema = $og_schema_forgot ? $og_schema_forgot : "";
$og_schema_forgot = str_replace(array("{verification_token}"),array($verification_token),$og_schema_forgot);

header('Content-Type: text/html; charset=utf-8');

$detect = new \App\Libraries\Mobile_Detect;

// Opengraph Requisites Ends

/*--}}
@include($dir.'header')

        <!-- Reminder Content -->
<div class="content overflow-hidden" id="content">
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
                                    <p class="text-center text-success">{!! $data["note"] !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group hide">
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
                            <div class="col-xs-12">
                                <div class="form-material form-material-primary floating">
                                    <input class="form-control" type="password" name="password" value="">
                                    <label for="password"><span class="text-danger">*</span> New Password</label>
                                    <div id="error_msg_password" class="help-block text-right animated fadeInDown hide"
                                         style="color:red"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <div class="form-material form-material-primary floating">
                                    <input class="form-control" type="password" name="confirm_password" value="">
                                    <label for="confirm_password"><span class="text-danger">*</span> Confirm
                                        Password</label>
                                    <div id="error_msg_confirm_password"
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

<div class="content overflow-hidden hide" id="thankyou">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">

            <!-- Reminder Block -->
            <div class="block block-themed animated fadeIn">
                <div class="block-header bg-success">
                    <h3 class="block-title">{!! $page_action !!}</h3>
                    <h4 class="block-content text-center">{!! $data["content"] !!}</h4>
                </div>
            </div>
            <!-- END Reminder Block -->
        </div>
    </div>
</div>
<!-- END Reminder Content -->

<!-- Reminder Footer -->
<script type="application/javascript">
    // disable alerts
    //window.alert = null;
    //var alert = null;

    // default form submit/validate
    $('form[name="data_form"]').submit(function (e) {
        e.preventDefault();
        // hide all errors
        $("div[id^=error_msg_]").removeClass("show").addClass("hide");
        // validate form
        return jsonValidate("", $(this));
    });


    $(document).ready(function () {

        @if($detect->isMobile())
        @if($detect->isiOS())
        $.ajax({
            type: "GET",
            url: "{!! $schema !!}",
            data: null,
            complete: function (e, xhr, settings) {
                if (e.status === 200) {
                    $("div[id=content]").remove();
                    $("div[id=thankyou]").removeClass("hide").addClass("show");
                    alert('{!! $schema !!}');
                    window.location = '{!! $schema !!}';
                } else {
                    //alert("error : " + e.statusText);
                    //window.location = '{!! $appstore_url2 !!}';
                }
            }
        });
        @endif

        @if($detect->isAndroidOS())
        $.ajax({
            type: "GET",
            url: "{!! $schema !!}",
            data: null,
            complete: function (e, xhr, settings) {
                if (e.status === 200) {
                    $("div[id=content]").remove();
                    $("div[id=thankyou]").removeClass("hide").addClass("show");
                    alert('{!! $schema !!}');
                    window.location = '{!! $schema !!}';
                } else {
                    //alert("error : " + e.statusText);
                    //window.location = '{!! $playstore_url2 !!}';
                }
            }
        });
        @endif
        @else
        //$('form[name="data_form"]').submit();
        @endif
    });


</script>
<!-- END Reminder Footer -->
@include($dir.'footer')
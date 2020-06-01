{{--*/
// extra models
$conf_model = $model_path."Conf";
$conf_model = new $conf_model;

$_meta = isset($_meta) ? $_meta : json_decode($conf_model->getBy('key','site')->value);

// get entity var
$setting_model = $model_path."Setting";
$setting_model = new $setting_model;

$entity = isset($data[$_entity_identifier]) ? $data[$_entity_identifier] : NULL;

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
$og_schema_signup = $setting ? $setting->value : FALSE;

// stores url
$appstore_url = $ios_app_url ? $ios_app_url : "";
$appstore_id = $ios_store_id ? $ios_store_id : "";
$appstore_url2 = "itms-apps://itunes.apple.com/app/id".$appstore_id;

$playstore_url = $android_app_url ? $android_app_url : "";
$playstore_keystore = $android_store_id ? $android_store_id : "";
$playstore_url2 = "market://details?id=".$playstore_keystore;



// mobile schema
$schema = $og_schema_signup ? $og_schema_signup : "";
$og_schema_signup = str_replace(array("{email}","{user_id}"),array(@$entity->email,@$entity->{$_entity_pk}),$og_schema_signup);

header('Content-Type: text/html; charset=utf-8');

$detect = new \App\Libraries\Mobile_Detect;

// Opengraph Requisites Ends
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
                    <h4 class="block-content text-center">{!! $data["content"] !!}</h4>
                </div>
            </div>
            <!-- END Reminder Block -->
        </div>
    </div>
</div>
<script type="application/javascript">
    // disable alerts
    //window.alert = null;
    //var alert = null;



    $(document).ready(function () {
        @if($detect->isiOS())
        $.ajax({
            type: "GET",
            url: "{!! $schema !!}",
            data: null,
            complete: function (e, xhr, settings) {
                if (e.status === 200) {
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
                    alert('{!! $schema !!}');
                    window.location = '{!! $schema !!}';
                } else {
                    //alert("error : " + e.statusText);
                    //window.location = '{!! $playstore_url2 !!}';
                }
            }
        });
        @endif
    });



</script>
<!-- END Reminder Footer -->
@include($dir.'footer')
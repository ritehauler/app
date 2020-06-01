<?php
include getcwd()."/node/Mobile_Detect.php";

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
$setting = $setting_model->getBy("key","og_schema_share");
$og_schema_share = $setting ? $setting->value : FALSE;

// app related
$app_name = $config->site_name;
$app_title = $config->site_slogan;
$app_description = $config->app_description;
$app_logo = \URL::to(config('constants.LOGO_PATH').$config->site_logo);

// stores url
$appstore_url = $ios_app_url ? $ios_app_url : "";
$appstore_id = $ios_store_id ? $ios_store_id : "";
$appstore_url2 = "itms-apps://itunes.apple.com/app/id".$appstore_id;

$playstore_url = $android_app_url ? $android_app_url : "";
$playstore_keystore = $android_store_id ? $android_store_id : "";
$playstore_url2 = "market://details?id=".$playstore_keystore;



// mobile schema
$schema = $og_schema_share ? $og_schema_share : "";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $app_title; ?></title>
<!-- opengraph tags -->
<meta property="og:site_name" content="<?php echo $app_name; ?>"/>
<meta property="og:url" content="<?php echo url('/')."/"; ?>og/share" />
<meta property="og:title" content="<?php echo $app_title; ?>" />
<meta property="og:description" content="<?php echo $app_description; ?>" />
<meta property="og:image" content="<?php echo $app_logo; ?>" />
<!-- Deeplinking Android -->
<meta property="al:android:url" content="<?php echo $schema; ?>" />
<meta property="al:android:package" content="<?php echo $playstore_keystore; ?>" />
<meta property="al:android:app_name" content="<?php echo $app_name; ?>" />
<!-- Deeplinking iOS -->
<meta property="al:ios:url" content="<?php echo $schema; ?>" />
<meta property="al:ios:app_store_id" content="<?php echo $appstore_id; ?>" />
<meta property="al:ios:app_name" content="<?php echo $app_name; ?>" />
<meta property="al:web:should_fallback" content="false" />
<meta property="og:type" content="website" />
<style>
body {
	margin: 0;
	padding: 0;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 16px;
	line-height: 18px;
	color: #433734;
}
.share {
 background: url(<?php echo url("/")."/public/";
?>images/bg.jpg) repeat;
	max-width: 640px;
	margin: auto;
}
.share a {
	text-decoration: none;
}
.share h1 {
	font-size: 24px;
}
.share .photo img {
	display: block;
	max-width: 100%;
	width: 100%;
	height: auto;
}
.share .photo video {
	display: block;
	max-width: 100%;
	width: 100%;
	height: auto;
}
.share .content {
	padding: 20px;
}
.share .content .btn-ios {
	background: #515151;
	padding: 10px 20px;
	color: #FFF;
	border-radius: 10px;
	font-size: 15px;
	height: 31px;
	line-height: 31px;
	display: inline-block;
	float: left;
}
.share .content .btn-ios img {
	margin-right: 10px;
	width: 28px;
	float: left;
}
.share .content .btn-ios:hover {
	background: #484848;
}
.share .content .btn-google {
	background: #B3C833;
	padding: 10px 20px;
	color: #FFF;
	border-radius: 10px;
	font-size: 15px;
	margin-left: 15px;
	height: 31px;
	line-height: 31px;
	display: inline-block;
	float: left;
}
.share .content .btn-google img {
	margin-right: 10px;
	width: 28px;
	float: left;
}
.share .content .btn-google:hover {
	background: #a5b929;
}
.btn-block {
	overflow: hidden;
}
</style>
</head>

<body>
<script type="text/javascript">
<?php $detect = new Mobile_Detect;?>
             
window.alert = null; // disable alerts
alert = null;
             
<?php if($detect->isiOS()) : ?>
             
window.location = '<?php echo $schema;?>';
setTimeout(function() {
	window.location = '<?php echo $appstore_url2;?>';
},100);

<?php endif; ?>

<?php if($detect->isAndroidOS() ):?>
window.location = '<?php echo $schema;?>';
setTimeout(function() {
	window.location = '<?php echo $playstore_url2;?>';
},500);
             
<?php endif; ?>
	window.location = "<?php echo url(); ?>"

</script>
<div class="share">
  <div class="photo">
    <img src="<?php echo $app_logo; ?>" alt="<?php echo $app_name; ?>" />
  </div>
  <div class="content">
    <h1><?php echo $app_name; ?></h1>
    <p><?php echo $app_description; ?></p>
    <div class="btn-block"> <a href="<?php echo $appstore_url; ?>" class="btn-ios"> <img src="<?php echo url("/")."/public/"; ?>images/ios.png" alt="Apple Store" /> AppStore </a> <a href="<?php echo $playstore_url; ?>" class="btn-google"> <img src="<?php echo url("/")."/public/"; ?>images/play.png" alt="Play Store" /> Google Play </a> </div>
  </div>
</div>
</body>
</html>
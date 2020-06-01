<?php
include $_SERVER['DOCUMENT_ROOT']."/node/Mobile_Detect.php";

$vid_raw = "thumb/media/300x300/";
$vid_replace = "mask/media/video/";
// stores url
$appstore_url = "https://itunes.apple.com/us/app/r4-rods-rifles-rest-relaxation/id994144817?ls=1&mt=8";
$appstore_id = "994144817";
$appstore_url2 = "itms-apps://itunes.apple.com/app/id".$appstore_id;

$playstore_url = "https://play.google.com/store/apps/details?id=com.r4enterprisesllc.R4";
$playstore_keystore = "com.r4enterprisesllc.R4";


// mobile schema
//$schema = "r4outdoors://media?media_id=".$media->media_id;
$schema = "fb1014060485281831://media?media_id=".$media->media_id;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $media->description; ?></title>
<!-- Deeplinking Android -->
<meta property="al:android:url" content="<?php echo $schema; ?>" />
<meta property="al:android:package" content="<?php echo $playstore_keystore; ?>" />
<meta property="al:android:app_name" content="R4" />
<!-- Deeplinking iOS -->
<meta property="al:ios:url" content="<?php echo $schema; ?>" />
<meta property="al:ios:app_store_id" content="<?php echo $appstore_id; ?>" />
<meta property="al:ios:app_name" content="R4" />
<meta property="al:web:should_fallback" content="false" />
<meta property="og:type" content="website" />
<!-- opengraph tags -->
<meta property="og:site_name" content="R4"/>
<!--<meta property="og:url" content="<?php echo url('/')."/"; ?>og/media/?media_id=<?php echo $media->media_id;?>" />-->
<meta property="og:title" content="<?php echo $media->description; ?>" />
<meta property="og:description" content="<?php echo $media->address; ?>" />
<?php if($media->type == "video") :?>
<meta property="og:image" content="<?php echo str_replace($vid_raw,$vid_replace,$media->thumb); ?>" />
<meta property="og:video" content="<?php echo $media->video; ?>" />
<meta property="og:video:height" content="405" />
<meta property="og:video:width" content="720" />
<meta property="og:video:type" content="video" />
<?php else: ?>
<meta property="og:image" content="<?php echo $media->image; ?>" />
<?php endif; ?>
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
<?php $detect = new Mobile_Detect; ?>

<?php if($detect->isiOS()) : ?>
window.location = '<?php echo $schema;?>';
setTimeout(function() {
	window.location = '<?php echo $appstore_url2;?>';
},500);
<?php endif; ?>

<?php if($detect->isAndroidOS() ):?>
window.location = '<?php echo $schema;?>';
setTimeout(function() {
	window.location = '<?php echo $playstore_url;?>';
},500);
<?php endif; ?>
</script>
<div class="share">
  <div class="photo">
    <?php if($media->type == "video"): ?>
    <video controls  poster="<?php echo $media->thumb; ?>">
      <source src="<?php echo $media->video; ?>" type="video/mp4">
      Your browser does not support the video tag or the file format of this video. <a href="http://www.webestools.com/">http://www.webestools.com/</a> </video>
    <!--<p><b>Note:</b> The .ogg fileformat is not supported in IE and Safari.</p>-->
    <?php else: ?>
    <img src="<?php echo $media->image; ?>" alt="Photo" />
    <?php endif; ?>
  </div>
  <div class="content">
    <h1><?php echo $media->description; ?></h1>
    <p><?php echo $media->address; ?></p>
    <div class="btn-block"> <a href="<?php echo $appstore_url; ?>" class="btn-ios"> <img src="<?php echo url("/")."/public/"; ?>images/ios.png" alt="Apple Store" /> AppStore </a> <a href="<?php echo $playstore_url; ?>" class="btn-google"> <img src="<?php echo url("/")."/public/"; ?>images/play.png" alt="Play Store" /> Google Play </a> </div>
  </div>
</div>
</body>
</html>
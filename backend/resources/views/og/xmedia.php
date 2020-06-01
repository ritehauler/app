<!DOCTYPE html>
<html>
<head>
<meta property="og:site_name" content="Outdoors App"/>
<meta property="og:url" content="<?php echo url('/')."/"; ?>og/media/?media_id=<?php echo $media->media_id;?>" />
<meta property="og:image" content="<?php echo $media->image; ?>" />
<meta property="og:title" content="<?php echo $media->description; ?>" />
<meta property="og:description" content="<?php echo $media->address; ?>" />
<?php if($media->type == "video") :?>
<meta property="og:video" content="<?php echo $media->video; ?>" />
<meta property="og:video:height" content="405" />
<meta property="og:video:width" content="720" />
<meta property="og:video:type" content="video" />
<?php endif; ?>
<style>
.cont_store a img {
	float:left;	
}
</style>
</head>
<body>
<?php if($media->type == "video"): ?>
<video width="720" height="405" controls  poster="<?php echo $media->thumb; ?>">
  <source src="<?php echo $media->video; ?>" type="video/mp4">
  Your browser does not support the video tag or the file format of this video. <a href="http://www.webestools.com/">http://www.webestools.com/</a> </video>
<p><b>Note:</b> The .ogg fileformat is not supported in IE and Safari.</p>
<?php else: ?>
<img src="<?php echo $media->image; ?>" height="405" width="720" />
<?php endif; ?>
<br />
<div class="cont_store">
<a href="#"><img src="<?php echo url("/")."/public/"; ?>images/appstore.png" /></a><span style="display:block; width:100px;"></span><a href="#"><img src="<?php echo url("/")."/public/"; ?>images/googleplay.png" /></a>
</div>
</body>
</html>
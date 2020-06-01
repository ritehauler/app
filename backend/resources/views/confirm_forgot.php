<!DOCTYPE html>
<html>
<head>
<base href="<?php echo url('/')."/"; ?>" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<!-- Meta, title, CSS, favicons, etc. -->
<meta charset="utf-8">
<title><?php echo APP_NAME; ?>: Reset Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Font CSS  -->
<link rel="stylesheet" type="text/css" href="<?php echo "public/".DIR_API; ?>asset/css.css">

<!-- Core CSS  -->
<link rel="stylesheet" type="text/css" href="<?php echo "public/".DIR_API; ?>asset/bootstrap.css">
<link rel="stylesheet" type="text/css" href="<?php echo "public/".DIR_API; ?>asset/font-awesome.css">
<link rel="stylesheet" type="text/css" href="<?php echo "public/".DIR_API; ?>asset/glyphicons.css">

<!-- Plugin CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo "public/".DIR_API; ?>asset/chosen.css">

<!-- Theme CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo "public/".DIR_API; ?>asset/theme.css">
<link rel="stylesheet" type="text/css" href="<?php echo "public/".DIR_API; ?>asset/pages.css">
<link rel="stylesheet" type="text/css" href="<?php echo "public/".DIR_API; ?>asset/plugins.css">
<link rel="stylesheet" type="text/css" href="<?php echo "public/".DIR_API; ?>asset/responsive.css">
<style>
pre {
	background-color: ghostwhite;
	border: 1px solid silver;
	padding: 10px 20px;
	margin: 20px;
}
.json-key {
	color: brown;
}
.json-value {
	color: navy;
}
.json-string {
	color: olive;
}
.pad-rgt {
	padding-right: 5%;
}
</style>
</head>
<body class="forms-page">
<!-- Start: Main -->
<div id="main"> 
  <!-- Start: Content -->
  <section>
    <div class="container">
      <div class="row">
        <div class="col-md-6 col-md-offset-2">
          <div class="row">
            <div class="col-md-16">
              <div class="panel">
                <form name="confirm_forgot" class="form-horizontal" role="form" method="post">
                  <div class="panel-heading">
                    <div class="panel-title"> <i class="fa fa-pencil"></i> <?php echo $action_title; ?> </div>
                  </div>
                  <div id="main_body">
                  <?php if($user !== FALSE): ?>
                  <div class="panel-body">
                    <div class="form-group">
                      <label class="col-md-3 control-label">New Password :</label>
                      <div class="col-md-9">
                        <input type="password" name="password" class="form-control" placeholder="New Password" value="" >
                      </div>
                    </div>
                  </div>
                  <div class="panel-body">
                    <div class="form-group">
                      <label class="col-md-3 control-label">Confirm Password :</label>
                      <div class="col-md-9">
                        <input type="password" name="c_password" class="form-control" placeholder="Confirm Password" value="" >
                      </div>
                    </div>
                  </div>
                  <div class="form-group pad-rgt">
                    <input class="submit btn btn-blue pull-right" id="submit_btn" value="Submit" type="submit">
                  </div>
                  <?php else: ?>
                  <br />
                  <h4 align="center">Invalid or Expired Confirmation Link Provided</h4>
                  <?php endif; ?>
                  <input type="hidden" name="email" value="<?php echo $email; ?>" >
                  <input type="hidden" name="hash" value="<?php echo $hash; ?>" >
                  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                  <input type="hidden" name="process_form" value="1" />
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- End: Content --> 
</div>
<!-- End: Main --> 

<!-- Core Javascript - via CDN --> 
<script type="text/javascript" src="<?php echo "public/".DIR_API; ?>asset/jquery.js"></script> 
<script type="text/javascript" src="<?php echo "public/".DIR_API; ?>asset/jquery-ui.js"></script> 
<script type="text/javascript" src="<?php echo "public/".DIR_API; ?>asset/bootstrap.js"></script> 
<script type="text/javascript" src="<?php echo "public/".DIR_API; ?>asset/chosen.js"></script> 
<script type="text/javascript">
var app_rel_url = "/<?php echo ADD_PATH.APP_ALIAS.(DO_URL_REWRITE === TRUE ? "" : "index.php/");?>";
var app_admin_rel_url = "/<?php echo ADD_PATH.APP_ALIAS.(DO_URL_REWRITE === TRUE ? "" : "index.php/");?><?php echo DIR_ADMIN;?>";
var app_rel_path = "/<?php echo ADD_PATH.APP_ALIAS;?>";
var base_admin_url = "<?php echo url('/')."/".DIR_ADMIN;?>";
var pls_wait_txt = "Please wait...";
</script> 
<script type="text/javascript" src="<?php echo "public/"; ?>js/core.js?v=<?php echo VERSION;?>"></script> 
<script type="text/javascript">
$(function() {
	<?php if($user !== FALSE): ?>
	$("form[name=confirm_forgot]").submit(function(e) {
		e.preventDefault();
		jsonValidate("confirm_forgot",$("form[name=confirm_forgot]"));
	});
	<?php endif; ?>
});
</script>
</body>
</html>
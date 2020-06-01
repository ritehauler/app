<!DOCTYPE html>
<html>

<head>
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <title>{!! $_meta->site_name !!}</title>
    <meta name="keywords" content="{!! $_meta->site_meta_keywords !!}"/>
    <meta name="description" content="{!! $_meta->site_meta_keywords !!}">
    <meta name="author" content="{!! $_meta->site_author !!}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font CSS (Via CDN) -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">

    <!-- Marterial CSS -->
    <link rel="stylesheet" type="text/css"
          href="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/fonts/material-design-icons/css/material-design-iconic-font.min.css"/><!--[if lt IE 9]>

    <!-- Theme CSS -->
    <link rel="stylesheet" type="text/css"
          href="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/skin/default_skin/css/theme.css">

    <!-- Admin Forms CSS -->
    <link rel="stylesheet" type="text/css"
          href="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/admin-tools/admin-forms/css/admin-forms.css">

    <!-- Moh CSS -->
    <link rel="stylesheet" type="text/css"
          href="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/skin/default_skin/css/moh.css">
		  
    <link rel="stylesheet" type="text/css" href="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/vendor/plugins/slick/slick-login.css">

    <!-- Favicon -->
    <link rel="shortcut icon"
          href="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/img/favicon.ico?v=0.0">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    @if(IS_CAPTCHA == 1)
        <script src='https://www.google.com/recaptcha/api.js'></script>
        @endif

    <!-- Dynamic CSS -->
    <?php include(config("panel.DIR_PANEL_RESOURCE") . "assets/skin/color.blade.php");?>

</head>

<body class="external-page external-alt sb-l-c sb-r-c ">
<!-- New Login Design Start -->
<div id="main">
	<div class="login-theme02 admin-form" id="login-form">
		<div class="login-wrap">
			<div class="left-panel light-color">
				<div class="logo-holder"><img src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/img/logos/admin-logo-white.png" class="logo" width="250px"></div>
				<div class="signup-carousel slider-carousel">
					<div class="f-s-slide slide1" style="background:url('{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/client/slider-signin01.jpg');">
						<div class="gradient-overlay"></div>
						<div class="f-s-wrap">
							<div class="f-s-cont">
								<h2>Keep Calm, Try Managly</h2>
								<p>World is a land of opportunities and with advent of digital world we have increased our chances to harness their opportunities and smoothen the edges of their rough diamonds.</p>
							</div>
						</div>    
					</div>
					<div class="f-s-slide slide2" style="background:url('{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/client/slider-signin02.jpg');">
						<div class="gradient-overlay"></div>
						<div class="f-s-wrap">
							<div class="f-s-cont">
								<h2>3 Ways to Increase Transparency with Remote Teams</h2>
								<p>World is a land of opportunities and with advent of digital world we have increased our chances to harness their opportunities and smoothen the edges of their rough diamonds.</p>
							</div>
						</div>    
					</div>
					<div class="f-s-slide slide3" style="background:url('{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/client/slider-signin03.jpg');">
						<div class="gradient-overlay"></div>
						<div class="f-s-wrap">
							<div class="f-s-cont">
								<h2>Why focus more on building team than company</h2>
								<p>World is a land of opportunities and with advent of digital world we have increased our chances to harness their opportunities and smoothen the edges of their rough diamonds.</p>
							</div>
						</div>    
					</div>
				</div>
			</div>
			<div class="right-panel light-color">
				<div class="d-v-table">
					<div class="d-v-cell">
						<div class="tab-content text-center">
							<div class="login-form tab-pane fade in active text-center" role="tabpanel" id="login-holder">
								<h2 class="login-header">{!! trans('panel.sign_in') !!}</h2>
								<p class="sub-text mb40">{!! trans('panel.enter_your_login_details_below') !!}</p>
								
								<!-- Login Panel/Form -->
								@include(config('panel.DIR').'flash_message')
								<form method="post" action="" class="login-area" name="signin">
									<div class="section mb15">
										<label for="email" class="field prepend-icon">
											<input type="text" name="email" id="email" class="gui-input" placeholder="{!! trans('panel.email') !!}" autocomplete="off">
											<label for="email" class="field-icon"> <i class="fa fa-user"></i> </label>
											<div id="error_msg_email" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
										</label>
									</div>
									<div class="section mb20">
										<label for="password" class="field prepend-icon">
											<input type="password" name="password" id="password" class="gui-input" placeholder="{!! trans('panel.password') !!}" autocomplete="off">
											<label for="password" class="field-icon"> <i class="fa fa-lock"></i> </label>
											<div id="error_msg_password" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
										</label>
									</div>
									<div class="mb30">
										<button type="submit" class="button btn-x-wide btn-theme">{!! trans('panel.sign_in') !!}</button>
									</div>
									<div class="checkbox-custom checkbox-theme checkbox-system mb5 pull-left">
										<input type="checkbox" id="login-remember-me" name="remember" value="1">
										<label for="login-remember-me" class="field-icon"> Remember Me?</label>
										<input type="hidden" id="remember_login_token" name="remember_login_token" value="{!! $remember_login_token !!}"/>
									</div>		
									<div class="footer-links pull-right">
										<p><a href="#forgot-holder" data-toggle="tab">{!! trans('panel.forgot_your_password_q') !!}</a></p>
									</div>  
									
									<input type="hidden" name="entity_type" value=""/>
									<input type="hidden" name="post_login" value="1"/>
									<input type="hidden" name="_token" value="{!! csrf_token() !!}"/>
								</form>
								
								<!-- Registration Links -->
								
								  
							</div>
							<div class="forgor-form tab-pane fade" role="tabpanel" id="forgot-holder">
								<h2 class="login-header">{!! trans('panel.forgot_password') !!}</h2>
								<p class="sub-text mb40">{!! trans('panel.enter_your_login_details_below') !!}</p>
								<form method="post" action=""class="forgot-area" name="forgot">
									<div class="section">
										<label for="email" class="field prepend-icon">
											<input type="text" name="email" id="email" class="gui-input" placeholder="{!! trans('panel.email') !!}" autocomplete="off">
											<label for="email" class="field-icon"> <i class="fa fa-user"></i> </label>
											<div id="error_msg_email" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
										</label>
									</div>
									@if(IS_CAPTCHA == 1)
										<div class="section">
											 <div class="g-recaptcha" data-sitekey="{!! CAPTCHA_SITE_KEY !!}"></div>
											<div id="error_msg_captcha" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
										</div>
									@endif
									<div class="mb30">
										<button type="submit" class="button btn-x-wide btn-theme">{!! trans('panel.submit') !!}</button>
									</div>    
									<input type="hidden" name="entity_type" value=""/>
									<input type="hidden" name="post_forgot" value="1"/>
									<input type="hidden" name="_token" value="{!! csrf_token() !!}"/>
								</form>
								<!-- Registration Links -->
								<div class="footer-links">
									<!--<p>Already have an <a href="#login-holder" data-toggle="tab">Account?</a> </p>-->
									<p>{!! trans('panel.html_already_have_an_account_q') !!}</p>
								</div>
							</div>    
						</div>     
					</div>    
				</div>    
			</div>
		</div>
	</div>   
</div>

<!-- New Login Design End -->

<!-- jQuery -->
<script src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/vendor/jquery/jquery-1.11.1.min.js"></script>
<script src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/vendor/jquery/jquery_ui/jquery-ui.min.js"></script>
<script src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/vendor/plugins/slick/slick.js"></script>
<!-- Theme Javascript -->
<script src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/js/utility/utility.js"></script>
<script src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/js/demo/demo.js"></script>
<script src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/js/main.js"></script>
<!-- END: PAGE SCRIPTS -->
<script type="text/javascript">
    jQuery(document).ready(function () {
        "use strict";
        // Init Theme Core
        Core.init();
        // Init Demo JS
        Demo.init();

        // Init Common JS
        Common.init();

        $('.footer-links a').click(function (e) {
            e.preventDefault();
            $('a[href="' + $(this).attr('href') + '"]').tab('show');
        });

        $("form[name=signin]").submit(function(e) {
            e.preventDefault();
            Common.jsonValidate("", this);
        });

        var remember_login_token = $('#remember_login_token').attr('value');
        if(remember_login_token != ''){
            $('#login-remember-me').attr("checked",'checked');
        }

        $("form[name=forgot]").submit(function(e) {
            e.preventDefault();
            Common.jsonValidate("", this);
        });
		
		/* Slick Slider */  
		if($('div').hasClass('signup-carousel')){
			$(".signup-carousel").slick({
				dots: true,
				arrows: false,
				infinite: true,
				slidesToShow: 1,
				slidesToScroll: 1,
				fade: true,
				autoplay: true,
				autoplaySpeed:4500,
			});
		}

    });
</script>
</body>
</html>
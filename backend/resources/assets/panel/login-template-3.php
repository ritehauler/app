<!DOCTYPE html>
<html>

<head>
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <title>AdminDesigns - A Responsive HTML5 Admin UI Framework</title>
    <meta name="keywords" content="HTML5 Bootstrap 3 Admin Template UI Theme" />
    <meta name="description" content="AdminDesigns - A Responsive HTML5 Admin UI Framework">
    <meta name="author" content="AdminDesigns">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font CSS (Via CDN) -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">

    <!-- Marterial CSS -->
    <link rel="stylesheet" type="text/css" href="assets/fonts/material-design-icons/css/material-design-iconic-font.min.css"/><!--[if lt IE 9]>  

    <!-- Theme CSS -->
    <link rel="stylesheet" type="text/css" href="assets/skin/default_skin/css/theme.css">

    <!-- Admin Forms CSS -->
    <link rel="stylesheet" type="text/css" href="assets/admin-tools/admin-forms/css/admin-forms.css">

    <!-- Moh CSS -->
    <link rel="stylesheet" type="text/css" href="assets/skin/default_skin/css/moh.css">
    
    <!-- Slick.js CSS -->
    <link rel="stylesheet" type="text/css" href="vendor/plugins/slick/slick-login.css">

    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/img/favicon.ico">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    
    <!-- Dynamic CSS -->
    <?php require_once("assets/skin/color.php"); ?>
    
</head>

<body class="external-page external-alt sb-l-c sb-r-c">
        <!-- Start: Main -->
        <div id="main">
            <div class="login-theme03 admin-form" id="login-form">
                <div class="login-wrap">
                    <div class="left-panel dark-color">
                        <div class="logo-holder"><img src="assets/client/glimmer-icon.png" class="logo" width="80px"></div>
                        <div class="signup-carousel slider-carousel" style="background-color:#30b6e4;">
                            <div class="f-s-slide slide1">
                                <div class="d-v-table">
                                    <div class="d-v-cell">
                                        <div class="f-s-wrap">
                                            <div class="f-s-img">
                                                <div class="iphone_device device-holder">
                                                    <img src="assets/client/live-intentionally-app-1-1.jpg" class="img-responsive"/>
                                                </div>
                                            </div>                                        
                                            <div class="f-s-cont">
                                                <h2>Keep Calm, Try Managly</h2>
                                                <p>World is a land of opportunities and with advent of digital world we have increased our chances to harness.</p>
                                            </div>
                                        </div>
                                    </div>    
                                </div>    
                            </div>
                            <div class="f-s-slide slide2">
                                <div class="d-v-table">
                                    <div class="d-v-cell">
                                        <div class="f-s-wrap">
                                            <div class="f-s-img">
                                                <div class="iphone_device device-holder">
                                                    <img src="assets/client/live-intentionally-app-1-1.jpg" class="img-responsive"/>
                                                </div>
                                            </div>
                                            <div class="f-s-cont">
                                                <h2>Keep Calm, Try Managly</h2>
                                                <p>World is a land of opportunities and with advent of digital world we have increased our chances to harness.</p>
                                            </div>
                                        </div>
                                    </div>    
                                </div>     
                            </div>
                            <div class="f-s-slide slide3">
                                <div class="d-v-table">
                                    <div class="d-v-cell">
                                        <div class="f-s-wrap">
                                            <div class="f-s-img">
                                                <div class="iphone_device device-holder">
                                                    <img src="assets/client/live-intentionally-app-1-1.jpg" class="img-responsive"/>
                                                </div>
                                            </div>
                                            <div class="f-s-cont">
                                                <h2>Keep Calm, Try Managly</h2>
                                                <p>World is a land of opportunities and with advent of digital world we have increased our chances to harness.</p>
                                            </div>
                                        </div>
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
                                        <h2 class="login-header">Sing In</h2>
                                        <p class="sub-text mb40">Enter your login details below</p>
                                        <!-- Login Panel/Form -->
                                        <form method="post" action="" class="login-area">
                                            <div class="section mb15">
                                                <label for="username" class="field prepend-icon">
                                                    <input type="text" name="username" id="username" class="gui-input" placeholder="Username">
                                                    <label for="username" class="field-icon"> <i class="fa fa-user"></i> </label>
                                                </label>
                                            </div>
                                            <div class="section mb20">
                                                <label for="password" class="field prepend-icon">
                                                    <input type="password" name="password" id="password" class="gui-input" placeholder="Password">
                                                    <label for="password" class="field-icon"> <i class="fa fa-lock"></i> </label>
                                                </label>
                                            </div>
                                            <div class="mb30">
                                                <button type="submit" class="button btn-x-wide btn-theme">Sign In</button>
                                            </div>    
                                        </form>
                                        <!-- Registration Links -->
                                        <div class="footer-links">
                                            <p><a href="#forgot-holder" data-toggle="tab">Forgot your Password?</a> </p>
                                        </div>    
                                    </div>
                                    <div class="forgor-form tab-pane fade" role="tabpanel" id="forgot-holder">
                                        <h2 class="login-header">Forgot Password</h2>
                                        <p class="sub-text mb40">Enter your login details below</p>
                                        <form method="post" action=""class="forgot-area">
                                            <div class="section">
                                                <label for="username" class="field prepend-icon">
                                                    <input type="text" name="email" id="email" class="gui-input" placeholder="Email">
                                                    <label for="email" class="field-icon"> <i class="fa fa-user"></i> </label>
                                                </label>
                                            </div>
                                            <div class="mb30">
                                                <button type="submit" class="button btn-x-wide btn-theme">Sign In</button>
                                            </div>    
                                        </form>
                                        <!-- Registration Links -->
                                        <div class="footer-links">
                                            <p>Already have an <a href="#login-holder" data-toggle="tab">Account?</a> </p>
                                        </div>
                                    </div>    
                                </div>     
                            </div>    
                        </div>    
                    </div>
                </div>
            </div>   
        </div>
        <!-- BEGIN: PAGE SCRIPTS -->
        <!-- jQuery -->
        <script src="vendor/jquery/jquery-1.11.1.min.js"></script>
        <script src="vendor/jquery/jquery_ui/jquery-ui.min.js"></script>
        <!-- Slick Slider Plugin -->
        <script src="https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js"></script>
        <!-- Theme Javascript -->
        <script src="assets/js/utility/utility.js"></script>
        <script src="assets/js/demo/demo.js"></script>
        <script src="assets/js/main.js"></script>
        <!-- END: PAGE SCRIPTS -->
        <script type="text/javascript">
            jQuery(document).ready(function () {
                "use strict";
                // Init Theme Core      
                Core.init();
                // Init Demo JS
                Demo.init();
            
                $('.footer-links a').click(function (e) {
                    e.preventDefault();
                    $('a[href="' + $(this).attr('href') + '"]').tab('show');
                })
                /* Slick Slider */  
                
                
                var $slideContainter = $('.signup-carousel'),
                    $slider = $slideContainter.slick({
                        dots: true,
                        centerMode: true,
                        arrows: false,
                        infinite: true,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed:4500,
                        draggable: false,
                    }),
                    colorSettings = {
                        section: ['#30b6e4', '#99d56f','#df4944']
                    },
                    changeColors = function (slide) {
                        $slideContainter.css({backgroundColor: colorSettings.section[slide]}).fadeIn( 3000 );
                    };

                    // Initial call to set color
                    changeColors(0);

                    $slider.on('beforeChange', function(event, slick, currentSlide, nextSlide){
                       changeColors(nextSlide);
                    });

            });
        </script>
    </body>
</html>
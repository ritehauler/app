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

<body class="external-page external-alt sb-l-c sb-r-c dark-color">
        <!-- Start: Main -->
        <div id="main">
            <div class="admin-form theme-info mw400 a-middle login-theme01" id="login-form">
                <!-- Login Logo -->
                <div class="row table-layout mb30">
                    <a href="dashboard.html" title="Return to Dashboard"> 
                        <img src="assets/client/glimmer-icon.png" title="AdminDesigns Logo" class="center-block img-responsive" style="max-width: 120px;"> 
                    </a>
                </div>
                <div class="login-wrap tab-content text-center">
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
                                <button type="submit" class="button btn-x-wide btn-dark">Sign In</button>
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
                                <button type="submit" class="button btn-x-wide btn-dark">Sign In</button>
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
        <!-- BEGIN: PAGE SCRIPTS -->
        <!-- jQuery -->
        <script src="vendor/jquery/jquery-1.11.1.min.js"></script>
        <script src="vendor/jquery/jquery_ui/jquery-ui.min.js"></script>
    
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
            });
        </script>
    </body>
</html>
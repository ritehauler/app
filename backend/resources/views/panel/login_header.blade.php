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
          href="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/img/favicon.ico">

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
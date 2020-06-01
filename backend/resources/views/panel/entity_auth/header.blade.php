<!DOCTYPE html>
<!--[if IE 9]>         <html class="ie9 no-focus"> <![endif]-->
<!--[if gt IE 9]><!-->
<html class="no-focus">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<title>{!! $_meta->site_name !!}{!! isset($p_title) ? " :: ".$p_title : "" !!}</title>
<meta name="keyword" content="{!! $_meta->meta_keywords !!}">
<meta name="description" content="{!! $_meta->meta_description !!}">
<meta name="author" content="">
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">

<!-- Icons -->
<!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
<link rel="shortcut icon" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/favicon.png') !!}">
<link rel="icon" type="image/png" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/favicon-16x16.png') !!}" sizes="16x16">
<link rel="icon" type="image/png" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/favicon-32x32.png') !!}" sizes="32x32">
<link rel="icon" type="image/png" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/favicon-96x96.png') !!}" sizes="96x96">
<link rel="icon" type="image/png" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/favicon-160x160.png') !!}" sizes="160x160">
<link rel="icon" type="image/png" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/favicon-192x192.png') !!}" sizes="192x192">
<link rel="apple-touch-icon" sizes="57x57" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/apple-touch-icon-57x57.png') !!}">
<link rel="apple-touch-icon" sizes="60x60" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/apple-touch-icon-60x60.png') !!}">
<link rel="apple-touch-icon" sizes="72x72" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/apple-touch-icon-72x72.png') !!}">
<link rel="apple-touch-icon" sizes="76x76" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/apple-touch-icon-76x76.png') !!}">
<link rel="apple-touch-icon" sizes="114x114" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/apple-touch-icon-114x114.png') !!}">
<link rel="apple-touch-icon" sizes="120x120" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/apple-touch-icon-120x120.png') !!}">
<link rel="apple-touch-icon" sizes="144x144" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/apple-touch-icon-144x144.png') !!}">
<link rel="apple-touch-icon" sizes="152x152" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/apple-touch-icon-152x152.png') !!}">
<link rel="apple-touch-icon" sizes="180x180" href="{!! URL::to(config('constants.ADMIN_IMG_URL').'favicons/apple-touch-icon-180x180.png') !!}">
<link rel="stylesheet" href="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css">
<link rel="stylesheet" href="{!! URL::to(config('constants.ADMIN_JS_URL')) !!}/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">

<!-- END Icons -->

<!-- Stylesheets -->
<!-- Web fonts -->
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">
<!-- OneUI CSS framework -->
<link rel="stylesheet" id="css-main" href="{!! URL::to(config('constants.ADMIN_CSS_URL').'clinique.css') !!}">
<link rel="stylesheet" id="css-main" href="{!! URL::to(config('constants.ADMIN_CSS_URL').'custom.css') !!}">
<link rel="stylesheet" id="css-main" href="{!! URL::to(config('constants.ADMIN_CSS_URL').'jquery-ui.css') !!}">

<!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
<!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/flat.min.css"> -->
<script src="{!! URL::to(config('constants.ADMIN_JS_URL').'jquery.js') !!}"></script>
<!--<link href="{!! URL::to(config('constants.JS_URL').'pace/themes/pace-theme-barber-shop.css') !!}" rel="stylesheet" />
<script src="{!! URL::to(config('constants.JS_URL').'pace/pace.js') !!}"></script>-->
<!-- END Stylesheets -->
<style>
/* table header sorting images paddings */
table.dataTable .css-checkbox.css-checkbox-sm {
    margin: 0;
}
</style>
</head>
<body>
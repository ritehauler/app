<?php
    /* Theme Color */
    $themeColor = "#ff825d";
    $pushColor = "#ffffff";
    $darkColor = "#ffffff";
    $lightColor = "#000000";
    
    /* Dark Color */
    $DarkColor = "#3b3f4f";
?>
<style type="text/css">
    /*====================== Text Color ====================== */
    
    /*  Theme Color  */
    .text-primary, .panel-menu .nav li a, #sidebar_left.sidebar-light .sidebar-menu > li.active > a > span:nth-child(1),.pagination > li > a, .pagination > li > span, .sb-l-m .navbar-right li a span.mdi-settings, .navbar[class*='bg-'] .nav > li.open > a span.mdi-notifications, .text-system, ul.side-nav-tab li.active a{
        color: <?php echo $themeColor; ?>!important;
    }  
    
    body a, body a:hover, #sidebar_left.sidebar-light .sidebar-widget.author-widget .media-links a,
    .admin-form.theme-info .gui-input:focus ~ .field-icon i, .admin-form.theme-info .gui-textarea:focus ~ .field-icon i{
        color: <?php echo $themeColor; ?>;
    } 
    
    /*  White Color  */
    .pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus, table tbody tr:hover .ticon{
        color: <?php echo $pushColor; ?>!important;
    } 
    
    .external-page.light-color p,
    .external-page.light-color a,
    .external-page.light-color b,
    .external-page.light-color span,
    .external-page.light-color h1,
    .external-page.light-color h2,
    .external-page.light-color h3,
    .external-page.light-color h4,
    .external-page.light-color h5,
    .external-page.light-color h6{    
        color: #000000!important;
    }
    .external-page.dark-color p,
    .external-page.dark-color a,
    .external-page.dark-color b,
    .external-page.dark-color span,
    .external-page.dark-color h1,
    .external-page.dark-color h2,
    .external-page.dark-color h3,
    .external-page.dark-color h4,
    .external-page.dark-color h5,
    .external-page.dark-color h6{
        color: #ffffff!important;
    }
    .external-page.dark-color a:active, .external-page.dark-color a:focus, .external-page.dark-color a:hover{
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=70)";
        filter: alpha(opacity=70);
        -moz-opacity: 0.7;
        -khtml-opacity: 0.7;
        opacity: 0.7;
    }
    
    /*====================== Btn Color ====================== */
    /*  Theme Color  */
    .btn-theme {
        background-color: <?php echo $themeColor; ?>!important;
    }
    .btn-theme span.ladda-label, .btn-theme{
        color: #ffffff!important;
    }
    .btn-theme:hover,
    .btn-theme:focus,
    .btn-theme:active{
        filter: brightness(108%);
        -webkit-filter: brightness(108%);
        -moz-filter: brightness(108%);
        -o-filter: brightness(108%);
        -ms-filter: brightness(108%);
    }
    
    .pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus, .bootstrap-datetimepicker-widget td.active {
        background-color:  <?php echo $themeColor; ?>!important;
    }
    
    /*====================== Background Color ====================== */
    /*  Theme Color  */
    .c-logo-holder, header .indicator, .daterangepicker td.active, .btn-success, table tbody tr:hover .ticon, div.switch.switch-success input:checked + label, div.jsoneditor-menu{
        background-color: <?php echo $themeColor; ?>!important;
    }
    
    /* Sigup Process */
    body.external-page.external-alt #main{
        background-color: <?php echo $themeColor; ?>!important;
    }
    
    /*  BG Theme  */
    .bg-theme{
        background-color: <?php echo $themeColor; ?>!important;
    }
    
    /*====================== Border Color ====================== */
    /*  Theme Color  */
    .pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus, .form-control:focus, .daterangepicker td.active, table tbody tr:hover .ticon, div.jsoneditor-menu,
    .admin-form.theme-info .gui-input:focus, .admin-form.theme-info .gui-textarea:focus, .admin-form.theme-info .select > select:focus, .admin-form.theme-info .select-multiple select:focus{
        border-color: <?php echo $themeColor; ?>!important;
    }
    
    /*====================== Differnt Color ====================== */
    
    /* ------- Dark Color -------- */
    
    /*  BG Color  */
    .btn-dark{
        background-color: <?php echo $DarkColor; ?>!important;
    }
    
    
    
</style>    
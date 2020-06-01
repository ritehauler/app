<?php
// stores url
$appstore_url = "https://itunes.apple.com/us/app/r4-rods-rifles-rest-relaxation/id994144817?ls=1&mt=8";
$playstore_url = "https://play.google.com/store/apps/details?id=com.r4enterprisesllc.R4";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="author" content="">
<title>R4 Outdoors</title>

<!-- CSS -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Droid+Serif:400,400italic|Open+Sans:300,200|Montserrat:700,400|Varela+Round">

<!-- Animate CSS -->
<link rel="stylesheet" href="{{\URL::to('/')}}/public/css/animate.min.css">

<!-- Slick CSS -->
<link rel="stylesheet" href="{{\URL::to('/')}}/public/js/slick/slick.css">
<link rel="stylesheet" href="{{\URL::to('/')}}/public/js/slick/slick-theme.css">

<!-- Custom CSS -->
<link rel="stylesheet" href="{{\URL::to('/')}}/public/css/style-v2.css">
<link rel="stylesheet" href="{{\URL::to('/')}}/public/css/style-responsive-v2.css">
<script src="{{\URL::to('/')}}/public/js/modernizr.js"></script><!-- Modernizr -->

<link rel="icon" type="image/png" href="{{\URL::to('/')}}/public/images/favicon.png">
</head>

<body>

<!--
		==========================
		== BEGIN HEADER CONTENT ==
		==========================
		-->
<header id="main-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-12"> <a href="#" class="logo"><img src="{{\URL::to('/')}}/public/images/logo-r4.png" alt="Outdoor Logo"></a> <!-- Your Logo --> 
        
      </div>
      <!--/ .col-lg-12 --> 
    </div>
    <!--/ .row --> 
  </div>
  <!--/ .container --> 
</header>
<!--
		=========================
		==/ END HEADER CONTENT ==
		=========================
		--> 

<!--
		========================
		== BEGIN MAIN CONTENT ==
		========================
		-->
<main id="main-content" class="app-layout"> <!-- margin value is the height of your footer --> 
  
  <!--
			========================
			== BEGIN HERO SECTION ==
			========================
			-->
  <section id="hero" class="breaking" data-stellar-background-ratio="0.5" data-stellar-vertical-offset="50">
    <div class="container"> 
      
      <!-- BEGIN Hero Content -->
      <div class="hero-content row">
        <div class="col-lg-12 col-md-12 col-xs-12">
          <h1 class="all-caps margin-bot-15">R4 Outdoors - Rods, Rifles, Rest and Relaxation</h1>
          <ul class="inline-cta" data-sr="enter bottom over 1s and move 100px wait 0.6s">
            <li> <a href="<?php echo $appstore_url; ?>" class="store-btn"><img src="{{\URL::to('/')}}/public/images/appstore-btn.png" alt="Appstore"/></a> </li>
            <li> <a href="<?php echo $playstore_url; ?>" class="store-btn"><img src="{{\URL::to('/')}}/public/images/playstore-btn.png" alt="Playstore"/></a> </li>
          </ul>
          <!--/ .inline-cta --> 
        </div>
        <!--/ .hero-app-content-right -->
        
        <div class="col-lg-12 col-md-12 col-xs-12">
          <div class="hero-mockup"> <img src="{{\URL::to('/')}}/public/images/phone-mock.png" alt="iPhone Mockup" /> </div>
          <!--/ .hero-mockup --> 
        </div>
        <!--/ .hero-app-content-left --> 
        
      </div>
      <!--/ .row --> 
      <!-- END Hero Content --> 
      
    </div>
    <!--/ .container --> 
    
  </section>
  <!--
			=======================
			==/ END HERO SECTION ==
			=======================
			--> 
  
  <!--
			===============================
			== BEGIN SCREENSHOTS SECTION ==
			===============================
			-->
  <section id="screenshots">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 centered">
          <h4 class="section-heading">Connect with current friends, make new friends, and create your own social network within the app and even share texts. Like the photos and videos posted by others and vote on your favorites each day.</h4>
        </div>
        <!--/ .col-lg-8 -->
        
        <div class="clearfix"></div>
      </div>
      <!--/ .row --> 
    </div>
    <!--/ .container -->
    
    <div class="container-full">
      <div class="row">
        <div class="col-lg-12 no-padding"> 
          
          <!--BEGIN Screenshots Carousel -->
          <div class="app-carousel">
            <div class="carousel-item"> <img src="{{\URL::to('/')}}/public/images/app-screen1.jpg" alt="Screenshot" /> </div>
            <!--/ .carousel-item -->
            
            <div class="carousel-item"> <img src="{{\URL::to('/')}}/public/images/app-screen2.jpg" alt="Screenshot" /> </div>
            <!--/ .carousel-item -->
            
            <div class="carousel-item"> <img src="{{\URL::to('/')}}/public/images/app-screen3.jpg" alt="Screenshot" /> </div>
            <!--/ .carousel-item -->
            
            <div class="carousel-item"> <img src="{{\URL::to('/')}}/public/images/app-screen4.jpg" alt="Screenshot" /> </div>
            <!--/ .carousel-item -->
            
            <div class="carousel-item"> <img src="{{\URL::to('/')}}/public/images/app-screen5.jpg" alt="Screenshot" /> </div>
            <!--/ .carousel-item -->
            
            <div class="carousel-item"> <img src="{{\URL::to('/')}}/public/images/app-screen6.jpg" alt="Screenshot" /> </div>
            <!--/ .carousel-item -->
            
            <!-- <div class="carousel-item"> <img src="{{\URL::to('/')}}/public/images/app-screen7.jpg" alt="Screenshot" /> </div>-->
            <!--/ .carousel-item --> 
            
          </div>
          <!--/ . app-carousel -->
          
          <div class="phone-frame"> <img src="{{\URL::to('/')}}/public/images/iphone-app-frame.png" alt="iPhone Frame"/> </div>
          <!--/ .phone-frame --> 
          <!--/ END Screenshots Carousel --> 
          
        </div>
        <!--/ .col-lg-12 --> 
        
      </div>
      <!--/ .row --> 
    </div>
    <!--/ .container-full --> 
    
  </section>
  <!--
			==============================
			==/ END SCREENSHOTS SECTION ==
			==============================
			--> 
  
  <!--
			==========================
			== BEGIN WHY US SECTION ==
			==========================
			-->
  <section id="why-us">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-sm-offset-0 col-xs-8 col-xs-offset-2">
          <div class="half-phone-mockup"> <img class="opacity-one" src="{{\URL::to('/')}}/public/images/half-iphone-side-right.png" alt="iPhone Side 1" data-sr="enter bottom over 1s and move 200px wait 0.3s"/> </div>
        </div>
        <!--/ .col-lg-6 -->
        
        <div class="col-lg-6 col-md-6">
          <div class="why-us-content">
            <p>The R4 Outdoors app contains 4 categories to allow you to go directly to your area of interest. Once in that category, you can upload old photos from your phone, or take a new photo or 10 second video of your newest catch, kill, camping trip, hiking experience or gun use at the range. Others can share their experiences and view what you have posted and vote on them!</p>
          </div>
          <!--/ .why-us-content --> 
        </div>
        <!--/ .col-lg-6 --> 
      </div>
      <!--/ .row --> 
    </div>
    <!--/ .container --> 
  </section>
  <!--
			=========================
			==/ END WHY US SECTION ==
			=========================
			--> 
  
  <!--
			============================
			== BEGIN WHY US 2 SECTION ==
			============================
			-->
  <section id="why-us-2" class="gray-bg">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-md-6">
          <div class="why-us-content">
            <p>Manage your own profile, check messages, follow other users and set custom notification sounds for your buddies!</p>
          </div>
          <!--/ .why-us-content --> 
        </div>
        <!--/ .col-lg-6 -->
        
        <div class="col-lg-6 col-md-6 col-sm-12 col-sm-offset-0 col-xs-8 col-xs-offset-2">
          <div class="half-phone-mockup zero-bottom"> <img class="opacity-one" src="{{\URL::to('/')}}/public/images/half-iphone-side-left.png" alt="iPhone Side 2" data-sr="enter bottom over 1s and move 200px wait 0.3s"/> </div>
        </div>
        <!--/ .col-lg-6 --> 
      </div>
      <!--/ .row --> 
    </div>
    <!--/ .container --> 
  </section>
  <!--
			============================
			==/ END WHY US 2  SECTION ==
			============================
			--> 
  
</main>
<!--/ #main-content --> 
<!--
		=======================
		==/ END MAIN CONTENT ==
		=======================
		--> 

<!--
		==========================
		== BEGIN FOOTER CONTENT ==
		==========================
		-->
<footer id="main-footer" class="app-layout centered">
  <div class="container">
    <div class="row">
      <div class="footer-content col-lg-12 centered"> <img class="margin-bot-40" src="{{\URL::to('/')}}/public/images/footer-logo.png" alt="Footer Logo" />
        <ul class="inline-cta">
          <li> <a href="<?php echo $appstore_url; ?>" class="store-btn"><img src="{{\URL::to('/')}}/public/images/appstore-btn.png" alt="Appstore"/></a> </li>
          <li> <a href="<?php echo $playstore_url; ?>" class="store-btn"><img src="{{\URL::to('/')}}/public/images/playstore-btn.png" alt="Playstore"/></a> </li>
        </ul>
        <!--/ .inline-cta --> 
        
      </div>
      <!--/ .footer-content -->
      
      <div class="clearfix"></div>
      <ul class="footer-nav all-caps">
        <li><a href="privacy">Privacy</a></li>
        <li><a href="terms">Terms</a></li>
        <li><a href=
"mailto:R4EnterprisesLLC@gmail.com?subject=We’d love to hear from you">Contact</a></li>
        <span class="copyright"> Copyright © 2015. All Rights Reserved. </span>
      </ul>
      <!--/ .footer-nav --> 
      
    </div>
    <!--/ .row --> 
  </div>
  <!--/ .container --> 
  
</footer>
<!--
		=========================
		==/ END FOOTER CONTENT ==
		=========================
		--> 

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script> 

<!-- Latest compiled and minified JavaScript --> 
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script> 

<!-- SmoothScroll --> 
<script src="{{\URL::to('/')}}/public/js/minified/SmoothScroll.min.js"></script> 

<!-- ScrollReveal --> 
<script src="{{\URL::to('/')}}/public/js/minified/scrollReveal.min.js"></script> 

<!-- Slick --> 
<script src="{{\URL::to('/')}}/public/js/slick/slick.min.js"></script> 
<script src="{{\URL::to('/')}}/public/js/urip-slick-carousel-setting.js"></script> 

<!-- Custom JS --> 
<script src="{{\URL::to('/')}}/public/js/urip-v2.js"></script>
</body>
</html>
<?php
/*
Theme Name: Yarseo
Unique Name: Yarseo Theme
Theme URI: https://echatbots.com/theme/yarseo
Author: Yarseo
Author URI: https://yarseo.com
Version: 2.1
Description: This is version 2.1 of our theme. To get more information please visit the <a href="https://echatbots.com/theme/yarseo/" target="_blank">theme page documentation</a>.
*/
?>
<?php
	include_once 'translator.php';
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
<meta charset=utf-8>
<meta http-equiv=X-UA-Compatible content="IE=edge">
<meta name=viewport content="width=device-width, initial-scale=1">
<title><?php echo $this->config->item('product_name'); if($this->config->item('slogan')!='') echo " | ".$this->config->item('slogan')?></title>
<meta name="description" content="<?php echo $this->config->item('slogan'); ?>">
<meta name="author" content="<?php echo $this->config->item('institute_address1');?>">
<?php
	include 'includes/css.php';
	include 'includes/custom.php';
?>
</head>
<body data-spy="scroll" data-target="#main-navbar">
<div class="loader bg-white">
<div class="loader-inner ball-scale-ripple-multiple vh-center">
<div></div>
<div></div>
<div></div>
</div>
</div>
<div class="main-container" id="page">
<?php
	include 'includes/nav.php';
?>

<!-- =========================
         HERO SECTION
============================== -->
<section id="hero6" class="hero hero-devices" style="background-image: url(<?php echo xit_load_images('images/shapes.svg'); ?>);background-size:cover;background-position:center right;">
<div class="container">
<div class="row y-middle">
<div class="col-sm-6 wow fadeInRight animated" style="visibility:visible;animation-name:fadeInLeft" <?php if($is_rtl) echo 'dir="rtl"';?>>
<h1 class="m-b-md center-md text-uppercase"><?php echo $lang['H_TITLE']; ?></h1>
<p class="lead m-b-md center-md"><?php echo $lang['H_SUBTITLE']; ?></p>
<div class="center-md m-t">
<?php if(isset($default_package[0])) : ?>
<a href="<?php echo base_url('home/sign_up'); ?>" class="btn btn-md btn-blue text-uppercase"><?php echo $default_package[0]["validity"] ?> <?php echo $this->lang->line("Days"); ?> <?php echo $this->lang->line("Free Trial"); ?></a>
<?php endif; ?>
<a href="#pricing" class="btn btn-md btn-green text-uppercase smooth-scroll"><?php echo $lang['N_PRICING']; ?></a>
</div>
</div>
<div class="col-sm-6 center-md jump p-t-md wow zoomIn" style="visibility: visible; animation-name: zoomIn;">
<img src="<?php echo xit_load_images('images/phone.png'); ?>" alt="" class="img-responsive updown">
<img src="<?php echo xit_load_images('images/shadow.png'); ?>" alt="" class="img-responsive">
</div>
</div>
</div>
<div class="container m-t-md">
<div class="row">
<div class="col-md-12 text-center c2 y-middle clients wow zoomIn rotate" style="visibility: visible; animation-name: zoomIn;">
<div class="col-md-2 col-xs-12">
<h6 class="m-b-0 text-uppercase" <?php if($is_rtl) echo 'dir="rtl"';?>><?php echo $lang['INTEGRATIONS']; ?>:</h6>
</div>
<div class="col-md-9 col-xs-12 col-md-offset-1">
<div id="carousel-partners1" class="carousel slide carousel-fade carousel-partner" data-ride="carousel">
<div class="carousel-inner" role="listbox">
<div class="item active">
<div class="col-lg-3 col-xs-6 m-b">
<img src="<?php echo xit_load_images('integrations/google-my-business.svg'); ?>" class="img-responsive m-x-auto" alt="Google My Business Integration">
</div>
<div class="col-lg-3 col-xs-6 m-b">
<img src="<?php echo xit_load_images('integrations/woocommerce.svg'); ?>" class="img-responsive m-x-auto" alt="WooCommerce Integration">
</div>
<div class="col-lg-3 col-xs-6 m-b">
<img src="<?php echo xit_load_images('integrations/paypal.svg'); ?>" class="img-responsive m-x-auto" alt="PayPal Integration">
</div>
<div class="col-lg-3 col-xs-6 m-b">
<img src="<?php echo xit_load_images('integrations/twilio.svg'); ?>" class="img-responsive m-x-auto" alt="Twilio Integration">
</div>
</div>
<div class="item">
<div class="col-lg-3 col-xs-6 m-b">
<img src="<?php echo xit_load_images('integrations/stripe.svg'); ?>" class="img-responsive m-x-auto" alt="Stripe Integration">
</div>
<div class="col-lg-3 col-xs-6 m-b">
<img src="<?php echo xit_load_images('integrations/sendgrid.svg'); ?>" class="img-responsive m-x-auto" alt="Sendgrid Integration">
</div>
<div class="col-lg-3 col-xs-6 m-b">
<img src="<?php echo xit_load_images('integrations/mailchimp.svg'); ?>" class="img-responsive m-x-auto" alt="Mailchimp Integration">
</div>
<div class="col-lg-3 col-xs-6 m-b">
<img src="<?php echo xit_load_images('integrations/wordpress.svg'); ?>" class="img-responsive m-x-auto" alt="WordPress Integration">
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>


<!-- =========================
        Channel
============================== -->
<section id="features11-1" class="p-y-md">
<div class="container">
<div class="row">
<div class="col-md-8 col-md-offset-2">

</div>
</div>
<div class="row features-panels insta-bg p-y-md p-x-sm center-md content-align-md rounded">
<div class="col-md-12 p-b-sm">
<div class="section-header text-center text-white wow fadeIn" style="visibility:visible;animation-name:fadeIn">
<h2><?php echo $lang['MT_TITLE']; ?></h2>
<p class="lead center-md"><?php echo $lang['MT_SUBTITLE']; ?></p>
</div>
<ul class="nav nav-tabs nav-justified m-b-lg choose" role="tablist">
<li role="presentation" class="active"><a href="#instagram" role="tab" data-toggle="tab" class="text-edit"><img src="<?php echo xit_load_images('images/instagram.svg'); ?>" alt="">Instagram</a></li>
<li role="presentation"><a href="#messenger" role="tab" data-toggle="tab" class="text-edit"><img src="<?php echo xit_load_images('images/facebook-messenger.svg'); ?>" alt="facebook messenger">Messenger</a></li>
<li role="presentation"><a href="#sms" role="tab" data-toggle="tab" class="text-edit"><img src="<?php echo xit_load_images('images/ios-message.svg'); ?>" alt="">SMS</a></li>
<li role="presentation"><a href="#email" role="tab" data-toggle="tab" class="text-edit"><img src="<?php echo xit_load_images('images/mail.svg'); ?>" alt="">Email</a></li>
</ul>
</div>
<div class="tab-content">
<div role="tabpanel" class="tab-pane fade in active" id="instagram">
<div class="content-block content-align-md">
<div class="col-md-10 col-md-offset-1 y-middle">
<div class="col-md-5 wow fadeInLeft  animated" style="visibility: visible; animation-name: fadeInLeft;">
<h2 class="f-w-900"><?php echo $lang['MT_TOOL1']; ?></h2>
<p class="m-t-md lead"><?php echo $lang['MT_TOOL1_DESC']; ?></p>
<a href="<?php echo base_url('home/sign_up'); ?>" class="btn btn-md btn-white text-uppercase m-y-md"><?php echo $this->lang->line("Free Trial"); ?> <?php echo $default_package[0]["validity"] ?> <?php echo $this->lang->line("Days"); ?></a>
</div>
<div class="col-md-6 col-md-offset-1 wow zoomIn" style="visibility:visible;animation-name:zoomIn">
<img src="<?php echo xit_load_images('images/instagram-dms.png'); ?>" class="img-responsive m-x-auto" alt="Instagram Direct Messages">
</div>
</div>
</div>
</div>
<div role="tabpanel" class="tab-pane fade" id="messenger">
<div class="content-block content-align-md">
<div class="col-md-10 col-md-offset-1 y-middle">
<div class="col-md-5">
<h2 class="f-w-900"><?php echo $lang['MT_TOOL2']; ?></h2>
<p class="m-t-md lead"><?php echo $lang['MT_TOOL2_DESC']; ?></p>
<a href="<?php echo base_url('home/sign_up'); ?>" class="btn btn-md btn-white text-uppercase m-y-md"><?php echo $this->lang->line("Free Trial"); ?> <?php echo $default_package[0]["validity"] ?> <?php echo $this->lang->line("Days"); ?></a>
</div>
<div class="col-md-6 col-md-offset-1">
<img src="<?php echo xit_load_images('images/facebook-messenger.png'); ?>" class="img-responsive m-x-auto" alt="Facebook Messenger">
</div>
</div>
</div>
</div>
<div role="tabpanel" class="tab-pane fade" id="sms">
<div class="content-block content-align-md">
<div class="col-md-10 col-md-offset-1 y-middle">
<div class="col-md-5">
<h2 class="f-w-900"><?php echo $lang['MT_TOOL3']; ?></h2>
<p class="m-t-md lead"><?php echo $lang['MT_TOOL3_DESC']; ?></p>
<a href="<?php echo base_url('home/sign_up'); ?>" class="btn btn-md btn-white text-uppercase m-y-md"><?php echo $this->lang->line("Free Trial"); ?> <?php echo $default_package[0]["validity"] ?> <?php echo $this->lang->line("Days"); ?></a>
</div>
<div class="col-md-6 col-md-offset-1">
<img src="<?php echo xit_load_images('images/mass-sms-marketing.png'); ?>" class="img-responsive m-x-auto" alt="Mass SMS Marketing">
</div>
</div>
</div>
</div>
<div role="tabpanel" class="tab-pane fade" id="email">
<div class="content-block content-align-md">
<div class="col-md-10 col-md-offset-1 y-middle">
<div class="col-md-5">
<h2 class="f-w-900"><?php echo $lang['MT_TOOL4']; ?></h2>
<p class="m-t-md lead"><?php echo $lang['MT_TOOL4_DESC']; ?></p>
<a href="<?php echo base_url('home/sign_up'); ?>" class="btn btn-md btn-white text-uppercase m-y-md"><?php echo $this->lang->line("Free Trial"); ?> <?php echo $default_package[0]["validity"] ?> <?php echo $this->lang->line("Days"); ?></a>
</div>
<div class="col-md-6 col-md-offset-1">
<img src="<?php echo xit_load_images('images/mass-email-marketing.png'); ?>" class="img-responsive m-x-auto" alt="Mass Email Marketing">
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>        


<!-- =========================
	Ads
============================== -->
<?php if($this->is_ad_enabled && $this->is_ad_enabled1) : ?>
<section class="content-align-md p-y bg-f4e9da">
<div class="container">
<div class="row y-middle c2">
<div class="col-md-12">
<div class="hidden-xs hidden-sm text-center"><?php echo $this->ad_content1; ?></div>
<div class="hidden-md hidden-lg text-center"><?php echo $this->ad_content1_mobile; ?></div>
</div>
</div>
</div>
</section>
<?php endif; ?>


<!-- =========================
        ECOMMERCE SECTION 
============================== -->
<section id="features" class="p-t-md">
<div class="container">
<div class="row">
<div class="col-md-8 col-md-offset-2 text-center" <?php if($is_rtl) echo 'dir="rtl"';?>>
<h2 class="m-b"><?php echo $lang['F_TITLE']; ?></h2>
<p class="lead"><?php echo $lang['F_SUBTITLE']; ?></p>
</div>
</div>
</div>
</section>
<section id="features7-1" class="p-b-md p-t content-dashboard content-align-md services-area bg-right-color">
<div class="container">
<div class="row features-block y-middle">
<div class="col-md-7 col-md-push-5 text-center wow fadeInRight animated" style="visibility:visible;animation-name:fadeInRight">
<img src="<?php echo xit_load_images('images/powerful-ecommerce-features.png'); ?>" class="" alt="Chat Marketing">
</div>
<div class="col-md-5 col-md-pull-7 wow fadeInLeft animated" style="visibility:visible;animation-name:fadeInLeft" <?php if($is_rtl) echo 'dir="rtl"';?>>
<div class="col-xs-12 icon-left m-b-md clearfix">
<img src="<?php echo xit_load_images('images/facebook-messenger.svg'); ?>" alt="Facebook Messenger">
<h5 class="m-t f-w-900"><?php echo $lang['F_MAIN1']; ?></h5>
<p><?php echo $lang['F_MAIN1_DESC']; ?></p>
</div>
<div class="col-xs-12 icon-left m-b-md clearfix">
<img src="<?php echo xit_load_images('images/instagram.svg'); ?>" alt="Instagram">
<h5 class="m-t f-w-900"><?php echo $lang['F_MAIN2']; ?></h5>
<p><?php echo $lang['F_MAIN2_DESC']; ?></p>
</div>
<div class="col-xs-12 icon-left m-b-md clearfix">
<img src="<?php echo xit_load_images('images/woocommerce.svg'); ?>" alt="E-Commerce">
<h5 class="m-t f-w-900"><?php echo $lang['F_MAIN3']; ?></h5>
<p><?php echo $lang['F_MAIN3_DESC']; ?></p>
</div>
</div>
</div>
</div>
</section>




<!-- =========================
        SOCIAL POSTING 
============================== -->
<section id="features8-1" class="p-y-md content-align-md">
<div class="container">
<div class="row">
<div class="col-md-8 col-md-offset-2">
<div class="section-header text-center wow fadeIn" style="visibility:visible;animation-name:fadeIn">
<h2><?php echo $lang['F_FEATURE_TITLE']; ?></h2>
<p class="lead"><?php echo $lang['F_FEATURE_SUBTITLE']; ?></p>
</div>
</div>
</div>
<div class="row y-middle underscore">
<div class="col-md-4 col-md-push-4 jump text-center features-list-img wow zoomIn p-b" style="visibility:visible;animation-name:zoomIn">
<img src="<?php echo xit_load_images('images/post-on-social-media.png'); ?>" class="img-responsive m-x-auto updown" alt="">
<img src="<?php echo xit_load_images('images/shadow.png'); ?>" alt="" class="img-responsive">
</div>
<div class="col-md-4 col-md-pull-4 col-sm-6">
<ul class="features-list features-list-left list-unstyled">
<li class="m-b-lg wow zoomIn" style="visibility:visible;animation-name:zoomIn">
<h5><?php echo $lang['F_FEATURE1']; ?></h5>
<p><?php echo $lang['F_FEATURE1_DESC']; ?></p>
</li>
<li class="m-b-lg wow zoomIn animated" data-wow-delay="0.4s" style="visibility:visible;animation-delay:.4s;animation-name:zoomIn">
<h5><?php echo $lang['F_FEATURE2']; ?></h5>
<p><?php echo $lang['F_FEATURE2_DESC']; ?></p>
</li>
<li class="m-b-lg wow zoomIn animated" data-wow-delay="0.2s" style="visibility:visible;animation-delay:.2s;animation-name:zoomIn">
<h5><?php echo $lang['F_FEATURE3']; ?></h5>
<p><?php echo $lang['F_FEATURE3_DESC']; ?></p>
</li>
</ul>
</div>
<div class="col-md-4 col-sm-6">
<ul class="features-list list-unstyled">
<li class="m-b-lg wow zoomIn" style="visibility:visible;animation-name:zoomIn">
<h5><?php echo $lang['F_FEATURE4']; ?></h5>
<p><?php echo $lang['F_FEATURE4_DESC']; ?></p>
</li>
<li class="m-b-lg wow zoomIn animated" data-wow-delay="0.2s" style="visibility:visible;animation-delay:.2s;animation-name:zoomIn">
<h5><?php echo $lang['F_FEATURE5']; ?></h5>
<p><?php echo $lang['F_FEATURE5_DESC']; ?></p>
</li>
<li class="m-b-lg wow zoomIn animated" data-wow-delay="0.4s" style="visibility:visible;animation-delay:.4s;animation-name:zoomIn">
<h5><?php echo $lang['F_FEATURE6']; ?></h5>
<p><?php echo $lang['F_FEATURE6_DESC']; ?></p>
</li>
</ul>
</div>
</div>
</div>
</section> 



<!-- =========================
        GOOGLE BUSINESS PROFILE
============================== -->
<section id="features" class="">
<div class="container">
<div class="row">
<div class="col-md-8 col-md-offset-2 text-center" <?php if($is_rtl) echo 'dir="rtl"';?>>
<h2 class="m-b"><?php echo $lang['GB_TITLE']; ?></h2>
<p class="lead"><?php echo $lang['GB_SUBTITLE']; ?></p>
</div>
</div>
</div>
</section>
<section id="content7-1" class="p-b-md p-t content-dashboard content-align-md services-area bg-left-color">
<div class="container">
<div class="row features-block y-middle">
<div class="col-md-7 text-center wow fadeInLeft animated" style="visibility:visible;animation-name:fadeInLeft">
<img src="<?php echo xit_load_images('images/google-business-profile.png'); ?>" class="dash-left" alt="">
</div>
<div class="col-md-5">
<div class="col-xs-12 icon-left m-b-md clearfix">
<img src="<?php echo xit_load_images('images/gbp.svg'); ?>" alt="Google Business Profile">
<h5 class="m-t f-w-900"><?php echo $lang['GB_MAIN1']; ?></h5>
<p><?php echo $lang['GB_MAIN1_DESC']; ?></p>
</div>
<div class="col-xs-12 icon-left m-b-md clearfix">
<img src="<?php echo xit_load_images('images/star.svg'); ?>" alt="Google Reviews">
<h5 class="m-t f-w-900"><?php echo $lang['GB_MAIN2']; ?></h5>
<p><?php echo $lang['GB_MAIN2_DESC']; ?></p>
</div>
<div class="col-xs-12 icon-left m-b-md clearfix">
<img src="<?php echo xit_load_images('images/google-maps-pin.svg'); ?>" alt="Google Maps">
<h5 class="m-t f-w-900"><?php echo $lang['GB_MAIN3']; ?></h5>
<p><?php echo $lang['GB_MAIN3_DESC']; ?></p>
</div>
</div>
</div>
</div>
</section>


<!-- =========================
	Ads
============================== -->
<?php if($this->is_ad_enabled && $this->is_ad_enabled2) : ?>
<section class="content-align-md p-y bg-f4e9da">
<div class="container">
<div class="row y-middle c2">
<div class="col-md-12">
<div class="hidden-xs hidden-sm text-center"><?php echo $this->ad_content2; ?></div>
<div class="hidden-md hidden-lg text-center"><?php echo $this->ad_content1_mobile; ?></div>
</div>
</div>
</div>
</section>
<?php endif; ?>


<!-- =========================
           VIDEO
============================== -->
<section id="tutorial" class="<?php if($this->config->item('display_video_block') == '0' || $this->config->item('promo_video') == '') echo 'hidden';?> p-y-lg" style="background-image: url(<?php echo xit_load_images('images/shapes.svg'); ?>);background-size:cover;background-position:center right;">
<div class="container">
<div class="row">
<div class="col-md-12 text-center" <?php if($is_rtl) echo 'dir="rtl"';?>>
<h2 class="m-b"><?php echo $lang['V_TITLE']; ?></h2>
<p class="lead"><?php echo $lang['V_SUBTITLE']; ?></p>
</div>
</div>
<div class="row p-t">
<div class="col-md-10 col-md-offset-1 text-center">
<div class="popup-box">
<img src="<?php echo xit_load_images('images/build-a-powerful-chatbot.jpg'); ?>" class="img-responsive" alt="">
<?php 
	$promo_video_link = $this->config->item('promo_video');
?>
<div class="popup-button">
<a class="mp-iframe" href="<?php echo $promo_video_link; ?>"><i class="fa fa-play" style="background-color:#0081FF"></i></a>
</div>
</div>
</div>
</div>
</div>
</section>

<!-- =========================
        ECOMMERCE SECTION 
============================== -->
<section id="features" class="p-t-md">
<div class="container">
<div class="row">
<div class="col-md-8 col-md-offset-2 text-center" <?php if($is_rtl) echo 'dir="rtl"';?>>
<h2 class="m-b"><?php echo $lang['F_TITLE']; ?></h2>
<p class="lead"><?php echo $lang['F_SUBTITLE']; ?></p>
</div>
</div>
</div>
</section>
<section id="features7-1" class="p-b-md p-t content-dashboard content-align-md services-area bg-right-color">
<div class="container">
<div class="row features-block y-middle">
<div class="col-md-7 col-md-push-5 text-center wow fadeInRight animated" style="visibility:visible;animation-name:fadeInRight">
<img src="<?php echo xit_load_images('images/powerful-ecommerce-features.png'); ?>" class="" alt="Chat Marketing">
</div>
<div class="col-md-5 col-md-pull-7 wow fadeInLeft animated" style="visibility:visible;animation-name:fadeInLeft" <?php if($is_rtl) echo 'dir="rtl"';?>>
<div class="col-xs-12 icon-left m-b-md clearfix">
<img src="<?php echo xit_load_images('images/facebook-messenger.svg'); ?>" alt="Facebook Messenger">
<h5 class="m-t f-w-900"><?php echo $lang['F_MAIN1']; ?></h5>
<p><?php echo $lang['F_MAIN1_DESC']; ?></p>
</div>
<div class="col-xs-12 icon-left m-b-md clearfix">
<img src="<?php echo xit_load_images('images/instagram.svg'); ?>" alt="Instagram">
<h5 class="m-t f-w-900"><?php echo $lang['F_MAIN2']; ?></h5>
<p><?php echo $lang['F_MAIN2_DESC']; ?></p>
</div>
<div class="col-xs-12 icon-left m-b-md clearfix">
<img src="<?php echo xit_load_images('images/woocommerce.svg'); ?>" alt="E-Commerce">
<h5 class="m-t f-w-900"><?php echo $lang['F_MAIN3']; ?></h5>
<p><?php echo $lang['F_MAIN3_DESC']; ?></p>
</div>
</div>
</div>
</div>
</section>    

<!-- =========================
	Ads
============================== -->
<?php if($this->is_ad_enabled && $this->is_ad_enabled3) : ?>
<section class="content-align-md p-y bg-f4e9da">
<div class="container">
<div class="row y-middle c2">
<div class="col-md-12">
<div class="hidden-xs hidden-sm text-center"><?php echo $this->ad_content3; ?></div>
<div class="hidden-md hidden-lg text-center"><?php echo $this->ad_content1_mobile; ?></div>
</div>
</div>
</div>
</section>
<?php endif; ?>


<!-- =========================
           BLOG
============================== -->
<section id="team3-1" class="p-y-md bg-edit">
<div class="container">
<div class="row">
<div class="col-md-8 col-md-offset-2">
<div class="section-header text-center wow fadeIn" style="visibility:visible;animation-name:fadeIn" <?php if($is_rtl) echo 'dir="rtl"';?>>
<h2><?php echo $lang['EK_TITLE']; ?></h2>
<p class="lead"><?php echo $lang['EK_SUBTITLE']; ?></p>
</div>
</div>
</div>
</div>
<div class="container portfolio-card">
<div class="row">
<div class="col-md-10 col-md-offset-1">
<div class="col-md-6 m-b-md clearfix">
<div class="h caption-5">
<figure><img src="<?php echo xit_load_images('images/join-our-facebook-group.jpg'); ?>" alt="<?php echo $lang['EK_GROUP_TITLE']; ?>">
<figcaption>
<div class="caption-box vertical-center-abs text-center text-white" <?php if($is_rtl) echo 'dir="rtl"';?>>
<h5><?php echo $lang['EK_GROUP_TITLE']; ?></h5>
<p class="lead m-b-md"><?php echo $lang['EK_GROUP_SUBTITLE']; ?></p>
<a href="<?php echo $lang['EK_GROUP_LINK']; ?>" target="_blank" class="btn btn-ghost smooth-scroll text-uppercase"><?php echo $lang['EK_GROUP_CTA']; ?></a>
</div>
</figcaption>
</figure>
</div>
</div>
<div class="col-md-6 m-b-md clearfix">
<div class="h caption-5">
<figure><img src="<?php echo xit_load_images('images/visit-our-blog.jpg'); ?>" alt="<?php echo $lang['EK_BLOG_TITLE']; ?>">
<figcaption>
<div class="caption-box vertical-center-abs text-center text-white" <?php if($is_rtl) echo 'dir="rtl"';?>>
<h5><?php echo $lang['EK_BLOG_TITLE']; ?></h5>
<p class="lead m-b-md"><?php echo $lang['EK_BLOG_SUBTITLE']; ?></p>
<a href="<?php echo $lang['EK_BLOG_LINK']; ?>" class="btn btn-ghost smooth-scroll text-uppercase"><?php echo $lang['EK_BLOG_CTA']; ?></a>
</div>
</figcaption>
</figure>
</div>
</div>
</div>
</div>
</div>
</section>

<!-- =========================
         TESTIMONIALS
============================== -->
<section id="reviews" class="<?php if($this->config->item('display_review_block') == '0') echo 'hidden';?> p-y-md bg-edit">
<div class="container">
<div class="row">
<div class="col-md-8 col-md-offset-2">
<div class="m-b-md text-center wow fadeIn" style="visibility:visible;animation-name:fadeIn" <?php if($is_rtl) echo 'dir="rtl"';?>>
<h2><?php echo $lang['T_TITLE']; ?></h2>
<p class="lead"><?php echo $lang['T_SUBTITLE']; ?></p>
</div>
</div>
</div>
<div class="row">
<div class="col-md-12 testimonials">
	<?php 
		$customerReview = $this->config->item('customer_review');
		$ct=0;
		foreach($customerReview as $singleReview) : 
		$ct++;
		$original = $singleReview[2];
		$base     = base_url();
		if (substr($original, 0, 4) != 'http') {
		$img = $base.$original;
		} else {
		$img = $original;
		}
	?>
<div class="col-md-4 text-center p-t-md clearfix" <?php if($is_rtl) echo 'dir="rtl"';?>>
<blockquote class="quote-border">
<figure><img src="<?php echo $img; ?>" alt="" class="img-circle img-thumbnail" width="90" height="90"> </figure>
<p><?php echo $str = $singleReview[3]; ?></p>
<div class="cite text-edit">
<?php echo $singleReview[0]; ?>
<span class="cite-info p-opacity"><?php echo $singleReview[1]; ?></span>
</div>
</blockquote>
</div>
	<?php endforeach;
	?>
</div>
</div>
</div>
</section>


<!-- =========================
         VIDEO REVIEW
============================== -->
<section class="<?php if($this->config->item('customer_review_video') == '') echo 'hidden';?> hero p-y-lg hero-countdown bg-img" style="background-image:url('<?php echo xit_load_images('images/customer-video-review.jpg'); ?>')">
<div class="container">
<h2 class="text-center" <?php if($is_rtl) echo 'dir="rtl"';?>><?php echo $lang['RV_TITLE']; ?></h2>
<div class="row m-y-lg">
<div class="col-sm-12 col-md-6 col-md-offset-3 text-center">
<div class="big-popup p-y-lg">
<?php 
	$demo = $this->config->item('customer_review_video');
	$customer_review_video = trim(str_replace('https://www.youtube.com/watch?v=','',$demo));
?>
<a class="mp-iframe" href="<?php echo $customer_review_video; ?>"><i class="fa fa-play-circle text-white"></i></a>
</div>
</div>
</div>
</div>
</section>


<!-- =========================
             PRICING
============================== -->
<?php if(!empty($pricing_table_data)) : ?>
<section id="pricing" class="p-y-md">
<div class="container">
<div class="row">
<div class="col-md-8 col-md-offset-2">
<div class="section-header text-center wow fadeIn" style="visibility:visible;animation-name:fadeIn" <?php if($is_rtl) echo 'dir="rtl"';?>>
<h2><?php echo $lang['P_TITLE']; ?></h2>
<p class="lead"><?php echo $lang['P_SUBTITLE']; ?></p>
</div>
</div>
</div>
<div class="row pricing-3po">
<?php 
	$i=0;
	foreach($pricing_table_data as $pack) :
	$i++;
?>
<div class="col-md-4" <?php if($is_rtl) echo 'dir="rtl"';?>>
<div class="info text-center">
<h2 class="m-b-md"><?php echo $pack["package_name"]; ?></h2>
<div class="price text-edit"> <span class="currency"><?php echo $curency_icon; ?></span><?php echo $pack["price"]?><small>/<?php echo $pack["validity"]?> <?php echo $this->lang->line("days"); ?></small></div>
<ul class="details m-b-md m-x text-left">
<?php 
	$module_ids=$pack["module_ids"];
	$monthly_limit=json_decode($pack["monthly_limit"],true);
	$module_names_array=$this->basic->execute_query('SELECT module_name,id FROM modules WHERE FIND_IN_SET(id,"'.$module_ids.'") > 0  ORDER BY module_name ASC');
	foreach ($module_names_array as $row) : 
?>
<li>
<i class="fa fa-check-circle green"></i>
<?php 
	$limit=0;
	$limit=$monthly_limit[$row["id"]];
	if($limit=="0") 
	$limit2="<b>".$this->lang->line("unlimited")."</b>";
	else 
	$limit2=$limit;
	if($row["id"]!="1" && $limit!="0") 
	$limit2="<p>".$limit2."/".$this->lang->line("month")."";
	echo $this->lang->line($row["module_name"]);
	if($row["id"]!="13" && $row["id"]!="14" && $row["id"]!="16") 
	echo " : <b>". $limit2."</b>"."</p>";
	else 
	echo "";
?>
</li>
<?php endforeach; ?>
</ul>
<?php if($this->config->item('enable_signup_form') != '0') : ?>
<a class="btn btn-sm btn-block btn-blue text-uppercase" href="<?php echo site_url('home/sign_up'); ?>"><?php echo $lang['P_CTA']; ?></a>
<?php endif; ?>
</div>
</div>
<?php endforeach; ?>
</div>
</div>
</section>
<?php endif; ?>


<!-- =========================
             FAQ
============================== -->
<section class="p-y-lg faqs schedule bg-light">
<div class="container">
<div class="row">
<div class="col-md-8 col-md-offset-2">
<div class="section-header text-center wow fadeIn" style="visibility:visible;animation-name:fadeIn" <?php if($is_rtl) echo 'dir="rtl"';?>>
<h2 class="m-b-md"><?php echo $lang['LE_TITLE']; ?></h2>
<p class="lead m-b-md"><?php echo $lang['LE_SUBTITLE']; ?></p>
<a href="<?php echo $lang['LE_LINK']; ?>" target="_blank" class="btn btn-blue text-uppercase"><?php echo $lang['LE_CTA']; ?></a>
</div>
</div>
</div>
<div class="row p-t-md c2" id="faq" <?php if($is_rtl) echo 'dir="rtl"';?>>
<div class="col-md-10 col-md-offset-1">
<h2 class="p-y-md text-center"><?php echo $lang['FAQ_TITLE']; ?></h2>
<div class="panel-group" id="accordion">
<div class="panel panel-default">
<div class="panel-heading">
<p class="panel-title">
<a data-toggle="collapse" data-parent="#accordion" href="#collapse1"><?php echo $lang['FAQ1']; ?></a>
</p>
</div>
<div id="collapse1" class="panel-collapse collapse">
<div class="panel-body">
<p><?php echo $lang['FAQ1_DESC']; ?></p>
</div>
</div>
</div>
<div class="panel panel-default">
<div class="panel-heading">
<p class="panel-title">
<a data-toggle="collapse" data-parent="#accordion" href="#collapse2"><?php echo $lang['FAQ2']; ?></a>
</p>
</div>
<div id="collapse2" class="panel-collapse collapse">
<div class="panel-body">
<p><?php echo $lang['FAQ2_DESC']; ?></p>
</div>
</div>
</div>
<div class="panel panel-default">
<div class="panel-heading">
<p class="panel-title">
<a data-toggle="collapse" data-parent="#accordion" href="#collapse3"><?php echo $lang['FAQ3']; ?></a>
</p>
</div>
<div id="collapse3" class="panel-collapse collapse">
<div class="panel-body">
<p><?php echo $lang['FAQ3_DESC']; ?></p>
</div>
</div>
</div>
<div class="panel panel-default">
<div class="panel-heading">
<p class="panel-title">
<a data-toggle="collapse" data-parent="#accordion" href="#collapse4"><?php echo $lang['FAQ4']; ?></a>
</p>
</div>
<div id="collapse4" class="panel-collapse collapse">
<div class="panel-body">
<p><?php echo $lang['FAQ4_DESC']; ?></p>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>


<!-- =========================
	CONTACT FORM
============================== -->
<section id="contact-us" class="p-y-lg contact bg-edit">
<div class="container">
<div class="row">
<div class="col-md-8 col-md-offset-2">
<div class="section-header text-center wow fadeIn" style="visibility:visible;animation-name:fadeIn">
<h2 <?php if($is_rtl) echo 'dir="rtl"';?>><?php echo $lang['CF_TITLE']; ?></h2>
<p class="lead" <?php if($is_rtl) echo 'dir="rtl"';?>><?php echo $lang['CF_SUBTITLE']; ?></p>
</div>
</div>
</div>
<div class="row">
<div class="col-md-8 col-md-offset-2">
<?php 
	if($this->session->userdata('mail_sent') == 1) {
	echo "<div class='alert alert-success text-center' id='success-alert'>".$this->lang->line("we have received your email. we will contact you through email as soon as possible")."</div>";
	$this->session->unset_userdata('mail_sent');
	}
?>
<form action="<?php echo site_url("home/email_contact"); ?>" method="post" <?php if($is_rtl) echo 'dir="rtl"';?>>
<div class="row">
<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
<div class="form-group" id="email-field">
<div class="form-input">
<input type="email" class="form-control" required id="email" <?php echo set_value("email"); ?> placeholder="<?php echo $lang['CF_EMAIL']; ?>" name="email">
</div>
<span class="red"><?php echo form_error("email"); ?></span>
</div>
</div>
<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
<div class="form-group" id="phone-field">
<div class="form-input">
<input type="text" class="form-control" required id="subject" <?php echo set_value("subject"); ?> placeholder="<?php echo $lang['CF_SUBJECT']; ?>" name="subject">
</div>
<span class="red"><?php echo form_error("subject"); ?></span>
</div>
</div>
<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
<div class="form-group" id="message-field">
<div class="form-input">
<input type="number" class="form-control" step="1" required id="captcha" <?php echo set_value("captcha"); ?> placeholder="<?php echo $contact_num1."+". $contact_num2." = ?"; ?>" name="captcha">
<span class="red">
<?php 
	if(form_error('captcha')) 
		echo form_error('captcha'); 
	else  
	{ 
		echo $this->session->userdata("contact_captcha_error"); 
		$this->session->unset_userdata("contact_captcha_error"); 
	} 
?>
</span>
</div>
<span class="red"><?php echo form_error("message") ?></span>
</div>
</div>
<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
<div class="form-group" id="message-field">
<div class="form-input">
<textarea class="form-control" rows="3" required id="message" <?php echo set_value("message"); ?> placeholder="<?php echo $lang['CF_MESSAGE']; ?>" name="message"></textarea>
</div>
<span class="red"><?php echo form_error("message") ?></span>
</div>
</div>
<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
<div class="form-group center">
<button type="submit" class="btn btn-blue text-uppercase"><?php echo $lang['CF_SEND']; ?></button>
</div>
</div>
</div>
</form>
</div>
</div>
</div>
</section>

<?php if ($this->session->userdata('license_type') == 'double')  {?>
<section id="cta4" class="p-y cta bg-light content-align-md">
<div class="container">
<div class="row">
<div class="col-md-12 outline no-border y-middle c2">
<div class="col-md-7" <?php if($is_rtl) echo 'dir="rtl"';?>>
<h4 class="text-uppercase"><?php echo $lang['AFFILIATE']; ?></h4>
<p class="m-b-0"><?php echo $lang['EARN']; ?></p>
</div>
<div class="col-md-5 text-center" <?php if($is_rtl) echo 'dir="rtl"';?>>
<a href="<?php echo base_url('affiliate_system/affiliate_sign_up'); ?>" class="btn btn-blue btn-md text-uppercase"><?php echo $lang['JOIN']; ?> <?php echo $lang['AFFILIATE']; ?></a>
</div>
</div>
</div>
</div>
</section>
<?php } ?>

<?php
	include 'includes/footer.php';
	include 'includes/js.php';
?>

<?php $this->load->view("include/fb_px"); ?> 
<?php $this->load->view("include/google_code"); ?> 

</body>
</html>
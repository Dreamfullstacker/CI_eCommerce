<!-- =========================
            NAVIGATION 
============================== -->
<header id="home">
<nav class="navbar navbar-fixed-top" id="main-navbar">
<div class="container">
<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
<span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a href="/" class="navbar-brand smooth-scroll"><img src="<?php echo base_url();?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>"></a>
</div>
<div class="collapse navbar-collapse" id="navbar-collapse">
<ul class="nav navbar-nav navbar-right" <?php if($is_rtl) echo 'dir="rtl"';?>>
<li>
<a href="<?php echo base_url();?>#home" class="smooth-scroll"><?php echo $lang['N_HOME']; ?></a>
</li>
<li>
<a href="<?php echo base_url();?>#features" class="smooth-scroll"><?php echo $lang['N_FEATURES']; ?></a>
</li>
<li>
<a href="<?php echo base_url();?>#pricing" class="smooth-scroll"><?php echo $lang['N_PRICING']; ?></a>
</li>
<li class="<?php if($this->config->item('display_video_block') == '0') echo 'hidden';?>">
<a href="<?php echo base_url();?>#tutorial"><?php echo $lang['N_TUTORIAL']; ?></a>
</li>
<?php if ($this->session->userdata('license_type') == 'double')  {?>
<li>
<a href="<?php echo base_url('blog');?>"><?php echo $lang['N_BLOG']; ?></a>
</li>
<?php } ?>
<?php if ($this->session->userdata('license_type') == 'double')  {?>
<li>
<a href="<?php echo base_url('affiliate_system/affiliate_sign_up'); ?>">Affiliate</a>
</li>
<?php } ?>
<li>
<a href="<?php echo base_url();?>#faq" class="smooth-scroll"><?php echo $lang['N_FAQ']; ?></a>
</li>
<li>
<a href="<?php echo base_url();?>#contact-us"><?php echo $lang['N_CONTACT']; ?></a>
</li>
<li><a href="<?php echo base_url('home/sign_up'); ?>" class="btn-nav btn-blue btn-login text-uppercase"><?php echo $lang['N_SIGNUP']; ?></a></li>
<li><a href="<?php echo base_url('home/login'); ?>" class="btn-nav btn-green btn-signup text-uppercase"><?php echo $lang['N_LOGIN']; ?></a></li>
</ul>
</div>
</div>
</nav>
</header>

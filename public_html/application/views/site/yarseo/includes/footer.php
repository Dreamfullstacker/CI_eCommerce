<section id="cta1-1" class="p-y-md bg-navy text-white">
<div class="container">
<div class="row">
<div class="col-md-8 col-md-offset-2 wow fadeIn animated" style="visibility:visible;animation-name:fadeIn" dir="<?php echo $lang['RTL']; ?>">
<div class="text-center">
<h2 class="m-t f-w-900"><?php echo $lang['FOOTER_TITLE']; ?></h2>
<p class="p-opacity m-b-md lead"><?php echo $lang['FOOTER_SUBTITLE']; ?></p>
<a href="<?php echo base_url('home/sign_up'); ?>" class="btn btn-white text-uppercase"><?php echo $lang['FOOTER_CTA']; ?></a>
</div>
</div>
</div>
</div>
</section>


<!-- =========================
           FOOTER
============================== -->
<footer id="footer2-2" class="p-y footer f2 bg-navy">
<div class="container">
<div class="row text-white">
<div class="col-sm-3 col-xs-12">
<p><?php echo $this->lang->line("Copyright"); ?> &copy; <?php echo date("Y"); ?> <?php echo $this->config->item("product_short_name"); ?>.</p>
</div>
<div class="col-sm-3 col-xs-12 text-center">
<ul class="footer-social" dir="<?php echo $lang['RTL']; ?>">
<?php if($this->config->item('facebook') != ''): ?>
<li><a href="<?php echo $this->config->item('facebook'); ?>" target="_blank" class="inverse"><i class="fa fa-facebook-square"></i></a></li>
<?php endif; ?>
<?php if($this->config->item('twitter') != ''): ?>
<li><a href="<?php echo $this->config->item('twitter'); ?>" target="_blank" class="inverse"><i class="fa fa-twitter"></i></a></li>
<?php endif; ?>
<?php if($this->config->item('youtube') != ''): ?>
<li><a href="<?php echo $this->config->item('youtube'); ?>" target="_blank" class="inverse"><i class="fa fa-youtube"></i></a></li>
<?php endif; ?>
<?php if($this->config->item('linkedin') != ''): ?>
<li><a href="<?php echo $this->config->item('linkedin'); ?>" target="_blank" class="inverse"><i class="fa fa-linkedin"></i></a></li>
<?php endif; ?>
</ul>
</div>
<div class="col-sm-5 col-xs-12" dir="<?php echo $lang['RTL']; ?>">
<ul class="footer-links">
<li><a href="<?php echo base_url('home/privacy_policy'); ?>" title="" class="edit inverse"><?php echo $lang['PRIVACY']; ?></a></li>
<li><a href="<?php echo base_url('home/terms_use'); ?>" title="" class="edit inverse"><?php echo $lang['TERMS']; ?></a></li>
<li><a href="<?php echo base_url('home/gdpr'); ?>" title="" class="edit inverse"><?php echo $lang['GDPR']; ?></a></li>
</ul>
</div>
<div class="col-sm-1 col-xs-12 text-center">
<select class="language minimal" onchange="location = this.value;" dir="<?php echo $lang['RTL']; ?>">
<option selected=""><?php echo $lang['LANGUAGE']; ?> <i class="fa fa-globe"></i></option>
<option value="?lang=en">English</option>
<option value="?lang=th">Thai</option>
</select>
</div>
</div>
</div>
</footer>
</div>

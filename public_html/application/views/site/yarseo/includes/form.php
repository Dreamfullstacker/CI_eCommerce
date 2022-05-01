<div class="modal fade" id="contact" tabindex="-1" role="dialog" aria-labelledby="contactLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
<h5 class="modal-title" id="contactLabel" dir="<?php echo $lang['RTL']; ?>"><?php echo $lang['CF_TITLE']; ?></h5>
<p class="modal-description" dir="<?php echo $lang['RTL']; ?>"><?php echo $lang['CF_SUBTITLE']; ?></p>

</div>
<div class="modal-body">




<form action="<?php echo site_url("home/email_contact"); ?>" method="post" dir="<?php echo $lang['RTL']; ?>">
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
<button type="submit" class="btn btn-blue"><?php echo $lang['CF_SEND']; ?></button>
</div>
</div>
</div>
</form>








</div>
<div class="modal-footer">
</div>
</div>
</div>
</div>
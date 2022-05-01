<div class="container vh-100">
<div class="row">
<div class="col-md-5 col-md-offset-1 img-block" style="background-image:url('<?php echo xit_load_images('images/login-or-signup.jpg'); ?>');min-height: 100%;"></div>
<div class="col-md-6 p-y-md">
          <?php 
            if($this->session->userdata('reg_success') == 1) {
              echo "<div class='alert alert-success text-center'>".$this->lang->line("An activation code has been sent to your email. please check your inbox to activate your account.")."</div>";
              $this->session->unset_userdata('reg_success');
            }                  
            if($this->session->userdata('reg_success') == 'limit_exceed') {
              echo "<div class='alert alert-danger text-center'>".$this->lang->line("Signup has been disabled. Please contact system admin.")."</div>";
              $this->session->unset_userdata('reg_success');
            }
            if(form_error('name') != '' || form_error('email') != '' || form_error('confirm_password') != '' ||form_error('password')!="" ) 
            {
              $form_error="";
              if(form_error('name') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('name'))."<br>";
              if(form_error('email') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('email'))."<br>";
              if(form_error('password') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('password'))."<br>";
              if(form_error('confirm_password') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('confirm_password'))."<br>";
              echo "<div class='alert alert-danger text-center'>".$form_error."</div>";
             
            }  
            if(form_error('captcha')) 
            echo "<div class='alert alert-danger text-center'>".form_error('captcha')."</div>"; 
            else if($this->session->userdata("sign_up_captcha_error")!='')  
            { 
              echo "<div class='alert alert-danger text-center'>".$this->session->userdata("sign_up_captcha_error")."</div>"; 
              $this->session->unset_userdata("sign_up_captcha_error"); 
            } 
          ?>
	<form method="POST" action="<?php echo site_url('home/sign_up_action');?>" class="form-white">
		<div class="text-center">
			<a href="<?php echo base_url();?>">
			<img src="<?php echo base_url();?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>" width="200">
			</a>
			<h5><?php echo $this->lang->line("Sign Up"); ?></h5>
		</div>
		<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
		<div class="form-group col-md-6">
			<label for="frist_name"><?php echo $this->lang->line("Name"); ?>*</label>
			<input placeholder="" id="name" type="text" class="form-control" name="name" autofocus required value="<?php echo set_value('name');?>">
		</div>
		<div class="form-group col-md-6">
			<label for="last_name"><?php echo $this->lang->line("Email"); ?>*</label>
			<input placeholder="" id="email" type="email" class="form-control" name="email" required value="<?php echo set_value('email');?>">
		</div>
		<div class="form-group col-md-6">
			<label for="password" class="d-block"><?php echo $this->lang->line("Password"); ?>*</label>
			<input placeholder="" id="password" type="password" class="form-control" required name="password" value="<?php echo set_value('password');?>">
		</div>
		<div class="form-group col-md-6">
			<label for="password2" class="d-block"><?php echo $this->lang->line("Confirm Password");?>*</label>
			<input placeholder="" id="password2" type="password" class="form-control" required name="confirm_password" value="<?php echo set_value('confirm_password');?>">
		</div>
		<div class="form-group col-md-12">
			<label><?php echo $this->lang->line("Captcha");?>: <span class="input-group-text" id="basic-addon3"><?php echo $num1. "+". $num2." = ?";?>*</span></label>
		</div>
		<div class="form-group col-md-12">
			<input type="number" class="form-control" required name="captcha" placeholder="<?php echo $this->lang->line("Put your answer here"); ?>" >
		</div>
		<div class="form-group col-md-6">
			<div class="custom-control custom-checkbox m-y-md">
			<input type="checkbox" name="agree" required class="custom-control-input m-r" id="agree">
			<label class="custom-control-label" for="agree"><a target="_blank" class="text-small" href="<?php echo site_url();?>home/terms_use"><?php echo $this->lang->line("I agree with user terms");?></a></label>
			</div>
		</div>
		<div class="form-group col-md-6">
			<div class="custom-control custom-checkbox m-y-md">
			<input type="checkbox" name="agree" required class="custom-control-input m-r" id="agree">
			<label class="custom-control-label" for="agree"><a target="_blank" class="text-small" href="<?php echo site_url();?>home/gdpr">I agree with GDPR policy</a></label>
			</div>
		</div>
		<div class="form-group col-md-12">
			<button type="submit" class="btn btn-blue text-uppercase">
			<?php echo $this->lang->line("sign up"); ?>
			</button>
		</div>
		<div class="form-group col-md-12">
			<div class="text-muted text-center">
			<?php echo $this->lang->line("Have an account?"); ?> <a href="<?php echo base_url('home/login'); ?>"><?php echo $this->lang->line("Login"); ?></a>
		</div>
	</form>
</div>
</div>
</div>
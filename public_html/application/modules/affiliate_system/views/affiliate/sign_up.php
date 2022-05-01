<div class="container mt-5">
  <div class="row">
    <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3">
      <div class="login-brand">
        <a href="<?php echo base_url();?>"><img src="<?php echo base_url(); ?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>" width="200"></a>
      </div>

      <div class="card card-primary">
        <div class="card-header"><h4><i class="far fa-user-circle"></i> <?php echo $this->lang->line("Sign Up"); ?></h4></div>

        <div class="card-body">
          <?php 
            if($this->session->userdata('affiliate_reg_success') == 1) {
              echo "<div class='alert alert-success text-center'>".$this->lang->line("An activation code has been sent to your email. please check your inbox to activate your account.")."</div>";
              $this->session->unset_userdata('affiliate_reg_success');
            } 

            if($this->session->userdata('affiliate_reg_success') == 'limit_exceed') {
              echo "<div class='alert alert-danger text-center'>".$this->lang->line("Signup has been disabled. Please contact system admin.")."</div>";
              $this->session->unset_userdata('affiliate_reg_success');
            }

            if(form_error('affiliate_name') != '' || form_error('user_name') || form_error('affiliate_email') != '' || form_error('affiliate_confirm_password') != '' ||form_error('affiliate_password')!= "" ) 
            {
              $form_error="";
              if(form_error('affiliate_name') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('affiliate_name'))."<br>";
              if(form_error('user_name') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('user_name'))."<br>";
              if(form_error('affiliate_email') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('affiliate_email'))."<br>";
              if(form_error('affiliate_password') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('affiliate_password'))."<br>";
              if(form_error('affiliate_confirm_password') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('affiliate_confirm_password'))."<br>";
              echo "<div class='alert alert-danger text-center'>".$form_error."</div>";
             
            }  
            
            if(form_error('affiliate_captcha')) 
              echo "<div class='alert alert-danger text-center'>".form_error('affiliate_captcha')."</div>"; 
            else if($this->session->userdata("affiliate_signup_captcha_error") != '')  
            { 
              echo "<div class='alert alert-danger text-center'>".$this->session->userdata("affiliate_signup_captcha_error")."</div>"; 
              $this->session->unset_userdata("affiliate_signup_captcha_error"); 
            } 
          ?>


          <form method="POST" action="<?php echo site_url('affiliate_system/affiliate_signup_action');?>">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
            <div class="row">
              <div class="form-group col-6">
                <label for="affiliate_name"><?php echo $this->lang->line("Name"); ?> *</label>
                <input id="affiliate_name" type="text" class="form-control" name="affiliate_name" autofocus required value="<?php echo set_value('affiliate_name');?>">
              </div>
              <div class="form-group col-6">
                <label for="user_name"><?php echo $this->lang->line("Username"); ?> *</label>
                <input id="user_name" type="text" class="form-control" name="user_name" value="<?php echo set_value('user_name');?>">
              </div>
              <div class="form-group col-6">
                <label for="affiliate_email"><?php echo $this->lang->line("Email"); ?> *</label>
                <input id="affiliate_email" type="email" class="form-control" name="affiliate_email" required value="<?php echo set_value('affiliate_email');?>">
              </div>
              <div class="form-group col-6">
                <label for="affiliate_mobile"><?php echo $this->lang->line("Phone Number"); ?></label>
                <input id="affiliate_mobile" type="text" class="form-control" name="affiliate_mobile" value="<?php echo set_value('affiliate_mobile');?>">
              </div>
            </div>

            <div class="row">
              <div class="form-group col-6">
                <label for="affiliate_password" class="d-block"><?php echo $this->lang->line("Password"); ?> *</label>
                <input id="affiliate_password" type="password" class="form-control" required name="affiliate_password" value="<?php echo set_value('affiliate_password');?>">
              </div>
              <div class="form-group col-6">
                <label for="affiliate_password2" class="d-block"><?php echo $this->lang->line("Confirm Password");?> *</label>
                <input id="affiliate_password2" type="password" class="form-control" required name="affiliate_confirm_password" value="<?php echo set_value('affiliate_confirm_password');?>">
              </div>
            </div>

            <div class="row">
              <div class="form-group col-12" style="margin-bottom:0">
                <label><?php echo $this->lang->line("Captcha");?> *</label>
              </div>
            </div>                  
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon3"><?php echo $num1. "+". $num2." = ?";?></span>
              </div>
              <input type="number" class="form-control" required name="affiliate_captcha" placeholder="<?php echo $this->lang->line("Put your answer here"); ?>" >
            </div>      

            <div class="form-group">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="agree" required class="custom-control-input" id="agree">
                <label class="custom-control-label" for="agree"><a target="_BLANK" href="<?php echo site_url();?>home/terms_use"><?php echo $this->lang->line("I agree with the terms and conditions");?></a></label>
              </div>
            </div>

            <div class="form-group mb-0">
              <button type="submit" class="btn btn-primary btn-lg btn-block">
                <i class="fa fa-user-circle"></i> <?php echo $this->lang->line("sign up"); ?>
              </button>
            </div>

            <div class="row">
               <div class="col-12">
                  <div class="text-muted text-center">
                   <br><?php echo $this->lang->line("Already have an account?"); ?> <a href="<?php echo base_url('affiliate_system/affiliate_login_page'); ?>"><?php echo $this->lang->line("Sign In"); ?></a>
               </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
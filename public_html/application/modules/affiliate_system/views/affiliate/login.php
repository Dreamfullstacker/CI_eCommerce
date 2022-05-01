<div class="container mt-5">
  <div class="row">
    <div class="col-12 col-sm-8 offset-sm-2 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3">
      <div class="login-brand">
        <a href="<?php echo base_url();?>"><img src="<?php echo base_url(); ?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>" width="200"></a>
      </div>

      <div class="card card-primary">
        <div class="card-header"><h4><i class="fas fa-sign-in-alt"></i> <?php echo $this->lang->line("Login"); ?></h4></div>
        <?php 
          if($this->session->flashdata('login_msg')!='') 
          {
              echo "<div class='alert alert-danger text-center'>"; 
                  echo $this->session->flashdata('login_msg');
              echo "</div>"; 
          }   
          if($this->session->flashdata('reset_success')!='') 
          {
              echo "<div class='alert alert-success text-center'>"; 
                  echo $this->session->flashdata('reset_success');
              echo "</div>"; 
          } 
          if($this->session->userdata('reg_success') != ''){
            echo '<div class="alert alert-success text-center">'.$this->session->userdata("reg_success").'</div>';
            $this->session->unset_userdata('reg_success');
          }    
          if(form_error('affilate_email') != '' || form_error('affiliate_password')!="" ) 
          {
            $form_error="";
            if(form_error('affilate_email') != '') $form_error .= form_error('affilate_email');
            if(form_error('affiliate_password') != '') $form_error .= form_error('affiliate_password');
            echo "<div class='alert alert-danger text-center'>".$form_error."</div>";
           
          } 
        ?>
        <div class="card-body">
          <form method="POST" action="<?php echo base_url('affiliate_system/affiliate_login'); ?>" class="needs-validation" novalidate="">
            <div class="form-group">
              <label for="affilate_email"><?php echo $this->lang->line("Email"); ?></label>
              <input id="affilate_email" type="email" class="form-control" value="<?php echo set_value('affilate_email'); ?>" name="affilate_email" tabindex="1" required autofocus>
              <!-- <div class="invalid-feedback">
                Please fill in your email
              </div> -->
            </div>

            <div class="form-group">
              <div class="d-block">
              	<label for="password" class="control-label"><?php echo $this->lang->line("Password"); ?></label>
                <div class="float-right">
                  <a href="<?php echo site_url();?>affiliate_system/forget_password" class="text-small">
                    <?php echo $this->lang->line("Forgot your password?"); ?>
                  </a>
                </div>
              </div>
              <input id="affiliate_password" type="password" class="form-control" name="affiliate_password" tabindex="2" required>

              <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">

              <!-- <div class="invalid-feedback">
                please fill in your password
              </div> -->
            </div>

            <div class="form-group mb-0">
              <button type="submit" class="btn btn-primary btn-lg btn-block login_btn" tabindex="4">
                <i class="fa fa-sign-in-alt"></i> <?php echo $this->lang->line("login"); ?>
              </button>
            </div>

            <div class="row">
                <div class="col-12">
                  <div class="text-muted text-center">
                    <br><?php echo $this->lang->line("Do not have an account?"); ?> <a href="<?php echo base_url('affiliate_system/affiliate_sign_up'); ?>"><?php echo $this->lang->line("Create one"); ?></a>
                  </div>
                </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

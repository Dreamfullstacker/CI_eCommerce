<div class="container mt-5">
  <div class="row">
    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3">
      <div class="login-brand">
        <a href="<?php echo base_url();?>"><img src="<?php echo base_url(); ?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>" width="200"></a>
      </div>

      <div class="card card-primary" >
        <div class="card-header"><h4><i class="fas fa-key"></i> <?php echo $this->lang->line("Reset Password"); ?></h4></div>

        <div class="card-body" id="recovery_form">
          <p class="text-muted"<?php echo $this->lang->line("You are one step away to get back access to your account") ?></p>
          <form method="POST">
            <div class="form-group">
              <label for="email"><?php echo $this->lang->line("Password Reset Code"); ?></label>
              <input id="code" type="text" class="form-control" name="code" tabindex="1" required autofocus>
              <div class="invalid-feedback"><?php echo $this->lang->line("Please enter your email"); ?></div>
            </div>

            <div class="row">
              <div class="form-group col-6">
                <label for="password" class="d-block"><?php echo $this->lang->line("New Password"); ?> *</label>
                <input id="new_password" type="password" class="form-control password" name="new_password">
                <div class="invalid-feedback"><?php echo $this->lang->line("You have to type new password twice"); ?></div>
              </div>
              <div class="form-group col-6">
                <label for="password2" class="d-block"><?php echo $this->lang->line("Confirm New Password");?> *</label>
                <input id="new_password_confirm" type="password" class="form-control password" name="new_password_confirm">
                <div class="invalid-feedback"><?php echo $this->lang->line("Passwords does not match"); ?></div>
              </div>
            </div>

            <div class="form-group">
              <button type="submit" id="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                <i class="fas fa-key"></i> <?php echo $this->lang->line("Reset Password"); ?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $('document').ready(function(){
    var confirm_match=0;
    $(".password").keyup(function(){
      var new_pass=$("#new_password").val();
      var conf_pass=$("#new_password_confirm").val();

      if(new_pass=='' || conf_pass=='') 
      {
        return false;
      }

      if(new_pass==conf_pass)
      {
          confirm_match=1;
          $("#new_password").removeClass('is-invalid');
          $("#new_password_confirm").removeClass('is-invalid');
      }
      else
      {
          confirm_match=0;
          $("#new_password_confirm").addClass('is-invalid');
      }

  });


  $("#submit").click(function(e){
    e.preventDefault();

    var is_code=$("#code").val();
    var new_pass=$("#new_password").val();
    var conf_pass=$("#new_password_confirm").val();
   
    if(is_code=='')
    {
        $("#code").addClass('is-invalid');
        return false;
    }
    else
    {
        $("#code").removeClass('is-invalid');
    }

    if(new_pass=='' || conf_pass=='')
    {
        $("#new_password").addClass('is-invalid');
        return false;
    }
    else
    {
        $("#new_password").removeClass('is-invalid');
    }

    if(confirm_match=='1')
    {
        $("#new_password_confirm").removeClass('is-invalid');
    }
    else
    {
        $("#new_password_confirm").addClass('is-invalid');
        return false;
    }
    
    var code=$("#code").val();
    var newp=$("#new_password").val();
    var conf=$("#new_password_confirm").val();
    $(this).addClass('btn-progress');
    $.ajax({
      context: this,
      type:'POST',
      url: "<?php echo base_url();?>home/recovery_check",
      data:{code:code,newp:newp,conf:conf},
      success:function(response)
      {
        $(this).removeClass('btn-progress');
        if(response=='0')
        {
         swal('<?php echo $this->lang->line("Error")?>', '<?php echo $this->lang->line("Password reset code does not match") ?>', 'error');
        }
        else if(response=='1')
        {
          swal('<?php echo $this->lang->line("Error")?>', '<?php echo $this->lang->line("Password reset code is expired") ?>', 'error');
        }
        else
        { 
          var string='<div class="alert alert-primary alert-has-icon"><div class="alert-icon"><i class="far fa-check-circle"></i></div><div class="alert-body"><div class="alert-title"><a href="<?php echo site_url();?>home/login"><?php echo $this->lang->line("You can login here") ?></a></div><?php echo $this->lang->line("Password has been updated successfully."); ?></div></div>';
          $("#recovery_form").slideUp();
          $("#recovery_form").html(string);
          $("#recovery_form").slideDown();
        }
    }
    });
    
  });
});
</script>

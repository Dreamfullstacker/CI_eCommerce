<div class="container vh-100">
<div class="col-md-6 img-block bg-img" style="background-image:url('<?php echo xit_load_images('images/login-or-signup.jpg'); ?>');min-height: 100%;"></div>
<div class="col-md-5 p-y-md m-x">
        <div class="" id="recovery_form">
          <form method="POST" class="form-white" <?php echo site_url();?>home/account_activation_action>
		  <p class="text-center"><a href="<?php echo base_url();?>"><img src="<?php echo base_url();?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>" width="200"></a></p>
		  <h5 class="text-center"><?php echo $this->lang->line("Account Activation");?></h5>
          <p class="text-muted"><?php echo $this->lang->line("Put your email and activation code that we sent to your email"); ?></p>
            <div class="form-group">
              <label for="email"><?php echo $this->lang->line("Email");?> *</label>
              <input id="email" type="email" class="form-control" name="email" tabindex="1" required autofocus>
              <div class="invalid-feedback"><?php echo $this->lang->line("Please enter your email"); ?></div>
            </div>
            <div class="form-group">
              <label for="email"><?php echo $this->lang->line("Account Activation Code");?> *</label>
              <input type="text" class="form-control" id="code" name="code" tabindex="1" required>
              <div class="invalid-feedback"><?php echo $this->lang->line("Please enter activation code"); ?></div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-blue text-uppercase" tabindex="4" name="submit" id="submit">
                <?php echo $this->lang->line("Activate My Account");?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>




<script type="text/javascript">
$('document').ready(function(){

  $("#submit").click(function(e){
    e.preventDefault();

    $("#msg").removeAttr('class');
    $("#msg").html("");

    var code=$("#code").val();
    var email=$("#email").val();  

    if(email=='')
    {
        $("#email").addClass('is-invalid');
        return false;
    }
    else
    {
        $("#email").removeClass('is-invalid');
    }

    if(code=='')
    {
        $("#code").addClass('is-invalid');
        return false;
    }
    else
    {
        $("#code").removeClass('is-invalid');
    }
    
    $(this).addClass('btn-progress');
    $.ajax({
      context: this,
      type:'POST',
      url: "<?php echo base_url();?>home/account_activation_action",
      data:{code:code,email:email},
      success:function(response){
            $(this).removeClass('btn-progress');
            if(response == 0)
            {
              swal('<?php echo $this->lang->line("Error")?>', '<?php echo $this->lang->line("Account activation code does not match") ?>', 'error');
            }
            if(response == 2)
            {
              var string='<div class="alert alert-primary alert-has-icon"><div class="alert-icon"><i class="far fa-check-circle"></i></div><div class="alert-body"><div class="alert-title"><a href="<?php echo site_url();?>home/login"><?php echo $this->lang->line("You can login here") ?></a></div><?php echo $this->lang->line("Congratulations, your account has been activated successfully."); ?></div></div>';
              $("#recovery_form").slideUp();
              $("#recovery_form").html(string);
              $("#recovery_form").slideDown();
            }
        }
    });
    
  });
});
</script>
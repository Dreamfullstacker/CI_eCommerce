<div class="container vh-100">
<div class="col-md-6 img-block bg-img" style="background-image:url('<?php echo xit_load_images('images/login-or-signup.jpg'); ?>');min-height: 100%;"></div>
<div class="col-md-5 p-y-md m-x">
        <div class="" id="recovery_form">
          <form method="POST">
		  <p class="text-center"><a href="<?php echo base_url();?>"><img src="<?php echo base_url();?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>" width="200"></a></p>
		  <h5 class="text-center"><?php echo $this->lang->line("Register your software"); ?></h5>
          <p class="text-muted"><?php echo $this->lang->line("Put purchase code to activate software"); ?></p>

            <div class="form-group">
              <label for="email"><?php echo $this->lang->line("Purchase Code"); ?> *</label>
              <input id="purchase_code" type="text" class="form-control" id="purchase_code" name="email" tabindex="1" autofocus>
              <div class="invalid-feedback"><?php echo $this->lang->line("Please enter purchase code"); ?></div>
            </div>

            <div class="form-group">
              <button type="submit" id="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
               <i class="far fa-paper-plane"></i> <?php echo $this->lang->line("Submit Purchase Code"); ?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>


<script type="text/javascript">
  $(document).ready(function(){    
    $(document).on('click','#submit',function(e){
      e.preventDefault();
      var purchase_code = $("#purchase_code").val().trim();
      if(purchase_code=='')
      {
          $("#purchase_code").addClass('is-invalid');
          return false;
      }
      else
      {
          $("#purchase_code").removeClass('is-invalid');
      }

      var domain_name = "<?php echo base_url(); ?>";

      $(this).addClass("btn-progress");
      $.ajax({
          context: this,
          type: "POST",
          url : "<?php echo site_url('home/credential_check_action'); ?>",
          data:{domain_name:domain_name,purchase_code:purchase_code},
          dataType: 'JSON',
          // async: false,
          success:function(response)
          {
            $(this).removeClass("btn-progress");
            if(response == "success")
            {
              var link = "<?php echo base_url('home/login'); ?>";
              window.location.assign(link);
            }
            else 
            {
			  var success_message=response.reason;
			  var span = document.createElement("span");
			  span.innerHTML = success_message;
			  swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'});
            }   
          }
        });


    });
  });
</script>

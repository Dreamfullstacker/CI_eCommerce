
<?php 
$general_signup_commission = isset($info['signup_commission']) ? $info['signup_commission']:0;
$general_sign_up_amount = isset($info['sign_up_amount']) ? $info['sign_up_amount']:'';
$general_payment_commission = isset($info['payment_commission']) ? $info['payment_commission']:0;
$general_payment_type = isset($info['payment_commission']) ? $info['payment_type']:'';
$general_payment_percentage = isset($info['percentage']) ? $info['percentage']:'';
$general_payment_fixed_amount = isset($info['fixed_amount']) ? $info['fixed_amount']:'';
$general_is_recurring = isset($info['is_recurring']) ? $info['is_recurring']:0;
?>

<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      	<div class="breadcrumb-item"><?php echo $this->lang->line("Affiliate System"); ?></div>
        <div class="breadcrumb-item active"><a href="<?php echo base_url('affiliate_system/affiliate_users'); ?>"><?php echo $this->lang->line("Affiliate Users"); ?></a></div>
      	<div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="row">
    <div class="col-12">

      <form class="form-horizontal" action="<?php echo site_url().'affiliate_system/add_affiliate_action';?>" method="POST">
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
        <div class="card">
          <div class="card-body">             
            <div class="row">
            	<div class="col-6">
            		<div class="form-group">
            		  <label for="name"> <?php echo $this->lang->line("Full Name")?> </label>
            		  <input name="name" value="<?php echo set_value('name');?>"  class="form-control" type="text">
            		  <span class="red"><?php echo form_error('name'); ?></span>
            		</div>
            	</div>

            	<div class="col-6">
            		<div class="form-group">
            		  <label for="username"> <?php echo $this->lang->line("username")?> *</label>
            		  <input name="username" value="<?php echo set_value('username');?>"  class="form-control" type="text">
            		  <span class="red"><?php echo form_error('username'); ?></span>
            		</div>
            	</div>

              <div class="col-6">
                <div class="form-group">
                  <label for="email"> <?php echo $this->lang->line("Email")?> *</label>
                  <input name="email" value="<?php echo set_value('email');?>"  class="form-control" type="email">
                  <span class="red"><?php echo form_error('email'); ?></span>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="mobile"><?php echo $this->lang->line("Mobile")?></label>              
                  <input name="mobile" value="<?php echo set_value('mobile');?>"  class="form-control" type="text">
                  <span class="red"><?php echo form_error('mobile'); ?></span>               
                </div>
              </div>

              <div class="col-12">
              	<div class="form-group">
              	  <label for="address"> <?php echo $this->lang->line("Address")?></label>
              	  <textarea name="address" class="form-control"><?php echo set_value('address');?></textarea>
              	  <span class="red"><?php echo form_error('address'); ?></span>
              	</div> 
              </div>
            </div>

            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="password"> <?php echo $this->lang->line("Password")?> *</label>
                  <input name="password" value="<?php echo set_value('password');?>"  class="form-control" type="password">
                  <span class="red"><?php echo form_error('password'); ?></span>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="confirm_password"> <?php echo $this->lang->line("Confirm Password")?> *</label>
                  <input name="confirm_password" value="<?php echo set_value('confirm_password');?>"  class="form-control" type="password">
                  <span class="red"><?php echo form_error('confirm_password'); ?></span>
                </div>
              </div>
            </div>

            <div class="row">
            	<div class="col-12 col-md-6">
            		<div class="form-group">
            			<label for="status" > <?php echo $this->lang->line('Status');?></label><br>
            			<label class="custom-switch mt-2">
            				<input type="checkbox" name="status" value="1" class="custom-switch-input" checked>
            				<span class="custom-switch-indicator"></span>
            				<span class="custom-switch-description"><?php echo $this->lang->line('Active');?></span>
            				<span class="red"><?php echo form_error('status'); ?></span>
            			</label>
            		</div>
            	</div>

              	<div class="col-12 col-md-6">
	              	<div class="form-group">
	              		<label for="status"><?php echo $this->lang->line('Set Custom Commission');?>
                      <a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Commission Settings"); ?>" data-content="<?php echo $this->lang->line("If you want to set special commission for this affiliate on signup/payment then enable it to start the procedure. If you set special commission, this affiliate will get commissions based on this settings instead of Generic payment setttings for affiliate."); ?>"><i class='fa fa-info-circle'></i> </a>
                    </label><br>
	              		<label class="custom-switch mt-2">
	              			<input type="checkbox" name="is_overwritten" id="is_overwritten" value="1" class="custom-switch-input">
	              			<span class="custom-switch-indicator"></span>
	              			<span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
	              			<span class="red"><?php echo form_error('is_overwritten'); ?></span>
	              		</label>
	              	</div>
              	</div>      
            </div>

            <div class="row" id="commission_section" style="display: none;">
            	<div class="col-12">
            		<div class="card">
            			<div class="card-header" style="border:1px solid #e4e6fc;border-bottom:none;">
            				<h4><i class="fas fa-gift"></i> <?php echo $this->lang->line('Affiliate Payment'); ?></h4>
            			</div>

            			<div class="card-body" style="border:1px solid #e4e6fc">
            				<div class="row">
            					<div class="col-6">
       									<div class="form-group mt-2">
       										<div class="custom-control custom-checkbox">
       										    <input type="checkbox" value="1" id="by_signup" name="signup_commission" class="custom-control-input" <?php if($general_signup_commission == '1') echo "checked"; ?>>
       										    <label class="custom-control-label pointer" for="by_signup"><?php echo $this->lang->line("Signup Commission"); ?>
                                <a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Signup Commission"); ?>" data-content="<?php echo $this->lang->line("Affiliate will get commission on every user signup who have come through the affiliate link."); ?>"><i class='fa fa-info-circle'></i> </a>         
                              </label>
       										</div>
       									</div>

                        <div class="form-group" id="signup_sec_div" <?php if($general_signup_commission == '1') echo 'style="display: block;"'; else echo 'style="display: none;"'; ?>>
                          <label for="signup_amount"><i class="fas fa-briefcase"></i> <?php echo $this->lang->line('Amount'); ?></label>
                          <div class="input-group">
                            <div class="input-group-prepend" title="<?php echo $this->lang->line('Currency'); ?>">
                              <div class="input-group-text">
                                <?php echo $curency_icon; ?>
                              </div>
                            </div>
                            <input type="text" class="form-control" name="signup_amount" id="signup_amount" value="<?php echo $general_sign_up_amount;?>">
                          </div>
                        </div>
            					</div>

            					<div class="col-6">
            						<div class="form-group mt-2">
            							<div class="custom-control custom-checkbox">
            								<input type="checkbox" value="1" id="by_payment" name="is_payment" class="custom-control-input" <?php if($general_payment_commission == '1') echo "checked"; ?>>
            								<label class="custom-control-label pointer" for="by_payment"><?php echo $this->lang->line("Payment Commission"); ?> 
                              <a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Payment Commission"); ?>" data-content="<?php echo $this->lang->line("Affiliate will get commission on every package buying payment who have registered with the affiliate link."); ?>"><i class='fa fa-info-circle'></i> </a>   
                          </label>
            							</div>
            						</div>

                          <div id="payment_sec_div" <?php if($general_payment_commission == '1') echo 'style="display: block;"'; else echo 'style="display: none;"'; ?> >
                            <div class="row">
                              <div class="col-4">
                                <div class="form-group">
                                  <label for=""><i class="fas fa-ankh"></i> <?php echo $this->lang->line('Fixed');?></label><br>
                                  <label class="custom-switch mt-2">
                                    <input type="radio" name="payment_type" id="payment_type" value="fixed" class="custom-switch-input" <?php if($general_payment_type !='' && $general_payment_type =='fixed') echo 'checked'; ?>>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                                    <span class="red"><?php echo form_error('payment_type'); ?></span>
                                  </label>
                                </div>
                              </div>
                              <div class="col-4">
                                <div class="form-group">
                                  <label for=""><i class="fas fa-percent"></i> <?php echo $this->lang->line('Percentage');?></label>
                                  <br>
                                  <label class="custom-switch mt-2">
                                    <input type="radio" name="payment_type" id="payment_type" value="percentage" class="custom-switch-input" <?php if($general_payment_type !='' && $general_payment_type =='percentage') echo 'checked'; ?>>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                                    <span class="red"><?php echo form_error('payment_type'); ?></span>
                                  </label>
                                </div>
                              </div>

                              <div class="col-4">
                                <div class="form-group">
                                  <label for=""><i class="fas fa-hand-holding-usd"></i> <?php echo $this->lang->line('Recurring');?></label>
                                  <br>
                                  <label class="custom-switch mt-2">
                                    <input type="checkbox" name="is_recurring" id="is_recurring" value="1" class="custom-switch-input" <?php if($general_is_recurring == '1') echo 'checked'; ?>>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                                    <span class="red"><?php echo form_error('is_recurring'); ?></span>
                                  </label>
                                </div>
                              </div>
                            </div>

                            <div class="form-group" id="fixed_amount_div" <?php if($general_payment_type == 'fixed') echo 'style="display: block;"'; else echo 'style="display: none;"'; ?>>
                              <div class="input-group">
                                <div class="input-group-prepend" title="<?php echo $this->lang->line('Currency'); ?>">
                                  <div class="input-group-text">
                                    <?php echo $curency_icon; ?>
                                  </div>
                                </div>
                                <input type="text" class="form-control" name="fixed_amount" id="fixed_amount" value="<?php echo $general_payment_fixed_amount;?>">
                                <span class="red"><?php echo form_error('fixed_amount'); ?></span>
                              </div>
                            </div>

                            <div class="form-group" id="percentage_div" <?php if($general_payment_type == 'percentage') echo 'style="display: block;"'; else echo 'style="display: none;"'; ?>>
                              <div class="input-group">
                                <div class="input-group-prepend">
                                  <div class="input-group-text">
                                    <i class="fas fa-percent"></i>
                                  </div>
                                </div>
                                <input type="text" class="form-control" name="percent_amount" id="percent_amount" value="<?php echo $general_payment_percentage; ?>">
                                <span class="red"><?php echo form_error('percent_amount'); ?></span>
                              </div>
                            </div>
                          </div>
            					</div>
            				</div>
            			</div>
            		</div>
            	</div>
            </div>
          </div>

          <div class="card-footer bg-whitesmoke">
            <button name="submit" type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
            <button  type="button" class="btn btn-secondary btn-lg float-right" onclick='goBack("affiliate_system/affiliate_users",0)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
          </div>
        </div>
      </form>  
    </div>
  </div>
</section>


<script>
	$(document).ready(function() {

		$(document).on('change', '#is_overwritten', function(event) {
			event.preventDefault();

			if($(this).prop('checked') == true) {
				$("#commission_section").show(500);
			} else {
				$("#commission_section").hide(500);
			}
		});


		$(document).on('change', '#by_signup', function(event) {
			event.preventDefault();

			if($(this).prop('checked')==true) {
			    $("#signup_sec_div").show(500);
			} else {   
			    $("#signup_sec_div").hide(500);
			}
		});
		
		$(document).on('change', '#by_payment', function(event) {
			event.preventDefault();

			if($(this).prop('checked')==true) {
			    $("#payment_sec_div").show(500);

			} else {   
			    $("#payment_sec_div").hide(500);
			}
		});

		$(document).on('change', '#payment_type', function(event) {
			event.preventDefault();

			if($(this).val() == 'fixed') {
				$("#fixed_amount_div").show(500);
				$("#percentage_div").hide(500);
			} else {
				$("#fixed_amount_div").hide(500);
			}

			if($(this).val() == 'percentage') {
				$("#percentage_div").show(500);
			} else {
				$("#percentage_div").hide(500);

			}
		});
	});
</script>
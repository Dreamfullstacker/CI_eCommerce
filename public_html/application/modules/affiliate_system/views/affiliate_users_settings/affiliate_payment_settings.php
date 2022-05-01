
<section class="section section_custom">
	<div class="section-header">
		<h1><i class="fas fa-handshake"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><?php echo $this->lang->line("Affiliate System"); ?></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>
	<?php $this->load->view('admin/theme/message'); ?>
	<div class="section-body">
		<div class="row">
			<div class="col-12">
				<form action="<?php echo base_url("affiliate_system/affiliate_payment_settings_action"); ?>" method="POST">
					<div class="card">
						<div class="card-body">
							<div class="row mb-3">
								<div class="col-12">
									<p class="section-title mt-0">
										<?php echo $this->lang->line('Affiliate signup URL'); ?>
										<a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Affiliate Signup URL"); ?>" data-content="<?php echo $this->lang->line("Copy this URL and provide this to your affiliate to signup for affiliation."); ?>"><i class='fa fa-info-circle'></i> </a>   
									</p>
									<div id="link_div">
									  <pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword"><?php echo site_url("affiliate_system/affiliate_sign_up"); ?></span></code></pre>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12"><p class="section-title"><?php echo $this->lang->line('Commission Settings'); ?></p></div>
								<div class="col-12 col-md-6">
									<ul class="list-group mb-4">
										<li class="list-group-item">
											<div class="form-group mb-0">
												<div class="custom-control custom-checkbox">
												    <input type="checkbox" value="1" id="by_signup" name="signup_commission" class="custom-control-input" <?php if(isset($info['signup_commission']) && $info['signup_commission'] == '1') echo "checked"; else echo ""; ?>>
												    <label class="custom-control-label pointer" for="by_signup"><?php echo $this->lang->line("Signup Commission"); ?>
												    	<a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Signup Commission"); ?>" data-content="<?php echo $this->lang->line("Affiliate will get commission on every user signup who have come through the affiliate link."); ?>"><i class='fa fa-info-circle'></i> </a> 
												    </label>
												</div>
											</div>
										</li>
									</ul>

									<div class="card" id="signup_sec_div" style="display: none;">
										<div class="card-header p-3" style="border:1px solid #e4e6fc;border-bottom:none;">
											<h4><?php echo $this->lang->line('Signup Amount'); ?> </h4>
										</div>
										<div class="card-body p-3" style="border:1px solid #e4e6fc;">
											<div class="form-group">
												<label for="signup_amount"><i class="fas fa-briefcase"></i> <?php echo $this->lang->line('Amount'); ?></label>
												<div class="input-group">
													<div class="input-group-prepend" title="<?php echo $this->lang->line('Currency'); ?>">
														<div class="input-group-text">
															<?php echo $curency_icon; ?>
														</div>
													</div>
													<input type="text" class="form-control" name="signup_amount" id="signup_amount" value="<?php echo isset($info['sign_up_amount'])? $info['sign_up_amount']:""; ?>">
												</div>
												<span class="red"><?php echo form_error("signup_amount"); ?></span>
												
											</div>
										</div>
									</div>
								</div>

								<div class="col-12 col-md-6">
									<ul class="list-group mb-4">
										<li class="list-group-item">
											<div class="form-group mb-0">
												<div class="custom-control custom-checkbox">
												    <input type="checkbox" value="1" id="by_payment" name="payment_commission" class="custom-control-input" <?php if(isset($info["payment_commission"]) && $info["payment_commission"] == '1') echo "checked"; echo ""; ?>>
												    <label class="custom-control-label pointer" for="by_payment"><?php echo $this->lang->line("Payment Commission"); ?>
												    	<a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Payment Commission"); ?>" data-content="<?php echo $this->lang->line("Affiliate will get commission on every package buying package payment who have registered with the affiliate link."); ?>"><i class='fa fa-info-circle'></i> </a>   
												    </label>
												</div>
											</div>
										</li>
									</ul>

									<div class="card" id="payment_sec_div" style="display: none;">
										<div class="card-header p-3" style="border:1px solid #e4e6fc;border-bottom:none;">
											<h4><?php echo $this->lang->line('Payment Type'); ?> </h4>
										</div>

										<div class="card-body p-3" style="border:1px solid #e4e6fc;">
											<div class="row">
												<div class="col-4">
													<div class="form-group">
														<label for=""><i class="fas fa-ankh"></i> <?php echo $this->lang->line('Fixed');?></label><br>
														<label class="custom-switch mt-2">
															<input type="radio" name="payment_type" id="payment_type" value="fixed" class="custom-switch-input" <?php if(isset($info["payment_type"]) && $info["payment_type"]=='fixed') echo "checked"; else echo ""; ?>>
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
													    <input type="radio" name="payment_type" id="payment_type" value="percentage" class="custom-switch-input" <?php if(isset($info["payment_type"]) && $info["payment_type"]=='percentage') echo "checked"; else echo ""; ?>>
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
													    <input type="checkbox" name="is_recurring" id="is_recurring" value="1" class="custom-switch-input" <?php if(isset($info["is_recurring"]) && $info["is_recurring"]=='1') echo "checked"; else echo ""; ?>>
													    <span class="custom-switch-indicator"></span>
													    <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
													    <span class="red"><?php echo form_error('is_recurring'); ?></span>
													  </label>
													</div>
												</div>
											</div>

											<div class="form-group" id="fixed_amount_div" <?php if(isset($info["payment_type"]) && $info["payment_type"]=='fixed') echo 'style="display: block;"'; else echo 'style="display: none;"'; ?>>
												<div class="input-group">
													<div class="input-group-prepend" title="<?php echo $this->lang->line('Currency'); ?>">
														<div class="input-group-text">
															<?php echo $curency_icon; ?>
														</div>
													</div>
													<input type="text" class="form-control" name="fixed_amount" id="fixed_amount" value="<?php echo isset($info['fixed_amount']) ? $info['fixed_amount']:""; ?>">
													<span class="red"><?php echo form_error('fixed_amount'); ?></span>
												</div>
											</div>

											<div class="form-group" id="percentage_div" <?php if(isset($info["payment_type"]) && $info["payment_type"]=='percentage') echo 'style="display: block;"'; else echo 'style="display: none;"'; ?>>
												<div class="input-group">
													<div class="input-group-prepend">
														<div class="input-group-text">
															<i class="fas fa-percent"></i>
														</div>
													</div>
													<input type="text" class="form-control" name="percent_amount" id="percent_amount" value="<?php echo isset($info['percentage']) ? $info['percentage']:""; ?>">
													<span class="red"><?php echo form_error('percent_amount'); ?></span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="card-footer bg-whitesmoke d-block text-center">
							<button class="btn btn-primary w-50 btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line('Save'); ?></button>
						</div>
					</div>
			    </form>
			</div>
		</div>
	</div>
</section>

<script>
	$(document).ready(function() {

		if($("#by_signup").prop('checked')==true) {
		    $("#signup_sec_div").show(500);

		} else {   
		    $("#signup_sec_div").hide(500);
		}

		if($("#by_payment").prop('checked')==true) {
		    $("#payment_sec_div").show(500);

		} else {   
		    $("#payment_sec_div").hide(500);
		}
		
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
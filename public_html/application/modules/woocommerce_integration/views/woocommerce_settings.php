<style>
	.blue{
		color: #2C9BB3 !important;
	}
</style>

<section class="section">
	<div class="section-header">
		   <h1><i class="fab fa-wordpress"></i> <?php echo $page_title; ?></h1>
		   <div class="section-header-breadcrumb">
		     <div class="breadcrumb-item"><a href="<?php echo base_url('integration'); ?>"><?php echo $this->lang->line("Integration"); ?></a></div>
		     <div class="breadcrumb-item"><a href="<?php echo base_url('woocommerce_integration'); ?>"><?php echo $this->lang->line("WooCommerce Integration"); ?></a></div>
		     <div class="breadcrumb-item"><?php echo $page_title; ?></div>
		   </div>
	</div>

	
 	<?php $this->load->view('admin/theme/message');?>


	
	<div class="section-body">
	  <div class="row">
	    <div class="col-12">
	        <form action="<?php echo base_url("woocommerce_integration/woocommerce_settings_update_action"); ?>" method="POST">
	        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
        	<input type="hidden" name="table_id" value="<?php echo $table_id ?>">
	        <div class="card">
	          <div class="card-header"><h4 class="card-title"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("WooCommerce API Settings"); ?></h4></div>
	          <div class="card-body"> 

	              <div class="row">
		                <div class="col-12">
		                  <div class="form-group">
		                    <label for=""><?php echo $this->lang->line("Consumer Key");?> *</label>
		                    <input name="consumer_key" value="<?php echo isset($woocommerce_settings['consumer_key']) ? $woocommerce_settings['consumer_key'] : set_value('consumer_key'); ?>" class="form-control" type="text">  
		                    <span class="red"><?php echo form_error('consumer_key'); ?></span>
		                  </div>
		                </div>

		                <div class="col-12">
		                  <div class="form-group">
		                    <label for=""><?php echo $this->lang->line("Consumer Secret");?> *</label>
		                    <input name="consumer_secret" value="<?php echo isset($woocommerce_settings['consumer_secret']) ? $woocommerce_settings['consumer_secret'] : set_value('consumer_secret'); ?>" class="form-control" type="text">  
		                    <span class="red"><?php echo form_error('consumer_secret'); ?></span>
		                  </div>
		                </div>

		                <div class="col-12">
		                  <div class="form-group">
		                    <label for=""><?php echo $this->lang->line("Website Home URL");?> *</label>
		                    <input name="home_url" value="<?php echo isset($woocommerce_settings['home_url']) ? $woocommerce_settings['home_url'] : set_value('home_url'); ?>" class="form-control" type="text">  
		                    <span class="red"><?php echo form_error('home_url'); ?></span>
		                  </div>
		                </div>
	              </div>
	          </div>

	          <div class="card-footer bg-whitesmoke">
	            <button class="btn btn-primary btn-lg" id="save-btn" type="submit"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save & Sync Data");?></button>
	            <button class="btn btn-secondary btn-lg float-right" onclick='goBack("woocommerce_integration")' type="button"><i class="fa fa-remove"></i>  <?php echo $this->lang->line("Cancel");?></button>
	          </div>
	        </div>
	      </form>
	    </div>
	  </div>
	</div>
	   				

</section>
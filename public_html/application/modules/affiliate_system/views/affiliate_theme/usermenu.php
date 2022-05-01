<li class="dropdown" id="xxx"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
  <img src="<?php echo $this->session->userdata("affiliate_pic"); ?>" class="rounded-circle mr-1">
  <div class="d-sm-none d-lg-inline-block"><?php echo $this->session->userdata('affiliate_username'); ?></div></a>
  <div class="dropdown-menu dropdown-menu-right">

    <div class="dropdown-title"><?php echo $this->config->item("product_short_name");?></div>
    <a href="<?php echo base_url('affiliate_system/profile'); ?>" class="dropdown-item has-icon">
      <i class="far fa-user"></i> <?php echo $this->lang->line("Profile"); ?>
    </a>
<!--     <a href="<?php echo base_url('calendar/index'); ?>" class="dropdown-item has-icon">
      <i class="fas fa-bolt"></i> <?php echo $this->lang->line("Activities"); ?>
    </a>

    <div class="dropdown-divider"></div> -->
    <a href="<?php echo base_url('affiliate_system/reset_password'); ?>" class="dropdown-item has-icon">
      <i class="fas fa-key"></i> <?php echo $this->lang->line("Change Password"); ?>
    </a>  

    <a href="<?php echo base_url('affiliate_system/affiliate_logout'); ?>" class="dropdown-item has-icon text-danger">
      <i class="fas fa-sign-out-alt"></i> <?php echo $this->lang->line("Logout"); ?>
    </a>


  </div>
</li>
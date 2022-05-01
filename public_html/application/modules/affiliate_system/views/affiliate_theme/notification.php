<li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg"><i class="far fa-bell"></i></a>
  <div class="dropdown-menu dropdown-list dropdown-menu-right">
    <div class="dropdown-header"><?php echo $this->lang->line('Notifications'); ?>
      <div class="float-right"><?php echo $this->lang->line("Nothing new"); ?></div>
    </div>
    <div class="dropdown-list-content dropdown-list-icons">
        <a href="#" class="dropdown-item">
          <div class="dropdown-item-icon text-white">
            <i class=""></i>
          </div>
          <div class="dropdown-item-desc">

            <div class="time"></div>
          </div>
        </a>
    </div>
    <div class="dropdown-footer text-center">
      <a href="#"><?php echo $this->lang->line('View all');?> <i class="fas fa-chevron-right"></i></a>
    </div>
  </div>
</li>
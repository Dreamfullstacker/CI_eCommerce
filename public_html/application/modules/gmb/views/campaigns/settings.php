<section class="section">
    <div class="section-header">
        <h1><i class="fas fa-hands-helping"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url('gmb'); ?>"><?php echo $this->lang->line("Google My Business"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon text-primary">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo $this->lang->line("Post campaigns"); ?></h4>
                        <p><?php echo $this->lang->line("Create campaigns using CTA, Event or Offer posts"); ?></p>
                        <a href="<?php echo base_url("gmb/posts"); ?>" class="card-cta"><?php echo $this->lang->line("Campaign list"); ?> <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon text-primary">
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo $this->lang->line("Media campaigns"); ?></h4>
                        <p><?php echo $this->lang->line("Create campaings using images or videos"); ?></p>
                        <a href="<?php echo base_url("gmb/media_campaigns"); ?>" class="card-cta"><?php echo $this->lang->line("Campaign list"); ?> <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <?php if($this->session->userdata('user_type') == 'Admin' || in_array(305,$this->module_access)) : ?>
            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon text-primary">
                        <i class="fas fa-rss"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo $this->lang->line("RSS Auto Post"); ?></h4>
                        <p><?php echo $this->lang->line("Create campaigns using RSS auto posts"); ?></p>
                        <a href="<?php echo base_url("gmb/rss"); ?>" class="card-cta"><?php echo $this->lang->line("Campaign list"); ?> <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
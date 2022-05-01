<?php include("application/modules/sms_email_sequence/views/sms_email_manager/sequence/sms_email_sequence_css.php"); ?>
<?php include("application/modules/sms_email_sequence/views/sms_email_manager/sequence/sms_email_sequence_module_js.php"); ?>

<style>
    /*.dropdown-toggle::after{content:none !important;}*/
    #template_text{max-width: 40% !important;}
</style>
<input type="hidden" name="sms_email_sequence_csrf_token" id="sms_email_sequence_csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-th-list"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-button">

            <button class="btn btn-primary create_new_template" template_type="<?php echo $template_type; ?>"><?php echo $this->lang->line('New template'); ?></button>

        </div>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("messenger_bot_broadcast"); ?>"><?php echo $this->lang->line("Broadcasting"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body data-card">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="input-group float-left" id="searchbox">
                                    <input type="text" class="form-control" id="template_text" name="template_text" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>" aria-label="" aria-describedby="basic-addon2">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive2 data-card">
                                    <input type="hidden" id="template_type" name="template_type" value="<?php echo $template_type; ?>">
                                    <table class="table table-bordered" id="mytable_sms_email_templates">
                                        <thead>
                                            <tr>
                                                <th>#</th>      
                                                <th><?php echo $this->lang->line("ID"); ?></th>      
                                                <th><?php echo $this->lang->line("Name"); ?></th>
                                                <th><?php echo $this->lang->line('Actions'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>            
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>


<div class="modal fade" id="create_template_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="min-width:70%;">
        <div class="modal-content">
            <div class="modal-header bbw">
                <h5 class="modal-title text-primary"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create Template") ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12" id="name-div">
                        <div class="form-group">
                            <label><?php echo $this->lang->line("Template Name"); ?></label>
                            <input type="text" class="form-control" name="template_name" id="template_name">
                        </div>
                    </div>
                    <div class="col-12 col-md-6" id="subject-div">
                        <div class="form-group">
                            <label><?php echo $this->lang->line("Subject"); ?></label>
                            <input type="text" class="form-control" name="template_subject" id="template_subject">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label><?php echo $this->lang->line("content"); ?></label>
                            <span class='float-right'> 
                              <a title="<?php echo $this->lang->line("You can include #LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                            </span>
                            <span class='float-right'> 
                              <a title="<?php echo $this->lang->line("You can include #FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                            </span>
                            <textarea name="template_contents" id="template_contents" class="form-control template_contents"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke">
                <button class="btn btn-lg btn-primary" button-type="" id="save_template" name="save_template" type="button"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save") ?> </button>
                <a class="btn btn-lg btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="update_template_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="min-width:70%;">
        <div class="modal-content">
            <div class="modal-header bbw">
                <h5 class="modal-title text-primary"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Update Template") ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            
            <div class="modal-body" id="update_template_content"></div>

            <div class="modal-footer bg-whitesmoke">
                <button class="btn btn-lg btn-primary" button-type="" id="update_template" name="update_template" type="button"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Update") ?> </button>
                <a class="btn btn-lg btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="email-template-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="fa fa-eye"></i> <?php echo $this->lang->line("Email Template") ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            
            <div class="modal-body p-0 pt-3" id="email-template-content"></div>

            <div class="modal-footer bg-whitesmoke">
                <a class="btn btn-lg btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
            </div>

            <div class="xit-spinner text-primary">
                <i class="fa fa-spinner fa-spin fa-3x" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</div>
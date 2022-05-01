<?php include("application/modules/messenger_bot_connectivity/views/json_api_connector_js.php"); ?>

<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-plug"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-button">
            <a class="btn btn-primary add_connector" id="add_feed" href="#">
                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Connection"); ?>
            </a> 
        </div>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item">
                <a href="<?php echo base_url("messenger_bot"); ?>">
                    <?php echo $this->lang->line("Messenger Bot"); ?>
                </a>
            </div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body data-card">
                        <div class="row">
                            <div class="col-md-9 col-12">
                                <input type="text" class="form-control" id="searching" name="searching" placeholder="<?php echo $this->lang->line('Search...'); ?>" aria-label="" aria-describedby="basic-addon2">
                            </div>
                            <div class="col-md-3 col-12">
                                <a href="javascript:;" id="post_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> <?php echo $this->lang->line("Choose Date");?></a>
                                <input type="hidden" id="post_date_range_val">
                            </div>
                        </div>

                        <div class="table-responsive2">
                            <table class="table table-bordered" id="mytable">
                                <thead>
                                    <tr>
                                        <th>#</th>      
                                        <th><?php echo $this->lang->line("Campaign ID"); ?></th>      
                                        <th><?php echo $this->lang->line("Name"); ?></th>
                                        <th><?php echo $this->lang->line("Webhook URL"); ?></th>
                                        <th><?php echo $this->lang->line("Actions"); ?></th>  
                                        <th><?php echo $this->lang->line("Page Name"); ?></th>
                                        <th><?php echo $this->lang->line("Created"); ?></th>
                                        <th><?php echo $this->lang->line("Last Triggered Time"); ?></th>
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
</section> 


<div class="modal fade" id="view_connector_info_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega" style="min-width: 85%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye"></i> <?php echo $this->lang->line("Report");?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-list-alt"></i> <?php echo $this->lang->line('Campaign Details'); ?></h4>
                            </div>
                            <div class="card-body" style="padding-bottom:0 !important;">
                                <div id="info_modal"></div>
                            </div>
                        </div>
                    </div>
                </div><br>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-flag-checkered"></i> <?php echo $this->lang->line('Last 10 Activities'); ?></h4>
                            </div>

                            <div class="card-body data-card">
                                <div class="table-responsive2">
                                    <input type="hidden" id="put_row_id">
                                    <table class="table table-bordered" id="mytable1">
                                    <thead>
                                        <tr>
                                            <th>#</th>      
                                            <th><?php echo $this->lang->line("ID"); ?></th>      
                                            <th><?php echo $this->lang->line("Http Code"); ?></th>
                                            <th><?php echo $this->lang->line("Curl Error"); ?></th>
                                            <th><?php echo $this->lang->line("Post Data"); ?></th>
                                            <th><?php echo $this->lang->line("Post Time"); ?></th>
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
</div>


<div class="modal fade" id="add_new_connector_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content" style="padding:5px;">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add New Connection");?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div id="AddconnectorBody">
                            <form id="json_api_connector_form" action="" method="POST">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line("Name"); ?></label>
                                            <input type="text" class="form-control" id="name" placeholder="<?php echo $this->lang->line("Enter your connector name"); ?>" name="connector_name">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6"> 
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line("Please select a page");?></label>
                                            <select name="page_table_id" id="page_table_id" class="form-control select2" style="width:100%;">
                                            <?php
                                                echo "<option value=''>{$this->lang->line('Please select a page')}</option>";
                                                foreach($page_info as $key => $val)
                                                {
                                                    $page_id   = $val['id'];
                                                    $page_name = $val['page_name'];
                                                    echo "<option value='{$page_id}'>{$page_name}</option>";
                                                }

                                            ?>
                                            </select>
                                        </div>       
                                    </div> 

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line("Webhook URL"); ?></label>
                                            <input type="text" class="form-control" id="webhook_url" placeholder="<?php echo $this->lang->line("Enter your webhook URL"); ?>" name="webhook_url">
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line("What Field Change Trigger Webhook"); ?></label>
                                            <div class="row">
                                                <div class="col-12 col-md-3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="trigger_email" id="trigger_email" name="field[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="trigger_email"><?php echo $this->lang->line("Email"); ?></label>
                                                    </div>

                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="trigger_phone_number" id="trigger_phone_number" name="field[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="trigger_phone_number"><?php echo $this->lang->line("Phone number"); ?></label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="trigger_location" id="trigger_location" name="field[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="trigger_location"><?php echo $this->lang->line("Location"); ?></label>
                                                    </div>

                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="trigger_birthdate" id="trigger_birthdate" name="field[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="trigger_birthdate"><?php echo $this->lang->line("Birthdate"); ?></label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="trigger_postbackid" id="trigger_postbackid" name="field[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="trigger_postbackid"><?php echo $this->lang->line("Postback ID"); ?></label>
                                                    </div>

                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="trigger_webview" id="trigger_webview" name="field[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="trigger_webview"><?php echo $this->lang->line("Webview Form"); ?></label>
                                                    </div>
                                                </div>

                                                <?php if($this->is_input_flow_addon_exists) { ?>
                                                    <?php if($this->basic->is_exist("modules",array("id"=>292))) { ?>
                                                        <?php if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access)) {  ?>
                                                        <div class="col-12 col-md-3">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" value="trigger_user_input" id="trigger_user_input" name="field[]" class="custom-control-input">
                                                                <label class="custom-control-label" for="trigger_user_input"><?php echo $this->lang->line("User input flow"); ?></label>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6" id="postback_div" style="display: none;"></div>
                                    <div class="col-12 col-md-6" id="webview_div" style="display: none;"></div>

                                    <?php if($this->is_input_flow_addon_exists) { ?>
                                        <?php if($this->basic->is_exist("modules",array("id"=>292))) { ?>
                                            <?php if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access)) {  ?>
                                            <div class="col-12 col-md-6" id="input_flow_div" style="display: none;"></div>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>

                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line("Which Data You Want To Send");?></label>
                                            <div class="row">
                                                <div class="col-12 col-md-3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="psid" id="psid" name="variable_post[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="psid"><?php echo $this->lang->line("PSID"); ?></label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="subscribed_at" id="subscribed_at" name="variable_post[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="subscribed_at"><?php echo $this->lang->line("Subscribed At"); ?></label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="postbackid" id="postbackid" name="variable_post[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="postbackid"><?php echo $this->lang->line("Postback ID"); ?></label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="formdata" id="formdata" name="variable_post[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="formdata"><?php echo $this->lang->line("Webview form data"); ?></label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="first_name" id="first_name" name="variable_post[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="first_name"><?php echo $this->lang->line("First Name"); ?></label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="last_name" id="last_name" name="variable_post[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="last_name"><?php echo $this->lang->line("Last Name"); ?></label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="email" id="email" name="variable_post[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="email"><?php echo $this->lang->line("Email"); ?></label>
                                                    </div>

                                                    <?php if($this->is_input_flow_addon_exists) { ?>
                                                        <?php if($this->basic->is_exist("modules",array("id"=>292))) { ?>
                                                            <?php if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access)) {  ?>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" value="user_input_flow_campaign" id="user_input_flow_campaign" name="variable_post[]" class="custom-control-input">
                                                                <label class="custom-control-label" for="user_input_flow_campaign"><?php echo $this->lang->line("User input data"); ?></label>
                                                            </div>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>

                                                <div class="col-12 col-md-3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="page_id" id="page_id" name="variable_post[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="page_id"><?php echo $this->lang->line("Page ID"); ?></label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="page_name" id="page_name" name="variable_post[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="page_name"><?php echo $this->lang->line("Page Name"); ?></label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="phone_number" id="phone_number" name="variable_post[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="phone_number"><?php echo $this->lang->line("Phone number"); ?></label>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-12 col-md-3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="birthdate" id="birthdate" name="variable_post[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="birthdate"><?php echo $this->lang->line("Birthdate"); ?></label>
                                                    </div>

                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="user_location" id="user_location" name="variable_post[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="user_location"><?php echo $this->lang->line("Location"); ?></label>
                                                    </div>
                                                    

                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" value="labels" id="labels" name="variable_post[]" class="custom-control-input">
                                                        <label class="custom-control-label" for="labels"><?php echo $this->lang->line("Labels"); ?></label>
                                                    </div>

                                                    
                                                </div>    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-12">
                        <button id="save_added_connector_infos" class="btn btn-lg btn-primary float-left"><i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?></button>
                        <a id="cancel" class="btn btn-lg btn-light float-right" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-times"></i> <?php echo $this->lang->line('Cancel'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="update_connector_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content" style="padding:5px;">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Update Connector");?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div id="updateConnectorForm"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view_post_data_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title blue"><i class="fas fa-dice-d6"></i> <?php echo $this->lang->line('Post Data Lists'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="card card-primary">
                    <div class="card-header">
                        <h4><?php echo $this->lang->line('JSON Post Data'); ?></h4>
                    </div>
                    <div class="card-body">
                        <div id="json_formate_data"></div><br>
                    </div>
                </div>
                <div class="card card-primary">
                    <div class="card-header">
                        <h4><?php echo $this->lang->line('Formatted Display'); ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="infos"></div>
                    </div>
                </div>

                <div class="card card-primary user_input_flows_card">
                    <div class="card-header">
                        <h4><?php echo $this->lang->line('Formatted User Input Data Display'); ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="user_input_flows"></div>
                    </div>
                </div>
        </div>
        </div>
    </div>
</div>


<style type="text/css">
    .activities .activity .activity-detail{width:100%;padding: 0 15px 0 0;box-shadow: none !important;}
    .activity-detail::before { content: none !important; }
    .activity::before{content:none !important;}
    .activities:last-child{border-bottom:none !important;margin-bottom:10px;}
    .scrolling{height:300px;overflow:hidden;}
    #last_activity_detail{border-bottom:none !important;}
    ::placeholder{color: #c5c5c5 !important;};
    .dropdown-toggle::after{content:none !important;}
    .dropdown-toggle::before{content:none !important;}
    .infos .table:not(.table-sm) thead th { color: #6777EF !important;font-weight:bold;background:rgb(248, 250, 251) !important; }
    .infos .table-hover tbody tr:hover { background:rgb(248, 250, 251) !important; }
    .user_input_flows .table:not(.table-sm) thead th { color: #6777EF !important;font-weight:bold;background:rgb(248, 250, 251) !important; }
    .user_input_flows .table-hover tbody tr:hover { background:rgb(248, 250, 251) !important; }
    #searching{max-width: 30%;}
    @media (max-width: 575.98px) {
        #searching{max-width: 50%;}
    }
</style>
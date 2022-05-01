<?php 
$name= isset($info[0]["name"]) ? $info[0]["name"] : ""; 
$email= isset($info[0]["email"]) ? $info[0]["email"] : "";
$username = isset($info[0]["username"]) ? $info[0]["username"] : "";
$mobile = isset($info[0]["mobile"]) ? $info[0]["mobile"]: "";
$address= isset($info[0]["address"]) ? $info[0]["address"]: ""; 
$logo= isset($info[0]["profile_img"]) ? $info[0]["profile_img"] : "";
if($logo=="") {
    $logo = file_exists("assets/img/avatar/avatar-1.png") ? base_url("assets/img/avatar/avatar-1.png") : "https://mysitespy.net/envato_image/avatar.png";
}
else 
    $logo = base_url().'upload/affiliator/'.$logo;
?>
<style>
    #my_name { font-size: 20px; }
    #email { font-size:14px;font-family:cursive; }
    /*.profile-widget-header img {width:120px !important;}*/
</style>

<section class="section">
    <div class="section-header">
        <h1 class="page_title"><i class="far fa-user"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>
    <?php $this->load->view('admin/theme/message'); ?>
    <div class="section-body">

        <div class="row">
            <div class="col-12 col-md-4">
                <div class="card profile-widget mt-0 pointer">
                    <div class="profile-widget-header text-center mb-0 bg-primary p-4">
                        <img alt="image" src="<?php echo $logo; ?>" class="rounded-circle profile-widget-picture img-thumbnail float-none m-0" style="width:120px !important;">
                    </div>
                    <div class="profile-widget-description text-center" style="border: solid 3px var(--blue);line-height:25px;">
                        <div class="mb-0 font-weight-bold text-primary" id="my_name"><?php echo $name; ?></div>
                        <div class="font-weight-normal mb-0" id="my_username"><small>@<?php echo $username; ?></small></div>
                        <div id="email"><span><?php echo $email; ?></span></div>
                        <div id="phone"><i class="fas fa-mobile-alt"></i> <small> <?php echo !empty($mobile) ? $mobile:$this->lang->line("Not Available"); ?> </small></div>
                        <div id="address"><sup><i class="fas fa-map-marker-alt"></i></sup> <small><?php echo !empty($address) ? $address:$this->lang->line("Not Available"); ?></small></div>
                    </div>

                </div>
            </div>

            <div class="col-12 col-md-8">
                <div class="card" readonly="">
                    <form class="form-horizontal" enctype="multipart/form-data" action="<?php echo site_url().'affiliate_system/edit_profile_action';?>" method="POST" id="affiliate_profile_form">
                        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
                        <div class="card-header">
                            <h4><i class="far fa-edit"></i> <?php echo $this->lang->line('Edit Profile'); ?> </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6 col-12">
                                    <label class="control-label" for=""><i class="fas fa-monument"></i> <?php echo $this->lang->line("Name");?> *</label>
                                    <div>
                                        <input name="name" value="<?php echo $name;?>"  class="form-control" type="text">                      
                                        <span class="red"><?php echo form_error('name'); ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 col-12">
                                    <label class="control-label" for=""><i class="fas fa-user-tag"></i> <?php echo $this->lang->line("Username");?> *</label>
                                    <div>
                                        <input readonly="" name="username" value="<?php echo $username;?>"  class="form-control" type="text">                  
                                        <span class="red"><?php echo form_error('username'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6 col-12">
                                    <label class="control-label" for=""><i class="far fa-envelope-open"></i> <?php echo $this->lang->line("Email");?> *</label>
                                    <div>
                                        <input name="email" value="<?php echo $email;?>"  class="form-control" type="email">                  
                                        <span class="red"><?php echo form_error('email'); ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 col-12">
                                    <label class="control-label" for=""><i class="fas fa-phone"></i> <?php echo $this->lang->line("Phone");?></label>
                                    <div>
                                        <input name="mobile" value="<?php echo $mobile;?>"  class="form-control" type="text">                  
                                        <span class="red"><?php echo form_error('mobile'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label class="control-label" for=""><i class="fas fa-map-marked-alt"></i> <?php echo $this->lang->line("Address");?></label>
                                    <div>
                                        <textarea name="address" class="form-control"><?php echo $address;?></textarea>           
                                        <span class="red"><?php echo form_error('address'); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-12">
                                    <label for=""><i class="fas fa-image"></i> <?php echo $this->lang->line("image");?> (png)</label>
                                    <div class="custom-file">
                                        <input name="logo" id="logo" class="custom-file-input" type="file">
                                        <label class="custom-file-label">Choose File</label>
                                        <small>
                                            <?php echo $this->lang->line("Max Dimension");?> : 300 x 300, <?php echo $this->lang->line("Max Size");?> : 200KB</small>             
                                            <span class="red"> <?php echo $this->session->userdata('logo_error'); $this->session->unset_userdata('logo_error'); ?></span>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary btn-lg btn-block" id="save-btn" type="submit"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
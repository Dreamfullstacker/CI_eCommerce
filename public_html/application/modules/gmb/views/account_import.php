<style>
    .card { padding-top: 0 !important; }
    .card .media-body .media-title { margin-bottom: 0px !important; }
    .card .media-body .page_email { line-height: 12px !important; }
    .card .page_delete { margin-top:10px;margin-right:10px; padding: .1rem .5rem !important; }
    .card .right-button { margin-top:10px;margin-right:10px; padding: .1rem .5rem !important; }
    .card .enable_webhook { margin-top:10px; padding: .1rem .5rem !important; }
    .card .disable_webhook { margin-top:10px; padding: .1rem .5rem !important; }
    .profile-widget-header .delete_account { position: absolute;top:10px;right:10px;}
    .profile-widget .profile-widget-items:after{position: relative;}
    .list-unstyled .media{padding-right:10px;}
    .list-unstyled-border li{border-bottom: none;}

    /* .profile-widget-item{border:none;} */
    .btn-circle{margin:0 !important;}

    @media (max-width: 575.98px)
    {
        .profile-widget { margin-top: 0 !important; }
    }
    
</style>
<style type="text/css">
  #customBtn {
    display: inline-block;
    background: white;
    color: #444;
    width: 100%;
    border-radius: 5px;
    border: thin solid #888;
    box-shadow: 1px 1px 1px grey;
    white-space: nowrap;
    text-align: left;
  }
  #customBtn:hover {
    cursor: pointer;
  }
  span.label {
    font-family: serif;
    font-weight: normal;
  }
  span.icon {
    background: url('<?php echo base_url("assets/img/google-sm.png"); ?>') transparent 5px 50% no-repeat;
    display: inline-block;
    vertical-align: middle;
    width: 42px;
    height: 42px;
  }
  span.buttonText {
    display: inline-block;
    vertical-align: middle;
    /*padding-left: 42px;*/
    padding-right: 42px;
    font-size: 14px;
    font-weight: bold;
    /* Use the Roboto font that is loaded in the <head> */
    font-family: 'Roboto', sans-serif;
  }
  .btn-social{font-family: 'Roboto', sans-serif;font-size:14px;padding-bottom: 10px;padding-top: 10px;border-radius: 5px;-moz-border-radius: 5px;-webkit-border-radius: 5px;}
  .btn-social > :first-child{line-height: 44px;}
</style>

<style type="text/css">
    #customBtn {
      display: inline-block;
      background: white;
      color: #444;
      width: 100%;
      border-radius: 5px;
      border: thin solid #888;
      box-shadow: 1px 1px 1px grey;
      white-space: nowrap;
      text-align: left;
    }
    #customBtn:hover {
      cursor: pointer;
    }
    span.label {
      font-family: serif;
      font-weight: normal;
    }
    span.icon {
      background: url('<?php echo base_url("assets/img/google-sm.png"); ?>') transparent 5px 50% no-repeat;
      display: inline-block;
      vertical-align: middle;
      width: 42px;
      height: 42px;
    }
    span.buttonText {
      display: inline-block;
      vertical-align: middle;
      /*padding-left: 42px;*/
      padding-right: 42px;
      font-size: 14px;
      font-weight: bold;
      text-decoration: none;
      color:black;
      /* Use the Roboto font that is loaded in the <head> */
      font-family: 'Roboto', sans-serif;
    }
    .btn-social{font-family: 'Roboto', sans-serif;font-size:14px;padding-bottom: 10px;padding-top: 10px;border-radius: 5px;-moz-border-radius: 5px;-webkit-border-radius: 5px;}
    .btn-social > :first-child{line-height: 44px;}
</style>

<?php $is_demo=$this->is_demo; ?>

<section class="section">
    <div class="section-header">
      <h1><i class="fas fa-store-alt"></i> <?php echo $this->lang->line("Business Accounts") ?></h1>
      <div class="section-header-action">
        <?php if($google_login_button) { ?>
                <?php               
                if(($is_demo=='1' && $this->session->userdata("user_type")=="Member") || $is_demo=='0')   {?>       
                    
                    <div class="text-center ml-2">
                        <p data-toggle="tooltip" data-placement="bottom" title="<?php echo $this->lang->line("You must be logged in your Google account for which you want to refresh your access token. For synch your new location, simply refresh your token. If any access token is restricted for any action, refresh your access token.");?>"> <?php echo $google_login_button; ?></p>
                    </div>
                            
                <?php } ?>
        <?php } ?>
      </div>
    </div>

    <?php 
        if($this->session->userdata('success_message') == 'success')
        {
            echo "<div class='text-info text-center' style='font-size : 20px;'><i class='fa fa-check-circle'></i> ".$this->lang->line('Your account has been imported successfully.')."</div><br/>";
            $this->session->unset_userdata('success_message');
        }

        if($this->session->userdata('limit_cross') != '')
        {
            echo "<div class='text-danger text-center' style='font-size : 20px;'><i class='fa fa-remove'></i> ".$this->session->userdata('limit_cross')."</div><br/>";
            $this->session->unset_userdata('limit_cross');
        }

        if($this->session->userdata('gmb_login_msg') != '')
        {
            echo "<div class='text-danger text-center' style='font-size : 20px;'><i class='fa fa-remove'></i> ".$this->session->userdata('gmb_login_msg')."</div><br/>";
            $this->session->unset_userdata('gmb_login_msg');
        }
    ?>
    
    <div class="section-body">
        <div class="">
            <?php if($google_login_button) : ?>
                <div class="row  justify-content-center">
                    <?php 
                    if($is_demo && $this->session->userdata("user_type")=="Admin")  
                    echo '<div class="alert alert-warning text-center">Account import has been disabled in admin account because you will not be able to unlink the Google account you import as admin. If you want to test with your own accout then <a href="'.base_url('home/sign_up').'" target="_BLANK">sign up</a> to create your own demo account then import your Google account there.</div>'; ?>
                </div>

                <?php if($existing_accounts != '0') : ?>        
                    <div>           
                        <div class="row">
                        <?php $i=0; foreach($existing_accounts as $value) : $profile_photo = $value['profile_photo'];?>

                            <div class="col-12 col-md-6">
                                <article class="article article-style-c">
                                  <div class="article-details">
                                    <div class="article-title pl-4">
                                      <h2><a class="text-primary"><?php  echo $value['account_display_name']; ?></a></h2>       
                                    </div>                                  
                                    <div class="article-user p-4">
                                      <img alt="image" src="<?php echo $profile_photo; ?>" style="width: 150px;">
                                      <div class="article-user-details pt-3">
                                        <div class="user-detail-name">
                                          <a class="text-primary"><?php  echo $value['email']; ?></a>
                                        </div>
                                        <div class="text-job"><?php echo $this->lang->line("Locations"); ?> : <?php echo count($value['location_list']); ?></div>

                                        <button class="delete_account btn btn-outline-dark btn-sm mt-4" table_id="<?php echo $value['useraccount_table_id']; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $this->lang->line("Do you want to remove this account from our database? you can import again.");?>"><i class="fas fa-user-times"></i> <?php echo $this->lang->line("Unlink Account");?></button>
                                      </div>
                                    </div>
                                  </div>

                                 <div style="height: 300px;overflow-y:auto;" class="nicescroll p-0 pl-5 pr-5">
                                    <ul class="list-unstyled list-unstyled-border">
                                        <?php 
                                            foreach($value['location_list'] as $location_info) : 
                                                $location_profile_photo = $location_info['profile_google_url'];
                                                if($location_profile_photo == '') $location_profile_photo = base_url('assets/img/product-4-50.png');
                                        ?>
                                            <li class="media">
                                                <div class="page_thumbnail">
                                                    <img alt="image" class="mr-3 rounded" width="50" src="<?php echo $location_profile_photo; ?>">
                                                </div><!--/.page_thumbnail-->
                                                
                                                <div class="media-body"> 
                                                    <div class="media-right">
                                                        <button class="btn-sm btn btn-outline-primary btn-circle location_insight" redirect_url="<?php echo base_url('gmb/location_insights_basic/').$location_info['id']; ?>" title="<?php echo $this->lang->line("Location insight");?>" data-placement="right" data-toggle="tooltip"><i class="fas fa-chart-line"></i> 
                                                        </button>
                                                            <button class="btn-sm btn btn-outline-danger btn-circle location_delete" table_id="<?php echo $location_info['id']; ?>" title="<?php echo $this->lang->line("Delete this location from database.");?>" data-placement="right" data-toggle="tooltip">
                                                                <i class="fas fa-trash-alt"></i> 
                                                        </button> 
                                                    </div>

                                                    <div class="media-title" style="margin-bottom: 0px !important;">
                                                        <a target="_BLANK" href="<?php echo $location_info['map_url'];?>" ><?php echo $location_info['location_display_name']; ?></a>
                                                    </div>

                                                    <div class="text-small text-muted">
                                                        <?php 
                                                            $address_info = json_decode($location_info['address'],true); 
                                                            echo isset($address_info['postalCode']) ? $address_info['postalCode'] : "";
                                                            echo ", ";
                                                            echo isset($address_info['locality']) ? $address_info['locality'] : "";
                                                        ?>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                 </div>
                                </article>
                            </div>

                        <?php
                            $i++;
                            if($i%2 == 0)
                                echo "</div><div class='row'>";
                            endforeach;             
                        ?>
                        </div> 
                    </div>
                <?php else : ?>
                    <div class="card" id="nodata">
                      <div class="card-body">
                        <div class="empty-state">
                          <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image">
                          <h2 class="mt-0"><?php echo $this->lang->line("You haven not connected any account yet.")?></h2>
                          <br/>
                          <h4>
                            <div class="text-center">
                                <p data-toggle="tooltip" data-placement="bottom" title="<?php echo $this->lang->line("you must be logged in your facebook account for which you want to refresh your access token. for synch your new page, simply refresh your token. if any access token is restricted for any action, refresh your access token.");?>"> <?php echo $google_login_button; ?></p>
                            </div>
                          </h4>
                        </div>
                      </div>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <div class="col-12 mb-4">
                    <div class="hero bg-primary text-white">
                      <div class="hero-inner">
                        <h2><?php echo $this->lang->line('Something missing!'); ?></h2>
                        <p class="lead"><?php echo $this->lang->line('No Google APP is configured yet, Admin needs to configure at least one APP.'); ?></p>
                        <?php if($this->session->userdata('user_type') == 'Admin') : ?>
                            <div class="mt-4">
                              <a href="<?php echo base_url('social_apps/google_settings'); ?>" class="btn btn-outline-white btn-lg btn-icon icon-left"><i class="fas fa-hands-helping"></i> <?php echo $this->lang->line('Setup Google APP'); ?></a>
                            </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
            <?php endif; ?>
        </div>
    </div>

</section>


<div class="modal fade" id="delete_confirmation" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-flag"></i> <?php echo $this->lang->line("Deletion Report") ?></h4>
            </div>
            <div class="modal-body" id="delete_confirmation_body">                

            </div>
        </div>
    </div>
</div>

<?php 
    $location_delete_confirmation = $this->lang->line("If you delete this location, all the campaigns corresponding to this location will also be deleted. Do you want to delete this location from database?");
    $account_delete_confirmation = $this->lang->line("If you delete this account, all the locations and all the campaigns corresponding to this account will also be deleted form database. do you want to delete this account from database?");

?>


<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
    
    $("document").ready(function() {
        var base_url = "<?php echo base_url(); ?>";


        $(document).on('click','.location_delete',function(){
            var location_delete_confirmation = "<?php echo $location_delete_confirmation; ?>";
            swal({
                title: '<?php echo $this->lang->line("Are you sure"); ?>',
                text: location_delete_confirmation,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) 
                {
                    var location_table_id = $(this).attr('table_id');

                    $(this).removeClass('btn-outline-danger');
                    $(this).addClass('btn-danger');
                    $(this).addClass('btn-progress');

                    $.ajax({
                        context: this,
                        type:'POST' ,
                        url:"<?php echo site_url();?>social_accounts/location_delete_action",
                        dataType: 'json',
                        data:{location_table_id : location_table_id},
                        success:function(response){ 
                            if(response.status == 1)
                            {
                                $(this).removeClass('btn-progress');
                                $(this).removeClass('btn-danger');
                                $(this).addClass('btn-outline-danger');
                                
                                swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                                      location.reload();
                                    });
                            }
                            else
                            {
                                swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
                            }
                        }
                    });
                } 
            });


        });



        $(document).on('click','.delete_account',function(){
            var account_delete_confirmation = "<?php echo $account_delete_confirmation; ?>";
            swal({
                title: '<?php echo $this->lang->line("Are you sure"); ?>',
                text: account_delete_confirmation,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) 
                {
                    var gmb_user_table_id = $(this).attr('table_id');
                    $(this).removeClass('btn-outline-danger');
                    $(this).addClass('btn-danger');
                    $(this).addClass('btn-progress');

                    $.ajax({
                        context: this,
                        type:'POST' ,
                        url:"<?php echo site_url();?>social_accounts/gmb_account_delete_action",
                        dataType: 'json',
                        data:{gmb_user_table_id : gmb_user_table_id},
                        success:function(response){ 
                            
                            $(this).removeClass('btn-progress');
                            $(this).removeClass('btn-danger');
                            $(this).addClass('btn-outline-danger');

                            if(response.status == 1)
                            {
                                swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                                      location.reload();
                                    });
                            }
                            else
                            {
                                swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
                            }
                        }
                    });
                } 
            });


        });


        $(document).on('click','.location_insight',function(){
            var redirect_url = $(this).attr('redirect_url');
            window.open(redirect_url, "_blank") || window.location.replace(redirect_url);
        });

    });
</script>
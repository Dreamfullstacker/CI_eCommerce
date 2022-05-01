<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fab fa-wordpress"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
        <a class="btn btn-primary" href="<?php echo base_url('woocommerce_integration/add_woocommerce_settings') ?>"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Connect WooCommerce API'); ?></a>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('integration#'); ?>"><?php echo $page_title; ?></a></div>
    </div>
  </div>

  <?php 
  $this->load->view('admin/theme/message'); 
  if($this->session->flashdata('error_message_woocommerce')!='')
  echo "<div class='alert alert-danger text-center'><i class='fa fa-remove'></i> ".$this->session->flashdata('error_message_woocommerce')."</div>";
  ?>

  <div class="section-body">

    <?php 
    if(!empty($info))
    {       
      echo "<div class='row'>";
      foreach($info as $value)
      {  ?>
        <div class="col-12 col-sm-6">
          <div class="card profile-widget mt-4">
              <div class="profile-widget-header">
                <div class="profile-widget-items">
                  <div class="profile-widget-item">
                    <div class="profile-widget-item-value">
                      <a target='_BLANK' href="<?php echo base_url("woocommerce_integration/store/".$value["id"]);?>"  class='btn btn-outline-info ' data-toggle='tooltip' data-placement='top' title="<?php echo $this->lang->line('Visit Store Webview');?>"><i class='fas fa-eye'></i> <?php echo $this->lang->line('Store Webview');?></a>
                    </div>
                  </div>
                  <div class="profile-widget-item">
                    <div class="profile-widget-item-value">
                     <a href='' data-site="<?php echo $value["home_url"];?>" data-id="<?php echo $value['id'];?>"  class='btn btn-outline-primary show_product' data-toggle='tooltip' data-placement='top' title="<?php echo $this->lang->line('Product List');?>"><i class='fas fa-box-open'></i> <?php echo $this->lang->line('Products');?></a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="profile-widget-description" style="padding-bottom: 0;">
                <div class="profile-widget-name text-center ltr"><a href='<?php echo $value["home_url"];?>' target="_BLANK"><i class='fab fa-wordpress'></i> <?php echo $value["home_url"];?></a></div>
                <div class="profile-widget-name text-center">
                  <small  data-toggle='tooltip' data-placement='top' title="<?php echo $this->lang->line('Consumer Key');?>"><i class='fas fa-key'></i> <?php echo (!$this->is_demo) ? $value["consumer_key"]:"xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";?></small><br>
                  <small  data-toggle='tooltip' data-placement='top' title="<?php echo $this->lang->line('Consumer Secret');?>"><i class='fas fa-mask'></i> <?php echo (!$this->is_demo) ? $value["consumer_secret"]:"xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";?></small><br>
                  <small  data-toggle='tooltip' data-placement='top' title="<?php echo $this->lang->line('Last Updated');?>"><i class='far fa-clock'></i> <?php echo date("M j, y H:i",strtotime($value["last_updated_at"]));?></small>
                </div>
              </div>
              <div class="card-footer text-center" style="padding-top: 10px;">
                
               <a href='#' csrf_token="<?php echo $this->session->userdata('csrf_token_session');?>" class='mt-2 btn btn-outline-danger delete_app' table_id="<?php echo $value['id'];?>" data-toggle='tooltip' data-placement='top' title="<?php echo $this->lang->line('Delete');?>"><i class='fas fa-trash-alt'></i> <?php echo $this->lang->line('Delete');?></a>

               <a href="<?php echo base_url('woocommerce_integration/edit_woocommerce_settings/').$value['id'];?>" class='mt-2 btn btn-outline-primary' data-toggle='tooltip' data-placement='top' title="<?php echo $this->lang->line('Update');?>"><i class='fas fa-edit'></i> <?php echo $this->lang->line('Update');?></a>

               <a href="" class='mt-2 btn btn-outline-dark copy_url'  data-id="<?php echo $value['id'];?>" data-toggle='tooltip' data-placement='top' title="<?php echo $this->lang->line('Copy URL');?>"><i class='fas fa-copy'></i> <?php echo $this->lang->line('Copy URL');?></a>

               <a href="<?php echo base_url('woocommerce_integration/sync_woocommerce_data/').$value['id'];?>" class='mt-2 btn btn-warning' data-toggle='tooltip' data-placement='top' title="<?php echo $this->lang->line('Re-sync Data');?>"><i class='fas fa-sync-alt'></i> <?php echo $this->lang->line('Re-sync Data');?></a>

              </div>
            </div>
          
        </div>            
        <?php 
      }
      echo "</div>";
    }
    else
    { ?>
      <div class="card">
          <div class="card-body">
            <div class="empty-state" data-height="400" style="height: 400px;">
              <div class="empty-state-icon">
                <i class="fas fa-times"></i>
              </div>
              <h2><?php echo $this->lang->line("No WooCommerce Integration found."); ?></h2>
              <p>&nbsp;</p>
              <a class="btn btn-primary" href="<?php echo base_url('woocommerce_integration/add_woocommerce_settings') ?>"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Connect WooCommerce API');?></a>
            </div>
          </div>
        </div>

      <?php
    }
    ?>  
    
  </div>
</section>





<script>       
  var base_url="<?php echo site_url(); ?>";  
 
  $(document).ready(function() {

    "use strict";

    $(document).on('click','.show_product',function(e){
      e.preventDefault();
      var id = $(this).attr('data-id');
      $("#show_products_modal").modal();
      $("#show_products_modal iframe").attr('src',base_url+'woocommerce_integration/product_list/'+id);
    });

    $(document).on('click','.copy_url',function(e){
      e.preventDefault();
      var id = $(this).attr('data-id');
      $("#copy_url_modal").modal();
      $("#copy_url_modal iframe").attr('src',base_url+'woocommerce_integration/copy_url/'+id);
    });


    $(document).on('click','.delete_app',function(e){
      e.preventDefault();
      var ifyoudeletethisaccount = "<?php echo $this->lang->line('Are you sure that you want to delete this API? Deleting API does not affect products exported to E-commerce.'); ?>";
      swal({
        title: '<?php echo $this->lang->line("Are you sure?"); ?>',
        text: ifyoudeletethisaccount,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) 
        {
          var app_table_id = $(this).attr('table_id');
          var csrf_token = $(this).attr('csrf_token');
          $(this).removeClass('btn-outline-danger');
          $(this).addClass('btn-danger');
          $(this).addClass('btn-progress');

          $.ajax({
            context: this,
            type:'POST' ,
            url:"<?php echo site_url();?>woocommerce_integration/delete_action",
            dataType: 'json',
            data:{app_table_id : app_table_id,csrf_token:csrf_token},
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


  });
</script>


<div class="modal fade" role="dialog" id="show_products_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-mega" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-box-open"></i> <?php echo $this->lang->line("Products");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
      </div>
      <div class="modal-body">
          <iframe src="" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>

      </div>
    </div>
  </div>
</div>


<div class="modal fade" role="dialog" id="copy_url_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-mega" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-copy"></i> <?php echo $this->lang->line("Copy URL");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
      </div>
      <div class="modal-body">
          <iframe src="" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>

      </div>
    </div>
  </div>
</div>


<style type="text/css">.profile-widget .profile-widget-items:after{left:0;}</style>
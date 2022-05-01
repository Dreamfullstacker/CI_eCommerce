<style>
  hr{
     margin-top: 10px;
  }

  .custom-top-margin{
    margin-top: 20px;
  }

  .sync_page_style{
     margin-top: 8px;
  }
  /* .wrapper,.content-wrapper{background: #fafafa !important;} */
  .well{background: #fff;}
  .box-shadow
  {
    -webkit-box-shadow: 0px 2px 14px -3px rgba(0,0,0,0.75);
      -moz-box-shadow: 0px 2px 14px -3px rgba(0,0,0,0.75);
      box-shadow: 0px 2px 14px -3px rgba(0,0,0,0.75);
      border-bottom: 4px solid <?php echo $THEMECOLORCODE; ?>;
  }

  .info-box-icon {
      border-top-left-radius: 2px;
      border-top-right-radius: 0;
      border-bottom-right-radius: 0;
      border-bottom-left-radius: 2px;
      display: block;
      float: left;
      height: 90px;
      width: 90px;
      text-align: center;
      font-size: 45px;
      line-height: 90px;
      background: rgba(0,0,0,0.2);
  }

  .info-box {
      display: block;
      min-height: 90px;
      background: #fff;
      width: 100%;
      box-shadow: 0 1px 1px rgba(0,0,0,0.1);
      border-radius: 2px;
      margin-bottom: 10px;
  }
  /*.info-box-content
  {
    margin-left: 50px;
  }*/
</style>


<?php if(empty($page_info)){ ?>

  <div class="well well_border_left">
      <h4 class="text-center blue"> <i class="fa fa-check-square"></i> <?php echo $this->lang->line("Enable Post : Page List");?></h4>
  </div>          


<?php }else{ ?>

  <div class="well well_border_left">
      <h4 class="text-center blue"> <i class="fa fa-check-square"></i> <?php echo $this->lang->line("Enable Post : Page List");?></h4><h4>
    </h4>
  </div>       

<div class="row" style="padding:0 15px;">
  <?php $i=0; foreach($page_info as $value) : ?>

  <div class="col-xs-12 col-sm-12 col-md-6">
    <div class="box box-shadow box-solid">
      <div class="box-header with-border text-center">
        <h3 class="box-title"> <a target="_BLANK" href="https://facebook.com/<?php echo $value['page_id']; ?>"><?php echo $value['page_name']; ?></a></h3>
        <div class="box-tools pull-right">
          <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body" style="padding:18px;">
        <div class="col-xs-12">
          <div class="row">
            <?php $profile_picture=$value['page_profile']; ?>
            <div class="col-xs-12 col-md-4">
              <img src="<?php echo $profile_picture;?>" alt="" class='custom-top-margin' style='padding:1px;border:1px solid #aaa;' height="90" width="90">

              <a style="margin-top:13px;" href="<?php echo base_url("commenttagmachine/post_list/".$value['id']); ?>" type="button" class="btn btn-outline-secondary btn-sm view_report"><i class="fa fa-eye"></i> <small><?php echo $this->lang->line("View Report") ?></small></a>
            </div>
            <div class="col-xs-12 col-md-8">
              <br/>
              <div class="info-box">
                <span class="info-box-icon bg-blue" style="background: <?php echo $THEMECOLORCODE;?> !important;"><i class="fa fa-tag"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><b><?php echo $this->lang->line("Tag Enabled");?></b></span><hr style="margin-bottom:2px;">
                  <span class="info-box-number" style="font-size:30px">
                    <?php 
                      echo number_format($value['enabled_post']);
                    ?>
                  </span>
                </div>
              </div>

              <div class="sync_page_style text-center" style="padding-top: 3px;">
                <span class="info-box-text">
                  <button table_id="<?php echo $value['id']; ?>" type="button" style='width:50%;' class="pull-left btn-sm btn btn-outline-primary get_post"><i class="fa fa-check-square"></i><?php echo $this->lang->line("Enable from Post List");?></button>

                  <button page_name="<?php echo $value['page_name']; ?>" page_table_id="<?php echo $value['id']; ?>" type="button" style='width:45%;' class="pull-right btn-sm btn btn-outline-info manual_enable"><i class="fa fa-check-square"></i><?php echo $this->lang->line("enable by post id");?></button>
                  
                </span>
              </div>               
            </div>                  
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php   
  $i++;
  if($i%2 == 0)
    echo "</div><div class='row' style='padding:0 15px;'>";
  endforeach;
  ?>
</div>
<?php } ?>

<?php 
  
  $Youdidntprovideallinformation = $this->lang->line("you didn't provide all information.");
  $Pleaseprovidepostid = $this->lang->line("please provide post id.");
  $Youdidntselectanyoption = $this->lang->line("you didn\'t select any option.");
  
  $AlreadyEnabled = $this->lang->line("already enabled");
  $ThispostIDisnotfoundindatabaseorthispostIDisnotassociatedwiththepageyouareworking = $this->lang->line("This post ID is not found in database or this post ID is not associated with the page you are working.");



 ?>

<script>
  $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();     
  });
  $(document).ready(function(){

    var base_url = "<?php echo base_url(); ?>";


    // enable and edit auto reply by post id
    $(".manual_enable").click(function(){
      var page_name = $(this).attr('page_name');
      var page_table_id = $(this).attr('page_table_id');
      $("#manual_enable_error").html('');
      $("#manual_page_name").html(page_name);
      $("#manual_post_id").val('');
      $("#enable_auto_tag").attr('page_table_id',page_table_id);
      $("#manual_enable_modal").modal();
    });

    $("#enable_auto_tag").click(function(){
      $("#manual_enable_error").html('');    
      var post_id = $("#manual_post_id").val();
      var page_id = $(this).attr('page_table_id');
      $(this).addClass('disabled');
      $.ajax({
        type:'POST' ,
        url:"<?php echo site_url();?>commenttagmachine/manual_sync_commenter_info",
        data:{page_id:page_id,post_id:post_id},
        dataType:'JSON',
        success:function(response){
          if(response.status != '1')
          $("#manual_enable_error").html("<div class='alert alert-danger text-center'><i class='fa fa-close'></i> "+response.message+"</div>");
          else
          {
            $("#manual_enable_error").html("<div class='alert alert-success text-center'><i class='fa fa-check'></i> "+response.message+"</div>");
            $("#manual_post_id").val();
          }
          $("#enable_auto_tag").removeClass('disabled');
        }
      });
    });


    $(".get_post").click(function(){
      var table_id = $(this).attr('table_id');
      var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
      $("#post_synch_modal_body").html(loading);
        $("#post_synch_modal").modal();
      $.ajax({
        type:'POST' ,
        url:"<?php echo site_url();?>commenttagmachine/import_latest_post",
        data:{table_id:table_id},
        dataType:'JSON',
        success:function(response){
            $("#page_name_div").html(": "+response.page_name);
            $("#post_synch_modal_body").html(response.message);
        }
      });

    });


    $(document).on('click','.sync_commenter_info',function(){
      var page_id = $(this).attr('page_table_id');
      var post_id = $(this).attr('post_id');
      var post_description = $(this).attr('post-description');
      var post_created_at = $(this).attr('post-created-at');
      var Pleaseprovidepostid = "<?php echo $Pleaseprovidepostid; ?>";

      if(typeof(post_id) === 'undefined' || post_id == '')
      {
        alert(Pleaseprovidepostid);
        return false;
      }
      var button_id=page_id+"-"+post_id;
      $("#"+button_id).addClass('disabled');

      var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
      $("#sync_commenter_info_response").attr('class','').html(loading);

      $.ajax({
        type:'POST' ,
        url:"<?php echo site_url();?>commenttagmachine/sync_commenter_info",
        data:{page_id:page_id,post_id:post_id,post_description:post_description,post_created_at:post_created_at},
        dataType:'JSON',
        success:function(response)
        {
            if(response.status=='1')
            {
              $("#sync_commenter_info_response").attr('class','alert alert-success text-center').html(response.message);
              $("#"+button_id).parent().html(response.button_replace);
            }
            else
            {
              $("#sync_commenter_info_response").attr('class','alert alert-danger text-center').html(response.message);
              $("#"+button_id).removeClass('disabled');
            }
        }
      });

    });
        

    $('#post_synch_modal').on('hidden.bs.modal', function () { 
      location.reload();
    });

    $('#manual_enable_modal').on('hidden.bs.modal', function () { 
      location.reload();
    });
    
  });
</script>


<div class="modal fade" id="post_synch_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-calendar"></i> <?php echo $this->lang->line("Latest Posts") ?> <span id="page_name_div"></span></h4>
            </div>
            <div class="modal-body text-center" id="post_synch_modal_body">                

            </div>
        </div>
    </div>
</div>

<div class="modal fade"  id="manual_enable_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
         <h4 class="modal-title"><?php echo $this->lang->line("please provide a post id of page") ?> (<span id="manual_page_name"></span>)</h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-xs-12" id="waiting_div"></div>
            <div class="col-xs-12 col-md-8 col-md-offset-2">
              <form>
                <div class="form-group">
                  <label for="manual_post_id"><?php echo $this->lang->line("post id") ?> :</label>
                  <input type="text" class="form-control" id="manual_post_id" placeholder="<?php echo $this->lang->line("please give a post id") ?>" value="">
                </div><br/>
                <div class="text-center" id="manual_enable_error"></div>
                <div class="form-group text-center">
                  <button type="button" class="btn btn-outline-primary" id="enable_auto_tag"><i class="fa fa-check-square"></i> <?php echo $this->lang->line("Enable & Fetch Commenter") ?></button>
                </div>
              </form>                        
            </div>                    
          </div> 
      </div>
<!--       <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
      </div> -->
    </div>
  </div>
</div>
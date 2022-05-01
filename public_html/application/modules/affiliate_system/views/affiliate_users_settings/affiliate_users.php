<style>
  /*.avatar-badge { background: 0 !important; }*/
  .avatar-item .avatar-badge {
      position: absolute;
      bottom: -3px;
      right: 0px;
      background-color: #fff;
      color: #000;
      box-shadow: 0 4px 8px rgb(0 0 0 / 3%);
      border-radius: 50%;
      text-align: center;
      line-height: 20px;
      width: 18px;
      height: 18px;
  }
</style>
<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-users"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
     <a class="btn btn-primary"  href="<?php echo site_url('affiliate_system/add_affiliate');?>">
        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Affiliate"); ?>
     </a> 
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("Affiliate System"); ?></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">

    <div class="row">
      <div class="col-12">
        <div class="card">

          <div class="card-body data-card">            
            <div class="table-responsive2">
              <table class="table table-bordered" id="mytable_affiliate_users">
                <thead>
                  <tr>
                    <th>#</th>      
                    <th><?php echo $this->lang->line("ID"); ?></th>      
                    <th><?php echo $this->lang->line("Avatar"); ?></th>      
                    <th class="text-left"><?php echo $this->lang->line("Name"); ?></th>      
                    <th><?php echo $this->lang->line("Email"); ?></th>
                    <th><?php echo $this->lang->line("Status"); ?></th>
                    <th><?php echo $this->lang->line("Registered"); ?></th>
                    <th><?php echo $this->lang->line("Last Login"); ?></th>
                    <th><?php echo $this->lang->line("Last Login IP"); ?></th>
                    <th><?php echo $this->lang->line("Actions"); ?></th>
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

<script>       
    var base_url="<?php echo site_url(); ?>";
   
    $(document).ready(function() {

      $('div.note-group-select-from-files').remove();
      
      var affiliate_users_perscroll;
      var affiliate_users_table = $("#mytable_affiliate_users").DataTable({
          serverSide: true,
          processing:true,
          bFilter: true,
          order: [[ 1, "desc" ]],
          pageLength: 10,
          ajax: {
              "url": base_url+'affiliate_system/affiliate_users_data',
              "type": 'POST'
          },
          language: 
          {
            url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
          },
          dom: '<"top"f>rt<"bottom"lip><"clear">',
          columnDefs: [
            {
                targets: [1],
                visible: false
            },
            {
                targets: '',
                className: 'text-center'
            },
            {
                targets: [0,1,2,3,4,8,9],
                sortable: false
            }
          ],
          fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
              if(areWeUsingScroll)
              {
                if (affiliate_users_perscroll) affiliate_users_perscroll.destroy();
                affiliate_users_perscroll = new PerfectScrollbar('#mytable_affiliate_users_wrapper .dataTables_scrollBody');
              }
          },
          scrollX: 'auto',
          fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
              if(areWeUsingScroll)
              {
                if (affiliate_users_perscroll) affiliate_users_perscroll.destroy();
                affiliate_users_perscroll = new PerfectScrollbar('#mytable_affiliate_users_wrapper .dataTables_scrollBody');
              }
          }
      });

      $(document).on('click','.delete_affiliate',function(e){
        e.preventDefault();
        var link = $(this).attr("href");
        var refresh = $(this).attr("data-refresh");
        var csrf_token = $(this).attr('csrf_token');
        
        if (typeof(csrf_token)==='undefined') csrf_token = '';

        var mes='<?php echo $this->lang->line("Do you really want to delete it?");?>';  
        swal({
          title: "<?php echo $this->lang->line("Are you sure?");?>",
          text: mes,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => 
        {
          if (willDelete) 
          {
              $(this).addClass('btn-progress btn-danger').removeClass('btn-outline-danger');
              $.ajax({
                context: this,
                url: link,
                type: 'POST',
                dataType: 'JSON',
                data: {csrf_token:csrf_token},
                  success:function(response)
                  {
                    $(this).removeClass('btn-progress btn-danger').addClass('btn-outline-danger');
                    if(response.status == 1)  
                    {
                      iziToast.success({title: '',message: response.message,position: 'bottomRight'});
                      if(refresh!='0')
                      {
                        if($(this).hasClass('non_ajax')) $(this).parent().parent().hide();
                        else $('#mytable_affiliate_users').DataTable().ajax.reload();
                      }
                    }
                    else iziToast.error({title: '',message: response.message,position: 'bottomRight'});
                  }
              });
          } 
        });
      });

      $(document).on('click', '.change_password', function(e) {
        e.preventDefault();

        var user_id = $(this).attr('data-id');
        var user_name = $(this).attr('data-user');

        $("#putname").html(user_name);
        $("#putid").val(user_id);

        $("#change_password").modal();
      });

      var confirm_match=0;
      $(".password").keyup(function(){
        
          var new_pass=$("#password").val();
          var conf_pass=$("#confirm_password").val();

          if(new_pass=='' || conf_pass=='') 
          {
            return false;
          }

          if(new_pass==conf_pass)
          {
              confirm_match=1;
              $("#password").removeClass('is-invalid');
              $("#confirm_password").removeClass('is-invalid');
          }
          else
          {
              confirm_match=0;
              $("#confirm_password").addClass('is-invalid');
          }

      });

      $(document).on('click', '#save_change_password_button', function(e) {
        e.preventDefault();

        var user_id =  $("#putid").val();
        var password =  $("#password").val();
        var confirm_password =  $("#confirm_password").val();
        var csrf_token = $("#csrf_token").val();

        password = password.trim();
        confirm_password = confirm_password.trim();

        if(password=='' || confirm_password=='')
        {
            $("#password").addClass('is-invalid');
            return false;
        }
        else
        {
            $("#password").removeClass('is-invalid');
        }

        if(confirm_match=='1')
        {
            $("#confirm_password").removeClass('is-invalid');
        }
        else
        {
            $("#confirm_password").addClass('is-invalid');
            return false;
        }

        $("#save_change_password_button").addClass("btn-progress");

        $.ajax({
        url: base_url+'affiliate_system/change_affiliate_password_action',
        type: 'POST',
        dataType: 'JSON',
        data: {user_id:user_id,password:password,confirm_password:confirm_password,csrf_token:csrf_token},
          success:function(response)
          {
            $("#save_change_password_button").removeClass("btn-progress");

            if(response.status == "1")  
              swal('<?php echo $this->lang->line("Success")?>',response.message, 'success')
             .then((value) => {
                 $("#change_password").modal('hide');
              });

            else  swal('<?php echo $this->lang->line("Error")?>',response.message, 'error');
          },
          error:function(response){
            var span = document.createElement("span");
            span.innerHTML = response.responseText;
            swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span,icon:'error'});
          }
      });

      });

  });

   
 
</script>



<div class="modal fade" tabindex="-1" role="dialog" id="change_password" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-key"></i> <?php echo $this->lang->line("Change Affiliator Password");?> (<span id="putname"></span>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">  
              <form class="form-horizontal" action="<?php echo site_url().'affiliate_system/change_affiliate_password_action';?>" method="POST">
                <div id="wait"></div>
                <input id="putid" value="" class="form-control" type="hidden">           
                <div class="form-group">
                  <label for="password"><?php echo $this->lang->line("New Password"); ?> *  </label>                  
                  <input id="password" class="form-control password" type="password">             
                  <div class="invalid-feedback"><?php echo $this->lang->line("You have to type new password twice"); ?></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password"><?php echo $this->lang->line("Confirm New Password"); ?> * </label>                  
                    <input id="confirm_password"  class="form-control password" type="password">             
                   <div class="invalid-feedback"><?php echo $this->lang->line("Passwords does not match"); ?></div>
                </div>
              </form>            
            </div>


            <div class="modal-footer bg-whitesmoke br">
              <button type="button" id="save_change_password_button" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save"); ?></button>
              <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close"); ?></button>
            </div>

        </div>
    </div>
</div>

<style>
    #sequence_search{max-width: 40% !important;}
    .bbw{border-bottom-width: thin !important;border-bottom:solid .5px #f9f9f9 !important;padding-bottom:20px;}
    .button-outline
    {
      background: #fff;
      border: .5px dashed #ccc;
    }
    .button-outline:hover
    {
      border: 1px dashed var(--blue) !important;
      cursor: pointer;
    }
</style>

<input type="hidden" name="sms_email_sequence_csrf_token" id="sms_email_sequence_csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">

<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-paper-plane"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-button">
            <a class="btn btn-primary" href="<?php echo base_url("sms_email_sequence/create_sequnce_for_external"); ?>">
                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Add Sequence"); ?>
            </a> 
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
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="input-group float-left" id="searchbox">
                                    <input type="text" class="form-control" id="sequence_search" name="sequence_search" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>" aria-label="" aria-describedby="basic-addon2">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive2 data-card">
                                    <table class="table table-bordered" id="mytable_external_sequence">
                                        <thead>
                                            <tr>
                                                <th>#</th>      
                                                <th><?php echo $this->lang->line("ID"); ?></th>      
                                                <th><?php echo $this->lang->line("Name"); ?></th>
                                                <th><?php echo $this->lang->line("Last Sent"); ?></th>
                                                <th><?php echo $this->lang->line('Campaign Type'); ?></th>
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


<script>
    
    $(document).ready(function($) {
        var base_url = '<?php echo base_url(); ?>';
        var perscroll_external_contacts_sequence_table;
        var external_contacts_sequence_table = $("#mytable_external_sequence").DataTable({
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 1, "desc" ]],
            pageLength: 10,
            ajax: 
            {
                "url": base_url+'sms_email_sequence/external_sequence_lists_data',
                "type": 'POST',
                data: function ( d )
                {
                    d.sequence_search = $('#sequence_search').val();
                }
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
                  targets: '',
                  sortable: false
                }
            ],
            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
              if(areWeUsingScroll)
              {
                if (perscroll_external_contacts_sequence_table) perscroll_external_contacts_sequence_table.destroy();
                perscroll_external_contacts_sequence_table = new PerfectScrollbar('#mytable_external_sequence_wrapper .dataTables_scrollBody');
              }
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
              if(areWeUsingScroll)
              { 
                if (perscroll_external_contacts_sequence_table) perscroll_external_contacts_sequence_table.destroy();
                perscroll_external_contacts_sequence_table = new PerfectScrollbar('#mytable_external_sequence_wrapper .dataTables_scrollBody');
              }
            }
        });

        $(document).on('keyup', '#sequence_search', function(event) {
          event.preventDefault(); 
          external_contacts_sequence_table.draw();
        });



        $(document).on('click','.delete_campaign',function(e){
          e.preventDefault();
          var id = $(this).attr('id');  
          var cam_type = $(this).attr("campaign_type");    
          var somethingwentwrong = "<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>";

          swal({
            title: '<?php echo $this->lang->line("Delete Campaign"); ?>',
            text: '<?php echo $this->lang->line("Do you really want to delete this campaign?"); ?>',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) 
            {
                $(this).parent().prev().addClass('btn-progress');
                $.ajax({
                  context: this,
                  type:'POST' ,
                  url: "<?php echo base_url('sms_email_sequence/delete_sequecne_campaign')?>",              
                  data: {id:id,cam_type:cam_type},
                  success:function(response){ 
                     $(this).parent().prev().removeClass('btn-progress');
                     if(response == '1')
                     {
                        iziToast.success({title: '',message: "<?php echo $this->lang->line('Camapign has been deleted successfully.')?>",position: 'bottomRight'});
                        external_contacts_sequence_table.draw();
                     }
                     else
                     {
                       swal('<?php echo $this->lang->line("Error"); ?>', somethingwentwrong, 'error');
                     }


                  }
                });
            } 
          });
        });

        $(document).on('click','.message_content',function(e){
          e.preventDefault();
          var campaign_id = $(this).attr('data-id'); // campaign id
          var is_day = $(this).attr('data-day');
          $('#sms_email_message_content_modal_content').html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>');  
          $("#sms_email_message_content_modal").modal(); 
          $.ajax({
            type:'POST' ,
            url:"<?php echo site_url();?>sms_email_sequence/get_campaign_report",
            data:{campaign_id:campaign_id,is_day:is_day},
            success:function(response){
               $('#sms_email_message_content_modal_content').html(response);  
            }
          });
        });

    });
</script>


<div class="modal fade" id="sms_email_message_content_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header smallpadding">
        <h5 class="modal-title"><i class="fa fa-eye"></i> <?php echo $this->lang->line('Campaign Report'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body smallpadding" id="sms_email_message_content_modal_content"></div>
    </div>
  </div>
</div>

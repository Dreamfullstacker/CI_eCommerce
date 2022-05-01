<?php $this->load->view('admin/theme/message'); ?>
<style>
    #page_id{width: 150px;}
    #searching{max-width: 40%;}
    .swal-text{text-align: left !important;}
    @media (max-width: 575.98px) {
      #page_id{width: 90px;}
      #searching{max-width: 50%;}
      #add_custom_field { max-width: 100% !important; }
    }
</style>

<input type="hidden" name="page_id" id="page_id" value="<?php echo $page_id; ?>">
<input type="hidden" name="media_type" id="media_type" value="<?php echo $media_type; ?>">
<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
<div class="table-responsive2 data-card">
	<table class="table table-bordered table-sm table-striped" id="mytable">
		<thead>
			<tr>
				<th>#</th>      
				<th><?php echo $this->lang->line("ID"); ?></th>      
				<th><?php echo $this->lang->line("Flow Name"); ?></th>      
				<th><?php echo $this->lang->line("Page Name"); ?></th>
                <th><?php echo $this->lang->line("Editor Type"); ?></th>
				<th><?php echo $this->lang->line("Action"); ?></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div> 



<div class="modal fade" id="detail-flow-input" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="min-width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-users"></i> <?php echo $this->lang->line("Flow Subscribers"); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body p-0" id="subscriber_actions_modal_body" data-backdrop="static" data-keyboard="false">
                <div class="card no_shadow">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-body pb-0">
                                <input type="text" id="searching2" name="searching2" class="form-control" placeholder="<?php echo $this->lang->line("Search..."); ?>" style='width:200px;'>                                          
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card-body data-card">                           
                                <div class="table-responsive2">
                                    <input type="hidden" id="put_table_id">
                                    <input type="hidden" id="media_type" value="<?php echo $media_type; ?>">
                                    <table class="table table-bordered table-sm table-striped" id="mytable1">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><?php echo $this->lang->line("Avatar"); ?></th> 
                                                <th><?php echo $this->lang->line("First Name"); ?></th>  
                                                <th><?php echo $this->lang->line("Last Name"); ?></th>  
                                                <th><?php echo $this->lang->line("Subscriber ID"); ?></th>  
                                                <th><?php echo $this->lang->line("Submitted At"); ?></th>  
                                                <th><?php echo $this->lang->line("Actions"); ?></th>  
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- <div id="detail-first-view">
                            <div class="first-view-spinner">
                                <i class="fa fa-spinner fa-spin fa-2x blue"></i>
                            </div>  
                        </div> -->
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('Close'); ?></button>
            </div>

        </div>
    </div>
</div>

<?php 
$areyousure=$this->lang->line("are you sure");
$builder_load_url = base_url("visual_flow_builder/load_builder/".$page_id.'/1/'.$media_type);
// $drop_menu = '<a class="btn btn-primary add_custom_field d-none" href="'.base_url('custom_field_manager/input_flow_builder/').$media_type">';
$drop_menu = '<a target="_BLANK" href="'.$builder_load_url.'" class="float-right btn btn-primary d-none"><i class="fas fa-plus-circle"></i> '.$this->lang->line("New Flow").'</a>';
?> 

<script>       
    var base_url="<?php echo site_url(); ?>";
   
    $(document).ready(function() {

        var drop_menu = '<?php echo $drop_menu;?>';
        setTimeout(function(){ 
          $("#mytable_filter").append(drop_menu);
        }, 1000);

    	var perscroll;
        var table = $("#mytable").DataTable({
    		serverSide: true,
    		processing:true,
    		bFilter: true,
    		order: [[ 2, "asc" ]],
    		pageLength: 10,
    		ajax: {
    			"url": base_url+'custom_field_manager/campaign_list_data',
    			"type": 'POST',
                data: function ( d )
                {
                    d.media_type = $('#media_type').val();
                }
    		},
    		language: 
    		{
    			url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
    		},
    		dom: '<"top"f>rt<"bottom"lip><"clear">',
    		columnDefs: [
    		{
    			targets: [1,3],
    			visible: false
    		},
    		{
    			targets: [3,4,5],
    			className: 'text-center'
    		},
    		{
    			targets: [0,5],
    			sortable: false
    		}
    		],
            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                   if(areWeUsingScroll)
                   {
                     if (perscroll) perscroll.destroy();
                     perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
                   }
               },
               scrollX: 'auto',
               fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                   if(areWeUsingScroll)
                   { 
                     if (perscroll) perscroll.destroy();
                     perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
                   }
               }
    	});



        $(document).on('click', '#search_submit', function(event) {
          event.preventDefault(); 
          table.draw();
        });
        // end of datatable


        // Displays flow details
        var table1 = '';
        var perscroll1;
        $(document).on('click', '.view_report', function(e) {
            e.preventDefault();

            // Grabs form ID
            var table_id = $(this).attr('table_id');
            $("#put_table_id").val(table_id);

            // var spinner = $('#detail-first-view');
            // $(spinner).show();

            $('#detail-flow-input').modal();

            setTimeout(function(){ 
                if (table1 == '')
                {
                  // $("#put_form_id").val(form_id);
                  var base_url = "<?php echo base_url(); ?>";
                  table1 = $("#mytable1").DataTable({
                      serverSide: true,
                      processing:true,
                      bFilter: false,
                      order: [[ 5, "desc" ]],
                      pageLength: 10,
                      ajax: {
                          url: base_url+'custom_field_manager/get_submitted_subscribers',
                          type: 'POST',
                          data: function ( d )
                          {
                              d.table_id = $("#put_table_id").val();
                              d.searching = $("#searching2").val();
                          }
                      },
                      language: 
                      {
                        url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
                      },
                      dom: '<"top"f>rt<"bottom"lip><"clear">',
                      columnDefs: [
                        {
                            targets: '',
                            className: 'text-center'
                        },
                        {
                            targets: [0,1,6],
                            sortable: false
                        }
                      ],
                      fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
                      if(areWeUsingScroll)
                      {
                        if (perscroll1) perscroll1.destroy();
                            perscroll1 = new PerfectScrollbar('#mytable1_wrapper .dataTables_scrollBody');
                      }
                      },
                      scrollX: 'auto',
                      fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                        if(areWeUsingScroll)
                        { 
                        if (perscroll1) perscroll1.destroy();
                        perscroll1 = new PerfectScrollbar('#mytable1_wrapper .dataTables_scrollBody');
                        }
                      }
                  });
                }
                else table1.draw();
            }, 1000);

        });


        $(document).on('keyup', '#searching2', function(event) {
          event.preventDefault(); 
          table1.draw();
        });


        $(document).on('click', '.get_subscriber_formdata', function(e){
            e.preventDefault();
            var subscriber_table_id = $(this).attr('data-id');
            var form_id = $(this).attr('data-form-id');
            var page_table_id = $(this).attr('page_table_id');
            var subscribe_id = $(this).attr('subscribe_id');
            $("#get_subscriber_formdata").modal();
            get_subscriber_formdata(subscriber_table_id,subscribe_id,page_table_id,form_id); 
        });

        $('.modal').on("hidden.bs.modal", function (e) { 
            if ($('.modal:visible').length) { 
                $('body').addClass('modal-open');
            }
        });


        // delete label
        $(document).on('click', '.delete_campaign', function(event) {
            event.preventDefault();
            swal({
                title: '<?php echo $this->lang->line("Delete Flow Campaign"); ?>',
                text: '<?php echo $this->lang->line("If you delete this campaign, all the questions and answers corresponding to this campaign will also be deleted. Are you sure about deleting this campaign?"); ?>',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) 
                {
                    var table_id = $(this).attr("table_id");
                    var media_type = $(this).attr("media_type");
                    var csrf_token = $("#csrf_token").val();

                    $(this).addClass('btn-danger btn-progress').removeClass('btn-outline-danger');
                    var that = $(this);

                    $.ajax({
                        url: '<?php echo base_url('custom_field_manager/ajax_delete_flow_campaign'); ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {table_id:table_id,csrf_token:csrf_token,media_type:media_type},
                        success: function(response) {
                            if(response.status == 'successfull')
                            {
                                iziToast.success({title: '',message: response.message,position: 'bottomRight'});
                            } 
                            else
                            {
                                swal("<?php echo $this->lang->line('Error') ?>", response.message, "error");
                            }

                            table.draw();
                            $(that).removeClass('btn-danger btn-progress').addClass('btn-outline-danger');
                        }
                    });
                } 
            });

        });

        $(document).on('click', '.export_data', function(e) {
            e.preventDefault();
            $(this).removeClass('btn-outline-success');
            $(this).addClass('btn-success btn-progress disabled');
            var table_id = $(this).attr('table_id');
            // Downloads file via ajax call
            $.ajax({
                context: this,
                type: 'POST',
                dataType: 'JSON',
                data: { table_id : table_id },
                url: '<?php echo base_url('custom_field_manager/export_flow_data'); ?>',
                success: function(res) {
                    // Stops spinner
                    $(this).removeClass('btn-success btn-progress disabled');
                    $(this).addClass('btn-outline-success');
                    // Shows error if something goes wrong
                    if (res.error) {
                        swal({
                          icon: 'error',
                          text: res.error,
                          title: '<?php echo $this->lang->line('Error!'); ?>',
                        });
                        return;
                    }
                    if (res.info) {
                        swal({
                          icon: 'info',
                          text: res.info,
                          title: '<?php echo $this->lang->line('Info!'); ?>',
                        });
                        return;
                    }               
                    // If everything goes well, requests for downloading the file
                    if (res.status && 'ok' === res.status) {
                        window.location = '<?php echo base_url('custom_field_manager/export_flow_data'); ?>';
                    }
                },
                error: function(xhr, status, error) {
                    // Stops spinner
                    $(this).removeClass('btn-success disabled');
                    $(this).addClass('btn-outline-success');
                    // Shows error message
                    swal({
                        icon: 'error',
                        text: error,
                        title: '<?php echo $this->lang->line('Error!'); ?>',
                    });
                },
            });
        });

      
    });

    function get_subscriber_formdata(id,subscribe_id,media_type,form_id)
    {
      $("#waiting-div").show();
      $.ajax({
        type:'POST' ,
        url: "<?php echo site_url(); ?>custom_field_manager/get_subscriber_formdata",
        data:{id:id,page_id:page_id,subscribe_id:subscribe_id,form_id:form_id},
        success:function(response)
        {
            $("#waiting-div").hide();
            $(".formdata_div").html(response);
        }
      }); 
    }
 
</script>


<div class="modal fade" id="get_subscriber_formdata" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle"></i> <?php echo $this->lang->line("All Submitted Data"); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body" data-backdrop="static" data-keyboard="false">
                <div class="row">
                    <div class="col-12">
                        <div class="row formdata_div"></div>
                    </div>
                        
                    <div class="text-center waiting" id="waiting-div">
                        <i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i>
                    </div>
                </div>            
            </div>

            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('Close'); ?></button>
            </div>

        </div>
    </div>
</div>
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
<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">

<div class="table-responsive2 data-card">
    <input type="hidden" id="media_type" value="<?php echo $media_type; ?>">
	<table class="table table-bordered table-sm table-striped" id="mytable">
		<thead>
			<tr>
				<th>#</th>      
				<th><?php echo $this->lang->line("ID"); ?></th>      
				<th><?php echo $this->lang->line("Name"); ?></th>      
				<th><?php echo $this->lang->line("Reply Type"); ?></th>
                <th><?php echo $this->lang->line("Created Time"); ?></th>
				<th><?php echo $this->lang->line("Action"); ?></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<?php 
$areyousure=$this->lang->line("are you sure");
// $drop_menu = '<a class="btn btn-primary add_custom_field d-none" href="'.base_url('custom_field_manager/input_flow_builder/').$media_type">';
$drop_menu = '<a class="btn btn-primary add_custom_field float-right"  href="#"><i class="fas fa-plus-circle"></i> '.$this->lang->line("New Custom Field").'</a>';
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
    		order: [[ 1, "desc" ]],
    		pageLength: 10,
    		ajax: {
    			"url": base_url+'custom_field_manager/custom_field_list_data',
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
    			targets: [1],
    			visible: false
    		},
    		{
    			targets: [3,4,5],
    			className: 'text-center'
    		},
    		{
    			targets: [0,2,3,4,5],
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

        $(document).on('click', '.add_custom_field', function(event) {
            event.preventDefault();
            $("#name_err").text("");
            $("#reply_type_err").text("");
            $("#custom_field_name").val("");
            $("#selected_reply_type").val("").change();
            $("#add_custom_field").modal();
        });

        // create new label
        $(document).on('click', '#create_custom_field', function(event) {
            event.preventDefault();

            $("#name_err").text("");
            $("#reply_type_err").text("");

            custom_field_name = $("#custom_field_name").val();
            selected_reply_type = $("#selected_reply_type").val();
            var media_type = "<?php echo $media_type; ?>";

            if(custom_field_name == '') {
                $("#name_err").text("<?php echo $this->lang->line('Name Is Required') ?>")
                return false;
            }
            if(selected_reply_type == '') {
                $("#reply_type_err").text("<?php echo $this->lang->line('Reply Type Is Required') ?>")
                return false;
            }

            $(this).addClass('btn-progress');
            var that = $(this);

            $.ajax({
                url: '<?php echo base_url('custom_field_manager/ajax_custom_field_insert'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {custom_field_name:custom_field_name,selected_reply_type:selected_reply_type,media_type:media_type},
                success: function(response) {
                    $("#result_status").html('');
                    $("#result_status").css({"background":"","padding":"","margin":""});

                    if(response.status =="0")
                    {   
                        var errorMessage = JSON.stringify(response,null,10);
                        swal('<?php echo $this->lang->line("Error"); ?>',errorMessage, "error");
                        $("#result_status").css({"background":"#EEE","margin":"10px"});

                    } else if(response.status=='1')
                    {
                        iziToast.success({title: '',message: response.message,position: 'bottomRight'});
                    }

                    table.draw();
                    $(that).removeClass('btn-progress');
                }
            });

        });

        $(document).on('keyup', '#custom_field_name', function(event) {
            event.preventDefault();
            $("#name_err").text("");
        });

        $(document).on('change', '#selected_reply_type', function(event) {
            event.preventDefault();
            $("#reply_type_err").text("");
        });


        // delete label
        $(document).on('click', '.delete_custom_field', function(event) {
            event.preventDefault();

            swal({
                title: '<?php echo $this->lang->line("Delete Custom Field"); ?>',
                text: '<?php echo $this->lang->line("Do you want to delete this custom field?"); ?>',
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
                        url: '<?php echo base_url('custom_field_manager/ajax_delete_custom_field'); ?>',
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

        $('#add_custom_field').on('hidden.bs.modal', function() { 
            $("#name_err").text("");
            $("#reply_type_err").text("");
            $("#custom_field_name").val("");
            $("#selected_reply_type").val("").change();
            table.draw();
        })
      
  });
 
 
</script>


<div class="modal fade" id="add_custom_field" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="min-width: 30%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add Custom Field") ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="add_custom_field_modal_body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                          <label><i class="fas fa-tags"></i> <?php echo $this->lang->line('Custom Field Name'); ?></label>
                          <input type="text" name="custom_field_name" id="custom_field_name" class="form-control">
                          <span id="name_err" class="red"></span>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                          <label><i class="fas fa-file-alt"></i> <?php echo $this->lang->line('Reply Type'); ?></label>
                          <select class="form-control select2" id="selected_reply_type" name="selected_reply_type" style="width: 100%;">
                            <?php 
                                foreach ($reply_types as $value) 
                                {
                                    $key = $value;
                                    if($value == 'Date') $value = "Date (YYYY-MM-DD)";
                                    if($value == 'Time') $value = "Time (HH:MM)";
                                    echo "<option value='".$key."'>".$value."</option>";
                                } 
                            ?>
                          </select>
                          <span id="reply_type_err" class="red"></span>
                        </div>
                    </div>
                </div>            
            </div>

            <div id="result_status"></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-lg btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line('Close'); ?></button>
              <button id="create_custom_field" type="button" class="btn btn-lg btn-primary"><i class="fas fa-save"></i> <?php echo $this->lang->line('Save'); ?></button>
            </div>
        </div>
    </div>
</div>

<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-paper-plane"></i> <?php echo $page_title;?></h1>
    <div class="section-header-button">
      <a href="<?php echo base_url('messenger_bot_enhancers/create_quick_broadcast_campaign'); ?>" class="btn btn-primary"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create Campaign"); ?></a>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="<?php echo base_url('messenger_bot_broadcast'); ?>"><?php echo $this->lang->line("Messenger Broadcast");?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title;?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <style type="text/css">
    #search_page_id{width: 145px;}
    #search_status{width: 95px;}
    @media (max-width: 575.98px) {
      #search_page_id{width: 90px;}
      #search_status{width: 75px;}
    }
  </style>

  <?php $status_options = array(""=>$this->lang->line("Status"),"0"=>$this->lang->line("Pending"),"1"=>$this->lang->line("Processing"),"2"=>$this->lang->line("Completed")) ?>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">
            <div class="row">
              <div class="col-12 col-md-9">
                <?php echo 
                '<div class="input-group mb-3" id="searchbox">
                  <div class="input-group-prepend">
                    '.form_dropdown('search_page_id',$page_list,'','class="form-control select2" id="search_page_id"').'
                  </div>
                  <div class="input-group-prepend">'; ?>

                  <select name="search_status" id="search_status"  class="form-control">
                  	<option value=""><?php echo $this->lang->line("status") ?></option>
                  	<option value="SCHEDULED"><?php echo $this->lang->line("Pending") ?></option>
                  	<option value="IN_PROGRESS"><?php echo $this->lang->line("Processing") ?></option>
                  	<option value="FINISHED"><?php echo $this->lang->line("Completed") ?></option>
                  	<option value="CANCELED"><?php echo $this->lang->line("Canceled") ?></option>
                  </select>
                  </div>
                  <?php
                  echo 
                  '<input type="text" class="form-control" id="search_value" autofocus name="search_value" placeholder="'.$this->lang->line("Search...").'" style="max-width:30%;">
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="button" id="search_action"><i class="fas fa-search"></i> <span class="d-none d-sm-inline">'.$this->lang->line("Search").'</span></button>
                  </div>
                </div>'; ?>                                          
              </div>

              <div class="col-12 col-md-3">

              	<?php
				echo $drop_menu ='<a href="javascript:;" id="campaign_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> '.$this->lang->line("Choose Date").'</a><input type="hidden" id="campaign_date_range_val">';
				?>

                                         
              </div>
            </div>

            <div class="table-responsive2">
                <input type="hidden" id="put_page_id">
                <table class="table table-bordered" id="mytable">
                  <thead>
                    <tr>
                      <th>#</th>      
                      <th style="vertical-align:middle;width:20px">
                          <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                      </th>
                      <th><?php echo $this->lang->line("Name"); ?></th>
                      <th><?php echo $this->lang->line("Page Name")?></th>
                      <th><?php echo $this->lang->line("Template")?></th>
                      <th><?php echo $this->lang->line("Status"); ?></th>
                      <th><?php echo $this->lang->line("Sent"); ?></th>
                      <th><?php echo $this->lang->line("Actions"); ?></th>
                      <th><?php echo $this->lang->line("Broadcast ID"); ?></th>
                      <th><?php echo $this->lang->line("Scheduled at"); ?></th>
                      <th><?php echo $this->lang->line("Created at"); ?></th>
                      <th><?php echo $this->lang->line("Labels"); ?></th>

                    </tr>
                  </thead>
                </table>
            </div>
          </div>
        </div>
      </div>       
        
    </div>
  </div>          

</section>


<?php
	$somethingwentwrong = $this->lang->line("Something went wrong.");
	$doyoureallywanttodeletethiscampaign = $this->lang->line("Do you really want to delete this campaign?");
 ?>
<script>

	var base_url="<?php echo site_url(); ?>";

	var somethingwentwrong = "<?php echo $somethingwentwrong; ?>";	
	var doyoureallywanttodeletethiscampaign = "<?php echo $doyoureallywanttodeletethiscampaign; ?>";

	$('#campaign_date_range').daterangepicker({
	  ranges: {
	    '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
	    '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
	    '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	  },
	  startDate: moment().subtract(29, 'days'),
	  endDate  : moment()
	}, function (start, end) {
	  $('#campaign_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
	});

	var perscroll;
	var table1 = '';
	table1 = $("#mytable").DataTable({
	  serverSide: true,
	  processing:true,
	  bFilter: false,
	  order: [[ 10, "desc" ]],
	  pageLength: 10,
	  ajax: {
	      url: base_url+'messenger_bot_enhancers/quick_broadcast_campaign_data',
	      type: 'POST',
	      data: function ( d )
	      {
	          d.search_page_id = $('#search_page_id').val();
	          d.search_value = $('#search_value').val();
	          d.search_status = $('#search_status').val();
	          d.campaign_date_range = $('#campaign_date_range_val').val();
	      }
	  },
	  language: 
	  {
	    url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
	  },
	  dom: '<"top"f>rt<"bottom"lip><"clear">',
	  columnDefs: [
	    {
	        targets: [1,4],
	        visible: false
	    },
	    {
	        targets: [4,5,6,7,8,9,10],
	        className: 'text-center'
	    },
	    {
	        targets: [7],
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


	$("document").ready(function(){	   

	    $(document).on('change', '#search_page_id', function(e) {
	        table1.draw();
	    });

	    $(document).on('change', '#search_status', function(e) {
	        table1.draw();
	    });

	    $(document).on('change', '#campaign_date_range_val', function(event) {
        	event.preventDefault(); 
        	table1.draw();
      	});

      	$(document).on('click', '#search_action', function(event) {
        	event.preventDefault(); 
        	table1.draw();
      	});

	});


	$(document).on('click','.delete',function(e){
		e.preventDefault();

		var id = $(this).attr('id');
	    if (typeof(id)==='undefined')
	    { 
	    	swal('', '<?php echo $this->lang->line("This campaign is in processing state and can not be deleted.");?>', 'warning');
	    	return;
	    }

		swal({
			title: '<?php echo $this->lang->line("Delete Campaign"); ?>',
			text: doyoureallywanttodeletethiscampaign,
			icon: 'warning',
			buttons: true,
			dangerMode: true,
		})
		.then((willDelete) => {
			if (willDelete) 
			{
			    $(this).addClass('btn-progress btn-danger').removeClass('btn-outline-danger');
			    $.ajax({
			       context: this,
			       type:'POST' ,
			       url: "<?php echo base_url('messenger_bot_enhancers/delete_quick_campaign')?>",
			       data: {id:id},
			       dataType:'JSON',
			       success:function(response)
			       {
				       	$(this).removeClass('btn-progress btn-danger').addClass('btn-outline-danger');

				      	if(response.status=='1') 
				       	{
				      		iziToast.success({title: '',message: response.message,position: 'bottomRight'});
				       		table1.draw();
				       	}      	
				      	else iziToast.error({title: '',message: response.message,position: 'bottomRight'});
			       }
				});
			} 
		});

	});


</script>
<?php 
$this->load->view('admin/theme/message'); 
$this->load->view("include/upload_js"); 
?>
<style>
/*.dropdown-toggle::after{content:none !important;}
.dropdown-toggle::before{content:none !important;}*/
#page_id{width: 150px;}
#searching{max-width: 40%;}
@media (max-width: 575.98px) 
{
	#page_id{width: 90px;}
	#searching{max-width: 50%;}
}
</style>


<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-comments"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('comment_automation/comment_growth_tools'); ?>"><?php echo $this->lang->line("Comment Growth Tools"); ?></a></div>
      <div class="breadcrumb-item">
      	<a href="<?php echo base_url("comment_automation/comment_section_report"); ?>">
      		<?php echo $this->lang->line("Report"); ?>
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
              	<div class="input-group mb-3 float-left" id="searchbox">
                  <!-- search by page name -->
                  <div class="input-group-prepend">
                    <select class="select2 form-control" id="page_id" name="page_id">
                      <option value=""><?php echo $this->lang->line("Page Name"); ?></option>
                      <?php foreach ($page_info as $key => $value): ?>
                        <option value="<?php echo $value['id'];?>" <?php if($value['id']==$this->session->userdata('selected_global_page_table_id')) echo 'selected'; ?>><?php echo $value['page_name'];?></option>
                      <?php endforeach ?>
                    </select>
                  </div>

  	          	    <input type="text" class="form-control" id="searching" name="searching" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>" aria-label="" aria-describedby="basic-addon2">

  	          	  	<div class="input-group-append">
  	          	    	<button class="btn btn-primary" id="search_submit" title="<?php echo $this->lang->line('Search'); ?>" type="button"><i class="fas fa-search"></i> <span class="d-none d-sm-inline"><?php echo $this->lang->line('Search'); ?></span></button>
  	      	 	 	    </div>
          		  </div>
          		</div>
          		<div class="col-md-3 col-12">
          			<a href="javascript:;" id="post_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> <?php echo $this->lang->line("Choose Date");?></a><input type="hidden" id="post_date_range_val">
          		</div>
          	</div>
            <div class="table-responsive2">
            	<table class="table table-bordered" id="mytable">
                  <thead>
                  	<tr>
        							<th>#</th>      
        							<th><?php echo $this->lang->line("Campaign ID"); ?></th>
        							<th><?php echo $this->lang->line("Avatar"); ?></th>
        							<th><?php echo $this->lang->line("Name"); ?></th>
        							<th><?php echo $this->lang->line("Page name"); ?></th>	
        							<th><?php echo $this->lang->line("Post ID"); ?></th>
        							<th><?php echo $this->lang->line("Reply"); ?></th>
        							<th><?php echo $this->lang->line("Sent"); ?></th>
        							<th><?php echo $this->lang->line("Failed"); ?></th>
        							<th><?php echo $this->lang->line("Actions"); ?></th>
        							<th style="min-width: 100px"><?php echo $this->lang->line("Status"); ?></th>
        							<th><?php echo $this->lang->line("Scheduled at"); ?></th>
        							<th><?php echo $this->lang->line("Last Updated"); ?></th>
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


<?php
	$somethingwentwrong = $this->lang->line("something went wrong.");
	$pleasewait = $this->lang->line("please wait").'...';
	$areyousure = $this->lang->line("are you sure");
	$Doyouwanttodeletethisrecordfromdatabase = $this->lang->line("do you want to delete this record from database?");
 ?>

<script>
var base_url="<?php echo site_url(); ?>";
var somethingwentwrong="<?php echo $somethingwentwrong;?>";
var pleasewait="<?php echo $pleasewait;?>";
var areyousure="<?php echo $areyousure;?>";


$("document").ready(function(){
	$('[data-toggle="tooltip"]').tooltip();

	$('[data-toggle="popover"]').popover(); 
	$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});

    $('.datepicker').datetimepicker({
    theme:'light',
    format:'Y-m-d',
    formatDate:'Y-m-d',
    timepicker:false
    });

	var base_url = '<?php echo base_url(); ?>';

	setTimeout(function(){ 
	$('#post_date_range').daterangepicker({
    	ranges: {
        '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
        '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
        '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    	},
    	startDate: moment().subtract(29, 'days'),
    	endDate  : moment()
    }, function (start, end) {
      $('#post_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
    });
  	}, 2000);

    // datatable section started
    var perscroll;
    var table = $("#mytable").DataTable({
        serverSide: true,
        processing:true,
        bFilter: false,
        order: [[ 1, "desc" ]],
        pageLength: 10,
        ajax: 
        {
          "url": base_url+'comment_reply_enhancers/bulk_comment_reply_campaign_list_data',
          "type": 'POST',
    	    data: function ( d )
    	    {
    	        d.page_id = $('#page_id').val();
    	        d.searching = $('#searching').val();
    	        d.post_date_range = $('#post_date_range_val').val();
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
            	targets: [2,5,6,7,8,9,10,11,12],
            	className: 'text-center'
            },
            {
            	targets:[0,1,2,5,6,7,8,9,10],
            	sortable: false
            }
        ],
        fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
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

    $(document).on('change', '#page_id', function(event) {
      event.preventDefault(); 
      table.draw();
    });

    $(document).on('change', '#post_date_range_val', function(event) {
      event.preventDefault(); 
      table.draw();
    });

    $(document).on('click', '#search_submit', function(event) {
      event.preventDefault(); 
      table.draw();
    });

    // End of datatable section


    // report table started
    var table1 = '';
    var perscroll1;
    $(document).on('click','.show_report',function(e){
      e.preventDefault();

      $("#view_report_modal").modal();

      var table_id      = $(this).attr('table_id');

      var decrease_pageName = "";
      var page_name     = $(this).attr("page_name");
      if(page_name.length > 20)  decrease_pageName  = page_name.substr(0,15)+" ...";
      else decrease_pageName = page_name;

      var decrease_cam= "";
      var campaign_name = $(this).attr("campaign_name");
      if(campaign_name.length > 20)  decrease_cam  = campaign_name.substr(0,15)+" ...";
      else decrease_cam = campaign_name;

      var page_id       = $(this).attr("page_id");
      var post_id       = $(this).attr("post_id");
      var onlypostid    = post_id.split("_");
      
      var errorMsg      = $(this).attr("errorMsg");
      var replyContent    = $(this).attr("replyContent");

      $("#errorMsg").css("display","none");

      // set values in view from controller
      $("#put_row_id").val(table_id);
      $("#pageName").prepend(decrease_pageName);
      $("#pageName").attr("href","https://facebook.com/"+page_id);
      $("#campaign_name").html(decrease_cam);
      $("#postID").attr("href","https://facebook.com/"+post_id);
      $("#postID").html(onlypostid[1]);
      $("#replyContent").html(replyContent);

      if(errorMsg != '')
      {
        $("#errorMsg").css("display","block");
        $("#errorMsg").prepend("<i class='fas fa-exclamation-circle'></i> "+errorMsg);
      }

      if (table1 == '')
      {
        table1 = $("#mytable1").DataTable({
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 5, "desc" ]],
            pageLength: 10,
            ajax: {
              url: base_url+'comment_reply_enhancers/bulk_comment_reply_campaign_report',
              type: 'POST',
              data: function ( d )
              {
                  d.table_id = $("#put_row_id").val();
                  d.searching1 = $("#searching1").val();
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
                  targets: [0,1,2],
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
    });

    $(document).on('keyup', '#searching1', function(event) {
      event.preventDefault(); 
      table1.draw();
    });

    $('#view_report_modal').on('hidden.bs.modal', function () {
      $("#put_row_id").val('');
      $("#pageName").attr("href","");
      $("#campaign_name").attr("href","");
      $("#pageName").text("");
      $("#campaign_name").html('');
      $("#errorMsg").html("");
      $("#replyContent").html("");
      $("#searching1").val("");
      table.draw();
    });
    // End of reply table


	var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $Doyouwanttodeletethisrecordfromdatabase; ?>";
	$(document).on('click','.delete_campaign',function(e){
		e.preventDefault();
		swal({
			title: '<?php echo $this->lang->line("Are you sure?"); ?>',
			text: Doyouwanttodeletethisrecordfromdatabase,
			icon: 'warning',
			buttons: true,
			dangerMode: true,
		})
		.then((willDelete) => {
			if (willDelete) 
			{
				var id = $(this).attr("data-id");

				$.ajax({
					context: this,
					type:'POST' ,
					url:"<?php echo base_url('comment_reply_enhancers/delete_bulk_comment_reply_campaign')?>",
					data:{id:id},
					success:function(response){ 
			      iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been deleted successfully."); ?>',position: 'bottomRight'});
						table.draw();
					}
				});
			} 
		});
	});

	$(document).on('click', '.not_editable .not_delete_campaign', function(event) {
		event.preventDefault();
		swal("","<?php echo $this->lang->line('Sorry, Processing Campaign Can not be deleted.'); ?>",'error');
	});

	$(document).on('click', '.not_delete_campaign', function(event) {
		event.preventDefault();
    swal("","<?php echo $this->lang->line('Sorry, Processing Campaign Can not be deleted.'); ?>",'error');
	});

});
</script>


<div class="modal fade" id="view_report_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="fas fa-comments"></i> <?php echo $this->lang->line("Report");?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body data-card">
                <div class="row">
                  <div class="col-12">
                    <div class="row">
                      <div class="col-4">
                        <div class="card card-statistic-1">
                          <div class="card-icon bg-primary"><i class="fas fa-info-circle"></i></div>
                          <div class="card-wrap">
                            <div class="card-header"><h4><?php echo $this->lang->line('Campaign Name'); ?></h4></div>
                            <div class="card-body" id="campaign_name"></div>
                          </div>
                        </div>
                      </div>

                      <div class="col-12 col-md-4">
                        <div class="card card-statistic-1">
                          <div class="card-icon bg-primary">
                            <i class="far fa-newspaper"></i>
                          </div>
                          <div class="card-wrap">
                            <div class="card-header">
                              <h4><?php echo $this->lang->line('Page Name'); ?></h4>
                            </div>
                            <div class="card-body">
                              <a target="_BLANK" href="" id="pageName"></a>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-12 col-md-4">
                        <div class="card card-statistic-1">
                          <div class="card-icon bg-primary">
                            <i class="fas fa-id-card-alt"></i>
                          </div>
                          <div class="card-wrap">
                            <div class="card-header">
                              <h4><?php echo $this->lang->line('Post ID'); ?></h4>
                            </div>
                            <div class="card-body">
                              <a target="_BLANK" href="" id="postID"></a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-12"><div class="alert alert-danger text-center" id="errorMsg"></div><br></div>
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <input type="text" id="searching1" name="searching1" class="form-control" placeholder="<?php echo $this->lang->line("Search..."); ?>" style='width: 200px;'>                                          
                  </div>

                  <div class="col-12">
                    <div class="table-responsive2">
                      <input type="hidden" id="put_row_id">
                      <table class="table table-bordered" id="mytable1">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th><?php echo $this->lang->line("Commenter Name"); ?></th>
                              <th><?php echo $this->lang->line("Commenter ID"); ?></th>
                              <th><?php echo $this->lang->line("Comment Time"); ?></th>
                              <th><?php echo $this->lang->line("Reply Status"); ?></th>
                              <th><?php echo $this->lang->line("Replied At"); ?></th>
                            </tr>
                          </thead>
                      </table>
                    </div>
                  </div> 
                  <br><br>
                  <div class="col-12">
                    <div class="section">
                      <div class="section-title">
                        <h6><?php echo $this->lang->line('Reply Content'); ?></h6>
                      </div>
                      <div class="alert alert-light" id="replyContent"></div>
                    </div>
                  </div>
                </div>               
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="commenter_list_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("Report"); ?></h4>
			</div>
			<div class="modal-body" id="commenter_list_body">

			</div>
		</div>
	</div>
</div>








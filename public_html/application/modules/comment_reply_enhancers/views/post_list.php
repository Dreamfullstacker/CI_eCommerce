<?php 
	$this->load->view('admin/theme/message'); 
	$this->load->view("include/upload_js"); 

	$image_upload_limit = 1; 
	if($this->config->item('autoreply_image_upload_limit') != '')
	$image_upload_limit = $this->config->item('autoreply_image_upload_limit'); 

	$video_upload_limit = 3; 
	if($this->config->item('autoreply_video_upload_limit') != '')
	$video_upload_limit = $this->config->item('autoreply_video_upload_limit');
?>

<style>
	/*.dropdown-toggle::after{content:none !important;}
	.dropdown-toggle::before{content:none !important;}*/
	#page_id{width: 150px;}
	#post_id{max-width: 40%;}
	::placeholder{color: #ccc9c9 !important;}
	@media (max-width: 575.98px) {
		#page_id{width: 90px;}
  		#post_id{max-width: 50%;}
	}
	.pointer {cursor: pointer;}
</style>


<section class="section section_custom">
  	<div class="section-header">
	    <h1><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create Campaign"); ?></h1>
	    <div class="section-header-breadcrumb">
	      <div class="breadcrumb-item"><a href="<?php echo base_url('comment_automation/comment_growth_tools'); ?>"><?php echo $this->lang->line("Comment Growth Tools"); ?></a></div>
	      <div class="breadcrumb-item"><?php echo $this->lang->line("Tag Campaign"); ?></div>
	    </div>
  	</div>

  	<div class="section-body">
	    <div class="row">
	      <div class="col-12">
	        <div class="card">
	          <div class="card-body data-card">
	        	<div class="row">
	        		<div class="col-md-8 col-12">
	            	<div class="input-group mb-3 float-left" id="searchbox">
		          	  	
						<!-- search by page name -->
		          	    <div class="input-group-prepend">
	          	      		<select class="select2 form-control" id="page_id" name="page_id">
	          	        		<option value=""><?php echo $this->lang->line("Page Name"); ?></option>
				          	    <?php foreach ($page_info as $key => $value):
				          	    	if($value['id'] == $auto_search_page_info_table_id) : ?>
			          	    		<option selected value="<?php echo $value['id'];?>"><?php echo $value['page_name'];?></option>
			          	    	<?php else : ?>
			          	    		<option value="<?php echo $value['id'];?>" <?php if($value['id']==$this->session->userdata('selected_global_page_table_id')) echo 'selected'; ?>><?php echo $value['page_name'];?></option>
			          	    	<?php endif; ?>
				          	    <?php endforeach ?>
	      	      		  </select>
		          	    </div>

		          	    <input type="text" class="form-control" id="post_id" name="post_id" value="<?php if($post_id != 0) echo $post_id; ?>" placeholder="<?php echo $this->lang->line('Post ID'); ?>" aria-label="" aria-describedby="basic-addon2">

		          	  	<div class="input-group-append">
		          	    	<button class="btn btn-primary" id="search_submit" title="<?php echo $this->lang->line('Search'); ?>" type="button"><i class="fas fa-search"></i> <span class="d-none d-sm-inline"><?php echo $this->lang->line('Search'); ?></span></button>
		      	 	 	</div>
	          		</div>
	        		</div>
	        		<div class="col-md-4 col-12">
	        			<a href="javascript:;" id="post_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> <?php echo $this->lang->line("Choose Date");?></a><input type="hidden" id="post_date_range_val">
	        		</div>
	        	</div>
	            <div class="table-responsive2">
	            	<table class="table table-bordered" id="mytable">
		                <thead>
		                	<tr>
								<th>#</th>      
								<th><?php echo $this->lang->line("Page ID"); ?></th>      
								<th><?php echo $this->lang->line("Avatar"); ?></th>
								<th><?php echo $this->lang->line("page name"); ?></th>
								<th><?php echo $this->lang->line("post ID"); ?></th>
								<?php if($this->session->userdata('user_type') == 'Admin'|| in_array(201,$this->module_access)) { ?>
								<th><?php echo $this->lang->line("Comment & Bulk Tag"); ?></th>
								<?php } ?>
								<?php if($this->session->userdata('user_type') == 'Admin'|| in_array(202,$this->module_access)) { ?>
								<th><?php echo $this->lang->line("Bulk Comment Reply"); ?></th>
								<?php } ?>							
								<th><?php echo $this->lang->line("Re-scan"); ?></th>
								<th><?php echo $this->lang->line("Comments"); ?></th>
								<th><?php echo $this->lang->line("Commenters"); ?></th>
								<th><?php echo $this->lang->line("Last Scanned"); ?></th>
								<th><?php echo $this->lang->line("Post Created"); ?></th>
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
	$item_per_range=$this->config->item('item_per_range');
	if($item_per_range=='') $item_per_range=50;

	$somethingwentwrong = $this->lang->line("something went wrong.");
	$pleasewait = $this->lang->line("please wait").'...';
	$startcommenternames = $this->lang->line("Start typing commenter names you want to excude from tag list");
	$list_of_commenters = $this->lang->line("List of commenters which this campaign will tag");
	$campaign_name_is_required=$this->lang->line("Campaign name is required.");
	$tag_content_is_required=$this->lang->line("Tag content is required.");
	$you_have_not_selected_commenters=$this->lang->line("You have not selected commenters.");
	$no_subscribed_commenter_found=$this->lang->line("No subscribed commenter found.");
	$reply_content_is_required=$this->lang->line("Reply content is required.");
	$pleaseselectscheduletimetimezone = $this->lang->line("Please select schedule time/time zone.");
	$areyousureyouwanttorescan = $this->lang->line("Are you Sure you want to Re-scan?");
?>

<script>
	var base_url = "<?php echo site_url(); ?>";
	var somethingwentwrong = "<?php echo $somethingwentwrong;?>";
	var pleasewait = "<?php echo $pleasewait;?>";
	var startcommenternames = "<?php echo $startcommenternames;?>";
	var item_per_range = "<?php echo $item_per_range;?>";
	var list_of_commenters = "<?php echo $list_of_commenters;?>";
	var campaign_name_is_required = "<?php echo $campaign_name_is_required;?>";
	var tag_content_is_required = "<?php echo $tag_content_is_required;?>";
	var you_have_not_selected_commenters = "<?php echo $you_have_not_selected_commenters;?>";
	var no_subscribed_commenter_found = "<?php echo $no_subscribed_commenter_found;?>";
	var reply_content_is_required = "<?php echo $reply_content_is_required;?>";

	var image_upload_limit = "<?php echo $image_upload_limit; ?>";
	var video_upload_limit = "<?php echo $video_upload_limit; ?>";

	$("document").ready(function(){

		$('[data-toggle="popover"]').popover(); 
		$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});

		$(".schedule_block_item").hide();	
		$(".schedule_block_item2").hide();

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
		        "url": base_url+'comment_reply_enhancers/post_list_data',
		        "type": 'POST',
			    data: function ( d )
			    {
			        d.page_id = $('#page_id').val();
			        d.post_id = $('#post_id').val();
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
		        	targets: [2,4,5,6,7,8,9,10,11],
		        	className: 'text-center'
		        },
		        {
		        	targets:[0,1,2,4,5,6,7],
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

		var post_id = "<?php echo $post_id; ?>";
		if(post_id != 0) $("#search_submit").click();

		$(document).on('click', '#search_submit', function(event) {
		  event.preventDefault(); 
		  table.draw();
		});


		var table2 = '';
		$(document).on('click','.show_comment_list',function(e){
		  e.preventDefault();
	      var base_url = "<?php echo base_url(); ?>";
		  var table_id = $(this).attr('table_id');
		  var page_id = $(this).attr('page_id');
		  var page_name = $(this).attr('page_name');
		  var post_id = $(this).attr('post_id');
		  var page_post_link = '<a class="orange" target="_BLANK" href="https://facebook.com/'+page_id+'"><i class="fa fa-newspaper-o"></i> '+page_name+'</a> <a target="_BLANK" href="https://facebook.com/'+post_id+'"> (Visit Post)</a>';
		  var download_button = "<?php echo $this->lang->line("Download comment list info"); ?>";
		  var drop_menu = "<a href='"+base_url+"comment_reply_enhancers/download_comment_list_info/"+table_id+"' class='float-right' target='_blank' ><button class='btn btm-lg btn-outline-info download_comment_list_info'><i class='fa fa-cloud-download'></i> "+download_button+"</button></a>";

		  $("#put_comment_table_id").val(table_id);
		  $(".page_post_link").html(page_post_link);
		  $("#comment_list_modal").modal(); 

		  

		  if (table2 == '')
		  {
		  	setTimeout(function(){ 
		  	  $("#mytable2_filter").append(drop_menu); 
		  	}, 2000);

		    var perscroll2;
		    table2 = $("#mytable2").DataTable({
		        serverSide: true,
		        processing:true,
		        bFilter: true,
		        order: [[ 3, "desc" ]],
		        pageLength: 10,
		        ajax: {
		            url: base_url+'comment_reply_enhancers/post_comment_list',
		            type: 'POST',
		            data: function ( d )
		            {
		                d.table_id = $("#put_comment_table_id").val();
		                // d.commenter_searching = $("#commenter_searching").val();
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
		              targets: [0,2,4],
		              sortable: false
		          }
		        ],
		        fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
		        if(areWeUsingScroll)
		        {
		          if (perscroll2) perscroll2.destroy();
		            perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
		        }
		        },
		        scrollX: 'auto',
		        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
		          if(areWeUsingScroll)
		          { 
		          if (perscroll2) perscroll2.destroy();
		          perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
		          }
		        }
		    });
		  }
		  else table2.draw();

		}); 



		var table1 = '';
		$(document).on('click','.show_commenter_list',function(e){
		  e.preventDefault();
	      var base_url = "<?php echo base_url(); ?>";
		  var table_id = $(this).attr('table_id');
		  var page_id = $(this).attr('page_id');
		  var page_name = $(this).attr('page_name');
		  var post_id = $(this).attr('post_id');
		  var page_post_link = '<a class="orange" target="_BLANK" href="https://facebook.com/'+page_id+'"><i class="fa fa-newspaper-o"></i> '+page_name+'</a> <a target="_BLANK" href="https://facebook.com/'+post_id+'"> (Visit Post)</a>';
		  var download_button = "<?php echo $this->lang->line("Download commenter list info"); ?>";
		  var drop_menu = "<a href='"+base_url+"comment_reply_enhancers/download_commenter_list_info/"+table_id+"' class='float-right' target='_blank' ><button class='btn btm-lg btn-outline-info download_comment_list_info'><i class='fa fa-cloud-download'></i> "+download_button+"</button></a>";

		  $("#put_table_id").val(table_id);
		  $(".page_post_link").html(page_post_link);
		  $("#commenter_list_modal").modal(); 

		  

		  if (table1 == '')
		  {
		  	setTimeout(function(){ 
		  	  $("#mytable1_filter").append(drop_menu); 
		  	}, 2000);

		    var perscroll1;
		    table1 = $("#mytable1").DataTable({
		        serverSide: true,
		        processing:true,
		        bFilter: true,
		        order: [[ 3, "desc" ]],
		        pageLength: 10,
		        ajax: {
		            url: base_url+'comment_reply_enhancers/post_commenter_list',
		            type: 'POST',
		            data: function ( d )
		            {
		                d.table_id = $("#put_table_id").val();
		                // d.commenter_searching = $("#commenter_searching").val();
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
		              targets: [0,2,4],
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

		// End of datatable section


	  	$(document).on('change','input[name=schedule_type]',function(){  
	  		var scheduletype = $("input[name=schedule_type]:checked").val();

	    	if(typeof(scheduletype)=="undefined")
	    		$(".schedule_block_item").show();
	    	else 
	    	{
	    		$("#schedule_time").val("");
	    		$("#time_zone").val("");
	    		$(".schedule_block_item").hide();
	    	}
	    }); 

	    $(document).on('change','input[name=schedule_type2]',function(){ 
	    	var scheduletype2 = $("input[name=schedule_type2]:checked").val();   
	    	if(typeof(scheduletype2)=="undefined")
	    		$(".schedule_block_item2").show();
	    	else 
	    	{
	    		$("#schedule_time2").val("");
	    		$("#time_zone2").val("");
	    		$(".schedule_block_item2").hide();
	    	}
	    });
	 	

		var areyousureyouwanttorescan = '<?php echo $areyousureyouwanttorescan; ?>';
		$(document).on('click','.rescan_comments',function(e){
			e.preventDefault();
			swal({
				title: '',
				text: areyousureyouwanttorescan,
				icon: 'warning',
				buttons: true,
				dangerMode: true,
				context: this,
			})
			.then((willDelete) => {
				if (willDelete) 
				{
					var page_id = $(this).attr("page-id");
					var post_id = $(this).attr("post-id");
					var enable_id = $(this).attr("enable-id");
					var btn_id="rescan_"+page_id+"_"+post_id;
					$("#"+btn_id).addClass('btn-primary btn-progress').removeClass('btn-outline-primary');

					$.ajax({
						context: this,
						type:'POST' ,
						url:"<?php echo base_url('comment_reply_enhancers/rescan_commenter_info')?>",
						data: {page_id:page_id,post_id:post_id,enable_id:enable_id},
						success:function(response){ 
							$(this).removeClass('btn-primary btn-progress').addClass('btn-outline-primary');
				         	iziToast.success({title: '',message: '<?php echo $this->lang->line("Post Comments Has been Updated Successfully."); ?>',position: 'bottomRight'});
							table.draw();
						}
					});
				} 
			});

		});

		$(document).on('click','.commenter_subscribe_unsubscribe',function(){
		    $(this).html(pleasewait).addClass('disabled');
		    var subscribe_unsubscribe_status = $(this).attr('id');
		    $.ajax({
		      type:'POST',
		      url:"<?php echo site_url();?>comment_reply_enhancers/subscribe_unsubscribe_status_change",
		      data:{subscribe_unsubscribe_status:subscribe_unsubscribe_status},
		      success:function(response)
		      {
		         $("#"+subscribe_unsubscribe_status).parent().html(response); 
		      }
	    	});
	    });

	    $(document).on('click','.create_bulk_tag_campaign',function(){
	    	event.preventDefault();

			$("#comment_bulk_tag_campaign").modal();
			var post_val = $(this).attr("id");
			var exploded=[];
			exploded=post_val.split('-');
			var tag_machine_enabled_post_list_id=exploded[1];
			var tag_campaign_tag_machine_commenter_count=exploded[2];
			$("#tag_campaign_tag_machine_enabled_post_list_id").val(tag_machine_enabled_post_list_id);
			$("#tag_campaign_tag_machine_commenter_count").val(tag_campaign_tag_machine_commenter_count);

		  	// $('.include_autocomplete').tokenize({
		   //      datas: base_url+"comment_reply_enhancers/commenter_autocomplete/"+tag_machine_enabled_post_list_id,
		   //      placeholder: list_of_commenters,
		   //      dropdownMaxItems: 20,
		   //      tokensMaxItems: item_per_range
		   //  });

		   // make the schedule time field empty as it's filled with current date at initial stage
		   var makeScheduleValEmptyifscheduleisNow = $("input[name=schedule_type]:checked").val();
		   if(makeScheduleValEmptyifscheduleisNow == 'now') $("#schedule_time").val("");

		    $('.exclude_autocomplete').tokenize({
		        datas: base_url+"comment_reply_enhancers/commenter_autocomplete/"+tag_machine_enabled_post_list_id,
		        placeholder: startcommenternames,
		        dropdownMaxItems: 20,
		        tokensMaxItems: item_per_range
		    });

		    $.ajax({
	            type:'POST' ,
	            url:"<?php echo site_url();?>comment_reply_enhancers/commenter_range_option",
	            data:{tag_machine_enabled_post_list_id:tag_machine_enabled_post_list_id},
	            success:function(response){
	            	$("#commenter_range").html(response);
	            }
	        });
	        $("#loading_div").hide();

		});


		$("#image_video_upload").uploadFile({
	        url:base_url+"comment_reply_enhancers/upload_image_video",
	        fileName:"myfile",
	        maxFileSize:video_upload_limit*1024*1024,
	        showPreview:false,
	        returnType: "json",
	        dragDrop: true,
	        showDelete: true,
	        multiple:false,
	        maxFileCount:1, 
	        acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF,.flv,.mp4,.wmv,.WMV,.MP4,.FLV",
	        deleteCallback: function (data, pd) {
	            var delete_url="<?php echo site_url('comment_reply_enhancers/delete_uploaded_file');?>";
	            $.post(delete_url, {op: "delete",name: data},
	                function (resp,textStatus, jqXHR) {
	                	$("#uploaded_image_video").val('');                      
	                });
	           
	         },
	         onSuccess:function(files,data,xhr,pd)
	           {
	               // var data_modified = base_url+"upload/comment_reply_enhancers/"+data;
	               $("#uploaded_image_video").val(data);		
	           }
	    });

	    $("#image_video_upload2").uploadFile({
	        url:base_url+"comment_reply_enhancers/upload_image_video",
	        fileName:"myfile",
	        maxFileSize:video_upload_limit*1024*1024,
	        showPreview:false,
	        returnType: "json",
	        dragDrop: true,
	        showDelete: true,
	        multiple:false,
	        maxFileCount:1, 
	        acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF,.flv,.mp4,.wmv,.WMV,.MP4,.FLV",
	        deleteCallback: function (data, pd) {
	            var delete_url="<?php echo site_url('comment_reply_enhancers/delete_uploaded_file');?>";
	            $.post(delete_url, {op: "delete",name: data},
	                function (resp,textStatus, jqXHR) {
	                	$("#uploaded_image_video2").val('');                      
	                });
	           
	         },
	         onSuccess:function(files,data,xhr,pd)
	           {
	               // var data_modified = base_url+"upload/comment_reply_enhancers/"+data;
	               $("#uploaded_image_video2").val(data);		
	           }
	    });


	    $(document).on('click','#submit_post',function(){    
	          		    	
	    	var campaign_name = $("#campaign_name").val();
	    	var message = $("#message").val();
	    	var commenter_range = $("#commenter_range").val();
	    	
	    	if(campaign_name=="")
	    	{
	    		swal('<?php echo $this->lang->line("Warning"); ?>',"<?php echo $this->lang->line('Campaign name is required.');?>", 'warning');
	    		return;
	    	}

	    	if(message=="")
	    	{
	    		swal('<?php echo $this->lang->line("Warning"); ?>',"<?php echo $this->lang->line('Tag content is required.');?>", 'warning');
	    		return;
	    	}

	    	if(commenter_range=="" || commenter_range==null || typeof(commenter_range) == "undefined")
	    	{    		
	    		swal('<?php echo $this->lang->line("Warning"); ?>',"<?php echo $this->lang->line('You have not selected commenters.');?>", 'warning');
	    		return;
	    	}

	    	var schedule_type = $("input[name=schedule_type]:checked").val();
	    	var schedule_time = $("#schedule_time").val();
	    	var time_zone = $("#time_zone").val();
	    	var pleaseselectscheduletimetimezone = "<?php echo $pleaseselectscheduletimetimezone; ?>";
	    	if(typeof(schedule_type)=='undefined' && (schedule_time=="" || time_zone==""))
	    	{
	    		swal('<?php echo $this->lang->line("Warning"); ?>',"<?php echo $this->lang->line('Please select schedule time/time zone.');?>",'warning');
	    		return;
	    	}

		    $(this).addClass('btn-progress')
	    	var that = $(this);
	  	        	
	      	var queryString = new FormData($("#bulk_tag_campaign_form")[0]);
	      	$.ajax({
		       type:'POST' ,
		       url: base_url+"comment_reply_enhancers/create_bulk_tag_campaign_action",
		       data: queryString,
		       cache: false,
		       contentType: false,
		       processData: false,
		       dataType:'JSON',
		       success:function(response)
		       {  
		       		$(that).removeClass('btn-progress');

	      			if(response.status=='1') 
	      			{
	      				var span = document.createElement("span");
	      				span.innerHTML = response.message;
	      				swal({ title:'<?php echo $this->lang->line("Campagin has been created successfully."); ?>', content:span,icon:'success'});
	      			}
	      			else {
	      				var span = document.createElement("span");
	      				span.innerHTML = '';
	      				swal({ title:response.message, content:span,icon:'error'});
	      			}
		       }
	      	});

	    });


	    $(document).on('click','.bulk_comment_reply_campaign',function(){  		
			$("#bulk_comment_reply_campaign").modal();
			var post_val = $(this).attr("id");
			var exploded=[];
			exploded=post_val.split('-');
			var tag_machine_enabled_post_list_id=exploded[1];
			var tag_campaign_tag_machine_comment_count=exploded[2];
			$("#bulk_comment_reply_campaign_enabled_post_list_id").val(tag_machine_enabled_post_list_id);
			$("#bulk_comment_reply_campaign_commenter_count").val(tag_campaign_tag_machine_comment_count);	 

			// make the schedule time field empty as it's filled with current date at initial stage
			var makeScheduleValEmptyifscheduleisNow2 = $("input[name=schedule_type2]:checked").val();
			if(makeScheduleValEmptyifscheduleisNow2 == 'now')
				$("#schedule_time2").val(""); 
		});

		$(document).on('click','#submit_post2',function(){    
	          		    	
	    	var campaign_name = $("#campaign_name2").val();
	    	var message = $("#message2").val();
	    	
	    	if(campaign_name=="")
	    	{
	    		swal('<?php echo $this->lang->line("Warning"); ?>',"<?php echo $this->lang->line('Campaign name is required.');?>", 'warning');
	    		return;
	    	}

	    	if(message=="")
	    	{
	    		swal('<?php echo $this->lang->line("Warning"); ?>',"<?php echo $this->lang->line('Reply content is required.');?>", 'warning');
	    		return;
	    	}

	    	var schedule_type = $("input[name=schedule_type2]:checked").val();
	    	var schedule_time = $("#schedule_time2").val();
	    	var time_zone = $("#time_zone2").val();
	    	var pleaseselectscheduletimetimezone = "<?php echo $pleaseselectscheduletimetimezone; ?>";

	    	if(typeof(schedule_type)=='undefined' && (schedule_time=="" || time_zone==""))
	    	{
	    		swal('<?php echo $this->lang->line("Warning"); ?>',"<?php echo $this->lang->line('Please select schedule time/time zone.');?>", 'warning');
	    		return;
	    	}

		    $(this).addClass('btn-progress')
	    	var that = $(this);
	  	        	
	      	var queryString = new FormData($("#bulk_comment_reply_campaign_form")[0]);
	      	$.ajax({
				type:'POST' ,
				url: base_url+"comment_reply_enhancers/create_comment_reply_campaign_action",
				data: queryString,
				cache: false,
				contentType: false,
				processData: false,
				dataType:'JSON',
				success:function(response)
				{	
		       		$(that).removeClass('btn-progress');  

	      			if(response.status=='1') {

	      				var span = document.createElement("span");
	      				span.innerHTML = response.message;
	      				swal({ title:'<?php echo $this->lang->line("Campagin has been created successfully."); ?>', content:span,icon:'success'});
	      			}
	      			else {

	      				var span = document.createElement("span");
	      				span.innerHTML = '';
	      				swal({ title:response.message, content:span,icon:'error'});
	      			}
			       }
		      	});

	    });

		$(document).on('click','#lead_first_name',function(){
	    	var caretPos = $("#message2")[0].selectionStart;
		    var textAreaTxt = $("#message2").val();
		    var txtToAdd = " #LEAD_USER_FIRST_NAME# ";
		    $("#message2").val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
		});

		$(document).on('click','#lead_last_name',function(){

	    	var caretPos =  $("#message2")[0].selectionStart;
		    var textAreaTxt =  $("#message2").val();
		    var txtToAdd = " #LEAD_USER_LAST_NAME# ";
		    $("#message2").val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
		});

		$(document).on('click','#lead_tag_name',function(){

	    	var caretPos =  $("#message2")[0].selectionStart;
		    var textAreaTxt =  $("#message2").val();
		    var txtToAdd = " #TAG_USER# ";
		    $("#message2").val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
		});

		$('#comment_bulk_tag_campaign').on('hidden.bs.modal', function () { 
			$("#bulk_tag_campaign_form").trigger('reset');
			table.draw();
		});

		$('#bulk_comment_reply_campaign').on('hidden.bs.modal', function () { 
			$("#bulk_comment_reply_campaign_form").trigger('reset');
			table.draw();
		});


		$('.datetimepicker2').daterangepicker({
			locale: {format: 'YYYY-MM-DD hh:mm'},
			singleDatePicker: true,
			timePicker: true,
			timePicker24Hour: true,
			drops: "up"
		});	

		$('.datetimepicker3').daterangepicker({
			locale: {format: 'YYYY-MM-DD hh:mm'},
			singleDatePicker: true,
			timePicker: true,
			timePicker24Hour: true,
			drops: "up"
		});

	});
</script>

<div class="modal fade" id="comment_bulk_tag_campaign" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title text-center"><i class="fas fa-tags"></i> <?php echo $this->lang->line("Comment & Bulk Tag Campaign"); ?></h6>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12">					
						<form action="#" enctype="multipart/form-data" id="bulk_tag_campaign_form" method="post">
							<input type="hidden" name="tag_campaign_tag_machine_enabled_post_list_id" id="tag_campaign_tag_machine_enabled_post_list_id">
							<input type="hidden" name="tag_campaign_tag_machine_commenter_count" id="tag_campaign_tag_machine_commenter_count">
	
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line("campaign name") ?> *
											<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("campaign name"); ?>" data-content="<?php echo $this->lang->line("put a name so that you can identify it later"); ?>"><i class='fa fa-info-circle'></i> </a>
										</label>
										<input type="text" class="form-control"  name="campaign_name" id="campaign_name">
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
				                        <label><?php echo $this->lang->line("Select Commenter Range") ?> *
				                        	<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Select Commenter Range");?>" data-content="<?php echo $this->lang->line("This range is sorted by comment time in decending order.") ?>"><i class='fa fa-info-circle'></i> </a>
				                        </label>

				                        <select name="commenter_range" id="commenter_range"  class="form-control select2" size="5" style="width:100%;"></select>
				                    </div> 
								</div>
								<div class="col-12">
									<div class="form-group">
										<label><?php echo $this->lang->line("Tag Content") ?> *
											<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Tag Content") ?>" data-content="<?php echo $this->lang->line("Content to bulk tag commenters."); ?>"><i class='fa fa-info-circle'></i> </a>
										</label>
										<textarea class="form-control" name="message" id="message" placeholder="<?php echo $this->lang->line("Content to bulk tag commenters.");?>" style="height:130px !important;"></textarea>
									</div>
								</div>
								
								<div class="col-12" style="padding-bottom: 100px;">
									<div class="form-group">
										 <label><?php echo $this->lang->line("Do not tag these commenters") ?>
				                        	<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Do not tag these commenters") ?>" data-content="<?php echo $this->lang->line("You can choose one or more. The commenters you choose here will be unlisted from this campaign and will not be tagged. Start typing a commenter name, it is auto-complete.") ?>"><i class='fa fa-info-circle'></i> </a>
				                        </label>
				                        <select style="width:100%"  name="exclude[]" id="exclude" multiple="multiple" class="tokenize-sample form-control exclude_autocomplete">                                     
				                        </select>
				                    </div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group">
										<label class="control-label" ><?php echo $this->lang->line("image/video upload") ?>
											<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("image/video upload") ?>" data-content="<?php echo $this->lang->line("upload image or video to embed with your bulk tag comment.") ?>"><i class='fa fa-info-circle'></i></a>
										</label>
										<div class="form-group">      
					                        <div id="image_video_upload"><?php echo $this->lang->line("upload") ?></div>	     
										</div>
										<input type="hidden" name="uploaded_image_video" id="uploaded_image_video">
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line("Schedule") ?></label><br>
									  	<label class="custom-switch mt-2">
											<input type="checkbox" name="schedule_type" value="now" id="schedule_type" class="custom-switch-input" checked>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo $this->lang->line('Now');?></span>
									  	</label>
									</div>
								</div>
							</div>

							<div class="row schedule_block_item">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line("Schedule time") ?>  <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("schedule time") ?>" data-content="<?php echo $this->lang->line("Select date and time when you want to process this campaign.") ?>"><i class='fa fa-info-circle'></i> </a></label>
										<input placeholder="<?php echo $this->lang->line("time");?>"  name="schedule_time" id="schedule_time" class="form-control datetimepicker2" type="text"/>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line("time zone") ?>
											 <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("time zone") ?>" data-content="<?php echo $this->lang->line("server will consider your time zone when it process the campaign.") ?>"><i class='fa fa-info-circle'></i> </a>
										</label>
										<?php
										$time_zone[''] = $this->lang->line("please select");
										echo form_dropdown('time_zone',$time_zone,$this->config->item('time_zone'),' class="form-control select2" id="time_zone" required style="width:100%;"'); 
										?>
									</div>
								</div>
							</div>

		                    <!-- <div class="form-group" id="custom_input_div">				                       
		                        <label>
		                       		<?php echo $this->lang->line("Tag List")." [".$this->lang->line("Up to").": ".$item_per_range."]";?> * 
		                        	<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Tag These Commenters") ?>" data-content="<?php echo $this->lang->line("Select the commenters you want to tag.") ?>"><i class='fa fa-info-circle'></i> </a>
		                        </label>
		                        <select style="width:100px;"  name="include[]" id="include" multiple="multiple" class="tokenize-sample form-control include_autocomplete">                                     
		                        </select>
		                    </div>	 -->	
						</form>
					</div>
					
				</div>
				<div class="col-12 padding-0" style="margin-top:20px;">
					<button class="btn btn-lg btn-primary" id="submit_post" name="submit_post" type="button"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Create Campaign") ?> </button>
					<a class="btn btn-lg btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
				</div>
			</div>
		</div>
	</div>
</div>



<div class="modal fade" id="bulk_comment_reply_campaign" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title text-center"><i class="fas fa-comments"></i> <?php echo $this->lang->line("Bulk Comment Reply Campaign");?></h6>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">				
				<div class="row">
					<div class="col-12">							
						<form action="#" enctype="multipart/form-data" id="bulk_comment_reply_campaign_form" method="post">
							<input type="hidden" name="bulk_comment_reply_campaign_enabled_post_list_id" id="bulk_comment_reply_campaign_enabled_post_list_id">
							<input type="hidden" name="bulk_comment_reply_campaign_commenter_count" id="bulk_comment_reply_campaign_commenter_count">
							
							<div class="row">
								<div class="col-12">
									<div class="form-group">
										<label><?php echo $this->lang->line("campaign name") ?> *
											<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("campaign name"); ?>" data-content="<?php echo $this->lang->line("put a name so that you can identify it later"); ?>"><i class='fa fa-info-circle'></i> </a>
										</label>
										<input type="text" class="form-control"  name="campaign_name2" id="campaign_name2">
									</div>
								</div>

								<div class="col-12">
									<div class="form-group">
										<label><?php echo $this->lang->line("Reply Content") ?> *
											<a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Reply Content") ?>" data-content="<?php echo $this->lang->line("Bulk comment reply content."); ?> Spintax example : {Hello|Hi|Hola} to you, {Mr.|Mrs.|Ms.} {{John|Tara|Sara}|Tom|Dave}"><i class='fa fa-info-circle'></i> </a>
										</label>

										<span class='float-right'>
											<a data-toggle="tooltip" data-placement="top" title='<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>' class='btn-outline btn-sm' id='lead_tag_name'><i class='fas fa-tag'></i> <?php echo $this->lang->line("Tag user") ?></a>
										</span>
										<span class='float-right'>
											<a data-toggle="tooltip" data-placement="top" title='<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>' class='btn-outline btn-sm' id='lead_last_name'><i class='fas fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
										</span>
										<span class='float-right'>
											<a data-toggle="tooltip" data-placement="top" title='<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>' class='btn-outline btn-sm' id='lead_first_name'><i class='fas fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
										</span>
										<textarea class="form-control" name="message2" id="message2" placeholder="<?php echo $this->lang->line("Bulk comment reply content.");?> Spintax example : {Hello|Hi|Hola} to you, {Mr.|Mrs.|Ms.} {{John|Tara|Sara}|Tom|Dave}" style="height:130px !important;"></textarea>
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line("reply same commenter multiple times?") ?>
											<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("reply same commenter multiple times?") ?>" data-content="<?php echo $this->lang->line("same user may comment multiple time, do you want to reply all of them or not.") ?>"><i class='fa fa-info-circle'></i></a>
										</label><br>
									  	<label class="custom-switch mt-2">
											<input type="checkbox" name="reply_multiple" value="1" id="reply_multiple" class="custom-switch-input" checked>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
									  	</label>
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group">
										<label class="control-label" ><?php echo $this->lang->line("image/video upload") ?>
											<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("image/video upload") ?>" data-content="<?php echo $this->lang->line("upload image or video to embed with your comment reply.") ?> "><i class='fa fa-info-circle'></i></a>
										</label>
										<div class="form-group">      
					                        <div id="image_video_upload2"><?php echo $this->lang->line("upload") ?></div>	     
										</div>
										<input type="hidden" name="uploaded_image_video2" id="uploaded_image_video2">
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line("delay between two replies [seconds]") ?> *
											<a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("delay between two replies [seconds]") ?>" data-content="<?php echo $this->lang->line("Too frequent replies can be suspicious to Facebook. It is safe to use some seconds of delay. Zero means random delay."); ?>"><i class='fa fa-info-circle'></i> </a>
										</label>
										<input class="form-control" name="delay_time" id="delay_time" type="number" min="0" value="0">
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line("Schedule") ?></label><br>
									  	<label class="custom-switch mt-2">
											<input type="checkbox" name="schedule_type2" value="now" id="schedule_type2" class="custom-switch-input" checked>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo $this->lang->line('Now');?></span>
									  	</label>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group schedule_block_item2">
										<label><?php echo $this->lang->line("schedule time") ?> <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("schedule time") ?>" data-content="<?php echo $this->lang->line("Select date and time when you want to process this campaign.") ?>"><i class='fa fa-info-circle'></i> </a></label>
										<input placeholder="<?php echo $this->lang->line("time");?>"  name="schedule_time2" id="schedule_time2" class="form-control datetimepicker3" type="text"/>
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group schedule_block_item2" style="padding-right:0;">
										<label><?php echo $this->lang->line("time zone") ?>
											 <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("time zone") ?>" data-content="<?php echo $this->lang->line("server will consider your time zone when it process the campaign.") ?>"><i class='fa fa-info-circle'></i> </a>
										</label>
										<?php
										$time_zone[''] = $this->lang->line("please select");
										echo form_dropdown('time_zone2',$time_zone,$this->config->item('time_zone'),' class="form-control select2" id="time_zone2" required style="width:100%;"'); 
										?>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="col-12 padding-0" style="margin-top:20px;">
					<button class="btn btn-lg btn-primary" id="submit_post2" name="submit_post2" type="button"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Create Campaign") ?> </button>
					<a class="btn btn-lg btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="comment_list_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fa fa-comment"></i> <?php echo $this->lang->line("Comment List"); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body" id="comment_list_body">
				<div class="row">
					<div class="col-12 text-center page_post_link"></div>
				</div>
				<br/>
				<div class="row">
					<!-- <div class="col-12 margin-top">
					  <input type="text" id="commenter_searching" name="commenter_searching" class="form-control" placeholder="<?php echo $this->lang->line("Search..."); ?>" style='width:200px;'>                                          
					</div> -->
					<div class="col-12">
					  <div class="data-card">   
					    <input type="hidden" name="put_comment_table_id" id="put_comment_table_id">                  
					    <div class="table-responsive2">
					      <table class="table table-bordered" id="mytable2">
					        <thead>
					          <tr>
					            <th>#</th>
					            <th><?php echo $this->lang->line("Commenter Name"); ?></th> 
					            <th><?php echo $this->lang->line("Comment ID"); ?></th> 
					            <th><?php echo $this->lang->line("Comment Time"); ?></th>  
					            <th><?php echo $this->lang->line("Comment"); ?></th>  
					          </tr>
					        </thead>
					      </table>
					    </div>
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
				<h5 class="modal-title"><i class="fa fa-user"></i> <?php echo $this->lang->line("Commenter List"); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body" id="commenter_list_body">
				<div class="row">
					<div class="col-12 text-center page_post_link"></div>
				</div>
				<br/>
				<div class="row">
					<!-- <div class="col-12 margin-top">
					  <input type="text" id="commenter_searching" name="commenter_searching" class="form-control" placeholder="<?php echo $this->lang->line("Search..."); ?>" style='width:200px;'>                                          
					</div> -->
					<div class="col-12">
					  <div class="data-card">   
					    <input type="hidden" name="put_table_id" id="put_table_id">                  
					    <div class="table-responsive2">
					      <table class="table table-bordered" id="mytable1">
					        <thead>
					          <tr>
					            <th>#</th>
					            <th><?php echo $this->lang->line("Commenter Name"); ?></th> 
					            <th><?php echo $this->lang->line("Last Comment ID"); ?></th> 
					            <th><?php echo $this->lang->line("Last Comment Time"); ?></th>  
					            <th><?php echo $this->lang->line("Actions"); ?></th>  
					          </tr>
					        </thead>
					      </table>
					    </div>
					  </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<style type="text/css" media="screen">
	.popover
	{
	    min-width: 300px !important;
	}
	.tokenize-sample,.Tokenize{border:none !important;padding:0 !important;}
	.box-header{border-bottom:1px solid #ccc !important;margin-bottom:15px;}
	.box-primary{border:1px solid #ccc !important;}
	.box-body{padding:10px 10px !important;}
	.preview{padding:10px 0 !important;}
	.box-footer{border-top:1px solid #ccc !important;padding:10px 0;}
	.padding-5{padding:5px;}
	.padding-20{padding:20px;}
	.padding-top-10{padding:10px;}
	.box-header{color:#3C8DBC;}
	.box-body
	{
		font-family: helvetica,​arial,​sans-serif;
		padding: 20px;
		background: #fcfcfc;
	}
	#test_msg_box_body
	{
		background: #fff !important;
	}
	.box-footer 
	{		
		background: #fcfcfc;
	}

	.ms-choice span
	{
		padding-top: 2px !important;
	}
	.hidden
	{
		display: none;
	}
	.box-primary
	{
		-webkit-box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
		-moz-box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
		box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
	}

	.TokensContainer{height: 140px !important;}	
	.content-wrapper{background: #fff;}
	.ajax-upload-dragdrop{width:100% !important;}
</style>




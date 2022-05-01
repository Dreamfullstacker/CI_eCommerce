<?php $this->load->view("include/upload_js"); ?>

<section class="section section_custom">
	<div class="section-header">
		<h1 class="modal-title text-center"><i class="fa fa-edit"></i> <?php echo $this->lang->line("Edit Bulk Comment Reply Campaign"); ?></h1>
		<div class="section-header-breadcrumb">
		  <div class="breadcrumb-item">
		  	<a href="<?php echo base_url("comment_reply_enhancers/post_list"); ?>">
		  	<?php echo $this->lang->line("Tag Campaign"); ?></a>
		  </div>
		  <div class="breadcrumb-item">
		  	<a href="<?php echo base_url("comment_reply_enhancers/bulk_comment_reply_campaign_list"); ?>">
		  		<?php echo $this->lang->line("Campaign List"); ?>
		  	</a>
		  </div>
		  <div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>
	<div class="section-body">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<form action="#" enctype="multipart/form-data" id="bulk_comment_reply_campaign_form" method="post">
							<input type="hidden" name="campaign_id" value="<?php echo $xdata[0]["id"];?>">
							<input type="hidden" name="bulk_comment_reply_campaign_enabled_post_list_id" value="<?php echo $xdata[0]["tag_machine_enabled_post_list_id"];?>" id="bulk_comment_reply_campaign_enabled_post_list_id">

							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line("campaign name") ?> *
											<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("campaign name"); ?>" data-content="<?php echo $this->lang->line("put a name so that you can identify it later"); ?>"><i class='fa fa-info-circle'></i> </a>
										</label>
										<input value="<?php echo $xdata[0]["campaign_name"];?>" type="text" class="form-control"  name="campaign_name2" id="campaign_name2">
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line("reply same commenter multiple times?") ?>
											<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("reply same commenter multiple times?") ?>" data-content="<?php echo $this->lang->line("same user may comment multiple time, do you want to reply all of them or not.") ?>"><i class='fa fa-info-circle'></i></a>
										</label><br>
									  	<label class="custom-switch mt-2">
											<input type="checkbox" name="reply_multiple" value="1" <?php if($xdata[0]["reply_multiple"]=='1') echo 'checked'; ?> id="reply_multiple" class="custom-switch-input">
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
									  	</label>
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
										<textarea class="form-control" name="message2" id="message2" placeholder="<?php echo $this->lang->line("Bulk comment reply content.");?> Spintax example : {Hello|Hi|Hola} to you, {Mr.|Mrs.|Ms.} {{John|Tara|Sara}|Tom|Dave}" style="height:130px !important;"><?php echo $xdata[0]["reply_content"];?></textarea>
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line("delay between two replies [seconds]") ?> *
											<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("delay between two replies [seconds]") ?>" data-content="<?php echo $this->lang->line("Too frequent replies can be suspicious to Facebook. It is safe to use some seconds of delay. Zero means random delay."); ?>"><i class='fa fa-info-circle'></i> </a>
										</label>
										<input value="<?php echo $xdata[0]["delay_time"];?>" class="form-control" name="delay_time" id="delay_time" type="number" min="0" value="0">
									</div>
								</div>
								
								<div class="col-12 col-md-6">
									<div class="row">
										<div class="col-12 col-md-6">
											<div class="form-group">
												<label><?php echo $this->lang->line("image/video upload") ?>
													<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("image/video upload") ?>" data-content="<?php echo $this->lang->line("upload image or video to embed with your comment reply.") ?>"><i class='fa fa-info-circle'></i></a>
												</label>
												<div class="form-group">      
							                        <div id="image_video_upload2"><?php echo $this->lang->line("upload") ?></div>	     
												</div>
												<input type="hidden" value="<?php echo $xdata[0]["uploaded_image_video"];?>" name="uploaded_image_video2" id="uploaded_image_video2">
											</div>
										</div>
										<div class="col-12 col-md-6">
											<?php if($xdata[0]["uploaded_image_video"]!="") 
											{
												$ext_exp=explode('.', $xdata[0]["uploaded_image_video"]);
												$ext=array_pop($ext_exp);
												$video_array=array("flv","mp4","wmv");
											?>
											<div class="form-group text-center">
												<label></label><br>
											<?php
												if(!in_array($ext,$video_array))
												{
													echo '<a href="#" title="'.$this->lang->line('See image').'" id="img_preview" img-src="'.base_url("upload/comment_reply_enhancers/").$xdata[0]["uploaded_image_video"].'" class="btn btn-primary" ><i class="fas fa-image"></i></a>';
												}
												else
												{
													echo '<a href="#" title="'.$this->lang->line('See Video').'" id="vid_preview" vid-src="'.base_url("upload/comment_reply_enhancers/").$xdata[0]["uploaded_image_video"].'" class="btn btn-primary" ><i class="fas fa-video"></i></a>';
												}
												echo '</div>';
											} ?>
										</div>
									</div>
								</div>

								<input type="hidden" name="schedule_type2" value="later" id="schedule_type2">

								<div class="col-12 col-md-6">
									<div class="form-group schedule_block_item2">
										<label><?php echo $this->lang->line("schedule time") ?>  <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("schedule time") ?>" data-content="<?php echo $this->lang->line("Select date and time when you want to process this campaign.") ?>"><i class='fa fa-info-circle'></i> </a></label>
										<input value="<?php echo $xdata[0]["schedule_time"];?>" placeholder="<?php echo $this->lang->line("time");?>"  name="schedule_time2" id="schedule_time2" class="form-control datetimepicker" type="text"/>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group schedule_block_item2">
										<label><?php echo $this->lang->line("time zone") ?>
											 <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("time zone") ?>" data-content="<?php echo $this->lang->line("server will consider your time zone when it process the campaign.") ?>"><i class='fa fa-info-circle'></i> </a>
										</label>
										<?php
										$time_zone[''] = $this->lang->line("please select");
										echo form_dropdown('time_zone2',$time_zone,$xdata[0]["time_zone"],' class="form-control select2" id="time_zone2" required style="width:100%;"'); 
										?>
									</div>
								</div>
							</div>
						</form>

						<div class="clearfix"></div>
						<div class="card-footer padding-0" style="margin-top:20px;">
							<button class="btn btn-lg btn-primary" id="submit_post2" name="submit_post2" type="button"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Update Campaign") ?> </button>
							<a class="btn btn-lg btn-light float-right" onclick='goBack("comment_reply_enhancers/bulk_comment_reply_campaign_list",0)'><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
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
	$startcommenternames = $this->lang->line("Start typing commenter names you want to excude from tag list");
	$list_of_commenters = $this->lang->line("List of commenters which this campaign will tag");
	$campaign_name_is_required=$this->lang->line("Campaign name is required.");
	$tag_content_is_required=$this->lang->line("Tag content is required.");
	$you_have_not_selected_commenters=$this->lang->line("You have not selected commenters.");
	$no_subscribed_commenter_found=$this->lang->line("No subscribed commenter found.");
	$reply_content_is_required=$this->lang->line("Reply content is required.");
	$pleaseselectscheduletimetimezone = $this->lang->line("Please select schedule time/time zone.");
	$item_per_range=$this->config->item('item_per_range');
    if($item_per_range=='') $item_per_range=50;
    $tag_machine_enabled_post_list_id=$xdata[0]["tag_machine_enabled_post_list_id"];
 ?>
<script>
	var base_url="<?php echo site_url(); ?>";
	var somethingwentwrong="<?php echo $somethingwentwrong;?>";
	var pleasewait="<?php echo $pleasewait;?>";
	var areyousure="<?php echo $areyousure;?>";
	var startcommenternames="<?php echo $startcommenternames;?>";
	var item_per_range="<?php echo $item_per_range;?>";
	var list_of_commenters="<?php echo $list_of_commenters;?>";
	var campaign_name_is_required="<?php echo $campaign_name_is_required;?>";
	var tag_content_is_required="<?php echo $tag_content_is_required;?>";
	var you_have_not_selected_commenters="<?php echo $you_have_not_selected_commenters;?>";
	var no_subscribed_commenter_found="<?php echo $no_subscribed_commenter_found;?>";
	var reply_content_is_required="<?php echo $reply_content_is_required;?>"
	var tag_machine_enabled_post_list_id="<?php echo $tag_machine_enabled_post_list_id;?>"
</script>

<script>

$("document").ready(function(){

	$('[data-toggle="popover"]').popover(); 
	$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});

    $('.datepicker').datetimepicker({
   	theme:'light',
   	format:'Y-m-d H:i:s',
   	formatDate:'Y-m-d H:i:s'
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

    	var schedule_type = 'later';
    	var schedule_time = $("#schedule_time2").val();
    	var time_zone = $("#time_zone2").val();
    	var pleaseselectscheduletimetimezone = "<?php echo $pleaseselectscheduletimetimezone; ?>";
    	if(schedule_type=='later' && (schedule_time=="" || time_zone==""))
    	{
    		swal('<?php echo $this->lang->line("Warning"); ?>',"<?php echo $this->lang->line('Please select schedule time/time zone.');?>", 'warning');
    		return;
    	}

	    $(this).addClass('btn-progress')
    	var that = $(this);
  	        	
      	var queryString = new FormData($("#bulk_comment_reply_campaign_form")[0]);
      	$.ajax({
	       type:'POST' ,
	       url: base_url+"comment_reply_enhancers/edit_comment_reply_campaign_action",
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
      				swal({ title:'<?php echo $this->lang->line("Campagin has been updated successfully."); ?>', content:span,icon:'success'});
      			}
      			else
      			{
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

  	$("#image_video_upload2").uploadFile({
        url:base_url+"comment_reply_enhancers/upload_image_video",
        fileName:"myfile",
        maxFileSize:100*1024*1024,
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
            // var data_modified = base_url+"upload/commenttagmachine/"+data;
            $("#uploaded_image_video2").val(data);                 
            $("#img_preview").hide();		
            $("#vid_preview").hide();		
       	}
    });


  	// to preview attachment if available
    $(document).on('click', '#img_preview,#vid_preview', function(event) {
    	event.preventDefault();

    	$("#preview_modal").modal();

    	var imgSrc = $("#img_preview").attr("img-src");
    	var vidSrc = $("#vid_preview").attr("vid-src");
    	if(imgSrc != "" && typeof(imgSrc) != "undefined")
    	{
    		$(".modal-body").append('<img id="showImage" src="'+imgSrc+'" alt="" style="width:100%">');
    	} 
    	
    	if(vidSrc != "" && typeof(vidSrc) != "undefined")
    	{
    		$(".modal-body").append('<video width="100%" controls style="border:1px solid #ccc"><source src="'+vidSrc+'"></video>');
    	}


    });

    $("#preview_modal").on('hidden.bs.modal', function (){
    	$(".modal-body").html("");
    });

});
</script>



<style type="text/css" media="screen">
	.popover
	{
	    min-width: 300px !important;
	}
	.tokenize-sample,.Tokenize{border:none !important;padding:0 !important;}
	.box-header{border-bottom:1px solid #ccc !important;margin-bottom:15px;}
	.box-primary{border:1px solid #ccc !important;}
	.box-body{padding:10px 20px !important;}
	.preview{padding:10px 0 !important;}
	.box-footer{border-top:1px solid #ccc !important;padding:10px 0;}
	.padding-5{padding:5px;}
	.padding-20{padding:20px;}
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
	.content-wrapper{background: #eee !important;}
</style>

<div class="modal fade" id="preview_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    	</div>
      <div class="modal-body"></div>
    </div>
  </div>
</div>
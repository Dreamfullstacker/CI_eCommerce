<?php 
$this->load->view("include/upload_js"); 

$image_upload_limit = 1; 
if($this->config->item('facebook_poster_image_upload_limit') != '')
$image_upload_limit = $this->config->item('facebook_poster_image_upload_limit');

$video_upload_limit = 30; 
if($this->config->item('allowed_video_size') != '')
$video_upload_limit = $this->config->item('allowed_video_size');

?>

<style type="text/css">
	.card{margin-bottom:0;border-radius: 0;}
	.main_card{box-shadow: none !important;height: 100%;}
	.collef{padding-right: 0px;border-right:1px solid #f9f9f9;}
	.colmid{padding-left: 0px;}
	.card .card-header input{max-width: 100% !important;}
	.card .card-header h4 a{font-weight: 700 !important;}
	::placeholder{color: white !important;}
	.input-group-prepend{margin-left:-1px;}
	.input-group-text{background: #eee;}
	.schedule_block_item label,label{color:#34395e !important;font-size:12px !important;font-weight:600 !important;letter-spacing: .5px !important;}
	.video_format{cursor: pointer;}
	.thumbnail_format{cursor: pointer;}
	.share_crosspost_mean{cursor: pointer;}
	.img-responsive{max-width: 100%;}
	}
</style>

<section class="section section_custom">
	<div class="section-header">
		<h1><i class="fas fa-tv"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><a href="<?php echo base_url("ultrapost"); ?>"><?php echo $this->lang->line("Facebook Poster"); ?></a></div>
			<div class="breadcrumb-item"><a href='<?php echo base_url("vidcasterlive/live_scheduler_list"); ?>'><?php echo $this->lang->line("Livestreaming");?></a></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>

	<div class="section-body">
		<div class="row">
			<div class="col-12 col-md-7 collef">
				<div class="card main_card">
					<div class="card-header"><h4><i class="fas fa-list"></i> <?php echo $this->lang->line('Campaign Form'); ?></h4></div>
					<div class="card-body">
						<form action="#" enctype="multipart/form-data" id="live_scheduler_form" method="post">

							<input type="hidden" name="hidden_id" value="<?php echo $xdata[0]["id"];?>">
							<div class="form-group">
								<label><?php echo $this->lang->line("Campaign Name");?></label>
								<input type="input" value='<?php echo $xdata[0]["scheduler_name"];?>' class="form-control"  name="scheduler_name" id="scheduler_name">
							</div>
							<div class="form-group">
								<label><?php echo $this->lang->line("Post Content");?></label>
								<textarea class="form-control" name="message" id="message"><?php echo $xdata[0]["message"];?></textarea>
							</div>

							<div class="form-group">
								<label><?php echo $this->lang->line("Broadcast pre-recorded video from system");?></label><br/>
								<div class="row">
									<div class="col-12 col-md-5">
										<label class="custom-switch">
										  <input type="radio" name="use_system_video" value="yes" class="custom-switch-input" <?php if($xdata[0]["use_system_video"]=="yes") echo "checked";?> >
										  <span class="custom-switch-indicator"></span>
										  <span class="custom-switch-description"><?php echo $this->lang->line('Yes'); ?></span>
										</label>
									</div>
									<div class="col-12 col-md-7">
										<label class="custom-switch">
										  <input type="radio" name="use_system_video" value="no" class="custom-switch-input" <?php if($xdata[0]["use_system_video"]=="no") echo "checked";?> >
										  <span class="custom-switch-indicator"></span>
										  <span class="custom-switch-description"><?php echo $this->lang->line("No (I'll use third party broadcasting software)"); ?></span>
										</label>
									</div>
								</div>
							</div>

							<div class="row system_video">
								<div class="col-12">
									<div class="form-group">
										<label><?php echo $this->lang->line("Upload Video");?> <i class='blue fa fa-info-circle video_format'></i></label>
										<div class="form-group">      
					                        <div id="live_video_upload"><?php echo $this->lang->line('Upload');?></div>	     
										</div>
										<input class="preview_video_block" type="hidden" name="scheduled_video_url" value="" id="scheduled_video_url">
										<!-- <input class="preview_video_block" type="hidden" name="scheduled_video_url" value="<?php echo $xdata[0]["scheduled_video_url"];?>" id="scheduled_video_url"> -->
										<a id="play-video" class="btn btn-sm btn-default btn-xs border_gray"><i class="fa fa-play blue"></i> <?php echo $this->lang->line("Play Video"); ?></a>
									</div>
								</div>
							</div>	


							<?php 
								$video_height=array(
									'1920' => '1920',
									'1280' => '1280',
									'1080' => '1080',
									'854' => '854',
									'720' => '720',
									'640' => '640',
									'540' => '540',
									'480' => '480',
									'360' => '360'
								); 

								$video_width=array(
									'1920' => '1920',
									'1280' => '1280',
									'1080' => '1080',
									'854' => '854',
									'720' => '720',
									'640' => '640',
									'540' => '540',
									'480' => '480',
									'360' => '360'
								); 
							?>

							<div class="row system_video">
								<div class="col-12 col-sm-12 col-md-6 col-lg-6">
									<div class="form-group">
										<label><?php echo $this->lang->line("Video width");?></label>
										<?php
										$video_width[''] = $this->lang->line('Please Select');
										echo form_dropdown('video_width',$video_width,$xdata[0]["video_width"],' class="form-control select2" id="video_width"'); 
										?>
									</div>	
								</div>
								<div class="col-12 col-sm-12 col-md-6 col-lg-6">
									<div class="form-group">
										<label><?php echo $this->lang->line("Video height");?></label>
										<?php
										$video_height[''] = $this->lang->line('Please Select');
										echo form_dropdown('video_height',$video_height,$xdata[0]["video_height"],' class="form-control select2" id="video_height"'); 
										?>
									</div>	
								</div>
							</div>

							<div class="form-group">
								<label><?php echo $this->lang->line("Schedule Type");?> *</label><br/>
								<div class="row">
									<div class="col-12 col-md-6">
										<label class="custom-switch">
										  <input type="radio" name="schedule_type" value="now" class="custom-switch-input" <?php if($xdata[0]["schedule_type"]=="now") echo "checked";?> >
										  <span class="custom-switch-indicator"></span>
										  <span class="custom-switch-description"><?php echo $this->lang->line('Now'); ?></span>
										</label>
									</div>
									<div class="col-12 col-md-6">
										<label class="custom-switch">
										  <input type="radio" name="schedule_type" value="later" class="custom-switch-input" <?php if($xdata[0]["schedule_type"]=="later") echo "checked";?> >
										  <span class="custom-switch-indicator"></span>
										  <span class="custom-switch-description"><?php echo $this->lang->line('Later'); ?></span>
										</label>
									</div>
								</div>
							</div>
											

							<div class="hide_if_now">

								<div class="row">
									<div class="col-12 col-md-6">
										<div class="form-group schedule_block_item">
											<label><?php echo $this->lang->line("Planned time to go live");?> *</label>
											<input  name="schedule_time" id="schedule_time" value='<?php echo $xdata[0]["schedule_time"];?>' class="form-control date_time_picker" type="text"/>
										</div>
									</div>
									<div class="col-12 col-md-6">
										<div class="form-group schedule_block_item">
											<label><?php echo $this->lang->line("Time zone");?> *</label>
											<?php
											$time_zone[''] = $this->lang->line("Please Select");
											echo form_dropdown('time_zone',$time_zone,$xdata[0]["time_zone"],' class="form-control select2" id="time_zone" required'); 
											?>
										</div>	
									</div>
								</div>


								<div class="form-group">
									<input name="create_event" <?php if($xdata[0]["create_event"]=="1") echo "checked"; ?> value="1" id="create_event_yes" type="radio"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->lang->line("I want to create live event now.");?> <br/>
									<input name="create_event" <?php if($xdata[0]["create_event"]=="0") echo "checked"; ?> value="0"  id="create_event_no"  type="radio"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->lang->line("I do not want to create live event, go live directly.");?>
								</div>

								<div class="hide_if_no">
									<div class="form-group hidden">
										<label><?php echo $this->lang->line("Thumbnail Image URL");?></label>
										<input class="form-control" value="<?php echo $xdata[0]["image_url"];?>" name="image_url" id="image_url" type="text"> 
									</div>
									<div class="form-group"> 
										<label><?php echo $this->lang->line("Upload Thumbnail Image");?> <i class='blue fa fa-info-circle thumbnail_format'></i></label>     
				                        <div id="image_url_upload"><?php echo $this->lang->line('Upload');?></div>
				                        <a id="play-image" class="btn btn-sm btn-default btn-xs border_gray"><i class="fa fa-eye blue"></i> <?php echo $this->lang->line("view image"); ?></a>
									</div>
								</div>
							</div>

							<div class="form-group">

								<?php 
								 	$facebook_rx_fb_user_info_id=isset($fb_user_info[0]["id"]) ? $fb_user_info[0]["id"] : 0; 
								 	$facebook_rx_fb_user_info_name=isset($fb_user_info[0]["name"]) ? $fb_user_info[0]["name"] : ""; 
								 	$facebook_rx_fb_user_info_access_token=isset($fb_user_info[0]["access_token"]) ? $fb_user_info[0]["access_token"] : ""; 
								 	$profile_picture="https://graph.facebook.com/me/picture?access_token={$facebook_rx_fb_user_info_access_token}&width=150&height=150";
								?>
								<?php 
									$page_or_group_or_user=$xdata[0]["page_or_group_or_user"];			
									$page_or_group_or_user_id=$xdata[0]["page_group_user_id"];			
									$facebook_rx_fb_user_info_id=$xdata[0]["facebook_rx_fb_user_info_id"]; 
								?>	
								<label><?php echo $this->lang->line("Post to page/group");?> *</label>
								<select class="form-control select2" name="post_to" id="post_to">
									<!-- <option <?php if($page_or_group_or_user=='user') echo 'selected'; ?> value="profile-<?php echo $facebook_rx_fb_user_info_id;?>" picture="<?php echo $profile_picture;?>" display_name="<?php echo $facebook_rx_fb_user_info_name;?>" ><?php echo $facebook_rx_fb_user_info_name." (".$this->lang->line('Timeline').")";?></option> -->
									<?php 
										foreach($fb_page_info as $key=>$val)
										{	
											$id=$val['id'];
											$page_name=$val['page_name'];
											$page_profile=$val['page_profile'];	
											$checked='';
											if($page_or_group_or_user=='page' && $id==$page_or_group_or_user_id) $checked='selected';

											echo '<option value="page-'.$id.'" '.$checked.' picture="'.$page_profile.'" display_name="'.$page_name.'" >'.$page_name.' ('.$this->lang->line("Page").')</option>';					
										}
									?>
									<?php 
										foreach($fb_group_info as $key=>$val)
										{	
											$id=$val['id'];
											$group_name=$val['group_name'];
											$group_profile=$val['group_profile'];
											$checked='';
											if($page_or_group_or_user=='group' && $id==$page_or_group_or_user_id) $checked='selected';

											echo '<option value="group-'.$id.'" '.$checked.' picture="'.$group_profile.'" display_name="'.$group_name.'" >'.$group_name.' ('.$this->lang->line("Group").')</option>';	
										}
									?>
								</select>
									
							</div>				

							<br>
							<?php 
							 	if($this->session->userdata("user_type")=="Admin" || in_array(254,$this->module_access)) $like_comment_Share_reply_block_class=""; 
							 	else $like_comment_Share_reply_block_class="hidden";
							?>

							<div id="like_comment_Share_reply_block" class="<?php echo $like_comment_Share_reply_block_class;?>">
								<div class="form-group">
									<label><?php echo $this->lang->line("Auto share as pages / Crosspost to pages");?> <i class='blue fa fa-info-circle share_crosspost_mean'></i></label>
									<div class="row">
										<div class="col-4">
											<label class="custom-switch">
											  <input type="radio" name="share_or_cross" value="nothing" class="custom-switch-input" <?php if($xdata[0]["share_or_cross"]=="nothing") echo "checked";?> >
											  <span class="custom-switch-indicator"></span>
											  <span class="custom-switch-description"><?php echo $this->lang->line('Nothing'); ?></span>
											</label>
										</div>
										<div class="col-4">
											<label class="custom-switch">
											  <input type="radio" name="share_or_cross" value="crossposting" class="custom-switch-input" <?php if($xdata[0]["share_or_cross"]=="crossposting") echo "checked";?> >
											  <span class="custom-switch-indicator"></span>
											  <span class="custom-switch-description"><?php echo $this->lang->line('Crossposting'); ?></span>
											</label>
										</div>
										<div class="col-4">
											<label class="custom-switch">
											  <input type="radio" name="share_or_cross" value="auto_share" class="custom-switch-input" <?php if($xdata[0]["share_or_cross"]=="auto_share") echo "checked";?> >
											  <span class="custom-switch-indicator"></span>
											  <span class="custom-switch-description"><?php echo $this->lang->line('Auto share as pages'); ?></span>
											</label>
										</div>
									</div>
								</div>

								<div class="form-group crosspost_block hidden">
									<label><?php echo $this->lang->line("Crosspost to pages (only works for page post)");?></label><br/>
									<div class="row">
										<div class="col-4">
											<label class="custom-switch">
											  <input type="radio" name="crosspost_enable_disable" value="1" class="custom-switch-input" <?php if($xdata[0]["crosspost_enable_disable"]=="1") echo "checked";?> >
											  <span class="custom-switch-indicator"></span>
											  <span class="custom-switch-description"><?php echo $this->lang->line('Enable'); ?></span>
											</label>
										</div>
										<div class="col-4">
											<label class="custom-switch">
											  <input type="radio" name="crosspost_enable_disable" value="0" class="custom-switch-input" <?php if($xdata[0]["crosspost_enable_disable"]=="0") echo "checked";?> >
											  <span class="custom-switch-indicator"></span>
											  <span class="custom-switch-description"><?php echo $this->lang->line('Disable'); ?></span>
											</label>
										</div>
									</div>
								</div>

								<div class="form-group crosspost_block_item">
									<label><?php echo $this->lang->line("Select pages for Crosspost");?> *</label>
									<select multiple="multiple"  class="form-control select2" id="crosspost_this_post_by_pages" name="crosspost_this_post_by_pages[]">	
									<?php
										$crosspost_this_post_by_pages=json_decode($xdata[0]["crosspost_this_post_by_pages"],true);
										foreach($fb_page_info as $key=>$val)
										{	
											$page_id=$val['page_id'];
											$page_name=$val['page_name'];
											if(in_array($page_id,$crosspost_this_post_by_pages))
												echo "<option selected='selected' value='{$page_id}'>{$page_name}</option>";
											else
												echo "<option value='{$page_id}'>{$page_name}</option>";								
										}
									 ?>						
									</select>
								</div>

								<div class="form-group auto_share_block hidden">
									<label><?php echo $this->lang->line("Auto share this post (only works for page post)");?></label><br/>
									<div class="row">
										<div class="col-4">
											<label class="custom-switch">
											  <input type="radio" name="auto_share_post" value="1" class="custom-switch-input" <?php if($xdata[0]["auto_share_post"]=="1")echo "checked";?> >
											  <span class="custom-switch-indicator"></span>
											  <span class="custom-switch-description"><?php echo $this->lang->line('Enable'); ?></span>
											</label>
										</div>
										<div class="col-4">
											<label class="custom-switch">
											  <input type="radio" name="auto_share_post" value="0" class="custom-switch-input" <?php if($xdata[0]["auto_share_post"]=="0")echo "checked";?> >
											  <span class="custom-switch-indicator"></span>
											  <span class="custom-switch-description"><?php echo $this->lang->line('Disable'); ?></span>
											</label>
										</div>
									</div>
								</div>


								<div class="form-group hidden">
									<label><?php echo $this->lang->line("Auto share to timeline");?></label><br/>
									<input name="auto_share_to_profile" <?php if($xdata[0]["auto_share_to_profile"]=="1")echo "checked";?> value="<?php echo $facebook_rx_fb_user_info_id;?>" id="auto_share_to_profile_yes"  type="radio"> <?php echo $this->lang->line("Share to timeline");?> (<?php echo $facebook_rx_fb_user_info_name;?>) &nbsp;&nbsp;&nbsp;&nbsp;
									<input name="auto_share_to_profile" <?php if($xdata[0]["auto_share_to_profile"]=="0")echo "checked";?> value="No" id="auto_share_to_profile_no" type="radio"> <?php echo $this->lang->line("No, do not share");?>
								</div>	

								<div class="form-group auto_share_post_block_item">
									<label><?php echo $this->lang->line("Select pages for auto share");?> *</label>
									<select multiple="multiple"  class="form-control select2" id="auto_share_this_post_by_pages" name="auto_share_this_post_by_pages[]">	
									<?php
										$auto_share_this_post_by_pages=json_decode($xdata[0]["auto_share_this_post_by_pages"],true);
										foreach($fb_page_info as $key=>$val)
										{	
											$id=$val['id'];
											$page_name=$val['page_name'];
											if(in_array($id,$auto_share_this_post_by_pages))
												echo "<option selected='selected' value='{$id}'>{$page_name}</option>";
											else
												echo "<option value='{$id}'>{$page_name}</option>";								
										}
									 ?>						
									</select>
								</div>		

								<div class="form-group hidden">
									<label><?php echo $this->lang->line("Auto like this post as all other pages (only works for page post)");?></label><br/>
									<div class="row">
										<div class="col-4">
											<label class="custom-switch">
											  <input type="radio" name="auto_like_post" value="1" class="custom-switch-input" <?php if($xdata[0]["auto_like_post"]=="1") echo "checked";?>>
											  <span class="custom-switch-indicator"></span>
											  <span class="custom-switch-description"><?php echo $this->lang->line('Enable'); ?></span>
											</label>
										</div>
										<div class="col-4">
											<label class="custom-switch">
											  <input type="radio" name="auto_like_post" value="0" class="custom-switch-input" <?php if($xdata[0]["auto_like_post"]=="0") echo "checked";?> >
											  <span class="custom-switch-indicator"></span>
											  <span class="custom-switch-description"><?php echo $this->lang->line('Disable'); ?></span>
											</label>
										</div>
									</div>
								</div>

								<div class="form-group hidden">
									<label><?php echo $this->lang->line("Auto private reply on user comments");?></label><br/>
									<div class="row">
										<div class="col-4">
											<label class="custom-switch">
											  <input type="radio" name="auto_private_reply" value="1" class="custom-switch-input" <?php if($xdata[0]["auto_private_reply"]=="1") echo "checked";?>>
											  <span class="custom-switch-indicator"></span>
											  <span class="custom-switch-description"><?php echo $this->lang->line('Enable'); ?></span>
											</label>
										</div>
										<div class="col-4">
											<label class="custom-switch">
											  <input type="radio" name="auto_private_reply" value="0" class="custom-switch-input" <?php if($xdata[0]["auto_private_reply"]=="0") echo "checked";?> >
											  <span class="custom-switch-indicator"></span>
											  <span class="custom-switch-description"><?php echo $this->lang->line('Disable'); ?></span>
											</label>
										</div>
									</div>
								</div>

								<div class="form-group auto_reply_block_item">
									<label><?php echo $this->lang->line("Private reply");?> *</label>
									<textarea class="form-control" name="auto_private_reply_text" id="auto_private_reply_text"><?php echo $xdata[0]["auto_private_reply"];?></textarea>
								</div>	

								<div class="form-group">
									<label><?php echo $this->lang->line("Auto comment");?></label><br/>
									<div class="row">
										<div class="col-4">
											<label class="custom-switch">
											  <input type="radio" name="auto_comment" value="1" class="custom-switch-input" <?php if($xdata[0]["auto_comment"]=="1") echo "checked";?> >
											  <span class="custom-switch-indicator"></span>
											  <span class="custom-switch-description"><?php echo $this->lang->line('Enable'); ?></span>
											</label>
										</div>
										<div class="col-4">
											<label class="custom-switch">
											  <input type="radio" name="auto_comment" value="0" class="custom-switch-input" <?php if($xdata[0]["auto_comment"]=="0") echo "checked";?> >
											  <span class="custom-switch-indicator"></span>
											  <span class="custom-switch-description"><?php echo $this->lang->line('Disable'); ?></span>
											</label>
										</div>
									</div>
								</div>

								<div class="form-group auto_comment_block_item">
									<label><?php echo $this->lang->line("Comment");?> *</label>
									<textarea class="form-control" name="auto_comment_text" id="auto_comment_text"><?php echo $xdata[0]["auto_comment_text"];?></textarea>
								</div>

							</div>						

							<div class="clearfix">
								<button class="btn btn-primary btn-lg" id="submit_post" name="submit_post" type="button"><i class="fa fa-clone"></i> <?php echo $this->lang->line("Clone Campaign");?> </button>
							</div>

						</form>
					</div>
				</div>
			</div>  <!-- end of col-6 left part -->

			<div class="col-12 col-md-5 colmid">
				<div class="card main_card">
				    <div class="card-header"><h4><i class="fa fa-facebook-official"></i> <?php echo $this->lang->line('Preview'); ?></h4></div>
				    <div class="card-body">
				    	<div class="preview">
										
							<img src="<?php echo $profile_picture;?>" class="preview_cover_img inline pull-left text-center" alt="X">
							<span class="preview_page"><?php echo $facebook_rx_fb_user_info_name;?></span><span id="live_text" style="color:#9197a3;"> plans to go live.</span><br/>
							<span class="preview_page_sm">Now <?php echo isset($app_info[0]['app_name']) ? $app_info[0]['app_name'] : $this->config->item("product_short_name");?></span><br/><br/>	
							<span class="preview_message"><br/></span>					
							<div class="preview_only_img_block">				
								<div class="row" style="padding-top:100px;padding-left:30px">
									<div class="hidden-xs hidden-sm col-md-3 col-lg-3">
										<img src="<?php echo $profile_picture;?>" alt="Thumb" style="width:100px;height:100px;border-radius:100px;padding:2px;background: #fff"; class="schedule_image_preview">
									</div>
									<div class="col-12 col-mg-9 col-lg-9">
										<span class="schedule_time_preview" style="color:#fff;font-size:18px;font-weight: bold;"><?php $cur_date=date("-m-d H:i:s"); echo date("F j",strtotime($cur_date))." at ".date("g:ia",strtotime($cur_date));?></span><br/>
										<span class="schedule_page_preview" style="color:#fff"><span class="schedule_page_preview_name"><?php echo $facebook_rx_fb_user_info_name;?></span> plans to go live</span><br/>
										<a href="#" class="btn btn-light" style="background: transparent;color:#fff;padding:10px 20px;margin-top:7px;"> <i class="fa fa-calendar"></i> Get Reminder</a>
									</div>
								</div>
							</div>

							<div class="preview_direct_block">		
								<img src="<?php echo base_url('assets/images/video-thumbnail.jpg');?>" alt="Thumb"  class="img-responsive">					
							</div>

						</div>
					</div>
				</div> <!-- end of col-6 right part -->
			</div>
		</div>
	</div>
</section>




<?php 
$xauto_share_post=$xdata[0]["auto_share_post"];
$xauto_private_reply=$xdata[0]["auto_private_reply"];
$xauto_comment=$xdata[0]["auto_comment"];
$xcreate_event=$xdata[0]["create_event"];
$user_id=$this->session->userdata("user_id");
?>
<script>
	function htmlspecialchars(str) {
		 if (typeof(str) == "string") {
		  str = str.replace(/&/g, "&amp;"); /* must do &amp; first */
		  str = str.replace(/"/g, "&quot;");
		  str = str.replace(/'/g, "&#039;");
		  str = str.replace(/</g, "&lt;");
		  str = str.replace(/>/g, "&gt;");
		  }
		 return str;
	}
	$("document").ready(function(){

		$(document).on('click','.share_crosspost_mean',function(e){
			e.preventDefault();
			$("#share_crosspost_modal").modal();
		});

		// put the current schedule time to preview section
		var schedule_time=$("#schedule_time").val();
		$.ajax({
		    type:'POST' ,
		    url:"<?php echo site_url();?>vidcasterlive/date_display_formatter",
		    data:{schedule_time:schedule_time},
		    success:function(response){
		    	$(".schedule_time_preview").html(response);	               
		    }
		});
		// end of put the current schedule time to preview section

        $(document).on('blur','#schedule_time',function(){ 
	        var schedule_time=$(this).val();
	        $.ajax({
	            type:'POST' ,
	            url:"<?php echo site_url();?>vidcasterlive/date_display_formatter",
	            data:{schedule_time:schedule_time},
	            success:function(response){
	            	$(".schedule_time_preview").html(response);	               
	            }
	        }); 	 
        });

		var emoji_message_div =	$("#message").emojioneArea({
        	autocomplete: false,
			pickerPosition: "bottom"
			//hideSource: false,
     	 });

		var base_url="<?php echo base_url();?>";
		$("#loading").hide();
		$(".preview_direct_block").hide();

		var today = new Date();
		var next_date = new Date(today.getFullYear(), today.getMonth(), today.getDate()+7);
		$('.date_time_picker').datetimepicker({
			theme:'light',
			format:'Y-m-d H:i:s',
			formatDate:'Y-m-d H:i:s',
			minDate: today,
			maxDate: next_date
		})	


        var xauto_share_post="<?php echo $xauto_share_post;?>";
        var xauto_private_reply="<?php echo $xauto_private_reply;?>";
        var xauto_comment="<?php echo $xauto_comment;?>";
        var xcreate_event="<?php echo $xcreate_event;?>";
        var user_id="<?php echo $user_id;?>";

        if(xauto_share_post=="0") $(".auto_share_post_block_item").hide();
        if(xauto_private_reply=="0") $(".auto_reply_block_item").hide();
        if(xauto_comment=="0") $(".auto_comment_block_item").hide();
        if(xcreate_event=="0") $(".hide_if_no").hide();

 
        $(document).on('click','#play-video',function(){  
        	var video_url=$("#scheduled_video_url").val();
        	if(video_url=="")
        	{
        		display_alert("<?php echo $this->lang->line("No video found to play");?>");
        		return false;
        	}
        	var src=base_url+"upload_caster/live_video/"+video_url;
        	var html='<video width="100%" border="1" height="auto" controls> <source src="'+src+'">Your browser does not support the video tag.</video>';
        	$("#modal-play .modal-body").html(html);
        	$("#modal-play").modal();
        }); 

        $(document).on('click','#play-image',function(){  
        	var image_url=$("#image_url").val();
        	if(image_url=="")
        	{
        		display_alert("<?php echo $this->lang->line("No image found to display");?>");
        		return false;
        	}
        	var src=image_url;
        	var html='<img width="100%" height="auto" src="'+src+'">';
        	$("#modal-play .modal-body").html(html);
        	$("#modal-play").modal();
        }); 

        // share or crosspost change section
        var share_or_cross = $("input[name=share_or_cross]:checked").val(); 
    	if(share_or_cross=="crossposting")
    	{
    		var go_live_page_id = $("#post_to").val();
    		var page_group_user = go_live_page_id.split("-");
    		if(page_group_user[0] == 'page')
    		{
        		var page_table_id = page_group_user[1];
        		$.ajax({
        		    type:'POST' ,
        		    url:"<?php echo site_url();?>vidcasterlive/get_crosspostallowed_pages",
        		    data:{page_table_id:page_table_id},
        		    success:function(response){
        		    	$("#crosspost_this_post_by_pages").html(response);	               
        		    }
        		});
    		}

        	if($("input[name=crosspost_enable_disable]:checked").val()=='1') $(".crosspost_block_item").show();
        	else $(".crosspost_block_item").hide();
        	$(".auto_share_post_block_item").hide();

    	}
        else if(share_or_cross=="auto_share")
        {
        	if($("input[name=auto_share_post]:checked").val()=='1') $(".auto_share_post_block_item").show();
        	else $(".auto_share_post_block_item").hide();
        	$(".crosspost_block_item").hide();
        }
        else
        {
        	$(".crosspost_block_item").hide();
        	$(".auto_share_post_block_item").hide();        	
        }

        $(document).on('change','input[name=share_or_cross]',function(){    
    		var go_live_page_id = $("#post_to").val();
    		var page_group_user = go_live_page_id.split("-");
        	if($(this).val()=="crossposting")
        	{
        		if(page_group_user[0] != 'page')
        			swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("This feature only works for Page post. It will not work if you go live to your any Groups. Please select a page from above list."); ?>', 'warning');
        		
        		if(page_group_user[0] == 'page')
        		{
	        		var page_table_id = page_group_user[1];
	        		$.ajax({
	        		    type:'POST' ,
	        		    url:"<?php echo site_url();?>vidcasterlive/get_crosspostallowed_pages",
	        		    data:{page_table_id:page_table_id},
	        		    success:function(response){
	        		    	$("#crosspost_this_post_by_pages").html(response);	               
	        		    }
	        		});
        		}

	        	if($("input[name=crosspost_enable_disable]:checked").val()=='1') $(".crosspost_block_item").show();
	        	else $(".crosspost_block_item").hide();
	        	$(".auto_share_post_block_item").hide();
        	}
	        else if($(this).val()=="auto_share")
	        {
	        	if(page_group_user[0] != 'page')
        			swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("This feature only works for Page post. It will not work if you go live to your any Groups. Please select a page from above list."); ?>', 'warning');
        		
	        	if($("input[name=auto_share_post]:checked").val()=='1') $(".auto_share_post_block_item").show();
	        	else $(".auto_share_post_block_item").hide();
	        	$(".crosspost_block_item").hide();
	        }
	        else
	        {
	        	$(".crosspost_block_item").hide();
	        	$(".auto_share_post_block_item").hide();
	        }
        }); 
        // end of share of crosspost change section

        $(document).on('change','input[name=crosspost_enable_disable]',function(){    
        	if($("input[name=crosspost_enable_disable]:checked").val()=="1")
        	$(".crosspost_block_item").show();
        	else $(".crosspost_block_item").hide();
        }); 

        $(document).on('change','input[name=auto_share_post]',function(){    
        	if($("input[name=auto_share_post]:checked").val()=="1")
        	$(".auto_share_post_block_item").show();
        	else $(".auto_share_post_block_item").hide();
        });  

        $(document).on('change','input[name=auto_private_reply]',function(){    
        	if($("input[name=auto_private_reply]:checked").val()=="1")
        	$(".auto_reply_block_item").show();
        	else $(".auto_reply_block_item").hide();
        }); 

         $(document).on('change','input[name=auto_comment]',function(){    
        	if($("input[name=auto_comment]:checked").val()=="1")
        	$(".auto_comment_block_item").show();
        	else $(".auto_comment_block_item").hide();
        }); 


        $(document).on('change','input[name=schedule_type]',function(){    
        	if($("input[name=schedule_type]:checked").val()=="later")
        	{
        		$(".hide_if_now").show();
        		$(".preview_direct_block").hide();
        		$(".preview_only_img_block").show();
        		$("#live_text").html(" plans to go live.");
        		if($("input[name=use_system_video]:checked").val()=="no")
        			$("#create_event_no").attr('disabled','disabled');

        	}
        	else 
        	{
        		$(".hide_if_now").hide();
        		$(".preview_direct_block").show();
        		$(".preview_only_img_block").hide();
        		$("#live_text").html(" is live now.");
        		$("#create_event_no").removeAttr('disabled');
        	}
        	
        }); 

        if($("input[name=use_system_video]:checked").val()=="yes")
    	{
        	$(".system_video").show();
        	$("#create_event_no").removeAttr('disabled');
    	}
    	else 
    	{
    		$(".system_video").hide();
    		if($("input[name=schedule_type]:checked").val()=="later")
    			$("#create_event_no").attr('disabled','disabled');
    	}
    	
        $(document).on('change','input[name=use_system_video]',function(){    
        	if($("input[name=use_system_video]:checked").val()=="yes")
        	{
	        	$(".system_video").show();
	        	$("#create_event_no").removeAttr('disabled');
        	}
        	else 
        	{
        		$(".system_video").hide();
        		if($("input[name=schedule_type]:checked").val()=="later")
        			$("#create_event_no").attr('disabled','disabled');
        	}
        }); 


        $(document).on('change','input[name=create_event]',function(){    
        	if($("input[name=create_event]:checked").val()=="1")
        	$(".hide_if_no").show();
        	else 
        	$(".hide_if_no").hide();
        	
        }); 

     
        var message_pre=$("#message").val();
    	message_pre=htmlspecialchars(message_pre);
		message_pre=message_pre.replace(/[\r\n]/g, "<br />");
    	if(message_pre!="")
    	{
    		message_pre=message_pre+"<br/><br/>";
    		$(".preview_message").html(message_pre);
    	}
    	    
   
        $(document).on('keyup','.emojionearea-editor',function(){  
        	var message=$("#message").val()
        	message=htmlspecialchars(message);
			message=message.replace(/[\r\n]/g, "<br />");
        	if(message!="")
        	{
        		message=message+"<br/><br/>";
        		$(".preview_message").html(message);
        		$(".demo_preview").hide();
        	}
        }); 
       

        $(document).on('blur','#image_url',function(){ 
	        var link=$("#image_url").val();   	                   
            if(link!="") $(".schedule_image_preview").attr("src",link);    
            else 
            {
            	var default_pic=$(".post_to:checked").attr("picture");
            	$(".schedule_image_preview").attr("src",default_pic);  
            } 	         
        });

        $(document).on('click','.video_format',function(){    
        	$("#video_format").modal();        	
        }); 

        $(document).on('click','.thumbnail_format',function(){    
        	$("#thumbnail_format").modal();        	
        }); 

        // put the selected page values to preview section
        var picture=$("#post_to option:selected").attr("picture");       	 
        var display_name=$("#post_to option:selected").attr("display_name");   
        $(".schedule_page_preview_name,.preview_page").html(display_name);  
       
        if($("#image_url").val()=="")       	 
        $(".schedule_image_preview").attr("src",picture); 
        else $(".schedule_image_preview").attr("src",$("#image_url").val()); 
        $(".preview_cover_img").attr("src",picture); 

        var go_live_page_id = $("#post_to").val();
        var page_group_user = go_live_page_id.split("-");
        if(page_group_user[0] == 'page')
        {
	        var page_table_id = page_group_user[1];
	        $.ajax({
	            type:'POST' ,
	            url:"<?php echo site_url();?>vidcasterlive/get_crosspostallowed_pages",
	            data:{page_table_id:page_table_id},
	            success:function(response){
	            	$("#crosspost_this_post_by_pages").html(response);	               
	            }
	        });
        }
        // end of put selected page values to preview section

        $(document).on('change','#post_to',function(){ 
	        var picture=$("option:selected",this).attr("picture");       	 
	        var display_name=$("option:selected",this).attr("display_name");   
	        $(".schedule_page_preview_name,.preview_page").html(display_name);  
	       
	        if($("#image_url").val()=="")       	 
	        $(".schedule_image_preview").attr("src",picture); 
	        else $(".schedule_image_preview").attr("src",$("#image_url").val()); 

	        $(".preview_cover_img").attr("src",picture); 

	        var go_live_page_id = $("#post_to").val();
	        var page_group_user = go_live_page_id.split("-");
	        if(page_group_user[0] == 'page')
	        {
		        var page_table_id = page_group_user[1];
		        $.ajax({
		            type:'POST' ,
		            url:"<?php echo site_url();?>vidcasterlive/get_crosspostallowed_pages",
		            data:{page_table_id:page_table_id},
		            success:function(response){
		            	$("#crosspost_this_post_by_pages").html(response);	               
		            }
		        });
	        }
        });

        var image_upload_limit = "<?php echo $image_upload_limit; ?>";
        $("#image_url_upload").uploadFile({
	        url:base_url+"vidcasterlive/upload_image_only",
	        fileName:"myfile",
	        maxFileSize:image_upload_limit*1024*1024,
	        showPreview:false,
	        returnType: "json",
	        dragDrop: true,
	        showDelete: true,
	        multiple:false,
	        maxFileCount:1, 
	        acceptFiles:".png,.jpg,.jpeg",
	        deleteCallback: function (data, pd) {
	            var delete_url="<?php echo site_url('vidcasterlive/delete_uploaded_file');?>";
                $.post(delete_url, {op: "delete",name: data},
                    function (resp,textStatus, jqXHR) {                         
                    });
	           
	         },
	         onSuccess:function(files,data,xhr,pd)
	           {
	               var data_modified = base_url+"upload_caster/scheduler/"+data;
	               $("#image_url").val(data_modified);	
	               $(".schedule_image_preview").attr("src",data_modified);	
	           }
	    });

        function display_alert(message)
        {
        	swal('<?php echo $this->lang->line("Warning"); ?>', message, 'warning');
        }

	    $(document.body).on('click','#submit_post',function(){ 
  			
  			var scheduler_name = $("#scheduler_name").val();
           	if(scheduler_name=="")
           	{
           		display_alert("<?php echo $this->lang->line('Please provide a campaign name.');?>");
	        	return;
           	}

           	var scheduled_video_url = $("#scheduled_video_url").val();
           	if($("input[name=use_system_video]:checked").val()=="yes")
           	{
	           	if(scheduled_video_url=="")
	           	{
	           		display_alert("<?php echo $this->lang->line('Please upload a video.');?>");
		        	return;
	           	}
           	}

        	var schedule_type= $("input[name=schedule_type]:checked").val();
           	var schedule_time = $("#schedule_time").val();
        	var time_zone = $("#time_zone").val();
        		
        	if(schedule_type=='later')
        	{
        		if(schedule_time=="")
	        	{
	        		display_alert("<?php echo $this->lang->line("Please select planned time to go live.");?>");
	        		return;
	        	}

	        	if(time_zone=="")
	        	{
	        		display_alert("<?php echo $this->lang->line("Please select a time zone.");?>");
	        		return;
        		}
        	}
        	

        	var post_to = $("input[name=post_to]:checked").val();
        	
        	if(post_to=="")
        	{
        		display_alert("<?php echo $this->lang->line("Please select page/group to go live.");?>");
        		return;
        	}

        	var share_or_cross = $("input[name=share_or_cross]:checked").val();

        	if(share_or_cross == 'crossposting')
        	{
	        	var crosspost_enable = $("input[name=crosspost_enable_disable]:checked").val();
	        	var crosspost_this_post_by_pages = $("#crosspost_this_post_by_pages").val();
	        	if(crosspost_enable=='1' && crosspost_this_post_by_pages.length==0)
	        	{
	        		display_alert('<?php echo $this->lang->line("Please select pages for crossposting.");?>');
	        		return;
	        	}
        	}
        	else if(share_or_cross == 'auto_share')
        	{
	        	var auto_share_post = $("input[name=auto_share_post]:checked").val();
	        	var auto_share_this_post_by_pages = $("#auto_share_this_post_by_pages").val();
	        	if(auto_share_post=='1' && auto_share_this_post_by_pages.length==0)
	        	{
	        		display_alert('<?php echo $this->lang->line("Please select pages for auto sharing.");?>');
	        		return;
	        	}
        	}

        	var auto_private_reply = $("input[name=auto_private_reply]:checked").val();
        	var auto_private_reply_text = $("#auto_private_reply_text").val();
        	if(auto_private_reply=='1' && auto_private_reply_text=="")
        	{
        		display_alert("<?php echo $this->lang->line("Please type private reply message.");?>");
        		return;
        	}

        	var auto_comment = $("input[name=auto_comment]:checked").val();
        	var auto_comment_text = $("#auto_comment_text").val();
        	if(auto_comment=='1' && auto_comment_text=="")
        	{
        		display_alert("<?php echo $this->lang->line("Please type auto comment message.");?>");
        		return;
        	}

        	$(this).addClass('btn-progress');

        	  var queryString = new FormData($("#live_scheduler_form")[0]);
		      $.ajax({
		       context: this,
		       type:'POST' ,
		       url: base_url+"vidcasterlive/add_live_scheduler_action",
		       data: queryString,
		       dataType : 'JSON',
		       // async: false,
		       cache: false,
		       contentType: false,
		       processData: false,
		       success:function(response){  
        		 $(this).removeClass('btn-progress');

		         var report_link="<br/><a href='"+base_url+"vidcasterlive/live_scheduler_list'>"+'<?php echo $this->lang->line("Go to List");?>'+"</a>";
		         var redirect_url=base_url+"vidcasterlive/live_scheduler_list";
		         
		         if(response.status=="1")
		         {
		         	var span = document.createElement("span");
		         	var display_content = response.message+report_link;
		         	span.innerHTML = display_content;
		         	swal({ title:'<?php echo $this->lang->line("Success"); ?>', content:span, icon:'success'}).then((value) => {
                              window.location.replace(redirect_url);
                            });
		         }
		         else
		         {
		         	var span = document.createElement("span");
		         	span.innerHTML = response.message;
		         	swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span, icon:'error'});
		         }

		       },
		       error:function(response){
		          var span = document.createElement("span");
		          span.innerHTML = response.responseText;
		          swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span, icon:'error'});
		        }

		      });

        });

	    var video_upload_limit = "<?php echo $video_upload_limit; ?>";
	    $("#live_video_upload").uploadFile({
	        url:base_url+"vidcasterlive/upload_live_video",
	        fileName:"myfile",
	        maxFileSize:video_upload_limit*1024*1024,
	        showPreview:false,
	        returnType: "json",
	        dragDrop: true,
	        showDelete: true,
	        multiple:false,
	        maxFileCount:1, 
	         acceptFiles:".avi,.divx,.flv,.mkv,.mov,.mp4,.mpeg,.mpeg4,.mpg,.wmv",
	        deleteCallback: function (data, pd) {
	            var delete_url="<?php echo site_url('vidcasterlive/delete_uploaded_live_file');?>";
                $.post(delete_url, {op: "delete",name: data},
                    function (resp,textStatus, jqXHR) {                         
                    });
	           
	         },
	         onSuccess:function(files,data,xhr,pd)
	           {
	               $(".preview_video_block").val(data);	
	           }
	    });




    });



</script>

<div class="modal fade" id="share_crosspost_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fas fa-retweet"></i> <?php echo $this->lang->line("Share/Crossposting");?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>

			<div class="modal-body">  
				<div class="section">                
					<div class="alert alert-info alert-has-icon">
                      <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                      <div class="alert-body">
                        <div class="alert-title"><?php echo $this->lang->line('Info'); ?></div>
                        <?php echo $this->lang->line('This feature only works for Page post. It will not work if you go live to your any Groups.'); ?>
                      </div>
                    </div>
				</div>

				<div class="section">                
					<h2 class="section-title"><?php echo $this->lang->line('Share'); ?></h2>
					<p><?php echo $this->lang->line("Live video share means, other pages will share the actual broadcast video link to their site. Visitor can see from which page the actual broadcasting is happening. "); ?></p>
				</div>

				<div class="section">                
					<h2 class="section-title"><?php echo $this->lang->line('Crossposting'); ?></h2>
					<p><?php echo $this->lang->line("Crossposting refers to streaming live broadcast to multiple Facebook pages without uploading to each pages or sharing the original live video.")."<br/><br/>".$this->lang->line("You will need setup one page for live streaming, and other page will crosspost the same live video as their page name. No one can determine in which page, the actual live broadcasting happening. Each page will broadcast the live video with its own comments & reactions from each page.")."<br/><br/>".$this->lang->line("You will also find details about Facebook Live Crossposting here")." <a target='_blank' href='https://www.facebook.com/business/help/1385580858214929?id=1123223941353904'>https://www.facebook.com/business/help/1385580858214929?id=1123223941353904</a>"; ?></p>
				</div>

			</div>

			<div class="modal-footer">
				<a class="btn btn-outline-secondary float-right" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close") ?></a>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="response_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><i class="fa fa-flag"></i> <?php echo $this->lang->line("Live Campaign Status"); ?></h4>
			</div>
			<div class="modal-body">
				<div class="alert text-center" id="response_modal_content">
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-live-video-library"  data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-youtube"></i> <?php echo $this->lang->line("Video Library"); ?></h4>
      </div>
      <div class="modal-body">
        
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modal-play"  data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-film"></i> <?php echo $this->lang->line("Image/Video Preview"); ?></h4>
      </div>
      <div class="modal-body">
        
      </div>
    </div>
  </div>
</div>



<style type="text/css" media="screen">
	.box-header{border-bottom:1px solid #ccc !important;margin-bottom:15px;}
	.box-primary{border:1px solid #ccc !important;}
	.box-footer{border-top:1px solid #ccc !important;}
	.padding-5{padding:5px;}
	.padding-20{padding:20px;}
	.box-header{color:#3C8DBC;}
	.preview
	{
		font-family: helvetica,​arial,​sans-serif;
		padding: 20px;
	}
	.preview_cover_img
	{
		width:45px;
		height:45px;
		border: .5px solid #ccc;
	}
	.preview_page
	{
		padding-left: 7px;
		color: #365899;
		font-weight: 700;
		font-size: 14px;
		cursor: pointer;
	}
	.preview_page_sm
	{
		padding-left: 7px;
		padding-top: 7px;
		color: #9197a3;
		font-size: 13px;
		font-weight: 300;
		cursor: pointer;
	}
	.preview_img
	{
		width:100%;
		border: 1px solid #ccc;
		border-bottom: none;
		cursor: pointer;
	}
	.only_preview_img
	{
		width:100%;
		border: 1px solid #ccc;
		cursor: pointer;
	}		
	
	.ms-choice span
	{
		padding-top: 2px !important;
	}
	.hidden
	{
		display: none;
	}
	.preview_only_img_block
	{
		width: 100%;
		height: 300px;
		background: url('<?php echo base_url("assets/images/demo_live.jpg");?>') no-repeat;
   		background-size: cover;
	}
	.btn-default:hover{background: transparent !important;border-color: #fff}
	.box-primary
	{
		-webkit-box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
		-moz-box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
		box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
	}
	.content-wrapper{background: #fff;}
	.ajax-upload-dragdrop{width:100% !important;}
	.well{
		border-radius: 0;
	}
	label{
		color: orange;
	}
	.table-responsive label{
		color:#000;
	}
	.table-responsive label:hover{
		cursor: pointer;
	}
</style>
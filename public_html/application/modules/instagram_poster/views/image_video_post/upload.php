<div id="image_block" class="mb-4">
	<div class="form-group mb-1">
	  <div class="input-group">
	    <div class="input-group-prepend">
	      <div class="input-group-text">
	        <?php echo $this->lang->line('Paste URL');?>		                            
	      </div>
	    </div>
	    <input class="form-control" name="image_url_pasted" id="image_url_pasted" type="text" value="">

	    <textarea class="form-control w-100 d-none" name="image_url" id="image_url">
	    	<?php if(set_value('image_url')) echo set_value('image_url');else {if(isset($all_data[0]['image_url'])) echo $all_data[0]['image_url'];}?>
	    </textarea>

	      <div class="input-group-text p-0">
	        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#video_format_info_modal">
	          <?php echo $this->lang->line('IG Media Manual'); ?>
	        </button>	                            
	      </div>

				
	  </div>
  </div>
	<div class="form-group mb-1">
    <div id="image_url_upload"><?php echo $this->lang->line('Upload');?></div>
	</div>

	<div class="card mr-1 mb-0 no_shadow d_none" id="image_edit_block">
    <div class="card-footer px-2 py-2 bg-primary no_radius">
      <a href="#" data-src="" class="btn btn-light btn-sm float-left edit_media image no_radius"><i class="fas fa-images"></i> <?php echo $this->lang->line("Editor");?></a>
      <a href="#" data-src="" class="btn btn-danger btn-sm float-right delete_media image no_radius"><i class="fas fa-trash-alt"></i> <?php echo $this->lang->line("Delete");?></a>
    </div>
  </div>

	<div class="col-12">
		<div class="row upload_block"> 
			 <?php
			 if(set_value('image_url')) $current_media = explode(',', set_value('image_url'));
       else if(isset($all_data[0]['image_url'])) $current_media = explode(',', $all_data[0]['image_url']);
       else $current_media = [];
			 $i=count($current_media);
			 $all_image_url  = $current_media;

       foreach ($current_media as $key => $src)
			 {
			 	echo '
			 	<div class="col-4 col-md-3 col-lg-2 p-0 no-gutters">
			 		<img src="'.$src.'" width="100%" height="100" class="pr-1 pb-1 select_media image pointer active">
			 	</div>';
			 }

			 foreach ($files as $key => $value)
			 {
			 	$src_exp = explode('upload_caster', $value["file"]);
			 	$src = isset($src_exp[1]) ? $src_exp[1] : '';
			 	$src = base_url("upload_caster".$src);
			 	if(in_array($src, $current_media)) continue;
			 	$rest = strtolower(substr($src, -4));
			 	if($rest!='.jpg' && $rest!='jpeg' && $rest!='.png') continue;
			 	echo '
			 	<div class="col-4 col-md-3 col-lg-2 p-0 no-gutters">
			 		<img src="'.$src.'" width="100%" height="100" class="pr-1 pb-1 select_media image pointer">
			 	</div>';
			 	array_push($all_image_url, $src);
			 	$i++;
			 	if($i==24) break;
			 }
			 ?>
       <div class="col-12 p-0 no-gutters">
       	<div class="alert alert-warning py-2"><?php echo $this->lang->line("Uploading multiple images will only post the first image chosen in case of Instagram. You can post up to 5 images to Facebook.")?></div> 
       </div>
		</div>
	</div>
</div>

<div id="video_block" class="mb-4">
	<div class="row">
		<div class="col-12">
			<div class="form-group mb-1">
        <div class="input-group">
          <div class="input-group-prepend">
            <div class="input-group-text">
              <?php echo $this->lang->line('Paste URL');?>				                            
            </div>
          </div>
          <input class="form-control" name="video_url" id="video_url" type="text" value="<?php if(set_value('video_url')) echo set_value('video_url');else {if(isset($all_data[0]['video_url'])) echo $all_data[0]['video_url'];}?>">
        </div>
      </div>
			<div class="form-group mb-1">
            <div id="video_url_upload"><?php echo $this->lang->line('Upload');?></div>
      </div>          			

      <div class="card mr-1 mb-0 no_shadow d_none" id="video_edit_block">
        <div class="card-footer px-2 py-2 bg-primary no_radius">
        	 <a href="#" data-src="" class="btn btn-light btn-sm float-left edit_media video disabled no_radius"><i class="fas fa-images"></i> <?php echo $this->lang->line("Editor");?></a>
          <a href="#" data-src="" class="btn btn-danger btn-sm float-right delete_media video no_radius"><i class="fas fa-trash-alt"></i> <?php echo $this->lang->line("Delete");?></a>
        </div>
      </div>

      <div class="col-12">
      	<div class="row upload_block nicescroll"> 
      		 <?php
		 			 if(set_value('video_url')) $current_media = explode(',', set_value('video_url'));
		       else if(isset($all_data[0]['video_url'])) $current_media = explode(',', $all_data[0]['video_url']);
		       else $current_media = [];
		 			 $i=count($current_media);
		 			 $all_video_url  = $current_media;

		       foreach ($current_media as $key => $src)
		 			 {
		 			 	echo '
		 			 	<div class="col-4 col-md-2 col-lg-2 p-0 no-gutters">
      		 		<video src="'.$src.'"  width="100%" height="100" class="pr-1 pb-1 select_media video pointer border active"></video>
      		 	</div>';
		 			 }

      		 foreach ($files as $key => $value)
      		 {
      		 	$src_exp = explode('upload_caster', $value["file"]);
      		 	$src = isset($src_exp[1]) ? $src_exp[1] : '';
      		 	$src = base_url("upload_caster".$src);
      		 	if(in_array($src, $current_media)) continue;
      		 	$rest = strtolower(substr($src, -4));
      		 	if($rest!='.mov' && $rest!='.mp4') continue;
      		 	$active  = $current_media==$src ? 'active' : '';
      		 	echo '
      		 	<div class="col-4 col-md-2 col-lg-2 p-0 no-gutters">
      		 		<video src="'.$src.'"  width="100%" height="100" class="pr-1 pb-1 select_media video pointer border'.$active.'"></video>
      		 	</div>';
      		 	array_push($all_video_url, $src);
      		 	$i++;
      		 	if($i==24) break;
      		 }
      		 ?>
      	</div>
      </div>
		</div>
	</div>	
</div>
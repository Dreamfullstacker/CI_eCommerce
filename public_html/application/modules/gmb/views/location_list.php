<?php 
	$this->load->view("include/upload_js"); 		
?>


<style type="text/css">
	.button-outline
	{
	  background: #fff;
	  border: .5px dashed #ccc;
	}
	.button-outline:hover
	{
	  border: 1px dashed #6777EF !important;
	  cursor: pointer;
	}
	.multi_layout{margin:0;background: #fff}
	.multi_layout .card{margin-bottom:0;border-radius: 0;}
	.multi_layout p, .multi_layout ul:not(.list-unstyled), .multi_layout ol{line-height: 15px;}
	.multi_layout .list-group li{padding: 15px 10px 12px 25px;}
	.multi_layout{border:.5px solid #dee2e6;}
	.multi_layout .collef,.multi_layout .colmid,.multi_layout .colrig,.multi_layout .colend{padding-left: 0px; padding-right: 0px;}
	.multi_layout .collef,.multi_layout .colmid{border-right: .5px solid #dee2e6;}
	.multi_layout .main_card{min-height: 500px;box-shadow: none;}
	.multi_layout .collef .makeScroll{max-height: 700px;overflow:auto;}
	.multi_layout .colrig .makeScroll{max-height: 700px;overflow:auto;}
	/*.multi_layout .colend .makeScroll{max-height: 500px;overflow:auto;}*/
	.multi_layout .list-group .list-group-item{border-radius: 0;border:.5px solid #dee2e6;border-left:none;border-right:none;cursor: pointer;z-index: 0;}
	.multi_layout .list-group .list-group-item:first-child{border-top:none;}
	.multi_layout .list-group .list-group-item:last-child{border-bottom:none;}
	.multi_layout .list-group .list-group-item.active{border:.5px solid #6777EF;}
	.multi_layout .mCSB_inside > .mCSB_container{margin-right: 0;}
	.multi_layout .card-statistic-1{border-radius: 0;}
	.multi_layout h6.page_name{font-size: 14px;}
	.multi_layout .card .card-header input{max-width: 100% !important;}
	.multi_layout .media-title{font-size: 13px;}
	.multi_layout .media-body{padding-left: 15px;}
	.multi_layout .media-body .small{font-size: 10px;color:#000;margin-top:12px;}
	.multi_layout .summary .summary-item{margin-top: 0;}
	.multi_layout .card-primary{margin-top: 35px;margin-bottom: 15px;}
	.multi_layout .product-details .product-name{font-size: 12px;}
	.multi_layout .set_cam_by_post:after {content: none !important;}
	.multi_layout .colrig .media {padding-bottom: 0;}
	.multi_layout .list-unstyled-border li {border-bottom: none;}
	.multi_layout .colmid .card-body {padding: 12px 10px;}
	.multi_layout .colrig .card-body {padding: 12px 20px;}

	.multi_layout .waiting,.modal_waiting {height: 100%;width:100%;display: table;}
    .multi_layout .waiting i,.modal_waiting i{font-size:60px;display: table-cell; vertical-align: middle;padding:30px 0;}

    .multi_layout .card .card-header h4 a{font-weight: 700 !important;}
    
    ::placeholder {
      color: #ccc !important;
    }
    .smallspace{padding: 10px 0;}
    .lead_first_name,.lead_last_name,.lead_tag_name{background: #fff !important;}
    .ajax-file-upload-statusbar{width: 100% !important;}
    hr{
       margin-top: 10px;
    }

    .custom-top-margin{
      margin-top: 20px;
    }

    .sync_page_style{
       margin-top: 8px;
    }
    /* .wrapper,.content-wrapper{background: #fafafa !important;} */
    .well{background: #fff;}
    
    .emojionearea, .emojionearea.form-control
    {
    	height: 140px !important;
    }


    .emojionearea.small-height
    {
    	height: 140px !important;
    }

</style>

<section class="section">
	<div class="section-header">
		<h1><i class="fas fa-map-marked-alt"></i> <?php echo $this->lang->line("Location Information");?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><?php echo $this->lang->line("Location Manager");?></div>
		</div>
	  </div>
</section>


<?php if(empty($location_info))
{ ?>
	 
<div class="card" id="nodata">
  <div class="card-body">
    <div class="empty-state">
      <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image">
      <h2 class="mt-0"><?php echo $this->lang->line("We could not find any data.") ?></h2>
      <a href="<?php echo base_url('social_accounts/index'); ?>" class="btn btn-outline-primary mt-4"><i class="fa fa-cloud-download-alt"></i> <?php echo $this->lang->line("Import Account");?></a>
    </div>
  </div>
</div>

<?php 
}
else
{ ?>
	<div class="row multi_layout">

		<div class="col-12 col-md-5 col-lg-3 collef">
		  <div class="card main_card">
		    <div class="card-header">
		      <div class="col-6 padding-0">
		        <h4><i class="fas fa-map-marked-alt"></i> <?php echo $this->lang->line("Location list"); ?></h4>
		      </div>
		      <div class="col-6 padding-0">            
		        <input type="text" class="form-control float-right" id="search_location_list" onkeyup="search_in_ul(this,'location_list_ul')" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>">
		      </div>
		    </div>
		    <div class="card-body padding-0">
		      <div class="makeScroll">
		      	<ul class="list-group" id="location_list_ul">
		      	  <?php 
		      	  	$i=0; 
		      	  	foreach($location_info as $value) { 
		      	  		$profile_photo = $value['profile_google_url'];
		      	  		if($profile_photo == '') $profile_photo = base_url('assets/img/product-4-50.png');
		      	  ?> 
		      	    <li class="list-group-item location_list_item <?php if($i==0) echo 'active'; ?>" location_table_id="<?php echo $value['id']; ?>">
		      	      <div class="row">
		      	        <div class="col-3 col-md-2"><img width="45px" class="rounded-circle" src="<?php echo $profile_photo; ?>"></div>
		      	        <div class="col-9 col-md-10">
		      	          <h6 class="location_name"><?php echo $value['location_display_name']; ?></h6>
		      	          <span class="">
		      	          	<?php 
		      	          		$address_info = json_decode($value['address'],true); 
		      	          		echo isset($address_info['postalCode']) ? $address_info['postalCode'] : "";
		      	          		echo ", ";
		      	          		echo isset($address_info['locality']) ? $address_info['locality'] : "";
		      	          	?>
		      	          		
		      	          </span>
		      	          </div>
		      	        </div>
		      	    </li> 
		      	    <?php $i++; } ?>                
		      	</ul>
		      </div>
		    </div>
		  </div>          
		</div>

		<div class="col-12 col-md-7 col-lg-3 colmid" id="middle_column">

		</div>

		<div class="col-12 col-md-12 col-lg-6 colend" id="right_column">

			<div class="text-center waiting">
			  <i class="fas fa-spinner fa-spin blue text-center"></i>
			</div>

			<div class="card main_card">
			  <div class="card-header padding-left-10 padding-right-10">
			    <div class="col-6 padding-0">
			      <h4 id="right_column_title"></h4>            
			    </div>
			    <div class="col-6 padding-0">
			      <a href="#" data-toggle="dropdown" class="btn btn-outline-primary dropdown-toggle float-right"><?php echo $this->lang->line("Options");?></a> 
			      <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right" style="width: 250px;">
			        <div class="dropdown-title"><?php echo $this->lang->line("Actions");?></div>
			        <li><a class="dropdown-item has-icon new_review_url" style="cursor: pointer;"><i class="fas fa-code"></i> <?php echo $this->lang->line("New review URL");?></a></li>
			        <li><a class="dropdown-item has-icon location_insight" href="#"><i class="fas fa-chart-line"></i> <?php echo $this->lang->line("Location insights");?></a></li>
			        
			     </ul>
			    </div>
			  </div>

			  <div class="card-body" style="padding: 10px 17px 10px 10px;">
			    <div class="row">
			      <div class="col-12">

			        <div id="right_column_content">              
			          <iframe src="" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>
			        </div>

			      </div>
			    </div>
			  </div>
			</div>
		</div>

<?php } ?>



<script>
	
	$(document).ready(function() {

		$(document).ready(function(){
			$(".location_list_item.active").click();
		});

		$(document).on('click', '.location_list_item', function(event) {
			event.preventDefault();

			var waiting_div_content = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>';
			$("#middle_column").html(waiting_div_content);
			
			$('#right_column .waiting').show();
			$('#right_column .main_card').hide();
			
			/* add active class */
			$(".location_list_item").removeClass('active');
			$(this).addClass('active');

			var location_table_id = $(this).attr('location_table_id');

			$.ajax({
				url: '<?php echo base_url('gmb/get_location_details'); ?>',
				type: 'POST',
				dataType: 'json',
				data: {location_table_id: location_table_id},
				success: function(response) {
					$("#middle_column").html(response.middle_column_content);
					$('#right_column .waiting').hide();
					$('#right_column .main_card').show();
					$('#review_reply_settings').click();
					$(".location_insight").attr('href',response.location_insight_url);
				}
			});
			
		});

		$(document).on('click','.iframed',function(e){
		  e.preventDefault();
		  $(".middle_col_item").removeClass('active');
		  $(this).parent().parent().addClass('active');
		  var iframe_url = $(this).attr('href');
		  var iframe_height = $(this).attr('iframe-height');
		  $("#right_column_content iframe").attr('src',iframe_url).show();
		  $("#right_column_bottom_content").hide();
		  $("#right_column_content iframe").attr('height',iframe_height);
		  $("#right_column .main_card").show();
		  $('#right_column .waiting').hide();

		  var title='';
		  if($(this).hasClass('dropdown-item')) title = $(this).html();
		  else 
		  {
		    title = $(this).parents('.card-condensed').children('.card-icon').html();
		    title += $(this).parents('.card-condensed').children('.card-body').children('h4').html();
		  }
		  $("#right_column_title").html(title);
		  
		});

		$(document).on('click','.new_review_url',function(e){
			e.preventDefault();
			var waiting_div_content = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>';
			$("#new_review_url_content").html(waiting_div_content);
			$("#new_review_url_modal").modal();
			$.ajax({
				url: '<?php echo base_url('gmb/get_new_review_url'); ?>',
				type: 'POST',
				success: function(response) {
					$("#new_review_url_content").html(response);
				}
			});

		});


	});

</script>

<style type="text/css">.ajax-upload-dragdrop{width:100% !important;}</style>

<!-- Modal -->
<div class="modal fade" id="new_review_url_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-code"></i> <?php echo $this->lang->line('New review URL'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="new_review_url_content">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
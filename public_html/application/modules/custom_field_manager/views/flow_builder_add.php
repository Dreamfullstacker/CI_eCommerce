<style type="text/css" media="screen">
	.first_row
	{
		margin-bottom: 5px !important;
	}
	.right_column_button
	{
	    background-color: #EBF4FA;
	    padding: 10px;
	    color: black;
	    border-radius: 5px;
	    cursor: pointer;
	    margin-bottom: 5px;
	}

	.waiting_reply_content {
	    padding: 10px;
	    text-align: center;
	    color: #bbb;
	}

	#left_header 
	{
	    padding: 10px;
	    text-align: center;
	    border-radius: 5px;
	}
	.waiting_reply_content hr, .waiting_reply_content p{
	    display: inline-block;
	    vertical-align: middle; 
	}
	.appended_icon { cursor: pointer; }
	.popover-header {width: 350px;}

	.custom_items {
		border: dashed 0.5px #aaa;
		display: inline-block;
		text-align: center;
		padding: 5px 10px;
		border-radius: 10px;
		margin: 5px;
		cursor:pointer;
	}

	.custom_items .custom_item_icon i {
		font-size: 14px;
	}

	.custom_items .custom_item_info {
		font-size: 13px;
	}

	.custom_items.active {
		background: var(--blue);
		border:0;
		color: #ffffff;
	}

	.select2-dropdown {
	   z-index: 9001;
	}

	.free_input_label
	{
		border: 1px dashed #ccc;
		padding: 5px 15px;
		border-radius: 15px 20px 0px 15px;
	}

	.edit_input_parent_card {
		margin: 0 0px 0 20px;
		box-shadow: 0 3px 10px 5px #bbb6b6;
		background:#f7f7f7;
	}


	.edit_input_parent_card:before {
		content: '\f0d9';
		font-family: 'Font Awesome 5 Free';
		font-weight: 900;
		font-size: 40px;
		position: absolute;
		left: -13px;
		top: -10px;
		color: #f7f7f7;
	}

	.multiple_input_more_parent {
		align-items: center;
		justify-content: center;
	}

	.multiple_input_more {
		width:22% !important;
		border-radius: 20px !important;
		border: dashed 0.5px #aaa;
		height: 30px !important;
	    font-size: 12px !important;
	    margin-right: 20px;
	    text-align: center;
	}

	.multiple_input_more:last-child {
		margin-right:0;
	}

	.right_column {
		position: -webkit-sticky;
		position: sticky;
		top: 0;
		width: 100%;
		height: 100%;
		z-index: 99;
	}

	@media (min-width: 768px) and (max-width: 1024px) {
	  
	  div.single {
	  	display: block !important;
	  }

	  .input_section,.edit_input_section {
	  	width:100% !important;
	  }

	  .edit_input_parent_card:before {
	  	content: '';
	  }

	  .multiple_input_more {
	  	width:100% !important;
	  	border-radius: 20px !important;
	  	border: dashed 0.5px #aaa;
	  	height: 30px !important;
	  	font-size: 12px !important;
	  	margin-right:0;
	  }
  
	}

	@media (max-width:480px) {

		div.single {
			display: block !important;
		}

		.input_section,.edit_input_section {
			width:100% !important;
		}

		.edit_input_parent_card:before {
			content: '';
		}

		.multiple_input_more {
			width:100% !important;
			border-radius: 20px !important;
			border: dashed 0.5px #aaa;
			height: 30px !important;
			font-size: 12px !important;
			margin-right:0;
		}

	}

	.variables { cursor: pointer; }

</style>

<form action="#" enctype="multipart/form-data" id="flowbuilder_form">
	<input type="hidden" id="media_type" name="media_type" value="<?php echo $media_type; ?>">
	<div class="row d-none">
		<div class="col-12">
			<h4 class="full_width">
				<a class="float-right icon-left text-primary btn btn-outline-primary variables"><i class="fas fa-plus"></i> <?php echo $this->lang->line('Variables'); ?></a>
			</h4>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="row">
				<div class="col-12 col-md-6">
					<div class="form-group">
						<label for=""><?php echo $this->lang->line("Campaign Name");?> </label>
						<input name="Campaign_name" id="Campaign_name" value="" class="form-control" type="text">
					</div>
				</div>
				<div class="col-12 col-md-6 d-none">
					<div class="form-group">
						<label for=""><?php echo $this->lang->line("Choose a page");?> </label>
						<?php 
							$page_list['']=$this->lang->line('Select a Page');
							echo form_dropdown('page_table_id',$page_list,$page_id,'class="form-control select2" id="page_table_id"'); 
						?>
					</div>
				</div>
			</div>

		   	<div class="row">
         		
             	<div class="col-12 col-sm-12 col-md-4 right_column">
             		<div class="right_column_button">
             			<i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Free Keyboard Input'); ?>
             		</div>

             		<div class="custom_links text-center">
             			<?php foreach($reply_types as $key=>$value) {  ?>
             				<div class="custom_items add_question" id="keyboard_input" reply_type="<?php echo $key; ?>">
             					<div class="custom_item_icon">
             						<span><i class="ml-0 <?php echo $value; ?>"></i></span>
             					</div>
             					<div class="custom_item_info"><i class="fas fa-plus"></i> <?php echo $this->lang->line($key); ?>
             				</div>
             			</div>
             			<?php } ?>
             		</div>

                 	<div class="right_column_button add_question btn btn-light w-100 border-0 text-left" id="multiple_choice" data-toggle="tooltip" title="Click to Add">
                 		<i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Multiple Choice'); ?>
                 	</div>
             	</div>

 	    		<div class="col-12 col-sm-12 col-md-8 left_column">
 	    			<div id="left_header" class="mb-2 alert alert-light">
 	    				<?php echo $this->lang->line('User Input Flow Start'); ?>
 	    			</div>
 	    			<div class="total_question_container">
 	    				
 	    			</div>

 					<div class="form-group postback_div" style="display: none;">
 						<label for=""><?php echo $this->lang->line("Select final reply template");?> </label>
 						<select class="form-control select2" id="postback_id" name="postback_id" style="width: 100%;">
 							<option value=""><?php echo $this->lang->line('Please select a page first'); ?></option>
 						</select>

 						<a href="" class="add_template float-left" page_id_add_postback=""><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add");?></a>
 						<a href="" class="ref_template float-right" page_id_ref_postback=""><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
 					</div>

 	        	</div>
           </div>
		</div>
	</div>

	<div class="row pt-4">
		<div class="col-12">
			<button class="btn btn-lg btn-primary" id="submit_flowbuilder" name="submit_flowbuilder" type="button"><i class="fas fa-submit"></i> <?php echo $this->lang->line("Submit");?></button>
			<button class="btn btn-lg btn-light float-right" onclick="goBack('custom_field_manager/campaign_list')" type="button"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel");?></button>
		</div>
	</div>

</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

<script>
	var base_url="<?php echo site_url(); ?>";
	$("document").ready(function(){

		$(".total_question_container").sortable({cancel: '.emojionearea-editor, select ,input, textarea, span, a , i'});


		$('body').on('shown.bs.popover', function (e) {
		    $(".select2").select2();
		});

		$(document).on('select2:select', '.selected_custom_field', function (e) {
		  var tag = e.params.data.id;
		  var type_of = e.params.data.disabled;
		  if (typeof type_of == 'undefined')
		  {
			  var id = $(this).attr('id');
			  var reply_type_id = $(this).attr('reply_type_id');
			  var reply_type = $("#"+reply_type_id).val();
			  $.ajax({
			    context: this,
			    type:'POST',
			    dataType:'JSON',
			    url:"<?php echo site_url();?>custom_field_manager/ajax_custom_field_insert",
			    data:{custom_field_name:tag,selected_reply_type:reply_type},
			    success:function(response){
			    	if(response.status == 'insert')
				    	$("#"+id).html(response.message);
			    }
			  });
		  }
		});

		$(document).on('click','.variables',function(e){
			$("#variable_display_section").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>');
			$('#variable_data_modal').modal();
			var media_type = "<?php echo $media_type; ?>";
			$.ajax({
			  context: this,
			  type:'POST',
			  // dataType:'JSON',
			  url:"<?php echo site_url();?>custom_field_manager/ajax_get_variables",
			  data:{media_type:media_type},
			  success:function(response){
			  	$("#variable_display_section").html(response);
			  }
			});
		});

		$(document).on('click','.add_template',function(e){
		    e.preventDefault();
		    var page_id=$("#page_table_id").val();
		    if(page_id=="")
		    {
		      swal('<?php echo $this->lang->line("Warning!"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'warning');
		      return false;
		    }
		    $("#add_template_modal").modal();
		});

		$(document).on('click','.ref_template',function(e){
		   e.preventDefault();
		   var current_val = $("#postback_id").val();
		   var page_id=$("#page_table_id").val();
		   var media_type="<?php echo $media_type; ?>";
		   if(page_id=="")
		   {
		     swal('<?php echo $this->lang->line("Warning!"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'warning');
		     return false;
		   }
		   
		   var get_postback_url = base_url+"messenger_bot/get_postback";
		   if(media_type == 'ig') {
		   		get_postback_url = base_url+"messenger_bot/get_ig_postback";
		   }

		   $.ajax({
		     type:'POST' ,
		     url: get_postback_url,
		     data: {page_id:page_id},
		     success:function(response){
		       $("#postback_id").html(response).val(current_val);
		     }
		   });
		});

		// getting postback list and making iframe
		$('#add_template_modal').on('shown.bs.modal',function(){ 

		  var rand_time="<?php echo time(); ?>";
		  var media_type="<?php echo $media_type; ?>";
		  var page_id=$("#page_table_id").val();
		  var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?>"+page_id+"/0/"+media_type+"?lev="+rand_time;
		  $(this).find('iframe').attr('src',iframe_link); 

		});  

		$('#add_template_modal').on('hidden.bs.modal', function (e) { 
		   	var page_id=$("#page_table_id").val();
		   	var media_type="<?php echo $media_type; ?>";
		   	if(page_id=="")
		   	{
		    	swal('<?php echo $this->lang->line("Warning!"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'warning');
		     	return false;
		   	}

		   var get_postback_url = base_url+"messenger_bot/get_postback";
		   if(media_type == 'ig') {
		   		get_postback_url = base_url+"messenger_bot/get_ig_postback";
		   }

		   $.ajax({
		     type:'POST' ,
		     url: get_postback_url,
		     data: {page_id:page_id},
		     success:function(response){
		       $("#postback_id").html(response);
		     }
		   });
		});


		
		var question_counter = 0;
		$(document).on('click','.add_question',function(e){
			e.preventDefault();
			var reply_type = ''
			var question_category = '';
			question_counter = question_counter+1;
			var question_type = $(this).attr('id');
			if(question_type == 'keyboard_input')
			{
				reply_type = $(this).attr('reply_type');
				question_category = 'keyboard_input';
			}
			else
				question_category = 'multiple_choice';

			var page_table_id = $("#page_table_id").val();
			var media_type = "<?php echo $media_type; ?>";

			if(page_table_id === '') {
				swal('<?php echo $this->lang->line("Warning!"); ?>', '<?php echo $this->lang->line("Please select a page first"); ?>', 'warning');
				return false;
			}


			$.ajax({
				url: '<?php echo base_url('custom_field_manager/ajax_add_question_content'); ?>',
				type: 'POST',
				dataType: 'json',
				data: {question_counter:question_counter,reply_type:reply_type,question_category:question_category,page_table_id:page_table_id,media_type:media_type},
				success: function(response) {
					$(".total_question_container").append(response.content);
					$(".postback_div").show();
					$(".edit_input_section").css("display","none");
				}
			});
			
		});


		$(document).on('click', '.append_icon', function(event) {
			event.preventDefault();

			var blockDivId = $(this).attr('id');
			$("#block_"+blockDivId).find("#edit_input_section_"+blockDivId).toggle(100);
			$(".edit_input_section").not("#edit_input_section_"+blockDivId).css("display","none");
			
		});

		$(document).on('change', '.selected_reply_type', function(event) {
			event.preventDefault();
			var blockDivId = $(this).attr('div_id');
			var checkbox_div_id = $(this).attr('checkbox_div_id');
			var phone_checkbox_div_id = $(this).attr('phone_checkbox_div_id');
			var block_array = blockDivId.split("_");
			var random_variable = block_array.pop();
			var custom_field_id = "selected_custom_field_"+random_variable;
			var selected_reply_type = $(this).val();

			if(selected_reply_type == 'Email') 
				$("#"+checkbox_div_id).removeClass('d-none');
			else
				$("#"+checkbox_div_id).addClass('d-none');

			if(selected_reply_type == 'Phone') 
				$("#"+phone_checkbox_div_id).removeClass('d-none');
			else
				$("#"+phone_checkbox_div_id).addClass('d-none');
			
			$.ajax({
				url: '<?php echo base_url('custom_field_manager/get_customfield_on_replytype'); ?>',
				type: 'POST',
				dataType: 'json',
				data: {selected_reply_type:selected_reply_type},
				success: function(response) {
					$("#"+custom_field_id).html(response.content);
				}
			});
		});

		$(document).on('change', '#page_table_id', function(event) {
			event.preventDefault();
			var page_table_id = $(this).val();
			var media_type = "<?php echo $media_type; ?>";

			$.ajax({
				url: '<?php echo base_url('custom_field_manager/get_postback_dropdown'); ?>',
				type: 'POST',
				dataType: 'json',
				data: {page_table_id:page_table_id,media_type:media_type},
				success: function(response) {
					$("#postback_id").html(response.content);
				}
			});
		});
		$("#page_table_id").val('<?php echo $page_id;?>').change();

		$(document).on('click', '.add_more_button', function(event) {
			event.preventDefault();
			var blockDivId = $(this).attr('div_id');
			var block_array = blockDivId.split("_");
			var random_variable = block_array.pop();
			var content = '<input type="text" class="form-control mb-2 multiple_input_more" name="multiple_choice['+random_variable+'][]" id="multiple_choice['+random_variable+'][]" placeholder="'+'<?php echo $this->lang->line("Another Option"); ?>'+'">';
			$("#"+blockDivId).append(content);
		});



		$(document).on('click','.delete_single_block',function(e){
			e.preventDefault();
			question_counter = question_counter-1;
			var single_block_div_id = $(this).attr('single_block_div_id');
			var popover_div_id = $(this).attr('popover_id');
			$("#"+popover_div_id).click();
			$("#"+single_block_div_id).remove();
		});


		$(document).on('click', '.custom_items', function(event) {
			event.preventDefault();
			$(".custom_items").removeClass("active");
		    $(this).addClass("active");  
		});
		
		$(document).on('click','#submit_flowbuilder',submit_flowbuilder);
		function submit_flowbuilder()
		{    
   			var valid = true;
   			if($('input.type_questions').length === 0) {
   				swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Please add atleast one question"); ?>', 'warning');
   				return false;
   			}

		  	$('input.type_questions').each(function() {
   			    if (!$(this).val() || $(this).val() === 'undefined' || $(this).val() === null) {
   			    	valid = false;
			    }
	  		})
		  	if (!valid) {
	  			swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Please fill all the questions"); ?>', 'warning');
	  			return false;
		  	}

		  	var campaign_name = $("#Campaign_name").val();
		  	var page_name = $("#page_table_id").val();
		  	var postback = $("#postback_id").val();

		  	if(campaign_name == '') {
		  		swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Campaign Name is required"); ?>', 'warning');	
		  		return false;
		  	}

		  	if(page_name == "") {
				swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Please select a Page"); ?>', 'warning');
				return false;
		  	}

		  	if(postback == "") {
		  		swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Please Select a Postback"); ?>', 'warning');
		  		return false;
		  	}

			
			$('#submit_flowbuilder').addClass('btn-progress');
			
			var queryString = new FormData($("#flowbuilder_form")[0]);
	
			$.ajax({
				type:'POST' ,
				url: base_url+"custom_field_manager/question_submit",
				data: queryString,
				dataType : 'JSON',
				cache: false,
				contentType: false,
				processData: false,
				success:function(response)
				{  
					$("#submit_flowbuilder").removeClass('btn-progress');
					if(response.status=='1') 
					{	
						var assign_url = "<?php echo base_url('custom_field_manager/campaign_list/'.$media_type); ?>";
						swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                              location.assign(assign_url);
                            });
					}
					else swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
				},
		        error:function(response){
		          var span = document.createElement("span");
		          span.innerHTML = response.responseText;
		          swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span, icon:'error'});
		        }

			});

		}

	});
</script>



<div class="modal fade" id="add_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-mega">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title full_width">
        	<i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Template'); ?>
        </h5>
        <button type="button" class="close red" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body"> 
        <iframe src="" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>
      </div>
      <div class="modal-footer">
        <button data-dismiss="modal" type="button" class="btn-lg btn btn-dark"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Close & Refresh List");?></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="variable_data_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-book-reader"></i> <?php echo $this->lang->line("All Variables you currently have"); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body" data-backdrop="static" data-keyboard="false">
                <div class="row">
                    <div class="col-12">
                    	<div class="section">
                    		<div class="section-title"><?php echo $this->lang->line('Variable'); ?></div>
                			<p><?php echo $this->lang->line('After you have saved a response in Custom Field, you can use it as a variable in your message reply to subscriber.'); ?></p>
                    	</div>
                    	<div class="section">
                    		<div class="section-title"><?php echo $this->lang->line('How to use Variable?'); ?></div>
                			<p><?php echo $this->lang->line('To use variable for Custom Field, write the variable surrounding by #  like')."<b> #Custom Field#</b>"; ?></p>
                    	</div>
                    	<div class="section" id="variable_display_section">
                    		<!-- content goes here -->
                    	</div>
                    </div>
                </div>            
            </div>

            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('Close'); ?></button>
            </div>

        </div>
    </div>
</div>

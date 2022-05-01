<style>
	.add_template,.ref_template{font-size: 10px;margin-top:5px}
</style>
<div id="put_script"></div>

	<div class="row">
		<div class="col-12">
				 	<form action="#" enctype="multipart/form-data" id="plugin_form">
					<div class="row">
					  <div class="form-group col-12 col-md-3 d-none">
					    <label>
					       <?php echo $this->lang->line("Select Page"); ?> *
					       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("select page") ?>" data-content='<?php echo $this->lang->line("Select your Facebook page for which you want to generate the plugin.") ?>'><i class='fas fa-info-circle'></i> </a>
					    </label>
					    <?php $page_info['']= $this->lang->line("select page"); ?>
					    <?php echo form_dropdown('page', $page_info,$page_id, 'class="form-control select2" id="page" style="width:100%;"' ); ?>                   
					  </div>  
					  <div class="form-group col-12 col-md-3">
					    <label>
					      <?php echo $this->lang->line("Domain"); ?> *
					       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("domain") ?>" data-content='<?php echo $this->lang->line("Domain where you want to embed this plugin. Domain must have https.") ?>'><i class='fas fa-info-circle'></i> </a>
					    </label>
					    <input type="text" name="domain_name" autocomplete="off" id="domain_name" class="form-control" placeholder="https://example.com">                      
					  </div>
					  <div class="form-group col-12 col-md-3">
					    <label>
					      <?php echo $this->lang->line("Language"); ?> *
					       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("language") ?>" data-content='<?php echo $this->lang->line("plugin will be loaded in this language.") ?>'><i class='fas fa-info-circle'></i> </a>
					    </label>
					    <?php echo form_dropdown('language', $sdk_list,'en_US', 'class="form-control select2" id="language" style="width:100%;"'); ?>                   
					  </div>
					  <div class="form-group col-12 col-md-3" >
					      <label>
					          <?php echo $this->lang->line("CTA button text"); ?>
					           <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("CTA button text") ?>" data-content='<?php echo $this->lang->line("you can choose CTA button text from this CTA list.") ?>'><i class='fa fa-info-circle'></i> </a>
					      </label>
					      <?php $cta_options['']= $this->lang->line("default"); ?>
					      <?php echo form_dropdown('cta_text_option', $cta_options,'', 'class="form-control select2" id="cta_text_option" style="width:100%;"'); ?>                                       
					  </div>  
					</div> 
					<div class="row">
						<div class="form-group col-12 col-md-6">
							<label>
								<?php echo $this->lang->line("Plugin Skin"); ?> *
								<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("plugin skin") ?>" data-content='<?php echo $this->lang->line("light skin is suitable for pages with dark background and dark skin is suitable for pages with light background.") ?>'><i class='fas fa-info-circle'></i> </a>
							</label>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="selectgroup w-100">
										<label class="selectgroup-item">
											<input type="radio" name="skin" value="white" id="white" class="selectgroup-input">
											<span class="selectgroup-button"> <?php echo $this->lang->line("White") ?></span>
										</label>
										<label class="selectgroup-item">
											<input type="radio" name="skin" value="blue" id="blue" class="selectgroup-input" checked>
											<span class="selectgroup-button"> <?php echo $this->lang->line("Blue") ?></span>
										</label>
									</div>
								</div>
							</div>

						</div>  
						<div class="form-group col-12 col-md-6" >
						    <label>
						        <?php echo $this->lang->line("Plugin Size"); ?> *
						         <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("plugin size") ?>" data-content='<?php echo $this->lang->line("overall plugin size.") ?>'><i class='fa fa-info-circle'></i> </a>
						    </label>
						    <div class="selectgroup selectgroup-pills">
						    <?php 
						    $i=0;
						    foreach ($btn_sizes as $key => $value) 
						    {
						        $i++;
						        $checked=$selected='';
						        if($value=='standard') 
						        {
						            $selected='default-label';
						            $checked='checked';
						        }
						        $val_print=$value;
						        if($val_print=="xlarge") $val_print="Extra Large";

						        echo '<label class="selectgroup-item">
								                    <input type="radio" name="btn_size" value="'.$value.'" id="btn_size'.$i.'" '.$checked.' class="selectgroup-input">
								                    <span class="selectgroup-button">'.$this->lang->line($val_print).'</span>
								                  </label>';
						    } 
						    ?> 
						    </div>
						</div>  

					</div> 

					<div class="row">
		            	
		            	<div class="col-12 col-md-6">
		            		<div class="form-group">
		            		  <label class="custom-switch mt-2">

		            		    <input type="checkbox" name="redirect" id="redirect_or_not"  class="custom-switch-input">
		            		    <span class="custom-switch-indicator"></span>
		            		    <span class="custom-switch-description"><?php echo $this->lang->line("Redirect to a webpage on successful OPT-IN") ?></span>
		            		  
		            		  </label>
		            		</div>        				           	
		             	</div> 
     					
 						<div class="form-group col-12 col-md-6 display_messsage_block" >
 						  <label>
 						    <?php echo $this->lang->line("OPT-IN success message in website"); ?>
 						     <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("OPT-IN success message") ?>" data-content='<?php echo $this->lang->line("this message will be displayed after successful OPT-IN.") ?> <?php echo $this->lang->line('Keep it blank if you do not want.');?>'><i class='fa fa-info-circle'></i> </a>
 						  </label>
 						  <textarea class="form-control" placeholder="<?php echo $this->lang->line('Keep it blank if you do not want.');?>" name="button_click_success_message" id="button_click_success_message" style="width: 100%;"><?php echo 'You have been subscribed successfully, thank you.';?></textarea>

 	  					<div class="custom-control custom-checkbox">
 	                        <input type="checkbox" value="1" class="custom-control-input" name="add_button_with_message" id="add_button_with_message">
 	                        <label class="custom-control-label" for="add_button_with_message"><?php echo $this->lang->line("I want to add a button in success message");?></label>
 	                      </div>
 	
 						</div>
     					

     		
					</div> 



					<div class="row display_messsage_block display_button_block">
					  <div class="form-group col-12 col-md-6" >
					    <label>
					      <?php echo $this->lang->line("button text"); ?> *
					       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("button text") ?>" data-content='<?php echo $this->lang->line("This button will be embeded with OPT-IN successful message.") ?>'><i class='fa fa-info-circle'></i> </a>
					    </label>
					    <input type="text" name="success_button" id="success_button" class="form-control" value="Send Message">                     
					  </div>
					  <div class="form-group col-12 col-md-6" >
					    <label>
					      <?php echo $this->lang->line("Button URL"); ?> *
					       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Button URL") ?>" data-content='<?php echo $this->lang->line("Button click action URL") ?>'><i class='fa fa-info-circle'></i> </a>
					    </label>
					    <input type="text" name="success_url" id="success_url" class="form-control" value="">                     
					  </div>
					</div>

					<div class="row display_messsage_block display_button_block">


						<div class="form-group col-12 col-md-3">
						  <label>
						    <?php echo $this->lang->line("Button background"); ?> *
						 
						  </label>
						  <div class="input-group colorpicker-component color-picker-rgb" >
						    <input type="text" class="form-control" name="success_button_bg_color" id="success_button_bg_color" value="#5CB85C">
						    <div class="input-group-append">
						      <div class="input-group-text">
						      	<span class="input-group-addon"><i></i></span>
						      </div>
						    </div>
						  </div>                 
						</div>
						<div class="form-group col-12 col-md-3">
						  <label class="margin-bottom-label">
						    <?php echo $this->lang->line("Button text color"); ?> *
				
						  </label>   
						  <div class="input-group colorpicker-component color-picker-rgb" >
						    <input type="text" class="form-control" name="success_button_color" id="success_button_color" value="#FFFFFF">
						    <div class="input-group-append">
						      <div class="input-group-text">
						      	<span class="input-group-addon"><i></i></span>
						      </div>
						    </div>
						  </div>               
						</div>
						<div class="form-group col-12 col-md-3">
						  <label class="margin-bottom-label">
						    <?php echo $this->lang->line("Button hover background"); ?> *
					
						  </label>
						  <div class="input-group colorpicker-component color-picker-rgb" >
						    <input type="text" class="form-control" name="success_button_bg_color_hover" id="success_button_bg_color_hover" value="#339966">
						    <div class="input-group-append">
						      <div class="input-group-text">
						      	<span class="input-group-addon"><i></i></span>
						      </div>
						    </div>
						  </div>                
						</div>
						<div class="form-group col-12 col-md-3">
						  <label class="margin-bottom-label">
						    <?php echo $this->lang->line("Button text hover color"); ?> *
		
						  </label>
						  <div class="input-group colorpicker-component color-picker-rgb" >
						    <input type="text" class="form-control" name="success_button_color_hover" id="success_button_color_hover" value="#FFFDDD">
						    <div class="input-group-append">
						      <div class="input-group-text">
						      	<span class="input-group-addon"><i></i></span>
						      </div>
						    </div>
						  </div>                 
						</div>            
						
					</div>

					<div class="row redirect_block">
					  <div class="form-group col-12" >
					    <label class="margin-bottom-label">
					      <?php echo $this->lang->line("OPT-IN success redirect URL"); ?> *
					       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("OPT-IN success redirect URL") ?>" data-content='<?php echo $this->lang->line("Visitors will be redirected to this URL after successful OPT-IN.") ?>'><i class='fa fa-info-circle'></i> </a>
					    </label>
					    <input type="text" name="success_redirect_url" id="success_redirect_url" class="form-control" value="">                   
					  </div>
					</div>

					<div class="row">
					  <div class="form-group col-12 <?php if(!$this->is_broadcaster_exist) echo 'col-md-6'; else echo 'col-md-5';?>" >
					    <label>
					      <?php echo $this->lang->line("OPT-IN inbox confirmation message template"); ?> *
					       <a href="#" data-html="true" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("OPT-IN inbox confirmation message template") ?>" data-content='<?php echo $this->lang->line("This content will be sent to messenger inbox on OPT-IN.") ?> <?php echo $this->lang->line("You must select page to fill this list with data."); ?> <?php echo $this->lang->line("You can create template from ").' <a href="'.base_url("messenger_bot/create_new_template").'">'.$this->lang->line("here.")?></a>'><i class='fa fa-info-circle'></i> </a>
					    </label>
					    <?php echo form_dropdown('template_id',array(), '','class="form-control select2" id="template_id" style="width:100%;"'); ?>
					    <a href="" class="add_template float-left" page_id_add_postback=""><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add Template");?></a>
					    <a href="" class="ref_template float-right" page_id_refresh_postback=""><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
					  </div>
					  <div class="form-group col-12 <?php if(!$this->is_broadcaster_exist) echo 'col-md-6'; else echo 'col-md-3';?>" >
					    <label>
					      <?php echo $this->lang->line("reference"); ?> *
					       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("reference") ?>" data-content='<?php echo $this->lang->line("put a unique reference to track this plugin later.") ?>'><i class='fa fa-info-circle'></i> </a>
					    </label>
					    <input type="text" name="reference" id="reference" class="form-control" value="">                 
					  </div>
					  <div class="form-group col-12 col-md-4 <?php if(!$this->is_broadcaster_exist) echo 'hidden';?>" >
					    <label class="d-block">
					      <?php echo $this->lang->line("select label"); ?>
					       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("select label") ?>" data-content='<?php echo $this->lang->line("subscriber obtained from this plugin will be enrolled in these labels.") ?> <?php echo $this->lang->line("You must select page to fill this list with data."); ?>'><i class='fa fa-info-circle'></i> </a>
					       <a class="blue float-right pointer" page_id_for_label="" id="create_label_sendmessenger"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create Label");?></a>
					    </label>
					    <?php echo form_dropdown('label_ids[]',array(), '','style="height:45px;overflow:hidden;width:100%;" multiple="multiple" class="form-control select2" id="label_ids"'); ?>
					  </div>              
					</div>

					<button class="btn btn-lg btn-primary" id="get_button" name="get_button" type="button"><i class="fa fa-code"></i> <?php echo $this->lang->line("Generate Embed code");?></button>

					</form>

		</div>
	</div>


<!-- postback template add modal -->
<div class="modal fade" id="add_template_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Template'); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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




<script>
	var base_url="<?php echo site_url(); ?>";
	$("document").ready(function()	{
	
		$('.color-picker-rgb').colorpicker({
		                 format: 'hex'
		             });
		$('.new_button_block,.redirect_block,.display_button_block,#create_label_sendmessenger,.add_template,.ref_template').css('display','none');

		$(document).on('change', '#redirect_or_not', function(event){

			var r_or_not= document.getElementById("redirect_or_not");

			if(r_or_not.checked == true)
			{
				$('.redirect_block').show(500);
				$('.display_messsage_block').hide(500);
			}
			else
			{
				$('.display_messsage_block').show(500);
				$('.redirect_block').hide(500);
				if($('input[name=add_button_with_message]').prop('checked'))
					$('.display_button_block').show();
				else
					$('.display_button_block').hide();
			}

		});

		$(document).on('click','#add_button_with_message', function(event){

			var btnw_msg = document.getElementById('add_button_with_message');
			if (btnw_msg.checked == true) 
				$('.display_button_block').show(500);
			else
				$('.display_button_block').hide(500);
		
		});

		$(document).on('blur','#domain_name',function(event){
			event.preventDefault();
			var ref=$(this).val();
			ref=ref.replace("http://", "");
			ref=ref.replace("https://", "");
			ref=ref.replace(/ /g, ""); 
			ref=ref.replace(/-/g, "");
			ref=ref.replace(/_/g, "");
			ref=ref.replace(/"/g, "");
			ref=ref.replace(/'/g, "");
			ref=ref.replace(/:/g, "");
			ref=ref.replace(/;/g, "");
			ref=ref.replace(/,/g, "");
			ref=ref.toUpperCase();
			$("#reference").val(ref);

		});

		$(document).on('change','#page',function(event){
			event.preventDefault();

			var page_id=$(this).val();

			 
			  $.ajax({
			  type:'POST' ,
			  url: base_url+"messenger_bot_enhancers/get_template_label_dropdown",
			  data: {page_id:page_id},
			  dataType : 'JSON',
			  success:function(response){
			  	if(page_id == "") {
			  		$("#create_label_sendmessenger,.add_template,.ref_template").css('display','none');
			  		$("#create_label_sendmessenger").attr('page_id_for_label','');
			  		$(".add_template").attr('page_id_add_postback','');
			  		$(".ref_template").attr('page_id_refresh_postback','');
			  	} else {
			  		$("#create_label_sendmessenger,.add_template,.ref_template").css('display','block');
			  		$("#create_label_sendmessenger").attr('page_id_for_label',page_id);
			  		$(".add_template").attr('page_id_add_postback',page_id);
			  		$(".ref_template").attr('page_id_refresh_postback',page_id);
			  	}

			    $("#template_id").html(response.template_option);
			    $("#label_ids").html(response.label_option);
			    $("#put_script").html(response.script);
			    $("#success_url").val(response.mme_link);
			    $("#success_redirect_url").val(response.mme_link);
			  }

			});
		});

		$("#page").val('<?php echo $page_id; ?>').change();

		// create an new label and put inside label list
		$(document).on('click','#create_label_sendmessenger',function(e){
		  e.preventDefault();

		  	var page_id=$(this).attr('page_id_for_label');

	  		swal("<?php echo $this->lang->line('Label Name'); ?>", {
		    	content: "input",
		    	button: {text: "<?php echo $this->lang->line('Create'); ?>"},
		  	})
		  	.then((value) => {
		    	var label_name = `${value}`;
			    if(label_name!="" && label_name!='null')
			    {
		      		$("#save_changes").addClass("btn-progress");
			      	$.ajax({
			        	context: this,
			        	type:'POST',
			        	dataType:'JSON',
			        	url:"<?php echo site_url();?>home/common_create_label_and_assign",
			        	data:{page_id:page_id,label_name:label_name},
			        	success:function(response){

			           		$("#save_changes").removeClass("btn-progress");

			           		if(response.error) {
			              		var span = document.createElement("span");
			              		span.innerHTML = response.error;

				              	swal({
				                	icon: 'error',
				                	title: '<?php echo $this->lang->line('Error'); ?>',
				                	content:span,
				              	});

			           		} else {
			              		var newOption = new Option(response.text, response.id, true, true);
			              		$('#label_ids').append(newOption).trigger('change');
			            	}
			        	}
			      	});
			    }
		  	});
		});


		// ===================== add & refresh postback section ====================

		// getting postback list and making iframe
		$('#add_template_modal').on('shown.bs.modal',function(){ 
			var page_id = $(".add_template").attr("page_id_add_postback");
			var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?>"+page_id;
		  	$(this).find('iframe').attr('src',iframe_link); 
		});
		// getting postback list and making iframe

		// add postback template modal
		$(document).on('click','.add_template',function(e){
		    e.preventDefault();

		    var page_id = $(this).attr("page_id_add_postback");
		    if(page_id=="")
		    {
		    	swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
		      return false;
		    }
		    $("#add_template_modal").modal();
		});

		$(document).on('click','.ref_template',function(e){
			e.preventDefault();
			var current_val = $("#template_id").val();
			var page_id= $(this).attr("page_id_refresh_postback");

			if(page_id=="")
			{
				swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
				return false;
			}

			$.ajax({
				type:'POST' ,
				url: base_url+"home/common_get_postback",
				data: {page_id:page_id},
				success:function(response){
					$("#template_id").html(response).val(current_val);
					$('#template_id').select2({
						width: '100%'
					});
				}
			});
		});

		$('#add_template_modal').on('hidden.bs.modal', function (e) { 
			var current_val = $("#template_id").val();
			var page_id= $(".add_template").attr("page_id_add_postback");
			if(page_id=="")
			{
				swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
				return false;
			}
			$.ajax({
				type:'POST' ,
				url: base_url+"home/common_get_postback",
				data: {page_id:page_id},
				success:function(response){
					$("#template_id").html(response);
					$('#template_id').select2({
						width: '100%'
					});
				}
			});
		});

		// ============================ Add & refresh Postback Section ===============================

		$(document).on('click','#get_button',get_button);
		function get_button()
		{        

			var page = $("#page").val();
			var domain_name = $("#domain_name").val();
			var template_id = $("#template_id").val();
			var reference = $("#reference").val();

			if(page=="")
			{
				swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please select a page.'); ?>", 'error');
				return false;
			}

			if(domain_name=="")
			{
				swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please put your domain name.'); ?>", 'error');
				return false;
			}


			if($("#redirect_or_not").val()=="1")
			{
				if($("#success_redirect_url").val()=='')
				{
					swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('OPT-IN success redirect URL is required.'); ?>", 'error');
					return false;
				}
			}
			else
			{
				if($('input[name=add_button_with_message]').prop('checked'))
				{
					if($("#success_button").val()=='' || $("#success_url").val()=='')
					{
						swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Missing OPT-IN success button parameters.'); ?>", 'error');
						return false;
					}
				}
			}

			if(template_id=='')
			{
				swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please select OPT-IN inbox confirmation message template.'); ?>", 'error');
				return false;
			}

			if(reference=='')
			{
				swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please enter an reference.'); ?>", 'error');
				return false;
			}

		
			$('#get_button').addClass('btn-progress');
			
			var queryString = new FormData($("#plugin_form")[0]);
			var redirect_or_not = document.getElementById("redirect_or_not");
			if(redirect_or_not.checked == true)
				queryString.append('redirect','1');
			else
				queryString.append('redirect','0');
	
			$.ajax({
				type:'POST' ,
				url: base_url+"messenger_bot_enhancers/send_to_messenger_add_action",
				data: queryString,
				dataType : 'JSON',
				// async: false,
				cache: false,
				contentType: false,
				processData: false,
				success:function(response)
				{  
					if(response.status=='1') 
					{
					   
						$("#response").attr('class','alert alert-success text-center').html(response.message);
						$("#get_button").removeClass('btn-progress');
						$("#get_button").attr('disabled',true);
						$(".description").text(response.js_code); 
						Prism.highlightElement($('#test')[0]);           
						$('.js_code_con').removeClass('hidden');
						$("#get_plugin_modal").modal();

						$(".toolbar-item").find('a').addClass('copy');
					}
					else 
					{
					    swal("<?php echo $this->lang->line('Error'); ?>", response.message, 'error');
						$("#get_button").removeClass('btn-progress');
					}

				
					
				}

				});

		}

		$(document).on('click', '.copy', function(event) {
		    event.preventDefault();

		    $(this).html('<?php echo $this->lang->line("Copied!"); ?>');
		    var that = $(this);
		    
		    var text = $(this).parent().parent().parent().find('code').text();
		    var $temp = $("<input>");
		    $("body").append($temp);
		    $temp.val(text).select();
		    document.execCommand("copy");
		    $temp.remove();


		    // iziToast.success({
		    //     title: "",
		    //     message: "<?php echo $this->lang->line('Copied to clipboard') ?>",
		    // });

		    setTimeout(function(){
		      $(that).html('<?php echo $this->lang->line("Copy"); ?>');
		    }, 2000); 

		});


		$('#get_plugin_modal').on('hidden.bs.modal', function () {
		   window.location.href = base_url+"messenger_bot_enhancers/send_to_messenger_list/"+'<?php echo $page_id; ?>'+'/1'
		});

		$(".xscroll1").mCustomScrollbar({
		autoHideScrollbar:true,
		theme:"light-thick",
		axis: "x"
		});


	});
</script>

<div class="modal fade" role="dialog" id="get_plugin_modal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fas fa-code"></i> <?php echo $this->lang->line('Send to Messenger Plugin Embed Code'); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12">
						<div id="response"></div>
						<div class="form-group js_code_con">
							<label> <?php echo $this->lang->line("copy the code below and paste inside the html element of your webpage where you want to display this plugin.")?> </label>

							<pre class="language-javascript" ><code id="test" class="dlanguage-javascript description" ></code></pre>

							
						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line('Close'); ?></button>
			   </button>
		    </div>
	 </div>
   </div>
</div>


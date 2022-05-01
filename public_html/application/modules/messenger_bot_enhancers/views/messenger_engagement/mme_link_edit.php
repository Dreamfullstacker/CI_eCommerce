<style>
	.add_template,.ref_template{font-size: 10px;margin-top:5px}
</style>
<div id="put_script"></div>

	<div class="row">
		<div class="col-12">
				 	<form action="#" enctype="multipart/form-data" id="plugin_form">
				 		<input type="hidden" name="hidden_id" id="hidden_id" value="<?php echo $xdata['id'];?>">
					<div class="row">
					  <div class="form-group col-12 col-md-6 d-none">
					    <label>
					       <?php echo $this->lang->line("Select Page"); ?> *
					       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("select page") ?>" data-content='<?php echo $this->lang->line("Select your Facebook page for which you want to generate the plugin.") ?>'><i class='fas fa-info-circle'></i> </a>
					    </label>
					    <?php $page_info['']= $this->lang->line("Select Page"); ?>
					    <?php echo form_dropdown('page', $page_info,'', 'class="form-control select2" id="page" style="width:100%;"' ); ?>                   
					  </div>  
					  <div class="form-group col-12 col-md-6">
					      <label>
					          <?php echo $this->lang->line("button text"); ?> *
					           <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("button text") ?>" data-content='<?php echo $this->lang->line("System will create a new button that will take visitors to m.me link") ?>'><i class='fa fa-info-circle'></i> </a>
					      </label>
					      <input type="text" name="new_button_display" id="new_button_display" class="form-control" value="Send us Message">                                          
					  </div>


					</div>

					<div class="row">
						


						<div class="form-group col-12 col-md-3">
						  <label>
						    <?php echo $this->lang->line("Button background"); ?> *
						 	<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("button background") ?>" data-content='<?php echo $this->lang->line("new button background color") ?>'><i class='fa fa-info-circle'></i> </a>
						  </label>
						  <div class="input-group colorpicker-component color-picker-rgb" >
						    <input type="text" class="form-control" name="new_button_bg_color" id="new_button_bg_color">
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
							<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("button background") ?>" data-content='<?php echo $this->lang->line("new button text color") ?>'><i class='fa fa-info-circle'></i> </a>
						  </label>   
						  <div class="input-group colorpicker-component color-picker-rgb" >
						    <input type="text" class="form-control" name="new_button_color" id="new_button_color" value="#FFFFFF">
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
							<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("button background") ?>" data-content='<?php echo $this->lang->line("New button background color on mouse over") ?>'><i class='fa fa-info-circle'></i> </a>
						  </label>
						  <div class="input-group colorpicker-component color-picker-rgb" >
						    <input type="text" class="form-control" name="new_button_bg_color_hover" id="new_button_bg_color_hover" value="#367FA9">
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
							<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("button background") ?>" data-content='<?php echo $this->lang->line("New button text color on mouse over") ?>'><i class='fa fa-info-circle'></i> </a>
						  </label>
						  <div class="input-group colorpicker-component color-picker-rgb" >
						    <input type="text" class="form-control" name="new_button_color_hover" id="new_button_color_hover" value="#FFFDDD">
						    <div class="input-group-append">
						      <div class="input-group-text">
						      	<span class="input-group-addon"><i></i></span>
						      </div>
						    </div>
						  </div>                 
						</div>            
					</div> 
					<div class="row">
						<div class="form-group col-12 col-md-6">
						    <label>
						        <?php echo $this->lang->line("Button Size"); ?> *
						         <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("plugin size") ?>" data-content='<?php echo $this->lang->line("choose how big you want the button to be.") ?>'><i class='fa fa-info-circle'></i> </a>
						    </label>
						    <div class="selectgroup selectgroup-pills">

						    <?php 
						    $i=0;
						    foreach ($btn_sizes as $key => $value) 
						    {
						        $i++;
						        $checked=$selected='';
						        if($value=='medium') 
						        {
						            $selected='default-label';
						            $checked='checked';
						        }
						        $val_print=$value;
						        if($val_print=="xlarge") $val_print="Extra Large";

						        echo '<label class="selectgroup-item"><input class="selectgroup-input" type="radio" name="btn_size" value="'.$value.'" id="btn_size'.$i.'" '.$checked.'> 
						        		<span class="selectgroup-button">'.$this->lang->line($val_print).'</span> 
						        	</label>';
						    } 
						    ?> 
						    </div>
						</div>  

					</div> 

					<div class="row">
					  <div class="form-group col-12 <?php if(!$this->is_broadcaster_exist) echo 'col-md-6'; else echo 'col-md-5';?>">
					    <label>
					      <?php echo $this->lang->line("OPT-IN inbox confirmation message template"); ?> *
					       <a href="#" data-html="true" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("OPT-IN inbox confirmation message template") ?>" data-content='<?php echo $this->lang->line("This content will be sent to messenger inbox on OPT-IN.") ?> <?php echo $this->lang->line("You must select page to fill this list with data."); ?> <?php echo $this->lang->line("You can create template from ").' <a href="'.base_url("messenger_bot/create_new_template").'">'.$this->lang->line("here.")?></a>'><i class='fa fa-info-circle'></i> </a>
					    </label>
					    <?php echo form_dropdown('template_id',array(), '','class="form-control select2" id="template_id" style="width:100%;"'); ?>
					    <a href="" class="add_template float-left" page_id_add_postback="<?php echo $xdata['page_id'];?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add Template");?></a>
					    <a href="" class="ref_template float-right" page_id_refresh_postback="<?php echo $xdata['page_id'];?>"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
					  </div>
					  <div class="form-group col-12 <?php if(!$this->is_broadcaster_exist) echo 'col-md-6'; else echo 'col-md-3';?>">
					    <label>
					      <?php echo $this->lang->line("reference"); ?> *
					       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("reference") ?>" data-content='<?php echo $this->lang->line("put a unique reference to track this plugin later.") ?>'><i class='fa fa-info-circle'></i> </a>
					    </label>
					    <input type="text" name="reference" id="reference" class="form-control" value="">                 
					  </div>
					  <div class="form-group col-12 col-md-4 <?php if(!$this->is_broadcaster_exist) echo 'hidden';?>">
					    <label class="d-block">
					      <?php echo $this->lang->line("select label"); ?>
					       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("select label") ?>" data-content='<?php echo $this->lang->line("subscriber obtained from this plugin will be enrolled in these labels.") ?> <?php echo $this->lang->line("You must select page to fill this list with data."); ?>'><i class='fa fa-info-circle'></i> </a>
					       <a class="blue float-right pointer" page_id_for_label="<?php echo $xdata["page_id"];?>" id="create_label_me_link"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create Label");?></a>
					    </label>
					    <?php echo form_dropdown('label_ids[]',array(), '','style="height:45px;overflow:hidden;width:100%;" multiple="multiple" class="form-control select2" id="label_ids"'); ?>
					  </div>              
					</div>

					<button class="btn btn-lg btn-primary" id="get_button" name="get_button" type="button"><i class="fas fa-save"></i> <?php echo $this->lang->line("Update Plugin");?></button>
					<a href="<?php echo base_url('messenger_bot_enhancers/mme_link_list/'.$xdata['page_id'].'/1'); ?>" class="btn btn-lg btn-secondary float-right" ><i class="fa fa-times"></i> <?php echo $this->lang->line("Cancel"); ?></a>

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




		$(document).on('change','#page',function(){     
		    var page_id=$(this).val();
		    var id=$("#hidden_id").val();
		    var table_name="messenger_bot_engagement_mme";
		      $.ajax({
		      type:'POST' ,
		      url: base_url+"messenger_bot_enhancers/get_template_label_dropdown_edit",
		      data: {page_id:page_id,id:id,table_name:table_name},
		      dataType : 'JSON',
		      success:function(response){

		      	if(page_id == "") {
		      		$("#create_label_me_link,.add_template,.ref_template").css('display','none');
		      		$("#create_label_me_link").attr('page_id_for_label','');
		      		$(".add_template").attr('page_id_add_postback','');
		      		$(".ref_template").attr('page_id_refresh_postback','');
		      	} else {
		      		$("#create_label_me_link,.add_template,.ref_template").css('display','block');
		      		$("#create_label_me_link").attr('page_id_for_label',page_id);
		      		$(".add_template").attr('page_id_add_postback',page_id);
		      		$(".ref_template").attr('page_id_refresh_postback',page_id);
		      	}

		        $("#template_id").html(response.template_option);
		        $("#label_ids").html(response.label_option);
		        $("#put_script").html(response.script);
		      }

		    });
		});


		// create an new label and put inside label list
		$(document).on('click','#create_label_me_link',function(e){
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

	
			$.ajax({
				type:'POST' ,
				url: base_url+"messenger_bot_enhancers/mme_link_edit_action",
				data: queryString,
				//dataType : 'JSON',
				// async: false,
				cache: false,
				contentType: false,
				processData: false,
				success:function(response)
				{  
					if(response=='1') 
					{
					   
						swal("<?php echo $this->lang->line('Updated Successfully'); ?>", "<?php echo $this->lang->line('Plugin has been updated successfully.') ?>", 'success').then(function() {
							    window.location = base_url+"messenger_bot_enhancers/mme_link_list/"+page+'/1';
							});
						$("#get_button").removeClass('btn-progress');
					}
					else 
					{
					    swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Something went wrong') ?>", 'error');
						$("#get_button").removeClass('btn-progress');
					}


				
					
				}

				});

		}

		$("#page").val('<?php echo $xdata["page_id"];?>').attr('disabled','disabled').change(); 
		$("#new_button_display").val('<?php echo $xdata["new_button_display"];?>');
		$("#new_button_bg_color").val('<?php echo $xdata["new_button_bg_color"];?>').change();
		$("#new_button_color").val('<?php echo $xdata["new_button_color"];?>').change();
		$("#new_button_bg_color_hover").val('<?php echo $xdata["new_button_bg_color_hover"];?>').change();
		$("#new_button_color_hover").val('<?php echo $xdata["new_button_color_hover"];?>').change();
		$("#reference").val('<?php echo $xdata["reference"];?>').attr('disabled','disabled');
		
		var btn_size='<?php echo $xdata["btn_size"];?>';
		$('input[type="radio"][name="btn_size"][value="'+btn_size+'"]').attr('checked','checked');


	});
</script>




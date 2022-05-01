<style>
	.add_template,.ref_template{font-size: 10px;margin-top:5px}
</style>
<section class="section section_custom">
	<div class="section-header">
		<h1><?php echo $this->lang->line("Edit Webview Form"); ?></h1>
		
		<div class="section-header-breadcrumb">
		  <div class="breadcrumb-item active"><a href="<?php echo base_url('messenger_bot/index'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a></div>
		  <div class="breadcrumb-item active"><a href="<?php echo base_url('messenger_bot_connectivity/webview_builder_manager'); ?>"><?php echo $this->lang->line("Webview manager"); ?></a></div>
		  <div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>
	<div class="section-body">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo $this->lang->line("Form Name"); ?></label>&nbsp;<a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('This is actually for identifying form name in our system') ?>"><i class="fa fa-info-circle"></i></a>
							<input id="form-name" type="text" name="form-name" class="form-control" value="<?= set_value('form-name', $form_name, true) ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo $this->lang->line('Form Title'); ?></label>&nbsp;<a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('The form title that will be shown on top of your form') ?>"><i class="fa fa-info-circle"></i></a>
							<input id="form-title" type="text" name="form-title" class="form-control" value="<?= set_value('form-title', $form_title, true) ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo $this->lang->line("Select Page"); ?></label>
							<select name="select-page" id="select-page" class="form-control">
								<option value=""></option>
								<?php foreach ($pages as $page): ?>
									<option value="<?= $page['id'] ?>" <?= ($page['id'] == $page_id) ? 'selected' : '' ?>><?= $page['page_name'] ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div id="assign-label-wrapper" class="form-group">
							<label class="d-block"><?php echo $this->lang->line("Assign Label"); ?>
								<a class="blue float-right pointer" page_id_for_label="<?php echo $page_id; ?>" id="create_label_webview"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create Label");?></a>
							</label>
							<div id="select-assign-label">
								
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div id="reply-template-wrapper" class="form-group">
							<label><?php echo $this->lang->line("Reply Template"); ?></label>
							<div id="select-reply-template"></div>
							<a href="" class="add_template float-left" page_id_add_postback="<?php echo $page_id; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add Template");?></a>
							<a href="" class="ref_template float-right" page_id_refresh_postback="<?php echo $page_id; ?>"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
						</div>			
					</div>
					<div class="col-md-12">
						<div id="webview-builder"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

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

<script src="<?= base_url('plugins/formbuilder/form-builder.min.js') ?>"></script>
<script>

	var page_id = '<?php echo $page_id; ?>',
		label_id = '<?php echo $label_id; ?>',
		template_id = '<?php echo $template_id; ?>',
		form_data = <?php echo $form_data; ?>,
		uri_canonical_id = '<?php echo $uri_canonical_id; ?>',
		real_canonical_id = '<?php echo $real_canonical_id; ?>',
		assign_label = $('#assign-label-wrapper'),
		reply_template = $('#reply-template-wrapper');

	$(document).ready(function() {
		var select_page = $('#select-page');
		var base_url="<?php echo site_url(); ?>";
		$(select_page).select2({
			width: '100%',
			placeholder: '<?php echo $this->lang->line('Select page') ?>'
		});

		// Fills select options initially
		make_ajax_call(page_id);

		// Options for form builder
	    var options = {
	    	// Defaults form data
	    	defaultFields: form_data,

			// Makes fields to be used for one time only
			allowOneTimeFields: ['button'],

			// Disables fields
			disableFields: ['autocomplete', 'file', 'hidden', 'number', 'paragraph', 'button'],

			// Decides whether controls should be draggable or not
			draggableControls: true,

			// Disables action button
			disabledActionButtons: ['data'], // save, data, clear

	      	// event to be used when saving data 
	      	onSave: function(e, formData) {
	      		e.preventDefault()

		        // Starts loading state
		        e.target.classList.remove('disabled', 'btn-progress')
		        e.target.classList.add('disabled', 'btn-progress')

	        	// Prepares data
	        	var parsed_form_data = JSON.parse(formData)

		        // Shows error if button field doesn't exist
		        if (Array.isArray(parsed_form_data)) {
			         var found = parsed_form_data.find((val) => {
			            if (val && val.hasOwnProperty('type')) {
			              	return val.type === 'button'
			            }
			        });

		          	if (! found) {
		           		swal('<?= $this->lang->line('Warning!') ?>', '<?= $this->lang->line('You forgot to choose a button field') ?>', 'warning')
		            	return;
		          	}
		        }

		        // Prepares form data to be submitted
		        var form_data = { 
					user_id: '<?= md5($user_id) ?>', 
					form_name: $('#form-name').val(), 
					form_title: $('#form-title').val(), 
					page_id: $('#select-page').val(),
					assign_label: $('#assign-label').val() ? $('#assign-label').val().join() : '',
					reply_template: $('#reply-template').val(),
					uri_canonical_id: uri_canonical_id,
					real_canonical_id: real_canonical_id,
					form_data: formData
		        }

	        	$.ajax({
					type: 'POST',
					url: '<?= base_url('messenger_bot_connectivity/save_form_data') ?>',
					dataType: 'JSON',
					data: form_data || null,
					success: function (response) {
		            	if (response) {
		              		if (response.success === true) {
			                	// Shows success message
								swal({
									title: '<?php echo $this->lang->line('Success!'); ?>', 
									text: response.message, 
									icon: 'success'
								})

				                // Changes loading state
				                e.target.classList.remove('disabled', 'btn-progress')

								// Empties fields
								if (parsed_form_data.length) {
									// Redirects to webview manager
									setTimeout(function() {
										window.location.replace('<?= base_url('messenger_bot_connectivity/webview_builder_manager') ?>')
									}, 2000)
								}	
		              		} else if (response.error === true) {
				                // Shows error message
				                swal({ 
									title: '<?php echo $this->lang->line('Error!'); ?>', 
									text: response.message, 
									icon: 'error'
				                })

			                	// Changes loading state
			                	e.target.classList.remove('disabled', 'btn-progress')              
		              		}
			            }
			        },
			        error: function (xhr, status, error) {
			            console.log('xhr: ', xhr)
			            console.log('status: ', status)

			            // Shows HTTP status error
			            swal({
			            	title: '<?php echo $this->lang->line('Error!'); ?>', 
			              	text: error, 
			              	icon: 'error'
			            })
			        }
	        	});
	      	},
	    }

	    // Inits form builder
    	$('#webview-builder').formBuilder(options);


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
    		var current_val = $("#reply-template").val();
    		var page_id= $(this).attr("page_id_refresh_postback");
    		var str = '';

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
    				str +='<select class="form-control" id="reply-template" name="reply-template" style="width:100%;">'+response+'</select>';
    				$("#select-reply-template").html(str);
    				$("#reply-template").trigger('change').val(current_val);
    				$('#reply-template').select2({
    					width: '100%'
    				});
    			}
    		});
    	});

    	$('#add_template_modal').on('hidden.bs.modal', function (e) { 
    		var current_val = $("#reply-template").val();
    		var page_id= $(".add_template").attr("page_id_add_postback");
    		var str = '';
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
    				str +='<select class="form-control" id="reply-template" name="reply-template" style="width:100%;">'+response+'</select>';
    				$("#select-reply-template").html(str);
    				$('#reply-template').select2({
    					width: '100%'
    				});
    			}
    		});
    	});

    	// ============================ Add & refresh Postback Section ===============================
	});

	// Hides select boxes primarily
	// $(assign_label).add(reply_template).hide();

	$(document).on('change', '#select-page', function() {
		var page_id = $(this).val();

		// push table id for label create
		$("#create_label_webview").attr('page_id_for_label',page_id);

		// Hides select boxes primarily
		$(assign_label).add(reply_template).hide();

		make_ajax_call(page_id);
	});

	// create an new label and put inside label list
	$(document).on('click','#create_label_webview',function(e){
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
	              $('#assign-label').append(newOption).trigger('change');
	            }
	        }
	      });
	    }
	  });

	});

	function make_ajax_call(page_id) {
		$.ajax({
			type: 'POST',
			dataType: 'JSON',
			data: { page_id },
			url: '<?= site_url() ?>messenger_bot_connectivity/handle_select_boxes_data',
			success: function(data) {
				if (data.assign_labels && Array.isArray(data.assign_labels)) {
					var select_assign_labels = generate_select_box(
						data.assign_labels, 
						'assign-label',
						label_id, 
						true
					);
					$('#select-assign-label').html('');
					$('#select-assign-label').html(select_assign_labels);
					$('#assign-label').select2({
						width: '100%',
						placeholder: '<?php echo $this->lang->line('Select label') ?>'
					});
					$(assign_label).show();
				}

				if (data.reply_template && Array.isArray(data.reply_template)) {
					var select_reply_template = generate_select_box(
						data.reply_template, 
						'reply-template',
						template_id
					);
					$('#select-reply-template').html('');
					$('#select-reply-template').html(select_reply_template);
					$('#reply-template').select2({
						width: '100%',
						placeholder: '<?php echo $this->lang->line('Select template') ?>'
					});
					$(reply_template).show();

					// push page_id into add & refresh postback button
					$(".add_template").attr("page_id_add_postback",page_id);
					$(".ref_template").attr("page_id_refresh_postback",page_id);
				}
			}
		});
	}

	function generate_select_box(
		options_array, 
		name_attribute,
		selected_id,
		multiple = false
	) {
		var multi_select = multiple ? 'multiple' : '';
		var str = '';
		str += '<select name="' + name_attribute + '" id="' + name_attribute + '" ' + multi_select + '>';
		str += '<option value=""></option>';
		var ids = selected_id.split(',');

		if (Array.isArray(options_array)) {
			options_array.forEach(option => {
				var found = ids.find(function(item) {
					return item === option.value;
				});

				var selected = found ? 'selected' : '';
				str += '<option value="'+ option.value +'" ' + selected + '>'+ option.text +'</option>';
			});
		}

		str += '</select>';

		return str;
	}	



	
</script>
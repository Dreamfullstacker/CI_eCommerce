<section class="section section_custom">
	<div class="section-header">
		<h1><?php echo $this->lang->line("Webview Manager"); ?></h1>
		<div class="section-header-button">
	     	<a class="btn btn-primary" href="<?= base_url('messenger_bot_connectivity') ?>">
	        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Create New Form'); ?></a>
	    </div>

	    <div class="section-header-breadcrumb">
	      <div class="breadcrumb-item active"><a href="<?php echo base_url('messenger_bot/index'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a></div>
	      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
	    </div>

	</div>
	<div class="section-body">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body data-card">
						<div class="table-responsive">
							<table id="webview-datatable" class="table table-bordered" style="width:100%">
						        <thead>
						            <tr>
						                <th>#</th>
						                <th><?php echo $this->lang->line("Title"); ?></th>
						                <th><?php echo $this->lang->line("Page Name"); ?></th>
						                <th><?php echo $this->lang->line("Created At"); ?></th>
						                <th><?php echo $this->lang->line("Total Form Submitted"); ?></th>
						                <th><?php echo $this->lang->line("Last Form Submitted"); ?></th>
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
</section>

<style>
	.card {box-shadow: none !important;}
	.data-div {margin-left: 45px;}
	.margin-top {margin-top: 30px;}
	.flex-column .nav-item .nav-link.active
	{
	  background: #fff !important;
	  color: #3516df !important;
	  border: 1px solid #988be1 !important;
	}

	.flex-column .nav-item .nav-link .form_id, .flex-column .nav-item .nav-link .insert_date
	{
	  color: #608683 !important;
	  font-size: 12px !important;
	  padding: 0 !important;
	  margin: 0 !important;
	}
	.waiting {height: 100%;width:100%;display: table;}
    .waiting i{font-size:60px;display: table-cell; vertical-align: middle;padding:30px 0;}
</style>

<div class="modal fade" id="detail-webview-form-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding:30px;">
            	<h5 class="modal-title"><i class="fas fa-info-circle"></i> <?php echo $this->lang->line("Form Details"); ?></h5>
              	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">×</span>
              	</button>
            </div>

            <div class="modal-body" id="subscriber_actions_modal_body" data-backdrop="static" data-keyboard="false">
                <div class="row">
                	<div class="col-12">
                		<div class="card">
                			<div class="card-header">
                				<h4 id="detail-title"><?php echo $this->lang->line("Title"); ?></h4>
                			</div>
                			<div class="card-body">
                				<div class="row">
                					<div class="col-12 col-md-6">
                						<div class="section">
                							<div class="section-title"><?php echo $this->lang->line("Page Name"); ?></div>
                							<div id="detail-page-name" class="data-div"></div>
                						</div>
                					</div>
									<div class="col-12 col-md-6">
                						<div class="section">
                							<div class="section-title"><?php echo $this->lang->line("Created At"); ?></div>
                							<div id="detail-created-at" class="data-div"></div>
                						</div>
                					</div>
                				</div>

                				<div class="row">
                					<div class="col-12 col-md-6">
                						<div class="section">
                							<div class="section-title"><?php echo $this->lang->line("Form ID"); ?></div>
                							<div id="detail-form-id" class="data-div"></div>
                						</div>
                					</div>
									<div class="col-12 col-md-6">
                						<div class="section">
                							<div class="section-title"><?php echo $this->lang->line("Labels"); ?></div>
                							<div id="detail-assign-label" class="badges data-div"></div>
                						</div>
                					</div>
                				</div>

                				<div class="row">
                					<div class="col-12 col-md-6">
                						<div class="section">
                							<div class="section-title"><?php echo $this->lang->line("Postback ID"); ?></div>
                							<div id="detail-postback-id" class="data-div"></div>
                						</div>
                					</div>
                				</div>

                			</div>
                		</div>
                	</div>
                </div>    

                <div class="row">
                	<div class="col-12">
                		<div class="card">
			    			<div class="row">
				    			<div class="col-12 margin-top">
							  		<div class="card-body pb-0">
							  			<input type="text" id="searching" name="searching" class="form-control" placeholder="<?php echo $this->lang->line("Search..."); ?>" style='width:200px;'>                                          
							  		</div>
				    			</div>
			                	<div class="col-12">
			                		<div class="card-body data-card">                			
				                		<div class="table-responsive2">
				                			<input type="hidden" id="put_form_id">
				                			<table class="table table-bordered" id="mytable1">
				                				<thead>
				                					<tr>
				                						<th>#</th>
				                						<th><?php echo $this->lang->line("Avatar"); ?></th> 
				                						<th><?php echo $this->lang->line("First Name"); ?></th>  
				                						<th><?php echo $this->lang->line("Last Name"); ?></th>  
				                						<th><?php echo $this->lang->line("Subscriber ID"); ?></th>  
				                						<th><?php echo $this->lang->line("Submitted At"); ?></th>  
				                						<th><?php echo $this->lang->line("Actions"); ?></th>  
				                					</tr>
				                				</thead>
				                			</table>
				                		</div>
			                		</div>
			                	</div>

			                	<div id="detail-first-view">
			                		<div class="first-view-spinner">
			                			<i class="fa fa-spinner fa-spin fa-2x blue"></i>
			                		</div>	
			                	</div>
			    			</div>
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

<script>
	$(document).ready(function() {
		var data_table = $('#webview-datatable').DataTable({
	      	processing: true,
	      	serverSide: true,
			order: [[ 0, "desc" ]],
			pageLength: 10,	        
	        ajax: {
	        	url: '<?= base_url('messenger_bot_connectivity/webview_manager_data') ?>',
	        	type: 'POST',
	        	dataSrc: function (json) {
	                $(".table-responsive").niceScroll();
	                return json.data;
	            },
	        },
	        columns: [
			    {data: 'id'},
			    {data: 'form_name'},
			    {data: 'page_name'},
			    {data: 'form_created_time'},
			    {data: 'total_form_submit'},
			    {data: 'last_form_submitted_at'},
			    {data: 'actions'}
			],
			language: {
        		url: "<?= base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
  			},
      		columnDefs: [
				{ "sortable": false, "targets": [0, 6] },
				{
				    targets: [0],
				    visible: false
				}
			],
			dom: '<"top"f>rt<"bottom"lip><"clear">',
		})

		// Displays form details
		var table1 = '';
		var perscroll1;
		$(document).on('click', '#detail-webview-form', function(e) {
			e.preventDefault();

			// Grabs form ID
			var form_id = $(this).data('form-id');
			$("#put_form_id").val(form_id);

			var spinner = $('#detail-first-view');

			$(spinner).show();
			$('#detail-webview-form-modal').modal();

			$.ajax({
				type: 'POST',
				dataType: 'JSON',
				url: '<?= base_url('messenger_bot_connectivity/handle_form_details_data') ?>',
				data: {form_id},
				success: function(data) {

					if (true === data.error) {
						swal({
							title: '<?php echo $this->lang->line('Error!'); ?>',
							text: data.message,
							icon: 'error'
						})
						$('#detail-webview-form-modal').modal('toggle')
						return
					}

					if (data) {
						$(spinner).hide();
					}

					if (data.form_title) {
						$('#detail-title').html(data.form_title)
					}

					if (data.page_name) {
						$('#detail-page-name').html(data.page_name)
					}

					if (data.postback_id) {
						$('#detail-postback-id').html(data.postback_id)
					}

					if (data.group_name) {
						if (Array.isArray(data.group_name)) {
							
							var str = '';
							data.group_name.forEach(group => {
								str += '<span class="badge badge-light">' + group + '</span>';
							})

							$('#detail-assign-label').html(str)
						}
					}

					if (data.inserted_at) {
						$('#detail-created-at').html(data.inserted_at)
					}

					if (data.canonical_id) {
						$('#detail-form-id').html(data.canonical_id)
					}
				}
			});

			setTimeout(function(){ 
				if (table1 == '')
				{
				  // $("#put_form_id").val(form_id);
				  var base_url = "<?php echo base_url(); ?>";
				  table1 = $("#mytable1").DataTable({
				      serverSide: true,
				      processing:true,
				      bFilter: false,
				      order: [[ 2, "asc" ]],
				      pageLength: 10,
				      ajax: {
				          url: base_url+'messenger_bot_connectivity/get_submitted_subscribers',
				          type: 'POST',
				          data: function ( d )
				          {
				              d.form_id = $("#put_form_id").val();
				              d.searching = $("#searching").val();
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
				            targets: [0,1,6],
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
			}, 1000);

		});

		$(document).on('keyup', '#searching', function(event) {
		  event.preventDefault(); 
		  table1.draw();
		});


		// Attempts to delete form
		$(document).on('click', '#delete-webview-form', function(e) {
			e.preventDefault()

			// Grabs form ID
			var form_id = $(this).data('form-id')

			swal({
				title: '<?php echo $this->lang->line('Are you sure?'); ?>',
				text: '<?php echo $this->lang->line('Once deleted, you will not be able to recover this form!'); ?>',
				icon: 'warning',
				buttons: true,
				dangerMode: true,
			}).then((yes) => {
				if (yes) {
					$.ajax({
						type: 'POST',
						url: '<?= base_url('messenger_bot_connectivity/delete_form_data') ?>',
						dataType: 'JSON',
						data: { form_id },
						success: function(response) {
							if (response) {
								if (response.success === true) {
									// Reloads datatable
									data_table.ajax.reload()

									// Displays success message
									iziToast.success({title: '',message: response.message,position: 'bottomRight'});
								} else if (response.error === true) {
									// Displays error message
									iziToast.error({title: '',message: response.message,position: 'bottomRight'});
								}	
							}
						},
						error: function(xhr, status, error) {
							// Displays error message
							iziToast.error({title: '',message: error,position: 'bottomRight'});							
						}
					})
				} else {
					return
				}
			})
		});

		$(document).on('click', '.get_subscriber_formdata', function(e){
			e.preventDefault();
			var subscriber_table_id = $(this).attr('data-id');
			var form_id = $(this).attr('data-form-id');
			var page_table_id = $(this).attr('page_table_id');
			var subscribe_id = $(this).attr('subscribe_id');
			$("#get_subscriber_formdata").modal();
			get_subscriber_formdata(subscriber_table_id,subscribe_id,page_table_id,form_id); 
		});

		$('.modal').on("hidden.bs.modal", function (e) { 
		    if ($('.modal:visible').length) { 
		        $('body').addClass('modal-open');
		    }
		});

	})

	function get_subscriber_formdata(id,subscribe_id,page_id,form_id)
	{
	  $("#waiting-div").show();
	  $.ajax({
	    type:'POST' ,
	    url: "<?php echo site_url(); ?>messenger_bot_connectivity/get_subscriber_formdata",
	    data:{id:id,page_id:page_id,subscribe_id:subscribe_id,form_id:form_id},
	    success:function(response)
	    {
	    	$("#waiting-div").hide();
	        $(".formdata_div").html(response);
	    }
	  }); 
	}
</script>

<div class="modal fade" id="get_subscriber_formdata" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" style="min-width: 65%;">
        <div class="modal-content">
            <div class="modal-header" style="padding:30px;">
            	<h5 class="modal-title"><i class="fas fa-info-circle"></i> <?php echo $this->lang->line("All Submitted Form Data"); ?></h5>
              	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">×</span>
              	</button>
            </div>

            <div class="modal-body" data-backdrop="static" data-keyboard="false">
                <div class="row">
					<div class="col-12">
						<div class="row formdata_div"></div>
					</div>
						
                	<!-- <div id="waiting-div">
                		<div class="first-view-spinner text-center" style="margin:">
                			<i class="fa fa-spinner fa-spin fa-2x blue"></i>
                		</div>	
                	</div> -->
            		<div class="text-center waiting" id="waiting-div">
            			<i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i>
            		</div>
                </div>            
            </div>

            <div class="modal-footer bg-whitesmoke br">
            	<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('Close'); ?></button>
            </div>

        </div>
    </div>
</div>
<script>
$(document).ready(function(){
	// Exports form data
	$(document).on('click', '#webview-export-form-data', function(e) {
		e.preventDefault();
		// Makes reference
		var that = this,
		// Gets class of this elment
		prev_el = $(that).parent().prev(),
		prev_el_classes = prev_el ? prev_el[0].className : '',
		new_el_classes = prev_el_classes.replace('-outline', ''),
		// Grabs form ID
		form_id = $(this).data('form-id');
		// Shows spinner
		$(prev_el).removeClass();
		$(prev_el).addClass(new_el_classes.concat(' btn-progress disabled'));
		// Downloads file via ajax call
		$.ajax({
			type: 'POST',
			dataType: 'JSON',
			data: { form_id },
			url: '<?php echo base_url('messenger_bot_connectivity/export_form_data'); ?>',
			success: function(res) {
				// Stops spinner
				$(prev_el).removeClass();
				$(prev_el).addClass(prev_el_classes);
				// Shows error if something goes wrong
				if (res.error) {
					swal({
					  icon: 'error',
					  text: res.error,
					  title: '<?php echo $this->lang->line('Error!'); ?>',
					});
					return;
				}
				if (res.info) {
					swal({
					  icon: 'info',
					  text: res.info,
					  title: '<?php echo $this->lang->line('Info!'); ?>',
					});
					return;
				}				
				// If everything goes well, requests for downloading the file
				if (res.status && 'ok' === res.status) {
					window.location = '<?php echo base_url('messenger_bot_connectivity/export_form_data'); ?>';
				}
			},
			error: function(xhr, status, error) {
				// Stops spinner
				$(prev_el).removeClass();
				$(prev_el).addClass(prev_el_classes);
				// Shows error message
				swal({
					icon: 'error',
					text: error,
					title: '<?php echo $this->lang->line('Error!'); ?>',
				});
			},
		});
	});
});
</script>
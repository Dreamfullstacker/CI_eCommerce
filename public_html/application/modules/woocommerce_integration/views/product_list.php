<section class="section section_custom pt-1">
    
  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card no_shadow">
          <div class="card-body data-card p-0 pt-1 pr-3">
            <div class="row">
              <div class="col-7 col-md-9">
                <?php echo 
                '<div class="input-group mb-3" id="searchbox">
                  <div class="input-group-prepend d-none">
                  <input type="hidden" class="form-control" id="search_store_id" autofocus name="search_store_id" value="'.$config_id.'">
                  </div>
                  <input type="text" class="form-control" id="search_value" autofocus name="search_value" placeholder="'.$this->lang->line("Search...").'" style="max-width:400px;">
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="button" id="search_action"><i class="fas fa-search"></i> <span class="d-none d-sm-inline">'.$this->lang->line("Search").'</span></button>
                  </div>
                </div>'; ?>                                          
              </div>          

              <div class="col-12">             
                <a href="" id="export_to_ecommerce" class="btn btn-lg btn-primary float-right"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Export to Ecommerce"); ?></a>                                 
              </div>
            </div>

            <div class="table-responsive2">
                <input type="hidden" id="put_page_id">
                <table class="table table-bordered" id="mytable">
                  <thead>
                    <tr>
                      <th>#</th>      
                      <th style="vertical-align:middle;width:20px">
                          <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                      </th>
                      <th><?php echo $this->lang->line("Thumb")?></th>                   
                      <th><?php echo $this->lang->line("Product")?></th>                   
                      <th><?php echo $this->lang->line("Price")?></th>                  
                      <th><?php echo $this->lang->line("Actions")?></th>                     
                      <th><?php echo $this->lang->line("Updated at")?></th>                   
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



<script>

	var base_url="<?php echo site_url(); ?>";

	var perscroll;
	var table1 = '';
  var woocommerce_config_id = '<?php echo $config_id;?>';
	table1 = $("#mytable").DataTable({
	  serverSide: true,
	  processing:true,
	  bFilter: false,
	  order: [[ 3, "asc" ]],
	  pageLength: 10,
	  ajax: {
	      url: base_url+'woocommerce_integration/product_list_data',
	      type: 'POST',
	      data: function ( d )
	      {
	          d.search_store_id = $('#search_store_id').val();
	          d.search_value = $('#search_value').val();
	      }
	  },
	  language: 
	  {
	    url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
	  },
	  dom: '<"top"f>rt<"bottom"lip><"clear">',
	  columnDefs: [	   
	    {
	        targets: [2,4,5,6],
	        className: 'text-center'
	    },
	    {
	        targets: [1,2,5],
	        sortable: false
	    }
	  ],
	  fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
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


	$("document").ready(function(){	   

      	$(document).on('keypress', '#search_value', function(e) {
        	if(e.which == 13) $("#search_action").click();
      	});

      	$(document).on('click', '#search_action', function(event) {
        	event.preventDefault(); 
        	table1.draw();
      	});

        $(document).on('click', '#export_to_ecommerce', function(event) {
          event.preventDefault(); 
          var ids = [];
          $(".datatableCheckboxRow:checked").each(function ()
          {
              ids.push(parseInt($(this).val()));
          });
          var selected = ids.length;
          if(selected==0) 
          {
            swal('<?php echo $this->lang->line("Warning") ?>', '<?php echo $this->lang->line("You have not selected any product to export."); ?>', 'warning');
            return;
          }
          if(selected>50) 
          {
            swal('<?php echo $this->lang->line("Warning") ?>', '<?php echo $this->lang->line("You can export maximum 50 products at a time"); ?>', 'warning');
            return;
          }
          $("#export_modal").modal();
        });

        $(document).on('click', '#export_now', function(event) {
          event.preventDefault(); 
          var ids = [];
          $(".datatableCheckboxRow:checked").each(function ()
          {
              ids.push(parseInt($(this).val()));
          });
          var selected = ids.length;
          if(selected==0) return;
          var store_id = $("#store_id").val();
          if(store_id=="")
          {
            swal('<?php echo $this->lang->line("Warning") ?>', '<?php echo $this->lang->line("Please select ecommerce store."); ?>', 'warning');
            return;
          }

          $(this).addClass('btn-progress');

          $.ajax({
            context: this,
            type:'POST' ,
            url:"<?php echo site_url();?>woocommerce_integration/export_product",
            dataType: 'json',
            data:{store_id,ids,woocommerce_config_id},
            success:function(response){ 
              
              $(this).removeClass('btn-progress');

              if(response.status == 1)
              {
                swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {location.reload();});
                
              }
              else
              {
                swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
              }
            }
          });

        });

	});

</script>


<div class="modal fade" role="dialog" id="export_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-file-export"></i> <?php echo $this->lang->line("Export to Ecommerce");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
      </div>
      <div class="modal-body">
        <?php 
        if(empty($store_data)) echo "<div class='alert alert-danger text-center'>".$this->lang->line("No ecommerce store found.")."</div>";
        else
        {
          $store_list[''] = $this->lang->line("Select Ecommerce Store");
          foreach ($store_data as $key => $value) {
            $store_list[$value['id']] = $value['store_name'];
          }
          echo form_dropdown('store_id', $store_list, '',"class='form-control select2' id='store_id' style='width:100%'");
          echo "<br><br><a href='' id='export_now' class='btn btn-primary btn-lg' style='width:200px;'><i class='fas fa-file-export'></i> ".$this->lang->line("Export")."</a>";
        }
        ?>        
      </div>
    </div>
  </div>
</div>


<style type="text/css">
  ins{text-decoration: none;}
</style>

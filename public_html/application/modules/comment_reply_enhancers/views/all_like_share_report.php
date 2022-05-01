<style type="text/css">
   #page_id{width: 120px;}
   @media (max-width: 575.98px) {
     #page_id{width: 100px;}
   }
 </style>
 <!-- new datatable section -->
<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-share-alt"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button"> 
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('comment_automation/comment_growth_tools'); ?>"><?php echo $this->lang->line("Comment Growth Tools"); ?></a></div>
      <div class="breadcrumb-item">
          <a href="<?php echo base_url("comment_automation/comment_section_report"); ?>">
            <?php echo $this->lang->line("Report"); ?>
          </a>
        </div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">

            <div class="input-group mb-3" id="searchbox">
                <input type="text" class="form-control" id="post_id" autofocus placeholder="<?php echo $this->lang->line('Post ID'); ?>" aria-label="" aria-describedby="basic-addon2" style="max-width: 30%">
                <div class="input-group-prepend">
                    <select class="select2 form-control" id="page_id">
                      <option value=""><?php echo $this->lang->line("Page Name"); ?></option>
                        <?php foreach ($page_info as $key => $value): ?>
                          <option value="<?php echo $value['id']; ?>" <?php if($value['id']==$this->session->userdata('selected_global_page_table_id')) echo 'selected'; ?>><?php echo $value['page_name']; ?></option>
                        <?php endforeach ?>
                  </select>
                </div>
                <div class="input-group-append">
                      <button class="btn btn-primary" id="search_submit" type="button"><i class="fas fa-search"></i> <span class="d-none d-sm-inline"><?php echo $this->lang->line('Search'); ?></span></button>
                </div>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered" id="mytable">
                <thead>
                  <tr>
                    <th>#</th>      
                    <th style="vertical-align:middle;width:20px">
                        <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                    </th>
                    <th><?php echo $this->lang->line("id")?></th>
                    <th><?php echo $this->lang->line("Page Name")?></th>
                    <th><?php echo $this->lang->line("Post ID")?></th>
                    <th><?php echo $this->lang->line("Auto Like")?></th>
                    <th><?php echo $this->lang->line("Auto Share")?></th>
                    <th><?php echo $this->lang->line("Actions")?></th>
                    <th><?php echo $this->lang->line("Last Share Try")?></th>
                    <th><?php echo $this->lang->line("Last Like Try")?></th>
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
    
   
    $(document).ready(function() {

      var table = $("#mytable").DataTable({
          serverSide: true,
          processing:true,
          bFilter: false,
          order: [[ 2, "desc" ]],
          pageLength: 10,
          ajax: {
              url: base_url+'comment_reply_enhancers/all_like_share_report_data',
              type: 'POST',
              dataSrc: function ( json ) 
              {
                $(".table-responsive").niceScroll();
                return json.data;
              },
              data: function ( d )
              {
                  d.page_id = $('#page_id').val();
                  d.post_id = $('#post_id').val();
              }
          },          
          language: 
          {
            url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
          },
          dom: '<"top"f>rt<"bottom"lip><"clear">',
          columnDefs: [
            {
                targets: [2],
                visible: false
            },
            {
                targets: '',
                className: 'text-center'
            },
            {
                targets: [0,1,7],
                sortable: false
            }
          ]
      });


      $(document).on('click', '#search_submit', function(event) {
        event.preventDefault(); 
        table.draw();
      });



      $(document).on('click','.view_report',function(){
      	var loading = '<div class="text-center"><img src="'+base_url+'assets/pre-loader/custom_lg.gif" class="center-block"></div>';
      	$("#view_report_modal_body").html(loading);
      	$("#view_report").modal();
      	var table_id = $(this).attr('table_id');
      	$.ajax({
          	type:'POST' ,
          	url: base_url+"comment_reply_enhancers/like_share_details",
          	data: {table_id:table_id},
          	// async: false,
          	success:function(response){
               	$("#view_report_modal_body").html(response);
          	}

          });

      });




    });
  
 
</script>


<div class="modal fade" id="view_report" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg" style="min-width: 80%;">
        <div class="modal-content">

            <div class="modal-header">
              <h5 class="modal-title"><i class="fas fa-list-alt"></i> <?php echo $this->lang->line("Auto Like/Share Report") ?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true" aria-label="Close">
            	<span aria-hidden="true">×</span>
              </button>
            </div>

            <div class="modal-body" id="view_report_modal_body">                

            </div>
        </div>
    </div>
</div>
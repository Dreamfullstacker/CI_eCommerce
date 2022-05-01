<?php 
if(ultraresponse_addon_module_exist())  $commnet_hide_delete_addon = 1;
else $commnet_hide_delete_addon = 0;
?>
<style type="text/css">
   #page_id{width: 120px;}
   @media (max-width: 575.98px) {
     #page_id{width: 100px;}
   }
 </style>
<!-- new datatable section -->
<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-list-alt"></i> <?php echo $page_title; ?></h1>
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
                <div class="input-group-prepend">
                    <select class="select2 form-control" id="page_id">
                      <option value=""><?php echo $this->lang->line("Page"); ?></option>
                        <?php foreach ($page_info as $key => $value): ?>
                          <option value="<?php echo $value['id']; ?>" <?php if($value['id']==$this->session->userdata('selected_global_page_table_id')) echo 'selected'; ?>><?php echo $value['page_name']; ?></option>
                        <?php endforeach ?>
                  </select>
                </div>
                <input type="text" class="form-control" id="post_id" autofocus placeholder="<?php echo $this->lang->line('Post ID'); ?>" aria-label="" aria-describedby="basic-addon2" style="max-width: 30%">
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
                    <th><?php echo $this->lang->line("Private Reply")?></th>
                    <th><?php echo $this->lang->line("Comment Reply")?></th>
                    <th><?php echo $this->lang->line("Comment Hidden")?></th>
                    <th><?php echo $this->lang->line("Comment Deleted")?></th>
                    <th><?php echo $this->lang->line("Actions")?></th>
                    <th><?php echo $this->lang->line("Last Replied")?></th>
                    <th><?php echo $this->lang->line("Error")?></th>
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
          order: [[ 10, "desc" ]],
          pageLength: 10,
          ajax: {
              url: base_url+'comment_reply_enhancers/all_response_report_data',
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
                targets: [1,2,5,6,7,8],
                visible: false
            },
            {
                targets: '',
                className: 'text-center'
            },
            {
                targets: [0,4,9,11],
                sortable: false
            }
          ]
      });


      $(document).on('click', '#search_submit', function(event) {
        event.preventDefault(); 
        table.draw();
      });


      // report table started
      var table1 = '';
      var perscroll1;
      $(document).on('click','.view_report',function(e){
        e.preventDefault();
        var table_id = $(this).attr('table_id');

        if(table_id !='') 
        {
          $("#put_row_id").val(table_id);
          $("#download").attr("href",base_url+"comment_reply_enhancers/download_get_reply_info/"+table_id);
        }


        $("#view_report_modal").modal();

        var commnet_hide_delete_addon = "<?php echo $commnet_hide_delete_addon; ?>";
        if(commnet_hide_delete_addon == 1)
          var visible_section = "";
        else
          var visible_section = [9];

        if (table1 == '')
        {

          table1 = $("#mytable1").DataTable({
              serverSide: true,
              processing:true,
              bFilter: false,
              order: [[ 3, "desc" ]],
              pageLength: 10,
              ajax: {
                  url: base_url+'comment_reply_enhancers/ajax_get_reply_info',
                  type: 'POST',
                  data: function ( d )
                  {
                      d.table_id = $("#put_row_id").val();
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
                  targets: visible_section,
                  visible: false
                },
                {
                    targets: '',
                    className: 'text-center'
                },
                {
                    targets: [0,1,2,5,6,7,8,9],
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

        $("#outside_filter").html('');
        setTimeout(function(){
          $.ajax({
            type:'POST' ,
            url: "<?php echo site_url(); ?>comment_reply_enhancers/get_count_info",
            data:{table_id:table_id},
            dataType:'JSON',
            success:function(response)
            {
              if(response.status == '1')
                $("#outside_filter").html(response.str); 
            }
          }); 
        }, 2000);

      });

      $(document).on('keyup', '#searching', function(event) {
        event.preventDefault(); 
        table1.draw();
      });




    });
  
 
</script>


<div class="modal fade" id="view_report_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="fas fa-reply-all"></i> <?php echo $this->lang->line("Page Response Report");?>
                <small>
                (
                <?php 
                $delete_junk_data_after_how_many_days = $this->config->item("delete_junk_data_after_how_many_days");
                if($delete_junk_data_after_how_many_days=="") $delete_junk_data_after_how_many_days = 30;
                ?>
                <?php echo $this->lang->line("Details data shows for last")." : ".$delete_junk_data_after_how_many_days." ".$this->lang->line("days"); ?>
                )
                </small>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body data-card">
                <div class="row">
                <div class="col-12 text-center" id="outside_filter"></div>
                <br><br>
            <div class="col-12 col-md-9">
              <input type="text" id="searching" name="searching" class="form-control" placeholder="<?php echo $this->lang->line("Search..."); ?>" style='width:200px;'>                                          
            </div>
            <div class="col-12 col-md-3">
              <a href="" target="_blank" class="btn btn-outline-primary download_lead_list float-right" id="download"><i class="fa fa-cloud-download"></i> <?php echo $this->lang->line("Download lead list"); ?></a>                         
            </div>

                    <div class="col-12">
                      <div class="table-responsive2">
                        <input type="hidden" id="put_row_id">
                        <table class="table table-bordered" id="mytable1">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line("Comment"); ?></th> 
                                <th><?php echo $this->lang->line("name"); ?></th> 
                                <th><?php echo $this->lang->line("comment time"); ?></th>      
                                <th><?php echo $this->lang->line("reply time"); ?></th>
                                <th><?php echo $this->lang->line("comment reply message"); ?></th>
                                <th><?php echo $this->lang->line("private reply message"); ?></th>
                                <th><?php echo $this->lang->line("comment reply status"); ?></th>      
                                <th><?php echo $this->lang->line("private reply status"); ?></th> 
                                <th><?php echo $this->lang->line("Hide/Delete status"); ?></th> 
                              </tr>
                            </thead>
                        </table>
                      </div>
                    </div> 
                </div>               
            </div>
        </div>
    </div>
</div>
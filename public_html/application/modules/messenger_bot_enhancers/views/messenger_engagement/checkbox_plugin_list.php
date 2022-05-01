<?php $this->load->view('admin/theme/message'); ?>
<style>
    .dropdown-toggle::after{content:none !important;}
  .dropdown-toggle::before{content:none !important;}
  /*#search_domain_name{max-width: 30% !important;}*/
  #search_page_id{width: 150px !important;}
  @media (max-width: 575.98px) {
    #search_page_id{width: 130px !important;}
    /*#search_domain_name{max-width: 77% !important;}*/
  }
</style>
<input type="hidden" name="page_id" id="page_id" value="<?php echo $selected_page; ?>">

<div class="table-responsive2 data-card">
  <table class="table table-bordered table-sm table-striped" id="mytable">
    <thead>
      <tr>
        <th>#</th>      
        <th><?php echo $this->lang->line("Campaign ID"); ?></th>      
        <th><?php echo $this->lang->line("Domain"); ?></th>
        <th ><?php echo $this->lang->line("Page"); ?></th>
        <th><?php echo $this->lang->line("Js Embed Code"); ?></th>
        <th><?php echo $this->lang->line("Actions"); ?></th>
        <!-- <th><?php echo $this->lang->line("New or Existing?"); ?></th> -->
        <th><?php echo $this->lang->line("Domain Code"); ?></th>
        <!-- <th><?php echo $this->lang->line("Js Event"); ?></th> -->
        <th><?php echo $this->lang->line('Reference'); ?></th>
        <th><?php echo $this->lang->line('Editor Type'); ?></th>
        <th><?php echo $this->lang->line('Created at'); ?></th>
        <th><?php echo $this->lang->line('Label'); ?></th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>             




<div class="modal fade" role="dialog" id="get_embed_modal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-code"></i> <?php echo $this->lang->line('Checkbox plugin embed code'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label > <?php echo $this->lang->line("Copy the code below and paste inside the html element of your webpage where you want to display this plugin")?> </label>

              <pre class="language-javascript" ><code id="test" class="dlanguage-javascript description" ></code></pre>

            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <!-- <button id="copy_js_code" type="button" class="btn btn-primary"><i class="fas fa-cut"></i> <?php echo $this->lang->line('Copy'); ?> -->
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line('Close'); ?></button>
      </button>
    </div>
  </div>
</div>
</div>

<?php 
$areyousure=$this->lang->line("are you sure");
// $builder_load_url = base_url("messenger_bot_enhancers/checkbox_plugin_add/".$selected_page.'/1');
$builder_load_url = base_url("visual_flow_builder/load_builder/".$selected_page.'/1/'.$media_type.'?type=messenger-engagement&plugin=checkbox_plugin&action=messenger_engagement_plugin');
$drop_menu = '<a target="_BLANK" href="'.$builder_load_url.'" class="float-right btn btn-primary d-none"><i class="fas fa-plus-circle"></i> '.$this->lang->line("New Plugin").'</a>';
?> 

<script>
$(document).ready(function($) {
   
  var base_url = '<?php echo base_url(); ?>';

  var drop_menu = '<?php echo $drop_menu;?>';
  setTimeout(function(){ 
    $("#mytable_filter").append(drop_menu);
  }, 1000);

  // datatable section started
  var perscroll;
  var table = $("#mytable").DataTable({
      serverSide: true,
      processing:true,
      bFilter: true,
      order: [[ 1, "desc" ]],
      pageLength: 10,
      ajax: 
      {
        "url": base_url+'messenger_bot_enhancers/checkbox_plugin_list_data',
        "type": 'POST',
        data: function ( d )
        {
            d.search_page_id = $('#page_id').val();
        }
      },
      language: 
      {
        url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
      },
      dom: '<"top"f>rt<"bottom"lip><"clear">',
      columnDefs: [
          {
            targets: [1,3],
            visible: false
          },
          {
            targets: [0,1,4,5,6],
            className: 'text-center'
          },
          {
            targets:[0,1,4,5,10],
            sortable: false
          },
          {
            targets:[4],
            render: function( data, type, row, meta )
                 {
                  
                    var embed_js = '<a campaign_id='+row[1]+' class="badge badge-status get_js_embed" title="<?php echo $this->lang->line('Get Embed Code') ?>" style="cursor: pointer;"><i class="fas fa-code"></i> <?php echo $this->lang->line('Js Code'); ?></a>';
                    return embed_js;
                 }
          },






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

  $(document).on('change', '#search_page_id', function(event) {
    event.preventDefault(); 
    table.draw();
  });

  $(document).on('keyup', '#search_domain_name', function(event) {
    event.preventDefault(); 
    table.draw();
  });


  $(document).on('click', '#search_submit', function(event) {
    event.preventDefault(); 
    table.draw();
  });  







  $(document).on('click', '.get_js_embed', function(event) {
    event.preventDefault();
    
    var campaign_id = $(this).attr('campaign_id');
    
    $.ajax({
      url: '<?php echo base_url('messenger_bot_enhancers/checkbox_plugin_js_code') ?>',
      type: 'POST',
      data: {campaign_id: campaign_id},
      success: function(response) {
          if(response)
          {

            $(".description").text(response);
            $("#get_embed_modal").modal();
            Prism.highlightElement($('#test')[0]);
            $(".toolbar-item").find('a').addClass('copy');

          }
          else
          {
            swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Something went wrong"); ?>', 'error');
          }

        
      }

    });

    
    
  });



  $(document).on("click", ".copy", function(event) {
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



  $(document).on('click', '.delete_campaign', function(event) {
      event.preventDefault();
      
      swal({
            title: '<?php echo $this->lang->line("Delete Plugin"); ?>',
            text: '<?php echo $this->lang->line("Do you really want to delete this plugin?"); ?>',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) 
            {
                var base_url = '<?php echo site_url();?>';
                $(this).addClass('btn-danger btn-progress');
                $(this).removeClass('btn-outline-danger');
                var that = $(this);
                var campaign_id = $(this).attr('campaign_id');

                $.ajax({
                  context: this,
                  type:'POST' ,
                  url:"<?php echo site_url();?>messenger_bot_enhancers/checkbox_plugin_delete",
                  dataType: 'json',
                  data:{campaign_id : campaign_id},
                  success:function(response){ 

                     $(that).removeClass('btn-danger btn-progress');
                    
                     if(response.status == '1')
                        iziToast.success({title: '<?php echo $this->lang->line("Deleted Successfully"); ?>', message: response.message,position: 'bottomRight'});
                     else
                        iziToast.error({title: '<?php echo $this->lang->line("Error"); ?>',message: response.message ,position: 'bottomRight'});
                      
                     

                     table.draw();
                  }
                });
            } 
          });
    });   


    $(".xscroll1").mCustomScrollbar({
    autoHideScrollbar:true,
    theme:"light-thick",
    axis: "x"
    });



  });

</script>

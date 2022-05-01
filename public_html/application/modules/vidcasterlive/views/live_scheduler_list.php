<style>
  .dropdown-toggle::after{content:none !important;}
  .dropdown-toggle::before{content:none !important;}
  #searching{max-width: 30% !important;}
  #post_type{width: 130px !important;}
  @media (max-width: 575.98px) {
    #page_id{width: 130px !important;}
    #post_type{max-width: 110px !important;}
    #searching{max-width: 77% !important;}
  }
  .waiting,.modal_waiting {height: 100%;width:100%;display: table;}
  .waiting i,.modal_waiting i{font-size:60px;display: table-cell; vertical-align: middle;padding:30px 0;}
</style>

<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-list"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
     <a class="btn btn-primary" href="<?php echo base_url("vidcasterlive/add_live_scheduler");?>">
        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create new campaign"); ?>
     </a> 
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url("ultrapost"); ?>"><?php echo $this->lang->line("Facebook Poster"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">
            <div class="row">
              <div class="col-md-9 col-12">
                <div class="input-group mb-3 float-left" id="searchbox">
                    <!-- search by post type -->
                    <div class="input-group-prepend">
                      <select class="select2 form-control" id="post_type" name="post_type">
                        <option value=""><?php echo $this->lang->line("All Posts"); ?></option>
                        <option value="0"><?php echo $this->lang->line("Pending"); ?></option>
                        <option value="1"><?php echo $this->lang->line("Processing"); ?></option>
                        <option value="2"><?php echo $this->lang->line("Completed"); ?></option>
                      </select>
                    </div>

                    <input type="text" class="form-control" id="searching" name="searching" autofocus placeholder="<?php echo $this->lang->line('Publisher/Campaign name'); ?>" aria-label="" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <button class="btn btn-primary" id="search_submit" title="<?php echo $this->lang->line('Search'); ?>" type="button"><i class="fas fa-search"></i> <span class="d-none d-sm-inline"><?php echo $this->lang->line('Search'); ?></span></button>
                    </div>
                </div>
              </div>
              <div class="col-md-3 col-12">
                <a href="javascript:;" id="post_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> <?php echo $this->lang->line("Choose schedule date");?></a><input type="hidden" id="post_date_range_val">
              </div>
            </div>
            <div class="table-responsive2">
              <table class="table table-bordered" id="mytable">
                <thead>
                  <tr>
                    <th>#</th>     
                    <th><?php echo $this->lang->line("ID");?></th>
                    <th><?php echo $this->lang->line("Publisher");?></th>
                    <th><?php echo $this->lang->line("Campaign Name");?></th>
                    <th><?php echo $this->lang->line("Live");?></th>
                    <th><?php echo $this->lang->line("Status");?></th>
                    <th><?php echo $this->lang->line("Actions");?></th>
                    <th><?php echo $this->lang->line("Scheduled time");?></th>
                    <th><?php echo $this->lang->line("Stream Started");?></th>
                    <th><?php echo $this->lang->line("Stream Ended");?></th>
                    <th><?php echo $this->lang->line("FFMPEG Error Log");?></th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>             
          </div>
        </div>
      </div>
    </div>
    
  </div>
</section> 

<script>
$(document).ready(function($) {

  var base_url = '<?php echo base_url(); ?>';

  setTimeout(function(){ 
    $('#post_date_range').daterangepicker({
      ranges: {
        '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
        '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
        '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      startDate: moment().subtract(29, 'days'),
      endDate  : moment()
    }, function (start, end) {
      $('#post_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
    });
  }, 2000);


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

      setTimeout(function(){
        $(that).html('<?php echo $this->lang->line("Copy"); ?>');
      }, 2000); 

  });



  // datatable section started
  var perscroll;
  var table = $("#mytable").DataTable({
      serverSide: true,
      processing:true,
      bFilter: false,
      order: [[ 1, "desc" ]],
      pageLength: 10,
      ajax: 
      {
        "url": base_url+'vidcasterlive/live_scheduler_list_data',
        "type": 'POST',
        data: function ( d )
        {
            d.post_type = $('#post_type').val();
            d.searching = $('#searching').val();
            d.post_date_range = $('#post_date_range_val').val();
        }
      },
      language: 
      {
        url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
      },
      dom: '<"top"f>rt<"bottom"lip><"clear">',
      columnDefs: [
          {
            targets: [1],
            visible: false
          },
          {
            targets: [0,1,3,5,6,7,8,9,10],
            className: 'text-center'
          },
          {
            targets:[0,1,2,4,5,6,10],
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


  $(document).on('change', '#post_type', function(event) {
    event.preventDefault(); 
    table.draw();
  });

  $(document).on('change', '#post_date_range_val', function(event) {
    event.preventDefault(); 
    table.draw();
  });

  $(document).on('click', '#search_submit', function(event) {
    event.preventDefault(); 
    table.draw();
  });
  // End of datatable section



  $('#embed_code_modal').on('hidden.bs.modal', function () {
    table.draw();
  });
  // End of reply table

  $(document.body).on('click','.ffmpeg_log',function(){ 
    var id = $(this).attr("id");
    var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>';
    $("#ffmpeg_log_content").html(loading);
    $("#ffmpeg_log_modal").modal();

    $.ajax({
         type:'POST' ,
         url: "<?php echo base_url('vidcasterlive/get_ffmpeg_log')?>",
         data: {id:id},
         success:function(response)
         { 
            $("#ffmpeg_log_content").html(response);
         }
    });
  });


  $(document).on('click','.delete',function(e){
    e.preventDefault();
    swal({
      title: '<?php echo $this->lang->line("Are you sure?"); ?>',
      text: "<?php echo $this->lang->line('Do you really want to delete this post from the database?'); ?>",
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) 
      {
        var id = $(this).attr('id');

        $.ajax({
          context: this,
          type:'POST' ,
          url:"<?php echo base_url('vidcasterlive/delete_post')?>",
          data:{id:id},
          success:function(response){ 
            if(response=='1')
              iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been deleted successfully."); ?>',position: 'bottomRight'});
            else
              iziToast.error({title: '',message: '<?php echo $this->lang->line("Something went wrong, please try again later."); ?>',position: 'bottomRight'});
            table.draw();
          }
        });
      } 
    });

  });


  $(document).on('click','.embed_code',function(){
    var id = $(this).attr("id");
    var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>';
    $("#embed_code_content").html(loading);
    $("#embed_code_modal").modal();

    $.ajax({
         type:'POST' ,
         url: "<?php echo base_url('vidcasterlive/get_embed_code')?>",
         data: {id:id},
         success:function(response)
         {
            $("#embed_code_content").html(response);
            Prism.highlightElement($('#test')[0]);
            $(".toolbar-item").find('a').addClass('copy');
         }
    });
  });

  $(document).on('click','.stream_info',function(){
    var id = $(this).attr("id");
    var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>';
    $("#stream_info_loading").html(loading);
    $("#stream_info_content").hide();
    $("#stream_info_modal").modal();

    $.ajax({
         type:'POST',
         url: "<?php echo base_url('vidcasterlive/get_stream_info')?>",
         data: {id:id},
         dataType : 'JSON',
         success:function(response)
         {
            $("#stream_info_loading").hide();
            $("#stream_info_content").show();

            $("#server_url").text(response.server_url);
            $("#stream_key").text(response.stream_key);
            $("#stream_url").text(response.stream_url);

            Prism.highlightElement($('#server_url')[0]);
            Prism.highlightElement($('#stream_key')[0]);
            Prism.highlightElement($('#stream_url')[0]);

            $(".toolbar-item").find('a').addClass('copy');
         }
    });
  });

    
});
</script>



<div class="modal fade" id="ffmpeg_log_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-code"></i>  <?php echo $this->lang->line("FFMPEG Error Log"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body" id="ffmpeg_log_content">
        
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="embed_code_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-code"></i> <?php echo $this->lang->line("Get Embed Code");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body" id="embed_code_content">
      
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="stream_info_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-code"></i> <?php echo $this->lang->line("Get Stream Info");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row" id="stream_info_loading">
          
        </div>
        <div class="row" id="stream_info_content">
          <div class="col-12">
            <div class="section">                
              <h2 class="section-title"><?php echo $this->lang->line('Server URL'); ?></h2>
              <pre class='language-javascript'><code id='server_url' class='dlanguage-javascript'></code></pre>
            </div>
          </div>
          <div class="col-12">
            <div class="section">                
              <h2 class="section-title"><?php echo $this->lang->line('Stream Key'); ?></h2>
              <pre class='language-javascript'><code id='stream_key' class='dlanguage-javascript'></code></pre>
            </div>
          </div>
          <div class="col-12">
            <div class="section">                
              <h2 class="section-title"><?php echo $this->lang->line('Stream URL'); ?></h2>
              <pre class='language-javascript'><code id='stream_url' class='dlanguage-javascript'></code></pre>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

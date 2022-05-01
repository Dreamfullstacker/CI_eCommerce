<?php
$this->load->view("include/upload_js");
$file_upload_limit = 2;
if($this->config->item('xerobiz_file_upload_limit') != '') {
    $file_upload_limit = $this->config->item('xerobiz_file_upload_limit');
}
?>
<style type="text/css">
    .space{height: 10px;}
    .select2{width: 100% !important;}
    #title_variable { border: 1px dashed #d0d0d0; }
    .ajax-upload-dragdrop{width:100% !important; border: 2px dashed #dadada;}
</style>

<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-rss"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-button">
            <a class="btn btn-primary" id="add_feed" data-toggle="modal" href='#add_feed_modal'><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('New auto posting feed');?></a>
        </div>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url('gmb'); ?>"><?php echo $this->lang->line("Google My Business"); ?></a></div>
            <div class="breadcrumb-item"><a href="<?php echo base_url('gmb/campaigns'); ?>"><?php echo $this->lang->line("Campaigns"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-light alert-has-icon">
                    <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                    <div class="alert-body">
                        <?php echo $this->lang->line("RSS auto posting will be publised as Link post.It will post once any new feed comes to RSS feed after setting it in the system. It will not post any existing feeds during setup the campaign."); ?>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <?php
                            echo "<div class='table-responsive data-card'> 
                            <table class='table table-bordered table-condensed' id='mytable'>";
                                echo "<thead>";
                                echo "<tr>";
                                    echo "<th>".$this->lang->line("SN")."</th>";
                                    echo "<th>".$this->lang->line("Feed Name")."</th>";
                                    echo "<th class='text-center'>".$this->lang->line("Status")."</th>";
                                    echo "<th class='text-center'>".$this->lang->line("Actions")."</th>";
                                    echo "<th class='text-center'>".$this->lang->line("Last Updated")."</th>";
                                    echo "<th class='text-center'>".$this->lang->line("Last Feed")."</th>";
                                echo "</tr></thead>";
                                echo "<tbody>";

                                $i=0;

                                foreach ($settings_data as $key => $value) {
                                    
                                    $i++;

                                    if($value['last_pub_date'] != "0000-00-00 00:00:00") {
                                        $last_pub_date = date('j M H:i', strtotime($value['last_pub_date']));
                                    } else { 
                                        $last_pub_date = "<i class='fas fa-times'></i>";
                                    }
                            
                                    $status = '';

                                    if($value['status'] == '1') {
                                        $status='<span class="text-success"><i class="fa fa-check-circle"></i> '.$this->lang->line("Active").'</span>';
                                    } else if($value['status'] == '0') {
                                        $status='<span class="text-danger"><i class="fa fa-times-circle"></i> '.$this->lang->line("Inactive").'</span>';
                                    } else {
                                        $status='<span class="text-warning"><i class="fas fa-ban"></i> '.$this->lang->line("Disabled").'</span>';
                                    }
                            
                                    echo "<tr>";
                                    echo "<td nowrap>".$i."</td>";
                                    echo "<td nowrap><a href='".$value['feed_url']."' target='_BLANK'>".$value['feed_name']."</a></td>";
                                    echo "<td class='text-center' nowrap>".$status."</td>";
                                    echo "<td class='text-center' nowrap>";
                                    echo '<div class="dropdown d-inline dropright">
                                        <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i></button>
                            
                                        <div class="dropdown-menu mini_dropdown text-center" style="width:208px !important">
                            
                                        <a href="" data-id="'.$value['id'].'" data-toggle="tooltip" title="'.$this->lang->line("Settings").'" class="btn btn-circle btn-outline-primary campaign_settings"><i class="fas fa-cog"></i></a>';
                            
                                        if($value['status']=='1') {
                                            echo  '<a href="" data-id="'.$value['id'].'" data-toggle="tooltip" title="'.$this->lang->line("Disable").'" class="btn btn-circle btn-outline-warning disable_settings"><i class="fas fa-ban"></i></a>';
                                        } else {
                                            echo  '<a href="" data-id="'.$value['id'].'" data-toggle="tooltip" title="'.$this->lang->line("Enable").'" class="btn btn-circle btn-outline-success enable_settings"><i class="fas fa-check-circle"></i></a>';
                                        }
                            
                                        echo '<a href="" data-id="'.$value['id'].'" data-toggle="tooltip" title="'.$this->lang->line("Delete").'" class="btn btn-circle btn-outline-danger delete_settings"><i class="fas fa-trash-alt"></i></a>';
                            
                                        echo '<a href="" data-id="'.$value['id'].'" data-toggle="tooltip" title="'.$this->lang->line("Error").'" class="btn btn-circle btn-outline-secondary error_log"><i class="fas fa-bug"></i></a>';
                            
                                    echo '</div></div>';
                                    echo "<td class='text-center' nowrap>".date("d M H:i",strtotime($value["last_updated_at"]))."</td>";
                                    echo "<td class='text-center' nowrap>".$last_pub_date."</td>"; 
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                            echo "</table></div>";
                        ?>             
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="error_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-bug"></i>&nbsp;
                    <?php echo $this->lang->line("Error Log") ?></span>
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="xit-spinner bg-white text-primary">
                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                </div>
                <div id="error_modal_container"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-lg btn btn-default float-right" data-dismiss="modal" id="close_settings">
                    <i class="fas fa-times"></i>&nbsp;
                    <?php echo $this->lang->line("Close");?>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="settings_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-cog"></i>&nbsp;
                    <?php echo $this->lang->line("Campaign Settings") ?>&nbsp;
                    <span id="put_feed_name"></span>
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body" id="feed_setting_container"></div>

            <div class="modal-footer pl-4 pr-4">
                <button type="button" class="btn-lg btn btn-default" data-dismiss="modal" id="close_settings">
                    <i class="fas fa-times"></i>&nbsp;
                    <?php echo $this->lang->line("Close");?>
                </button>
                <button type="button" class="btn-lg btn btn-primary ml-0" id="save_settings">
                    <i class="fas fa-paper-plane"></i>&nbsp;
                    <?php echo $this->lang->line("Create Campaign");?>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_feed_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-rss"></i>&nbsp;
                    <?php echo $this->lang->line("Auto-Posting Feed") ?>
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#" id="add_feed_form">
                <div class="modal-body">
                    <div class="text-center waiting hidden" id="loader">
                        <i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i>
                    </div>
                    <div id="response"></div>
                    <div class="space"></div>
                    
                    <label class="margin-bottom-label">
                        <?php echo $this->lang->line("Feed Name") ?> *
                    </label>
                    <input type="text" name="feed_name" id="feed_name" class="form-control mb-3">

                    <label class="margin-bottom-label">
                        <?php echo $this->lang->line("RSS Feed URL") ?> *
                    </label>
                    <input type="text" name="feed_url" id="feed_url" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-lg btn btn-primary" id="add_feed_submit">
                        <i class='fa fa-plus-circle'></i>&nbsp;
                        <?php echo $this->lang->line('Add Feed');?>
                    </button>

                    <button type="button" class="btn-lg btn btn-default" data-dismiss="modal">
                        <i class='fas fa-times'></i>&nbsp;
                        <?php echo $this->lang->line('Close');?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$("document").ready(function(){
    var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
    var base_url="<?php echo site_url(); ?>";
      
    var table = $("#mytable").DataTable({
        language: 
        {
            url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columnDefs: [
            {
                targets: [3,5],
                sortable: false
            }
        ]
    });
    
    (function ($, undefined) {
        $.fn.getCursorPosition = function() {
            var el = $(this).get(0);
            var pos = 0;
            if('selectionStart' in el) {
                pos = el.selectionStart;
            } else if('selection' in document) {
                el.focus();
                var Sel = document.selection.createRange();
                var SelLength = document.selection.createRange().text.length;
                Sel.moveStart('character', -el.value.length);
                pos = Sel.text.length - SelLength;
            }

            return pos;
        }
    })(jQuery);
    
    $(document).on('click', '#title_variable', function(event) {
        // event.preventDefault();

        let textAreaTxt = $(".emojionearea-editor").html();
        var lastIndex = textAreaTxt.lastIndexOf("<br>");   
        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
        lastTag=lastTag.trim(lastTag);

        if(lastTag=="<br>") {
        textAreaTxt = textAreaTxt.substring(0, lastIndex); 
        }

        var txtToAdd = " #TITLE# ";
        var new_text = textAreaTxt + txtToAdd;
        $(".emojionearea-editor").html(new_text);
        $(".emojionearea-editor").click();  
    });
    
    $(document).on('click', '.campaign_settings', function(e) { 
        e.preventDefault();

        $('.xit-spinner').show();
        var id = $(this).attr('data-id');

        $.ajax({
            type:'POST' ,
            url: base_url + "gmb/rss_campaign_settings",
            data: {id},
            dataType: 'JSON',
            success:function(response) {  
                if(response.status =='0' ) {
                    $("#settings_modal .modal-footer").hide();
                } else {
                    $("#settings_modal .modal-footer").show();
                }

                $("#feed_setting_container").html(response.html);
                $("#put_feed_name").html(" : "+response.feed_name);

                $("#submit_status").hide();                       
                $('.xit-spinner').hide();
                $("#settings_modal").modal();

            }
        });
    });

    $(document).on('click','#save_settings',function(e) { 
        e.preventDefault();
    
        var location_name = $("#location_name").val(),
            posting_start_time=$("#posting_start_time").val(),
            posting_end_time=$("#posting_end_time").val();

        if (! Array.isArray(location_name) || ! location_name.length) {
            swal(
                '<?php echo $this->lang->line("Error"); ?>',
                '<?php echo $this->lang->line('Please select location name(s)');?>', 
                'error'
            );

            return;
        }
    
    
        if(location_name.length)
        {
            if(posting_start_time == '' ||  posting_end_time == '') {
                swal(
                    '<?php echo $this->lang->line("Error"); ?>',
                    '<?php echo $this->lang->line('Please select post between times'); ?>',
                     'error'
                );

                return;
            }

            var rep1 = parseFloat(posting_start_time.replace(":", "."));
            var rep2 = parseFloat(posting_end_time.replace(":", "."));
            var rep_diff = rep2 - rep1;
    
            if(rep1 >= rep2 || rep_diff < 1.0)
            {
                swal(
                    '<?php echo $this->lang->line("Error"); ?>', 
                    '<?php echo $this->lang->line("Post time was invalid. (The time difference should be 1 hour at least)");?>', 
                    'error'
                );

                return;
            }
        }

        $("#save_settings").addClass("btn-progress");
        var queryString = new FormData($("#campaign_settings_form")[0]);

        $.ajax({
            type:'POST' ,
            url: base_url + "gmb/create_rss_campaign",
            dataType: 'JSON',
            data: queryString,
            contentType: false,
            processData: false,
            success:function(response)
            { 
                $("#save_settings").removeClass("btn-progress");

                if ("0" === response.status) {
                    swal('<?php echo $this->lang->line("Error"); ?>', response.message , 'error');
                    return;
                }

                if(response.status == '1') {
                    swal('<?php echo $this->lang->line("Success"); ?>', response.message , 'success');
                }
              
                $("#settings_modal").modal('hide');
                $('.modal-backdrop').hide();
            }
        });
    });
    
    $(document).on('click','.enable_settings',function(e){ 
        e.preventDefault();

        $(this).addClass('disabled');
        var id=$(this).attr('data-id');
    
        swal({
            title: '<?php echo $this->lang->line("Delete Campaign"); ?>',
            text: '<?php echo $this->lang->line("Do you really want to enable this campaign?"); ?>',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) 
            {
                $.ajax({
                    type:'POST' ,
                    url: base_url + "gmb/enable_rss_settings",
                    data: {id:id},
                    dataType:'JSON',
                    success:function(response)
                    {  
                        if(response.status=='0') 
                        {
                            $("#enable"+id).removeClass('disabled');
                            iziToast.error({title: '<?php echo $this->lang->line("Error"); ?>',message: response.message,position: 'bottomRight'});
                        } else {
                            iziToast.success({title: '<?php echo $this->lang->line("Success"); ?>',message: '<?php echo $this->lang->line("Campaign has been enabled successfully."); ?>',position: 'bottomRight'});
                            setTimeout(function(){ location.reload(); }, 1000);
                        }
                    }
                });
            } 
        });
    });
    
    $(document).on('click','.disable_settings',function(e){ 
        e.preventDefault();
    
        var id=$(this).attr('data-id');
    
        swal({
            title: '<?php echo $this->lang->line("Delete Campaign"); ?>',
            text: '<?php echo $this->lang->line("Do you really want to disable this campaign?"); ?>',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) 
            {
                $.ajax({
                    type:'POST' ,
                    url: base_url + "gmb/disable_rss_settings",
                    data: {id},
                    success:function(response)
                    {  
                        iziToast.success({title: '<?php echo $this->lang->line("Success"); ?>',message: '<?php echo $this->lang->line("Campaign has been disabled successfully."); ?>',position: 'bottomRight'});
                        setTimeout(function(){ location.reload(); }, 1000);
                    }
                });
            } 
        });
    });
    
    $(document).on('click','.delete_settings',function(e){ 
        e.preventDefault();
    
        var id=$(this).attr('data-id');
    
        swal({
            title: '<?php echo $this->lang->line("Delete Campaign"); ?>',
            text: '<?php echo $this->lang->line("Do you really want to delete this campaign?"); ?>',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) 
            {
                $.ajax({
                    type:'POST' ,
                    url: base_url + "gmb/delete_rss_settings",
                    data: {id:id},
                    success:function(response)
                    {  
                        iziToast.success({title: '<?php echo $this->lang->line("Success"); ?>',message: '<?php echo $this->lang->line("Campaign has been deleted successfully."); ?>',position: 'bottomRight'});
                        setTimeout(function(){ location.reload(); }, 1000);
                    }
                });
            } 
        });
    });
    
    $(document).on('click','#add_feed_submit',function() { 
    
        var feed_name = $("#feed_name").val();
        var feed_url = $("#feed_url").val();

        if(feed_name=='') {
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select feed type name');?>" , 'error');
          return;
        }

        if(feed_url=='') {
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Feed URL can not be empty');?>" , 'error');
          return;
        }

        $("#add_feed_submit").addClass('btn-progress');

        var queryString = new FormData($("#add_feed_form")[0]);

        $.ajax({
            type:'POST' ,
            url: base_url + "gmb/add_feed_action",
            data: queryString,
            dataType : 'JSON',
            processData: false,
            contentType: false,
            success: function(response) {  
                $("#add_feed_submit").removeClass('btn-progress');

                if(response.status=='1') {
                    swal('<?php echo $this->lang->line("Success"); ?>', response.message , 'success');
                } else {
                    swal('<?php echo $this->lang->line("Error"); ?>', response.message , 'error');
                }

            }
        });
    }); 
    
    $('#add_feed_modal').on('hidden.bs.modal', function () { 
        location.reload();
    });

    $('#settings_modal').on('hidden.bs.modal', function () { 
        location.reload();
    });
    
    $(document).on('click','.error_log',function(e){ 
        e.preventDefault();
        $(".xit-spinner").show();
        $("#error_modal_container").html("");
        $("#error_modal").modal();
        var id=$(this).attr('data-id');
        $.ajax({
            type:'POST' ,
            url: base_url + "gmb/show_rss_error_log",
            data: {id:id},
            success:function(response)
            {  
                $("#error_modal_container").html(response);
                $(".xit-spinner").hide();
            }
        });     
    });
    
    $(document).on('click','.clear_log',function(e){ 
        e.preventDefault();      
        var id=$(this).attr('data-id');
        swal({
            title: '<?php echo $this->lang->line("Clear Log"); ?>',
            text: '<?php echo $this->lang->line("Do you really want to clear log?"); ?>',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) 
            {
                $.ajax({
                    type:'POST' ,
                    url: base_url + "gmb/clear_rss_error_log",
                    data: {id:id},
                    success:function(response)
                    {  
                        $("#error_modal").modal('toggle');
                        swal('<?php echo $this->lang->line("Clear Log"); ?>', "<?php echo $this->lang->line('Log has been cleared successfully.');?>" , 'success');
                    }
                });
            } 
        });
    });

    // Upload status
    $(document).on('change', '#media_status', function(e) {
        e.preventDefault();

        if (true === $('#media_status').prop('checked')) {
            $('#upload-wrapper').removeClass('d-none');
        } else {
            $('#upload-wrapper').addClass('d-none');
        }
    });       
});
</script>



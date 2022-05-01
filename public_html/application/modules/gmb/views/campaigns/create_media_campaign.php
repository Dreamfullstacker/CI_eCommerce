<?php
$this->load->view("include/upload_js");
$file_upload_limit = 2;
if($this->config->item('xerobiz_file_upload_limit') != '') {
    $file_upload_limit = $this->config->item('xerobiz_file_upload_limit');
}
?>
<style type="text/css">
    .card{margin-bottom:0;border-radius: 0;}
    .main_card{box-shadow: none !important;height: 100%;}
    .collef{padding-right: 0px; border-right:1px solid #f9f9f9;}
    .colmid{padding-left: 0px;}
    .card .card-header input{max-width: 100% !important;}
    .card .card-header h4 a{font-weight: 700 !important;}
    ::placeholder{color: white !important;}
    .full-documentation{cursor: pointer;}
    .input-group-prepend{margin-left:-1px;}
    .input-group-text{background: #eee;}
    .schedule_block_item label,label{color:#34395e !important;font-size:12px !important;font-weight:600 !important;letter-spacing: .5px !important;}
    .card-body #post_tab_content { border:solid 1px #dee2e6;border-top:0 !important;padding:25px 20px; }
</style>
<style type="text/css" media="screen">
    /* .box-header{border-bottom:1px solid #ccc !important;margin-bottom:15px;} */
    /* .box-primary{border:1px solid #ccc !important;} */
    /* .box-footer{border-top:1px solid #ccc !important;} */
    .padding-5{padding:5px;}
    .padding-20{padding:20px;}
    .box-body,.box-footer{padding:20px;}
    .box-header{padding-left: 20px;}

    .preview
    {
        font-family: helvetica,​arial,​sans-serif;
        padding: 20px;
    }
    /*.preLoader{ margin-bottom:30px !important; }*/
    .preview_cover_img
    {
        width:45px;
        height:45px;
        border: .5px solid #ccc;
    }
    .preview_page
    {
        padding-left: 7px;
        color: #365899;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
    }
    .preview_page_sm
    {
        padding-left: 7px;
        padding-top: 7px;
        color: #9197a3;
        font-size: 13px;
        font-weight: 300;
        cursor: pointer;
    }
    .preview_img
    {
        width:100%;
        border: 1px solid #ccc;
        border-bottom: none;
        cursor: pointer;
    }
    .only_preview_img
    {
        width:100%;
        border: 1px solid #ccc;
        cursor: pointer;
    }
    .demo_preview
    {
        width:100%;
        /*border: 1px solid #f5f5f5; */
        cursor: pointer;
    }
    .preview_og_info
    {
        position: relative;
        word-wrap: break-word;
        border: 1px solid #ccc;
        /*box-shadow: 0px 0px 2px #ddd;
        -webkit-box-shadow: 0px 0px 2px #ddd;
        -moz-box-shadow: 0px 0px 2px #ddd;
        padding: 10px;*/
        cursor: pointer;
    }
    .preview_og_info_title
    {
        font-size: 23px;
        font-weight: 400;
        font-family: 'Times New Roman',helvetica,​arial;
    }
    .preview_og_info_desc
    {
        margin-top: 5px;
        font-size: 13px;
    }
    .preview_og_info_link
    {
        position: relative;
        word-wrap: break-word;
        text-transform: uppercase;
        color: #9197a3;
        margin-top: 7px;
    }
    .preview_og_info_coupon {
        background-color: #f8f9fa;
        border: 2px dashed #dadce0;
        border-radius: 6px;
        margin-top: 12px;
        padding-bottom: 15px;
        padding-left: 5px;
        padding-right: 5px;
        padding-top: 15px;
        text-align: center;
        font-size: 20px;
    }
    .post_type
    {
        padding: 10px 12px;
        border: 1px solid <?php echo $THEMECOLORCODE;?>;
        font-weight: bold;
        color: <?php echo $THEMECOLORCODE;?>;
        margin-right: 2px;
    }
    .post_type.active
    {
        background: <?php echo $THEMECOLORCODE;?>;
        /*color: #fff;*/
    }
    .ms-choice span
    {
        padding-top: 2px !important;
    }
    .hidden
    {
        display: none;
    }
    .box-primary
    {
        -webkit-box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
        -moz-box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
        box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
    }
    .content-wrapper{background: #fff;}
    .ajax-upload-dragdrop{width:100% !important; border: 2px dashed #dadada;}
</style>

<?php
    if($this->session->userdata("user_type")=="Admin" || in_array(74,$this->module_access)) {
        $like_comment_Share_reply_block_class = "";
    } else {
        $like_comment_Share_reply_block_class = "hidden";
    }
?>
<section class="section">
    <div class="section-header">
        <h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href='<?php echo base_url("gmb/media_campaigns"); ?>'><?php echo $this->lang->line("Media Campaigns"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-12 col-md-7 collef">
                <div class="card">

                    <div class="card-header mb-3">
                        <h4>
                            <i class="fa fa-plus-circle"></i>&nbsp;
                            <?php echo $page_title; ?>        
                        </h4>
                    </div>

                    <!-- starts card-body -->
                    <div class="card-body" style="padding-top:0 !important;margin-top: -3px;">

                        <!-- starts form -->
                        <form action="<?php echo base_url('media/create_campaign'); ?>" enctype="multipart/form-data" id="auto_media_poster_form" method="post">

                            <!-- campaign name -->
                            <div class="form-group">
                                <label><?php echo $this->lang->line('Campaign Name');?></label>
                                <input type="input" class="form-control"  name="campaign_name" id="campaign_name">
                            </div>
                            <!-- ends campaign name -->

                            <!-- media categories -->
                            <div class="form-group">
                                <label>
                                    <?php echo $this->lang->line('Media category'); ?>
                                </label>
                                <?php
                                    if (count($media_categories)) {
                                        $media_categories[''] = $this->lang->line('Select media category');
                                        echo form_dropdown(
                                            'media_category',
                                            $media_categories,
                                            null,
                                            'class="form-control select2" id="media_category" required style="width:100%;"'
                                        );
                                    }
                                ?>
                            </div>
                            <!-- ends categories -->

                            <!-- media description -->
                            <div id="media_description_text_area" class="form-group">
                                <label>
                                    <?php echo $this->lang->line('Media description'); ?>
                                </label>
                                <textarea class="form-control" name="media_description" id="media_description" maxlength="500" placeholder="<?php echo $this->lang->line('Type description here...');?>"></textarea>
                            </div>
                            <!-- ends description -->

                            <!-- media upload -->
                            <div class="form-group">
                                <label>
                                    <?php echo $this->lang->line('Media upload');?>
                                    <a href="#" data-placement="top" data-html="true" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Photo & video guidelines") ?>" data-content="<?php echo $this->lang->line("<strong>Photo guidelines</strong><br>Your photos look best on Google if they meet the following standards:<br><strong>Format</strong>: JPG or PNG.<br><strong>Size</strong>: Between 10 KB and 5 MB.</br><strong>Recommended resolution</strong>: 720 px tall, 720 px wide.<br><strong>Minimum resolution</strong>: 250 px tall, 250 px wide.<br><br><strong>Video guidelines</strong><br>Make sure your videos meet the following requirements:<br><strong>Duration</strong>: Up to 30 seconds long<br><strong>File size</strong>: Up to 75 MB<br><strong>Resolution</strong>: 720p or higher") ?>"><i class='fa fa-info-circle'></i></a>
                                    <p class="text-muted small mb-0"><?php echo $this->lang->line("If any video does not support, we recommend convert the video to mp4 first and then try again please."); ?></p>
                                </label>
                                <div id="media_file"><?php echo $this->lang->line('Upload PHOTO/VIDEO');?></div>
                                <br/>
                            </div>
                            <!-- ends media upload -->

                            <!-- location name -->
                            <div class="form-group">
                                <label>
                                    <?php echo $this->lang->line('Location Name'); ?>
                                    <p class="text-muted small mb-0"><?php echo $this->lang->line('You could select multiple location.'); ?></p>
                                </label>
                                <?php
                                    if (count($locations)) {
                                        echo form_dropdown(
                                            'location_name[]',
                                            $locations,
                                            null,
                                            'class="form-control select2" id="location_name" style="width:100%;" multiple required'
                                        );
                                    }
                                ?>
                            </div>
                            <!-- ends location name -->

                            <!-- starts posting time section -->
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line("Posting Time") ?>
                                            <a 
                                                href="#" 
                                                data-placement="top" 
                                                data-toggle="popover" 
                                                data-trigger="focus" 
                                                title="<?php echo $this->lang->line("Posting Time") ?>" 
                                                data-content="<?php echo $this->lang->line("If you schedule a campaign, system will automatically process this campaign at mentioned time and time zone. Schduled campaign may take upto 1 hour longer than your schedule time depending on server's processing.") ?>"
                                            >
                                                <i class='fa fa-info-circle'></i> 
                                            </a>
                                        </label>
                                        <br>
                                        <label class="custom-switch mt-2">
                                            <input type="checkbox" name="schedule_type" value="now" id="schedule_type" class="custom-switch-input" checked>
                                            <span class="custom-switch-indicator"></span>
                                            <span class="custom-switch-description"><?php echo $this->lang->line('Post Now');?></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- ends posting time section -->

                            <!-- starts scheduling time and timezone -->
                            <div id="schedule-post-box" class="row d-none">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Schedule time'); ?></label>
                                        <input placeholder="Time"  name="schedule_time" id="schedule_time" class="form-control datepicker_x" type="text"/>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>
                                            <?php echo $this->lang->line('Time zone'); ?>
                                        </label>
                                        <?php
                                            if (count($time_zone)) {
                                                $time_zone[''] = $this->lang->line('Select your timezone');
                                                echo form_dropdown(
                                                    'time_zone',
                                                    $time_zone,
                                                    $this->config->item('time_zone'),
                                                    'class="form-control select2" id="time_zone" style="width:100%;"'
                                                );
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <!-- ends scheduling time and timezone -->

                            <!-- clears floating divs -->
                            <div class="clearfix"></div>

                            <!-- starts create campaign button -->
                            <div class="card-footer padding-0">
                                <button class="btn btn-lg btn-primary" id="create_media_campaign" name="create_media_campaign" type="button"><i class="fas fa-paper-plane"></i>
                                    <?php echo $this->lang->line("Create Media Campaign") ?>
                                </button>
                                <a class="btn btn-lg btn-light float-right" onclick='goBack("media", 0)'>
                                    <i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?>
                                </a>
                            </div>
                            <!-- ends create campaign button -->
                        </form>
                        <!-- ends form -->
                    </div>
                    <!-- ends card-body -->
                </div>
            </div>

            <!-- preview section -->
            <div class="col-12 col-md-5 colmid d-none d-sm-block">
                <div class="card gmb-preview">
                    <div class="card-header">
                        <h4><i class="fab fa-google"></i> <?php echo $this->lang->line('Preview'); ?></h4>
                    </div>
                    <div class="card-body">

                        <!-- starts post_preview -->
                        <div class="post_preview">
                            <div class="post_preview_block">
                                <img src="<?php echo base_url('assets/images/demo_image.png'); ?>" class="preview_img" alt="No Image Preview">

                                <div class="preview_og_info"></div>
                            </div>
                            <p class="text-muted small"><?php echo $this->lang->line("This preview may differ from actual post."); ?></p>
                        </div>
                        <!-- ends post_preview -->
                    </div>
                </div>
            </div>
            <!-- ends preview section -->
        </div>
        <!-- ends row -->
    </div>
    <!-- ends section-body -->
</section>

<script>
    $("document").ready(function()	{

        var base_url = "<?php echo base_url(); ?>",
            gmb_dummy_img_url = "<?php echo base_url('assets/images/demo_image.png'); ?>";
        
        // Emoji
        $("#media_description").emojioneArea({
            autocomplete: false,
            pickerPosition: "bottom",
            // hideSource: false,
        });

        var today = new Date();
        var next_date = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());

        // DateTimePicker
        $('.datepicker_x').datetimepicker({
            theme:'light',
            format:'Y-m-d H:i:s',
            formatDate:'Y-m-d H:i:s',
            minDate: today,
            maxDate: next_date
        })

        // Popover
        $('[data-toggle="popover"]').popover();
        $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});

        
        var makeScheduleValEmptyifscheduleisNow = $("input[name=schedule_type]:checked").val();
        if(makeScheduleValEmptyifscheduleisNow == 'now') {
            $("#schedule_time").val("");
        }

        $(document).on('change', '#schedule_type', function(e) {
            e.preventDefault();

            // Schedule
            if (false === $('#schedule_type').prop('checked')) {
                $('#schedule-post-box').removeClass('d-none');
            } else {
                $('#schedule-post-box').addClass('d-none');
                $('#schedule_time').val('');
                $('#time_zone').val('');
            }
            
        });

        function findFileType(str) {
            var allowed_img_extension = ['.jpeg', '.jpg', '.png', '.gif'];
            var allowed_vid_extension = ['.flv', '.3gp', '.mp4', '.mov', '.avi', '.mts', '.m4v', '.mkv', '.mpeg', '.ogv', '.webm'];
            var extension = str.substring(str.lastIndexOf('.'));

            var foundImg = allowed_img_extension.indexOf(extension);
            var foundVid = allowed_vid_extension.indexOf(extension);

            return (foundImg !== -1) ? 'image' : ((foundVid !== -1) ? 'video' : null);
        }

        // Tricks to know mime type of a file
        var input_el = $('ajax-upload-id-*');
        $(document).on('change', input_el, (event) => {
            if (event.target.files) {
                window.xit_gmb_file_mime_type = event.target.files[0].type
            } else {
                window.xit_gmb_file_mime_type = null;
            }
        });

        // Uploads media
        $("#media_file").uploadFile({
            url: base_url + 'gmb/media_campaign_upload',
            fileName: 'media_file',
            maxFileSize: <?php echo $file_upload_limit; ?> * 1024 * 1024,
            showPreview: false,
            returnType: 'json',
            dragDrop: true,
            showDelete: true,
            multiple: false,
            maxFileCount: 1,
            acceptFiles:'.jpeg, .jpg, .png, .gif, .flv, .ogg, .webm, .3gpp, .mp4, .mkv, .mpeg, .mov, .avi, .wmv, .m4v',
            deleteCallback: function (data, pd) {
                var delete_url = '<?php echo site_url('gmb/delete_media_campaign_upload'); ?>';
                $.post(delete_url, { op: 'delete', name: data }, function (resp, textStatus, jqXHR) {
                        var result = JSON.parse(resp),
                            preview_img = $('.preview_img'),
                            preview_block = $('.post_preview_block');

                        preview_block.find('video').remove();
                        preview_img.attr('src', gmb_dummy_img_url);
                        preview_img.show();

                        // if (true === result.status) {
                        // }
                    }
                );
            },
            onSuccess:function(files, data, xhr, pd) {

                // files - an array of user provided files' name
                // data - an object with status and filename attributes
                // pd - an object containing progress bar data

                if (false === data.status) {
                    swal({
                        title: '<?php echo $this->lang->line('Error!'); ?>',
                        text: data.message,
                        icon: 'error',
                        button: '<?php echo $this->lang->line('Ok'); ?>'
                    });

                    exit;
                }

                if (true === data.status) {
                    var file_type = findFileType(data.filename),
                        preview_block = $('.post_preview_block'),
                        preview_img = $('.preview_img'),
                        media_url = base_url + 'upload/xerobiz/media/' + data.filename;

                        console.log(file_type);

                    if ('image' === file_type) {
                        // Removes video if there is any
                        preview_block.find('video').remove();

                        // Hides demo image first
                        preview_img.hide();

                        // Updates src of image and displays it
                        preview_img.attr('src', media_url);
                        preview_img.show();
                        
                    } else if ('video' === file_type) {
                        var mime_type = window.xit_gmb_file_mime_type;
                        var vid = '<video controls width="100%" height="auto">';
                            vid += '<source src="' + media_url + '" type="' + mime_type + '">';
                            vid += 'Your browser does not support the video tag.';
                            vid += '</video>';
                        preview_img.hide();
                        preview_block.prepend(vid);
                    }

                }
            },
            onError: function(files, status, errMsg, pd) {

                //files: list of files
                //status: error status
                //errMsg: error message

                swal({
                    title: '<?php echo $this->lang->line('Info!'); ?>',
                    text: errMsg,
                    icon: 'info',
                    button: '<?php echo $this->lang->line('Ok'); ?>'
                });
            }
        });

        // Submits form data
        $(document).on('click','#create_media_campaign',function() {

            $(this).addClass('btn-progress');
            var that = $(this);

            var formData = new FormData($("#auto_media_poster_form")[0]);

            $.ajax({
                type:'POST' ,
                url: '<?php echo base_url('gmb/handle_media_campaign'); ?>',
                data: formData,
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                success:function(response) {
                    $(that).removeClass('btn-progress');
                    if (false === response.status) {
                        if (response.message) {
                            swal({
                                title:'<?php echo $this->lang->line("Error!"); ?>',
                                text: response.message,
                                icon:'error'
                            });
                            return;
                        }

                        var error_content = '';
                        if (response.errors) {
                            for (var error_item of Object.values(response.errors)) {
                                error_content += '<span class="d-block">' + error_item + '</span>';
                            }

                            var span = document.createElement("span");
                            span.innerHTML = error_content;
                            swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span, icon:'error'});
                        }
                    } else if (true === response.status) {
                        if (response.message) {
                            swal({
                                title: '<?php echo $this->lang->line("Success!"); ?>',
                                text: response.message,
                                icon: "success",
                                button: '<?php echo $this->lang->line("Ok"); ?>',
                            })
                            .then((willDelete) => {
                                if (willDelete) {
                                    window.location.href = base_url + 'gmb/media_campaigns';
                                }
                            });
                        }
                    }
                }
            });
        });
    });
</script>

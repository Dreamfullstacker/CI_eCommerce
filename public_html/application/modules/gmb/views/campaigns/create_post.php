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
        /*		box-shadow: 0px 0px 2px #ddd;
        -webkit-box-shadow: 0px 0px 2px #ddd;
        -moz-box-shadow: 0px 0px 2px #ddd;*/
        padding: 10px;
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
    .ajax-upload-dragdrop{width:100% !important;border: 2px dashed #dadada;}
</style>

<?php
    if($this->session->userdata("user_type")=="Admin" || in_array(74,$this->module_access)) {
        $like_comment_Share_reply_block_class = "";
    } else {
        $like_comment_Share_reply_block_class = "hidden";
    }
?>
<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href='<?php echo base_url("gmb/posts"); ?>'><?php echo $this->lang->line("Post campaigns"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-12 col-md-7 collef">
                <div class="card main_card">
                    <div class="card-header" style="border-bottom: 0;padding-bottom:0 !important;">
                        <ul class="nav nav-tabs" role="tablist" style="width:100% !important">
                            <li class="nav-item">
                                <a id="cta_post" class="nav-link post_type active" data-toggle="tab" href="#ctaPost" role="tab" aria-selected="false">
                                    <i class="fas fa-file-alt"></i>
                                    <?php echo $this->lang->line('CTA') ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a id="event_post" class="nav-link post_type" data-toggle="tab" href="#eventPost" role="tab" aria-selected="true">
                                    <i class="fas fa-link"></i>
                                    <?php echo $this->lang->line("EVENT") ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a id="offer_post" class="nav-link post_type" data-toggle="tab" href="#offerPost" role="tab" aria-selected="false">
                                    <i class="fas fa-image"></i>
                                    <?php echo $this->lang->line("OFFER"); ?>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- starts card-body -->
                    <div class="card-body" style="padding-top:0 !important;margin-top: -3px;">

                        <!-- starts tab-content -->
                        <div class="tab-content" id="post_tab_content">

                            <!-- starts form -->
                            <form action="<?php echo base_url('gmb/create_campaign'); ?>" enctype="multipart/form-data" id="auto_poster_form" method="post">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('Campaign Name');?></label>
                                    <input type="input" class="form-control"  name="campaign_name" id="campaign_name">
                                </div>

                                <!-- starts cta_block -->
                                <div id="cta_block">
                                    <div class="form-group">
                                        <label>
                                            <?php echo $this->lang->line('Action Type'); ?>
                                        </label>
                                        <?php
                                            if (isset($actionTypes) && count($actionTypes)) {
                                                echo form_dropdown(
                                                    'cta_action_type',
                                                    $actionTypes,
                                                    null,
                                                    'class="form-control select2" id="cta_action_type" required style="width:100%;"'
                                                );
                                            }
                                        ?>
                                        <span id="cta_action_info" class="d-none text-muted small"><?php echo $this->lang->line('The number that you used to register on Google My Business will be set in the Call Now button.'); ?></span>
                                    </div>
                                    <div id="cta_action_box" class="form-group">
                                        <label for="cta_action_url"><?php echo $this->lang->line('Action URL: '); ?></label>
                                        <input type="url" name="cta_action_url" id="cta_action_url" class="form-control">
                                    </div>
                                </div>
                                <!-- ends cta_block -->

                                <!-- starts event_block -->
                                <div id="event_block" class="d-none">
                                    <div class="form-group">
                                        <label for="event_post_title"><?php echo $this->lang->line('Post Title: '); ?></label>
                                        <input type="text" name="event_post_title" id="event_post_title" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="start_date_time"><?php echo $this->lang->line('Date Range'); ?></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-calendar"></i>
                                                </div>
                                            </div>
                                            <input type="text" name="start_date_time" id="start_date_time" class="form-control datepicker_x" placeholder="Start date">
                                            <div class="input-group-append">
                                                <div class="input-group-text"><?php echo $this->lang->line('To'); ?></div>
                                            </div>
                                            <input type="text" name="end_date_time" id="end_date_time" class="form-control datepicker_x" placeholder="End date">
                                        </div>
                                    </div>
                                </div>
                                <!-- ends event_block -->

                                <!-- starts offer_block -->
                                <div id="offer_block" class="d-none">
                                    <div class="form-group">
                                        <label for="offer_coupon_code"><?php echo $this->lang->line('Coupon Code: '); ?></label>
                                        <input type="text" name="offer_coupon_code" id="offer_coupon_code" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="offer_redeem_url"><?php echo $this->lang->line('Redeem URL: '); ?></label>
                                        <input type="text" name="offer_redeem_url" id="offer_redeem_url" class="form-control">
                                    </div>
                                </div>
                                <!-- ends offer_block -->

                                <div id="message_textarea" class="form-group">
                                    <label><?php echo $this->lang->line('Summary'); ?></label>
                                    <a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("support Spintax"); ?>, Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}">
                                        <i class='fa fa-info-circle'></i>
                                    </a>
                                    <textarea class="form-control" name="message" id="message" maxlength="500" placeholder="<?php echo $this->lang->line('Type summery here...');?>"></textarea>
                                </div>

                                <div class="form-group">
                                    <label><?php echo $this->lang->line('Media URL');?> <a href="#" data-placement="top" data-html="true" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Photo guidelines") ?>" data-content="<?php echo $this->lang->line("Your photos look best on Google if they meet the following standards:<br><br><strong>Format</strong>: JPG or PNG.<br><strong>Size</strong>: Between 10 KB and 5 MB.</br><strong>Recommended resolution</strong>: 720 px tall, 720 px wide.<br><strong>Minimum resolution</strong>: 250 px tall, 250 px wide.") ?>"><i class='fa fa-info-circle'></i></a></label>
                                    <input class="form-control" name="media_url" id="media_url" type="text" readonly>
                                </div>
                                <div class="form-group">
                                    <div id="media_url_upload"><?php echo $this->lang->line('Upload');?></div>
                                    <br/>
                                </div>

                                <!-- location name -->
                                <div class="form-group">
                                    <label>
                                        <?php echo $this->lang->line('Location Name'); ?>
                                    </label>
                                    <?php
                                        echo form_dropdown(
                                        'location_name[]',
                                        $locations,
                                        null,
                                        'class="form-control select2" id="location_name" style="width:100%;" multiple'
                                        );
                                    ?>
                                </div>
                                <!-- ends location name -->

                                <!-- starts posting time section -->
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line("Posting Time") ?>
                                                <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Posting Time") ?>" data-content="<?php echo $this->lang->line("If you schedule a campaign, system will automatically process this campaign at mentioned time and time zone. Schduled campaign may take upto 1 hour longer than your schedule time depending on server's processing.") ?>"><i class='fa fa-info-circle'></i></a>
                                            </label><br>
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
                                                    $time_zone[''] = $this->lang->line('Please Select');
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
                                    <input type="hidden" name="submitted_post_type" id="submitted_post_type" value="cta_post">
                                    <button class="btn btn-lg btn-primary" id="submit_post" name="submit_post" type="button"><i class="fas fa-paper-plane"></i>
                                        <?php echo $this->lang->line("Create Campaign") ?>
                                    </button>
                                    <a class="btn btn-lg btn-light float-right" onclick='goBack("gmb/posts", 0)'>
                                        <i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?>
                                    </a>
                                </div>
                                <!-- ends create campaign button -->

                            </form>
                            <!-- ends form -->
                        </div>
                        <!-- ends tab-content -->
                    </div>
                    <!-- ends card-body -->
                </div>
            </div>

            <!-- preview section -->
            <div class="col-12 col-md-5 colmid d-none d-sm-block">
                <div class="card main_card gmb-preview">
                    <div class="card-header">
                        <h4><i class="fab fa-google"></i> <?php echo $this->lang->line('Preview'); ?></h4>
                    </div>
                    <div class="card-body">
                        <!-- starts post_preview -->
                        <div class="post_preview">
                            <div class="post_preview_block">
                                <img src="<?php echo base_url('assets/images/demo_image.png'); ?>" class="preview_img" alt="No Image Preview">
                                <div class="preview_og_info">
                                    <div class="preview_og_info_title inline-block"></div>
                                    <div class="preview_og_info_date inline-block mb-2"></div>
                                    <div class="preview_og_info_desc inline-block"></div>
                                    <div class="preview_og_info_link inline-block"></div>
                                    <div class="preview_og_info_coupon inline-block d-none">
                                        <div class="preview_coupon_code large"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ends post_preview -->
                    </div>
                </div>
            </div>
            <!-- ends preview section -->
        </div>
    </div>
</section>

<script>
    $("document").ready(function()	{

        var gmb_dummy_img_url = "<?php echo base_url('assets/images/demo_image.png'); ?>";
        var emoji_message_div =	$("#message").emojioneArea({
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

        var base_url = "<?php echo base_url(); ?>";
        var makeScheduleValEmptyifscheduleisNow = $("input[name=schedule_type]:checked").val();
        if(makeScheduleValEmptyifscheduleisNow == 'now') {
            $("#schedule_time").val("");
        }

        // Preview message
        var message_pre =$('#message').val();
        message_pre = message_pre.replace(/[\r\n]/g, "<br />");
        if(message_pre) {
            message_pre = message_pre + '<br/><br/>';
            $('.preview_message').html(message_pre);
        }

        // Make post-tab global
        window.gmb_post_tab_type = 'cta_post';
            var link = $('.preview_og_info_link'),
            date_time = $('.preview_og_info_date'),
            desc = $('.preview_og_info_desc'),
            title = $('.preview_og_info_title'),
            coupon_box = $('.preview_og_info_coupon');

        $(document).on('click','#cta_post, #event_post, #offer_post', function(e) {
            e.preventDefault();

            // Gets the ID
            var post_type = $(this).attr('id');

            // Make post-tab global
            window.gmb_post_tab_type = post_type;

            // Sets value to hidden field
            $('#submitted_post_type').val(post_type)

            if('cta_post' === post_type) {
                // Hides unnecessary blocks
                $('#event_block')
                    .add('#offer_block')
                    .add(desc)
                    .add(date_time)
                    .add(coupon_box)
                    .addClass('d-none');

                // Displays CTA block
                $('#cta_block')
                    .add(link)
                    .add(title)
                    .removeClass('d-none');

                // Refills preview
                if ('CALL' !== window.gmb_cta_action_type) {
                    if (window.gmb_cta_action_url && window.gmb_cta_action_type) {
                        var button = generateButton(window.gmb_cta_action_url, window.gmb_cta_action_type, 'primary');
                        $(link).html(button);
                    }
                }

                if (window.gmb_post_summery) {
                    $(title).html(window.gmb_post_summery);
                }
            } else if('event_post' === post_type) {
                // Hides unnecessary blocks
                $('#cta_block')
                    .add('#offer_block')
                    .add(link)
                    .add(coupon_box)
                    .addClass('d-none');

                // Shows necessary blocks
                $('#event_block')
                    .add('#message_textarea')
                    .add(title)
                    .add(date_time)
                    .add(desc)
                    .removeClass('d-none');

                // Refills preview
                if (window.gmb_event_post_title) {
                    $(title).text(window.gmb_event_post_title);
                }

                if (window.gmb_post_summery) {
                    $(desc).text(window.gmb_post_summery);
                }

                if (window.gmb_start_date_time && window.gmb_end_date_time) {
                    $(date_time).find('span').remove();
                    $(date_time).append('<span class="text-muted small d-block text-left">' + window.gmb_start_date_time + ' - ' + window.gmb_end_date_time + '</span>')
                }

            } else if('offer_post' === post_type) {
                // Hides unnecessary blocks
                $('#cta_block')
                    .add('#event_block')
                    .add(desc)
                    .add(date_time)
                    .addClass('d-none');

                // Shows necessary blocks
                $('#offer_block')
                    .add(title)
                    .add(link)
                    .removeClass('d-none');

                // Refills preview
                if (window.gmb_offer_redeem_url) {
                    $(link).html(window.gmb_offer_redeem_url);
                }

                if (window.gmb_offer_coupon_code) {
                    $(coupon_box).removeClass('d-none');
                    $('.preview_coupon_code').text(window.gmb_offer_coupon_code);
                }

                if (window.gmb_post_summery) {
                    $(title).text(window.gmb_post_summery);
                }

                $('#submit_post').attr("submit_type","image_submit");
            }

            $(this).addClass("active");
        });

        function generateButton(url, name, type, block = false) {
            var state = block ? ' btn-block' : '';
            var button_text = ('CALL' === name) ? 'Call Now' : name;
            return '<a class="btn btn-' + type + '' + state + '" href="' + url + '" target="_blank">' + button_text + '</a>';
        }

        function htmlspecialchars(str) {
            if (typeof(str) == "string") {
                str = str.replace(/&/g, "&amp;"); /* must do &amp; first */
                str = str.replace(/"/g, "&quot;");
                str = str.replace(/'/g, "&#039;");
                str = str.replace(/</g, "&lt;");
                str = str.replace(/>/g, "&gt;");
            }
            return str;
        }

        $(document).on('keyup','.emojionearea-editor',function() {
            var message=$("#message").val();
            message=htmlspecialchars(message);
            message=message.replace(/[\r\n]/g, "<br />");

            if(message!="") {
                message=message+"<br/><br/>";
                $(".preview_message").html(message);
                $(".demo_preview").hide();
            }
        });

        $(document).on(
            'keyup change',
            '#cta_action_type, '
            + '#cta_action_url, '
            + '#event_post_title, '
            + '#start_date_time, '
            + '#end_date_time, '
            + '#offer_title, '
            + '#offer_coupon_code, '
            + '#offer_redeem_url,'
            + '.emojionearea-editor, '
            + '#schedule_type',
            function(e) {
                e.preventDefault();

                var elm = $(this)[0];

                // Handles CTA post preview
                if ('cta_action_type' === elm.id) {
                    window.gmb_cta_action_type = $(this).val();
                    var button = generateButton(window.gmb_cta_action_url, window.gmb_cta_action_type, 'primary');
                    $(link).html(button);

                    if ('CALL' === $(this).val()) {
                        $('#cta_action_box').addClass('d-none');
                        $('#cta_action_info').removeClass('d-none');
                    } else {
                        $('#cta_action_info').addClass('d-none');
                        $('#cta_action_box').removeClass('d-none');
                    }
                }

                if ('cta_action_url' === elm.id) {
                    window.gmb_cta_action_url = $(this).val();
                    var button = '<a class="btn btn-primary" href="' + window.gmb_cta_action_url + '" target="_blank">' + window.gmb_cta_action_type + '</a>';
                    $(link).html(button);
                }

                // Handles EVENT post preview
                if ('event_post_title' === elm.id) {
                    window.gmb_event_post_title = $(this).val();
                    $(title).text($(this).val());
                }

                if ('start_date_time' === elm.id) {
                    var start_date = moment($(this).val());
                    if (start_date.isValid()) {
                        window.gmb_start_date_time = start_date.format('MMM D hh:MMA');
                    }
                }

                if ('end_date_time' === elm.id) {
                    var end_date = moment($(this).val());
                    if (end_date.isValid()) {
                        window.gmb_end_date_time = end_date.format('MMM D HH:MM A');
                    }
                    if (window.gmb_start_date_time && window.gmb_end_date_time) {
                        $(date_time).find('span').remove();
                        $(date_time).append('<span class="text-muted small d-block text-left">' + window.gmb_start_date_time + '-' + window.gmb_end_date_time + '</span>')
                    }
                }

                // Handles OFFER post preview
                if ('offer_coupon_code' === elm.id) {
                    window.gmb_offer_coupon_code = $(this).val();
                    $(coupon_box).removeClass('d-none');
                    $('.preview_coupon_code').text(window.gmb_offer_coupon_code);
                }
                if ('offer_redeem_url' === elm.id) {
                    var redeem_text = '<?php echo $this->lang->line("Redeem Online"); ?>';
                    var redeem_url = '<a href="' + $(this).val() + '" target="_blank">' + redeem_text + '</a>';
                    window.gmb_offer_redeem_url = redeem_url;
                    $(link).html(redeem_url);
                }

                // Summery
                if ('emojionearea-editor' === elm.className) {
                    window.gmb_post_summery = elm.innerText;
                    if ('cta_post' === window.gmb_post_tab_type
                        || 'offer_post' === window.gmb_post_tab_type
                    ) {
                        $(title).text(elm.innerText);
                    } else {
                        $(desc).text(elm.innerText);
                    }
                }

                // Schedule
                if (false === $('#schedule_type').prop('checked')) {
                    $('#schedule-post-box').removeClass('d-none');
                } else {
                    $('#schedule-post-box').addClass('d-none');
                    $('#schedule_time').val('');
                    $('#time_zone').val('');
                }
            }
        );

        function findFileType(str) {
            var allowed_img_extension = ['.jpeg', '.jpg', '.png', '.gif'];
            var allowed_vid_extension = ['.flv', '.3gp', '.mp4', '.mov', '.avi', '.wmv'];
            var extension = str.substring(str.lastIndexOf('.'));

            var foundImg = allowed_img_extension.indexOf(extension);
            var foundVid = allowed_vid_extension.indexOf(extension);

            return (foundImg !== -1) ? 'image' : ((foundVid !== -1) ? 'video' : null);
        }

        // Uploads media
        $("#media_url_upload").uploadFile({
            url: base_url + "gmb/upload_post_media",
            fileName: 'xerobiz_file',
            maxFileSize: <?php echo $file_upload_limit; ?> * 1024 * 1024,
            showPreview: false,
            returnType: 'json',
            dragDrop: true,
            showDelete: true,
            multiple: false,
            maxFileCount: 1,
            acceptFiles: 'image/png,image/jpeg,image/gif',
            deleteCallback: function (data, pd) {
                var delete_url = "<?php echo site_url('gmb/delete_post_media'); ?>";
                $.post(delete_url, { op: 'delete', name: data },
                    function (resp, textStatus, jqXHR) {
                        if ('success' === textStatus) {
                            $('#media_url').val('');
                            $('.post_preview_block .preview_img').attr('src', gmb_dummy_img_url);
                            $('.post_preview_block .preview_img').show();
                        }
                    }
                );
            },
            onSuccess:function(files, data, xhr, pd) {
                var gmb_image_src = base_url + 'upload/xerobiz/' + data;
                $('#media_url').val(gmb_image_src);
                $('.post_preview_block .preview_img').attr('src', gmb_image_src);
            }
        });

        // Submits form data
        $(document).on('click','#submit_post',function() {

            $(this).addClass('btn-progress');
            var that = $(this);

            var formData = new FormData($("#auto_poster_form")[0]);

            console.log(formData);

            $.ajax({
                type:'POST' ,
                url: '<?php echo base_url('gmb/create_campaign'); ?>',
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
                                    window.location.href = base_url + 'gmb/posts';
                                }
                            });
                        }
                    }
                }
            });
        });
    });
</script>

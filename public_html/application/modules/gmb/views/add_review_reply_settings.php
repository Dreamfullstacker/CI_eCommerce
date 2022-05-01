<section class="section section_custom">

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card no_shadow">

                    <div class="card-body p-0 pt-2">
                        <form id="auto_reply_templete_form" action="#" method="post">
                            <input type="hidden" name="action_type" value="create">

                            <!-- rating block -->
                            <div class="form-group">
                                <label for="star_rating"> <?php echo $this->lang->line("Select rating")?> </label>
                                <select class="select2 form-control" id="star_rating" name="star_rating">
                                    <option value=""><?php echo $this->lang->line("Select rating"); ?></option>
                                    <option value="five_star"><?php echo $this->lang->line("5 Star"); ?></option>
                                    <option value="four_star"><?php echo $this->lang->line("4 Star"); ?></option>
                                    <option value="three_star"><?php echo $this->lang->line("3 Star"); ?></option>
                                    <option value="two_star"><?php echo $this->lang->line("2 Star"); ?></option>
                                    <option value="one_star"><?php echo $this->lang->line("1 Star"); ?></option>
                                </select>
                            </div>

                            <!-- offensive words -->
                            <div class="form-group" id="offensive_keywords_block">
                                <label for="offensive_keywords"> <?php echo $this->lang->line("Offensive keywords (press enter to separate words)")?>
                                </label>
                                <textarea id="offensive_keywords" name="offensive_keywords" class="form-control inputtags"></textarea>
                            </div>


                            <!-- generic and keyword state block -->
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for=""> <?php echo $this->lang->line('Reply type');?></label>
                                        <div class="custom-switches-stacked mt-2">
                                            <div class="row">
                                                <div class="col-6 col-md-4">
                                                    <label class="custom-switch">
                                                        <input type="radio" name="reply_type" value="generic" checked class="custom-switch-input">
                                                        <span class="custom-switch-indicator"></span>
                                                        <span class="custom-switch-description"><?php echo $this->lang->line("Generic"); ?></span>
                                                    </label>
                                                </div>
                                                <div class="col-6 col-md-4">
                                                    <label class="custom-switch">
                                                        <input type="radio" name="reply_type" value="keyword" class="custom-switch-input">
                                                        <span class="custom-switch-indicator"></span>
                                                        <span class="custom-switch-description"><?php echo $this->lang->line("Keyword"); ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- generic message block -->
                            <div class="form-group generic_message_block">
                                <label for="generic_message">
                                    <?php echo $this->lang->line("Message for generic reply.")?>
                                    <a href="#"
                                       data-placement="bottom"
                                       data-toggle="popover"
                                       data-trigger="focus"
                                       title="<?php echo $this->lang->line("Spintax"); ?>"
                                       data-content="Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"
                                    >
                                        <i class='fa fa-info-circle'></i>
                                    </a>  
                                </label>
                                <textarea id="generic_message" name="generic_message" class="form-control"></textarea>
                            </div>

                            <!-- reply message block -->
                            <div class="reply_settings_block">
                                <div class="card card-info single_card">
                                    <div class="card-header">
                                        <h4><?php echo $this->lang->line("Keyword"); ?></h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="keyword_settings"> <?php echo $this->lang->line("Keyword")?> </label>
                                            <input name="keyword_settings[]"  class="form-control keyword_word_input" type="text">
                                        </div>

                                        <div class="form-group">
                                            <label for="reply_settings">
                                                <?php echo $this->lang->line("Reply message")?>
                                                <a href="#"
                                                   data-placement="bottom"
                                                   data-toggle="popover"
                                                   data-trigger="focus"
                                                   title="<?php echo $this->lang->line("Spintax"); ?>"
                                                   data-content="Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"
                                                >
                                                    <i class='fa fa-info-circle'></i>
                                                </a> 
                                            </label>
                                            <textarea name="reply_settings[]" class="form-control" id="reply_settings"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="clearfix add_more_button_block">
                                    <input type="hidden" id="content_block" value="1">
                                    <input type="hidden" id="odd_or_even" value="odd">
                                    <button class="btn btn-outline-primary float-right" id="add_more_keyword_button"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add more settings') ?></button>
                                </div>

                                <div class="form-group">
                                    <label for="not_found_reply_settings">
                                        <?php echo $this->lang->line("Message for no match")?>
                                        <a href="#"
                                           data-placement="bottom"
                                           data-toggle="popover"
                                           data-trigger="focus"
                                           title="<?php echo $this->lang->line("Spintax"); ?>"
                                           data-content="Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"
                                        >
                                            <i class='fa fa-info-circle'></i>
                                        </a>
                                    </label>
                                    <textarea id="not_found_reply_settings" name="not_found_reply_settings" class="form-control"></textarea>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="card-footer">
                        <button id="create_template" type="button" class="btn btn-lg btn-primary"><i class="fa fa-save"></i> <?php echo $this->lang->line('Add Settings'); ?></button>
                        <a href="<?php echo base_url('gmb/add_settings'); ?>" class="btn btn-lg float-right btn-secondary cancel_template" ><i class="fas fa-times"></i> <?php echo $this->lang->line('Cancel'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    $(document).ready(function() {

        $("#generic_message, #reply_settings, #not_found_reply_settings").emojioneArea({
            autocomplete: false,
            pickerPosition: "bottom",
            // hideSource: false,
        });

        $(document).on("keypress", "#offensive_keywords", function(event) {
            if(event.which == 13) event.preventDefault();
        });

        $(".inputtags").tagsinput('items');
        $("#offensive_keywords_block").hide();
        $(".reply_settings_block").hide();

        $(document).on('click', '#delete_offensive_comment', function(event) {
            if (!this.checked) {
                $("#offensive_keywords_block").hide();
            } else {
                $("#offensive_keywords_block").show();
            }
        });

        $(document).on('click', 'input[name="reply_type"]', function(event) {

            let checked_value = $('input[name="reply_type"]:checked').val();

            if (checked_value == 'generic') {

                $(".reply_settings_block").hide();
                $(".generic_message_block").show();
            } else if (checked_value == 'keyword') {

                $(".generic_message_block").hide();
                $(".reply_settings_block").show();
            }
        });

        /* keyword message section start */
        $(document).on('click', '#add_more_keyword_button', function(event) {
            event.preventDefault();

            var content_amount = parseInt($("#content_block").val(), 10);
            
            if (content_amount < 20) {

                $("#content_block").val(content_amount + 1);

                var current_block = $("#odd_or_even").val();
                var card_class = '';
                var next_block = '';

                if (current_block == 'odd') {
                    card_class = 'card-primary';
                    next_block = 'even';
                }
                else if (current_block == 'even') {
                    card_class = 'card-info';
                    next_block = 'odd';
                }

                var div_string = '<div class="card ' + card_class + ' single_card">';
                    div_string += '<div class="card-header justify-content-between">';
                    div_string += '<h4><?php echo $this->lang->line("Keyword"); ?></h4>';
                    div_string += '<div>';
                    div_string += '<button class="btn btn-outline-secondary remove_div">';
                    div_string += '<i class="fas fa-times"></i>&nbsp;';
                    div_string += '<?php echo $this->lang->line('Remove'); ?>';
                    div_string += '</button>';
                    div_string += '</div>';
                    div_string += '</div>';
                    div_string += '<div class="card-body">';
                    div_string += '<div class="form-group">';
                    div_string += '<label for="keyword_settings">';
                    div_string += '<?php echo $this->lang->line("Keyword")?>';
                    div_string += '</label>';
                    div_string += '<input name="keyword_settings[]" class="form-control keyword_word_input" type="text">';
                    div_string += '</div>';
                    div_string += '<div class="form-group">';
                    div_string += '<label for="reply_settings">';
                    div_string += '<?php echo $this->lang->line("Reply message")?>';
                    div_string += '<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Spintax"); ?>" data-content="Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}" > <i class="fa fa-info-circle"></i></a>'; 
                    div_string += '</label>';
                    div_string += '<textarea name="reply_settings[]" class="form-control" id="reply_settings_' + content_amount + '"></textarea>';
                    div_string += '</div>';
                    div_string += '</div>';
                    div_string += '</div>';
                    div_string += '</div>';

                $(".add_more_button_block").before(div_string);
                $("#odd_or_even").val(next_block);

                $("#reply_settings_" + content_amount).emojioneArea({autocomplete:false,pickerPosition:"bottom"});
            }
            else {
                $("#add_more_keyword_button").attr('disabled', 'true');
            }
        });

        $(document).on('click', '.remove_div', function(event) {
            event.preventDefault();

            var parent_div = $(this).parent().parent().parent();
            $(parent_div).remove();

            var content_amount = parseInt($("#content_block").val(), 10);
            $("#content_block").val(content_amount - 1);
            $("#add_more_keyword_button").removeAttr('disabled');
        });
        /* keyword message section end */

        $(document).on('click', '.cancel_template', function(event) {
            event.preventDefault();

            swal({
                title: '<?php echo $this->lang->line("Are you sure?"); ?>',
                text: '<?php echo $this->lang->line("Do you really want to cancel this template?"); ?>',
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    window.location.href = '<?php echo base_url('gmb/review_replies'); ?>';
                }
            });
        });


        $(document).on('click', '#create_template', function(event) {
            event.preventDefault();

            $(this).addClass('btn-progress');
            var that = $(this);

            let form_data = new FormData($("#auto_reply_templete_form")[0]);

            $.ajax({
                url: '<?php echo base_url('gmb/save_review_reply'); ?>',
                type: 'POST',
                dataType: 'json',
                cache: false,
                processData: false,
                contentType: false,
                data: form_data,
                success: function(response) {
                    $(that).removeClass('btn-progress');
                    if (false === response.status) {
                        if(response.html == 'yes')
                        {
                            var span = document.createElement("span");
                            span.innerHTML = response.message;
                            swal({ title:'<?php echo $this->lang->line("Warning!"); ?>', content:span,icon:'warning'});
                            return;
                        }
                        else
                        {
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
                        }
                    } else if (true === response.status) {
                        if (response.message) {
                            swal({
                                title: '<?php echo $this->lang->line('Success!'); ?>',
                                text: response.message,
                                icon: 'success',
                                buttons: true,
                            })
                            .then((willDelete) => {
                                if (willDelete) {
                                    window.location.href = '<?php echo base_url('gmb/review_replies'); ?>';
                                }
                            });
                        }
                    }
                }
            });
        });
    });
</script>
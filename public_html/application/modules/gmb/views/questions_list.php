<style>
    .dropright .dropdown-toggle::after {
        display: none;
    }
    .media .avatar-item .answer-to-question-box {
        width: 24px;
        height: 24px;
        bottom: -32px;
        left: 24px;
        position: absolute;
        display: flex;
        text-align: center;
        justify-content: center;
        align-items: center;
    }
    .media .avatar-item .answer-to-question-box a {
        width: 24px;
        height: 24px;
        font-size: 24px;
    }
    .question-info { line-height: 16px; }
</style>
<section class="section section_custom">
    <div class="section-body">
        <div class="row">
            <div id="right_column" class="col-12 col-md-12 col-lg-12 colrig">
                <div class="card main_card no_shadow">
                    <div class="card-header p-0 pt-3">
                        <div>
                            <p class="question-info text-muted text-center"><?php echo $this->lang->line('Question & answer report may take upto few minutes/hours to update & synchronize here.'); ?></p>
                        </div>
                        <div class="col-12 padding-0">
                            <input type="text" class="form-control float-right" onkeyup="search_in_ul(this,'post_list_ul')" placeholder="Search...">
                        </div>
                    </div>
                    <div class="card-body p-0 pt-4">
                        
                        <div class="text-center" id="sync_commenter_info_response"></div>
                        <ul class="list-unstyled list-unstyled-border makeScroll" id="post_list_ul" style="max-height:700px;overflow-y: auto;">
                            <?php if (count($questions)): ?>
                                <?php foreach ($questions as $key => $question): ?>
                                    <li class="media">
                                        <div class="avatar-item mr-3">
                                            <?php
                                                $isAnswered = isset($question['answers']) && count($question['answers']) ? true : false;
                                                $answerTitle = $isAnswered ? $this->lang->line('Update answer') : $this->lang->line('Answer to Question');
                                            ?>
                                            <?php if (! empty($question['profilePhotoUrl'])): ?>
                                                <img alt="image" src="<?php echo $question['profilePhotoUrl']; ?>" width="70" height="70" style="border:1px solid #eee;" data-toggle="tooltip" title="">
                                            <?php else: ?>
                                                <img alt="image" src="<?php echo base_url('upload/xerobiz/dummy_author.jpg'); ?>" width="70" height="70" style="border:1px solid #eee;" data-toggle="tooltip" title="">
                                            <?php endif; ?>
                                            <div class="dropdown dropright avatar-badge">
                                                <span class="dropdown-toggle set_cam_by_post pointer blue" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-cog"></i>
                                                </span>
                                                <div class="dropdown-menu large">
                                                    <a
                                                        id="answer-to-question-link"
                                                        class="dropdown-item pointer edit_reply_info text-primary"
                                                        data-question-id="<?php echo $question['name']; ?>"
                                                        data-question-text="<?php echo htmlspecialchars($question['text']); ?>"
                                                        data-toggle="tooltip"
                                                        data-original-title="<?php echo $answerTitle; ?>"
                                                    >
                                                        <i class="fa fa-edit"></i> <span id="question-answer-title"><?php echo $answerTitle; ?></span>
                                                    </a>
                                                    <?php if ($isAnswered): ?>
                                                        <a
                                                            id="delete-question-answer-link"
                                                            class="dropdown-item pointer edit_reply_info text-danger"
                                                            data-question-id="<?php echo $question['name']; ?>"
                                                            data-question-text="<?php echo htmlspecialchars($question['text']); ?>"
                                                            data-toggle="tooltip"
                                                            data-original-title="<?php echo $this->lang->line('Delete answer'); ?>"
                                                        >
                                                            <i class="fa fa-trash-alt"></i> <?php echo $this->lang->line('Delete answer'); ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="media-body">
                                            <div class="d-block">
                                                <strong><?php echo $question['displayName']; ?></strong>
                                            </div>
                                            <div class="media-title d-inline">
                                                <?php echo $question['text'] ?>&nbsp;
                                                <span class="text-small text-muted d-inline">
                                                    <i class="fas fa-clock"></i>&nbsp;
                                                    <?php echo date('M j, Y', strtotime($question['createTime'])); ?>
                                                </span>
                                            </div>
                                            <?php if (count($question['answers'])): ?>
                                                <ul class="list-unstyled list-unstyled-border mt-4 ml-4">
                                                    <?php foreach ($question['answers'] as $answer): ?>
                                                        <li class="media mb-0">
                                                            <div class="avatar-item mr-3">
                                                                <?php if (! empty($question['profilePhotoUrl'])): ?>
                                                                    <img alt="image" src="<?php echo $answer['profilePhotoUrl']; ?>" width="42" height="42" style="border:1px solid #eee;" data-toggle="tooltip" title="">
                                                                <?php else: ?>
                                                                    <img alt="image" src="<?php echo base_url('upload/xerobiz/dummy_author.jpg'); ?>" width="42" height="42" style="border:1px solid #eee;" data-toggle="tooltip" title="">
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="media-body">
                                                                <div class="d-block">
                                                                    <strong><?php echo $answer['displayName']; ?></strong>
                                                                </div>
                                                                <div class="media-title">
                                                                    <?php echo $answer['text'] ?>&nbsp;
                                                                    <span class="text-small text-muted d-inline">
                                                                    <i class="fas fa-clock"></i>&nbsp;
                                                                    <?php echo date('M j, Y', strtotime($answer['createTime'])); ?>
                                                                </span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" tabindex="-1" role="dialog" id="answer-to-question-modal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $this->lang->line("Answer to question"); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="answer-to-question-form">
                    <div class="form-group">
                        <label><?php echo $this->lang->line("Answer"); ?></label>
                        <textarea id="answer-to-question-message" class="form-control"></textarea>
                    </div>
                    <input type="hidden" id="question-id">
                    <input type="hidden" id="question-text">
                    <button type="submit" class="btn btn-primary btn-shadow" id="answer-to-question-submit"><?php echo $this->lang->line("Answer now"); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $("document").ready(function()	{
        var base_url = '<?php echo base_url(); ?>';

        $("#right_column .makeScroll").mCustomScrollbar({
            autoHideScrollbar:true,
            theme:"rounded-dark"
        });

        $(document).on('click', '#answer-to-question-link', function(e) {
            e.preventDefault();
            var question_id = $(this).data('question-id'),
                question_text = $(this).data('question-text');
            $('#question-id').val(question_id);
            $('#question-text').val(question_text);

            // Opens up modal
            $('#answer-to-question-modal').modal();
        });

        $(document).on('submit', '#answer-to-question-form', function(e) {
            e.preventDefault();

            // Starts spinner
            $('#answer-to-question-submit').addClass('btn-progress');

            // Gets form data
            var question_id = $('#question-id').val(),
                question_text = $('#question-text').val(),
                answer_to_question_message = $('#answer-to-question-message').val();

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: { question_id, question_text, answer_to_question_message },
                url: '<?php echo base_url('gmb/answer_to_question'); ?>',
                success: function(response) {
                    $('#answer-to-question-submit').removeClass('btn-progress');
                    if (false === response.status) {
                        if (response.message) {
                            swal({
                                title:'<?php echo $this->lang->line("Error!"); ?>',
                                text: response.message,
                                icon: 'error'
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
                        $('#answer-to-question-message').val('');
                        $('#answer-to-question-modal').modal('hide');
                        if (response.message) {
                            swal({
                                title: '<?php echo $this->lang->line("Success!"); ?>',
                                text: response.message,
                                icon: "success",
                                button: '<?php echo $this->lang->line("Ok"); ?>',
                            }).then(() => {
                                $('.xit-spinner').show();
                                setTimeout(function() {
                                    window.location.replace(base_url + 'gmb/question_list');
                                }, 12000);
                            });
                        }
                    }
                },
                error: function (xhr, xhrStatus, xhrError) {
                    $('#answer-to-question-submit').removeClass('btn-progress');
                    if ('string' === typeof xhrError) {
                        swal({
                            title: '<?php echo $this->lang->line('Error!'); ?>',
                            text: xhrError,
                            icon: 'error'
                        });
                    }
                }
            });

        });

        $(document).on('click', '#delete-question-answer-link', function(e) {
            e.preventDefault();

            // Makes reference to current object
            var that = this;

            // Gets form data
            var question_id = $(that).data('question-id');

            swal({
                title: '<?php echo $this->lang->line('Are you sure?'); ?>',
                text: '<?php echo $this->lang->line('The answer will be deleted from your google account'); ?>',
                icon: 'warning',
                buttons: true,
            }).then(yes => {
                if (yes) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        data: { question_id },
                        url: '<?php echo base_url('gmb/delete_question_answer'); ?>',
                        success: function(response) {
                            if (false === response.status) {
                                swal({
                                    title:'<?php echo $this->lang->line("Error!"); ?>',
                                    text: response.message,
                                    icon:'error'
                                });
                                return;
                            } else if (true === response.status) {

                                $(that).remove();
                                $('#question-answer-title').text('<?php echo $this->lang->line('Answer to Question'); ?>');

                                if (response.message) {
                                    swal({
                                        title: '<?php echo $this->lang->line("Success!"); ?>',
                                        text: response.message,
                                        icon: "success",
                                        button: '<?php echo $this->lang->line("Ok"); ?>',
                                    }).then(yes => {
                                        $('.xit-spinner').show();
                                        setTimeout(function() {
                                            window.location.replace(base_url + 'gmb/question_list');
                                        }, 12000);
                                    });

                                }
                            }
                        },
                        error: function (xhr, xhrStatus, xhrError) {
                            if ('string' === typeof xhrError) {
                                swal({
                                    title: '<?php echo $this->lang->line('Error!'); ?>',
                                    text: xhrError,
                                    icon: 'error'
                                });
                            }
                        }
                    });
                }
            });
        });
    });
</script>
<style>
    .dropright .dropdown-toggle::after {
        display: none;
    }
    .media .media-body .list-group .list-group-item .media-title {
        margin-top: -6px !important;
    }
</style>
<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("campaigns"); ?>"><?php echo $this->lang->line("Campaigns"); ?></a></div>
            <div class="breadcrumb-item"><a href='<?php echo base_url("gmb/question_list"); ?>'><?php echo $this->lang->line("Questions Campaign List"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>
    <div class="section-body">
        <div class="row">
            <div id="right_column" class="col-12 col-md-12 col-lg-12 colrig">
                <div class="card main_card">
                    <div class="card-header">
                        <div class="col-4 col-md-3 padding-0">
                            <input type="text" class="form-control float-right" onkeyup="search_in_ul(this,'post_list_ul')" placeholder="Search...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="makeScroll">
                            <div class="text-center" id="sync_commenter_info_response"></div>
                            <ul class="list-unstyled list-unstyled-border" id="post_list_ul">
                                <?php if (count($questions)): ?>
                                    <?php foreach ($questions as $key => $question): ?>
                                        <li class="media">
                                            <div class="avatar-item mr-3">
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
                                                            class="pointer dropdown-item has-icon edit_reply_info text-primary"
                                                            data-question-id="<?php echo $question['name']; ?>"
                                                            data-question-text="<?php echo $question['text']; ?>"
                                                            data-toggle="modal"
                                                            data-target="#answer-to-question-modal"
                                                        >
                                                            <i class="fa fa-edit"></i> <?php echo $this->lang->line('Answer to Question'); ?>
                                                        </a>
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
                        $('#answer-to-question-message').val('');
                        $('#answer-to-question-modal').modal('hide');
                        if (response.message) {
                            swal({
                                title: '<?php echo $this->lang->line("Success!"); ?>',
                                text: response.message,
                                icon: "success",
                                button: '<?php echo $this->lang->line("Ok"); ?>',
                            })

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
    });
</script>
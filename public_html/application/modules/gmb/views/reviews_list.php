<style>
    .dropright .dropdown-toggle::after {
        display: none;
    }
    #main_review_content .waiting {height: 100%;width:100%;display: table;}
    #main_review_content .waiting i {font-size:60px;display: table-cell; vertical-align: middle;padding:30px 0;}
    .review-info { line-height: 16px; }
</style>
<section class="section section_custom">
    <div class="section-body">
        <div class="row">
            <div id="right_column" class="col-12 col-md-12 col-lg-12 colrig">
                <div class="card main_card no_shadow">
                    <div class="card-header p-0 pt-3">
                        <div>
                            <p class="review-info text-muted text-center"><?php echo $this->lang->line('Review report may take upto few minutes/hours to update & synchronize here.'); ?></p>
                        </div>
                        <div class="col-12 padding-0">
                            <input type="text" class="form-control float-right" onkeyup="search_in_ul(this,'post_list_ul')" placeholder="Search...">
                        </div>
                    </div>
                    <div class="card-body p-0 pt-4" id="main_review_content">
                        
                        <ul class="list-unstyled list-unstyled-border makeScroll" id="post_list_ul" style="max-height:700px;overflow-y: auto;">
                            <?php if (count($reviews)): ?>
                                <?php foreach ($reviews as $key => $review): ?>
                                    <li class="media">
                                        <div class="avatar-item mr-3">
                                            <?php if (! empty($review['profilePhotoUrl'])): ?>
                                                <img alt="image" src="<?php echo $review['profilePhotoUrl']; ?>" width="70" height="70" style="border:1px solid #eee;" data-toggle="tooltip" title="">
                                            <?php else: ?>
                                                <img alt="image" src="<?php echo base_url('upload/xerobiz/dummy_author.jpg'); ?>" width="70" height="70" style="border:1px solid #eee;" data-toggle="tooltip" title="">
                                            <?php endif; ?>
                                            <div class="dropdown dropright avatar-badge">
                                                <span class="dropdown-toggle pointer blue" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-cog"></i>
                                                </span>
                                                <div class="dropdown-menu large">
                                                    <a
                                                        class="pointer dropdown-item has-icon text-primary update-review-reply"
                                                        data-review-id="<?php echo isset($review['name']) ? $review['name'] : ''; ?>"
                                                        data-review-star="<?php echo isset($review['starRating']) ? $review['starRating'] : ''; ?>"
                                                        data-review-comment="<?php echo isset($review['comment']) ? $review['comment'] : ''; ?>"
                                                        data-location-name="<?php echo isset($review['locationName']) ? $review['locationName'] : ''; ?>"
                                                        data-display-name="<?php echo isset($review['displayName']) ? $review['displayName'] : ''; ?>"
                                                        data-profile-photo="<?php echo isset($review['profilePhotoUrl']) ? $review['profilePhotoUrl'] : ''; ?>"
                                                        data-toggle="tooltip"
                                                        data-original-title="<?php echo $this->lang->line("Update reply to review"); ?>"
                                                    >
                                                        <i class="fa fa-edit"></i> <?php echo $this->lang->line('Update review reply'); ?>
                                                    </a>
                                                    <a
                                                        class="pointer dropdown-item has-icon text-danger delete-review-reply"
                                                        data-toggle="tooltip"
                                                        title="<?php $this->lang->line("Delete review reply"); ?>"
                                                        data-review-id="<?php echo isset($review['name']) ? $review['name'] : ''; ?>"
                                                    >
                                                        <i class="fa fa-trash-alt"></i> <?php echo $this->lang->line('Delete review reply'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="media-body">
                                            <div class="media-title">
                                                <?php echo isset($review['displayName']) ? $review['displayName'] : $this->lang->line('Anonymous'); ?>
                                                &nbsp;
                                                <span class="text-small text-muted">
                                                    <i class="fas fa-clock"></i>&nbsp;
                                                    <?php echo date('M j, Y', strtotime($review['createTime'])); ?>
                                                </span>
                                            </div>
                                            <div class="media-title">
                                                <?php if ('FIVE' == $review['starRating']): ?>
                                                    <i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>
                                                <?php elseif ('FOUR' == $review['starRating']): ?>
                                                    <i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>
                                                <?php elseif ('THREE' == $review['starRating']): ?>
                                                    <i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>
                                                <?php elseif ('TWO' == $review['starRating']): ?>
                                                    <i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>
                                                <?php elseif ('ONE' == $review['starRating']): ?>
                                                    <i class="fas fa-star text-warning"></i>
                                                <?php endif; ?>
                                            </div>
                                            <span class="text-medium text-justify">
                                                <?php echo $review['comment']; ?>
                                            </span>
                                            <?php if (is_array($review['reviewReply']) && count($review['reviewReply'])): ?>
                                                <ul class="list-unstyled list-unstyled-border mt-4 ml-4">
                                                    <li class="media mb-0">
                                                        <div class="media-body">
                                                            <?php if (isset($review['reviewReply']['comment'])): ?>
                                                                <div class="d-block">
                                                                    <?php echo $review['reviewReply']['comment']; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if (isset($review['reviewReply']['updateTime'])): ?>
                                                                <span class="text-small text-muted d-inline">
                                                                    <i class="fas fa-clock"></i>
                                                                    &nbsp;&nbsp;
                                                                    <?php echo date('M j, Y', strtotime($review['reviewReply']['updateTime'])); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </li>
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

<div class="modal fade" tabindex="-1" role="dialog" id="update-review-reply-modal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?php echo $this->lang->line('Reply to review'); ?>
                    
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="update-review-reply-form">
                    <div class="form-group">
                        <label>
                            <?php echo $this->lang->line('Reply message'); ?>
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
                        <textarea id="review-reply-message" class="form-control"></textarea>
                    </div>
                    <input type="hidden" id="review-id">
                    <input type="hidden" id="review-star">
                    <input type="hidden" id="review-comment">
                    <input type="hidden" id="reviewer-location-name">
                    <input type="hidden" id="reviewer-display-name">
                    <input type="hidden" id="reviewer-profile-photo">
                    <input type="hidden" id="reply-type" value="location_manager_index">
                    <button type="submit" class="btn btn-primary btn-shadow" id="update-review-reply-submit"><?php echo $this->lang->line('Reply now'); ?></button>
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

        $("#review-reply-message").emojioneArea({
            autocomplete: false,
            pickerPosition: "bottom",
            // hideSource: false,
        });

        // Updates review reply
        $(document).on('click', '.update-review-reply', function(e) {
            e.preventDefault();
            var review_id = $(this).data('review-id'),
                review_star = $(this).data('review-star'),
                review_comment = $(this).data('review-comment'),
                reviewer_location_name = $(this).data('location-name'),
                reviewer_display_name = $(this).data('display-name'),
                reviewer_profile_photo = $(this).data('profile-photo');

            $('#review-id').val(review_id);
            $('#review-star').val(review_star);
            $('#review-comment').val(review_comment);
            $('#reviewer-location-name').val(reviewer_location_name);
            $('#reviewer-display-name').val(reviewer_display_name);
            $('#reviewer-profile-photo').val(reviewer_profile_photo);

            // Opens up modal
            $('#update-review-reply-modal').modal();
        });
        $(document).on('submit', '#update-review-reply-form', function(e) {
            e.preventDefault();

            // Starts spinner
            $('#update-review-reply-submit').addClass('btn-progress');

            // Gets form data
            var review_id = $('#review-id').val(),
                reply_type = $('#reply-type').val(),
                review_star = $('#review-star').val(),
                review_comment = $('#review-comment').val(),
                reviewer_location_name = $('#reviewer-location-name').val(),
                reviewer_display_name = $('#reviewer-display-name').val(),
                reviewer_profile_photo = $('#reviewer-profile-photo').val(),
                review_reply_message = $('#review-reply-message').val();

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: {
                    review_id,
                    reply_type,
                    review_star,
                    review_comment,
                    reviewer_location_name,
                    reviewer_display_name,
                    reviewer_profile_photo,
                    review_reply_message
                },
                url: '<?php echo base_url('gmb/reply_to_review'); ?>',
                success: function(response) {
                    $('#update-review-reply-submit').removeClass('btn-progress');
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
                        $('#review-reply-message').val('');
                        $('#update-review-reply-modal').modal('hide');

                        if (response.message) {
                            swal({
                                title: '<?php echo $this->lang->line("Success!"); ?>',
                                text: response.message,
                                icon: "success",
                                button: '<?php echo $this->lang->line('Ok'); ?>',
                            }).then(() => {
                                var waiting_div_content = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>';
                                $("#main_review_content").html(waiting_div_content)

                                setTimeout(function() {
                                    window.location.replace(base_url + 'gmb/review_list');
                                }, 12000);
                            });
                        }
                    }
                },
                error: function (xhr, xhrStatus, xhrError) {
                    $('#update-review-reply-submit').removeClass('btn-progress');
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

        // Deletes reviews
        $(document).on('click','.delete-review-reply',function(e) {
            e.preventDefault();
            swal({
                title: '<?php echo $this->lang->line("Are you sure?"); ?>',
                text: "<?php echo $this->lang->line('Do you really want to delete the review reply from google and database?'); ?>",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {

                    var review_id = $(this).data('review-id');

                    $.ajax({
                        context: this,
                        type: 'POST',
                        dataType: 'JSON',
                        data:{ review_id },
                        url: '<?php echo base_url('gmb/delete_reply_to_review'); ?>',
                        success:function(response) {
                            if (false === response.status) {
                                swal({
                                    title: '<?php echo $this->lang->line("Error!"); ?>',
                                    text: response.message,
                                    icon: "error",
                                })
                            } else if (true === response.status) {
                                swal({
                                    title: '<?php echo $this->lang->line("Success!"); ?>',
                                    text: response.message,
                                    icon: "success",
                                    button: '<?php echo $this->lang->line('Ok'); ?>',
                                }).then(() => {
                                    var waiting_div_content = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>';
                                    $("#main_review_content").html(waiting_div_content)

                                    setTimeout(function() {
                                        window.location.replace(base_url + 'gmb/review_list');
                                    }, 15000);
                                });
                            }
                        }
                    });
                }
            });
        });
    });
</script>
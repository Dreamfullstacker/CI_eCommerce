<?php $this->load->view('admin/theme/message'); ?>
<?php 
$location_table_id = $this->uri->segment(4);
$star_number = $this->uri->segment(3);
// $stars = ['five_star' => 'FIVE', 'four_star' => 'FOUR', 'three_star' => 'THREE', 'two_star' => 'TWO', 'one_star' => 'ONE'];
?>
<style>
    .dropdown-toggle::after{content:none !important;}
    .dropdown-toggle::before{content:none !important;}
    #searching{max-width: 30% !important;}
    #page_id{width: 150px !important;}
    #review_star{width: 110px !important;}
    @media (max-width: 575.98px) {
        #page_id{width: 130px !important;}
        #review_star{max-width: 105px !important;}
        #searching{max-width: 77% !important;}
    }
    .media-post-title {
        line-height: 22px;
        font-weight: normal !important;
    }
</style>

<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-list"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url('gmb'); ?>"><?php echo $this->lang->line('Google My Business'); ?></a></div>
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
                                        <select class="select2 form-control" id="review_star" name="review_star">
                                            <option value=""><?php echo $this->lang->line("All Stars"); ?></option>
                                            <option value="FIVE" <?php if($star_number == 'five_star') echo 'selected'; ?> ><?php echo $this->lang->line("5 Star"); ?></option>
                                            <option value="FOUR" <?php if($star_number == 'four_star') echo 'selected'; ?> ><?php echo $this->lang->line("4 Star"); ?></option>
                                            <option value="THREE" <?php if($star_number == 'three_star') echo 'selected'; ?> ><?php echo $this->lang->line("3 Star"); ?></option>
                                            <option value="TWO" <?php if($star_number == 'two_star') echo 'selected'; ?> ><?php echo $this->lang->line("2 Star"); ?></option>
                                            <option value="ONE" <?php if($star_number == 'one_star') echo 'selected'; ?> ><?php echo $this->lang->line("1 Star"); ?></option>
                                        </select>
                                    </div>

                                    <!-- search by page name -->
                                    <div class="input-group-prepend">
                                        <select class="select2 form-control" id="location_name" name="location_name">
                                            <option value=""><?php echo $this->lang->line("Location Name"); ?></option>
                                            <?php if (count($locations)): ?>
                                            <?php foreach ($locations as $key => $value): ?>
                                                <option value="<?php echo $value['id'];?>" <?php if($value['id'] == $location_table_id) echo 'selected'; ?> ><?php echo $value['location_display_name'];?></option>
                                            <?php endforeach ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <input type="text" class="form-control" id="searching" name="searching" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>" aria-label="" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" id="search_submit" title="<?php echo $this->lang->line('Search'); ?>" type="button"><i class="fas fa-search"></i> <span class="d-none d-sm-inline"><?php echo $this->lang->line('Search'); ?></span></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <a href="javascript:;" id="post_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> <?php echo $this->lang->line("Choose Date");?></a><input type="hidden" id="post_date_range_val">
                            </div>
                        </div>
                        <div class="table-responsive2">
                            <table class="table table-bordered" id="mytable">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line("ID"); ?></th>
                                    <th><?php echo $this->lang->line("Photo"); ?></th>
                                    <th><?php echo $this->lang->line("Name"); ?></th>
                                    <th><?php echo $this->lang->line("Star"); ?></th>
                                    <th><?php echo $this->lang->line("Comment"); ?></th>
                                    <th><?php echo $this->lang->line("Reply"); ?></th>
                                    <th><?php echo $this->lang->line("Actions"); ?></th>
                                    <th><?php echo $this->lang->line("Location Name"); ?></th>
                                    <th><?php echo $this->lang->line("Replied at"); ?></th>
                                    <th><?php echo $this->lang->line('Error'); ?></th>
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

<div class="modal fade" tabindex="-1" role="dialog" id="update-review-reply-modal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $this->lang->line("Reply to review") ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">               <span aria-hidden="true">×</span>             </button>
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
                    <input type="hidden" id="reply-type" value="review_report">
                    <button type="submit" class="btn btn-primary btn-shadow" id="update-review-reply-submit"><?php echo $this->lang->line('Reply now'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

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

        $("#review-reply-message").emojioneArea({
            autocomplete: false,
            pickerPosition: "bottom",
            // hideSource: false,
        });

        // datatable section started
        var perscroll;
        var table = $("#mytable").DataTable({
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 0, "desc" ]],
            pageLength: 10,
            ajax: {
                "url": base_url + 'gmb/review_report_data',
                "type": 'POST',
                data: function (d) {
                    d.location_name = $('#location_name').val();
                    d.review_star = $('#review_star').val();
                    d.searching = $('#searching').val();
                    d.post_date_range = $('#post_date_range_val').val();
                }
            },
            language: {
                url: "<?php echo base_url('assets/modules/datatables/language/' . $this->language . '.json'); ?>"
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [
                {
                    targets: [0],
                    visible: false
                },
                {
                    targets: [0,1,2,3,6,7,8],
                    className: 'text-center'
                },
                {
                    targets:[0,1,4,5,6,7,8,9],
                    sortable: false
                }
            ],
            fnInitComplete:function() {  // when initialization is completed then apply scroll plugin
                if(areWeUsingScroll) {
                    if (perscroll) perscroll.destroy();
                    perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
                }
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
                if(areWeUsingScroll) {
                    if (perscroll) perscroll.destroy();
                    perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
                }
            }
        });

        $(document).on('change', '#location_name', function(event) {
            event.preventDefault();
            table.draw();
        });

        $(document).on('change', '#review_star', function(event) {
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

        // Opens up review reply modal
        $(document).on('click','.update-review-reply',function(e) {
            e.preventDefault();

            var review_id = $(this).data('review-id'),
                review_star = $(this).data('review-star'),
                review_comment = $(this).data('review-comment'),
                reviewer_location_name = $(this).data('location-name');

            $('#review-id').val(review_id);
            $('#review-star').val(review_star);
            $('#review-comment').val(review_comment);
            $('#reviewer-location-name').val(reviewer_location_name);;

            // Opens up modal
            $('#update-review-reply-modal').modal();
        });

        // Updates review reply
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
                        table.draw();
                        if (response.message) {
                            swal({
                                title: '<?php echo $this->lang->line("Success!"); ?>',
                                text: response.message,
                                icon: "success",
                                button: '<?php echo $this->lang->line('Ok'); ?>',
                            })

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
                text: "<?php echo $this->lang->line('Do you really want to delete the reply to review from the database?'); ?>",
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
                                iziToast.error({
                                    title: '',
                                    message: response.message,
                                    position: 'bottomRight'
                                });
                            } else if (true === response.status) {
                                iziToast.success({
                                    title: '',
                                    message: response.message,
                                    position: 'bottomRight'
                                });
                                table.draw();
                            }
                        }
                    });
                }
            });
        });
    });
</script>

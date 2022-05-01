<?php $this->load->view('admin/theme/message'); ?>
<style>
    .dropdown-toggle::after{content:none !important;}
    .dropdown-toggle::before{content:none !important;}
    .text-decoration-none { text-decoration: none !important; }
    .article.article-style-c .article-details .article-category { text-transform:unset; }
    .text-transform-none{text-transform: none !important}
    #searching{max-width: 30% !important;}
    #page_id{width: 150px !important;}
    #post_type{width: 110px !important;}
    @media (max-width: 575.98px) {
        #page_id{width: 130px !important;}
        #post_type{max-width: 105px !important;}
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
        <div class="section-header-button">
            <a class="btn btn-primary" href="<?php echo base_url("gmb/create_post");?>">
                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create new post campaign"); ?>
            </a>
        </div>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("gmb"); ?>"><?php echo $this->lang->line("Google My Business"); ?></a></div>
            <div class="breadcrumb-item"><a href="<?php echo base_url("gmb/campaigns"); ?>"><?php echo $this->lang->line("Campaigns"); ?></a></div>
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
                                            <option value="cta_post"><?php echo $this->lang->line("CTA Post"); ?></option>
                                            <option value="event_post"><?php echo $this->lang->line("Event Post"); ?></option>
                                            <option value="offer_post"><?php echo $this->lang->line("Offer Post"); ?></option>
                                        </select>
                                    </div>

                                    <!-- search by page name -->
                                    <div class="input-group-prepend">
                                        <select class="select2 form-control" id="location_name" name="location_name">
                                            <option value=""><?php echo $this->lang->line("Location Name"); ?></option>
                                            <?php if (count($locations)): ?>
                                            <?php foreach ($locations as $key => $value): ?>
                                                <option value="<?php echo $value['location_display_name'];?>"><?php echo $value['location_display_name'];?></option>
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
                                    <th>#</th>
                                    <th><?php echo $this->lang->line("Campaign ID"); ?></th>
                                    <th><?php echo $this->lang->line("Campaign Name"); ?></th>
                                    <th><?php echo $this->lang->line("Post Type"); ?></th>
                                    <th><?php echo $this->lang->line("Post Title"); ?></th>
                                    <th><?php echo $this->lang->line("Actions"); ?></th>
                                    <th><?php echo $this->lang->line("Status"); ?></th>
                                    <th><?php echo $this->lang->line("Scheduled at"); ?></th>
                                    <th><?php echo $this->lang->line('Error Message'); ?></th>
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

<div class="modal fade" tabindex="-1" role="dialog" id="campaign-report-modal" aria-hidden="true">
    <div class="modal-dialog" role="document" style="min-width:30%;">
        <div class="modal-content">
            <div class="modal-body p-0" id="report_data"></div>
            <div class="modal-footer bg-whitesmoke">
                <button class="btn btn-light float-right" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line('Close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function($) {

        var base_url = '<?php echo base_url(); ?>';

        setTimeout(function() {
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

        var perscroll;
        var table = $("#mytable").DataTable({
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 1, "desc" ]],
            pageLength: 10,
            ajax: {
                "url": base_url + 'gmb/posts_data',
                "type": 'POST',
                data: function (d) {
                    d.location_name = $('#location_name').val();
                    d.post_type = $('#post_type').val();
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
                    targets: [1,8],
                    visible: false
                },
                {
                    targets: [0,2,3,4,6],
                    className: 'text-center'
                },
                {
                    targets:[0,5,7,8],
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


        $(document).on('click', '.campaign-report', function(event) {
            event.preventDefault();

            var post_id = $(this).data('post-id');
            var campaign_name = $(this).data('campaign-name');

            $("#report_data").html('<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i></div>');
            $("#campaign-report-modal").modal();

            $("#campaign-report-modal .modal-footer").attr('style', 'display:none !important');

            $.ajax({
                type: 'POST',
                data: { post_id },
                url: base_url + 'gmb/campaign_report',
                success:function(response){
                    $("#campaign-report-modal .modal-footer").attr('style', 'display:block !important');
                    $("#report_data").html(response)
                }
            })

        });

        $(document).on('click','.delete',function(e){
            e.preventDefault();
            swal({
                title: '<?php echo $this->lang->line("Are you sure?"); ?>',
                text: "<?php echo $this->lang->line('Do you really want to delete this post from the database?'); ?>",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var id = $(this).attr('id');
                    $.ajax({
                        context: this,
                        type: 'POST',
                        url: "<?php echo base_url('gmb/delete_post')?>",
                        data:{ id },
                        success:function(response) {
                            var res = JSON.parse(response);

                            if (false === res.status) {
                                iziToast.error({
                                    title: '',
                                    message: '<?php echo $this->lang->line("You do not have permission delete this campaign."); ?>',
                                    position: 'bottomRight'
                                });
                            } else if (true === res.status) {
                                iziToast.success({
                                    title: '',
                                    message: '<?php echo $this->lang->line("Campaign has been deleted successfully."); ?>',
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

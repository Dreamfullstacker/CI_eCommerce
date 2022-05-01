<?php $this->load->view('admin/theme/message'); ?>
<style>
    .dropdown-toggle::after{content:none !important;}
    .dropdown-toggle::before{content:none !important;}
    #searching{max-width: 30% !important;}
    #page_id{width: 150px !important;}
    @media (max-width: 575.98px) {
        #page_id{width: 130px !important;}
        #searching{max-width: 77% !important;}
    }
</style>

<section class="section section_custom">

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card no_shadow">
                    <div class="card-body data-card p-0 pr-2 pt-2">
                        <div class="row">
                            <div class="col-md-9 col-12">
                                <input type="hidden" name="location_id" id="location_id" value="<?php echo $location_table_id; ?>">
                            </div>
                            <div class="col-md-3 col-12 text-right">
                                <a class="btn btn-primary" href="<?php echo base_url("gmb/add_settings"); ?>">
                                    <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Add settings"); ?>
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive2">
                            <table class="table table-bordered" id="mytable">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line("#"); ?></th>
                                    <th><?php echo $this->lang->line("ID"); ?></th>
                                    <th><?php echo $this->lang->line("Star"); ?></th>
                                    <th><?php echo $this->lang->line("Action"); ?></th>
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

        // datatable section started
        var perscroll;
        var table = $("#mytable").DataTable({
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 1, "desc" ]],
            pageLength: 10,
            ajax: {
                "url": base_url + 'gmb/review_reply_data',
                "type": 'POST',
                data: function (d) {
                    d.location_id = $('#location_id').val();
                }
            },
            language: {
                url: "<?php echo base_url('assets/modules/datatables/language/' . $this->language . '.json'); ?>"
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [
                {
                    targets:[1],
                    visible: false
                },
                {
                    targets: [2,3],
                    className: 'text-center'
                },
                {
                    targets:[0,2,3],
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


        $(document).on('keyup', '#searching1', function(event) {
            event.preventDefault();
            table1.draw();
        });


        // End of reply table

        $(document).on('click','.delete',function(e){
            e.preventDefault();
            swal({
                title: '<?php echo $this->lang->line("Are you sure?"); ?>',
                text: '<?php echo $this->lang->line('Do you really want to delete this review reply settings from the database?'); ?>',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var star_rating = $(this).attr('id');
                    $.ajax({
                        context: this,
                        type: 'POST',
                        url: "<?php echo base_url('gmb/delete_star')?>",
                        data:{ star_rating },
                        success:function(response) {
                            var res = JSON.parse(response);

                            if (false === res.status) {
                                iziToast.error({
                                    title: '',
                                    message: res.message,
                                    position: 'bottomRight'
                                });
                            } else if (true === res.status) {
                                iziToast.success({
                                    title: '',
                                    message: res.message,
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
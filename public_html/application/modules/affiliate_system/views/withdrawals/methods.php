<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-calendar-plus"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-button">
            <a class="btn btn-primary add_method" href="#">
                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Method"); ?>
            </a> 
        </div>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("affiliate_system"); ?>"><?php echo $this->lang->line("Affiliate System"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive2 data-card">
                                    <table class="table table-bordered" id="mytable_withdrawal_method">
                                        <thead>
                                            <tr>
                                                <th>#</th>      
                                                <th><?php echo $this->lang->line("ID"); ?></th>
                                                <th><?php echo $this->lang->line("Method"); ?></th>
                                                <th><?php echo $this->lang->line('Created At'); ?></th>
                                                <th><?php echo $this->lang->line('Actions'); ?></th>
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
        </div>

    </div>
</section>


<script>

    /* Check Email valid or not from Email API section */
    function validateEmail(email) {
      var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/; 
      return regex.test(email);
    }
    
    $(document).ready(function($) {
        var base_url = '<?php echo base_url(); ?>';
        var withdrawal_method_perscroll;
        var withdrawal_method_table = $("#mytable_withdrawal_method").DataTable({
            serverSide: true,
            processing:true,
            bFilter: true,
            order: [[ 1, "desc" ]],
            pageLength: 10,
            ajax: 
            {
                "url": base_url+'affiliate_system/withdrawal_method_data',
                "type": 'POST',
                data: function ( d )
                {
                    d.method_search = $('#method_search').val();
                }
            },
            language: 
            {
              url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [
                {
                  targets: [1],
                  visible: false
                },
                {
                  targets: '',
                  className: 'text-center'
                },
                {
                  targets: '',
                  sortable: false
                }
            ],
            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
              if(areWeUsingScroll)
              {
                if (withdrawal_method_perscroll) withdrawal_method_perscroll.destroy();
                withdrawal_method_perscroll = new PerfectScrollbar('#mytable_withdrawal_method_wrapper .dataTables_scrollBody');
              }
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
              if(areWeUsingScroll)
              { 
                if (withdrawal_method_perscroll) withdrawal_method_perscroll.destroy();
                withdrawal_method_perscroll = new PerfectScrollbar('#mytable_withdrawal_method_wrapper .dataTables_scrollBody');
              }
            }
        });

        $(document).on('click', '.add_method', function(event) {
            event.preventDefault();

            $("#add_witdrawalMethod_modal").modal();
        });

        $(document).on('change', '#method_type', function(event) {
            event.preventDefault();

            var methodType = $("#method_type").val();

            if(methodType == 'paypal') {
                $("#paypal_email_div").css('display','block');
                $("#bank_acc_div").css('display','none');
            }

            if(methodType == 'bank_acc') {
                $("#paypal_email_div").css('display','none');
                $("#bank_acc_div").css('display','block');
            }

            if(methodType == '') {
                $("#paypal_email_div").css('display','none');
                $("#bank_acc_div").css('display','none');
            }
        });

        $(document).on('change', '#edit_method_type', function(event) {
            event.preventDefault();

            var methodType = $("#edit_method_type").val();

            if(methodType == 'paypal') {
                $("#edit_paypal_email_div").css('display','block');
                $("#edit_bank_acc_div").css('display','none');
            }

            if(methodType == 'bank_acc') {
                $("#edit_paypal_email_div").css('display','none');
                $("#edit_bank_acc_div").css('display','block');
            }

            if(methodType == '') {
                $("#edit_paypal_email_div").css('display','none');
                $("#edit_bank_acc_div").css('display','none');
            }
        });


        $(document).on('click', '#save_method_info', function(event) {
            event.preventDefault();

            var method_type = $("#method_type").val();
            var paypal_email = $("#paypal_email").val();
            var bank_acc_no = $("#bank_acc_no").val();

            if(method_type == '') {
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please Select Withdrawal Method"); ?>', 'warning');
                return;
            }

            if(method_type == 'paypal' && paypal_email == '') {
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide PayPal email address"); ?>', 'warning');
                return;
            }

            if(method_type == 'bank_acc' && bank_acc_no == '') {
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide bank account no"); ?>', 'warning');
                return;
            }

            $(this).addClass('btn-progress disabled');

            $.ajax({
                context:this,
                url: base_url+'affiliate_system/new_method',
                type: 'POST',
                data: {method_type: method_type,paypal_email: paypal_email,bank_acc_no: bank_acc_no},
                success:function(response) {

                    $(this).removeClass('btn-progress disabled');

                    if(response == '1') {
                        iziToast.success({title: '',message: '<?php echo $this->lang->line("Withdrawal Method has been added successfully"); ?>',position: 'bottomRight'});
                        $("#add_witdrawalMethod_modal").modal('hide');
                        $("#witdrawalMethod_add_form").trigger('reset');
                        $("#method_type").val('').trigger('change');
                    }
                    
                    if(response == '0') {
                        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Something Went wrong, please try once again."); ?>', 'warning');
                    }

                    withdrawal_method_table.draw();

                }
            })
            
        });


        $(document).on('click', '.edit_method', function(event) {
            event.preventDefault();

            $("#edit_witdrawalMethod_modal").modal();

            var method_id = $(this).attr("table_id");

            var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';
            $("#method_update_body").html(loading);

            $(".action_div").attr('style', 'display: none !important');

            $.ajax({
                url: base_url+'affiliate_system/get_method_info',
                type: 'POST',
                data: {table_id: method_id},
                success:function(response) {
                    $("#method_update_body").html(response);
                    $(".action_div").attr('style', 'display: block !important');
                }
            })

        });


        $(document).on('click', '#update_method_info', function(event) {
            event.preventDefault();

            var table_id = $("#table_id").val();
            var method_type = $("#edit_method_type").val();
            var paypal_email = $("#edit_paypal_email").val();
            var bank_acc_no = $("#edit_bank_acc_no").val();

            if(method_type == '') {
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please Select Withdrawal Method"); ?>', 'warning');
                return;
            }

            if(method_type == 'paypal' && paypal_email == '') {
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide PayPal email address"); ?>', 'warning');
                return;
            }

            if(method_type == 'bank_acc' && bank_acc_no == '') {
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide bank account no"); ?>', 'warning');
                return;
            }

            $(this).addClass('btn-progress disabled');

            $.ajax({
                context:this,
                url: base_url+'affiliate_system/update_method_info',
                type: 'POST',
                data: {table_id: table_id,method_type: method_type,paypal_email: paypal_email,bank_acc_no: bank_acc_no},
                success:function(response) {

                    $(this).removeClass('btn-progress disabled');

                    if(response == '1') {
                        iziToast.success({title: '',message: '<?php echo $this->lang->line("Withdrawal Method has been updated successfully"); ?>',position: 'bottomRight'});
                        $("#edit_witdrawalMethod_modal").modal('hide');
                        $("#witdrawalMethod_edit_form").trigger('reset');
                        $("#edit_method_type").val('').trigger('change');
                    }
                    
                    if(response == '0') {
                        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Something Went wrong, please try once again."); ?>', 'warning');
                    }

                    withdrawal_method_table.draw();

                }
            })
        });

        $("#add_witdrawalMethod_modal").on('hidden.bs.modal',function() {
            $("#witdrawalMethod_add_form").trigger('reset');
            $("#method_type").val('').trigger('change');
            withdrawal_method_table.draw();
        });

        $("#edit_witdrawalMethod_modal").on('hidden.bs.modal',function() {
            withdrawal_method_table.draw();
        });


        $(document).on('click','.delete_method',function(e){
          e.preventDefault();
          var id = $(this).attr('table_id');
          var somethingwentwrong = "<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>";

          swal({
            title: '<?php echo $this->lang->line("Delete Method"); ?>',
            text: '<?php echo $this->lang->line("Do you really want to delete this method?"); ?>',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) 
            {
                $(this).removeClass('btn-outline-danger');
                $(this).addClass('btn-progress btn-danger');

                $.ajax({
                  context: this,
                  type:'POST' ,
                  url: "<?php echo base_url('affiliate_system/delete_withdrawal_method')?>",              
                  data: {id:id},
                  success:function(response){ 

                     $(this).removeClass('btn-progress btn-danger');
                     $(this).addClass('btn-outline-danger');

                     if(response == '1')
                     {
                        iziToast.success({title: '',message: "<?php echo $this->lang->line('Camapign has been deleted successfully.')?>",position: 'bottomRight'});
                        withdrawal_method_table.draw();
                     }
                     else
                     {
                       swal('<?php echo $this->lang->line("Error"); ?>', somethingwentwrong, 'error');
                     }


                  }
                });
            } 
          });
        });

        $(document).on('click', '.method_details', function(event) {
            event.preventDefault();
            /* Act on the event */
            var method_name = $(this).attr("method_name");
            var details = $(this).attr("details");
            $("#method_name").html(method_name);
            $("#method_details").html(details);
            $("#method_details_modal").modal();

        });
    });
</script>



<div class="modal fade" id="method_details_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bbw">
                <h5 class="modal-title text-center blue">
                    <i class="fas fa-bars"></i> <?php echo $this->lang->line("Method Details"); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body section">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title text-small"><?php echo $this->lang->line('Method Name'); ?></div>
                        <div class="section-lead" id="method_name"></div>

                        <div class="section-title text-small"><?php echo $this->lang->line('Method Details'); ?></div>
                        <div class="section-lead">
                            <div class="alert alert-light" id="method_details"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_witdrawalMethod_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bbw">
                <h5 class="modal-title text-center blue">
                    <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Method"); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">                    
                        <form action="#" enctype="multipart/form-data" id="witdrawalMethod_add_form" method="post">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Method'); ?></label>
                                        <select name="method_type" id="method_type" class="form-control select2" style="width:100%;">
                                            <option value=""><?php echo $this->lang->line('Select Method'); ?></option>
                                            <option value="paypal"><?php echo $this->lang->line('PayPal'); ?></option>
                                            <option value="bank_acc"><?php echo $this->lang->line('Manual'); ?></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12" id="paypal_email_div" style="display: none;">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('PayPal Email'); ?></label>
                                        <input type="email" class="form-control" name="paypal_email" id="paypal_email">
                                    </div>
                                </div>

                                <div class="col-12" id="bank_acc_div" style="display: none;">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Details'); ?></label>
                                        <textarea class="form-control" name="bank_acc_no" id="bank_acc_no" placeholder="<?php echo $this->lang->line("write your details..."); ?>" style="height: 100px !important;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke">
                <div class="col-12 padding-0">
                    <button class="btn btn-primary" id="save_method_info" type="button"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save") ?> </button>
                    <a class="btn btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_witdrawalMethod_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bbw">
                <h5 class="modal-title text-center blue">
                    <i class="fas fa-edit"></i> <?php echo $this->lang->line("Update Method"); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" id="method_update_body"></div>

            <div class="modal-footer bg-whitesmoke action_div">
                <div class="col-12 padding-0">
                    <button class="btn btn-primary" id="update_method_info" type="button"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Update") ?> </button>
                    <a class="btn btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
                </div>
            </div>
        </div>
    </div>
</div>
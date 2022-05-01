<style>
    .group_search{padding: 21px 15px !important;}
    ::placeholder{color:#adadad !important;}
    .card-header{border-bottom-width:thin !important;}
    .text-decoration-none { text-decoration: none !important; }
    .pagination_div .pagination { justify-content: center !important; }
    .block_head {height: 100px;width: 100%;background: var(--blue);border-top-right-radius: 5px;border-top-left-radius: 5px;}
    .method_info span {margin: 0 auto;width: 100px;height: 100px;text-align: center;border-radius: 50%;margin-top: -65px;background: #fff;}
    .method_info span i {font-size: 50px;line-height:85px;}
    .details_section { border: 0.5px solid #eee; }
    .reques_info_body {line-height:24px !important;}
    .reques_info_body li {border: 0;padding:5px 15px;}
    .reques_info_body li .amount {font-size:18px;color:#000;font-weight: bold;font-family:emoji;}
    .info_sec button {padding: 9px;border-radius: 3px;width:30%;margin-top:0;}
    .request_method { margin-top: -30px;margin-bottom:12px;font-weight:600;font-size:16px;}
    .earning-value,.earning-text { font-family:cursive;font-weight: normal !important; }
</style>
<?php 
    $logo= isset($profile_img) ? $profile_img : "";
    if($logo=="") {
        $logo = file_exists("assets/img/avatar/avatar-1.png") ? base_url("assets/img/avatar/avatar-1.png") : "https://mysitespy.net/envato_image/avatar.png";
    }
    else 
        $logo = base_url().'upload/affiliator/'.$logo;
?>

<section class="section" id="main_body">
    <div class="section-header">
        <h1 class="page_title"><i class="fas fa-hands-helping"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-button new_request_button">
            <a class="btn btn-primary add_request" href="#">
                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Request"); ?>
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
                <div class="card profile-widget mb-0">
                    <div class="profile-widget-header">
                        <img alt="image" src="<?php echo $logo; ?>" class="rounded-circle profile-widget-picture ml-4">
                        <div class="profile-widget-items">
                            <div class="profile-widget-item">
                                <div class="profile-widget-item-value earning-value"><?php echo $curency_icon.$total_earned; ?></div>
                                <div class="profile-widget-item-label text-primary earning-text"><?php echo $this->lang->line('Earned'); ?></div>
                            </div>
                            <div class="profile-widget-item">
                                <div class="profile-widget-item-value earning-value"><?php echo $curency_icon.$pending_money; ?></div>
                                <div class="profile-widget-item-label text-danger earning-text"><?php echo $this->lang->line('Pending'); ?></div>
                            </div>
                            <div class="profile-widget-item">
                                <div class="profile-widget-item-value earning-value"><?php echo $curency_icon.$transfered_money; ?></div>
                                <div class="profile-widget-item-label text-success earning-text"><?php echo $this->lang->line('Transfered'); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body" id="table_div">
                        <div class="row">
                            <div class="col-12">
                                <form action="<?php echo base_url('affiliate_system/withdrawal_requests/'); ?>" method="post">
                                    <div class="form-group">
                                        <div class="input-group mb-3" id="searchbox">
                                            <select name="rows_number" class="select2 form-control" id="rows_number">
                                                <option value="10" <?php if ($per_page == 10) echo 'selected'; ?>><?php echo $this->lang->line('10 items'); ?></option>
                                                <option value="25" <?php if ($per_page == 25) echo 'selected'; ?>><?php echo $this->lang->line('25 items'); ?></option>
                                                <option value="50" <?php if ($per_page == 50) echo 'selected'; ?>><?php echo $this->lang->line('50 items'); ?></option>
                                                <option value="100" <?php if ($per_page == 100) echo 'selected'; ?>><?php echo $this->lang->line('100 items'); ?></option>
                                                <option value="500" <?php if ($per_page == 500) echo 'selected'; ?>><?php echo $this->lang->line('500 items'); ?></option>
                                                <option value="all" <?php if ($per_page == 'all') echo 'selected'; ?>><?php echo $this->lang->line('All items'); ?></option>
                                            </select>
                                            <select name="search_value" class="select2 form-control" id="search_value">
                                                <option value="" <?php if ($search_value == "") echo 'selected'; ?>><?php echo $this->lang->line('Status'); ?></option>
                                                <option value="0" <?php if ($search_value == "0") echo 'selected'; ?>><?php echo $this->lang->line('Pending'); ?></option>
                                                <option value="1" <?php if ($search_value == "1") echo 'selected'; ?>><?php echo $this->lang->line('Approved'); ?></option>
                                                <option value="2" <?php if ($search_value == "2") echo 'selected'; ?>><?php echo $this->lang->line('Canceled'); ?></option>
                                            </select>

                                            <div class="input-group-append" style="margin-top:-1px !important;">
                                                <button class="btn btn-primary no_radius" id="group_search_submit" type="submit"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <?php if(!empty($withdrawal_requests)) : ?>
                            <?php foreach ($withdrawal_requests as $value) { ?>
                                <div class="col-12 col-md-4">
                                    <div class="card pointer" data-toggle="tooltip" data-title="<?php echo $value['payment_type']; ?>">
                                        <div class="card-body p-0">
                                            <div class="block_head bg-primary"></div>
                                            <div class="details_section">
                                                <div class="d-flex method_info">
                                                    <span><?php echo $value['icon']; ?></span>
                                                </div>
                                                <div class="text-center request_method"><?php echo $value['payment_type']; ?></div>
                                                <ul class="list-group reques_info_body">
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span><i class="fas fa-bookmark text-primary"></i>&nbsp;&nbsp;<?php echo $value['method_id']; ?></span>
                                                        <span class="amount"><?php echo $curency_icon.$value['requested_amount']; ?></span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span><i class="fas fa-flag text-success"></i> <?php echo $this->lang->line('Status'); ?></span>
                                                        <?php echo $value['request_status_icon']; ?>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center mb-3">
                                                        <span><i class="fas fa-star text-warning"></i> <?php echo $this->lang->line('Approved'); ?></span>
                                                        <span class="text-muted text-small"><?php echo $value['completed_at']; ?></span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <?php if ($value['request_status'] != '1'): ?>
                                        <div class="card-footer text-center bg-light pr-3 pl-3 pt-2 pb-2">
                                            <?php if($value['request_status'] == '0') : ?>
                                            <button class="btn btn-sm btn-primary float-left edit_request" table_id="<?php echo $value['id']; ?>"><i class="fas fa-edit"></i> <?php echo $this->lang->line('Edit'); ?></button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-danger float-right delete_request" table_id="<?php echo $value['id']; ?>"><i class="fas fa-trash-alt"></i> <?php echo $this->lang->line('Delete'); ?></button>
                                            
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            <?php } ?>
                            <?php else : ?>
                                <div class="col-12">
                                    <div class="empty-state p-0">
                                      <img class="img-fluid" width="40%" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image">
                                      <h2 class="mt-0"><?php echo $this->lang->line("We could not find any data.");?></h2>

                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                        <div class="pagination_div">
                            <?php echo $page_links; ?>
                        </div>
                    </div>

                </div>

                <div class="card" id="new_form_div">

                    <div class="card-header">
                        <h4><i class="fas fa-paper-plane"></i> <span class="form_header_title"><?php echo $this->lang->line('New Request'); ?></span></h4>
                        <div class="card-header-action">
                            <a href="#" class="reverse_form"><i class="far fa-times-circle" style="font-size:18px;"></i></a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="#" id="new_requests_form" method="post">
                            <input type="hidden" name="tableId" id="tableId" value="">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend"><div class="input-group-text"><i class="fas fa-signature"></i></div></div>

                                    <select name="withdrawal_account" id="withdrawal_account" class="form-control select2">
                                        <option value=""><?php echo $this->lang->line('Select withdrawal Account'); ?></option>
                                        <?php foreach ($method_info as $value) {

                                            if($value['payment_type'] == 'paypal') {
                                                echo '<option value="'.$value['id'].'"> PayPal : '.$value['paypal_email'].'</option>';
                                            }
                                            else if($value['payment_type'] == 'bank_acc') {
                                                echo '<option value="'.$value['id'].'"> Manual : '.$value['bank_acc_no'].'</option>';
                                            }
                                        } ?>
                                    </select>
                                    <input type="number" class="form-control" placeholder="<?php echo $this->lang->line('Provide Requested Amount'); ?>" id="requested_amount" name="requested_amount">
                                    <input type="hidden" class="form-control" id="previous_amount" name="previous_amount" value="">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary float-left" id="add_request_submit" submit_action="add"><i class="fas fa-save"></i> <?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="method_details_modal">
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


<script>

    /* Check Email valid or not from Email API section */
    function validateEmail(email) {
      var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/; 
      return regex.test(email);
    }
    
    $(document).ready(function($) {
        var base_url = '<?php echo base_url(); ?>';
        var reversePageTitle = '<?php echo $page_title; ?>';
        var method_numbers = '<?php echo $count_method_info; ?>'

        $("#new_form_div").hide();

        $(document).on('click', '.add_request', function(event) {
            event.preventDefault();

            var method_url = base_url+"affiliate_system/withdrawal_method";
            var method_link = "<?php echo $this->lang->line('Sorry, we do not find any withdrawal methods. Please add atleast one method to issue a request. create method from'); ?>"+" "+"<a target='_BLANK' href='"+method_url+"'><?php echo $this->lang->line('here'); ?></a>";

            if(method_numbers == 0) {

                var span = document.createElement("span");
                span.innerHTML = method_link;

                swal({ title:'<?php echo $this->lang->line("Warning"); ?>', content:span,icon:'warning'});
                return false;
            }

            $("#table_div").hide(500);
            $("#new_form_div").show(500);
            $(".new_request_button").hide();
            $(".form_header_title").html('<?php echo $this->lang->line("New Request"); ?>');
        });

        $(document).on('click', '.reverse_form', function(event) {
            event.preventDefault();

            $("#table_div").show(500);
            $("#new_form_div").hide(500);
            $(".new_request_button").show();
            $("#new_requests_form").trigger('reset');
            $("#withdrawal_account").val('').trigger("change");
            $("#tableId").val("");
        });


        $(document).on('click', '.edit_request', function(event) {
            event.preventDefault();

            $("#table_div").hide(500);
            $("#new_form_div").show(500);
            $(".new_request_button").hide();
            $("#add_request_submit").attr('submit_action', 'edit');
            var tableid = $(this).attr('table_id');
            $(".form_header_title").html('<?php echo $this->lang->line("Edit Request"); ?>');

            $.ajax({
                url: base_url+'affiliate_system/get_requests_info',
                type: 'POST',
                data: {table_id: tableid},
                dataType: "json",
                success:function(response) {

                    $("#tableId").val(tableid);
                    $("#withdrawal_account option[value='"+response.method_id+"']").prop('selected', true).trigger('change');
                    $("#requested_amount").val(response.requested_amount);
                    $("#previous_amount").val(response.requested_amount);
                }
            })

        });

        $(document).on('click', '#add_request_submit', function(event) {
            event.preventDefault();

            var withdrawal_account = $("#withdrawal_account").val();
            var requested_amount = $("#requested_amount").val();
            var previous_amount = $("#previous_amount").val();
            var tableId = $("#tableId").val();
            var submit_action = $(this).attr('submit_action');

            if(withdrawal_account == '') {
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please Select a method."); ?>', 'warning');
                return;
            }

            if(requested_amount == '') {
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please Provide your requested amount"); ?>', 'warning');
                return;
            }

            $(this).addClass('btn-progress');

            $.ajax({
                context:this,
                url: base_url+'affiliate_system/issue_new_request',
                type: 'POST',
                data: {withdrawal_account: withdrawal_account,requested_amount: requested_amount,submit_action:submit_action,tableId:tableId,previous_amount:previous_amount},
                dataType: 'json',
                success:function(response) {

                    $(this).removeClass('btn-progress');

                    if(response.status == '1') {
                        var span = document.createElement("span");
                        span.innerHTML = response.response_success;
                        var report_link = base_url+"affiliate_system/withdrawal_requests";
                        swal({ title:'<?php echo $this->lang->line("success"); ?>', content:span,icon:'success'}).then((value) => {
                            window.location.href=report_link;
                        });
                    }

                    if(response.status == '0') {
                        var span = document.createElement("span");
                        span.innerHTML = response.response_error;

                        swal({ title:'<?php echo $this->lang->line("Warning"); ?>', content:span,icon:'warning'});
                    }
                }
            })
            
        });


        $(document).on('click','.delete_request',function(e){
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
                $.ajax({
                  context: this,
                  type:'POST' ,
                  url: "<?php echo base_url('affiliate_system/delete_withdrawal_request')?>",              
                  data: {id:id},
                  success:function(response){ 
                    var report_link = base_url+"affiliate_system/withdrawal_requests";
                    if(response == '1')
                    {

                        var span = document.createElement("span");
                        span.innerHTML = '<?php echo $this->lang->line("Pending Request has been successfully deleted.") ?>';
                        swal({ title:'<?php echo $this->lang->line("success"); ?>', content:span,icon:'success'}).then((value) => {
                            window.location.href=report_link;
                        });
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



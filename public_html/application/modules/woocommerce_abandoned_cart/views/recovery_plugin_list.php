<?php $this->load->view('admin/theme/message'); ?>
<style>
    #search_page_id{min-width: 150px !important;}
    .list-unstyled-border li{border-bottom: none;margin-bottom: 10px;padding-top: 0;}
    .tickets-list .ticket-item{border-bottom: none;padding:0;margin-bottom: 19px;}
    .tickets-list .ticket-item h4{margin-bottom: 9px;}
    .media{margin-bottom: 12px;}
    .media .media-title {margin-bottom: 0;font-size: 14px;}
    .ticket-title h4{color:#000 !important;margin-bottom: 3px !important;font-weight:600 !important;}
    .website{font-size: 20px;}
    .website i{font-size: 20px !important;}
    .website h4{font-size: 20px !important;}
</style>

<style type="text/css">
  .button-outline
  {
    background: #fff;
    border: .5px dashed #ccc;
  }
  .button-outline:hover
  {
    border: 1px dashed #6777EF !important;
    cursor: pointer;
  }
  .multi_layout{margin:0;background: #fff}
  .multi_layout .card{margin-bottom:0;border-radius: 0;}
  /*.multi_layout p, .multi_layout ul:not(.list-unstyled), .multi_layout ol{line-height: 15px;margin-top: 15px}*/
  .multi_layout .list-group li{padding: 15px 10px 12px 25px;}
  .multi_layout{border:.5px solid #dee2e6;}
  .multi_layout .collef,.multi_layout .colmid,.multi_layout .colrig{padding-left: 0px; padding-right: 0px;}
  .multi_layout .collef,.multi_layout .colmid{border-right: .5px solid #dee2e6;}
  .multi_layout .main_card{box-shadow: none;}
  /*.multi_layout .collef .makeScroll{max-height: 500px;overflow:auto;}*/
  .multi_layout .colrig .makeScroll{max-height:750px;overflow:auto;}
  .multi_layout .list-group{padding-top:6px;}
  .multi_layout .list-group .list-group-item{border-radius: 0;border:.5px solid #dee2e6;border-left:none;border-right:none;cursor: pointer;z-index: 0;padding:21px;}
  .multi_layout .list-group .list-group-item:first-child{border-top:none;}
  .multi_layout .list-group .list-group-item:last-child{border-bottom:none;}
  .multi_layout .list-group .list-group-item.active{border:.5px solid #6777EF;}
  .multi_layout .mCSB_inside > .mCSB_container{margin-right: 0;}
  .multi_layout .card-statistic-1{border-radius: 0;}
  .multi_layout h6.page_name{font-size: 14px;margin:10px 0;}
  .multi_layout .card .card-header input{max-width: 100% !important;}
  .multi_layout .card .card-header h4 a{font-weight: 700 !important;}  
  .product-item .product-name{font-weight: 500;}
  .badge-status{border-color:#eee;}
  /* #right_column_title i{font-size: 17px;} */  
  ::placeholder {
    color: #ccc !important;
  } 

  #right_column_bottom_content .no_shadow .card-header,#right_column_bottom_content .no_shadow .card-body{padding:20px 0;}  
  /*.multi_layout .card-statistic-1 .card-icon{border: .5px solid #dee2e6;}*/
  .multi_layout .card.card-statistic-1 .card-icon, .card.card-statistic-2 .card-icon{margin:0;border-radius: 4px 0 0 4px;background: transparent;}
  .multi_layout .card-statistic-1{border:.5px solid #dee2e6;border-radius: 4px;margin-bottom: 5px;}
  .multi_layout .card.card-statistic-1 .card-header, .multi_layout  .card.card-statistic-2 .card-header{padding:0;padding-top: 20px;}
  .multi_layout .card.card-statistic-1 .card-body, .multi_layout  .card.card-statistic-2 .card-body{padding: 0 ;}
  /*#right_column_bottom_content div[class^='col'], #right_column_bottom_content  div[class*=' col']{padding-left: 5px;padding-right: 5px;}*/

</style>

<section class="section">
  <div class="section-header m-0">
    <h1><i class="fas fa-shopping-cart"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
      <a class="btn btn-primary" href="<?php echo base_url("woocommerce_abandoned_cart/recovery_plugin_add");?>">
        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create Plugin"); ?>
      </a> 
    </div>    
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-md-6">
            <a href="<?php echo base_url('messenger_bot'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a>
            / <?php echo $this->lang->line("WooCommerce Abandoned Cart"); ?>
          </div>
          <div class="col-12 col-md-6">
            <div class="breadcrumb-item">
              <form method="POST" action="<?php echo base_url('woocommerce_abandoned_cart/recovery_plugin_list') ?>">          
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <i class="fas fa-calendar"></i>
                    </div>
                  </div>
                  <input type="hidden" name="plugin_id" id="plugin_id">
                  <input type="text" class="form-control datepicker_x" value="<?php echo $this->session->userdata("woocommerce_from_date"); ?>" id="from_date" name="from_date" style="width:115px"> 
                  <input type="text" class="form-control datepicker_x" value="<?php echo $this->session->userdata("woocommerce_to_date"); ?>" id="to_date" name="to_date" style="width:115px">
                  <button class="btn btn-outline-primary" style="margin-left:1px" id="search_submit" type="submit"><i class="fa fa-search"></i> <?php echo $this->lang->line("Search");?></button>
                </div>
            </form>
            </div>
          </div>
        </div>
      </div>
    </div>


    <?php if(empty($domain_info) || empty($plugin_info))
    { ?>       
    <div class="card" id="nodata">
      <div class="card-body">
        <div class="empty-state">
          <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image">
           <h2 class="mt-0"><?php echo $this->lang->line("We could not find any woocommerce plugin.");?></h2>
          <p class="lead"><?php echo $this->lang->line("You have create a plugin for your woocommerce store first."); ?></p>
          <a href="<?php echo base_url('woocommerce_abandoned_cart/recovery_plugin_add'); ?>" class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-circle-right"></i> <?php echo $this->lang->line("Create Plugin");?></a>
        </div>
      </div>
    </div>

    <?php 
    }
    else
    { 
      $summary_earning = 0;
      $summary_recovered_cart = 0;
      $summary_reminder_cart = 0;
      $earning_chart_labels = array();
      $earning_chart_values = array();
      $top_buyers = array();
   
      $from_date = strtotime($this->session->userdata("woocommerce_from_date")); 
      $to_date = strtotime($this->session->userdata("woocommerce_to_date")); 
      do 
      {
         $temp = date("Y-m-d",$from_date);
         $temp2 = date("j M",$from_date);;
         $earning_chart_values[$temp] = 0;
         $earning_chart_labels[] = $temp2;
         $from_date = strtotime('+1 day',$from_date); 
      } 
      while ($from_date <= $to_date);

      foreach ($webhook_info as $key => $value) 
      {
        if($value["last_completed_hour"]>0)
        {
          $summary_reminder_cart++;
          if($value['action_type']=='checkout')
          {
            $summary_earning += $value["checkout_amount"];
            $summary_recovered_cart++;

            $updated_at_formatted = date("Y-m-d",strtotime($value['updated_at']));
            if(isset($earning_chart_values[$updated_at_formatted])) $earning_chart_values[$updated_at_formatted] += $value["checkout_amount"];
            else $earning_chart_values[$updated_at_formatted] = $value["checkout_amount"];

            if($value['checkout_country']!='')
            {
              if(isset($top_buyers[$value['checkout_country']])) $top_buyers[$value['checkout_country']] += $value["checkout_amount"];
              else $top_buyers[$value['checkout_country']] = $value["checkout_amount"];
            }
          }
        }
      }
      arsort($top_buyers);   
      ?>
      <div class="row multi_layout">

        <div class="col-12 col-md-4 col-lg-2 collef">
          <div class="card main_card">
            <div class="card-header">
              <!-- <div class="col-6 padding-0"> -->
                <h4><i class="fas fa-store-alt"></i> <?php echo $this->lang->line("Stores"); ?></h4>
              <!-- </div> -->
              <!-- <div class="col-6 padding-0">             -->
                <!-- <input type="text" class="form-control float-right" id="search_page_list" onkeyup="search_in_ul(this,'page_list_ul')" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>"> -->
              <!-- </div> -->
            </div>
            <div class="card-body padding-0">
              <ul class="list-group" id="page_list_ul">
                <?php $i=0; foreach($domain_info as $value) { ?> 
                  <li class="list-group-item <?php if($value['id']==$this->session->userdata("woocommerce_selected_plugin")) echo 'active'; ?> page_list_item" page_table_id="<?php echo $value['id']; ?>">
                    <h6 class="page_name"><i class="fas fa-store"></i> <?php echo str_replace(array('https://','http://'), '', $value['domain_name']); ?></h6>
                  </li>
                  <?php $i++; } ?>                
              </ul>
            </div>
          </div>          
        </div>

        <div class="col-12 col-md-8 col-lg-10 colrig" id="right_column">

          <div class="card main_card">
            <div class="card-header">
              <div class="col-8 p-0">
                <h4 id="right_column_title"><i class="fas fa-globe"></i> <?php echo str_replace(array('https://','http://'), '', $plugin_info['domain_name']); ?> (<?php echo $plugin_info['page_name']; ?>)</h4>
              </div>
              <div class="col-4 p-0">
                <div class="card-header-action dropdown float-right">
                  <a href="#" data-toggle="dropdown" class="btn btn-lg btn-outline-primary dropdown-toggle"><?php echo $this->lang->line("Actions"); ?></a>
                  <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                    <li class="dropdown-title"><?php echo $this->lang->line("Actions"); ?></li>
                    <li><?php echo '<a class="dropdown-item reminder_report" href="" data-toggle="tooltip" title="'.$this->lang->line('Report').'" campaign_id='.$plugin_info['id'].'><i class="fas fa-eye text-info"></i> &nbsp; '.$this->lang->line("Reminder Report").'</a>';?></li>
                    <li><?php echo '<a class="dropdown-item" href="'.base_url()."woocommerce_abandoned_cart/recovery_plugin_edit/".$plugin_info['id'].'" data-toggle="tooltip" title="'.$this->lang->line('Edit').'" campaign_id='.$plugin_info['id'].'><i class="fas fa-edit text-warning"></i> &nbsp; '.$this->lang->line("Edit").'</a>';?></li>
                    <li><?php echo '<a class="dropdown-item download" data-toggle="tooltip" href="#" title="'.$this->lang->line('Download').'" data-toggle="tooltip" campaign_id='.$plugin_info['id'].'><i class="fas fa-cloud-download-alt text-primary"></i> &nbsp; '.$this->lang->line("Download").'</a>'; ?></li>
                    <li><?php echo '<a class="dropdown-item delete_campaign" data-toggle="tooltip" href="#" title="'.$this->lang->line('Delete').'" data-toggle="tooltip" campaign_id='.$plugin_info['id'].'><i class="fas fa-trash-alt text-danger"></i> &nbsp; '.$this->lang->line("Delete").'</a>'; ?></li>
                  </ul>
                </div> 
              </div>
            </div>

            <div class="card-body">
              <div class="row">
                <div class="col-12">

                  <div id="right_column_content">              

                    <div id="right_column_bottom_content">

                      <div class="row">
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                          <div class="card card-statistic-1">
                            <div class="card-icon">
                              <i class="fas fa-cart-plus text-primary"></i>
                            </div>
                            <div class="card-wrap">
                              <div class="card-header">
                                <h4><?php echo $this->lang->line("Total Cart"); ?></h4>
                              </div>
                              <div class="card-body">
                                <?php echo count($webhook_info); ?>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                          <div class="card card-statistic-1">
                            <div class="card-icon">
                              <i class="fas fa-bell text-primary"></i>
                            </div>
                            <div class="card-wrap">
                              <div class="card-header">
                                <h4><?php echo $this->lang->line("Reminded Cart"); ?></h4>
                              </div>
                              <div class="card-body">
                                <?php echo $summary_reminder_cart; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                          <div class="card card-statistic-1">
                            <div class="card-icon">
                              <i class="fas fa-shopping-bag text-primary"></i>
                            </div>
                            <div class="card-wrap">
                              <div class="card-header">
                                <h4><?php echo $this->lang->line("Recovered Cart"); ?></h4>
                              </div>
                              <div class="card-body">
                                <?php echo $summary_recovered_cart; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                          <div class="card card-statistic-1">
                            <div class="card-icon">
                              <i class="fas fa-coins text-primary"></i>
                            </div>
                            <div class="card-wrap">
                              <div class="card-header">
                                <h4><?php echo $this->lang->line("Total Earnings"); ?></h4>
                              </div>
                              <div class="card-body">
                               <?php echo custom_number_format($summary_earning); ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row">

                        <div class="col-12 col-md-8">
                          <div class="card no_shadow">
                            <div class="card-header">
                              <h4><i class="fas fa-coins"></i> <?php echo $this->lang->line("Earnings"); ?></h4>
                            </div>
                            <div class="card-body">
                              <canvas id="myChart" height="182"></canvas>
                            </div>
                          </div>

                          <div class="card no_shadow">
                            <div class="card-header">
                              <h4><i class="fas fa-globe-asia"></i> <?php echo $this->lang->line("Top Buyers"); ?></h4>
                            </div>
                            <div class="card-body">
                              <div class="row">
                                <?php
                                if(empty($top_buyers)) echo '<div class="col-12"><div class="alert alert-light">'.$this->lang->line("No country data found").'</div></div>';
                                $country_limit = 0;
                                foreach ($top_buyers as $key => $value) 
                                {
                                  $country_limit++;
                                  if($country_limit>12) break;
                                  $top_flag = '<img class="img-fluid mt-1 img-shadow" src="'.base_url().'assets/modules/flag-icon-css/flags/4x3/<?php echo strtolower($key);?>.svg" alt="image" width="45">';
                                  $key2 = strtoupper($key);strtoupper($key)
                                  ?>
                                  <div class="col-12 col-sm-6 col-md-4">
                                    <li class="media">
                                      <img class="img-fluid mt-1 img-shadow" src="<?php echo base_url(); ?>assets/modules/flag-icon-css/flags/4x3/<?php echo strtolower($key);?>.svg" alt="image" width="45">
                                      <div class="media-body ml-3">
                                        <div class="media-title"><?php echo isset($country_names[$key2]) ? ucwords(strtolower($country_names[$key2])) : "-"; ?></div>
                                        <div class="text-small text-muted"><?php echo custom_number_format($value); ?></div>
                                      </div>
                                    </li>
                                  </div>
                                  <?php
                                } ?>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-12 col-md-4">
                          
                          <div class="card no_shadow">
                            <div class="card-header">
                              <h4><i class="fas fa-tasks"></i> <?php echo $this->lang->line("Activities"); ?></h4>
                            </div>
                            <div class="card-body makeScroll">
                              
                              <div class="tickets-list">
                                <?php
                                if(empty($webhook_info)) echo '<div class="alert alert-light">'.$this->lang->line("No activity found").'</div>';
                                foreach ($webhook_info as $key => $value) 
                                { 
                                  $hook_ago = date_time_calculator($value['updated_at'],true);
                                  if($value['action_type']=='add') 
                                  {
                                    $hook_icon ='fas fa-cart-plus';
                                    $hook_color = 'text-primary';
                                    $hook_activity = $this->lang->line("New item added to cart");
                                  }
                                  else if($value['action_type']=='remove') 
                                  {
                                    $hook_icon ='fas fa-cart-arrow-down';
                                    $hook_color = 'text-danger';
                                    $hook_activity = $this->lang->line("Item removed from cart");
                                  }
                                  else 
                                  {
                                    $hook_icon = 'fas fa-shopping-bag';
                                    $hook_color = 'text-success';
                                    $currency_icon = isset($currency_icons[strtoupper($value['checkout_currency'])]) ? $currency_icons[strtoupper($value['checkout_currency'])] : '';
                                    $hook_activity = $this->lang->line("Successful checkout").' <span class="text-success">('.$currency_icon.$value['checkout_amount'].')</span>';
                                  }


                                 
                                  $hook_class = $hook_icon.' '.$hook_color;
                                  
                                  $hook_user = ($value['wc_email_bill']!='') ? $value['wc_email_bill'] : $value['wc_email'];
                                  echo 
                                  '<a href="#" class="ticket-item webhook_data" data-id="'.$value['id'].'">
                                    <div class="ticket-title">
                                      <h4><i class="'.$hook_class.' m-0"></i> '.$hook_activity.'</h4>
                                    </div>
                                    <div class="ticket-info">
                                      <div>'.$hook_user.'</div>
                                      <div class="bullet"></div>
                                      <div class="text-primary">'.$hook_ago.'</div>
                                    </div>
                                  </a>';                         
                                } 
                                ?>                               
                              </div>
                            </div>
                          </div>
                        </div>                     

                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
        
          </div>
        </div>
      </div>

    <?php 
    } ?>

  </div>
</section>

<div class="modal fade" role="dialog" id="get_embed_modal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-code"></i> <?php echo $this->lang->line('Checkbox plugin embed code'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label > <?php echo $this->lang->line("Copy the code below and paste inside the html element of your webpage where you want to display this plugin")?> </label>

              <pre class="language-javascript" ><code id="test" class="dlanguage-javascript description" ></code></pre>

            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <!-- <button id="copy_js_code" type="button" class="btn btn-primary"><i class="fas fa-cut"></i> <?php echo $this->lang->line('Copy'); ?> -->
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line('Close'); ?></button>
      </button>
    </div>
  </div>
</div>
</div>


<?php 
if(!empty($domain_info) && !empty($plugin_info)) 
{
  $max = (!empty($earning_chart_values)) ? max($earning_chart_values) : 0;
  $steps = $max/5;
  if($steps==0) $steps = 1;
  ?>

  <script type="text/javascript">
    var statistics_chart = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(statistics_chart, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($earning_chart_labels); ?>,
        datasets: [{
          label: '<?php echo $this->lang->line("Earning"); ?>',
          data: <?php echo json_encode(array_values($earning_chart_values)); ?>,
          borderWidth: 3,
          borderColor: '#6777ef',
          backgroundColor: 'transparent',
          pointBackgroundColor: '#6777ef',
          pointBorderColor: '#6777ef',
          pointRadius: 3
        }]
      },
      options: {
        legend: {
          display: false
        },
        scales: {
          yAxes: [{
            gridLines: {
              display: false,
              drawBorder: false,
            },
            ticks: {
              stepSize: <?php echo $steps; ?>
            }
          }],
          xAxes: [{
            gridLines: {
              display: false,
              drawBorder: false,
              color: '#dee2e6',
              lineWidth: 1
            }
          }]
        },
      }
    });  
  </script>
  <?php 
} ?>

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
        ajax: 
        {
          "url": base_url+'woocommerce_abandoned_cart/recovery_plugin_list_data',
          "type": 'POST',
          data: function ( d )
          {
              d.search_page_id = $('#search_page_id').val();
              d.search_domain_name = $('#search_domain_name').val();
          }
        },
        language: 
        {
          url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columnDefs: [
            {
              targets: [1,5,6],
              visible: false
            },
            {
              targets: [0,1,4,6,7],
              className: 'text-center'
            },
            {
              targets:[0,1,4,8],
              sortable: false
            }
        ],
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
          if(areWeUsingScroll)
          {
            if (perscroll) perscroll.destroy();
            perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
          }
        },
        scrollX: 'auto',
        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
          if(areWeUsingScroll)
          { 
            if (perscroll) perscroll.destroy();
            perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
          }
        }
    });
   

    $(document).on('click', '.delete_campaign', function(event) {
        event.preventDefault();
        
        swal({
              title: '<?php echo $this->lang->line("Delete Plugin"); ?>',
              text: '<?php echo $this->lang->line("Do you really want to delete this plugin?"); ?>',
              icon: 'warning',
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete) 
              {
                  var base_url = '<?php echo site_url();?>';
                  $(this).addClass('btn-danger btn-progress');
                  $(this).removeClass('btn-outline-danger');
                  var that = $(this);
                  var campaign_id = $(this).attr('campaign_id');

                  $.ajax({
                    context: this,
                    type:'POST' ,
                    url:"<?php echo site_url();?>woocommerce_abandoned_cart/recovery_plugin_delete",
                    dataType: 'json',
                    data:{campaign_id : campaign_id},
                    success:function(response){ 

                      $(that).removeClass('btn-danger btn-progress');
                      $(this).addClass('btn-outline-danger');
                      
                      if(response.status == '1')
                      {
                        iziToast.success({title: '<?php echo $this->lang->line("Deleted Successfully"); ?>', message: response.message,position: 'bottomRight'});
                        $("#search_submit").click();
                      }
                      else
                      iziToast.error({title: '<?php echo $this->lang->line("Error"); ?>',message: response.message ,position: 'bottomRight'});    
                    }
                  });
              } 
            });
    });

    $(document).on('click', '.page_list_item', function(event) {
        event.preventDefault();
        $('.page_list_item').removeClass('active');
        $(this).addClass('active');
        var plugin_id = $(this).attr('page_table_id');
        $("#plugin_id").val(plugin_id);
        $("#search_submit").click();
    });

    var table2="";
    $(document).on('click', '.reminder_report', function(event) {
        event.preventDefault();
        $("#reminder_data").modal();

        setTimeout(function(){    
        if (table2 == '')
        {
          var perscroll2;
          table2 = $("#mytable2").DataTable({
              serverSide: true,
              processing:true,
              bFilter: true,
              order: [[ 7, "desc" ]],
              pageLength: 10,
              ajax: {
                  url: '<?php echo base_url("woocommerce_abandoned_cart/campaign_sent_status_data"); ?>',
                  type: 'POST',
                  dataSrc: function ( json ) 
                  {
                    $(".table-responsive").niceScroll();
                    return json.data;
                  },
                  data: function ( d )
                  {
                      d.page_id = $('#hidden_page_id').val();
                  }
              },
              language: 
              {
                url: '<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json');?>'
              },
              dom: '<"top"f>rt<"bottom"lip><"clear">',
              columnDefs: [
                {
                    targets: [1],
                    visible: false
                },
                {
                    targets: [0,5,6,7,8],
                    className: 'text-center'
                }
              ],
              fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                  if(areWeUsingScroll)
                  {
                    if (perscroll2) perscroll2.destroy();
                    perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
                  }
              },
              scrollX: 'auto',
              fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                  if(areWeUsingScroll)
                  { 
                    if (perscroll2) perscroll2.destroy();
                    perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
                  }
              }
          });
        }
        else table2.draw();
        }, 1000);
    });


    $(document).on('click','.woo_error_log',function(e){
      e.preventDefault();
      $(this).removeClass('btn-outline-primary').addClass("btn-primary").addClass('btn-progress');
      var id = $(this).attr('data-id');

      $.ajax
        ({
           type:'POST',
           url:base_url+'woocommerce_abandoned_cart/reminder_reponse',
           data:{id:id},
           context: this,
           success:function(response)
            {
              $(this).addClass('btn-outline-primary').removeClass("btn-primary").removeClass('btn-progress');

              var success_message= response;
              var span = document.createElement("span");
              span.innerHTML = success_message;
              swal({ title:'<?php echo $this->lang->line("API Response"); ?>', content:span,icon:'info'});
            } 
        }); 
    });

  });

</script>


<?php include(APPPATH.'modules/woocommerce_abandoned_cart/views/download_code.php'); ?>
<?php include(APPPATH.'modules/woocommerce_abandoned_cart/views/cart_modal.php'); ?>



<div class="modal fade" id="reminder_data" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-mega" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-bell"></i> <?php echo $this->lang->line("Reminder Report"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body data-card">
        <div class="table-responsive2">
            <table class="table table-bordered" id="mytable2">
              <thead>
                <tr>
                  <th>#</th>      
                  <th style="vertical-align:middle;width:20px">
                      <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                  </th>
                  <th><?php echo $this->lang->line("First Name"); ?></th>
                  <th><?php echo $this->lang->line("Last Name")?></th>
                  <th><?php echo $this->lang->line("Email"); ?></th>
                  <th><?php echo $this->lang->line("Subscriber ID"); ?></th>
                  <th><?php echo $this->lang->line("Reminder Hour"); ?></th>
                  <th><?php echo $this->lang->line("Sent at"); ?></th>
                  <th><?php echo $this->lang->line("API Response"); ?></th>
                </tr>
              </thead>
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close"); ?></button>
      </div>
    </div>
  </div>
</div>
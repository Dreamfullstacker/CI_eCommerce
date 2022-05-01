<section class="section">
    <div class="section-header">
        <h1 class="page_title"><i class="fas fa-chart-area"></i> <?php echo $this->lang->line('Clicked vs Signed Up Visitors'); ?></h1>
        <div class="section-header-button">
            <a class="btn btn-primary add_method" href="<?php echo base_url("affiliate_system/visitor_reports#visitor_history_div") ?>">
                <i class="fas fa-history"></i> <?php echo $this->lang->line("Visitors History"); ?>
            </a> 
        </div>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
          <div class="col-12 col-lg-8">
            <div class="card">
              <div class="card-header">
                <h4><i class="fas fa-chart"></i> <?php echo $this->lang->line('Visitor Analysis : Last 30 days'); ?> </h4>
              </div>
              <div class="card-body">
                <canvas id="clicked_vs_signedup_report" height="150"></canvas>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="card gradient-bottom">
              <div class="card-header">
                <h4><i class="fas fa-database"></i> <?php echo $this->lang->line("Visitors Data"); ?></h4>
                <div class="card-header-action dropdown">
                  <a href="#" data-toggle="dropdown" class="btn btn-danger dropdown-toggle" id="selected_period"><?php echo $this->lang->line("This Month");?></a>
                  <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                    <li class="dropdown-title"><?php echo $this->lang->line("Select Period");?></li>
                    <li><a href="#" class="dropdown-item period_change" period="today"><?php echo $this->lang->line("Today");?></a></li>
                    <li><a href="#" class="dropdown-item period_change" period="week"><?php echo $this->lang->line("Last 7 Days");?></a></li>
                    <li><a href="#" class="dropdown-item period_change active" period="month"><?php echo $this->lang->line("This Month");?></a></li>
                    <li><a href="#" class="dropdown-item period_change" period="year"><?php echo $this->lang->line("This Year");?></a></li>
                  </ul>
                </div>
              </div>
              <div class="card-body" style="height:200px">
                <div class="text-center waiting hidden" id="period_loader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div>
                <ul class="list-unstyled list-unstyled-border" id="period_change_content">
                  <li class="media">
                    <img class="img-fluid mt-1 img-shadow" src="<?php echo base_url('assets/img/icon/paper-plane.png'); ?>" alt="image" width="60">
                    <div class="media-body ml-3">
                      <div class="media-title"><?php echo $this->lang->line('Link Visited'); ?></div>
                      <div class="text-small text-muted" id="link_visited_number"><?php echo number_format($link_clicked); ?> <i class="fas fa-caret-down text-primary"></i></div>
                    </div>
                  </li>
                  <li class="media">
                    <img class="img-fluid mt-1 img-shadow" src="<?php echo base_url('assets/img/icon/access.png'); ?>" alt="image" width="60">
                    <div class="media-body ml-3">
                      <div class="media-title"><?php echo $this->lang->line('Signed Up'); ?></div>
                      <div class="text-small text-muted" id="signup_number"><?php echo number_format($signedUp); ?> <i class="fas fa-caret-down text-danger"></i></div>
                    </div>
                  </li>
                </ul>
              </div>
              <div class="card-footer pt-3 d-flex justify-content-center">
                <div class="budget-price justify-content-center">
                  <div class="budget-price-square bg-primary" data-width="30" data-height="7"></div>
                  <div class="budget-price-label"><?php echo $this->lang->line('Clicked'); ?></div>
                </div>
                <div class="budget-price justify-content-center">
                  <div class="budget-price-square bg-danger" data-width="30" data-height="7"></div>
                  <div class="budget-price-label"><?php echo $this->lang->line('Signed Up'); ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
</section>

<div class="row" id="visitor_history_div">
  <div class="col-12">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-history"></i> <?php echo $this->lang->line("Visitors History"); ?></h4>
        </div>
        <div class="card-body data-card">
            <div class="table-responsive2">
                <table class="table table-bordered" id="mytable_visitor_history">
                    <thead>                  
                        <tr>
                            <th><?php echo $this->lang->line('SL'); ?></th>      
                            <th><?php echo $this->lang->line("Visited On"); ?></th>
                            <th><?php echo $this->lang->line("Visitors Type"); ?></th>      
                            <th><?php echo $this->lang->line("Visited IP"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl=0;
                        foreach ($info as $key => $value) 
                        {
                            $sl++;
                            echo "<tr>";
                            echo "<td>".$sl."</td>";
                            echo "<td>".$value['clicked_time']."</td>";
                            echo "<td>".$value['type']."</td>";
                            echo "<td>".$value['ip_address']."</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>             
        </div>

    </div>
  </div>
</div>

<?php
$drop_menu = '<div class="btn-group dropleft float-right"><button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  '.$this->lang->line("Options").'  </button>  <div class="dropdown-menu dropleft"> <a class="dropdown-item delete_visitors_old_data" href="'.base_url('affiliate_system/delete_visitor_old_log').'"><i class="fas fa-trash"></i> '.$this->lang->line("Delete old data").'</a></div> </div>';
?> 


<script type="text/javascript">

    var drop_menu = '<?php echo $drop_menu;?>';
      setTimeout(function(){ 
        $("#mytable_visitor_history_filter").append(drop_menu); 
    }, 2000);

    var myChart1; 
    $(document).ready(function() {      
        var stepsize = "<?php echo $step_size; ?>"; 
        var clicked_vs_signedup_report = document.getElementById('clicked_vs_signedup_report').getContext('2d');
        var myChart1 = new Chart(clicked_vs_signedup_report, {
          type: 'line',
          data: {
            labels: <?php echo json_encode(array_values($click_signup_date_list));?>,
            datasets: [{
              label: '<?php echo $this->lang->line('Sign up'); ?>',
              data: <?php echo json_encode(array_values($signup_list));?>,
              borderWidth: 2,
              backgroundColor: 'rgba(254,86,83,.7)',
              borderWidth: 0,
              borderColor: 'transparent',
              pointBorderWidth: 0,
              pointRadius: 3.5,
              pointBackgroundColor: 'transparent',
              pointHoverBackgroundColor: 'rgba(254,86,83,.8)',
            },
            {
              label: '<?php echo $this->lang->line('Clicked'); ?>',
              data: <?php echo json_encode(array_values($click_list));?>,
              borderWidth: 2,
              backgroundColor: 'rgba(63,82,227,.8)',
              borderWidth: 0,
              borderColor: 'transparent',
              pointBorderWidth: 0 ,
              pointRadius: 3.5,
              pointBackgroundColor: 'transparent',
              pointHoverBackgroundColor: 'rgba(63,82,227,.8)',
            }]
          },
          options: {
            legend: {
              display: false
            },
            scales: {
              yAxes: [{
                gridLines: {
                  // display: false,
                  drawBorder: false,
                  color: '#f2f2f2',
                },
                ticks: {
                  beginAtZero: true,
                  stepSize: stepsize,
                }
              }],
              xAxes: [{
                gridLines: {
                  display: false,
                  tickMarkLength: 10,
                  lineWidth: 1
                }
              }]
            },
          }
        });

        var base_url = '<?php echo base_url(); ?>';
        var perscroll_visitor_history;
        var history_table = $("#mytable_visitor_history").DataTable({          
            processing:true,
            bFilter: true,
            order: [[ 1, "desc" ]],
            pageLength: 25,
            language: 
            {
              url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [            
              {
                  targets: '',
                  className: 'text-center'
              },
              {
                  targets: [0],
                  sortable: false
              }
            ],
            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                if(areWeUsingScroll)
                {
                  if (perscroll_visitor_history) perscroll_visitor_history.destroy();
                  perscroll_visitor_history = new PerfectScrollbar('#mytable_visitor_history_wrapper .dataTables_scrollBody');
                }
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                if(areWeUsingScroll)
                { 
                  if (perscroll_visitor_history) perscroll_visitor_history.destroy();
                  perscroll_visitor_history = new PerfectScrollbar('#mytable_visitor_history_wrapper .dataTables_scrollBody');
                }
            }
        });

        $(document).on('click','.delete_visitors_old_data',function(e){
          e.preventDefault();
          var link = $(this).attr("href");
          var mes='<?php echo $this->lang->line("Do you really want to delete it?");?>';  
          swal({
            title: "<?php echo $this->lang->line("Are you sure?");?>",
            text: mes,
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => 
          {
            if (willDelete) 
            {
                $.ajax({
                  context: this,
                  url: link,
                  type: 'POST',
                  dataType: 'JSON',
                  data: {},
                    success:function(response)
                    {
                      if(response.status == 1)  
                      {
                        var span = document.createElement("span");
                        span.innerHTML = response.message;
                        var report_link = base_url+"affiliate_system/visitor_reports";
                        swal({ title:'<?php echo $this->lang->line("success"); ?>', content:span,icon:'success'}).then((value) => {
                            window.location.href=report_link;
                        });
                      }
                      else iziToast.error({title: '',message: response.message,position: 'bottomRight'});
                    }
                });
            } 
          });
        });

        $(document).on('click', '.period_change', function(e) {
          e.preventDefault(); 
          $(".period_change").removeClass('active');
          $(this).addClass('active');
          var period = $(this).attr('period');
          var selected_period = $(this).html();
          $("#selected_period").html(selected_period);

          $("#period_change_content").hide();
          $("#period_loader").removeClass('hidden');
          var url = "<?php echo base_url('affiliate_system/get_visitors_date_wise_data')?>";

          $.ajax({
             type:'POST' ,
             url: url,
             data: {period:period},
             dataType : 'JSON',
             success:function(response)
             {
                $("#period_loader").addClass('hidden');

                $("#signup_number").html(response.signup_number);
                $("#link_visited_number").html(response.link_visited_number);

                $("#period_change_content").show();
             }
          });

        });
    });
</script>
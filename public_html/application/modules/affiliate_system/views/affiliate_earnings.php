<section class="section">
  <div class="row">
	<div class="col-lg-4 col-md-4 col-sm-12">
      <div class="card card-statistic-2">
        <div class="card-stats">
          <div class="card-stats-title"><i class="fas fa-eye"></i> <?php echo $this->lang->line("Summary"); ?>
          </div>
          <div class="card-stats-items">
            <div class="card-stats-item">
              <div class="card-stats-item-count"><?php echo $curency_icon.$payment_today; ?></div>
              <div class="card-stats-item-label"><?php echo $this->lang->line("Today"); ?></div>
            </div>
            <div class="card-stats-item">
              <div class="card-stats-item-count"><?php echo $curency_icon.$payment_month; ?></div>
              <div class="card-stats-item-label"><?php echo $this->lang->line(date("M")); ?></div>
            </div>
            <div class="card-stats-item">
              <div class="card-stats-item-count"><?php echo $curency_icon.$payment_year; ?></div>
              <div class="card-stats-item-label"><?php echo $this->lang->line("Year"); ?></div>
            </div>
          </div>
        </div>
        <div class="card-icon shadow-primary bg-info">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4><?php echo $this->lang->line("Life Time")." ".$this->lang->line("Earning"); ?></h4>
          </div>
          <div class="card-body">
            <?php echo $curency_icon.$payment_life; ?>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">
      <div class="card card-statistic-2">
        <div class="card-chart">
          <canvas id="month-chart" height="80"></canvas>
        </div>
        <div class="card-icon shadow-primary bg-primary">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4><?php echo date("M - Y")." ".$this->lang->line("Earning"); ?></h4>
          </div>
          <div class="card-body">
            <?php echo $curency_icon.$payment_month; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-12">
      <div class="card card-statistic-2">
        <div class="card-chart">
          <canvas id="year-chart" height="80"></canvas>
        </div>
        <div class="card-icon shadow-primary bg-warning">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4><?php echo date("Y")." ".$this->lang->line("Earning"); ?></h4>
          </div>
          <div class="card-body">
           <?php echo $curency_icon.$payment_year; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12 col-md-12">
      <div class="card">
        <div class="card-header">
          <h4><i class="fas fa-balance-scale"></i> <?php echo $this->lang->line("Affiliate Earning");?></h4>
        </div>
        <div class="card-body">
          <canvas id="earning_chart" height="120"></canvas>

          <div class="statistic-details">
            <div class="statistic-details-item">
              <span class="text-muted"><span class="text-primary"><i class="fas fa-caret-up statistic_icon"></i></span></span>
              <div class="detail-value"><?php echo $curency_icon.($singup_earning+$payment_earning); ?></div>
              <div class="detail-name"><?php echo $this->lang->line("Total Earning"); ?></div>
            </div>
            <div class="statistic-details-item">
              <span class="text-muted"><span class="text-danger"><i class="fas fa-caret-down statistic_icon"></i></span></span>
              <div class="detail-value"><?php echo $curency_icon.$singup_earning; ?></div>
              <div class="detail-name"><?php echo $this->lang->line("Signup Earning"); ?></div>
            </div>
            <div class="statistic-details-item">
              <span class="text-muted"><span class="text-primary"><i class="fas fa-caret-up statistic_icon"></i></span></span>
              <div class="detail-value"><?php echo $curency_icon.$payment_earning; ?></div>
              <div class="detail-name"><?php echo $this->lang->line("Payment Earning"); ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<?php 
	$max = (!empty($earning_chart_values)) ? max($earning_chart_values) : 0;
	$steps = $max/5;
	if($steps==0) $steps = 1;
?>
<style>
  .statistic_icon { font-size: 20px; }
</style>

<script type="text/javascript">
  var statistics_chart = document.getElementById("earning_chart").getContext('2d');
  var myChart = new Chart(statistics_chart, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($earning_chart_labels); ?>,
      datasets: [{
        label: '<?php echo $this->lang->line("Earning"); ?>',
        data: <?php echo json_encode(array_values($earning_chart_values)); ?>,
        borderWidth: 0,
        borderColor: 'transparent',
        backgroundColor: 'rgba(103, 119, 239, 0.6)',
        pointBackgroundColor: 'rgba(103, 119, 239, 2)',
        pointBorderColor: 'transparent',
        pointRadius: 4
      }]
    },
    options: {
      legend: {
        display: false
      },
      scales: {
        yAxes: [{
          gridLines: {
            display: true,
            color: 'rgba(0, 0, 0, 0.1)',
          },
          ticks: {
            stepSize: <?php echo $steps; ?>
          }
        }],
        xAxes: [{
          gridLines: {
            display: true,
            color: 'rgba(0, 0, 0, 0.1)',
          }
        }]
      },
    }
  });

  var month_chart = document.getElementById("month-chart").getContext('2d');

  var month_chart_bg_color = month_chart.createLinearGradient(0, 0, 0, 70);
  month_chart_bg_color.addColorStop(0, 'rgba(63,82,227,.2)');
  month_chart_bg_color.addColorStop(1, 'rgba(63,82,227,0)');

  var myChart1 = new Chart(month_chart, {
    type: 'line',
    data: {
      labels: <?php echo json_encode(array_keys($array_month));?>,
      datasets: [{
        label: '<?php echo $this->lang->line("Earning");?>',
        data: <?php echo json_encode(array_values($array_month)) ;?>,
        backgroundColor: month_chart_bg_color,
        borderWidth: 3,
        borderColor: 'rgba(63,82,227,1)',
        pointBorderWidth: 0,
        pointBorderColor: 'transparent',
        pointRadius: 3,
        pointBackgroundColor: 'transparent',
        pointHoverBackgroundColor: 'rgba(63,82,227,1)',
      }]
    },
    options: {
      layout: {
        padding: {
          bottom: -1,
          left: -1
        }
      },
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
            beginAtZero: true,
            display: false
          }
        }],
        xAxes: [{
          gridLines: {
            drawBorder: false,
            display: false,
          },
          ticks: {
            display: false
          }
        }]
      },
    }
  }); 

  var year_chart = document.getElementById("year-chart").getContext('2d');

  var year_chart_bg_color = year_chart.createLinearGradient(0, 0, 0, 80);
  year_chart_bg_color.addColorStop(0, 'rgba(63,82,227,.2)');
  year_chart_bg_color.addColorStop(1, 'rgba(63,82,227,0)');

  var myChart2 = new Chart(year_chart, {
    type: 'line',
    data: {
      labels: <?php echo json_encode(array_keys($array_year));?>,
      datasets: [{
        label:  '<?php echo $this->lang->line("Earning");?>',
        data: <?php echo json_encode(array_values($array_year));?>,
        borderWidth: 2,
        backgroundColor: year_chart_bg_color,
        borderWidth: 3,
        borderColor: 'rgba(63,82,227,1)',
        pointBorderWidth: 0,
        pointBorderColor: 'transparent',
        pointRadius: 3,
        pointBackgroundColor: 'transparent',
        pointHoverBackgroundColor: 'rgba(63,82,227,1)',
      }]
    },
    options: {
      layout: {
        padding: {
          bottom: -1,
          left: -1
        }
      },
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
            beginAtZero: true,
            display: false
          }
        }],
        xAxes: [{
          gridLines: {
            drawBorder: false,
            display: false,
          },
          ticks: {
            display: false
          }
        }]
      },
    }
  }); 
</script>
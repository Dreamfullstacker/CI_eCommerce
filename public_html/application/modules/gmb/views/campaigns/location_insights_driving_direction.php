<?php $this->load->view('admin/theme/message'); ?>
<style>
    canvas{
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
</style>
<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-list"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item">
                <form method="POST" action="<?php echo base_url("campaigns/location_insights_basic/{$location_table_id}"); ?>">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </div>
                        <?php
                            if (isset($number_of_days) && count($number_of_days)) {
                                echo form_dropdown(
                                    'cta_action_type',
                                    $number_of_days,
                                    null,
                                    'class="form-control select2" id="cta_action_type" required style="width:100%;"'
                                );
                            }
                        ?>
                        <input type="hidden" class="form-control" value="<?php echo $location_name; ?>" name="location_name">
                        <button class="btn btn-outline-primary" style="margin-left:1px" type="submit"><i class="fa fa-search"></i> <?php echo $this->lang->line("Search");?></button>
                    </div>
                </form>
            </div>
        </div>
        <h3 id="section-header-title" class="text-capitalize"></h3>
    </div>

    <div class="section-body">

        <!-- starts QUERIES_DIRECT and QUERIES_INDIRECT -->
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Queries Direct'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-toggle="tooltip"
                                data-original-title="<?php echo $this->lang->line("The number of times the resource was shown when searching for the location directly."); ?>"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="queries_direct" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Queries Indirect'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-original-title="<?php echo $this->lang->line("The number of times the resource was shown as a result of a categorical search (for example, restaurant)."); ?>"
                                data-toggle="tooltip"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="queries_indirect" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- ends ./row -->

        <!-- starts QUERIES_CHAIN chain and VIEWS_MAPS -->
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Queries Chain'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-toggle="tooltip"
                                data-original-title="<?php echo $this->lang->line("	The number of times a resource was shown as a result of a search for the chain it belongs to, or a brand it sells. For example, Starbucks, Adidas. This is a subset of QUERIES_INDIRECT."); ?>"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="queries_chain" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Views Maps'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-toggle="tooltip"
                                data-original-title="<?php echo $this->lang->line("The number of times the resource was viewed on Google Maps."); ?>"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="views_maps" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- ends ./row -->

        <!-- starts VIEWS_SEARCH and ACTIONS_WEBSITE -->
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Views Search'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-toggle="tooltip"
                                data-original-title="<?php echo $this->lang->line("The number of times the resource was viewed on Google Search."); ?>"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="views_search" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Actions Website'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-toggle="tooltip"
                                data-original-title="<?php echo $this->lang->line("The number of times the website was clicked."); ?>"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="actions_website" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- ends ./row -->

        <!-- starts ACTIONS_PHONE and ACTIONS_DRIVING_DIRECTIONS -->
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Actions Phone'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-toggle="tooltip"
                                data-original-title="<?php echo $this->lang->line("The number of times the phone number was clicked."); ?>"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="actions_phone" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Actions Driving Directions'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-toggle="tooltip"
                                data-original-title="<?php echo $this->lang->line("The number of times driving directions were requested."); ?>"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="actions_driving_directions" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- ends ./row -->

        <!-- starts ACTIONS_PHONE and ACTIONS_DRIVING_DIRECTIONS -->
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Photos Views Merchant'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-toggle="tooltip"
                                data-original-title="<?php echo $this->lang->line("The number of views on media items uploaded by the merchant."); ?>"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="photos_views_merchant" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Photos Views Customers'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-toggle="tooltip"
                                data-original-title="<?php echo $this->lang->line("The number of views on media items uploaded by customers."); ?>"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="photos_views_customers" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- ends ./row -->

        <!-- starts PHOTOS_COUNT_MERCHANT and PHOTOS_COUNT_CUSTOMERS -->
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Photos Count Merchant'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-toggle="tooltip"
                                data-original-title="<?php echo $this->lang->line("The total number of media items that are currently live that have been uploaded by the merchant."); ?>"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="photos_count_merchant" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Photos Count Customers'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-toggle="tooltip"
                                data-original-title="<?php echo $this->lang->line("The total number of media items that are currently live that have been uploaded by customers."); ?>"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="photos_count_customers" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- ends ./row -->

        <!-- starts LOCAL_POST_VIEWS_SEARCH and LOCAL_POST_ACTIONS_CALL_TO_ACTION -->
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Local Post Views Search'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-toggle="tooltip"
                                data-original-title="<?php echo $this->lang->line("The number of times the local post was viewed on Google Search."); ?>"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="local_post_views_search" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <?php echo $this->lang->line('Local Post Actions Call To Action'); ?>
                            &nbsp;<i
                                class="fas fa-info-circle"
                                data-toggle="tooltip"
                                data-original-title="<?php echo $this->lang->line("The number of times the call to action button was clicked on Google."); ?>"
                            ></i>
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="local_post_actions_call_to_action" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- ends ./row -->

    </div>
</section>

<script>
    $(document).ready(function($) {
        'use strict';

        var base_url = '<?php echo base_url(); ?>';

        var queries_direct = '<?php echo isset($post_insights['QUERIES_DIRECT']) ? json_encode($post_insights['QUERIES_DIRECT']) : ''; ?>',
            queries_indirect = '<?php echo isset($post_insights['QUERIES_INDIRECT']) ? json_encode($post_insights['QUERIES_INDIRECT']) : ''; ?>',
            queries_chain = '<?php echo isset($post_insights['QUERIES_CHAIN']) ? json_encode($post_insights['QUERIES_CHAIN']) : ''; ?>',
            views_maps = '<?php echo isset($post_insights['VIEWS_MAPS']) ? json_encode($post_insights['VIEWS_MAPS']) : ''; ?>',
            views_search = '<?php echo isset($post_insights['VIEWS_SEARCH']) ? json_encode($post_insights['VIEWS_SEARCH']) : ''; ?>',
            actions_website = '<?php echo isset($post_insights['ACTIONS_WEBSITE']) ? json_encode($post_insights['ACTIONS_WEBSITE']) : ''; ?>',
            actions_phone = '<?php echo isset($post_insights['ACTIONS_PHONE']) ? json_encode($post_insights['ACTIONS_PHONE']) : ''; ?>',
            actions_driving_directions = '<?php echo isset($post_insights['ACTIONS_DRIVING_DIRECTIONS']) ? json_encode($post_insights['ACTIONS_DRIVING_DIRECTIONS']) : ''; ?>',
            photos_views_merchant = '<?php echo isset($post_insights['PHOTOS_VIEWS_MERCHANT']) ? json_encode($post_insights['PHOTOS_VIEWS_MERCHANT']) : ''; ?>',
            photos_views_customers = '<?php echo isset($post_insights['PHOTOS_VIEWS_CUSTOMERS']) ? json_encode($post_insights['PHOTOS_VIEWS_CUSTOMERS']) : ''; ?>',
            photos_count_merchant = '<?php echo isset($post_insights['PHOTOS_COUNT_MERCHANT']) ? json_encode($post_insights['PHOTOS_COUNT_MERCHANT']) : ''; ?>',
            photos_count_customers = '<?php echo isset($post_insights['PHOTOS_COUNT_CUSTOMERS']) ? json_encode($post_insights['PHOTOS_COUNT_CUSTOMERS']) : ''; ?>',
            local_post_views_search = '<?php echo isset($post_insights['LOCAL_POST_VIEWS_SEARCH']) ? json_encode($post_insights['LOCAL_POST_VIEWS_SEARCH']) : ''; ?>',
            local_post_actions_call_to_action = '<?php echo isset($post_insights['LOCAL_POST_ACTIONS_CALL_TO_ACTION']) ? json_encode($post_insights['LOCAL_POST_ACTIONS_CALL_TO_ACTION']) : ''; ?>';

        // 1
        if ('object' === typeof JSON.parse(queries_direct)) {
            var qd = JSON.parse(queries_direct);
            var queries_direct_ctx = document.getElementById("queries_direct").getContext('2d');
            new Chart(queries_direct_ctx, {
                type: 'line',
                data: {
                    labels: qd.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line('Searched directly'); ?>',
                        data: qd.value,
                        backgroundColor: 'rgba(0, 151, 255, 0.3)',
                        borderColor: '#00ABFF',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#ffffff',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Queries Direct'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }

        // 2
        if ('object' === typeof JSON.parse(queries_indirect)) {
            var queries_indirect_obj = JSON.parse(queries_indirect);
            var queries_indirect_ctx = document.getElementById("queries_indirect").getContext('2d');
            new Chart(queries_indirect_ctx, {
                type: 'line',
                data: {
                    labels: queries_indirect_obj.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line('Searched indirectly'); ?>',
                        data: queries_indirect_obj.value,
                        borderColor: '#0DDEFF',
                        fill: false,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#00ABFF',
                        pointBorderWidth: 3,
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Queries indirect'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }

        // 3
        if ('object' === typeof JSON.parse(queries_chain)) {
            var queries_chain_obj = JSON.parse(queries_chain);
            var queries_chain_ctx = document.getElementById("queries_chain").getContext('2d');
            new Chart(queries_chain_ctx, {
                type: 'line',
                data: {
                    labels: queries_chain_obj.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line('Searched chain'); ?>',
                        data: queries_chain_obj.value,
                        borderColor: '#F2CD5C',
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#df7000',
                        pointBorderWidth: 3,
                        pointRadius: 4,
                        fill: false,
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Queries chain'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }

        // 4
        if ('object' === typeof JSON.parse(views_maps)) {
            var views_maps_obj = JSON.parse(views_maps);
            var views_maps_ctx = document.getElementById("views_maps").getContext('2d');
            new Chart(views_maps_ctx, {
                type: 'line',
                data: {
                    labels: views_maps_obj.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line('Views on map'); ?>',
                        data: views_maps_obj.value,
                        backgroundColor: 'rgba(31, 8, 255, 0.5)',
                        borderColor: 'transparent',
                        borderWidth: 0,
                        pointBackgroundColor: '#7224ff',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Views maps'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }

        // 5
        if ('object' === typeof JSON.parse(views_search)) {
            var views_search_obj = JSON.parse(views_search);
            var views_search_ctx = document.getElementById("views_search").getContext('2d');
            new Chart(views_search_ctx, {
                type: 'bar',
                data: {
                    labels: views_search_obj.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line('Viewed on google'); ?>',
                        data: views_search_obj.value,
                        backgroundColor: 'rgba(242, 48, 100, 0.6)',
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Views search'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }

        // 6
        if ('object' === typeof JSON.parse(actions_website)) {
            var actions_website_obj = JSON.parse(actions_website);
            var actions_website_ctx = document.getElementById("actions_website").getContext('2d');
            new Chart(actions_website_ctx, {
                type: 'bar',
                data: {
                    labels: actions_website_obj.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line('Website clicked'); ?>',
                        data: actions_website_obj.value,
                        backgroundColor: 'rgba(1, 4, 64, 0.7)',
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Actions website'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }

        // 7
        if ('object' === typeof JSON.parse(actions_phone)) {
            var actions_phone_obj = JSON.parse(actions_phone);
            var actions_phone_ctx = document.getElementById("actions_phone").getContext('2d');
            new Chart(actions_phone_ctx, {
                type: 'line',
                data: {
                    labels: actions_phone_obj.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line("Phone number clicked"); ?>',
                        data: actions_phone_obj.value,
                        backgroundColor: 'rgba(32, 140, 101, 0.3)',
                        borderColor: '#208C65',
                        borderWidth: 4,
                        pointBackgroundColor: '#ffffff',
                        pointRadius: 0,
                        fill: 'origin'
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Views search'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }

        // 8
        if ('object' === typeof JSON.parse(actions_driving_directions)) {
            var actions_driving_directions_obj = JSON.parse(actions_driving_directions);
            var actions_driving_directions_ctx = document.getElementById("actions_driving_directions").getContext('2d');
            new Chart(actions_driving_directions_ctx, {
                type: 'line',
                data: {
                    labels: actions_driving_directions_obj.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line("driving directions requested"); ?>',
                        data: actions_driving_directions_obj.value,
                        backgroundColor: 'rgba(85, 140, 3, 0.3)',
                        borderColor: '#558C03',
                        borderWidth: 4,
                        borderDash: [4, 4],
                        pointBackgroundColor: '#ffffff',
                        pointRadius: 0,
                        fill: 'origin'
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Actions driving directions'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }

        // 9
        if ('object' === typeof JSON.parse(photos_views_merchant)) {
            var photos_views_merchant_obj = JSON.parse(photos_views_merchant);
            var photos_views_merchant_ctx = document.getElementById("photos_views_merchant").getContext('2d');
            new Chart(photos_views_merchant_ctx, {
                type: 'line',
                data: {
                    labels: photos_views_merchant_obj.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line('Media uploaded by merchant viewed'); ?>',
                        data: photos_views_merchant_obj.value,
                        backgroundColor: 'rgba(255, 82, 79, 0.3)',
                        borderColor: '#FF524F',
                        borderWidth: 1,
                        borderDash: [1, 1],
                        pointBackgroundColor: '#ffffff',
                        pointRadius: 4,
                        fill: 'origin'
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Photos views merchant'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }

        // 10
        if ('object' === typeof JSON.parse(photos_views_customers)) {
            var photos_views_customers_obj = JSON.parse(photos_views_customers);
            var photos_views_customers_ctx = document.getElementById("photos_views_customers").getContext('2d');
            new Chart(photos_views_customers_ctx, {
                type: 'line',
                data: {
                    labels: photos_views_customers_obj.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line("Media uploaded by customers viewed"); ?>',
                        data: photos_views_customers_obj.value,
                        backgroundColor: 'rgba(146, 0, 255, 0.3)',
                        borderColor: '#F84BFF',
                        borderWidth: 1,
                        pointBackgroundColor: '#ffffff',
                        pointRadius: 4,
                        fill: 'origin'
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Photos views customers'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }

        // 11
        if ('object' === typeof JSON.parse(photos_count_merchant)) {
            var photos_count_merchant_obj = JSON.parse(photos_count_merchant);
            var photos_count_merchant_ctx = document.getElementById("photos_count_merchant").getContext('2d');
            new Chart(photos_count_merchant_ctx, {
                type: 'line',
                data: {
                    labels: photos_count_merchant_obj.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line('Live media items by merchant'); ?>',
                        data: photos_count_merchant_obj.value,
                        borderColor: 'transparent',
                        pointBackgroundColor: '#ffffff',
                        pointBorderWidth: 5,
                        pointBorderColor: 'rgba(0,81,255,0.8)',
                        pointRadius: 5,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Photos count merchant'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }

        // 12
        if ('object' === typeof JSON.parse(photos_count_customers)) {
            var photos_count_customers_obj = JSON.parse(photos_count_customers);
            var photos_count_customers_ctx = document.getElementById("photos_count_customers").getContext('2d');
            new Chart(photos_count_customers_ctx, {
                type: 'line',
                data: {
                    labels: photos_count_customers_obj.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line('Live media items by customers'); ?>',
                        data: photos_count_customers_obj.value,
                        borderColor: 'transparent',
                        pointBackgroundColor: '#F2F2F2',
                        pointBorderColor: 'rgba(98, 4, 191, 0.8)',
                        pointBorderWidth: 5,
                        pointRadius: 5,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Photos count customers'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }

        // 13
        if ('object' === typeof JSON.parse(local_post_views_search)) {
            var local_post_views_search_obj = JSON.parse(local_post_views_search);
            var local_post_views_search_ctx = document.getElementById("local_post_views_search").getContext('2d');
            new Chart(local_post_views_search_ctx, {
                type: 'line',
                data: {
                    labels: local_post_views_search_obj.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line('Local post viewed'); ?>',
                        data: local_post_views_search_obj.value,
                        backgroundColor: 'rgba(89, 11, 62, 0.3)',
                        borderColor: '#590B3E',
                        borderWidth: 1,
                        pointBackgroundColor: 'transparent',
                        pointRadius: 0,
                        fill: 'origin'
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Local post views search'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }

        // 14
        if ('object' === typeof JSON.parse(local_post_actions_call_to_action)) {
            var local_post_actions_call_to_action_obj = JSON.parse(local_post_actions_call_to_action);
            var local_post_actions_call_to_action_ctx = document.getElementById("local_post_actions_call_to_action").getContext('2d');
            new Chart(local_post_actions_call_to_action_ctx, {
                type: 'line',
                data: {
                    labels: local_post_actions_call_to_action_obj.date,
                    datasets: [{
                        label: '<?php echo $this->lang->line('CTA button clicked'); ?>',
                        data: local_post_actions_call_to_action_obj.value,
                        backgroundColor: 'rgba(255, 185, 43, 0.3)',
                        borderColor: '#FFB92B',
                        borderWidth: 1,
                        pointBackgroundColor: 'transparent',
                        pointRadius: 0,
                        fill: 'end'
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: '<?php echo $this->lang->line('Local post actions call to action'); ?>',
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)',
                            },
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        }]
                    },
                }
            });
        }
    });
</script>

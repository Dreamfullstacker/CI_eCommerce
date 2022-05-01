<?php 
    $general_signup_commission = isset($info['signup_commission']) ? $info['signup_commission']:0;
    $general_sign_up_amount = isset($info['sign_up_amount']) ? $info['sign_up_amount']:'';
    $general_payment_commission = isset($info['payment_commission']) ? $info['payment_commission']:0;
    $general_payment_type = isset($info['payment_type']) ? $info['payment_type']:'fsddsf';
    $general_payment_percentage = isset($info['percentage']) ? $info['percentage']."%":'';
    $general_payment_fixed_amount = isset($info['fixed_amount']) ? $info['fixed_amount']:'';
    $general_is_recurring = isset($info['is_recurring']) ? $info['is_recurring']:0;
    $payment_amount = '0';

    if($general_payment_type !='' && $general_payment_type == 'fixed') {
        $payment_amount = $general_payment_fixed_amount;
    }
    if($general_payment_type !='' && $general_payment_type == 'percentage') {
        $payment_amount = $general_payment_percentage;
    }


    if($affiliate_info['is_overwritten'] == '1') {
        if($affiliate_info['is_signup_commission'] == '1') $general_sign_up_amount = $affiliate_info['signup_amount'];

        if($affiliate_info['is_payment'] == '1') {
            $general_payment_type = isset($affiliate_info['payment_type']) ? $affiliate_info['payment_type']:"";
            if($general_payment_type !='' && $general_payment_type == 'fixed') {
                $payment_amount = $affiliate_info['fixed_amount'];
            }
            if($general_payment_type !='' && $general_payment_type == 'percentage') {
                $payment_amount = $affiliate_info['percentage_amount'].'%';
            }
        }
    }


?>
<section class="section">
    <?php $this->load->view('admin/theme/message'); ?>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-lg-4 col-12">
                        <div class="card card-statistic-1 pointer" data-toggle='tooltip' title="<?php echo $this->lang->line("You will get commission on every user signup who have come through the affiliation link."); ?>">
                            <div class="card-icon bg-primary">
                                <i class="far fa-user"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4><?php echo $this->lang->line('Signup Commission amount'); ?></h4>
                                </div>
                                <div class="card-body mt-2">
                                    <?php echo $curency_icon.$general_sign_up_amount; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="card card-statistic-1 pointer" data-toggle='tooltip' title="<?php echo $this->lang->line("Affiliate will get fixed/percentage commission on package buying."); ?>">
                            <div class="card-icon bg-warning">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4><?php echo $this->lang->line('Payment Commission Type'); ?></h4>
                                </div>
                                <div class="card-body mt-2">
                                    <?php echo ucfirst($general_payment_type); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="card card-statistic-1 pointer" data-toggle='tooltip' title="<?php echo $this->lang->line("Affiliate will get commission on every user signup who have come through the affiliation link."); ?>">
                            <div class="card-icon bg-danger">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4><?php echo $this->lang->line('Payment Commission Amount'); ?></h4>
                                </div>
                                <div class="card-body mt-2">
                                    <?php echo $payment_amount; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">			
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-link"></i> <?php echo $this->lang->line('Affiliate Link'); ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center" id="gif_div">
                                    <img width="30%" class="center-block" src="<?php echo base_url('assets/pre-loader/loading-animations.gif'); ?>" alt="Processing...">
                                </div>
                                <div id="link_div" style="display: none;">
                                    <pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword"><?php echo site_url().'?ref='.bin2hex($this->session->userdata('affiliate_userid')); ?></span></code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


<script>
    $(document).ready(function($) {
        setTimeout(function(){
            $("#gif_div").hide(500);
            $("#link_div").show(500);
        },1000);
    });
</script>
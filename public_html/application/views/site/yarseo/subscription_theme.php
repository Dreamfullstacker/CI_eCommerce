<?php
	include_once 'translator.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset=utf-8>
<meta http-equiv=X-UA-Compatible content="IE=edge">
<meta name=viewport content="width=device-width, initial-scale=1">
<title><?php echo $this->config->item('product_name'); if($this->config->item('slogan')!='') echo " | ".$this->config->item('slogan')?></title>
<meta name="description" content="<?php echo $this->config->item('slogan'); ?>">
<meta name="author" content="<?php echo $this->config->item('institute_address1');?>">

<?php
	include 'includes/css.php';
?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-social/bootstrap-social.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fontawesome/css/all.min.css">
<script src="<?php echo base_url(); ?>assets/modules/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/sweetalert/sweetalert.min.js"></script>

<style>
.btn-social>:first-child {
left: 5px;
bottom: 5px;
border-right: none;
}
.vh-100 {
    min-height: 100vh;
}
body {
    background: #fff;
}
</style>

</head>
<body data-spy="scroll" data-target="#main-navbar">
<div class="loader bg-white">
<div class="loader-inner ball-scale-ripple-multiple vh-center">
<div></div>
<div></div>
<div></div>
</div>
</div>
<div class="main-container" id="page">
<section id="content12-1" class="content-split bg-edit">

<?php echo $this->load->view($body); ?>

</section>
</div>
</div>

</div>
<?php
	include 'includes/js.php';
?>
<?php $this->load->view("include/fb_px"); ?> 
<?php $this->load->view("include/google_code"); ?>

</body>
</html>
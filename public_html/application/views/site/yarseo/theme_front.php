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
<meta name=description content>
<meta name=keywords content>
<meta name=author content>

<?php
	include 'includes/css.php';
?>

    <?php if($this->uri->segment('1')=='blog' && $this->uri->segment('2')=='post_details') 
    {
        $ogtitle = $this->config->item("product_short_name")." | ".$post[0]['title'];
        $ogdesc = mb_substr(strip_tags($post[0]["body"]), 0,200);
        $ogtitle = str_replace(array("'",'"',"\\"), array('`','`','/'), $ogtitle );
        $ogdesc = str_replace(array("'",'"',"\\"), array('`','`','/'), $ogdesc );
        ?>
        <meta name="keywords" content="<?php echo $post[0]["tags"]; ?>">
        <meta name="author" content="<?php echo $this->config->item("product_short_name");?>">
        <meta name="copyright" content="<?php echo $this->config->item("product_short_name");?>" />
        <meta name="application-name" content="<?php echo $this->config->item("product_short_name");?>" />  
        <meta property="og:type" content="website" />
        <meta property="og:url" content="<?php echo current_url(); ?>"/>
        <meta name="twitter:card" content="summary" />
        <meta property="og:title" content='<?php echo $ogtitle; ?>' />
        <meta name="twitter:title" content='<?php echo $ogtitle; ?>' />
        <meta property="og:description" content="<?php echo $ogdesc; ?>" />
        <meta name="twitter:description" content="<?php echo $ogdesc; ?>" />
        <meta name="description" content="<?php echo $ogdesc; ?>">
        <?php if($post[0]['thumbnail'] !=''): ?>
        <meta property="og:image" content="<?php echo base_url('upload/blog/'.$post[0]['thumbnail']); ?>" />
        <meta name="twitter:image" content="<?php echo base_url('upload/blog/'.$post[0]['thumbnail']); ?>" />
        <?php endif; ?>
    <?php 
    } ?>

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

<?php
	include 'includes/nav.php';
?>

<?php $this->load->view($body); ?>

<?php
	include 'includes/footer.php';
	include 'includes/js.php';
?>

<?php $this->load->view("include/fb_px"); ?> 
<?php $this->load->view("include/google_code"); ?> 
<?php include("application/modules/blog/views/blog_js.php"); ?>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
	<head>
	  <meta charset="UTF-8">
	  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
	  <title><?php echo $this->config->item('product_name')." | ".$page_title;?></title>
	  <link rel="shortcut icon" href="<?php echo base_url();?>assets/img/favicon.png"> 
	  <?php 
	  include(FCPATH.'application/views/include/css_include_back.php'); 
	  include(FCPATH.'application/views/include/js_include_back.php'); 
	  ?>
	</head>

	<body>
	  <div id="app">
	    <div class="main-wrapper">
			<?php 
			include(FCPATH.'application/modules/affiliate_system/views/affiliate_theme/header.php');

			include(FCPATH.'application/modules/affiliate_system/views/affiliate_theme/sidebar.php');
			echo '<div class="main-content">';
				$this->load->view($body);
			echo '</div>';
			include(FCPATH.'application/views/admin/theme/footer.php'); ?>
		</div>
	  </div>
	</body>
</html>

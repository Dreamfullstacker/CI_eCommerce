<?php 
foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>

<?php $i=0; foreach($js_files as $file): if($i==1) { ?>

	<script> var $crud = $.noConflict(); </script>

	<?php }  $i++; ?>
	
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>
<?php echo $output; ?>
<style>
	.quickSearchBox{display:block !important;}
</style>

<script type="text/javascript">
	$j('#field-expired_date').datetimepicker({
	  theme:'light',
	  format:'Y-m-d',
	  formatDate:'Y-m-d',
	  timepicker:false
	});  
</script>

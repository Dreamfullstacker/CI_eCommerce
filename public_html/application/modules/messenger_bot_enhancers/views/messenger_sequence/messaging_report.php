<?php $this->load->view('admin/theme/message'); ?>

<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'><i class="fa fa-history"></i> <?php echo $this->lang->line("Message Sent Log");?></h1>
</section>
<section class="content">
	<div class="row" >
		<div class="col-xs-12">
			<div class="grid_container" style="width:100%; height:840px;">
				<table
				id="tt"
				class="easyui-datagrid"
				url="<?php echo base_url()."drip_messaging/messaging_report_data"; ?>"

				pagination="true"
				rownumbers="true"
				toolbar="#tb"
				pageSize="15"
				pageList="[5,10,15,20,50,100]"
				fit= "true"
				fitColumns= "true"
				nowrap= "true"
				view= "detailview"
				idField="id"
				>

					<thead>
						<tr>							
							<th field="subscriber" sortable="true"><?php echo $this->lang->line("subscriber name"); ?></th>
							<th field="subscribe_id" align="center" sortable="true"><?php echo $this->lang->line("subscriber ID"); ?></th>
							<th field="page_name" sortable="true"><?php echo $this->lang->line("page"); ?></th>
							<th field="campaign_name" sortable="true"><?php echo $this->lang->line("campaign name"); ?></th>
							<th field="campaign_details" align="center"><?php echo $this->lang->line("campaign info"); ?></th>
							<th field="status" align="center" sortable="true"><?php echo $this->lang->line("sent status"); ?></th>
							<th field="last_completed_day" align="center" sortable="true"><?php echo $this->lang->line("completed day"); ?></th>
							<th field="sent_at" align="center" sortable="true"><?php echo $this->lang->line("sent at"); ?></th>
							<th field="delivered_at" align="center" sortable="true"><?php echo $this->lang->line("delivered at"); ?></th>
							<th field="opened_at" align="center" sortable="true"><?php echo $this->lang->line("opened at"); ?></th>
							<!-- <th field="last_updated_at" align="center" sortable="true"><?php echo $this->lang->line("last updated at"); ?></th> -->
							<!-- <th field="response"><?php echo $this->lang->line("response"); ?></th> -->
						</tr>
					</thead>
				</table>
			</div>

			<div id="tb" style="padding:3px">
				<?php
					$search_campaign_name  = $this->session->userdata('drip_messaging_report_campaign_name');
			        $search_drip_type  = $this->session->userdata('drip_messaging_report_drip_type');
			        $search_page_id  = $this->session->userdata('drip_messaging_report_page_id');
				?>
			

				<form class="form-inline" style="margin-top:20px">

					<div class="form-group">
						<input id="search_campaign_name" name="search_campaign_name" value="<?php echo $search_campaign_name;?>" class="form-control" size="20" placeholder="<?php echo $this->lang->line("campaign name") ?>">
					</div>

					<div class="form-group">
						<select name="search_page" id="search_page"  class="form-control">
							<option value=""><?php echo $this->lang->line("all page") ?></option>
							<?php
								foreach ($page_info as $key => $value)
								{
									if($value['id'] == $search_page_id)
									echo "<option selected value='".$value['id']."'>".$value['page_name']."</option>";
									else echo "<option value='".$value['id']."'>".$value['page_name']."</option>";
								}
							?>
						</select>
					</div>

					<div class="form-group">
						<?php 
						$drip_types['']='all drip types'; 
						foreach ($drip_types as $key => $value) 
						{
							$drip_types[$key]=$this->lang->line($value);
						} 
						echo form_dropdown('search_drip_type', $drip_types, $search_drip_type,'class="form-control" id="search_drip_type"'); ?>
					</div>

					<button class='btn btn-info'  onclick="doSearch(event)"><i class="fa fa-search"></i> <?php echo $this->lang->line("search");?></button>
			
				</div>

				</form>
		</div>
	</div>
</section>

<script>

	var base_url="<?php echo site_url(); ?>";

    function doSearch(event)
	{
		event.preventDefault();
		$j('#tt').datagrid('load',{
			search_campaign_name   :     $j('#search_campaign_name').val(),
			search_page   		:     $j('#search_page').val(),
			search_drip_type  :     $j('#search_drip_type').val(),
			is_searched		:     1
		});

	}

</script>


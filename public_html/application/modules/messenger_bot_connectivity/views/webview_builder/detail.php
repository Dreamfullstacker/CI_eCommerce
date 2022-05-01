<section class="section">
	<div class="section-header">
		<h1>Form Details</h1>
		<div class="section-header-button">
	     	<a class="btn btn-primary" href="<?= base_url('webview_builder') ?>">
	        <i class="fas fa-plus-circle"></i> <?= $this->lang->line('New Form') ?></a>
	    </div>
	</div>
	<div class="section-body">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body data-card">
						<div class="table-responsive">
							<table id="webview-datatable" class="table table-bordered" style="width:100%">
						        <thead>
						            <tr>
						                <th>#</th>
						                <th>Title</th>
						                <th>Page</th>
						                <th>Label</th>
						                <th>Template</th>
						                <th>Inserted At</th>
						            </tr>
						        </thead>
						        <?php if (count($form_details)): ?>
							        <tbody>
							        	<tr>
							        		<td></td>
							        	</tr>
							        </tbody>
						    	<?php endif; ?>
						    </table>
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fa fa-search-location"></i> <?php echo $page_title;?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><?php echo $page_title;?></div>
        </div>
    </div>

    <div class="section-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?php echo $this->lang->line('Add a new board', true); ?></h4>
        </div>
        <div class="card-body">
            <form class="formAjax" action="<?php echo base_url(); ?>n_task/new_board" method="post">
                <div class="row">
                    <div class="col-12">
                        <label for="board_name_add"><?php echo $this->lang->line('Title', true); ?>:</label>
                        <fieldset class="form-group position-relative has-icon-left">
                            <input type="text" class="form-control task_title" name="board_name" id="board_name_add" placeholder="<?php echo $this->lang->line('Title', true); ?>">
                            <div class="form-control-position">
                                <i class="fa fa-text"></i>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <fieldset class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="template_add">Template</label>
                                </div>
                                <select class="form-control" id="template_add" name="template">
                                    <option value="0" selected>Blank</option>
                                    <option value="1">GTD Method</option>
                                    <option value="2">Basic ToDo</option>
                                    <option value="3">Customer introduce</option>
                                </select>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('Add new board', true); ?></button>
            </form>
        </div>
    </div>
</div>

</section>
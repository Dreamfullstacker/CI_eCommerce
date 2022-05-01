<!------------------ ############################ MODALS ########################################## -->

<div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><?php echo $this->lang->line('Add Task', true); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <form class="formAjax" action="<?php echo base_url(); ?>n_task/save_task" method="post">

                <div class="modal-body">
                    <input id="task_container" name="task_container" type="hidden" value="" />
                    <div class="row">
                        <div class="col-12">
                            <label for="task_title"><?php echo $this->lang->line('Title', true); ?>:</label>
                            <fieldset class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" name="task_title" id="task_title" placeholder="<?php echo $this->lang->line('Title', true); ?>">
                            </fieldset>
                        </div>
                        <div class="col-12">
                            <label for="task_description"><?php echo $this->lang->line('Description', true); ?>:</label>
                            <fieldset class="form-group">
                                <textarea class="form-control" id="task_description" name="task_description" rows="3" placeholder="<?php echo $this->lang->line('Description', true); ?>"></textarea>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row to_do">
                        <div class="col-12">
                            <fieldset>
                                <label for="task_todo"><?php echo $this->lang->line('Subtask', true); ?>:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control todoInput" placeholder="<?php echo $this->lang->line('Title', true); ?>" id="AddTodoInput">
                                    <div class="input-group-append" id="button-addon2">
                                        <button class="addBtn btn btn-primary newTaskAddBtn" type="button"><?php echo $this->lang->line('Add', true); ?></button>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="form-group mt-1">
                                <input type="hidden" name="task_todo" id="add_task_todo" value="" />
                                <ul id="newTaskTodoUl" class="todo_ul">
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <label for="task_time_estimate"><?php echo $this->lang->line('Time estimate (hh:mm)', true); ?>:</label>
                            <fieldset class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" name="task_time_estimate" id="task_time_estimate" value="00:00">
                            </fieldset>
                        </div>


                        <div class="col-md-4">
                            <label for="task_time_spent"><?php echo $this->lang->line('Time spent (hh:mm)', true); ?>:</label>
                            <fieldset class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" name="task_time_spent" id="task_time_spent" value="00:00">
                            </fieldset>
                        </div>

                        <div class="col-md-3">
                            <label for="task_color2" class="form-control-label"><?php echo $this->lang->line('Color', true); ?>:</label>
                            <div class="form-group">
                                <input id="task_color2" class="form-control colorPicker" name="task_color" value="" />
                            </div>
                        </div>
                    </div>

                    <fieldset>
                        <label for="task_due_date_add" class="form-control-label"><?php echo $this->lang->line('Due date', true); ?>:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="far fa-calendar"></i></span>
                            </div>
                            <input id="task_due_date_add" name="task_due_date" type="text" class="form-control datetimepicker" />
                        </div>
                    </fieldset>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close_button" data-dismiss="modal"><?php echo $this->lang->line('Close', true); ?></button>

                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('Save task', true); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- *************************** E D I T MODAL ********************************** -->
<div class="modal fade" id="editTaskModal" tabindex="1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Task', true); ?>: <span class="task_header"></span></h4>
                <small><?php echo $this->lang->line('Created by', true); ?>: <span class="task_user_name"></span></small>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs justify-content-center" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab_edit"><?php echo $this->lang->line('Edit Task', true); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab_attachments"><?php echo $this->lang->line('Attachments', true); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab_periods"><?php echo $this->lang->line('Working periods', true); ?></a>
                    </li>
                </ul>
                <div class="tab-content bck">
                    <div id="tab_edit" class="tab-pane active">
                        <form class="formAjax" action="<?php echo base_url(); ?>n_task/edit_task/" method="post">
                            <input class="task_id" type="hidden" name="task_id" value="" />
                            <div class="row">
                                <div class="col-12">
                                    <label for="task_title_edit"><?php echo $this->lang->line('Title', true); ?>:</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control task_title" name="task_title" id="task_title_edit" placeholder="<?php echo $this->lang->line('Title', true); ?>">
                                    </fieldset>
                                </div>
                                <div class="col-12">
                                    <label for="task_description_edit"><?php echo $this->lang->line('Description', true); ?>:</label>
                                    <fieldset class="form-group">
                                        <textarea class="form-control task_description" id="task_description_edit" name="task_description" rows="3" placeholder="<?php echo $this->lang->line('Description', true); ?>"></textarea>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="row to_do">
                                <div class="col-12">
                                    <fieldset>
                                        <label for="editTodoInput"><?php echo $this->lang->line('Subtask', true); ?>:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control todoInput" placeholder="<?php echo $this->lang->line('Title', true); ?>" id="editTodoInput">
                                            <div class="input-group-append" id="button-addon2">
                                                <button class="addBtn btn btn-primary" id="editTaskAddBtn" type="button"><?php echo $this->lang->line('Add', true); ?></button>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <div class="form-group mt-1">
                                        <input type="hidden" name="task_todo" id="edit_task_todo" value="" />
                                        <ul id="editTaskTodoUl" class="todo_ul todo_ul_edit_mode">
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5">
                                    <label for="task_time_estimate_edit" class="form-control-label"><?php echo $this->lang->line('Time estimate', true); ?>:</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control task_time_estimate" name="task_time_estimate" id="task_time_estimate_edit" value="00:00" />
                                    </fieldset>
                                </div>

                                <div class="col-md-4">
                                    <label for="task_time_spent_edit" class="form-control-label"><?php echo $this->lang->line('Time spent', true); ?>:</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="task_time_spent form-control" name="task_time_spent" id="task_time_spent_edit" value="00:00">
                                    </fieldset>
                                </div>

                                <div class="col-md-3">
                                    <label for="task_color" class="form-control-label"><?php echo $this->lang->line('Color', true); ?>:</label>
                                    <div class="form-group">
                                        <input id="task_color" class="form-control colorPicker" name="task_color" value="" />
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                            <div class="col-12">
                                <label for="task_due_date" class="form-control-label"><?php echo $this->lang->line('Due date', true); ?>:</label>
                                <fieldset class="form-group position-relative has-icon-left date">
                                    <input id="task_due_date" name="task_due_date" type="text" class="task_due_date form-control datetimepicker" />
                                </fieldset>
                            </div>

                        </div>

                            <div class="form-group">
                                <label for="task_container_edit" class="form-control-label"><?php echo $this->lang->line('Move to column', true); ?>:</label>
                                <select class="form-control task_container" id="task_container_edit" name="task_container">
                                    <?php foreach ($containers as $container) : ?>
                                        <option value="<?php echo $container['container_id']; ?>" style="color:<?php echo $container['container_color']; ?>"><?php echo $container['container_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary close_button" data-dismiss="modal">
                                    <?php echo $this->lang->line('Close', true); ?>
                                </button>
                                <button type="button" class="btn btn-danger" id="delete_task" rel="">
                                    <?php echo $this->lang->line('Delete task', true); ?>
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <?php echo $this->lang->line('Save task', true); ?>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="tab_attachments" class="tab-pane fade in">
                        <table class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th><?php echo $this->lang->line('Filename', true); ?></th>
                                <th><?php echo $this->lang->line('User', true); ?></th>
                                <th><?php echo $this->lang->line('Date', true); ?></th>
                                <th><?php echo $this->lang->line('Action', true); ?></th>
                            </tr>
                            </thead>
                            <tbody class="attachments_body">

                            </tbody>
                        </table>
                        <?php //if ($this->session->userdata('user_session')['user_permissions'] <= 10) :
                        ?>
                            <div class="dropzone_error text-danger"></div>
                            <form action="/n_task/upload-target" class="dropzone dropzone-area dz-clickable p-0" id="dropzone_form">
                                <div class="dz-message mt-0">Drop Files Here To Upload</div>
                                <input class="upload_task_id" type="hidden" name="task_id" value="" />
                            </form>
                        <?php
                        //endif;
                        ?>
                    </div>

                    <div id="tab_periods" class="tab-pane fade in">

                        <div class="row">
                            <div class="col-md-4 text-center">
                                <h4><?php echo $this->lang->line('Date creation', true); ?></h4>
                                <span class="label label-success task_date_creation"></span>
                            </div>
                            <div class="col-md-4 text-center">
                                <h4><?php echo $this->lang->line('Date Closed', true); ?></h4>
                                <span class="label label-danger task_date_closed"></span>
                            </div>
                            <div class="col-md-4 text-center">
                                <h4><?php echo $this->lang->line('Time spent', true); ?></h4>
                                <span class="label label-info total_time_spent"></span>
                            </div>

                        </div>

                        <div class="row" style="margin-top:20px">
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('User', true); ?></th>
                                        <th><?php echo $this->lang->line('From', true); ?></th>
                                        <th><?php echo $this->lang->line('To', true); ?></th>
                                        <th><?php echo $this->lang->line('Total time', true); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody class="periods_body">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>


</div>

<?php if ($task_standby) : ?>
    <div class="modal fade" id="resumeWorkTaskModal" tabindex="-1" role="dialog" aria-labelledby="resumeWorkTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="resumeWorkTaskModalLabel"><?php echo $this->lang->line('Resume work?', true); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        <?php echo $this->lang->line('Hi, in your recent work you have left open the tracking of a task.', true); ?>
                    </p>
                    
                    <ul>
                        <li>
                            <strong><?php echo $this->lang->line('Task title', true); ?>:</strong> <?php echo $task_standby['task_title']; ?>
                        </li>
                        <li>
                            <strong><?php echo $this->lang->line('Time spent', true); ?>:</strong> <?php echo $task_standby['task_time_spent']; ?>
                        </li>
                    </ul>

                    <p>
                        <?php echo $this->lang->line('Last tracking is', true); ?>: <strong><?php echo $task_standby['last_tracking']; ?></strong>
                    </p>

                    <div class="text-center">
                        <h4><?php echo $this->lang->line('What do you want to do', true); ?>?</h4>
                        <a href="<?php echo base_url(); ?>n_task/delete_r/task_periods/task_periods_id/<?php echo $task_standby['task_periods_id']; ?>" class="btn btn-secondary"><?php echo $this->lang->line('Dismiss tracking', true); ?></a>
                        <button type="button" class="btn btn-danger" OnClick="$('.time_tracker_action[rel=<?php echo $task_standby['task_id']; ?>]').trigger('click');$('#resumeWorkTaskModal').modal('hide');" id="delete_task" rel=""><?php echo $this->lang->line('Resume work', true); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<div class="modal fade" id="addColumnModal" tabindex="-1" role="dialog" aria-labelledby="addColumnModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><?php echo $this->lang->line('Add Column', true); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <form class="formAjax " action="<?php echo base_url(); ?>n_task/new_container" method="post">
                <div class="modal-body">
                    <input type="hidden" name="container_board" value="<?php echo $board_id; ?>"/>
                    <div class="row">
                        <div class="col-md-4">
                            <fieldset class="form-group">
                                <label for="container_color_add"><?php echo $this->lang->line('Color', true); ?>:</label>
                                <input id="container_color_add" class="form-control colorPicker" name="container_color" value="" spellcheck="false" />
                            </fieldset>
                        </div>
                        <div class="col-md-8">
                            <label for="container_name_add"><?php echo $this->lang->line('Name', true); ?>:</label>
                            <fieldset class="form-group position-relative has-icon-left">
                                    <input id="container_name_add" name="container_name" type="text" class="form-control container_name" placeholder="<?php echo $this->lang->line('Name', true); ?>">
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="custom-control custom-switch custom-control-inline">
                                <input type="checkbox" class="custom-control-input" value="1" id="container_done_add" name="container_done">
                                <label class="custom-control-label mr-1 container_done" for="container_done_add">
                                </label>
                                <span><?php echo $this->lang->line('Is this "Done" column?', true); ?></span>
                            </div>
                            <small id="fileHelp" class="form-text text-muted">
                                <?php echo $this->lang->line('If checked, all task moved into this colum will \'closed\' automatically.', true); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close_button" data-dismiss="modal"><?php echo $this->lang->line('Close', true); ?></button>

                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('Add column', true); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editColumnModal" tabindex="-1" role="dialog" aria-labelledby="addColumnModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><?php echo $this->lang->line('Edit Column', true); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <form class="formAjax " action="<?php echo base_url(); ?>n_task/edit_container" method="post">
                <div class="modal-body">
                    <input id="container_id_edit" class="container_id" type="hidden" name="container_id" value=""/>
                    <div class="row">
                        <div class="col-md-4">
                            <fieldset class="form-group">
                                <label for="container_color_edit"><?php echo $this->lang->line('Color', true); ?></label>
                                <input type="text" class="form-control colorPicker" id="container_color_edit" name="container_color" spellcheck="false">
                            </fieldset>
                        </div>
                        <div class="col-md-8">
                            <label for="container_name_edit"><?php echo $this->lang->line('Name', true); ?>:</label>
                            <fieldset class="form-group position-relative has-icon-left">
                                <input id="container_name_edit" type="text" class="form-control container_name" name="container_name" placeholder="<?php echo $this->lang->line('Name', true); ?>">
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="custom-control custom-switch custom-control-inline">
                                <input type="checkbox" class="custom-control-input" value="1" id="container_done_edit" name="container_done">
                                <label class="custom-control-label mr-1 container_done" for="container_done_edit">
                                </label>
                                <span><?php echo $this->lang->line('Is this "Done" column?', true); ?></span>

                            </div>
                            <small id="fileHelp" class="form-text text-muted">
                                <?php echo $this->lang->line('If checked, all task moved into this colum will \'closed\' automatically.', true); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close_button" data-dismiss="modal"><?php echo $this->lang->line('Close', true); ?></button>

                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('Save column', true); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addBoardModal" tabindex="-1" role="dialog" aria-labelledby="addBoardModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo $this->lang->line('Add a new board', true); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <form class="formAjax " action="<?php echo base_url(); ?>n_task/new_board" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                                <label for="board_name_add"><?php echo $this->lang->line('Title', true); ?>:</label>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="text" class="form-control task_title" name="board_name" id="board_name_add" placeholder="<?php echo $this->lang->line('Title', true); ?>">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close_button" data-dismiss="modal"><?php echo $this->lang->line('Close', true); ?></button>

                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('Add board', true); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editBoardModal" tabindex="-1" role="dialog" aria-labelledby="editBoardModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo $this->lang->line('Edit board', true); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <form class="formAjax " action="<?php echo base_url(); ?>n_task/edit_board" method="post">
                <input type="hidden" name="board_id" value="<?php echo $board_id_active; ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="board_name_edit"><?php echo $this->lang->line('Title', true); ?>:</label>
                            <fieldset class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control task_title" name="board_name" id="board_name_edit" placeholder="<?php echo $this->lang->line('Title', true); ?>" value="<?php echo $board_name_active; ?>">
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <?php if (count($containers) > 0): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                            <div class="text-center">
                                                <h4 class="card-title"><?php echo $this->lang->line('Select your prefer order', true); ?></h4>

                                            </div>
                                            <div class="sortable_container">
                                                <ul class="list-group" id="handle-list-1">
                                                <?php $column_value = round(12 / count($containers), 0, PHP_ROUND_HALF_DOWN); ?>
                                                <?php foreach ($containers as $container): ?>
                                                    <li class="list-group-item"
                                                         id="<?php echo $container['container_id']; ?>" style="min-height:0;">
                                                        <span class="handle" style="color:<?php echo $container['container_color']; ?>;"><i class="fas fa-circle"></i></span> <?php echo $container['container_name']; ?>
                                                        <a href="#" data-toggle="modal" style="float:right;"
                                                           data-target="#deleteContainerModal"
                                                           data-container_id="<?php echo $container['container_id']; ?>"><i class="fa fa-trash"></i></a>

                                                    </li>
                                                <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">


                    <button type="button" class="btn btn-danger" id="delete_board"><?php echo $this->lang->line('Delete board', true); ?></button>

                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('Close and save', true); ?></button>

                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteContainerModal" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="deleteContainerLabel"><?php echo $this->lang->line('Attention', true); ?>!</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <form class="formAjax" action="<?php echo base_url(); ?>n_task/delete_container/" method="post">
                <div class="modal-body modal-warning">

                    <h4><?php echo $this->lang->line('Are you sure?', true); ?></h4>
                    <p><?php echo $this->lang->line('You have', true); ?> <span class="counter_tasks" style="font-weight:bold"></span> <?php echo $this->lang->line('tasks in this column. If you delete the column, you will lose its tasks. If you want to prevent this from happening, move the tasks to column:', true); ?> </p>


                    <input class="container_id" type="hidden" name="container_id" value=""/>
                    <div class="form-group">
                        <div class="form-group">
                            <label for="move_container" class="form-control-label"><?php echo $this->lang->line('Move to column', true); ?>:</label>
                            <select class="form-control" id="move_container" name="move_container" >
                                <option value="0"><?php echo $this->lang->line('No! I want to lose the tasks!', true); ?>
                                <?php foreach ($containers as $container) : ?>
                                    <option value="<?php echo $container['container_id']; ?>" style="color:<?php echo $container['container_color']; ?>"><?php echo $container['container_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-body modal-normal">
                    <h3><?php echo $this->lang->line('Are you really sure?', true); ?></h3>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close_button"
                            data-dismiss="modal"><?php echo $this->lang->line('Close', true); ?>
                    </button>
                    <button type="submit" class="btn btn-danger"><?php echo $this->lang->line('Delete column', true); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>






<style>
    <?php
    include(APPPATH.'modules/n_task/views/spectrum/spectrum.min.css');
    include(APPPATH.'modules/n_task/views/app-kanban.css');
    include(APPPATH.'modules/n_task/views/jkanban.min.css');
    ?>

    .li-placeholder {
        border: 2px dotted #FDAC41;
        margin: 1em;
        height: 40px;
        min-width: 100px;
    }
    .kanban-container{
        overflow: hidden;
        overflow-x: scroll;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
        height: calc(100vh - 300px);
        max-height: calc(100vh - 300px);
    }
    .kanban-container .kanban-board{
        white-space: normal;
        float:none!important;
        display: inline-block;
        vertical-align: top;
    }

    .kanban-container .kanban-board main{
        max-height: calc(100vh - 406px);
    }
    .time_tracker_action{
        cursor: pointer;
    }

    .to_do ul {
        padding: 0;
        margin-bottom: 20px;
    }

    .to_do ul li {
        list-style-type: none;
        cursor: pointer;
        position: relative;
        padding: 12px 8px 12px 40px;
        background: #eee;
        transition: 0.2s;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .to_do ul li:nth-child(odd) {
        background: #f9f9f9;
    }
    .to_do ul li:hover {
        background: #ddd;
    }

    .to_do ul li.checked {
        background: #888;
        color: #fff;
        text-decoration: line-through;
    }


</style>


<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fa fa-search-location"></i> <?php echo $page_title;?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><?php echo $page_title;?></div>
        </div>
    </div>


<div class="section-body">



<div class="card">
    <div class="row pl-1 pt-3">
        <div class="col-sm-12 col-md-3">
            <button href="#" class="btn btn-primary mb-1" data-toggle="modal" data-target="#addBoardModal">
                <i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Create new board"); ?>
            </button>
            <button href="#" class="btn btn-primary mb-1 ml-2" data-toggle="modal" data-target="#editBoardModal">
                <i class="fa fa-edit"></i>
            </button>
        </div>
            <div class="col-sm-12 col-md-4 ml-2">
                <fieldset class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="select_board_open"><?php echo $this->lang->line("Board"); ?></label>
                        </div>
                        <select class="form-control" id="select_board_open">
                            <?php $x = 0; ?>
                            <?php foreach ($boards as $board): ?>
                                <option <?php if ($board_id_active == $board['board_id']): ?>selected=""<?php endif; ?> value="<?php echo $board['board_id']; ?>"><?php echo $board['board_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
            </div>
            <div id="timer_container" class="width-300 badge badge-light-primary mb-1 d-inline-flex align-items-center ml-2" style="display: none!important;">
                <div class="col-8 text-left">
                    <span class="timer_task_title"></span>
                </div>
                <div class="col-3 text-center">
                        <span class="timer_box hide">
                            <span id="timer"></span>
                        </span>
                </div>
                <div class="col-1 text-right">
                    <a class="time_tracker_action pause_button" rel="">
                        <i class="fa fa-pause"></i>
                    </a>
                </div>
            </div>
        </div>

<div class="kanban-container">
    <?php if (count($containers) < 1) : ?>
        <a href="<?php echo base_url(); ?>home/settings/<?php echo $board_id; ?>#tab_containers"><?php echo $this->lang->line('no containers', true); ?></a>
    <?php endif; ?>

    <?php $numItems = count($containers);
    $i = 0; ?>
    <?php foreach ($containers as $container) : ?>
        <?php $division = round(12 / $numItems, 0, PHP_ROUND_HALF_DOWN); ?>
        <?php if ($numItems == 7) $division = 1; ?>
        <?php $column_value = (count($containers) > 2) ? $division : 4; ?>
        <?php if (++$i === $numItems && ($division * $numItems) < 12) $column_value = round(12 - ($division * ($numItems - 1)), 0, PHP_ROUND_HALF_UP); ?>
        <div class="kanban-board " style="border-top: <?php echo $container['container_color']; ?> 3px solid;" >
            <header class="kanban-board-header nodrag ">
                <div class="kanban-title-board line-ellipsis" contenteditable="false" spellcheck="false" data-ms-editor="false"><?php echo $container['container_name']; ?> (<?php echo count($tasks[$container['container_id']]); ?>)</div>
                <a href="" data-toggle="modal" data-target="#editColumnModal" data-container_name="<?php echo $container['container_name']; ?>" data-container_done="<?php echo $container['container_done']; ?>" data-container_id="<?php echo $container['container_id']; ?>"
                   data-container_color="<?php echo $container['container_color']; ?>"><i class="fa fa-edit"></i></a>
                <a href="" data-toggle="modal" data-target="#addTaskModal" data-container_name="<?php echo $container['container_name']; ?>" data-container_id="<?php echo $container['container_id']; ?>"><i class="fa fa-plus"></i></a>
            </header>

                <?php //if ($this->session->userdata('user_session')['user_permissions'] <= 10) :
                // ?>

                <?php
                //endif;
                ?>

            <main class="kanban-drag sortable"  rel="<?php echo $container['container_id']; ?>">
            <?php foreach ($tasks[$container['container_id']] as $task) : ?>
                <div class="kanban-item portlet task_element" <?php if ($task['task_color']) : ?>style="border-left: solid 4px <?php echo $task['task_color']; ?>;<?php endif; ?>" id="<?php echo $task['task_id']; ?>" data-toggle="modal" data-target="#editTaskModal" data-task_id="<?php echo $task['task_id']; ?>">
                    <div class="portlet-border"></div>
                    <div class="portlet-header">
                        <span class="task_title"><?php echo $task['task_title']; ?></span>


                            <?php if ($task['task_description']) : ?>
                                <span class='ui-icon ui-icon-plusthick portlet-toggle nodrag'></span>
                            <?php endif; ?>

                        <div class="action_button hidden-xs float-right">
                            <?php
                            //if ($this->session->userdata('user_session')['user_permissions'] <= 10) :
                            ?>
                            <a class="time_tracker_action" rel="<?php echo $task['task_id']; ?>"><i class="fa fa-play"></i></a>
                            <?php
                            //endif;
                            ?>
                        </div>
                    </div>
                    <?php if ($task['task_description']) : ?>
                        <div class="portlet-content" style="display:none"><?php echo nl2br($task['task_description']); ?></div>
                    <?php endif; ?>
                    <div class="kanban-footer-left d-flex mt-1">
                        <?php if($task['task_due_date'] != 0){ ?>
                        <div class="kanban-due-date d-flex align-items-center mr-3 <?php if (date('Y-m-d', strtotime($task['task_due_date'])) < date('Y-m-d')) : ?>text-danger<?php endif; ?>">
                            <i class="far fa-calendar font_size_12px mr-1"></i>
                            <span class="font_size_12px"><?php echo ($task['task_due_date'] != 0) ? date('M d', strtotime($task['task_due_date'])) : null; ?></span>
                        </div>
                        <?php } ?>
                        <?php if($task['task_time_estimate'] != "00:00:00"){ ?>
                            <div class="kanban-comment d-flex align-items-center mr-3">
                                <i class='fas fa-hourglass-half font_size_12px mr-1'></i>
                                <span class="font_size_12px"><?php echo ($task['task_time_estimate'] != "00:00:00") ? $task['task_time_estimate'] : null; ?></span>
                            </div>
                        <?php } ?>
                        <?php if($task['task_time_spent'] != "00:00:00"){ ?>
                        <div class="kanban-comment d-flex align-items-center mr-3">
                            <i class='fas fa-stopwatch font_size_12px mr-1'></i>
                            <span class="font_size_12px"><?php echo ($task['task_time_spent'] != "00:00:00") ? $task['task_time_spent'] : null; ?></span>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </main>
            <button class="kanban-title-button btn btn-default btn-xs nodrag"  data-toggle="modal" data-target="#addTaskModal" data-container_name="<?php echo $container['container_name']; ?>" data-container_id="<?php echo $container['container_id']; ?>">+ <?php echo $this->lang->line('Add New Item');?></button>
        </div>
    <?php endforeach; ?>
    <div class="kanban-board " style="" >
        <button class="kanban-title-button btn btn-default btn-xs nodrag"  data-toggle="modal" data-target="#addColumnModal" data-board_id="<?php echo $board_id; ?>">+ <?php echo $this->lang->line('Add New Column');?></button>
    </div>
</div>

<div class="drag_options" style="display:none;">

    <div class="darg_options_container">

        <div class="icon icon_archive sortable pull-left" rel="archive">


        </div>
        <div class="icon icon_bin sortable pull-right" rel="bin">

        </div>
        <div class="clearfix"></div>
    </div>

</div>

<div class="board-footer hidden-xs">
    <span class="board-time-spent"><?php echo $this->lang->line('TIME SPENT ON THIS BOARD', true); ?>
        <strong><?php echo $board_time_spent_active; ?></strong> (<?php echo $board_time_spent_archived; ?> Archived task)</span>
</div>
</div>
</div>
</section>
<?php include(APPPATH.'modules/n_task/views/kanban_modals.php'); ?>
<?php include(APPPATH.'modules/n_task/views/kanban_js.php'); ?>

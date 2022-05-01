<?php
$include_dropzone=1;
$include_datetimepicker=1;
$include_jqueryui=1;
$include_alertify=1;
?>


<style>
    <?php
include(APPPATH.'modules/n_task/views/spectrum/spectrum.min.css');
include(APPPATH.'modules/n_task/views/app-todo_v2.min.css');
?>
    .sidebar-left {
        float: left;
    }
    .sidebar-right {
        width: calc(100% - 260px);
        float: right;
    }
    html .content.app-content .content-area-wrapper {
        height: calc(100% - 5rem);
        margin: calc(5rem) 2.2rem 0;
        display: flex;
        position: relative;
    }
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





    <div class="row pl-1">
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

    <!-- BEGIN: Content-->
<div class="todo-application">
        <div class="content-area-wrapper" style="margin:initial;">
            <div class="sidebar-left">
                <div class="sidebar"><div class="todo-sidebar d-flex">
  <span class="sidebar-close-icon">
    <i class="bx bx-x"></i>
  </span>
                        <!-- todo app menu -->
                        <div class="todo-app-menu">
                            <div class="form-group text-center add-task pl-0 pr-0">
                                <button href="#" class="btn btn-primary mb-1" data-toggle="modal" data-target="#addBoardModal">
                                    <i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Create new board"); ?>
                                </button>
                                <button href="#" class="btn btn-primary mb-1 ml-2" data-toggle="modal" data-target="#editBoardModal">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </div>

                            <!-- sidebar list start -->
                            <div class="sidebar-menu-list">
                                <div class="list-group">
                                    <?php $x = 0; ?>
                                    <?php foreach ($boards as $board): ?>

                                        <a href="<?php echo base_url().$uri_def;?>/list/<?php echo $board['board_id'];?>" class="list-group-item border-0 <?php if ($board_id_active == $board['board_id']): ?>active<?php endif; ?>" data-id="<?php echo $board['board_id']; ?>">
          <span class="fonticon-wrap mr-50">
            <i class="fa fa-list"></i>
          </span>
                                            <span><?php echo $board['board_name']; ?></span>
                                        </a>
                                    <?php endforeach; ?>


                                </div>

                            </div>
                            <!-- sidebar list end -->
                        </div>
                    </div>

                </div>
            </div>
            <div class="content-right">
                <div class="content-overlay"></div>
                <div class="content-wrapper">
                    <div class="content-header row">
                    </div>
                    <div class="content-body"><div class="app-content-overlay"></div>
                        <div class="todo-app-area">
                            <div class="todo-app-list-wrapper">
                                <div class="todo-app-list">


                                    <div class="todo-task-list list-group">
                                        <!-- task list start -->
<div class="card-header pb-0 mb-0">
    <h4 class="card-title mb-0"><?php echo $board_name_active;?>        <button class="kanban-title-button btn btn-primary btn-xs nodrag float-right"  data-toggle="modal" data-target="#addColumnModal" data-board_id="<?php echo $board_id_active; ?>">+ <?php echo $this->lang->line('Add New Column');?></button></h4>



</div>

<?php $numItems = count($containers);
$i = 0; ?>
<?php foreach ($containers as $container) : ?>
    <?php $division = round(12 / $numItems, 0, PHP_ROUND_HALF_DOWN); ?>
    <?php if ($numItems == 7) $division = 1; ?>
    <?php $column_value = (count($containers) > 2) ? $division : 4; ?>
    <?php if (++$i === $numItems && ($division * $numItems) < 12) $column_value = round(12 - ($division * ($numItems - 1)), 0, PHP_ROUND_HALF_UP); ?>

    <div class="d-flex justify-content-between align-items-center mt-2" style="border-bottom: <?php echo $container['container_color']; ?> 3px solid;" >
                                            <div class="todo-title-wrapper d-flex justify-content-sm-between justify-content-end align-items-center nodrag">
                                                <p class="todo-title mx-50 m-0 truncate font-medium-2"><?php echo $container['container_name']; ?> (<?php echo count($tasks[$container['container_id']]); ?>)</p>
                                            </div>

                                        </div>

                                        <ul class="todo-task-list-wrapper list-unstyled kanban-drag sortable pt-2" rel="<?php echo $container['container_id']; ?>" >
                                            <?php foreach ($tasks[$container['container_id']] as $task) : ?>
                                            <li class="todo-item portlet task_element" <?php if ($task['task_color']) : ?>style="border-left: solid 4px <?php echo $task['task_color']; ?>;<?php endif; ?>" id="<?php echo $task['task_id']; ?>" data-toggle="modal" data-target="#editTaskModal" data-task_id="<?php echo $task['task_id']; ?>">
                                                <div class="todo-title-wrapper d-flex justify-content-sm-between justify-content-end align-items-center">
                                                    <div class="todo-title-area d-flex">
                                                        <i class='fa fa-list handle'></i>
                                                        <p class="todo-title mx-50 m-0 truncate"><?php echo $task['task_title']; ?></p>
                                                    </div>
                                                    <div class="todo-item-action d-flex align-items-center">
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
                                                        <div class="action_button hidden-xs float-right">
                                                            <?php
                                                            //if ($this->session->userdata('user_session')['user_permissions'] <= 10) :
                                                            ?>
                                                            <a class="time_tracker_action" rel="<?php echo $task['task_id']; ?>"><i class="fa fa-play"></i></a>
                                                            <?php
                                                            //endif;
                                                            ?>
                                                        </div>
<!--                                                        <a class='todo-item-delete ml-75'><i class="bx bx-trash"></i></a>-->
                                                    </div>
                                                </div>
                                            </li>
                                            <?php endforeach; ?>

                                        </ul>
    <div><button class="kanban-title-button btn btn-default btn-xs nodrag"  data-toggle="modal" data-target="#addTaskModal" data-container_name="<?php echo $container['container_name']; ?>" data-container_id="<?php echo $container['container_id']; ?>">+ <?php echo $this->lang->line('Add New Item');?></button>
    </div>

                                        <?php endforeach; ?>

                                    </div>


                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
</div>
    <!-- END: Content-->


<div class="drag_options" style="display:none;">

    <div class="darg_options_container">

        <div class="icon icon_archive sortable pull-left" rel="archive">


        </div>
        <div class="icon icon_bin sortable pull-right" rel="bin">

        </div>
        <div class="clearfix"></div>
    </div>

</div>


</div>
</section>
<?php include(APPPATH.'modules/n_task/views/kanban_modals.php'); ?>
<?php include(APPPATH.'modules/n_task/views/todo_js.php'); ?>


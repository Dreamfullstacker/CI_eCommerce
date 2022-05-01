<script src="<?php echo base_url(); ?>n_assets/js/spectrum/spectrum.min.js"></script>

<script>
    /****************************************************** TODO LIST ********************************************** */

        // Click on a close button to hide the current list item
    var close = document.getElementsByClassName("close");
    var i;
    for (i = 0; i < close.length; i++) {
        close[i].onclick = function() {
            var div = this.parentElement;
            div.style.display = "none";
        }
    }

    var todo_json = [];


    $('.todo_ul_edit_mode').on("click", "li", function(e) {

        if ($(this).hasClass("checked")) {
            new_value = 0;
        } else {
            new_value = 1;
        }
        $(this).toggleClass("checked");
        current_todo_id = $(this).data('todoid');
        $.ajax({
            url: base_url + "task/update_field/tasks_todo/status/" + new_value + "/id/" + current_todo_id,
            dataType: 'json',
            cache: false,
            success: function(data) {

            }
        });
        e.preventDefault();
    });

    $('.todo_ul_edit_mode ').on("click", ".close", function(e) {
        e.preventDefault();
        parent_li = $(this).parent();
        current_todo_id = $(this).parent().data('todoid');
        $.ajax({
            url: base_url + "task/delete_j/tasks_todo/id/" + current_todo_id,
            dataType: 'json',
            cache: false,
            success: function(data) {
                parent_li.remove();
            }
        });
        e.stopPropagation();
    });

    $('.newTaskAddBtn').on('click', function() {
        var li = document.createElement("li");
        var inputValue = $('#AddTodoInput').val();
        var t = document.createTextNode(inputValue);
        li.appendChild(t);
        if (inputValue === '') {
            alert("You must write something!");
        } else {
            todo_json.push(inputValue);
            $('#add_task_todo').val(JSON.stringify(todo_json));
            $('#newTaskTodoUl').append("<li>" + inputValue + "</li>");;
        }
        $('#AddTodoInput').val("");

    });

    $('#editTaskAddBtn').on('click', function() {
        var li = document.createElement("li");
        var inputValue = $('#editTodoInput').val();
        var t = document.createTextNode(inputValue);
        li.appendChild(t);
        if (inputValue === '') {
            alert("You must write something!");
        } else {
            todo_json.push(inputValue);
            $('#edit_task_todo').val(JSON.stringify(todo_json));
            $('#editTaskTodoUl').append("<li>" + inputValue + "</li>");;
        }
        $('#editTodoInput').val("");

    });

    function removeA(arr) {
        var what, a = arguments,
            L = a.length,
            ax;
        while (L > 1 && arr.length) {
            what = a[--L];
            while ((ax = arr.indexOf(what)) !== -1) {
                arr.splice(ax, 1);
            }
        }
        return arr;
    }

    /****************************************************** DROP ZONE UPLOAD ********************************************** */


    var myDropzone = new Dropzone("#dropzone_form", {
        url: "<?php echo base_url(); ?>task/upload_attachments",
        dictDefaultMessage: ""
    });

    myDropzone.on("error", function(file, error, errorxhr) {
        error_message = JSON.parse(errorxhr.response);
        $('.dropzone_error').html(error_message.error);
    });
    myDropzone.on("success", function(file, xhrmessage) {
        popolate_attachment(JSON.parse(xhrmessage))
    });
    myDropzone.on("complete", function(file, error, xhrmessage) {
        //$('.dropzone_error').html("");
        myDropzone.removeFile(file);
    });

    Dropzone.autoDiscover = false;

    /****************************************************** VARIOUS ********************************************** */

    $('.colorPicker').spectrum();

    $('#delete_task').on('click', function(event) {
        var result = confirm("Are you sure?");
        var task_id = $(this).attr("rel");
        if (result) {
            $.ajax({
                url: base_url + "task/delete_j/tasks/task_id/" + task_id,
                dataType: 'json',
                cache: false,
                success: function(data) {
                    window.location.reload();
                }
            });
        }
    })

    $('#delete_board').on('click', function(event) {
        var result = confirm("Are you sure?");
        var task_id = $(this).attr("rel");
        if (result) {
            $.ajax({
                url: base_url + "task/delete_board/" + <?php echo $board_id_active; ?>,
                dataType: 'json',
                cache: false,
                success: function(data) {
                    window.location.reload();
                }
            });
        }
    })


    /****************************************************** MODALS  ********************************************** */

    $('#editColumnModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var container_name = button.data('container_name');
        var container_id = button.data('container_id');
        var container_color = button.data('container_color');
        var container_done = button.data('container_done');


        var modal = $(this);
        modal.find('#container_color_edit').spectrum({color:container_color});
        modal.find('#container_id_edit').val(container_id);
        modal.find('#container_name_edit').val(container_name);
        if(container_done==1){
            modal.find('#container_done_edit').prop( "checked", true );
        }
    })

    $('#addTaskModal').on('show.bs.modal', function(event) {

        var button = $(event.relatedTarget) // Button that triggered the modal
        var container_name = button.data('container_name');
        var container_id = button.data('container_id');

        todo_json = [];
        $('#add_task_todo').val("");

        var modal = $(this)
        modal.find('.modal-title').text('Add Task in: ' + container_name)
        $('#task_container').val(container_id)

        modal.find('.todo_ul').html("");
        modal.find('.todo_ul').on("click", "li", function() {
            removeA(todo_json, $(this).html());
            $('#task_todo').val(JSON.stringify(todo_json));
            $(this).remove();

            /*var index = $.inArray("prova", todo_json);
            if (index >= 0) todo_json.splice(index, 1);*/

        });

    })

    $('#editTaskModal').on('show.bs.modal', function(event) {

        var button = $(event.relatedTarget) // Button that triggered the modal
        var current_task_id = button.data('task_id');

        var modal = $(this)
        if (!current_task_id) {
            return false;
        }

        todo_json = [];
        $('#edit_task_todo').val("");

        $.ajax({
            url: base_url + "task/get_task_details/" + current_task_id,
            dataType: 'json',
            cache: false,
            success: function(data) {
                $('.upload_task_id').val(data.task.task_id);
                modal.find('.task_id').val(data.task.task_id);
                modal.find('#delete_task').attr('rel', data.task.task_id);
                modal.find('.task_title').val(data.task.task_title);
                modal.find('.task_user_name').html(data.task.name);
                modal.find('.task_header').html(data.task.task_title);
                modal.find('.task_description').val(data.task.task_description);
                modal.find('.task_time_estimate').val(data.task.task_time_estimate);
                modal.find('.task_time_spent').val(data.task.task_time_spent);
                modal.find('.colorPicker').spectrum({color:data.task.task_color});
                modal.find('.task_container').val(data.task.task_container);

                if (data.task.task_due_date != "0000-00-00 00:00:00")
                    modal.find('.task_due_date').val(data.task.task_due_date);
                else
                    modal.find('.task_due_date').val('');


                // Details tab
                modal.find('.task_date_creation').html(data.task.task_date_creation);
                modal.find('.task_date_closed').html(data.task.task_date_closed);

                // Working periods task
                $('.periods_body').html("");
                if (data.task_periods.length > 0) {
                    data.task_periods.forEach(function(p) {
                        $('.periods_body').append("<tr><td>" + p.name + "</td><td>" + p.task_date_start + "</td><td>" + p.task_date_stop + "</td><td>" + p.total_time + "</td></tr>");
                    });
                    $('.total_time_spent').html(data.task_time_spent);
                } else {
                    $('.periods_body').append("<tr><td colspan='5'><?php echo $this->lang->line('No working periods found for this task.'); ?></td></tr>");
                }

                // Task Todo
                modal.find('.todo_ul').html("");
                if (data.task_todo.length > 0) {
                    data.task_todo.forEach(function(a) {
                        if (a.status == 0) {
                            modal.find('.todo_ul').append("<li data-todoid='" + a.id + "'>" + a.title + "<span class='close'><i class='bx bx-x'></i></span></li>");
                        } else {
                            modal.find('.todo_ul').append("<li data-todoid='" + a.id + "' class='checked'>" + a.title + "<span class='close'><i class='bx bx-x'></i></span></li>");
                        }
                    });
                }

                // Attachments
                $('.attachments_body').html("");
                if (data.task_attachments.length > 0) {
                    data.task_attachments.forEach(function(a) {
                        popolate_attachment(a)
                    });


                } else {
                    $('.attachments_body').append("<tr><td colspan='5' class='text-center'><?php echo $this->lang->line('No attachments found for this task.'); ?></td></tr>");
                }


            },
        });




        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {

            var tab = $(e.target).attr('href');

            $.ajax({
                url: base_url + "task/get_task_details/" + current_task_id,
                dataType: 'json',
                cache: false,
                success: function(data) {
                    modal.find('.task_id').val(data.task_id);

                },
            });

        })

        event.stopPropagation();
    });

    function popolate_attachment(a) {
        $('.attachments_body').append("<tr><td><i class='bx bx-file'></i></td><td><a href='<?php echo base_url(); ?>uploads/" + a.attachment_filename + "'>" + a.attachment_original_filename + "</a></td><td>" + a.name + "</td><td>" + a.attachment_creation_date + "</td><td><butto class='btn delete_attachment' rel='" + a.attachment_id + "' title='Delete file'><i class='bx bx-trash'></i></button></tr>");
        $('.delete_attachment').on('click', function(event) {
            var result = confirm("Are you sure?");
            var attachment_id = $(this).attr("rel");
            if (result) {
                $.ajax({
                    url: base_url + "task/delete_j/attachments/attachment_id/" + attachment_id,
                    dataType: 'json',
                    cache: false,
                    success: function(data) {
                        window.location.reload();
                    }
                });
            }
        })
    }

    $(function() {

        <?php if (!empty($task_standby['task_title'])) : ?>
        $('#resumeWorkTaskModal').modal('show');
        <?php endif; ?>

        $('.datetimepicker').datetimepicker({
            format: 'Y-m-d H:i'
        });

        /* Here we will store all data */
        var myArguments = {};

        function assembleData(object, arguments) {
            var data = $(object).sortable('toArray'); // Get array data
            var container_id = $(object).attr("rel"); // Get step_id and we will use it as property name
            var arrayLength = data.length; // no need to explain

            /* Create step_id property if it does not exist */
            if (!arguments.hasOwnProperty(container_id)) {
                arguments[container_id] = new Array();
            }

            /* Loop through all items */
            for (var i = 0; i < arrayLength; i++) {
                if (data[i]) {
                    var task_id = data[i];
                    /* push all image_id onto property step_id (which is an array) */
                    arguments[container_id].push(task_id);
                }
            }
            return arguments;
        }

        /* Sort task */
        var globalTimer;
        <?php //if ($this->session->userdata('user_session')['user_permissions'] <= 10) :
        ?>
        $(".sortable").sortable({
            connectWith: ".sortable",
            cancel: ".nodrag",
            opacity: 0.7,
            placeholder: "li-placeholder",
            /* That's fired first */
            start: function(event, ui) {
                $('.column').css('overflow-y', 'inherit'); // fix for x scroll bug
                myArguments = {};
                /*$('.column').css('overflow', 'hidden');*/
                ui.item.addClass('rotate');
                globalTimer = setTimeout(function() {
                    $('.drag_options').fadeIn(300);
                }, 800);
            },
            /* That's fired second */
            remove: function(event, ui) {
                /* Get array of items in the list where we removed the item */
                myArguments = assembleData(this, myArguments);
            },
            /* That's fired thrird */
            receive: function(event, ui) {
                /* Get array of items where we added a new item */
                myArguments = assembleData(this, myArguments);
            },
            update: function(e, ui) {
                if (this === ui.item.parent()[0]) {
                    /* In case the change occures in the same container */
                    if (ui.sender == null) {
                        myArguments = assembleData(this, myArguments);
                    }
                }
            },
            /* That's fired last */
            stop: function(event, ui) {
                clearTimeout(globalTimer);
                ui.item.removeClass('rotate');
                $('.column').css('overflow-y', 'auto'); // fix for x scroll bug
                if ($(ui.item.parent()[0]).attr('rel') == 'archive' || $(ui.item.parent()[0]).attr('rel') == 'bin') {
                    ui.item.hide();
                }
                $('.drag_options').fadeOut(100);

                $('.bin_container').fadeOut(500);
                /* Send JSON to the server */

                if ($(ui.item.parent()[0]).attr('rel') == 'bin') {
                    task_id = $(ui.item).attr('id');

                    $.ajax({
                        url: base_url + "task/delete/tasks/task_id/" + task_id,
                        type: 'post',
                        dataType: 'json',
                        data: myArguments,
                        cache: false
                    });
                } else if ($(ui.item.parent()[0]).attr('rel') == 'archive') {
                    task_id = $(ui.item).attr('id');

                    $.ajax({
                        url: base_url + "task/update_field/tasks/task_archived/1/task_id/" + task_id,
                        type: 'post',
                        dataType: 'json',
                        data: myArguments,
                        cache: false
                    });
                } else {
                    $.ajax({
                        url: base_url + "task/update_position",
                        type: 'post',
                        dataType: 'json',
                        data: myArguments,
                        cache: false
                    });
                }
            },
        });
        <?php
        //endif;
        ?>


        $(".portlet").addClass("ui-helper-clearfix ui-corner-all");

        $(".portlet-toggle").on("click", function() {
            var icon = $(this);
            icon.toggleClass("ui-icon-minusthick ui-icon-plusthick");
            icon.closest(".portlet").find(".portlet-content").toggle();
            return false;
        });

        $(".column").on("tap", function() {

        });

        $('.time_tracker_action').on("click", function() {

            var task_id = $(this).attr("rel");

            if (current_task_tracking != null && task_id != current_task_tracking) {
                alert("You have already tracking now.");
                return false;
            }

            if (current_task_tracking == null) {

                current_task_tracking = task_id;

                // START TIMER
                $('#timer').runner('start');
                $.ajax({
                    url: base_url + "task/time_tracker/start/" + task_id,
                    type: 'get',
                    dataType: 'json',
                    cache: false,
                    success: function(data) {
                        $('.timer_task_title').html(data.task_title.substring(0, 20) + '...');

                    },
                });

                $('#timer_container').addClass('d-inline-flex');
                $('#timer_container').show();
                $('.pause_button').attr("rel", task_id);

                $('.time_tracker_action[rel=' + current_task_tracking + '] i').removeClass('bx-play');
                $('.time_tracker_action[rel=' + current_task_tracking + '] i').addClass('bx-pause');




            } else {
                // STOP TIMER

                $('#timer').runner('reset');
                $('#timer').runner('stop');
                $.ajax({
                    url: base_url + "task/time_tracker/stop/" + task_id,
                    type: 'get',
                    dataType: 'json',
                    cache: false
                });


                $('#timer_container').removeClass('d-inline-flex');
                $('#timer_container').hide();
                $('.pause_button').attr("rel", null);
                $('.timer_task_title').html("");

                // Change button
                $('.time_tracker_action[rel=' + current_task_tracking + '] i').removeClass('bx-pause');
                $('.time_tracker_action[rel=' + current_task_tracking + '] i').addClass('bx-play');

                current_task_tracking = null;
            }

            return false;
        });


        $('#select_board_open').on('change', function() {
            window.location.assign(base_url+'task/board/'+this.value);
        });

        $('.sortable_container #handle-list-1').sortable({

            update: function (event, ui) {
                var data = $(this).sortable('toArray');

                $.ajax({
                    url: base_url + "task/update_containers_position",
                    type: 'post',
                    dataType: 'json',
                    data: {containers_id: data,board_id: <?php echo $board_id_active; ?>},
                    cache: false
                });
            }
        });

        $('#deleteContainerModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var container_id = button.data('container_id');
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var modal = $(this);

            $.ajax({
                url: base_url + "task/get_container_details/" + container_id,
                dataType: 'json',
                cache: false,
                success: function (data) {
                    if (data.container_tasks_count.count < 1) {
                        $('.modal-warning').hide();
                        $('.modal-normal').show();
                    } else {
                        $('.modal-warning').show();
                        $('.modal-normal').hide();
                    }
                    modal.find('.counter_tasks').html(data.container_tasks_count.count);
                    modal.find('.container_id').val(data.container_data.container_id);
                    modal.find('#move_container option').show();
                    modal.find('#move_container option[value=' + container_id + ']').hide();
                },
            });
            $("#move_column_selector option[value=" + container_id + "]").each(function () {
                $(this).attr("disabled", "disabled");
            });

            $('#deleteContainerModal').on('hide.bs.modal', function (event) {
                $("#move_column_selector option[value=" + container_id + "]").each(function () {
                    $(this).removeAttr("disabled");
                });
            });
        });



    });
</script>

<script>
    // Todo App variables
    var todoNewTasksidebar = $(".todo-new-task-sidebar"),
        appContentOverlay = $(".app-content-overlay"),
        sideBarLeft = $(".sidebar-left"),
        todoTaskListWrapper = $(".todo-task-list-wrapper"),
        todoItem = $(".todo-item"),
        selectAssignLable = $(".select2-assign-label"),
        selectUsersName = $(".select2-users-name"),
        avatarUserImage = $(".avatar-user-image"),
        updateTodo = $(".update-todo"),
        addTodo = $(".add-todo"),
        markCompleteBtn = $(".mark-complete-btn"),
        newTaskTitle = $(".new-task-title"),
        taskTitle = $(".task-title"),
        noResults = $(".no-results"),
        assignedAvatarContent = $(".assigned .avatar .avatar-content"),
        todoAppMenu = $(".todo-app-menu");


    // Sidebar scrollbar
    if ($('.todo-application .sidebar-menu-list').length > 0) {
        var sidebarMenuList = new PerfectScrollbar('.sidebar-menu-list', {
            theme: "dark",
            wheelPropagation: false
        });
    }

    //  New task scrollbar
    if (todoNewTasksidebar.length > 0) {
        var todo_new_task_sidebar = new PerfectScrollbar('.todo-new-task-sidebar', {
            theme: "dark",
            wheelPropagation: false
        });
    }

    // Task list scrollbar
    if ($('.todo-application .todo-task-list').length > 0) {
        var sidebar_todo = new PerfectScrollbar('.todo-task-list', {
            theme: "dark",
            wheelPropagation: false
        });
    }
</script>

<?php include(APPPATH.'n_views/modules/n_task/main_js.php'); ?>
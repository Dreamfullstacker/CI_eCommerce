<?php
/*
Addon Name: NVX Task Manager
Unique Name: n_task
Modules:
{
   "3010":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"0",
      "extra_text":"",
      "module_name":"To Do List / Kanban"
   }
}
Project ID: 1101
Addon URI: https:/nvxgroup.com
Author: Mario Devado
Author URI: https:/nvxgroup.com
Version: 1.21
Description: Task Manager
*/
require_once("application/controllers/Home.php"); // loading home controller
include("application/libraries/Facebook/autoload.php");


class N_task extends Home
{
    public $key = "0B29D8D0CD9D17F8";
    private $product_id = 8;
    private $product_base = "n_task";
    private $server_host = "https://nvxgroup.com/wp-json/licensor/";
    private $nvx_version = 1.21;
    /* @var self */
    private static $selfobj = null;
    public $fb;
    var $board_id_active;
    var $url_short = false;


    public $addon_data = array();

    public function __construct()
    {
        parent::__construct();
        //$this->load->config('instagram_reply_config');// config
        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $addon_path = APPPATH . "modules/" . strtolower($this->router->fetch_class()) . "/controllers/" . ucfirst($this->router->fetch_class()) . ".php"; // path of addon controller
        $addondata = $this->get_addon_data($addon_path);
        $this->addon_data = $addondata;
        $this->user_id = $this->session->userdata('user_id'); // user_id of logged in user, we may need it

        $function_name = $this->uri->segment(2);
        if ($function_name != "webhook_callback")
        {
            // all addon must be login protected
            //------------------------------------------------------------------------------------------
            if ($this->session->userdata('logged_in') != 1) redirect('home/login', 'location');
            // if you want the addon to be accessed by admin and member who has permission to this addon
            //-------------------------------------------------------------------------------------------

            switch($function_name){
                case 'settings';
                        if ($this->session->userdata('user_type') != 'Admin'){
                            redirect('home/login_page', 'location');
                            exit();
                        }
                break;
                        default;
                            if ($this->session->userdata('user_type') != 'Admin' && !in_array(3010,$this->module_access)){
                                redirect('home/access_forbidden', 'location');
                                exit();
                            }
                        break;
            }


        }

        $this->load->library('encryption');

        $addon_lang = 'n_task';
        if (file_exists(APPPATH . 'modules/' . $addon_lang . '/language/' . $this->language . '/' . $addon_lang . '_lang.php')) {
            $this->lang->load($addon_lang, $this->language, FALSE, TRUE, APPPATH . 'modules/' . $addon_lang . '/language/' . $this->language);
        } else {
            $this->lang->load($addon_lang, 'english', FALSE, TRUE, APPPATH . 'modules/' . $addon_lang . '/language/english');
        }


        if (file_exists(APPPATH . 'modules/' . $addon_lang . '/language/' . $this->language . '/' . $addon_lang . '_custom_lang.php')) {
            $this->lang->load($addon_lang . '_custom', $this->language, FALSE, TRUE, APPPATH . 'modules/' . $addon_lang . '/language/' . $this->language);
        }

        if(file_exists(FCPATH.'application/n_views/config.php')){
            $this->url_short = true;
        }

    }

    public function index(){
        $board = $this->db->query("SELECT * FROM boards WHERE board_id
                                            IN (SELECT board_id FROM boards_users WHERE user_id = '{$this->user_id}')
                                            ORDER BY board_default DESC LIMIT 1");
        if ($board->num_rows() > 0) {
            $this->board($board->row()->board_id);
        } else {
            $data['title'] = $this->lang->line('Kanban');
            $data['body'] = 'new_board';
            $data['page_title'] = $data['title'];
            $this->_viewcontroller($data);
            return;
        }
//
//
//        $data['containers'] = $this->db->query("SELECT * FROM boards WHERE user_id = '$this->user_id' limit 1")->result_array();
//
//        if(empty($data['containers'])){
//            $data['title'] = $this->lang->line('Kanban');
//            $data['body'] = 'new_board';
//            $data['page_title'] = $data['title'];
//            $this->_viewcontroller($data);
//            return;
//        }
//
//        $this->board($data['containers'][0]['board_id']);
    }

    public function board($board_id = ''){
        if ($this->session->userdata('logged_in') != 1) exit();
        if($board_id==''){
            if($this->url_short){
                redirect('task', 'location');
            }else{
                redirect('n_task/', 'location');
            }
        }

        $data = array();

        $check_permission = $this->db->query("SELECT * FROM boards WHERE board_id
                                            IN (SELECT board_id FROM boards_users WHERE user_id = '{$this->user_id}')
                                            AND board_id = '$board_id' LIMIT 1");

        if (!$board_id || $check_permission->num_rows() < 1) {
            if($this->url_short){
                redirect('task', 'location');
            }else{
                redirect('n_task/', 'location');
            }
        } else {
            $this->board_id_active = $board_id;
        }
        $board_a = $check_permission->result_array();

        $data['board_name_active'] = $board_a[0]['board_name'];

        $data['board_id'] = $board_id;
        $data['containers'] = $this->db->query("SELECT * FROM containers WHERE container_board = '$board_id' ORDER BY container_order ASC")->result_array();


        if(empty($data['containers'])){
            $data['title'] = $this->lang->line('Kanban');
            $data['body'] = 'new_board';
            $data['page_title'] = $data['title'];
            $this->_viewcontroller($data);
            return;
        }

        $data['board_id_active'] = $board_id;

        $data['boards'] = $this->db->query("SELECT * FROM boards WHERE board_id
                                            IN (SELECT board_id FROM boards_users WHERE user_id = '{$this->user_id}')
                                            ORDER BY board_order ASC")->result_array();


        foreach ($data['containers'] as $key => $container) {
            // Convert hex in rgb for background
            $hex = $container['container_color'];
            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
            $data['containers'][$key]['container_rgb'] = "$r,$g,$b";

            $data['tasks'][$container['container_id']] = $this->db->query("SELECT * FROM tasks WHERE task_container = '{$container['container_id']}' AND task_archived = 0 ORDER BY task_order ASC")->result_array();
        }

        // Check resume work
        $data['task_standby'] = $this->db->query("SELECT *, TIMEDIFF(NOW(), task_date_start) AS last_tracking
                                                  FROM task_periods LEFT JOIN tasks ON tasks.task_id = task_periods.task_id
                                                  WHERE task_periods_user = '{$this->user_id}' AND task_date_stop IS NULL ORDER BY task_periods_id ASC LIMIT 1")->row_array();

        $data['board_time_spent_active'] = $this->db->query("SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( `task_time_spent` ) ) ) AS board_time_spent FROM tasks
                                                       LEFT JOIN containers ON tasks.task_container = containers.container_id
                                                       LEFT JOIN boards ON containers.container_board = boards.board_id
                                                       WHERE board_id = '$board_id' AND task_archived = '0' ")->row()->board_time_spent;

        $data['board_time_spent_archived'] = $this->db->query("SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( `task_time_spent` ) ) ) AS board_time_spent FROM tasks
                                                       LEFT JOIN containers ON tasks.task_container = containers.container_id
                                                       LEFT JOIN boards ON containers.container_board = boards.board_id
                                                       WHERE board_id = '$board_id' AND task_archived = '1'")->row()->board_time_spent;

        $this->db->query("UPDATE boards SET board_default = 0 where  user_id = '{$this->user_id}'");
        $this->db->query("UPDATE boards SET board_default = 1 where board_id= '{$board_id}' AND user_id = '{$this->user_id}'");

        $data['title'] = $this->lang->line('Kanban');
        $data['body'] = 'kanban';
        $data['page_title'] = $data['title'];

        $this->_viewcontroller($data);
    }

    public function list($board_id = ''){
        if ($this->session->userdata('logged_in') != 1) exit();

        if($board_id == ''){
            $board = $this->db->query("SELECT * FROM boards WHERE board_id
                                                IN (SELECT board_id FROM boards_users WHERE user_id = '{$this->user_id}')
                                                ORDER BY board_default DESC LIMIT 1");
            if ($board->num_rows() > 0) {
                $board_id = $board->row()->board_id;
            } else {
                $data['title'] = $this->lang->line('Kanban');
                $data['body'] = 'new_board';
                $data['page_title'] = $data['title'];
                $this->_viewcontroller($data);
                return;
            }
        }

        $data = array();

        $check_permission = $this->db->query("SELECT * FROM boards WHERE board_id
                                            IN (SELECT board_id FROM boards_users WHERE user_id = '{$this->user_id}')
                                            AND board_id = '$board_id' LIMIT 1");

        if (!$board_id || $check_permission->num_rows() < 1) {
            if($this->url_short){
                redirect('task', 'location');
                $body['uri_def'] = 'task';
            }else{
                redirect('n_task/', 'location');
                $body['uri_def'] = 'n_task';
            }
        } else {
            $this->board_id_active = $board_id;
        }

        $board_a = $check_permission->result_array();

        $data['board_name_active'] = $board_a[0]['board_name'];

        $data['board_id'] = $board_id;
        $data['containers'] = $this->db->query("SELECT * FROM containers WHERE container_board = '$board_id' ORDER BY container_order ASC")->result_array();


        if(empty($data['containers'])){
            $data['title'] = $this->lang->line('Kanban');
            $data['body'] = 'new_board';
            $data['page_title'] = $data['title'];
            $this->_viewcontroller($data);
            return;
        }

        $data['board_id_active'] = $board_id;

        $data['boards'] = $this->db->query("SELECT * FROM boards WHERE board_id
                                            IN (SELECT board_id FROM boards_users WHERE user_id = '{$this->user_id}')
                                            ORDER BY board_order ASC")->result_array();


        foreach ($data['containers'] as $key => $container) {
            // Convert hex in rgb for background
            $hex = $container['container_color'];
            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
            $data['containers'][$key]['container_rgb'] = "$r,$g,$b";

            $data['tasks'][$container['container_id']] = $this->db->query("SELECT * FROM tasks WHERE task_container = '{$container['container_id']}' AND task_archived = 0 ORDER BY task_order ASC")->result_array();
        }

        // Check resume work
        $data['task_standby'] = $this->db->query("SELECT *, TIMEDIFF(NOW(), task_date_start) AS last_tracking
                                                  FROM task_periods LEFT JOIN tasks ON tasks.task_id = task_periods.task_id
                                                  WHERE task_periods_user = '{$this->user_id}' AND task_date_stop IS NULL ORDER BY task_periods_id ASC LIMIT 1")->row_array();

        $data['board_time_spent_active'] = $this->db->query("SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( `task_time_spent` ) ) ) AS board_time_spent FROM tasks
                                                       LEFT JOIN containers ON tasks.task_container = containers.container_id
                                                       LEFT JOIN boards ON containers.container_board = boards.board_id
                                                       WHERE board_id = '$board_id' AND task_archived = '0' ")->row()->board_time_spent;

        $data['board_time_spent_archived'] = $this->db->query("SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( `task_time_spent` ) ) ) AS board_time_spent FROM tasks
                                                       LEFT JOIN containers ON tasks.task_container = containers.container_id
                                                       LEFT JOIN boards ON containers.container_board = boards.board_id
                                                       WHERE board_id = '$board_id' AND task_archived = '1'")->row()->board_time_spent;

        $this->db->query("UPDATE boards SET board_default = 0 where  user_id = '{$this->user_id}'");
        $this->db->query("UPDATE boards SET board_default = 1 where board_id= '{$board_id}' AND user_id = '{$this->user_id}'");

        $data['title'] = $this->lang->line('To Do List');
        $data['body'] = 'todo';
        $data['page_title'] = $data['title'];

        if($this->url_short){
            $data['uri_def'] = 'task';
        }else{
            $data['uri_def'] = 'n_task';
        }

        $this->_viewcontroller($data);
    }

    public function new_board(){

        $this->load->helper(array('form'));
        $this->load->library('form_validation');

        $this->form_validation->set_rules('board_name', 'Name', 'required');

        $post = $this->input->post();
        $post['user_id'] = $this->user_id;

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'txt' => strip_tags(validation_errors())));
        } else {

            if (isset($post['board_sharing'])) {
                $users_sharing = $post['board_sharing'];
                unset($post['board_sharing']);
            }
            $template = $post['template'];
            unset($post['template']);
            // Save new board
            $this->db->insert("boards", $post);
            $board_id = $this->db->insert_id();


            switch ($template){
                case '1';
                    $template_load = array();
                    $template_load[] = array(
                        'container_color' => '#3d85c6',
                        'container_name' => $this->lang->line('Inbox'),
                        'container_done' => 0,
                        'container_board' => $board_id
                        );
                    $template_load[] = array(
                        'container_color' => '#b45f06',
                        'container_name' => $this->lang->line('Next action'),
                        'container_done' => 0,
                        'container_board' => $board_id
                    );
                    $template_load[] = array(
                        'container_color' => '#6aa84f',
                        'container_name' => $this->lang->line('Waiting for'),
                        'container_done' => 0,
                        'container_board' => $board_id
                    );
                    $template_load[] = array(
                        'container_color' => '#e06666',
                        'container_name' => $this->lang->line('Projects'),
                        'container_done' => 0,
                        'container_board' => $board_id
                    );
                    $template_load[] = array(
                        'container_color' => '#741b47',
                        'container_name' => $this->lang->line('Some day/maybe'),
                        'container_done' => 0,
                        'container_board' => $board_id
                    );

                    //#6aa84f #3d85c6 #b45f06 #3d85c6 #e06666 #741b47 #444444
                    $this->insert_tenplate($template_load);
                break;

                case '2';
                    $template_load = array();
                    $template_load[] = array(
                        'container_color' => '#3d85c6',
                        'container_name' => $this->lang->line('To Do'),
                        'container_done' => 0,
                        'container_board' => $board_id
                    );
                    $template_load[] = array(
                        'container_color' => '#b45f06',
                        'container_name' => $this->lang->line('In progress'),
                        'container_done' => 0,
                        'container_board' => $board_id
                    );
                    $template_load[] = array(
                        'container_color' => '#6aa84f',
                        'container_name' => $this->lang->line('Completed'),
                        'container_done' => 1,
                        'container_board' => $board_id
                    );

                    //#6aa84f #3d85c6 #b45f06 #3d85c6 #e06666 #741b47 #444444
                    $this->insert_tenplate($template_load);
                    break;

                case '3';
                    $template_load = array();
                    $template_load[] = array(
                        'container_color' => '#6aa84f',
                        'container_name' => $this->lang->line('Inbox'),
                        'container_done' => 0,
                        'container_board' => $board_id
                    );
                    $template_load[] = array(
                        'container_color' => '#b45f06',
                        'container_name' => $this->lang->line('Proposal'),
                        'container_done' => 0,
                        'container_board' => $board_id
                    );
                    $template_load[] = array(
                        'container_color' => '#3d85c6',
                        'container_name' => $this->lang->line('Negotiating'),
                        'container_done' => 0,
                        'container_board' => $board_id
                    );
                    $template_load[] = array(
                        'container_color' => '#e06666',
                        'container_name' => $this->lang->line('Waiting'),
                        'container_done' => 0,
                        'container_board' => $board_id
                    );
                    $template_load[] = array(
                        'container_color' => '#741b47',
                        'container_name' => $this->lang->line('Deal'),
                        'container_done' => 1,
                        'container_board' => $board_id
                    );

                    //#6aa84f #3d85c6 #b45f06 #3d85c6 #e06666 #741b47 #444444
                    $this->insert_tenplate($template_load);
                    break;

                default;
                    $post = array();
                    $post['container_color'] = '#6aa84f';
                    $post['container_name'] = $this->lang->line('Start');
                    $post['container_done'] = 0;
                    $post['container_board'] = $board_id;
                    $this->db->insert("containers", $post);
                break;
            }

            // Save user-board association
            $this->db->insert('boards_users', array('board_id' => $board_id, 'user_id' => $this->session->userdata('user_id')));

            //Check and save sharing users
//            if (isset($users_sharing)) {
//                foreach ($users_sharing as $user_id) {
//                    $this->db->insert('boards_users', array('board_id' => $board_id, 'user_id' => $user_id));
//                }
//            }

            if($this->url_short){
                $uri_short = 'task/board/';
            }else{
                $uri_short = 'n_task/board/';
            }


            echo json_encode(array('status' => 1, 'txt' => base_url() . $uri_short . $board_id));
        }

    }

    private function insert_tenplate($template){
        foreach($template as $k => $v){
            $this->db->insert("containers", $v);
        }
    }

    public function save_task(){
        //$this->load->model('mail_model');


//        if ($this->session->userdata('user_session')['user_permissions'] > 10) {
//            echo json_encode(array('status' => 0, 'txt' => 'You don\'t have permission to create new task :('));
//            return false;
//        }
        $this->load->helper(array('form'));
        $this->load->library('form_validation');

        $this->form_validation->set_rules('task_title', 'Title', 'required');

        $post = $this->input->post();


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'txt' => strip_tags(validation_errors())));

        } else {
            $task_todo = $post['task_todo'];
            unset($post['task_todo']);

            $post['task_user'] = $this->user_id;
            $post['user_id'] = $this->user_id;
            $this->db->insert("tasks", $post);
            $task_id = $this->db->insert_id();

            // Save todo
            if ($task_todo) {
                foreach (json_decode($task_todo) as $todo) {
                    if ($todo) {
                        $this->db->insert("tasks_todo", array("task_id" => $task_id, "title" => $todo, "status" => 0, "user_id" => $this->user_id));
                    }
                }
            }


            echo json_encode(array('status' => 4));

            // Check if this board is shared with other users
            $container_id = $post['task_container'];
            $my_user_id = $this->user_id;

            $board = $this->db->query("SELECT * FROM containers LEFT JOIN boards ON container_board = board_id WHERE container_id = '$container_id'")->row_array();
//            $users = $this->db->query("SELECT * FROM boards_users NATURAL LEFT JOIN users WHERE board_id = '{$board['board_id']}' AND user_id <> '$my_user_id'");

//            if ($users->num_rows() > 0) {
//                foreach ($users->result_array() as $user) {
//                    $data['user'] = $user;
//                    $data['user_creator'] = $this->session->userdata('user_session');
//                    $data['task'] = $post;
//                    $data['board'] = $board;
//                    $this->mail_model->sendFromView($user['user_email'], "mail_template/new_task.php", $data, array(), "New task!");
//                }
//            }


        }

    }

    public function update_position(){
        $data = $this->input->post();
        $to_done = false;
        //todo: add check permission

        foreach ($data as $container_id => $tasks) {
            $x = 1;
            if ($this->db->query("SELECT * FROM containers WHERE container_id = '$container_id' AND container_done = '1'")->num_rows() > 0) {
                $to_done = true;
            }
            foreach ($tasks as $task) {
                // Check if drag to DONE column
                if ($to_done == true) {
                    $this->db->query("UPDATE tasks SET task_date_closed = IF(task_date_closed IS NULL AND task_container <> '$container_id', NOW(), task_date_closed), task_container = '$container_id', task_order = '$x' WHERE task_id = '$task'");
                } else {
                    $this->db->query("UPDATE tasks SET task_container = '$container_id', task_order = '$x' WHERE task_id = '$task'");
                }
                $x++;
            }
        }
    }

    public function time_tracker($what, $task_id){
        if (!$task_id || !$what)
            return false;

        if ($what == "start") {
            // Check if i have a same record
            if ($this->db->query("SELECT * FROM task_periods WHERE task_id = '$task_id' AND task_date_stop IS NULL")->num_rows() < 1) {
                $this->db->insert("task_periods", array("task_id" => $task_id, "task_date_start" => date("Y-m-d H:i:s"), 'task_periods_user' => $this->user_id));
            }
            $task = $this->db->query("SELECT * FROM tasks WHERE task_id = '$task_id'")->row_array();
            echo json_encode($task);

        } else if ($what == "stop") {
            echo date('Y-m-d H:i:s');

            $this->db->query("UPDATE task_periods SET task_date_stop = '" . date('Y-m-d H:i:s') . "' WHERE task_id = '$task_id' AND task_date_stop IS NULL ");

            $this->db->query("UPDATE tasks SET task_time_spent = (SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(timediff(task_date_stop, task_date_start)))) FROM task_periods WHERE task_id = '$task_id') WHERE task_id = '$task_id'");

        }
    }

    public function get_task_details($task_id){
        if (!$task_id)
            return false;

//        switch ($this->session->userdata('conf_date_format')) {
//            case 1:
//                $date_hour_format = "%Y-%m-%d %H:%i";
//                $date_format = "%Y-%m-%d";
//                break;
//            case 2:
//                $date_hour_format = "%d-%m-%Y %H:%i";
//                $date_format = "%d-%m-%Y";
//                break;
//            case 3:
//                $date_hour_format = "%m-%d-%Y %h:%i %p";
//                $date_format = "%m-%d-%Y";
//                break;
//        }

        $date_hour_format = "%Y-%m-%d %H:%i";
        $date_format = "%Y-%m-%d";

        $data['task'] = $this->db->query("SELECT tasks.*, users.id, users.name, DATE_FORMAT(task_date_creation,'$date_hour_format') AS task_date_creation, DATE_FORMAT(task_date_closed,'$date_hour_format') AS task_date_closed FROM tasks LEFT JOIN users ON task_user = id WHERE task_id = '$task_id'")->row_array();

        $data['task_attachments'] = $this->db->query("SELECT attachments.*, users.id, users.name,  DATE_FORMAT(attachment_creation_date,'%d-%m-%Y') AS attachment_creation_date FROM attachments LEFT JOIN users ON attachment_user_id = id WHERE attachment_task_id = '$task_id'")->result_array();

        $data['task_todo'] = $this->db->query("SELECT * FROM tasks_todo WHERE task_id = '$task_id'")->result_array();

        $data['task_periods'] = $this->db->query("SELECT task_periods.*, users.id, users.name, TIMEDIFF(task_date_stop, task_date_start) AS total_time, DATE_FORMAT(task_date_start,'$date_hour_format') AS task_date_start, DATE_FORMAT(task_date_stop,'$date_hour_format') AS task_date_stop
                                                      FROM task_periods
                                                      LEFT JOIN users ON task_periods.task_periods_user = users.id
                                                      WHERE task_id = '$task_id'")->result_array();
        //      }
        $data['task_time_spent'] = $this->db->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(timediff(task_date_stop, task_date_start)))) AS total_time_spent FROM task_periods WHERE task_id = '$task_id'")->row()->total_time_spent;

        echo json_encode($data);
    }

    public function new_container(){
        $this->load->helper(array('form'));
        $this->load->library('form_validation');

        $this->form_validation->set_rules('container_name', 'Name', 'required');

        $post = $this->input->post();

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'txt' => strip_tags(validation_errors())));
        } else {

            $this->db->insert("containers", $post);
            echo json_encode(array('status' => 4));
        }
    }

    public function edit_container(){
        $this->load->helper(array('form'));
        $this->load->library('form_validation');

        $this->form_validation->set_rules('container_name', 'Name', 'required');

        $post = $this->input->post();

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'txt' => strip_tags(validation_errors())));
        } else {
            if (!isset($post['container_done'])) {
                $post['container_done'] = 0;
            }
            $this->db->where("container_id", $post['container_id']);
            $this->db->update("containers", $post);
            echo json_encode(array('status' => 4));
        }
    }

    private function delete_safe($from, $field_id, $id_element){
        if ($from == "attachments") {
            $AND = 'AND attachment_user_id = '.$this->user_id;
            $this->db->query("DELETE FROM $from WHERE $field_id = '$id_element' ".$AND);
        }

        if ($from == "task_periods") {
            $AND = 'AND task_periods_user = '.$this->user_id;
            $this->db->query("DELETE FROM $from WHERE $field_id = '$id_element' ".$AND);
        }

        if ($from == "tasks") {
            $AND = 'AND task_user = '.$this->user_id;
            $this->db->query("DELETE FROM tasks WHERE task_id = '$id_element' ".$AND);

            $AND = 'AND task_periods_user = '.$this->user_id;
            $this->db->query("DELETE FROM task_periods WHERE task_id = '$id_element' ".$AND);
        }

        if($from=='tasks_todo'){
            $AND = 'AND user_id = '.$this->user_id;
            $this->db->query("DELETE FROM tasks_todo WHERE $field_id = '$id_element' ".$AND);
        }
    }

    public function delete_j($from, $field_id, $id_element){
        $this->delete_safe($from, $field_id, $id_element);
        echo json_encode(array('status' => 4));
    }

    public function delete_r($from, $field_id, $id_element){
        $this->delete_safe($from, $field_id, $id_element);

        if($this->url_short){
            redirect('task', 'location');
        }else{
            redirect('n_task/', 'location');
        }
    }

    public function edit_task(){

        $this->load->helper(array('form'));
        $this->load->library('form_validation');

        $this->form_validation->set_rules('task_title', 'Title', 'required');

        $post = $this->input->post();

        $task_todo = $post['task_todo'];
        unset($post['task_todo']);

        if ($this->db->query("SELECT * FROM tasks WHERE task_user = '{$this->user_id}' AND task_id = {$post['task_id']} limit 1")->num_rows() == 0) {
            echo json_encode(array('status' => 0, 'txt' => 'You don\'t have permission to edit task :('));
            return false;
        }

        // Save todo
        if ($task_todo) {
            foreach (json_decode($task_todo) as $todo) {
                if ($todo) {
                    $this->db->insert("tasks_todo", array("task_id" => $post['task_id'], "title" => $todo, "status" => 0));
                }
            }
        }


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'txt' => strip_tags(validation_errors())));
        } else {

            if ($this->db->query("SELECT * FROM containers WHERE container_id = '{$post['task_container']}' AND container_done = '1'")->num_rows() > 0) {

                $this->db->query("UPDATE tasks SET task_date_closed = IF(task_date_closed IS NULL, NOW(), task_date_closed) WHERE task_id = '{$post['task_id']}'");
            }

            $this->db->where("task_id", $post['task_id']);
            $this->db->update("tasks", $post);
            echo json_encode(array('status' => 4));
        }
    }

    public function edit_board(){
        $this->load->helper(array('form'));
        $this->load->library('form_validation');

        $this->form_validation->set_rules('board_name', 'Title', 'required');

        $post = $this->input->post();

        $this->db->query("UPDATE boards SET board_name = '{$post['board_name']}' WHERE board_id = '{$post['board_id']}' AND user_id = ".$this->user_id);

        echo json_encode(array('status' => 4));
    }

    public function upload_attachments() {

        $myfile = FCPATH.'upload/n_task/';
        if (!file_exists($myfile)) {
            mkdir($myfile, 0755, true);
        }

        $config['upload_path']          = FCPATH.'upload/n_task/';
        $config['allowed_types']        = 'gif|jpg|png|pdf|xls|csv|xml|odt|doc|ppt|jpeg|mov|mp4|mp3|zip|rar|docx|xlsx|pptx|avi|html|js|svg';
        $config['max_size']             = 10000; // 100.00 = 10MB
        $config['file_name']            = md5(time());

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ( ! $this->upload->do_upload('file'))
        {
            $error = array('error' => $this->upload->display_errors());

            header('HTTP/1.1 500 Internal Server Booboo');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode($error));
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $data_file = array('attachment_filename' => $data['upload_data']['file_name'],
                'attachment_original_filename' => $data['upload_data']['client_name'],
                'attachment_task_id' => $this->input->post('task_id'),
                'attachment_user_id' => $this->user_id);

            $this->db->insert('attachments', $data_file);

            $data_file['attachment_creation_date'] = "now!";
            $data_file['user_name'] = $this->session->userdata('name');
            $data_file['attachment_id'] = $this->db->insert_id();
            echo (json_encode($data_file));
        }
    }

    public function update_field($table, $field, $value, $field_id, $element_id){

        if($table=='tasks_todo'){
            $this->db->where('user_id', $this->user_id);
            $this->db->where($field_id, $element_id);
            $this->db->update($table, array($field => $value));
        }


        echo json_encode(array('status' => 4));
    }

    public function update_containers_position(){
        $data = $this->input->post("containers_id");
        $board_id = $this->input->post("board_id");
        if($this->check_perm_board($board_id)==false){
            return;
        }

        $x = 0;
        foreach ($data as $container_id) {
            $this->db->query("UPDATE containers SET container_order = '$x' WHERE container_id = '$container_id'");
            $x++;
        }
    }

    private function check_perm_board($board_id){
        $check_permission = $this->db->query("SELECT * FROM boards WHERE board_id
                                            IN (SELECT board_id FROM boards_users WHERE user_id = '{$this->user_id}')
                                            AND board_id = '$board_id' LIMIT 1");

        if (!$board_id || $check_permission->num_rows() < 1) {
            return false;
        } else {
            return true;
        }
    }

    private function check_perm_from_container($container_id){
        $check_permission = $this->db->query("SELECT container_board FROM containers WHERE container_id = '$container_id' LIMIT 1")->row_array();

        if(!empty($check_permission['container_board'])){
           return $this->check_perm_board($check_permission['container_board']);
        }else{
            return false;
        }
    }

    public function get_container_details($container_id){
        if (!$container_id){return false;}
        if($this->check_perm_from_container($container_id)==false){exit;}

        $data['container_tasks_count'] = $this->db->query("SELECT COUNT(*) AS count FROM tasks WHERE task_container = '$container_id'")->row_array();
        $data['container_data'] = $this->db->query("SELECT * FROM containers WHERE container_id = '$container_id'")->row_array();
        echo json_encode($data);
    }

    public function delete_container(){
        $data = $this->input->post();

        if($this->check_perm_from_container($data['container_id'])==false){exit;}

        $this->db->query("DELETE FROM containers WHERE container_id = '{$data['container_id']}'");

        // Move task to another column
        if ($data['move_container'] != 0) {
            $this->db->query("UPDATE tasks SET task_container = '{$data['move_container']}' WHERE task_container = '{$data['container_id']}'");
        }
        echo json_encode(array('status' => 4));
    }

    public function delete_board($board_id){
        if($this->check_perm_board($board_id)==false){exit;}
        $this->db->query("DELETE FROM boards WHERE board_id = '{$board_id}'");
        $this->db->query("DELETE FROM boards_users WHERE board_id = '{$board_id}'");

        // Delete all task and all columns from board
        $this->db->query("DELETE FROM tasks WHERE task_container IN (SELECT container_id FROM containers WHERE container_board = '{$board_id}')");
        $this->db->query("DELETE FROM containers WHERE container_board = '{$board_id}'");
        echo json_encode(array('status' => 4));
    }





    //////core
    public function activate()
    {
        $this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $purchase_code=$this->input->post('purchase_code');

        //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
         $sidebar=array();
        // mysql raw query needed to run, it's an array, put each query in a seperate index, create table query must should IF NOT EXISTS
        $sql=array
        (
            0 => "CREATE TABLE IF NOT EXISTS `containers` (
`container_id` int(11) NOT NULL,
  `container_board` int(11) NOT NULL,
  `container_name` varchar(255) NOT NULL,
  `container_order` int(11) NOT NULL,
  `container_color` varchar(11) NOT NULL,
  `container_done` tinyint(1) NOT NULL DEFAULT '0'
);",

            1 => "CREATE TABLE IF NOT EXISTS `task_periods` (
`task_periods_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `task_date_start` datetime NOT NULL,
  `task_date_stop` datetime DEFAULT NULL
);",

            2 => "CREATE TABLE IF NOT EXISTS `tasks` (
`task_id` int(11) NOT NULL,
  `task_title` varchar(255) NOT NULL,
  `task_description` text NOT NULL,
  `task_user` int(11) NOT NULL,
  `task_date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `task_due_date` datetime DEFAULT NULL,
  `task_date_closed` timestamp NULL DEFAULT NULL,
  `task_container` int(11) NOT NULL,
  `task_order` int(11) NOT NULL,
  `task_time_spent` time DEFAULT NULL,
  `task_time_estimate` time DEFAULT NULL,
  `task_color` varchar(20) DEFAULT '0'
);",

            3 => "ALTER TABLE `tasks` ADD PRIMARY KEY (`task_id`);",

            4 => "ALTER TABLE `tasks`
MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=74;",

            5 => "ALTER TABLE `tasks` ADD `task_archived` BOOLEAN NOT NULL DEFAULT FALSE ;",

            6 => "ALTER TABLE `containers` ADD PRIMARY KEY (`container_id`);",

            7 => "ALTER TABLE `containers` MODIFY `container_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=43;",

            8 => "ALTER TABLE `task_periods` ADD `task_periods_user` INT NOT NULL ;",

            9 => "UPDATE `task_periods` SET `task_periods_user` = 1;",

            10 => "ALTER TABLE `task_periods` ADD PRIMARY KEY (`task_periods_id`);",

            11 => "ALTER TABLE `task_periods` MODIFY `task_periods_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=106;",

            12 => "CREATE TABLE IF NOT EXISTS `boards` (
`board_id` int(11) NOT NULL,
  `board_name` varchar(255) NOT NULL,
  `board_default` tinyint(1) NOT NULL,
  `board_order` int(11) NOT NULL
)",

            13 => "ALTER TABLE `boards` ADD PRIMARY KEY (`board_id`);",

            14 => "ALTER TABLE `boards` MODIFY `board_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;",

            15 => "CREATE TABLE IF NOT EXISTS `boards_users` (
  `board_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
)",

            16 => "ALTER TABLE `boards` ADD `user_id` INT(11) NOT NULL AFTER `board_order`;",

            17 => "CREATE TABLE IF NOT EXISTS `tasks_todo` (
`id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL
)",

            18 => "ALTER TABLE `tasks_todo` ADD PRIMARY KEY (`id`);",

            19 => "ALTER TABLE `tasks_todo` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;",

            20 => "ALTER TABLE `tasks_todo` ADD `user_id` INT(11) NOT NULL AFTER `status`;",

            21 => "ALTER TABLE `tasks` ADD `user_id` INT(11) NOT NULL AFTER `task_archived`;",

            22 => "CREATE TABLE IF NOT EXISTS `attachments` (
`attachment_id` int(11) NOT NULL,
  `attachment_filename` varchar(255) NOT NULL,
  `attachment_original_filename` varchar(255) NOT NULL,
  `attachment_task_id` int(11) NOT NULL,
  `attachment_creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
)",

            23 => "ALTER TABLE `attachments` ADD PRIMARY KEY (`attachment_id`);",

            24 => "ALTER TABLE `attachments` MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT;",

            25 => "ALTER TABLE `attachments` CHANGE `attachment_filename` `attachment_filename` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",

            26 => "ALTER TABLE `attachments` CHANGE `attachment_original_filename` `attachment_original_filename` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",

            27 => "ALTER TABLE `attachments` ADD `attachment_user_id` INT NOT NULL ;",

            28 => "ALTER TABLE `users` ADD `user_permissions` INT NOT NULL DEFAULT '0' ;",

            29 => "ALTER TABLE `boards` CHANGE `board_name` `board_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",

            30 => "ALTER TABLE `containers` CHANGE `container_name` `container_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `container_color` `container_color` VARCHAR(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",

            31 => "ALTER TABLE `tasks` CHANGE `task_title` `task_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `task_description` `task_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `task_color` `task_color` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0';",

            32 => "ALTER TABLE `tasks_todo` CHANGE `title` `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",



        );

        $sql_cust = "DELETE from `menu` where module_access = 3010" ;
        $this->db->query($sql_cust);

        $sql_cust = "DELETE from `menu_child_1` where url like '%n_task/%' " ;
        $this->db->query($sql_cust);

        $menu_exists = $this->db->query(" SELECT id FROM `menu` where url LIKE '%n_task/%' ")->row_array();
        $parent_id_to_add = $this->db->query(" SELECT serial FROM `menu` where url LIKE '%search_tools%' ")->row_array();
        if(!$menu_exists){
            try{
                $sql_cust = "INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Kanban', 'fa fa-tasks', 'n_task/', ".$parent_id_to_add['serial'].", '3010', '0', '0', '0', '0', '0', '', '0', '0')" ;
                $this->db->query($sql_cust);
            }catch(Exception $e){

            }
        }

        //send blank array if you does not need sidebar entry,send a blank array if your addon does not need any sql to run
        $this->register_addon($addon_controller_name,$sidebar,$sql,$purchase_code);
    }

    public function fix_database(){

        $sql_cust = "ALTER TABLE `boards` CHANGE `board_name` `board_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;";
        $this->db->query($sql_cust);

            $sql_cust = "ALTER TABLE `containers` CHANGE `container_name` `container_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `container_color` `container_color` VARCHAR(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;";
        $this->db->query($sql_cust);

            $sql_cust = "ALTER TABLE `tasks` CHANGE `task_title` `task_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `task_description` `task_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `task_color` `task_color` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0';";
        $this->db->query($sql_cust);

            $sql_cust = "ALTER TABLE `tasks_todo` CHANGE `title` `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;";

        $this->db->query($sql_cust);

        echo 'done';
    }

    public function fix_menu(){
        if($this->url_short){
            $uri_short = 'task';
        }else{
            $uri_short = 'n_task';
        }


        $sql_cust = "DELETE from `menu` where module_access = 3010" ;
        $this->db->query($sql_cust);

        $sql_cust = "DELETE from `menu_child_1` where url like '%n_task/%' " ;
        $this->db->query($sql_cust);

        $sql_cust = "DELETE from `menu_child_1` where url like '%task/%' " ;
        $this->db->query($sql_cust);

        $menu_exists = $this->db->query(" SELECT id FROM `menu` where url LIKE '%".$uri_short."/%' ")->row_array();
        $parent_id_to_add = $this->db->query(" SELECT serial FROM `menu` where url LIKE '%search_tools%' ")->row_array();
        if(!$menu_exists){
            try{
                $sql_cust = "INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Kanban', 'fa fa-tasks', '".$uri_short."/', ".$parent_id_to_add['serial'].", '3010', '0', '0', '0', '0', '0', '', '0', '0')" ;
                $this->db->query($sql_cust);
            }catch(Exception $e){
            }
            try{
                $sql_cust = "INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'To Do List', 'fa fa-tasks', '".$uri_short."/list/', ".$parent_id_to_add['serial'].", '3010', '0', '0', '0', '0', '0', '', '0', '0')" ;
                $this->db->query($sql_cust);
            }catch(Exception $e){
            }
        }

        echo 'done';
    }

    public function deactivate(){
        echo json_encode(array('status'=>'0','message'=>$this->lang->line('For deactivate addon please use our NVX Addon Manager. Download: https://nvxgroup.com/addon-manager/')));
        exit();
    }

    public function delete(){
        $this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $add_inf = $this->get_addon_data(APPPATH.'modules/'.$this->router->fetch_class().'/controllers/'.$addon_controller_name.'.php');
        if($add_inf['installed']==1){
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Please first deactivate addon using our NVX Addon Manager. Download: https://nvxgroup.com/addon-manager/')));
            exit();
        }


        // mysql raw query needed to run, it is an array, put each query in a seperate index, drop table/column query should have IF EXISTS
        $sql=array
        (
          0 => "DELETE from `menu` where module_access = 3009",
        );

        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);
    }

    private function nvx_lic($purchase_code){
        $error = '';
        $param    = $this->getParam( $purchase_code );
        $response = $this->_request( 'product/active/'.$this->product_id, $param, $error );

        return $response->status;
    }

    private function encrypt($plainText,$password='') {
        if(empty($password)){
            $password=$this->key;
        }
        $plainText=rand(10,99).$plainText.rand(10,99);
        $method = 'aes-256-cbc';
        $key = substr( hash( 'sha256', $password, true ), 0, 32 );
        $iv = substr(strtoupper(md5($password)),0,16);
        return base64_encode( openssl_encrypt( $plainText, $method, $key, OPENSSL_RAW_DATA, $iv ) );
    }

    private function decrypt($encrypted,$password='') {
        if(empty($password)){
      		$password=$this->key;
      	}
        $method = 'aes-256-cbc';
        $key = substr( hash( 'sha256', $password, true ), 0, 32 );
        $iv = substr(strtoupper(md5($password)),0,16);
        $plaintext=openssl_decrypt( base64_decode( $encrypted ), $method, $key, OPENSSL_RAW_DATA, $iv );
        return substr($plaintext,2,-2);
    }

    private function processs_response($response){
        $resbk="";
          if ( ! empty( $response ) ) {
              if ( ! empty( $this->key ) ) {
                $resbk=$response;
                  $response = $this->decrypt( $response );
              }
              $response = json_decode( $response );

              if ( is_object( $response ) ) {
                  return $response;
              } else {
                $response=new stdClass();
                $response->status = false;
                $bkjson=@json_decode($resbk);
                if(!empty($bkjson->msg)){
                    $response->msg    = $bkjson->msg;
                }else{
                    $response->msg    = "Response Error, contact with the author or update the plugin or theme";
                }
                  $response->data = NULL;
                  return $response;

              }
          }
          $response=new stdClass();
          $response->msg    = "unknown response";
          $response->status = false;
          $response->data = NULL;

          return $response;
    }

    private function _request( $relative_url, $data, &$error = '' ) {
        $response         = new stdClass();
        $response->status = false;
        $response->msg    = "Empty Response";
        $curl             = curl_init();
        $finalData        = json_encode( $data );
        if ( ! empty( $this->key ) ) {
            $finalData = $this->encrypt( $finalData );
        }
        $url = rtrim( $this->server_host, '/' ) . "/" . ltrim( $relative_url, '/' );

        //curl when fall back
        curl_setopt_array( $curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $finalData,
            CURLOPT_HTTPHEADER     => array(
                "Content-Type: text/plain",
                "cache-control: no-cache"
            ),
        ) );
        $serverResponse = curl_exec( $curl );
        //echo $response;
        $error = curl_error( $curl );
        curl_close( $curl );
        if ( ! empty( $serverResponse ) ) {
            return $this->processs_response($serverResponse);
        }
        $response->msg    = "unknown response";
        $response->status = false;
        $response->data = NULL;
        return $response;
    }

    private function getParam( $purchase_key ) {
        $req               = new stdClass();
        $req->license_key  = $purchase_key;
        // $req->email        = ! empty( $admin_email ) ? $admin_email : $this->getEmail();
        $req->domain       = $this->getDomain();
        $req->app_version  = $this->nvx_version;
        $req->product_id   = $this->product_id;
        $req->product_base = $this->product_base;

        return $req;
    }

    private function getDomain() {
	    $base_url = ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == "on" ) ? "https" : "http" );
	    $base_url .= "://" . $_SERVER['HTTP_HOST'];
	    $base_url .= str_replace( basename( $_SERVER['SCRIPT_NAME'] ), "", $_SERVER['SCRIPT_NAME'] );
	    return $base_url;

    }
}
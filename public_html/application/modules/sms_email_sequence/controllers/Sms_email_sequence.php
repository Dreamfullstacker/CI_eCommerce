<?php
/*
Addon Name: SMS & Email Sequence
Unique Name: sms_email_sequence
Modules:
{
   "270":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"month",
      "module_name":"SMS Broadcast - Sequence Campaign"
   },
   "271":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"month",
      "module_name":"Email Broadcast - Sequence Campaign"
   }
}
Project ID: 40
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 2.0
Description: 
*/

require_once("application/controllers/Home.php"); // loading home controller

class sms_email_sequence extends Home
{
    public $addon_data = array();

    protected $module_path;

    public function __construct()
    {
        parent::__construct();

        $function_name=$this->uri->segment(2);

        if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');         
        $this->member_validity();        

        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $this->addon_data=$this->get_addon_data($addon_path);

        // Sets module path
        $this->module_path = APPPATH . '/modules/';
    }

    public function template_lists($type='')
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['270','271']))==0) {
            redirect('home/login_page', 'location');
        }

        $data['body'] = 'sms_email_manager/sequence/template_lists';
        $data['page_title'] = ucfirst($type). ' ' .$this->lang->line('Template');
        $data['template_type'] = $type;
        $this->_viewcontroller($data); 
    }

    public function template_lists_data()
    {
        $this->ajax_check();

        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['270','271']))==0) exit;

        $template_type = trim($this->input->post("template_type",true));
        $template_text  = trim($this->input->post("template_text",true));

        $display_columns = array("#",'id','template_name','actions');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple = array();
        $where_simple['user_id'] = $this->user_id;
        $where_simple['template_type'] = $template_type;

        if($template_text != '') $where_simple['template_name like'] = "%".$template_text."%";

        $where  = array('where'=>$where_simple);

        $table = "email_sms_template";
        $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

        $total_rows_array = $this->basic->count_row($table,$where,$count="id",$join="",$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];


        for($i = 0; $i < count($info); $i++)
        {
            $tempType = $info[$i]['template_type'];
            
            $info[$i]['actions'] = "<div><a href='".base_url()."sms_email_sequence/view_template/".$info[$i]['id']."' data-toggle='tooltip' title='".$this->lang->line("View Template")."' class='btn btn-circle btn-outline-primary'><i class='fas fa-eye'></i></a>&nbsp;&nbsp;";

            $info[$i]['actions'] .= "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Edit Template")."' class='btn btn-circle btn-outline-warning edit_template' table_id='".$info[$i]['id']."' type='".$tempType."'><i class='fas fa-edit'></i></a>&nbsp;&nbsp;";

            $info[$i]['actions'] .= "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Delete Template")."' class='btn btn-circle btn-outline-danger delete_template' table_id='".$info[$i]['id']."' type='".$tempType."'><i class='fas fa-trash-alt'></i></a></div>
            <script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }


        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function view_template($id='')
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['270','271']))==0) {
            redirect('home/login_page', 'location');
        }

        if($id == '' || $id == "0") {
            redirect("home/error_404","location");
        }

        $data['template_data'] = $this->basic->get_data("email_sms_template",['where'=>['id'=>$id,'user_id'=>$this->user_id]]);
        $data['templateType'] = $data['template_data'][0]['template_type'];
        $data['body'] = 'sms_email_manager/sequence/view_template';
        $data['page_title'] = $this->lang->line("View"). ' '. ucfirst($data['templateType']). ' ' .$this->lang->line('Template');
        $this->_viewcontroller($data); 
    }

    public function delete_template()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['270','271']))==0) exit;
        $this->csrf_token_check();

        $table_id = $this->input->post("table_id",true);
        $type = $this->input->post("type",true);

        if($table_id == "" || $table_id == "0") exit;

        if($this->basic->delete_data("email_sms_template",array("id"=>$table_id,"user_id"=>$this->user_id)))
        {
            echo "1";

        } else {

            echo "0";
        }

    }

    public function sms_email_template_sequence($for_hour='0')
    {
        $this->ajax_check();
        $push_id = $this->input->post('push_id');
        $campaign_types = $this->input->post('campaign_types');
        $current_template_id = $this->input->post('current_template_id');

        if($for_hour=='1') {
          $template_id_str="hour_template_id";
        }
        else {
            $template_id_str="template_id";
        }  

        $sms_email_template=$this->basic->get_data("email_sms_template",["where"=>['user_id'=>$this->user_id,'template_type'=>$campaign_types]],'','','',$start=NULL,$order_by='id DESC');
        $push_template ='<select name="'.$template_id_str.$push_id.'" class="form-control '.$template_id_str.'" id="'.$template_id_str.$push_id.'">';
        $push_template .="<option value=''>"."--- ".$this->lang->line("Do not send message")." ---"."</option>";
        foreach ($sms_email_template as $key => $value) 
        {
            $selected_id = '';
            if($value['id'] == $current_template_id) $selected_id = 'selected';
            $push_template .="<option value='".$value['id']."' ".$selected_id.">".$value['template_name'].' ['.$value['template_type'].']'."</option>";
        }
        $push_template .='</select><script>$("#'.$template_id_str.$push_id.'").select2();</script>';
        echo $push_template ;   
    }

    private function get_sms_email_template_lists($type="")
    {
        $email_sms_templates = $this->basic->get_data("email_sms_template",['where'=>['user_id'=>$this->user_id,'template_type'=>$type]]);

        $templates = [];
        foreach ($email_sms_templates as $key => $value) {
            $templates[$value['id']]=$value['template_name'].' ['.$value['template_type'].']';
        }

        return $templates;
    }


    public function edited_get_selected_sequence_lists()
    {
        $timezones = $this->_time_zone_list();
        $how_many_days = $this->input->post("how_many_days");
        $how_many_hours = $this->input->post("how_many_hours");
        $page_auto_id = $this->input->post("page_auto_id");

        $campaign_types = $this->input->post("campaign_types"); // on change type
        $current_campaign_id = $this->input->post("current_campaign_id"); // current type id
        $current_campaign_type = $this->input->post("current_campaign_type"); // current database type
        $sms_email_sequence_templates = $this->get_sms_email_template_lists($campaign_types);

        if(isset($current_campaign_id) && ($campaign_types == $current_campaign_type)) {

            $xdata = $this->basic->get_data("messenger_bot_drip_campaign",["where"=>["id"=>$current_campaign_id,"user_id"=>$this->user_id]]);

            $message_content=isset($xdata[0]['message_content'])?json_decode($xdata[0]['message_content'],true):array();
            $default_display = (!empty($message_content)) ? max(array_keys($message_content)) : 3;
            
            $message_content_hourly=isset($xdata[0]['message_content_hourly'])?json_decode($xdata[0]['message_content_hourly'],true):array();
            if(!empty($message_content_hourly))
            {
              $default_display_hour = max(array_keys($message_content_hourly));
              if($default_display_hour==1) $default_display_hour=1;
              else if($default_display_hour==5) $default_display_hour=1;
              else if($default_display_hour==15) $default_display_hour=1;
              else if($default_display_hour==30) $default_display_hour=1;
              else $default_display_hour = ($default_display_hour/60)+4;
            }
            else  $default_display_hour = 3;

            $msg_content = true;

        } else {

            $default_display = 3;
            $default_display_hour = 3;
            $msg_content = false;
        }

        $between_start = isset($xdata[0]['between_start']) ? $xdata[0]['between_start']:"00:00";
        $between_end = isset($xdata[0]['between_end']) ? $xdata[0]['between_end']:"23:59";
        $selcted_timezone = isset($xdata[0]['timezone']) ? $xdata[0]['timezone'] : $this->config->item('time_zone');

        if($campaign_types == 'sms') {
            $tooplip1='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Starting & Closing Time").'" data-content="'.$this->lang->line("System will start processing sms from starting hour & terminate processing at closing hour of the day. The time interval must be minimum one hour. If your subscriber list for this campaign is large, you should select larger time interval in order to send all sms properly.").'"><i class="fa fa-info-circle"></i> </a>';
        }

        if($campaign_types == 'email') {
            $tooplip1='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Starting & Closing Time").'" data-content="'.$this->lang->line("System will start processing email from starting hour & terminate processing at closing hour of the day. The time interval must be minimum one hour. If your subscriber list for this campaign is large, you should select larger time interval in order to send all email properly.").'"><i class="fa fa-info-circle"></i> </a>';
        }

        $html = '
            <ul class="nav nav-tabs" id="sequence_tab" role="tablist">

                <li class="nav-item">
                    <a class="nav-link active" id="sequence_tab2" data-toggle="tab" href="#hourwise" role="tab" aria-controls="profile" aria-selected="false">'.$this->lang->line("24 Hour").'
                </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="sequence_tab1" data-toggle="tab" href="#daywise" role="tab" aria-selected="true">'.$this->lang->line("Daily").'</a>
                </li>
            </ul>

            <div class="tab-content tab-bordered">
                <div class="tab-pane fade" id="daywise" role="tabpanel" aria-labelledby="sequence_tab1">
                    <div class="row">
                        <div class="col-6 col-md-4">
                            <div class="form-group">
                                <label>'.$this->lang->line("Starting Time")." ".$tooplip1.'</label>
                                <input type="text" class="form-control timepicker_x" value="'.$between_start.'" id="between_start" name="between_start">
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="form-group">
                                <label>'.$this->lang->line("Closing Time")." ".$tooplip1.'</label>
                                <input type="text" class="form-control timepicker_x" value="'.$between_end.'" id="between_end" name="between_end">
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="form-group">
                            <label>'.$this->lang->line("Time Zone").'</label>'.
                            form_dropdown('timezone', $timezones,$selcted_timezone,"class='form-control select2' id='timezone' style='width:100%;'").'
                            </div>
                        </div>
                    </div>';

                    for($i=1; $i <=$how_many_days ; $i++) 
                    { 
                        $hideshowclass='';
                        if($i>$default_display) $hideshowclass='hidden';

                        $html .='<div class="row '.$hideshowclass.'" id="day_container'.$i.'">
                                    <div class="form-group col-3">
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="checkbox" value="'.$i.'" id="day'.$i.'" class="selectgroup-input" checked>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-calendar"></i> '.$this->lang->line('Day').'-'.$i.'</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group col-7">              
                                        <div id="sms_email_sequence_templates'.$i.'">';

                                        $select_template = '';
                                        if($msg_content == true) {
                                            $message_content=json_decode($xdata[0]['message_content'],true);
                                            $select_template=isset($message_content[$i])?$message_content[$i]:'';
                                        }

                                        $template_id="template_id".$i;
                                        $sms_email_sequence_templates['']="--- ".$this->lang->line("Do not send message")." ---";
                        $html .= form_dropdown($template_id,$sms_email_sequence_templates, $select_template,'class="form-control template_id select2" id="'.$template_id.'" style="width:100%;"').
                                        '</div>
                                    </div>
                                    <div class="form-group col-2">              
                                        <a href="" title="'.$this->lang->line("Refresh Template List").'" data-toggle="tooltip" data-id="'.$i.'" class="ref_template btn btn-lg"><i class="fas blue fa-sync"></i></a>
                                    </div>
                                </div>';
                    }

                    $html .='<div class="row button_container">
                                <div class="form-group col-7 offset-3">
                                    <a id="add_more_day" href="" class="btn btn-outline-primary btn-sm float-left"><i class="fas fa-plus-circle"></i> '.$this->lang->line('Add More Day').'</a>
                                    <a id="remove_last_day" href="" class="btn btn-outline-danger btn-sm float-right"><i class="fas fa-times-circle"></i> '.$this->lang->line('Remove Last Day').'</a>
                                </div>
                                <div class="form-group col-2">
                                  <a target="_BLANK" title="'.$this->lang->line('Add New Template').'" data-toggle="tooltip" class="btn btn-default btn-lg add_template"  href=""><i class="fas fa-plus-circle"></i></a>
                                </div>
                            </div>
                </div>

                <div class="tab-pane fade show active" id="hourwise" role="tabpanel" aria-labelledby="sequence_tab2">';
                    for($i=0; $i <=$how_many_hours ; $i++) 
                    { 
                        $hideshowclass='';
                        if($i>$default_display_hour) $hideshowclass='hidden';
                        if($i==0)
                        {
                            $minutes = 1;
                            $displayname = $this->lang->line('1 Mins');
                        }

                        if($i==1)
                        {
                            $minutes = 5;
                            $displayname = $this->lang->line('5 Mins');
                        }

                        if($i==2)
                        {
                            $minutes = 15;
                            $displayname = $this->lang->line('15 Mins');
                        }

                        if($i==3)
                        {
                            $minutes = 30;
                            $displayname = $this->lang->line('30 Mins');
                        } 

                        if($i > 3) {
                            $minutes = ($i-3)*60;
                            $displayname = ($i-3)." ".$this->lang->line('Hour');
                        }

                        $select_template_hourly = '';
                        if($msg_content == true) {
                            $message_content_hourly=json_decode($xdata[0]['message_content_hourly'],true);
                            $select_template_hourly=isset($message_content_hourly[$minutes])?$message_content_hourly[$minutes]:'';
                        }

                        $html .='<div class="row '.$hideshowclass.'" id="hour_container'.$i.'">
                                    <div class="form-group col-3">
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="checkbox" value="'.$minutes.'" id="hour'.$i.'" class="selectgroup-input" checked>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="far fa-clock"></i> '.$displayname.'</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group col-7">              
                                        <div id="hour_sms_email_sequence_templates'.$i.'">';
                                        $template_id="hour_template_id".$i;
                                        $sms_email_sequence_templates['']="--- ".$this->lang->line("Do not send message")." ---";
                        $html .=        form_dropdown($template_id,$sms_email_sequence_templates, $select_template_hourly,'class="form-control hour_template_id select2" id="'.$template_id.'" style="width:100%;"').'
                                        </div>
                                    </div>
                                    <div class="form-group col-2">              
                                      <a href="" title="'.$this->lang->line("Refresh Template List").'" data-toggle="tooltip" data-id="'.$i.'" class="hour_ref_template btn btn-lg"><i class="fas blue fa-sync"></i></a>
                                    </div>
                                </div>';
                    }



                $html .= '
                    <div class="row button_container">
                        <div class="form-group col-7 offset-3">
                            <a id="add_more_hour" href="" class="btn btn-outline-primary btn-sm float-left"><i class="fas fa-plus-circle"></i> '.$this->lang->line('Add More Hour').'</a>
                            <a id="remove_last_hour" href="" class="btn btn-outline-danger btn-sm float-right"><i class="fas fa-times-circle"></i> '.$this->lang->line('Remove Last Hour').'</a>
                        </div>
                        <div class="form-group col-2">
                            <a target="_BLANK" title="'.$this->lang->line('Add New Template').'" data-toggle="tooltip" class="btn btn-default btn-lg add_template"  href=""><i class="fas fa-plus-circle"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <script>
            $(".template_id,.hour_template_id,#timezone").select2();
            $(".timepicker_x").datetimepicker({
              datepicker:false,
              format:"H:i"
            });
            $(\'[data-toggle="popover"]\').popover(); 
            $(\'[data-toggle="popover"]\').on("click", function(e) {e.preventDefault(); return true;});
            $(\'[data-toggle=\"tooltip\"]\').tooltip();
        </script>
        ';

        echo $html;
    }


    public function get_selected_sequence_lists()
    {
        $timezones = $this->_time_zone_list();
        $how_many_days = $this->input->post("how_many_days");
        $how_many_hours = $this->input->post("how_many_hours");
        $default_display = $this->input->post("default_display");
        $default_display_hour = $this->input->post("default_display_hour");
        $page_auto_id = $this->input->post("page_auto_id");
        $campaign_types = $this->input->post("campaign_types");
        $sms_email_sequence_templates = $this->get_sms_email_template_lists($campaign_types);

        if($campaign_types == "sms") {
            $tooplip1='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Starting & Closing Time").'" data-content="'.$this->lang->line("System will start processing sms from starting hour & terminate processing at closing hour of the day. The time interval must be minimum one hour. If your subscriber list for this campaign is large, you should select larger time interval in order to send all sms properly.").'"><i class="fa fa-info-circle"></i> </a>';
        }

        if($campaign_types == "email") {
            $tooplip1='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Starting & Closing Time").'" data-content="'.$this->lang->line("System will start processing email from starting hour & terminate processing at closing hour of the day. The time interval must be minimum one hour. If your subscriber list for this campaign is large, you should select larger time interval in order to send all email properly.").'"><i class="fa fa-info-circle"></i> </a>';
        }

        $html = '
            <ul class="nav nav-tabs" id="sequence_tab" role="tablist">

                <li class="nav-item">
                <a class="nav-link active" id="sequence_tab2" data-toggle="tab" href="#hourwise" role="tab" aria-controls="profile" aria-selected="false">'.$this->lang->line("24 Hour").'
                </a>
                </li>
                <li class="nav-item">
                <a class="nav-link" id="sequence_tab1" data-toggle="tab" href="#daywise" role="tab" aria-selected="true">'.$this->lang->line("Daily").'</a>
                </li>
            </ul>

            <div class="tab-content tab-bordered">
                <div class="tab-pane fade" id="daywise" role="tabpanel" aria-labelledby="sequence_tab1">
                    <div class="row">
                        <div class="col-6 col-md-4">
                            <div class="form-group">
                            <label>'.$this->lang->line("Starting Time")." ".$tooplip1.'</label>
                            <input type="text" class="form-control timepicker_x" value="00:00" id="between_start" name="between_start">
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="form-group">
                                <label>'.$this->lang->line("Closing Time")." ".$tooplip1.'</label>
                                <input type="text" class="form-control timepicker_x" value="23:59" id="between_end" name="between_end">
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="form-group">
                            <label>'.$this->lang->line("Time Zone").'</label>'.
                            form_dropdown('timezone', $timezones,$this->config->item('time_zone'),"class='form-control select2' id='timezone' style='width:100%;'").'
                            </div>
                        </div>
                    </div>';

                    for($i=1; $i <=$how_many_days ; $i++) 
                    { 
                        $hideshowclass='';
                        if($i>$default_display) $hideshowclass='hidden';

                        $html .='<div class="row '.$hideshowclass.'" id="day_container'.$i.'">
                                    <div class="form-group col-3">
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="checkbox" value="'.$i.'" id="day'.$i.'" class="selectgroup-input" checked>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-calendar"></i> '.$this->lang->line('Day').'-'.$i.'</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group col-7">              
                                        <div id="sms_email_sequence_templates'.$i.'">';
                                        $template_id="template_id".$i;
                                        $sms_email_sequence_templates['']="--- ".$this->lang->line("Do not send message")." ---";
                        $html .= form_dropdown($template_id,$sms_email_sequence_templates, '','class="form-control template_id select2" id="'.$template_id.'" style="width:100%;"').
                                        '</div>
                                    </div>
                                    <div class="form-group col-2">              
                                        <a href="" title="'.$this->lang->line("Refresh Template List").'" data-toggle="tooltip" data-id="'.$i.'" class="ref_template btn btn-lg"><i class="fas blue fa-sync"></i></a>
                                    </div>
                                </div>';
                    }

                    $html .='<div class="row button_container">
                                <div class="form-group col-7 offset-3">
                                    <a id="add_more_day" href="" class="btn btn-outline-primary btn-sm float-left"><i class="fas fa-plus-circle"></i> '.$this->lang->line('Add More Day').'</a>
                                    <a id="remove_last_day" href="" class="btn btn-outline-danger btn-sm float-right"><i class="fas fa-times-circle"></i> '.$this->lang->line('Remove Last Day').'</a>
                                </div>
                                <div class="form-group col-2">
                                  <a target="_BLANK" title="'.$this->lang->line('Add New Template').'" data-toggle="tooltip" class="btn btn-default btn-lg add_template"  href=""><i class="fas fa-plus-circle"></i></a>
                                </div>
                            </div>
                </div>

                <div class="tab-pane fade show active" id="hourwise" role="tabpanel" aria-labelledby="sequence_tab2">';
                    for($i=0; $i <=$how_many_hours ; $i++) 
                    { 
                        $hideshowclass='';
                        if($i>$default_display_hour) $hideshowclass='hidden';
                        if($i==0)
                        {
                            $minutes = 1;
                            $displayname = $this->lang->line('1 Mins');
                        }

                        if($i==1)
                        {
                            $minutes = 5;
                            $displayname = $this->lang->line('5 Mins');
                        }

                        if($i==2)
                        {
                            $minutes = 15;
                            $displayname = $this->lang->line('15 Mins');
                        }

                        if($i==3)
                        {
                            $minutes = 30;
                            $displayname = $this->lang->line('30 Mins');
                        } 

                        if($i > 3) {
                            $minutes = ($i-3)*60;
                            $displayname = ($i-3)." ".$this->lang->line('Hour');
                        }

                        $html .='<div class="row '.$hideshowclass.'" id="hour_container'.$i.'">
                                    <div class="form-group col-3">
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="checkbox" value="'.$minutes.'" id="hour'.$i.'" class="selectgroup-input" checked>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="far fa-clock"></i> '.$displayname.'</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group col-7">              
                                        <div id="hour_sms_email_sequence_templates'.$i.'">';
                                        $template_id="hour_template_id".$i;
                                        $sms_email_sequence_templates['']="--- ".$this->lang->line("Do not send message")." ---";
                        $html .=        form_dropdown($template_id,$sms_email_sequence_templates, '','class="form-control hour_template_id select2" id="'.$template_id.'" style="width:100%;"').'
                                        </div>
                                    </div>
                                    <div class="form-group col-2">              
                                      <a href="" title="'.$this->lang->line("Refresh Template List").'" data-toggle="tooltip" data-id="'.$i.'" class="hour_ref_template btn btn-lg"><i class="fas blue fa-sync"></i></a>
                                    </div>
                                </div>';
                    }



                $html .= '
                    <div class="row button_container">
                        <div class="form-group col-7 offset-3">
                            <a id="add_more_hour" href="" class="btn btn-outline-primary btn-sm float-left"><i class="fas fa-plus-circle"></i> '.$this->lang->line('Add More Hour').'</a>
                            <a id="remove_last_hour" href="" class="btn btn-outline-danger btn-sm float-right"><i class="fas fa-times-circle"></i> '.$this->lang->line('Remove Last Hour').'</a>
                        </div>
                        <div class="form-group col-2">
                            <a target="_BLANK" title="'.$this->lang->line('Add New Template').'" data-toggle="tooltip" class="btn btn-default btn-lg add_template"  href=""><i class="fas fa-plus-circle"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <script>
            $(".template_id,.hour_template_id,#timezone").select2();
            $(".timepicker_x").datetimepicker({
              datepicker:false,
              format:"H:i"
            });
            $(\'[data-toggle="popover"]\').popover(); 
            $(\'[data-toggle="popover"]\').on("click", function(e) {e.preventDefault(); return true;});
            $(\'[data-toggle=\"tooltip\"]\').tooltip();
        </script>
            ';

        echo $html;
    }

    public function sms_email_sequence_message_campaign($page_auto_id=0,$iframe='0')
   	{
    	if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['270','271']))==0) {
    	    redirect('home/login_page', 'location');
    	}

    	if($page_auto_id==0) exit();
        $this->is_engagement_exist=$this->engagement_exist();
    	
    	$data['body'] = 'sms_email_manager/sequence/sequence_campaign_lists';
    	$data['page_title'] = $this->lang->line('SMS/Email Sequence Message'); 
    	$data["page_auto_id"]=$page_auto_id;
        $data['campaign_types'] = ['sms'=>'SMS','email'=>'Email'];
        $data['sms_email_sequence_settings'] = $this->basic->get_data("messenger_bot_drip_campaign",["where"=>["page_id"=>$page_auto_id,"user_id"=>$this->user_id,'campaign_type !='=>'messenger']],$select='',$join='',$limit='',$start=NULL,$order_by='created_at DESC');

        $data['sms_email_sequence_templates'] = $this->get_sms_email_template_lists("email");
        $data["how_many_days"]=30;
        $data["how_many_hours"]=26;
        $data["default_display"]=3;
        $data["default_display_hour"]=3;
        $data['timezones']=$this->_time_zone_list();

        $data['iframe']=$iframe;
        $this->_viewcontroller($data);
    }

    public function create_sequence_campaign_action()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('270','271')))==0) exit();
        $this->is_engagement_exist=$this->engagement_exist();
        $post=$_POST;

        foreach ($post as $key => $value) 
        {
            // $$key=$this->input->post($key,true);
            if(!is_array($value)) $temp = strip_tags($value);
            else $temp = $value;
            $$key=$temp;
        }

        $mid = $sms_api_id = $email_api_id = '';
        if($campaign_types == "sms") {
            $mid=270;
            $sms_api_id = $this->input->post("sms_api_id");
            if(is_null($sms_api_id)) $sms_api_id="";
        }

        if($campaign_types == "email") {
            $mid=271;
            $email_api_id = $this->input->post("email_api_id");
            if(is_null($email_api_id)) $email_api_id="";

        }

        $status=$this->_check_usage($module_id=$mid,$request=1);
        if($status=="3") 
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("You can not create more sequence message campaign. Module limit exceeded.")));
            exit();
        }

        $message_content=array();
        for($i=1; $i<=$day_counter;$i++)
        { 
           $temp="template_id".$i;
           if($$temp!="") $message_content[$i]=$$temp;
        }
        $message_content=json_encode($message_content);

        $message_content_hourly=array();
        for($i=0; $i<=$hour_counter;$i++)
        { 
           if($i==0) $minutes = 1;         
           if($i==1) $minutes = 5;         
           if($i==2) $minutes = 15;         
           if($i==3) $minutes = 30;         
           if($i > 3) {
            $minutes = ($i-3)*60;
            $displayname = ($i-3)." ".$this->lang->line('Hour');
           }

           $temp="hour_template_id".$i;
           if($$temp!="") $message_content_hourly[$minutes]=$$temp;
        }
        $message_content_hourly=json_encode($message_content_hourly);

        $insert_data=array
        (
            "campaign_name"=>$campaign_name,
            "page_id"=>$page_id,
            "user_id"=>$this->user_id,
            "message_content"=>$message_content,
            "message_content_hourly"=>$message_content_hourly,
            "created_at"=>date("Y-m-d H:i:s"),
            "drip_type"=>"custom",
            "campaign_type"=>$campaign_types,
            "between_start"=>$between_start,
            "between_end"=>$between_end,
            "timezone"=>$timezone,
            "message_tag"=>"",
            "engagement_table_id"=>'0',
            "external_sequence_sms_api_id"=>$sms_api_id,
            "external_sequence_email_api_id"=>$email_api_id,
        );

        $this->db->trans_start();
        $this->basic->insert_data("messenger_bot_drip_campaign",$insert_data);

        $this->_insert_usage_log($module_id=$mid,$request=1);

        $this->db->trans_complete();
        if($this->db->trans_status() === false)
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("Something went wrong, please try again.")));
            exit();
        }
        else
        {
            echo json_encode(array("status" => "1", "message" =>$this->lang->line('Campaign has been created successfully.')));
            exit(); 
        }    
    }

    public function edit_sequence_campaign($id=0,$page_auto_id=0,$iframe='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('270','271')))==0)
        redirect('home/login_page', 'location');

        if($page_auto_id==0) exit();
        $this->is_engagement_exist=$this->engagement_exist();

        $data['body'] = 'sms_email_manager/sequence/edit_sequence_campaign';
        $data['page_title'] = $this->lang->line('Edit Sequence Campaign');  
        $data["page_auto_id"]=$page_auto_id;
        $data['campaign_types'] = ['sms'=>'SMS','email'=>'Email'];
        $xdata = $this->basic->get_data("messenger_bot_drip_campaign",["where"=>["id"=>$id,"user_id"=>$this->user_id]]);
        $data['xdata']=isset($xdata[0])?$xdata[0]:[];

        $data["template_list"]=$this->get_sms_email_template_lists($xdata[0]['campaign_type']);
        
        $data["how_many_days"]=30;
        $data["how_many_hours"]=26;

        if($xdata[0]['campaign_type'] == "sms") {
            $data['tooplip1'] ='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Starting & Closing Time").'" data-content="'.$this->lang->line('System will start processing sms from starting hour & terminate processing at closing hour of the day. The time interval must be minimum one hour. If your subscriber list for this campaign is large, you should select larger time interval in order to send all sms properly.').'"><i class="fa fa-info-circle"></i> </a>';
        }

        if($xdata[0]['campaign_type'] == 'email') {
            $data['tooplip1'] ='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Starting & Closing Time").'" data-content="'.$this->lang->line('System will start processing email from starting hour & terminate processing at closing hour of the day. The time interval must be minimum one hour. If your subscriber list for this campaign is large, you should select larger time interval in order to send all email properly.').'"><i class="fa fa-info-circle"></i> </a>';
        }

        $message_content=isset($xdata[0]['message_content'])?json_decode($xdata[0]['message_content'],true):array();
        $default_display = (!empty($message_content)) ? max(array_keys($message_content)) : 3;
        $data["default_display"]=$default_display;
        
        $message_content_hourly=isset($xdata[0]['message_content_hourly'])?json_decode($xdata[0]['message_content_hourly'],true):array();
        if(!empty($message_content_hourly))
        {
          $default_display_hour = max(array_keys($message_content_hourly));
          if($default_display_hour==1) $default_display_hour=1;
          else if($default_display_hour==5) $default_display_hour=2;
          else if($default_display_hour==15) $default_display_hour=3;
          else if($default_display_hour==30) $default_display_hour=4;
          else $default_display_hour = ($default_display_hour/60)+4;
        }
        else  $default_display_hour = 3;
        $data["default_display_hour"]=$default_display_hour;

        $data['timezones']=$this->_time_zone_list();
        // $data['tag_list'] = $this->get_broadcast_tags();

        $data2=$data;
        if(!isset($iframe)) $iframe = '0';
        $data2['iframe']=$iframe;
        
        if($iframe=='1') $this->_viewcontroller($data2);
        else $this->_viewcontroller($data); ; 
    }

    public function edit_sequence_message_campaign_action()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('270','271')))==0) exit();

        $this->is_engagement_exist=$this->engagement_exist();
        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            if(!is_array($value)) $temp = strip_tags($value);
            else $temp = $value;
            $$key=$temp;
        }

        $sms_api_id = $email_api_id = '';

        if($campaign_types == "email") {
            $email_api_id = $this->input->post("email_api_id");
        }

        if($campaign_types == "sms") {
            $sms_api_id = $this->input->post("sms_api_id");
        }

        $message_content=array();
        for($i=1; $i<=$day_counter;$i++)
        { 
           $temp="template_id".$i;
           if($$temp!="") $message_content[$i]=$$temp;
        }
        $message_content=json_encode($message_content);

        $message_content_hourly=array();
        for($i=0; $i<=$hour_counter;$i++)
        { 
            if($i==0) $minutes = 1;         
            if($i==1) $minutes = 5;         
            if($i==2) $minutes = 15;         
            if($i==3) $minutes = 30;         
            if($i > 3) {
             $minutes = ($i-3)*60;
             $displayname = ($i-3)." ".$this->lang->line('Hour');
            }       

           $temp="hour_template_id".$i;
           if($$temp!="") $message_content_hourly[$minutes]=$$temp;
        }
        $message_content_hourly=json_encode($message_content_hourly);

        $insert_data=array
        (
            "campaign_name"=>$campaign_name,
            "campaign_type" => $campaign_types,
            "message_content"=>$message_content,            
            "message_content_hourly"=>$message_content_hourly,
            "between_start"=>$between_start,
            "between_end"=>$between_end,
            "timezone"=>$timezone,
            "drip_type"=>"custom",
            "message_tag"=>"",
            "engagement_table_id"=>"0",
            "external_sequence_sms_api_id"=>$sms_api_id,
            "external_sequence_email_api_id"=>$email_api_id,

        );

        $this->basic->update_data("messenger_bot_drip_campaign",array("id"=>$campaign_id,"user_id"=>$this->user_id),$insert_data);
        echo json_encode(array("status" => "1", "message" =>$this->lang->line('Campaign has been updated successfully.')));          
    }

    public function delete_sequecne_campaign()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('270','271')))==0) exit();

        $id=$this->input->post("id");       
        $campaign_type = $this->input->post("cam_type"); 

        $mid = '';
        if($campaign_type == "sms") $mid = 270;
        if($campaign_type == "email") $mid = 271;

        $this->db->trans_start();

        $this->basic->delete_data("messenger_bot_drip_campaign",array("id"=>$id,"user_id"=>$this->user_id));
        $this->basic->delete_data("messenger_bot_drip_campaign_assign",array("messenger_bot_drip_campaign_id"=>$id,"user_id"=>$this->user_id));
        $this->basic->delete_data("messenger_bot_drip_report",array("messenger_bot_drip_campaign_id"=>$id,"user_id"=>$this->user_id));       

        $this->db->trans_complete();
        if($this->db->trans_status() === false) echo '0';
        else
        {
            $this->_delete_usage_log($mid,1);
            echo '1';
        }
    }

    public function get_campaign_report()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('270','271')))==0) exit();
        $id = $this->input->post("campaign_id");
        $is_day = $this->input->post("is_day");

        $select = array("messenger_bot_drip_report.*","messenger_bot_drip_campaign.message_content","messenger_bot_drip_campaign.message_content_hourly","messenger_bot_drip_campaign.campaign_name");
        $join = array('messenger_bot_drip_campaign'=>"messenger_bot_drip_campaign.id=messenger_bot_drip_report.messenger_bot_drip_campaign_id,left");
        $where = array("where"=>array("messenger_bot_drip_campaign_id"=>$id,"messenger_bot_drip_report.user_id"=>$this->user_id));
        if($is_day=='1')
        {
          $where["where"]["message_content !="]="[]";
          $where["where"]["last_completed_day !="]="0";
        }
        else 
        {
          $where["where"]["message_content_hourly !="]="[]";
          $where["where"]["last_completed_hour !="]="0";
        }

        $report_data = $this->basic->get_data("messenger_bot_drip_report",$where,$select,$join,'',NULL,'messenger_bot_drip_report.id DESC');

        if($is_day=='1') {
            $message_content = isset($report_data[0]["message_content"]) ? json_decode($report_data[0]["message_content"],true) : array();
        }
        else {
            $message_content = isset($report_data[0]["message_content_hourly"]) ? json_decode($report_data[0]["message_content_hourly"],true) : array();
        } 

        $campaign_name  = isset($report_data[0]["campaign_name"]) ? $report_data[0]["campaign_name"] : "";
        
        // subscriber count of this campaign
        $query = "SELECT count(id) as subscriber_count FROM messenger_bot_drip_campaign_assign WHERE messenger_bot_drip_campaign_id=".$id." AND user_id=".$this->user_id;
        // if($is_day=='1') $query.=" AND messenger_bot_drip_last_completed_day!=0";
        // else $query.=" AND messenger_bot_drip_last_completed_hour!=0";
        $sql=$this->db->query($query);
        $subscriber_data=$sql->result_array();
        $total_subscriber_count=isset($subscriber_data[0]['subscriber_count'])?$subscriber_data[0]['subscriber_count']:0;

        // assosiative array of days report
        $report_data_formatted=array();
        foreach ($report_data as $key => $value) 
        {
            if($is_day=='1') $report_data_formatted[$value['last_completed_day']][]=$value;
            else $report_data_formatted[$value['last_completed_hour']][]=$value;
        }
        
        
        $report_data_stat=array(); // day-wise sent/delivered/opened/subsciber 
        $total_report_data_stat=array('sent'=>0,'delivered'=>0,'opened'=>0,'subscribers'=>$total_subscriber_count); // combined report stat of all days
        foreach ($report_data_formatted as $key => $value)
        {
            foreach ($value as $key2 => $value2) 
            {
               if(!isset($report_data_stat[$key]['sent'])) $report_data_stat[$key]['sent']=0;
               if(!isset($report_data_stat[$key]['delivered'])) $report_data_stat[$key]['delivered']=0; 
               if(!isset($report_data_stat[$key]['opened'])) $report_data_stat[$key]['opened']=0; 

               if($value2['is_sent']=='1') $report_data_stat[$key]['sent']++;
               if($value2['is_delivered']=='1') $report_data_stat[$key]['delivered']++;
               if($value2['is_opened']=='1') $report_data_stat[$key]['opened']++;
            }
            
            $report_data_stat[$key]['subscribers']=isset($report_data_formatted[$key])?count($report_data_formatted[$key]):0;

            $total_report_data_stat['sent']+=$report_data_stat[$key]['sent'];
            $total_report_data_stat['delivered']+=$report_data_stat[$key]['delivered'];
            $total_report_data_stat['opened']+=$report_data_stat[$key]['opened'];
        }

        $successfully_sent=isset($total_report_data_stat['sent'])?$total_report_data_stat['sent']:0;
        $successfully_delivered=isset($total_report_data_stat['delivered'])?$total_report_data_stat['delivered']:0;
        $successfully_opened=isset($total_report_data_stat['opened'])?$total_report_data_stat['opened']:0;
        
        if($successfully_delivered==0 || $successfully_sent==0) $delivery_rate=0;
        else $delivery_rate=round(($successfully_delivered/$successfully_sent)*100);

        if($successfully_opened==0 || $successfully_sent==0) $open_rate=0;
        else $open_rate=round(($successfully_opened/$successfully_sent)*100);

        //echo "<h5 class='text-center'>".$this->lang->line('Campaign Name')." : ".$campaign_name."</h5><br>";

        echo '        
        <div class="card card-statistic-2 border_me" style="margin-bottom:0">
          <div class="card-stats" style="padding-bottom:35px">
            <div class="card-stats-title">
            </div>
            <div class="card-stats-items">
              <div class="card-stats-item" data-toggle="tooltip" title="'.$this->lang->line("Targeted Subscribers").'">
                <div class="card-stats-item-count">'.$total_report_data_stat['subscribers'].'</div>
                <div class="card-stats-item-label">'.$this->lang->line("Targeted").'</div>
              </div>
              <div class="card-stats-item" data-toggle="tooltip" title="'.$this->lang->line("Total Sent").'">
                <div class="card-stats-item-count">'.$total_report_data_stat['sent'].'</div>
                <div class="card-stats-item-label">'.$this->lang->line("Sent").'</div>
              </div>
              <div class="card-stats-item" data-toggle="tooltip" title="'.$this->lang->line("Total Delivered").' ('.round($delivery_rate).'%)">
                <div class="card-stats-item-count">'.$total_report_data_stat['delivered'].'</div>
                <div class="card-stats-item-label">'.$this->lang->line("Delivered").' ('.round($delivery_rate).'%)</div>
              </div>
              <!--<div class="card-stats-item" data-toggle="tooltip" title="'.$this->lang->line("Total Opened").' ('.round($open_rate).'%)">
                <div class="card-stats-item-count">'.$total_report_data_stat['opened'].'</div>
                <div class="card-stats-item-label">'.$this->lang->line("Opened").' ('.round($open_rate).'%)</div>
              </div>-->
            </div>
          </div>
        </div>';
        echo "
        <script>
        $('[data-toggle=\"tooltip\"]').tooltip();
        $(document).ready(function() { 
          setTimeout(function(){ $('.btn-link:not(#btn1)').click();  }, 1000);          
        });
        </script>";

        echo '<br><br><div id="accordion">';
        $i=0;
        foreach ($message_content as $key => $value) 
        {
            $i++;  

            $temp_subscribers=isset($report_data_stat[$key]['subscribers'])?$report_data_stat[$key]['subscribers']:0;
            $temp_sent=isset($report_data_stat[$key]['sent'])?$report_data_stat[$key]['sent']:0;
            $temp_delivered=isset($report_data_stat[$key]['delivered'])?$report_data_stat[$key]['delivered']:0;
            $temp_opened=isset($report_data_stat[$key]['opened'])?$report_data_stat[$key]['opened']:0;

            if($temp_delivered==0 || $temp_sent==0) $temp_delivery_rate=0;
            else $temp_delivery_rate=round(($temp_delivered/$temp_sent)*100);

            if($temp_opened==0 || $temp_sent==0) $temp_open_rate=0;
            else $temp_open_rate=round(($temp_opened/$temp_sent)*100);

            $accor_title = "";
            if($is_day=='1')
            $accor_title = '<i class="fa fa-calendar"></i> '.$this->lang->line("Day").'-'.$key;     
            else 
            {
              if($key==1)
                $accor_title = '<i class="fa fa-calendar"></i> 1 '.$this->lang->line("Minute");
              else if($key==5)
                $accor_title = '<i class="fa fa-calendar"></i> 5 '.$this->lang->line("Minute");
              else if($key==15)
                $accor_title = '<i class="fa fa-calendar"></i> 15 '.$this->lang->line("Minute");
              else if($key==30)
                $accor_title = '<i class="fa fa-calendar"></i> 30 '.$this->lang->line("Minute");
              else 
              {
                $hourval = $key/60;
                $accor_title = '<i class="fa fa-calendar"></i> '.$this->lang->line("Hour").'-'.$hourval;
              }
            }   

            echo'            
              <div class="card border_me" style="margin-bottom:0">
                <div class="card-header smallpadding" id="heading'.$key.'">
                  <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" id="btn'.$i.'"  data-target="#collapse'.$key.'" aria-expanded="true" aria-controls="collapse'.$key.'">
                      '.$accor_title.'
                    </button>
                  </h5>
                </div>

                <div id="collapse'.$key.'" class="collapse show" aria-labelledby="heading'.$key.'" data-parent="#accordion">
                  <div class="card-body smallpadding">               

                    <div class="card card-statistic-2 border_me" style="margin-bottom:15px">
                      <div class="card-stats" style="padding-bottom:35px">
                        <div class="card-stats-title">
                        </div>
                        <div class="card-stats-items">
                          <div class="card-stats-item" data-toggle="tooltip" title="'.$this->lang->line("Targeted Subscribers").'">
                            <div class="card-stats-item-count">'.$temp_subscribers.'</div>
                            <div class="card-stats-item-label">'.$this->lang->line("Targeted").'</div>
                          </div>
                          <div class="card-stats-item" data-toggle="tooltip" title="'.$this->lang->line("Total Sent").'">
                            <div class="card-stats-item-count">'.$temp_sent.'</div>
                            <div class="card-stats-item-label">'.$this->lang->line("Sent").'</div>
                          </div>
                          <div class="card-stats-item" data-toggle="tooltip" title="'.$this->lang->line("Total Delivered").' ('.round($temp_delivery_rate).'%)">
                            <div class="card-stats-item-count">'.$temp_delivered.'</div>
                            <div class="card-stats-item-label">'.$this->lang->line("Delivered").' ('.round($temp_delivery_rate).'%)</div>
                          </div>
                          <!--<div class="card-stats-item d-none d-sm-block" data-toggle="tooltip" title="'.$this->lang->line("Total Opened").' ('.round($temp_open_rate).'%)">
                            <div class="card-stats-item-count">'.$temp_opened.'</div>
                            <div class="card-stats-item-label">'.$this->lang->line("Opened").' ('.round($temp_open_rate).'%)</div>
                          </div>-->
                        </div>
                      </div>
                    </div>';

                     echo '
                     <script>
                     $(document).ready(function() { 
                                         
                        var perscroll'.$key.';
                        var table'.$key.' = $("#table'.$key.'").DataTable({
                            language: 
                            {
                              url: "'.base_url('assets/modules/datatables/language/'.$this->language.'.json').'"
                            },
                            dom: \'<"top"f>rt<"bottom"lip><"clear">\',
                            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                                  if(areWeUsingScroll)
                                  {
                                    if (perscroll'.$key.') perscroll'.$key.'.destroy();
                                    perscroll'.$key.' = new PerfectScrollbar("#table'.$key.'_wrapper .dataTables_scrollBody");
                                  }
                              },
                              scrollX: "auto",
                              fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                                  if(areWeUsingScroll)
                                  { 
                                    if (perscroll'.$key.') perscroll'.$key.'.destroy();
                                    perscroll'.$key.' = new PerfectScrollbar("#table'.$key.'_wrapper .dataTables_scrollBody");
                                  }
                              }
                        });

                    });
                    </script>';

                 
                    echo "<br>
                    <div class='table-responsive2 data-card'>
                    <table id='table".$key."' class='table-bordered table-hover table-sm'>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th nowrap>";
                                echo $this->lang->line("SL");
                            echo "</th>";
                            echo "<th class='text-center' nowrap>";
                                echo $this->lang->line("Subscriber ID");
                            echo "</th>";
                            echo "<th nowrap>";
                                echo $this->lang->line("Name");
                            echo "</th>";;
                            echo "<th class='text-center' nowrap>";
                                echo $this->lang->line("Status");
                            echo "</th>"; 
                             echo "<th class='text-center' nowrap>";
                                echo $this->lang->line("Sent");
                            echo "</th>";
                            // echo "<th class='text-center' nowrap>";
                                // echo $this->lang->line("Delivery");
                            // echo "</th>"; 
                            // echo "<th class='text-center' nowrap>";
                            //     echo $this->lang->line("Open");
                            // echo "</th>";                                                           
                            echo "<th nowrap>";
                                echo $this->lang->line("Response");
                            echo "</th>";

                        echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    $sl=0;
                    if(isset($report_data_formatted[$key]))                  
                    foreach ($report_data_formatted[$key] as $key2 => $value2) 
                    {
                        $sl++;
                        if($value2['sent_at']!='0000-00-00 00:00:00') $value2['sent_at']=date("jS M, y H:i",strtotime($value2['sent_at']));
                        else $value2['sent_at']='x';

                        if($value2['delivered_at']!='0000-00-00 00:00:00') $value2['delivered_at']=date("jS M, y H:i",strtotime($value2['delivered_at']));
                        else $value2['delivered_at']='x';

                        if($value2['last_updated_at']!='0000-00-00 00:00:00') $value2['last_updated_at']=date("jS M, y H:i",strtotime($value2['last_updated_at']));
                        else $value2['last_updated_at']='x';

                        if($value2['opened_at']!='0000-00-00 00:00:00') $value2['opened_at']=date("jS M, y H:i:s",strtotime($value2['opened_at']));
                        else $value2['opened_at']='x';
                    
                        if($value2['is_opened']=='1') $value2['status'] = "<span class='badge badge-status'><i class='fa fa-eye text-primary'></i> ".$this->lang->line('Opened')."</span>";
                        // else if($value2['is_delivered']=='1') $value2['status'] = "<span class='badge badge-status'><i class='fa fa-check-circle text-success'></i> ".$this->lang->line('Delivered')."</span>";
                        else $value2['status'] = "<span class='badge badge-status'><i class='fa fa-send text-info'></i> ".$this->lang->line('Sent')."</span>";
                      
                        $db_res=json_decode($value2["sent_response"]);
                        $print_res="";
                        $message_num=0;
                        if(is_array($db_res ))
                        {
                            foreach ($db_res as $key_res => $value_res) 
                            {
                                $message_num++;
                                $tempu=explode(' ', $value_res);
                                if(isset($tempu[0]) && strlen($tempu[0])>50) $value_res=' <i class="fa fa-check-circle green"></i> '.$this->lang->line("Sent");
                                $print_res.=$this->lang->line("Message")."-".$message_num." : ".$value_res."<br>";
                            }
                        }
                        else $print_res=$value2["sent_response"];

                        if($print_res=="") $print_res='<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("Success").'</span>';

                        echo "<tr>";
                            echo "<td nowrap>".$sl."</td>";
                            echo "<td align='center' nowrap>".$value2["subscribe_id"]."</td>";
                            echo "<td nowrap>".$value2["first_name"]." ".$value2["last_name"]."</td>";
                            echo "<td align='center' nowrap>".$value2["status"]."</td>";
                            echo "<td align='center' nowrap>".$value2["sent_at"]."</td>";
                            // echo "<td align='center' nowrap>".$value2["delivered_at"]."</td>";
                            // echo "<td align='center' nowrap>".$value2["opened_at"]."</td>";
                            echo "<td nowrap>".$print_res."</td>";
                        echo "</tr>";
                    }                    
                    echo "</tbody>";
                    echo "</table></div>";


                  echo '
                  </div>
                </div>
              </div>';
        }                

        echo'</div>'; 
    }

    public function external_sequence_lists()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('270','271')))==0) {
            redirect("home/access_forbidden","location");
        }

        $data = [];
        $data['page_title'] = $this->lang->line("Sequence Campaign");
        $data['body'] = "sms_email_manager/sequence/external_sequence/campaign_list";

        $this->_viewcontroller($data);
    }

    public function external_sequence_lists_data()
    {
        $this->ajax_check();

        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['270','271']))==0) exit;

        $sequence_search = trim($this->input->post("sequence_search",true));
        $display_columns = array("#",'id','campaign_name','last_sent_at','campaign_type','actions');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'created_at';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'DESC';
        $order_by=$sort." ".$order;

        $where_simple = array();
        $where_simple['user_id'] = $this->user_id;
        $where_simple['page_id'] = '0';
        $where_simple['campaign_type !='] = 'messenger';

        if($sequence_search !='') {
            $where_simple['campaign_name like'] = "%".$sequence_search."%";
        }

        $where  = array('where'=>$where_simple);

        $table = "messenger_bot_drip_campaign";
        $info = $this->basic->get_data($table,$where,'','',$limit,$start,$order_by);

        $total_rows_array = $this->basic->count_row($table,$where,$count="id",$join="",$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];


        for($i = 0; $i < count($info); $i++)
        {
            $action_count = 2;

            if($info[$i]['last_sent_at'] != "0000-00-00 00:00:00") {
                $info[$i]['last_sent_at'] = date('M j, Y H:i',strtotime($info[$i]['last_sent_at']));
            }

            $tempType = $info[$i]['campaign_type'];
            if($tempType == 'sms') {
                $info[$i]['campaign_type'] = "<span class='badge badge-light'><i class='fas fa-sms'></i> ".strtoupper($tempType)."</span>";
            } else {
                $info[$i]['campaign_type'] = "<span class='badge badge-light'><i class='fas fa-envelope'></i> ".ucfirst($tempType)."</span>";
            }

            $hourly_report_btn= $daily_report_btn ='';
            if($info[$i]['message_content_hourly'] != '[]') {
                $action_count++;
                $hourly_report_btn .= '<a class="btn btn-outline-info btn-circle message_content" href="" data-toggle="tooltip" title="'.$this->lang->line("24H Report").'" data-day="0" data-id="'.$info[$i]['id'].'"><i class="far fa-clock"></i></a>';
            }

            if($info[$i]['message_content']!='[]') {
                $action_count++;
                $daily_report_btn.= '<a class="btn btn-outline-primary btn-circle message_content" data-toggle="tooltip" title="'.$this->lang->line("Daily Report").'" href="" data-day="1" data-id="'.$info[$i]['id'].'"><i class="fas fa-calendar"></i></a>';
            }

            $editurl = base_url("sms_email_sequence/update_external_sequence/").$info[$i]['id'];
            $editbtn = '<a class="btn btn-circle btn-outline-warning edit_sequence_settings" data-toggle="tooltip" title="'.$this->lang->line("Edit Sequence").'" href="'.$editurl.'"><i class="fas fa-edit"></i></a>';
            $delete_btn = '<a href="" class="btn btn-outline-danger btn-circle delete_campaign" data-toogle="tooltip" title="'.$this->lang->line("Delete Campaign").'" campaign_type="'.$tempType.'" id="'.$info[$i]['id'].'"><i class="fas fa-trash-alt"></i></a>';

            $action_width = ($action_count*47)+20;
            $info[$i]['actions'] ='
            <div class="dropdown d-inline dropright">
              <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-briefcase"></i>
              </button>
              <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';
                $info[$i]['actions'] .= $hourly_report_btn;
                $info[$i]['actions'] .= $daily_report_btn;
                $info[$i]['actions'] .= $editbtn;
                $info[$i]['actions'] .= $delete_btn;
                $info[$i]['actions'] .="
              </div>
            </div>
            <script>
            $('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }


    public function create_sequnce_for_external()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('270','271')))==0) {
            redirect("home/access_forbidden","location");
        }

        $data = [];
        $data['page_title'] = $this->lang->line("Add Sequence");
        $data['body'] = "sms_email_manager/sequence/external_sequence/add_campaign";

        $data['external_sms_email_sequence_settings'] = $this->basic->get_data("messenger_bot_drip_campaign",["where"=>["page_id"=>'0',"user_id"=>$this->user_id,'campaign_type !='=>'messenger']],$select='',$join='',$limit='',$start=NULL,$order_by='created_at DESC');

        $data['sms_email_sequence_templates'] = $this->get_sms_email_template_lists("email");
        $data["how_many_days"]=30;
        $data["how_many_hours"]=26;
        $data["default_display"]=3;
        $data["default_display_hour"]=3;
        $data['timezones']=$this->_time_zone_list();

        /***get sms config***/
        $temp_userid = $this->user_id;
        $apiAccess = $this->config->item('sms_api_access');
        if($this->config->item('sms_api_access') == "") $apiAccess = "0";

        if(isset($apiAccess) && $apiAccess == '1' && $this->session->userdata("user_type") == 'Member')
        {
            $join = array('users' => 'sms_api_config.user_id=users.id,left');
            $select = array('sms_api_config.*','users.id AS usersId','users.user_type');
            $where_in = array('sms_api_config.user_id'=>array('1',$temp_userid),'users.user_type'=>array('Admin','Member'));
            $where = array('where'=> array('sms_api_config.status'=>'1'),'where_in'=>$where_in);
            $sms_api_config=$this->basic->get_data('sms_api_config', $where, $select, $join, $limit='', $start='', $order_by='phone_number ASC', $group_by='', $num_rows=0);
        } else
        {
            $where = array("where" => array('user_id'=>$temp_userid,'status'=>'1'));
            $sms_api_config=$this->basic->get_data('sms_api_config', $where, $select='', $join='', $limit='', $start='', $order_by='phone_number ASC', $group_by='', $num_rows=0);
        }

        $sms_api_config_option=array();
        foreach ($sms_api_config as $info) {
            $id=$info['id'];

            if ($info['gateway_name'] == 'custom') {
                $info['gateway_name'] = $this->lang->line("Custom"). ' : '. $info['custom_name'];
            }

            if($info['phone_number'] !="")
                $sms_api_config_option[$id]=$info['gateway_name'].": ".$info['phone_number'];
            else
                $sms_api_config_option[$id]=$info['gateway_name'];
        }
        $data['sms_option'] = $sms_api_config_option;


        /***get smtp  option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_smtp_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        $smtp_option=array();
        foreach ($smtp_info as $info) {
            $id="email_smtp_config_".$info['id'];
            $smtp_option[$id]="SMTP: ".$info['email_address'];
        }
        
        /***get mandrill option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_mandrill_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="email_mandrill_config_".$info['id'];
            $smtp_option[$id]="Mandrill: ".$info['email_address'];
        }

        /***get sendgrid option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_sendgrid_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="email_sendgrid_config_".$info['id'];
            $smtp_option[$id]="SendGrid: ".$info['email_address'];
        }

        /***get mailgun option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_mailgun_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="email_mailgun_config_".$info['id'];
            $smtp_option[$id]="Mailgun: ".$info['email_address'];
        }
        $data['email_apis'] = $smtp_option;

        
        $this->_viewcontroller($data);
    }

    public function update_external_sequence($id)
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('270','271')))==0) {
            redirect("home/access_forbidden","location");
        }

        if($id == 0 || $id == '') {
            redirect("home/error_404","location");
        }

        $data = [];
        $data['page_title'] = $this->lang->line("Edit Sequence");
        $data['body'] = "sms_email_manager/sequence/external_sequence/edit_campaign";

        $data['campaign_types'] = ['sms'=>'SMS','email'=>'Email'];
        $xdata = $this->basic->get_data("messenger_bot_drip_campaign",["where"=>["id"=>$id,"user_id"=>$this->user_id]]);
        $data['xdata']=isset($xdata[0])?$xdata[0]:[];

        $data["template_list"]=$this->get_sms_email_template_lists($xdata[0]['campaign_type']);
        
        $data["how_many_days"]=30;
        $data["how_many_hours"]=26;

        if($xdata[0]['campaign_type'] == "sms") {
            $data['tooplip1'] ='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Starting & Closing Time").'" data-content="'.$this->lang->line('System will start processing sms from starting hour & terminate processing at closing hour of the day. The time interval must be minimum one hour. If your subscriber list for this campaign is large, you should select larger time interval in order to send all sms properly.').'"><i class="fa fa-info-circle"></i> </a>';
        }

        if($xdata[0]['campaign_type'] == 'email') {
            $data['tooplip1'] ='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Starting & Closing Time").'" data-content="'.$this->lang->line('System will start processing email from starting hour & terminate processing at closing hour of the day. The time interval must be minimum one hour. If your subscriber list for this campaign is large, you should select larger time interval in order to send all email properly.').'"><i class="fa fa-info-circle"></i> </a>';
        }

        $message_content=isset($xdata[0]['message_content'])?json_decode($xdata[0]['message_content'],true):array();
        $default_display = (!empty($message_content)) ? max(array_keys($message_content)) : 3;
        $data["default_display"]=$default_display;
        
        $message_content_hourly=isset($xdata[0]['message_content_hourly'])?json_decode($xdata[0]['message_content_hourly'],true):array();
        // echo "<pre>"; print_r($message_content_hourly); exit;
        if(!empty($message_content_hourly))
        {
          $default_display_hour = max(array_keys($message_content_hourly));
          if($default_display_hour==1) $default_display_hour=1;
          else if($default_display_hour==5) $default_display_hour=2;
          else if($default_display_hour==15) $default_display_hour=3;
          else if($default_display_hour==30) $default_display_hour=4;
          else $default_display_hour = ($default_display_hour/60)+4;
        }
        else  $default_display_hour = 3;
        $data["default_display_hour"]=$default_display_hour;

        $data['timezones']=$this->_time_zone_list();


        /***get sms config***/
        $temp_userid = $this->user_id;
        $apiAccess = $this->config->item('sms_api_access');
        if($this->config->item('sms_api_access') == "") $apiAccess = "0";

        if(isset($apiAccess) && $apiAccess == '1' && $this->session->userdata("user_type") == 'Member')
        {
            $join = array('users' => 'sms_api_config.user_id=users.id,left');
            $select = array('sms_api_config.*','users.id AS usersId','users.user_type');
            $where_in = array('sms_api_config.user_id'=>array('1',$temp_userid),'users.user_type'=>array('Admin','Member'));
            $where = array('where'=> array('sms_api_config.status'=>'1'),'where_in'=>$where_in);
            $sms_api_config=$this->basic->get_data('sms_api_config', $where, $select, $join, $limit='', $start='', $order_by='phone_number ASC', $group_by='', $num_rows=0);
        } else
        {
            $where = array("where" => array('user_id'=>$temp_userid,'status'=>'1'));
            $sms_api_config=$this->basic->get_data('sms_api_config', $where, $select='', $join='', $limit='', $start='', $order_by='phone_number ASC', $group_by='', $num_rows=0);
        }

        $sms_api_config_option=array();
        foreach ($sms_api_config as $info) {
            $id=$info['id'];

            if ($info['gateway_name'] == 'custom') {
                $info['gateway_name'] = $this->lang->line("Custom"). ' : '. $info['custom_name'];
            }

            if($info['phone_number'] !="")
                $sms_api_config_option[$id]=$info['gateway_name'].": ".$info['phone_number'];
            else
                $sms_api_config_option[$id]=$info['gateway_name'];
        }
        $data['sms_option'] = $sms_api_config_option;


        /***get smtp  option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_smtp_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        $smtp_option=array();
        foreach ($smtp_info as $info) {
            $id="email_smtp_config_".$info['id'];
            $smtp_option[$id]="SMTP: ".$info['email_address'];
        }
        
        /***get mandrill option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_mandrill_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="email_mandrill_config_".$info['id'];
            $smtp_option[$id]="Mandrill: ".$info['email_address'];
        }

        /***get sendgrid option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_sendgrid_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="email_sendgrid_config_".$info['id'];
            $smtp_option[$id]="SendGrid: ".$info['email_address'];
        }

        /***get mailgun option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_mailgun_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="email_mailgun_config_".$info['id'];
            $smtp_option[$id]="Mailgun: ".$info['email_address'];
        }
        $data['email_apis'] = $smtp_option;


        $this->_viewcontroller($data);
    }


    public function activate()
    {
        $this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $purchase_code=$this->input->post('purchase_code');
        $this->addon_credential_check($purchase_code,strtolower($addon_controller_name)); // retuns json status,message if error

        //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
        $sidebar=array(); 
        // mysql raw query needed to run, it's an array, put each query in a seperate index, create table query must should IF NOT EXISTS
        $sql=array(); 
        //send blank array if you does not need sidebar entry,send a blank array if your addon does not need any sql to run
        $this->register_addon($addon_controller_name,$sidebar,$sql,$purchase_code);
    }


    public function deactivate()
    {        
        $this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        // only deletes add_ons,modules and menu, menu_child1 table entires and put install.txt back, it does not delete any files or custom sql
        $this->unregister_addon($addon_controller_name);         
    }

    public function delete()
    {        
        $this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]

        // mysql raw query needed to run, it's an array, put each query in a seperate index, drop table/column query should have IF EXISTS
         $sql=array(); 

        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }


}
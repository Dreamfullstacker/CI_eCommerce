<?php
/*
Addon Name: Messenger Bot Enhancers 
Unique Name: messenger_bot_enhancers
Modules:
{
   "211":{
      "bulk_limit_enabled":"1",
      "limit_enabled":"1",
      "extra_text":"month",
      "module_name":"Messenger Bot - Enhancers : Broadcast : Subscriber Bulk Message Send"
   },
   "213":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Messenger Bot - Enhancers : Engagement : Checkbox Plugin"
   },
   "214":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Messenger Bot - Enhancers : Engagement : Send to Messenger"
   },
   "215":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Messenger Bot - Enhancers : Engagement : m.me Links"
   },
   "217":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Messenger Bot - Enhancers : Engagement : Customer Chat Plugin"
   },
   "218":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Messenger Bot - Enhancers : Sequence Messaging : Message Send"
   },
   "219":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Messenger Bot - Enhancers : Sequence Messaging Campaign"
   }
}
Project ID: 30
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 2.0.3
Description: 
*/


require_once("application/controllers/Home.php"); // loading home controller

class Messenger_bot_enhancers extends Home
{
  public $addon_data=array();
  public $page_table_name="";
  public $fb_user_info_table_name="";
  public $fb_rx_config_table_name="";
  public $user_info_id=0;
  public $facebook_rx_config_id=0;
  public $user_info_session=0;
    
  public function __construct()
  {
      parent::__construct();

      $function_name=$this->uri->segment(2);
      if($function_name!="messenger_checkbox_plugin.js" && $function_name!="send_to_messenger_plugin.js" && $function_name!="mme_link.js" && $function_name!="rss_autoposting_quick_broadcast_cron_call") 
      {
        if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');         
        $this->member_validity();
      }

      // if(file_exists(APPPATH.'modules/'.strtolower($this->router->fetch_class()).'/config/messenger_bot_enhancers_config.php'))
      // $this->load->config("messenger_bot_enhancers_config");

      // getting addon information in array and storing to public variable
      // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
      //------------------------------------------------------------------------------------------
      $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
      $addondata=$this->get_addon_data($addon_path); 
      $this->addon_data=$addondata;
      // Engagement variables
      $this->page_table_name="facebook_rx_fb_page_info";
      $this->user_info_id="facebook_rx_fb_user_info_id";
      $this->user_info_session=$this->session->userdata("facebook_rx_fb_user_info");
      $this->fb_user_info_table_name="facebook_rx_fb_user_info";
      $this->fb_rx_config_table_name="facebook_rx_config";
      $this->facebook_rx_config_id="facebook_rx_config_id";
     
    }

    public function index()
    {
      $this->subscriber_broadcast_campaign();
    }

    public function estimate_reach()
    {
        $this->ajax_check();
        $auto_id=$this->input->post('auto_id');

        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('user_id' => $this->user_id,"id"=>$auto_id);
        $page_info = $this->basic->get_data($table_name,$where);

        $access_token=isset($page_info[0]['page_access_token']) ? $page_info[0]['page_access_token'] : "";
        if($access_token=='')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
            exit();
        }
        $this->load->library('fb_rx_login');
        $start_reach_estimation=$this->fb_rx_login->start_reach_estimation($access_token);
        $reach_estimation_id=isset($start_reach_estimation['reach_estimation_id']) ? $start_reach_estimation['reach_estimation_id'] : "";
        if($reach_estimation_id=='')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
            exit();
        }
        sleep(20);
        $reach_estimation_count=$this->fb_rx_login->reach_estimation_count($reach_estimation_id,$access_token);
        $reach_estimation=isset($reach_estimation_count['reach_estimation']) ? $reach_estimation_count['reach_estimation'] : "";
        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$auto_id,"user_id"=>$this->user_id),array("estimated_reach"=>$reach_estimation,"last_estimaed_at"=>date("Y-m-d H:i:s")));

        echo json_encode(array('status'=>'1','message'=>$reach_estimation));
    }

    public function check_review_status()
    {
        $this->ajax_check();
        $auto_id=$this->input->post('auto_id'); // database id

        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('user_id' => $this->user_id,"id"=>$auto_id);
        $page_info = $this->basic->get_data($table_name,$where);

        $page_id=isset($page_info[0]['page_id']) ? $page_info[0]['page_id'] : "";
        $access_token=isset($page_info[0]['page_access_token']) ? $page_info[0]['page_access_token'] : "";
        if($access_token=='')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
            exit();
        }
        $this->load->library('fb_rx_login');
        $get_page_review_status=$this->fb_rx_login->get_page_review_status($access_token);

        $review_status=isset($get_page_review_status["data"][0]["status"]) ? strtoupper($get_page_review_status["data"][0]["status"]) : "NOT SUBMITTED";
        if($review_status=="") $review_status="NOT SUBMITTED";

        /*
        $existing_labels=$this->fb_rx_login->retrieve_label($access_token);
        if(isset($existing_labels['error']['message'])) $error=$this->lang->line("During the review status check process system also tries to create default unsubscribe label and retrieve the existing labels as well. We got this error : ")." ".$existing_labels["error"]["message"];

        $user_id=$this->user_id;
        $group_name="Unsubscribe";
        $group_name2="SystemInvisible01";
        
        if(isset($existing_labels["data"]))
        foreach ($existing_labels["data"] as $key => $value) 
        {
            $existng_name=$value['name'];
            $existng_id=$value['id'];

            $unsbscribed='0';
            if($existng_name==$group_name) $unsbscribed='1';

            $is_invisible='0';
            if($existng_name==$group_name2) $is_invisible='1';

            $existng_name = $this->db->escape($existng_name);

            $sql="INSERT IGNORE INTO messenger_bot_broadcast_contact_group(page_id,group_name,user_id,label_id,unsubscribe,invisible) VALUES('$auto_id',$existng_name,'$user_id','$existng_id','$unsbscribed','$is_invisible')";
            $this->basic->execute_complex_query($sql);
        }

        
        if(!$this->basic->is_exist("messenger_bot_broadcast_contact_group",array("page_id"=>$auto_id,"unsubscribe"=>"1")))
        {
            $response=$this->fb_rx_login->create_label($access_token,$group_name);
            $label_id=isset($response['id']) ? $response['id'] : "";

            $errormessage=isset($response["error"]["error_user_msg"])?$response["error"]["error_user_msg"]:$response["error"]["message"];
            
            if($label_id=="") 
            $error=$this->lang->line("During the review status check process system also tries to create default unsubscribe label and retrieve the existing labels as well. We got this error : ")." ".$errormessage;
            else $this->basic->insert_data("messenger_bot_broadcast_contact_group",array("page_id"=>$auto_id,"group_name"=>$group_name,"user_id"=>$this->user_id,"label_id"=>$label_id,"deleted"=>"0","unsubscribe"=>"1"));
        }

        if(!$this->basic->is_exist("messenger_bot_broadcast_contact_group",array("page_id"=>$auto_id,"invisible"=>"1")))
        {            
            $response=$this->fb_rx_login->create_label($access_token,$group_name2);
            $label_id=isset($response['id']) ? $response['id'] : "";

            $errormessage=isset($response["error"]["error_user_msg"])?$response["error"]["error_user_msg"]:$response["error"]["message"];
            
            if($label_id=="") 
            $error=$this->lang->line("During the review status check process system also tries to create default unsubscribe label and retrieve the existing labels as well. We got this error : ")." ".$errormessage;
            else $this->basic->insert_data("messenger_bot_broadcast_contact_group",array("page_id"=>$auto_id,"group_name"=>$group_name2,"user_id"=>$this->user_id,"label_id"=>$label_id,"deleted"=>"0","unsubscribe"=>"0","invisible"=>"1"));
        }
        */

        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$auto_id,"user_id"=>$this->user_id),array("review_status"=>$review_status,"review_status_last_checked"=>date("Y-m-d H:i:s")));

       if(isset($error)) echo json_encode(array('status'=>'0','message'=>$error));
       else echo json_encode(array('status'=>'1','message'=>$review_status));
    }      

       


    /*-------------BROADCASTING FUNCTIONS-----------*/
    /*==============================================*/
    public function subscriber_broadcast_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(211,$this->module_access))
        redirect('home/login_page', 'location');

        $data['body'] = "messenger_broadcaster/subscriber_bulk_broadcast_report";
        $data['page_title'] = $this->lang->line("Subscriber Broadcast");
        $page_list = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"bot_enabled"=>"1")),$select='',$join='',$limit='',$start=NULL,$order_by='page_name ASC');
        $page_info = [];
        foreach($page_list as $value)
        {
            $page_info[$value['id']] = $value['page_name'];
        }      
        // $page_info[''] = $this->lang->line("Page");
        $data['page_list'] = $page_info;
        $this->_viewcontroller($data);
    }
    

    public function subscriber_broadcast_campaign_data()
    { 
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(211,$this->module_access)) exit();

        $search_value = $this->input->post("search_value",TRUE);
        $page_id = $this->input->post("search_page_id",TRUE);
        $status = $this->input->post("search_status",TRUE);
        $campaign_date_range = $this->input->post("campaign_date_range",TRUE);


        $display_columns = 
        array(
          "#",
          "CHECKBOX",
          'campaign_name',
          'page_name',
          'broadcast_type',
          'posting_status',
          'actions',
          'social_media',
          'total_thread',
          'successfully_sent',
          'successfully_delivered',
          'successfully_opened',
          'schedule_time',
          'created_at',
          'label_names'
        );
        

        $search_columns = array('campaign_name','label_names','postback_id','broadcast_type');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 12;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'created_at';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom="messenger_bot_broadcast_serial.user_id = ".$this->user_id;

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }
        if($campaign_date_range!="")
        {
            $exp = explode('|', $campaign_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date = isset($exp[1])?$exp[1]:"";
            if($from_date!="Invalid date" && $to_date!="Invalid date")
            $where_custom .= " AND created_at >= '{$from_date}' AND created_at <='{$to_date}'";
        }
        $this->db->where($where_custom);

        if($page_id!="") $this->db->where(array("page_id"=>$page_id)); 
        if($status!="") $this->db->where(array("posting_status"=>$status));  
        $this->db->where(array("broadcast_type !="=>"OTN"));      
        
        $table="messenger_bot_broadcast_serial";
        $info=$this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');
        
        $this->db->where($where_custom);
        if($page_id!="") $this->db->where(array("page_id"=>$page_id)); 
        if($status!="") $this->db->where(array("posting_status"=>$status)); 
        $total_rows_array=$this->basic->count_row($table,$where='',$count=$table.".id",$join,$group_by='');

        $total_result=$total_rows_array[0]['total_rows'];

        foreach($info as $key => $value) 
        {
            $action_count = 3;
            $info[$key]['social_media'] = strtoupper($info[$key]['social_media']);

            if($info[$key]['schedule_time'] != "0000-00-00 00:00:00")
            $scheduled_at = date("M j, y H:i",strtotime($info[$key]['schedule_time']));
            else $scheduled_at = '<span class="text-muted"><i class="fas fa-exclamation-circle"></i> '.$this->lang->line("Not Scheduled")."<span>";
            $info[$key]['schedule_time'] =  "<div style='min-width:110px;'>".$scheduled_at."</div>";

            if($info[$key]['created_at'] != "0000-00-00 00:00:00")
            $info[$key]['created_at'] = "<div style='min-width:110px;'>".date("M j, y H:i",strtotime($info[$key]['created_at']))."</div>";

            $posting_status = $info[$key]['posting_status'];

            $info[$key]['page_name']="<a target='_BLANK' href='https://facebook.com/".$info[$key]['fb_page_id']."'>".$info[$key]['page_name']."</a>";

            if($posting_status=='1')
            $info[$key]['delete'] =  "<a class='btn btn-circle btn-light pointer text-muted'  data-toggle='tooltip' title='".$this->lang->line("Campaign in processing can not be deleted. You can pause campaign and then delete it.")."'><i class='fas fa-trash-alt'></i></a>";
            else  $info[$key]['delete'] =  "<a class='btn btn-circle btn-outline-danger delete'  id='".$info[$key]['id']."' data-toggle='tooltip' title='".$this->lang->line("Delete Campaign")."' href=''><i class='fas fa-trash-alt'></i></a>";
         
            $is_try_again=$info[$key]["is_try_again"];
            $force_porcess_str="";
            if($this->config->item("broadcaster_number_of_message_to_be_sent_in_try")=="" || $this->config->item("broadcaster_number_of_message_to_be_sent_in_try")=="0")
            {
                $force_porcess_str="";
            }
            else
            {
                $action_count++;
                if($posting_status=='3')$force_porcess_str .= "<a href='' class='btn btn-circle btn-outline-success play_campaign_info' data-toggle='tooltip' title='".$this->lang->line("Resume Campaign")."' table_id='".$info[$key]['id']."'><i class='fas fa-play'></i></a>";
                else if($posting_status!='4')  $force_porcess_str .= "<a href='' class='btn btn-circle btn-outline-dark pause_campaign_info' data-toggle='tooltip' title='".$this->lang->line("Pause Campaign")."' table_id='".$info[$key]['id']."'><i class='fas fa-pause'></i></a>";
            }

            if($posting_status=='1')
            {
                $action_count++;
                $force_porcess_str .= "<a href='' class='btn btn-circle btn-outline-warning force' data-toggle='tooltip' title='".$this->lang->line("Force Re-process Campaign")."' id='".$info[$key]['id']."'><i class='fas fa-sync'></i></a>";
            } 
            $info[$key]['force'] = $force_porcess_str;

            $hold_message = '<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Campaign Status : On-hold").'" data-content="'.$this->lang->line("If campaign receive more than `Subscriber Broadcast - hold after number of errors` error message during broadcast, system hold the campaign to avoid risk. The subscribers those get error, automatically marked as unavailable for future campaign to reduce error rate in future until subscriber send message to your Messenger BOT again. 
In this case, we suggest you to check the error message in report, and if you think it’s not for your message content, but for specific subscribers, you can restart the campaign from where it is left off, by clicking on the option menu & then click Force Resume. ").'"><i class="fas fa-info-circle"></i> </a>';


            if( $posting_status == '2') $info[$key]['posting_status'] = '<div style="min-width:100px"><span class="text-success badge"><i class="fas fa-check-circle"></i> '.$this->lang->line("Completed").'</span></div>';
            else if( $posting_status == '1') $info[$key]['posting_status'] = '<div style="min-width:100px"><span class="text-warning"><i class="fas fa-spinner"></i> '.$this->lang->line("Processing").'</span></div>';
            else if( $posting_status == '3') $info[$key]['posting_status'] = '<div style="min-width:100px"><span class="text-muted"><i class="fas fa-stop"></i> '.$this->lang->line("Paused").'</span></div>';
            else if( $posting_status == '4') $info[$key]['posting_status'] = '<div style="min-width:100px"><span class="text-dark"><i class="fas fa-ban"></i> '.$this->lang->line("On-hold").$hold_message.'</span></div>';
            else $info[$key]['posting_status'] = '<div style="min-width:100px"><span class="text-danger"><i class="far fa-times-circle"></i> '.$this->lang->line("Pending").'</span></div>';

            $info[$key]['posting_status'] = '<div style="min-width:80px;">'.$info[$key]['posting_status'].'</div>';

            $info[$key]['report'] =  "<a class='btn btn-circle btn-outline-primary sent_report' data-toggle='tooltip' title='".$this->lang->line("Campaign Report")."' href='' cam-id='".$info[$key]['id']."'><i class='fas fa-eye'></i></a>";

            if($posting_status!='0' || $info[$key]['schedule_type']!="later") 
            $info[$key]['edit'] =  "<a class='btn btn-circle btn-light text-muted' data-toggle='tooltip' title='".$this->lang->line("Only scheduled pending campaign can be edited.")."'><i class='fas fa-edit'></i></a>";
            else
            {                
                $edit_url = site_url('messenger_bot_enhancers/edit_subscriber_broadcast_campaign/'.$info[$key]['id']);
                $info[$key]['edit'] =  "<a class='btn btn-circle btn-outline-warning' data-toggle='tooltip' title='".$this->lang->line("Edit Campaign")."' href='".$edit_url."'><i class='fas fa-edit'></i></a>";
            }

            $action_width = ($action_count*47)+20;
            $info[$key]['actions'] ='
            <div class="dropdown d-inline dropright">
              <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-briefcase"></i>
              </button>
              <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';
                $info[$key]['actions'] .= $info[$key]['report'];
                $info[$key]['actions'] .= $info[$key]['edit'];
                $info[$key]['actions'] .= $force_porcess_str;
                $info[$key]['actions'] .= $info[$key]['delete'];
                $info[$key]['actions'] .="
              </div>
            </div>
            <script>
            $('[data-toggle=\"tooltip\"]').tooltip();
            $('[data-toggle=\"popover\"]').popover(); 
            $('[data-toggle=\"popover\"]').on(\"click\", function(e) {e.preventDefault(); return true;});
            </script>";
        }
        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
        echo json_encode($data);
    }

    public function subscriber_delete_campaign()
    {   
        $this->ajax_check();
        $this->csrf_token_check();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(211,$this->module_access)) exit();

        $id=$this->input->post("id");

        $xdata = $this->basic->get_data("messenger_bot_broadcast_serial",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $posting_status  = isset($xdata[0]["posting_status"]) ? $xdata[0]["posting_status"] : "";
        $broadcast_id  = isset($xdata[0]["broadcast_id"]) ? $xdata[0]["broadcast_id"] : "";
        $page_id  = isset($xdata[0]["page_id"]) ? $xdata[0]["page_id"] : "";
        $total_thread  = isset($xdata[0]["total_thread"]) ? $xdata[0]["total_thread"] : 0;

        if($posting_status=='1')
        {
           echo $this->lang->line("This campaign is in processing state and can not be deleted.");
           exit();
        }

        if($this->basic->delete_data("messenger_bot_broadcast_serial",array("id"=>$id,"user_id"=>$this->user_id)))
        {
            $this->basic->delete_data("messenger_bot_broadcast_serial_send",array("campaign_id"=>$id,"user_id"=>$this->user_id));
            echo "1";
        } 
        if($posting_status!="2") // removing usage data if deleted and campaign is pending
        $this->_delete_usage_log($module_id=211,$request=$total_thread);   
      
    }

    public function force_reprocess_campaign()
    {
        $this->ajax_check();
        $this->csrf_token_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(211,$this->module_access)) exit();

        $id=$this->input->post("id");

        $where = array('id'=>$id,'user_id'=>$this->user_id);
        $data = array('is_try_again'=>'1','posting_status'=>'1');
        $this->basic->update_data('messenger_bot_broadcast_serial',$where,$data);
        if($this->db->affected_rows() != 0)  echo "1";
        else  echo "0";
    }

    public function restart_campaign()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(211,$this->module_access)) exit();

        $id=$this->input->post("table_id");

        $where = array('id'=>$id,'user_id'=>$this->user_id);
        $data = array('is_try_again'=>'1','posting_status'=>'1','last_try_error_count'=>0);
        $this->basic->update_data('messenger_bot_broadcast_serial',$where,$data);
        echo '1';
    }

    public function ajax_campaign_pause()
    {
        $this->ajax_check();
        $this->csrf_token_check();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(211,$this->module_access)) exit();

        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->update_data('messenger_bot_broadcast_serial',array('id'=>$table_id),array('posting_status'=>'3','is_try_again'=>'0'));
        echo '1';
    }

    public function ajax_campaign_play()
    {
        $this->ajax_check();
        $this->csrf_token_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(211,$this->module_access)) exit();

        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->update_data('messenger_bot_broadcast_serial',array('id'=>$table_id),array('posting_status'=>'1','is_try_again'=>'1'));
        echo '1';
    }


    public function campaign_sent_status()
    {
        $this->ajax_check();
        $this->csrf_token_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(211,$this->module_access)) exit();
        
        $id = $this->input->post("id");

        $campaign_data = $this->basic->get_data("messenger_bot_broadcast_serial",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $report = isset($campaign_data[0]["report"]) ? json_decode($campaign_data[0]["report"],true) : array();
        $campaign_name  = isset($campaign_data[0]["campaign_name"]) ? $campaign_data[0]["campaign_name"] : "";
        $total_thread  = isset($campaign_data[0]["total_thread"]) ? $campaign_data[0]["total_thread"] : 0;
        $successfully_sent  = isset($campaign_data[0]["successfully_sent"]) ? $campaign_data[0]["successfully_sent"] : 0;
        $successfully_delivered  = isset($campaign_data[0]["successfully_delivered"]) ? $campaign_data[0]["successfully_delivered"] : 0;
        $successfully_opened  = isset($campaign_data[0]["successfully_opened"]) ? $campaign_data[0]["successfully_opened"] : 0;
        $successfully_clicked  = isset($campaign_data[0]["successfully_clicked"]) ? $campaign_data[0]["successfully_clicked"] : 0;
        $error_message  = isset($campaign_data[0]["error_message"]) ? $campaign_data[0]["error_message"] : "";
        $page_name  = isset($campaign_data[0]["page_name"]) ? $campaign_data[0]["page_name"] : "";
        $fb_page_id  = isset($campaign_data[0]["fb_page_id"]) ? $campaign_data[0]["fb_page_id"] : "";

        
        if($successfully_sent==0) $sent_rate=0;
        else $sent_rate=($successfully_sent/$total_thread)*100;

        if($successfully_delivered==0) $delivery_rate=0;
        else $delivery_rate=($successfully_delivered/$successfully_sent)*100;

        if($successfully_opened==0) $open_rate=0;
        else $open_rate=($successfully_opened/$successfully_sent)*100;

        if($successfully_clicked==0) $click_rate=0;
        else $click_rate=($successfully_clicked/$successfully_sent)*100;


        $sent_rate = round($sent_rate);
        $delivery_rate = round($delivery_rate);
        $open_rate = round($open_rate);
        $click_rate = round($click_rate);

        $posting_status = $campaign_data[0]['posting_status'];

        if( $posting_status == '2') $posting_status = '<span class="text-success"> ('.$this->lang->line("Completed").')</span>';
        else if( $posting_status == '1') $posting_status = '<span class="text-warning"> ('.$this->lang->line("Processing").')</span>';
        else if( $posting_status == '3') $posting_status = '<span class="text-muted"> ('.$this->lang->line("Paused").')</span>';
        else if( $posting_status == '4') $posting_status = '<span class="text-dark"> ('.$this->lang->line("On-hold").')</span>';
        else $posting_status = '<span class="text-danger"> ('.$this->lang->line("Pending").')</span>';


        $response = "";

        $drop_menu = "";
        $send_where_it_is_left_off = "";

        if($campaign_data[0]['posting_status']=='4')
        {            
        $drop_menu = '<div class="btn-group dropleft float-right"><button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$this->lang->line("Options").'  </button>  <div class="dropdown-menu dropleft"> <a class="dropdown-item has-icon restart_button pointer" title="'.$this->lang->line('If the campaign has been completed due to error but there are still some subscriber to be sent, you can resume it from it was left off by force.').'" data-toggle="tooltip" table_id="'.$id.'"><i class="fas fa-sync"></i> '.$this->lang->line("Force Resume").'</a></div> </div>';
        }

        if($error_message!="")
        $response .= "<div class='alert alert-danger text-center'> {$this->lang->line("Something went wrong for one or more message. Original error message :")} {$error_message} <br><a class='pointer' style='text-decoration:underline;' href='' data-toggle='modal' data-target='#error_message_learn'>".$this->lang->line("Learn more about common error messages")."</a></div>";


        // $response .= "<div class='row'><h6 style='width:100%;padding:0 20px'><span class='float-left'>".$campaign_name." : <a href='https://facebook.com/".$fb_page_id."'>".$page_name."</a></span> <span class='float-right'>".$posting_status."</span></h6></div>";

        $response .='
        <div class="row">
        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="card card-statistic-1">
              <div class="card-icon bg-primary">
                <i class="fas fa-info-circle"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>'. $this->lang->line("Campaign").$posting_status.'</h4>
                </div>
                <div class="card-body">
                  '.$campaign_name.'
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="card card-statistic-1">
              <div class="card-icon bg-primary">
                <i class="far fa-newspaper"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>'.$this->lang->line("Page Name").'</h4>
                </div>
                <div class="card-body">
                  <a target="_BLANK" href="https://facebook.com/'.$campaign_data[0]["fb_page_id"].'">'.$campaign_data[0]["page_name"].'</a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="card card-statistic-1">
              <div class="card-icon bg-primary">
                <i class="far fa-paper-plane"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>'.$this->lang->line("Sent").' ('.$sent_rate.'%)</h4>
                </div>
                <div class="card-body">
                  '.$successfully_sent.'/'.$total_thread.'</a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="card card-statistic-1">
              <div class="card-icon bg-primary">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>'.$this->lang->line("Delivered").' ('.$delivery_rate.'%)</h4>
                </div>
                <div class="card-body">
                  '.$successfully_delivered.'/'.$total_thread.'
                </div>
              </div>
            </div>
          </div>
          <!--<div class="col-md-4 col-12">
            <div class="card card-statistic-1">
              <div class="card-icon bg-primary">
                <i class="fas fa-eye"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>'.$this->lang->line("Opened").' ('.$open_rate.'%)</h4>
                </div>
                <div class="card-body">
                  '.$successfully_opened.'/'.$total_thread.'
                </div>
              </div>
            </div>
          </div>-->

        </div>
        <style>
        .card-statistic-1{border:.5px solid #dee2e6;border-radius: 4px;}
        .card-statistic-1 .card-icon i{font-size:40px !important;margin-top:20px;}
        </style>';       

        echo json_encode(array("response1"=>$response,"response3"=>$drop_menu));
    }

    public function campaign_sent_status_data()
    { 
        $this->ajax_check();
        $this->csrf_token_check();

        $search_value = $_POST['search']['value'];
        $id = $this->input->post("campaign_id");

        $display_columns = 
        array(
          "#",
          "CHECKBOX",
          'subscriber_name',
          'subscriber_lastname',
          'subscribe_id',
          'sent_time',
          'open_time',
          'delivery_time',
          'message_sent_id'
        );
        $search_columns = array('subscriber_name','subscriber_lastname','subscribe_id','sent_time');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 3;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'sent_time';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom="user_id = ".$this->user_id." AND campaign_id = ".$id;

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }
     
        $this->db->where($where_custom);
        
        $table="messenger_bot_broadcast_serial_send";
        $info=$this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');

        $this->db->where($where_custom);
        $total_rows_array=$this->basic->count_row($table,$where='',$count=$table.".id",$join,$group_by='');

        $total_result=$total_rows_array[0]['total_rows'];


        $i=0;
        foreach($info as $key => $value) 
        {
            $sent=$opened=$clicked=$delivered="<i class='fa fa-remove red'></i>";
            if($value["sent_time"]!="0000-00-00 00:00:00") $sent="<i class='fa fa-check-circle green'></i> ".date("M j, y H:i",strtotime($value['sent_time']));
            if($value["opened"]=="1") $opened="<i class='fa fa-check-circle green'></i> ".date("M j, y H:i",strtotime($value['open_time']));
            if($value["delivered"]=="1") $delivered="<i class='fa fa-check-circle green'></i> ".date("M j, y H:i",strtotime($value['delivery_time']));
            if($value["clicked"]=="1") $clicked="<i class='fa fa-check-circle green'></i> ".date("M j, y H:i",strtotime($value['click_time']));

            $info[$key]['sent_time'] =  $sent;
            $info[$key]['open_time'] =  $opened;
            $info[$key]['delivery_time'] =  $delivered;

            $tempu=explode(' ', $value['message_sent_id']);
            if(isset($tempu[0]) && (strlen($tempu[0])>50) || strpos($tempu[0], 'mid.$') !== false) $msg_sent_id=' <i class="fa fa-check green"></i> '.$this->lang->line("sent")." : ". $value['message_sent_id'];
            else $msg_sent_id=$value['message_sent_id'];

            if($value['message_sent_id']=="") $info[$key]["message_sent_id"]= "<i class='fa fa-remove red'></i>";
            else $info[$key]["message_sent_id"] = $msg_sent_id;

            $i++;
        }
        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
        echo json_encode($data);
    }
    

    public function create_subscriber_broadcast_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(211,$this->module_access))
        redirect('home/login_page', 'location');

        $data["templates"]=$this->basic->get_enum_values("messenger_bot_broadcast_serial","template_type");

        $data['body'] = 'messenger_broadcaster/subscriber_bulk_broadcast_add';
        $data['page_title'] = $this->lang->line('Add Subscriber Broadcast');  

        // $data['page_info'] = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"bot_enabled"=>"1")),$select='',$join='',$limit='',$start=NULL,$order_by='page_name ASC');

        $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array("facebook_rx_fb_page_info.facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),'facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name'),$join);

        $ig_page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array("facebook_rx_fb_page_info.facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),'facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1','has_instagram'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name','insta_username'),$join);

        $group_page_list = array();

        $flow_page_list = array();
        if(isset($page_info) && count($page_info) > 0) {
            $flow_page_list['media_name'] = $this->lang->line("Facebook");
            foreach($page_info as $value)
            {
                $flow_page_list['page_list'][$value['id']."-fb"] = $value['page_name']." [".$value['name']."]";
            }
            array_push($group_page_list,$flow_page_list);
        }

        $ig_flow_page_list = array();
        if(isset($ig_page_info) && count($ig_page_info) > 0) {
            $ig_flow_page_list['media_name'] = $this->lang->line("Instagram");
            foreach($ig_page_info as $ig_value)
            {
                $ig_flow_page_list['page_list'][$ig_value['id']."-ig"] = $ig_value['page_name']." [".$ig_value['insta_username']."]";
            }
            array_push($group_page_list,$ig_flow_page_list);
        }

        $data['group_page_list'] = $group_page_list;

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id)));  
        $data['postback_ids'] = $postback_id_list;

        $data['tag_list'] = $this->get_broadcast_tags();
        $data["broadcast_types"]=$this->basic->get_enum_values_assoc("messenger_bot_broadcast_serial","broadcast_type");
        unset($data['broadcast_types']['OTN']);

        $data['locale_list'] = $this->sdk_locale();
        $data["time_zone_numeric"]= $this->_time_zone_list_numeric();

        $data["time_zone"]= $this->_time_zone_list();
        $this->_viewcontroller($data); 
    }

    public function subscriber_bulk_broadcast_add_action()
    {
      
      if(function_exists('ini_set')){
          ini_set('memory_limit', '-1');
       } 

        $this->ajax_check();
        $this->csrf_token_check();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(211,$this->module_access)) exit();

        $post=$_POST;
        foreach ($post as $key => $value) 
        {            
            if(!is_array($value)) $temp = strip_tags($value);
            else $temp = $value;
            $$key=$temp;
        }

        if($broadcast_type!="Non Promo") $message_tag = "";

        $posting_status = "0";
        $successfully_sent = 0;
        $successfully_delivered = 0;
        $successfully_opened = 0;
        $successfully_clicked = 0;
        $total_thread = 0;
        $insert_data = array();
        $page_id=$this->input->post("page");// database id
        $pageid=explode("-",$page_id);
        $page_id = $pageid[0];
        $media_type = "fb";

        if(isset($pageid[1]) && $pageid[1]=="ig") {
            $media_type = "ig";
        }

        $page_table_id=$page_id;
        $insert_data['campaign_name'] = $campaign_name;
        $insert_data['page_id'] = $page_table_id;
        $insert_data['broadcast_type'] = $broadcast_type;
        $insert_data['message_tag'] = $message_tag;
        $insert_data['user_gender'] = $user_gender;
        $insert_data['user_time_zone'] = $user_time_zone;
        $insert_data['user_locale'] = $user_locale;
        $insert_data['social_media'] = $media_type;

        // domain white list section
        $messenger_bot_user_info_id = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id)));
        $page_access_token = $messenger_bot_user_info_id[0]['page_access_token'];
        $page_name = $messenger_bot_user_info_id[0]['page_name'];
        $fb_page_id=$messenger_bot_user_info_id[0]['page_id'];
        $insert_data['fb_page_id'] =  $fb_page_id;
        $messenger_bot_user_info_id = $messenger_bot_user_info_id[0]["facebook_rx_fb_user_info_id"];
        $white_listed_domain = $this->basic->get_data("messenger_bot_domain_whitelist",array("where"=>array("user_id"=>$this->user_id,"messenger_bot_user_info_id"=>$messenger_bot_user_info_id,"page_id"=>$page_table_id)),"domain");

        $white_listed_domain_array = array();
        foreach ($white_listed_domain as $value) {
            $white_listed_domain_array[] = $value['domain'];
        }
        $need_to_whitelist_array = array();

        $postback_insert_data = array();
        $reply_bot = array();
        $bot_message = array();


        for ($k=1; $k <=1 ; $k++) 
        {    
            $template_type = 'template_type_'.$k;
            $template_type = $$template_type;
            $insert_data['template_type'] = $template_type;
            $template_type = str_replace(' ', '_', $template_type);

            if($template_type == 'text')
            {
                $text_reply = 'text_reply_'.$k;
                $text_reply = isset($$text_reply) ? $$text_reply : '';
                if($text_reply != '')
                {
                    $reply_bot[$k]['text'] = $text_reply;                    
                }
            }
            if($template_type == 'image')
            {
                $image_reply_field = 'image_reply_field_'.$k;
                $image_reply_field = isset($$image_reply_field) ? $$image_reply_field : '';
                if($image_reply_field != '')
                {
                    $reply_bot[$k]['attachment']['type'] = 'image';
                    $reply_bot[$k]['attachment']['payload']['url'] = $image_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }
            if($template_type == 'audio')
            {
                $audio_reply_field = 'audio_reply_field_'.$k;
                $audio_reply_field = isset($$audio_reply_field) ? $$audio_reply_field : '';
                if($audio_reply_field != '')
                {
                    $reply_bot[$k]['attachment']['type'] = 'audio';
                    $reply_bot[$k]['attachment']['payload']['url'] = $audio_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
                
            }
            if($template_type == 'video')
            {
                $video_reply_field = 'video_reply_field_'.$k;
                $video_reply_field = isset($$video_reply_field) ? $$video_reply_field : '';
                if($video_reply_field != '')
                {
                    $reply_bot[$k]['attachment']['type'] = 'video';
                    $reply_bot[$k]['attachment']['payload']['url'] = $video_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }
            if($template_type == 'file')
            {
                $file_reply_field = 'file_reply_field_'.$k;
                $file_reply_field = isset($$file_reply_field) ? $$file_reply_field : '';
                if($file_reply_field != '')
                {                    
                    $reply_bot[$k]['attachment']['type'] = 'file';
                    $reply_bot[$k]['attachment']['payload']['url'] = $file_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
            }



        
            if($template_type == 'media')
            {
                $media_input = 'media_input_'.$k;
                $media_input = isset($$media_input) ? $$media_input : '';
                if($media_input != '')
                {
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'media';
                    $template_media_type = '';
                    if (strpos($media_input, '/videos/') !== false) {
                        $template_media_type = 'video';
                    }
                    else
                        $template_media_type = 'image';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['media_type'] = $template_media_type;
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['url'] = $media_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'media_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'media_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'media_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'media_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                     //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'media_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';

                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            if($button_extension != '' && $button_extension == 'birthday'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'compact';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }



            if($template_type == 'text_with_buttons')
            {
                $text_with_buttons_input = 'text_with_buttons_input_'.$k;
                $text_with_buttons_input = isset($$text_with_buttons_input) ? $$text_with_buttons_input : '';
                if($text_with_buttons_input != '')
                {
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'button';
                    $reply_bot[$k]['attachment']['payload']['text'] = $text_with_buttons_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'text_with_buttons_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'text_with_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'text_with_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'text_with_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'text_with_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';

                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'web_url';

                            if($button_extension != '' && $button_extension == 'birthday'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'compact';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday')
                            {
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }

            if($template_type == 'quick_reply')
            {
                $quick_reply_text = 'quick_reply_text_'.$k;
                $quick_reply_text = isset($$quick_reply_text) ? $$quick_reply_text : '';
                if($quick_reply_text != '')
                {
                    $reply_bot[$k]['text'] = $quick_reply_text;                    
                }

                for ($i=1; $i <= 11 ; $i++) 
                { 
                    $button_text = 'quick_reply_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_postback_id = 'quick_reply_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_type = 'quick_reply_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    if($button_type=='post_back')
                    {
                        if($button_text != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'text';
                            $reply_bot[$k]['quick_replies'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['quick_replies'][$i-1]['title'] = $button_text;
                        }                    
                    }
                    if($button_type=='phone_number')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_phone_number';
                    }
                    if($button_type=='user_email')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_email';
                    }
                    if($button_type=='location')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'location';
                    }

                }
            }

            if($template_type == 'generic_template')
            {
                $generic_template_title = 'generic_template_title_'.$k;
                $generic_template_title = isset($$generic_template_title) ? $$generic_template_title : '';
                $generic_template_image = 'generic_template_image_'.$k;
                $generic_template_image = isset($$generic_template_image) ? $$generic_template_image : '';
                $generic_template_subtitle = 'generic_template_subtitle_'.$k;
                $generic_template_subtitle = isset($$generic_template_subtitle) ? $$generic_template_subtitle : '';
                $generic_template_image_destination_link = 'generic_template_image_destination_link_'.$k;
                $generic_template_image_destination_link = isset($$generic_template_image_destination_link) ? $$generic_template_image_destination_link : '';

                if($generic_template_title != '')
                {
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['title'] = $generic_template_title;
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;                    
                }

                if($generic_template_subtitle != '')
                $reply_bot[$k]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;

                if($generic_template_image!="")
                {
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['image_url'] = $generic_template_image;
                    if($generic_template_image_destination_link!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['url'] = $generic_template_image_destination_link;
                    }

                    if(function_exists('getimagesize') && $generic_template_image!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($generic_template_image);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }

                }
                

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'generic_template_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'generic_template_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'generic_template_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'generic_template_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'generic_template_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';

                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            if($button_extension != '' && $button_extension == 'birthday'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'compact';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }

            if($template_type == 'carousel')
            {
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                for ($j=1; $j <=10 ; $j++) 
                {                                 
                    $carousel_image = 'carousel_image_'.$j.'_'.$k;
                    $carousel_title = 'carousel_title_'.$j.'_'.$k;

                    if(!isset($$carousel_title) || $$carousel_title == '') continue;

                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$carousel_title;
                    $carousel_subtitle = 'carousel_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$carousel_subtitle;

                    if(isset($$carousel_image) && $$carousel_image!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$carousel_image;                    
                        $carousel_image_destination_link = 'carousel_image_destination_link_'.$j.'_'.$k;
                        if($$carousel_image_destination_link!="") 
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$carousel_image_destination_link;
                        }

                        if(function_exists('getimagesize') && $$carousel_image!='') 
                        {
                            list($width, $height, $type, $attr) = getimagesize($$carousel_image);
                            if($width==$height)
                                $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                        }

                    }

                    for ($i=1; $i <= 3 ; $i++) 
                    { 
                        $button_text = 'carousel_button_text_'.$j."_".$i.'_'.$k;
                        $button_text = isset($$button_text) ? $$button_text : '';
                        $button_type = 'carousel_button_type_'.$j."_".$i.'_'.$k;
                        $button_type = isset($$button_type) ? $$button_type : '';
                        $button_postback_id = 'carousel_button_post_id_'.$j."_".$i.'_'.$k;
                        $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                        $button_web_url = 'carousel_button_web_url_'.$j."_".$i.'_'.$k;
                        $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                        //add an extra query parameter for tracking the subscriber to whom send 
                        if($button_web_url!='')
                          $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                        $button_call_us = 'carousel_button_call_us_'.$j."_".$i.'_'.$k;
                        $button_call_us = isset($$button_call_us) ? $$button_call_us : '';

                        if($button_type == 'post_back')
                        {
                            if($button_text != '' && $button_type != '' && $button_postback_id != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'postback';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_postback_id;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                            }
                        }
                        if(strpos($button_type,'web_url') !== FALSE)
                        {
                            $button_type_array = explode('_', $button_type);
                            if(isset($button_type_array[2]))
                            {
                                $button_extension = trim($button_type_array[2],'_'); 
                                array_pop($button_type_array);
                            }            
                            else $button_extension = '';
                            $button_type = implode('_', $button_type_array);

                            if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'web_url';
                                if($button_extension != '' && $button_extension == 'birthday'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'compact';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                }
                                else
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = $button_web_url;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;

                                if($button_extension != '' && $button_extension != 'birthday')
                                {
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                    // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                                }

                                if(!in_array($button_web_url, $white_listed_domain_array))
                                {
                                    $need_to_whitelist_array[] = $button_web_url;
                                }
                            }
                        }
                        if($button_type == 'phone_number')
                        {
                            if($button_text != '' && $button_type != '' && $button_call_us != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'phone_number';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_call_us;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                            }
                        }
                    }
                }
            }

            if($template_type == 'list')
            {
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'list';

                for ($j=1; $j <=4 ; $j++) 
                {                                 
                    $list_image = 'list_image_'.$j.'_'.$k;
                    if(!isset($$list_image) || $$list_image == '') continue;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$list_image;
                    $list_title = 'list_title_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$list_title;
                    $list_subtitle = 'list_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$list_subtitle;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                    $list_image_destination_link = 'list_image_destination_link_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$list_image_destination_link;
                    
                }

                $button_text = 'list_with_buttons_text_'.$k;
                $button_text = isset($$button_text) ? $$button_text : '';
                $button_type = 'list_with_button_type_'.$k;
                $button_type = isset($$button_type) ? $$button_type : '';
                $button_postback_id = 'list_with_button_post_id_'.$k;
                $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                $button_web_url = 'list_with_button_web_url_'.$k;
                $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                //add an extra query parameter for tracking the subscriber to whom send 
                if($button_web_url!='')
                  $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                $button_call_us = 'list_with_button_call_us_'.$k;
                $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                
                if($button_type == 'post_back')
                {
                    if($button_text != '' && $button_type != '' && $button_postback_id != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'postback';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_postback_id;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                    }
                }
                if(strpos($button_type,'web_url') !== FALSE)
                {
                    $button_type_array = explode('_', $button_type);
                    if(isset($button_type_array[2]))
                    {
                        $button_extension = trim($button_type_array[2],'_'); 
                        array_pop($button_type_array);
                    }            
                    else $button_extension = '';
                    $button_type = implode('_', $button_type_array);

                    if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'web_url';
                        if($button_extension != '' && $button_extension == 'birthday'){
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['messenger_extensions'] = 'true';
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['webview_height_ratio'] = 'compact';
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                        }
                        else
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['url'] = $button_web_url;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;

                        if($button_extension != '' && $button_extension != 'birthday')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['messenger_extensions'] = 'true';
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['webview_height_ratio'] = $button_extension;
                            // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                        }

                        if(!in_array($button_web_url, $white_listed_domain_array))
                        {
                            $need_to_whitelist_array[] = $button_web_url;
                        }
                    }
                }
                if($button_type == 'phone_number')
                {
                    if($button_text != '' && $button_type != '' && $button_call_us != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'phone_number';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_call_us;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                    }
                }


            }

            $bot_message['message'] = $reply_bot[$k]; 

        }
             
        // domain white list section start
        $this->load->library("fb_rx_login"); 
        $domain_whitelist_insert_data = array();
        foreach($need_to_whitelist_array as $value)
        {
            $response=$this->fb_rx_login->domain_whitelist($page_access_token,$value);
            if($response['status'] != '0')
            {
                $temp_data = array();
                $temp_data['user_id'] = $this->user_id;
                $temp_data['messenger_bot_user_info_id'] = $messenger_bot_user_info_id;
                $temp_data['page_id'] = $page_table_id;
                $temp_data['domain'] = $value;
                $temp_data['created_at'] = date("Y-m-d H:i:s");

                $domain_whitelist_insert_data[] = $temp_data;
            }
        }
        if(!empty($domain_whitelist_insert_data)) $this->db->insert_batch('messenger_bot_domain_whitelist',$domain_whitelist_insert_data);
        // domain white list section end

        $campaign_message_send=$bot_message;
        $campaign_message_send["recipient"]=array("id"=>"PUT_SUBSCRIBER_ID");

        if($broadcast_type=='Non Promo')
        {
          $campaign_message_send["messaging_type"]="MESSAGE_TAG";
          $campaign_message_send["tag"]=$message_tag;
        }
        else $campaign_message_send["messaging_type"]="RESPONSE";

        

        $insert_data['message'] = json_encode($campaign_message_send,true);
        $insert_data['user_id'] = $this->user_id;        
        // $insert_data['template_type'] = $template_type;  
        $insert_data['created_at'] = date('Y-m-d H:i:s');

        if(!isset($schedule_type) || $broadcast_type!="Non Promo") $schedule_type='now';       
        $insert_data['schedule_type'] = $schedule_type;        
        if(!isset($schedule_time) || $broadcast_type!="Non Promo") $schedule_time = "";
        if($broadcast_type!="Non Promo") $time_zone = "";

        $insert_data['schedule_time'] = $schedule_time; 
        $insert_data['page_name'] = $page_name;         
        $insert_data["posting_status"]=$posting_status;  
        $insert_data['timezone'] = $time_zone;  

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids=array();
        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids=array();

        if(!empty($label_ids)) $insert_data['label_ids'] = implode(',', $label_ids); else $insert_data['label_ids'] ="";
        if(!empty($excluded_label_ids)) $insert_data['excluded_label_ids'] = implode(',', $excluded_label_ids); else $insert_data['excluded_label_ids'] = "";

        $fb_label_names = array();
        if(!empty($label_ids))
        {
            $fb_label_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where_in"=>array("id"=>$label_ids)));
            foreach ($fb_label_data as $key => $value) 
            {
               if($value['invisible']=='0')
               $fb_label_names[]=$value["group_name"];
            }  
        }
        $insert_data['label_names'] = implode(',', $fb_label_names);

        // =========24H and 24+1 campaign=========
        $promo_sql = "";
        date_default_timezone_set('UTC');
        $current_time  = date("Y-m-d H:i:s");
        $previous_time = date("Y-m-d H:i:s",strtotime('-23 hour',strtotime($current_time)));
        if($broadcast_type=='24H Promo') $promo_sql = "last_subscriber_interaction_time > '{$previous_time}' AND";
        if($broadcast_type=='24+1 Promo') $promo_sql = "(last_subscriber_interaction_time < '{$previous_time}' AND is_24h_1_sent='0') AND";
        $this->_time_zone_set();
        //========================================

        $excluded_label_ids_temp=$excluded_label_ids;
        $unsubscribe_labeldata=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("user_id"=>$this->user_id,"page_id"=>$page_table_id,"unsubscribe"=>"1")));
        foreach ($unsubscribe_labeldata as $key => $value) 
        {
            array_push($excluded_label_ids_temp, $value["id"]);
        }


        $sql_part = $sql_part2 = '';
        if(count($label_ids)>0) $sql_part = ' messenger_bot_subscribers_label.contact_group_id IN ('.implode(',', $label_ids).') AND ';
        if(count($label_ids)>0) $sql_part2 = ' messenger_bot_subscribers_label.contact_group_id NOT IN ('.implode(',', $excluded_label_ids_temp).') AND ';

        $sql_part3="";
        $sql_part_array3 = array();
        if($user_gender!='') $sql_part_array3[] = "gender = '{$user_gender}'";
        if($user_time_zone!='') $sql_part_array3[] = "timezone = '{$user_time_zone}'";
        if($user_locale!='') $sql_part_array3[] = "locale = '{$user_locale}'";

        if(count($sql_part_array3)>0) 
        {
            $sql_part3 = implode(' AND ', $sql_part_array3);
            $sql_part3 .=" AND ";
        }


        $sql="SELECT messenger_bot_subscriber.* FROM messenger_bot_subscriber LEFT JOIN `messenger_bot_subscribers_label` ON `messenger_bot_subscribers_label`.`subscriber_table_id`=`messenger_bot_subscriber`.`id` WHERE ".$sql_part." ".$sql_part2." ".$sql_part3." ".$promo_sql." user_id = ".$this->user_id." AND unavailable = '0' AND is_bot_subscriber='1' AND page_table_id = {$page_table_id} AND social_media='".$media_type."' AND subscriber_type!='system'";
        $lead_list=$this->basic->execute_query($sql);

        $report = array();
        $subscriber_auto_ids = [];   
        foreach ($lead_list as $key => $value)
        {          
           // $temp=explode(',', $value['contact_group_id']);
           // if(count($temp) > count($excluded_label_ids_temp))
           // $result=array_intersect($temp, $excluded_label_ids_temp);
           // else $result=array_intersect($excluded_label_ids_temp, $temp);
           // if(count($result)>0) continue;

           $total_thread++;

           $report[$value['subscribe_id']] = array
           (
                "subscribe_id"=>$value["subscribe_id"],
                "subscriber_auto_id"=>$value["id"],               
                "subscriber_name"=>$value["first_name"],
                "subscriber_lastname"=>$value["last_name"],
                "sent"=>"0",
                "sent_time"=>"",
                "delivered"=>"0",
                "delivery_time"=>"",
                "opened"=>"0",
                "open_time"=>"",
                "clicked"=>"0",
                "click_time"=>"",
                "click_ref"=>"",
                "message_sent_id"=>""
            );
            $subscriber_auto_ids[] = $value["id"];
        }

        if($total_thread==0)
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Campaign could not target any subscriber to reach message. Please try again with different targeting options.")));
            exit();
        }

        // 24+1 inactivation becuase he is already sending a promo message
        if($broadcast_type!='24H Promo' && !empty($subscriber_auto_ids))
        {
          $sql_24h="UPDATE messenger_bot_subscriber SET is_24h_1_sent='1' WHERE id IN (".implode(',', $subscriber_auto_ids).")";
          $this->basic->execute_complex_query($sql_24h);
        }
        // ===============================================================

        $status=$this->_check_usage($module_id=211,$request=$total_thread);
        if($status=="2")  //monthly limit is exceeded, can not send another ,message this month
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Sorry, your bulk to send subscriber message is exceeded.")));
            exit();
        }
        else if($status=="3")  //monthly limit is exceeded, can not send another ,message this month
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Sorry, your monthly limit to send subscriber message is exceeded.")));
            exit();
        }

        $insert_data["total_thread"]=$total_thread;
        // $insert_data["report"]=json_encode($report);

        if($this->basic->insert_data('messenger_bot_broadcast_serial',$insert_data))
        {
            $campaign_id= $this->db->insert_id();
            $this->_insert_usage_log($module_id=211,$request=$total_thread);

            $report_insert=array();
            foreach($report as $key2=>$value2) 
            {               
                $client_thread_id_send = $key2;
                $report_insert[]=array
                (
                    "campaign_id"=>$campaign_id,   
                    "user_id"=>$this->user_id,   
                    "page_id"=>$page_id,   
                    "subscribe_id"=>$value2["subscribe_id"],   
                    "subscriber_auto_id"=>$value2["subscriber_auto_id"],
                    "subscriber_name"=>$value2['subscriber_name'],
                    "subscriber_lastname"=>$value2['subscriber_lastname']
                );
            }
            $this->db->insert_batch('messenger_bot_broadcast_serial_send', $report_insert); // strong the leads to send message in database

            $this->session->set_flashdata('broadcast_success',1);
            echo json_encode(array("status" => "1"));            
        }
        
    }

    public function edit_subscriber_broadcast_campaign($id=0)
    {  
        if($this->session->userdata('user_type') != 'Admin' && !in_array(211,$this->module_access))
        redirect('home/login_page', 'location');

        // $data["templates"]=$this->basic->get_enum_values("messenger_bot_broadcast_serial","template_type");
        $template_types=$this->basic->get_enum_values("messenger_bot_broadcast_serial","template_type");

        $data['body'] = 'messenger_broadcaster/subscriber_bulk_broadcast_edit';
        $data['page_title'] = $this->lang->line('Edit Subscriber Broadcast');  

        // $data['page_info'] = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"bot_enabled"=>"1")),$select='',$join='',$limit='',$start=NULL,$order_by='page_name ASC');
        $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array("facebook_rx_fb_page_info.facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),'facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name'),$join);

        $ig_page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array("facebook_rx_fb_page_info.facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),'facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1','has_instagram'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name','insta_username'),$join);

        $group_page_list = array();

        $flow_page_list = array();
        if(isset($page_info) && count($page_info) > 0) {
            $flow_page_list['media_name'] = $this->lang->line("Facebook");
            foreach($page_info as $value)
            {
                $flow_page_list['page_list'][$value['id']."-fb"] = $value['page_name']." [".$value['name']."]";
            }
            array_push($group_page_list,$flow_page_list);
        }

        $ig_flow_page_list = array();
        if(isset($ig_page_info) && count($ig_page_info) > 0) {
            $ig_flow_page_list['media_name'] = $this->lang->line("Instagram");
            foreach($ig_page_info as $ig_value)
            {
                $ig_flow_page_list['page_list'][$ig_value['id']."-ig"] = $ig_value['page_name']." [".$ig_value['insta_username']."]";
            }
            array_push($group_page_list,$ig_flow_page_list);
        }

        $data['group_page_list'] = $group_page_list;

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id)));  
        $data['postback_ids'] = $postback_id_list;

        $data["time_zone"]= $this->_time_zone_list();

        $xdata=$this->basic->get_data("messenger_bot_broadcast_serial",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        if(!isset($xdata[0])) exit();
        if($xdata[0]['posting_status']!='0') exit();
        $data['xdata']=$xdata[0];

        $page_id=$xdata[0]['page_id'];// database id      
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1",'template_for'=>"reply_message")),'','','',$start=NULL,$order_by='template_name ASC');        
        $poption=array();
        foreach ($postback_data as $key => $value) 
        {            
            $poption[$value["postback_id"]]=$value['template_name'].' ['.$value['postback_id'].']';
        }
        $data['poption']=$poption;

        $data['tag_list'] = $this->get_broadcast_tags();
        $data["broadcast_types"]=$this->basic->get_enum_values_assoc("messenger_bot_broadcast_serial","broadcast_type");
        unset($data['broadcast_types']['OTN']);

        $data['locale_list'] = $this->sdk_locale();
        $data["time_zone_numeric"]= $this->_time_zone_list_numeric();


        $template_types = array_diff($template_types,['list']);
        if($xdata[0]['social_media'] == 'ig') {
            $need_to_remove = ['audio','video','file','text with buttons','media'];
            foreach ($need_to_remove as $value) {
                if (($key = array_search($value, $template_types)) !== false) {
                    unset($template_types[$key]);
                }
            }
        }
        // echo "<pre>"; print_r($template_types); exit;

        $data['templates'] = $template_types;
    
        $this->_viewcontroller($data); 
    }

    public function subscriber_bulk_broadcast_edit_action()
    {
      
        if(function_exists('ini_set')){
            ini_set('memory_limit', '-1');
         } 


        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(211,$this->module_access)) exit();

        $xid=$this->input->post("xid");

        $xdata = $this->basic->get_data("messenger_bot_broadcast_serial",array("where"=>array("id"=>$xid,"user_id"=>$this->user_id)));
        if(!isset($xdata[0])) exit();
        $total_thread  = isset($xdata[0]["total_thread"]) ? $xdata[0]["total_thread"] : 0;
        $posting_status  = isset($xdata[0]["posting_status"]) ? $xdata[0]["posting_status"] : "";
        $schedule_type  = isset($xdata[0]["schedule_type"]) ? $xdata[0]["schedule_type"] : "now";
        if($posting_status!='0') exit();
        if($schedule_type!='later') exit();

        $this->db->trans_start();
        $this->basic->delete_data("messenger_bot_broadcast_serial",array("id"=>$xid,"user_id"=>$this->user_id));
        $this->basic->delete_data("messenger_bot_broadcast_serial_send",array("campaign_id"=>$xid,"user_id"=>$this->user_id));
        $this->_delete_usage_log(211,$total_thread);
        $this->db->trans_complete();
        if($this->db->trans_status() === false) 
        echo json_encode(array('status'=>'0','message'=>$this->lang->line('Something went wrong, please try again.')));
        else 
        {
            echo json_encode(array('status'=>'1','message'=>$this->lang->line('Campaign has been updated successfully.')));
            $this->session->set_flashdata('broadcast_success',1);
        }
    }
    /*-------------BROADCASTING FUNCTIONS-----------*/
    /*==============================================*/






    /*-------------DRIP MESSAGING FUNCTIONS-----------*/
    /*================================================*/
    public function get_postback()
    {
        $this->ajax_check();
        $page_id=$this->input->post('page_id');// database id      

        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,'is_template'=>'1','template_for'=>"reply_message")),'','','',$start=NULL,$order_by='id DESC');
        $push_postback="";
        foreach ($postback_data as $key => $value) 
        {
            $push_postback.="<option value='".$value['postback_id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        echo $push_postback;   
    }

    public function get_postback_sequence($for_hour='0')
    {
        $this->ajax_check();

        $page_id=$this->input->post('page_id');// database id    
        $push_id=$this->input->post('push_id');
        $media_type=$this->input->post('media_type');
        if($media_type=='') $media_type=='fb';

        if($for_hour=='1')
        {
          $template_id_str="hour_template_id";
        }
        else $template_id_str="template_id";
       
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,'is_template'=>'1','template_for'=>"reply_message","media_type"=>$media_type)),'','','',$start=NULL,$order_by='id DESC');
        $push_postback='<select name="'.$template_id_str.$push_id.'" class="form-control '.$template_id_str.'" id="'.$template_id_str.$push_id.'">';
        $push_postback.="<option value=''>"."--- ".$this->lang->line("Do not send message")." ---"."</option>";
        foreach ($postback_data as $key => $value) 
        {
            $push_postback.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        $push_postback.='</select><script>$("#'.$template_id_str.$push_id.'").select2();</script>';
        echo $push_postback;   
    }

    public function get_campaign_report()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(219,$this->module_access)) exit();
        $id = $this->input->post("campaign_id");
        $is_day = $this->input->post("is_day");
        $media_type = $this->input->post("media_type");

        $select = array("messenger_bot_drip_report.*","messenger_bot_drip_campaign.message_content","messenger_bot_drip_campaign.message_content_hourly","messenger_bot_drip_campaign.campaign_name");
        $join = array('messenger_bot_drip_campaign'=>"messenger_bot_drip_campaign.id=messenger_bot_drip_report.messenger_bot_drip_campaign_id,left");
        $where = array("where"=>array("messenger_bot_drip_campaign_id"=>$id,"messenger_bot_drip_report.user_id"=>$this->user_id,"messenger_bot_drip_campaign.media_type"=>$media_type));
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

        if($is_day=='1')
        $message_content = isset($report_data[0]["message_content"]) ? json_decode($report_data[0]["message_content"],true) : array();
        else $message_content = isset($report_data[0]["message_content_hourly"]) ? json_decode($report_data[0]["message_content_hourly"],true) : array();

        $campaign_name  = isset($report_data[0]["campaign_name"]) ? $report_data[0]["campaign_name"] : "";
        $template_ids=array_values($message_content);
        $template_ids=array_unique($template_ids);
        $template_data = array();
        if(!empty($template_ids))
        $template_data=$this->basic->get_data("messenger_bot_postback",array("where_in"=>array("id"=>$template_ids)));
        $template_data_formatted=array();
        foreach ($template_data as $key => $value) 
        {
            $template_data_formatted[$value['id']]['id']=$value['id'];
            $template_data_formatted[$value['id']]['name']=$value['template_name'];
            $template_data_formatted[$value['id']]['link']= base_url('/messenger_bot/edit_template/'.$value['id'].'/1/0/'.$media_type);
        }
        
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
              if($key==30)
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
                             echo "<th class='text-center' nowrap>";
                                echo $this->lang->line("Delivery");
                            echo "</th>"; 
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
                        else if($value2['is_delivered']=='1') $value2['status'] = "<span class='badge badge-status'><i class='fa fa-check-circle text-success'></i> ".$this->lang->line('Delivered')."</span>";
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
                            echo "<td align='center' nowrap>".$value2["delivered_at"]."</td>";
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

    public function sequence_message_campaign($page_auto_id=0,$iframe='0',$media_type='fb')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(219,$this->module_access))
        redirect('home/login_page', 'location');

        if($page_auto_id==0) exit();
        $this->is_engagement_exist=$this->engagement_exist();
        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('bot_enabled' => "1","facebook_rx_fb_page_info.id"=>$page_auto_id,"facebook_rx_fb_page_info.user_id"=>$this->user_id);
        $join = array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left");   
        $page_info = $this->basic->get_data($table_name,$where,array("facebook_rx_fb_page_info.*","facebook_rx_fb_user_info.name as account_name","facebook_rx_fb_user_info.fb_id"),$join);

        if(!isset($page_info[0])) exit();
        
        $data['body'] = 'messenger_sequence/campaign_list';
        $data['page_title'] = $this->lang->line('Sequence Message');  
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array();        
        $data["page_auto_id"]=$page_auto_id;
        $data["drip_types"]=$this->get_drip_type();
        $data['bot_settings'] = $this->basic->get_data("messenger_bot_drip_campaign",array("where"=>array("page_id"=>$page_auto_id,"user_id"=>$this->user_id,'campaign_type'=>'messenger','media_type'=>$media_type)),$select='',$join='',$limit='',$start=NULL,$order_by='created_at DESC');
        // echo "<pre>"; print_r($data['bot_settings']); exit;
        $data["template_list"]=$this->get_page_template($page_auto_id,$media_type);
        $data["how_many_days"]= $media_type=='fb' ? 30 : 7;
        $data["how_many_hours"]=23;
        $data["default_display"]=3;
        $data["default_display_hour"]=3;
        $data['timezones']=$this->_time_zone_list();
        $data['tag_list'] = $this->get_broadcast_tags($media_type);

        if($this->addon_exist("visual_flow_builder"))
            $data['visual_flow_builder_exist'] = 'yes';
        else
            $data['visual_flow_builder_exist'] = 'no';
    
        $data['iframe']=$iframe;
        $data['media_type']=$media_type;
        $this->_viewcontroller($data); 
    }

    public function create_sequence_campaign($page_auto_id=0,$iframe='0',$media_type='fb')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(219,$this->module_access))
        redirect('home/login_page', 'location');

        if($page_auto_id==0) exit();
        $this->is_engagement_exist=$this->engagement_exist();
        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('bot_enabled' => "1","facebook_rx_fb_page_info.id"=>$page_auto_id,"facebook_rx_fb_page_info.user_id"=>$this->user_id);
        $join = array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left");   
        $page_info = $this->basic->get_data($table_name,$where,array("facebook_rx_fb_page_info.*","facebook_rx_fb_user_info.name as account_name","facebook_rx_fb_user_info.fb_id"),$join);

        if(!isset($page_info[0])) exit();
        
        $data['body'] = 'messenger_sequence/create_campaign';
        $data['page_title'] = $this->lang->line('Sequence Message');  
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array();        
        $data["page_auto_id"]=$page_auto_id;
        $data["drip_types"]=$this->get_drip_type();
        $data['bot_settings'] = $this->basic->get_data("messenger_bot_drip_campaign",array("where"=>array("page_id"=>$page_auto_id,"user_id"=>$this->user_id,'campaign_type'=>'messenger','media_type'=>$media_type)),$select='',$join='',$limit='',$start=NULL,$order_by='created_at DESC');
        // echo "<pre>"; print_r($data['bot_settings']); exit;
        $data["template_list"]=$this->get_page_template($page_auto_id,$media_type);
        $data["how_many_days"]= $media_type=='fb' ? 30 : 7;
        $data["how_many_hours"]=23;
        $data["default_display"]=3;
        $data["default_display_hour"]=3;
        $data['timezones']=$this->_time_zone_list();
        $data['tag_list'] = $this->get_broadcast_tags($media_type);

        if($this->addon_exist("visual_flow_builder"))
            $data['visual_flow_builder_exist'] = 'yes';
        else
            $data['visual_flow_builder_exist'] = 'no';
        
        $data['iframe']=$iframe;
        $data['media_type']=$media_type;
        $this->_viewcontroller($data); 
    }

    public function create_sequence_campaign_action()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(219,$this->module_access)) exit();
        $this->is_engagement_exist=$this->engagement_exist();
        $post=$_POST;

        foreach ($post as $key => $value) 
        {
            // $$key=$this->input->post($key,true);
            if(!is_array($value)) $temp = strip_tags($value);
            else $temp = $value;
            $$key=$temp;
        }

        if(!isset($drip_type) || $drip_type=='') $drip_type='default';

     
        $status=$this->_check_usage($module_id=219,$request=1);
        if($status=="3") 
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("You can not create more sequence message campaign. Module limit exceeded.")));
            exit();
        }
       

        // every page must have an default campaign
        // if($drip_type!='default' && !$this->basic->is_exist("messenger_bot_drip_campaign",array("page_id"=>$page_id,"user_id"=>$this->user_id,"drip_type"=>"default")))
        // {            
        //     echo json_encode(array("status" => "0", "message" =>$this->lang->line("You must first create a default type campaign for the page.")));
        //     exit();        
        // }

        // if default campaign exists and trying to create again, prevent it
        if($drip_type=='default' && $this->basic->is_exist("messenger_bot_drip_campaign",array("page_id"=>$page_id,"user_id"=>$this->user_id,"drip_type"=>"default","media_type"=>$media_type)))
        {            
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("Default type campaign has been already created for this page.")));
            exit();        
        }

        // can not duplicate enagement re-targeting
        if($drip_type!='default' && $drip_type!='custom' && $this->basic->is_exist("messenger_bot_drip_campaign",array("page_id"=>$page_id,"user_id"=>$this->user_id,"drip_type"=>$drip_type,"engagement_table_id"=>$engagement_table_id,"media_type"=>$media_type)))
        {            
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("This messenger engagement re-targeting has been already used.")));
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
           $minutes = $i*60;
           $displayname = $i." ".$this->lang->line('Hour');
           if($i==0) $minutes = 30;           

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
            "drip_type"=>$drip_type,
            "between_start"=>$between_start,
            "between_end"=>$between_end,
            "timezone"=>$timezone,
            "message_tag"=>$message_tag,
            "media_type"=>$media_type
        );
        if($drip_type!='default' && $drip_type!='custom') $insert_data['engagement_table_id']=$engagement_table_id;

        $this->db->trans_start();
        $this->basic->insert_data("messenger_bot_drip_campaign",$insert_data);
        $this->_insert_usage_log($module_id=219,$request=1);     
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


    public function edit_sequence_message_campaign($id=0,$page_auto_id=0,$iframe='0',$media_type='fb')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(219,$this->module_access))
        redirect('home/login_page', 'location');

        if($page_auto_id==0) exit();
        $this->is_engagement_exist=$this->engagement_exist();
        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('bot_enabled' => "1","facebook_rx_fb_page_info.id"=>$page_auto_id);
        $join = array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left");   
        $page_info = $this->basic->get_data($table_name,$where,array("facebook_rx_fb_page_info.*","facebook_rx_fb_user_info.name as account_name","facebook_rx_fb_user_info.fb_id"),$join);
        if(!isset($page_info[0])) exit();

        
        $data['body'] = 'messenger_sequence/edit_campaign';
        $data['page_title'] = $this->lang->line('Edit Sequence Message');  
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array();        
        $data["page_auto_id"]=$page_auto_id;
        $data["drip_types"]=$this->get_drip_type();
        $xdata = $this->basic->get_data("messenger_bot_drip_campaign",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $data['xdata']=isset($xdata[0])?$xdata[0]:array();
        $data["template_list"]=$this->get_page_template($page_auto_id,$media_type);
        
        $data["how_many_days"]= $media_type=='fb' ? 30 : 7;
        $data["how_many_hours"]=23;

        $message_content=isset($xdata[0]['message_content'])?json_decode($xdata[0]['message_content'],true):array();
        $default_display = (!empty($message_content)) ? max(array_keys($message_content)) : 3;
        $data["default_display"]=$default_display;
        
        $message_content_hourly=isset($xdata[0]['message_content_hourly'])?json_decode($xdata[0]['message_content_hourly'],true):array();
        if(!empty($message_content_hourly))
        {
          $default_display_hour = max(array_keys($message_content_hourly));
          if($default_display_hour==30) $default_display_hour=1;
          else $default_display_hour = ($default_display_hour/60)+1;
        }
        else  $default_display_hour = 3;
        $data["default_display_hour"]=$default_display_hour;

        $data['timezones']=$this->_time_zone_list();
        $data['tag_list'] = $this->get_broadcast_tags($media_type);

        $data['media_type']=$media_type;
        $data2=$data;
        if(!isset($iframe)) $iframe = '0';
        $data2['iframe']=$iframe;
        
        if($iframe=='1') $this->_viewcontroller($data2);
        else $this->_viewcontroller($data);
    }

    public function edit_sequence_message_campaign_action()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(219,$this->module_access)) exit();
        $this->is_engagement_exist=$this->engagement_exist();
        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            if(!is_array($value)) $temp = strip_tags($value);
            else $temp = $value;
            $$key=$temp;
        }

        if(!isset($drip_type) || $drip_type=='') $drip_type='default';

        $xdata=$this->basic->get_data("messenger_bot_drip_campaign",array("where"=>array("id"=>$campaign_id,"user_id"=>$this->user_id)));
        $xdrip_type=isset($xdata[0]['drip_type'])?$xdata[0]['drip_type']:'default';
        $xengagement_table_id=isset($xdata[0]['engagement_table_id'])?$xdata[0]['engagement_table_id']:'';

        // I dont allow to switch drip type if default :p
        // if($drip_type!='default' && $xdrip_type=='default')
        // {
        //     echo json_encode(array("status" => "0", "message" =>$this->lang->line("Drip type can not be edited to others from default type.")));
        //     exit();   
        // }

        // if default campaign exists and trying to create again, prevent it
        if($drip_type=='default' && $xdrip_type!='default' && $this->basic->is_exist("messenger_bot_drip_campaign",array("page_id"=>$page_id,"user_id"=>$this->user_id,"drip_type"=>"default")))
        {            
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("Default type campaign has been already created for this page.")));
            exit();        
        }

        // can not duplicate enagement re-targeting
        if($drip_type!='default' && $drip_type!='custom' && $engagement_table_id!=$xengagement_table_id && $drip_type!=$xdrip_type && $this->basic->is_exist("messenger_bot_drip_campaign",array("page_id"=>$page_id,"user_id"=>$this->user_id,"drip_type"=>$drip_type,"engagement_table_id"=>$engagement_table_id)))
        {            
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("This messenger engagement re-targeting has been already used.")));
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
           $minutes = $i*60;
           $displayname = $i." ".$this->lang->line('Hour');
           if($i==0) $minutes = 30;           

           $temp="hour_template_id".$i;
           if($$temp!="") $message_content_hourly[$minutes]=$$temp;
        }
        $message_content_hourly=json_encode($message_content_hourly);

        $insert_data=array
        (
            "campaign_name"=>$campaign_name,
            "message_content"=>$message_content,            
            "message_content_hourly"=>$message_content_hourly,
            "drip_type"=>$drip_type,
            "between_start"=>$between_start,
            "between_end"=>$between_end,
            "timezone"=>$timezone,
            "message_tag"=>$message_tag,
            "media_type"=>$media_type
        );
        if($drip_type!='default' && $drip_type!='custom') $insert_data['engagement_table_id']=$engagement_table_id;

        $this->basic->update_data("messenger_bot_drip_campaign",array("id"=>$campaign_id,"user_id"=>$this->user_id),$insert_data);
        echo json_encode(array("status" => "1", "message" =>$this->lang->line('Campaign has been updated successfully.')));          
    }


    public function delete_sequecne_campaign()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(219,$this->module_access)) exit();

        $id=$this->input->post("id");        
        $page_auto_id=$this->input->post("page_auto_id");        
        $this->db->trans_start();

        $this->basic->delete_data("messenger_bot_drip_campaign",array("id"=>$id,"user_id"=>$this->user_id));
        $this->basic->delete_data("messenger_bot_drip_campaign_assign",array("messenger_bot_drip_campaign_id"=>$id,"user_id"=>$this->user_id));
        $this->basic->delete_data("messenger_bot_drip_report",array("messenger_bot_drip_campaign_id"=>$id,"user_id"=>$this->user_id));       

        $this->db->trans_complete();
        if($this->db->trans_status() === false) echo '0';
        else
        {
            $this->_delete_usage_log(219,1);
            echo '1';
        }
    }


    public function page_messaging_report($page_id=0)
    {
        $this->session->set_userdata('drip_messaging_report_page_id', $page_id);
        redirect('drip_messaging/messaging_report','refresh');
    }

    public function messaging_report()
    {
        $data['body'] = "messaging_report";
        $data['page_title'] = $this->lang->line("Message Sent Log");
        $page_info = $this->db->query("SELECT page_id,page_name,id FROM `facebook_rx_fb_page_info` WHERE bot_enabled='1' AND user_id = '".$this->user_id."'")->result_array();
        $data['page_info'] = $page_info;
        $data["drip_types"]=$this->get_drip_type();
        $this->_viewcontroller($data);
    }

    public function messaging_report_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'last_updated_at';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $campaign_name = trim($this->input->post("search_campaign_name"));
        $drip_type = trim($this->input->post("search_drip_type", true));
        $page_id = trim($this->input->post("search_page", true));
        $is_searched = $this->input->post('is_searched', true);

        if($is_searched)
        {
            $this->session->set_userdata('drip_messaging_report_campaign_name', $campaign_name);
            $this->session->set_userdata('drip_messaging_report_drip_type', $drip_type);
            $this->session->set_userdata('drip_messaging_report_page_id', $page_id);
        }

        $search_campaign_name  = $this->session->userdata('drip_messaging_report_campaign_name');
        $search_drip_type  = $this->session->userdata('drip_messaging_report_drip_type');
        $search_page_id  = $this->session->userdata('drip_messaging_report_page_id');

        $where_simple=array();

        if ($search_campaign_name) $where_simple['campaign_name like ']    = "%".$search_campaign_name."%";
        if ($search_drip_type) $where_simple['drip_type']    = $search_drip_type;
        if ($search_page_id) $where_simple['messenger_bot_drip_report.page_id'] = $search_page_id;

        $where_simple['messenger_bot_drip_report.user_id'] = $this->user_id;
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $where = array('where' => $where_simple);

        $table = "messenger_bot_drip_report";
        $select="messenger_bot_drip_report.*,campaign_name,message_content,drip_type,facebook_rx_fb_page_info.page_name,facebook_rx_fb_page_info.page_id as fb_page_id";
        $join=array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot_drip_report.page_id,left",'messenger_bot_drip_campaign'=>"messenger_bot_drip_campaign.id=messenger_bot_drip_report.messenger_bot_drip_campaign_id,left");
        $info = $this->basic->get_data($table,$where,$select,$join,$limit=$rows, $start=$offset,$order_by=$order_by_str);
        
        for($i=0;$i<count($info);$i++) 
        {
            $info[$i]['campaign_details']="<a target='_BLANK' class='btn btn-outline-info' href='".base_url("drip_messaging/edit_campaign/".$info[$i]["messenger_bot_drip_campaign_id"]."/".$info[$i]["page_id"])."'><i class='fa fa-list-alt'></i> ".$this->lang->line("details")."</a>";
            $info[$i]['subscriber']=$info[$i]['first_name']." ".$info[$i]['last_name'];
            
            if($info[$i]['is_opened']=='1') $info[$i]['status'] = "<span class='label label-light'><i class='fa fa-eye blue'></i> ".$this->lang->line('opened')."</span>";
            else if($info[$i]['is_delivered']=='1') $info[$i]['status'] = "<span class='label label-light'><i class='fa fa-check-circle green'></i> ".$this->lang->line('delivered')."</span>";
            else $info[$i]['status'] = "<span class='label label-light'><i class='fa fa-send orange'></i> ".$this->lang->line('sent')."</span>";
            
            if($info[$i]['last_completed_day']==0) $info[$i]['last_completed_day']='x';
            else $info[$i]['last_completed_day']=$this->lang->line("day")."-".$info[$i]['last_completed_day'];

            if($info[$i]['sent_at']!='0000-00-00 00:00:00') $info[$i]['sent_at']=date("jS M, y H:i:s",strtotime($info[$i]['sent_at']));
            else $info[$i]['sent_at']='x';

            if($info[$i]['delivered_at']!='0000-00-00 00:00:00') $info[$i]['delivered_at']=date("jS M, y H:i:s",strtotime($info[$i]['delivered_at']));
            else $info[$i]['delivered_at']='x';

            if($info[$i]['last_updated_at']!='0000-00-00 00:00:00') $info[$i]['last_updated_at']=date("jS M, y H:i:s",strtotime($info[$i]['last_updated_at']));
            else $info[$i]['last_updated_at']='x';

            if($info[$i]['opened_at']!='0000-00-00 00:00:00') $info[$i]['opened_at']=date("jS M, y H:i:s",strtotime($info[$i]['opened_at']));
            else $info[$i]['opened_at']='x';

            $info[$i]['page_name']="<a target='_BLANK' href='https://facebook.com/".$info[$i]['fb_page_id']."'>".$info[$i]['page_name']."</a>";

        }

        $total_rows_array = $this->basic->count_row($table, $where, $count = "messenger_bot_drip_report.id",$join);
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }

    

    private function get_page_template($page_id=0,$media_type='fb')
    {
        if($page_id==0) return array();  

        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1","template_for"=>"reply_message","media_type"=>$media_type)),'','','',$start=NULL,$order_by="template_name ASC");
        $push_postback=array();
        foreach ($postback_data as $key => $value) 
        {
            $push_postback[$value['id']]=$value['template_name'].' ['.$value['postback_id'].']';
        }
        return $push_postback;
    }
  

    public function get_engagement_list()
    {
        $this->ajax_check();
        $page_id=$this->input->post('page_auto_id');// database id    
        $table_name=$this->input->post('table_name');        
        $engagement_id=$this->input->post('engagement_id');  // provided when edit     

        $page_id_field='page_id';
        if($table_name=="messenger_bot_engagement_2way_chat_plugin") $page_id_field='page_auto_id';
        $getdata=$this->basic->get_data($table_name,array("where"=>array($page_id_field=>$page_id,"user_id"=>$this->user_id)),$select='',$join='',$limit='',$start=NULL,$order_by='id desc');

        $langurl = base_url('assets/modules/datatables/language/'.$this->language.'.json');
        
        echo '<script>
        var table2 = $("#engagement_list_data_table").DataTable({
            language: 
            {
              url: "'.$langurl.'"
            },
            dom: \'<"top"f>rt<"bottom"lip><"clear">\'
        });
        $("div:not(.data-card) > .table-responsive").niceScroll(); 
        </script>';

        if($table_name=="messenger_bot_engagement_checkbox")
        echo '<div class="well text-justify" style="border:1px solid var(--blue);padding:15px;color:var(--blue);">'.$this->lang->line("Sequence message campaign will be assigned for checkbox plugin only after replying back of the message sent for checkbox OPTIN.").'</div><br>';

        echo "
        <div class='table-responsive data-card'><table class='table table-hover table-bordered table-sm' id='engagement_list_data_table'>";
          echo "<thead>";
            echo "<tr>";
              echo "<th class='text-center'>".$this->lang->line("SL")."</th>";
              echo "<th class='text-center'>".$this->lang->line("Select")."</th>";
              if(isset($getdata[0]['domain_name']))
              echo "<th>".$this->lang->line("Domain")."</th>";
              echo "<th>".$this->lang->line("Reference")."</th>";
              echo "<th class='text-center'>".$this->lang->line("Created at")."</th>";
            echo "</tr>";
          echo "</thead>";

          echo "<tbody>";
            $i=0;
            foreach ($getdata as $key => $value) 
            {
              $i++;
              if(isset($value['created_at'])) $created_at=date('d M y H:i',strtotime($value['created_at']));
              else $created_at=date('d M y - H:i:s',strtotime($value['add_date']));

              $checked='';
              if($engagement_id!="" && $engagement_id!="0")
              {
                if($value["id"]==$engagement_id) 
                $checked='checked'; 
              }
              else 
              {
                if($i==1) 
                $checked='checked'; 
              }
            
              $radio ='
              <div class="custom-control custom-checkbox">
                  <input type="radio" class="custom-control-input" value="'.$value["id"].'" id="engagement_table_id'.$i.'" name="engagement_table_id" '.$checked.'>
                  <label class="custom-control-label" for="engagement_table_id'.$i.'">&nbsp;</label>
              </div>';
             
              echo "<tr>";
                echo "<td class='text-center' style='vertical-align:middle;'>".$i."</td>";
                echo "<td class='text-center' style='vertical-align:middle;'>".$radio."</td>";
                if(isset($getdata[0]['domain_name']))
                echo "<td><a target='_BLANK' href='".$value['domain_name']."'>".$value['domain_name']."</a></td>";
                echo "<td>".$value['reference']."</td>";
                echo "<td class='text-center'>".$created_at."</td>";
              echo "</tr>";
            }
          echo "</tbody>";
        echo "</table></div>";
        
    }
    /*-------------DRIP MESSAGING FUNCTIONS-----------*/
    /*================================================*/








    /*---------------ENGAGEMENT FUNCTIONS-------------*/
    /*================================================*/
    /* http://example.com/messenger_bot_enhancers/messenger_checkbox_plugin.js?code=54848488 */
    public function messenger_checkbox_plugin() // echo js getting domain code
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/javascript');
        $code=$this->input->get('code');
        if($code=="") 
        {
            echo "console.error('".$this->config->item('product_name')." Error : Facebook messenger checkbox plugin is failed to load, no domain code found.');";
            exit();
        }

        $plugin_data=$this->basic->get_data("messenger_bot_engagement_checkbox",array("where"=>array("domain_code"=>$code)));
        if(!isset($plugin_data[0])) 
        {
            echo "console.error('".$this->config->item('product_name')." Error : Facebook messenger checkbox plugin is failed to load, invalid domain code.');";
            exit();
        }

        $user_id=$plugin_data[0]["user_id"];
        $user_data=$this->basic->get_data("users",array("where"=>array("id"=>$user_id,"status"=>"1")));
        if(!isset($user_data[0])) 
        {
            echo "console.error('".$this->config->item('product_name')." Error : Facebook messenger checkbox plugin is failed to load, the requesting user is no longer valid.');";
            exit();
        }

        $join=array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left",'facebook_rx_config'=>"facebook_rx_config.id=facebook_rx_fb_user_info.facebook_rx_config_id,left");
        $get_app_data=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("facebook_rx_fb_page_info.id"=>$plugin_data[0]["page_id"])),array("api_id","page_id"),$join);

        $app_id=isset($get_app_data[0]['api_id']) ? $get_app_data[0]['api_id'] : "";
        $fb_page_id=isset($get_app_data[0]['page_id']) ? $get_app_data[0]['page_id'] : "";
        $unique_ref=time().$user_id;
        $language=$plugin_data[0]["language"];
        if($language=="") $language="en_US";
        $validation_error=$plugin_data[0]["validation_error"];
        $button_click_success_message=$plugin_data[0]["button_click_success_message"];

        $redirect=$plugin_data[0]["redirect"];
        $success_redirect_url=$plugin_data[0]["success_redirect_url"];
        $add_button_with_message=$plugin_data[0]["add_button_with_message"];
        $button_with_message_content=json_decode($plugin_data[0]['button_with_message_content'],true);
        $success_button=isset($button_with_message_content['success_button'])?$button_with_message_content['success_button']:"";
        $success_url=isset($button_with_message_content['success_url'])?$button_with_message_content['success_url']:"";
        $success_button_bg_color=isset($button_with_message_content['success_button_bg_color'])?$button_with_message_content['success_button_bg_color']:"";
        $success_button_color=isset($button_with_message_content['success_button_color'])?$button_with_message_content['success_button_color']:"";
        $success_button_bg_color_hover=isset($button_with_message_content['success_button_bg_color_hover'])?$button_with_message_content['success_button_bg_color_hover']:"";
        $success_button_color_hover=isset($button_with_message_content['success_button_color_hover'])?$button_with_message_content['success_button_color_hover']:"";
        if($success_button_bg_color=='') $success_button_bg_color='#5CB85C';
        if($success_button_color=='') $success_button_color='#FFFFFF';
        if($success_button_bg_color_hover=='') $success_button_bg_color_hover='#339966';
        if($success_button_color_hover=='') $success_button_color_hover='#FFFDDD';

        $margin='';

        switch ($plugin_data[0]["btn_size"]) 
        {
            case 'small':
            $fontsize='10';
            $paddingtop='7';
            $paddingleft='9';
            $radius='3';
            $auto=60;
            $margin='margin:10px 0 10px 100px';
            break;

            case 'medium':
            $fontsize='11';
            $paddingtop='9';
            $paddingleft='12';
            $radius='5';
            $margin='margin:10px 0 10px 85px';
            break;

            case 'large':
            $fontsize='12';
            $paddingtop='11';
            $paddingleft='17';
            $radius='7';
            $margin='margin:10px 0 10px 80px';
            break;

            case 'xlarge':
            $fontsize='14';
            $paddingtop='14';
            $paddingleft='22';
            $radius='10';
            $margin='margin:10px 0 10px 70px';
            break;
            
            default:
            $fontsize='11';
            $paddingtop='9';
            $paddingleft='12';
            $radius='5';
            $margin='margin:10px 0 10px 85px';
            break;
        }

        if($plugin_data[0]['center_align']=='false') $margin='10px 0';

        $html_content='';
        // $html_content.='<style>#'.$plugin_data[0]["id_or_class_value"].'{font-family:Arial;display:block;'.$margin.';border:none;border-radius:'.$radius.'px;-moz-border-radius:'.$radius.'px;-webkit-border-radius:'.$radius.'px;cursor:pointer;text-align:center;background:'.$plugin_data[0]["new_button_bg_color"].';color:'.$plugin_data[0]["new_button_color"].';font-size:'.$fontsize.'px;padding:'.$paddingtop.'px '.$paddingleft.'px;}</style>';
        // $html_content.='<style>#'.$plugin_data[0]["id_or_class_value"].':hover{background:'.$plugin_data[0]["new_button_bg_color_hover"].';color:'.$plugin_data[0]["new_button_color_hover"].';}</style>';
        // $html_content.='<style>#'.$plugin_data[0]["id_or_class_value"].':focus{outline:none}</style>';
        $html_content.='<div class="fb-messenger-checkbox" origin="'.$plugin_data[0]["domain_name"].'" page_id="'.$fb_page_id.'" messenger_app_id="'.$app_id.'" user_ref="'.$unique_ref.'" prechecked="true" allow_login="true" size="'.$plugin_data[0]["btn_size"].'" skin="'.$plugin_data[0]["skin"].'" center_align="'.$plugin_data[0]["center_align"].'"></div><input type="hidden" value="'.$unique_ref.'" id="cart_user_unique_ref" name="cart_user_unique_ref" />';
        
        $html_content2='';
        $html_content2.='<style>#MESSENGER_CHECKBOX_PLUGIN_LOADER_01 a{background:'.$success_button_bg_color.';color:'.$success_button_color.';padding:10px 12px;text-decoration:none;}</style>';
        $html_content2.='<style>#MESSENGER_CHECKBOX_PLUGIN_LOADER_01 a:hover{background:'.$success_button_bg_color_hover.';color:'.$success_button_color_hover.';}</style>';
        

        // if($plugin_data[0]["new_button"]=='1')
        // {
        //     $btn='<input type="button" id="'.$plugin_data[0]["id_or_class_value"].'" value="'.$plugin_data[0]["new_button_display"].'"/>';
        //     if($plugin_data[0]["new_button_position"]=='top')
        //     $html_content=$btn.$html_content;
        //     else $html_content=$html_content.$btn;
        // }        

        $script='  
        var node = document.getElementById("MESSENGER_CHECKBOX_PLUGIN_LOADER_01");
        node.innerHTML=\''.$html_content.'\';

        var myCheckBoxState_01="";
        var send_to_messenger_state_01="";
        window.fbAsyncInit = function(){
              FB.init({
                  appId            : "'.$app_id.'",
                  autoLogAppEvents : true,
                  xfbml            : true,
                  version          : "v2.11"
              });
                
            FB.Event.subscribe("messenger_checkbox", function(e){            
              if (e.event == "rendered") 
              {
                console.log("Checkbox plugin was rendered");
              } 
              else if (e.event == "checkbox") 
              {
                myCheckBoxState_01=e.state;
                if(myCheckBoxState_01=="checked")
                  confirmOptIn();
              }  
              else if (e.event == "hidden") 
              {
                console.log("Checkbox plugin was hidden");
              }
              
            });   

            FB.Event.subscribe("send_to_messenger", function(e) {

              if (e.event == "rendered") 
              {
                console.log("Send to messenger plugin was rendered");
              }  
              else if (e.event == "hidden") 
              {
                console.log("Send to messenger plugin was hidden");
              }   

              else if(e.event=="opt_in")  
              {
                send_to_messenger_state_01=e.event;
                confirm_send_to_messenger();
              }   

            });  


          };
        
          (function(d, s, id){
             var js, fjs = d.getElementsByTagName(s)[0];
             if (d.getElementById(id)) {return;}
             js = d.createElement(s); js.id = id;
             js.src = "https://connect.facebook.net/'.$language.'/sdk.js";
             js.src = "//connect.facebook.net/'.$language.'/sdk/xfbml.customerchat.js";    
             fjs.parentNode.insertBefore(js, fjs);
           }(document, "script", "facebook-jssdk"));

           function confirmOptIn() {
              if(myCheckBoxState_01!="checked")
              {
                var validation_error="'.$validation_error.'";
                if(validation_error!="")
                {
                    alert(validation_error);
                }
                return false;
              }
              FB.AppEvents.logEvent("MessengerCheckboxUserConfirmation", null, {
                "app_id":"'.$app_id.'",
                "page_id":"'.$fb_page_id.'",
                "ref":"'.$plugin_data[0]["reference"].'",
                "user_ref":"'.$unique_ref.'"
              });

              
              var redirect="'.$redirect.'";
              var button_click_success_message="'.$button_click_success_message.'";
              var full_button_click_success_message="<span style=\"font-family:Arial\">'.$html_content2.'";
              if(redirect=="0")
              {
                full_button_click_success_message=full_button_click_success_message+button_click_success_message;
                var add_button_with_message="'.$add_button_with_message.'";
             
                if(add_button_with_message=="1")
                full_button_click_success_message=full_button_click_success_message+" <a href=\"'.$success_url.'\">'.$success_button.'</a>";

                full_button_click_success_message=full_button_click_success_message+"</span>";

                var node1 = document.getElementById("MESSENGER_CHECKBOX_PLUGIN_LOADER_01");        
                node1.innerHTML=full_button_click_success_message;
              }
              else
              {
                window.location.replace("'.$success_redirect_url.'");
              }
           }';

       // $click_js="";
       // if($plugin_data[0]["element_type"]=="id")
       // $click_js.='var action_event_01 = document.getElementById("'.$plugin_data[0]["id_or_class_value"].'");';
       // else
       // $click_js.='var action_event_01 = document.querySelector(".'.$plugin_data[0]["id_or_class_value"].'");';

       // $click_js.='
       // action_event_01.addEventListener("'.$plugin_data[0]["js_event"].'",function(e){
       //      confirmOptIn();
       // },false);';

       // $script.=$click_js;

       echo $script;        

    }  

    /* http://example.com/messenger_bot_enhancers/send_to_messenger_plugin.js?code=54848488 */
    public function send_to_messenger_plugin() // echo js getting domain code
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/javascript');
        $code=$this->input->get('code');
        if($code=="") 
        {
            echo "console.error('".$this->config->item('product_name')." Error : Facebook send to messenger plugin is failed to load, no domain code found.');";
            exit();
        }

        $plugin_data=$this->basic->get_data("messenger_bot_engagement_send_to_msg",array("where"=>array("domain_code"=>$code)));
        if(!isset($plugin_data[0])) 
        {
            echo "console.error('".$this->config->item('product_name')." Error : Facebook send to messenger plugin is failed to load, invalid domain code.');";
            exit();
        }

        $user_id=$plugin_data[0]["user_id"];
        $user_data=$this->basic->get_data("users",array("where"=>array("id"=>$user_id,"status"=>"1")));
        if(!isset($user_data[0])) 
        {
            echo "console.error('".$this->config->item('product_name')." Error : Facebook send to messenger plugin is failed to load, the requesting user is no longer valid.');";
            exit();
        }

        $join=array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left",'facebook_rx_config'=>"facebook_rx_config.id=facebook_rx_fb_user_info.facebook_rx_config_id,left");
        $get_app_data=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("facebook_rx_fb_page_info.id"=>$plugin_data[0]["page_id"])),array("api_id","page_id"),$join);

        $app_id=isset($get_app_data[0]['api_id']) ? $get_app_data[0]['api_id'] : "";
        $fb_page_id=isset($get_app_data[0]['page_id']) ? $get_app_data[0]['page_id'] : "";
        $language=$plugin_data[0]["language"];
        if($language=="") $language="en_US";
        $button_click_success_message=$plugin_data[0]["button_click_success_message"];


        $redirect=$plugin_data[0]["redirect"];
        $success_redirect_url=$plugin_data[0]["success_redirect_url"];
        $add_button_with_message=$plugin_data[0]["add_button_with_message"];
        $button_with_message_content=json_decode($plugin_data[0]['button_with_message_content'],true);
        $success_button=isset($button_with_message_content['success_button'])?$button_with_message_content['success_button']:"";
        $success_url=isset($button_with_message_content['success_url'])?$button_with_message_content['success_url']:"";
        $success_button_bg_color=isset($button_with_message_content['success_button_bg_color'])?$button_with_message_content['success_button_bg_color']:"";
        $success_button_color=isset($button_with_message_content['success_button_color'])?$button_with_message_content['success_button_color']:"";
        $success_button_bg_color_hover=isset($button_with_message_content['success_button_bg_color_hover'])?$button_with_message_content['success_button_bg_color_hover']:"";
        $success_button_color_hover=isset($button_with_message_content['success_button_color_hover'])?$button_with_message_content['success_button_color_hover']:"";
        if($success_button_bg_color=='') $success_button_bg_color='#5CB85C';
        if($success_button_color=='') $success_button_color='#FFFFFF';
        if($success_button_bg_color_hover=='') $success_button_bg_color_hover='#339966';
        if($success_button_color_hover=='') $success_button_color_hover='#FFFDDD';
    
        $cta_text=isset($plugin_data[0]['cta_text_option'])?$plugin_data[0]['cta_text_option']:"";
        
        if($cta_text!="")
          $cta_text = ' cta_text="'.$cta_text.'" ';

        $html_content='';
       
        $html_content.=' <div class="fb-send-to-messenger" '.$cta_text.' messenger_app_id="'.$app_id.'" page_id="'.$fb_page_id.'" data-ref="'.$plugin_data[0]["reference"].'" color="'.$plugin_data[0]["skin"].'"  size="'.$plugin_data[0]["btn_size"].'"></div>';

        $html_content2='';
        $html_content2.='<style>#SEND_TO_MESSENGER_PLUGIN_LOADER_01 a{background:'.$success_button_bg_color.';color:'.$success_button_color.';padding:10px 12px;text-decoration:none;}</style>';
        $html_content2.='<style>#SEND_TO_MESSENGER_PLUGIN_LOADER_01 a:hover{background:'.$success_button_bg_color_hover.';color:'.$success_button_color_hover.';}</style>';
        

        $script='  
        var node = document.getElementById("SEND_TO_MESSENGER_PLUGIN_LOADER_01");
        node.innerHTML=\''.$html_content.'\';

         var myCheckBoxState_01="";
         var send_to_messenger_state_01="";

        window.fbAsyncInit = function(){
          
              FB.init({
                  appId            : "'.$app_id.'",
                  autoLogAppEvents : true,
                  xfbml            : true,
                  version          : "v2.11"
              }); 

            FB.Event.subscribe("send_to_messenger", function(e) {

              if (e.event == "rendered") 
              {
                console.log("Send to messenger plugin was rendered");
              }  
              else if (e.event == "hidden") 
              {
                console.log("Send to messenger plugin was hidden");
              }   

              else if(e.event=="opt_in")  
              {
                send_to_messenger_state_01=e.event;
                confirm_send_to_messenger();
              }   
           

            });  


            FB.Event.subscribe("messenger_checkbox", function(e){            
              if (e.event == "rendered") 
              {
                console.log("Checkbox plugin was rendered");
              } 
              else if (e.event == "checkbox") 
              {
                myCheckBoxState_01=e.state;
                if(myCheckBoxState_01=="checked")
                  confirmOptIn();
              }  
              else if (e.event == "hidden") 
              {
                console.log("Checkbox plugin was hidden");
              }
              
            });  

          };

          function confirm_send_to_messenger(){

              var redirect2="'.$redirect.'";
              var button_click_success_message2="'.$button_click_success_message.'";
              var full_button_click_success_message2="<span style=\"font-family:Arial\">'.$html_content2.'";
              if(send_to_messenger_state_01=="opt_in" && redirect2=="0")
              {
                full_button_click_success_message2=full_button_click_success_message2+button_click_success_message2;
                var add_button_with_message2="'.$add_button_with_message.'";
             
                if(add_button_with_message2=="1")
                full_button_click_success_message2=full_button_click_success_message2+" <a href=\"'.$success_url.'\">'.$success_button.'</a>";

                full_button_click_success_message2=full_button_click_success_message2+"</span>";

                var node1 = document.getElementById("SEND_TO_MESSENGER_PLUGIN_LOADER_01");        
                node1.innerHTML=full_button_click_success_message2;
              }
              else if(send_to_messenger_state_01=="opt_in" && redirect2=="1")
              {
                window.location.replace("'.$success_redirect_url.'");
              }
            }
                  
          (function(d, s, id){
             var js, fjs = d.getElementsByTagName(s)[0];
             if (d.getElementById(id)) {return;}
             js = d.createElement(s); js.id = id;
             js.src = "https://connect.facebook.net/'.$language.'/sdk.js";
             js.src = "//connect.facebook.net/'.$language.'/sdk/xfbml.customerchat.js"; 
             fjs.parentNode.insertBefore(js, fjs);
           }(document, "script", "facebook-jssdk"));';

       echo $script;   
          
    }

    /* http://example.com/messenger_bot_enhancers/mme_link.js?code=54848488 */
    public function mme_link() // echo js getting link code
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/javascript');
        $code=$this->input->get('code');
        if($code=="") 
        {
            echo "console.error('".$this->config->item('product_name')." Error : m.me link plugin is failed to load, no link code found.');";
            exit();
        }

        $plugin_data=$this->basic->get_data("messenger_bot_engagement_mme",array("where"=>array("link_code"=>$code)));
        if(!isset($plugin_data[0])) 
        {
            echo "console.error('".$this->config->item('product_name')." Error : m.me link plugin is failed to load, invalid link code.');";
            exit();
        }

        $user_id=$plugin_data[0]["user_id"];
        $user_data=$this->basic->get_data("users",array("where"=>array("id"=>$user_id,"status"=>"1")));
        if(!isset($user_data[0])) 
        {
            echo "console.error('".$this->config->item('product_name')." Error : m.me link plugin is failed to load, the requesting user is no longer valid.');";
            exit();
        }   

        switch ($plugin_data[0]["btn_size"]) 
        {
            case 'small':
            $fontsize='10';
            $paddingtop='7';
            $paddingleft='9';
            $radius='3';
            break;

            case 'medium':
            $fontsize='11';
            $paddingtop='9';
            $paddingleft='12';
            $radius='5';
            break;

            case 'large':
            $fontsize='12';
            $paddingtop='11';
            $paddingleft='17';
            $radius='7';
            break;

            case 'xlarge':
            $fontsize='14';
            $paddingtop='14';
            $paddingleft='22';
            $radius='10';
            break;
            
            default:
            $fontsize='11';
            $paddingtop='9';
            $paddingleft='12';
            $radius='5';
            break;
        }

        $html_content='';
        $html_content.='<style>#MME_LINK_LOADER_01 button{font-family:Arial;text-decoration:none;border:none;border-radius:'.$radius.'px;-moz-border-radius:'.$radius.'px;-webkit-border-radius:'.$radius.'px;cursor:pointer;text-align:center;background:'.$plugin_data[0]["new_button_bg_color"].';color:'.$plugin_data[0]["new_button_color"].';font-size:'.$fontsize.'px;padding:'.$paddingtop.'px '.$paddingleft.'px;}</style>';
        $html_content.='<style>#MME_LINK_LOADER_01 button:hover{background:'.$plugin_data[0]["new_button_bg_color_hover"].';color:'.$plugin_data[0]["new_button_color_hover"].';}</style>';
        $html_content.='<style>#MME_LINK_LOADER_01 button:focus{outline:none}</style>';
        $html_content.='<style>#MME_LINK_LOADER_01{margin:15px 0; text-align:center;}</style>';

        $page_info=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$plugin_data[0]["page_id"])));
        $username=isset($page_info[0]["username"]) ? $page_info[0]["username"] : "";
        $fb_id=isset($page_info[0]["page_id"]) ? $page_info[0]["page_id"] : "";
        $value=($username!="")?$username:$fb_id;

        $href='https://m.me/'.$value.'?ref='.urlencode($plugin_data[0]["reference"]);
        $html_content.='<button onclick="openInNewTab()" href="'.$href.'" target="_BLANK">'.$plugin_data[0]["new_button_display"].'</button>';
      
        $script='
        var node = document.getElementById("MME_LINK_LOADER_01");
        node.innerHTML=\''.$html_content.'\';
        function openInNewTab() {
          var win = window.open("'.$href.'", "_blank");
          win.focus();
        }';

        echo $script;        
    }

    public function checkbox_plugin_list($page_id=0,$iframe='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(213,$this->module_access))
        redirect('home/login_page', 'location');

        $data['body'] = 'messenger_engagement/checkbox_plugin_list';
        $data['page_title'] = $this->lang->line("Checkbox Plugin");
        
        $data['page_info'] = $this->get_user_page();
        // $data['label_info'] = $this->get_page_label();
        $data['iframe'] = $iframe;
        $data['selected_page'] = $page_id;
        $data['media_type'] = $this->session->userdata("selected_global_media_type");
        $this->_viewcontroller($data);
    }

    public function checkbox_plugin_list_data()
    {
      $this->ajax_check();
      if($this->session->userdata('user_type') != 'Admin' && !in_array(213,$this->module_access)) exit();

      $pagename        = trim($this->input->post("search_page_id",true));
      $domain_name     = isset($_POST['search']) ? $_POST['search']['value'] : null;
      $display_columns = array("#",'id','domain_name','page_name','domain_code','actions','domain_code','reference','visual_flow_type','created_at','label_names');
      $search_columns = array('domain_name','page_name','created_at');

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
      $order_by=$sort." ".$order;

      $where_simple=array();

      if($pagename !="") $where_simple['messenger_bot_engagement_checkbox.page_id'] = $pagename;
      if($domain_name !="") $where_simple['domain_name like'] = "%".$domain_name."%";

      $where_simple['messenger_bot_engagement_checkbox.user_id'] = $this->user_id;
      $where_simple['messenger_bot_engagement_checkbox.for_woocommerce'] ='0';

      $where  = array('where'=>$where_simple);
      $select = array("messenger_bot_engagement_checkbox.*","page_name","facebook_rx_fb_page_info.page_id as fb_page_id");
      $join   = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot_engagement_checkbox.page_id,left");   

      $table = "messenger_bot_engagement_checkbox";
      $info = $this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');
      $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
      $total_result=$total_rows_array[0]['total_rows'];

      for($i=0;$i<count($info);$i++)
      {   

          // $info[$i]["new_button"] = ($info[$i]["new_button"]=='1') ? "<span title=".$this->lang->line('New')." class='text-success'><i class='fa fa-star-o '></i> ".$this->lang->line('New')."</span>" : "<span title='".$this->lang->line('Already Existing')."' class='text-warning'><i class='fa fa-circle-o'></i> ".$this->lang->line('Exist')."</span>";

          $info[$i]["domain_name"] = "<a data-toggle='tooltip' data-original-title='".$info[$i]["domain_name"]."' target='_BLANK' href='".addHttp($info[$i]["domain_name"])."'>".$info[$i]["domain_name"]."</a>";

          $info[$i]["page_name"] = "<a data-toggle='tooltip' data-original-title='".$this->lang->line('Visit Page')."' target='_BLANK' href='https://facebook.com/".$info[$i]["fb_page_id"]."'>".$info[$i]["page_name"]."</a>";

          $info[$i]['created_at'] = date('jS F y', strtotime($info[$i]['created_at']));
          $label_ids=$info[$i]["label_ids"];

          $label_names="";
          if($label_ids!="")
          {
              $label_ids_array=explode(',', $label_ids);
              $label_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where_in"=>array("id"=>$label_ids_array)));
              $label_names_array=array_column($label_data, 'group_name');
              $label_names=implode(', ', $label_names_array);
          }
          $info[$i]["label_names"]=$label_names;
      
            $info[$i]['actions'] = '<div style="min-width:90px;">';
          if($info[$i]['visual_flow_type'] == 'flow')
          {
            $flow_campaign_exist = $this->basic->get_data('visual_flow_builder_campaign',['where'=>['id'=>$info[$i]['visual_flow_campaign_id'],'user_id'=>$this->user_id]],['id']);
            if(!empty($flow_campaign_exist))
                $info[$i]['actions'] .= '<a target="_blank" class="btn btn-circle btn-outline-warning" href="'.base_url()."visual_flow_builder/edit_builder_data/".$info[$i]['visual_flow_campaign_id'].'/4" title="'.$this->lang->line('Edit').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-edit"></i></a> &nbsp;';
            else
                $info[$i]['actions'] .= '<div style="min-width:100px;"><a class="btn btn-circle btn-outline-warning" href="'.base_url()."messenger_bot_enhancers/checkbox_plugin_edit/".$info[$i]['id'].'/1" title="'.$this->lang->line('Edit').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-edit"></i></a> &nbsp;';
          }
          else
            $info[$i]['actions'] .= '<div style="min-width:100px;"><a class="btn btn-circle btn-outline-warning" href="'.base_url()."messenger_bot_enhancers/checkbox_plugin_edit/".$info[$i]['id'].'/1" title="'.$this->lang->line('Edit').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-edit"></i></a> &nbsp;';

          if($info[$i]['visual_flow_type'] == 'general') {

            $info[$i]['actions'] .= '<a class="btn btn-circle btn-outline-danger delete_campaign" href="#" title="'.$this->lang->line('delete').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-trash-alt"></i></a>';
          }
          $info[$i]['actions'] .= "</div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";

          $info[$i]['visual_flow_type'] = ucfirst($info[$i]['visual_flow_type']);

      }

      $data['draw'] = (int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

      echo json_encode($data);
    }

    public function checkbox_plugin_js_code()
    {
       $this->ajax_check();
       if($this->session->userdata('user_type') != 'Admin' && !in_array(213,$this->module_access)) exit();
       $id=$this->input->post("campaign_id");

       $plugin_data=$this->basic->get_data("messenger_bot_engagement_checkbox",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
       $domain_code=isset($plugin_data[0]["domain_code"])?$plugin_data[0]["domain_code"]:"";       

       $str ='<div style="z-index:9999999;" id="MESSENGER_CHECKBOX_PLUGIN_LOADER_01"></div><script type="text/javascript" src="'.base_url('messenger_bot_enhancers/messenger_checkbox_plugin.js?code='.$domain_code).'"></script>';
       echo $str;
    }

    public function checkbox_plugin_delete($campaign_id=0)
    {   
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(213,$this->module_access)) exit();

        $id = $this->input->post('campaign_id',true);
        $response = array();
        $this->db->trans_start();
        $xdata=$this->basic->get_data("messenger_bot_engagement_checkbox",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $domain_code=isset($xdata[0]['domain_code'])?$xdata[0]['domain_code']:"";

        if($domain_code=="")
        {
            $response['status'] = '0';
            $response['message'] = $this->lang->line('Something went wrong.');
        }
        
        $this->basic->delete_data('messenger_bot_engagement_checkbox',$where=array('id'=>$id));

        //******************************//
        // delete data to useges log table
        $this->_delete_usage_log($module_id=213,$request=1);   
        //******************************//

        $this->db->trans_complete();
        if($this->db->trans_status() === false) 
        {
          $response['status'] = '0';
          $response['message'] = $this->lang->line('Something went wrong.');
        } 
        else 
        {

          $response['status'] = '1';
          $response['message'] = $this->lang->line('Plugin has been deleted successfully.');
        }
        echo json_encode($response);
    }

    public function checkbox_plugin_add($page_id=0,$iframe='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(213,$this->module_access))
        redirect('home/login_page', 'location');
        $this->is_broadcaster_exist=$this->broadcaster_exist();
        $data['body'] = 'messenger_engagement/checkbox_plugin_add';
        $data['page_title'] = $this->lang->line("Add Checkbox Plugin");
        $data['page_info'] = $this->get_user_page();
        $data['js_events'] = $this->get_js_events();
        $data['sdk_list'] = $this->sdk_locale();
        $data['iframe'] = $iframe;
        $data['page_id'] = $page_id;
        $data['btn_sizes'] = $this->basic->get_enum_values("messenger_bot_engagement_checkbox","btn_size");
        $this->_viewcontroller($data);
    }

    public function checkbox_plugin_add_action()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(213,$this->module_access)) exit();

        $status=$this->_check_usage($module_id=213,$request=1);
        if($status=="3")  //monthly limit is exceeded, can not create another campaign this month
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("limit has been exceeded. you can no longer use this feature.")));
            exit();
        }

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$this->input->post($key,true);
        }
        //echo "<pre>";print_r($post);exit;
        $button_click_success_message=str_replace('"',"'", $button_click_success_message);

        if(!isset($add_button_with_message)) $add_button_with_message='0';

        $button_with_message_content=array();
        if($redirect=='1')
        {
          $button_click_success_message="";
          $add_button_with_message='0';
        }
        else
        {
          $success_redirect_url="";
          if($add_button_with_message=='1')
          $button_with_message_content=array("success_button"=>$success_button,"success_url"=>$success_url,"success_button_bg_color"=>$success_button_bg_color,"success_button_color"=>$success_button_color,"success_button_bg_color_hover"=>$success_button_bg_color_hover,"success_button_color_hover"=>$success_button_color_hover);
        }


        $pageinfo=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page)));
        $access_token=isset($pageinfo[0]["page_access_token"])?$pageinfo[0]["page_access_token"]:"";
        
        if($access_token=="")
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Facebook page not found.')));
            exit();
        }

        // if($this->basic->is_exist("messenger_bot_engagement_checkbox",array("domain_name"=>$domain_name,"page_id"=>$page),$select='id'))
        // {
        //     echo json_encode(array('status'=>'0','message'=>"".$this->lang->line("Plugin has been already generated for this page & domain before.")));
        //     exit();
        // }

        $this->load->library('fb_rx_login');
        $domain_whitelist=$this->fb_rx_login->domain_whitelist($access_token,$domain_name);
        if(!isset($domain_whitelist['status']) || $domain_whitelist['status']=='0')
        {
            $fb_login_button='';
            $error=$this->lang->line('Domain failed to white-list.');
            if(isset($domain_whitelist['error']['code']) && trim($domain_whitelist['error']['code'])=='230') //does not have page_messages permission, need to login again
            {
                // $redirect_url = base_url()."home/redirect_rx_link";
                // $fb_login_button = $this->fb_rx_login->login_for_user_access_token($redirect_url);
                // $fb_login_button="<br><br>".$fb_login_button;
                $error= $this->lang->line('Domain failed to white-list. Requires pages_messaging permission to perform this operation. You need to login with Facebook again clicking the button below and then you can continue.');
            }
            else if(isset($domain_whitelist['error']['message']))
            {
                $error=$domain_whitelist['error']['message'];
            }
            echo json_encode(array('status'=>'0','message'=>"".$error.$fb_login_button));
            exit();
        }

        $domain_code = $this->_random_number_generator(8);
        // $reference=$reference."-".$domain_code;
        if($this->basic->is_exist("messenger_bot_engagement_checkbox",array("reference"=>$reference))) 
        {
            $unique_lang=$this->lang->line("is_unique");
            $unique_lang=str_replace('<b>%s</b>', $this->lang->line('reference'), $unique_lang);
            echo json_encode(array("status" => "0", "message" =>$unique_lang));
            exit();
        }
        $js_url=base_url('messenger_bot_enhancers/messenger_checkbox_plugin.js?code='.$domain_code);
        $js_code='<div style="z-index:9999999;" id="MESSENGER_CHECKBOX_PLUGIN_LOADER_01"></div><script type="text/javascript" src="'.$js_url.'"></script>';

        $this->db->trans_start(); 

        // if($new_button=='0')
        // {
        //     $new_button_display=$new_button_position=$new_button_bg_color=$new_button_bg_color_hover=$new_button_color=$new_button_color_hover="";
        // }
        // else
        // {
        //     $id_or_class_value="MESSENGER_CHECKBOX_PLUGIN_CONFIRM_OPTION_01";
        //     $element_type="id";
        // }

        if(!isset($label_ids)) $label_ids=array();
        $insert_data=array
        (
            "domain_code"=>$domain_code,
            "user_id"=>$this->user_id,
            "page_id"=>$page,
            "domain_name"=>$domain_name,
            // "js_event"=>$js_event,
            // "element_type"=>$element_type,
            // "id_or_class_value"=>$id_or_class_value,
            "btn_size"=>$btn_size,
            "skin"=>$skin,
            "center_align"=>$center_align,
            // "new_button"=>$new_button,
            // "new_button_display"=>$new_button_display,
            // "new_button_position"=>$new_button_position,
            // "new_button_bg_color"=>$new_button_bg_color,
            // "new_button_bg_color_hover"=>$new_button_bg_color_hover,
            // "new_button_color"=>$new_button_color,
            // "new_button_color_hover"=>$new_button_color_hover,
            "button_click_success_message"=>$button_click_success_message,
            "label_ids"=>implode(',',$label_ids),
            "reference"=>$reference,
            "template_id"=>$template_id,
            "validation_error"=>$validation_error,
            "language"=>$language,
            "created_at"=>date("Y-m-d H:i:s"),
            "redirect"=>$redirect,
            "add_button_with_message"=>$add_button_with_message,
            "button_with_message_content"=>json_encode($button_with_message_content),
            "success_redirect_url"=>$success_redirect_url
        );    

        $this->basic->insert_data("messenger_bot_engagement_checkbox",$insert_data);   

        $this->_insert_usage_log($module_id=213,$request=1);
        $this->db->trans_complete();

        if($this->db->trans_status() === false)
        {
             echo json_encode(array('status'=>'0','message'=>"".$this->lang->line('something went wrong, please try again.')));
             exit();
        }
        else
        {
            echo json_encode(array('status'=>'1','message'=>"<i class='fa fa-check'></i> ".$this->lang->line('plugin has been created successfully.'),'js_code'=>$js_code));
            exit();
        } 
    }

    public function checkbox_plugin_edit($id=0,$iframe='0')
    {
        if($id==0) exit();
        $this->is_broadcaster_exist=$this->broadcaster_exist();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(213,$this->module_access))
        redirect('home/login_page', 'location');

        $data['body'] = 'messenger_engagement/checkbox_plugin_edit';
        $data['page_title'] = $this->lang->line("Edit Checkbox Plugin");
        $data['page_info'] = $this->get_user_page();
        $data['js_events'] = $this->get_js_events();
        $data['sdk_list'] = $this->sdk_locale();
        $data['btn_sizes'] = $this->basic->get_enum_values("messenger_bot_engagement_checkbox","btn_size");
        $xdata=$this->basic->get_data("messenger_bot_engagement_checkbox",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        if(!isset($xdata[0])) exit();
        $data['xdata']=$xdata[0];
        $data['iframe'] = $iframe;
        $this->_viewcontroller($data);
    }

    public function checkbox_plugin_edit_action()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(213,$this->module_access)) exit();

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$this->input->post($key,true);
        }
        $button_click_success_message=str_replace('"',"'", $button_click_success_message);

        if(!isset($add_button_with_message)) $add_button_with_message='0';

        $button_with_message_content=array();
        if($redirect=='1')
        {
          $button_click_success_message="";
          $add_button_with_message='0';
        }
        else
        {
          $success_redirect_url="";
          if($add_button_with_message=='1')
          $button_with_message_content=array("success_button"=>$success_button,"success_url"=>$success_url,"success_button_bg_color"=>$success_button_bg_color,"success_button_color"=>$success_button_color,"success_button_bg_color_hover"=>$success_button_bg_color_hover,"success_button_color_hover"=>$success_button_color_hover);
        }

        // if($new_button=='0')
        // {
        //     $new_button_display=$new_button_position=$new_button_bg_color=$new_button_bg_color_hover=$new_button_color=$new_button_color_hover="";
        // }
        // else
        // {
        //     $id_or_class_value="MESSENGER_CHECKBOX_PLUGIN_CONFIRM_OPTION_01";
        //     $element_type="id";
        // }
        if(!isset($label_ids)) $label_ids=array();
        $insert_data=array
        (
            // "js_event"=>$js_event,
            // "element_type"=>$element_type,
            // "id_or_class_value"=>$id_or_class_value,
            "btn_size"=>$btn_size,
            "skin"=>$skin,
            "center_align"=>$center_align,
            // "new_button"=>$new_button,
            // "new_button_display"=>$new_button_display,
            // "new_button_position"=>$new_button_position,
            // "new_button_bg_color"=>$new_button_bg_color,
            // "new_button_bg_color_hover"=>$new_button_bg_color_hover,
            // "new_button_color"=>$new_button_color,
            // "new_button_color_hover"=>$new_button_color_hover,
            "button_click_success_message"=>$button_click_success_message,
            "label_ids"=>implode(',',$label_ids),
            "template_id"=>$template_id,
            "validation_error"=>$validation_error,
            "language"=>$language,
            "redirect"=>$redirect,
            "add_button_with_message"=>$add_button_with_message,
            "button_with_message_content"=>json_encode($button_with_message_content),
            "success_redirect_url"=>$success_redirect_url
        );    

        if($this->basic->update_data("messenger_bot_engagement_checkbox",array("id"=>$hidden_id,"user_id"=>$this->user_id),$insert_data))   echo "1";
        else
             echo "0";    
    }

    public function send_to_messenger_list($page_id=0,$iframe='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(214,$this->module_access))
        redirect('home/login_page', 'location');

        $data['body'] = 'messenger_engagement/send_to_messenger_list';
        $data['page_title'] = $this->lang->line("Send to Messenger Plugin");
        
        $data['page_info'] = $this->get_user_page();
        // $data['label_info'] = $this->get_page_label();
        $data['page_id'] = $page_id;
        $data['iframe'] = $iframe;
        $data['media_type'] = $this->session->userdata("selected_global_media_type");
        $this->_viewcontroller($data);
    }

    public function send_to_messenger_list_data()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(214,$this->module_access)) exit();

        $pagename        = trim($this->input->post("search_page_id",true));
        $domain_name       = isset($_POST['search']) ? $_POST['search']['value'] : null;;
        $display_columns = array("#",'id','domain_name','page_name','domain_code','actions','visual_flow_type','domain_code','reference','created_at','label_names');
        $search_columns = array('domain_name','page_name','created_at');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple=array();

        if($pagename !="") 
          $where_simple['messenger_bot_engagement_send_to_msg.page_id'] = $pagename;
        if($domain_name !="") 
          $where_simple['domain_name like'] = "%".$domain_name."%";

        $where_simple['messenger_bot_engagement_send_to_msg.user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);



        $table = "messenger_bot_engagement_send_to_msg";  
        $join =array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot_engagement_send_to_msg.page_id,left");
        $select = array("messenger_bot_engagement_send_to_msg.*","page_name","facebook_rx_fb_page_info.page_id as fb_page_id");

        $info = $this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');
        //echo "<pre>";print_r($info);exit;
        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        for($i=0;$i<count($info);$i++)
        {
            $info[$i]["domain_name"] = "<a data-toggle='tooltip' data-original-title='".$info[$i]["domain_name"]."' target='_BLANK' href='".addHttp($info[$i]["domain_name"])."'>".$info[$i]["domain_name"]."</a>";

            $info[$i]["page_name"] = "<a data-toggle='tooltip' data-original-title='".$this->lang->line('Visit Page')."' target='_BLANK' href='https://facebook.com/".$info[$i]["fb_page_id"]."'>".$info[$i]["page_name"]."</a>";

            $info[$i]['created_at'] = date('jS F y', strtotime($info[$i]['created_at']));

            $label_ids=$info[$i]["label_ids"];
            $label_names="";
            if($label_ids!="")
            {
                $label_ids_array=explode(',', $label_ids);
                $label_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where_in"=>array("id"=>$label_ids_array)));
                $label_names_array=array_column($label_data, 'group_name');
                $label_names=implode(', ', $label_names_array);
            }
            $info[$i]["label_names"]=$label_names;

            $info[$i]['actions'] = '<div style="min-width:100px;">';
            if($info[$i]['visual_flow_type'] == 'flow')
            {
                $flow_campaign_exist = $this->basic->get_data('visual_flow_builder_campaign',['where'=>['id'=>$info[$i]['visual_flow_campaign_id'],'user_id'=>$this->user_id]],['id']);
                if(!empty($flow_campaign_exist))
                    $info[$i]['actions'] .= '<a target="_BLANK" class="btn btn-circle btn-outline-warning" href="'.base_url()."visual_flow_builder/edit_builder_data/".$info[$i]['visual_flow_campaign_id'].'/5" title="'.$this->lang->line('Edit').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-edit"></i></a> &nbsp;';
                else
                    $info[$i]['actions'] .= '<div style="min-width:100px;"><a class="btn btn-circle btn-outline-warning" href="'.base_url()."messenger_bot_enhancers/send_to_messenger_edit/".$info[$i]['id'].'/1" title="'.$this->lang->line('Edit').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-edit"></i></a> &nbsp;';
            }
            else
                $info[$i]['actions'] .= '<div style="min-width:100px;"><a class="btn btn-circle btn-outline-warning" href="'.base_url()."messenger_bot_enhancers/send_to_messenger_edit/".$info[$i]['id'].'/1" title="'.$this->lang->line('Edit').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-edit"></i></a> &nbsp;';
            
            if($info[$i]['visual_flow_type'] == 'general') {
                $info[$i]['actions'] .= '<a class="btn btn-circle btn-outline-danger delete_campaign" href="#" title="'.$this->lang->line('delete').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-trash-alt"></i></a>';
            }
            $info[$i]['actions'] .= "</div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
            $info[$i]['visual_flow_type'] = ucfirst($info[$i]['visual_flow_type']);

        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    } 

    public function send_to_messenger_js_code()
    {
       $this->ajax_check();
       if($this->session->userdata('user_type') != 'Admin' && !in_array(214,$this->module_access)) exit();
       $id=$this->input->post("campaign_id");

       $plugin_data=$this->basic->get_data("messenger_bot_engagement_send_to_msg",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
       $domain_code=isset($plugin_data[0]["domain_code"])?$plugin_data[0]["domain_code"]:"";       

       $str ='<div style="z-index:9999999;" id="SEND_TO_MESSENGER_PLUGIN_LOADER_01"></div><script type="text/javascript" src="'.base_url('messenger_bot_enhancers/send_to_messenger_plugin.js?code='.$domain_code).'"></script>';
       echo $str;
    }

    public function send_to_messenger_delete($id=0)
    {
        $this->ajax_check(); 
        if($this->session->userdata('user_type') != 'Admin' && !in_array(214,$this->module_access)) exit();

        $id = $this->input->post('campaign_id',true);
        $response = array();
        $this->db->trans_start();
        $xdata=$this->basic->get_data("messenger_bot_engagement_send_to_msg",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $domain_code=isset($xdata[0]['domain_code'])?$xdata[0]['domain_code']:"";
        if($domain_code=="")
        {
          $response['status'] = '0';
          $response['message'] = $this->lang->line('Something went wrong.');
        }

        $this->basic->delete_data('messenger_bot_engagement_send_to_msg',$where=array('id'=>$id));

        //******************************//
        // delete data to useges log table
        $this->_delete_usage_log($module_id=214,$request=1);   
        //******************************//

        $this->db->trans_complete();
        if($this->db->trans_status() === false) 
        {
           $response['status'] = '0';
           $response['message'] = $this->lang->line('Something went wrong.');
        } 
        else 
        {
          $response['status'] = '1';
          $response['message'] = $this->lang->line('Plugin has been deleted successfully.');
        }
        echo json_encode($response);
    }

    public function send_to_messenger_add($page_id=0,$iframe='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(214,$this->module_access))
        redirect('home/login_page', 'location');
        $this->is_broadcaster_exist=$this->broadcaster_exist();
        $data['body'] = 'messenger_engagement/send_to_messenger_add';
        $data['page_title'] = $this->lang->line("Add Send to Messenger Plugin");
        $data['page_info'] = $this->get_user_page();
        $data['sdk_list'] = $this->sdk_locale();
        $data['cta_options'] = $this->get_cta_options();
        $data['btn_sizes'] = $this->basic->get_enum_values("messenger_bot_engagement_send_to_msg","btn_size");
        $data['page_id'] = $page_id;
        $data['iframe'] = $iframe;
        $this->_viewcontroller($data);
    }

    public function send_to_messenger_add_action()
    {
        $this->ajax_check(); 
        if($this->session->userdata('user_type') != 'Admin' && !in_array(214,$this->module_access)) exit();

        $status=$this->_check_usage($module_id=214,$request=1);
        if($status=="3")  //monthly limit is exceeded, can not create another campaign this month
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("limit has been exceeded. you can no longer use this feature.")));
            exit();
        }

        $post=$_POST;
        //echo "<pre>";print_r($post);exit;
        foreach ($post as $key => $value) 
        {
            $$key=$this->input->post($key,true);
        }
        $button_click_success_message=str_replace('"',"'", $button_click_success_message);

        if(!isset($add_button_with_message)) $add_button_with_message='0';

        $button_with_message_content=array();
        if($redirect=='1')
        {
          $button_click_success_message="";
          $add_button_with_message='0';
        }
        else
        {
          $success_redirect_url="";
          if($add_button_with_message=='1')
          $button_with_message_content=array("success_button"=>$success_button,"success_url"=>$success_url,"success_button_bg_color"=>$success_button_bg_color,"success_button_color"=>$success_button_color,"success_button_bg_color_hover"=>$success_button_bg_color_hover,"success_button_color_hover"=>$success_button_color_hover);
        }

        $pageinfo=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page)));
        $access_token=isset($pageinfo[0]["page_access_token"])?$pageinfo[0]["page_access_token"]:"";
        
        if($access_token=="")
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Facebook page not found.')));
            exit();
        }

        // if($this->basic->is_exist("messenger_bot_engagement_send_to_msg",array("domain_name"=>$domain_name,"page_id"=>$page),$select='id'))
        // {
        //     echo json_encode(array('status'=>'0','message'=>"".$this->lang->line("Plugin has been already generated for this page & domain before.")));
        //     exit();
        // }

        $this->load->library('fb_rx_login');
        $domain_whitelist=$this->fb_rx_login->domain_whitelist($access_token,$domain_name);
        if(!isset($domain_whitelist['status']) || $domain_whitelist['status']=='0')
        {
            $fb_login_button='';
            $error=$this->lang->line('Domain failed to white-list.');
            if(isset($domain_whitelist['error']['code']) && trim($domain_whitelist['error']['code'])=='230') //does not have page_messages permission, need to login again
            {
                // $redirect_url = base_url()."home/redirect_rx_link";
                // $fb_login_button = $this->fb_rx_login->login_for_user_access_token($redirect_url);
                // $fb_login_button="<br><br>".$fb_login_button;
                $error= $this->lang->line('Domain failed to white-list. Requires pages_messaging permission to perform this operation. You need to login with Facebook again clicking the button below and then you can continue.');
            }
            else if(isset($domain_whitelist['error']['message']))
            {
                $error=$domain_whitelist['error']['message'];
            }
            echo json_encode(array('status'=>'0','message'=>$error.$fb_login_button));
            exit();
        }

        $domain_code = time().$this->user_id;
        // $reference=$reference."-".$domain_code;
        if($this->basic->is_exist("messenger_bot_engagement_send_to_msg",array("reference"=>$reference))) 
        {
            $unique_lang=$this->lang->line("is_unique");
            $unique_lang=str_replace('<b>%s</b>', $this->lang->line('reference'), $unique_lang);
            echo json_encode(array("status" => "0", "message" =>$unique_lang));
            exit();
        }
        $js_url=base_url('messenger_bot_enhancers/send_to_messenger_plugin.js?code='.$domain_code);
        $js_code='<div style="z-index:9999999;" id="SEND_TO_MESSENGER_PLUGIN_LOADER_01"></div><script type="text/javascript" src="'.$js_url.'"></script>';

        $this->db->trans_start(); 
       
        if(!isset($label_ids)) $label_ids=array();
        $insert_data=array
        (
            "domain_code"=>$domain_code,
            "user_id"=>$this->user_id,
            "page_id"=>$page,
            "domain_name"=>$domain_name,
            "btn_size"=>$btn_size,
            "skin"=>$skin,            
            "button_click_success_message"=>$button_click_success_message,
            "label_ids"=>implode(',',$label_ids),
            "reference"=>$reference,
            "template_id"=>$template_id,
            "language"=>$language,
            "cta_text_option"=>$cta_text_option,
            "created_at"=>date("Y-m-d H:i:s"),
            "redirect"=>$redirect,
            "add_button_with_message"=>$add_button_with_message,
            "button_with_message_content"=>json_encode($button_with_message_content),
            "success_redirect_url"=>$success_redirect_url
        );    

        $this->basic->insert_data("messenger_bot_engagement_send_to_msg",$insert_data);   

        $this->_insert_usage_log($module_id=214,$request=1);
        $this->db->trans_complete();

        if($this->db->trans_status() === false)
        {
             echo json_encode(array('status'=>'0','message'=> $this->lang->line('something went wrong, please try again.')));
             exit();
        }
        else
        {
            echo json_encode(array('status'=>'1','message'=> $this->lang->line('plugin has been created successfully.'),'js_code'=>$js_code));
            exit();
        } 
    }

    public function send_to_messenger_edit($id=0,$iframe='0')
    {
        if($id==0) exit();
        $this->is_broadcaster_exist=$this->broadcaster_exist();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(214,$this->module_access))
        redirect('home/login_page', 'location');

        $data['body'] = 'messenger_engagement/send_to_messenger_edit';
        $data['page_title'] = $this->lang->line("Edit Send to Messenger Plugin");
        $data['page_info'] = $this->get_user_page();
        $data['sdk_list'] = $this->sdk_locale();
        $data['cta_options'] = $this->get_cta_options();
        $data['btn_sizes'] = $this->basic->get_enum_values("messenger_bot_engagement_send_to_msg","btn_size");
        $xdata=$this->basic->get_data("messenger_bot_engagement_send_to_msg",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        //echo "<pre>";print_r($xdata);exit;
        if(!isset($xdata[0])) exit();
        $data['xdata']=$xdata[0];
        $data['iframe']=$iframe;
        $this->_viewcontroller($data);
    }

    public function send_to_messenger_edit_action()
    {
        $this->ajax_check(); 
        if($this->session->userdata('user_type') != 'Admin' && !in_array(214,$this->module_access)) exit();

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$this->input->post($key,true);
        }
        $button_click_success_message=str_replace('"',"'", $button_click_success_message);

        if(!isset($add_button_with_message)) $add_button_with_message='0';

        $button_with_message_content=array();
        if($redirect=='1')
        {
          $button_click_success_message="";
          $add_button_with_message='0';
        }
        else
        {
          $success_redirect_url="";
          if($add_button_with_message=='1')
          $button_with_message_content=array("success_button"=>$success_button,"success_url"=>$success_url,"success_button_bg_color"=>$success_button_bg_color,"success_button_color"=>$success_button_color,"success_button_bg_color_hover"=>$success_button_bg_color_hover,"success_button_color_hover"=>$success_button_color_hover);
        }
        
        if(!isset($label_ids)) $label_ids=array();
        $insert_data=array
        (
            "btn_size"=>$btn_size,
            "skin"=>$skin,            
            "button_click_success_message"=>$button_click_success_message,
            "label_ids"=>implode(',',$label_ids),
            "template_id"=>$template_id,
            "language"=>$language,
            "cta_text_option"=>$cta_text_option,
            "redirect"=>$redirect,
            "add_button_with_message"=>$add_button_with_message,
            "button_with_message_content"=>json_encode($button_with_message_content),
            "success_redirect_url"=>$success_redirect_url
        );    

        if($this->basic->update_data("messenger_bot_engagement_send_to_msg",array("id"=>$hidden_id,"user_id"=>$this->user_id),$insert_data))
            echo "1";
        else
            echo "0";

    }

    public function mme_link_list($page_id=0,$iframe='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(215,$this->module_access))
        redirect('home/login_page', 'location');

        $data['body'] = 'messenger_engagement/mme_link_list';
        $data['page_title'] = $this->lang->line("m.me link");
        
        $data['page_info'] = $this->get_user_page();
        // $data['label_info'] = $this->get_page_label();

        $data['page_id'] = $page_id;
        $data['iframe'] = $iframe;
        $data['media_type'] = $this->session->userdata('selected_global_media_type');

        $this->_viewcontroller($data);
    }

    public function mme_link_list_data()
    {

        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(215,$this->module_access)) exit();

        $pagename = trim($this->input->post("search_page_id",true));
        $search_ref_name = isset($_POST['search']) ? $_POST['search']['value'] : null;
        $display_columns = array("#",'id','page_name','link_code','actions','visual_flow_type','link_code','reference','created_at','label_names');
        $search_columns = array('page_name','reference','created_at');


        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple=array();

        if($pagename !="") 
          $where_simple['messenger_bot_engagement_mme.page_id'] = $pagename;
        if($search_ref_name !="")
          $where_simple['reference like'] = "%".$search_ref_name."%";

        $where_simple['messenger_bot_engagement_mme.user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);

        $table  = "messenger_bot_engagement_mme";
        $select = array("messenger_bot_engagement_mme.*","page_name","facebook_rx_fb_page_info.page_id as fb_page_id");  
        $join =array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot_engagement_mme.page_id,left");  

        $info = $this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');
      
        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];
        
        for($i=0;$i<count($info);$i++)
        {
            $info[$i]["page_name"] = "<a data-toggle='tooltip' data-original-title='".$this->lang->line('Visit Page')."' target='_BLANK' href='https://facebook.com/".$info[$i]["fb_page_id"]."'>".$info[$i]["page_name"]."</a>";
            $info[$i]['created_at'] = date('jS F y', strtotime($info[$i]['created_at']));

            $info[$i]['actions'] = '<div style="min-width:120px;">';
            if($info[$i]['visual_flow_type'] == 'flow')
            {
                $flow_campaign_exist = $this->basic->get_data('visual_flow_builder_campaign',['where'=>['id'=>$info[$i]['visual_flow_campaign_id'],'user_id'=>$this->user_id]],['id']);
                if(!empty($flow_campaign_exist))
                    $info[$i]['actions'] .= '<a target="_BLANK" class="btn btn-circle btn-outline-warning" href="'.base_url()."visual_flow_builder/edit_builder_data/".$info[$i]['visual_flow_campaign_id'].'/6" title="'.$this->lang->line('Edit').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-edit"></i></a> &nbsp;';
                else
                    $info[$i]['actions'] .= '<a class="btn btn-circle btn-outline-warning" href="'.base_url()."messenger_bot_enhancers/mme_link_edit/".$info[$i]['id'].'/1" title="'.$this->lang->line('Edit').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-edit"></i></a> &nbsp;';
            }
            else
                $info[$i]['actions'] .= '<a class="btn btn-circle btn-outline-warning" href="'.base_url()."messenger_bot_enhancers/mme_link_edit/".$info[$i]['id'].'/1" title="'.$this->lang->line('Edit').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-edit"></i></a> &nbsp;';

            if($info[$i]['visual_flow_type'] == 'general') {
                $info[$i]['actions'] .= '<a class="btn btn-circle btn-outline-danger delete_campaign" href="#" title="'.$this->lang->line('delete').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-trash-alt"></i></a>';
            }
            $info[$i]['actions'] .= "</div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";

            $label_ids=$info[$i]["label_ids"];
            $label_names="";
            if($label_ids!="")
            {
                $label_ids_array=explode(',', $label_ids);
                $label_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where_in"=>array("id"=>$label_ids_array)));
                $label_names_array=array_column($label_data, 'group_name');
                $label_names=implode(', ', $label_names_array);
            }
            $info[$i]["label_names"]=$label_names;
            $info[$i]['visual_flow_type'] = ucfirst($info[$i]['visual_flow_type']);

        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function mme_link_js_code()
    {
       $this->ajax_check();
       if($this->session->userdata('user_type') != 'Admin' && !in_array(215,$this->module_access)) exit();

       $id=$this->input->post("campaign_id");

       $plugin_data=$this->basic->get_data("messenger_bot_engagement_mme",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
       $link_code=isset($plugin_data[0]["link_code"])?$plugin_data[0]["link_code"]:"";       
       $page_id=isset($plugin_data[0]["page_id"])?$plugin_data[0]["page_id"]:"";       
       $reference=isset($plugin_data[0]["reference"])?$plugin_data[0]["reference"]:"";         

       $str1 ='<div style="z-index:9999999;" id="MME_LINK_LOADER_01"></div><script type="text/javascript" src="'.base_url('messenger_bot_enhancers/mme_link.js?code='.$link_code).'"></script>';

       $page_info=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id)));
       $username=isset($page_info[0]["username"]) ? $page_info[0]["username"] : "";
       $fb_id=isset($page_info[0]["page_id"]) ? $page_info[0]["page_id"] : "";

       $value=($username!="")?$username:$fb_id;

       $str2='https://m.me/'.$value.'?ref='.urlencode($reference);

       echo json_encode(array("str1"=>$str1,"str2"=>$str2));
    }


    public function mme_link_qr_code()
    {
       $this->ajax_check();
       if($this->session->userdata('user_type') != 'Admin' && !in_array(215,$this->module_access)) exit();

       $id=$this->input->post("campaign_id");

       $plugin_data=$this->basic->get_data("messenger_bot_engagement_mme",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
       $link_code=isset($plugin_data[0]["link_code"])?$plugin_data[0]["link_code"]:"";       
       $page_id=isset($plugin_data[0]["page_id"])?$plugin_data[0]["page_id"]:"";       
       $reference=isset($plugin_data[0]["reference"])?$plugin_data[0]["reference"]:"";         

       $page_info=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id)));
       $username=isset($page_info[0]["username"]) ? $page_info[0]["username"] : "";
       $fb_id=isset($page_info[0]["page_id"]) ? $page_info[0]["page_id"] : "";

       $value=($username!="")?$username:$fb_id;

       $str2='https://m.me/'.$value.'?ref='.urlencode($reference);

       $this->load->library('quick_response_code');
       $qrc = $this->quick_response_code;
       // header('Content-Type: image/png');
       $filename = $this->user_id."_".time().".png";
       $qrc->create($str2,$filename,$qrc::QRC_ECLEVEL_L,8,1);
       echo '<img src="'.base_url().'upload/qrc/'.$filename.'">';
    }

    public function mme_link_delete($id=0)
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(215,$this->module_access)) exit();

        $id = $this->input->post('campaign_id',true);
        $response = array();

        $this->db->trans_start();
        $xdata=$this->basic->get_data("messenger_bot_engagement_mme",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $link_code=isset($xdata[0]['link_code'])?$xdata[0]['link_code']:"";
        if($link_code=="")
        {
          $response['status'] = '0';
          $response['message'] = $this->lang->line('Something went wrong.');
        }
        
        $this->basic->delete_data('messenger_bot_engagement_mme',$where=array('id'=>$id));

        //******************************//
        // delete data to useges log table
        $this->_delete_usage_log($module_id=215,$request=1);   
        //******************************//

        $this->db->trans_complete();
        if($this->db->trans_status() === false) 
        {
          $response['status'] = '0';
          $response['message'] = $this->lang->line('Something went wrong.');
        } 
        else 
        {
          $response['status'] = '1';
          $response['message'] = $this->lang->line('Plugin has been deleted successfully.');
        }
        echo json_encode($response);
    }

    public function mme_link_add($page_id=0,$iframe='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(215,$this->module_access))
        redirect('home/login_page', 'location');
        $this->is_broadcaster_exist=$this->broadcaster_exist();
        $data['body'] = 'messenger_engagement/mme_link_add';
        $data['page_title'] = $this->lang->line("Add m.me link");
        $data['page_info'] = $this->get_user_page();
        $data['page_id'] = $page_id;
        $data['iframe'] = $iframe;
        $data['btn_sizes'] = $this->basic->get_enum_values("messenger_bot_engagement_mme","btn_size");
        $this->_viewcontroller($data);
    }

    public function mme_link_add_action()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(215,$this->module_access)) exit();

        $status=$this->_check_usage($module_id=215,$request=1);
        if($status=="3")  //monthly limit is exceeded, can not create another campaign this month
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("limit has been exceeded. you can no longer use this feature.")));
            exit();
        }

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$this->input->post($key,true);
        }

        $pageinfo=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page)));
        $access_token=isset($pageinfo[0]["page_access_token"])?$pageinfo[0]["page_access_token"]:"";
        
        if($access_token=="")
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Facebook page not found.')));
            exit();
        }


        // if($this->basic->is_exist("messenger_bot_engagement_mme",array("page_id"=>$page),$select='id'))
        // {
        //     echo json_encode(array('status'=>'0','message'=>"".$this->lang->line("Plugin has been already generated for this page.")));
        //     exit();
        // }

        $link_code = $this->_random_number_generator(8);
        // $reference=$reference."-".$link_code;
        if($this->basic->is_exist("messenger_bot_engagement_mme",array("reference"=>$reference))) 
        {
            $unique_lang=$this->lang->line("is_unique");
            $unique_lang=str_replace('<b>%s</b>', $this->lang->line('reference'), $unique_lang);
            echo json_encode(array("status" => "0", "message" =>$unique_lang));
            exit();
        }
        $js_url=base_url('messenger_bot_enhancers/mme_link.js?code='.$link_code);
        $js_code='<div style="z-index:9999999;" id="MME_LINK_LOADER_01"></div><script type="text/javascript" src="'.$js_url.'"></script>';

        $username=isset($pageinfo[0]["username"]) ? $pageinfo[0]["username"] : "";
        $fb_id=isset($pageinfo[0]["page_id"]) ? $pageinfo[0]["page_id"] : "";
        $value=($username!="")?$username:$fb_id;
        $js_code2='https://m.me/'.$value.'?ref='.urlencode($reference);

        $this->db->trans_start(); 

        if(!isset($label_ids)) $label_ids=array();
        $insert_data=array
        (
            "link_code"=>$link_code,
            "user_id"=>$this->user_id,
            "page_id"=>$page,            
            "btn_size"=>$btn_size,   
            "new_button_display"=>$new_button_display,
            "new_button_bg_color"=>$new_button_bg_color,
            "new_button_bg_color_hover"=>$new_button_bg_color_hover,
            "new_button_color"=>$new_button_color,
            "new_button_color_hover"=>$new_button_color_hover,            
            "label_ids"=>implode(',',$label_ids),
            "reference"=>$reference,
            "template_id"=>$template_id,
            "created_at"=>date("Y-m-d H:i:s")
        );    

        $this->basic->insert_data("messenger_bot_engagement_mme",$insert_data);   

        $this->_insert_usage_log($module_id=215,$request=1);
        $this->db->trans_complete();

        if($this->db->trans_status() === false)
        {
             echo json_encode(array('status'=>'0','message'=>"".$this->lang->line('something went wrong, please try again.')));
             exit();
        }
        else
        {
            echo json_encode(array('status'=>'1','message'=>"<i class='fa fa-check'></i> ".$this->lang->line('plugin has been created successfully.'),'js_code'=>$js_code,'js_code2'=>$js_code2));
            exit();
        } 
    }

    public function mme_link_edit($id=0,$iframe='0')
    {
        if($id==0) exit();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(215,$this->module_access))
        redirect('home/login_page', 'location');
        $this->is_broadcaster_exist=$this->broadcaster_exist(); 
        $data['body'] = 'messenger_engagement/mme_link_edit';
        $data['page_title'] = $this->lang->line("Edit m.me link");
        $data['page_info'] = $this->get_user_page();
        $data['btn_sizes'] = $this->basic->get_enum_values("messenger_bot_engagement_mme","btn_size");
        $xdata=$this->basic->get_data("messenger_bot_engagement_mme",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        if(!isset($xdata[0])) exit();
        $data['xdata']=$xdata[0];
        $data['iframe']=$iframe;
        $this->_viewcontroller($data);
    }

    public function mme_link_edit_action()
    {        
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(215,$this->module_access)) exit();
        $this->is_broadcaster_exist=$this->broadcaster_exist(); 
        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$this->input->post($key,true);
        }      

        if(!isset($label_ids)) $label_ids=array();
        $insert_data=array
        (                   
            "btn_size"=>$btn_size,   
            "new_button_display"=>$new_button_display,
            "new_button_bg_color"=>$new_button_bg_color,
            "new_button_bg_color_hover"=>$new_button_bg_color_hover,
            "new_button_color"=>$new_button_color,
            "new_button_color_hover"=>$new_button_color_hover,            
            "label_ids"=>implode(',',$label_ids),
            "template_id"=>$template_id
        );    

        if($this->basic->update_data("messenger_bot_engagement_mme",array("id"=>$hidden_id,"user_id"=>$this->user_id),$insert_data))   
          echo "1";
        else
          echo "0";
    }


    public function customer_chat_plugin_list($page_id=0,$iframe='0') // customer chat plugin
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(217,$this->module_access))
        redirect('home/login_page', 'location');

        $data['body'] = 'messenger_engagement/customer_chat_list';
        $data['page_title'] = $this->lang->line("Customer Chat Plugin");
        $data['page_info'] = $this->get_user_page();

        $data['page_id'] = $page_id;
        $data['iframe'] = $iframe;
        $data['media_type'] = $this->session->userdata('selected_global_media_type');

        $this->_viewcontroller($data);
    }

    public function customer_chat_plugin_list_data()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(217,$this->module_access)) exit();

        $pagename = trim($this->input->post("search_page_id",true));
        $search_ref_name = isset($_POST['search']) ? $_POST['search']['value'] : null;
        $display_columns = array("#",'id','domain_name','page_name','domain_code','domain_code','actions','visual_flow_type','add_date','language');
        $search_columns = array('page_name','domain_name','created_at');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;


        $where_simple=array();

        if($pagename !="") 
          $where_simple['messenger_bot_engagement_2way_chat_plugin.page_auto_id'] = $pagename;
        if($search_ref_name !="")
          $where_simple['domain_name like'] = "%".$search_ref_name."%";

        $where_simple['messenger_bot_engagement_2way_chat_plugin.user_id'] = $this->user_id;
        $where_simple['facebook_rx_fb_page_info.deleted'] = '0';
        $where  = array('where'=>$where_simple);

        $table = "messenger_bot_engagement_2way_chat_plugin";
        $select = array("messenger_bot_engagement_2way_chat_plugin.*","page_name","page_id");
        $join =array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot_engagement_2way_chat_plugin.page_auto_id,left");      
        $info = $this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');
        //echo "<pre>";print_r($info);exit;
        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        for($i=0;$i<count($info);$i++)
        {
            $info[$i]["domain_name"] = "<a data-toggle='tooltip' data-original-title='".$this->lang->line('Visit website')."' target='_BLANK' href='".addHttp($info[$i]["domain_name"])."'>".$info[$i]["domain_name"]."</a>";

            $info[$i]["page_name"] = "<a data-toggle='tooltip' data-original-title='".$this->lang->line('Visit Page')."' target='_BLANK' href='https://facebook.com/".$info[$i]["page_id"]."'>".$info[$i]["page_name"]."</a>";
            $info[$i]['add_date'] = date('jS F y', strtotime($info[$i]['add_date']));

            $info[$i]['actions'] = '<div style="min-width:140px;">';
            if($info[$i]['visual_flow_type'] == 'flow')
            {
                $flow_campaign_exist = $this->basic->get_data('visual_flow_builder_campaign',['where'=>['id'=>$info[$i]['visual_flow_campaign_id'],'user_id'=>$this->user_id]],['id']);
                if(!empty($flow_campaign_exist))
                    $info[$i]['actions'] .= '<a target="_BLANK" class="btn btn-circle btn-outline-warning" href="'.base_url()."visual_flow_builder/edit_builder_data/".$info[$i]['visual_flow_campaign_id'].'/7" title="'.$this->lang->line('Edit').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-edit"></i></a> &nbsp;';
                else
                    $info[$i]['actions'] .= '<a class="btn btn-circle btn-outline-warning" href="'.base_url()."messenger_bot_enhancers/customer_chat_edit/".$info[$i]['id'].'/1" title="'.$this->lang->line('Edit').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-edit"></i></a> &nbsp;';
            }
            else
                $info[$i]['actions'] .= '<a class="btn btn-circle btn-outline-warning" href="'.base_url()."messenger_bot_enhancers/customer_chat_edit/".$info[$i]['id'].'/1" title="'.$this->lang->line('Edit').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-edit"></i></a> &nbsp;';



            $info[$i]['actions'] .= '<a class="btn btn-circle btn-outline-success" target="_blank" href="'.base_url()."messenger_bot_enhancers/wp_plugin2/".$info[$i]['page_auto_id'].'/'.$info[$i]['id'].'" title="'.$this->lang->line('Download Wordpress Plugin').'" campaign_id='.$info[$i]['id'].'><i class="fa fa-wordpress"></i></a> &nbsp;';

            if($info[$i]['visual_flow_type'] == 'general') {
                $info[$i]['actions'] .= '<a class="btn btn-circle btn-outline-danger delete_campaign" href="#" title="'.$this->lang->line('delete').'" campaign_id='.$info[$i]['id'].'><i class="fas fa-trash-alt"></i></a>';
            }

            $info[$i]['actions'] .= "</div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";

            $info[$i]['visual_flow_type'] = ucfirst($info[$i]['visual_flow_type']);
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }
    
    public function customer_chat_js_code()
    {
       $this->ajax_check();
       if($this->session->userdata('user_type') != 'Admin' && !in_array(217,$this->module_access)) exit();

       $id=$this->input->post("campaign_id");
       $plugin_data=$this->basic->get_data("messenger_bot_engagement_2way_chat_plugin",array("where"=>array("id"=>$id)));
       $domain_code=isset($plugin_data[0]["domain_code"])?$plugin_data[0]["domain_code"]:"";
       $page_auto_id=isset($plugin_data[0]["page_auto_id"])?$plugin_data[0]["page_auto_id"]:"";
       $minimized=isset($plugin_data[0]["minimized"])?$plugin_data[0]["minimized"]:"default";
       $reference=isset($plugin_data[0]["reference"])?$plugin_data[0]["reference"]:"";
       $delay=isset($plugin_data[0]["delay"])?$plugin_data[0]["delay"]:"";
       $color=isset($plugin_data[0]["color"])?$plugin_data[0]["color"]:"";
       $logged_in=isset($plugin_data[0]["logged_in"])?$plugin_data[0]["logged_in"]:"";
       $logged_out=isset($plugin_data[0]["logged_out"])?$plugin_data[0]["logged_out"]:"";

       $pagetable="facebook_rx_fb_page_info";
       $page_data=$this->basic->get_data($pagetable,array("where"=>array("id"=>$page_auto_id)));
       $PAGE_ID=isset($page_data[0]["page_id"])?$page_data[0]["page_id"]:"";

       $new_feture="";
       if($color!="")    $new_feture.=' theme_color="'.$color.'"'; 
       if($logged_in!="") $new_feture.=' logged_in_greeting="'.$logged_in.'"'; 
       if($logged_out!="") $new_feture.=' logged_out_greeting="'.$logged_out.'"';
       if($delay!="" && $delay>0) $new_feture.=' greeting_dialog_delay="'.$delay.'"';

       $str='<script type="text/javascript" src="'.base_url('js/2waychat/plugin-'.$domain_code.'.js').'"></script>';
       $str.='<div style="z-index:9999999"><div class="fb-customerchat" page_id="'.$PAGE_ID.'" ref="'.$reference.'" greeting_dialog_display="'.$minimized.'"'.$new_feture.'></div></div>';  
       echo $str;
    }

    public function customer_chat_add($page_id=0,$iframe='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(217,$this->module_access))
        redirect('home/login_page', 'location');
        $this->is_broadcaster_exist=$this->broadcaster_exist();
        $data['body'] = 'messenger_engagement/customer_chat_add';
        $data['page_title'] = $this->lang->line("Add Customer Chat Plugin");
        $data['sdk_locale']=$this->sdk_locale();
        $data['page_info'] = $this->get_user_page();
        $data['load_chatbox']=array
        (
          'hide'=>$this->lang->line('hide'),
          'show'=>$this->lang->line('show'),
          'fade'=>$this->lang->line('fade')
        );

        $data['page_id'] = $page_id;
        $data['iframe'] = $iframe;
        $this->_viewcontroller($data);
    }

    public function customer_chat_add_action()
    {

        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(217,$this->module_access)) exit();

        $status=$this->_check_usage($module_id=217,$request=1);
        if($status=="3")  //monthly limit is exceeded, can not create another campaign this month
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("limit has been exceeded. you can no longer use this feature.")));
            exit();
        }

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$this->input->post($key,true);
        }
    

        $pageinfo=$this->basic->get_data($this->page_table_name,array("where"=>array("id"=>$page)));
        $access_token=isset($pageinfo[0]["page_access_token"])?$pageinfo[0]["page_access_token"]:"";
        
        if($access_token=="")
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Facebook page not found.')));
            exit();
        }

        // if($this->basic->is_exist("messenger_bot_engagement_2way_chat_plugin",array("domain_name"=>$domain_name,"page_auto_id"=>$page),$select='id'))
        // {
        //     echo json_encode(array('status'=>'0','message'=>"".$this->lang->line("Chat plugin for this page & website has already been generated before.")));
        //     exit();
        // }

        // $strpos=strpos($domain_name,'https://');
        // if($strpos===FALSE)
        // {
        //     echo json_encode(array('status'=>'0','message'=>"".$this->lang->line("The website you have entered is not HTTPS, this operation needs HTTPS website to work.")));
        //     exit();
        // }

        $PAGE_ID=isset($pageinfo[0]["page_id"]) ? $pageinfo[0]["page_id"] : "";
        $LOCALE=$language;
        $where2=array();
        $where2['where'] = array($this->fb_user_info_table_name.'.id'=>$this->user_info_session,$this->fb_rx_config_table_name.".status"=>'1',$this->fb_rx_config_table_name.".deleted"=>'0');
        $join=array($this->fb_rx_config_table_name=>$this->fb_user_info_table_name.".".$this->facebook_rx_config_id."=".$this->fb_rx_config_table_name.".id,left");
        $app_info = $this->basic->get_data($this->fb_user_info_table_name,$where2,array('api_id'),$join);
        $APP_ID=isset($app_info[0]['api_id']) ? $app_info[0]['api_id']: '';
        $DONOT_SHOW_IF_NOT_LOGIN=($donot_show_if_not_login=='0')?'false':'true';

        $this->load->library('fb_rx_login');
        $domain_whitelist=$this->fb_rx_login->domain_whitelist($access_token,$domain_name);

        if(!isset($domain_whitelist['status']) || $domain_whitelist['status']=='0')
        {
            $fb_login_button='';
            $error=$this->lang->line("Error in Domain Whitelisting ").$domain_whitelist['result'];
            if(isset($domain_whitelist['error']['code']) && trim($domain_whitelist['error']['code'])=='230') //does not have page_messages permission, need to login again
            {
                // $redirect_url = base_url()."home/redirect_rx_link";
                // $fb_login_button = $this->fb_rx_login->login_for_user_access_token($redirect_url);
                // $fb_login_button="<br><br>".$fb_login_button;
                $error= $this->lang->line('Domain failed to white-list. Requires pages_messaging permission to perform this operation. You need to login with Facebook again clicking the button below and then you can continue.');
            }
            else if(isset($domain_whitelist['error']['message']))
            {
                $error=$domain_whitelist['error']['message'];
            }
            echo json_encode(array('status'=>'0','message'=>$error.$fb_login_button));
            exit();
        }
       
        $this->db->trans_start(); 
        $domain_code = time().$this->user_id;
        // $reference=$reference."-".$domain_code;

        if($this->basic->is_exist("messenger_bot_engagement_2way_chat_plugin",array("reference"=>$reference))) 
        {
            $unique_lang=$this->lang->line("is_unique");
            $unique_lang=str_replace('<b>%s</b>', $this->lang->line('reference'), $unique_lang);
            echo json_encode(array("status" => "0", "message" =>$unique_lang));
            exit();
        }

        if(!isset($label_ids)) $label_ids=array();

        $data = array(
            'user_id' => $this->user_id,
            'page_auto_id'=>$page,
            'facebook_rx_fb_user_info_id'=>$this->user_info_session,
            'domain_name' => $domain_name,
            'domain_code' => $domain_code,
            'add_date' => date("Y-m-d H:i:s"),
            'language'=>$language,
            'minimized'=>$minimized,
            'logged_in'=>$logged_in,
            'logged_out'=>$logged_out,
            'color'=>$color,
            "label_ids"=>implode(',',$label_ids),
            "reference"=>$reference,
            "template_id"=>$template_id,
            'donot_show_if_not_login'=>$donot_show_if_not_login,
            'delay'=>$delay
            );        

        $this->basic->insert_data('messenger_bot_engagement_2way_chat_plugin',$data);
        $last_id = $this->db->insert_id();
        $new_feture="";
        if($color!="")    $new_feture.=' theme_color="'.$color.'"'; 
        if($logged_in!="") $new_feture.=' logged_in_greeting="'.$logged_in.'"'; 
        if($logged_out!="") $new_feture.=' logged_out_greeting="'.$logged_out.'"';
        if($delay!="" && $delay>0) $new_feture.=' greeting_dialog_delay="'.$delay.'"';

        $where_update = array('id' => $last_id);
        $update_code = $last_id.$domain_code;
        $data_update = array('domain_code'=>$update_code);
        $this->basic->update_data('messenger_bot_engagement_2way_chat_plugin',$where_update,$data_update);
        $js_code = '<script type="text/javascript" src="'.base_url('js/2waychat/plugin-'.$update_code.'.js').'"></script>';    
        $js_code.='<div style="z-index:9999999"><div class="fb-customerchat" page_id="'.$PAGE_ID.'" ref="'.$reference.'" greeting_dialog_display="'.$minimized.'"'.$new_feture.'></div></div>';   
       
        $this->_insert_usage_log($module_id=217,$request=1);   

        $this->db->trans_complete();

        if($this->db->trans_status() === false)
        {
             echo json_encode(array('status'=>'0','message'=>"".$this->lang->line('something went wrong, please try again.')));
             exit();
        } 
        else 
        {
            $chat_plugin_js=file_get_contents(APPPATH.'modules/messenger_bot_enhancers/chat-js-base.txt',true);
            $chat_plugin_js_new=str_replace("APP_ID",$APP_ID, $chat_plugin_js);
            $chat_plugin_js_new=str_replace("LOCALE",$LOCALE, $chat_plugin_js_new);
            $chat_plugin_js_new=str_replace("{DONOT_SHOW_IF_NOT_LOGIN}",$DONOT_SHOW_IF_NOT_LOGIN, $chat_plugin_js_new);
            file_put_contents('js/2waychat/plugin-'.$update_code.'.js', $chat_plugin_js_new, LOCK_EX);

            $name="easyembedchat";
            $name2="EasyEmbedChat-".$this->user_id;
            $chat_plugin_js2=file_get_contents(APPPATH.'modules/messenger_bot_enhancers/chat-js-base2.txt',true);
            $chat_plugin_js_new2=str_replace("APP_ID",$APP_ID, $chat_plugin_js2);
            $chat_plugin_js_new2=str_replace("PAGE_ID",$PAGE_ID, $chat_plugin_js_new2);
            $chat_plugin_js_new2=str_replace("LOCALE",$LOCALE, $chat_plugin_js_new2);
            $chat_plugin_js_new2=str_replace("GREETING_DIALOG_DISPLAY",$minimized, $chat_plugin_js_new2);
            $chat_plugin_js_new2=str_replace("REFERENCE",$reference, $chat_plugin_js_new2);
            $chat_plugin_js_new2=str_replace("{DONOT_SHOW_IF_NOT_LOGIN}",$DONOT_SHOW_IF_NOT_LOGIN, $chat_plugin_js_new2);

            $color_replace=$logged_in_replace=$logged_out_replace=$greeting_dialog_delay_replace="";
            if($color!="")    $color_replace=' theme_color="'.$color.'"'; 
            if($logged_in!="") $logged_in_replace=' logged_in_greeting="'.$logged_in.'"'; 
            if($logged_out!="") $logged_out_replace=' logged_out_greeting="'.$logged_out.'"';
            if($delay!="" && $delay>0) $greeting_dialog_delay_replace=' greeting_dialog_delay="'.$delay.'"';
            $chat_plugin_js_new2=str_replace("{COLOR_PARAM}",$color_replace, $chat_plugin_js_new2);
            $chat_plugin_js_new2=str_replace("{LOGGED_IN_PARAM}",$logged_in_replace, $chat_plugin_js_new2);
            $chat_plugin_js_new2=str_replace("{LOGGED_OUT_PARAM}",$logged_out_replace, $chat_plugin_js_new2);
            $chat_plugin_js_new2=str_replace("{GREETING_DIALOG_DELAY_PARAM}",$greeting_dialog_delay_replace, $chat_plugin_js_new2);

            $wp_content=file_get_contents(APPPATH.'modules/messenger_bot_enhancers/fb-chat-wp.txt',true);
            $wp_content=str_replace("LOAD_CHAT_CODE_HERE",$chat_plugin_js_new2, $wp_content);
            // file_put_contents('download/'.$name.'.php', $wp_content, LOCK_EX);

            if(!class_exists('ZipArchive'))
            {
               $download_url=base_url('messenger_bot_enhancers/zip_error');
            }
            else
            {
                $zip = new ZipArchive;
                if ($zip->open('download/'.$name2.'.zip', ZipArchive::CREATE) === TRUE)
                {
                    $zip->addFile($name.'/'.$name.'.php');
                    $zip->addFromString($name.'/'.$name.'.php', $wp_content);
                    $zip->close();
                }
                $download_url=base_url('download/'.$name2.'.zip');
            }

            echo json_encode(array('status'=>'1','message'=>"<i class='fa fa-check'></i> ".$this->lang->line('Chat plugin has been created successfully. Now copy the embed code and paste it in your webpage.')." <a href='".base_url("messenger_bot_enhancers/customer_chat_plugin_list")."'>".$this->lang->line('go to list')."</a>",'js_code'=>$js_code,"wp_plugin"=>$download_url));
        }
    }

    public function zip_error($value='')
    {
        echo "<h2 class='text-align:center;color:red;border:1px solid red;padding:20px;margin-top:30px;'>".$this->lang->line("EasyEmbedChat plugin for WordPress can not be generated beacuse PHP ZipArchive class is not installed.");
    }

    public function wp_plugin2($page='',$auto_id='')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(217,$this->module_access)) exit();

        $xdata=$this->basic->get_data("messenger_bot_engagement_2way_chat_plugin",array("where"=>array("id"=>$auto_id)));
        if(!isset($xdata[0])) exit();

        $pageinfo=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page)));
        $access_token=isset($pageinfo[0]["page_access_token"])?$pageinfo[0]["page_access_token"]:"";

        $PAGE_ID=isset($pageinfo[0]["page_id"]) ? $pageinfo[0]["page_id"] : "";
        $LOCALE=isset($xdata[0]["language"])?$xdata[0]["language"]:"en_US";
        $minimized=isset($xdata[0]["minimized"])?$xdata[0]["minimized"]:"default";
        $reference=isset($xdata[0]["reference"])?$xdata[0]["reference"]:"";
        $delay=isset($xdata[0]["delay"])?$xdata[0]["delay"]:0;
        $DONOT_SHOW_IF_NOT_LOGIN=($xdata[0]["donot_show_if_not_login"]=='0')?'false':'true';
        $where2=array();
        $where2['where'] = array('facebook_rx_fb_user_info.id'=>$this->session->userdata("facebook_rx_fb_user_info"),"facebook_rx_config.status"=>'1',"facebook_rx_config.deleted"=>'0');
        $join=array('facebook_rx_config'=>"facebook_rx_fb_user_info.facebook_rx_config_id=facebook_rx_config.id,left");
        $app_info = $this->basic->get_data('facebook_rx_fb_user_info',$where2,array('api_id'),$join);
        $APP_ID=isset($app_info[0]['api_id']) ? $app_info[0]['api_id']: '';

        $color=isset($xdata[0]["color"]) ? $xdata[0]["color"] : "";
        $logged_in=isset($xdata[0]["logged_in"]) ? $xdata[0]["logged_in"] : "";
        $logged_out=isset($xdata[0]["logged_out"]) ? $xdata[0]["logged_out"] : "";
        
        $name="easyembedchat";
        $name2="EasyEmbedChat-".$this->user_id;
        $chat_plugin_js2=file_get_contents(APPPATH.'modules/messenger_bot_enhancers/chat-js-base2.txt',true);
        $chat_plugin_js_new2=str_replace("APP_ID",$APP_ID, $chat_plugin_js2);
        $chat_plugin_js_new2=str_replace("PAGE_ID",$PAGE_ID, $chat_plugin_js_new2);
        $chat_plugin_js_new2=str_replace("LOCALE",$LOCALE, $chat_plugin_js_new2);
        $chat_plugin_js_new2=str_replace("GREETING_DIALOG_DISPLAY",$minimized, $chat_plugin_js_new2);
        $chat_plugin_js_new2=str_replace("REFERENCE",$reference, $chat_plugin_js_new2);
        $chat_plugin_js_new2=str_replace("{DONOT_SHOW_IF_NOT_LOGIN}",$DONOT_SHOW_IF_NOT_LOGIN, $chat_plugin_js_new2);

        $color_replace=$logged_in_replace=$logged_out_replace=$greeting_dialog_delay_replace="";
        if($color!="")    $color_replace=' theme_color="'.$color.'"'; 
        if($logged_in!="") $logged_in_replace=' logged_in_greeting="'.$logged_in.'"'; 
        if($logged_out!="") $logged_out_replace=' logged_out_greeting="'.$logged_out.'"';
        if($delay!="" && $delay>0) $greeting_dialog_delay_replace=' greeting_dialog_delay="'.$delay.'"';
        $chat_plugin_js_new2=str_replace("{COLOR_PARAM}",$color_replace, $chat_plugin_js_new2);
        $chat_plugin_js_new2=str_replace("{LOGGED_IN_PARAM}",$logged_in_replace, $chat_plugin_js_new2);
        $chat_plugin_js_new2=str_replace("{LOGGED_OUT_PARAM}",$logged_out_replace, $chat_plugin_js_new2);
        $chat_plugin_js_new2=str_replace("{GREETING_DIALOG_DELAY_PARAM}",$greeting_dialog_delay_replace, $chat_plugin_js_new2);

        $wp_content=file_get_contents(APPPATH.'modules/messenger_bot_enhancers/fb-chat-wp.txt',true);
        $wp_content=str_replace("LOAD_CHAT_CODE_HERE",$chat_plugin_js_new2, $wp_content);
        // file_put_contents('download/'.$name.'.php', $wp_content, LOCK_EX);

        if(!class_exists('ZipArchive'))
        {
           $download_url=base_url('messenger_bot_enhancers/zip_error');
        }  
        else
        {
            $zip = new ZipArchive;
            if ($zip->open('download/'.$name2.'.zip', ZipArchive::CREATE) === TRUE)
            {
                $zip->addFile($name.'/'.$name.'.php');
                $zip->addFromString($name.'/'.$name.'.php', $wp_content);

                $zip->close();
            }
            $download_url=base_url('download/'.$name2.'.zip');
        }      
        
        redirect($download_url,'location');
     
    }

    public function customer_chat_edit($id=0,$iframe='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(217,$this->module_access)) redirect('home/login_page', 'location');
        $this->is_broadcaster_exist=$this->broadcaster_exist();

        $xdata=$this->basic->get_data("messenger_bot_engagement_2way_chat_plugin",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        if(!isset($xdata[0])) exit();
        $data['xdata']=$xdata[0];
        $data['body'] = 'messenger_engagement/customer_chat_edit';
        $data['page_title'] = $this->lang->line("Edit Customer Chat Plugin");
        $data['sdk_locale']=$this->sdk_locale();
        $data['page_info'] = $this->get_user_page();
        $data['load_chatbox']=array
        (
          'hide'=>$this->lang->line('hide'),
          'show'=>$this->lang->line('show'),
          'fade'=>$this->lang->line('fade')
        );
        $data['iframe'] = $iframe;
        $this->_viewcontroller($data);
    }

    public function customer_chat_edit_action()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(217,$this->module_access)) exit();

        $id=$this->input->post("hidden_id");
        $response = array();
        $xdata=$this->basic->get_data("messenger_bot_engagement_2way_chat_plugin",array("where"=>array("id"=>$id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        if(!isset($xdata[0]))
        {
            $response['status'] = '0';
            $response['message'] = $this->lang->line('Something went wrong.');
        }

        $domain_name = $xdata[0]['domain_name'];
        $page = $xdata[0]['page_auto_id'];
        $domain_code = $xdata[0]['domain_code'];
        $language = $this->input->post('language', true);
        $minimized = $this->input->post('minimized', true);
        $logged_in = $this->input->post('logged_in', true);
        $logged_out = $this->input->post('logged_out', true);
        $color = $this->input->post('color', true);
        $label_ids=$this->input->post("label_ids",true);
        $reference=$this->input->post("reference",true);
        $template_id=$this->input->post("template_id",true);
        $donot_show_if_not_login=$this->input->post("donot_show_if_not_login",true);
        $delay=$this->input->post("delay",true);


        $pageinfo=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page)));
        $access_token=isset($pageinfo[0]["page_access_token"])?$pageinfo[0]["page_access_token"]:"";
        
        if($access_token=="")
        {
          $response['status'] = '0';
          $response['message'] = $this->lang->line('Something went wrong.');
        }
    
        $LOCALE=$language;
        $where2=array();
        $where2['where'] = array('facebook_rx_fb_user_info.id'=>$this->session->userdata("facebook_rx_fb_user_info"),"facebook_rx_config.status"=>'1',"facebook_rx_config.deleted"=>'0');
        $join=array('facebook_rx_config'=>"facebook_rx_fb_user_info.facebook_rx_config_id=facebook_rx_config.id,left");
        $app_info = $this->basic->get_data('facebook_rx_fb_user_info',$where2,array('api_id'),$join);
        $APP_ID=isset($app_info[0]['api_id']) ? $app_info[0]['api_id']: '';
        $DONOT_SHOW_IF_NOT_LOGIN=($donot_show_if_not_login=='0')?'false':'true';

         $data = array
         (            
          'language'=>$language,
          'minimized'=>$minimized,
          'logged_in'=>$logged_in,
          'logged_out'=>$logged_out,
          'color'=>$color,
          "label_ids"=>implode(',',$label_ids),
          "template_id"=>$template_id,
          'donot_show_if_not_login'=>$donot_show_if_not_login,
          'delay'=>$delay
          );        

        $this->basic->update_data('messenger_bot_engagement_2way_chat_plugin',array("id"=>$id),$data);

        $chat_plugin_js=file_get_contents(APPPATH.'modules/messenger_bot_enhancers/chat-js-base.txt',true);
        $chat_plugin_js_new=str_replace("APP_ID",$APP_ID, $chat_plugin_js);
        $chat_plugin_js_new=str_replace("LOCALE",$LOCALE, $chat_plugin_js_new);
        $chat_plugin_js_new=str_replace("{DONOT_SHOW_IF_NOT_LOGIN}",$DONOT_SHOW_IF_NOT_LOGIN, $chat_plugin_js_new);
        file_put_contents('js/2waychat/plugin-'.$domain_code.'.js', $chat_plugin_js_new, LOCK_EX);

        $response['status'] = '1';
        $response['message'] = $this->lang->line('Plugin has been updated successfully.');
        echo json_encode($response);


    }

    public function customer_chat_delete($id=0)
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(217,$this->module_access)) exit();

        $id = $this->input->post('campaign_id',true);
        $response = array();
        $this->db->trans_start();
        $xdata=$this->basic->get_data("messenger_bot_engagement_2way_chat_plugin",array("where"=>array("id"=>$id)));
        $domain_code=isset($xdata[0]['domain_code'])?$xdata[0]['domain_code']:"";

        if($domain_code == "")
        {
          $response['status'] = '0';
          $response['message'] = $this->lang->line('Something went wrong.');
        }
        $this->basic->delete_data('messenger_bot_engagement_2way_chat_plugin',$where=array('id'=>$id,"user_id"=>$this->user_id));

        //******************************//
        // delete data to useges log table
        $this->_delete_usage_log($module_id=217,$request=1);   
        //******************************//

        $this->db->trans_complete();
        if($this->db->trans_status() === false) 
        {
          $response['status'] = '0';
          $response['message'] = $this->lang->line('Something went wrong.');
        } 
        else 
        {
            if($domain_code!='')
            @unlink(FCPATH.'js/2waychat/plugin-'.$domain_code.'.js');

          $response['status'] = '1';
          $response['message'] = $this->lang->line('Plugin has been deleted successfully.');
        }
        echo json_encode($response);
    }

    /************************************************************************************************/
    /****************************************TWAY CHAT PLUGIN****************************************/


    public function get_template_label_dropdown()
    {
        if(!$_POST) exit();
        $page_id=$this->input->post('page_id');// database id

        $label_list=$this->get_page_label($page_id);
        $template_list=$this->get_page_template($page_id);

        $dropdown=array();
        $js='<script>
              $("document").ready(function()  {
                $("#label_ids").select2();
                $("#template_id").select2();
              });


            </script>';
        $str=$str2='';
        $str2.=  "<option value=''>".$this->lang->line('select template')."</option>";
        foreach ($label_list as  $key=>$value)
        {            
            $str.=  "<option value='{$key}'>".$value."</option>";
        }
        foreach ($template_list as  $key=>$value)
        {            
            $str2.= "<option value='{$key}'>".$value."</option>";   
        }

        $pageinfo=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id)));        
        $mme_link=base_url();
        if(isset($pageinfo[0]))
        {
          $param=isset($pageinfo[0]['username'])?$pageinfo[0]['username']:"";
          if($param=="") $param=$pageinfo[0]['page_id'];
          $mme_link="https://m.me/".$param;
        }

        echo json_encode(array('label_option'=>$str,'template_option'=>$str2,"script"=>$js,"mme_link"=>$mme_link));
    }

    public function get_template_label_dropdown_edit()
    {
        if(!$_POST) exit();
        $page_id=$this->input->post('page_id');// database id
        $table_name=$this->input->post('table_name');
        $id=$this->input->post('id');

        $xdata=$this->basic->get_data($table_name,array("where"=>array("id"=>$id)));
        $xtemplate_id=isset($xdata[0]["template_id"])?$xdata[0]["template_id"]:"";
        $xlabel_ids=isset($xdata[0]["label_ids"])?$xdata[0]["label_ids"]:"";
        $xlabel_ids=explode(',', $xlabel_ids);


        $label_list=$this->get_page_label($page_id);
        $template_list=$this->get_page_template($page_id);

        $dropdown=array();
        $js='<script>
              $("document").ready(function()  {
                $("#label_ids").select2();
                $("#template_id").select2();
              });


            </script>';
        $str=$str2='';
        $str2.=  "<option value=''>".$this->lang->line('select template')."</option>";
        foreach ($label_list as  $key=>$value)
        {            
            if(in_array($key, $xlabel_ids)) $selected="selected";
            else $selected="";
            $str.=  "<option value='{$key}' {$selected}>".$value."</option>";
        }
        foreach ($template_list as  $key=>$value)
        {            
            if($key==$xtemplate_id) $selected="selected";
            else $selected="";
            $str2.= "<option value='{$key}' {$selected}>".$value."</option>";   
        }

        echo json_encode(array('label_option'=>$str,'template_option'=>$str2,"script"=>$js));
    }


    private function get_page_label($page_id=0)
    {
        if($page_id==0) return array();  

        if(!$this->db->table_exists('messenger_bot_broadcast_contact_group')) return array();

        $label_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("page_id"=>$page_id,"unsubscribe"=>"0","invisible"=>"0")),'','','',$start=NULL,$order_by="group_name ASC");
        $push_label=array();
        foreach ($label_data as $key => $value) 
        {    
            $push_label[$value['id']]=$value['group_name'].' ['.$value['label_id'].']';
        }
        return $push_label;
    }

    private function get_user_page()
    {

        $facebook_rx_fb_user_info = $this->session->userdata('facebook_rx_fb_user_info');

        $page_data=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info,"bot_enabled"=>"1")),'','','',$start=NULL,$order_by="page_name ASC");
        $push_page=array();
        foreach ($page_data as $key => $value) 
        {
            $push_page[$value['id']]=$value['page_name'];
        }
        return $push_page;
    }

    
    private function get_js_events()
    {
        return array
        (
            "blur"=>"Blur",
            "click"=>"Click",
            "change"=>"Change",
            "dblclick"=>"Double Click",
            "focus"=>"Focus",
            "mouseenter"=>"Mouse Enter",
            "mouseover"=>"Mouse Over",
            // "keydown"=>"Key Down",
            "keypress"=>"Key Press",
            "keyup"=>"Key Up"
        );
    }

    private function get_cta_options()
    {
        return array
        (
            "GET_THIS_IN_MESSENGER"=>"GET THIS IN MESSENGER",
            "RECEIVE_THIS_IN_MESSENGER"=>"RECEIVE THIS IN MESSENGER",
            "SEND_THIS_TO_ME"=>"SEND THIS TO ME",
            "GET_CUSTOMER_ASSISTANCE"=>"GET CUSTOMER ASSISTANCE",
            "GET_CUSTOMER_SERVICE"=>"GET CUSTOMER SERVICE",
            "GET_SUPPORT"=>"GET SUPPORT",
            "LET_US_CHAT"=>"LET US CHAT",
            "SEND_ME_MESSAGES"=>"SEND ME MESSAGES",
            "ALERT_ME_IN_MESSENGER"=>"ALERT ME IN MESSENGER",
            "SEND_ME_UPDATES"=>"SEND ME UPDATES",
            "MESSAGE_ME"=>"MESSAGE ME",
            "LET_ME_KNOW"=>"LET ME KNOW",
            "KEEP_ME_UPDATED"=>"KEEP ME UPDATED",
            "TELL_ME_MORE"=>"TELL ME MORE",
            "SUBSCRIBE_IN_MESSENGER"=>"SUBSCRIBE IN MESSENGER",
            "SUBSCRIBE_TO_UPDATES"=>"SUBSCRIBE TO UPDATES",
            "GET_MESSAGES"=>"GET MESSAGES",
            "SUBSCRIBE"=>"SUBSCRIBE",
            "GET_STARTED_IN_MESSENGER"=>"GET STARTED IN MESSENGER",
            "LEARN_MORE_IN_MESSENGER"=>"LEARN MORE IN MESSENGER",
            "GET_STARTED"=>"GET STARTED"
        );
    }
    /*---------------ENGAGEMENT FUNCTIONS-------------*/
    /*================================================*/









    

    public function activate()
    {
        $this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $purchase_code=$this->input->post('purchase_code');
        $this->addon_credential_check($purchase_code,strtolower($addon_controller_name)); // retuns json status,message if error
                  
        //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
        $sidebar=array(); 
        // mysql raw query needed to run, it's an array, put each query in a seperate index, create table query must should IF NOT EXISTS
        $sql=array
        (           
            1=>"
            CREATE TABLE IF NOT EXISTS `messenger_bot_engagement_2way_chat_plugin` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `page_auto_id` int(11) NOT NULL,
              `facebook_rx_fb_user_info_id` int(11) NOT NULL,
              `domain_name` varchar(250) NOT NULL,
              `language` varchar(255) NOT NULL,
              `minimized` enum('hide','show','fade','default') DEFAULT 'default',
              `logged_in` varchar(80) NOT NULL,
              `logged_out` varchar(80) NOT NULL,
              `color` varchar(7) NOT NULL,
              `domain_code` varchar(200) NOT NULL,
              `label_ids` varchar(250) NOT NULL,
              `template_id` int(11) NOT NULL,
              `reference` varchar(200) NOT NULL,
              `delay` int(11) NOT NULL DEFAULT '0',
              `donot_show_if_not_login` enum('0','1') NOT NULL DEFAULT '0',
              `add_date` datetime NOT NULL,
              `visual_flow_campaign_id` int(11) NOT NULL,
              `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general',
              PRIMARY KEY (`id`),
              UNIQUE KEY `domain_code` (`domain_code`),
              KEY `page_auto_id` (`page_auto_id`,`facebook_rx_fb_user_info_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            2=>"
            CREATE TABLE IF NOT EXISTS `messenger_bot_engagement_mme` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `link_code` varchar(100) NOT NULL,
              `user_id` int(11) NOT NULL,
              `page_id` int(11) NOT NULL COMMENT 'auto id',
              `btn_size` enum('small','medium','large','xlarge') NOT NULL DEFAULT 'medium',
              `new_button_display` varchar(250) NOT NULL,
              `new_button_bg_color` varchar(10) NOT NULL DEFAULT '#0084FF',
              `new_button_bg_color_hover` varchar(10) NOT NULL DEFAULT '#367FA9',
              `new_button_color` varchar(10) NOT NULL DEFAULT '#FFFFFF',
              `new_button_color_hover` varchar(50) NOT NULL DEFAULT '#FFFDDD',
              `label_ids` varchar(250) NOT NULL COMMENT 'comma seperated,messenger_bot_broadcast_contact_group.id',
              `reference` varchar(250) NOT NULL,
              `template_id` int(11) NOT NULL COMMENT 'messenger_bot_postback.id',
              `created_at` datetime NOT NULL,
              `visual_flow_campaign_id` int(11) NOT NULL,
              `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general',
              PRIMARY KEY (`id`),
              UNIQUE KEY `link_code` (`link_code`),
              KEY `user_id` (`user_id`,`page_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            3=>"
            CREATE TABLE IF NOT EXISTS `messenger_bot_engagement_send_to_msg` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `domain_code` varchar(100) NOT NULL,
              `user_id` int(11) NOT NULL,
              `page_id` int(11) NOT NULL COMMENT 'auto id',
              `domain_name` varchar(255) NOT NULL,
              `btn_size` enum('standard','large','xlarge') NOT NULL DEFAULT 'standard',
              `skin` enum('blue','white') NOT NULL DEFAULT 'blue',
              `reference` varchar(250) NOT NULL,
              `button_click_success_message` tinytext NOT NULL,
              `label_ids` varchar(250) NOT NULL COMMENT 'comma seperated,messenger_bot_broadcast_contact_group.id',
              `cta_text_option` varchar(250) NOT NULL,
              `template_id` int(11) NOT NULL COMMENT 'messenger_bot_postback.id',
              `language` varchar(200) NOT NULL DEFAULT 'en_US',
              `created_at` datetime NOT NULL,
              `redirect` enum('0','1') NOT NULL DEFAULT '0',
              `add_button_with_message` enum('0','1') NOT NULL DEFAULT '0',
              `button_with_message_content` tinytext NOT NULL COMMENT 'json',
              `success_redirect_url` tinytext NOT NULL,
              `visual_flow_campaign_id` int(11) NOT NULL,
              `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general',
              PRIMARY KEY (`id`),
              UNIQUE KEY `domain_code` (`domain_code`),
              KEY `user_id` (`user_id`,`page_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        ); 

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
        $sql=array
        (
          1=> "DROP TABLE IF EXISTS `messenger_bot_engagement_2way_chat_plugin`;",
          2=> "DROP TABLE IF EXISTS `messenger_bot_engagement_mme`;",
          3=>"DROP TABLE IF EXISTS `messenger_bot_engagement_send_to_msg`;",
          4 => "DELETE FROM messenger_bot_engagement_checkbox WHERE for_woocommerce='0';",
          5 => "DELETE FROM messenger_bot_engagement_checkbox_reply WHERE for_woocommerce='0';"
        );  
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }

   







    /*--------DEPRECATED FUNCTION FOR QUICK BROADCAST---------
    /*======================================================*/
    /*
    
   
    public function rss_autoposting_quick_broadcast_cron_call($api_key="") // will be called usig curl to broadcast rss autopost
    {       
        $api_user_id="";
        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $api_user_id="";
            if(array_key_exists(0, $explde_api_key))
            $api_user_id=$explde_api_key[0];
        }

        if($api_key=="" || !$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$api_user_id)) || !$this->basic->is_exist("users",array("id"=>$api_user_id,"status"=>"1","deleted"=>"0","user_type"=>"Admin")))
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("Invalid API key.")));
            exit();
        }  
              
        $post_data=json_decode(file_get_contents('php://input'), true);
        foreach($post_data as $key=>$value) 
        {
            $$key=$value;
        }

        $label_ids=array_filter(explode(',', $label_ids));
        $excluded_label_ids=array_filter(explode(',', $excluded_label_ids));       

        $status=$this->_check_usage($module_id=210,$request=1,$user_id);
        if($status=="3")  //monthly limit is exceeded, can not create another campaign this month
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("Your monthly limit for quick bulk broadcasting module has been exceeded.")));
            exit();
        }


        $insert_data = array();
        $page_table_id=$page;
        $insert_data['campaign_name'] = $campaign_name;
        $insert_data['fb_page_id'] = $fb_page_id;
        $insert_data['page_id'] = $page_table_id;

        // domain white list section
        $messenger_bot_user_info_id = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id)),array("facebook_rx_fb_user_info_id","page_name","page_access_token"));
        $page_access_token = $messenger_bot_user_info_id[0]['page_access_token'];
        $page_name = $messenger_bot_user_info_id[0]['page_name'];
        $messenger_bot_user_info_id = $messenger_bot_user_info_id[0]["facebook_rx_fb_user_info_id"];
        $white_listed_domain = $this->basic->get_data("messenger_bot_domain_whitelist",array("where"=>array("user_id"=>$user_id,"messenger_bot_user_info_id"=>$messenger_bot_user_info_id,"page_id"=>$page_table_id)),"domain");

        $white_listed_domain_array = array();
        foreach ($white_listed_domain as $value) {
            $white_listed_domain_array[] = $value['domain'];
        }
        $need_to_whitelist_array = array();

        $postback_insert_data = array();
        $reply_bot = array();
        $bot_message = array();

        for($k=1; $k <=1 ; $k++) 
        {    
            $template_type = 'template_type_'.$k;
            $template_type = $$template_type;
            $insert_data['template_type'] = $template_type;
            $template_type = str_replace(' ', '_', $template_type);

            if($template_type == 'generic_template')
            {
                $generic_template_title = 'generic_template_title_'.$k;
                $generic_template_title = $$generic_template_title;

                $generic_template_image = 'generic_template_image_'.$k;
                $generic_template_image = $$generic_template_image;

                $generic_template_subtitle = 'generic_template_subtitle_'.$k;
                $generic_template_subtitle = $$generic_template_subtitle;

                $generic_template_image_destination_link = 'generic_template_image_destination_link_'.$k;
                $generic_template_image_destination_link = $$generic_template_image_destination_link;

                if(function_exists('getimagesize') && $generic_template_image!='') 
                {
                    list($width, $height, $type, $attr) = getimagesize($generic_template_image);
                    if($width==$height)
                        $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                }

                // $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                $reply_bot[$k]['attachment']['payload']['elements'][0]['title'] = $generic_template_title;
                if($generic_template_image!="")
                $reply_bot[$k]['attachment']['payload']['elements'][0]['image_url'] = $generic_template_image;
              if($generic_template_subtitle!="")
                $reply_bot[$k]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;
                $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['type'] = 'web_url';
                $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['url'] = $generic_template_image_destination_link;   

                if($display_unsubscribe=="1")
                {
                  for ($i=1; $i <= 1 ; $i++) 
                  { 
                      $button_text = 'generic_template_button_text_'.$i.'_'.$k;
                      $button_text = $$button_text;
                      $button_type = 'generic_template_button_type_'.$i.'_'.$k;
                      $button_type = $$button_type;
                      $button_postback_id = 'generic_template_button_post_id_'.$i.'_'.$k;
                      $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';

                      if($button_type == 'post_back')
                      {
                          if($button_text != '' && $button_type != '' && $button_postback_id != '')
                          {
                              $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                              $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                              $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                          }
                      }
                  }
                }

            }
            $bot_message['messages'][] = $reply_bot[$k];   
        }

      
        // domain white list section start
        $this->load->library("fb_rx_login"); 
        $domain_whitelist_insert_data = array();
        foreach($need_to_whitelist_array as $value)
        {
            $response=$this->fb_rx_login->domain_whitelist($page_access_token,$value);
            if($response['status'] != '0')
            {
                $temp_data = array();
                $temp_data['user_id'] = $user_id;
                $temp_data['messenger_bot_user_info_id'] = $messenger_bot_user_info_id;
                $temp_data['page_id'] = $page_table_id;
                $temp_data['domain'] = $value;
                $temp_data['created_at'] = date("Y-m-d H:i:s");

                $domain_whitelist_insert_data[] = $temp_data;
            }
        }
        if(!empty($domain_whitelist_insert_data)) $this->db->insert_batch('messenger_bot_domain_whitelist',$domain_whitelist_insert_data);
        // domain white list section end

        $insert_data['message'] = json_encode($bot_message,true);
        $insert_data['user_id'] = $user_id;        
        // $insert_data['template_type'] = $template_type;  
        $insert_data['created_at'] = date('Y-m-d H:i:s');        
        $insert_data['schedule_type'] = $schedule_type;        
        $insert_data['schedule_time'] = $schedule_time; 
        $insert_data['page_name'] = $page_name; 
        
        if($schedule_type=="now") $insert_data["posting_status"]="IN_PROGRESS";       
        else $insert_data["posting_status"]="SCHEDULED";       
        
        $insert_data['timezone'] = $time_zone;  

        $schedule_time_formatted="";
        if($schedule_time!="" && $time_zone!="")
        {
            date_default_timezone_set($time_zone);
            $schedule_time_formatted=strtotime($schedule_time);
        }

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids=array();
        if(count($label_ids)<2) // facebook need min 2 labels to work, adding the invisible label if less than 1 label selected
        {
            $invisible_where=array("where"=>array("page_id"=>$page_table_id,"user_id" =>$user_id,"invisible"=>'1'));
            $invisible_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",$invisible_where);
            
            foreach($invisible_data as $key => $value) 
            {
               array_push($label_ids, $value["id"]);
               break;
            }  
        }

        $fb_label_ids=array();
        $fb_label_names=array();
        if(!empty($label_ids))
        {
            $insert_data['label_ids'] = implode(',', $label_ids);
            $fb_label_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where_in"=>array("id"=>$label_ids)));
            foreach ($fb_label_data as $key => $value) 
            {
               $fb_label_ids[]=$value["label_id"];
               if($value['invisible']=='0')
               $fb_label_names[]=$value["group_name"];
            }  
        }
        $insert_data['label_names'] = implode(',', $fb_label_names);

        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids=array();
        $fb_excluded_label_ids=array();
       
        $insert_data['excluded_label_ids'] = implode(',', $excluded_label_ids);
        
        if(empty($excluded_label_ids)) 
        {
            $where_clause=array("where"=>array("page_id"=>$page_table_id,"user_id"=>$user_id,"unsubscribe"=>"1"));
            $fb_label_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",$where_clause);
        }
        else 
        {
            $sql="SELECT * FROM `messenger_bot_broadcast_contact_group` WHERE `id` IN(".$insert_data['excluded_label_ids'].") OR (`page_id` = ".$page_table_id." AND `user_id` = ".$user_id." AND `unsubscribe` = '1') AND `messenger_bot_broadcast_contact_group`.`deleted` = '0'";
            $query=$this->db->query($sql);
            $fb_label_data=$query->result_array();
        }
     
        foreach ($fb_label_data as $key => $value) 
        {
           $fb_excluded_label_ids[]=$value["label_id"];
        }  
              

        $create_message_creative=$this->fb_rx_login->create_message_creative(json_encode($bot_message,true),$page_access_token);
        $creative_id = isset($create_message_creative['message_creative_id'])?$create_message_creative['message_creative_id']:"";
        $insert_data['creative_id']=$creative_id;
        if($creative_id=="") 
        {
            $error_message=isset($create_message_creative['error']) ? $create_message_creative['error']['message'] : $this->lang->line("Broadcast campaign failed to create message creative.");
            $insert_data["error_message"]=$error_message;
            echo json_encode(array("status" => "0", "message" =>$error_message,"cid"=>$creative_id));
            exit(); 
        }
        
        $send_broadcast=array();
        if($creative_id!="") $send_broadcast=$this->fb_rx_login->send_broadcast($creative_id,$page_access_token,$fb_label_ids,$fb_excluded_label_ids,$notification_type,$schedule_time_formatted);            

        $broadcast_id=isset($send_broadcast['broadcast_id'])?$send_broadcast['broadcast_id']:"";
        $insert_data['broadcast_id']=$broadcast_id;
        if($broadcast_id=="") 
        {            
            $error_message=isset($send_broadcast['error']) ? $send_broadcast['error']['message'] : $this->lang->line("Broadcast failed, could not fetch broadcast ID.");
            $insert_data["error_message"]=$error_message;
            echo json_encode(array("status" => "0", "message" =>$error_message));
            exit(); 
        }

        if($this->basic->insert_data('messenger_bot_broadcast',$insert_data))
        {
            $this->_insert_usage_log($module_id=210,$request=1,$user_id);
            echo json_encode(array("status" => "1", "message" =>$this->lang->line("Campaing has been created successfully.")));            
        }
        
    }
    public function quick_broadcast_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(210,$this->module_access))
        redirect('home/login_page', 'location');

        $data['body'] = "messenger_broadcaster/quick_bulk_broadcast_report";
        $data['page_title'] = $this->lang->line("Quick Broadcast");
        $page_list = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"bot_enabled"=>"1")),$select='',$join='',$limit='',$start=NULL,$order_by='page_name ASC');
        foreach($page_list as $value)
        {
            $page_info[$value['id']] = $value['page_name'];
        }
      
        $page_info[''] = $this->lang->line("Page");
        $data['page_list'] = $page_info;

        $this->_viewcontroller($data);
    }

    public function quick_broadcast_campaign_data()
    { 
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(210,$this->module_access)) exit();

        $search_value = $this->input->post("search_value");
        $page_id = $this->input->post("search_page_id");
        $status = $this->input->post("search_status");
        $campaign_date_range = $this->input->post("campaign_date_range");


        $display_columns = 
        array(
          "#",
          "CHECKBOX",
          'campaign_name',
          'page_name',
          'template_type',
          'posting_status',
          'sent_count',
          'actions',
          'broadcast_id',
          'schedule_time',
          'created_at',
          'label_names'
        );
        $search_columns = array('campaign_name','label_names','broadcast_id','postback_id');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 10;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'created_at';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom="messenger_bot_broadcast.user_id = ".$this->user_id;

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }
        if($campaign_date_range!="")
        {
            $exp = explode('|', $campaign_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date = isset($exp[1])?$exp[1]:"";
            if($from_date!="Invalid date" && $to_date!="Invalid date")
            $where_custom .= " AND created_at >= '{$from_date}' AND created_at <='{$to_date}'";
        }
        $this->db->where($where_custom);

        if($page_id!="") $this->db->where(array("page_id"=>$page_id)); 
        if($status!="") $this->db->where(array("posting_status"=>$status));       
        
        $table="messenger_bot_broadcast";
        $info=$this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');
        
        $this->db->where($where_custom);
        if($page_id!="") $this->db->where(array("page_id"=>$page_id)); 
        if($status!="") $this->db->where(array("posting_status"=>$status)); 
        $total_rows_array=$this->basic->count_row($table,$where='',$count=$table.".id",$join,$group_by='');

        $total_result=$total_rows_array[0]['total_rows'];
        $this->load->library("fb_rx_login");
        foreach($info as $key => $value)
        {            
          if($info[$key]['schedule_time'] != "0000-00-00 00:00:00")
          $scheduled_at = date("M j, y H:i",strtotime($info[$key]['schedule_time']));
          else $scheduled_at = '<span class="text-muted"><i class="fas fa-exclamation-circle"></i> '.$this->lang->line("Not Scheduled")."<span>";
          $info[$key]['schedule_time'] =  $scheduled_at;

          $info[$key]['created_at'] = date("M j, y H:i",strtotime($info[$key]['created_at']));

          $posting_status = $info[$key]['posting_status'];
          $schedule_type = $info[$key]['schedule_type'];

          if($posting_status!='SCHEDULED' || $schedule_type!="later") 
          $info[$key]['edit'] = "<a data-toggle='tooltip' title='".$this->lang->line("Only scheduled pending campaigns can be edited.")."' class='btn btn-light btn-circle text-muted'><i class='fas fa-edit'></i></a>";
          else
          {
              $edit_url = site_url('messenger_bot_enhancers/edit_quick_broadcast_campaign/'.$info[$key]['id']);
              $info[$key]['edit'] =  "<a data-toggle='tooltip title='".$this->lang->line("Edit")."' href='".$edit_url."' class='btn btn-outline-warning btn-circle'><i class='fas fa-edit'></i></a>";
          }

          if($posting_status != 'FINISHED')
          {
              $accesstoken=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$info[$key]['page_id'])));
              $post_access_token=isset($accesstoken[0]["page_access_token"]) ? $accesstoken[0]["page_access_token"] : "";

              $broadcast_status_response=$this->fb_rx_login->broadcast_status($info[$key]['broadcast_id'],$post_access_token);
              $message_sent_count_response=$this->fb_rx_login->get_broadcast_message_sent_count($info[$key]['broadcast_id'],$post_access_token);

              $new_posting_status=isset($broadcast_status_response["status"]) ? $broadcast_status_response["status"] : "";
              $sent_count=isset($message_sent_count_response["data"][0]["values"][0]["value"]) ?  $message_sent_count_response["data"][0]["values"][0]["value"] : 0;
              if($new_posting_status!="" || $sent_count>0)
              {
                  $this->basic->update_data("messenger_bot_broadcast",array("id"=>$info[$key]['id']),array("posting_status"=>$new_posting_status,"sent_count"=>$sent_count));
                  $info[$key]['posting_status']=$new_posting_status;
                  $info[$key]['sent_count']=$sent_count;
              }
          }

          //upated posting status
          $posting_status = $info[$key]['posting_status'];

          if($posting_status=='IN_PROGRESS')
          $info[$key]["delete"] = "<a data-toggle='tooltip' title='".$this->lang->line("Campaign in processing can not be deleted.")."' class='btn btn-light  btn-circle text-muted'><i class='fas fa-trash-alt'></i></a>";
          else $info[$key]['delete'] =  "<a data-toggle='tooltip' href='' title='".$this->lang->line("Selete")."' id='".$info[$key]['id']."' class='delete btn btn-outline-danger btn-circle'><i class='fas fa-trash-alt'></i></a>";

          if($posting_status == 'FINISHED') $info[$key]['posting_status'] = '<span class="text-success"><i class="fas fa-check-circle"></i> '.$this->lang->line("Completed").'</span>';
          else if( $posting_status == 'IN_PROGRESS') $info[$key]['posting_status'] = '<span class="text-warning"><i class="fas fa-spinner"></i> '.$this->lang->line("Processing").'</span>';
          else if( $posting_status == 'CANCELED') $info[$key]['posting_status'] = '<span class="text-dark"><i class="fas fa-ban"></i> '.$this->lang->line("Canceled").'</span>';
          else $info[$key]['posting_status'] = '<span class="text-danger"><i class="far fa-times-circle"></i> '.$this->lang->line("Pending").'</span>';
          $info[$key]['posting_status'] = '<div style="min-width:80px;">'.$info[$key]['posting_status'].'</div>';
          $info[$key]["actions"]="<div style='min-width:100px;'>".$info[$key]["edit"]." ".$info[$key]["delete"]."</div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
        echo json_encode($data);
    }

    public function delete_quick_campaign()
    {       
        if($this->session->userdata('user_type') != 'Admin' && !in_array(210,$this->module_access)) exit();
        $this->ajax_check();
        $id=$this->input->post("id");

        $xdata = $this->basic->get_data("messenger_bot_broadcast",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        if(!isset($xdata[0])) exit();
        $posting_status  = isset($xdata[0]["posting_status"]) ? $xdata[0]["posting_status"] : "";
        $broadcast_id  = isset($xdata[0]["broadcast_id"]) ? $xdata[0]["broadcast_id"] : "";
        $page_id  = isset($xdata[0]["page_id"]) ? $xdata[0]["page_id"] : "";

        if($posting_status=='IN_PROGRESS')
        {
            echo json_encode(array("status"=>"0","message"=>$this->lang->line("This campaign is in processing state and can not be deleted.")));
            exit();
        }
        
        if($posting_status=="SCHEDULED") // removing usage data if deleted and campaign is pending
        {
            $accesstoken=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id)));
            $post_access_token=isset($accesstoken[0]["page_access_token"]) ? $accesstoken[0]["page_access_token"] : "";

            $this->load->library('fb_rx_login');
            $response=$this->fb_rx_login->cancel_broadcast_schedule($broadcast_id,$post_access_token);
            if(isset($response['success']))
            {              
                $this->basic->delete_data("messenger_bot_broadcast",array("id"=>$id,"user_id"=>$this->user_id));       
                $this->_delete_usage_log($module_id=210,$request=1);
                echo json_encode(array("status"=>"1","message"=>$this->lang->line("Campaign has been deleted successfully.")));
            }
            else
            {
                $errormessage =  isset($response['error']) ? $response['error']['message'] : $this->lang->line("Something went wrong.");

                echo json_encode(array("status"=>"0","message"=>$errormessage));
            }            
        }
        else 
        {
          $this->basic->delete_data("messenger_bot_broadcast",array("id"=>$id,"user_id"=>$this->user_id));
          echo json_encode(array("status"=>"1","message"=>$this->lang->line("Campaign has been deleted successfully.")));
        }
      
    }
    
    public function create_quick_broadcast_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(210,$this->module_access))
        redirect('home/login_page', 'location');

        $data["templates"]=$this->basic->get_enum_values("messenger_bot_broadcast","template_type");

        $data['body'] = 'messenger_broadcaster/quick_bulk_broadcast_add';
        $data['page_title'] = $this->lang->line('Create Quick Broadcast');  

        $data['page_info'] = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"bot_enabled"=>"1")),$select='',$join='',$limit='',$start=NULL,$order_by='page_name ASC');

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id)));  
        $data['postback_ids'] = $postback_id_list;

        $data["time_zone"]= $this->_time_zone_list();
        $this->_viewcontroller($data); 
    }

    public function quick_bulk_broadcast_add_action()
    {
       if($this->session->userdata('user_type') != 'Admin' && !in_array(210,$this->module_access)) exit();
       $this->ajax_check();

       if($this->is_demo == '1')
       {
           if($this->session->userdata('user_type') == "Admin")
           {
               echo json_encode(array("status" => "0", "message" =>$this->lang->line("This function is disabled from admin account in this demo!!")));
               exit();
           }
       }

        $status=$this->_check_usage($module_id=210,$request=1);
        if($status=="3")  //monthly limit is exceeded, can not create another campaign this month
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("Sorry, your monthly limit to send quick broadcast message is exceeded.")));
            exit();
        }

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$value;
        }

        $insert_data = array();
        $page_table_id=$page;
        $insert_data['campaign_name'] = $campaign_name;
        $insert_data['fb_page_id'] = $fb_page_id;
        $insert_data['page_id'] = $page_table_id;
        $insert_data['notification_type'] = $notification_type;

        // domain white list section
        $messenger_bot_user_info_id = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id)),array("facebook_rx_fb_user_info_id","page_name","page_access_token"));
        $page_access_token = $messenger_bot_user_info_id[0]['page_access_token'];
        $page_name = $messenger_bot_user_info_id[0]['page_name'];
        $messenger_bot_user_info_id = $messenger_bot_user_info_id[0]["facebook_rx_fb_user_info_id"];
        $white_listed_domain = $this->basic->get_data("messenger_bot_domain_whitelist",array("where"=>array("user_id"=>$this->user_id,"messenger_bot_user_info_id"=>$messenger_bot_user_info_id,"page_id"=>$page_table_id)),"domain");

        $white_listed_domain_array = array();
        foreach ($white_listed_domain as $value) {
            $white_listed_domain_array[] = $value['domain'];
        }
        $need_to_whitelist_array = array();

        $postback_insert_data = array();
        $reply_bot = array();
        $bot_message = array();


        for ($k=1; $k <=1 ; $k++) 
        {    
            $template_type = 'template_type_'.$k;
            $template_type = $$template_type;
            $insert_data['template_type'] = $template_type;
            $template_type = str_replace(' ', '_', $template_type);

            if($template_type == 'text')
            {
                $text_reply = 'text_reply_'.$k;
                $text_reply = isset($$text_reply) ? $$text_reply : '';
                if($text_reply != '')
                {
                    $reply_bot[$k]['text'] = $text_reply;                    
                }
            }
            if($template_type == 'image')
            {
                $image_reply_field = 'image_reply_field_'.$k;
                $image_reply_field = isset($$image_reply_field) ? $$image_reply_field : '';
                if($image_reply_field != '')
                {
                    $reply_bot[$k]['attachment']['type'] = 'image';
                    $reply_bot[$k]['attachment']['payload']['url'] = $image_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }
            if($template_type == 'audio')
            {
                $audio_reply_field = 'audio_reply_field_'.$k;
                $audio_reply_field = isset($$audio_reply_field) ? $$audio_reply_field : '';
                if($audio_reply_field != '')
                {
                    $reply_bot[$k]['attachment']['type'] = 'audio';
                    $reply_bot[$k]['attachment']['payload']['url'] = $audio_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
                
            }
            if($template_type == 'video')
            {
                $video_reply_field = 'video_reply_field_'.$k;
                $video_reply_field = isset($$video_reply_field) ? $$video_reply_field : '';
                if($video_reply_field != '')
                {
                    $reply_bot[$k]['attachment']['type'] = 'video';
                    $reply_bot[$k]['attachment']['payload']['url'] = $video_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }
            if($template_type == 'file')
            {
                $file_reply_field = 'file_reply_field_'.$k;
                $file_reply_field = isset($$file_reply_field) ? $$file_reply_field : '';
                if($file_reply_field != '')
                {                    
                    $reply_bot[$k]['attachment']['type'] = 'file';
                    $reply_bot[$k]['attachment']['payload']['url'] = $file_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
            }



        
            if($template_type == 'media')
            {
                $media_input = 'media_input_'.$k;
                $media_input = isset($$media_input) ? $$media_input : '';
                if($media_input != '')
                {
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'media';
                    $template_media_type = '';
                    if (strpos($media_input, '/videos/') !== false) {
                        $template_media_type = 'video';
                    }
                    else
                        $template_media_type = 'image';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['media_type'] = $template_media_type;
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['url'] = $media_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'media_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'media_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'media_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'media_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                     //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'media_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';

                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            if($button_extension != '' && $button_extension == 'birthday'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'compact';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }



            if($template_type == 'text_with_buttons')
            {
                $text_with_buttons_input = 'text_with_buttons_input_'.$k;
                $text_with_buttons_input = isset($$text_with_buttons_input) ? $$text_with_buttons_input : '';
                if($text_with_buttons_input != '')
                {
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'button';
                    $reply_bot[$k]['attachment']['payload']['text'] = $text_with_buttons_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'text_with_buttons_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'text_with_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'text_with_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'text_with_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'text_with_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'web_url';

                            if($button_extension != '' && $button_extension == 'birthday'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'compact';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday')
                            {
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }

            if($template_type == 'quick_reply')
            {
                $quick_reply_text = 'quick_reply_text_'.$k;
                $quick_reply_text = isset($$quick_reply_text) ? $$quick_reply_text : '';
                if($quick_reply_text != '')
                {
                    $reply_bot[$k]['text'] = $quick_reply_text;                    
                }

                for ($i=1; $i <= 11 ; $i++) 
                { 
                    $button_text = 'quick_reply_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_postback_id = 'quick_reply_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_type = 'quick_reply_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    if($button_type=='post_back')
                    {
                        if($button_text != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'text';
                            $reply_bot[$k]['quick_replies'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['quick_replies'][$i-1]['title'] = $button_text;
                        }                    
                    }
                    if($button_type=='phone_number')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_phone_number';
                    }
                    if($button_type=='user_email')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_email';
                    }
                    if($button_type=='location')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'location';
                    }

                }
            }

            if($template_type == 'generic_template')
            {
                $generic_template_title = 'generic_template_title_'.$k;
                $generic_template_title = isset($$generic_template_title) ? $$generic_template_title : '';
                $generic_template_image = 'generic_template_image_'.$k;
                $generic_template_image = isset($$generic_template_image) ? $$generic_template_image : '';
                $generic_template_subtitle = 'generic_template_subtitle_'.$k;
                $generic_template_subtitle = isset($$generic_template_subtitle) ? $$generic_template_subtitle : '';
                $generic_template_image_destination_link = 'generic_template_image_destination_link_'.$k;
                $generic_template_image_destination_link = isset($$generic_template_image_destination_link) ? $$generic_template_image_destination_link : '';

                if($generic_template_title != '')
                {
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['title'] = $generic_template_title;                    
                }

                if($generic_template_subtitle != '')
                $reply_bot[$k]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;

                if($generic_template_image!="")
                {
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['image_url'] = $generic_template_image;
                    if($generic_template_image_destination_link!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['url'] = $generic_template_image_destination_link;
                    }

                    if(function_exists('getimagesize') && $generic_template_image!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($generic_template_image);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }

                }
                

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'generic_template_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'generic_template_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'generic_template_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'generic_template_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'generic_template_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            if($button_extension != '' && $button_extension == 'birthday'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'compact';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }

            if($template_type == 'carousel')
            {
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                for ($j=1; $j <=10 ; $j++) 
                {                                 
                    $carousel_image = 'carousel_image_'.$j.'_'.$k;
                    $carousel_title = 'carousel_title_'.$j.'_'.$k;

                    if(!isset($$carousel_title) || $$carousel_title == '') continue;

                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$carousel_title;
                    $carousel_subtitle = 'carousel_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$carousel_subtitle;

                    if(isset($$carousel_image) && $$carousel_image!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$carousel_image;                    
                        $carousel_image_destination_link = 'carousel_image_destination_link_'.$j.'_'.$k;
                        if($$carousel_image_destination_link!="") 
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$carousel_image_destination_link;
                        }

                        if(function_exists('getimagesize') && $$carousel_image!='') 
                        {
                            list($width, $height, $type, $attr) = getimagesize($$carousel_image);
                            if($width==$height)
                                $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                        }

                    }

                    for ($i=1; $i <= 3 ; $i++) 
                    { 
                        $button_text = 'carousel_button_text_'.$j."_".$i.'_'.$k;
                        $button_text = isset($$button_text) ? $$button_text : '';
                        $button_type = 'carousel_button_type_'.$j."_".$i.'_'.$k;
                        $button_type = isset($$button_type) ? $$button_type : '';
                        $button_postback_id = 'carousel_button_post_id_'.$j."_".$i.'_'.$k;
                        $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                        $button_web_url = 'carousel_button_web_url_'.$j."_".$i.'_'.$k;
                        $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                        //add an extra query parameter for tracking the subscriber to whom send 
                        if($button_web_url!='')
                          $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                        $button_call_us = 'carousel_button_call_us_'.$j."_".$i.'_'.$k;
                        $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                        if($button_type == 'post_back')
                        {
                            if($button_text != '' && $button_type != '' && $button_postback_id != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'postback';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_postback_id;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                            }
                        }
                        if(strpos($button_type,'web_url') !== FALSE)
                        {
                            $button_type_array = explode('_', $button_type);
                            if(isset($button_type_array[2]))
                            {
                                $button_extension = trim($button_type_array[2],'_'); 
                                array_pop($button_type_array);
                            }            
                            else $button_extension = '';
                            $button_type = implode('_', $button_type_array);

                            if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'web_url';
                                if($button_extension != '' && $button_extension == 'birthday'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'compact';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                }
                                else
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = $button_web_url;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;

                                if($button_extension != '' && $button_extension != 'birthday')
                                {
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                    // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                                }

                                if(!in_array($button_web_url, $white_listed_domain_array))
                                {
                                    $need_to_whitelist_array[] = $button_web_url;
                                }
                            }
                        }
                        if($button_type == 'phone_number')
                        {
                            if($button_text != '' && $button_type != '' && $button_call_us != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'phone_number';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_call_us;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                            }
                        }
                    }
                }
            }

            if($template_type == 'list')
            {
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'list';

                for ($j=1; $j <=4 ; $j++) 
                {                                 
                    $list_image = 'list_image_'.$j.'_'.$k;
                    if(!isset($$list_image) || $$list_image == '') continue;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$list_image;
                    $list_title = 'list_title_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$list_title;
                    $list_subtitle = 'list_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$list_subtitle;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                    $list_image_destination_link = 'list_image_destination_link_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$list_image_destination_link;
                    
                }

                $button_text = 'list_with_buttons_text_'.$k;
                $button_text = isset($$button_text) ? $$button_text : '';
                $button_type = 'list_with_button_type_'.$k;
                $button_type = isset($$button_type) ? $$button_type : '';
                $button_postback_id = 'list_with_button_post_id_'.$k;
                $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                $button_web_url = 'list_with_button_web_url_'.$k;
                $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                //add an extra query parameter for tracking the subscriber to whom send 
                if($button_web_url!='')
                  $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                $button_call_us = 'list_with_button_call_us_'.$k;
                $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                if($button_type == 'post_back')
                {
                    if($button_text != '' && $button_type != '' && $button_postback_id != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'postback';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_postback_id;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                    }
                }
                if(strpos($button_type,'web_url') !== FALSE)
                {
                    $button_type_array = explode('_', $button_type);
                    if(isset($button_type_array[2]))
                    {
                        $button_extension = trim($button_type_array[2],'_'); 
                        array_pop($button_type_array);
                    }            
                    else $button_extension = '';
                    $button_type = implode('_', $button_type_array);

                    if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'web_url';
                        if($button_extension != '' && $button_extension == 'birthday'){
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['messenger_extensions'] = 'true';
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['webview_height_ratio'] = 'compact';
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                        }
                        else
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['url'] = $button_web_url;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;

                        if($button_extension != '' && $button_extension != 'birthday')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['messenger_extensions'] = 'true';
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['webview_height_ratio'] = $button_extension;
                            // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                        }

                        if(!in_array($button_web_url, $white_listed_domain_array))
                        {
                            $need_to_whitelist_array[] = $button_web_url;
                        }
                    }
                }
                if($button_type == 'phone_number')
                {
                    if($button_text != '' && $button_type != '' && $button_call_us != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'phone_number';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_call_us;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                    }
                }


            }

            $bot_message['messages'][] = $reply_bot[$k]; 

        }

      
        // domain white list section start
        $this->load->library("fb_rx_login"); 
        $domain_whitelist_insert_data = array();
        foreach($need_to_whitelist_array as $value)
        {
            $response=$this->fb_rx_login->domain_whitelist($page_access_token,$value);
            if($response['status'] != '0')
            {
                $temp_data = array();
                $temp_data['user_id'] = $this->user_id;
                $temp_data['messenger_bot_user_info_id'] = $messenger_bot_user_info_id;
                $temp_data['page_id'] = $page_table_id;
                $temp_data['domain'] = $value;
                $temp_data['created_at'] = date("Y-m-d H:i:s");

                $domain_whitelist_insert_data[] = $temp_data;
            }
        }
        if(!empty($domain_whitelist_insert_data)) $this->db->insert_batch('messenger_bot_domain_whitelist',$domain_whitelist_insert_data);
        // domain white list section end

        $insert_data['message'] = json_encode($bot_message,true);
        $insert_data['user_id'] = $this->user_id;        
        // $insert_data['template_type'] = $template_type;  
        $insert_data['created_at'] = date('Y-m-d H:i:s');

        if(!isset($schedule_type)) $schedule_type='now';
        if(!isset($schedule_time)) $schedule_time = ""; 

        $insert_data['schedule_time'] = $schedule_time; 
        $insert_data['schedule_type'] = $schedule_type;
        $insert_data['page_name'] = $page_name; 
        
        if($schedule_type=="now") $insert_data["posting_status"]="IN_PROGRESS";       
        else $insert_data["posting_status"]="SCHEDULED";       
        
        $insert_data['timezone'] = $time_zone;  

        $schedule_time_formatted="";
        if($schedule_time!="" && $time_zone!="")
        {
            date_default_timezone_set($time_zone);
            $schedule_time_formatted=strtotime($schedule_time);
        }

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids=array();
        if(count($label_ids)<2) // facebook need min 2 labels to work, adding the invisible label if less than 1 label selected
        {
            $invisible_where=array("where"=>array("page_id"=>$page_table_id,"user_id" =>$this->user_id,"invisible"=>'1'));
            $invisible_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",$invisible_where);
            foreach($invisible_data as $key => $value) 
            {
               array_push($label_ids, $value["id"]);
               break;
            }  
        }

        $fb_label_ids=array();
        $fb_label_names=array();
        if(!empty($label_ids))
        {
            $fb_label_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where_in"=>array("id"=>$label_ids)));
            foreach ($fb_label_data as $key => $value) 
            {
               $fb_label_ids[]=$value["label_id"];
               if($value['invisible']=='0')
               $fb_label_names[]=$value["group_name"];
            }  
        }
        $insert_data['label_ids'] = implode(',', $label_ids);
        $insert_data['label_names'] = implode(',', $fb_label_names);


        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids=array();
        $fb_excluded_label_ids=array();       
        $insert_data['excluded_label_ids'] = implode(',', $excluded_label_ids);

        // 24+1 inactivation becuase he is already sending a promo message
        $excluded_label_ids_temp=$excluded_label_ids;
        $unsubscribe_labeldata=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("user_id"=>$this->user_id,"page_id"=>$page_table_id,"unsubscribe"=>"1")));
        foreach ($unsubscribe_labeldata as $key => $value) 
        {
            array_push($excluded_label_ids_temp, $value["id"]);
        }

        if(count($label_ids)>0) $sql_part="("; else $sql_part="";        
        $sql_part_array=array();
        foreach ($label_ids as $key => $value) 
        {
           $sql_part_array[]="FIND_IN_SET('".$value."',contact_group_id) !=0";
        }
        if(count($label_ids)>0) 
        {
          $sql_part.=implode(' OR ', $sql_part_array);
          $sql_part.=") AND ";
        }

        $sql_part2="";
        $sql_part_array2=array();
        foreach ($excluded_label_ids_temp as $key => $value) 
        {
          $sql_part_array2[]="NOT FIND_IN_SET('".$value."',contact_group_id) !=0";          
        }        
        if(count($excluded_label_ids_temp)>0) 
        {
            $sql_part2=implode(' AND ', $sql_part_array2);
            $sql_part2.=" AND ";
        }


        $sql_24h="UPDATE messenger_bot_subscriber SET is_24h_1_sent='1' WHERE ".$sql_part." ".$sql_part2." user_id = ".$this->user_id." AND unavailable = '0' AND is_bot_subscriber='1' AND page_table_id = {$page_table_id}";
        $this->basic->execute_complex_query($sql_24h);
        //====================================================================
        
        if(empty($excluded_label_ids)) 
        {
            $where_clause=array("where"=>array("page_id"=>$page_table_id,"user_id"=>$this->user_id,"unsubscribe"=>"1"));
            $fb_label_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",$where_clause);
        }
        else 
        {
            $sql="SELECT * FROM `messenger_bot_broadcast_contact_group` WHERE `id` IN(".$insert_data['excluded_label_ids'].") OR (`page_id` = ".$page_table_id." AND `user_id` = ".$this->user_id." AND `unsubscribe` = '1') AND `messenger_bot_broadcast_contact_group`.`deleted` = '0'";
            $query=$this->db->query($sql);
            $fb_label_data=$query->result_array();
        }
     
        foreach ($fb_label_data as $key => $value) 
        {
           $fb_excluded_label_ids[]=$value["label_id"];
        }  
       
       

        $create_message_creative=$this->fb_rx_login->create_message_creative(json_encode($bot_message,true),$page_access_token);
        $creative_id = isset($create_message_creative['message_creative_id'])?$create_message_creative['message_creative_id']:"";
        $insert_data['creative_id']=$creative_id;
        if($creative_id=="") 
        {
            $error_message=isset($create_message_creative['error']) ? $create_message_creative['error']['message'] : $this->lang->line("Broadcast campaign failed to create message creative.");
            $insert_data["error_message"]=$error_message;
            echo json_encode(array("status" => "0", "message" =>$error_message,"cid"=>$creative_id));
            exit(); 
        }
        
        $send_broadcast=array();
        if($creative_id!="") $send_broadcast=$this->fb_rx_login->send_broadcast($creative_id,$page_access_token,$fb_label_ids,$fb_excluded_label_ids,$notification_type,$schedule_time_formatted);            

        $broadcast_id=isset($send_broadcast['broadcast_id'])?$send_broadcast['broadcast_id']:"";
        $insert_data['broadcast_id']=$broadcast_id;
        if($broadcast_id=="") 
        {            
            $error_message=isset($send_broadcast['error']) ? $send_broadcast['error']['message'] : $this->lang->line("Broadcast failed, could not fetch broadcast ID.");
            $insert_data["error_message"]=$error_message;
            echo json_encode(array("status" => "0", "message" =>$error_message));
            exit(); 
        }

        if($this->basic->insert_data('messenger_bot_broadcast',$insert_data))
        {
            $this->_insert_usage_log($module_id=210,$request=1);
            $this->session->set_flashdata('broadcast_success',1);
            echo json_encode(array("status" => "1", "message" =>$this->lang->line("Campaing has been created successfully.")));            
        }
        
    }

    public function edit_quick_broadcast_campaign($id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(210,$this->module_access))
        redirect('home/login_page', 'location');

        $data["templates"]=$this->basic->get_enum_values("messenger_bot_broadcast","template_type");
        $data['body'] = 'messenger_broadcaster/quick_bulk_broadcast_edit';
        $data['page_title'] = $this->lang->line('Edit Quick Broadcast');  

        $data['page_info'] = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"bot_enabled"=>"1")),$select='',$join='',$limit='',$start=NULL,$order_by='page_name ASC');

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id)));  
        $data['postback_ids'] = $postback_id_list;

        $data["time_zone"]= $this->_time_zone_list();        

        $xdata=$this->basic->get_data("messenger_bot_broadcast",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        if(!isset($xdata[0])) exit();
        if($xdata[0]['posting_status']!='SCHEDULED') exit();
        if($xdata[0]['schedule_type']!='later') exit();
        $data['xdata']=$xdata[0];

        $page_id=$xdata[0]['page_id'];// database id      
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1",'template_for'=>"reply_message")),'','','',$start=NULL,$order_by='template_name ASC');        
        $poption=array();
        foreach ($postback_data as $key => $value) 
        {
            $poption[$value["postback_id"]]=$value['template_name'].' ['.$value['postback_id'].']';
        }
        $data['poption']=$poption;
    
        $this->_viewcontroller($data); 
    }

    public function quick_bulk_broadcast_edit_action()
    {
       if($this->session->userdata('user_type') != 'Admin' && !in_array(210,$this->module_access)) exit();
       $this->ajax_check();

       if($this->is_demo == '1')
       {
           if($this->session->userdata('user_type') == "Admin")
           {
               echo json_encode(array("status" => "0", "message" =>$this->lang->line("This function is disabled from admin account in this demo!!")));
               exit();
           }
       }

        $xid=$this->input->post("xid");
        $xdata=$this->basic->get_data("messenger_bot_broadcast",array("where"=>array("id"=>$xid,"user_id"=>$this->user_id)));
        $broadcast_id=isset($xdata[0]["broadcast_id"])?$xdata[0]["broadcast_id"]:"";
        $page_id=isset($xdata[0]["page_id"])?$xdata[0]["page_id"]:"";
        $xpage=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));
        $post_access_token=isset($xpage[0]["page_access_token"])?$xpage[0]["page_access_token"]:"";

        $response=$this->fb_rx_login->cancel_broadcast_schedule($broadcast_id,$post_access_token);
        if(isset($response['success']))
        {              
            $this->db->trans_start();
            $this->basic->delete_data("messenger_bot_broadcast",array("id"=>$xid,"user_id"=>$this->user_id));
            $this->_delete_usage_log(210,1);
            $this->db->trans_complete();
            if($this->db->trans_status() === false) 
            {
              echo json_encode(array('status'=>'0','message'=>$this->lang->line('Something went wrong, please try again.')));
              exit();
            }
        }
        else
        {
            $errormessage =  isset($response['error']) ? $response['error']['message'] : $this->lang->line("Something went wrong.");
            echo json_encode(array("status"=>"0","message"=>$errormessage));
            exit();
        }  

        echo json_encode(array('status'=>'1'));
        
    }
    */


}
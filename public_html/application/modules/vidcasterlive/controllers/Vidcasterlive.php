<?php
/*
Addon Name: VidCasterLive
Unique Name: vidcasterlive
Modules:
{
   "252":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"month",
      "module_name":"Facebook Live Streaming - Campaigns"
   },
   "254":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Facebook Live Streaming - Crossposting/Auto Share/Comment"
   }
}
Project ID: 41
Addon URI: https://demo.xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 1.0
Description: Facebook Live Streaming With Pre-recorded Video
*/

require_once("application/controllers/Home.php"); // loading home controller
class Vidcasterlive extends Home
{
	public $addon_data=array();
    public function __construct()
    {
        parent::__construct();
        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $addondata=$this->get_addon_data($addon_path); 
        $this->member_validity();
        $this->addon_data=$addondata;
        $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it
        $function_name=$this->uri->segment(2);
        if($function_name!="live_stream_ffmpeg_command") 
        {
             // all addon must be login protected
              //------------------------------------------------------------------------------------------
              if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');          
              // if you want the addon to be accessed by admin and member who has permission to this addon
              //-------------------------------------------------------------------------------------------
              if(isset($addondata['module_id']) && is_numeric($addondata['module_id']) && $addondata['module_id']>0)
              {
                   if($this->session->userdata('user_type') != 'Admin' && !in_array($addondata['module_id'],$this->module_access))
                   {
                        redirect('home/login_page', 'location');
                        exit();
                   }
              }
        }
    }

    public function index()
    {
        $this->live_scheduler_list();
    }

    public function live_stream_ffmpeg_command() // media caster go live now
    {
        $video_height = $this->input->get("video_height");
        $video_width = $this->input->get("video_width");

        $file_name = $this->input->get("filename");
        $stream_url = $this->input->get("strem_url");
        $secret = $this->input->get("secret");
        $campaign_id = $this->input->get("campaign_id");
        $ffmpeg=$this->config->item('ffmpeg_path');
        if($ffmpeg=="") $ffmpeg="ffmpeg";

        if($secret!="3582583258972672396532987") exit();

        if($file_name=="" || $stream_url=="" || $campaign_id=="") exit();

        $file_name=urldecode($file_name);
        $stream_url=urldecode($stream_url);

        if($video_width == '') $video_width=1280;
        if($video_height == '') $video_height=720;
        
        $url=$ffmpeg.' -re -i '.$file_name.' -codec:a aac  -ar 44100 -b:a 128k -pix_fmt yuv420p -profile:v baseline -s '.$video_width.'x'.$video_height.' -bufsize 6000k -vb 2048k -minrate 3000k -maxrate 4500k -deinterlace -strict -2 -vcodec libx264 -preset ultrafast -r 30 -g 60 -f flv "'.$stream_url.'" -loglevel error 2>&1';

        exec($url,$log);
        $log=json_encode($log);
        $this->basic->update_data("vidcaster_facebook_rx_live_scheduler",array("id"=>$campaign_id),array("stream_completed"=>date("Y-m-d H:i:s"),'ffmpeg_log'=>$log));

    }

    public function live_scheduler_list()
    {
        if($this->session->userdata("facebook_rx_fb_user_info")==0)
        redirect('social_accounts/index','refresh');

        if($this->session->userdata('user_type') != 'Admin' && !in_array(252,$this->module_access))
        redirect('home/login_page', 'location'); 

        $data=array("page_title"=>$this->lang->line("Live Streaming Campaign List"), "body" => "live_scheduler_list");
        $this->_viewcontroller($data);
    }

    public function live_scheduler_list_data()
    {

        if($this->session->userdata('user_type') != 'Admin' && !in_array(252,$this->module_access)) exit();
        $this->ajax_check();

        $post_type       = trim($this->input->post("post_type",true));
        $searching       = trim($this->input->post("searching",true));
        $post_date_range = $this->input->post("post_date_range",true);
        $display_columns = array("#","id", "publisher", "scheduler_name", "go_live", "formatted_status", "actions", "planned_time", "stream_started", "stream_completed", "ffmpeg_log");
        $search_columns = array('scheduler_name','publisher','stream_started');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple=array();
        if($post_date_range!="")
        {
            $exp = explode('|', $post_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date   = isset($exp[1])?$exp[1]:"";

            if($from_date!="Invalid date" && $to_date!="Invalid date")
            {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date   = date('Y-m-d', strtotime($to_date));
                $where_simple["Date_Format(schedule_time,'%Y-%m-%d') >="] = $from_date;
                $where_simple["Date_Format(schedule_time,'%Y-%m-%d') <="] = $to_date;
            }
        }

        if($post_type != '')
        {
            if($post_type == '2')
            {
                $where_simple['posting_status'] = $post_type;
                $where_simple['is_live'] = '1';
            }
            else
                $where_simple['posting_status'] = $post_type;
        }

        $sql = '';
        if($searching != '')
        {
            $sql = "(scheduler_name LIKE  '%".$searching."%' OR page_or_group_or_user_name LIKE '%".$searching."%')";
        }

        $where_simple['user_id'] = $this->user_id;
        $where_simple['facebook_rx_fb_user_info_id'] = $this->session->userdata("facebook_rx_fb_user_info");
        $where  = array('where'=>$where_simple);
        if($sql != '') $this->db->where($sql);

        $result = array();
        $table = "vidcaster_facebook_rx_live_scheduler";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit, $start, $order_by, $group_by='');

        if($sql != '') $this->db->where($sql);
        $total_rows_array = $this->basic->count_row($table, $where, $count="id");
        $total_result = $total_rows_array[0]['total_rows'];
        
        for($i=0;$i<count($info);$i++) 
        {           
            $publisher = ucfirst($info[$i]['page_or_group_or_user'])." : ".$info[$i]['page_or_group_or_user_name'];
            $info[$i]['publisher'] =  $publisher;

            $schedule_type = $info[$i]["schedule_type"];

            $scheduled_at = date("j M, y H:i",strtotime($info[$i]['schedule_time']));
            if($schedule_type=="later") $info[$i]['planned_time'] =  $scheduled_at;
            else  $info[$i]['planned_time'] = 'X';


            $post_url=$info[$i]['post_url'];
            if($post_url!="")
            $post_url = "<a target='_BLANK' href='".$post_url."' data-toggle='tooltip' title='".$this->lang->line("Visit")."' class='btn btn-circle btn-outline-info'><i class='fas fa-hand-point-right'></i></a>";
            else  
                $post_url = "<a data-toggle='tooltip' title='".$this->lang->line("Post is not available yet.")."' class='btn btn-circle btn-light pointer text-muted'><i class='fas fa-hand-point-right'></i></a>";

            $info[$i]['visit_post'] =  $post_url;   

            $info[$i]['delete'] = '<a class="btn btn-circle btn-outline-danger delete" data-toggle="tooltip" title="'.$this->lang->line("Delete Campaign").'" id="'.$info[$i]['id'].'" href="#"><i class="fas fa-trash-alt"></i></a>';; 

            if($info[$i]['is_live']=="0")
            $info[$i]['go_live'] = '<span data-toggle="tooltip" title="'.$this->lang->line('Event is not live yet').'" class="badge badge-light"><i class="fa fa-remove red"></i> '.$this->lang->line('No').'</span>';
            else $info[$i]['go_live'] = '<span data-toggle="tooltip" title="'.$this->lang->line('Event is live now').'" class="badge badge-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line('Yes').'</span>';

            if($info[$i]['scheduled_video_url']=="")
            $info_button = "<i class='fa fa-remove orange' title='".$this->lang->line("Not uploaded before")."'></i>";
            else
            $info_button = "<i class='fa fa-check-circle green' title='".$this->lang->line("Already uploaded")."'></i>";

            
            if($info[$i]['schedule_type']=='later')
            {
                if($info[$i]['posting_status']=='0')
                $info[$i]['edit'] =  "<a data-toggle='tooltip' title='".$this->lang->line("Edit")."' href='".base_url("vidcasterlive/edit_live_scheduler/".$info[$i]['id'])."' class='btn btn-circle btn-outline-warning'><i class='fa fa-edit'></i></a>";
                else $info[$i]['edit'] =  "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Only pending campaigns are editable.")."'  class='btn btn-circle btn-light text-muted'><i class='fa fa-edit'></i></a>";
            }
            else $info[$i]['edit'] =  "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Only scheduled campaigns are editable.")."'  class='btn btn-circle btn-light text-muted'><i class='fa fa-edit'></i></a>";
            
            $info[$i]['clone'] =  "<a data-toggle='tooltip' title='".$this->lang->line("Clone")."' href='".base_url("vidcasterlive/clone_live_scheduler/".$info[$i]['id'])."' class='btn btn-circle btn-outline-success'><i class='fa fa-clone'></i></a>";
            
            if($info[$i]['posting_status']=='2' && $info[$i]['is_live']=="1")
            $info[$i]["formatted_status"]="<span class='badge badge-light'><i class='fa fa-check-circle green'></i> ".$this->lang->line("Completed")."</span>";
            else if($info[$i]['posting_status']=='1')
            $info[$i]["formatted_status"]="<span class='badge badge-light'><i class='fa fa-spinner orange'></i> ".$this->lang->line("Processing")."</span>";
            else $info[$i]["formatted_status"]="<span class='badge badge-light'><i class='fa fa-remove red'></i> ".$this->lang->line("Pending")."</span>"; 

            $info[$i]['embed_code'] =  "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Get embed code")."' id='".$info[$i]['id']."' class='embed_code btn btn-circle btn-outline-primary'><i class='fa fa-code'></i></a>"; 
            
            $info[$i]['stream_info'] =  "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Get stream info")."' id='".$info[$i]['id']."' class='stream_info btn btn-circle btn-outline-primary'><i class='fa fa-code'></i></a>"; 

            if($info[$i]['stream_started']=="0000-00-00 00:00:00") $info[$i]['stream_started'] = "X";
            else $info[$i]['stream_started'] = date("j M, Y H:i:s",strtotime($info[$i]['stream_started']));

            if($info[$i]['stream_completed']=="0000-00-00 00:00:00") $info[$i]['stream_completed'] = "X";
            else  $info[$i]['stream_completed'] = date("j M, Y H:i:s",strtotime($info[$i]['stream_completed']));

            // Action section started from here
            $action_count = 6;
            $action_width = ($action_count*47)+20;
            $info[$i]['actions'] = '<div class="dropdown d-inline dropright">
            <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i></button>
            <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';

            $info[$i]['actions'] .= $info[$i]['visit_post'];
            $info[$i]['actions'] .= $info[$i]['embed_code'];
            $info[$i]['actions'] .= $info[$i]['edit'];
            $info[$i]['actions'] .= $info[$i]['clone'];
            $info[$i]['actions'] .= $info[$i]['stream_info'];
            $info[$i]['actions'] .= $info[$i]['delete'];

            $info[$i]['actions'] .= "</div></div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";


            $info[$i]['ffmpeg_log'] =  "<a href='#' data-toggle='tooltip' title='".$this->lang->line('FFMPEG Error Log')."' id='".$info[$i]['id']."' class='ffmpeg_log btn btn-circle btn-outline-primary'><i class='fa fa-bug'></i></a>"; 

        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }
  
    public function add_live_scheduler()
    {
        if($this->session->userdata("facebook_rx_fb_user_info")==0)
        redirect('social_accounts/index','refresh');

        if($this->session->userdata('user_type') != 'Admin' && !in_array(252,$this->module_access))
        redirect('home/login_page', 'location'); 

        $data['body'] = 'add_live_scheduler.php';
        $data['page_title'] = $this->lang->line('Create Live Campaign');
        $data["time_zone"]= $this->_time_zone_list();       
        $data["fb_user_info"]=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_page_info"]=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_group_info"]=$this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["app_info"]=$this->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->session->userdata("fb_rx_login_database_id"))));        
        $this->_viewcontroller($data);
    }

    public function add_live_scheduler_action()
    {    	
        $this->ajax_check();

        //************************************************//
        $status=$this->_check_usage($module_id=252,$request=1);
        if($status=="3") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Sorry, your monthly limit to create campaign is exceeded. You can not create another campaign this month.')));
            exit();
        }
        //************************************************//

        $this->load->library("fb_rx_login");

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
           $$key=$value;
           if(!is_array($value))
           $insert_data[$key]=$value;
        }

        if($video_width == '')
        {
            unset($insert_data["video_width"]);
            $video_width = 1280;
        }
        if($video_height == '')
        {
            unset($insert_data["video_height"]);
            $video_height = 720;
        }

        unset($insert_data["post_to"]);
        if(isset($insert_data['hidden_id'])) unset($insert_data['hidden_id']);
        $post_to_seperate = explode('-', $post_to);
        $page_or_group_or_user = isset($post_to_seperate["0"]) ? $post_to_seperate["0"] : "";
        if($page_or_group_or_user == "profile") $page_or_group_or_user ="user";
        $page_group_user_id = isset($post_to_seperate["1"]) ? $post_to_seperate["1"] : 0;
        $insert_data["page_or_group_or_user"] = $page_or_group_or_user;
        $insert_data["page_group_user_id"] = $page_group_user_id;
        if($auto_share_to_profile=="No") $insert_data["auto_share_to_profile"]= "0";
        else $insert_data["auto_share_to_profile"]= "1";

        $insert_data["user_id"] = $this->user_id;  
        $insert_data["facebook_rx_fb_user_info_id"] = $this->session->userdata("facebook_rx_fb_user_info");           
        $insert_data["last_updated_at"] = date("Y-m-d H:i:s");         

        if(!isset($crosspost_this_post_by_pages) || !is_array($crosspost_this_post_by_pages)) $crosspost_this_post_by_pages=array();
        $insert_data["crosspost_this_post_by_pages"] = json_encode($crosspost_this_post_by_pages);

        if(!isset($auto_share_this_post_by_pages) || !is_array($auto_share_this_post_by_pages)) $auto_share_this_post_by_pages=array();
        $insert_data["auto_share_this_post_by_pages"] = json_encode($auto_share_this_post_by_pages);

        $insert_data["auto_private_reply_status"]= "0";
        $insert_data["auto_private_reply_count"]= 0;
        $insert_data["auto_private_reply_done_ids"]= json_encode(array());
        $insert_data["posting_status"]= '2'; 
        // $message = $message." [Pre-recorded content] "; 
        $insert_data["message"]= $message; 


        if($page_or_group_or_user != "page") 
        {
            $insert_data["auto_share_post"] = "0";
            $auto_share_post = "0";
            $insert_data["auto_share_to_profile"] = "0";
            $auto_share_to_profile = "No";
            $insert_data["auto_share_this_post_by_pages"] = json_encode(array());
            $auto_share_this_post_by_pages = array();
        }

        $user_id_array=array($this->user_id);  
        $account_switching_id = $this->session->userdata("facebook_rx_fb_user_info"); // table > facebook_rx_fb_user_info.id
        $count=0;

        $fb_id = "";
        $use_access_token = "";
        if($page_or_group_or_user=="page") 
        {
            $token_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_group_user_id,"user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));
            $fb_id = isset($token_info[0]["page_id"]) ? $token_info[0]["page_id"] : "";
            $use_access_token = isset($token_info[0]["page_access_token"]) ? $token_info[0]["page_access_token"] : "";
            $page_or_group_or_user_name = isset($token_info[0]["page_name"]) ? $token_info[0]["page_name"] : "";
        }
        else if($page_or_group_or_user=="group") 
        {
            $token_info = $this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("id"=>$page_group_user_id,"user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));
            $fb_id = isset($token_info[0]["group_id"]) ? $token_info[0]["group_id"] : "";
            $use_access_token = isset($token_info[0]["group_access_token"]) ? $token_info[0]["group_access_token"] : "";
            $page_or_group_or_user_name = isset($token_info[0]["group_name"]) ? $token_info[0]["group_name"] : "";
        }
        else
        {
            $token_info = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$account_switching_id)));
            $fb_id = isset($token_info[0]["fb_id"]) ? $token_info[0]["fb_id"] : "";
            $use_access_token = isset($token_info[0]["access_token"]) ? $token_info[0]["access_token"] : "";
            $page_or_group_or_user_name = isset($token_info[0]["name"]) ? $token_info[0]["name"] : "";
        }

        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));    
        
        $insert_data["page_or_group_or_user_name"] = $page_or_group_or_user_name;
        

        if($schedule_type=="later")        
        {
            date_default_timezone_set($time_zone);
            $scheduled_publish_time= $schedule_time;
            $scheduled_publish_time=strtotime($scheduled_publish_time);
        }
        else
        {
            $scheduled_publish_time= date("Y-m-d H:i:s");
            $scheduled_publish_time=strtotime($scheduled_publish_time);
        }
        
        try
        {
            if($schedule_type=='later')
            {
                if($create_event=="1") 
                {
                    if($share_or_cross == 'crossposting' && !empty($crosspost_this_post_by_pages))
                        $response = $this->fb_rx_login->live_video_schedule($message,$scheduled_publish_time,$image_url,$use_access_token,$fb_id,$crosspost_this_post_by_pages); 
                    else
                        $response = $this->fb_rx_login->live_video_schedule($message,$scheduled_publish_time,$image_url,$use_access_token,$fb_id); 
                }
                else
                {
                    $insert_data2=array();
                    $insert_data2=$insert_data;
                    $insert_data2["posting_status"]="0"; // scheduled live and no live event , will process in cron job
                    if($this->basic->insert_data('vidcaster_facebook_rx_live_scheduler', $insert_data2))   
                    $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i> ".$this->lang->line("Live campaign has been created successfully."));
                    else  $return_val=array("status"=>"1","message"=>"<i class='fa fa-remove'></i> ".$this->lang->line("Live campaign has been failder to create. Something went wrong."));
                    $this->session->set_flashdata('success_message',1);
                    echo json_encode($return_val);
                    exit();
                }
            }
            else
            {
                if($share_or_cross == 'crossposting' && !empty($crosspost_this_post_by_pages))
                    $response = $this->fb_rx_login->live_video_schedule_direct($message,$use_access_token,$fb_id,$crosspost_this_post_by_pages); 
                else
                    $response = $this->fb_rx_login->live_video_schedule_direct($message,$use_access_token,$fb_id); 
            }
            $this->_insert_usage_log($module_id=252,$request=1);
        }
        catch(Exception $e) 
        {
          $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
          $return_val=array("status"=>"0","message"=>$error_msg);
          echo json_encode($return_val);
          $this->session->set_flashdata('error_message',1);
          exit();
        }

        $stream_url = isset($response["stream_url"]) ? $response["stream_url"] : "";
        $secure_stream_url = isset($response["secure_stream_url"]) ? $response["secure_stream_url"] : "";
       

        $post_id = isset($response["id"]) ? $response["id"] : "";
        $insert_data["stream_url"] = $stream_url;
        $insert_data["secure_stream_url"] = $secure_stream_url;
        $insert_data["post_id"] = $post_id;


        $temp_data=$this->fb_rx_login->get_post_permalink($post_id,$use_access_token);
        $permalink = isset($temp_data["permalink_url"]) ? $temp_data["permalink_url"] : ""; 
        $insert_data["post_url"] = $permalink;
        $live_video_id = $this->fb_rx_login->get_live_video_id($permalink);


        
        //go live now
        if($schedule_type=="now")
        {
            $filename=urlencode("upload_caster/live_video/".$scheduled_video_url);
            $streamurl = urlencode($stream_url);   
             $secure_stream_url=urlencode($secure_stream_url);

            $insert_data2=array();
            $insert_data2=$insert_data;
            $insert_data2["is_live"]="1";
            $insert_data2["stream_started"]=date("Y-m-d H:i:s");
            $campaign_id=0;

            if($this->basic->insert_data('vidcaster_facebook_rx_live_scheduler', $insert_data2))
            {
                $campaign_id = $this->db->insert_id(); 
                $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i> ".$this->lang->line("Live campaign has been created successfully."));

                $curl_url = base_url("vidcasterlive/live_stream_ffmpeg_command?filename={$filename}&strem_url={$streamurl}&secret=3582583258972672396532987&campaign_id={$campaign_id}&video_width={$video_width}&video_height={$video_height}");

                if($use_system_video == 'yes')
                    $this->fb_rx_login->live_stream_ffmpeg_command_run_using_curl($curl_url);
            }
            else  $return_val=array("status"=>"1","message"=>"<i class='fa fa-remove'></i> ".$this->lang->line("Live campaign has been failder to create. Something went wrong."));         
            
        }
        

        if($auto_share_post=="1" || $auto_like_post=="1" || $auto_comment=="1" ) sleep(20);


        if($page_or_group_or_user=="page" && $auto_like_post=="1" && ($this->session->userdata('user_type') == 'Admin' || in_array(254,$this->module_access)))
        {  
           foreach ($page_info as $key2 => $value2) 
            {
                $like_page_accesstoken =  isset($value2["page_access_token"]) ? $value2["page_access_token"] : ""; 
                try
                {
                    $this->fb_rx_login->auto_like($live_video_id,$like_page_accesstoken);
                }
                catch(Exception $e) 
                {
                  $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                  $return_val=array("status"=>"0","message"=>$error_msg);
                  // echo json_encode($return_val);
                  // exit();
                }
            } 
               
        }

        if($auto_comment=="1" && ($this->session->userdata('user_type') == 'Admin' || in_array(254,$this->module_access)))
        {               
            if($page_or_group_or_user=="page")
            {
                $table_name = "facebook_rx_fb_page_info";
                $access_token_field =  "page_access_token";  
            }
            else if($page_or_group_or_user=="user")
            {
                $table_name = "facebook_rx_fb_user_info";
                $access_token_field =  "access_token";               
            }
            else
            {
                $table_name = "facebook_rx_fb_group_info`";
                $access_token_field =  "group_access_token";

            }

            $access_data = $this->basic->get_data($table_name,array("where"=>array("id"=>$page_group_user_id)));
            $comment_page_accesstoken = isset($access_data["0"][$access_token_field]) ? $access_data["0"][$access_token_field] : "";

           try
           {
                $this->fb_rx_login->auto_comment($auto_comment_text,$live_video_id,$comment_page_accesstoken);   
           }
           catch(Exception $e) 
            {
              $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
              $return_val=array("status"=>"0","message"=>$error_msg);
              // echo json_encode($return_val);
              // exit();
            }
        }


       if($this->session->userdata('user_type') == 'Admin' || in_array(254,$this->module_access))
       {
            if(($auto_share_post=="1" || $auto_share_to_profile!="No"))
            {                
                if($permalink!='')
                {
                    if($auto_share_post=="1")
                    {
                       foreach ($page_info as $key => $value) 
                       {
                            if(!in_array($value["id"],$auto_share_this_post_by_pages)) continue;
                            if($page_or_group_or_user="page" && ($page_group_user_id==$value["id"])) continue;
                            $share_page_id =  isset($value["page_id"]) ? $value["page_id"] : ""; 
                            $share_page_accesstoken =  isset($value["page_access_token"]) ? $value["page_access_token"] : "";
                            try
                            {
                                $this->fb_rx_login->feed_post("",$permalink,"","","","",$share_page_accesstoken,$share_page_id);
                            }
                            catch(Exception $e) 
                            {
                              $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                              $return_val=array("status"=>"0","message"=>$error_msg);
                              // echo json_encode($return_val);
                              // exit();
                            }
                        } 
                    }

                    if($auto_share_to_profile!="No")
                    {                        
                        $profile_info = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("id"=> $account_switching_id,"user_id"=>$this->user_id)));  
                        $user_access_token =  isset($profile_info[0]["access_token"]) ? $profile_info[0]["access_token"] : ""; 
                        $user_fb_id =  isset($profile_info[0]["fb_id"]) ? $profile_info[0]["fb_id"] : ""; 
                        try
                        {
                            $this->fb_rx_login->feed_post("",$permalink,"","","","",$user_access_token,$user_fb_id);
                        }
                        catch(Exception $e) 
                        {
                          $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                          $return_val=array("status"=>"0","message"=>$error_msg);
                          // echo json_encode($return_val);
                          // exit();
                        }
                    }  

                }
            }
          
       }      
       // $report_link = "<a href='".base_url("facebook_rx_live_scheduler/live_scheduler_list")."'> Go to live scheduler list</a>";
       
       if($schedule_type=='later') 
       {
            if($this->basic->insert_data('vidcaster_facebook_rx_live_scheduler', $insert_data))
            {
                $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i> ".$this->lang->line("Live campaign has been created successfully."));
            }
            else
            {
                $return_val=array("status"=>"1","message"=>"<i class='fa fa-remove'></i> ".$this->lang->line("Live campaign has been failder to create. Something went wrong."));
            }
       }      

       echo json_encode($return_val);        
    }

    public function upload_scheduled_video($id=0)
    {
        if($id==0 || $id=="") exit();
        $data['body'] = 'schedule_video.php';
        $data['page_title'] = $this->lang->line('Schedule Video');       
        $data["xdata"] = $this->basic->get_data("vidcaster_facebook_rx_live_scheduler",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $this->_viewcontroller($data);
    }
  

    public function upload_live_video()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ffmpeg=$this->config->item('ffmpeg_path');
        if($ffmpeg=="") $ffmpeg="ffmpeg";

        $ret=array();
        $output_dir = FCPATH."upload_caster/live_video";
        $thumb_dir = FCPATH."upload_caster/live_video/thumb/".$this->user_id;
        if (!file_exists($thumb_dir)) {
            mkdir($thumb_dir, 0777, true);
        }
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="video_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;

            $allow=".avi,.divx,.flv,.mkv,.mov,.mp4,.mpeg,.mpeg4,.mpg,.wmv";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                $custom_error['jquery-upload-file-error']=$this->lang->line('File format is not supported.');
                echo json_encode($custom_error);
                exit();
            }

            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            $this->session->set_userdata("go_live_video_file_path_name", $output_dir.'/'.$filename);
            $this->session->set_userdata("go_live_video_filename", $filename);
            exec($ffmpeg." -ss 00:00:05 -i ".$this->session->userdata("go_live_video_file_path_name")." -vf scale=320:200 -vframes 1 ".$thumb_dir."/".$filename.".jpg"); 
            echo json_encode($filename);
        }
    }

    public function update_upload_live_video()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();       
        $id = $this->input->post("id");
        $file_name  =  $this->session->userdata("go_live_video_filename");
        if($this->basic->update_data("vidcaster_facebook_rx_live_scheduler",array("id"=>$id,"user_id"=>$this->user_id),array("scheduled_video_url"=>$file_name)))
        {
            $this->session->set_flashdata("success_message",1);
            $this->session->unset_userdata("go_live_video_file_path_name");
            $this->session->unset_userdata("go_live_video_filename");
            echo "1";
        }
        else echo "0";

    }

    public function edit_live_scheduler($id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(252,$this->module_access))
        redirect('home/login_page', 'location'); 

        if($id==0 || $id=="") exit();
        $data['body'] = 'edit_live_scheduler.php';
        $data['page_title'] = $this->lang->line('Edit Live Campaign');
        $data["time_zone"]= $this->_time_zone_list();       
        $data["fb_user_info"]=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_page_info"]=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_group_info"]=$this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["app_info"]=$this->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->session->userdata("fb_rx_login_database_id"))));        
        $data["xdata"] = $this->basic->get_data("vidcaster_facebook_rx_live_scheduler",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));

        $this->_viewcontroller($data);
    }

    public function edit_live_scheduler_action()
    {
        $this->ajax_check();

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
           $$key=$value;
           if(!is_array($value))
           $insert_data[$key]=$value;
        }

        if($video_width == '') unset($insert_data["video_width"]);
        if($video_height == '') unset($insert_data["video_height"]);

        unset($insert_data["post_to"]);
        unset($insert_data["hidden_id"]);
        $post_to_seperate = explode('-', $post_to);
        $page_or_group_or_user = isset($post_to_seperate["0"]) ? $post_to_seperate["0"] : "";
        if($page_or_group_or_user == "profile") $page_or_group_or_user ="user";
        $page_group_user_id = isset($post_to_seperate["1"]) ? $post_to_seperate["1"] : 0;
        $insert_data["page_or_group_or_user"] = $page_or_group_or_user;
        $insert_data["page_group_user_id"] = $page_group_user_id;
        if($auto_share_to_profile=="No") $insert_data["auto_share_to_profile"]= "0";
        else $insert_data["auto_share_to_profile"]= "1";
         
        $insert_data["last_updated_at"] = date("Y-m-d H:i:s");      

        if(!isset($crosspost_this_post_by_pages) || !is_array($crosspost_this_post_by_pages)) $crosspost_this_post_by_pages=array();
        $insert_data["crosspost_this_post_by_pages"] = json_encode($crosspost_this_post_by_pages);     

        if(!isset($auto_share_this_post_by_pages) || !is_array($auto_share_this_post_by_pages)) $auto_share_this_post_by_pages=array();
        $insert_data["auto_share_this_post_by_pages"] = json_encode($auto_share_this_post_by_pages);

        $insert_data["auto_private_reply_status"]= "0";
        // $message = $message." [Pre-recorded content] "; 
        $insert_data["message"]= $message; 


        if($page_or_group_or_user != "page") 
        {
            $insert_data["auto_share_post"] = "0";
            $auto_share_post = "0";
            $insert_data["auto_share_to_profile"] = "0";
            $auto_share_to_profile = "No";
            $insert_data["auto_share_this_post_by_pages"] = json_encode(array());
            $auto_share_this_post_by_pages = array();
        }

        $user_id_array=array($this->user_id);  
        $account_switching_id = $this->session->userdata("facebook_rx_fb_user_info"); // table > facebook_rx_fb_user_info.id
        $count=0;

        $fb_id = "";
        $use_access_token = "";
        if($page_or_group_or_user=="page") 
        {
            $token_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_group_user_id,"user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));
            $fb_id = isset($token_info[0]["page_id"]) ? $token_info[0]["page_id"] : "";
            $use_access_token = isset($token_info[0]["page_access_token"]) ? $token_info[0]["page_access_token"] : "";
            $page_or_group_or_user_name = isset($token_info[0]["page_name"]) ? $token_info[0]["page_name"] : "";
        }
        else if($page_or_group_or_user=="group") 
        {
            $token_info = $this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("id"=>$page_group_user_id,"user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));
            $fb_id = isset($token_info[0]["group_id"]) ? $token_info[0]["group_id"] : "";
            $use_access_token = isset($token_info[0]["group_access_token"]) ? $token_info[0]["group_access_token"] : "";
            $page_or_group_or_user_name = isset($token_info[0]["group_name"]) ? $token_info[0]["group_name"] : "";
        }
        else
        {
            $token_info = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("id"=>$page_group_user_id,"user_id"=>$this->user_id,"id"=>$account_switching_id)));
            $fb_id = isset($token_info[0]["fb_id"]) ? $token_info[0]["fb_id"] : "";
            $use_access_token = isset($token_info[0]["access_token"]) ? $token_info[0]["access_token"] : "";
            $page_or_group_or_user_name = isset($token_info[0]["name"]) ? $token_info[0]["name"] : "";
        }

        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));    
        
        $insert_data["page_or_group_or_user_name"] = $page_or_group_or_user_name;

    
        date_default_timezone_set($time_zone);
        $scheduled_publish_time= $schedule_time;
        $scheduled_publish_time=strtotime($scheduled_publish_time);        
       

        if($this->basic->update_data('vidcaster_facebook_rx_live_scheduler',array("id"=>$hidden_id,"user_id"=>$this->user_id),$insert_data))   
        {
            $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i> ".$this->lang->line("Live campaign has been edited successfully."));
            $this->session->set_flashdata('success_message',1);
        }
        else 
        {
            $return_val=array("status"=>"1","message"=>"<i class='fa fa-remove'></i> ".$this->lang->line("Live campaign has been failder to edit. Something went wrong."));
            $this->session->set_flashdata('error_message',1);
        }
        echo json_encode($return_val); 
    }


    public function clone_live_scheduler($id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(252,$this->module_access))
        redirect('home/login_page', 'location'); 

        if($id==0 || $id=="") exit();
        $data['body'] = 'clone_live_scheduler.php';
        $data['page_title'] = $this->lang->line('Clone Live campaign');
        $data["time_zone"]= $this->_time_zone_list();       
        $data["fb_user_info"]=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_page_info"]=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_group_info"]=$this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["app_info"]=$this->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->session->userdata("fb_rx_login_database_id"))));        
        $data["xdata"] = $this->basic->get_data("vidcaster_facebook_rx_live_scheduler",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $this->_viewcontroller($data);
    }


    public function meta_info_grabber()
    {
        if($_POST)
        {
            $link= $this->input->post("link");
            $this->load->library("fb_rx_login");
            $response=$this->fb_rx_login->get_meta_tag_fb($link);
            echo json_encode($response);
        }
    } 


    public function upload_image_only()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ret=array();
        $output_dir = FCPATH."upload_caster/scheduler";
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="image_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;

            $allow=".jpg,.jpeg,.png,.gif";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                $custom_error['jquery-upload-file-error']=$this->lang->line('File format is not supported.');
                echo json_encode($custom_error);
                exit();
            }
            
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            echo json_encode($filename);
        }
    }


    public function delete_uploaded_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();

        $output_dir = FCPATH."upload_caster/scheduler/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                @unlink($filePath);
             }
        }
    }

    public function delete_uploaded_live_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();

        $output_dir = FCPATH."upload_caster/live_video/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                @unlink($filePath);
             }
        }
    }

    public function date_display_formatter()
    {
        $this->ajax_check();
        $date_time = $this->input->post("schedule_time");
        if($date_time=="") {echo ""; exit();}
        echo date("F j",strtotime($date_time))." at ".date("g:ia",strtotime($date_time));
    }

    public function delete_post()
    {
        $this->ajax_check();
        $id=$this->input->post("id");

        $xdata = $this->basic->get_data("vidcaster_facebook_rx_live_scheduler",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $posting_status  = isset($xdata[0]["posting_status"]) ? $xdata[0]["posting_status"] : "0";
        $is_live  = isset($xdata[0]["is_live"]) ? $xdata[0]["is_live"] : "0";

        $video_file_name = isset($xdata[0]['scheduled_video_url']) ? $xdata[0]['scheduled_video_url'] : '';
        $user_id = isset($xdata[0]['user_id']) ? $xdata[0]['user_id'] : '0';

        if($posting_status=="1")
        {
            echo "0"; 
            exit();
        }

        if($posting_status=="0" && $is_live=="0") // removing usage data if deleted and campaign is pending
        $this->_delete_usage_log($module_id=252,$request=1);

        if($this->basic->delete_data("vidcaster_facebook_rx_live_scheduler",array("id"=>$id,"user_id"=>$this->user_id)))
        {
            $video_file_link = "upload_caster/live_video/".$video_file_name;
            $image_file_link = "upload_caster/live_video/thumb/".$user_id."/".$video_file_name.".jpg";
            @unlink($video_file_link);
            @unlink($image_file_link);
            echo "1";
        }
        else echo "0"; 
    } 


    public function get_embed_code()
    {
        $this->ajax_check();
        $id=$this->input->post("id");

        $video_data = $this->basic->get_data("vidcaster_facebook_rx_live_scheduler",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));

        $post_id = isset($video_data[0]["post_id"]) ? $video_data[0]["post_id"] : "";  
        $page_or_group_or_user = isset($video_data[0]["page_or_group_or_user"]) ? $video_data[0]["page_or_group_or_user"] : "";  
        $page_group_user_id = isset($video_data[0]["page_group_user_id"]) ? $video_data[0]["page_group_user_id"] : "";  
        $facebook_rx_fb_user_info_id = isset($video_data[0]["facebook_rx_fb_user_info_id"]) ? $video_data[0]["facebook_rx_fb_user_info_id"] : ""; 

        $fb_user_info = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('id'=>$facebook_rx_fb_user_info_id)),array('facebook_rx_config_id'));

        if($page_or_group_or_user=="page")
        {
            $table_name = "facebook_rx_fb_page_info";
            $access_token_field =  "page_access_token";  
        }
        else if($page_or_group_or_user=="user")
        {
            $table_name = "facebook_rx_fb_user_info";
            $access_token_field =  "access_token";               
        }
        else
        {
            $table_name = "facebook_rx_fb_group_info`";
            $access_token_field =  "group_access_token";

        }

        $access_data = $this->basic->get_data($table_name,array("where"=>array("id"=>$page_group_user_id)));
        $page_access_token = isset($access_data["0"][$access_token_field]) ? $access_data["0"][$access_token_field] : "";
       
        $this->load->library("fb_rx_login");
        $this->fb_rx_login->app_initialize($fb_user_info[0]['facebook_rx_config_id']);
        $response = $this->fb_rx_login->get_live_video_embed_code($post_id,$page_access_token);

        $embed_code = isset($response["embed_html"]) ?  urldecode($response["embed_html"]) : "";
        $embed_code2 = preg_replace('/(width|height)="\d*"\s/', "", $embed_code);
        
        $embed_html = "<center><style>iframe{width:100% !important; height:450px !important;}</style><pre class='language-javascript'><code id='test' class='dlanguage-javascript'>".htmlentities($embed_code)."</code></pre><br/><br/><b>".$this->lang->line("Preview")."</b> <br/>".$embed_code2."</center>";

        echo $embed_html;
        
    }


    public function get_stream_info()
    {
    	$this->ajax_check();
        $id=$this->input->post("id");
        $video_data = $this->basic->get_data("vidcaster_facebook_rx_live_scheduler",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $stream_url = isset($video_data[0]['stream_url']) ? $video_data[0]['stream_url'] : '';
    	$response['stream_url'] = $stream_url;
    	$stream_info = explode('/rtmp/', $stream_url);
    	$stream_key = isset($stream_info[1]) ? $stream_info[1] : '';
    	$server_url = isset($stream_info[0]) ? $stream_info[0] : '';
    	$response['stream_key'] = $stream_key;
    	if($server_url != '')
	    	$response['server_url'] = $server_url.'/rtmp/';
	    else
	    	$response['server_url'] = $server_url;

    	echo json_encode($response);
    }




    public function get_ffmpeg_log()
    {
        $this->ajax_check();
        $id=$this->input->post("id");

        $video_data = $this->basic->get_data("vidcaster_facebook_rx_live_scheduler",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $ffmpeg_log = isset($video_data[0]["ffmpeg_log"]) ? $video_data[0]["ffmpeg_log"] : "";  

        echo "<pre>";
        print_r(json_decode($ffmpeg_log,true));
    }


    public function get_crosspostallowed_pages()
    {
        $this->ajax_check();
        $page_table_id = $this->input->post("page_table_id",true);
        $campaign_id = $this->input->post("campaign_id",true);
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where'=>['id'=>$page_table_id,'user_id'=>$this->user_id]],['page_access_token','page_id']);
        $page_id = isset($page_info[0]['page_id']) ? $page_info[0]['page_id'] : 0;
        $page_access_token = isset($page_info[0]['page_access_token']) ? $page_info[0]['page_access_token'] : 0;

        if($page_id != 0)
        {
            $this->load->library('fb_rx_login');
            $whitelisted_pages_info = $this->fb_rx_login->get_crosspost_whitelisted_pages($page_id,$page_access_token);
            $whitelisted_pages = $whitelisted_pages_info['data'];
            if(empty($whitelisted_pages))
                echo "<option>".$this->lang->line('You have no whitelisted pages for crosspost.')."</option>";
            else
            {
                $crosspost_to_pages = [];
                if($campaign_id != 0)
                {
                    $campaign_info = $this->basic->get_data('vidcaster_facebook_rx_live_scheduler',['where'=>['id'=>$campaign_id]],['crosspost_this_post_by_pages']);
                    $crosspost_to_pages = json_decode($campaign_info[0]['crosspost_this_post_by_pages'],true);
                }

                $str = '';
                foreach($whitelisted_pages as $value)
                {
                    $page_id = $value['id'];
                    $page_name = $value['name'];

                    if(in_array($value['id'], $crosspost_to_pages)) 
                    {
                        $str .= "<option selected='selected' value='{$page_id}'>{$page_name}</option>";
                    }
                    else
                        $str .= "<option value='{$page_id}'>{$page_name}</option>";
                }
                echo $str;
            }

        }
        else
        {
            echo "<option>".$this->lang->line('You have no whitelisted pages for crosspost.')."</option>";
        }

    }




    public function activate()
    {
        if(!$_POST) exit();
   
        $is_free_addon=false; 
        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $purchase_code=$this->input->post('purchase_code');
        if(!$is_free_addon)
        {
            $this->addon_credential_check($purchase_code,strtolower($addon_controller_name)); // retuns json status,message if error
        }  
        //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
        $sidebar=array(); 
        // mysql raw query needed to run, it's an array, put each query in a seperate index, create table query must should IF NOT EXISTS
        $sql=array
        (
            0=>"CREATE TABLE IF NOT EXISTS `vidcaster_facebook_rx_live_scheduler` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `facebook_rx_fb_user_info_id` int(11) NOT NULL,
              `scheduler_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
              `page_group_user_id` int(11) NOT NULL,
              `page_or_group_or_user` enum('page','group','user') COLLATE utf8mb4_unicode_ci NOT NULL,
              `page_or_group_or_user_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
              `message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `image_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `use_system_video` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes',
              `share_or_cross` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
              `crosspost_enable_disable` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
              `crosspost_this_post_by_pages` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `auto_share_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
              `auto_share_this_post_by_pages` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `auto_share_to_profile` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
              `auto_like_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
              `auto_private_reply` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
              `auto_private_reply_text` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `auto_private_reply_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'taken by cronjob or not',
              `auto_private_reply_count` int(11) NOT NULL,
              `auto_private_reply_done_ids` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `auto_comment` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
              `auto_comment_text` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
              `posting_status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2',
              `post_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'fb post id',
              `post_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `stream_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `scheduled_video_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `secure_stream_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `create_event` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
              `schedule_type` enum('now','later') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'later',
              `schedule_time` datetime NOT NULL,
              `time_zone` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
              `last_updated_at` datetime NOT NULL,
              `is_live` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
              `stream_started` datetime NOT NULL,
              `stream_completed` datetime NOT NULL,
              `video_width` int(11) NOT NULL DEFAULT '1280',
              `video_height` int(11) NOT NULL DEFAULT '720',
              `ffmpeg_log` text COLLATE utf8mb4_unicode_ci NOT NULL,
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`,`facebook_rx_fb_user_info_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        ); 
        //send blank array if you does not need sidebar entry,send a blank array if your addon does not need any sql to run
        $this->register_addon($addon_controller_name,$sidebar,$sql,$purchase_code,"Facebook Live Streaming - Campaign"); 
    }

    public function deactivate()
    {        
        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        // only deletes add_ons,modules and menu, menu_child1 table entires and put install.txt back, it does not delete any files or custom sql
        $this->unregister_addon($addon_controller_name);         
    }
    public function delete()
    {        
        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
         // mysql raw query needed to run, it's an array, put each query in a seperate index, drop table/column query should have IF EXISTS
        $sql=array
        (
          0=>"DROP TABLE IF EXISTS `vidcaster_facebook_rx_live_scheduler`;"
        );  
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }

    

}
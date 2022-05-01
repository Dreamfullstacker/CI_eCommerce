<?php
/*
Addon Name: Comment Reply Enhancers
Unique Name: comment_reply_enhancers
Modules:
{
   "88":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Comment Reply Enhancers : Comment Hide/Delete and Reply with multimedia content"
   },
   "201":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"month",
      "module_name":"Comment Reply Enhancers : Comment & Bulk Tag Campaign"
   },
   "202":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"month",
      "module_name":"Comment Reply Enhancers : Bulk Comment Reply Campaign"
   },
   "204":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Comment Reply Enhancers : Full Page Auto Reply"
   },
   "206":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Comment Reply Enhancers : Full Page Auto Like/Share"
   }
}
Project ID: 29
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 2.0
Description: 
*/

require_once("application/controllers/Home.php"); // loading home controller

class Comment_reply_enhancers extends Home
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
        $this->addon_data=$addondata;

        $this->member_validity();

        $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it

        $function_name=$this->uri->segment(2);
        if($function_name!="auto_like_on_post" && $function_name!="auto_share_on_post")
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
          $this->page_list(); 
  	}



    public function sync_commenter_info($value='')
    {
       if(!$_POST) exit();
       if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access) && !in_array(201,$this->module_access))
       exit(); 

       $page_id=$this->input->post('page_id');
       $post_id=$this->input->post('post_id');
       $post_created_at=$this->input->post('post_created_at');
       $post_description=$this->input->post('post_description');
       $post_description=htmlspecialchars_decode($post_description);
       
       $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));
       $page_access_token=isset($page_info[0]['page_access_token']) ? $page_info[0]['page_access_token'] : "";
       $fb_page_id=isset($page_info[0]['page_id']) ? $page_info[0]['page_id'] : "";
       $page_name=isset($page_info[0]['page_name']) ? $page_info[0]['page_name'] : "";
       $page_profile=isset($page_info[0]['page_profile']) ? $page_info[0]['page_profile'] : "";

       if($page_access_token=='')
       {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
            exit();
       }
       $this->load->library("fb_rx_login");
       $comment_list = $this->fb_rx_login->get_all_comment_of_post_pagination($post_id,$page_access_token);
       
       $commenter_count=isset($comment_list["commenter_info"]) ? count($comment_list["commenter_info"]) : 0 ;
       if(isset($comment_list["commenter_info"]))
       {
          if(isset($comment_list["commenter_info"][$fb_page_id])) 
          {
            $commenter_count--; // if page is also commenter
            unset($comment_list["commenter_info"][$fb_page_id]);
          }
       }

       $comment_count=isset($comment_list["comment_info"]) ? count($comment_list["comment_info"]) : 0 ;
       
       $insert_data=array
       (
        "facebook_rx_fb_user_info_id" => $this->session->userdata("facebook_rx_fb_user_info"),
        "user_id" => $this->user_id,
        "page_info_table_id" => $page_id,
        "page_id" => $fb_page_id,
        "page_name" => $page_name,
        "page_profile" => $page_profile,
        "post_id" => $post_id,
        "post_description" => $post_description,
        "post_created_at" => substr($post_created_at, 0, 19),
        "last_updated_at" => date("Y-m-d H:i:s"),
        "commenter_count" => $commenter_count
       );  

       $this->db->trans_start();    

       $this->basic->insert_data("tag_machine_enabled_post_list",$insert_data);
       $tag_machine_enabled_post_list_id=$this->db->insert_id();

       $insert_batch=array();
       if(isset($comment_list["commenter_info"]))
       {
          foreach ($comment_list["commenter_info"] as $key => $value) 
          {
            $last_comment_time=isset($value["last_comment_time"]) ? $value["last_comment_time"] : "";
            $last_comment_time=str_replace('T',' ',$last_comment_time);
            $last_comment_time=substr($last_comment_time, 0, 19);

            $insert_batch[]=array
            (
                "tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,
                "facebook_rx_fb_user_info_id" => $this->session->userdata("facebook_rx_fb_user_info"),
                "user_id" => $this->user_id,
                "page_info_table_id" => $page_id,
                "page_id" => $fb_page_id,
                "page_name" => $page_name,
                "post_id"=>$post_id,
                "last_comment_id"=>$value["last_comment_id"],
                "last_comment_time"=>$last_comment_time,
                "commenter_fb_id"=>$key,
                "commenter_name"=>$value["name"]
            );
          }
       }

       $insert_batch2=array();
       if(isset($comment_list["comment_info"]))
       {
          foreach ($comment_list["comment_info"] as $key => $value) 
          {            
            if($value["commenter_id"]==$fb_page_id) // skipping self comments
            {
                $comment_count--;
                continue;
            }

            $comment_time=isset($value["created_time"]) ? $value["created_time"] : "";
            $comment_time=substr($comment_time, 0, 19);

            $insert_batch2[]=array
            (
                "tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,
                "facebook_rx_fb_user_info_id" => $this->session->userdata("facebook_rx_fb_user_info"),
                "user_id" => $this->user_id,
                "page_info_table_id" => $page_id,
                "page_id" => $fb_page_id,
                "page_name" => $page_name,
                "post_id"=> $post_id,
                "comment_id" => $value["comment_id"],
                "comment_text" => $value["message"],
                "commenter_fb_id" => $value["commenter_id"],
                "commenter_name"=>$value["commenter_name"],
                "comment_time" => $comment_time
            );
          }
       }

       $this->basic->update_data("tag_machine_enabled_post_list",array("id"=>$tag_machine_enabled_post_list_id),array("comment_count"=>$comment_count));

       if($commenter_count>0)
       $this->db->insert_batch("tag_machine_commenter_info",$insert_batch);

       if($comment_count>0)
       $this->db->insert_batch("tag_machine_comment_info",$insert_batch2);

       $this->db->trans_complete();
       if($this->db->trans_status() === false) 
       {
         echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
         exit();
       }

       $button_replace = "<label class='label label-success'><i class='fa fa-check'></i> ".$this->lang->line("Tag Enabled successfully")."</label>";
       $report_link = $this->lang->line("Post has been successfully enabled for tagging and commenter information has been fetched. To create campaign go")." <a href='".base_url('comment_reply_enhancers/post_list/0/').$post_id."'>".$this->lang->line('here')."</a>";
       echo json_encode(array('status'=>'1','message'=>$report_link,"button_replace"=>$button_replace));
      
    }

    public function manual_sync_commenter_info()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access) && !in_array(201,$this->module_access))
        exit();
        if(!$_POST) exit();

        $page_id=$this->input->post('page_id');
        $post_id=$this->input->post('post_id');

        $page_table_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$page_id)));
        $fb_page_id = isset($page_table_info[0]['page_id']) ? $page_table_info[0]['page_id'] : "";
        $page_access_token = isset($page_table_info[0]['page_access_token']) ? $page_table_info[0]['page_access_token'] : "";
        $page_name = isset($page_table_info[0]['page_name']) ? $page_table_info[0]['page_name'] : "";
        $page_profile = isset($page_table_info[0]['page_profile']) ? $page_table_info[0]['page_profile'] : "";

        if($fb_page_id=='' || $page_access_token=='')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('page not found.')));
            exit();
        }
        
        if(strpos($post_id,'_')!==FALSE)
        $post_id_use=$post_id;
        else $post_id_use = $fb_page_id."_".$post_id;

        if($this->basic->is_exist("tag_machine_enabled_post_list",array("facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"page_info_table_id"=>$page_id,"post_id"=>$post_id_use),'id'))
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("this post is already enabled for tagging.")));
            exit();
        }

        try
        {
            $this->load->library('fb_rx_login');
            $post_info = $this->fb_rx_login->get_post_info_by_id($post_id_use,$page_access_token);

            if(isset($post_info['error']))
            {
                $response['error_msg'] = $post_info['error']['message'];
                echo json_encode(array('status'=>'0','message'=>$post_info['error']['message']));
                exit();
            }

            if(empty($post_info))
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("please provide correct post id.")));
                exit();
            }

            foreach ($post_info as $key => $value) 
            {
                $post_description=isset($value["message"])?$value["message"]:"";
                $post_created_at=isset($value["created_time"])?$value["created_time"]:"";
                $post_created_at=str_replace('T',' ',$post_created_at);
                $post_created_at=substr($post_created_at, 0, 19);
            }

            $this->load->library("fb_rx_login");
            $comment_list = $this->fb_rx_login->get_all_comment_of_post_pagination($post_id_use,$page_access_token);
           
            $commenter_count=isset($comment_list["commenter_info"]) ? count($comment_list["commenter_info"]) : 0 ;
            if(isset($comment_list["commenter_info"]))
            {
              if(isset($comment_list["commenter_info"][$fb_page_id])) 
              {
                $commenter_count--; // if page is also commenter
                unset($comment_list["commenter_info"][$fb_page_id]);
              }
            }

            $comment_count=isset($comment_list["comment_info"]) ? count($comment_list["comment_info"]) : 0 ;

            $insert_data=array
            (
                "facebook_rx_fb_user_info_id" => $this->session->userdata("facebook_rx_fb_user_info"),
                "user_id" => $this->user_id,
                "page_info_table_id" => $page_id,
                "page_id" => $fb_page_id,
                "page_name" => $page_name,
                "page_profile" => $page_profile,
                "post_id" => $post_id_use,
                "post_description" => $post_description,
                "post_created_at" => $post_created_at,
                "last_updated_at" => date("Y-m-d H:i:s"),
                "commenter_count" => $commenter_count
            ); 


           $this->db->trans_start();       

           $this->basic->insert_data("tag_machine_enabled_post_list",$insert_data);
           $tag_machine_enabled_post_list_id=$this->db->insert_id();

           $insert_batch=array();
           if(isset($comment_list["commenter_info"]))
           {
              foreach ($comment_list["commenter_info"] as $key => $value) 
              {                
                $last_comment_time=$value["last_comment_time"];
                $last_comment_time=str_replace('T',' ',$last_comment_time);
                $last_comment_time=substr($last_comment_time, 0, 19);

                $insert_batch[]=array
                (
                    "tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,
                    "facebook_rx_fb_user_info_id" => $this->session->userdata("facebook_rx_fb_user_info"),
                    "user_id" => $this->user_id,
                    "page_info_table_id" => $page_id,
                    "page_id" => $fb_page_id,
                    "page_name" => $page_name,
                    "post_id"=>$post_id_use,
                    "last_comment_id"=>$value["last_comment_id"],
                    "last_comment_time"=>$last_comment_time,
                    "commenter_fb_id"=>$key,                    
                    "commenter_name"=>$value["name"]
                );
              }
           }

           $insert_batch2=array();
           if(isset($comment_list["comment_info"]))
           {
              foreach ($comment_list["comment_info"] as $key => $value) 
              {                
                if($value["commenter_id"]==$fb_page_id) // skipping self comments
                {
                    $comment_count--;
                    continue;
                }

                $comment_time=$value["created_time"];
                $comment_time=substr($comment_time, 0, 19);

                $insert_batch2[]=array
                (
                    "tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,
                    "facebook_rx_fb_user_info_id" => $this->session->userdata("facebook_rx_fb_user_info"),
                    "user_id" => $this->user_id,
                    "page_info_table_id" => $page_id,
                    "page_id" => $fb_page_id,
                    "page_name" => $page_name,
                    "post_id"=> $post_id,
                    "comment_id" => $value["comment_id"],
                    "comment_text" => $value["message"],
                    "commenter_fb_id" => $value["commenter_id"],
                    "commenter_name"=>$value["commenter_name"],
                    "comment_time" => $comment_time
                );
              }
           }

           $this->basic->update_data("tag_machine_enabled_post_list",array("id"=>$tag_machine_enabled_post_list_id),array("comment_count"=>$comment_count));

           if($commenter_count>0)
           $this->db->insert_batch("tag_machine_commenter_info",$insert_batch);

           if($comment_count>0)
           $this->db->insert_batch("tag_machine_comment_info",$insert_batch2);

           $this->db->trans_complete();
           if($this->db->trans_status() === false) 
           {
             echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
             exit();
           }

           echo json_encode(array('status'=>'1','message'=>$this->lang->line("post has been successfully enabled for tagging and commenter information has been fetched.")));

        }
        catch(Exception $e)
        {
            echo json_encode(array('status'=>'0','message'=>$e->getMessage()));
        }

    }

    public function rescan_commenter_info()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access) && !in_array(201,$this->module_access))
        exit();
        if(!$_POST) exit();

        $page_id=$this->input->post('page_id');
        $post_id=$this->input->post('post_id');
        $tag_machine_enabled_post_list_id=$this->input->post('enable_id');

        $page_table_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$page_id)));
        $fb_page_id = isset($page_table_info[0]['page_id']) ? $page_table_info[0]['page_id'] : "";
        $page_access_token = isset($page_table_info[0]['page_access_token']) ? $page_table_info[0]['page_access_token'] : "";
        $page_name = isset($page_table_info[0]['page_name']) ? $page_table_info[0]['page_name'] : "";
        $page_profile = isset($page_table_info[0]['page_profile']) ? $page_table_info[0]['page_profile'] : "";

        if($fb_page_id=='' || $page_access_token=='')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('page not found.')));
            exit();
        }
        
        $post_id_use=$post_id;      

        try
        {
            $this->load->library('fb_rx_login');
            $post_info = $this->fb_rx_login->get_post_info_by_id($post_id_use,$page_access_token);

            if(isset($post_info['error']))
            {
                $response['error_msg'] = $post_info['error']['message'];
                echo json_encode(array('status'=>'0','message'=>$post_info['error']['message']));
                exit();
            }

            if(empty($post_info))
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong.")));
                exit();
            }

            foreach ($post_info as $key => $value) 
            {
                $post_description=isset($value["message"])?$value["message"]:"";
                $post_created_at=isset($value["created_time"])?$value["created_time"]:"";
                $post_created_at=str_replace('T',' ',$post_created_at);
                $post_created_at=substr($post_created_at, 0, 19);
            }

            $this->load->library("fb_rx_login");
            $comment_list = $this->fb_rx_login->get_all_comment_of_post_pagination($post_id_use,$page_access_token);
           
            $commenter_count=isset($comment_list["commenter_info"]) ? count($comment_list["commenter_info"]) : 0 ;
            if(isset($comment_list["commenter_info"]))
            {
              if(isset($comment_list["commenter_info"][$fb_page_id])) 
              {
                $commenter_count--; // if page is also commenter
                unset($comment_list["commenter_info"][$fb_page_id]);
              }
            }

           $comment_count=isset($comment_list["comment_info"]) ? count($comment_list["comment_info"]) : 0 ;
      
           $insert_batch=array();
           if(isset($comment_list["commenter_info"]))
           {
              foreach ($comment_list["commenter_info"] as $key => $value) 
              {                
                $last_comment_time=$value["last_comment_time"];
                $last_comment_time=str_replace('T',' ',$last_comment_time);
                $last_comment_time=substr($last_comment_time, 0, 19);

                $insert_batch[]=array
                (
                    "tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,
                    "facebook_rx_fb_user_info_id" => $this->session->userdata("facebook_rx_fb_user_info"),
                    "user_id" => $this->user_id,
                    "page_info_table_id" => $page_id,
                    "page_id" => $fb_page_id,
                    "page_name" => $page_name,
                    "post_id"=>$post_id_use,
                    "last_comment_id"=>$value["last_comment_id"],
                    "last_comment_time"=>$last_comment_time,
                    "commenter_fb_id"=>$key,                    
                    "commenter_name"=>$value["name"]
                );
              }
           }

           $insert_batch2=array();
           if(isset($comment_list["comment_info"]))
           {
              foreach ($comment_list["comment_info"] as $key => $value) 
              {                
                if($value["commenter_id"]==$fb_page_id) // skipping self comments
                {
                    $comment_count--;
                    continue;
                }

                $comment_time=$value["created_time"];
                $comment_time=substr($comment_time, 0, 19);

                $insert_batch2[]=array
                (
                    "tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,
                    "facebook_rx_fb_user_info_id" => $this->session->userdata("facebook_rx_fb_user_info"),
                    "user_id" => $this->user_id,
                    "page_info_table_id" => $page_id,
                    "page_id" => $fb_page_id,
                    "page_name" => $page_name,
                    "post_id"=> $post_id,
                    "comment_id" => $value["comment_id"],
                    "comment_text" => $value["message"],
                    "commenter_fb_id" => $value["commenter_id"],
                    "commenter_name"=>$value["commenter_name"],
                    "comment_time" => $comment_time
                );
              }
           }

           $unsubscribe_commenter=array();
           $unsubscribe_data=$this->basic->get_data("tag_machine_commenter_info",array("where"=>array("tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,"subscribed"=>"0")));
           foreach ($unsubscribe_data as $key => $value) 
           {
               $unsubscribe_commenter[]=$value['commenter_fb_id'];
           }

           $this->db->trans_start();

           $this->basic->update_data("tag_machine_enabled_post_list",array("id"=>$tag_machine_enabled_post_list_id),array("comment_count"=>$comment_count,"commenter_count"=>$commenter_count,"last_updated_at"=>date("Y-m-d H:i:s")));
           $this->basic->delete_data("tag_machine_commenter_info",array("tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id));           
           $this->basic->delete_data("tag_machine_comment_info",array("tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id)); 
          
           if($commenter_count>0)
           $this->db->insert_batch("tag_machine_commenter_info",$insert_batch);

           if($comment_count>0)
           $this->db->insert_batch("tag_machine_comment_info",$insert_batch2);

           if(count($unsubscribe_commenter)>0)
           {
             $this->db->where("tag_machine_enabled_post_list_id",$tag_machine_enabled_post_list_id);
             $this->db->where_in("commenter_fb_id",$unsubscribe_commenter);
             $this->db->update("tag_machine_commenter_info",array("subscribed"=>"0"));

             $this->db->where("tag_machine_enabled_post_list_id",$tag_machine_enabled_post_list_id);
             $this->db->where_in("commenter_fb_id",$unsubscribe_commenter);
             $this->db->update("tag_machine_comment_info",array("subscribed"=>"0"));
           }

           $this->db->trans_complete();
           if($this->db->trans_status() === false) 
           {
             echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
             exit();
           }

           echo json_encode(array('status'=>'1','message'=>$this->lang->line("post comments has been updated successfully.")));

        }
        catch(Exception $e)
        {
            echo json_encode(array('status'=>'0','message'=>$e->getMessage()));
        }

    }

    public function post_list($page_info_table_id=0,$post_id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access) && !in_array(201,$this->module_access))
        redirect('home/login_page', 'location'); 

        $data['body'] = "post_list";
        $data['page_title'] = $this->lang->line("Campaign List");
        $page_info=$this->basic->get_data("tag_machine_enabled_post_list",array("where"=>array("tag_machine_enabled_post_list.facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),'facebook_rx_fb_page_info.bot_enabled'=>'1')),array("facebook_rx_fb_page_info.*"),array('facebook_rx_fb_page_info'=>"tag_machine_enabled_post_list.page_info_table_id=facebook_rx_fb_page_info.id,left"),'','','facebook_rx_fb_page_info.page_name ASC','tag_machine_enabled_post_list.page_info_table_id');
        $data['page_info'] = $page_info;
        $data['auto_search_page_info_table_id'] = $page_info_table_id;
        $data['post_id'] = $post_id;
        $this->session->set_userdata("comment_tag_machine_post_list_auto_search_page_info_table_id",$page_info_table_id);
        $data["time_zone"]= $this->_time_zone_list();
        $this->_viewcontroller($data);
    }


    public function post_list_data()
    {
        $this->ajax_check();

        $page_id = trim($this->input->post("page_id",true));
        $post_id = trim($this->input->post("post_id",true));
        $post_date_range = $this->input->post("post_date_range",true);

        $display_columns = array("#",'id','page_profile','page_name','post_id','comment_bulk_tag','bulk_comment_reply','rescan','comment_count','commenter_count','last_updated_at','post_created_at','last_updated_at');
        $search_columns = array('post_id');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple=array();
        $where_simple['facebook_rx_fb_user_info_id'] = $this->session->userdata("facebook_rx_fb_user_info");
        if ($post_id != '') $where_simple['post_id like ']    = "%".$post_id."%";
        if ($page_id != '') $where_simple['page_info_table_id']    = $page_id;

        if($this->session->userdata("comment_tag_machine_post_list_auto_search_page_info_table_id")!=0)
          $search_page_id  = $this->session->userdata('comment_tag_machine_post_list_auto_search_page_info_table_id');

        if (isset($search_page_id)) $where_simple['page_info_table_id']    = $search_page_id;

        if($post_date_range!="")
        {
            $exp = explode('|', $post_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date   = isset($exp[1])?$exp[1]:"";

            if($from_date!="Invalid date" && $to_date!="Invalid date")
            {
              $from_date = date('Y-m-d', strtotime($from_date));
              $to_date   = date('Y-m-d', strtotime($to_date));
              $where_simple["Date_Format(post_created_at,'%Y-%m-%d') >="] = $from_date;
              $where_simple["Date_Format(post_created_at,'%Y-%m-%d') <="] = $to_date;
            }
        }

        $where = array('where' => $where_simple);

        $table = "tag_machine_enabled_post_list";
        $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

        $total_rows_array=$this->basic->count_row($table,$where,$count="id",$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        for($i=0;$i<count($info);$i++)
        {
            $info[$i]['last_updated_at'] = date("jS M y H:i",strtotime($info[$i]['last_updated_at']));
            $info[$i]['post_created_at'] = date("jS M y H:i",strtotime($info[$i]['post_created_at']));

            $info[$i]['rescan'] = "<a href='#' enable-id='".$info[$i]["id"]."' page-id='".$info[$i]["page_info_table_id"]."' post-id='".$info[$i]["post_id"]."' id='rescan_".$info[$i]["page_info_table_id"]."_".$info[$i]["post_id"]."' data-toggle='tooltip' title='".$this->lang->line("Re-scan Comments")."' class='rescan_comments btn btn-outline-dark btn-sm'><i class='fa fa-refresh'></i> ".$this->lang->line("Re-scan")."</a>";

            $onlypostid = explode('_', $info[$i]['post_id']);
              
            $onlypostid2 = isset($onlypostid[1])?$onlypostid[1]:$info[$i]['post_id'];

            // $info[$i]['page_name'] = "<div style='min-width:100px;'><a title='".$this->lang->line("Visit Page")."' class='ash' target='_BLANK' href='https://facebook.com/".$info[$i]['page_id']."'>".$info[$i]['page_name']."</a></div>";
            // $info[$i]['post_id'] = "<a target='_BLANK' href='https://facebook.com/".$info[$i]['post_id']."' data-toggle='tooltip' title='".$this->lang->line("Visit Post")."'>".$onlypostid2."</a>";

            $page_profile = ($info[$i]['page_profile']!="") ? $info[$i]['page_profile'] : base_url('assets/images/50x50.png');
            $info[$i]['page_profile'] = "<img class='rounded-circle' src='".$page_profile."' style='height:40px;width:40px;'>";          

            $disable_create1=$disable_create2="";
            if($info[$i]['commenter_count']<=1) $disable_create1="disabled";
            if($info[$i]['comment_count']<=1) $disable_create2="disabled";


            $info[$i]['comment_bulk_tag'] = "<div style='width:100px !important;'><a href='#' data-toggle='tooltip' data-placement='top' id='bulktag-".$info[$i]["id"]."-".$info[$i]["commenter_count"]."' title='".$this->lang->line("Create Campaign")."' class='".$disable_create1." create_bulk_tag_campaign btn-circle btn btn-outline-primary'><i class='fa fa-plus-circle'></i></a>&nbsp;";
            $info[$i]['comment_bulk_tag'] .=  "<a data-toggle='tooltip' data-placement='top' href='".base_url("comment_reply_enhancers/bulk_tag_campaign_list/".$info[$i]["id"])."' title='".$this->lang->line("Report")."' class='comment_bulk_tag_report btn-circle btn btn-outline-info'><i class='fas fa-eye'></i></a></div>";

            $info[$i]['bulk_comment_reply'] = "<div style='width:100px !important;'><a href='#' data-toggle='tooltip' data-placement='top' id='bulkreply-".$info[$i]["id"]."-".$info[$i]["comment_count"]."' title='".$this->lang->line("Create Campaign")."' class='".$disable_create2." bulk_comment_reply_campaign btn-circle btn btn-outline-primary'><i class='fa fa-plus-circle'></i></a>&nbsp;";
            $info[$i]['bulk_comment_reply'] .=  "<a data-toggle='tooltip' data-placement='top' href='".base_url("comment_reply_enhancers/bulk_comment_reply_campaign_list/".$info[$i]["id"])."' title='".$this->lang->line("Report")."' class='bulk_comment_reply_report btn-circle btn btn-outline-info'><i class='fas fa-eye'></i></a></div>
              <script>
                $('[data-toggle=\"tooltip\"]').tooltip();
              </script>
            ";

            $info[$i]['commenter_count'] = "<span class='badge badge-status pointer show_commenter_list' table_id='".$info[$i]['id']."' page_name='".$info[$i]['page_name']."' page_id='".$info[$i]['page_id']."' post_id='".$info[$i]['post_id']."'><i class='fa fa-user text-info'></i> ".$info[$i]['commenter_count']."</span>";
            $info[$i]['comment_count'] = "<span class='badge badge-status pointer show_comment_list' table_id='".$info[$i]['id']."' page_name='".$info[$i]['page_name']."' page_id='".$info[$i]['page_id']."' post_id='".$info[$i]['post_id']."'><i class='fa fa-comment text-info'></i> ".$info[$i]['comment_count']."</span>";
        }
        
        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function post_comment_list()
    {
    	$this->ajax_check();
    	if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access) && !in_array(201,$this->module_access)) exit();

    	$table_id = $this->input->post("table_id");
    	$searching = $_POST['search']['value'];
    	$display_columns = array("#", 'commenter_name', 'comment_id', 'comment_time', 'comment');

    	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    	$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    	$limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
    	$sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 3;
    	$sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'comment_time';
    	$order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
    	$order_by=$sort." ".$order;


    	$table_name = "tag_machine_comment_info";
    	$where_simple = array();
    	$where_simple['user_id'] = $this->user_id;
    	$where_simple['tag_machine_enabled_post_list_id'] = $table_id;
    	$where_simple['facebook_rx_fb_user_info_id'] = $this->session->userdata("facebook_rx_fb_user_info");
    	if($searching != '')
    	$where_simple['commenter_name like'] = '%'.$searching.'%';

    	$where['where'] = $where_simple;
    	$info = $this->basic->get_data($table_name,$where,'','',$limit,$start,$order_by);

    	$total_rows_array=$this->basic->count_row('tag_machine_comment_info',$where,"tag_machine_comment_info.id");
    	$total_result=$total_rows_array[0]['total_rows'];

    	foreach ($info as $key => $value) 
    	{
    	    $info[$key]['commenter_name'] = "<a target='_BLANK' href='https://facebook.com/".$value["commenter_fb_id"]."'>".$value["commenter_name"]."</a>";
    	    $info[$key]['comment_id'] = "<a target='_BLANK' href='https://facebook.com/".$value["comment_id"]."'>".$value["comment_id"]."</a>";
    	    $info[$key]['comment_time'] = date("M j, y H:i",strtotime($value["comment_time"]));
    	    $info[$key]['comment'] = $value["comment_text"];
    	}

    	$data['draw'] = (int)$_POST['draw'] + 1;
    	$data['recordsTotal'] = $total_result;
    	$data['recordsFiltered'] = $total_result;
    	$data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

    	echo json_encode($data);

    }


    /**
     * downloads comment id, text, commenters id, name
     * uses php memory to download on the fly
     *             
     * @return null
     */
    public function download_comment_list_info($id = 1)
    {
      
        
        if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access) && !in_array(201,$this->module_access)) exit();

        $table="tag_machine_comment_info";        

        $comment_data = $this->basic->get_data($table,array("where"=>array("tag_machine_enabled_post_list_id"=>$id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))),'','','',NULL,$order_by='comment_time DESC');


        $postid=isset($comment_data[0]["post_id"])?$comment_data[0]["post_id"]:"";
        $pagename=isset($comment_data[0]["page_name"])?$comment_data[0]["page_name"]:"";
        $pageid=isset($comment_data[0]["page_id"])?$comment_data[0]["page_id"]:"";

        $filename="{$this->user_id}_comment_list_info.csv";
        // make output csv file unicode compatible
        $f = fopen('php://memory', 'w'); 
        fputs( $f, "\xEF\xBB\xBF" );

        /**Write header in csv file***/
        $write_data[]="Commenter Name";
        $write_data[]="Commenter Id";
        $write_data[]="Comment Id";
        $write_data[]="Comment Time";
        $write_data[]="Comment Text";

        fputcsv($f,$write_data, ",");

        foreach ($comment_data as $key2 => $value)
        {
            
            $write_data=array();
            $write_data[]=$value["commenter_name"];
            $write_data[]=$value["commenter_fb_id"];
            $write_data[]=$value["comment_id"];
            $write_data[]=$value["comment_time"];
            $write_data[]=$value["comment_text"];

            fputcsv($f,$write_data, ",");   
        }
        

        // reset the file pointer to the start of the file
        fseek($f, 0);
        // tell the browser it's going to be a csv file
        header('Content-Type: application/csv');
        // tell the browser we want to save it instead of displaying it
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        // make php send the generated csv lines to the browser
        fpassthru($f); 

        
    }

    public function post_commenter_list()
    {
    	$this->ajax_check();
    	if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access) && !in_array(201,$this->module_access)) exit();

    	$table_id = $this->input->post("table_id");
    	$searching = $_POST['search']['value'];
    	$display_columns = array("#", 'commenter_name', 'last_comment_id', 'last_comment_time', 'actions');

    	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    	$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    	$limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
    	$sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 3;
    	$sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'last_comment_time';
    	$order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
    	$order_by=$sort." ".$order;


    	$table_name = "tag_machine_commenter_info";
    	$where_simple = array();
    	$where_simple['user_id'] = $this->user_id;
    	$where_simple['tag_machine_enabled_post_list_id'] = $table_id;
    	$where_simple['facebook_rx_fb_user_info_id'] = $this->session->userdata("facebook_rx_fb_user_info");
    	if($searching != '')
    	$where_simple['commenter_name like'] = '%'.$searching.'%';

    	$where['where'] = $where_simple;
    	$info = $this->basic->get_data($table_name,$where,'','',$limit,$start,$order_by);

    	$total_rows_array=$this->basic->count_row('tag_machine_commenter_info',$where,"tag_machine_commenter_info.id");
    	$total_result=$total_rows_array[0]['total_rows'];

    	foreach ($info as $key => $value) 
    	{
    	    $info[$key]['commenter_name'] = "<a target='_BLANK' href='https://facebook.com/".$value["commenter_fb_id"]."'>".$value["commenter_name"]."</a>";
    	    $info[$key]['last_comment_id'] = "<a target='_BLANK' href='https://facebook.com/".$value["last_comment_id"]."'>".$value["last_comment_id"]."</a>";
    	    $info[$key]['last_comment_time'] = date("M j, y H:i",strtotime($value["last_comment_time"]));

    	    if($value['subscribed'] == '1')
    	    {
    	        $subscribe_unsubscribe_button = "<button id ='".$value['tag_machine_enabled_post_list_id']."-".$value['commenter_fb_id']."-".$value['subscribed']."' type='button' class='commenter_subscribe_unsubscribe btn-sm btn btn-danger'>".$this->lang->line("unsubscribe")."</button>";
    	        $status = "<span class='badge badge-status'><i class='fas fa-check-square green'></i> ".$this->lang->line('Subscribed')."</span>";
    	    }
    	    else
    	    {
    	        $subscribe_unsubscribe_button = "<button id ='".$value['tag_machine_enabled_post_list_id']."-".$value['commenter_fb_id']."-".$value['subscribed']."' type='button' class='commenter_subscribe_unsubscribe btn-sm btn btn-success'>".$this->lang->line("subscribe")."</button>";
    	        $status = "<span class='badge badge-status'><i class='fas fa-window-close red'></i> ".$this->lang->line('Un-Subscribed')."</span>";
    	    }
    	    // $info[$key]['status'] = $status;
    	    $info[$key]['actions'] = $subscribe_unsubscribe_button;
    	}

    	$data['draw'] = (int)$_POST['draw'] + 1;
    	$data['recordsTotal'] = $total_result;
    	$data['recordsFiltered'] = $total_result;
    	$data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

    	echo json_encode($data);

    }


    /**
     * downloads commenters id, name, Last Comment ID, Status, Last Comment Time
     * uses php memory to download on the fly
     * @return null
     */
    public function download_commenter_list_info($id = 1)
    {
      
        if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access) && !in_array(201,$this->module_access)) exit();

        $table="tag_machine_commenter_info";        

        $commenter_data = $this->basic->get_data($table,array("where"=>array("tag_machine_enabled_post_list_id"=>$id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))),'','','',NULL,$order_by='last_comment_time DESC');

        $postid=isset($commenter_data[0]["post_id"])?$commenter_data[0]["post_id"]:"";
        $pagename=isset($commenter_data[0]["page_name"])?$commenter_data[0]["page_name"]:"";
        $pageid=isset($commenter_data[0]["page_id"])?$commenter_data[0]["page_id"]:"";
        
        $filename="{$this->user_id}_commenter_list_info.csv";
        // make output csv file unicode compatible
        $f = fopen('php://memory', 'w'); 
        fputs( $f, "\xEF\xBB\xBF" );

        /**Write header in csv file***/
        $write_data[]="Commenter Name";
        $write_data[]="Commenter Id";
        $write_data[]="Last Comment ID";
        $write_data[]="Status";
        $write_data[]="Last Comment Time";

        fputcsv($f,$write_data, ",");
        
        foreach ($commenter_data as $key2 => $value)
        {

            $write_data=array();
            $write_data[] = $value["commenter_name"];
            $write_data[] = $value["commenter_fb_id"];
            $write_data[] = $value["last_comment_id"];
            $write_data[] = $value['subscribed'];
            $write_data[] = $value["last_comment_time"];

            fputcsv($f,$write_data, ",");  
        }

        // reset the file pointer to the start of the file
        fseek($f, 0);
        // tell the browser it's going to be a csv file
        header('Content-Type: application/csv');
        // tell the browser we want to save it instead of displaying it
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        // make php send the generated csv lines to the browser
        fpassthru($f); 
            
    }

    public function subscribe_unsubscribe_status_change()
    {     
        if (empty($_POST['subscribe_unsubscribe_status'])) exit();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access) && !in_array(201,$this->module_access)) exit();

        $client_subscribe_unsubscribe = array();
        $post_val=$this->input->post('subscribe_unsubscribe_status');
        $client_subscribe_unsubscribe = explode("-",$post_val);
        
        $tag_machine_enabled_post_list_id = isset($client_subscribe_unsubscribe[0]) ? $client_subscribe_unsubscribe[0]: 0;
        $commenter_fb_id = isset($client_subscribe_unsubscribe[1]) ? $client_subscribe_unsubscribe[1]: "";
        $current_status =  isset($client_subscribe_unsubscribe[2]) ? $client_subscribe_unsubscribe[2]: "0";
        
        if($current_status=="1") $permission="0";
        else $permission="1";

        $where = array
        (
            'tag_machine_enabled_post_list_id' => $tag_machine_enabled_post_list_id,
            'commenter_fb_id'=>$commenter_fb_id,
            'user_id' => $this->user_id
        );
        $data = array('subscribed' => $permission);

        if($this->basic->update_data('tag_machine_commenter_info', $where, $data))
        {     
            
            $this->basic->update_data('tag_machine_comment_info', $where, $data);

            if($permission=="0")  $response = "<button id ='".$tag_machine_enabled_post_list_id."-".$commenter_fb_id."-".$permission."' type='button' class='commenter_subscribe_unsubscribe btn-sm btn btn-success'>".$this->lang->line("subscribe")."</button>";
            else  $response = "<button id ='".$tag_machine_enabled_post_list_id."-".$commenter_fb_id."-".$permission."' type='button' class='commenter_subscribe_unsubscribe btn-sm btn btn-danger'>".$this->lang->line("unsubscribe")."</button>";
          
            echo $response;
        }
    }

    public function commenter_autocomplete($tag_campaign_tag_machine_enabled_post_list_id="")
    {
       
       $search_query= $this->input->get('search');

       $this->db->select();
       $this->db->from('tag_machine_commenter_info');
       $this->db->like('commenter_name', $search_query);
       $this->db->order_by('commenter_name', 'ASC');
       $this->db->where("subscribed","1");
       $this->db->where("tag_machine_enabled_post_list_id",$tag_campaign_tag_machine_enabled_post_list_id);
       $this->db->limit(20);
       $data=$this->db->get()->result_array();
       $results=array();

       foreach ($data as $key => $value)
       {
          $results[]=array("value"=>$value["commenter_fb_id"],"text"=>$value["commenter_name"]);
       }
       echo json_encode($results);
    }

    public function commenter_range_option()
    {       
        if(!$_POST) exit();
        $resposne='';

        $tag_machine_enabled_post_list_id=$this->input->post("tag_machine_enabled_post_list_id",true);
        $commenter_list = $this->basic->count_row("tag_machine_commenter_info",array("where"=>array("tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,"subscribed"=>"1")));
        $commenter_count=$commenter_list[0]['total_rows'];
       
        $item_per_range=$this->config->item('item_per_range');
        if($item_per_range=='') $item_per_range=50;

        $cal=ceil($commenter_count/$item_per_range);

        for($i=1;$i<=$cal;$i++)
        {
            if($i==1)
            {
                $start=$i;
                $end=$item_per_range;
            }
            else
            {
                $start=$end+1;
                $end=$end+$item_per_range;
            }
            if($end>=$commenter_count) $end=$commenter_count;
            if($start==$end) continue;

            $resposne.= '<option style="padding:5px;" value="'.$start.'-'.$end.'">&nbsp;&nbsp;&nbsp; o &nbsp;'.$this->lang->line("Latest")." ".$start.'-'.$end.'</option>';
        }
        echo $resposne;
    }

    public function create_bulk_tag_campaign_action()
    {
        if(!$_POST) exit();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(201,$this->module_access))  exit();
        $status=$this->_check_usage($module_id=201,$request=1);
        if($status=="3")  
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('sorry, your monthly limit is exceeded for this module.')));
            exit();
        }

        $schedule_type = $this->input->post('schedule_type');
        $schedule_time = $this->input->post('schedule_time');
        $time_zone = $this->input->post('time_zone');
        if($schedule_type=='now') $schedule_time='';
        if($schedule_type == '') $schedule_type = 'later';

        $tag_machine_enabled_post_list_id = $this->input->post('tag_campaign_tag_machine_enabled_post_list_id');
        $facebook_rx_fb_user_info_id = $this->session->userdata("facebook_rx_fb_user_info");
        $user_id = $this->user_id;
        $campaign_name = $this->input->post('campaign_name');
        $tag_content = $this->input->post('message');
        $uploaded_image_video = $this->input->post('uploaded_image_video');
        $commenter_range = $this->input->post('commenter_range');

        $item_per_range=$this->config->item('item_per_range');
        if($item_per_range=='') $item_per_range=50;

        $explode_range=explode('-', $commenter_range);
        $start=isset($explode_range[0])?$explode_range[0]:1;
        $end=isset($explode_range[1])?$explode_range[1]:$item_per_range;
        $comenter_limit=($end-$start)+1;
        $commenter_start=$start-1;
        
        $tag_exclude = $this->input->post('exclude');
        if(!is_array($tag_exclude)) $tag_exclude = array();

        $page_info=$this->basic->get_data("tag_machine_enabled_post_list",array("where"=>array("id"=>$tag_machine_enabled_post_list_id,"facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id)));

        if(count($page_info)==0)
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('something went wrong, please try again.')));
            exit();
        }

        $page_info_table_id=isset($page_info[0]["page_info_table_id"])?$page_info[0]["page_info_table_id"]:"";
        $page_id=isset($page_info[0]["page_id"])?$page_info[0]["page_id"]:""; // fb page id
        $page_name=isset($page_info[0]["page_name"])?$page_info[0]["page_name"]:"";
        $page_profile=isset($page_info[0]["page_profile"])?$page_info[0]["page_profile"]:"";
        $post_id=isset($page_info[0]["post_id"])?$page_info[0]["post_id"]:"";
        $post_created_at=isset($page_info[0]["post_created_at"])?$page_info[0]["post_created_at"]:"";
        $post_description=isset($page_info[0]["post_description"])?$page_info[0]["post_description"]:"";

        $commenter_list = $this->basic->get_data("tag_machine_commenter_info",array("where"=>array("tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,"subscribed"=>"1")),"","",$comenter_limit,$commenter_start,"last_comment_time DESC");
        $tag_database_array=array();
        $commenter_count=0;
        foreach ($commenter_list as $key => $value) 
        {
            if(in_array($value['commenter_fb_id'], $tag_exclude)) continue;

            $tag_database_array[$value["commenter_fb_id"]]=
            array
            (
                "commenter_name"=>$value["commenter_name"],
                "commenter_fb_id"=>$value["commenter_fb_id"],
                "last_comment_id"=>$value["last_comment_id"],
                "last_comment_time"=>$value["last_comment_time"]
            );
            $commenter_count++;
        }
        $tag_database=json_encode($tag_database_array);
        $current_time=date("Y-m-d H:i:s");
        $data = array
        (
           "campaign_name"=>$campaign_name, 
           "tag_database"=>$tag_database, 
           "tag_exclude"=>json_encode($tag_exclude), 
           "tag_content"=>$tag_content, 
           "uploaded_image_video"=>$uploaded_image_video,
           "campaign_created"=>$current_time, 
           "posting_status"=>"0", 
           "last_updated_at"=>$current_time, 
           "tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id, 
           "facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id, 
           "user_id"=>$user_id, 
           "page_info_table_id"=>$page_info_table_id, 
           "page_id"=>$page_id, 
           "page_name"=>$page_name, 
           "page_profile"=>$page_profile, 
           "post_id"=>$post_id, 
           "post_created_at"=>$post_created_at, 
           "post_description"=>$post_description, 
           "commenter_count"=>$commenter_count,
           "schedule_type"=>$schedule_type,
           "schedule_time"=>$schedule_time,
           "time_zone"=>$time_zone
        );
        $this->basic->insert_data("tag_machine_bulk_tag",$data);
        $campaign_id=$this->db->insert_id();
        $this->_insert_usage_log($module_id=201,$request=1);
        $campaign_link = "<a target='_BLANK' href='".base_url('comment_reply_enhancers/bulk_tag_campaign_list/0/'.$campaign_id)."'>".$this->lang->line('click here to see report')."</a>";
        $success_message = $campaign_link;
        echo json_encode(array('status'=>'1','message'=>$success_message));

    }

    public function create_comment_reply_campaign_action()
    {
        if(!$_POST) exit();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access))  exit();

        $status=$this->_check_usage($module_id=202,$request=1);
        if($status=="3")  
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('sorry, your monthly limit is exceeded for this module.')));
            exit();
        }

        $schedule_type = $this->input->post('schedule_type2');
        $schedule_time = $this->input->post('schedule_time2');
        $time_zone = $this->input->post('time_zone2');
        if($schedule_type=='now') $schedule_time='';
        if($schedule_type == '') $schedule_type = 'later';

        $tag_machine_enabled_post_list_id = $this->input->post('bulk_comment_reply_campaign_enabled_post_list_id');
        $facebook_rx_fb_user_info_id = $this->session->userdata("facebook_rx_fb_user_info");
        $user_id = $this->user_id;
        $campaign_name = $this->input->post('campaign_name2');
        $reply_content = $this->input->post('message2');
        $uploaded_image_video = $this->input->post('uploaded_image_video2');
        $reply_multiple = $this->input->post('reply_multiple');
        if($reply_multiple == "") $reply_multiple = "0";
        $delay_time = $this->input->post('delay_time');
        if($delay_time=="") $delay_time=0;
        $delay_time=abs($delay_time);

        $page_info=$this->basic->get_data("tag_machine_enabled_post_list",array("where"=>array("id"=>$tag_machine_enabled_post_list_id,"facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id)));
        if(count($page_info)==0)
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('something went wrong, please try again.')));
            exit();
        }
        $page_info_table_id=isset($page_info[0]["page_info_table_id"])?$page_info[0]["page_info_table_id"]:"";
        $page_id=isset($page_info[0]["page_id"])?$page_info[0]["page_id"]:""; // fb page id
        $page_name=isset($page_info[0]["page_name"])?$page_info[0]["page_name"]:"";
        $page_profile=isset($page_info[0]["page_profile"])?$page_info[0]["page_profile"]:"";
        $post_id=isset($page_info[0]["post_id"])?$page_info[0]["post_id"]:"";
        $post_created_at=isset($page_info[0]["post_created_at"])?$page_info[0]["post_created_at"]:"";
        $post_description=isset($page_info[0]["post_description"])?$page_info[0]["post_description"]:"";

        $comment_list = $this->basic->get_data("tag_machine_comment_info",array("where"=>array("tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,"subscribed"=>"1")),"","","","","comment_time DESC");
        $report=array();
        $comment_count=0;
        $only_commenter_id_array=array();
        foreach ($comment_list as $key => $value) 
        {
            if($reply_multiple=='0')
            {
                if(in_array($value["commenter_fb_id"], $only_commenter_id_array))
                continue;
            } 

            $report[$value["comment_id"]]=
            array
            (
                "commenter_name"=>$value["commenter_name"],
                "commenter_fb_id"=>$value["commenter_fb_id"],
                "comment_id"=>$value["comment_id"],
                "comment_time"=>$value["comment_time"],
                "status"=>'Pending',
                "replied_at"=>"x"
            );
            $only_commenter_id_array[]=$value["commenter_fb_id"];
            $comment_count++;
        }
        $report_json=json_encode($report);
        $current_time=date("Y-m-d H:i:s");
        $data = array
        (
           "campaign_name"=>$campaign_name, 
           "reply_content"=>$reply_content, 
           "uploaded_image_video"=>$uploaded_image_video,
           "reply_multiple"=>$reply_multiple,
           "report"=>$report_json,
           "campaign_created"=>$current_time, 
           "posting_status"=>"0", 
           "delay_time"=>$delay_time, 
           "is_try_again"=>'1', 
           "total_reply"=>$comment_count, 
           "last_updated_at"=>$current_time, 
           "tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id, 
           "facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id, 
           "user_id"=>$user_id, 
           "page_info_table_id"=>$page_info_table_id, 
           "page_id"=>$page_id, 
           "page_name"=>$page_name, 
           "page_profile"=>$page_profile, 
           "post_id"=>$post_id, 
           "post_created_at"=>$post_created_at, 
           "post_description"=>$post_description,
           "schedule_type"=>$schedule_type,
           "schedule_time"=>$schedule_time,
           "time_zone"=>$time_zone
        );

        $this->db->trans_start();

        $this->basic->insert_data("tag_machine_bulk_reply",$data);
        $campaign_id=$this->db->insert_id();
        $this->_insert_usage_log($module_id=202,$request=1);

        foreach ($report as $key => $value) 
        {
            $insert_now=$value;
            $insert_now['campaign_id']=$campaign_id;
            unset($insert_now['status']);
            unset($insert_now['replied_at']);
            $this->basic->insert_data("tag_machine_bulk_reply_send",$insert_now);
        }
        
        $this->db->trans_complete();
        if($this->db->trans_status() === false) 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
            exit();
        }

        $campaign_link=" <a href='".base_url('comment_reply_enhancers/bulk_comment_reply_campaign_list/0/'.$campaign_id)."'>".$this->lang->line('click here to see report.')."</a>";
        $success_message = $campaign_link;
        echo json_encode(array('status'=>'1','message'=>$success_message));

    }

    public function bulk_tag_campaign_list($tag_machine_enabled_post_list=0,$campaign_id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(201,$this->module_access))
        redirect('home/login_page', 'location'); 

        $data['body'] = "bulk_tag_campaign_list";
        $data['page_title'] = $this->lang->line("Comment & Bulk Tag Campaign Report");
        $page_info = $this->basic->get_data("tag_machine_enabled_post_list",array("where"=>array("tag_machine_enabled_post_list.facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),'facebook_rx_fb_page_info.bot_enabled'=>'1')),array("facebook_rx_fb_page_info.*"),array('facebook_rx_fb_page_info'=>"tag_machine_enabled_post_list.page_info_table_id=facebook_rx_fb_page_info.id,left"),'','','facebook_rx_fb_page_info.page_name ASC','tag_machine_enabled_post_list.page_info_table_id');
        $data['page_info'] = $page_info;
        $data['auto_search_enabled_post_list'] = $tag_machine_enabled_post_list;
        $data['auto_search_campaign_id'] = $campaign_id;
        $this->session->set_userdata("bulk_tag_campaign_list_auto_search_enabled_post_list_id",$tag_machine_enabled_post_list);
        $this->session->set_userdata("bulk_tag_campaign_list_auto_search_campaign_id",$campaign_id);
        $data["time_zone"]= $this->_time_zone_list();
        $this->_viewcontroller($data);
    }

    public function bulk_tag_campaign_list_data()
    {
        $this->ajax_check();

        $pagename = trim($this->input->post("page_id",true));
        $searching = trim($this->input->post("searching",true));
        $post_date_range = $this->input->post("post_date_range",true);

        $display_columns = array("#",'id','page_profile','campaign_name','page_name','post_id','commenter_count','actions','status','schedule_time','last_updated_at');
        $search_columns = array('campaign_name','post_id');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple=array();

        if($this->session->userdata("bulk_tag_campaign_list_auto_search_enabled_post_list_id") != 0)
          $search_enabled_post_id = $this->session->userdata('bulk_tag_campaign_list_auto_search_enabled_post_list_id');

        if($this->session->userdata('bulk_tag_campaign_list_auto_search_campaign_id') != 0)
          $search_campaign_id = $this->session->userdata('bulk_tag_campaign_list_auto_search_campaign_id');


        //==================================auto search===============================
        if (isset($search_campaign_id)) $where_simple['id']    = $search_campaign_id;
        if (isset($search_enabled_post_id)) $where_simple['tag_machine_enabled_post_list_id'] = $search_enabled_post_id;
        //==================================auto search===============================

        if($pagename != '') $where_simple['page_info_table_id'] = $pagename;


        if($post_date_range!="")
        {
            $exp = explode('|', $post_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date   = isset($exp[1])?$exp[1]:"";
            if($from_date!="Invalid date" && $to_date!="Invalid date")
            {              
              $from_date = date('Y-m-d', strtotime($from_date));
              $to_date   = date('Y-m-d', strtotime($to_date));
              $where_simple["Date_Format(campaign_created,'%Y-%m-%d') >="] = $from_date;
              $where_simple["Date_Format(campaign_created,'%Y-%m-%d') <="] = $to_date;
            }
        }

        $where_simple['facebook_rx_fb_user_info_id'] = $this->session->userdata("facebook_rx_fb_user_info");

        $sql = '';
        if ($searching != '') $sql = "(campaign_name LIKE  '%".$searching."%' OR post_id LIKE '%".$searching."%')";
        if($sql != '') $this->db->where($sql);


        $where = array('where' => $where_simple);

        $table = "tag_machine_bulk_tag";
        $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        for($i=0;$i<count($info);$i++)
        {
          $tagContent_process= htmlentities($info[$i]['tag_content']);
          $errorMsg=htmlentities($info[$i]['error_message']);

          $action_count = 3;
          $view_report = "<a href='#' class='btn btn-circle btn-outline-primary show_report' 
          campaign_name='".$info[$i]['campaign_name']."' 
          page_name='".$info[$i]['page_name']."' 
          page_id='".$info[$i]['page_id']."' 
          table_id='".$info[$i]['id']."' 
          errorMsg=\"{$errorMsg}\"
          post_id='".$info[$i]['post_id']."' 
          tagContent=\"{$tagContent_process}\"
          data-toggle='tooltip' title='".$this->lang->line("Campaign Report")."'>
          <i class='fas fa-eye'></i></a>";

          $info[$i]['campaign_created'] = date("jS M y H:i",strtotime($info[$i]['campaign_created']));
          $info[$i]['last_updated_at'] = "<div class='text-muted' style='min-width:100px;'>".date("jS M y H:i",strtotime($info[$i]['last_updated_at']))."</div>";

          $onlypostid = explode('_', $info[$i]['post_id']);
            
          $onlypostid2 = isset($onlypostid[1])?$onlypostid[1]:$info[$i]['post_id'];

          $info[$i]['page_name'] = "<div class='text-muted' style='min-width:100px;'><a data-toggle='tooltip' title='".$this->lang->line("Visit Page")."' class='ash' target='_BLANK' href='https://facebook.com/".$info[$i]['page_id']."'>".$info[$i]['page_name']."</a></div>";
          $info[$i]['post_id'] = "<a target='_BLANK' data-toggle='tooltip' title='".$this->lang->line("Visit Post")."' href='https://facebook.com/".$info[$i]['post_id']."'>".$onlypostid2."</a>";


          $page_profile = ($info[$i]['page_profile']!="") ? $info[$i]['page_profile'] : base_url('assets/images/50x50.png');
          $info[$i]['page_profile'] = "<img class='rounded-circle' src='".$page_profile."' style='height:40px;width:40px;'>";

          if($info[$i]['schedule_type']=='later') 
            $info[$i]['schedule_time'] = "<div class='text-muted' style='min-width:100px;'>".date("M j, y H:i",strtotime($info[$i]['schedule_time'])).'</div>';
          else 
            $info[$i]['schedule_time'] = "<div class='text-muted' style='min-width:100px;'><i class='fa fa-exclamation-circle'></i> ".$this->lang->line("Not Scheduled")."</div>";

          if(isset($info[$i]['uploaded_image_video']) && $info[$i]['uploaded_image_video'] !== '')
          {
            $action_count++;
            $attachment = "<a class='btn btn-circle btn-outline-info' data-toggle='tooltip' title='".$this->lang->line("See Attachment")."' target='_BLANK' href='".base_url('upload/comment_reply_enhancers/'.$info[$i]['uploaded_image_video'])."'><i class='fas fa-paperclip'></i></a>";
          } else
          {
            $attachment = "";
          }

          if($info[$i]['posting_status']=='2') 
            $info[$i]['status'] =  "<div class='text-success' class='text-success' style='min-width:120px;'><i class='fa fa-check-circle'></i> ".$this->lang->line('Completed')."</div>";
          else if($info[$i]['posting_status']=='1') 
            $info[$i]['status'] =  "<div class='text-warning' class='text-warning' style='min-width:120px;'><i class='fa fa-spinner'></i> ".$this->lang->line('Processing')."</div>";
          else 
            $info[$i]['status'] =  "<div class='text-danger' style='min-width:120px;'><i class='far fa-times-circle'></i> ".$this->lang->line('Pending')."</div>";

          // Action section started from here
          

          if($info[$i]['posting_status']=='0' && $info[$i]['schedule_type']=='later')
            $editUrl =  "<a href='".base_url("comment_reply_enhancers/edit_bulk_tag_campaign/".$info[$i]["id"])."' title='".$this->lang->line("Edit")."' class='btn btn-circle btn-outline-warning'><i class='fas fa-edit'></i></a>";
          else 
            $editUrl = "<a data-toggle='tooltip' title='".$this->lang->line("Only Pending Campaigns are editable.")."' class='btn btn-circle btn-light text-muted'><i class='fas fa-edit'></i></a>";

          if($info[$i]['posting_status'] !='1') 
            $deleteUrl = "<a href='#' data-id='".$info[$i]["id"]."' data-toggle='tooltip' title='".$this->lang->line("Delete Campaign")."' class='delete_campaign btn btn-circle btn-outline-danger'><i class='fas fa-trash-alt'></i></a>";
          else 
            $deleteUrl = "<a class='btn btn-circle btn-light pointer text-muted' data-toggle='tooltip' title='".$this->lang->line("Processing Campaigns are not deletable.")."'><i class='fas fa-trash-alt'></i></a>";

          $action_width = ($action_count*47)+20;
          $info[$i]['actions'] = '<div class="dropdown d-inline dropright">
          <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i></button>
          <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';
          $info[$i]['actions'] .= $view_report;
          $info[$i]['actions'] .= $editUrl;
          if(isset($attachment) && $attachment != "")
          {
            $info[$i]['actions'] .= $attachment;
          }

          $info[$i]['actions'] .= $deleteUrl;
          $info[$i]['actions'] .= "</div></div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
          
        }
        
        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function bulk_tag_campaign_report()
    {
      $this->ajax_check();
      if($this->session->userdata('user_type') != 'Admin' && !in_array(201,$this->module_access)) exit();

      $table_id = $this->input->post('table_id');
      $searching = trim($this->input->post("searching1",true));

      $display_columns = array("#","commenter_name","last_comment_id","last_comment_time");

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 3;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'last_comment_time';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
      $order_by=$sort." ".$order;

      $table="tag_machine_bulk_tag";
      $info = $this->basic->get_data($table,array("where"=>array("id"=>$table_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));

      if(isset($info[0]['tag_database']) && $info[0]['tag_database'] != '')
      {
      
        $campaign_details = $info[0];

        $report_info = json_decode($campaign_details['tag_database'],true);
        // echo "<pre>"; print_r($report_info); exit;
        $reply_info = $report_info;

        $reply_info = array_filter($reply_info, function($single_reply) use ($searching) 
        {
          if ($searching != '') {

            if (stripos($single_reply['commenter_name'], $searching) !== false || stripos($single_reply['last_comment_id'], $searching) !== false || stripos($single_reply['last_comment_time'], $searching) !== false) {
              return TRUE; 
            }
            else
              return FALSE;  
          }
          else
            return TRUE;

        });

        
        usort($reply_info, function($first, $second) use ($sort, $order)
        {
          if ($first[$sort] == $second[$sort]) {
            return 0;
          }
          else if ($first[$sort] > $second[$sort]) {
            if ($order == 'desc') return 1;
            else return -1;
          }
          else if ($first[$sort] < $second[$sort]) {
            if ($order == 'desc') return -1;
            else return 1;
          }
                          
        });


        $final_info = array();
        $i = 0;
        $upper_limit = $start + $limit;

        foreach ($reply_info as $key => $value) {
            
            if ($i >= $start && $i < ($upper_limit))
                array_push($final_info, $value);

            $i++;
        }

        $result = array();
        foreach ($final_info as $value) {
            
          $temp = array();
          array_push($temp, ++$start);

          $commenter_fb_id = $value['commenter_fb_id'];

          foreach ($value as $key => $column) 
          {
            if($key == 'commenter_name')
              $column = $column;

            if($key == 'last_comment_id')
            {
              $exploded = explode("_", $column);
              $column = '<a target="_BLANK" href="https://facebook.com/'.$column.'">'.$exploded[1].'</a>';
            }

            if ($key == 'last_comment_time')
                $column = date('jS F y, H:i', strtotime($column));
            
            if (in_array($key, $display_columns)) 
              array_push($temp, $column);
          }

          array_push($result, $temp);
            
        }

      }
      else {

          $total_result = 0;
          $reply_info = array();
          $result = array();
      }
      
      $total_result = count($reply_info);
      $data['draw'] = (int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = $result;


      echo json_encode($data);
        
    }    

    public function delete_bulk_tag_campaign($id=0)
    {
        if(!$_POST) exit();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(201,$this->module_access)) exit();
        $id=$this->input->post("id");

        $xdata = $this->basic->get_data("tag_machine_bulk_tag",array("where"=>array("id"=>$id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));

        $posting_status  = isset($xdata[0]["posting_status"]) ? $xdata[0]["posting_status"] : "";

        if($posting_status=="0") // removing usage data if deleted and campaign is pending
        $this->_delete_usage_log($module_id=201,$request=1);
        
        if($this->basic->delete_data("tag_machine_bulk_tag",array("id"=>$id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))))
        echo "1";        
        else echo "0";
    }

    public function edit_bulk_tag_campaign($id='')
    {
        if($id==0) exit();

        $data['body'] = "edit_bulk_tag_campaign";
        $data['page_title'] = $this->lang->line("Edit Comment & Bulk Tag Campaign");
        $data["time_zone"]= $this->_time_zone_list();
        $data["xdata"] = $this->basic->get_data("tag_machine_bulk_tag",array("where"=>array("id"=>$id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));

        // only pending campaigns are editable
        if(!isset($data["xdata"][0]["posting_status"]) || $data["xdata"][0]["posting_status"]!='0' ) exit();
        // only scheduled campaigns can be editted
        if($data["xdata"][0]["schedule_type"]!='later') exit();

        $tag_machine_enabled_post_list_id=$data["xdata"][0]["tag_machine_enabled_post_list_id"];
        $commenter_list = $this->basic->count_row("tag_machine_commenter_info",array("where"=>array("tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,"subscribed"=>"1")));
        $commenter_count=$commenter_list[0]['total_rows'];
       
        $item_per_range=$this->config->item('item_per_range');
        if($item_per_range=='') $item_per_range=50;

        $cal=ceil($commenter_count/$item_per_range);
        $resposne='';
        for($i=1;$i<=$cal;$i++)
        {
            if($i==1)
            {
                $start=$i;
                $end=$item_per_range;
            }
            else
            {
                $start=$end+1;
                $end=$end+$item_per_range;
            }
            if($end>=$commenter_count) $end=$commenter_count;
            if($start==$end) continue;

            $resposne.= '<option style="padding:5px;" value="'.$start.'-'.$end.'">&nbsp;&nbsp;&nbsp; o &nbsp;'.$this->lang->line("Latest")." ".$start.'-'.$end.'</option>';
        }
        $data["commenter_range"]=$resposne;
    
        $previous_exclude = isset($data["xdata"][0]["tag_exclude"]) ? json_decode($data["xdata"][0]["tag_exclude"],true) : array();

        $data["xtag_exclude"]=array();
        if(count($previous_exclude)>0)
        $data["xtag_exclude"] = $this->basic->get_data("tag_machine_commenter_info",array("where_in"=>array("commenter_fb_id"=>$previous_exclude)));

        $this->_viewcontroller($data);
    }

    public function edit_bulk_tag_campaign_action()
    {
        if(!$_POST) exit();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(201,$this->module_access))  exit();
  
        $campaign_id = $this->input->post('campaign_id');
        $schedule_type = "later";
        $schedule_time = $this->input->post('schedule_time');
        $time_zone = $this->input->post('time_zone');

        $tag_machine_enabled_post_list_id = $this->input->post('tag_campaign_tag_machine_enabled_post_list_id');
        $facebook_rx_fb_user_info_id = $this->session->userdata("facebook_rx_fb_user_info");
        $user_id = $this->user_id;
        $campaign_name = $this->input->post('campaign_name');
        $tag_content = $this->input->post('message');
        $uploaded_image_video = $this->input->post('uploaded_image_video');
        $commenter_range = $this->input->post('commenter_range');

        $item_per_range=$this->config->item('item_per_range');
        if($item_per_range=='') $item_per_range=50;

        $explode_range=explode('-', $commenter_range);
        $start=isset($explode_range[0])?$explode_range[0]:1;
        $end=isset($explode_range[1])?$explode_range[1]:$item_per_range;
        $comenter_limit=($end-$start)+1;
        $commenter_start=$start-1;
        
        $tag_exclude = $this->input->post('exclude');
        if(!is_array($tag_exclude)) $tag_exclude = array();

        $commenter_list = $this->basic->get_data("tag_machine_commenter_info",array("where"=>array("tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,"subscribed"=>"1")),"","",$comenter_limit,$commenter_start,"last_comment_time DESC");
        $tag_database_array=array();
        $commenter_count=0;
        foreach ($commenter_list as $key => $value) 
        {
            if(in_array($value['commenter_fb_id'], $tag_exclude)) continue;

            $tag_database_array[$value["commenter_fb_id"]]=
            array
            (
                "commenter_name"=>$value["commenter_name"],
                "commenter_fb_id"=>$value["commenter_fb_id"],
                "last_comment_id"=>$value["last_comment_id"],
                "last_comment_time"=>$value["last_comment_time"]
            );
            $commenter_count++;
        }
        $tag_database=json_encode($tag_database_array);
        $current_time=date("Y-m-d H:i:s");
        $data = array
        (
           "campaign_name"=>$campaign_name, 
           "tag_database"=>$tag_database, 
           "tag_exclude"=>json_encode($tag_exclude), 
           "tag_content"=>$tag_content, 
           "uploaded_image_video"=>$uploaded_image_video,
           "last_updated_at"=>$current_time,            
           "commenter_count"=>$commenter_count,
           "schedule_time"=>$schedule_time,
           "time_zone"=>$time_zone
        );
        $this->basic->update_data("tag_machine_bulk_tag",array("id"=>$campaign_id,"facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id),$data);
        $campaign_link ="<a href='".base_url('comment_reply_enhancers/bulk_tag_campaign_list/0/'.$campaign_id)."'>".$this->lang->line('click here to see report.')."</a>";
        $success_message = $campaign_link;
        echo json_encode(array('status'=>'1','message'=>$success_message));

    }

    public function bulk_comment_reply_campaign_list($tag_machine_enabled_post_list=0,$campaign_id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access))
        redirect('home/login_page', 'location'); 

        $data['body'] = "bulk_comment_reply_campaign_list";
        $data['page_title'] = $this->lang->line("Bulk Comment Reply Campaign Report");
        $page_info=$this->basic->get_data("tag_machine_enabled_post_list",array("where"=>array("tag_machine_enabled_post_list.facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),'facebook_rx_fb_page_info.bot_enabled'=>'1')),array("facebook_rx_fb_page_info.*"),array('facebook_rx_fb_page_info'=>"tag_machine_enabled_post_list.page_info_table_id=facebook_rx_fb_page_info.id,left"),'','','facebook_rx_fb_page_info.page_name ASC','tag_machine_enabled_post_list.page_info_table_id');
        $data['page_info'] = $page_info;
        $data['auto_search_enabled_post_list'] = $tag_machine_enabled_post_list;
        $data['auto_search_campaign_id'] = $campaign_id;
        $this->session->set_userdata("bulk_comment_reply_campaign_list_auto_search_enabled_post_list_id",$tag_machine_enabled_post_list);
        $this->session->set_userdata("bulk_comment_reply_campaign_list_auto_search_campaign_id",$campaign_id);
        $data["time_zone"]= $this->_time_zone_list();
        $this->_viewcontroller($data);
    }

    public function bulk_comment_reply_campaign_list_data()
    {
      $this->ajax_check();

      $pagename = trim($this->input->post("page_id",true));
      $searching = trim($this->input->post("searching",true));
      $post_date_range = $this->input->post("post_date_range",true);

      $display_columns = array("#",'id','page_profile','campaign_name','page_name','post_id','total_reply','successfully_sent','last_try_error_count','actions','status','schedule_time','last_updated_at');
      $search_columns = array('campaign_name','post_id');

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
      $order_by=$sort." ".$order;


      $where_simple=array();
      $where_simple['facebook_rx_fb_user_info_id'] = $this->session->userdata("facebook_rx_fb_user_info");

      //==================================auto search===============================
      if($this->session->userdata("bulk_comment_reply_campaign_list_auto_search_enabled_post_list_id")!=0)
        $search_enabled_post_id = $this->session->userdata('bulk_comment_reply_campaign_list_auto_search_enabled_post_list_id');

      if($this->session->userdata('bulk_comment_reply_campaign_list_auto_search_campaign_id')!=0)
        $search_campaign_id = $this->session->userdata('bulk_comment_reply_campaign_list_auto_search_campaign_id');       

      if (isset($search_campaign_id)) $where_simple['id']    = $search_campaign_id;
      if (isset($search_enabled_post_id)) $where_simple['tag_machine_enabled_post_list_id'] = $search_enabled_post_id;
      //==================================auto search===============================

      $sql = '';
      if ($searching != '') $sql = "(campaign_name LIKE  '%".$searching."%' OR post_id LIKE '%".$searching."%')";
      if($sql != '') $this->db->where($sql);

      if($post_date_range!="")
      {
        $exp = explode('|', $post_date_range);
        $from_date = isset($exp[0])?$exp[0]:"";
        $to_date   = isset($exp[1])?$exp[1]:"";
        if($from_date!="Invalid date" && $to_date!="Invalid date")
        {
          $from_date = date('Y-m-d', strtotime($from_date));
          $to_date   = date('Y-m-d', strtotime($to_date));
          $where_simple["Date_Format(campaign_created,'%Y-%m-%d') >="] = $from_date;
          $where_simple["Date_Format(campaign_created,'%Y-%m-%d') <="] = $to_date;          
        }
      }

      if($pagename != '') $where_simple['page_info_table_id'] = $pagename;


      $where = array('where' => $where_simple);

      $table = "tag_machine_bulk_reply";
      $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

      $total_rows_array=$this->basic->count_row($table,$where,$count="id",$group_by='');
      $total_result=$total_rows_array[0]['total_rows'];

      for($i=0;$i<count($info);$i++)
      {
        $action_count = 3;
        $view_report = "<a href='#' title='".$this->lang->line('Campaign Report')."' data-toggle='tooltip' class='btn btn-circle btn-outline-primary show_report' 
        table_id='".$info[$i]['id']."'
        campaign_name='".$info[$i]['campaign_name']."' 
        page_name='".$info[$i]['page_name']."' 
        page_id='".$info[$i]['page_id']."'
        errorMsg='".$info[$i]['error_message']."'
        post_id='".$info[$i]['post_id']."'
        replyContent ='".$info[$i]['reply_content']."'>
        <i class='fas fa-eye'></i></a>";

        $info[$i]['campaign_created'] = date("jS M y H:i",strtotime($info[$i]['campaign_created']));
        $info[$i]['last_updated_at'] = "<div style='min-width:100px;'>".date("M j, y H:i",strtotime($info[$i]['last_updated_at']))."</div>";

        $info[$i]['page_name'] = "<div style='min-width:120px;'><a class='ash' data-toggle='tooltip' title='".$this->lang->line('Visit Page')."' target='_BLANK' href='https://facebook.com/".$info[$i]['page_id']."'>".$info[$i]['page_name']."</a></div>";

        // post id section
        $onlypostid = explode('_', $info[$i]['post_id']);  
        $onlypostid2 = isset($onlypostid[1])?$onlypostid[1]:$info[$i]['post_id'];
        $info[$i]['post_id'] = "<a target='_BLANK' data-toggle='tooltip' title='".$this->lang->line('Visit Post')."' href='https://facebook.com/".$info[$i]['post_id']."'>".$onlypostid2."</a>";

        // page image section
        $page_profile = ($info[$i]['page_profile']!="") ? $info[$i]['page_profile'] : base_url('assets/images/50x50.png');
        $info[$i]['page_profile'] = "<img class='rounded-circle' src='".$page_profile."' style='height:40px;width:40px;'>";

        // schedule time section
        if($info[$i]['schedule_type']=='later') 
          $info[$i]['schedule_time'] = "<div style='min-width:100px;'>".date("M j, y H:i",strtotime($info[$i]['schedule_time']))."</div>";
        else 
          $info[$i]['schedule_time'] = "<div style='min-width:100px;' class='text-muted'><i class='fas fa-exclamation-circle'></i> ".$this->lang->line('Not scheduled')."</div>";

        // attachment section
        if($info[$i]['uploaded_image_video'] != '')
        {
          $action_count++;
          $attachment = "<a target='_BLANK' data-toggle='tooltip' title='".$this->lang->line("See Attachment")."' href='".base_url('upload/comment_reply_enhancers/'.$info[$i]['uploaded_image_video'])."' class='btn btn-circle btn-outline-info'><i class='fa fa-paperclip'></i></a>";
        } else 
        {
          $attachment = "";
        }

        //status section
        if($info[$i]['posting_status']=='2') 
          $info[$i]['status'] ="<div class='text-success' style='min-width:100px;'><i class='fas fa-check-circle'></i> ".$this->lang->line("Completed")."</div>";
        else if($info[$i]['posting_status']=='1') 
          $info[$i]['status'] ="<div class='text-warning' style='min-width:100px;'><i class='fas fa-spinner'></i> ".$this->lang->line("Processing")."</div>";
        else 
          $info[$i]['status'] ="<div class='text-danger' style='min-width:100px;'><i class='far fa-times-circle'></i> ".$this->lang->line("Pending")."</div>";

        // Action section started from here
        if($info[$i]['posting_status']=='0' && $info[$i]['schedule_type']=='later')
          $editUrl =  "<a href='".base_url("comment_reply_enhancers/edit_bulk_comment_reply_campaign/".$info[$i]["id"])."' data-toggle='tooltip' title='".$this->lang->line("Edit Campaign")."' class='btn btn-circle btn-outline-warning'><i class='fas fa-edit'></i></a>";
        else 
          $editUrl = "<a data-toggle='tooltip' title='".$this->lang->line("Only Pending and scheduled Campaigns Are Editable")."' class='btn btn-circle btn-light pointer text-muted'><i class='fas fa-edit'></i></a>";

        if($info[$i]['posting_status'] !='1') 
          $deleteUrl = "<a href='#' data-id='".$info[$i]["id"]."' data-toggle='tooltip' title='".$this->lang->line("Delete Campaign")."' class='btn btn-circle btn-outline-danger delete_campaign'><i class='fas fa-trash-alt'></i></a>";
        else 
          $deleteUrl = "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Processing Campaigns are not deletable.")."' class='btn btn-circle btn-light pointer text-muted'><i class='fas fa-trash-alt'></i></a>";

        $action_width = ($action_count*47)+20;
        $info[$i]['actions'] = '<div class="dropdown d-inline dropright">
       <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i></button>
      <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';
        $info[$i]['actions'] .= $view_report;
        $info[$i]['actions'] .= $editUrl;
        if(isset($attachment))
          $info[$i]['actions'] .= $attachment;

        $info[$i]['actions'] .= $deleteUrl;
        $info[$i]['actions'] .= "</div></div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";         

      }

      $data['draw'] = (int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

      echo json_encode($data);
    }

    public function bulk_comment_reply_campaign_report()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access)) exit();

        $table_id = $this->input->post('table_id');
        $searching = trim($this->input->post("searching1",true));

        $display_columns = array("#","commenter_name","comment_id","comment_time","status","replied_at");

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 5;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'replied_at';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $table="tag_machine_bulk_reply";
        $info = $this->basic->get_data($table,array("where"=>array("id"=>$table_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));

        if (isset($info[0]['report']) && $info[0]['report'] != '')
        {
          $campaign_details = $info[0];

          $report_info = json_decode($campaign_details['report'],true);
          $reply_info = $report_info;

          $reply_info = array_filter($reply_info, function($single_reply) use ($searching) 
          {
            if ($searching != '') {

              if (stripos($single_reply['commenter_name'], $searching) !== false || stripos($single_reply['comment_id'], $searching) !== false || stripos($single_reply['comment_time'], $searching) !== false || stripos($single_reply['replied_at'], $searching) !== false) {
                return TRUE; 
              }
              else
                return FALSE;  
            }
            else
              return TRUE;

          });

          
          usort($reply_info, function($first, $second) use ($sort, $order)
          {
            if ($first[$sort] == $second[$sort]) {
              return 0;
            }
            else if ($first[$sort] > $second[$sort]) {
              if ($order == 'desc') return 1;
              else return -1;
            }
            else if ($first[$sort] < $second[$sort]) {
              if ($order == 'desc') return -1;
              else return 1;
            }
                            
          });


          $final_info = array();
          $i = 0;
          $upper_limit = $start + $limit;

          foreach ($reply_info as $key => $value) {
              
              if ($i >= $start && $i < ($upper_limit))
                  array_push($final_info, $value);

              $i++;
          }

          $result = array();
          foreach ($final_info as $value) {
              
            $temp = array();
            array_push($temp, ++$start);

            $commenter_fb_id = $value['commenter_fb_id'];

            foreach ($value as $key => $column) 
            {
              if($key == 'commenter_name')
                $column = $column;

              if($key == 'comment_id')
              {
                $exploded = explode("_", $column);
                $column = '<a target="_BLANK" href="https://facebook.com/'.$column.'">'.$exploded[1].'</a>';
              }

              if($key =='status' && $column == 'Pending')
                $column = '<div class="text-danger"><i class="far fa-times-circle"></i> '.$column.'</div>';

              if ($key == 'comment_time')
                  $column = date('jS F y, H:i', strtotime($column));

                if ($key == 'replied_at')
                  $column = date('jS F y, H:i', strtotime($column));
              
              if (in_array($key, $display_columns)) 
                array_push($temp, $column);
            }

            array_push($result, $temp);
              
          }

        }
        else {

            $total_result = 0;
            $reply_info = array();
            $result = array();
        }
        
        $total_result = count($reply_info);
        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = $result;


        echo json_encode($data);
    }    


    public function delete_bulk_comment_reply_campaign($id=0)
    {
        if(!$_POST) exit();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access)) exit();
        $id=$this->input->post("id");

        $xdata = $this->basic->get_data("tag_machine_bulk_reply",array("where"=>array("id"=>$id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));

        $posting_status  = isset($xdata[0]["posting_status"]) ? $xdata[0]["posting_status"] : "";

        if($posting_status=="0") // removing usage data if deleted and campaign is pending
        $this->_delete_usage_log($module_id=202,$request=1);
        
        if($this->basic->delete_data("tag_machine_bulk_reply",array("id"=>$id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))))
        {
          if($this->basic->delete_data("tag_machine_bulk_reply_send",array("campaign_id"=>$id))) echo "1"; 
          else echo "0"; 
        }      
        else echo "0";
    }

    public function edit_bulk_comment_reply_campaign($id='')
    {
        if($id==0) exit();

        $data['body'] = "edit_bulk_comment_reply_campaign";
        $data['page_title'] = $this->lang->line("Edit Bulk Comment Reply Campaign");
        $data["time_zone"]= $this->_time_zone_list();
        $data["xdata"] = $this->basic->get_data("tag_machine_bulk_reply",array("where"=>array("id"=>$id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));

        // only pending campaigns are editable
        if(!isset($data["xdata"][0]["posting_status"]) || $data["xdata"][0]["posting_status"]!='0' ) exit();
        // only scheduled campaigns can be editted
        if($data["xdata"][0]["schedule_type"]!='later') exit();
    
        $this->_viewcontroller($data);
    }

    public function edit_bulk_comment_reply_campaign_action()
    {
        if(!$_POST) exit();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access))  exit();
  
        $campaign_id = $this->input->post('campaign_id');
        $schedule_type = "later";
        $schedule_time = $this->input->post('schedule_time');
        $time_zone = $this->input->post('time_zone');

        $tag_machine_enabled_post_list_id = $this->input->post('tag_campaign_tag_machine_enabled_post_list_id');
        $facebook_rx_fb_user_info_id = $this->session->userdata("facebook_rx_fb_user_info");
        $user_id = $this->user_id;
        $campaign_name = $this->input->post('campaign_name');
        $tag_content = $this->input->post('message');
        $uploaded_image_video = $this->input->post('uploaded_image_video');
        $commenter_range = $this->input->post('commenter_range');

        $item_per_range=$this->config->item('item_per_range');
        if($item_per_range=='') $item_per_range=50;

        $explode_range=explode('-', $commenter_range);
        $start=isset($explode_range[0])?$explode_range[0]:1;
        $end=isset($explode_range[1])?$explode_range[1]:$item_per_range;
        $comenter_limit=($end-$start)+1;
        $commenter_start=$start-1;
        
        $tag_exclude = $this->input->post('exclude');
        if(!is_array($tag_exclude)) $tag_exclude = array();

        $commenter_list = $this->basic->get_data("tag_machine_commenter_info",array("where"=>array("tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,"subscribed"=>"1")),"","",$comenter_limit,$commenter_start,"last_comment_time DESC");
        $tag_database_array=array();
        $commenter_count=0;
        foreach ($commenter_list as $key => $value) 
        {
            if(in_array($value['commenter_fb_id'], $tag_exclude)) continue;

            $tag_database_array[$value["commenter_fb_id"]]=
            array
            (
                "commenter_name"=>$value["commenter_name"],
                "commenter_fb_id"=>$value["commenter_fb_id"],
                "last_comment_id"=>$value["last_comment_id"],
                "last_comment_time"=>$value["last_comment_time"]
            );
            $commenter_count++;
        }
        $tag_database=json_encode($tag_database_array);
        $current_time=date("Y-m-d H:i:s");
        $data = array
        (
           "campaign_name"=>$campaign_name, 
           "tag_database"=>$tag_database, 
           "tag_exclude"=>json_encode($tag_exclude), 
           "tag_content"=>$tag_content, 
           "uploaded_image_video"=>$uploaded_image_video,
           "last_updated_at"=>$current_time,            
           "commenter_count"=>$commenter_count,
           "schedule_time"=>$schedule_time,
           "time_zone"=>$time_zone
        );
        $this->basic->update_data("tag_machine_bulk_tag",array("id"=>$campaign_id,"facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id),$data);
        $campaign_link=" <a href='".base_url('comment_reply_enhancers/bulk_tag_campaign_list/0/'.$campaign_id)."'>".$this->lang->line('click here to see report.')."</a>";
        $success_message = $campaign_link;
        echo json_encode(array('status'=>'1','message'=>$success_message));

    }

    public function edit_comment_reply_campaign_action()
    {
        if(!$_POST) exit();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(202,$this->module_access))  exit();

        $campaign_id = $this->input->post('campaign_id');
        $schedule_type = 'later';
        $schedule_time = $this->input->post('schedule_time2');
        $time_zone = $this->input->post('time_zone2');

        $tag_machine_enabled_post_list_id = $this->input->post('bulk_comment_reply_campaign_enabled_post_list_id');
        $facebook_rx_fb_user_info_id = $this->session->userdata("facebook_rx_fb_user_info");
        $user_id = $this->user_id;
        $campaign_name = $this->input->post('campaign_name2');
        $reply_content = $this->input->post('message2');
        $uploaded_image_video = $this->input->post('uploaded_image_video2');
        $reply_multiple = $this->input->post('reply_multiple');
        $delay_time = $this->input->post('delay_time');
        if($delay_time=="") $delay_time=0;
        $delay_time=abs($delay_time);

        $comment_list = $this->basic->get_data("tag_machine_comment_info",array("where"=>array("tag_machine_enabled_post_list_id"=>$tag_machine_enabled_post_list_id,"subscribed"=>"1")),"","","","","comment_time DESC");
        $report=array();
        $comment_count=0;
        $only_commenter_id_array=array();
        foreach ($comment_list as $key => $value) 
        {
            if($reply_multiple=='0')
            {
                if(in_array($value["commenter_fb_id"], $only_commenter_id_array))
                continue;
            } 

            $report[]=
            array
            (
                "commenter_name"=>$value["commenter_name"],
                "commenter_fb_id"=>$value["commenter_fb_id"],
                "comment_id"=>$value["comment_id"],
                "comment_time"=>$value["comment_time"],
                "status"=>'Pending',
                "replied_at"=>"x"
            );
            $only_commenter_id_array[]=$value["commenter_fb_id"];
            $comment_count++;
        }
        $report_json=json_encode($report);
        $current_time=date("Y-m-d H:i:s");
        $data = array
        (
           "campaign_name"=>$campaign_name, 
           "reply_content"=>$reply_content, 
           "uploaded_image_video"=>$uploaded_image_video,
           "reply_multiple"=>$reply_multiple,
           "report"=>$report_json,           
           "delay_time"=>$delay_time,            
           "total_reply"=>$comment_count, 
           "last_updated_at"=>$current_time,   
           "schedule_time"=>$schedule_time,
           "time_zone"=>$time_zone
        );

        $this->db->trans_start();

        $this->basic->update_data("tag_machine_bulk_reply",array("id"=>$campaign_id,"facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id),$data);

        $this->basic->delete_data("tag_machine_bulk_reply_send",array("campaign_id"=>$campaign_id));
        foreach ($report as $key => $value) 
        {
            $insert_now=$value;
            $insert_now['campaign_id']=$campaign_id;
            unset($insert_now['status']);
            unset($insert_now['replied_at']);
            $this->basic->insert_data("tag_machine_bulk_reply_send",$insert_now);
        }
        
        $this->db->trans_complete();
        if($this->db->trans_status() === false) 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
            exit();
        }

        $campaign_link="<a href='".base_url('comment_reply_enhancers/bulk_comment_reply_campaign_list/0/'.$campaign_id)."'>".$this->lang->line('click here to see report.')."</a>";
        $success_message = $campaign_link;
        echo json_encode(array('status'=>'1','message'=>$success_message));

    }


  

    public function upload_image_video()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();
        $ret=array();
        $output_dir = FCPATH."upload/comment_reply_enhancers";

        $folder_path = FCPATH."upload/comment_reply_enhancers";
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }

        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $ext=strtolower($ext);
            $filename=implode('.', $post_fileName_array);
            $filename="image_video_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;

            $allow=".jpg,.jpeg,.png,.flv,.mp4,.wmv,.WMV,.MP4,.FLV";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
              $custom_error['jquery-upload-file-error']=$this->lang->line("File type not allowed.");
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
        $output_dir = FCPATH."upload/comment_reply_enhancers/";
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
        // $this->db->query("DELETE FROM `menu_child_1` WHERE `url` = 'comment_reply_enhancers/post_list'");
        $this->unregister_addon($addon_controller_name);         
    }

    public function delete()
    {        
        $this->ajax_check();
 
        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]

        // mysql raw query needed to run, it's an array, put each query in a seperate index, drop table/column query should have IF EXISTS
        $sql = array();
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }



    // PageResponse add-on section
    public function api_member_validity($user_id='')
    {
        if($user_id!='') {
            $where['where'] = array('id'=>$user_id);
            $user_expire_date = $this->basic->get_data('users',$where,$select=array('expired_date'));
            $expire_date = strtotime($user_expire_date[0]['expired_date']);
            $current_date = strtotime(date("Y-m-d"));
            $package_data=$this->basic->get_data("users",$where=array("where"=>array("users.id"=>$user_id)),$select="package.price as price, users.user_type",$join=array('package'=>"users.package_id=package.id,left"));

            if(is_array($package_data) && array_key_exists(0, $package_data) && $package_data[0]['user_type'] == 'Admin' )
                return true;

            $price = '';
            if(is_array($package_data) && array_key_exists(0, $package_data))
            $price=$package_data[0]["price"];
            if($price=="Trial") $price=1;

            
            if ($expire_date < $current_date && ($price>0 && $price!=""))
            return false;
            else return true;
        }
    }


    public function ajax_get_reply_info()
    {
        $this->ajax_check();

        $table_id = $this->input->post('table_id');
        $searching = $this->input->post('searching',true);

        $display_columns = array("#","comment_text","commenter_name","comment_time","reply_time","comment_reply_id","reply_id","reply_status_comment","reply_status","hide_delete_status");

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 3;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'comment_time';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where['where'] = array('autoreply_table_id'=> $table_id,'reply_type'=>'full_page_response');

        $sql = '';
        if ($searching != '') 
          $sql = "(comment_text LIKE '%".$searching."%' OR commenter_name LIKE '%".$searching."%' OR comment_reply_text LIKE '%".$searching."%' OR reply_text LIKE '%".$searching."%')";
        if($sql != '')
          $this->db->where($sql);

        $table="facebook_ex_autoreply_report";
        $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');


        if($sql != '')
          $this->db->where($sql);
        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $i = 0;
        $info_new = array();
        foreach($info as $value)
        {
          $info_new[$i]['comment_text'] = "<a href='https://facebook.com/".$value['comment_id']."' target='_BLANK'>".$value['comment_text']."</a>";
          $info_new[$i]['commenter_name'] = $value['commenter_name'];
          $info_new[$i]['comment_time'] = date('jS F y, H:i', strtotime($value['comment_time']));
          $info_new[$i]['reply_time'] = date('jS F y, H:i', strtotime($value['reply_time']));

          $search_char = ["'",'"'];

          if($value['comment_reply_text'] != '')
          {
            $comment_text = str_replace($search_char, "`", $value['comment_reply_text']);
            $short_comment = mb_substr($value['comment_reply_text'],0,20);
            $short_comment = str_replace($search_char, "`", $short_comment);
            $info_new[$i]['comment_reply_id'] = "<a data-toggle='tooltip' data-placement='top' title='".$comment_text."' href='https://facebook.com/".$value['comment_reply_id']."' target='_BLANK'>".$short_comment."...</a>";            
          }
          else
            $info_new[$i]['comment_reply_id'] = '';

          if($value['reply_text'] != '')
          {
            $substr = substr($value['reply_text'],0,2);
            if($substr == '["')
            {
              $reply_text = json_decode($value['reply_text'],true);
              $postback_link = base_url('messenger_bot/edit_template').'/'.$reply_text[0];
              $info_new[$i]['reply_id'] = '<div data-toggle="tooltip" data-placement="top" title="You can view/edit private reply message template by clicking here."><a href="'.$postback_link.'" target="_BLANK">View/Edit</a></div><script>$(\'[data-toggle="tooltip"]\').tooltip();</script>';
              
            }
            else
            {
              $full_message = str_replace($search_char, "`", $value['reply_text']);
              $short_msg = mb_substr($value['reply_text'],0,20);
              $short_msg = str_replace($search_char, "`", $short_msg);
              $info_new[$i]['reply_id'] = '<div data-toggle="tooltip" data-placement="top" title="'.$full_message.'">'.$short_msg.'...</div><script>$(\'[data-toggle="tooltip"]\').tooltip();</script>';
            }
          }
          else
            $info_new[$i]['reply_id'] = '';

          if($value['reply_status_comment'] == 'success')
            $info_new[$i]['reply_status_comment'] = "<span class='text-success'><i class='fas fa-check-circle'></i> Success</span>";
          else
            $info_new[$i]['reply_status_comment'] = $value['reply_status_comment'];

          $substr = substr($value['reply_status'],0,2);
          if($substr == '["')
          {
            $reply_status = json_decode($value['reply_status'],true);
            $info_new[$i]['reply_status'] = '';
            foreach($reply_status as $valuex)
            {
              if($valuex == 'success')
                $info_new[$i]['reply_status'] .= "<span class='text-success'><i class='fas fa-check-circle'></i> Success</span><br/>";
              else
                $info_new[$i]['reply_status'] .= $valuex."<br/>";
            }
          }
          else
          {
            if($value['reply_status'] == 'success')
              $info_new[$i]['reply_status'] = "<span class='text-success'><i class='fas fa-check-circle'></i> Success</span>";
            else
              $info_new[$i]['reply_status'] = $value['reply_status'];
          }

          if($value['is_deleted'] == '1')
            $info_new[$i]['hide_delete_status'] = "<span class='text-danger'><i class='fas fa-trash'></i> Deleted</span>";
          else if ($value['is_hidden'] == '1')
            $info_new[$i]['hide_delete_status'] = "<span class='text-warning'><i class='fas fa-eye-slash'></i> Hidden</span>";
          else
            $info_new[$i]['hide_delete_status'] = "";
          $i++;
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info_new, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function get_count_info()
    {
      $this->ajax_check();
      $table_id = $this->input->post('table_id',true);
      $info = $this->basic->get_data('facebook_ex_autoreply_report',array('where'=>array('autoreply_table_id'=>$table_id,'reply_type'=>'full_page_response')));
      $comment_reply_sent = 0;
      $private_reply_sent = 0;
      $hidden_comment = 0;
      $deleted_comment = 0;
      foreach($info as $value)
      {
        if($value['reply_status_comment'] == 'success')
          $comment_reply_sent++;
        if($value['reply_status'] == 'success')
          $private_reply_sent++;
        if($value['is_deleted'] == '1')
          $deleted_comment++;
        if($value['is_hidden'] == '1')
          $hidden_comment++;
      }
      $str = "<div class='row text-center'><div class='col-6 col-sm-3'><i class='fas fa-reply-all blue'></i> ".$this->lang->line('Private reply sent')." : ".$private_reply_sent."</div>";
      $str .= "<div class='col-6 col-sm-3'><i class='fas fa-comment-dots green'></i> ".$this->lang->line('Comment reply sent')." : ".$comment_reply_sent."</div>";
      if(ultraresponse_addon_module_exist())
      {        
        $str .= "<div class='col-6 col-sm-3'><i class='fas fa-trash red'></i> ".$this->lang->line('Comment deleted')." : ".$deleted_comment."</div>";
        $str .= "<div class='col-6 col-sm-3'><i class='fas fa-eye-slash orange'></i> ".$this->lang->line('Comment hidden')." : ".$hidden_comment."</div>";
      }
      $str .= "</div>";

      echo json_encode(array('status'=>'1','str'=>$str));
    }

    public function download_get_reply_info($table_id)
    {

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        
        $reply_info = $this->basic->get_data('facebook_ex_autoreply_report',array('where'=>array('autoreply_table_id'=>$table_id,'reply_type'=>'full_page_response')));

        if(!empty($reply_info))
        {
            $filename="{$this->user_id}_commentator_info.csv";
            // make output csv file unicode compatible
            $f = fopen('php://memory', 'w'); 
            fputs( $f, "\xEF\xBB\xBF" );

            /**Write header in csv file***/
            $write_data[]="Name";
            $write_data[]="Client Id";
            $write_data[]="Comment Id";
            $write_data[]="Comment Text";

            fputcsv($f,$write_data, ",");

            foreach($reply_info as $value)
            {
                
                $write_data=array();
                $write_data[]=$value['commenter_name'];
                $write_data[]=$value['commenter_id'];
                $write_data[]=$value['comment_id'];
                $write_data[]=$value['comment_text'];

                fputcsv($f,$write_data, ",");
            }

            // reset the file pointer to the start of the file
            fseek($f, 0);
            // tell the browser it's going to be a csv file
            header('Content-Type: application/csv');
            // tell the browser we want to save it instead of displaying it
            header('Content-Disposition: attachment; filename="'.$filename.'";');
            // make php send the generated csv lines to the browser
            fpassthru($f);  
        }
        else
        {
            $str = "<div class='alert alert-danger'>{$this->lang->line("no data to show")}</div>";
        }

        // echo $str;
    }


    public function all_response_report()
    {
      $page_list = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),'bot_enabled'=>'1')),array('page_name','id'));
      $data['page_info'] = $page_list;
      $data['body'] = 'all_response_report';
      $data['page_title'] = $this->lang->line('Full Page Response - Report');
      $this->_viewcontroller($data);
    }

    public function all_response_report_data()
    {
      $this->ajax_check();
      $page_id = $this->input->post('page_id',true);
      $post_id = $this->input->post('post_id',true);
      $display_columns = array("#","CHECKBOX",'id', 'page_name', 'post_id', 'auto_private_reply_count', 'auto_comment_reply_count', 'hidden_comment_count', 'deleted_comment_count', 'view', 'last_reply_time', 'error_message');

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 10;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'last_reply_time';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
      $order_by=$sort." ".$order;


      $where_simple = array();
      $where_simple['user_id'] = $this->user_id;
      if($page_id != '') $where_simple['page_info_table_id'] = $page_id;
      if($post_id != '') $where_simple['post_id like'] = "%".$post_id."%";
      $table="page_response_report";
      $where = array('where'=>$where_simple);

      $info=$this->basic->get_data($table,$where,'','',$limit,$start,$order_by,$group_by='');

      $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
      $total_result=$total_rows_array[0]['total_rows'];

      $i=0;
      $base_url=base_url();
      foreach ($info as $key => $value) 
      {
          $info[$i]['post_id']="<a target='_BLANK' href='https://facebook.com/".$value['post_id']."'>".$value['post_id']."</a>";
          $info[$i]['last_reply_time']= date('jS F y, H:i', strtotime($value['last_reply_time']));
          $info[$i]["view"] = "<button title='".$this->lang->line("Report")."' class='btn btn-outline-info view_report' table_id='".$value['id']."'><i class='fa fa-eye'></i></button>";
          $i++;
      }

      $data['draw'] = (int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

      echo json_encode($data);

    }


    public function page_response_report($id=0)
    {
      if($id==0) exit();
        $data['table_id'] = $id;
        $data['body'] = 'page_response_report';
        $data['page_title'] = $this->lang->line('Page Response - Report');
        $this->_viewcontroller($data);
    }

    public function page_response_report_data($page_table_id=0)
    {
      $this->ajax_check();
      $page_id = $this->input->post('page_id',true);
      $post_id = $this->input->post('post_id',true);
      $display_columns = array("#","CHECKBOX",'id', 'page_name', 'post_id', 'auto_private_reply_count', 'auto_comment_reply_count', 'hidden_comment_count', 'deleted_comment_count', 'view', 'last_reply_time', 'error_message');

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 10;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'last_reply_time';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
      $order_by=$sort." ".$order;


      $where_simple = array();
      $where_simple['user_id'] = $this->user_id;
      $where_simple['page_info_table_id'] = $page_table_id;
      if($post_id != '') $where_simple['post_id like'] = "%".$post_id."%";
      $table="page_response_report";
      $where = array('where'=>$where_simple);

      $info=$this->basic->get_data($table,$where,'','',$limit,$start,$order_by,$group_by='');

      $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
      $total_result=$total_rows_array[0]['total_rows'];

      $i=0;
      $base_url=base_url();
      foreach ($info as $key => $value) 
      {
          $info[$i]['post_id']="<a target='_BLANK' href='https://facebook.com/".$value['post_id']."'>".$value['post_id']."</a>";
          $info[$i]['last_reply_time']= date('jS F y, H:i', strtotime($value['last_reply_time']));
          $info[$i]["view"] = "<button title='".$this->lang->line("Report")."' class='btn btn-outline-info view_report' table_id='".$value['id']."'><i class='fa fa-eye'></i></button>";
          $i++;
      }

      $data['draw'] = (int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

      echo json_encode($data);
    }



    public function all_like_share_report()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(206,$this->module_access))
        redirect('home/login_page', 'location'); 
        $page_list = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"user_id"=>$this->user_id,'bot_enabled'=>'1')),array('page_name','id'));
        $data['page_info'] = $page_list;
        $data['body'] = 'all_like_share_report';
        $data['page_title'] = $this->lang->line('Auto like/share - Report');
        $this->_viewcontroller($data);
    }



    public function all_like_share_report_data()
    {
      $this->ajax_check();
      $page_id = $this->input->post('page_id',true);
      $post_id = $this->input->post('post_id',true);
      $display_columns = array("#","CHECKBOX",'id', 'page_name', 'post_id', 'like_done', 'share_done', 'view', 'share_last_tried', 'like_last_tried');

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'share_last_tried';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
      $order_by=$sort." ".$order;


      $where_simple = array();
      $where_simple['user_id'] = $this->user_id;
      if($page_id != '') $where_simple['page_info_table_id'] = $page_id;
      if($post_id != '') $where_simple['post_id like'] = "%".$post_id."%";
      $table="page_response_auto_like_share_report";
      $where = array('where'=>$where_simple);

      $info=$this->basic->get_data($table,$where,'','',$limit,$start,$order_by,$group_by='');

      $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
      $total_result=$total_rows_array[0]['total_rows'];

      $i=0;
      $base_url=base_url();
      foreach ($info as $key => $value) 
      {
          $info[$i]['post_id']="<a target='_BLANK' href='https://facebook.com/".$value['post_id']."'>".$value['post_id']."</a>";
          $info[$i]["view"] = "<button title='".$this->lang->line("Report")."' class='btn btn-outline-info view_report' table_id='".$value['id']."'><i class='fa fa-eye'></i></button>";
          $i++;
      }

      $data['draw'] = (int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

      echo json_encode($data);

    }

    public function page_like_share_report($id=0)
    {
      if($id==0) exit();
      $data['table_id'] = $id;
      $data['body'] = 'page_like_share_report';
      $data['page_title'] = $this->lang->line('Auto like/share - Report');
      $this->_viewcontroller($data);
    }

    public function page_like_share_report_data($page_table_id=0)
    {
      $this->ajax_check();
      $post_id = $this->input->post('post_id',true);
      $display_columns = array("#","CHECKBOX",'id', 'page_name', 'post_id', 'like_done', 'share_done', 'view', 'share_last_tried', 'like_last_tried');

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'share_last_tried';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
      $order_by=$sort." ".$order;


      $where_simple = array();
      $where_simple['page_info_table_id'] = $page_table_id;
      $where_simple['user_id'] = $this->user_id;
      if($post_id != '') $where_simple['post_id like'] = "%".$post_id."%";
      $table="page_response_auto_like_share_report";
      $where = array('where'=>$where_simple);

      $info=$this->basic->get_data($table,$where,'','',$limit,$start,$order_by,$group_by='');

      $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
      $total_result=$total_rows_array[0]['total_rows'];

      $i=0;
      $base_url=base_url();
      foreach ($info as $key => $value) 
      {
          $info[$i]['post_id']="<a target='_BLANK' href='https://facebook.com/".$value['post_id']."'>".$value['post_id']."</a>";
          $info[$i]["view"] = "<button title='".$this->lang->line("Report")."' class='btn btn-outline-info view_report' table_id='".$value['id']."'><i class='fa fa-eye'></i></button>";
          $i++;
      }

      $data['draw'] = (int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

      echo json_encode($data);

    }

    public function like_share_details()
    {
        if(!$_POST) exit();
        $campaign_id = $this->input->post('table_id');
        $campaign_data = $this->basic->get_data('page_response_auto_like_share_report',array('where'=>array('id'=>$campaign_id,'user_id'=>$this->user_id)));

        

        if(isset($campaign_data[0]))
        {
            $page_info = array();
            $page_list = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id)));
            if(!empty($page_list))
            {
                foreach($page_list as $value)
                {
                    $page_info[$value['id']]=$value['page_name'];
                }
            }

            if($campaign_data[0]['auto_share_post']=='0') $auto_like_status = "<span><i class='fa fa-ban red'></i> N/A</span>"; 
         else if($campaign_data[0]['auto_share_post']=='3') $auto_like_status = '<span><i class="fa fa-check green"></i> '.$this->lang->line('completed').'</span>';
         else $auto_like_status = '<span><i class="fa fa-remove orange"></i> '.$this->lang->line('pending').'</span>';

         if($campaign_data[0]['auto_share_post']=='0') $auto_share_status = "<span><i class='fa fa-ban red'></i> N/A</span>"; 
         else if($campaign_data[0]['auto_share_post']=='3') $auto_share_status = '<span><i class="fa fa-check green"></i> '.$this->lang->line('completed').'</span>';
         else $auto_share_status = '<span><i class="fa fa-remove orange"></i> '.$this->lang->line('pending').'</span>';

         $auto_share_report=json_decode($campaign_data[0]['auto_share_report'],true);
         $auto_like_post_report=json_decode($campaign_data[0]['auto_like_report'],true);

         $str = '
           	<div class="row">
   	        	<div class="col-md-6 col-sm-6 col-12">
   	        	  <div class="card card-statistic-1">
   	        	    <div class="card-icon bg-primary">
   	        	      <i class="far fa-newspaper"></i>
   	        	    </div>
   	        	    <div class="card-wrap">
   	        	      <div class="card-header">
   	        	        <h4>'.$this->lang->line('Page Name').'</h4>
   	        	      </div>
   	        	      <div class="card-body"> 
   	        	        '.$campaign_data[0]['page_name'].'
   	        	      </div>
   	        	    </div>
   	        	  </div>
   	        	</div>
   	        	<div class="col-md-6 col-sm-6 col-12">
   	        	  <div class="card card-statistic-1">
   	        	    <div class="card-icon bg-info">
   	        	      <i class="fas fa-id-card-alt"></i>
   	        	    </div>
   	        	    <div class="card-wrap">
   	        	      <div class="card-header">
   	        	        <h4>'.$this->lang->line('Post ID').'</h4>
   	        	      </div>
   	        	      <div class="card-body"> 
   	        	        <a target="__BLANK" href="https://facebook.com/.'.$campaign_data[0]['post_id'].'">'.$campaign_data[0]['post_id'].'</a>
   	        	      </div>
   	        	    </div>
   	        	  </div>
   	        	</div>
           	</div>
           	<div class="row">
           		<div class="col-md-6 col-sm-6 col-12">
           			<div class="card">
           			  <div class="card-header">
           			    <h4><i class="fas fa-thumbs-up"></i> '.$this->lang->line('Auto Like').'</h4>
           			  </div>
           			  <div class="card-body">
           			  	 <div class="section">
   	        			    <div class="section-title mt-0">'.$this->lang->line('Status').'</div>
   	        			    <p style="margin-left: 8%;">'.$auto_like_status.'</p>
           			  	 </div>
           			  	 <div class="section">
   	        			    <div class="section-title mt-0">'.$this->lang->line('Auto Like Report').'</div>
   	        			    <p style="margin-left: 8%;">';
   	        	$sl=0;
   	        	foreach ($auto_like_post_report as $key2 => $value2) 
   	        	{
   	        	    $sl++;
   	        	    if($value2["status"]=='Success') $icon="<i class='fa fa-check-circle green'></i>";
   	        	    else $icon="<i class='fa fa-times-circle red'></i>";

   	        	    $str .= "<br>".$icon." ".$sl.". ".$value2["page_name"]." : ".$value2["status"]."<br>";
   	        	}
	   	      
	   	      $str .= '
   	        			    </p>
           			  	 </div>           			    
           			  </div>
           			</div>
           		</div>
           		<div class="col-md-6 col-sm-6 col-12">
           			<div class="card">
           			  <div class="card-header">
           			    <h4><i class="fas fa-share-alt"></i> '.$this->lang->line('Auto Share').'</h4>
           			  </div>
           			  <div class="card-body">
           			  	 <div class="section">
   	        			    <div class="section-title mt-0">'.$this->lang->line('Status').'</div>
   	        			    <p style="margin-left: 8%;">'.$auto_share_status.'</p>
           			  	 </div>
           			  	 <div class="section">
   	        			    <div class="section-title mt-0">'.$this->lang->line('Auto Share as Pages').'</div>
   	        			    <p style="margin-left: 8%;">';

   	     		if(!empty($auto_share_report))
   	     		{
   	     		   $sl=0;
   	     		   foreach ($auto_share_report as $value2) 
   	     		   {
   	     		       $sl++;
   	     		       $tempsucc=$value2["status"];
   	     		       if($tempsucc=='Success') $icon="<i class='fa fa-check-circle green'></i>";
   	     		       else $icon="<i class='fa fa-times-circle red'></i>";
                   $str .= "<br>".$icon." ".$sl.". ".$value2["page_name"]." : ".$tempsucc."<br>";
   	     		   }
   	     		}

   	   			$str .='
   	        			    </p>
           			  	 </div>           			    
           			  </div>
           			</div>
           		</div>
           	</div>
           ';

            
        }
        else
        {
            $str = "<div class='alert alert-danger'>No data to show</div>";
        }

        echo $str;
    }



    public function pause_play_campaign()
    {
        $table_id=$this->input->post('table_id');
        $to_do=$this->input->post('to_do');
        $update_data = array('pause_play'=>$to_do);
        $this->basic->update_data('page_response_autoreply',array('id'=>$table_id),$update_data);
        $response['success'] = 'success';
        echo json_encode($response);
    }



}
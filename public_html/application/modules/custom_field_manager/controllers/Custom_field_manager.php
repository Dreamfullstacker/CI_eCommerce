<?php
/*
Addon Name: User Input & Custom Fields
Unique Name: custom_field_manager
Modules:
{
   "292":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"month",
      "module_name":"User Input Flow Campaign"
   }
}
Project ID: 49
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 2.0.1
Description: 
*/

require_once("application/controllers/Home.php"); // loading home controller

class Custom_field_manager extends Home
{
  public $addon_data=array();
  public function __construct()
  {
    parent::__construct();
    if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');

    if($this->basic->is_exist("add_ons",array("project_id"=>49)))
      if($this->session->userdata('user_type') != 'Admin' && !in_array(292,$this->module_access))
        redirect('home/login', 'location');
    // getting addon information in array and storing to public variable
    // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
    //------------------------------------------------------------------------------------------
    $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
    $this->addon_data=$this->get_addon_data($addon_path); 
    $this->member_validity();
    $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it
  }


  public function index()
	{
    $this->activate(); 
	}

  public function custom_field_list($page_id=0,$iframe='0',$media_type="fb")
  { 
    $reply_types = ["Email","Phone","Text","Number","URL","File","Image","Video","Date","Time","Datetime"];
    $data['reply_types'] = $reply_types;
    $data['media_type'] = $media_type;
    $data['body'] = 'custom_field_list';
    $data['page_title'] = $this->lang->line("Custom field list");
    if($media_type =="ig")
    $data['page_title'] = $this->lang->line("Instagram Custom field list");

    $data['page_id'] = $page_id;
    $data['iframe'] = $iframe;
    $data['media_type'] = $this->session->userdata('selected_global_media_type');

    $this->_viewcontroller($data);  
    
  }

  public function custom_field_list_data()
  { 
    $this->ajax_check();
    $searching = isset($_POST['search']) ? $_POST['search']['value'] : null;
    $media_type = $this->input->post('media_type',true);
    $display_columns = array("#","id","name","reply_type","create_time","actions");

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
    $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
    $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
    $order_by=$sort." ".$order;

    $where_simple = array();
    $where_simple['user_id'] = $this->user_id;
    $where_simple['media_type'] = $media_type;

    $sql = '';
    if($searching != '') $where_simple['name LIKE'] = "%".$searching."%";

    $where = array("where"=> $where_simple);
    $table="user_input_custom_fields";
    $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

    $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
    $total_result=$total_rows_array[0]['total_rows'];


    for($i=0;$i<count($info);$i++) 
    {
      $info[$i]['name'] = $info[$i]['name'];
      $info[$i]['reply_type'] = $info[$i]['reply_type'];
      $info[$i]['create_time'] = date("d M y H:i",strtotime($info[$i]['create_time']));
      $info[$i]['actions'] = "<a href='#' class='btn btn-outline-danger btn-circle delete_custom_field' media_type='".$media_type."' table_id='".$info[$i]['id']."' title='".$this->lang->line("Delete Custom Field")."'><i class='fas fa-trash-alt'></i></a>";
    }

    $data['draw'] = (int)$_POST['draw'] + 1;
    $data['recordsTotal'] = $total_result;
    $data['recordsFiltered'] = $total_result;
    $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

    echo json_encode($data);
  }

  
  public function ajax_custom_field_insert()
  {
    $this->ajax_check();

    $return = array();
    $user_id = $this->user_id;
    $custom_field_name = strip_tags(trim($this->input->post("custom_field_name")));
    $media_type = $this->input->post("media_type",true);
    $selected_reply_type    = trim($this->input->post("selected_reply_type"));
    $custom_field_info = $this->basic->get_data('user_input_custom_fields',['where'=>['reply_type'=>$selected_reply_type,'name'=>$custom_field_name,'user_id'=>$user_id,'media_type'=>$media_type]]);
    if(empty($custom_field_info))
    {
      $insert_data = [
        'user_id' => $user_id,
        'name' => $custom_field_name,
        'reply_type' => $selected_reply_type,
        'media_type' => $media_type,
        'create_time' => date("Y-m-d H:i:s")
      ];
      if($this->basic->insert_data("user_input_custom_fields",$insert_data))
      {
        $insert_id = $this->db->insert_id();
        $str = "<option value=''>".$this->lang->line('Please select')."</option>";
        $all_custom_fields = $this->basic->get_data('user_input_custom_fields',['where'=>['reply_type'=>$selected_reply_type,'user_id'=>$user_id,"media_type"=>$media_type]]);
        foreach($all_custom_fields as $value)
        {
          $selected = '';
          if($value['id']==$insert_id) $selected = 'selected';
          $str .= "<option value='".$value['id']."' ".$selected.">".$value['name']."</option>";
        }
        $return['status'] = "insert";
        $return['message'] = $str;
      }
    }
    else
    {
      $where = [
        'user_id' => $user_id,
        'name' => $custom_field_name,
        'reply_type' => $selected_reply_type,
        'media_type' => $media_type,
      ];
      $update_data = ['create_time' => date("Y-m-d H:i:s")];
      $this->basic->update_data('user_input_custom_fields',$where,$update_data);
      $return['status'] = "1";
      $return['message'] = $this->lang->line("Custom field been updated successfully.");
    }

    echo json_encode($return);
  }

  public function ajax_custom_field_insert2()
  {
    $this->ajax_check();

    $return = array();
    $user_id = $this->user_id;
    $custom_field_name = strip_tags(trim($this->input->post("name")));
    $media_type = $this->input->post("media_type",true);
    $selected_reply_type = trim($this->input->post("reply_type"));

    $custom_field_info = $this->basic->get_data('user_input_custom_fields',['where'=>['reply_type'=>$selected_reply_type,'name'=>$custom_field_name,'user_id'=>$user_id,'media_type'=>$media_type]]);

    if(empty($custom_field_info)) {
      $insert_data = [
        'user_id' => $user_id,
        'name' => $custom_field_name,
        'reply_type' => $selected_reply_type,
        'media_type' => $media_type,
        'create_time' => date("Y-m-d H:i:s")
      ];

      if($this->basic->insert_data("user_input_custom_fields",$insert_data)) {
        $insert_id = $this->db->insert_id();
        $return['id'] = $insert_id;
        $return['name'] = $custom_field_name;
      }
    } else {
      $where = [
        'user_id' => $user_id,
        'name' => $custom_field_name,
        'reply_type' => $selected_reply_type,
        'media_type' => $media_type,
      ];

      $update_data = ['create_time' => date("Y-m-d H:i:s")];
      $this->basic->update_data('user_input_custom_fields',$where, $update_data);

      $return['id'] = $custom_field_info[0]['id'];
      $return['name'] = $custom_field_name;
    }

    echo json_encode($return);
  }  

  public function ajax_delete_custom_field()
  {
    $this->ajax_check();
    $this->csrf_token_check();
    $return = array();
    $primary_key = trim($this->input->post("table_id",true));  
    $media_type = $this->input->post("media_type",true);  

    $this->db->trans_start();

    $this->basic->delete_data('user_input_custom_fields',['id'=>$primary_key,'user_id'=>$this->user_id,"media_type"=>$media_type]);     
    $this->basic->delete_data('user_input_custom_fields_assaign',['custom_field_id'=>$primary_key]);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
    {
        echo json_encode(array("status" => "0", "message" =>$this->lang->line("Database error occured during delete.")));
        exit();
    }
    else
    {
      $return['status'] = 'successfull';
      $return['message'] = $this->lang->line("Custom field has been deleted successfully.");
      echo json_encode($return); 
    }

  }

  public function campaign_list($page_id=0,$iframe='0',$media_type="fb")
  { 
    if($this->session->userdata('selected_global_media_type') != '') {
      $media_type = $this->session->userdata('selected_global_media_type');
    }
    $data['body'] = 'campaign_list';
    $data['media_type'] = $media_type;
    $data['page_title'] = $this->lang->line("Facebook Flow Campaign list");
    if($media_type=="ig")
      $data['page_title'] = $this->lang->line("Instagram Flow Campaign list");

    $data['page_id'] = $page_id;
    $data['iframe'] = $iframe;
    $data['media_type'] = $this->session->userdata('selected_global_media_type');

    $this->_viewcontroller($data);
  }

  public function campaign_list_data()
  { 
    $this->ajax_check();
    $searching = isset($_POST['search']) ? $_POST['search']['value'] : null;
    $media_type = $this->input->post('media_type',true);
    $display_columns = array("#","id","flow_name","page_name",'visual_flow_type',"actions");

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
    $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'flow_name';
    $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'asc';
    $order_by=$sort." ".$order;

    $where_simple = array();
    $where_simple['user_input_flow_campaign.user_id'] = $this->user_id;
    $where_simple['user_input_flow_campaign.media_type'] = $media_type;

    $sql = '';
    if($searching != '') $where_simple['flow_name LIKE'] = "%".$searching."%";

    $where = array("where"=> $where_simple);
    $table="user_input_flow_campaign";
    $join = ['facebook_rx_fb_page_info'=>'user_input_flow_campaign.page_table_id=facebook_rx_fb_page_info.id,left'];
    $select = ['user_input_flow_campaign.*','page_name'];
    $info = $this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');

    $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
    $total_result=$total_rows_array[0]['total_rows'];

    for($i=0;$i<count($info);$i++) 
    {
      $info[$i]['flow_name'] = $info[$i]['flow_name'];
      $info[$i]['page_name'] = $info[$i]['page_name'];
      
      // $edit_url = "<a href='".base_url("custom_field_manager/edit_question_content/").$info[$i]['id']."/".$media_type."' class='btn btn-outline-warning btn-circle' data-toggle='tooltip' title='".$this->lang->line("Edit Campaign")."'>";

      $edit_url = $delete_url = '';
      $edit_url = "<a href='".base_url("custom_field_manager/edit_question_content/").$info[$i]['id']."/1/".$media_type."' class='btn btn-outline-warning btn-circle' data-toggle='tooltip' title='".$this->lang->line("Edit Campaign")."'><i class='fas fa-edit'></i></a>&nbsp;";
      if($info[$i]['visual_flow_type'] == 'flow')
      {
        $flow_campaign_exist = $this->basic->get_data('visual_flow_builder_campaign',['where'=>['id'=>$info[$i]['visual_flow_campaign_id'],'user_id'=>$this->user_id]],['id']);
        if(!empty($flow_campaign_exist)) {
          $edit_url = "<a target='_BLANK' href='".base_url("visual_flow_builder/edit_builder_data/").$info[$i]['visual_flow_campaign_id']."/3/".$media_type."' class='btn btn-outline-warning btn-circle' data-toggle='tooltip' title='".$this->lang->line("Edit Campaign")."'>  <i class='fas fa-edit'></i></a>&nbsp;";
        }
      }

      if($info[$i]['visual_flow_type'] == 'general') {
        $delete_url = "<a href='#' class='btn btn-outline-danger btn-circle delete_campaign' media_type='".$media_type."' table_id='".$info[$i]['id']."' data-toggle='tooltip' title='".$this->lang->line("Delete Campaign")."'><i class='fas fa-trash-alt'></i></a>";
      }

      $info[$i]['actions'] = "<a href='#' class='btn btn-outline-info btn-circle view_report' media_type='".$media_type."' table_id='".$info[$i]['id']."' data-toggle='tooltip' title='".$this->lang->line("Report")."'><i class='fas fa-eye'></i></a>&nbsp;<a data-toggle='tooltip' title='".$this->lang->line('Export flow data')."' href='#' class='btn btn-circle btn-outline-success export_data' media_type='".$media_type."' table_id='". $info[$i]['id'] . "'><i class='fas fa-file-export'></i></a>&nbsp;".$edit_url.$delete_url." <script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";

      $info[$i]['visual_flow_type'] = ucfirst($info[$i]['visual_flow_type']);
    }

    $data['draw'] = (int)$_POST['draw'] + 1;
    $data['recordsTotal'] = $total_result;
    $data['recordsFiltered'] = $total_result;
    $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="user_input_flow_campaign.id");

    echo json_encode($data);
  }

  public function ajax_delete_flow_campaign()
  {
    $this->ajax_check();
    $this->csrf_token_check();
    $return = array();
    $primary_key = trim($this->input->post("table_id",true));  
    $media_type = $this->input->post("media_type",true);

    $this->db->trans_start();

    $this->basic->delete_data('user_input_flow_questions_answer',['flow_campaign_id'=>$primary_key]);     
    $this->basic->delete_data('user_input_flow_questions',['flow_campaign_id'=>$primary_key,'user_id'=>$this->user_id]);
    $this->basic->delete_data('user_input_flow_campaign',['id'=>$primary_key,'user_id'=>$this->user_id,'media_type'=>$media_type]);
    $this->_delete_usage_log($module_id=292,$request=1);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
    {
        echo json_encode(array("status" => "0", "message" =>$this->lang->line("Deleting campaign was unsuccessful. Database error occured during campaign delete.")));
        exit();
    }
    else
    {
      $return['status'] = 'successfull';
      $return['message'] = $this->lang->line("Flow campaign and all of it's corresponding data has been deleted successfully.");
      echo json_encode($return); 
    }

    
  }

  public function get_submitted_subscribers()
  {
    $this->ajax_check();
    $table_id = $this->input->post('table_id',true);
    $searching = $this->input->post('searching',true);
    $display_columns = array("#", 'image_path', 'first_name', 'last_name', 'subscribe_id', 'answer_time', 'actions');

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
    $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'user_input_flow_questions_answer.answer_time';
    $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
    $order_by=$sort." ".$order;

    $sql = '';
    if($searching != '')
    {
      $sql = "messenger_bot_subscriber.first_name like '%".$searching."%' OR messenger_bot_subscriber.last_name like '%".$searching."%' OR messenger_bot_subscriber.subscribe_id like '%".$searching."%'";
      $this->db->where($sql);
    }
    $where = array(
      'where' => array('flow_campaign_id'=>$table_id)
    );
    $join = array('messenger_bot_subscriber'=>'messenger_bot_subscriber.subscribe_id=user_input_flow_questions_answer.subscriber_id,left');
    $select = array('messenger_bot_subscriber.id','messenger_bot_subscriber.first_name','messenger_bot_subscriber.last_name','messenger_bot_subscriber.full_name','messenger_bot_subscriber.profile_pic','messenger_bot_subscriber.subscribe_id','messenger_bot_subscriber.image_path','messenger_bot_subscriber.page_table_id','user_input_flow_questions_answer.answer_time');
    $info = $this->basic->get_data('user_input_flow_questions_answer',$where,$select,$join,$limit,$start,$order_by,'messenger_bot_subscriber.subscribe_id');

    if($sql != '') $this->db->where($sql);
    $total_rows_array=$this->basic->count_row('user_input_flow_questions_answer',$where,"user_input_flow_questions_answer.id",$join,$group_by='messenger_bot_subscriber.subscribe_id');
    $total_result=$total_rows_array[0]['total_rows'];


    $base_url=base_url();
    foreach ($info as $key => $value) 
    {
      $profile_pic = ($value['profile_pic']!="") ? "<img class='rounded-circle' style='height:40px;width:40px;' src='".$value["profile_pic"]."'>" :  "<img class='rounded-circle' style='height:40px;width:40px;' src='".base_url('assets/images/50x50.png')."'>";
      $info[$key]['image_path']=($value["image_path"]!="") ? "<a  target='_BLANK' href='".base_url($value["image_path"])."'><img class='rounded-circle' style='height:40px;width:40px;' src='".base_url($value["image_path"])."'></a>" : $profile_pic;

      $info[$key]['actions']='<a href="#" class="btn btn-circle btn-outline-info get_subscriber_formdata" data-id="'.$value['id'].'" subscribe_id="'.$value['subscribe_id'].'" page_table_id="'.$value['page_table_id'].'" data-form-id="'.$table_id.'" title="'.$this->lang->line('View Form Data').'" ><i class="fas fa-eye"></i></a>&nbsp;<a target="_BLANK" href="'.base_url('subscriber_manager/bot_subscribers/').$value['subscribe_id'].'" class="btn btn-circle btn-outline-warning" title="'.$this->lang->line('Go to subscriber list').'" ><i class="far fa-hand-point-right"></i></a>';
      $info[$key]['answer_time'] = date('Y-m-d H:i:s', strtotime($info[$key]['answer_time']));
    }


    $data['draw'] = (int)$_POST['draw'] + 1;
    $data['recordsTotal'] = $total_result;
    $data['recordsFiltered'] = $total_result;
    $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

    echo json_encode($data);

  }

  public function get_subscriber_formdata()
  {
    $this->ajax_check();
    $page_table_id = $this->input->post("page_id",true);
    $subscribe_id = $this->input->post("subscribe_id",true);
    $form_id = $this->input->post("form_id",true);

    $page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where'=>['id'=>$page_table_id,'user_id'=>$this->user_id]],['page_id']);
    $fb_page_id = isset($page_info[0]['page_id']) ? $page_info[0]['page_id'] : 0;

    $table = 'user_input_flow_questions_answer';
    $where = ['where'=>['user_input_flow_questions_answer.subscriber_id'=>$subscribe_id,'user_input_flow_questions_answer.page_id'=>$fb_page_id,'user_input_flow_questions_answer.flow_campaign_id'=>$form_id]];
    $select = ['user_input_flow_campaign.flow_name','user_input_flow_questions.question','user_input_flow_questions_answer.user_answer','serial_no'];
    $join = [
              'user_input_flow_campaign'=>'user_input_flow_campaign.id=user_input_flow_questions_answer.flow_campaign_id,left',
              'user_input_flow_questions'=>'user_input_flow_questions.id=user_input_flow_questions_answer.question_id,left'
            ];
    $info = $this->basic->get_data($table,$where,$select,$join,$limit='',$start=NULL,'serial_no asc');

    $flow_name = isset($info[0]['flow_name']) ? $info[0]['flow_name'] : '';

    $content = '<div class="card w-100 no_shadow">
                  <div class="card-body">
                    <div class="section"><div class="section-title">'.$flow_name.'</div></div>
                    
                    <div class="table-responsive">
                      <table class="table table-sm">
                        <thead>
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">'.$this->lang->line("Question").'</th>
                            <th scope="col">'.$this->lang->line("Answer").'</th>
                          </tr>
                        </thead>
                        <tbody>';
      $i = 1;
      foreach($info as $value)
      {
        $answer = $value["user_answer"];
        $substr = substr($value['user_answer'],0,8);
        if($substr == 'https://') 
        {
          $answer = "<a target='_BLANK' href='".$value["user_answer"]."'>".$this->lang->line('Visit Link')."</a>";
        }

        $content .= '<tr>
                      <th scope="row">'.$i.'</th>
                      <td>'.$value["question"].'</td>
                      <td>'.$answer.'</td>
                    </tr>';
        $i++;
      }
                          
      $content .=      '</tbody>
                      </table>
                    </div>
                  </div>
                </div>';

    if(!empty($info))
      echo $content;
    else
      echo '<div class="col-12 card" id="nodata">
                        <div class="card-body">
                          <div class="empty-state">
                            <img class="img-fluid" style="height: 200px" src="'.base_url('assets/img/drawkit/drawkit-nature-man-colour.svg').'" alt="image">
                            <h2 class="mt-0">'.$this->lang->line("We could not find any data.").'</h2>
                          </div>
                        </div>
                      </div>';


  }

  public function input_flow_builder($page_id=0,$iframe='0',$media_type="fb")
  {
    $reply_types = [
      'Email' => "far fa-envelope",
      "Phone" => "fas fa-phone",
      "Text" => "fas fa-font",
      "Number" => "fas fa-list-ol",
      "URL" => "fas fa-link",
      "File" => "fas fa-paperclip",
      "Image" => "far fa-image",
      "Video" => "fas fa-video",
      "Date" => "fas fa-calendar-alt",
      "Time" => "fas fa-clock",
      "Datetime" => "fas fa-business-time"
    ];
    $data['reply_types'] = $reply_types;
    $data['media_type'] = $media_type;
    $data['page_id'] = $page_id;
    $data['iframe'] = $iframe;

    $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
    $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name'),$join);

    $page_list = array();
    foreach($page_info as $value)
    {
        $page_list[$value['id']] = $value['page_name']." [".$value['name']."]";
    }
    $data['page_list'] = $page_list;

    if($media_type =="ig") {

      $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1','has_instagram'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name','facebook_rx_fb_page_info.has_instagram','facebook_rx_fb_page_info.insta_username'),$join);

      $page_list = array();
      foreach($page_info as $value)
      {
          $page_list[$value['id']] = $value['insta_username']." [".$value['page_name']."]";
      }
      $data['page_list'] = $page_list;
    }


    $data['body'] = 'flow_builder_add';
    $data['page_title'] = $this->lang->line("Create Facebook User Input Flow");
    if($media_type =="ig") {
      $data['page_title'] = $this->lang->line("Create Instagram User Input Flow");
    }
    $this->_viewcontroller($data); 
  }

  public function ajax_add_question_content()
  {
    $this->ajax_check();
    $question_counter = $this->input->post('question_counter',true);
    $reply_type = $this->input->post('reply_type',true);
    $media_type = $this->input->post('media_type',true);
    $page_table_id = $this->input->post('page_table_id',true);

    $random_variable = time();
    
    $question_category = $this->input->post('question_category',true);
    if($question_category == 'keyboard_input')
      $question_type = 'keyboard input';
    else
    {
      $question_type = 'quick replies';
      $reply_type = 'Text';
    }


    $custom_fileds_content = '';
    $custom_field_list = $this->basic->get_data('user_input_custom_fields',['where'=>['reply_type'=>$reply_type,'user_id'=>$this->user_id,'media_type'=>$media_type]]);
    if(!empty($custom_field_list))
    {
      $custom_fileds_content .= "<option value=''>".$this->lang->line('Please select')."</option>";
      foreach ($custom_field_list as $value) {
        $custom_fileds_content .= "<option value='".$value["id"]."'>".$value["name"]."</option>";
      }
    }
    else
      $custom_fileds_content .= '<option value="">'.$this->lang->line('No custom field found').'</option>';

    $selected_reply_type = '';
    $reply_types = ["Email","Phone","Text","Number","URL","File","Image","Video","Date","Time","Datetime"];
    foreach ($reply_types as $value)
    {
      $selected = '';
      $key = $value;
      if($value == $reply_type) $selected = "selected";
      if($value == 'Date') $value = "Date (YYYY-MM-DD)";
      if($value == 'Time') $value = "Time (HH:MM)";
      $selected_reply_type .= "<option value='".$key."' ".$selected.">".$value."</option>";
    }

    $user_labels = '';
    $user_label_info = $this->basic->get_data('messenger_bot_broadcast_contact_group',['where'=>["unsubscribe"=>"0","invisible"=>"0","user_id"=>$this->user_id,'page_id'=>$page_table_id,'social_media'=>$media_type]]);
    if(!empty($user_label_info))
    {
      $user_labels .= "<option value=''>".$this->lang->line('Please select')."</option>";
      foreach ($user_label_info as $value) {
        $user_labels .= "<option value='".$value["id"]."'>".$value["group_name"]."</option>";
      }
    }
    else
      $user_labels .= '<option value="">'.$this->lang->line('No label found').'</option>';

    $messenger_sequence = '';
    if($this->addon_exist("messenger_bot_enhancers"))
    {
      if($this->session->userdata('user_type') == 'Admin' || count(array_intersect($this->module_access, ['219']))!=0)
      {
        $messenger_sequence_info = $this->basic->get_data('messenger_bot_drip_campaign',['where'=>["campaign_type"=>"messenger","user_id"=>$this->user_id,'page_id'=>$page_table_id,'media_type'=>$media_type]],['id','campaign_name','media_type']);
        if(!empty($messenger_sequence_info))
        {
          $messenger_sequence .= "<option value=''>".$this->lang->line('Please select')."</option>";
          foreach ($messenger_sequence_info as $value) {
            $messenger_sequence .= "<option value='".$value["id"]."'>".$value["campaign_name"]."</option>";
          }
        }
        else
          $messenger_sequence .= '<option value="">'.$this->lang->line('No sequence campaign found').'</option>';
      }
    }


    $email_phone_sequence = '';
    if($this->addon_exist("sms_email_sequence"))
    {
      if($this->session->userdata('user_type') == 'Admin' || count(array_intersect($this->module_access, ['270','271']))!=0)
      {
        $sql = "SELECT `id`, `campaign_name`, `campaign_type`
                FROM `messenger_bot_drip_campaign`
                WHERE (`campaign_type` = 'email' OR `campaign_type` = 'sms') AND `page_id` = ".$page_table_id." AND `user_id` = ".$this->user_id." AND `media_type`= ".$this->db->escape($media_type);
        $email_phone_sequence_info = $this->basic->execute_query($sql);
        if(!empty($email_phone_sequence_info))
        {
          $email_phone_sequence .= "<option value=''>".$this->lang->line('Please select')."</option>";
          foreach ($email_phone_sequence_info as $value) {
            $email_phone_sequence .= "<option value='".$value["id"]."'>".$value["campaign_name"]." [".$value['campaign_type']."]</option>";
          }
        }
        else
          $email_phone_sequence .= '<option value="">'.$this->lang->line('No sequence campaign found').'</option>';
      }
    }

    if($reply_type == 'Email') $display_class = '';
    else $display_class = 'd-none';

    $hide_quick_reply_checkbox ='';
    if($media_type == "ig") $hide_quick_reply_checkbox = "hidden";

    if($reply_type == 'Phone') $phone_checkbox_display_class = '';
    else $phone_checkbox_display_class = 'd-none';

    $response = '<div class="ui-sortable-handle single_question_container">
      <input type="hidden" name="question_type['.$random_variable.']" id="question_type['.$random_variable.']" value="'.$question_type.'" />
      <div class="single d-flex mb-5" id="block_'.$random_variable.'">';

    if($question_category == 'keyboard_input')
    {
      $response .= '<div class="input_section" style="width:100%" id="input_section_'.$random_variable.'">
                      <div class="form-group">
                        <div class="input-group mb-2">
                          <input type="text" class="form-control type_questions" name="question['.$random_variable.']" placeholder="'.$this->lang->line('Put Your Question Here').'">
                          <div class="input-group-append append_icon pointer" id="'.$random_variable.'">
                            <div class="input-group-text" id="append_icon_body_'.$random_variable.'"><i class="fas fa-cogs"></i></div>
                          </div>
                        </div>
                        <div class="float-right free_input_label">'.$this->lang->line('Free keyboard input').'</div>
                      </div>
                      <br>
                      <div class="waiting_reply_content">
                        <span>..... '.$this->lang->line('Waiting for a reply from the user').' ....</span>
                      </div>
                    </div>';
    }
    else
    {
      $response .= '<div class="input_section" style="width:100%" id="input_section_'.$random_variable.'">
                      <div class="form-group mb-2">
                        <div class="input-group mb-2">
                          <input type="text" class="form-control type_questions" name="question['.$random_variable.']" placeholder="'.$this->lang->line('Put Your Question Here').'">
                          <div class="input-group-append append_icon pointer" id="'.$random_variable.'">
                            <div class="input-group-text" id="append_icon_body_'.$random_variable.'"><i class="fas fa-cogs"></i></div>
                          </div>
                        </div>
                        <div class="form-inline multiple_input_more_parent">
                          <div class="multiple_input_item" id="multiple_choice_buttons_'.$random_variable.'">
                            <input type="text" class="form-control mb-2 multiple_input_more" name="multiple_choice['.$random_variable.'][]" id="multiple_choice['.$random_variable.'][]" placeholder="'.$this->lang->line("Option 1").'">
                            <input type="text" class="form-control mb-2 multiple_input_more" name="multiple_choice['.$random_variable.'][]" id="multiple_choice['.$random_variable.'][]" placeholder="'.$this->lang->line("Option 2").'">
                            <input type="text" class="form-control mb-2 multiple_input_more" name="multiple_choice['.$random_variable.'][]" id="multiple_choice['.$random_variable.'][]" placeholder="'.$this->lang->line("Option 3").'">
                            <input type="text" class="form-control mb-2 multiple_input_more" name="multiple_choice['.$random_variable.'][]" id="multiple_choice['.$random_variable.'][]" placeholder="'.$this->lang->line("Option 4").'">
                          </div>
                        </div>
                      </div>
                      <div class="form-group mr-2">
                          <button type="" class="btn btn-sm btn-outline-primary float-right add_more_button" div_id="multiple_choice_buttons_'.$random_variable.'"><i class="fas fa-plus-circle"></i> '.$this->lang->line('Add more').'</button>
                      </div><br>
                      <div class="waiting_reply_content">
                        <span>..... '.$this->lang->line('Waiting for a reply from the user').' ....</span>
                      </div>
                    </div>';
    }
        
    $response .= '<div class="edit_input_section" style="width:70%;display:none;" id="edit_input_section_'.$random_variable.'">
          <div class="row">
            <div class="col-12">
              <div class="card edit_input_parent_card">
                <div class="card-body p-3">
                  <div class="form-group mb-1" id="selected_reply_type_'.$random_variable.'">
                    <label>'.$this->lang->line('Reply Type').'</label>
                    <select name="reply_type['.$random_variable.'][]" id="reply_type_'.$random_variable.'" class="form-control selected_reply_type select2" div_id="selected_reply_type_'.$random_variable.'" checkbox_div_id="email_quickreply_checkbox_'.$random_variable.'" phone_checkbox_div_id="phone_quickreply_checkbox_'.$random_variable.'" style="width:100%;">
                      '.$selected_reply_type.'
                    </select>
                  </div>

                  <div id="email_quickreply_checkbox_'.$random_variable.'" class="'.$display_class.' mb-1 '.$hide_quick_reply_checkbox.'">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" value="yes" id="quickreply_checkbox['.$random_variable.'][]" name="quickreply_checkbox['.$random_variable.'][]" class="custom-control-input">
                        <label class="custom-control-label" for="quickreply_checkbox['.$random_variable.'][]">'.$this->lang->line("Attach Email Quick-reply").'</label>
                    </div>
                  </div>

                  <div id="phone_quickreply_checkbox_'.$random_variable.'" class="'.$phone_checkbox_display_class.' mb-1 '.$hide_quick_reply_checkbox.'">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" value="yes" id="phone_quickreply_checkbox['.$random_variable.'][]" name="phone_quickreply_checkbox['.$random_variable.'][]" class="custom-control-input">
                        <label class="custom-control-label" for="phone_quickreply_checkbox['.$random_variable.'][]">'.$this->lang->line("Attach Phone Quick-reply").'</label>
                    </div>
                  </div>

                  <div class="form-group mb-1" div_id="selected_custom_field_'.$random_variable.'">
                    <label>'.$this->lang->line('Save to Custom Field').'</label>
                    <select name="custom_field['.$random_variable.'][]" id="selected_custom_field_'.$random_variable.'" reply_type_id="reply_type_'.$random_variable.'" class="form-control selected_custom_field select2" style="width:100%;">
                      '.$custom_fileds_content.'
                    </select>
                  </div>


                  <div class="form-group mb-1" id="selected_system_field_'.$random_variable.'">
                    <label>'.$this->lang->line('Save to System Field').'</label>
                    <select name="system_field['.$random_variable.'][]" div_id="selected_system_field_'.$random_variable.'" class="form-control selected_system_field select2" style="width:100%;">
                      <option value="">'.$this->lang->line("Please select").'</option>
                      <option value="email">'.$this->lang->line("Email").'</option>
                      <option value="phone">'.$this->lang->line("Phone").'</option>
                      <option value="birthday">'.$this->lang->line("Birthday").'</option>
                      <option value="location">'.$this->lang->line("Location").'</option>
                    </select>
                  </div>

                  <div class="form-group mb-1" id="assign_to_labels_'.$random_variable.'">
                    <label>'.$this->lang->line('Assign to labels').'</label>
                    <select multiple class="form-control assign_to_labels select2" name="label_assigned['.$random_variable.'][]" div_id="assign_to_labels_'.$random_variable.'" style="width:100%;">
                        '.$user_labels.'
                    </select>
                  </div>';

    if($this->addon_exist("messenger_bot_enhancers"))
    {
      if($this->session->userdata('user_type') == 'Admin' || count(array_intersect($this->module_access, ['219']))!=0)
      {
        $response .= '<div class="form-group mb-1" id="assign_to_messenger_sequence_'.$random_variable.'">
          <label>'.$this->lang->line('Assign to a Messenger Sequence').'</label>
          <select class="form-control assign_to_messenger_sequence select2" name="messenger_sequence_assigned['.$random_variable.'][]" div_id="assign_to_messenger_sequence_'.$random_variable.'" style="width:100%;">
            '.$messenger_sequence.'
          </select>
        </div>';
      }
    }

    if($this->addon_exist("sms_email_sequence"))
    {
      if($this->session->userdata('user_type') == 'Admin' || count(array_intersect($this->module_access, ['270','271']))!=0)
      {
        $response .= '<div class="form-group mb-1" id="assign_to_email_phone_sequence_'.$random_variable.'">
          <label>'.$this->lang->line('Assign to a Email/Phone Sequence').'</label>
          <select class="form-control assign_to_email_phone_sequence select2" name="email_phone_sequence_assigned['.$random_variable.'][]" div_id="assign_to_email_phone_sequence_'.$random_variable.'" style="width:100%;">
            '.$email_phone_sequence.'
          </select>
        </div>';

      }
    }


    $response .= '<div class="form-group mb-1" id="skip_button_field_'.$random_variable.'">
                    <label>'.$this->lang->line('Skip button text').'</label>
                    <input type="text" class="form-control" name="skip_button_text['.$random_variable.']" placeholder="'.$this->lang->line('Put your skip button text here').'">
                  </div>

                </div>

                <div class="card-footer text-center pt-0">
                  <a href="#" class="btn btn-icon btn-sm icon-left btn-danger delete_single_block" single_block_div_id="block_'.$random_variable.'"><i class="fas fa-times"></i> '.$this->lang->line('Remove This Question').'</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      </div> <!-- end of single question container -->
    ';

    $response .= '<script>
      $(".select2").select2();
      $(".selected_custom_field").select2({
          tags: true
      });
    </script>';
    $return_value = [];
    $return_value['content'] = $response;
    echo json_encode($return_value);
  }

  public function ajax_add_question_content_only_options()
  {
      // $this->ajax_check();

      $reply_type = $this->input->post('reply_type',true);
      $media_type = $this->input->post('media_type',true);
      $page_table_id = $this->input->post('page_table_id',true);
  
      $question_category = $this->input->post('question_category',true);
      if($question_category == 'keyboard_input') {
          $question_type = 'keyboard input';
      } else {
          $question_type = 'quick replies';
          $reply_type = 'Text';
      }

      $custom_fileds_content = '';
      $custom_field_list = $this->basic->get_data('user_input_custom_fields',['where'=>['reply_type'=>$reply_type,'user_id'=>$this->user_id,'media_type'=>$media_type]]);
      if(!empty($custom_field_list)) {
          $custom_fileds_content .= "<option value=''>".$this->lang->line('Please select')."</option>";
          foreach ($custom_field_list as $value) {
              $custom_fileds_content .= "<option value='".$value["id"]."'>".$value["name"]."</option>";
          }
      } else {
          $custom_fileds_content .= '<option value="">'.$this->lang->line('No custom field found').'</option>';
      }
  
      // $selected_reply_type = '';
      // $reply_types = ["Email","Phone","Text","Number","URL","File","Image","Video","Date","Time","Datetime"];
      // foreach ($reply_types as $value) {
      //     $selected = '';
      //     $key = $value;
      //     if($value == $reply_type) $selected = "selected";
      //     if($value == 'Date') $value = "Date (YYYY-MM-DD)";
      //     if($value == 'Time') $value = "Time (HH:MM)";
      //     $selected_reply_type .= "<option value='".$key."' ".$selected.">".$value."</option>";
      // }

      $user_labels = '';
      $user_label_info = $this->basic->get_data('messenger_bot_broadcast_contact_group',['where'=>["unsubscribe"=>"0","invisible"=>"0","user_id"=>$this->user_id,'page_id'=>$page_table_id,'social_media'=>$media_type]]);
      if(!empty($user_label_info)) {
          // $user_labels .= "<option value=''>".$this->lang->line('Please select')."</option>";
          foreach ($user_label_info as $value) {
              $user_labels .= "<option value='".$value["id"]."'>".$value["group_name"]."</option>";
          }
      } else {
          $user_labels .= '<option value="">'.$this->lang->line('No label found').'</option>';
      }

      $messenger_sequence = '';
      if($this->addon_exist("messenger_bot_enhancers")) {
          if($this->session->userdata('user_type') == 'Admin' 
              || (is_array($this->module_access) 
                  && count(array_intersect($this->module_access, ['219']))!=0)
          ) {
              $messenger_sequence_info = $this->basic->get_data('messenger_bot_drip_campaign',['where'=>["campaign_type"=>"messenger","user_id"=>$this->user_id,'page_id'=>$page_table_id,'media_type'=>$media_type]],['id','campaign_name','media_type']);
              if(!empty($messenger_sequence_info)) {
                  $messenger_sequence .= "<option value=''>".$this->lang->line('Please select')."</option>";
                  foreach ($messenger_sequence_info as $value) {
                      $messenger_sequence .= "<option value='".$value["id"]."'>".$value["campaign_name"]."</option>";
                  }
              } else {
                  $messenger_sequence .= '<option value="">'.$this->lang->line('No sequence campaign found').'</option>';
              }
          }
      }

      $email_phone_sequence = '';
      if($this->addon_exist("sms_email_sequence")) {
          if($this->session->userdata('user_type') == 'Admin' 
              || (is_array($this->module_access) 
                  && count(array_intersect($this->module_access, ['270','271']))!=0)
          ) {
              $sql = "SELECT `id`, `campaign_name`, `campaign_type`
                      FROM `messenger_bot_drip_campaign`
                      WHERE (`campaign_type` = 'email' OR `campaign_type` = 'sms') AND `page_id` = ".$page_table_id." AND `user_id` = ".$this->user_id." AND `media_type`= ".$this->db->escape($media_type);
              $email_phone_sequence_info = $this->basic->execute_query($sql);

              if(!empty($email_phone_sequence_info)) {
                  $email_phone_sequence .= "<option value=''>".$this->lang->line('Please select')."</option>";
                  foreach ($email_phone_sequence_info as $value) {
                      $email_phone_sequence .= "<option value='".$value["id"]."'>".$value["campaign_name"]." [".$value['campaign_type']."]</option>";
                  }
              } else {
                  $email_phone_sequence .= '<option value="">'.$this->lang->line('No sequence campaign found').'</option>';
              }
          }
      }

      $return_value = [
          'user_labels' => $user_labels,
          'custom_fileds_content' => $custom_fileds_content,
          'messenger_sequence' => $messenger_sequence,
          'email_phone_sequence' => $email_phone_sequence,
      ];

      echo json_encode($return_value);
  } 

  public function question_submit()
  {
    $this->ajax_check();

    //************************************************//
    $status=$this->_check_usage($module_id=292,$request=1);
    if($status=="2") 
    {
        $error_msg = $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
        $return_val=array("status"=>"0","message"=>$error_msg);
        echo json_encode($return_val);
        exit();
    }
    else if($status=="3") 
    {
        $error_msg = $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
        $return_val=array("status"=>"0","message"=>$error_msg);
        echo json_encode($return_val);
        exit();
    }
    //************************************************//
    
    $campaign_name = $this->input->post('Campaign_name',true);
    $campaign_name = $this->security->xss_clean($campaign_name);
    $page_table_id = $this->input->post('page_table_id',true);
    $postback_id = $this->input->post('postback_id',true);
    $media_type = $this->input->post('media_type',true);

    $insert_data = [
      'user_id' => $this->user_id,
      'flow_name' => $campaign_name,
      'page_table_id' => $page_table_id,
      'postback_id' => $postback_id,
      'media_type' => $media_type,
    ];

    $this->db->trans_start();

    $this->basic->insert_data('user_input_flow_campaign',$insert_data);
    $flow_campaign_id = $this->db->insert_id();

    $i = 0;
    foreach ($_POST['question'] as $key => $value) {
      if($this->security->xss_clean($value) == '') continue;
      $i++;
      $insert_data = [];
      $insert_data['serial_no'] = $i;
      $insert_data['user_id'] = $this->user_id;
      $insert_data['flow_campaign_id'] = $flow_campaign_id;
      $insert_data['question'] = $this->security->xss_clean($value);

      if(isset($_POST['reply_type'][$key]) && count($_POST['reply_type'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['reply_type'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['reply_type'] = trim($single_item,',');

      }
      else
      {
        $insert_data['reply_type'] = (isset($_POST['reply_type'][$key][0]) && $_POST['reply_type'][$key][0]!='') ? $this->security->xss_clean($_POST['reply_type'][$key][0]) : '';
      } // end of if


      if(isset($_POST['quickreply_checkbox'][$key]) && count($_POST['quickreply_checkbox'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['quickreply_checkbox'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['quick_reply_email'] = trim($single_item,',');

      }
      else
      {
        $insert_data['quick_reply_email'] = (isset($_POST['quickreply_checkbox'][$key][0]) && $_POST['quickreply_checkbox'][$key][0]!='') ? $this->security->xss_clean($_POST['quickreply_checkbox'][$key][0]) : '';
      } // end of if


      if(isset($_POST['phone_quickreply_checkbox'][$key]) && count($_POST['phone_quickreply_checkbox'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['phone_quickreply_checkbox'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['quick_reply_phone'] = trim($single_item,',');

      }
      else
      {
        $insert_data['quick_reply_phone'] = (isset($_POST['phone_quickreply_checkbox'][$key][0]) && $_POST['phone_quickreply_checkbox'][$key][0]!='') ? $this->security->xss_clean($_POST['phone_quickreply_checkbox'][$key][0]) : '';
      } // end of if


      if(isset($_POST['label_assigned'][$key]) && count($_POST['label_assigned'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['label_assigned'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['label_ids'] = trim($single_item,',');

      }
      else
      {
        $insert_data['label_ids'] = (isset($_POST['label_assigned'][$key][0]) && $_POST['label_assigned'][$key][0]!='') ? $this->security->xss_clean($_POST['label_assigned'][$key][0]) : '';
      } // end of if


      if(isset($_POST['custom_field'][$key]) && count($_POST['custom_field'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['custom_field'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['custom_field_id'] = trim($single_item,',');

      }
      else
      {
        $insert_data['custom_field_id'] = (isset($_POST['custom_field'][$key][0]) && $_POST['custom_field'][$key][0]!='') ? $this->security->xss_clean($_POST['custom_field'][$key][0]) : 0;
      } // end of if


      if(isset($_POST['system_field'][$key]) && count($_POST['system_field'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['system_field'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['system_field'] = trim($single_item,',');

      }
      else
      {
        $insert_data['system_field'] = (isset($_POST['system_field'][$key][0]) && $_POST['system_field'][$key][0]!='') ? $this->security->xss_clean($_POST['system_field'][$key][0]) : '';
      } // end of if


      if(isset($_POST['messenger_sequence_assigned'][$key]) && count($_POST['messenger_sequence_assigned'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['messenger_sequence_assigned'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['messenger_sequence_id'] = trim($single_item,',');

      }
      else
      {
        $insert_data['messenger_sequence_id'] = (isset($_POST['messenger_sequence_assigned'][$key][0]) && $_POST['messenger_sequence_assigned'][$key][0]!='') ? $this->security->xss_clean($_POST['messenger_sequence_assigned'][$key][0]) : 0;
      } // end of if


      if(isset($_POST['email_phone_sequence_assigned'][$key]) && count($_POST['email_phone_sequence_assigned'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['email_phone_sequence_assigned'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['email_phone_sequence_id'] = trim($single_item,',');

      }
      else
      {
        $insert_data['email_phone_sequence_id'] = (isset($_POST['email_phone_sequence_assigned'][$key][0]) && $_POST['email_phone_sequence_assigned'][$key][0]!='') ? $this->security->xss_clean($_POST['email_phone_sequence_assigned'][$key][0]) : 0;
      } // end of if


      if(isset($_POST['multiple_choice'][$key]) && count($_POST['multiple_choice'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['multiple_choice'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['multiple_choice_options'] = trim($single_item,',');

      }
      else
      {
        $insert_data['multiple_choice_options'] = (isset($_POST['multiple_choice'][$key][0]) && $_POST['multiple_choice'][$key][0]!='') ? $this->security->xss_clean($_POST['multiple_choice'][$key][0]) : '';
      } // end of if


      
      $insert_data['skip_button_text'] = (isset($_POST['skip_button_text'][$key]) && $_POST['skip_button_text'][$key]!='') ? $this->security->xss_clean($_POST['skip_button_text'][$key]) : '';
      $insert_data['type'] = (isset($_POST['question_type'][$key]) && $_POST['question_type'][$key]!='') ? $this->security->xss_clean($_POST['question_type'][$key]) : '';

      $this->basic->insert_data('user_input_flow_questions',$insert_data);
      

    } //end of first foreach

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
    {
      echo json_encode(array("status" => "0", "message" =>$this->lang->line("Creating campaign was unsuccessful. Database error occured during campaign create.")));
        exit();
    }
    else
    {
      $this->_insert_usage_log($module_id=292,$request=1);
      echo json_encode(array("status" => "1", "message" =>$this->lang->line("Campaign has been created successfully.")));
    }


  }

  public function get_customfield_on_replytype()
  {
    $this->ajax_check();
    $reply_type = $this->input->post('selected_reply_type',true);
    $custom_fileds_content = '';
    $custom_field_list = $this->basic->get_data('user_input_custom_fields',['where'=>['reply_type'=>$reply_type,'user_id'=>$this->user_id]]);
    if(!empty($custom_field_list))
    {
      foreach ($custom_field_list as $value) {
        $custom_fileds_content .= "<option value='".$value["id"]."'>".$value["name"]."</option>";
      }
    }
    else
      $custom_fileds_content .= '<option value="">'.$this->lang->line('No custom field found').'</option>';
    $response = ['content'=>$custom_fileds_content];
    echo json_encode($response);
  }

  public function get_postback_dropdown()
  {
    $this->ajax_check();
    $page_table_id=$this->input->post('page_table_id');// database id
    $media_type=$this->input->post('media_type');

    $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array('user_id'=>$this->user_id,"page_id"=>$page_table_id,"is_template"=>"1",'template_for'=>'reply_message','media_type'=>$media_type)),array('postback_id','bot_name','template_name','media_type'));
    $push_postback = "<option value=''>".$this->lang->line("Select a postback")."</option>";
    foreach ($postback_data as $key => $value) 
    {
      $push_postback .= "<option value='".$value['postback_id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
    }
    $response = ['content'=>$push_postback];
    echo json_encode($response);
  }


  public function edit_question_content($id=0,$iframe='0',$media_type="fb")
  {
    $join = ['user_input_flow_questions'=>'user_input_flow_campaign.id=user_input_flow_questions.flow_campaign_id,right'];
    $where = ['where'=>['user_input_flow_campaign.id'=>$id,'user_input_flow_campaign.user_id'=>$this->user_id,"user_input_flow_campaign.media_type"=>$media_type]];
    $select = ['user_input_flow_questions.*','flow_name','page_table_id','postback_id','user_input_flow_questions.id as q_table_id','serial_no','media_type'];
    $question_info = $this->basic->get_data('user_input_flow_campaign',$where,$select,$join,$limit='',$start=NULL,'serial_no asc');
    $data['question_info'] = $question_info;
    $data['media_type'] = $media_type;

    $data['flow_campaign_id'] = isset($question_info[0]['flow_campaign_id']) ? $question_info[0]['flow_campaign_id'] : 0;

    $selected_page_id = isset($question_info[0]['page_table_id']) ? $question_info[0]['page_table_id'] : 0;
    $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array('user_id'=>$this->user_id,"page_id"=>$selected_page_id,"is_template"=>"1",'template_for'=>'reply_message','media_type'=>$media_type)),array('postback_id','bot_name','template_name','media_type'));
    $push_postback = "<option value=''>".$this->lang->line("Select a postback")."</option>";
    foreach ($postback_data as $key => $value) 
    {
      $selected = '';
      $selected_postback_id = isset($question_info[0]['postback_id']) ? $question_info[0]['postback_id'] : 0;
      if($value['postback_id']==$selected_postback_id) $selected = 'selected';
      $push_postback .= "<option value='".$value['postback_id']."' ".$selected." >".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
    }
    $data['postbacks'] = $push_postback;
    
    $reply_types = [
      'Email' => "far fa-envelope",
      "Phone" => "fas fa-phone",
      "Text" => "fas fa-font",
      "Number" => "fas fa-list-ol",
      "URL" => "fas fa-link",
      "File" => "fas fa-paperclip",
      "Image" => "far fa-image",
      "Video" => "fas fa-video",
      "Date" => "fas fa-calendar-alt",
      "Time" => "fas fa-clock",
      "Datetime" => "fas fa-business-time"
    ];
    $data['reply_types'] = $reply_types;

    $reply_types_array = ["Email","Phone","Text","Number","URL","File","Image","Video","Date","Time","Datetime"];
    $data['reply_types_array'] = $reply_types_array;

    $custom_field_info = $this->basic->get_data('user_input_custom_fields',['where'=>['user_id'=>$this->user_id,'media_type'=>$media_type]]);
    $custom_fields = [];
    foreach ($custom_field_info as $value) {
      $custom_fields[$value['reply_type']][$value['id']] = $value['name'];
    }
    $data['custom_fields'] = $custom_fields;

    $system_fields_array = ["email","phone","birthday","location"];
    $data['system_fields_array'] = $system_fields_array;

    $user_label_info = $this->basic->get_data('messenger_bot_broadcast_contact_group',['where'=>["unsubscribe"=>"0","invisible"=>"0","user_id"=>$this->user_id,'page_id'=>$selected_page_id,'social_media'=>$media_type]]);
    $data['user_label_info'] = $user_label_info;

    $messenger_sequence_info = [];
    $messenger_sequence_exist = 'no';
    if($this->addon_exist("messenger_bot_enhancers"))
    {
      if($this->session->userdata('user_type') == 'Admin' || count(array_intersect($this->module_access, ['219']))!=0)
      {
        $messenger_sequence_info = $this->basic->get_data('messenger_bot_drip_campaign',['where'=>["campaign_type"=>"messenger","user_id"=>$this->user_id,'page_id'=>$selected_page_id,'media_type'=>$media_type]],['id','campaign_name']);
        $messenger_sequence_exist = 'yes';
      }
    }
    $data['messenger_sequence_info'] = $messenger_sequence_info;
    $data['messenger_sequence_exist'] = $messenger_sequence_exist;

    $email_phone_sequence_info = [];
    $sms_email_sequence_exist = 'no';
    if($this->addon_exist("sms_email_sequence"))
    {
      if($this->session->userdata('user_type') == 'Admin' || count(array_intersect($this->module_access, ['270','271']))!=0)
      {
        $sql = "SELECT `id`, `campaign_name`, `campaign_type`
                FROM `messenger_bot_drip_campaign`
                WHERE (`campaign_type` = 'email' OR `campaign_type` = 'sms') AND `page_id` = ".$selected_page_id." AND `user_id` = ".$this->user_id." AND `media_type`=".$this->db->escape($media_type); 
        $email_phone_sequence_info = $this->basic->execute_query($sql);
        $sms_email_sequence_exist = 'yes';
      }
    }
    $data['email_phone_sequence_info'] = $email_phone_sequence_info;
    $data['sms_email_sequence_exist'] = $sms_email_sequence_exist;

    $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
    $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name'),$join);
    $page_list = array();
    foreach($page_info as $value)
    {
        $page_list[$value['id']] = $value['page_name']." [".$value['name']."]";
    }
    $data['page_list'] = $page_list;

    if($media_type == "ig") {
      $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1','has_instagram'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name','facebook_rx_fb_page_info.insta_username'),$join);
      $page_list = array();
      foreach($page_info as $value)
      {
          $page_list[$value['id']] = $value['insta_username']." [".$value['page_name']."]";
      }
      $data['page_list'] = $page_list;
    }

    $data['body'] = 'flow_builder_edit';
    $data['iframe'] = $iframe;
    $data['page_title'] = $this->lang->line("Edit User Input Flow");

    $this->_viewcontroller($data); 
  }

  public function edit_question_submit()
  {
    $this->ajax_check();
    
    $campaign_name = $this->input->post('Campaign_name',true);
    $campaign_name = $this->security->xss_clean($campaign_name);
    $page_table_id = $this->input->post('page_table_id',true);
    $postback_id = $this->input->post('postback_id',true);
    $flow_campaign_id = $this->input->post('flow_campaign_id',true);
    $media_type = $this->input->post('media_type',true);

    $update_data = [
      'flow_name' => $campaign_name,
      'page_table_id' => $page_table_id,
      'postback_id' => $postback_id,
      'media_type' => $media_type,
    ];

    $this->db->trans_start();

    $this->basic->update_data('user_input_flow_campaign',['id'=>$flow_campaign_id,'user_id'=>$this->user_id],$update_data);

    $last_question_ids = [];
    $last_question_ids_info = $this->basic->get_data('user_input_flow_questions',['where'=>['flow_campaign_id'=>$flow_campaign_id,'user_id'=>$this->user_id]],['id']);
    foreach($last_question_ids_info as $value)
    {
      array_push($last_question_ids, $value['id']);
    }
    $new_question_ids = [];


    $i = 1;

    foreach ($_POST['question'] as $key => $value) 
    {
      if($this->security->xss_clean($value) == '') continue;

      $insert_data = [];
      $insert_data['user_id'] = $this->user_id;
      $insert_data['flow_campaign_id'] = $flow_campaign_id;
      $insert_data['question'] = $this->security->xss_clean($value);

      if(isset($_POST['reply_type'][$key]) && count($_POST['reply_type'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['reply_type'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['reply_type'] = trim($single_item,',');

      }
      else
      {
        $insert_data['reply_type'] = (isset($_POST['reply_type'][$key][0]) && $_POST['reply_type'][$key][0]!='') ? $this->security->xss_clean($_POST['reply_type'][$key][0]) : '';
      } // end of if


      if(isset($_POST['quickreply_checkbox'][$key]) && count($_POST['quickreply_checkbox'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['quickreply_checkbox'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['quick_reply_email'] = trim($single_item,',');

      }
      else
      {
        $insert_data['quick_reply_email'] = (isset($_POST['quickreply_checkbox'][$key][0]) && $_POST['quickreply_checkbox'][$key][0]!='') ? $this->security->xss_clean($_POST['quickreply_checkbox'][$key][0]) : '';
      } // end of if


      if(isset($_POST['phone_quickreply_checkbox'][$key]) && count($_POST['phone_quickreply_checkbox'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['phone_quickreply_checkbox'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['quick_reply_phone'] = trim($single_item,',');

      }
      else
      {
        $insert_data['quick_reply_phone'] = (isset($_POST['phone_quickreply_checkbox'][$key][0]) && $_POST['phone_quickreply_checkbox'][$key][0]!='') ? $this->security->xss_clean($_POST['phone_quickreply_checkbox'][$key][0]) : '';
      } // end of if


      if(isset($_POST['label_assigned'][$key]) && count($_POST['label_assigned'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['label_assigned'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['label_ids'] = trim($single_item,',');

      }
      else
      {
        $insert_data['label_ids'] = (isset($_POST['label_assigned'][$key][0]) && $_POST['label_assigned'][$key][0]!='') ? $this->security->xss_clean($_POST['label_assigned'][$key][0]) : '';
      } // end of if


      if(isset($_POST['custom_field'][$key]) && count($_POST['custom_field'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['custom_field'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['custom_field_id'] = trim($single_item,',');

      }
      else
      {
        $insert_data['custom_field_id'] = (isset($_POST['custom_field'][$key][0]) && $_POST['custom_field'][$key][0]!='') ? $this->security->xss_clean($_POST['custom_field'][$key][0]) : 0;
      } // end of if


      if(isset($_POST['system_field'][$key]) && count($_POST['system_field'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['system_field'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['system_field'] = trim($single_item,',');

      }
      else
      {
        $insert_data['system_field'] = (isset($_POST['system_field'][$key][0]) && $_POST['system_field'][$key][0]!='') ? $this->security->xss_clean($_POST['system_field'][$key][0]) : '';
      } // end of if


      if(isset($_POST['messenger_sequence_assigned'][$key]) && count($_POST['messenger_sequence_assigned'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['messenger_sequence_assigned'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['messenger_sequence_id'] = trim($single_item,',');

      }
      else
      {
        $insert_data['messenger_sequence_id'] = (isset($_POST['messenger_sequence_assigned'][$key][0]) && $_POST['messenger_sequence_assigned'][$key][0]!='') ? $this->security->xss_clean($_POST['messenger_sequence_assigned'][$key][0]) : 0;
      } // end of if


      if(isset($_POST['email_phone_sequence_assigned'][$key]) && count($_POST['email_phone_sequence_assigned'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['email_phone_sequence_assigned'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['email_phone_sequence_id'] = trim($single_item,',');

      }
      else
      {
        $insert_data['email_phone_sequence_id'] = (isset($_POST['email_phone_sequence_assigned'][$key][0]) && $_POST['email_phone_sequence_assigned'][$key][0]!='') ? $this->security->xss_clean($_POST['email_phone_sequence_assigned'][$key][0]) : 0;
      } // end of if


      if(isset($_POST['multiple_choice'][$key]) && count($_POST['multiple_choice'][$key])>1)
      {
        $single_item = '';
        foreach($_POST['multiple_choice'][$key] as $index => $data)
        {
          $single_item .= $this->security->xss_clean($data).',';
        } // end of foreach
        $insert_data['multiple_choice_options'] = trim($single_item,',');

      }
      else
      {
        $insert_data['multiple_choice_options'] = (isset($_POST['multiple_choice'][$key][0]) && $_POST['multiple_choice'][$key][0]!='') ? $this->security->xss_clean($_POST['multiple_choice'][$key][0]) : '';
      } // end of if


      
      $insert_data['skip_button_text'] = (isset($_POST['skip_button_text'][$key]) && $_POST['skip_button_text'][$key]!='') ? $this->security->xss_clean($_POST['skip_button_text'][$key]) : '';

      $insert_data['type'] = (isset($_POST['question_type'][$key]) && $_POST['question_type'][$key]!='') ? $this->security->xss_clean($_POST['question_type'][$key]) : '';

      $question_table_id = (isset($_POST['question_table_id'][$key]) && $_POST['question_table_id'][$key]!='') ? $this->security->xss_clean($_POST['question_table_id'][$key]) : 0;

      $insert_data['serial_no'] = $i;
      if($question_table_id != 0)
      {
        array_push($new_question_ids, $question_table_id);
        $this->basic->update_data('user_input_flow_questions',['id'=>$question_table_id],$insert_data);
      }
      else
      {
        $this->basic->insert_data('user_input_flow_questions',$insert_data);
      }
      
      $i++;
    }
    
    $need_to_delete_ids = array_diff($last_question_ids, $new_question_ids);
    if(!empty($need_to_delete_ids))
    {
      $this->db->where_in('id',$need_to_delete_ids);
      $this->db->delete('user_input_flow_questions'); 
    }

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
    {
        echo json_encode(array("status" => "0", "message" =>$this->lang->line("Updating campaign was unsuccessful. Database error occured during update.")));
        exit();
    }
    else
    {
        echo json_encode(array("status" => "1", "message" =>$this->lang->line("Campaign has been updated successfully.")));
    }


  }

  public function ajax_get_variables()
  {
    $this->ajax_check();
    $media_type = 'fb';
    $media_type = $this->input->post("media_type",true);
    $content = '<div class="section-title">'.$this->lang->line('Variables you currently have').'</div>';
    $custom_fields_array = [];
    $custom_fields_info = $this->basic->get_data('user_input_custom_fields',['where'=>['user_id'=>$this->user_id,"media_type"=>$media_type]]);
    foreach($custom_fields_info as $value)
      $custom_fields_array[$value['reply_type']][] = '#'.$value['name'].'#';
    
    foreach ($custom_fields_array as $key => $value) :
      $content .= '<p>'.$this->lang->line('Reply Type').': <b><i>'.$key.'</i> </b></p>
      <p>
        '.$this->lang->line('Variables').': 
        <b>'.implode(' , ', $value).'</b>
      </p>';
    endforeach;

    echo $content;
  }


  public function export_flow_data() 
  {
    // Fixes out-of-memory issue
    if (ob_get_level()) {
      ob_end_clean();
    }

    // Determines request method
    $method = $this->input->method();

    // Handles POST request
    if ('post' == strtolower($method)) {
      if (! $this->input->is_ajax_request()) {
                $message = $this->lang->line('Bad request.');
                echo json_encode(['msg' => $message]);
                exit;       
      }

      $this->form_validation->set_rules('table_id', 'Flow ID', 'required');

      if (false === $this->form_validation->run()) {
        if ($this->form_validation->error('table_id')) {
          $message = $this->form_validation->error('table_id');
        } else {
          $message = $this->lang->line('Bad request.');
        }

        echo json_encode(['error' => strip_tags($message)]);
        exit;
      }

      // Holds form ID
      $table_id = filter_var($this->input->post('table_id'), FILTER_SANITIZE_STRING);

      $where = [
        'where' => [
          'user_input_flow_questions_answer.flow_campaign_id' => $table_id
        ],
      ];
      $select = ['id'];

      $form = $this->basic->get_data('user_input_flow_questions_answer', $where, $select, [], 1);  

      // Exits displaying error if there is no data to be exported
      if (empty($form)) {
        $message = $this->lang->line('No flow data to be exported.');
        echo json_encode(['info' => $message]);
        exit;
      }

      // Sets form ID into session
      $this->session->set_userdata('inputflow_export_form_data_form_id', $table_id);
      
      // Sends success response
      echo json_encode(['status' => 'ok']);
      exit;

    } elseif ('get' == strtolower($method)) {
      $table_id = $this->session->userdata('inputflow_export_form_data_form_id');

      // Exits from here if we've no form ID in session
      if (! $table_id) {
        $message = $this->lang->line('No flow data to be exported.');
        echo json_encode(['error' => $message]);
        exit;
      }

      $page_table_info = $this->basic->get_data('user_input_flow_campaign',['where'=>['user_input_flow_campaign.id'=>$table_id,'user_input_flow_campaign.user_id'=>$this->user_id]],['page_id'],['facebook_rx_fb_page_info'=>'user_input_flow_campaign.page_table_id=facebook_rx_fb_page_info.id,left']);
      $fb_page_id = isset($page_table_info[0]['page_id']) ? $page_table_info[0]['page_id'] : 0;


      $where = [
        'where' => [
          'user_input_flow_questions_answer.flow_campaign_id' => $table_id,
          'user_input_flow_questions_answer.page_id' => $fb_page_id,
          'messenger_bot_subscriber.page_id' => $fb_page_id,
        ],
      ];

      $join = [
        'messenger_bot_subscriber' => 'messenger_bot_subscriber.subscribe_id = user_input_flow_questions_answer.subscriber_id, left',
      ];

      $select = [
        'user_input_flow_questions_answer.subscriber_id',
        'user_input_flow_questions_answer.user_answer',
        'user_input_flow_questions_answer.question_id',
        'messenger_bot_subscriber.first_name',
        'messenger_bot_subscriber.last_name',
      ];

      $order_by = 'user_input_flow_questions_answer.flow_campaign_id asc,user_input_flow_questions_answer.subscriber_id asc,user_input_flow_questions_answer.answer_time asc';

      $data = $this->basic->get_data('user_input_flow_questions_answer', $where, $select, $join, $limit='', $start=NULL, $order_by);

      $new_data = [];
      foreach($data as $value)
      {
        $new_data[$value['subscriber_id']]['first_name'] = $value['first_name'];
        $new_data[$value['subscriber_id']]['last_name'] = $value['last_name'];
        $new_data[$value['subscriber_id']]['question'][$value['question_id']] = $value['user_answer'];
      }


      // Exits displaying error if there is no data to be exported
      if (! count($data) > 0) {
        $message = $this->lang->line('No flow data to be exported.');
        echo json_encode(['error' => $message]);
        exit;
      }

      // Sets the csv file name
      $filename = 'flowdata_' . time() . '.csv';

      $question_info = $this->basic->get_data('user_input_flow_campaign',['where'=>['user_input_flow_campaign.id'=>$table_id,'user_input_flow_campaign.user_id'=>$this->user_id]],['flow_name','question','user_input_flow_questions.id as question_id'],['user_input_flow_questions'=>'user_input_flow_campaign.id=user_input_flow_questions.flow_campaign_id,left'],'',NULL,'serial_no asc');

      // Prepares headers for csv file
      $csv_headers = [
        'Subscriber ID',
        'First Name',
        'Last Name',
      ];

      $question_table_ids = [];

      foreach($question_info as $value)
      {
        array_push($csv_headers, $value['question']);
        array_push($question_table_ids, $value['question_id']);
      }


      // Writes into output buffer using php output stream
      $fp = fopen('php://output', 'w');

      if ($fp) {
        // Sets headers for making csv file downloadable
              header('Expires: 0');
              header('Pragma: no-cache');
              header('Content-Type: text/csv');
              header('Content-Disposition: attachment; filename="' . $filename . '"');
              
              // Puts headers into csv file
              fputcsv($fp, $csv_headers);

              // Preapares values for csv file
        foreach ($new_data as $key => $values) {
          $csv_values = [];
          $csv_values[] = $key;
          $csv_values[] = $values['first_name'];
          $csv_values[] = $values['last_name'];
          foreach($question_table_ids as $single_question)
          {
            if(isset($values['question'][$single_question]))
              $csv_values[] = $values['question'][$single_question];
            else
              $csv_values[] = '';
          }
          // Puts values into csv file
          fputcsv($fp, $csv_values);
        }
      }

      // Closes the file pointer
      fclose($fp);

      // Unsets form ID from session
            $this->session->unset_userdata('inputflow_export_form_data_form_id');
            exit;
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
    $sql=array(
        1=> "
        CREATE TABLE IF NOT EXISTS `user_input_flow_campaign` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `flow_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `page_table_id` int(11) NOT NULL,
          `postback_id` varchar(255) NOT NULL,
          `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
          `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general',
          `unique_id` varchar(25) NOT NULL,
          `visual_flow_campaign_id` int(11) NOT NULL,
          `deleted` enum('0','1') NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
        2=>"
        CREATE TABLE IF NOT EXISTS `user_input_flow_questions` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `flow_campaign_id` int(11) NOT NULL,
          `serial_no` int(11) NOT NULL,
          `question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `type` enum('keyboard input','quick replies') NOT NULL DEFAULT 'keyboard input',
          `reply_type` varchar(50) NOT NULL,
          `quick_reply_email` varchar(50) NOT NULL,
          `quick_reply_phone` varchar(10) NOT NULL,
          `multiple_choice_options` text NOT NULL,
          `custom_field_id` int(11) NOT NULL,
          `label_ids` varchar(255) NOT NULL,
          `messenger_sequence_id` int(11) NOT NULL,
          `email_phone_sequence_id` int(11) NOT NULL,
          `system_field` varchar(100) NOT NULL,
          `skip_button_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `unique_id` varchar(25) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`,`flow_campaign_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
        3=>"
        CREATE TABLE IF NOT EXISTS `user_input_flow_questions_answer` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `subscriber_id` varchar(50) CHARACTER SET utf8 NOT NULL,
          `page_id` varchar(50) CHARACTER SET utf8 NOT NULL,
          `flow_campaign_id` int(10) NOT NULL,
          `question_id` int(10) NOT NULL,
          `user_answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `fb_message_id` varchar(200) NOT NULL,
          `answer_time` datetime NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `subscriber_id` (`subscriber_id`,`page_id`,`flow_campaign_id`,`question_id`) USING BTREE,
          KEY `subscriber_id_x` (`subscriber_id`),
          KEY `flow_campaign_id` (`flow_campaign_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
        4=>"
        INSERT INTO `email_template_management` (`id`, `title`, `template_type`, `subject`, `message`, `icon`, `tooltip`, `info`) VALUES (NULL, 'New Input Flow Submission Alert', 'input_flow_submission', '#FLOW_NAME# | #SUBSCRIBER_NAME# Has Submitted Input Flow', '#SUBSCRIBER_NAME# has just submitted your Input Flow #FLOW_NAME# with below data. <br/><br/>\r\n#FLOW_DATA#\r\n<br/><br/>\r\nThank you,<br/>\r\n<a href=\"#APP_URL#\">#APP_NAME#</a> Team', 'fab fa-stack-overflow', '#FLOW_NAME#,#SUBSCRIBER_NAME#,#FLOW_DATA#,#APP_URL#,#APP_NAME#', 'Subscriber information received');"
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
    $sql = array(
      1=>"DROP TABLE IF EXISTS `user_input_flow_campaign`;",
      2=>"DROP TABLE IF EXISTS `user_input_flow_questions`;",
      3=>"DROP TABLE IF EXISTS `user_input_flow_questions_answer`;",
      4=>"DELETE FROM `email_template_management` WHERE `email_template_management`.`template_type` = 'input_flow_submission';"
    ); 
    
    // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
    $this->delete_addon($addon_controller_name,$sql);         
  }


}
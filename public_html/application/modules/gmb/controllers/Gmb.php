<?php
/*
Addon Name: XeroBizz - Google My Business Made Easy
Unique Name: gmb
Modules:
{
   "300":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Google My Business: Account Import"
   },
   "301":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"0",
      "extra_text":"",
      "module_name":"Google My Business: Answer To Questions"
   },
   "302":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"0",
      "extra_text":"",
      "module_name":"Google My Business: Reply To Reviews"
   },
   "303":{
      "bulk_limit_enabled":"1",
      "limit_enabled":"1",
      "extra_text":"month",
      "module_name":"Google My Business: Post To Locations"
   },
   "304":{
      "bulk_limit_enabled":"1",
      "limit_enabled":"1",
      "extra_text":"month",
      "module_name":"Google My Business: Media Upload To Locations"
   },
   "305":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"month",
      "module_name":"Google My Business: RSS Auto Posting"
   }
}
Project ID: 55
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 1.0
Description: 
*/

require_once("application/controllers/Home.php"); // loading home controller
require_once APPPATH . '/libraries/Google_My_Business/Exception.php';
require_once APPPATH . '/libraries/Google_My_Business/Service/Exception.php';

class Gmb extends Home
{
    /**
     * An array of allowed mime types
     * @var $allowed_mime_types
     */
    protected $allowed_mime_types = [
        // jpeg or jpg images
        '.jpeg',
        
        '.jpg', 

        // png images
        '.png',

        // gif images
        '.gif',

        // flv videos
        '.flv',

        // ogv or ogg videos
        '.ogg',

        // webm videos
        '.webm',

        // 3gp or mts videos 
        '.3gpp',

        // mp4 videos 
        '.mp4',

        // mkv videos
        '.mkv',

        // mpeg videos
        '.mpeg',

        // mov videos
        '.mov',

        // avi videos
        '.avi', 

        // wmv videos 
        '.wmv',

        // m4v videos
        '.m4v',
    ];

    /**
     * An array of allowed mime types for rss
     * @var $rss_allowed_mime_types
     */
    protected $rss_allowed_mime_types = [
        // jpeg or jpg images
        '.jpeg',
        '.jpg', 

        // png images
        '.png',

        // gif images
        '.gif',
    ];

    /**
     * An array of GMB media categories
     * @var $media_categories
     */
    protected $media_categories = [
        'COVER' => 'COVER',
        'PROFILE' => 'PROFILE',
        'LOGO' => 'LOGO',
        'EXTERIOR' => 'EXTERIOR',
        'INTERIOR' => 'INTERIOR',
        'PRODUCT' => 'PRODUCT',
        'AT_WORK' => 'AT_WORK',
        'FOOD_AND_DRINK' => 'FOOD_AND_DRINK',
        'MENU' => 'MENU',
        'COMMON_AREA' => 'COMMON_AREA',
        'ROOMS' => 'ROOMS',
        'TEAMS' => 'TEAMS',
        'ADDITIONAL' => 'ADDITIONAL',
    ];

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');   
        
        $this->important_feature();
        $this->member_validity();     
    }

    public function index()
    {
      $this->location_list();
    }

    public function location_list()
    {        
        // $this->session->set_userdata('google_mybusiness_user_table_id', null);
        $gmb_account_table_id = $this->session->userdata('google_mybusiness_user_table_id');

        $selected_location_table_id = $this->session->userdata('location_list_location_table_id');
        $location_infos = $this->basic->get_data('google_business_locations',['where'=>['user_account_id'=>$gmb_account_table_id]]);

        if (! count($location_infos)) {
            return redirect('gmb/business_accounts', 'location');
        }

        $location_info = array();
        $i=1;
        foreach($location_infos as $value)
        {
            if($value['id'] == $selected_location_table_id)
                $location_info[0] = $value;
            else
                $location_info[$i] = $value;

          $i++;
        }

        ksort($location_info);

        $data['location_info'] = $location_info;
        $data['body'] = 'gmb';
        $data['page_title'] = $this->lang->line('Location manager');
        $this->_viewcontroller($data);
    }

    public function business_accounts() 
    {
        $data['body'] = 'account_import';
        $data['page_title'] = $this->lang->line('Google Account Import');

        $data["google_login_button"] = '';

        $login_msg = '';
        $login_config = $this->basic->get_data("login_config",array("where"=>array("status"=>"1"),$select='',$join='',$limit=1,$start=NULL,$order_by=rand()));

        if(!empty($login_config))
        {
            try
            {
                $params['redirectUri'] = base_url("social_accounts/import_gmb_account_callback");
                $this->load->library("google_my_business",$params);
                $data["google_login_button"] = $this->google_my_business->login_button();

            } catch (Google_Service_Exception $e) {
                $login_msg = $e->getMessage();
            } catch (Google_Exception $e) {
                $login_msg = $e->getMessage();
            } catch (\Exception $e) {
                $login_msg = $e->getMessage();
            }
        }

        $where['where'] = array('user_id'=>$this->user_id);
        $existing_accounts = $this->basic->get_data('google_user_account',$where);

        $show_import_account_box = 1;
        $data['show_import_account_box'] = 1;

        if(!empty($existing_accounts))
        {
            $i=0;
            foreach($existing_accounts as $value)
            {
                $existing_account_info[$i]['useraccount_table_id'] = $value['id'];
                $existing_account_info[$i]['account_display_name'] = $value['account_display_name'];
                $existing_account_info[$i]['email'] = $value['email'];
                $existing_account_info[$i]['account_id'] = $value['account_id'];
                $existing_account_info[$i]['profile_photo'] = $value['profile_photo'];

                $where = array();
                $where['where'] = array('user_account_id'=>$value['id']);
                $location_list = $this->basic->get_data('google_business_locations',$where);
                $existing_account_info[$i]['location_list'] = $location_list;

                $i++;
            }

            $data['existing_accounts'] = $existing_account_info;
        }
        else
            $data['existing_accounts'] = '0';

        $this->_viewcontroller($data);
    }

    public function get_location_details()
    {
        $this->ajax_check();
        $response = [];
        $location_table_id =  $this->input->post('location_table_id');
        // $response['review_reply_settings_url'] = base_url("gmb/review_replies/$location_table_id");
        $this->session->set_userdata('location_list_location_table_id',$location_table_id);
        $user_id = $this->user_id;
        $select = ['google_business_locations.*'];
        $join = ['google_user_account'=>'google_user_account.id=google_business_locations.user_account_id,left'];
        $where = ['where'=>['google_user_account.user_id'=>$user_id,'google_business_locations.id'=>$location_table_id]];
        $location_info = $this->basic->get_data('google_business_locations',$where,$select,$join);

        if(empty($location_info))
        {
            $middle_column_content = '
                        <div class="card" id="nodata">
                          <div class="card-body">
                            <div class="empty-state">
                              <img class="img-fluid" style="height: 200px" src="'.base_url('assets/img/drawkit/drawkit-nature-man-colour.svg').'" alt="image">
                              <h2 class="mt-0">'.$this->lang->line("We could not find any data.").'</h2>
                            </div>
                          </div>
                        </div>';
        }
        else
        {

            $middle_column_content='
            <div class="card main_card">
              <div class="card-header padding-left-10">
                <h4 class="put_location_name_url"><i class="fas fa-map-pin"></i> <a target="_BLANK" href="'.$location_info[0]['map_url'].'">'.$location_info[0]['location_display_name'].'</a></h4>
              </div>
              <div class="card-body padding-10">

                <div class="row">
                
                  <div class="col-12">
                    <div class="card card-large-icons card-condensed middle_col_item active">
                      <div class="card-icon">
                        <i class="fas fa-cogs"></i>
                      </div>
                      <div class="card-body">
                        <h4>'.$this->lang->line("Review reply settings").'</h4>                    
                        <a href="'.base_url("gmb/review_replies").'" id="review_reply_settings" data-page-id="'.$location_info[0]['id'].'" data-height="500" class="card-cta iframed">'.$this->lang->line("Change Settings").'</a>
                      </div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="card card-large-icons card-condensed middle_col_item">
                      <div class="card-icon">
                        <i class="fas fa-star"></i>
                      </div>
                      <div class="card-body">
                        <h4>'.$this->lang->line("Review list").'</h4>                    
                        <a href="'.base_url("gmb/review_list").'" id="review_list" data-page-id="'.$location_info[0]['id'].'" data-height="500" class="card-cta iframed">'.$this->lang->line("Change Settings").'</a>
                      </div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="card card-large-icons card-condensed middle_col_item">
                      <div class="card-icon">
                        <i class="fas fa-share-square"></i>
                      </div>
                      <div class="card-body">
                        <h4>'.$this->lang->line("Post list").'</h4>                    
                        <a href="'.base_url("gmb/post_list").'" id="get_post_list" data-page-id="'.$location_info[0]['id'].'" data-height="500" class="card-cta iframed">'.$this->lang->line("Change Settings").'</a>
                      </div>
                    </div>
                  </div>';
            if($this->session->userdata('user_type') == 'Admin' || in_array(301,$this->module_access))
            {
              $middle_column_content .='
                  <div class="col-12">
                    <div class="card card-large-icons card-condensed middle_col_item">
                      <div class="card-icon">
                        <i class="fas fa-question-circle"></i>
                      </div>
                      <div class="card-body">
                        <h4>'.$this->lang->line("Questions & Ans.").'</h4>          
                        <a href="'.base_url("gmb/question_list").'" id="question_answer" data-page-id="'.$location_info[0]['id'].'" data-height="500" class="card-cta iframed">'.$this->lang->line("Change Settings").'</a>
                      </div>
                    </div>
                  </div>';
            }

            
            $middle_column_content .='
                </div>
              </div>
            </div>
            
            <script>
            $(\'[data-toggle="popover"]\').popover(); 
            $(\'[data-toggle="popover"]\').on("click", function(e) {e.preventDefault(); return true;});
            </script>
            '; 
        }

        $response['middle_column_content'] = $middle_column_content;
        $response['location_insight_url'] = base_url('gmb/location_insights_basic/').$location_table_id;
        echo json_encode($response);

    }

    public function get_new_review_url()
    {
        $selected_location_table_id = $this->session->userdata('location_list_location_table_id');
        $location_info = $this->basic->get_data('google_business_locations',['where'=>['id'=>$selected_location_table_id]],'new_review_url');
        $review_url = isset($location_info[0]['new_review_url']) ? $location_info[0]['new_review_url'] : '';
        $content='<div class="row">
                    <div class="col-12">';
            $content .= '
                        <div class="card">
                          <div class="card-body">
                            <p>'.$this->lang->line("Copy the below URL for further use.").'</p>
                            <pre class="language-javascript">
                                <code class="dlanguage-javascript copy_code">
'.$review_url.'
                                </code>
                            </pre>
                          </div>
                        </div>';
            $content .='</div>
                </div>
                <script>
                    $(document).ready(function() {
                        Prism.highlightAll();
                        $(".toolbar-item").find("a").addClass("copy");

                        $(document).on("click", ".copy", function(event) {
                            event.preventDefault();

                            $(this).html("'.$this->lang->line('Copied!').'");
                            var that = $(this);
                            
                            var text = $(this).prev("code").text();
                            var temp = $("<input>");
                            $("body").append(temp);
                            temp.val(text).select();
                            document.execCommand("copy");
                            temp.remove();

                            setTimeout(function(){
                              $(that).html("'.$this->lang->line('Copy').'");
                            }, 2000); 

                        });
                    });
                </script>
                ';
        echo $content;
    }

    public function review_replies()
    {
      $selected_location_table_id = $this->session->userdata('location_list_location_table_id');
      $data['location_table_id'] = $selected_location_table_id;
      $data['page_title'] = $this->lang->line('Review reply list');
      $data['title'] = $this->lang->line('Review reply list');
      $data['body'] = 'review_replies';
      $data['iframe'] = 1;
      $this->_viewcontroller($data);
    }

    public function review_reply_data()
    {
        $this->ajax_check();

        $location_id = trim($this->input->post("location_id", true));
        $display_columns = array('#', 'id', 'star', 'actions');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by = $sort . " " . $order;

        // if(! $location_id) {
        //     $location_id = $this->session->userdata('gmb_review_reply_location_id');
        // }
        // // Sets location ID for add review reply settings
        // $this->session->set_userdata('gmb_add_review_reply_settings_location_id', $location_id);

        $where = [
            'where' => [
                'user_id' => $this->user_id,
                'location_id' => $location_id,
            ]
        ];

        $select = ['google_review_reply_settings.*'];

        $table = 'google_review_reply_settings';
        $info = $this->basic->get_data($table,$where,$select,$join='',$limit,$start,$order_by,$group_by='');

        // Gets total rows
        $total_rows_array = $this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
        $total_result = $total_rows_array[0]['total_rows'];

        $info_data = isset($info[0]) ? $info[0] : [];
        $td_data = [];
        $id = 0;
        foreach ($info_data as $key => $value) {
            $stars = ['five_star', 'four_star', 'three_star', 'two_star', 'one_star'];
            if (! in_array($key, $stars)) {
                continue;
            }

            $edit_review_reply = '<a class="btn btn-circle btn-outline-warning" href="' . base_url('gmb/edit_settings/') . $key . '" data-toggle="tooltip" title="' . $this->lang->line("Edit") . '"><i class="fas fa-edit"></i></a>';
            $delete_review_reply = '<a class="btn btn-circle btn-outline-danger delete" data-toggle="tooltip" title="' . $this->lang->line("Delete") . '" id="' . $key . '" href="#"><i class="fas fa-trash-alt"></i></a>';
            $report_review_reply = '<a class="btn btn-circle btn-outline-info report" data-toggle="tooltip" title="' . $this->lang->line("Report") . '" id="' . $key . '" href="'.base_url('gmb/review_report/').$key.'/'.$location_id.'" target="_BLANK"><i class="fas fa-eye"></i></a>';

            // Action section started from here
            $account_count = 2;
            $action_width = ($account_count*47)+20;
            $buttons = '<div class="dropdown d-inline dropright">
            <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i></button>
            <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';

            $buttons .= $edit_review_reply;
            $buttons .= $report_review_reply;
            $buttons .= $delete_review_reply;
            $buttons .= "</div></div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";

            switch ($key) {
                case 'five_star':
                    $val = (isset($info[0][$key]) && ! empty($info[0][$key])) ? $info[0][$key] : null;
                    if (null != $val) {
                        $id++;
                        $five_star = [
                            'id' => $id,
                            'star' => '<i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>',
                            'actions' => $buttons
                        ];
                        array_push($td_data, $five_star);
                    }
                    break;
                case 'four_star':
                    $val = (isset($info[0][$key]) && ! empty($info[0][$key])) ? $info[0][$key] : null;
                    if (null != $val) {
                        $id++;
                        $four_star = [
                            'id' => $id,
                            'star' => '<i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>',
                            'actions' => $buttons
                        ];
                        array_push($td_data, $four_star);
                    }
                    break;
                case 'three_star':
                    $val = (isset($info[0][$key]) && ! empty($info[0][$key])) ? $info[0][$key] : null;
                    if (null != $val) {
                        $id++;
                        $three_star = [
                            'id' => $id,
                            'star' => '<i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>',
                            'actions' => $buttons
                        ];
                        array_push($td_data, $three_star);
                    }
                    break;
                case 'two_star':
                    $val = (isset($info[0][$key]) && ! empty($info[0][$key])) ? $info[0][$key] : null;
                    if (null != $val) {
                        $id++;
                        $two_star = [
                            'id' => $id,
                            'star' => '<i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>',
                            'actions' => $buttons
                        ];
                        array_push($td_data, $two_star);
                    }
                    break;
                case 'one_star':
                    $val = (isset($info[0][$key]) && ! empty($info[0][$key])) ? $info[0][$key] : null;
                    if (null != $val) {
                        $id++;
                        $one_star = [
                            'id' => $id,
                            'star' => '<i class="fas fa-star text-warning"></i>',
                            'actions' => $buttons
                        ];
                        array_push($td_data, $one_star);
                    }
                    break;
            }
        }

        $data['draw'] = (int) $_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($td_data, $display_columns ,$start, $primary_key = "id");

        echo json_encode($data);
    }

    public function add_settings()
    {
        $data['page_title'] = $this->lang->line('Add review reply settings');
        $data['title'] = $this->lang->line('Add review reply settings');
        $data['body'] = 'add_review_reply_settings';
        $data['iframe'] = 1;
        $this->_viewcontroller($data);
    }

    public function validate_keyword_settings() {
        $keyword_settings = isset($_POST['keyword_settings'][0]) ? strlen($_POST['keyword_settings'][0]) : 0;

        if (0 == $keyword_settings) {
            $this->form_validation->set_message('validate_keyword_settings', $this->lang->line("Keyword is required"));
            return false;
        }

        return true;
    }

    public function validate_reply_settings() {
        $reply_settings = isset($_POST['reply_settings'][0]) ? strlen($_POST['reply_settings'][0]) : 0;

        if (0 == $reply_settings) {
            $this->form_validation->set_message('validate_reply_settings', $this->lang->line("Reply is required"));
            return false;
        }

        return true;
    }

    public function save_review_reply()
    {
        $this->form_validation->set_rules('star_rating', $this->lang->line("Star rating"), 'required|in_list[five_star,four_star,three_star,two_star,one_star]');
        $this->form_validation->set_rules('reply_type', $this->lang->line("Reply type"), 'required|in_list[generic,keyword]');

        $reply_type = $this->input->post('reply_type', true);
        if ('generic' == $reply_type) {
            $this->form_validation->set_rules('generic_message', $this->lang->line("Generic message"), 'required');
        } elseif ('keyword') {
            $this->form_validation->set_rules('keyword_settings', $this->lang->line('Keyword'), 'callback_validate_keyword_settings');
            $this->form_validation->set_rules('reply_settings', $this->lang->line('Reply'), 'callback_validate_reply_settings');
            $this->form_validation->set_rules('not_found_reply_settings', $this->lang->line("Message for no match"), 'required');
        }

        if (false === $this->form_validation->run()) {
            $errors = $this->form_validation->error_array();
            $errors = str_replace(['<b>', '</b>'], '', $errors);
            echo json_encode([
                'status' => false,
                'errors' => $errors
            ]);
            exit;
        }

        // Gets location ID for updating star rating
        $selected_location_table_id = $this->session->userdata('location_list_location_table_id');
        
        $only_location_id_info = $this->basic->get_data('google_business_locations',['where'=>['id'=>$selected_location_table_id]],['only_location_id']);
        $only_location_id = isset($only_location_id_info[0]['only_location_id']) ? $only_location_id_info[0]['only_location_id'] : '';
        $existing_data = $this->basic->get_data('google_review_reply_settings',['where'=>['only_location_id'=>$only_location_id,'location_id !='=>$selected_location_table_id]]);
        if(!empty($existing_data))
        {
          $where = ['where'=>['google_business_locations.id'=>$existing_data[0]['location_id']]];
          $join = ['google_user_account'=>'google_business_locations.user_account_id=google_user_account.id,left'];
          $select = ['google_user_account.*'];
          $account_info = $this->basic->get_data('google_business_locations',$where,$select,$join);
          $name = isset($account_info[0]['account_display_name']) ? $account_info[0]['account_display_name'] : "";
          $email = isset($account_info[0]['email']) ? $account_info[0]['email'] : "";
          $profile_photo = isset($account_info[0]['profile_photo']) ? $account_info[0]['profile_photo'] : "";
          $response_html = '<div class="row text-center">
                              <div class="col-12">
                                <p><b>'.$this->lang->line('Reply settings is already enabled for this location by').'</b></p>
                              </div>
                              <div class="col-8 offset-2">
                                <ul class="list-unstyled list-unstyled-border">
                                    <li class="media">
                                        <img alt="image" class="mr-3 rounded-circle" width="50" src="'.$profile_photo.'">
                                        <div class="media-body">
                                          <div class="mt-0 mb-1 font-weight-bold">'.$name.'</div>
                                          <div class="text-success text-small font-600-bold"><i class="fas fa-envelope"></i> '.$email.'</div>
                                        </div>
                                    </li>
                                </ul>
                              </div>
                            </div>';
          echo json_encode([
              'status' => false,
              'message' => $response_html,
              'html' => 'yes'
          ]);
          exit;
        }

        // Gets star rating
        $star_rating = $this->input->post('star_rating');

        $data = [
            'status' => '0',
            'only_location_id' => $only_location_id,
            $star_rating => json_encode($_POST)
        ];
        $insert_data = [
          'user_id' => $this->user_id,
          'location_id' => $selected_location_table_id,
          'status' => '0',
          'only_location_id' => $only_location_id,
          $star_rating => json_encode($_POST)
        ];

        $where = [
            'user_id' => $this->user_id,
            'location_id' => $selected_location_table_id,
        ];

        $existing_where = [];
        $existing_where['where'] = [
            'user_id' => $this->user_id,
            'location_id' => $selected_location_table_id
        ];
        $existing_info = $this->basic->get_data('google_review_reply_settings',$existing_where);

        if(empty($existing_info))
        {
          if($this->basic->insert_data('google_review_reply_settings',$insert_data))
          {
            $message = $this->lang->line('Review reply settings inserted successfully.');
            echo json_encode([
                'status' => true,
                'message' => $message
            ]);
            exit;
          }
          else
          {
            $message = $this->lang->line('Something went wrong while updating data');
            echo json_encode([
                'status' => false,
                'message' => $message
            ]);
            exit;
          }
        }
        else
        {
          $this->basic->update_data('google_review_reply_settings', $where, $data);
          $message = $this->lang->line('Review reply settings successfully updated.');
          echo json_encode([
              'status' => true,
              'message' => $message
          ]);
          exit;
        }
    }

    public function edit_settings($star)
    {
        $stars = ['five_star', 'four_star', 'three_star', 'two_star', 'one_star'];
        if (! in_array($star, $stars)) {
            echo $this->error_404();
            exit;
        }

        $selected_location_table_id = $this->session->userdata('location_list_location_table_id');


        $where = [
            'where' => [
                'user_id' => $this->user_id,
                'location_id' => $selected_location_table_id
            ]
        ];

        $ratings = $this->basic->get_data('google_review_reply_settings', $where);

        $rating = isset($ratings[0][$star]) ? $ratings[0][$star] : [];

        $data['rating_details'] = json_decode($rating, true);
        $data['page_title'] = $this->lang->line('Edit review reply settings');
        $data['title'] = $this->lang->line('Edit review reply settings');
        $data['body'] = 'edit_review_reply_settings';
        $data['iframe'] = 1;
        $this->_viewcontroller($data);
    }

    public function delete_star()
    {
        $this->ajax_check();

        $star = $this->input->post('star_rating');
        $stars = ['five_star', 'four_star', 'three_star', 'two_star', 'one_star'];
        if (! in_array($star, $stars)) {
            echo json_encode([
                'status' => false,
                'message' => $this->lang->line('Bad request'),
            ]);
            exit;
        }

        $selected_location_table_id = $this->session->userdata('location_list_location_table_id');
        $where = [
            'user_id' => $this->user_id,
            'location_id' => $selected_location_table_id,
        ];

        $data = [
            $star => null,
        ];

        if ($this->basic->update_data('google_review_reply_settings', $where, $data)) {
            
            $existing_stars = $this->basic->get_data('google_review_reply_settings',['where'=>['user_id'=>$this->user_id,'location_id'=>$selected_location_table_id]]);
            if($existing_stars[0]['five_star'] === null && $existing_stars[0]['four_star'] === null && $existing_stars[0]['three_star'] === null && $existing_stars[0]['two_star'] === null && $existing_stars[0]['one_star'] === null)
            {
              $this->basic->delete_data('google_review_reply_settings',['user_id'=>$this->user_id,'location_id'=>$selected_location_table_id]);
            }
            
            $message = $this->lang->line('Review reply settings deleted successfully');
            echo json_encode([
                'status' => true,
                'message' => $message
            ]);
            exit;
        } else {
            $message = $this->lang->line('Something went wrong while deleting review reply settings');
            echo json_encode([
                'status' => false,
                'message' => $message
            ]);
            exit;
        }
    }

    public function post_list()
    {
        $this->load->library('google_my_business');
        $gmb = $this->google_my_business;

        $location_table_id = $this->session->userdata('location_list_location_table_id');
        $location_info = $this->basic->get_data('google_business_locations',['where'=>['id'=>$location_table_id]]);
        $location_id = isset($location_info[0]['location_id']) ? $location_info[0]['location_id'] : null;

        // Holds posts list
        $posts_list = null;

        try {
            $posts_list = $gmb->postsList($location_id);
        } catch (Google_Service_Exception $e) {
            $e->getMessage();
        } catch (Google_Exception $e) {
            $e->getMessage();
        } catch (\Exception $e) {
            $e->getMessage();
        }

        $prepared_posts_list = [];
        if (is_array($posts_list['localPosts'])) {
            foreach ($posts_list['localPosts'] as $key => $post) {
                $photo = isset($post->getMedia()[0]) && is_object($post->getMedia()[0]) ? $post->getMedia()[0]->getGoogleUrl() : null;
                if ($post->getCallToAction()) {
                    $prepared_posts_list[] = [
                        'post_type' => 'callToAction',
                        'name' => $post->getName(),
                        'summary' => $post->getSummary(),
                        'searchUrl' => $post->getSearchUrl(),
                        'photo' => $photo,
                        'createTime' => $post->getCreateTime(),
                        'actionType' => $post->getCallToAction()->getActionType(),
                        'url' => $post->getCallToAction()->getUrl(),
                    ];
                } elseif ($post->getOffer()) {
                    $photo = isset($post->getMedia()[0]) && is_object($post->getMedia()[0]) ? $post->getMedia()[0]->getGoogleUrl() : null;
                    $prepared_posts_list[] = [
                        'post_type' => 'offer',
                        'name' => $post->getName(),
                        'summary' => $post->getSummary(),
                        'searchUrl' => $post->getSearchUrl(),
                        'photo' => $photo,
                        'createTime' => $post->getCreateTime(),
                        'couponCode' => $post->getOffer()->getCouponCode(),
                        'redeemUrl' => $post->getOffer()->getRedeemOnlineUrl(),
                    ];
                } elseif ($post->getEvent()) {
                    $start_date_time = '';
                    $end_date_time = '';

                    if ($post->getEvent()->getSchedule()) {
                        $schedule = $post->getEvent()->getSchedule();
                        $startDate = $schedule->getStartDate();
                        $startTime = $schedule->getStartTime();

                        if (is_object($startDate)) {
                            $start_date_time = $startDate->getYear() . '-' . $startDate->getMonth() . '-' . $startDate->getDay();
                        }
                        if (is_object($startTime)) {
                            $start_date_time .= $startTime->getHours() ? $startTime->getHours() . ':' . $startTime->getMinutes() : '';
                        }

                        $endDate = $schedule->getEndDate();
                        $endTime = $schedule->getEndTime();

                        if (is_object($endDate)) {
                            $end_date_time = $endDate->getYear() . '-' . $endDate->getMonth() . '-' . $endDate->getDay();
                        }
                        if (is_object($endTime)) {
                            $end_date_time .= $endTime->getHours() ? $endTime->getHours() . ':' . $endTime->getMinutes() : '';
                        }
                    }

                    $photo = isset($post->getMedia()[0]) && is_object($post->getMedia()[0]) ? $post->getMedia()[0]->getGoogleUrl() : null;
                    $prepared_posts_list[] = [
                        'post_type' => 'event',
                        'name' => $post->getName(),
                        'title' => $post->getEvent()->getTitle(),
                        'summary' => $post->getSummary(),
                        'searchUrl' => $post->getSearchUrl(),
                        'photo' => $photo,
                        'createTime' => $post->getCreateTime(),
                        'start_date_time' => $start_date_time,
                        'end_date_time' => $end_date_time,
                    ];
                }
            }
        }

        $data['posts_list'] = $prepared_posts_list;
        $data['page_title'] = $this->lang->line('Posts list');
        $data['title'] = $this->lang->line('Posts list');
        $data['body'] = 'posts_list';
        $data['iframe'] = 1;
        $this->_viewcontroller($data);
    }

    public function review_list()
    {
        $this->load->library('google_my_business');
        $gmb = $this->google_my_business;

        $location_table_id = $this->session->userdata('location_list_location_table_id');
        $location_info = $this->basic->get_data('google_business_locations',['where'=>['id'=>$location_table_id]]);
        $location_id = isset($location_info[0]['location_id']) ? $location_info[0]['location_id'] : null;
        $location_name = isset($location_info[0]['location_display_name']) ? $location_info[0]['location_display_name'] : '';

        // Holds reviews list
        $reviews_list = null;

        try {
            $reviews_list = $gmb->reviewsList($location_id);
        } catch (Google_Service_Exception $e) {
            $e->getMessage();
        } catch (Google_Exception $e) {
            $e->getMessage();
        } catch (\Exception $e) {
            $e->getMessage();
        }

        $reviews = [];
        if (isset($reviews_list['reviews'])) {
            foreach ($reviews_list['reviews'] as $key => $review) {

                $anonymous = '';
                $displayName = '';
                $profilePhotoUrl = '';

                if (is_object($review->getReviewer())) {
                    $reviewer = $review->getReviewer();

                    $anonymous = $reviewer->getIsAnonymous();
                    $displayName = $reviewer->getDisplayName();
                    $profilePhotoUrl = $reviewer->getProfilePhotoUrl();
                }

                $reviewReplies = [];
                if (is_object($review->getReviewReply())) {
                    $reviewReplies['comment'] = $review->getReviewReply()->getComment();
                    $reviewReplies['updateTime'] = $review->getReviewReply()->getUpdateTime();
                }

                $reviews[] = [
                    'comment' => $review->getComment(),
                    'name' => $review->getName(),
                    'reviewId' => $review->getReviewId(),
                    'starRating' => $review->getStarRating(),
                    'anonymous' => $anonymous,
                    'displayName' => $displayName,
                    'profilePhotoUrl' => $profilePhotoUrl,
                    'locationName' => $location_name,
                    'createTime' => $review->getCreateTime(),
                    'reviewReply' => $reviewReplies,
                ];
            }
        }

        $data['reviews'] = $reviews;
        $data['page_title'] = $this->lang->line('Reviews list');
        $data['title'] = $this->lang->line('Reviews list');
        $data['body'] = 'reviews_list';
        $data['iframe'] = 1;
        $this->_viewcontroller($data);
    }

    public function validate_review_id()
    {
        $review_id = $_POST['review_id'];

        if (! preg_match('~accounts/[0-9]+/locations/[0-9]+/reviews/[a-zA-Z0-9\-_]+~ui', $review_id, $matches)) {
            $this->form_validation->set_message('validate_review_id', $this->lang->line('Invalid review ID provided'));
            return false;
        }

        return true;
    }

    public function reply_to_review()
    {
        $this->ajax_check();

        $reply_type = $this->input->post('reply_type', true);

        if (! in_array($reply_type, ['location_manager_index', 'review_report'])) {
            $message = $this->lang->line('Review reply type is invalid');
            echo json_encode([
                'status' => false,
                'message' => $message,
            ]);
            exit;
        }

        $this->form_validation->set_rules('review_id', $this->lang->line('Review ID'), 'callback_validate_review_id');
        $this->form_validation->set_rules(
            'review_star',
            $this->lang->line('Star Rating'),
            'required|in_list[FIVE,FOUR,THREE,TWO,ONE]',
            [
                'in_list' => $this->lang->line('Star rating must be one of FIVE, FOUR, THREE, TWO and ONE'),
            ]
        );

        $this->form_validation->set_rules('reviewer_location_name', $this->lang->line('Location name'), 'required');
        $this->form_validation->set_rules('review_reply_message', $this->lang->line('Reply message'), 'required');

        if ('location_manager_index' == $reply_type) {
            $this->form_validation->set_rules('reviewer_display_name', $this->lang->line('Review name'), 'required');
            $this->form_validation->set_rules('reviewer_profile_photo', $this->lang->line('Profile photo'), 'required');
        }

        if (false === $this->form_validation->run()) {
            $errors = $this->form_validation->error_array();
            $errors = str_replace(['<b>', '</b>'], '', $errors);
            echo json_encode([
                'status' => false,
                'errors' => $errors
            ]);
            exit;
        }

        $review_id = $this->input->post('review_id');
        $review_star = $this->input->post('review_star');
        $review_comment = $this->input->post('review_comment', true);
        $review_reply_message = $this->input->post('review_reply_message', true);
        $review_reply_message = spintax_process($review_reply_message);
        $reviewer_location_name = $this->input->post('reviewer_location_name', true);

        if ('location_manager_index' == $reply_type) {
            $reviewer_display_name = $this->input->post('reviewer_display_name', true);
            $reviewer_profile_photo = $this->input->post('reviewer_profile_photo', true);
        }

        $location_id = explode('/reviews/', $review_id, 2);
        $location_id = $location_id[0];
        $user_id = $this->user_id;
        $reply_time = date('Y-m-d H:i:s');

        $where = [
            'where' => [
                'user_id' => $this->user_id,
                'location_id' => $location_id
            ],
        ];

        $location = $this->basic->get_data('google_business_locations', $where, ['id','location_id'], [], 1);
        if (1 != count($location)) {
            $message = $this->lang->line('You do not have permission to reply to the review');
            echo json_encode([
                'status' => false,
                'message' => $message,
            ]);
            exit;
        }

        $location_table_id = isset($location[0]['id']) ? $location[0]['id'] : null;

        $this->load->library('google_my_business');
        $gmb = $this->google_my_business;

        $error = '';

        try {
            $response = $gmb->replyReview($review_id, $review_reply_message);
        } catch (Google_Service_Exception $e) {
            $error = $e->getMessage();
        } catch (Google_Exception $e) {
            $error = $e->getMessage();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (! empty($error)) {
            $where = [
                'user_id' => $this->user_id,
                'location_id' => $location_table_id,
                'review_id' => $review_id,
            ];

            $data = [
                'error' => $error,
                'reply_time' => date('Y-m-d H:i:s')
            ];

            $this->basic->update_data('google_review_reply_report', $where, $data);

            echo json_encode([
                'status' => false,
                'message' => $error,
            ]);
            exit;
        }

        if ('location_manager_index' == $reply_type) {
            $sql = "
              INSERT INTO `google_review_reply_report` (user_id, location_id, location_display_name, review_id, reviewer_name, reviewer_photo, review_star, review_comment, review_reply, reply_time) 
              VALUES ('{$user_id}', '{$location_table_id}', '{$reviewer_location_name}', '{$review_id}', '{$reviewer_display_name}', '{$reviewer_profile_photo}', '{$review_star}', '{$review_comment}', '{$review_reply_message}', '{$reply_time}') 
              ON DUPLICATE KEY 
              UPDATE `review_reply` = '{$review_reply_message}', `location_display_name` = '{$reviewer_location_name}', `reviewer_name` = '{$reviewer_display_name}', `reviewer_photo` = '{$reviewer_profile_photo}'";
                      } else {
                          $sql = "
              INSERT INTO `google_review_reply_report` (user_id, location_id, location_display_name, review_id, review_star, review_comment, review_reply, reply_time) 
              VALUES ('{$user_id}', '{$location_table_id}', '{$reviewer_location_name}', '{$review_id}', '{$review_star}', '{$review_comment}', '{$review_reply_message}', '{$reply_time}') 
              ON DUPLICATE KEY 
              UPDATE `review_reply` = '{$review_reply_message}', `location_display_name` = '{$reviewer_location_name}'";
        }

        $this->basic->execute_complex_query($sql);

        if (! $this->db->affected_rows() > 0) {
            $message = $this->lang->line('Unable to update review-reply data to database');
            echo json_encode([
                'status' => false,
                'message' => $message,
            ]);
            exit;
        }

        $message = $this->lang->line('Reply to the review updated successfully');
        echo json_encode([
            'status' => true,
            'message' => $message,
        ]);
        exit;
    }

    public function delete_reply_to_review()
    {
        $this->ajax_check();

        $this->form_validation->set_rules('review_id', $this->lang->line('Review ID'), 'callback_validate_review_id');

        if (false === $this->form_validation->run()) {
            $message = $this->lang->line('Invalid review reply ID provided');
            echo json_encode([
                'status' => false,
                'errors' => $message
            ]);
            exit;
        }

        $review_id = $this->input->post('review_id');

        $this->load->library('google_my_business');
        $gmb = $this->google_my_business;

        $error = '';

        try {
            $response = $gmb->deleteReply($review_id);
        } catch (Google_Service_Exception $e) {
            $error = $e->getMessage();
        } catch (Google_Exception $e) {
            $error = $e->getMessage();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (! empty($error)) {
            $where = [
                'user_id' => $this->user_id,
                'location_id' => $this->session->userdata('location_list_location_table_id'),
                'review_id' => $review_id,
            ];

            $data = [
                'error' => $error,
                'reply_time' => date('Y-m-d H:i:s')
            ];

            $this->basic->update_data('google_review_reply_report', $where, $data);

            echo json_encode([
                'status' => false,
                'message' => $error,
            ]);
            exit;
        }

        $where = [
            'user_id' => $this->user_id,
            'review_id' => $review_id,
            'location_id' => $this->session->userdata('location_list_location_table_id'),
        ];

        $this->basic->update_data('google_review_reply_report', $where, [
            'review_reply' => null,
            'reply_time' => date('Y-m-d H:i:s'),
        ]);

        $message = $this->lang->line('Review reply has been deleted successfully');
        echo json_encode([
            'status' => true,
            'message' => $message,
        ]);
        exit;
    }

    public function question_list()
    {
        $this->load->library('google_my_business');
        $gmb = $this->google_my_business;

        $location_table_id = $this->session->userdata('location_list_location_table_id');
        $location_info = $this->basic->get_data('google_business_locations',['where'=>['id'=>$location_table_id]]);
        $location_id = isset($location_info[0]['location_id']) ? $location_info[0]['location_id'] : null;

        // Holds questions list
        $question_list = null;

        try {
            $question_list = $gmb->questionsList($location_id);
        } catch (Google_Service_Exception $e) {
            $e->getMessage();
        } catch (Google_Exception $e) {
            $e->getMessage();
        } catch (\Exception $e) {
            $e->getMessage();
        }

        $questions = [];
        if (isset($question_list['questions'])) {
            foreach ($question_list['questions'] as $key => $question) {
                $displayName = '';
                $profilePhotoUrl = '';
                if (is_object($question->getAuthor())) {
                    $author = $question->getAuthor();

                    $displayName = $author->getDisplayName();
                    $profilePhotoUrl = $author->getProfilePhotoUrl();
                }


                $answers = [];
                if (is_array($question->getTopAnswers())) {
                    foreach ($question->getTopAnswers() as $topAnswer) {
                        $questionerAnswerInfo = [
                            'text' => $topAnswer->getText(),
                            'createTime' => $topAnswer->getUpdateTime(),
                        ];

                        if (is_object($topAnswer)) {
                            $questionerAuthor = $topAnswer->getAuthor();
                            $questionerAuthorInfo = [
                                'displayName' => $questionerAuthor->getDisplayName(),
                                'profilePhotoUrl' => $questionerAuthor->getProfilePhotoUrl(),
                            ];
                        }

                        $answers[] = array_merge($questionerAnswerInfo, $questionerAuthorInfo);
                    }
                }

                $questions[] = [
                    'text' => $question->getText(),
                    'name' => $question->getName(),
                    'displayName' => $displayName,
                    'profilePhotoUrl' => $profilePhotoUrl,
                    'createTime' => $question->getCreateTime(),
                    'displayName' => $displayName,
                    'profilePhotoUrl' => $profilePhotoUrl,
                    'answers' => $answers,
                ];
            }
        }

        $data['questions'] = $questions;
        $data['page_title'] = $this->lang->line('Questions list');
        $data['title'] = $this->lang->line('Questions list');
        $data['body'] = 'questions_list';
        $data['iframe'] = 1;
        $this->_viewcontroller($data);
    }

    public function validate_question_id()
    {
        $question_id = $_POST['question_id'];

        if (! preg_match('~accounts/[0-9]+/locations/[0-9]+/questions/[a-zA-Z0-9\-_]+~i', $question_id, $matches)) {
            $this->form_validation->set_message('validate_question_id', $this->lang->line('Invalid question ID provided'));
            return false;
        }

        return true;
    }

    public function answer_to_question()
    {
        $this->ajax_check();

        $this->form_validation->set_rules('question_id', $this->lang->line('Question ID'), 'callback_validate_question_id');
        $this->form_validation->set_rules('answer_to_question_message', $this->lang->line('Answer'), 'required|max_length[4096]');

        if (false === $this->form_validation->run()) {
            $errors = $this->form_validation->error_array();
            $errors = str_replace(['<b>', '</b>'], '', $errors);
            echo json_encode([
                'status' => false,
                'errors' => $errors
            ]);
            exit;
        }

        $question_id = $this->input->post('question_id');
        $question_text = $this->input->post('question_text', true);
        $answer_to_question_message = $this->input->post('answer_to_question_message', true);
        $location_id = explode('/questions/', $question_id, 2);
        $location_id = $location_id[0];

        $where = [
            'where' => [
                'user_id' => $this->user_id,
                'location_id' => $location_id
            ],
        ];

        $location = $this->basic->get_data('google_business_locations', $where, ['id','location_id'], [], 1);
        if (1 != count($location)) {
            $message = $this->lang->line('You do not have permission to answer the question');
            echo json_encode([
                'status' => false,
                'message' => $message,
            ]);
            exit;
        }

        $location_table_id = isset($location[0]['id']) ? $location[0]['id'] : null;

        $this->load->library('google_my_business');
        $gmb = $this->google_my_business;

        $error = '';

        try {
            $response = $gmb->answerQuestion($question_id, $answer_to_question_message);
        } catch (Google_Service_Exception $e) {
            $error = $e->getMessage();
        } catch (Google_Exception $e) {
            $error = $e->getMessage();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (! empty($error)) {

            echo json_encode([
                'status' => false,
                'message' => $error,
            ]);
            exit;
        }

        $message = $this->lang->line('Answer to the question created successfully');
        echo json_encode([
            'status' => true,
            'message' => $message,
        ]);
        exit;
    }

    public function delete_question_answer()
    {
        $this->ajax_check();

        // accounts/107745512734031207626/locations/14692206365244175995/questions/AIe9_BGhYIk2KhOdF8fekWDvq8JUw-YhSYIY-XWMwRYsbtV-iL3nuz2hq1edih4FpamwR9UrnyRNx15zYxZtvLxrw2fIEkxv-UzRxl124s_fVjmbXyxaLDV3il5u7wGAyGEFrGhNMYH_
        $question_id = $this->input->post('question_id', true);
        
        if (! preg_match('@^accounts/[0-9]+/locations/[0-9]+/questions/[0-9a-zA-Z\-_]+$@i', $question_id)) {
            echo json_encode([
                'status' => false,
                'message' => $this->lang->line('Bad request')
            ]);
            exit;
        }

        // Inits google_my_business
        $params['gmb_user_table_id'] = $this->session->userdata('google_mybusiness_user_table_id');
        $params['redirectUri'] = '';
        $this->load->library('google_my_business', $params);
        $gmb = $this->google_my_business;

        if (is_object($gmb)) {
            try {
                $response = $gmb->deleteQuestionAnswer($question_id);
            } catch (Google_Service_Exception $e) {
                $error = $e->getMessage();
            } catch (Google_Exception $e) {
                $error = $e->getMessage();
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }

            // @TODO
            // Log or send email the error message
        }

        if (! empty($error)) {
            echo json_encode([
                'status' => false,
                'message' => $error,
            ]);
            exit;
        }

        echo json_encode([
            'status' => true,
            'message' => $this->lang->line('The answer to the question has been deleted successfully'),
        ]);
        exit;
    }

    public function review_report()
    {
      $gmb_user_table_id = $this->session->userdata('google_mybusiness_user_table_id');
      $locations = $this->basic->get_data('google_business_locations',['where'=>['user_account_id'=>$gmb_user_table_id,'user_id'=>$this->user_id]],['id','location_display_name']);

      $data['locations'] = $locations;
      $data['page_title'] = $this->lang->line('Review report');
      $data['title'] = $this->lang->line('Review report');
      $data['body'] = 'review_report';
      $this->_viewcontroller($data);
    }

    public function review_report_data()
    {
        $this->ajax_check();

        $review_star     = trim($this->input->post("review_star", true));
        $location_table_id   = trim($this->input->post("location_name", true));
        $searching       = trim($this->input->post("searching", true));
        $post_date_range = $this->input->post("post_date_range", true);

        $display_columns = array('id', 'reviewer_photo', 'reviewer_name', 'review_star', 'review_comment', 'review_reply', 'actions', 'location_display_name', 'reply_time', 'error');
        $search_columns  = array('review_comment','review_reply','reply_time');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by = $sort . " " . $order;

        $where_simple = array();

        if(! empty($post_date_range)) {
            $exp        = explode('|', $post_date_range);
            $from_date  = isset($exp[0]) ? $exp[0] : '';
            $to_date    = isset($exp[1]) ? $exp[1] : '';

            if('Invalid date' != $from_date && 'Invalid date' != $to_date) {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date   = date('Y-m-d', strtotime($to_date));
                $where_simple["Date_Format(reply_time, '%Y-%m-%d') >="] = $from_date;
                $where_simple["Date_Format(reply_time, '%Y-%m-%d') <="] = $to_date;
            }
        }

        if($review_star !="") {
            $where_simple['google_review_reply_report.review_star'] = $review_star;
        }
        if($location_table_id !="") {
            $where_simple['google_review_reply_report.location_id'] = $location_table_id;
        }
        if($searching !="") {
            $where_simple['google_review_reply_report.review_reply like'] = "%" . $searching . "%";
        }

        $where_simple['google_review_reply_report.user_id'] = $this->user_id;

        $where  = array('where' => $where_simple);

        if($searching !="") {
            $or_where_simple['google_review_reply_report.review_comment like'] = "%" . $searching . "%";
            $where['or_where'] = $or_where_simple;
        }

        $table = 'google_review_reply_report';
        $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

        // Gets total rows
        $total_rows_array = $this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
        $total_result = $total_rows_array[0]['total_rows'];

        // Prepares some vars
        for($i = 0; $i < count($info); $i++) {

            if ($info[$i]['reviewer_photo']) {
                $info[$i]['reviewer_photo'] = '<img src="' . $info[$i]['reviewer_photo'] . '" class="img-fluid" width="32" alt="' . $info[$i]['reviewer_name'] . '">';
            } else {
                $info[$i]['reviewer_photo'] = '<img src="' . base_url('upload/xerobiz/dummy_author.png') . '" class="img-fluid" width="32" alt="' . $info[$i]['reviewer_name'] . '">';
            }

            // Report campaign action
            $createReview = '<a 
                class="btn btn-circle btn-outline-primary update-review-reply" 
                data-toggle="tooltip" 
                title="' . $this->lang->line("Update reply") . '" 
                data-review-id="' . $info[$i]['review_id'] . '" 
                data-review-star="' . $info[$i]['review_star'] . '" 
                data-review-reply="' . $info[$i]['review_reply'] . '" 
                data-review-comment="' . $info[$i]['review_comment'] . '"
                data-location-name="' . $info[$i]['location_display_name'] . '"
                href="#"
            >
                <i class="fas fa-reply"></i>
            </a>';

            // Delete campaign action
            $deleteReview = '<a 
                class="btn btn-circle btn-outline-danger delete-review-reply" 
                data-toggle="tooltip" 
                title="' . $this->lang->line("Delete reply") . '" 
                data-review-id="' . $info[$i]['review_id'] . '" 
                href="#"
            >
                <i class="fas fa-trash-alt"></i>
            </a>';

            // Defines start rating
            if ('FIVE' == $info[$i]['review_star']) {
                $info[$i]['review_star'] = '<i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>';
            } elseif ('FOUR' == $info[$i]['review_star']) {
                $info[$i]['review_star'] = '<i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>';
            } elseif ('THREE' == $info[$i]['review_star']) {
                $info[$i]['review_star'] = '<i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>';
            } elseif ('TWO' == $info[$i]['review_star']) {
                $info[$i]['review_star'] = '<i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>';
            } elseif ('ONE' == $info[$i]['review_star']) {
                $info[$i]['review_star'] = '<i class="fas fa-star text-warning"></i>';
            }

            if ($info[$i]['reply_time']) {
                $info[$i]['reply_time'] = "<div style='min-width:120px !important;'>" . date("M j, y H:i", strtotime($info[$i]['reply_time'])) . "</div>";
            }

            // Action section started from here
            $account_count = 2;
            $action_width = ($account_count*47)+20;
            $info[$i]['actions'] = '<div class="dropdown d-inline dropright">
            <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i></button>
            <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';

            $info[$i]['actions'] .= $createReview;
            $info[$i]['actions'] .= $deleteReview;

            $info[$i]['actions'] .= "</div></div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start, $primary_key = "id");

        echo json_encode($data);
    }

    public function location_insights_basic($location_id = null)
    {
        if (null == $location_id) {
            return $this->error_404();
        }

        $where = [
            'where' => [
                'id' => $location_id,
                'deleted' => '0',
                'user_id' => $this->user_id
            ],
        ];

        $select = ['id', 'location_id', 'user_account_id', 'location_display_name'];

        $location = $this->basic->get_data('google_business_locations', $where, $select, [], 1);

        $no_data = false;
        if (1 != count($location)) {
            $no_data = true;
        }

        // Initial vars
        $error = '';
        $response = [];

        $location_name = '';
        $user_account_id = '';
        $location_table_id = '';
        $location_display_name = '';

        $start_date_time = '';
        $end_date_time = '';

        $no_data_message = '';

        if (false == $no_data) {
            $location_name = isset($location[0]['location_id'])
                ? $location[0]['location_id']
                : null;

            $location_table_id = isset($location[0]['id'])
                ? $location[0]['id']
                : null;

            $user_account_id = isset($location[0]['user_account_id'])
                ? $location[0]['user_account_id']
                : null;

            $location_display_name = isset($location[0]['location_display_name'])
                ? $location[0]['location_display_name']
                : null;

            // Inits google_my_business
            $params['gmb_user_table_id'] = $user_account_id;
            $params['redirectUri'] = '';
            $this->load->library('google_my_business', $params);
            $gmb = $this->google_my_business;

            if ($_POST) {
                $start_date_time_value = $this->input->post('from_date', true);
                $end_date_time_value = $this->input->post('to_date', true);
                $start_date_time = new DateTime($start_date_time_value);
                $end_date_time = new DateTime($end_date_time_value);

                if ($end_date_time <= $start_date_time) {
                    $start_date_time = new DateTime();
                    $start_date_time->modify('-2 months');
                    $end_date_time = new DateTime();
                }

                if ($end_date_time > $start_date_time) {
                    $too_much_interval = $start_date_time->diff($end_date_time);
                    $days = $too_much_interval->format('%R%a');
                    $date_time_difference = (int) $days;

                    if ($date_time_difference > 186) {
                        $start_date_time = new DateTime($start_date_time_value);
                        $end_date_time = new DateTime($start_date_time_value);
                        $interval = new DateInterval('P6M');
                        $end_date_time->add($interval);
                    }
                }
            } else {
                $start_date_time = new DateTime();
                $start_date_time->modify('-1 months');
                $end_date_time = new DateTime();
            }

            if (is_object($gmb)) {
                // accounts/107745512734031207626/locations/14692206365244175995
                $account_name = explode('/locations/', $location_name, 2);

                try {
                    $response = $gmb->locationInsightsBasicMetric(
                        $account_name[0],
                        [
                            $location_name,
                        ],
                        'ALL',
                        [
                            'AGGREGATED_DAILY',
                        ],
                        $start_date_time,
                        $end_date_time
                    );
                } catch (Google_Service_Exception $e) {
                    $error = $e->getMessage();
                } catch (Google_Exception $e) {
                    $error = $e->getMessage();
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }

                // @TODO
                // Log or send email the error message
            }
        }

        if (! count($response)) {
            $no_data = true;
        }

        $metrics = [
            'QUERIES_DIRECT',
            'QUERIES_INDIRECT',
            'QUERIES_CHAIN',
            'VIEWS_MAPS',
            'VIEWS_SEARCH',
            'ACTIONS_WEBSITE',
            'ACTIONS_PHONE',
            'ACTIONS_DRIVING_DIRECTIONS',
            'PHOTOS_VIEWS_MERCHANT',
            'PHOTOS_VIEWS_CUSTOMERS',
            'PHOTOS_COUNT_MERCHANT',
            'PHOTOS_COUNT_CUSTOMERS',
            'LOCAL_POST_VIEWS_SEARCH',
            'LOCAL_POST_ACTIONS_CALL_TO_ACTION',
        ];

        // Prepares array based on metrics
        $location_insights = [];
        if (is_object($response)) {
            foreach ($response as $metric) {
                if (is_array($metric->getMetricValues())) {

                    $i = 0;
                    // Loops 14 times
                    foreach ($metric->getMetricValues() as $metricValue) {
                        // Loops 30 times by default based on date range
                        foreach ($metricValue->getDimensionalValues() as $dimensionalValue) {
                            $startDateTime = '';
                            $value = $dimensionalValue->getValue();
                            if (is_object($dimensionalValue->getTimeDimension())) {
                                $timeRange = $dimensionalValue
                                    ->getTimeDimension()
                                    ->getTimeRange();
                                if (is_object($timeRange)) {
                                    $startDateTime = $timeRange->getStartTime();
                                }
                            }

                            $stringDateTime = date('M j, Y', strtotime($startDateTime));
                            $metricType = $metricValue->getMetric();

                            if (in_array($metricType, $metrics)) {
                                $location_insights[$metricType]['date'][] = $stringDateTime;
                                $location_insights[$metricType]['value'][] = $value ? $value : 0;
                            } else {
                                $location_insights[$metrics[$i]]['date'][] = $stringDateTime;
                                $location_insights[$metrics[$i]]['value'][] = mt_rand(100, 1000);
                            }
                        }

                        $i++;
                    }
                }
            }
        }

        $data['no_data'] = $no_data
            ? (! empty($error)
                ? $error
                : $this->lang->line("No data found."))
            : '';

        $data['post_insights'] = $location_insights;
        $data['location_name'] = $location_name;
        $data['location_table_id'] = $location_table_id;
        $data['location_display_name'] = $location_display_name;
        $data['from_date'] = is_object($start_date_time) ? $start_date_time->format('Y-m-d') : date('Y-m-d');
        $data['to_date'] = is_object($end_date_time) ? $end_date_time->format('Y-m-d') : date('Y-m-d');
        $data['title'] = $this->lang->line('Location Insights');
        $data['page_title'] = $this->lang->line('Location Insights');
        $data['body'] = 'location_insights_basic';
        $this->_viewcontroller($data);
    }

    ############################################################
    ## Post related methods
    ############################################################

    public function campaigns()
    {
        $data['page_title'] = $this->lang->line('Campaigns');
        $data['title'] = $this->lang->line('Campaigns');
        $data['body'] = 'campaigns/settings';
        $this->_viewcontroller($data);
    }

    public function posts()
    {
        $where = [
            'where' => [
                'user_account_id' => $this->session->userdata('google_mybusiness_user_table_id'),
            ],
        ];

        $locations = $this->basic->get_data('google_business_locations', $where, ['location_display_name']);

        $data['locations'] = $locations;
        $data['page_title'] = $this->lang->line('Post campaigns');
        $data['title'] = $this->lang->line('Post campaigns');
        $data['body'] = 'campaigns/posts';
        $this->_viewcontroller($data);
    }

    public function posts_data()
    {
        $this->ajax_check();

        $post_type       = trim($this->input->post("post_type", true));
        $location_name   = trim($this->input->post("location_name", true));
        $searching       = trim($this->input->post("searching", true));
        $post_date_range = $this->input->post("post_date_range", true);

        $display_columns = array("#", 'id', 'campaign_name', 'post_type', 'title', 'actions', 'status', 'schedule_time', 'error');
        $search_columns  = array('campaign_name','post_type','schedule_time');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by = $sort . " " . $order;

        $where_simple = array();

        if(! empty($post_date_range)) {
            $exp        = explode('|', $post_date_range);
            $from_date  = isset($exp[0]) ? $exp[0] : "";
            $to_date    = isset($exp[1]) ? $exp[1] : "";

            if("Invalid date" != $from_date && "Invalid date" != $to_date) {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date   = date('Y-m-d', strtotime($to_date));
                $where_simple["Date_Format(created_at,'%Y-%m-%d') >="] = $from_date;
                $where_simple["Date_Format(created_at,'%Y-%m-%d') <="] = $to_date;
            }
        }

        if($post_type != "") {
            $post_type = addslashes($post_type);
            $where_simple['google_posts_campaign.post_type'] = $post_type;
        }

        if($location_name != "") {
            // $where_simple['google_media_campaign.location_names'] = $location_name;
            $location_name = addslashes($location_name);
            $where_simple['google_posts_campaign.location_names like'] = "%\"name\":\"{$location_name}\"%";
        }

        if($searching != "") {
            $searching = addslashes($searching);
            $where_simple['google_posts_campaign.campaign_name like'] = "%" . $searching . "%";
        }

        $where_simple['google_posts_campaign.user_id'] = $this->user_id;
        $where_simple['google_posts_campaign.deleted'] = '0';

        $where  = array('where' => $where_simple);
        $select = array('google_posts_campaign.*');

        $table = 'google_posts_campaign';
        $info = $this->basic->get_data($table,$where,$select,$join='',$limit,$start,$order_by,$group_by='');

        // Gets total rows
        $total_rows_array = $this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
        $total_result = $total_rows_array[0]['total_rows'];

        // Prepares some vars
        for($i = 0; $i < count($info); $i++) {
            // Defines statuses
            $post_type = $info[$i]['post_type'];
            $status = $info[$i]['status'];
            if ('0' == $status) {
                $info[$i]['status'] = '<div style="min-width:120px;" class="text-danger"><i class="fas fa-hourglass-start"></i> ' . $this->lang->line("Pending") . '</div>';
            } elseif ('1' == $status) {
                $info[$i]['status'] = '<div style="min-width:120px;" class="text-info"><i class="fas fa-spinner"></i> ' . $this->lang->line("Processing") . '</div>';
            } elseif ('2' == $status) {
                $info[$i]['status'] = '<div style="min-width:120px;" class="text-success"><i class="fas fa-check-circle"></i> ' . $this->lang->line("Completed") . '</div>';
            }

            // Defines post types
            if ('cta_post' == $post_type) {
                $info[$i]['post_type'] = '<div style="min-width:70px !important;"><i class="fa fa-file-alt"></i> ' . $this->lang->line("CTA") . '</div>';
            }
            if ('event_post' == $post_type) {
                $info[$i]['post_type'] = '<div style="min-width:70px !important;"><i class="fa fa-image"></i> ' . $this->lang->line("EVENT") . '</div>';
            }
            if ('offer_post' == $post_type) {
                $info[$i]['post_type'] = '<div style="min-width:70px !important;"><i class="fa fa-video"></i> ' . $this->lang->line("OFFER") . '</div>';
            }

            // Defines post titles
            if ('cta_post' == $post_type) {
                $info[$i]['title'] = $info[$i]['summary'];
            }

            if ('event_post' == $post_type) {
                $info[$i]['title'] = $info[$i]['event_post_title'];
            }

            if ('offer_post' == $post_type) {
                $info[$i]['title'] = $info[$i]['summary'];
            }

            if ($info[$i]['schedule_time']) {
                $info[$i]['schedule_time'] = "<div style='min-width:120px !important;'>" . date("M j, y H:i", strtotime($info[$i]['schedule_time'])) . "</div>";
            } else {
                $info[$i]['schedule_time'] = "<div style='min-width:120px !important;' class='text-muted'><i class='fas fa-exclamation-circle'></i> " . $this->lang->line('Not Scheduled') . "</div>";
            }

            // visit post action
            if ('cta_post' == $post_type) {
                $cta_action_type = $info[$i]['cta_action_type'];
                if ('call' == strtolower($cta_action_type)) {

                }
                // $visit_post = "<a target='_blank' href='" . $info[$i]['post_url'] . "' data-toggle='tooltip' title='" . $this->lang->line("Visit Post") . "' class='btn btn-circle btn-outline-info'><i class='fas fa-hand-point-right'></i></a>";
            } elseif ('event_post' == $post_type) {
                // $visit_post = "<a data-toggle='tooltip' title='" . $this->lang->line("not published yet.") . "' class='btn btn-circle btn-light pointer text-muted'><i class='fas fa-hand-point-right'></i></a>";
            } elseif ('offer_post' == $post_type) {

            }

            // Report campaign action
            if ('2' == $status) {
                $reportPost = '<a class="btn btn-circle btn-outline-info campaign-report" data-toggle="tooltip" title="' . $this->lang->line("Campaign report") . '" data-post-id="' . $info[$i]['id'] . '" data-campaign-name="' . $info[$i]['campaign_name'] . '" data-toggle="modal" data-target="#campaign-report-modal" href="#"><i class="fas fa-eye"></i></a>';
            } else {
                $reportPost = '<a class="btn btn-circle btn-outline-info disabled" data-toggle="tooltip" title="' . $this->lang->line("Campaign report") . '" data-campaign-name="' . $info[$i]['campaign_name'] . '" href="#"><i class="fas fa-eye"></i></a>';
            }

            // Edit campaign action
            if ('0' == $status) {
                $editPost = '<a class="btn btn-circle btn-outline-warning" href="' . base_url('gmb/edit_post/') . $info[$i]['id'] . '" data-toggle="tooltip" title="' . $this->lang->line("Edit Campaign") . '"><i class="fas fa-edit"></i></a>';
            } else {
                $editPost = "<a class='btn btn-circle btn-light pointer text-muted' data-toggle='tooltip' title='" . $this->lang->line("Only pending and scheduled campaigns are editable") . "'><i class='fas fa-edit'></i></a>";
            }

            // Delete campaign action
            $deletePost = '<a class="btn btn-circle btn-outline-danger delete" data-toggle="tooltip" title="' . $this->lang->line("Delete Campaign") . '" id="' . $info[$i]['id'] . '" href="#"><i class="fas fa-trash-alt"></i></a>';

            // Action section started from here
            $account_count = 4;
            $action_width = ($account_count*47)+20;
            $info[$i]['actions'] = '<div class="dropdown d-inline dropright">
            <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i></button>
            <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';

            $info[$i]['actions'] .= $reportPost;
            $info[$i]['actions'] .= $editPost;
            $info[$i]['actions'] .= $deletePost;

            $info[$i]['actions'] .= "</div></div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start, $primary_key = "id");

        echo json_encode($data);
    }

    public function create_post()
    {
        $where = [
            'where' => [
                'user_id' => $this->user_id,
                'user_account_id' => $this->session->userdata('google_mybusiness_user_table_id'),
            ],
        ];

        $locations = $this->basic->get_data('google_business_locations', $where, ['id', 'location_display_name']);

        $prepared_locations = [];
        if (count($locations)) {
            foreach ($locations as $location) {
                $location_key = $location['id'] . '-' . $location['location_display_name'];
                $prepared_locations[$location_key] = $location['location_display_name'];
            }
        }

        $data['locations'] = $prepared_locations;
        $actionTypes = [
            '' => $this->lang->line('Please Select'),
            'BOOK' => 'BOOK',
            'ORDER' => 'ORDER',
            'SHOP' => 'SHOP',
            'LEARN_MORE' => 'LEARN_MORE',
            'SIGN_UP' => 'SIGN_UP',
            'CALL' => 'CALL'
        ];

        $data['actionTypes'] = $actionTypes;
        $data['time_zone'] = $this->_time_zone_list();
        $data['page_title'] = $this->lang->line('Create campaign');
        $data['title'] = $this->lang->line('Create campaign');
        $data['body'] = 'campaigns/create_post';
        $this->_viewcontroller($data);
    }

    public function campaign_report()
    {
        $this->ajax_check();

        $post_id = (int) $this->input->post('post_id');
        $where = [
            'where' => [
                'id' => $post_id,
                'user_id' => $this->user_id,
                'status' => '2',
                'deleted' => '0'
            ],
        ];

        $campaign = $this->basic->get_data('google_posts_campaign', $where, [], [], 1);
        $campaign_data = $campaign[0];

        $html = "";
        if(count($campaign) != 1) {
            $html .= '<div class="col-12 p-0">
                    <article class="article article-style-c shadow-none mb-0">
                        <div class="article-header">
                            <div class="article-image" data-background="'.base_url("assets/img/news/img01.jpg").'"" style="background-image: url(&quot;../assets/img/news/img01.jpg&quot;);">
                            </div>
                        </div>
                        <div class="article-details">
                            <div class="article-title">
                                <h6 class="text-center text-muted">'.$this->lang->line('No data found for this campaign').'</h6>
                            </div>
                        </div>
                    </article>
                </div>';

            echo $html; exit;
        }

        $media_url = $campaign_data['media_url'];
        $posted_locations = json_decode($campaign_data['response'],true);

        if(! $posted_locations) {
            $html .= '<div class="col-12 p-0">
                    <article class="article article-style-c shadow-none mb-0">
                        <div class="article-header">
                            <div class="article-image" data-background="'.base_url("assets/img/news/img01.jpg").'"" style="background-image: url(&quot;../assets/img/news/img01.jpg&quot;);">
                            </div>
                        </div>
                        <div class="article-details">
                            <div class="article-title">
                                <h6 class="text-center text-muted">'.$this->lang->line('We were unable to find data').'</h6>
                            </div>
                        </div>
                    </article>
                </div>';

            echo $html; exit;
        }

        $html = '
            <div class="col-12 p-0">
                <article class="article article-style-c shadow-none mb-0">
                    <div class="article-header">
                        <img src="'.$media_url.'" width="100%">
                    </div>
                    <div class="article-details">
                        <div class="article-category">
                            <a class="text-decoration-none" href="#">'.$this->lang->line("Post Type").'</a> <div class="bullet"></div> <a class="text-decoration-none text-primary" href="#">'.ucwords(str_replace('_',' ',$campaign_data['post_type'])).'</a>
                            <a data-toggle="tooltip" title="'.$this->lang->line("Created at").'" class="float-right text-decoration-none text-muted" href="#"><i class="far fa-clock"></i> '.date("M j, Y",strtotime($campaign_data['created_at'])).'</a>
                        </div>
                        <div class="article-title mb-0">
                            <h2><a data-toggle="" href="#" class="text-decoration-none">'.$campaign_data['summary'].'</a></h2>
                        </div>
                        <div class="article-user mt-2">
                            <div class="article-user-details">
                                <div class="user-detail-name">
                                    <a class="text-decoration-none" href="#">'.$this->lang->line('Posted to Locations').'</a>
                                </div>
                                <div class="text-job text-transform-none">';

            foreach ($posted_locations as $key => $value) {

                if(isset($value['error_message']) && !empty($value['error_message'])) {

                    $html .= '<a data-toggle="tooltip" title="'.$value['error_message'].'" href="#" class="btn btn-sm btn-danger mt-2 mb-2 mr-2 ml-0"><i class="fas fa-exclamation-circle"></i> '.$value['location_name'].'</a>';

                } else if(isset($value['searchUrl']) && !empty($value['searchUrl'])) {

                    $html .= '<a target="_BLANK" data-toggle="tooltip" title="Visit Post"  href="'.$value['searchUrl'].'" class="btn btn-sm btn-primary mt-2 mb-2 mr-2 ml-0"><i class="fas fa-check"></i> '.$value['location_name'].'</a>';
                }

            }
                                    
            $html .='</div>
                            </div>
                        </div>
                    </div>
                </article>
            </div><script>$("[data-toggle=\'tooltip\']").tooltip();</script>
        ';

        echo $html; exit;
    }

    public function upload_post_media()
    {
        $this->ajax_check();

        // Determines upload path
        $output_dir = APPPATH . '../upload/xerobiz/';

        // Starts uploading file
        if (isset($_FILES['xerobiz_file'])) {

            $error = $_FILES['xerobiz_file']['error'];
            if( $error ) {
                echo json_encode([$error]);
                exit;
            }

            if (is_uploaded_file($_FILES['xerobiz_file']['tmp_name'])) {

                $tmp_name = $_FILES['xerobiz_file']['tmp_name'];
                $post_fileName =$_FILES['xerobiz_file']['name'];
                $allowed_extensions = ['.jpg', '.jpeg', '.png', '.gif'];
                $extension = mb_substr($post_fileName, mb_strrpos($post_fileName, '.'));

                if(! in_array(strtolower($extension), $allowed_extensions)) {
                    $custom_error['jquery-upload-file-error'] = "File type not allowed.";
                    echo json_encode($custom_error);
                    exit();
                }

                $extension = mb_substr($post_fileName, mb_strrpos($post_fileName, '.'));
                $filename = 'image_' . $this->user_id . '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) . $extension;
                $destination = $output_dir .'/'. $filename;

                if (move_uploaded_file($tmp_name, $destination)) {
                    echo json_encode($filename);
                    exit;
                } else {
                    $custom_error['jquery-upload-file-error'] = "Something went wrong while uploading file";
                    echo json_encode($custom_error);
                    exit();
                }
            }
        }
        $custom_error['jquery-upload-file-error'] = "Please upload a valid file";
        echo json_encode($custom_error);
        exit;
    }

    public function delete_post_media()
    {
        // Checks ajax call
        $this->ajax_check();

        if( ! $_POST) {
            exit();
        }

        $output_dir = APPPATH . '../upload/xerobiz/';
        if(isset($_POST['op']) && $_POST['op'] == 'delete' && isset($_POST['name'])) {
            $fileName = is_string($_POST['name']) ? strip_tags($_POST['name']) : '';

            // Requires if somebody tries parent folder files
            $fileName = str_replace("..",".",$fileName);
            $filePath = $output_dir . $fileName;

            if (! is_dir($filePath) && file_exists($filePath)) {

                // Deletes the file
                unlink($filePath);

                echo json_encode([
                    'status' => true,
                    'message' => $this->lang->line('File has been deleted successfully'),
                ]);
                exit;
            }
        }

        echo json_encode([
            'status' => false,
            'message' => $this->lang->line('Bad request'),
        ]);
        exit;
    }

    public function create_campaign()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                $response['status'] = false;
                $response['errors'] = "You can not delete anything from admin account!!";
                echo json_encode($response);
                exit();
            }
        }

        $this->form_validation->set_rules('campaign_name', $this->lang->line('Campaign name'), 'required');
        $this->form_validation->set_rules('submitted_post_type', $this->lang->line('Campaign type'), 'required|in_list[cta_post,event_post,offer_post]');

        if ('cta_post' == $this->input->post('submitted_post_type')) {
            $this->form_validation->set_rules('cta_action_type', $this->lang->line('Action type'), 'required');
            if ('call' != strtolower($this->input->post('cta_action_type'))) {
                $this->form_validation->set_rules('cta_action_url', $this->lang->line('Action url'), 'required');
            }
        } elseif ('event_post' == $this->input->post('submitted_post_type')) {
            $this->form_validation->set_rules('event_post_title', $this->lang->line('Post title'), 'required');
            $this->form_validation->set_rules('start_date_time', $this->lang->line('Start date'), 'required');
            $this->form_validation->set_rules('end_date_time', $this->lang->line('End date'), 'required');
        } elseif ('offer_post' == $this->input->post('submitted_post_type')) {
            $this->form_validation->set_rules(
                'offer_coupon_code',
                $this->lang->line('Coupon code'),
                'regex_match[/^[a-zA-Z0-9]+$/]',
                [
                    'regex_match' => $this->lang->line('Coupon code must be alphanumeric characters'),
                ]
            );
            $this->form_validation->set_rules('offer_redeem_url', $this->lang->line('Redeem url'), 'required|valid_url');
        }

        if ('now' != $this->input->post('schedule_type')) {
            $this->form_validation->set_rules('schedule_time', $this->lang->line('Schedule time'), 'required');
            $this->form_validation->set_rules('time_zone', $this->lang->line('Timezone'), 'required');
        }

        $this->form_validation->set_rules('message', $this->lang->line('Summary'), 'required');
        $this->form_validation->set_rules('media_url', $this->lang->line('Media url'), 'required');

        if (false === $this->form_validation->run()) {
            $errors = $this->form_validation->error_array();
            $errors = str_replace(['<b>', '</b>'], '', $errors);
            echo json_encode([
                'status' => false,
                'errors' => $errors
            ]);
            exit;
        }


        $data = [];
        if ('cta_post' == $this->input->post('submitted_post_type')) {
            $data['cta_action_type'] = $this->input->post('cta_action_type', true);
            if ('call' != strtolower($this->input->post('cta_action_type'))) {
                $data['cta_action_url'] = $this->input->post('cta_action_url', true);
            }
        } elseif ('event_post' == $this->input->post('submitted_post_type')) {
            $data['event_post_title'] = $this->input->post('event_post_title', true);
            $data['start_date_time'] = $this->input->post('start_date_time', true);
            $data['end_date_time'] = $this->input->post('end_date_time', true);
        } elseif ('offer_post' == $this->input->post('submitted_post_type')) {
            $data['offer_coupon_code'] = $this->input->post('offer_coupon_code', true);
            $data['offer_redeem_url'] = $this->input->post('offer_redeem_url', true);
        }

        $data['user_id'] = $this->user_id;
        $data['post_type'] = $this->input->post('submitted_post_type', true);
        $data['user_account_id'] = $this->session->userdata('google_mybusiness_user_table_id');

        $location_data = (array) $this->input->post('location_name', true);

        if (empty($location_data)) {
            $message = $this->lang->line('Location name is required');
            echo json_encode([
                'status' => false,
                'message' => $message,
            ]);
            exit;
        }

        $location_names = [];
        $location_table_id = [];

        foreach ($location_data as $location) {
            list($location_id, $location_name) = explode('-', $location, 2);
            $location_table_id[] = $location_id;
            $location_names[] = [
                'id' => $location_id,
                'name' => $location_name,
                'status' => true,
            ];
        }

        //************************************************//
        $status=$this->_check_usage($module_id=15,$request=count($location_table_id));
        if($status=="2") 
        {
            echo json_encode([
                'status' => false,
                'message' => $this->lang->line("Module limit is over.")
            ]);               
            exit();
        }
        else if($status=="3") 
        {
            echo json_encode([
                'status' => false,
                'message' => $this->lang->line("Module limit is over.")
            ]);               
            exit();
        }
        //************************************************//

        $data['location_table_id'] = json_encode($location_table_id);
        $data['location_names'] = json_encode($location_names);
        $data['campaign_name'] = $this->input->post('campaign_name', true);
        $data['summary'] = $this->input->post('message', true);
        $data['media_type'] = 'PHOTO';
        $data['media_url'] = $this->input->post('media_url', true);
        $data['status'] = '0';
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->input->post('schedule_time')
            && ! empty($this->input->post('schedule_time'))
        ) {
            $data['schedule_type'] = null;
            $data['schedule_time'] = $this->input->post('schedule_time', true);
            $data['time_zone'] = $this->input->post('time_zone', true);
        } else {
            $data['schedule_type'] = 'now';
            $tz = '';
            if ($this->config->item('time_zone')) {
                $tz = $this->config->item('time_zone');
            } else {
                if (date_default_timezone_get()) {
                    $tz = date_default_timezone_get();
                }
            }

            $data['schedule_time'] = date('Y-m-d H:i:s');
            $data['time_zone'] = $tz;
        }

        $campaign_post_id = $this->session->userdata('gmb_campaign_post_id');
        if ($this->input->post('submitted_post_id')) {
            if ($campaign_post_id != $this->input->post('submitted_post_id')) {
                $message = $this->lang->line('It was a bad request!');
                echo json_encode([
                    'status' => false,
                    'message' => $message,
                ]);
                exit;
            }

            if ($this->basic->update_data('google_posts_campaign', ['id' => $campaign_post_id], $data)) {
                $this->session->unset_userdata('gmb_campaign_post_id');
                $message = $this->lang->line("Campaign updated successfully.");
                echo json_encode([
                    'status' => true,
                    'message' => $message,
                ]);
                exit;
            } else {
                $message = $this->lang->line("Something went wrong while submitting campaign.");
                echo json_encode([
                    'status' => false,
                    'message' => $message,
                ]);
            }
        }

        if ($this->basic->insert_data('google_posts_campaign', $data)) {
            $this->_insert_usage_log($module_id=303,$request=count($location_table_id)); 
            $message = $this->lang->line("Campaign submitted successfully.");
            echo json_encode([
                'status' => true,
                'message' => $message,
            ]);
            exit;
        } else {
            $message = $this->lang->line("Something went wrong while submitting campaign.");
            echo json_encode([
                'status' => false,
                'message' => $message,
            ]);
        }
    }

    public function edit_post($id)
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>";
                exit();
            }
        }

        $postId = (int) $id;

        $where = [
            'where' => [
                'id' => $postId,
                'user_id' => $this->user_id,
                'user_account_id' => $this->session->userdata('google_mybusiness_user_table_id'),
                'deleted' => '0',
                'status' => '0'
            ],
        ];
        $campaign = $this->basic->get_data('google_posts_campaign', $where);

        $where = [
            'where' => [
                'user_id' => $this->user_id,
                'user_account_id' => $this->session->userdata('google_mybusiness_user_table_id'),
            ],
        ];

        $locations = $this->basic->get_data('google_business_locations', $where, ['id', 'location_display_name']);

        if (! count($campaign) || ! count($locations)) {
            return $this->error_404();
            exit;
        }

        $prepared_locations = [];
        foreach ($locations as $location) {
            $location_key = $location['id'] . '-' . $location['location_display_name'];
            $prepared_locations[$location_key] = $location['location_display_name'];
        }

        $actionTypes = [
            '' => $this->lang->line('Please Select'),
            'BOOK' => 'BOOK',
            'ORDER' => 'ORDER',
            'SHOP' => 'SHOP',
            'LEARN_MORE' => 'LEARN_MORE',
            'SIGN_UP' => 'SIGN_UP',
            'GET_OFFER' => 'GET_OFFER',
            'CALL' => 'CALL'
        ];

        // Prepares selected locations and timezone
        $location_table_id = json_decode($campaign[0]['location_table_id'], true);
        $location_names = json_decode($campaign[0]['location_names'], true);
        $selected_timezone = isset($campaign[0]['time_zone']) ? $campaign[0]['time_zone'] : null;

        $selected_locations = [];
        foreach($location_table_id as $location_id) {
            $first_element = array_shift($location_names);
            $location_name = $first_element['name'];
            $location_key = $location_id . '-' . $location_name;
            $selected_locations[] = $location_key;
        }

        $this->session->set_userdata('gmb_campaign_post_id', $campaign[0]['id']);
        $data['campaign'] = isset($campaign[0]) ? $campaign[0] : [];
        $data['time_zone'] = $this->_time_zone_list();
        $data['locations'] = $prepared_locations;
        $data['selected_locations'] = $selected_locations;
        $data['actionTypes'] = $actionTypes;
        $data['page_title'] = $this->lang->line('Edit campaign');
        $data['title'] = $this->lang->line('Edit campaign');
        $data['body'] = 'campaigns/edit_post';
        $this->_viewcontroller($data);
    }

    public function delete_post()
    {
        $this->ajax_check();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                $response['status'] = false;
                $response['errors'] = "You can not delete anything from admin account!!";
                echo json_encode($response);
                exit();
            }
        }

        $this->form_validation->set_rules('id', $this->lang->line('Post ID'), 'required');

        if (false === $this->form_validation->run()) {
            $message = $this->lang->line("Something went wrong while deleting post!");
            if ($this->form_validation->error('id')) {
                $message = $this->lang->line('It was a BAD request!');
            }

            echo json_encode([
                'status' => false,
                'message' => $message
            ]);
            exit;
        }

        $where = [
            'id' => $this->input->post('id'),
            'user_id' => $this->user_id
        ];

        $post_info = $this->basic->get_data('google_posts_campaign',['where'=>$where],['location_table_id','status']);
        $locations_array = isset($post_info[0]['location_table_id']) ? json_decode($post_info[0]['location_table_id'],true) : 0;
        $number_of_locations = count($locations_array);
        $posting_status = isset($post_info[0]['status']) ? $post_info[0]['status'] : 2;
        if($posting_status != 2)
            $this->_delete_usage_log($module_id=15,$request=$number_of_locations);

        if ($this->basic->delete_data('google_posts_campaign', $where)) {
            echo json_encode([
               'status' => true
            ]);
            exit;
        } else {
            echo json_encode([
                'status' => false
            ]);
            exit;
        }
    }

    public function post_insights()
    {
        // accounts/107745512734031207626/locations/14692206365244175995/localPosts/1247337822086088354
        // Check if is an ajax request
        $this->ajax_check();

        $post_name = $this->input->post('post_name', true);

        if (! preg_match('@^accounts/[0-9]+/locations/[0-9]+/localPosts/[0-9]+$@', $post_name)) {
            echo json_encode([
                'status' => false,
                'message' => $this->lang->line('Bad request')
            ]);
            exit;
        }

        // Inits google_my_business
        $params['gmb_user_table_id'] = $this->session->userdata('google_mybusiness_user_table_id');
        $params['redirectUri'] = '';
        $this->load->library('google_my_business', $params);
        $gmb = $this->google_my_business;

        // Initial vars
        $error = '';
        $response = '';
        $start_date_time = '';
        $end_date_time = '';

        // if ($_POST) {
        //     $start_date_time_value = $this->input->post('from_date', true);
        //     $end_date_time_value = $this->input->post('to_date', true);
        //     $start_date_time = new DateTime($start_date_time_value);
        //     $end_date_time = new DateTime($end_date_time_value);
        //
        //     if ($end_date_time <= $start_date_time) {
        //         $start_date_time = new DateTime();
        //         $start_date_time->modify('-2 months');
        //         $end_date_time = new DateTime();
        //     }
        //
        //     if ($end_date_time > $start_date_time) {
        //         $too_much_interval = $start_date_time->diff($end_date_time);
        //         $days = $too_much_interval->format('%R%a');
        //         $date_time_difference = (int) $days;
        //
        //         if ($date_time_difference > 186) {
        //             $start_date_time = new DateTime($start_date_time_value);
        //             $end_date_time = new DateTime($start_date_time_value);
        //             $interval = new DateInterval('P6M');
        //             $end_date_time->add($interval);
        //         }
        //     }
        // } else {
        //     $start_date_time = new DateTime();
        //     $start_date_time->modify('-5 days');
        //     $end_date_time = new DateTime();
        // }

        // Prepares date
        $start_date_time = new DateTime();
        $start_date_time->modify('-2 months');
        $end_date_time = new DateTime();

        if (is_object($gmb)) {
            $location_name = explode('/localPosts/', $post_name, 2);

            $post_names = [
                $post_name
            ];

            try {
                $response = $gmb->postsInsightsBasicMetric(
                    $location_name[0],
                    $post_names,
                    'ALL',
                    [
                        'AGGREGATED_DAILY',
                    ],
                    $start_date_time,
                    $end_date_time
                );
            } catch (Google_Service_Exception $e) {
                $error = $e->getMessage();
            } catch (Google_Exception $e) {
                $error = $e->getMessage();
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }

            // @TODO
            // Log or send email the error message
        }

        if (! empty($error)) {
            echo json_encode([
                'status' => false,
                'message' => $error,
            ]);
            exit;
        }

        $metrics = [
            'QUERIES_DIRECT',
            'QUERIES_INDIRECT',
            'QUERIES_CHAIN',
            'VIEWS_MAPS',
            'VIEWS_SEARCH',
            'ACTIONS_WEBSITE',
            'ACTIONS_PHONE',
            'ACTIONS_DRIVING_DIRECTIONS',
            'PHOTOS_VIEWS_MERCHANT',
            'PHOTOS_VIEWS_CUSTOMERS',
            'PHOTOS_COUNT_MERCHANT',
            'PHOTOS_COUNT_CUSTOMERS',
            'LOCAL_POST_VIEWS_SEARCH',
            'LOCAL_POST_ACTIONS_CALL_TO_ACTION',
        ];

        // Prepares array based on metrics
        $post_insights = [];
        if (is_object($response)) {
            foreach ($response as $metric) {
                if (is_array($metric->getMetricValues())) {
                    foreach ($metric->getMetricValues() as $metricValue) {

                        $metricType = $metricValue->getMetric();

                        if (! empty($metricType) && 'LOCAL_POST_VIEWS_SEARCH' == $metricType ) {
                            foreach ($metricValue->getDimensionalValues() as $dimensionalValue) {
                                $startDateTime = '';
                                $value = $dimensionalValue->getValue();
                                if (is_object($dimensionalValue->getTimeDimension())) {
                                    $timeRange = $dimensionalValue
                                        ->getTimeDimension()
                                        ->getTimeRange();
                                    if (is_object($timeRange)) {
                                        $startDateTime = $timeRange->getStartTime();
                                    }
                                }

                                $stringDateTime = date('M j, Y', strtotime($startDateTime));

                                $post_insights[$metricType]['date'][] = $stringDateTime;
                                $post_insights[$metricType]['value'][] = $value ? $value : 0;
                            }
                        }
                    }
                }
            }
        }

        echo json_encode([
            'status' => true,
            'data' => isset($post_insights['LOCAL_POST_VIEWS_SEARCH']) ? json_encode($post_insights['LOCAL_POST_VIEWS_SEARCH']) : []
        ]);
        exit;
    }

    ############################################################
    ## Media related methods
    ############################################################

    public function media_campaigns()
    {
        $where = [
            'where' => [
                'user_id' => $this->user_id,
                'user_account_id' => $this->session->userdata('google_mybusiness_user_table_id'),
            ],
        ];

        $locations = $this->basic->get_data('google_business_locations', $where, ['location_display_name']);

        $data['locations'] = $locations;        
        $data['media_categories'] = $this->media_categories;        
        $data['page_title'] = $this->lang->line('Media campaigns');
        $data['title'] = $this->lang->line('Media campaigns');
        $data['body'] = 'campaigns/media_list';
        $this->_viewcontroller($data);
    }

    public function media_list_data() 
    {
        $this->ajax_check();

        $media_category   = trim($this->input->post("media_category", true));
        $location_name   = trim($this->input->post("location_name", true));
        $searching       = trim($this->input->post("searching", true));
        $post_date_range = $this->input->post("post_date_range", true);

        $display_columns = array("#", 'id', 'campaign_name', 'media_category', 'media_type', 'actions', 'status', 'schedule_time', 'error');
        $search_columns  = array('campaign_name','media_category','schedule_time');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by = $sort . " " . $order;

        $where_simple = array();

        if(! empty($post_date_range)) {
            $exp        = explode('|', $post_date_range);
            $from_date  = isset($exp[0]) ? $exp[0] : "";
            $to_date    = isset($exp[1]) ? $exp[1] : "";

            if("Invalid date" != $from_date && "Invalid date" != $to_date) {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date   = date('Y-m-d', strtotime($to_date));
                $where_simple["Date_Format(created_at,'%Y-%m-%d') >="] = $from_date;
                $where_simple["Date_Format(created_at,'%Y-%m-%d') <="] = $to_date;
            }
        }

        if($media_category != "") {
            $media_category = addslashes($media_category);
            $where_simple['google_media_campaign.media_category'] = $media_category;
        }

        if($location_name != "") {
            // $where_simple['google_media_campaign.location_names'] = $location_name;
            $location_name = addslashes($location_name);
            $where_simple['google_media_campaign.location_names like'] = "%\"name\":\"{$location_name}\"%";
        }

        if($searching != "") {
            $searching = addslashes($searching);
            $where_simple['google_media_campaign.campaign_name like'] = "%" . $searching . "%";
        }

        $where_simple['google_media_campaign.user_id'] = $this->user_id;
        $where_simple['google_media_campaign.deleted'] = '0';

        $where  = array('where' => $where_simple);
        $select = array('google_media_campaign.*');

        $table = 'google_media_campaign';
        $info = $this->basic->get_data($table,$where,$select,$join='',$limit,$start,$order_by,$group_by='');

        // Gets total rows
        $total_rows_array = $this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
        $total_result = $total_rows_array[0]['total_rows'];

        // Prepares some vars
        for($i = 0; $i < count($info); $i++) {

            // Handles media type
            if ('PHOTO' == $info[$i]['media_type']) {
                $info[$i]['media_type'] = '<div class="text-info"><i class="fas fa-image"></i> ' . $info[$i]['media_type'] . '</div>';
            } elseif ('VIDEO' == $info[$i]['media_type']) {
                $info[$i]['media_type'] = '<div style="min-width:120px;" class="text-primary"><i class="fas fa-video"></i> ' . $info[$i]['media_type'] . '</div>';
            }

            // Handles statuses
            $status = $info[$i]['status'];
            if ('0' == $status) {
                $info[$i]['status'] = '<div style="min-width:120px;" class="text-danger"><i class="fas fa-hourglass-start"></i> ' . $this->lang->line("Pending") . '</div>';
            } elseif ('1' == $status) {
                $info[$i]['status'] = '<div style="min-width:120px;" class="text-info"><i class="fas fa-spinner"></i> ' . $this->lang->line("Processing") . '</div>';
            } elseif ('2' == $status) {
                $info[$i]['status'] = '<div style="min-width:120px;" class="text-success"><i class="fas fa-check-circle"></i> ' . $this->lang->line("Completed") . '</div>';
            }

            if ($info[$i]['schedule_time']) {
                $info[$i]['schedule_time'] = "<div style='min-width:120px !important;'>" . date("M j, y H:i", strtotime($info[$i]['schedule_time'])) . "</div>";
            } else {
                $info[$i]['schedule_time'] = "<div style='min-width:120px !important;' class='text-muted'><i class='fas fa-exclamation-circle'></i> " . $this->lang->line('Not Scheduled') . "</div>";
            }

            // Report campaign action
            if ('2' == $status) {
                $reportPost = '<a class="btn btn-circle btn-outline-info campaign-report" data-toggle="tooltip" title="' . $this->lang->line("Campaign report") . '" data-post-id="' . $info[$i]['id'] . '" data-campaign-name="' . $info[$i]['campaign_name'] . '" data-toggle="modal" data-target="#campaign-report-modal" href="#"><i class="fas fa-eye"></i></a>';
            } else {
                $reportPost = '<a class="btn btn-circle btn-outline-info disabled" data-toggle="tooltip" title="' . $this->lang->line("Campaign report") . '" data-campaign-name="' . $info[$i]['campaign_name'] . '" href="#"><i class="fas fa-eye"></i></a>';
            }

            // Edit campaign action
            if ('0' == $status) {
                $editPost = '<a class="btn btn-circle btn-outline-warning" href="' . base_url('gmb/edit_media_campaign/') . $info[$i]['id'] . '" data-toggle="tooltip" title="' . $this->lang->line("Edit Campaign") . '"><i class="fas fa-edit"></i></a>';
            } else {
                $editPost = "<a class='btn btn-circle btn-light pointer text-muted' data-toggle='tooltip' title='" . $this->lang->line("Only pending and scheduled campaigns are editable") . "'><i class='fas fa-edit'></i></a>";
            }

            // Delete campaign action
            $deletePost = '<a class="btn btn-circle btn-outline-danger delete" data-toggle="tooltip" title="' . $this->lang->line("Delete Campaign") . '" id="' . $info[$i]['id'] . '" href="#"><i class="fas fa-trash-alt"></i></a>';

            // Action section started from here
            $account_count = 4;
            $action_width = ($account_count*47)+20;
            $info[$i]['actions'] = '<div class="dropdown d-inline dropright">
            <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i></button>
            <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';

            $info[$i]['actions'] .= $reportPost;
            $info[$i]['actions'] .= $editPost;
            $info[$i]['actions'] .= $deletePost;

            $info[$i]['actions'] .= "</div></div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start, $primary_key = "id");

        echo json_encode($data);
    }

    public function create_media_campaign() 
    {
        $where = [
            'where' => [
                'user_id' => $this->user_id,
                'user_account_id' => $this->session->userdata('google_mybusiness_user_table_id'),
            ],
        ];

        $locations = $this->basic->get_data('google_business_locations', $where, ['id', 'location_display_name']);

        $prepared_locations = [];
        if (count($locations)) {
            foreach ($locations as $location) {
                $location_key = $location['id'] . '-' . $location['location_display_name'];
                $prepared_locations[$location_key] = $location['location_display_name'];
            }
        }

        $data['locations'] = $prepared_locations;
        $data['media_categories'] = $this->media_categories;
        $data['time_zone'] = $this->_time_zone_list();
        $data['page_title'] = $this->lang->line('Create media campaign');
        $data['title'] = $this->lang->line('Create media campaign');
        $data['body'] = 'campaigns/create_media_campaign';
        $this->_viewcontroller($data);
    }

    public function report_media_campaign()
    {
        $this->ajax_check();

        $post_id = (int) $this->input->post('post_id');
        $where = [
            'where' => [
                'id' => $post_id,
                'user_id' => $this->user_id,
                'status' => '2',
                'deleted' => '0'
            ],
        ];

        $campaign = $this->basic->get_data('google_media_campaign', $where, [], [], 1);
        $campaign_data = $campaign[0];

        $html = "";
        if(count($campaign) != 1) {
            $html .= '<div class="col-12 p-0">
                    <article class="article article-style-c shadow-none mb-0">
                        <div class="article-header">
                            <div class="article-image" data-background="'.base_url("assets/img/news/img01.jpg").'"" style="background-image: url(&quot;../assets/img/news/img01.jpg&quot;);">
                            </div>
                        </div>
                        <div class="article-details">
                            <div class="article-title">
                                <h6 class="text-center text-muted">'.$this->lang->line('No data found for this campaign').'</h6>
                            </div>
                        </div>
                    </article>
                </div>';

            echo $html; exit;
        }

        $media_url = $campaign_data['media_url'];
        $media_type = $this->find_file_type($media_url);
        $posted_locations = json_decode($campaign_data['response'], true);

        $media = '';
        if ('image' == $media_type) {
            $media = '<img src="'.$media_url.'" width="100%">';
        } elseif ('video' == $media_type) {
            $media = '<video controls width="100%" height="auto">';
            $media .= '<source src="' . $media_url . '"';
            $media .= 'Your browser does not support the video tag.';
            $media .= '</video>';
        }

        $html = '
            <div class="col-12 p-0">
                <article class="article article-style-c shadow-none mb-0">
                    <div class="">' . $media . '</div>
                    <div class="article-details">
                        <div class="article-category">
                            <a class="text-decoration-none" href="#">' . $this->lang->line("Media category") . '</a> <div class="bullet"></div> <a class="text-decoration-none text-primary" href="#">' .ucwords(str_replace('_', ' ', $campaign_data['media_type'])) . '</a>
                            <a data-toggle="tooltip" title="' . $this->lang->line("Created at") . '" class="float-right text-decoration-none text-muted" href="#"><i class="far fa-clock"></i> ' . date("M j, Y",strtotime($campaign_data['created_at'])) . '</a>
                        </div>
                        <div class="article-title mb-0">
                            <h2><a data-toggle="" href="#" class="text-decoration-none">' . $campaign_data['media_description'] . '</a></h2>
                        </div>
                        <div class="article-user mt-2">
                            <div class="article-user-details">
                                <div class="user-detail-name">
                                    <a class="text-decoration-none" href="#">' . $this->lang->line('Posted to Locations') . '</a>
                                </div>
                                <div class="text-job text-transform-none">';

            foreach ($posted_locations as $key => $value) {

                if(isset($value['error_message']) && !empty($value['error_message'])) {

                    $html .= '<a data-toggle="tooltip" title="'.$value['error_message'].'" href="#" class="btn btn-sm btn-danger mt-2 mb-2 mr-2 ml-0"><i class="fas fa-exclamation-circle"></i> '.$value['location_name'].'</a>';

                } else if(isset($value['googleUrl']) && !empty($value['googleUrl'])) {

                    $html .= '<a target="_BLANK" data-toggle="tooltip" title="Visit Post"  href="'.$value['googleUrl'].'" class="btn btn-sm btn-primary mt-2 mb-2 mr-2 ml-0"><i class="fas fa-check"></i> '.$value['location_name'].'</a>';
                }

            }
                                    
            $html .='</div>
                            </div>
                        </div>
                    </div>
                </article>
            </div><script>$("[data-toggle=\'tooltip\']").tooltip();</script>
        ';

        echo $html; exit;
    }

    public function media_campaign_upload()
    {
        $this->ajax_check();

        // Determines upload path
        $upload_dir = APPPATH . '../upload/xerobiz/media/';

        // Starts uploading file
        if (isset($_FILES['media_file'])) {

            $error = $_FILES['media_file']['error'];
            if( $error ) {
                echo json_encode([$error]);
                exit;
            }

            if (is_uploaded_file($_FILES['media_file']['tmp_name'])) {
                $tmp_name = $_FILES['media_file']['tmp_name'];
                $post_filename = $_FILES["media_file"]["name"];
                $extension = mb_substr($post_filename, mb_strrpos($post_filename, '.'));

                if(! in_array(strtolower($extension), $this->allowed_mime_types)) {
                    echo json_encode([
                        'status' => false,
                        'message' => $this->lang->line('File type not allowed'),
                    ]);
                    exit();
                }

                $filename = 'media_' . $this->user_id . '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) . $extension;
                $destination = $upload_dir . $filename;

                if (move_uploaded_file($tmp_name, $destination)) {

                    // Changes the file permission
                    chmod($destination, 0644);

                    // Stores the filename in session so that 
                    // we can make a simple security check on deleting this file
                    $this->session->set_userdata('gmb_media_file_uploaded_name', $filename);

                    echo json_encode([
                        'status' => true,
                        'filename' => $filename,
                    ]);
                    exit;
                } else {
                    echo json_encode([
                        'status' => false,
                        'message' => $this->lang->line('Something went wrong while uploading file'),
                    ]);
                    exit();
                }
            }
        }

        echo json_encode([
            'status' => false,
            'message' => $this->lang->line('Something went wrong while uploading file'),
        ]);
        exit();
    }

    public function delete_media_campaign_upload()
    {
        // Checks ajax call
        $this->ajax_check();

        if( ! $_POST) {
            exit();
        }

        $upload_dir = APPPATH . '../upload/xerobiz/media/';

        if(isset($_POST['op']) && $_POST['op'] == 'delete' && isset($_POST['name'])) {
            $stored_filename = $this->session->userdata('gmb_media_file_uploaded_name');
            $filename = (isset($_POST['name']['filename']) && is_string($_POST['name']['filename']))
                ? strip_tags($_POST['name']['filename'])
                : '';

            if ($filename != $stored_filename) {
                echo json_encode([
                    'status' => false,
                    'message' => $this->lang->line('Bad request'),
                ]);
                exit;
            }

            $filePath = $upload_dir . $stored_filename;

            if (! is_dir($filePath) && file_exists($filePath)) {
                
                // Deletes the file
                unlink($filePath);

                // Unsets the filename reference
                $this->session->unset_userdata('gmb_media_file_uploaded_name');

                echo json_encode([
                    'status' => true,
                    'message' => $this->lang->line('File has been deleted successfully'),
                ]);
                exit;
            }
        }

        echo json_encode([
            'status' => false,
            'message' => $this->lang->line('Bad request'),
        ]);
        exit;
    }

    public function is_media_category_array()
    {
        $media_category = isset($_POST['media_category']) 
            ? $_POST['media_category'] 
            : null;

        if (! $media_category) {
            $this->form_validation->set_message(
                'is_media_category_array', 
                $this->lang->line("Media category is required")
            );

            return false;
        }

        if (! array_key_exists($media_category, $this->media_categories)) {
            $this->form_validation->set_message(
                'is_media_category_array', 
                $this->lang->line("Media category is invalid")
            );

            return false;
        }

        return true;
    }

    public function handle_media_campaign()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                $response['status'] = false;
                $response['errors'] = "You can not delete anything from admin account!!";
                echo json_encode($response);
                exit();
            }
        }

        $this->form_validation->set_rules('campaign_name', $this->lang->line('Campaign name'), 'required');
        $this->form_validation->set_rules(
            'media_category', 
            $this->lang->line('Media category'), 
            'callback_is_media_category_array'
        );
        $this->form_validation->set_rules('media_description', $this->lang->line('Media description'), 'required');

        if ('now' != $this->input->post('schedule_type')) {
            $this->form_validation->set_rules('schedule_time', $this->lang->line('Schedule time'), 'required');
            $this->form_validation->set_rules('time_zone', $this->lang->line('Timezone'), 'required');
        }


        if (false === $this->form_validation->run()) {
            $errors = $this->form_validation->error_array();
            $errors = str_replace(['<b>', '</b>'], '', $errors);

            echo json_encode([
                'status' => false,
                'errors' => $errors
            ]);
            exit;
        }

        // Gets the reference of uploaded file from session
        $media_file = $this->session->userdata('gmb_media_file_uploaded_name');

        // Shows error if the request is for creating media campaign and no media file
        if (! $this->input->post('submitted_post_id') && ! $media_file) {
            echo json_encode([
                'status' => false,
                'errors' => ['media_file' => $this->lang->line('Media file is required')],
            ]);
            exit;
        }

        $selected_locations = (array) $this->input->post('location_name', true);

        if (empty($selected_locations)) {
            $message = $this->lang->line('Location name is required');
            echo json_encode([
                'status' => false,
                'message' => $message,
            ]);
            exit;
        }

        $location_names = [];
        $location_table_id = [];

        foreach ($selected_locations as $location) {
            list($location_id, $location_name) = explode('-', $location, 2);
            $location_table_id[] = $location_id;
            $location_names[] = [
                'id' => $location_id,
                'name' => $location_name,
                'status' => true,
            ];
        }

        //************************************************//
        $status=$this->_check_usage($module_id=20,$request=count($location_table_id));
        if($status=="2") 
        {
            echo json_encode([
                'status' => false,
                'message' => $this->lang->line("Module limit is over.")
            ]);               
            exit();
        }
        else if($status=="3") 
        {
            echo json_encode([
                'status' => false,
                'message' => $this->lang->line("Module limit is over.")
            ]);               
            exit();
        }
        //************************************************//

        // Prepares data for saving into database
        $data = [];
        $data['user_id'] = $this->user_id;
        $data['user_account_id'] = $this->session->userdata('google_mybusiness_user_table_id');
        $data['campaign_name'] = $this->input->post('campaign_name', true);
        $data['media_category'] = $this->input->post('media_category', true);
        $data['media_description'] = $this->input->post('media_description', true);
        $data['location_names'] = json_encode($location_names);
        $data['location_table_id'] = json_encode($location_table_id);
        $data['media_description'] = $this->input->post('media_description', true);
        $data['status'] = '0';
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->input->post('schedule_time')
            && ! empty($this->input->post('schedule_time'))
        ) {
            $data['schedule_type'] = null;
            $data['schedule_time'] = $this->input->post('schedule_time', true);
            $data['time_zone'] = $this->input->post('time_zone', true);
        } else {
            $data['schedule_type'] = 'now';
            $tz = '';
            if ($this->config->item('time_zone')) {
                $tz = $this->config->item('time_zone');
            } else {
                if (date_default_timezone_get()) {
                    $tz = date_default_timezone_get();
                }
            }

            $data['schedule_time'] = date('Y-m-d H:i:s');
            $data['time_zone'] = $tz;
        }

        
        if ($this->input->post('submitted_post_id')) {
            $campaign_post_id = $this->session->userdata('gmb_campaign_post_id');
            if (md5($campaign_post_id) != $this->input->post('submitted_post_id')) {
                $message = $this->lang->line('It was a bad request!');
                echo json_encode([
                    'status' => false,
                    'message' => $message,
                ]);
                exit;
            }

            // Updates media data if there is any new media file
            if ($media_file) {
                $media_data = $this->handle_media_data($media_file);

                $data['media_url'] = $media_data['media_url'];
                $data['media_type'] = $media_data['media_type'];
            }

            if ($this->basic->update_data('google_media_campaign', ['id' => $campaign_post_id], $data)) {
                $this->session->unset_userdata('gmb_campaign_post_id');
                $message = $this->lang->line("Campaign updated successfully.");
                echo json_encode([
                    'status' => true,
                    'message' => $message,
                ]);
                exit;
            } else {
                $message = $this->lang->line("Something went wrong while submitting campaign.");
                echo json_encode([
                    'status' => false,
                    'message' => $message,
                ]);
            }

            exit;
        }

        if (! $this->input->post('submitted_post_id')) {
            if ($media_file) {
                $media_data = $this->handle_media_data($media_file);

                $data['media_url'] = $media_data['media_url'];
                $data['media_type'] = $media_data['media_type'];
            }

            if ($this->basic->insert_data('google_media_campaign', $data)) {

                // Unsets uploaded file reference on campaign creation
                $this->session->unset_userdata('gmb_media_file_uploaded_name');

                $this->_insert_usage_log($module_id=304,$request=count($location_table_id));


                $message = $this->lang->line("Campaign submitted successfully.");
                echo json_encode([
                    'status' => true,
                    'message' => $message,
                ]);
                exit;
            } else {
                $message = $this->lang->line("Something went wrong while submitting campaign.");
                echo json_encode([
                    'status' => false,
                    'message' => $message,
                ]);
            }
        }
    }

    public function edit_media_campaign($id)
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>";
                exit();
            }
        }

        $postId = (int) $id;

        $where = [
            'where' => [
                'id' => $postId,
                'user_id' => $this->user_id,
                'user_account_id' => $this->session->userdata('google_mybusiness_user_table_id'),
                'deleted' => '0',
                'status' => '0'
            ],
        ];
        $campaign = $this->basic->get_data('google_media_campaign', $where);

        $where = [
            'where' => [
                'user_id' => $this->user_id,
                'user_account_id' => $this->session->userdata('google_mybusiness_user_table_id'),
            ],
        ];

        $locations = $this->basic->get_data('google_business_locations', $where, ['id', 'location_display_name']);

        if (! count($campaign) || ! count($locations)) {
            return $this->error_404();
            exit;
        }

        $prepared_locations = [];
        foreach ($locations as $location) {
            $location_key = $location['id'] . '-' . $location['location_display_name'];
            $prepared_locations[$location_key] = $location['location_display_name'];
        }

        $this->session->set_userdata('gmb_campaign_post_id', $campaign[0]['id']);

        // Prepares selected locations
        $location_table_id = json_decode($campaign[0]['location_table_id'], true);
        $location_names = json_decode($campaign[0]['location_names'], true);

        $selected_locations = [];
        foreach($location_table_id as $location_id) {
            $first_element = array_shift($location_names);
            $location_name = $first_element['name'];
            $location_key = $location_id . '-' . $location_name;
            $selected_locations[] = $location_key;
        }

        $data['campaign'] = isset($campaign[0]) ? $campaign[0] : [];
        $data['time_zone'] = $this->_time_zone_list();
        $data['locations'] = $prepared_locations;
        $data['selected_locations'] = $selected_locations;
        $data['media_categories'] = $this->media_categories;
        $data['page_title'] = $this->lang->line('Edit media campaign');
        $data['title'] = $this->lang->line('Edit media campaign');
        $data['body'] = 'campaigns/edit_media_campaign';
        $this->_viewcontroller($data);
    }

    public function delete_media_campaign()
    {
        $this->ajax_check();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                $response['status'] = false;
                $response['errors'] = "You can not delete anything from admin account!!";
                echo json_encode($response);
                exit();
            }
        }

        $this->form_validation->set_rules('id', $this->lang->line('Post ID'), 'required');

        if (false === $this->form_validation->run()) {
            $message = $this->lang->line("Something went wrong while deleting post");
            if ($this->form_validation->error('id')) {
                $message = $this->lang->line('It was a bad request');
            }

            echo json_encode([
                'status' => false,
                'message' => $message
            ]);
            exit;
        }

        $where = [
            'id' => $this->input->post('id'),
            'user_id' => $this->user_id
        ];

        $post_info = $this->basic->get_data(
            'google_media_campaign', 
            ['where' => $where], 
            ['location_table_id','status']
        );

        $locations_array = isset($post_info[0]['location_table_id']) 
            ? json_decode($post_info[0]['location_table_id'],true) 
            : 0;

        $number_of_locations = count($locations_array);
        $posting_status = isset($post_info[0]['status']) ? $post_info[0]['status'] : 2;

        if($posting_status != 2)
            $this->_delete_usage_log($module_id=20, $request=$number_of_locations);

        if ($this->basic->delete_data('google_media_campaign', $where)) {
            echo json_encode([
               'status' => true
            ]);
            exit;
        } else {
            echo json_encode([
                'status' => false
            ]);
            exit;
        }
    }

    private function get_file_permission($filename) 
    {
        $numeric_perm = fileperms($filename);
        $octal_perm = sprintf("%o", $numeric_perm);

        return substr($octal_perm, -4);
    }

    private function get_media_type($filepath) 
    {
        $media_type = $this->find_file_type($filepath);

        $type = ('image' == $media_type)
            ? 'PHOTO'
            : (('video' == $media_type) 
                ? 'VIDEO'
                : null);

        return $type;
    }

    private function handle_media_data($media_file) 
    {
        $media_url = base_url("upload/xerobiz/media/{$media_file}");
        // $filepath = APPPATH . "../upload/xerobiz/media/{$media_file}";
        $media_type = $this->get_media_type($media_file);

        $data = [];
        $data['media_type'] = $media_type;
        $data['media_url'] = $media_url;

        return $data;
    }

    private function find_file_type($filename) {

        $allowed_img_extension = [
            // jpeg or jpg images
            '.jpeg',
            '.jpg', 

            // png images
            '.png',

            // gif images
            '.gif',
        
        ];

        $allowed_vid_extension = [
            // flv videos
            '.flv',

            // ogv or ogg videos
            '.ogg',

            // webm videos
            '.webm',

            // 3gp or mts videos 
            '.3gpp',

            // mp4 videos 
            '.mp4',

            // mkv videos
            '.mkv',

            // mpeg videos
            '.mpeg',

            // mov videos
            '.mov',

            // avi videos
            '.avi', 

            // wmv videos 
            '.wmv',

            // m4v videos
            '.m4v',
        ];

        $extension = mb_substr($filename, mb_strrpos($filename, '.'));

        $foundImg = array_search($extension, $allowed_img_extension);
        $foundVid = array_search($extension, $allowed_vid_extension);

        return (false !== $foundImg) ? 'image' : ((false !== $foundVid) ? 'video' : null);
    }

    ############################################################
    ## RSS related methods
    ############################################################

    public function rss()
    {        
        $settings_data = $this->basic->get_data("google_rss_feed_posting",array("where"=>array("user_id"=>$this->user_id)),'','','','','feed_name asc');
        
        $data['body'] = 'rss/feed_list';
        $data['page_title'] = $this->lang->line('RSS Auto-Posting');   
        $data['settings_data'] = $settings_data;
        $data["feed_types"] = $this->basic->get_enum_values("google_rss_feed_posting","feed_type");

        $this->_viewcontroller($data); 
    }    

    public function add_feed_action()
    {
        $this->ajax_check();

        $feed_name=$this->input->post('feed_name',true);
        $feed_url=$this->input->post('feed_url',true);

        if($this->basic->is_exist("google_rss_feed_posting",array("feed_url"=>$feed_url,"user_id"=>$this->user_id),'id'))
        {
            $error_message=$this->lang->line("This feed URL has been already added.");
            echo json_encode(array('status'=>'0','message'=>$error_message));
            exit();
        }

        $this->load->library('rss_feed');
        $feed = $this->rss_feed->getFeed($feed_url);

        if(! isset($feed['success']) || $feed['success'] != '1') {
            $error_message = isset($feed['error_message'])?$feed['error_message']:$this->lang->line("Something went wrong, please try again.");
            echo json_encode(array('status'=>'0','message'=>$error_message));
            exit();
        }

        $datetime=date("Y-m-d H:i:s");
        date_default_timezone_set('Europe/Dublin'); // operating in GMT

        $last_pub_date = "";
        $last_pub_title = "";
        $last_pub_url = "";
        $element_list  = isset($feed['element_list']) ? $feed['element_list'] : array();

        foreach ($element_list as $key => $value) {
            if($value['pubDate']=="") continue;
            if($last_pub_date=="" || (strtotime($value['pubDate']) > strtotime($last_pub_date)))
            {
                $last_pub_date = isset($value['pubDate']) ? $value['pubDate'] : "";
                $last_pub_date = date("Y-m-d H:i:s",strtotime($last_pub_date));
                $last_pub_title = isset($value['title']) ? $value['title'] : "";
                $last_pub_url = isset($value['link']) ? $value['link'] : "";
            }            
        }

        $insert_data=array
        (
            "user_id" => $this->user_id,
            "user_account_id" => $this->session->userdata('google_mybusiness_user_table_id'),
            "feed_name" => $feed_name,
            "feed_url" => $feed_url,
            "last_pub_date" => $last_pub_date,
            "last_pub_title" => $last_pub_title,
            "last_pub_url" => $last_pub_url,
            "last_updated_at" => $datetime,
            "error_message" => json_encode(array())
        );

        if($this->basic->insert_data("google_rss_feed_posting",$insert_data)) 
        {
            // $this->_insert_usage_log(25,1);
            $success_message=$this->lang->line("Feed has been added successfully.");
            echo json_encode(array('status'=>'1','message'=>$success_message));
        }
        else
        {
            $error_message=$this->lang->line("Something went wrong, please try again.");
            echo json_encode(array('status'=>'0','message'=>$error_message));
        }
    }

    public function rss_campaign_settings()
    {
        $this->ajax_check();

        $id = $this->input->post('id', true);

        $timezones = $this->_time_zone_list();

        $where = [
            'where' => [
                'user_id' => $this->user_id,
                'user_account_id' => $this->session->userdata('google_mybusiness_user_table_id'),
            ],
        ];

        $locations = $this->basic->get_data('google_business_locations', $where, ['id', 'location_display_name']);

        $prepared_locations = [];
        if (count($locations)) {
            foreach ($locations as $location) {
                $location_key = $location['id'] . '-' . $location['location_display_name'];
                $prepared_locations[$location_key] = $location['location_display_name'];
            }
        }

        $get_data = $this->basic->get_data("google_rss_feed_posting",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));

        if(! isset($get_data[0]))
        {
             $error = '<div class="alert alert-danger text-center"><i class="fa fa-remove"></i> '.$this->lang->line("Feed not found.").'</div>';
             echo json_encode(array('html'=>$error,'feed_name'=>'','status'=>'0'));
             exit();
        }

        $location_names = (isset($get_data[0]['location_names']) && ! empty($get_data[0]['location_names']))
            ? json_decode($get_data[0]['location_names'], true)
            : [];

        $selected_locations = array();
        if (count($location_names)) {
            foreach ($location_names as $location) {
                $selected_locations[] = $location['id'] . '-' . $location['name'];
            }
        }

        $feed_name = isset($get_data[0]['feed_name'])?$get_data[0]['feed_name']:'';
        $feed_url = isset($get_data[0]['feed_url'])?$get_data[0]['feed_url']:'';
        $feed_name_send = "<a href='".$feed_url."' target='_BLANK'>".$feed_name."</a>";

        $posting_message = ($get_data[0]['posting_message'] != '') ? $get_data[0]['posting_message'] : '';
        $posting_timezone = isset($get_data[0]['posting_timezone']) ? $get_data[0]['posting_timezone'] : "";
        $posting_start_time = isset($get_data[0]['posting_start_time']) ? $get_data[0]['posting_start_time'] : "";
        $posting_end_time = isset($get_data[0]['posting_end_time']) ? $get_data[0]['posting_end_time'] : "";

        $default_media_url = isset($get_data[0]['default_media_url']) ? $get_data[0]['default_media_url'] : null;

        if($posting_timezone == "") {
            $posting_timezone=$this->config->item("time_zone");
        }

        if($posting_start_time == "") {
            $posting_start_time = "00:00";
        }

        if($posting_end_time == "") {
            $posting_end_time = "23:59";
        }

        $file_upload_limit = 2;
        if($this->config->item('xerobiz_file_upload_limit') != '') {
            $file_upload_limit = $this->config->item('xerobiz_file_upload_limit');
        }

        $html = '';
        $script = '
            <script>
                $("[data-toggle=\"tooltip\"]").tooltip();
                $("#posting_message").emojioneArea({autocomplete: false, pickerPosition: "bottom"});
                $("#posting_timezone,#location_name").select2();

                // Uploads media
                $("#media_url").uploadFile({
                    url: "' . base_url("gmb/upload_rss_media") . '",
                    fileName: "posting_image",
                    maxFileSize: ' . $file_upload_limit . ' * 1024 * 1024,
                    showPreview: false,
                    returnType: "json",
                    dragDrop: true,
                    showDelete: true,
                    multiple: false,
                    maxFileCount: 1,
                    acceptFiles: ".png, .jpg, .jpeg, .gif",
                    deleteCallback: function (data, pd) {
                        var delete_url = "' . site_url('gmb/delete_rss_media') . '";
                        $.post(delete_url, { op: "delete", name: data }, function (resp, textStatus, jqXHR) {
                                var result = JSON.parse(resp);

                            }
                        );
                    },
                    onSuccess:function(files, data, xhr, pd) {

                        /*  files - an array of user provided files name
                            data - an object with status and filename attributes
                            pd - an object containing progress bar data */

                        if (false === data.status) {
                            swal({
                                title: "' . $this->lang->line('Error!') . '",
                                text: data.message,
                                icon: "error",
                                button: "' . $this->lang->line('Ok') . '"
                            });

                            exit;
                        }

                        if (true === data.status) {

                        }
                    },
                    onError: function(files, status, errMsg, pd) {

                        /*  files: list of files
                            status: error status
                            errMsg: error message */

                        swal({
                            title: "' . $this->lang->line('Info!') . '",
                            text: errMsg,
                            icon: "info",
                            button: "' . $this->lang->line('Ok') . '"
                        });
                    }
                });
            </script>';

        $tooltip = '<span data-toggle="tooltip" data-original-title="' . $this->lang->line("If the system gets small number of feeds they will be processed in first hour of given time range. If system gets large amount of feeds then they will be processed spanning all over the time range.") . '">&nbsp;<i class="fa fa-info-circle text-primary"></i></span>';
        
        $html .= '<form action="#" id="campaign_settings_form" method="post">';

        $html .= '<div class="xit-spinner bg-white text-primary"><i class="fa fa-spinner fa-spin fa-3x"></i></div>';
 
        $html .= '<div class="row">';

        $html .= '<div class="col-12 col-md-6">
                <div class="form-group">
                    <input type="hidden" name="campaign_id" id="campaign_id" value="' . $id . '">
                    <label>' . $this->lang->line("Locations") . '</label>' . form_dropdown('location_name[]', $prepared_locations, $selected_locations, 'class="form-control select2" id="location_name" style="width:100%;" multiple required') . '
                </div>
            </div>';

        $html .= '<div class="col-12 col-md-6">
                    <div class="form-group">
                    <label>'.$this->lang->line("Posting Timezone").'</label>'.form_dropdown('posting_timezone', $timezones, $posting_timezone,"class='form-control' id='posting_timezone'").'
                    </div>
                </div>';

        $html .='<div class="col-12 col-md-6">
                    <div class="form-group">
                    <label>'.$this->lang->line("Post Between Time")." ".$tooltip.'</label>
                    <input type="text" class="form-control timepicker" value="'.$posting_start_time.'" id="posting_start_time" name="posting_start_time">
                    </div>
                </div>';

        $html .='<div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="hidden-xs hidden-sm" style="position: relative;right: 22px;top: 32px;">'.$this->lang->line("To").'</label>
                        <input type="text" class="form-control timepicker" value="'.$posting_end_time.'" id="posting_end_time" name="posting_end_time">
                    </div>
                </div>';

        $html .='<div class="col-12">
                    <div class="form-group">
                        <label>'.$this->lang->line("Message").'</label>
                        <span class="float-right" id="title_variable"><a title="" data-toggle="tooltip" class="btn btn-sm" data-original-title="'.$this->lang->line("You can use the original title from the feed.").'"><i class="far fa-lightbulb"></i>  '.$this->lang->line("Title").'</a></span>
                        <textarea class="form-control" id="posting_message" name="posting_message">'.$posting_message.'</textarea>
                    </div>
                </div>';


        $html .= '<div class="col-12">
                    <div class="form-group">
                        <label>'. $this->lang->line("Default media") . '
                            <span
                                class="text-primary"
                                data-toggle="tooltip"
                                data-original-title="' . $this->lang->line("If you upload an image then, system will use that image as a default one if there is no image attached to a RSS feed.") . '"
                            >
                                <i class="fa fa-info-circle"></i> 
                            </span>
                        </label>
                        <br>
                        <label class="custom-switch mt-2">
                            <input type="checkbox" name="media_status" value="yes" id="media_status" class="custom-switch-input">
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description">' . $this->lang->line("Upload media") . '</span>
                        </label>
                    </div>
                </div>';

        $html .= '<div class="col-12 d-none" id="upload-wrapper">
                    <div class="form-group">
                        <div id="media_url">'. $this->lang->line('Upload media') . '</div>
                        <br/>
                    </div>
                </div>';

        if ($default_media_url) {
            $html .= '<div class="col-12">
                        <div class="form-group">
                            <label class="d-block">' . $this->lang->line("Previous upload") . '</label>
                            <img id="rss_previous_upload" class="rounded img-thumbnail" src="' . $default_media_url . '" alt="" width="200" height="auto">
                        </div>
                    </div>';

        }


        $html .= '</div><!-- ends row -->';

        $html .='</form><!-- ends row -->';
        $html .= '<div class="clearfix"></div>';

        $html .= $script;

        echo json_encode(array('html'=>$html,'feed_name'=>$feed_name_send,'status'=>'1'));
    }

    public function create_rss_campaign()
    {
        $this->ajax_check();

        $campaign_id = $this->input->post("campaign_id", true);
        $data = $this->basic->get_data("google_rss_feed_posting",array("where"=>array("id"=>$campaign_id,"user_id"=>$this->user_id)));

        if(! count($data)) {
            return;
        }

        $this->form_validation->set_rules(
            'posting_timezone',
            $this->lang->line("Timezone"),
            'required'
        );

        $this->form_validation->set_rules(
            'posting_start_time',
            $this->lang->line("Post start time"),
            'required'
        );

        $this->form_validation->set_rules(
            'posting_start_time',
            $this->lang->line("Post end time"),
            'required'
        );

        $this->form_validation->set_rules(
            'posting_message',
            $this->lang->line("Message"),
            'required'
        );

        // Fixes user input time
        $posting_start_time = $this->input->post("posting_start_time", true);
        $posting_end_time = $this->input->post("posting_end_time", true);

        if (false == $this->isValidTime($posting_start_time, $posting_end_time)) {
            echo json_encode([
                'status' => "0",
                'message' => $this->lang->line("Post time was invalid. (The time difference should be 1 hour at least)"),
            ]);

            exit;
        }

        if (false === $this->form_validation->run()) {
            $errors = $this->form_validation->error_array();
            $errors = str_replace(['<b>', '</b>'], '', $errors);
            $first_error = current($errors);
            reset($errors);

            echo json_encode([
                'status' => "0",
                'message' => $first_error
            ]);

            exit;
        }

        // Prepares update data
        $update_data = [
            "posting_timezone" => $this->input->post("posting_timezone", true),
            "posting_start_time" => $this->input->post("posting_start_time", true),
            "posting_end_time" => $this->input->post("posting_end_time", true),
            "posting_message" => $this->input->post('posting_message', true),
        ];

        $default_media_url = null;
        $media_status = $this->input->post('media_status', true);
        if ($media_status && 'yes' == $media_status) {
            $filename = $this->session->userdata('gmb_rss_posting_image_name');
            if (! $filename) {
                echo json_encode([
                    'status' => "0",
                    'message' => $this->lang->line("You wanted to upload an image but you did not"),
                ]);

                exit;
            }

            $default_media_url = base_url("upload/xerobiz/media/{$filename}");

            $update_data['default_media_url'] = $default_media_url;
        }

        $locations = (array) $this->input->post('location_name', true);

        if (empty($locations)) {
            $message = $this->lang->line('Location name is required');
            echo json_encode([
                'status' => false,
                'message' => $message,
            ]);
            exit;
        }

        $location_names = [];
        $location_table_id = [];

        foreach ($locations as $location) {
            $array = explode('-', $location, 2);
            $location_id = isset($array[0]) ? $array[0] : '';
            $location_name = isset($array[1]) ? $array[1] : '';

            if ($location_id && $location_name) {
                $location_table_id[] = $location_id;
                $location_names[] = [
                    'id' => $location_id,
                    'name' => $location_name,
                    'status' => true,
                ];
            }
        }

        $update_data["location_ids"] = json_encode($location_table_id);
        $update_data["location_names"] = json_encode($location_names);

        $this->basic->update_data("google_rss_feed_posting",array( "id" => $campaign_id, "user_id" => $this->user_id) ,$update_data);
        {
            echo json_encode(array("status"=>"1","message"=>$this->lang->line("Campaign has been submitted successfully and will start processing shortly as per your settings.")));
        }
    }

    public function upload_rss_media()
    {
        $this->ajax_check();

        // Determines upload path
        $upload_dir = APPPATH . '../upload/xerobiz/media/';

        // Starts uploading file
        if (isset($_FILES['posting_image'])) {

            $error = $_FILES['posting_image']['error'];
            if( $error ) {
                echo json_encode([$error]);
                exit;
            }

            if (is_uploaded_file($_FILES['posting_image']['tmp_name'])) {
                $tmp_name = $_FILES['posting_image']['tmp_name'];
                $post_filename = $_FILES["posting_image"]["name"];
                $extension = mb_substr($post_filename, mb_strrpos($post_filename, '.'));

                if(! in_array(strtolower($extension), $this->rss_allowed_mime_types)) {
                    echo json_encode([
                        'status' => false,
                        'message' => $this->lang->line('File type not allowed'),
                    ]);
                    exit();
                }

                $filename = 'image_' . $this->user_id . '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) . $extension;
                $destination = $upload_dir . $filename;

                if (move_uploaded_file($tmp_name, $destination)) {

                    // Changes the file permission
                    chmod($destination, 0644);

                    // Stores the filename in session so that 
                    // we can make a simple security check on deleting this file
                    $this->session->set_userdata('gmb_rss_posting_image_name', $filename);

                    echo json_encode([
                        'status' => true,
                        'filename' => $filename,
                    ]);
                    exit;
                } else {
                    echo json_encode([
                        'status' => false,
                        'message' => $this->lang->line('Something went wrong while uploading file'),
                    ]);
                    exit();
                }
            }
        }

        echo json_encode([
            'status' => false,
            'message' => $this->lang->line('Something went wrong while uploading file'),
        ]);
        exit();
    }

    public function delete_rss_media()
    {
        // Checks ajax call
        $this->ajax_check();

        if( ! $_POST) {
            exit();
        }

        $upload_dir = APPPATH . '../upload/xerobiz/media/';

        if(isset($_POST['op']) && $_POST['op'] == 'delete' && isset($_POST['name'])) {
            $stored_filename = $this->session->userdata('gmb_rss_posting_image_name');
            $filename = (isset($_POST['name']['filename']) && is_string($_POST['name']['filename']))
                ? strip_tags($_POST['name']['filename'])
                : '';

            if ($filename != $stored_filename) {
                echo json_encode([
                    'status' => false,
                    'message' => $this->lang->line('Bad request'),
                ]);
                exit;
            }

            $filePath = $upload_dir . $stored_filename;

            if (!is_dir($filePath) && file_exists($filePath)) {
                
                // Deletes the file
                unlink($filePath);

                // Unsets the filename reference
                $this->session->unset_userdata('gmb_rss_posting_image_name');

                echo json_encode([
                    'status' => true,
                    'message' => $this->lang->line('File has been deleted successfully'),
                ]);
                exit;
            }
        }

        echo json_encode([
            'status' => false,
            'message' => $this->lang->line('Bad request'),
        ]);
        exit;
    }

    public function enable_rss_settings()
    {
        $this->ajax_check();
        $id=$this->input->post('id',true);
        $get_data=$this->basic->get_data("google_rss_feed_posting",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));

        if(!isset($get_data[0]))
        {
            $error=$this->lang->line("Feed not found.");
            echo json_encode(array('message'=>$error,'status'=>'0'));
            exit();
        }

        $feed_url=isset($get_data[0]['feed_url'])?$get_data[0]['feed_url']:'';

        $this->load->library("rss_feed");
        $feed=$this->rss_feed->getFeed($feed_url);

        if(!isset($feed['success']) || $feed['success']!='1')
        {
            $error_message=isset($feed['error_message'])?$feed['error_message']:$this->lang->line("Something went wrong, please try again.");
            echo json_encode(array('status'=>'0','message'=>$error_message));
            exit();
        }
        
        $datetime=date("Y-m-d H:i:s");
        date_default_timezone_set('Europe/Dublin'); // operating in GMT
        $last_pub_date=isset($feed['element_list'][0]['pubDate'])?$feed['element_list'][0]['pubDate']:"";
        $last_pub_date=date("Y-m-d H:i:s",strtotime($last_pub_date));
        $last_pub_title=isset($feed['element_list'][0]['title'])?$feed['element_list'][0]['title']:"";
        $last_pub_url=isset($feed['element_list'][0]['link'])?$feed['element_list'][0]['link']:"";

        $update_data=array
        (
            "last_pub_date"=>$last_pub_date,
            "last_pub_title"=>$last_pub_title,
            "last_pub_url"=>$last_pub_url,
            "last_updated_at"=>$datetime,
            "status"=>"1"
        );

        if($this->basic->update_data("google_rss_feed_posting",array("id"=>$id,"user_id"=>$this->user_id),$update_data))
        $this->session->set_flashdata('auto_success',1);
        else $this->session->set_flashdata('auto_success',0);       

        echo json_encode(array('status'=>'1'));     
    }

    public function disable_rss_settings()
    {
        $this->ajax_check();

        $id = $this->input->post('id',true);

        if($this->basic->update_data("google_rss_feed_posting",array("id"=>$id,"user_id"=>$this->user_id),array("status"=>"0")))
        {
            $this->session->set_flashdata('auto_success', 1);
        }
        else
        {
            $this->session->set_flashdata('auto_success', 0);
        }
    }

    public function delete_rss_settings()
    {
        $this->ajax_check();

        $id = $this->input->post('id', true);

        if($this->basic->delete_data("google_rss_feed_posting",array("id"=>$id,"user_id"=>$this->user_id)))
        {
            $this->session->set_flashdata('auto_success',1);
            // $this->_delete_usage_log(256,1);
        }
        else
        {
            $this->session->set_flashdata('auto_success',0);
        }
    }

    public function show_rss_error_log()
    {
        $this->ajax_check();
        $id=$this->input->post('id',true);
        $get_data=$this->basic->get_data("google_rss_feed_posting",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $error_log=isset($get_data[0]["error_message"])?json_decode($get_data[0]["error_message"],true):array();
        if(!is_array($error_log) || count($error_log)==0)
        {
            echo "<div class='alert alert-light text-center'>".$this->lang->line('no error found')."</div>";
        }
        else
        {
            $error_log=array_reverse($error_log);

            echo '<script>
                  $(document).ready(function() {
                      $(".mypre").mCustomScrollbar({
                        autoHideScrollbar:true,
                        theme:"3d-dark",
                        axis: "x"
                      });
                    });
                  </script>';
             echo "<div class='clearfix'><a href='' class='clear_log btn btn-outline-danger btn-sm float-right' data-id='".$id."'><i class='fa fa-trash'></i>".$this->lang->line('Delete')."</a></div>";
            echo "<ul class='list-group'>";
            foreach ($error_log as $key => $value) 
            {
                echo "<li class='list-group-item'>".date("d-m-Y H:i:s",strtotime($value['time']))." : ".$value["message"]."</li>";
            }
            echo "</ul>";
           
        }
    }

    public function clear_rss_error_log()
    {
        $this->ajax_check();
        $id=$this->input->post('id',true);
        $this->basic->update_data("autoposting",array("id"=>$id,"user_id"=>$this->user_id),array("error_message"=>json_encode(array()),"last_updated_at"=>date("Y-m-d H:i:s")));      
        echo "1";        
    }

    /**
     * Tries to validate time 
     * Time difference should be 1 hour at least
     *
     * @param string $start
     * @param string $end
     * @return bool
     */
    private function isValidTime($start, $end) 
    {
        $start_time = explode(':', $start);
        $end_time = explode(':', $end);

        $start_time = array_slice($start_time, 0, 2);
        $end_time = array_slice($end_time, 0, 2);

        if (2 != count($start_time) || 2 != count($end_time)) {
            return false;
        }

        if (is_array($start_time)) {
            $minutes = (int) $start_time[0];
            $seconds = (int) $start_time[1];

            if($minutes < 0 || $minutes > 23) {
                return false;
            } elseif ($seconds < 0 || $seconds > 59) {
                return false;
            }
        }

        if (is_array($end_time)) {
            $minutes = (int) $end_time[0];
            $seconds = (int) $end_time[1];

            if($minutes < 0 || $minutes > 23) {
                return false;
            } elseif ($seconds < 0 || $seconds > 59) {
                return false;
            }
        }

        $start_number = (float) join('.', $start_time);
        $end_number = (float) join('.', $end_time);

        if ($start_number > $end_number) {
            return false;
        }

        return true;
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
            $sql=array
            (
                1=> "
                CREATE TABLE IF NOT EXISTS `google_business_locations` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `user_account_id` int(11) NOT NULL,
                  `location_display_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `location_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `only_location_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `latitude_longitude` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `map_url` text COLLATE utf8mb4_unicode_ci,
                  `profile_google_url` text COLLATE utf8mb4_unicode_ci,
                  `cover_google_url` text COLLATE utf8mb4_unicode_ci,
                  `new_review_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
                  `address` text COLLATE utf8mb4_unicode_ci,
                  `total_reviews` int(11) DEFAULT '0',
                  `total_products` int(11) DEFAULT '0',
                  `last_review_reply_id` text COLLATE utf8mb4_unicode_ci,
                  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                2=>"
                CREATE TABLE IF NOT EXISTS `google_posts_campaign` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `user_account_id` int(11) NOT NULL,
                  `location_table_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
                  `location_names` text COLLATE utf8mb4_unicode_ci,
                  `post_type` enum('cta_post','event_post','offer_post') COLLATE utf8mb4_unicode_ci DEFAULT 'cta_post',
                  `status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 = Pending, 1 = Processing, 2 = Completed',
                  `campaign_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `cta_action_type` enum('BOOK','LEARN_MORE','SIGN_UP','CALL','ORDER','SHOP') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `cta_action_url` text COLLATE utf8mb4_unicode_ci,
                  `event_post_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `start_date_time` datetime DEFAULT NULL,
                  `end_date_time` datetime DEFAULT NULL,
                  `offer_coupon_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `offer_redeem_url` text COLLATE utf8mb4_unicode_ci,
                  `location_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `summary` text COLLATE utf8mb4_unicode_ci,
                  `media_type` char(5) COLLATE utf8mb4_unicode_ci DEFAULT 'PHOTO',
                  `media_url` text COLLATE utf8mb4_unicode_ci,
                  `terms_conditions` text COLLATE utf8mb4_unicode_ci,
                  `schedule_type` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `schedule_time` datetime DEFAULT NULL,
                  `time_zone` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `response` text COLLATE utf8mb4_unicode_ci,
                  `error` text COLLATE utf8mb4_unicode_ci,
                  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
                  `created_at` date DEFAULT NULL,
                  `updated_at` date DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                3=>"
                CREATE TABLE IF NOT EXISTS `google_review_reply_report` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `location_id` int(11) NOT NULL,
                  `location_display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `review_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `reviewer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `reviewer_photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `review_star` char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `review_comment` text COLLATE utf8mb4_unicode_ci,
                  `review_reply` text COLLATE utf8mb4_unicode_ci,
                  `review_create_time` datetime DEFAULT NULL,
                  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
                  `error` text COLLATE utf8mb4_unicode_ci,
                  `reply_time` datetime DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `google_rrr_unique_keys` (`location_id`,`review_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                4=>"
                CREATE TABLE IF NOT EXISTS `google_review_reply_settings` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `location_id` int(11) NOT NULL,
                  `only_location_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `five_star` text COLLATE utf8mb4_unicode_ci,
                  `four_star` text COLLATE utf8mb4_unicode_ci,
                  `three_star` text COLLATE utf8mb4_unicode_ci,
                  `two_star` text COLLATE utf8mb4_unicode_ci,
                  `one_star` text COLLATE utf8mb4_unicode_ci,
                  `updated_at` datetime DEFAULT NULL,
                  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0=free 1=processing',
                  `last_reply_time` datetime DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                5=>"
                CREATE TABLE IF NOT EXISTS `google_user_account` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `app_config_id` int(11) NOT NULL,
                  `account_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `account_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `account_display_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `access_token` text COLLATE utf8mb4_unicode_ci,
                  `profile_photo` text COLLATE utf8mb4_unicode_ci NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                6=>"
                CREATE TABLE IF NOT EXISTS `google_media_campaign` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `user_account_id` int(11) NOT NULL,
                  `location_table_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
                  `location_names` text COLLATE utf8mb4_unicode_ci,
                  `campaign_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `media_category` enum('COVER','PROFILE','LOGO','EXTERIOR','INTERIOR','PRODUCT','AT_WORK','FOOD_AND_DRINK','MENU','COMMON_AREA','ROOMS','TEAMS','ADDITIONAL') COLLATE utf8mb4_unicode_ci NOT NULL,
                  `media_type` enum('PHOTO','VIDEO') COLLATE utf8mb4_unicode_ci NOT NULL,
                  `media_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
                  `media_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
                  `schedule_type` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `schedule_time` datetime DEFAULT NULL,
                  `time_zone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
                  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
                  `response` text COLLATE utf8mb4_unicode_ci,
                  `error` text COLLATE utf8mb4_unicode_ci,
                  `updated_at` datetime DEFAULT NULL,
                  `created_at` datetime DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`),
                  KEY `user_account_id` (`user_account_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                7=>"
                CREATE TABLE IF NOT EXISTS `google_rss_feed_posting` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `user_account_id` int(11) NOT NULL COMMENT 'google user account table',
                  `feed_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `feed_type` enum('rss') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rss',
                  `feed_url` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
                  `location_ids` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'location table ID',
                  `location_names` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'location names',
                  `posting_message` text COLLATE utf8mb4_unicode_ci NOT NULL,
                  `posting_start_time` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `posting_end_time` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `posting_timezone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `default_media_url` text COLLATE utf8mb4_unicode_ci,
                  `last_pub_date` datetime NOT NULL,
                  `last_pub_title` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
                  `last_pub_url` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
                  `status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT 'pending, processing, abandoned',
                  `last_updated_at` datetime NOT NULL,
                  `cron_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
                  `error_message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`),
                  KEY `user_account_id` (`user_account_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
                8=>"INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'GMB Manager', 'fas fa-store-alt', '', (SELECT serial FROM menu as menu2 WHERE module_access='220,222,223,256,100'), '300,301,302,303,304,305', '1', '0', '0', (SELECT id FROM add_ons WHERE project_id='55'), '0', '', '0', '0');",
                9=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Location Manager', 'gmb/location_list', '5', 'fas fa-map-marked-alt', '301,302', (SELECT id FROM menu WHERE module_access='300,301,302,303,304,305'), '0', '0', '0', '0', '0', 0);",
                10=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Campaigns', 'gmb/campaigns', '10', 'fas fa-arrows-alt', '303,304', (SELECT id FROM menu WHERE module_access='300,301,302,303,304,305'), '0', '0', '0', '0', '0', 0);",
                11=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Review Replies', 'gmb/review_report', '15', 'fas fa-reply-all', '302', (SELECT id FROM menu WHERE module_access='300,301,302,303,304,305'), '0', '0', '0', '0', '0', 0);",
                12=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Account Import', 'gmb/business_accounts', '1', 'fa fa-cloud-download-alt', '300', (SELECT id FROM menu WHERE module_access='300,301,302,303,304,305'), '0', '0', '0', '0', '0', 0);"

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
              1=> "DROP TABLE IF EXISTS `google_business_locations`;",
              2=> "DROP TABLE IF EXISTS `google_posts_campaign`;",
              3=> "DROP TABLE IF EXISTS `google_review_reply_report`;",
              4=> "DROP TABLE IF EXISTS `google_review_reply_settings`;",
              5=> "DROP TABLE IF EXISTS `google_user_account`;",
              6=> "DROP TABLE IF EXISTS `google_media_campaign`;",
              7=> "DROP TABLE IF EXISTS `google_rss_feed_posting`;",
              8=> "DELETE FROM `menu` WHERE `module_access` = '300,301,302,303,304,305';",
              9=> "DELETE FROM `menu_child_1` WHERE `url` = 'gmb/location_list';",
              10=> "DELETE FROM `menu_child_1` WHERE `url` = 'gmb/campaigns';",
              11=> "DELETE FROM `menu_child_1` WHERE `url` = 'gmb/review_report';",
              12=> "DELETE FROM `menu_child_1` WHERE `url` = 'gmb/business_accounts';"
            );  
            
            // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
            $this->delete_addon($addon_controller_name,$sql);         
        }

}

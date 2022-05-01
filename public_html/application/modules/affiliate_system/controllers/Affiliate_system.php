<?php
/*
Addon Name: Affiliate Manager
Unique Name: affiliate_system
Project ID: 57
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 2.0
Description: 
*/

require_once("application/controllers/Home.php"); // loading home controller

class Affiliate_system extends Home
{
    public $addon_data = array();

    protected $module_path;
    // public $is_rtl;

    public function __construct()
    {
        parent::__construct();

        // $is_rtl = $this->config->item("is_rtl");
        // if(!empty($is_rtl) && $is_rtl=='1') $this->is_rtl=TRUE;
        // else $this->is_rtl=FALSE;

        $function_name=$this->uri->segment(2);

        $this->load->helper('cookie');

        // if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');
        // $this->member_validity();

        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $this->addon_data=$this->get_addon_data($addon_path);

        // Sets module path
        $this->module_path = APPPATH . '/modules/';
    }

    public function is_logged_in()
    {
        $user = $this->session->userdata('affiliate_userid');
        return isset($user);
    }

    public function index()
    {
        if (!$this->is_logged_in()) redirect('affiliate_system/affiliate_login_page');
        
        $this->earnings();
    }

    public function alpha_numeric_underscore($str)
    {
        if (! preg_match("/^([a-zA-Z0-9_])+$/i", $str)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function affiliate_sign_up()
    {   
        $data = [];
        $data['page_title'] = $this->lang->line("Affiliate Registration");
        $data['body'] = 'affiliate/sign_up';
        $data['num1'] = $this->_random_number_generator(1);
        $data['num2'] = $this->_random_number_generator(1);
        $captcha = $data['num1']+ $data['num2'];
        $this->session->set_userdata("affiliate_signup_captcha",$captcha);

        $this->_aff_subscription_viewcontroller($data);
    }

    public function affiliate_signup_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $enable_signup_activation = $this->config->item('enable_signup_activation');

        if($_POST) {
            $this->form_validation->set_rules('affiliate_name', '<b>'.$this->lang->line("name").'</b>', 'trim|required');
            $this->form_validation->set_rules('user_name', '<b>'.$this->lang->line("username").'</b>', 'trim|required|is_unique[affiliate_users.username]|callback_alpha_numeric_underscore',
                array(
                    'alpha_numeric_underscore' => $this->lang->line('Username is required and only characters, underscores, digits are allowed'),
                    'is_unique'                => $this->lang->line('username already exists')
                )
            );
            $this->form_validation->set_rules('affiliate_email', '<b>'.$this->lang->line("email").'</b>', 'trim|required|valid_email|is_unique[affiliate_users.email]',
                array(
                    'is_unique' => $this->lang->line('Email Already Exists')
                )
            );
            $this->form_validation->set_rules('affiliate_mobile', '<b>'.$this->lang->line("mobile").'</b>', 'trim');
            $this->form_validation->set_rules('affiliate_password', '<b>'.$this->lang->line("password").'</b>', 'trim|required');
            $this->form_validation->set_rules('affiliate_confirm_password', '<b>'.$this->lang->line("confirm password").'</b>', 'trim|required|matches[affiliate_password]');
            $this->form_validation->set_rules('affiliate_captcha', '<b>'.$this->lang->line("captcha").'</b>', 'trim|required|integer');

            if($this->form_validation->run() == FALSE)
            {
                $this->affiliate_sign_up();
            }
            else
            {
                // $this->csrf_token_check();

                $captcha = $this->input->post('affiliate_captcha', TRUE);

                if($captcha != $this->session->userdata("affiliate_signup_captcha"))
                {
                    $this->session->set_userdata("affiliate_signup_captcha_error",$this->lang->line("invalid captcha"));
                    return $this->affiliate_sign_up();
                }  

                $code = $this->_random_number_generator();

                $data = [];
                $data['name'] = strip_tags($this->input->post('affiliate_name', TRUE));
                $data['username'] = strip_tags($this->input->post('user_name', TRUE));
                $data['email'] = $this->input->post('affiliate_email', TRUE);
                $data['mobile'] = $this->input->post('affiliate_mobile', TRUE);
                $data['password'] = md5($this->input->post('affiliate_password', TRUE));
                $data['activation_code'] = $code;
                $data['status'] = '0';

                // $this->db->trans_start();

                if($enable_signup_activation == '0') $data['status']='1';

                if ($this->basic->insert_data('affiliate_users', $data)) {

                    if($enable_signup_activation == '1') {
                        $email_template_info = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>"affiliate_signup_activation")),array('subject','message'));
                        $url = site_url()."affiliate_system/affiliate_account_activation";
                        $url_final = "<a href='".$url."' target='_BLANK'>".$url."</a>";
                        $productname = $this->config->item('product_name');

                        if(isset($email_template_info[0]) && $email_template_info[0]['subject'] != '' && $email_template_info[0]['message'] != '')
                        {
                            $subject = str_replace('#APP_NAME#',$productname,$email_template_info[0]['subject']);
                            $message = str_replace(array("#APP_NAME#","#ACTIVATION_URL#","#ACCOUNT_ACTIVATION_CODE#"),array($productname,$url_final,$code),$email_template_info[0]['message']);
                            // echo "Database Has data"; exit();

                        } else
                        {
                            $subject = $productname." | Affiliate Account activation";
                            $message = "<p>".$this->lang->line("to activate your affiliate account please perform the following steps")."</p>
                                        <ol>
                                            <li>".$this->lang->line("go to this url").":".$url_final."</li>
                                            <li>".$this->lang->line("enter this code").":".$code."</li>
                                            <li>".$this->lang->line("activate your account")."</li>
                                        </ol>";
                        }

                        $from = $this->config->item('institute_email');
                        $to = $data['email'];
                        $mask = $this->config->item("product_name");
                        $html = 1;

                        $this->_mail_sender($from, $to, $subject, $message, $mask, $html);

                        $this->session->set_userdata('affiliate_reg_success',1);
                        return $this->affiliate_sign_up();
                    } else {
                        redirect("affiliate_system/affiliate_login_page","refresh");
                    }
                }   

            }

        }
    }

    public function affiliate_account_activation()
    {
        $data["page_title"] = $this->lang->line("Affiliate Account Activation");
        $data["body"] =  "affiliate/account_activation";

        $this->_aff_subscription_viewcontroller($data);
    }

    public function affiliate_account_activation_action()
    {
        if ($_POST) {

            $code = trim($this->input->post('code', true));
            $email = $this->input->post('email', true);

            $table='affiliate_users';
            $where['where'] = array('activation_code'=>$code,'email'=>$email,'status'=>"0");
            $select = array('id');

            $result = $this->basic->get_data($table, $where, $select);

            if (empty($result)) {
                echo 0;
            } else {
                foreach ($result as $row) {
                    $user_id=$row['id'];
                }

                $this->basic->update_data('affiliate_users', array('id'=>$user_id), array('status'=>'1'));
                echo 2;

            }
        }
    }

    public function affiliate_login_page()
    {
        if($this->is_logged_in()) redirect("affiliate_system","refresh");

        $data['body']       = 'affiliate/login';
        $data['page_title'] = $this->lang->line("Log In");

        $this->_aff_subscription_viewcontroller($data);
    }

    public function affiliate_login() 
    {

        $this->form_validation->set_rules('affilate_email', '<b>'.$this->lang->line("email").'</b>', 'trim|required|valid_email');
        $this->form_validation->set_rules('affiliate_password', '<b>'.$this->lang->line("password").'</b>', 'trim|required');

        if ($this->form_validation->run() == false)
            $this->affiliate_login_page();
        else {

            // $this->csrf_token_check();

            $email          = $this->input->post('affilate_email', TRUE);
            $password       = md5($this->input->post('affiliate_password', TRUE));

            $table          = 'affiliate_users';
            $where['where'] = array('email' => $email, 'password' => $password,'status'=>'1');
            $info           = $this->basic->get_data($table,$where,$select='',$join='',$limit='',$start='',$order_by='',$group_by='',$num_rows=1);
            
            $count          = $info['extra_index']['num_rows'];

            if ($count == 0) {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                redirect(uri_string());
            } else {

                $affiliate_userid   = $info[0]['id'];
                $affiliate_name     = $info[0]['name'];
                $affiliate_username = $info[0]['username'];
                $affiliate_pic      = $info[0]['profile_img'];

                if($affiliate_pic=="") $affiliate_pic=base_url("assets/img/avatar/avatar-1.png");
                else $affiliate_pic = base_url().'upload/affiliator/'.$affiliate_pic;

                $login_ip = $this->real_ip();

                $affiliate_session_data = array(
                    'affiliate_email'    =>$email,
                    'affiliate_userid'   => $affiliate_userid,
                    'affiliate_name'     => $affiliate_name,
                    'affiliate_username' => $affiliate_username,
                    'affiliate_pic'      => $affiliate_pic,
                );

                $this->basic->update_data("affiliate_users",['id'=>$affiliate_userid],["last_login_at"=>date("Y-m-d H:i:s"),'last_login_ip'=>$login_ip]);

                $this->session->set_userdata($affiliate_session_data);
                redirect('affiliate_system/index');

            }
        }
    }

    public function affiliate_logout()
    {
        $this->session->sess_destroy();
        redirect('affiliate_system/affiliate_login_page', 'location');
    }

    public function _aff_subscription_viewcontroller($data=array())
    {
        if (!isset($data['body'])) $data['body']="affiliate/blank";
        if (!isset($data['page_title'])) $data['page_title']="";

        $theme_load = "affiliate/subscription_theme";
        $data['is_rtl'] = $this->is_rtl;

        $this->load->view($theme_load, $data);
    }


    public function _affiliate_viewcontroller($data=array())
    {   
        if (!isset($data['body'])) {
            $data['body']=$this->config->item('default_page_url');
        }

        if (!isset($data['page_title'])) {
            $data['page_title']=$this->lang->line("Affiliate Panel");
        }

        $data["language_info"] = $this->_language_list();
        $data['is_rtl'] = $this->is_rtl;

        $this->load->view('affiliate_theme/theme', $data);
    }

    public function affiliate_link()
    {
        if(!$this->is_logged_in()) redirect("affiliate_system/affiliate_login_page","location");
        
        $data = [];
        $data['body'] = 'affiliate/get_affiliate_link';
        $data['page_title'] = $this->lang->line('Affiliate Link');

        $affiliate_id = $this->session->userdata("affiliate_userid");
        $affiliate_infos = $this->basic->get_data("affiliate_users",['where'=>['id'=>$affiliate_id]]);
        $data['affiliate_info'] = $affiliate_infos[0];

        $table = "affiliate_payment_settings";
        $existing_info = $this->basic->get_data($table);
        $data['info'] = isset($existing_info[0]) ? $existing_info[0]:[];

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";

        $this->_affiliate_viewcontroller($data);
    }


    public function withdrawal_method()
    {
        if(!$this->is_logged_in()) redirect("affiliate_system/affiliate_login_page","location");

        $data = [];
        $data['page_title'] = $this->lang->line('Withdrawal Methods');
        $data['body'] = "withdrawals/methods";

        $this->_affiliate_viewcontroller($data);
    }


    public function withdrawal_method_data()
    {
        $this->ajax_check();

        $display_columns = array("#",'id','payment_type','created_at','actions');
        $search_value = $_POST['search']['value'];
        $search_columns = array('payment_type', 'paypal_email','bank_acc_no');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom = 'affiliate_id='.$this->session->userdata('affiliate_userid');

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }

        $table = "affiliate_withdrawal_methods";
        $this->db->where($where_custom);
        $info = $this->basic->get_data($table,$where='','','',$limit,$start,$order_by,$group_by='');
        $this->db->where($where_custom);
        $total_rows_array = $this->basic->count_row($table,$where='',$count="id",$join='',$group_by='');
        $total_result = $total_rows_array[0]['total_rows'];

        for ($i=0; $i < count($info) ; $i++) 
        { 

            if($info[$i]['created_at'] != "0000-00-00 00:00:00")
                $info[$i]['created_at'] = "<div style='min-width:100px !important;'>".date("M j, Y H:i A",strtotime($info[$i]['created_at']))."</div>";

            if($info[$i]['payment_type'] == 'paypal') {
                $info[$i]['payment_type'] = "<div class='text-center'>PayPal <span data-toggle='tooltip' title='".$this->lang->line('See details')."' class='text-primary pointer method_details' method_name='PayPal' details='".$info[$i]['paypal_email']."'><i class='fas fa-info-circle'></i></span></div>";
            }

            if($info[$i]['payment_type'] == 'bank_acc') {
                $info[$i]['payment_type'] = "<div class='text-center'>Manual <span data-toggle='tooltip' title='".$this->lang->line('See details')."' class='text-primary pointer method_details' method_name='Manual' details='".nl2br(htmlspecialchars($info[$i]['bank_acc_no'],ENT_QUOTES))."'><i class='fas fa-info-circle'></i></span></div>";
            }

            $info[$i]['actions'] = "<div style='min-width:150px'><a href='#' title='".$this->lang->line("Edit Method")."' class='btn btn-sm btn-warning edit_method' table_id='".$info[$i]['id']."'><i class='fa fa-edit'></i> ".$this->lang->line("Edit")."</a>&nbsp;&nbsp;";

            $info[$i]['actions'] .= "<a href='#' title='".$this->lang->line("Delete Method")."' class='btn btn-sm btn-danger delete_method' table_id='".$info[$i]['id']."'><i class='fa fa-trash-alt'></i> ".$this->lang->line("Delete")."</a></div>
                <script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";


        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }


    public function new_method()
    {
        $this->ajax_check();

        if(!$this->is_logged_in()) redirect("affiliate_system/affiliate_login_page","location");

        $method_type = $this->input->post('method_type',true);
        $paypal_email = trim(strip_tags($this->input->post('paypal_email',true)));
        $bank_acc_no = trim(strip_tags($this->input->post('bank_acc_no',true)));
        $affiliate_id = $this->session->userdata('affiliate_userid');

        $insert_data = [
            'affiliate_id' => $affiliate_id,
            'payment_type' => $method_type,
            'paypal_email' => $paypal_email,
            'bank_acc_no' => $bank_acc_no,
            'created_at' => date("Y-m-d H:i:s")
        ];

        if($this->basic->insert_data("affiliate_withdrawal_methods",$insert_data)) {
            echo '1';
        } else {
            echo '0';
        }
    }

    public function get_method_info()
    {
        $table_id = $this->input->post('table_id',true);
        $affiliate_id = $this->session->userdata('affiliate_userid');

        if($table_id == '' || $table_id == 0) exit;

        $get_method_info = $this->basic->get_data("affiliate_withdrawal_methods",['where'=>['id'=>$table_id,'affiliate_id'=>$affiliate_id]]);

        // $account_name = $get_method_info[0]['account_name'];
        $method_type = $get_method_info[0]['payment_type'];
        $paypal_email = $get_method_info[0]['paypal_email'];
        $bank_acc_no = $get_method_info[0]['bank_acc_no'];

        $edit_paypal_div = $edit_bank_div = 'none';
        $paypal_selected = $bank_selected = '';

        if($method_type == 'paypal') {
            $edit_paypal_div = "block";
            $paypal_selected = 'selected';
        }

        if($method_type == 'bank_acc') {
            $edit_bank_div = "block";
            $bank_selected = 'selected';
        }

        $html = '
            <div class="row">
                <div class="col-12">                    
                    <form action="#" enctype="multipart/form-data" id="witdrawalMethod_edit_form" method="post">
                        <input type="hidden" name="table_id" id="table_id" value='.$table_id.'>
                        <div class="row">

                            <div class="col-12">
                                <div class="form-group">
                                    <label>'.$this->lang->line('Method').'</label>
                                    <select name="method_type" id="edit_method_type" class="form-control select2" style="width:100%;">
                                        <option value="">'.$this->lang->line('Select Method').'</option>
                                        <option value="paypal" '.$paypal_selected.'>'.$this->lang->line('PayPal').'</option>
                                        <option value="bank_acc" '.$bank_selected.'>'.$this->lang->line('Manual').'</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12" id="edit_paypal_email_div" style="display: '.$edit_paypal_div.';">
                                <div class="form-group">
                                    <label>'.$this->lang->line('PayPal Email').'</label>
                                    <input type="email" class="form-control" name="paypal_email" id="edit_paypal_email" value='.$paypal_email.'>
                                    
                                </div>
                            </div>

                            <div class="col-12" id="edit_bank_acc_div" style="display: '.$edit_bank_div.';">
                                <div class="form-group">
                                    <label>'.$this->lang->line('Details').'</label>
                                    <textarea class="form-control" name="bank_acc_no" id="edit_bank_acc_no" style="height: 100px !important;">'.$bank_acc_no.'</textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <script>$("#edit_method_type").select2();</script>
        ';

        echo $html;
    }

    public function update_method_info()
    {
        $this->ajax_check();

        $method_type = $this->input->post('method_type',true);
        $paypal_email = trim(strip_tags($this->input->post('paypal_email',true)));
        $bank_acc_no = trim(strip_tags($this->input->post('bank_acc_no',true)));
        $table_id = $this->input->post('table_id',true);
        $affiliate_id = $this->session->userdata('affiliate_userid');

        if($method_type == 'paypal') {
            $bank_acc_no = '';
        }

        if($method_type == 'bank_acc') {
            $paypal_email = '';
        }

        $update_data = [
            'affiliate_id' => $affiliate_id,
            'payment_type' => $method_type,
            'paypal_email' => $paypal_email,
            'bank_acc_no' => $bank_acc_no,
            'created_at' => date("Y-m-d H:i:s")
        ];

        if($this->basic->update_data("affiliate_withdrawal_methods",['id'=>$table_id,'affiliate_id'=>$affiliate_id],$update_data)) {
            echo '1';
        } else {
            echo '0';
        }

    }

    public function delete_withdrawal_method()
    {
        $this->ajax_check();

        $table_id = $this->input->post("id",true);
        if($table_id == '' || $table_id == 0) exit;

        if($this->basic->delete_data("affiliate_withdrawal_methods",['id'=>$table_id,'affiliate_id'=>$this->session->userdata("affiliate_userid")])) {
            echo "1";
        } else {
            echo "0";
        }
    }


    public function withdrawal_requests()
    {
        if(!$this->is_logged_in()) redirect("affiliate_system/affiliate_login_page","location");

        $per_page = 10;
        $search_value = "";

        // set per_page and search_value from user_submission
        if (isset($_POST['rows_number']) || isset($_POST['search_value'])) {

            $per_page = $this->input->post('rows_number', true);
            $search_value = $this->input->post('search_value', true);

            $this->session->set_userdata('request_per_page', $per_page);
            $this->session->set_userdata('request_search_value', $search_value);
        }


        // set session so that pagination can get proper per_page & search_value
        if ($this->session->userdata('request_per_page')) 
            $per_page = $this->session->userdata('request_per_page');

        if ($this->session->userdata('request_search_value')) 
            $search_value = $this->session->userdata('request_search_value');

        $where['where'] = array('affiliate_id' => $this->session->userdata("affiliate_userid"));

        if($search_value != "")
            $where['where'] = array('affiliate_id' => $this->session->userdata("affiliate_userid"),'request_status'=>$search_value);


        $total_withdrawal_requests = $this->basic->get_data('affiliate_withdrawal_requests', $where,'','','','','id DESC');


        if ($per_page == 'all')
            $per_page = count($total_withdrawal_requests);

        /* set cinfiguration for pagination */
        $config = array(
            'uri_segment' => 3,
            'base_url' => base_url('affiliate_system/withdrawal_requests/'),
            'total_rows' => count($total_withdrawal_requests),
            'per_page' => $per_page,

            'full_tag_open' => '<ul class="pagination">',
            'full_tag_close' => '</ul>',

            'first_link' => $this->lang->line('First Page'),
            'first_tag_open' => '<li class="page-item">',
            'first_tag_close' => '</li>',

            'last_link' => $this->lang->line('Last Page'),
            'last_tag_open' => '<li class="page-item">',
            'last_tag_close' => '</li>',

            'next_link' => $this->lang->line('Next'),
            'next_tag_open' => '<li class="page-item">',
            'next_tag_close' => '</li>',

            'prev_link' => $this->lang->line('Previous'),
            'prev_tag_open' => '<li class="page-item">',
            'prev_tag_close' => '</li>',

            'cur_tag_open' => '<li class="page-item active"><a class="page-link">',
            'cur_tag_close' => '</a></li>',

            'num_tag_open' => '<li class="page-item">',
            'num_tag_close' => '</li>',
            'attributes' => array('class' => 'page-link')
        );
        $this->pagination->initialize($config);
        $page_links = $this->pagination->create_links();


        $start = $this->uri->segment(3);
        $limit = $config['per_page'];

        $new_all = [];

        $where2['where'] = array('affiliate_id' => $this->session->userdata("affiliate_userid"));
        $where3['where'] = array('affiliate_id' => $this->session->userdata("affiliate_userid"),'request_status'=>'0');
        $where4['where'] = array('affiliate_id' => $this->session->userdata("affiliate_userid"),'request_status'=>'1');

        if($search_value != "")
            $where2['where'] = array('affiliate_id' => $this->session->userdata("affiliate_userid"),'request_status'=>$search_value);


        $withdrawal_request_lists = $this->basic->get_data('affiliate_withdrawal_requests',$where2, '', '', $limit, $start, 'id DESC');
        $withdrawal_request_pending = $this->basic->get_data('affiliate_withdrawal_requests',$where3);
        $withdrawal_request_completed = $this->basic->get_data('affiliate_withdrawal_requests',$where4);

        // calculating total pending money of affiliator
        $pendingData = array_map(function ($value) {
            return $value['requested_amount'];
        },$withdrawal_request_pending);
        $finalData = array_sum($pendingData);

        // calculating total pending money of affiliator
        $completeData = array_map(function ($value) {
            return $value['requested_amount'];
        },$withdrawal_request_completed);
        $finalData2 = array_sum($completeData);

        $total_earn = $this->basic->get_data("affiliate_users",['where'=>['id'=>$this->session->userdata("affiliate_userid")]],['name','profile_img','total_earn']);


        for ($i=0; $i < count($withdrawal_request_lists); $i++) { 
            
            $status = $withdrawal_request_lists[$i]['request_status'];
            if($status == '0') {
                $withdrawal_request_lists[$i]['request_status_icon'] = '<small class="text-warning">'.$this->lang->line('Pending').'</small>';

            } else if($status == '1') {
                $withdrawal_request_lists[$i]['request_status_icon'] = '<small class="text-success">'.$this->lang->line('Approved').'</small>';

            } else if($status == '2') {
                $withdrawal_request_lists[$i]['request_status_icon'] = '<small class="text-danger">'.$this->lang->line('Canceled').'</small>';
            }

            $method_details = $this->basic->get_data("affiliate_withdrawal_methods",['where'=>['id'=>$withdrawal_request_lists[$i]['method_id']]]);

            if($method_details[0]['payment_type'] == 'paypal') {
                $withdrawal_request_lists[$i]['method_id'] = "PayPal <span data-toggle='tooltip' title='".$this->lang->line('See details')."' class='text-primary pointer method_details' method_name='PayPal' details='".nl2br(htmlspecialchars($method_details[0]['paypal_email'],ENT_QUOTES))."'><i class='fas fa-info-circle'></i></span>";
                $withdrawal_request_lists[$i]['icon'] = '<i class="fab fa-paypal text-primary"></i> ';
                $withdrawal_request_lists[$i]['background'] = 'var(--blue)';
                $withdrawal_request_lists[$i]['payment_type'] = 'PayPal';

            } else if($method_details[0]['payment_type'] == 'bank_acc') {
                $withdrawal_request_lists[$i]['method_id'] = "Manual <span data-toggle='tooltip' title='".$this->lang->line('See details')."' class='text-primary pointer method_details' method_name='Manual' details='".nl2br(htmlspecialchars($method_details[0]['bank_acc_no'],ENT_QUOTES))."'><i class='fas fa-info-circle'></i></span>";
                $withdrawal_request_lists[$i]['icon'] = '<i class="fas fa-university text-primary"></i>';
                $withdrawal_request_lists[$i]['background'] = '#c765ab';
                $withdrawal_request_lists[$i]['payment_type'] = "Manual";
            }

            if($withdrawal_request_lists[$i]['created_at'] != "0000-00-00 00:00:00") {
                $withdrawal_request_lists[$i]['created_at'] = $withdrawal_request_lists[$i]['created_at'];
                $withdrawal_request_lists[$i]['created_at_ago'] = date_time_calculator($withdrawal_request_lists[$i]['created_at'],true);
            }

            if($withdrawal_request_lists[$i]['completed_at'] != "0000-00-00 00:00:00") {
                $withdrawal_request_lists[$i]['completed_at'] = date("M j, Y H:i:s",strtotime($withdrawal_request_lists[$i]['completed_at']));
                $withdrawal_request_lists[$i]['completed_at_ago'] = date_time_calculator($withdrawal_request_lists[$i]['completed_at'],true);
            }
            else {
                $withdrawal_request_lists[$i]['completed_at_ago'] = $this->lang->line('0 sec Ago');
                $withdrawal_request_lists[$i]['completed_at'] = $this->lang->line('Not yet');
            }

        }

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";

        $data['method_info'] = $this->basic->get_data("affiliate_withdrawal_methods",['where'=>['affiliate_id'=>$this->session->userdata("affiliate_userid")]]);

        $data['count_method_info'] = count($data['method_info']);

        // echo "<pre>"; print_r($withdrawal_request_lists); exit;

        $data['page_title'] = $this->lang->line("Withdrawal Requests");
        $data['withdrawal_requests'] = $withdrawal_request_lists;
        $data['page_links'] = $page_links;
        $data['per_page'] = ($per_page == count($total_withdrawal_requests)) ? 'all' : $per_page;
        $data['search_value'] = $search_value;
        $data['total_earned'] = $total_earn[0]['total_earn'];
        $data['profile_img'] = $total_earn[0]['profile_img'];
        $data['pending_money'] = $finalData;
        $data['transfered_money'] = $finalData2;
        $data['body'] = "withdrawals/requests";

        $this->_affiliate_viewcontroller($data);
    }

    public function get_requests_info()
    {
        $this->ajax_check();

        $table_id = $this->input->post("table_id",true);
        $affiliate_id = $this->session->userdata('affiliate_userid');

        if($table_id == "" || $table_id == 0) exit;

        $requests_info = $this->basic->get_data("affiliate_withdrawal_requests",['where'=>['id'=>$table_id,'affiliate_id'=>$affiliate_id]]);
        $requests_info = $requests_info[0];

        $get_method = $this->basic->get_data("affiliate_withdrawal_methods",['where'=>['id'=>$requests_info['method_id'],'affiliate_id'=>$affiliate_id]]);

        if($get_method[0]['payment_type'] == 'paypal') {

            $requests_info['method_name'] = $get_method[0]['payment_type'];
            $requests_info['method_details'] = "PayPal : ".$get_method[0]['paypal_email'];

        } else if($get_method[0]['payment_type'] == 'bank_acc') {
            $requests_info['method_name'] = $get_method[0]['payment_type'];
            $requests_info['method_details'] = "Bank Account : ".$get_method[0]['bank_acc_no'];
        }

        echo json_encode($requests_info);

    }

    public function issue_new_request()
    {
        $this->ajax_check();

        $responses = [];

        $withdrawal_requests = $this->input->post("withdrawal_account",true);
        $requested_amount = $this->input->post("requested_amount",true);
        $submit_action = $this->input->post("submit_action",true);
        $tableId = $this->input->post("tableId",true);
        $previous_amount = $this->input->post("previous_amount",true);
        $affiliate_id = $this->session->userdata('affiliate_userid');

        $affiliate_total_money = $this->basic->get_data("affiliate_users",['where'=>['id'=>$affiliate_id]],['total_earn']);
        $affiliator_total_earn = $affiliate_total_money[0]['total_earn'];

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $curency_icon= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";

        if($affiliator_total_earn == 0) {
            $responses['status'] = '0';
            $responses['response_error'] = $this->lang->line("Sorry, you can not issue new request because you have 0 balance.");
            echo json_encode($responses); exit;
        }

        $table          = 'affiliate_withdrawal_requests';
        $pending_data   = $this->basic->get_data($table,array('where'=>array('request_status' => '0','affiliate_id' => $affiliate_id)));
        $completed_data   = $this->basic->get_data($table,array('where'=>array('request_status' => '1','affiliate_id' => $affiliate_id)));

        // calculating total pending money of affiliator
        $pendingData = array_map(function ($value) {
            return $value['requested_amount'];
        },$pending_data);
        $pendingAmount = array_sum($pendingData);

        // calculating total completed money of affiliator
        $completedData = array_map(function ($value) {
            return $value['requested_amount'];
        },$completed_data);
        $transferedAmout = array_sum($completedData);

        if($requested_amount <= 0) {

            $responses['status'] = '0';
            $responses['response_error'] = $this->lang->line("Please provide a valid amount.");

            echo json_encode($responses); exit;

        }

        if($requested_amount > $affiliator_total_earn) {
            $responses['status'] = '0';
            $responses['response_error'] = $this->lang->line("You can not make request more than your total earn");

            echo json_encode($responses); exit;
        }

        if($transferedAmout == $affiliator_total_earn) {

            $responses['status'] = '0';
            $responses['response_error'] = $this->lang->line("Sorry, you can not issue new request because you have 0 balance.");

            echo json_encode($responses);exit;
        }


        if($submit_action == 'add') {

            // calculating not allowed money
            $pending_sum_transfered = $pendingAmount + $transferedAmout;

            $remaining_money_to_request = $affiliator_total_earn - $pending_sum_transfered;

            if($requested_amount > $remaining_money_to_request) {

                $responses['status'] = '0';
                $responses['response_error'] = $this->lang->line("You have")." ".$remaining_money_to_request.$curency_icon." ".$this->lang->line("left. You can not request more than your left balance.");

                echo json_encode($responses);exit;
            }

            $insert_data = [
                'affiliate_id' => $affiliate_id,
                'method_id' => $withdrawal_requests,
                'requested_amount' => $requested_amount,
                'request_status' => '0',
                'created_at' => date("Y-m-d H:i:s"),
                'completed_at' => "0000-00-00 00:00:00"
            ];

            if($this->basic->insert_data("affiliate_withdrawal_requests",$insert_data)) {

                $responses['status'] = '1';
                $responses['response_success'] = $this->lang->line("Your Request has been issued successfully.");

                $affiliate_email = $this->session->userdata("affiliate_email");
                $affiliate_name = $this->session->userdata("affiliate_name");
                $affiliate_username = $this->session->userdata("affiliate_username");

                // sending email to admin to notify
                $to = $this->config->item('institute_email');
                $from = $affiliate_email;
                $mask = $this->config->item("product_name");
                $html = 1;
                $productname = $this->config->item('product_name');

                $email_template_info = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>"new_withdrawal_request")),array('subject','message'));

                if(isset($email_template_info[0]) && $email_template_info[0]['subject'] != '' && $email_template_info[0]['message'] != '')
                {
                    // #AFFILIATOR_NAME#,#AFFILIATOR_EMAIL#,#REQUESTED_AMOUNT#
                    $subject = str_replace('#APP_NAME#',$productname,$email_template_info[0]['subject']);
                    $message = str_replace(array("#AFFILIATOR_NAME#","#AFFILIATOR_EMAIL#","#REQUESTED_AMOUNT#"),array($affiliate_name,$affiliate_email,$requested_amount),$email_template_info[0]['message']);

                } else
                {
                    $subject = $this->lang->line("Affiliate Withdrawal Request");
                    $message = "Dear Admin,<br> A new withdrawal request has been made by an affiliate. Please check the below information of the request.";
                    $message .= "<ul>
                        <li>".$this->lang->line("Affiliator Name").": {$affiliate_name}</li>
                        <li>".$this->lang->line("Affiliator Email").": {$affiliate_email}</li>
                        <li>".$this->lang->line("Requested Amount").": {$requested_amount}</li>
                    </ul>";

                }

                @$this->_mail_sender($from, $to, $subject, $message, $mask, $html);

                echo json_encode($responses);exit;
            }
        } else if($submit_action == 'edit') {

            if($previous_amount == '') $previous_amount = 0;

            // substract prev value from total pending value
            $pending_money_sub_prev = $pendingAmount - $previous_amount;
            $remaining_money = $pending_money_sub_prev + $transferedAmout;
            $can_request = $affiliator_total_earn - $remaining_money;

            if($requested_amount > $can_request) {

                $responses['status'] = '0';
                $responses['response_error'] = $this->lang->line("You have")." ".$can_request.$curency_icon." ".$this->lang->line("left. You can not request more than your left balance.");

                echo json_encode($responses);exit;

            }

            $update_data = [
                'affiliate_id' => $affiliate_id,
                'method_id' => $withdrawal_requests,
                'requested_amount' => $requested_amount,
                'request_status' => '0',
            ];

            $where2 = ['id'=>$tableId,'affiliate_id'=>$affiliate_id];

            if($this->basic->update_data("affiliate_withdrawal_requests",$where2,$update_data)) {
                $responses['status'] = '1';
                $responses['response_success'] = $this->lang->line("Your Request has been updated successfully.");

                echo json_encode($responses);exit;
            }

        }
    }


    public function delete_withdrawal_request()
    {
        $this->ajax_check();

        $table_id = $this->input->post("id",true);
        $affiliate_id = $this->session->userdata("affiliate_userid");

        if($table_id == "" || $table_id == 0) exit;

        if($this->basic->delete_data("affiliate_withdrawal_requests",['id'=>$table_id,'affiliate_id'=>$affiliate_id])) {
            echo "1";
        } else {
            echo "0";
        }
    }

    public function earnings()
    {
        if(!$this->is_logged_in()) redirect("affiliate_system/affiliate_login_page","location");

        $data = [];

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d", strtotime("$to_date - 30 days"));
        $month = date("m");
        $year = date("Y");
        $affiliate_id = $this->session->userdata("affiliate_userid");

        $table = "affiliate_earning_history";
        $where['where'] = ['affiliate_id'=>$affiliate_id,'event_date >='=>$from_date,'event_date <='=>$to_date];

        $earnings = $this->basic->get_data($table,$where,'','','','','date_format(event_date,"%Y-%m-%d") asc');

        $all_earnings = $this->basic->get_data($table,['where'=>['affiliate_id'=>$affiliate_id]],'','','','','date_format(event_date,"%Y-%m-%d") asc');

        $earning_chart_labels = array();
        $earning_chart_values = array();

        $from_date = strtotime($from_date);
        $to_date = strtotime($to_date);
        $array_month = array();
        $array_year = array();
        $payment_today=$payment_month=$payment_year=$payment_life=0;

        do 
        {
           $temp = date("Y-m-d",$from_date);
           $temp2 = date("j M",$from_date);;
           $earning_chart_values[$temp] = 0;
           $earning_chart_labels[] = $temp2;
           $from_date = strtotime('+1 day',$from_date); 
        } 
        while ($from_date <= $to_date);

        foreach ($earnings as $key => $value) 
        {

            $updated_at_formatted = date("Y-m-d",strtotime($value['event_date']));

            if(isset($earning_chart_values[$updated_at_formatted])) {
                $earning_chart_values[$updated_at_formatted] += $value["amount"];
            }
            else {
                $earning_chart_values[$updated_at_formatted] = $value["amount"];
            } 
        }

        $singup_earning = $payment_earning = $recurring_earning = 0;

        foreach ($all_earnings as $key1 => $value1) 
        {

            $mon = date("F",strtotime($value1['event_date']));
            $mon2 = date("m",strtotime($value1['event_date']));

            if(strtotime($value1['event_date']) == $to_date) $payment_today += $value1["amount"];

            if(date("m",strtotime($value1['event_date'])) == $month && date("Y",strtotime($value1['event_date'])) == $year) 
            {
                 $payment_month += $value1["amount"];
                 $event_date = date("jS M y",strtotime($value1['event_date']));

                 if(!isset($array_month[$event_date])) $array_month[$event_date] = 0;
                 $array_month[$event_date] += $value1["amount"];
            }

            if(date("Y",strtotime($value1['event_date'])) == $year) 
            {
                 $payment_year += $value1["amount"];
                 $payment_life += $value1["amount"];
                 if(!isset($array_year[$mon])) $array_year[$mon] = 0;
                 $array_year[$mon] += $value1["amount"];
            }

            if($value1['event'] == 'signup') {
                $singup_earning = $singup_earning + $value1['amount'];
            }

            if($value1['event'] == 'payment') {
                $payment_earning = $payment_earning + $value1['amount'];
            }

            if($value1['event'] == 'recurring') {
                $recurring_earning = $recurring_earning + $value1['amount'];
            }
        }

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";

        $data['earning_chart_labels'] = $earning_chart_labels;
        $data['earning_chart_values'] = $earning_chart_values;
        $data['payment_today'] = $payment_today;
        $data['payment_month'] = $payment_month;
        $data['payment_year'] = $payment_year;
        $data['payment_life'] = $payment_life;
        $data['array_month'] = $array_month;
        $data['array_year'] = $array_year;
        $data['singup_earning'] = $singup_earning;
        $data['payment_earning'] = $payment_earning;
        $data['recurring_earning'] = $recurring_earning;

        $data['body'] = "affiliate_earnings";
        $data['page_title'] = $this->lang->line("Earning Analysis");
        $data['page_title'] = $this->lang->line("Affiliate Earning");

        $this->_affiliate_viewcontroller($data);
    }

    public function visitor_reports()
    {
        if(!$this->is_logged_in()) redirect("affiliate_system/affiliate_login_page","location");

        $data = [];

        $data['body'] = "affiliate_visitor_reports";
        $data['page_title'] = $this->lang->line("Affiliate Visitor Analysis");

        $affiliate_id = $this->session->userdata("affiliate_userid");

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime($to_date." - 30 days"))." 00:00:00";
        $current_month = date("Y-m");
        $click_list = array();
        $signup_list = array();
        $click_signup_date_list = array();

        $table = "affiliate_visitors_action";
        $where['where'] = ['affiliate_id'=>$affiliate_id,'clicked_time >='=>$from_date];

        $select = array('count(id) as visitor','date_format(clicked_time,"%Y-%m-%d") as visited_at','type','ip_address','user_id');
        $visitor_info = $this->basic->get_data($table,$where,$select,'','','','date_format(clicked_time,"%Y-%m-%d") asc','date_format(clicked_time,"%Y-%m-%d"),type');

        $where22 = array('where' => array('affiliate_id'=>$affiliate_id,'date_format(clicked_time,"%Y-%m")' => $current_month));
        $total_visitor_info = $this->basic->get_data($table,$where22);

        // calculating total pending money of affiliator
        $link_clicked = $signedUp = 0;
        foreach ($total_visitor_info as $visitors) {
            if($visitors['type'] == 'click') {
                $link_clicked++;
            }
            if($visitors['type'] == 'signup') {
                $signedUp++;
            }
        }

        foreach($visitor_info as $value)
        {
            if($value['type'] == 'click')
                $click_list[$value['visited_at']] = $value['visitor'];
            else if($value['type'] == 'signup')
                $signup_list[$value['visited_at']] = $value['visitor'];

            if(!isset($click_list[$value['visited_at']])) $click_list[$value['visited_at']] = 0;
            if(!isset($signup_list[$value['visited_at']])) $signup_list[$value['visited_at']] = 0;

            $formated_date = date("jS M",strtotime($value['visited_at']));
            $click_signup_date_list[$value['visited_at']] = $formated_date;
        }

        $largest_values = array();
        $max_value = 1;
        if(!empty($click_list)) array_push($largest_values, max($click_list));
        if(!empty($signup_list)) array_push($largest_values, max($signup_list));
        if(!empty($largest_values)) $max_value = max($largest_values);
        if($max_value > 10) $data['step_size'] = floor($max_value/10);
        else $data['step_size'] = 1;

        // get last 30 days visitors
        $today = date("Y-m-d");
        $prev_day = date('Y-m-d', strtotime($today. ' - 30 days'))." 00:00:00";

        $last_visited_history_info = $this->basic->get_data('affiliate_visitors_action',array('where'=>array('affiliate_id'=>$affiliate_id,'clicked_time >='=>$prev_day)),$select='',$join='',$limit='',$start=NULL,$order_by='clicked_time DESC'); 
        
        $count_info = count($last_visited_history_info);

        for($i=0; $i < $count_info; $i++) {

            if($last_visited_history_info[$i]['type'] == 'click') {
                $last_visited_history_info[$i]['type'] = "<div class='badge badge-primary font-weight-bold'>".$this->lang->line('Click')."</div>";
            } else if($last_visited_history_info[$i]['type'] == 'signup') {
                $last_visited_history_info[$i]['type'] = "<div class='badge badge-danger font-weight-bold'>".$this->lang->line('Signup')."</div>";
            } else if($last_visited_history_info[$i]['type'] == 'payment') {
                $last_visited_history_info[$i]['type'] = "<div class='badge badge-success font-weight-bold'>".$this->lang->line('Payment')."</div>";

            }
        }

        // echo "<pre>"; print_r($last_visited_history_info); exit;

        $data['info'] = $last_visited_history_info;
        $data['click_list'] = $click_list;
        $data['signup_list'] = $signup_list;
        $data['link_clicked'] = $link_clicked;
        $data['signedUp'] = $signedUp;
        $data['click_signup_date_list'] = $click_signup_date_list;

        $this->_affiliate_viewcontroller($data);
    }

    public function get_visitors_date_wise_data()
    {
        $this->ajax_check();
        $period = $this->input->post('period',true);
        $today = date("Y-m-d");
        $last_seven_day = date("Y-m-d", strtotime("$today - 7 days"));
        $this_month = date("Y-m");
        $this_year = date("Y");

        $affiliate_id = $this->session->userdata("affiliate_userid");

        $where_simple = array();
        $where_simple['affiliate_id'] = $affiliate_id;

        if($period == 'today')
            $where_simple['date_format(clicked_time,"%Y-%m-%d")'] = $today;
        else if($period == 'week')
            $where_simple['date_format(clicked_time,"%Y-%m-%d") >='] = $last_seven_day;
        else if($period == 'month')
            $where_simple['date_format(clicked_time,"%Y-%m")'] = $this_month;
        else if($period == 'year')
            $where_simple['date_format(clicked_time,"%Y")'] = $this_year;

        $where = array('where' => $where_simple);

        $get_visitor_number = $this->basic->get_data('affiliate_visitors_action',$where);

        // calculating total pending money of affiliator
        $link_clicked = $signedUp = 0;
        foreach ($get_visitor_number as $visitors) {
            if($visitors['type'] == 'click') {
                $link_clicked++;
            }
            if($visitors['type'] == 'signup') {
                $signedUp++;
            }
        }

        echo json_encode(array("link_visited_number"=>$link_clicked,"signup_number"=>$signedUp));
    }


    public function delete_visitor_old_log()
    {       
        $this->ajax_check();

        $table_name = "affiliate_visitors_action";
        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date - 30 days"));
        $from_date = $from_date." 23:59:59";
        $where = array('affiliate_id'=>$this->session->userdata("affiliate_userid"),'clicked_time <' => $from_date);
        if($this->basic->delete_data($table_name,$where)) {
            echo json_encode(array("status"=>"1","message"=>$this->lang->line("Log has been deleted successfully"))); 
        }
        else {
            echo json_encode(array("status"=>"0","message"=>$this->lang->line("Something went wrong, please try again")));
        }
    }

    function unique_username_check($str, $edited_id)
    {
        $username= strip_tags(trim($this->input->post('username',TRUE)));
        if($username==""){
            $s= $this->lang->line("required");
            $s=str_replace("<b>%s</b>","",$s);
            $s="<b>".$this->lang->line("username")."</b> ".$s;
            $this->form_validation->set_message('unique_username_check', $s);
            return FALSE;
        }
        
        if(!isset($edited_id) || !$edited_id)
            $where=array("username"=>$username);
        else        
            $where=array("username"=>$username,"id !="=>$edited_id);
        
        
        $is_unique=$this->basic->is_unique("affiliate_users",$where,$select='');
        
        if (!$is_unique) {
            $s = $this->lang->line("is_unique");
            $s=str_replace("<b>%s</b>","",$s);
            $s="<b>".$this->lang->line("username")."</b> ".$s;
            $this->form_validation->set_message('unique_username_check', $s);
            return FALSE;
        }
                
        return TRUE;
    }

    function unique_email_check($str, $edited_id)
    {
        $email= strip_tags(trim($this->input->post('email',TRUE)));
        if($email==""){
            $s= $this->lang->line("required");
            $s=str_replace("<b>%s</b>","",$s);
            $s="<b>".$this->lang->line("email")."</b> ".$s;
            $this->form_validation->set_message('unique_email_check', $s);
            return FALSE;
        }
        
        if(!isset($edited_id) || !$edited_id)
            $where=array("email"=>$email);
        else        
            $where=array("email"=>$email,"id !="=>$edited_id);
        
        
        $is_unique=$this->basic->is_unique("affiliate_users",$where,$select='');
        
        if (!$is_unique) {
            $s = $this->lang->line("is_unique");
            $s=str_replace("<b>%s</b>","",$s);
            $s="<b>".$this->lang->line("email")."</b> ".$s;
            $this->form_validation->set_message('unique_email_check', $s);
            return FALSE;
        }
                
        return TRUE;
    }

    public function profile()
    {
        if(!$this->is_logged_in()) redirect("affiliate_system/affiliate_login_page","location");

        $data = [];
        $data['body'] = "affiliate_profile";
        $data['page_title'] = $this->lang->line("Profile");

        $affiliate_id = $this->session->userdata("affiliate_userid");

        $info = $this->basic->get_data("affiliate_users",['where'=>['id'=>$affiliate_id]]);

        $data['info'] = $info;

        $this->_affiliate_viewcontroller($data);
    }

    public function edit_profile_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        if ($_POST) 
        {
            // validation
            $this->form_validation->set_rules('name',                '<b>'.$this->lang->line("name").'</b>',             'trim|required');
            $this->form_validation->set_rules('username',                '<b>'.$this->lang->line("Username").'</b>',   'trim|required|callback_unique_username_check['.$this->session->userdata('affiliate_userid').']');
            $this->form_validation->set_rules('address',             '<b>'.$this->lang->line("address").'</b>',          'trim');
            $this->form_validation->set_rules('email',               '<b>'.$this->lang->line("email").'</b>',      'trim|required|valid_email|callback_unique_email_check['.$this->session->userdata('affiliate_userid').']');

            $this->form_validation->set_rules('mobile',             '<b>'.$this->lang->line("Phone").'</b>',          'trim');
            
            if ($this->form_validation->run() == false) 
            {
                return $this->profile();
            } 
            else 
            {
                // assign
                $this->csrf_token_check();
                $name = addslashes(strip_tags($this->input->post('name', true)));
                $username = addslashes(strip_tags($this->input->post('username', true)));
                $email = addslashes(strip_tags($this->input->post('email', true)));
                $address = addslashes(strip_tags($this->input->post('address', true)));
                $mobile = addslashes(strip_tags($this->input->post('mobile', true)));
                $base_path = FCPATH . 'upload/affiliator';
                if(!file_exists($base_path)) mkdir($base_path,0755);

                $this->load->library('upload');

                $photo="";
                if ($_FILES['logo']['size'] != 0) {
                    $photo = $this->session->userdata("affiliate_userid").".png";
                    $config = array(
                        "allowed_types" => "png",
                        "upload_path" => $base_path,
                        "overwrite" => true,
                        "file_name" => $photo,
                        'max_size' => '200',
                        'max_width' => '500',
                        'max_height' => '500'
                        );
                    $this->upload->initialize($config);
                    $this->load->library('upload', $config);

                    if (!$this->upload->do_upload('logo')) {
                        $this->session->set_userdata('logo_error', $this->upload->display_errors());
                        return $this->profile();
                    }
                }

                $update_data=array
                (
                    "name"=>$name,
                    "username"=>$username,
                    "email"=>$email,
                    "address"=>$address,
                    "mobile"=>$mobile
                );

                if($photo!="") $update_data["profile_img"] = $photo;
        
                $this->basic->update_data("affiliate_users",array("id"=>$this->session->userdata("affiliate_userid")),$update_data);
                     
                $this->session->set_flashdata('success_message', 1);
                redirect('affiliate_system/profile', 'location');
            }
        }
    }

    public function reset_password()
    {
        $data['page_title'] = $this->lang->line("Change Password");
        $data['body'] = 'affiliate/password_reset_form';
        $this->_aff_subscription_viewcontroller($data);
    }

    public function reset_password_action()
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('access_forbidden', 'location');
        }

        $this->csrf_token_check();

        $this->form_validation->set_rules('old_password', '<b>'.$this->lang->line("Old Password").'</b>', 'trim|required');
        $this->form_validation->set_rules('new_password', '<b>'.$this->lang->line("New Password").'</b>', 'trim|required');
        $this->form_validation->set_rules('confirm_new_password', '<b>'.$this->lang->line("Confirm Password").'</b>', 'trim|required|matches[new_password]');
        if ($this->form_validation->run() == false) {
            $this->reset_password();
        } else {
            $user_id = $this->session->userdata("affiliate_userid");
            $password = $this->input->post('old_password', true);
            $new_password = $this->input->post('new_password', true);
            $table = 'affiliate_users';
            $where['where'] = array(
                'id' => $user_id,
                'password' => md5($password)
                );
            $select = array('');
            if ($this->basic->get_data($table, $where, $select)) {
                $where = array(
                    'id' => $user_id,
                    'password' => md5($password)
                    );
                $data = array('password' => md5($new_password));
                $this->basic->update_data($table, $where, $data);
                $this->session->sess_destroy();
                $this->session->set_flashdata('reset_success', $this->lang->line('Please login with new password'));
                redirect('affiliate_system/affiliate_login_page', 'location');
                // echo $this->session->userdata('reset_success');exit();
            } else {
                $this->session->set_userdata('error', $this->lang->line('The old password you have given is wrong'));
                $this->reset_password();
            }
        }
    }


    public function forget_password()
    {
        $data["page_title"] = $this->lang->line("Password Recovery");

        $data['body'] = "affiliate/forgot_password";

        $this->_aff_subscription_viewcontroller($data);
    }


    public function recovery_code_genaration()
    {
        $this->ajax_check();

        $email = trim($this->input->post('email',true));
        $result = $this->basic->get_data('affiliate_users', array('where' => array('email' => $email)), array('count(*) as num'));

        if ($result[0]['num'] == 1) {
            //entry to forget_password table
            $expiration = date("Y-m-d H:i:s", strtotime('+1 day', time()));
            $code = $this->_random_number_generator();
            $url = site_url().'affiliate_system/password_recovery';
            $url_final="<a href='".$url."' target='_BLANK'>".$url."</a>";
            $productname = $this->config->item('product_name');

            $table = 'affiliate_forgot_password';
            $info = array(
                'confirmation_code' => $code,
                'email' => $email,
                'expiration' => $expiration
                );

            if ($this->basic->insert_data($table, $info)) {

                //email to user
                $email_template_info = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'reset_password')),array('subject','message'));

                if(isset($email_template_info[0]) && $email_template_info[0]['subject'] != '' && $email_template_info[0]['message'] != '') {

                    $subject = str_replace('#APP_NAME#',$productname,$email_template_info[0]['subject']);
                    $message =str_replace(array("#APP_NAME#","#PASSWORD_RESET_URL#","#PASSWORD_RESET_CODE#"),array($productname,$url_final,$code),$email_template_info[0]['message']);

                } else {

                    $subject = $productname." | Affiliate Password recovery";
                    $message = "<p>".$this->lang->line('to reset your affiliate password please perform the following steps')." : </p>
                                <ol>
                                    <li>".$this->lang->line("go to this url")." : ".$url_final."</li>
                                    <li>".$this->lang->line("enter this code")." : ".$code."</li>
                                    <li>".$this->lang->line("reset your password")."</li>
                                </ol>
                                <h4>".$this->lang->line("link and code will be expired after 24 hours")."</h4>";

                }


                $from = $this->config->item('institute_email');
                $to = $email;
                $mask = $this->config->item("product_name");
                $html = 1;
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html);
            }
        } else {
            echo 0;
        }
    }

    public function password_recovery()
    {
        $data['page_title']=$this->lang->line("password recovery");

        $data['body'] = "affiliate/password_recovery";

        $this->_aff_subscription_viewcontroller($data);
    }


    public function recovery_check()
    {
        $this->ajax_check();
        if ($_POST) {
            $code=trim($this->input->post('code', true));
            $newp=md5($this->input->post('newp', true));
            $conf=md5($this->input->post('conf', true));

            if($code=="" || $newp=="" || $conf=="" || ($newp != $conf) )
            {
                echo 0;
                exit();
            }

            $table='affiliate_forgot_password';
            $where['where']=array('confirmation_code'=>$code,'success'=>0);
            $select=array('email','expiration');

            $result=$this->basic->get_data($table, $where, $select);

            if (empty($result)) {
                echo 0;
            } else {
                foreach ($result as $row) {
                    $email=$row['email'];
                    $expiration=$row['expiration'];
                }

                $now=time();
                $exp=strtotime($expiration);

                if ($now>$exp) {
                    echo 1;
                } else {
                    $student_info_where['where'] = array('email'=>$email);
                    $student_info_select = array('id');
                    $student_info_id = $this->basic->get_data('affiliate_users', $student_info_where, $student_info_select);
                    $this->basic->update_data('affiliate_users', array('id'=>$student_info_id[0]['id']), array('password'=>$newp));
                    $this->basic->update_data('affiliate_forgot_password', array('confirmation_code'=>$code), array('success'=>1));
                    echo 2;
                }
            }
        }
    }


    /**
    * Manage Affiliate Section
    * Retrieve, saves into database
    */
    public function affiliate_payment_settings()
    {
        if(!$this->addon_exist("affiliate_system")) {
            redirect("access_forbidden","location");
        }

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        $data['page_title'] = $this->lang->line("Affiliate Commission Settings");
        $data['body'] = "affiliate_users_settings/affiliate_payment_settings";

        $table = "affiliate_payment_settings";
        $existing_info = $this->basic->get_data($table);
        $data['info'] = isset($existing_info[0]) ? $existing_info[0]:[];

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";


        $this->_viewcontroller($data);
    }

    public function affiliate_payment_settings_action()
    {
        if(!$this->addon_exist("affiliate_system")) {
            redirect("access_forbidden","location");
        }

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        // validation
        $this->form_validation->set_rules('signup_commission',   '<b>'.$this->lang->line("Signup Commission").'</b>',    'trim');
        if($this->input->post("signup_commission") == '1') {
            $this->form_validation->set_rules('signup_amount',       '<b>'.$this->lang->line("Signup Amount").'</b>',    'trim|required');
        }
        
        $this->form_validation->set_rules('payment_commission',       '<b>'.$this->lang->line("Payment Commission").'</b>',        'trim');
        $this->form_validation->set_rules('payment_type',       '<b>'.$this->lang->line("Payment Type").'</b>',  'trim');
        $this->form_validation->set_rules('fixed_amount',       '<b>'.$this->lang->line("Fixed Amount").'</b>',  'trim');
        $this->form_validation->set_rules('percent_amount',       '<b>'.$this->lang->line("Percentage").'</b>',  'trim');
        $this->form_validation->set_rules('is_recurring',   '<b>'.$this->lang->line("Recurring").'</b>',    'trim');


        // go to config form page if validation wrong
        if ($this->form_validation->run() == false) 
        {
            return $this->affiliate_payment_settings();
        } 
        else {

            $signup_commission = $this->input->post("signup_commission",true);
            $signup_amount = trim($this->input->post("signup_amount",true));
            $payment_commission = $this->input->post("payment_commission",true);
            $payment_type = $this->input->post("payment_type",true);
            $fixed_amount = trim($this->input->post("fixed_amount",true));
            $percent_amount = trim($this->input->post("percent_amount",true));
            $is_recurring = $this->input->post("is_recurring",true);

            if($signup_commission == "") {
                $signup_commission = '0';
                $signup_amount = "";
            }

            if($payment_commission == "") $payment_commission = '0';
            if($is_recurring == '') $is_recurring = '0';

            if($payment_commission == '1') {

                if($payment_type == 'fixed') {
                    $percent_amount = '';
                } else if($payment_type == 'percentage') {
                    $fixed_amount = '';
                }

            } else {
                $payment_type = '';
                $percent_amount = '';
                $fixed_amount = '';
            }


            $update_data = [];
            $update_data['user_id'] = 0;
            $update_data['signup_commission'] = $signup_commission;
            $update_data['sign_up_amount'] = $signup_amount;
            $update_data['payment_commission'] = $payment_commission;
            $update_data['payment_type'] = $payment_type;
            $update_data['fixed_amount'] = $fixed_amount;
            $update_data['percentage'] = $percent_amount;
            $update_data['is_recurring'] = $is_recurring;

            $get_data = $this->basic->get_data("affiliate_payment_settings");

            if(isset($get_data[0])) {
                $this->basic->update_data("affiliate_payment_settings",array("id >"=>0),$update_data);
            }
            else { 
                $this->basic->insert_data("affiliate_payment_settings",$update_data);                 
            }

            $this->session->set_flashdata('success_message', 1);
            redirect('affiliate_system/affiliate_payment_settings', 'location');
        }

    }

    public function all_withdrawal_requests()
    {
        if(!$this->addon_exist("affiliate_system")) {
            redirect("access_forbidden","location");
        }

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $data['curency_icon']= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";

        $data['body'] = "affiliate_users_settings/request_lists";
        $data['page_title'] = $this->lang->line("Affiliate Withdrawal Requests");

        $this->_viewcontroller($data);

    }

    public function all_requests_data()
    {
        $this->ajax_check();

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        $display_columns = array("#",'id','email','method','total_earn','requested_amount','request_status','created_at','completed_at');
        $request_date_range = $this->input->post("request_date_range");
        $request_status = $this->input->post("search_request_status");


        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'affiliate_withdrawal_requests.id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple=array();

        if($request_date_range!="")
        {
            $exp = explode('|', $request_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date   = isset($exp[1])?$exp[1]:"";

            if($from_date!="Invalid date" && $to_date!="Invalid date")
            {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date   = date('Y-m-d', strtotime($to_date));
                $where_simple["Date_Format(affiliate_withdrawal_requests.created_at,'%Y-%m-%d') >="] = $from_date;
                $where_simple["Date_Format(affiliate_withdrawal_requests.created_at,'%Y-%m-%d') <="] = $to_date;
            }
        }

        if($request_status !="") $where_simple['affiliate_withdrawal_requests.request_status'] = $request_status;

        $table = "affiliate_withdrawal_requests";
        $join = ["affiliate_users"=>"affiliate_withdrawal_requests.affiliate_id=affiliate_users.id,left"];
        $select = ["affiliate_withdrawal_requests.*","affiliate_users.id AS affiliate_userid","affiliate_users.email","affiliate_users.total_earn"];

        if($request_status !="") $where_simple['affiliate_withdrawal_requests.request_status'] = $request_status;

        $where  = array('where'=>$where_simple);

        $info = $this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');

        $total_rows_array = $this->basic->count_row($table,$where='',$count=$table.".id",$join,$group_by='');
        $total_result = $total_rows_array[0]['total_rows'];

        $count_requests = count($info);

        $base_url=base_url();

        $request_status_arr = array("0"=>$this->lang->line('pending'),"1"=>$this->lang->line("Approved"),"2"=>$this->lang->line("Canceled"));

        for ($i=0; $i < count($info) ; $i++) 
        { 

            $info[$i]['email'] = '<a href="'.base_url("affiliate_system/edit_affiliate/{$info[$i]['affiliate_userid']}").'">'.$info[$i]['email'].'</a>';

            $status = $info[$i]['request_status'];
            $methodId = $info[$i]['method_id'];
            $get_method = $this->basic->get_data("affiliate_withdrawal_methods",['where'=>['id'=>$methodId]],['payment_type','paypal_email','bank_acc_no']);

            if($get_method[0]['payment_type'] == 'paypal') {
                $info[$i]['method'] = "<div style='min-width:70px;' class='text-center'>PAYPAL <span data-toggle='tooltip' title='".$this->lang->line('See details')."' class='text-primary pointer method_details' method_name='PayPal' details='".$get_method[0]['paypal_email']."'><i class='fas fa-info-circle'></i></span></div>";
            }

            if($get_method[0]['payment_type'] == 'bank_acc') {
                $info[$i]['method'] = "<div style='min-width:70px;' class='text-center'>MANUAL <span data-toggle='tooltip' title='".$this->lang->line('See details')."' class='text-primary pointer method_details' method_name='Manual' details='".nl2br(htmlspecialchars($get_method[0]['bank_acc_no'],ENT_QUOTES))."'><i class='fas fa-info-circle'></i></span></div>";
            }


            if($info[$i]['created_at'] != "0000-00-00 00:00:00")
                $info[$i]['created_at'] = "<div style='min-width:100px !important;'>".date("M j, Y",strtotime($info[$i]['created_at']))."</div>";

            if($info[$i]['completed_at'] != "0000-00-00 00:00:00") {
                $info[$i]['completed_at'] = "<div style='min-width:100px'>".date("M j, Y",strtotime($info[$i]['completed_at']))."</div>";
            } else {
                $info[$i]['completed_at'] = "<div class='text-muted'><i class='fas fa-exclamation-circle'></i> ".$this->lang->line('Not Yet')."</div>";
            }

            $disbale_btn = '';
            if($status == '1' || $status == '2') $disbale_btn = 'disabled'; 

            $info[$i]['request_status'] = form_dropdown('request_status', $request_status_arr, $status,'class="text-muted select2 request_status" '.$disbale_btn.' style="width:120px !important;font-size:12px;" amount="'.$info[$i]['requested_amount'].'" affiliate_id="'.$info[$i]['affiliate_id'].'" request_id="'.$info[$i]['id'].'" id="request_status"')."<script>$('[data-toggle=\"tooltip\"]').tooltip();$('.request_status').select2();</script>";            

            $info[$i]['total_earn'] = "<div style=''><span class=''>".$info[$i]['total_earn']."</span></div>";           

            $info[$i]['requested_amount'] = "<div style=''><span class=''>".$info[$i]['requested_amount']."</span></div>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }


    public function affiliate_users()
    {
        if(!$this->addon_exist("affiliate_system")) {
            redirect("access_forbidden","location");
        }

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }


        $data['body'] = "affiliate_users_settings/affiliate_users";
        $data['page_title'] = $this->lang->line("Affiliate Users");

        $this->_viewcontroller($data);
    }

    public function affiliate_users_data()
    {
        $this->ajax_check();
        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','avatar','name', 'email','status', 'add_date','last_login_at','last_login_ip', 'actions');
        $search_columns = array('name', 'email','mobile','add_date','last_login_ip');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where = array();
        if ($search_value != '') 
        {
            $or_where = array();
            foreach ($search_columns as $key => $value) 
                $or_where[$value.' LIKE '] = "%$search_value%";
            $where = array('or_where' => $or_where);
        }

        $table="affiliate_users";
        $info=$this->basic->get_data($table,'','','',$limit,$start,$order_by,$group_by='');
        $total_rows_array=$this->basic->count_row($table,$where='',$count="id",$join='',$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];
        // echo "<pre>"; print_r($info); exit;

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $curency_icon= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";

        $i=0;
        $base_url=base_url();
        foreach ($info as $key => $value) 
        {
            $status = $info[$i]["status"];
            if($status=='1') $info[$i]["status"] = '<div class="text-success" style="min-width:70px;"><i class="fas fa-circle"></i> '.$this->lang->line('Active');
            else $info[$i]["status"] = '<div class="text-danger" style="min-width:70px;"><i class="fas fa-circle"></i> '.$this->lang->line('Inactive')."</span>";

            $last_login_at = $info[$i]["last_login_at"];
            if($last_login_at=='0000-00-00 00:00:00') $info[$i]["last_login_at"] = $this->lang->line("Never");
            else $info[$i]["last_login_at"] = date("jS M y H:i",strtotime($info[$i]["last_login_at"]));

            $info[$i]["add_date"] = date("jS M y",strtotime($info[$i]["add_date"]));

            $user_name = $info[$i]["name"];
            $user_id = $info[$i]["id"];
            $str="";  

            $report_btn = "<a target='_BLANK' class='btn btn-circle btn-outline-primary' data-toggle='tooltip' title='".$this->lang->line('Info')."' href='".$base_url.'affiliate_system/request_info/'.$user_id."'>".'<i class="fas fa-eye"></i>'."</a>";
            $edit_btn = "<a class='btn btn-circle btn-outline-warning' data-toggle='tooltip' title='".$this->lang->line('Edit')."' href='".$base_url.'affiliate_system/edit_affiliate/'.$user_id."'>".'<i class="fas fa-edit"></i>'."</a>";
            $change_password = "<a class='btn btn-circle btn-outline-dark change_password' href='' data-toggle='tooltip' title='".$this->lang->line('Change affiliate Password')."' data-id='".$user_id."' data-user='".htmlspecialchars($user_name)."'>".'<i class="fas fa-key"></i>'."</a>";
            $delete_btn = "<a href='".$base_url.'affiliate_system/delete_affiliate/'.$user_id."' class='delete_affiliate btn btn-circle btn-outline-danger' csrf_token='".$this->session->userdata('csrf_token_session')."' data-toggle='tooltip' title='".$this->lang->line('Delete')."'>".'<i class="fa fa-trash"></i>'."</a>";

            $logo=$info[$i]["profile_img"];

            if($logo=="") $logo=base_url("assets/img/avatar/avatar-1.png");
            else $logo=base_url().'upload/affiliator/'.$logo;

            $info[$i]["avatar"] = "<img src='".$logo."' width='40px' height='40px' class='rounded-circle'>";

            $info[$i]['name'] = "<div data-toggle='tooltip'>".$info[$i]['name']." </div><script> $('[data-toggle=\"tooltip\"]').tooltip();</script>";

            if($info[$i]['mobile'] == "") $info[$i]['mobile'] = "-";

            if($this->is_demo=='1')  $info[$i]["email"] ="******@*****.***";
            if($this->is_demo=='1')  $info[$i]["last_login_ip"] ="XXXXXXXXX";

            $action_width = (4*47)+20;
            $info[$i]['actions'] ='
            <div class="dropdown d-inline dropright text-center">
              <button class="btn btn-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-briefcase"></i>
              </button>
              <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';
                $info[$i]['actions'] .= $report_btn;
                $info[$i]['actions'] .= $edit_btn;
                $info[$i]['actions'] .= $change_password;
                $info[$i]['actions'] .= $delete_btn;
                $info[$i]['actions'] .="
              </div>
            </div>
            <script>
            $('[data-toggle=\"tooltip\"]').tooltip();</script>";

            $i++;
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="user_id");

        echo json_encode($data);

    }

    public function add_affiliate()
    {

        if(!$this->addon_exist("affiliate_system")) {
            redirect("access_forbidden","location");
        }

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        $data['body'] = 'affiliate_users_settings/add_affiliate';
        $data['page_title'] = $this->lang->line("Add Affiliate");

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";

        $table = "affiliate_payment_settings";
        $existing_info = $this->basic->get_data($table);
        $data['info'] = isset($existing_info[0]) ? $existing_info[0]:[];

        $this->_viewcontroller($data);

    }

    public function add_affiliate_action()
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] === 'GET') 
            redirect('home/access_forbidden','location');

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        if($_POST)
        {
            $this->form_validation->set_rules('name', '<b>'.$this->lang->line("Full Name").'</b>', 'trim');      
            $this->form_validation->set_rules('username', '<b>'.$this->lang->line("username").'</b>', 'trim|required|is_unique[affiliate_users.username]|callback_alpha_numeric_underscore',
                array(
                    'alpha_numeric_underscore' => $this->lang->line('Username is required and only characters, underscores, digits are allowed'),
                    'is_unique'                => $this->lang->line('username already exists')
                )
            );
            $this->form_validation->set_rules('email', '<b>'.$this->lang->line("Email").'</b>', 'trim|required|valid_email|is_unique[affiliate_users.email]');    
            $this->form_validation->set_rules('mobile', '<b>'.$this->lang->line("Mobile").'</b>', 'trim');      
            $this->form_validation->set_rules('password', '<b>'.$this->lang->line("Password").'</b>', 'trim|required');      
            $this->form_validation->set_rules('confirm_password', '<b>'.$this->lang->line("Confirm Password").'</b>', 'trim|required|matches[password]');      
            $this->form_validation->set_rules('address', '<b>'.$this->lang->line("Address").'</b>', 'trim');      
            $this->form_validation->set_rules('status', '<b>'.$this->lang->line("Status").'</b>', 'trim');
                
            if ($this->form_validation->run() == FALSE)
            {
                $this->add_affiliate(); 
            }
            else
            {               
                $this->csrf_token_check();

                $name=strip_tags($this->input->post('name',true));
                $username = strip_tags($this->input->post('username',true));
                $email=strip_tags($this->input->post('email',true));
                $mobile=strip_tags($this->input->post('mobile',true));
                $password=md5($this->input->post('password',true));
                $address=strip_tags($this->input->post('address',true));
                $status = $this->input->post('status',true);
                $is_overwritten = $this->input->post('is_overwritten',true);
                $signup_commission = $this->input->post('signup_commission',true);
                $is_payment = $this->input->post('is_payment',true);
                $signup_amount = $this->input->post('signup_amount',true);
                $payment_type = $this->input->post('payment_type',true);
                $fixed_amount = $this->input->post('fixed_amount',true);
                $percent_amount = $this->input->post('percent_amount',true);
                $is_recurring = $this->input->post('is_recurring',true);
                
                // if status is unchecked
                if($status=='') $status='0';
                if($is_recurring == '') $is_recurring = '0';
                if($is_overwritten == '') $is_overwritten = '0';
                if($signup_commission == '') $signup_commission = '0';
                if($is_payment == '') $is_payment = '0';

                if($payment_type == NULL) $payment_type = '';
                if($payment_type == 'fixed') $percent_amount = '';
                if($payment_type == 'percentage') $fixed_amount = '';
                                                       
                $data=array(
                    'name'=>$name,
                    'username'=>$name,
                    'email'=>$email,
                    'mobile'=>$mobile,
                    'password'=>$password,
                    'address'=>$address,
                    'status'=>$status,
                    'add_date' => date("Y-m-d H:i:s"),
                    'is_overwritten' => $is_overwritten,
                    'is_signup_commission' => $signup_commission,
                    'signup_amount' => $signup_amount,
                    'is_payment' => $is_payment,
                    'payment_type' => $payment_type,
                    'fixed_amount' => $fixed_amount,
                    'percentage_amount' => $percent_amount,
                    'is_recurring' => $is_recurring,
                );

                if($this->basic->insert_data('affiliate_users',$data)) $this->session->set_flashdata('success_message',1);   
                else $this->session->set_flashdata('error_message',1);     
                
                redirect('affiliate_system/affiliate_users','location');                 
                
            }
        }  
    }


    public function edit_affiliate($id=0)
    {       
        if(!$this->addon_exist("affiliate_system")) {
            redirect("access_forbidden","location");
        }

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        $data['body']='affiliate_users_settings/edit_affiliate';     
        $data['page_title']=$this->lang->line('Edit Affiliate'); 

        $xdata = $this->basic->get_data('affiliate_users',array("where"=>array("id"=>$id)));

        if(!isset($xdata[0])) {
            redirect("error_404","location");
        }

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";

        $table = "affiliate_payment_settings";
        $existing_info = $this->basic->get_data($table);
        $data['info'] = isset($existing_info[0]) ? $existing_info[0]:[];

        $data['xdata'] = $xdata[0];
        $this->_viewcontroller($data);
    }

    public function edit_affiliate_action()
    {

        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] === 'GET') 
            redirect('home/access_forbidden','location');

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        if($_POST)
        {
            $id = $this->input->post("affiliate_id",true);
            $this->form_validation->set_rules('name', '<b>'.$this->lang->line("Full Name").'</b>', 'trim');     

            $unique_username = "affiliate_users.email.".$id;
            $this->form_validation->set_rules('username', '<b>'.$this->lang->line("username").'</b>', "trim|required|is_unique[$unique_username]|callback_alpha_numeric_underscore",
                array(
                    'alpha_numeric_underscore' => $this->lang->line('Username is required and only characters, underscores, digits are allowed'),
                    'is_unique'                => $this->lang->line('username already exists')
                )
            );

            $unique_email = "affiliate_users.email.".$id; 
            $this->form_validation->set_rules('email', '<b>'.$this->lang->line("Email").'</b>', "trim|required|valid_email|is_unique[$unique_email]");  
            $this->form_validation->set_rules('mobile', '<b>'.$this->lang->line("Mobile").'</b>', 'trim');        
            $this->form_validation->set_rules('address', '<b>'.$this->lang->line("Address").'</b>', 'trim');      
            $this->form_validation->set_rules('status', '<b>'.$this->lang->line("Status").'</b>', 'trim');
                
            if ($this->form_validation->run() == FALSE)
            {
                $this->edit_affiliate($id); 
            }
            else
            {               
                $this->csrf_token_check();

                $name=strip_tags($this->input->post('name',true));
                $username = strip_tags($this->input->post('username',true));
                $email=strip_tags($this->input->post('email',true));
                $mobile=strip_tags($this->input->post('mobile',true));
                $address=strip_tags($this->input->post('address',true));
                $status = $this->input->post('status',true);
                $is_overwritten = $this->input->post('is_overwritten',true);
                $signup_commission = $this->input->post('signup_commission',true);
                $is_payment = $this->input->post('is_payment',true);
                $signup_amount = $this->input->post('signup_amount',true);
                $payment_type = $this->input->post('payment_type',true);
                $fixed_amount = $this->input->post('fixed_amount',true);
                $percent_amount = $this->input->post('percent_amount',true);
                $is_recurring = $this->input->post('is_recurring',true);

                if($status=='') $status='0';
                if($is_recurring == '') $is_recurring = '0';
                if($is_overwritten == '') $is_overwritten = '0';
                if($signup_commission == '') $signup_commission = '0';
                if($is_payment == '') $is_payment = '0';

                if($payment_type == NULL) $payment_type = '';
                if($payment_type == 'fixed') $percent_amount = '';
                if($payment_type == 'percentage') $fixed_amount = '';

                // echo $is_recurring;exit;
                $data=array(
                    'name'=>$name,
                    'username'=>$username,
                    'email'=>$email,
                    'mobile'=>$mobile,
                    'address'=>$address,
                    'status'=>$status,
                    'is_overwritten' => $is_overwritten,
                    'is_signup_commission' => $signup_commission,
                    'signup_amount' => isset($signup_amount) ? $signup_amount:"",
                    'is_payment' => $is_payment,
                    'payment_type' => isset($payment_type) ? $payment_type:'',
                    'fixed_amount' => isset($fixed_amount) ? $fixed_amount:"",
                    'percentage_amount' => isset($percent_amount) ? $percent_amount:"",
                    'is_recurring' => $is_recurring,
                );
                
                if($this->basic->update_data('affiliate_users',['id'=>$id],$data)) $this->session->set_flashdata('success_message',1);   
                else $this->session->set_flashdata('error_message',1);     
                
                redirect('affiliate_system/affiliate_users','location');                 
                
            }
        } 

    }

    public function delete_affiliate($id=0)
    {

        $this->ajax_check();
        $this->csrf_token_check();

        if($this->is_demo == '1' && $this->session->userdata('user_type')=="Admin")
        {
            
            $response['status'] = 0;
            $response['message'] = "This feature is disabled in this demo.";
            echo json_encode($response);
            exit();
            
        }

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        if($id==0) exit;

        $this->db->trans_start();
        $affiliate_existence_table = array(
            1 => 
            array (
              'table_name' => 'affiliate_visitors_action',
              'column_name' => 'affiliate_id',
            ),
            2 => 
            array (
              'table_name' => 'affiliate_withdrawal_methods',
              'column_name' => 'affiliate_id',
            ),
            3 => 
            array (
              'table_name' => 'affiliate_withdrawal_requests',
              'column_name' => 'affiliate_id',
            ),
            4 => 
            array (
              'table_name' => 'affiliate_users',
              'column_name' => 'id',
            ),
            5 => 
            array (
              'table_name' => 'affiliate_earning_history',
              'column_name' => 'affiliate_id',
            ),
        );

        foreach($affiliate_existence_table as $value)
        {
          if($this->db->table_exists($value['table_name']))
            $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$id));
        }

        $this->db->trans_complete();                

        if ($this->db->trans_status() === FALSE) 
        {   
            $response['status'] = 0;
            $response['message'] = $this->lang->line('Database error. Something went wrong, please try again.');           
        } else {

            $this->session->unset_userdata("affiliate_userid");
            $response['status'] = 1;
            $response['message'] = $this->lang->line("Account and all of it's corresponding data have been deleted successfully.");

        }

        echo json_encode($response);

    }

    public function change_affiliate_password_action()
    {
        if($this->is_demo == '1')
        {
            
            $response['status'] = 0;
            $response['message'] = "This feature is disabled in this demo.";
            echo json_encode($response);
            exit();
            
        }

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        $this->ajax_check();

        $id = $this->input->post('user_id');
        if ($_POST) 
        {
            $this->form_validation->set_rules('password', '<b>'. $this->lang->line("password").'</b>', 'trim|required');
            $this->form_validation->set_rules('confirm_password', '<b>'. $this->lang->line("confirm password").'</b>', 'trim|required|matches[password]');
        }
        if ($this->form_validation->run() == false) 
        {
           echo json_encode(array("status"=>"0","message"=>$this->lang->line("Something went wrong, please try again")));
           exit();
        } 
        else 
        {
            $this->csrf_token_check();

            $new_password = $this->input->post('password',true);
            $new_confirm_password = $this->input->post('confirm_password',true);

            $table_change_password = 'affiliate_users';
            $where_change_passwor = array('id' => $id);
            $data = array('password' => md5($new_password));
            $this->basic->update_data($table_change_password, $where_change_passwor, $data);

            $where['where'] = array('id' => $id);
            $mail_info = $this->basic->get_data('affiliate_users', $where);
            
            $name = $mail_info[0]['name'];
            $to = $mail_info[0]['email'];
            $password = $new_password;

            $mask = $this->config->item('product_name');
            $from = $this->config->item('institute_email');
            $url = site_url();


            $email_template_info = $this->basic->get_data('email_template_management',array('where'=>array('template_type'=>'change_password')),array('subject','message'));

            if(isset($email_template_info[0]) && $email_template_info[0]['subject'] != '' && $email_template_info[0]['message'] != '') 
            {
                $subject = $email_template_info[0]['subject'];
                $message = str_replace(array("#USERNAME#","#APP_URL#","#APP_NAME#","#NEW_PASSWORD#"),array($name,$url,$mask,$password),$email_template_info[0]['message']);
            } 
            else 
            {
                $subject = 'Change Password Notification';
                $message = "Dear {$name},<br/> Your <a href='".$url."'>{$mask}</a> affiliate account password has been changed. Your new password is: {$password}.<br/><br/> Thank you.";
            }
           
            @$this->_mail_sender($from, $to, $subject, $message, $mask);
            echo json_encode(array("status"=>"1","message"=>$this->lang->line("Password has been changed successfully")));
        }
    }
    

    public function change_request_states()
    {
        $this->ajax_check();

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $curency_icon = isset($currency_icons[$currency])?$currency_icons[$currency]:"$";

        $id = $this->input->post("id",true);
        $affiliate_id = $this->input->post("affiliate_id",true);
        $amount = $this->input->post("amount",true);
        $status = $this->input->post("status",true);
        $message = $this->input->post("message",true);

        $status_text = "";
        if($status == '0') $status_text = $this->lang->line('Pending');
        else if($status == '1') $status_text = $this->lang->line('Approved');
        else if($status == '2') $status_text = $this->lang->line('Canceled');

        // echo $amount;exit;
        $get_affiliate_info = $this->basic->get_data("affiliate_users",['where'=>['id'=>$affiliate_id]]);


        $name = $get_affiliate_info[0]['name'];
        $to = $get_affiliate_info[0]['email'];
        $mask = $this->config->item('product_name');
        $from = $this->config->item('institute_email');
        $url = site_url();
        $productname = $this->config->item('product_name');

        $email_template_info = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>"affiliate_withdrawal_request_approval")),array('subject','message'));
        $email_template_info2 = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>"affiliate_withdrawal_request_cancel")),array('subject','message'));
        if($status == '1') {
            if(isset($email_template_info[0]) && $email_template_info[0]['subject'] != '' && $email_template_info[0]['message'] != '') {
                // #USERNAME#,#AMOUNT#,#REQUEST_STATUS#,#ADMIN_EMAIL#,#APP_URL#,#APP_NAME#
                $subject = str_replace('#APP_NAME#',$productname,$email_template_info[0]['subject']);
                $email_content =str_replace(array("#USERNAME#","#AMOUNT#","#REQUEST_STATUS#","#ADMIN_EMAIL#","#APP_URL#","#APP_NAME#"),array($name,$amount,$status_text,$from,$url,$mask),$email_template_info[0]['message']);

            } else {

                $subject = $this->lang->line('Affiliate Withdrawal Request Approval');
                $email_content = "<p>Dear {$name},<br>".$this->lang->line('We have reviewed your withdrawal Request and the below is your request update.')."</p>
                            <ul>
                                <li>".$this->lang->line("Requested Amount")." : ".$curency_icon.$amount."</li>
                                <li>".$this->lang->line("Status")." : <strong>".$status_text."</strong></li>
                            </ul>
                            <p>".$this->lang->line("Thank You")."</p><a href='".$url."'>{$mask}</a>";
            }
        }

        if($status =='2') {

            if(isset($email_template_info2[0]) && $email_template_info2[0]['subject'] != '' && $email_template_info2[0]['message'] != '') {
                #USERNAME#,#AMOUNT#,#REQUEST_STATUS#,#CANCEL_MESSAGE#,#ADMIN_EMAIL#,#APP_URL#,#APP_NAME#
                $subject = str_replace('#APP_NAME#',$productname,$email_template_info2[0]['subject']);
                $email_content =str_replace(array("#USERNAME#","#AMOUNT#","#REQUEST_STATUS#","#CANCEL_MESSAGE#","#ADMIN_EMAIL#","#APP_URL#","#APP_NAME#"),array($name,$amount,$status_text,$message,$from,$url,$mask),$email_template_info2[0]['message']);

            } else {
                $email_content = "<p>Dear {$name},<br>".$this->lang->line('We have reviewed your withdrawal Requestand the below is your request update.')."</p>
                            <ul>
                                <li>".$this->lang->line("Requested Amount")." : ".$curency_icon.$amount."</li>
                                <li>".$this->lang->line("Status")." : <strong>".$status_text."</strong></li>
                                <li>".$this->lang->line("Reason of Cancelation")." : {$message}"."</li>
                            </ul>
                            <p>".$this->lang->line("Thank You")."</p><a href='".$url."'>{$mask}</a>";
            }
        }


        $updated_data = [];
        $updated_data['request_status'] = $status;

        if($status == '1')
            $updated_data['completed_at'] = date("Y-m-d H:i:s");
        else 
            $updated_data['completed_at'] = "0000-00-00 00:00:00";

        if($this->basic->update_data("affiliate_withdrawal_requests",['id'=>$id],$updated_data)) {

            if($status == "1" || $status == '2') {
                @$this->_mail_sender($from, $to, $subject, $email_content, $mask);
            }

            echo "1";
        } else {
            echo "0";
        }

    }

    public function request_info($affiliate_id='')
    {
        if($affiliate_id == 0 || $affiliate_id == "") redirect("error_404","refresh");

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        $data = [];
        $data['body'] = "affiliate_users_settings/request_info";
        $data['page_title'] = $this->lang->line("Request Details");
        $data['affiliate_id'] = $affiliate_id;

        // $this->affiliate_commission("18",'32','payment','49');exit;

        //affiliator info
        $affiliate_info = $this->basic->get_data("affiliate_users",['where'=>['id'=>$affiliate_id]]);
        $data['affiliate_info'] = $affiliate_info;

        $total_users_by_affiliate = $this->basic->count_row("users",['where'=>['affiliate_id'=>$affiliate_id]],'id');
        $data['total_users_by_affiliate'] = $total_users_by_affiliate[0]['total_rows'];

        $total_transfered = $this->basic->get_data("affiliate_withdrawal_requests",['where'=>['affiliate_id'=>$affiliate_id,'request_status'=>'1']],['requested_amount']);

        $methods = $this->basic->get_data("affiliate_withdrawal_methods",['where'=>['affiliate_id'=>$affiliate_id]]);
        for ($i=0; $i < count($methods) ; $i++) { 
            $payment_type = $methods[$i]['payment_type'];
            if($payment_type == 'paypal') {
                $methods[$i]['method'] = '<li class="list-group-item d-flex justify-content-between align-items-center bg-primary method_details pointer border-0" method_name="PayPal" details="'.nl2br(htmlspecialchars($methods[$i]['paypal_email'],ENT_QUOTES)).'"><span class="text-white">PAYPAL</span><span class="pointer" style="line-height:0 !important;"><i class="fas fa-info-circle text-light" style="font-size:24px !important;"></i></span></li>';
            } else if($payment_type == 'bank_acc') {
                $methods[$i]['method'] = '<li class="list-group-item d-flex justify-content-between align-items-center bg-info method_details pointer border-0" method_name="Manual" details="'.nl2br(htmlspecialchars($methods[$i]['bank_acc_no'],ENT_QUOTES)).'"><span class="text-white">MANUAL</span><span class="pointer" style="line-height:0 !important;"><i class="fas fa-info-circle text-light" style="font-size:24px !important;"></i></span></li>';
            }
        }
        // echo "<pre>"; print_r($methods); exit;
        // echo "<pre>"; print_r($get_affiliate_methods); exit;
        $data['methods'] = $methods;
        // calculating total pending money of affiliator
        $completeData = array_map(function ($value) {
            return $value['requested_amount'];
        },$total_transfered);
        $data['transferedAmount'] = array_sum($completeData);
        
        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime($to_date." - 30 days"))." 00:00:00";
        $current_month = date("Y-m");
        $click_list = array();
        $signup_list = array();
        $click_signup_date_list = array();

        $table = "affiliate_visitors_action";
        $where['where'] = ['affiliate_id'=>$affiliate_id,'clicked_time >='=>$from_date];

        $select = array('count(id) as visitor','date_format(clicked_time,"%Y-%m-%d") as visited_at','type');
        $visitor_info = $this->basic->get_data($table,$where,$select,'','','','date_format(clicked_time,"%Y-%m-%d") asc','date_format(clicked_time,"%Y-%m-%d"),type');


        $where22 = array('where' => array('affiliate_id'=>$affiliate_id,'date_format(clicked_time,"%Y-%m")' => $current_month));
        $total_visitor_info = $this->basic->get_data($table,$where22);

        // calculating total pending money of affiliator
        $link_clicked = $signedUp = 0;
        foreach ($total_visitor_info as $visitors) {
            if($visitors['type'] == 'click') {
                $link_clicked++;
            }
            if($visitors['type'] == 'signup') {
                $signedUp++;
            }
        }

        foreach($visitor_info as $value)
        {
            if($value['type'] == 'click')
                $click_list[$value['visited_at']] = $value['visitor'];
            else if($value['type'] == 'signup')
                $signup_list[$value['visited_at']] = $value['visitor'];

            if(!isset($click_list[$value['visited_at']])) $click_list[$value['visited_at']] = 0;
            if(!isset($signup_list[$value['visited_at']])) $signup_list[$value['visited_at']] = 0;

            $formated_date = date("jS M",strtotime($value['visited_at']));
            $click_signup_date_list[$value['visited_at']] = $formated_date;
        }

        $largest_values = array();
        $max_value = 1;
        if(!empty($click_list)) array_push($largest_values, max($click_list));
        if(!empty($signup_list)) array_push($largest_values, max($signup_list));
        if(!empty($largest_values)) $max_value = max($largest_values);
        if($max_value > 10) $data['step_size'] = floor($max_value/10);
        else $data['step_size'] = 1;

        $data['click_list'] = $click_list;
        $data['signup_list'] = $signup_list;
        $data['link_clicked'] = $link_clicked;
        $data['signedUp'] = $signedUp;
        $data['click_signup_date_list'] = $click_signup_date_list;


        // earning history
        $table = "affiliate_earning_history";
        $where['where'] = ['affiliate_id'=>$affiliate_id,'event_date >='=>$from_date,'event_date <='=>$to_date];

        $earnings = $this->basic->get_data($table,$where,'','','','','date_format(event_date,"%Y-%m-%d") asc');

        $all_earnings = $this->basic->get_data($table,['where'=>['affiliate_id'=>$affiliate_id]],'','','','','date_format(event_date,"%Y-%m-%d") asc');

        $earning_chart_labels = array();
        $earning_chart_values = array();

        $from_date = strtotime($from_date);
        $to_date = strtotime($to_date);
        $array_month = array();
        $array_year = array();
        $payment_today=$payment_month=$payment_year=$payment_life=0;

        do 
        {
           $temp = date("Y-m-d",$from_date);
           $temp2 = date("j M",$from_date);;
           $earning_chart_values[$temp] = 0;
           $earning_chart_labels[] = $temp2;
           $from_date = strtotime('+1 day',$from_date); 
        } 
        while ($from_date <= $to_date);

        foreach ($earnings as $key => $value) 
        {

            $updated_at_formatted = date("Y-m-d",strtotime($value['event_date']));

            if(isset($earning_chart_values[$updated_at_formatted])) {
                $earning_chart_values[$updated_at_formatted] += $value["amount"];
            }
            else {
                $earning_chart_values[$updated_at_formatted] = $value["amount"];
            } 
        }

        $data['earning_chart_labels'] = $earning_chart_labels;
        $data['earning_chart_values'] = $earning_chart_values;

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";
        $this->_viewcontroller($data);
    }


    public function requests_data()
    {
        $this->ajax_check();

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        $affiliate_id = $this->input->post("affiliate_id",true);
        $request_date_range = $this->input->post("request_date_range");
        $request_status = $this->input->post("search_request_status");

        $display_columns = array("#","id",'method','requested_amount','request_status','status', 'created_at','completed_at');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple=array();
        $where_simple['affiliate_withdrawal_requests.affiliate_id'] = $affiliate_id;

        if($request_date_range!="")
        {
            $exp = explode('|', $request_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date   = isset($exp[1])?$exp[1]:"";

            if($from_date!="Invalid date" && $to_date!="Invalid date")
            {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date   = date('Y-m-d', strtotime($to_date));
                $where_simple["Date_Format(affiliate_withdrawal_requests.created_at,'%Y-%m-%d') >="] = $from_date;
                $where_simple["Date_Format(affiliate_withdrawal_requests.created_at,'%Y-%m-%d') <="] = $to_date;
            }
        }

        if($request_status !="") $where_simple['affiliate_withdrawal_requests.request_status'] = $request_status;

        $join = ['affiliate_withdrawal_methods'=>"affiliate_withdrawal_requests.method_id=affiliate_withdrawal_methods.id,left"];
        $select = ["affiliate_withdrawal_requests.*","affiliate_withdrawal_methods.payment_type","affiliate_withdrawal_methods.paypal_email","affiliate_withdrawal_methods.bank_acc_no"];
        $table = "affiliate_withdrawal_requests";
        $where  = array('where'=>$where_simple);

        $info = $this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');

        $total_rows_array = $this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
        $total_result = $total_rows_array[0]['total_rows'];

        $count_requests = count($info);

        $base_url=base_url();

        $request_status_arr = array("0"=>$this->lang->line('pending'),"1"=>$this->lang->line("Approved"),"2"=>$this->lang->line("Canceled"));

        for ($i=0; $i < $count_requests; $i++) { 

            $status = $info[$i]['request_status'];
            $disbale_btn = '';
            if($status == '1' || $status == '2') $disbale_btn = 'disabled';

            $info[$i]['request_status'] = form_dropdown('request_status', $request_status_arr, $status,'class="select2 request_status" '.$disbale_btn.'  style="width:120px !important;font-size:12px;" amount="'.$info[$i]['requested_amount'].'" affiliate_id="'.$info[$i]['affiliate_id'].'" request_id="'.$info[$i]['id'].'" id="request_status"')."<script>$('[data-toggle=\"tooltip\"]').tooltip();$('.request_status').select2();</script>";

            if($status == '1') 
                $info[$i]['status'] = '<span class="text-success"><i class="fas fa-check-circle"></i> '.$this->lang->line('Approved').'</span>';
            else if($status == '0')
                $info[$i]['status'] = '<span class="text-danger"><i class="far fa-times-circle"></i> '.$this->lang->line('Pending').'</span>';
            else if($status == '2')
                $info[$i]['status'] = '<span class="text-warning"><i class="fas fa-ban"></i> '.$this->lang->line('canceled').'</span>';

            $info[$i]['created_at'] = date("M j, Y",strtotime($info[$i]['created_at']));

            if($info[$i]['completed_at'] != "0000-00-00 00:00:00") 
                $info[$i]['completed_at'] = date("M j, Y",strtotime($info[$i]['completed_at']));
            else 
                $info[$i]['completed_at'] = '<span class="text-muted"><i class="fas fa-exclamation-circle"></i> '.$this->lang->line('Not yet').'</span>';

            if($info[$i]['payment_type'] == 'paypal') {
                $info[$i]['method'] = "<div style='min-width:70px;' class='text-center'>PAYPAL <span data-toggle='tooltip' title='".$this->lang->line('See details')."' class='text-primary pointer method_details' method_name='PayPal' details='".$info[$i]['paypal_email']."'><i class='fas fa-info-circle'></i></span></div>";
            }

            if($info[$i]['payment_type'] == 'bank_acc') {
                $info[$i]['method'] = "<div style='min-width:70px;' class='text-center'>MANUAL <span data-toggle='tooltip' title='".$this->lang->line('See details')."' class='text-primary pointer method_details' method_name='Manual' details='".nl2br(htmlspecialchars($info[$i]['bank_acc_no'],ENT_QUOTES))."'><i class='fas fa-info-circle'></i></span></div>";
            }

            $info[$i]['requested_amount'] = '<span class="">'.$info[$i]['requested_amount'].'</span>';

            // $str="";  
            // $str=$str."<a href='' class='delete_affiliate_request btn btn-sm btn-danger {$disbale_btn}' affiliate_id='".$info[$i]['affiliate_id']."' table_id='".$info[$i]['id']."' csrf_token='".$this->session->userdata('csrf_token_session')."' data-toggle='tooltip' title='".$this->lang->line('Delete')."'>".'<i class="fa fa-trash"></i> '.$this->lang->line('Delete')."</a>";

            // $info[$i]["actions"] = "<div style='min-width:60px'>".$str."</div>";
            
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function delete_affiliate_request()
    {
        $this->ajax_check();
        $this->csrf_token_check();

        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login', 'location');         
        }

        $table_id = $this->input->post("table_id",true);
        $affiliate_id = $this->input->post("affiliate_id",true);
        if($table_id == "" || $table_id == 0 || $affiliate_id =="" || $affiliate_id == 0 || ($table_id == "" && $affiliate_id =="")) exit;

        if($this->basic->delete_data("affiliate_withdrawal_requests",['id'=>$table_id,"affiliate_id"=>$affiliate_id])) {
            echo "1";
        } else {
            echo "0";
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
            0 => "
            CREATE TABLE IF NOT EXISTS `affiliate_users` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `email` varchar(255) NOT NULL,
              `password` varchar(255) NOT NULL,
              `mobile` varchar(100) NOT NULL,
              `address` text NOT NULL,
              `username` varchar(255) NOT NULL,
              `profile_img` text NOT NULL,
              `activation_code` varchar(20) NOT NULL,
              `total_earn` double NOT NULL,
              `is_overwritten` enum('0','1') NOT NULL DEFAULT '0',
              `is_signup_commission` enum('0','1') NOT NULL DEFAULT '0',
              `signup_amount` varchar(100) NOT NULL,
              `is_payment` enum('0','1') NOT NULL DEFAULT '0',
              `payment_type` varchar(100) NOT NULL,
              `fixed_amount` varchar(255) NOT NULL,
              `percentage_amount` varchar(255) NOT NULL,
              `is_recurring` enum('0','1') NOT NULL DEFAULT '0',
              `last_login_at` datetime NOT NULL,
              `last_login_ip` varchar(30) NOT NULL,
              `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `status` enum('0','1') NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            1 => "
            CREATE TABLE IF NOT EXISTS `affiliate_forgot_password` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `confirmation_code` varchar(15) NOT NULL,
              `email` varchar(100) NOT NULL,
              `success` int(11) NOT NULL DEFAULT '0',
              `expiration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            2 => "
            CREATE TABLE IF NOT EXISTS `affiliate_payment_settings` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `signup_commission` enum('0','1') NOT NULL DEFAULT '0',
              `payment_commission` enum('0','1') NOT NULL DEFAULT '0',
              `payment_type` varchar(50) NOT NULL,
              `sign_up_amount` varchar(255) NOT NULL,
              `percentage` varchar(255) NOT NULL,
              `fixed_amount` varchar(255) NOT NULL,
              `is_recurring` enum('0','1') NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            3=>"
            CREATE TABLE IF NOT EXISTS `affiliate_visitors_action` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `affiliate_id` int(11) NOT NULL,
              `type` enum('click','signup','payment') NOT NULL,
              `clicked_time` datetime NOT NULL,
              `user_id` int(11) NOT NULL COMMENT 'visitors sign up id',
              `ip_address` varchar(30) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `affiliate_id` (`affiliate_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            4=> "
            CREATE TABLE IF NOT EXISTS `affiliate_withdrawal_methods` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `affiliate_id` int(11) NOT NULL,
              `payment_type` varchar(255) NOT NULL,
              `paypal_email` varchar(150) NOT NULL,
              `bank_acc_no` text NOT NULL,
              `created_at` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            5=> "
            CREATE TABLE IF NOT EXISTS `affiliate_withdrawal_requests` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `affiliate_id` int(11) NOT NULL,
              `method_id` int(11) NOT NULL,
              `requested_amount` double NOT NULL,
              `request_status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0(pending),1(approved),2(canceled)',
              `created_at` datetime NOT NULL,
              `completed_at` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            6=> "
            CREATE TABLE IF NOT EXISTS `affiliate_earning_history` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `affiliate_id` int(11) NOT NULL,
              `user_id` int(11) NOT NULL,
              `event` enum('signup','payment','recurring') NOT NULL,
              `amount` float NOT NULL,
              `event_date` varchar(100) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `affiliate_id` (`affiliate_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            7=> "INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Affiliate System', 'fas fa-people-carry', '', (SELECT serial FROM menu as menu2 WHERE only_admin='1' ORDER BY serial DESC LIMIT 1), '0', '1', '1', '0', 0, '0', '', '0', 0);",
            8=> "INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Affiliate Users', 'affiliate_system/affiliate_users', 1, 'fas fa-users', '', (SELECT id FROM menu WHERE module_access='0'), '0', '1', '0', '0', '0', 0);",
            9=> "INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Commission Settings', 'affiliate_system/affiliate_payment_settings', 5, 'fas fa-money-check-alt', '', (SELECT id FROM menu WHERE module_access='0'), '0', '1', '0', '0', '0', 0);",
            10=> "INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Withdrawal Requests', 'affiliate_system/all_withdrawal_requests', 9, 'fas fa-hands-helping', '', (SELECT id FROM menu WHERE module_access='0'), '0', '1', '0', '0', '0', 0);",
            11=> "INSERT INTO `email_template_management` (`id`, `title`, `template_type`, `subject`, `message`, `icon`, `tooltip`, `info`) VALUES (NULL,'Affiliate Signup Activation','affiliate_signup_activation','#APP_NAME# | Affiliate Account Activation','<p>To activate your affiliate account please perform the following steps :</p>\r\n<ol>\r\n<li>Go to this url : #ACTIVATION_URL#</li>\r\n<li>Enter this code : #ACCOUNT_ACTIVATION_CODE#</li>\r\n<li>Activate your account</li>\r\n</ol>','fas fa-hands-helping','#APP_NAME#,#ACTIVATION_URL#,#ACCOUNT_ACTIVATION_CODE#','When affiliate account open');",
            12=> "INSERT INTO `email_template_management` (`id`, `title`, `template_type`, `subject`, `message`, `icon`, `tooltip`, `info`) VALUES (NULL, 'Affiliate Withdrawal Request Approval', 'affiliate_withdrawal_request_approval', '#APP_NAME# | Affiliate Withdrawal Request Approval', '<p>Dear #USERNAME#,<br/>\r\n We have reviewed your withdrawal Request and the below is your withdrawal request update.</p>\r\n<ul>\r\n<li>Requested Amount : #AMOUNT#</li>\r\n<li>Request Status : #REQUEST_STATUS#</li>\r\n</ul>\r\n<p>If you have any queries on this, please contact the #APP_NAME# admin with this #ADMIN_EMAIL#.</p>\r\n<br></br>\r\nThank you<br>\r\n<a href=\"#APP_URL#\">#APP_NAME#</a> Team', 'fas fa-vote-yea', '#USERNAME#,#AMOUNT#,#REQUEST_STATUS#,#ADMIN_EMAIL#,#APP_URL#,#APP_NAME#', 'When affiliate withdrawal request approved');",
            13=> "INSERT INTO `email_template_management` (`id`, `title`, `template_type`, `subject`, `message`, `icon`, `tooltip`, `info`) VALUES (NULL, 'Affiliate Withdrawal Request Cancelation', 'affiliate_withdrawal_request_cancel', '#APP_NAME# | Affiliate Withdrawal Request Cancelation', '<p>Dear #USERNAME#,<br/>\r\n We have reviewed your withdrawal Request and the below is your withdrawal request update.</p>\r\n<ul>\r\n<li>Requested Amount : #AMOUNT#</li>\r\n<li>Request Status : #REQUEST_STATUS#</li>\r\n<li>Reason Of cancelation : #CANCEL_MESSAGE#</li>\r\n</ul>\r\n<p>If you have any queries on this, please contact the #APP_NAME# admin with this #ADMIN_EMAIL#.</p>\r\n<br></br>\r\nThank you<br>\r\n<a href=\"#APP_URL#\">#APP_NAME#</a> Team', 'fas fa-vote-yea', '#USERNAME#,#AMOUNT#,#REQUEST_STATUS#,#CANCEL_MESSAGE#,#ADMIN_EMAIL#,#APP_URL#,#APP_NAME#', 'When affiliate withdrawal request cancel');",
            14=> "INSERT INTO `email_template_management` (`id`, `title`, `template_type`, `subject`, `message`, `icon`, `tooltip`, `info`) VALUES (NULL, 'New Withdrawal Request', 'new_withdrawal_request', '#APP_NAME# | New Withdrawal Request', '<p>Dear Admin,<br>\r\nA withdrawal request has been made by an affiliate. Please check the below information of the request.</p>\r\n<ul>\r\n<li>Affiliator Name : #AFFILIATOR_NAME#</li>\r\n<li>Affiliator Email : #AFFILIATOR_EMAIL#</li>\r\n<li>Requested Amount : #REQUESTED_AMOUNT#</li>\r\n</ul>\r\n', 'fas fa-ankh', '#AFFILIATOR_NAME#,#AFFILIATOR_EMAIL#,#REQUESTED_AMOUNT#', 'When new withdrawal Request create');"
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
        $sql=array(
            0=>"DROP TABLE IF EXISTS `affiliate_users`;",
            1=>"DROP TABLE IF EXISTS `affiliate_forgot_password`;",
            2=>"DROP TABLE IF EXISTS `affiliate_payment_settings`;",
            3=>"DROP TABLE IF EXISTS `affiliate_visitors_action`;",
            4=>"DROP TABLE IF EXISTS `affiliate_withdrawal_methods`;",
            5=>"DROP TABLE IF EXISTS `affiliate_withdrawal_requests`;",
            6=>"DROP TABLE IF EXISTS `affiliate_earning_history`;",
            7=>"DELETE FROM `email_template_management` WHERE `email_template_management`.`template_type` = 'affiliate_signup_activation';",
            8=>"DELETE FROM `email_template_management` WHERE `email_template_management`.`template_type` = 'affiliate_withdrawal_request_approval';",
            9=>"DELETE FROM `email_template_management` WHERE `email_template_management`.`template_type` = 'affiliate_withdrawal_request_cancel';",
            10=>"DELETE FROM `email_template_management` WHERE `email_template_management`.`template_type` = 'new_withdrawal_request';",
            11=> "DELETE FROM `menu` WHERE `module_access` = '0';",
            12=> "DELETE FROM `menu_child_1` WHERE `url` = 'affiliate_system/affiliate_users';",
            13=> "DELETE FROM `menu_child_1` WHERE `url` = 'affiliate_system/affiliate_payment_settings';",
            14=> "DELETE FROM `menu_child_1` WHERE `url` = 'affiliate_system/all_withdrawal_requests';",
        ); 

        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }


}
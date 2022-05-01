<?php
/*
Addon Name: Messenger Bot Connectivity 
Unique Name: messenger_bot_connectivity
Modules:
{
   "258":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Messenger Bot - Connectivity : JSON API"
   },
   "261":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Messenger Bot - Connectivity : Webview Builder"
   }
}
Project ID: 31
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 2.5.7
Description: 
*/

require_once("application/controllers/Home.php"); // loading home controller

class Messenger_bot_connectivity extends Home
{
  public $addon_data=array();

  	public function __construct() 
  	{
      	parent::__construct();

		$function_name=$this->uri->segment(2);
		if($function_name!="webview" && $function_name!="form_submit") 
		{
			if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');         
		}

		// if(file_exists(APPPATH.'modules/'.strtolower($this->router->fetch_class()).'/config/messenger_bot_enhancers_config.php'))
		// $this->load->config("messenger_bot_enhancers_config");

		// getting addon information in array and storing to public variable
		// addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
		//------------------------------------------------------------------------------------------
		$addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
		$addondata=$this->get_addon_data($addon_path); 
		$this->member_validity();
		$this->addon_data=$addondata;
		// Engagement variables
		$this->page_table_name="facebook_rx_fb_page_info";
		$this->user_info_id="facebook_rx_fb_user_info_id";
		$this->user_info_session=$this->session->userdata("facebook_rx_fb_user_info");
		$this->fb_user_info_table_name="facebook_rx_fb_user_info";
		$this->fb_rx_config_table_name="facebook_rx_config";
		$this->facebook_rx_config_id="facebook_rx_config_id";
     
    }


    /* 
	===============================================
	WEBVIEW BUILDER
	***********************************************
	*/
	public function index() 
	{
		if($this->session->userdata('user_type') != 'Admin' && !in_array(261,$this->module_access))  redirect('home/login_page', 'location');

		$data['pages'] = $this->get_pages();
        $data['user_id'] = $this->user_id;
        $data['body'] = "webview_builder/webview";
        $data['page_title'] = $this->lang->line("Webview Builder");
        $this->_viewcontroller($data);   
	}


	public function handle_select_boxes_data() 
	{
		if($this->session->userdata('user_type') != 'Admin' && !in_array(261,$this->module_access))  exit();
		// Kicks out if not an AJAX request
		if (! $this->input->is_ajax_request()) {
			$message = $this->lang->line('Bad Request');
			return $this->customJsonResponse($message);
		}

		// Sets validation rules
		$this->form_validation->set_rules('page_id', 'Page ID', 'required|numeric');

		// Checks whether data are valid
		if (false === $this->form_validation->run()) {
			$message = $this->form_validation->error('page_id');
			return $this->customJsonResponse($message);
		}

		$page_id = (int) $this->input->post('page_id');

		$assign_labels = $this->get_labels($page_id);
		$reply_template = $this->get_page_template($page_id);

		echo json_encode([
			'assign_labels' => $assign_labels,
			'reply_template' => $reply_template,
		]);
	}

	public function save_form_data() 
	{
		// Kicks out if not an AJAX request
		if($this->session->userdata('user_type') != 'Admin' && !in_array(261,$this->module_access))  exit();
		if (! $this->input->is_ajax_request()) {
			$message = $this->lang->line('Bad Request');
			return $this->customJsonResponse($message);
		}

		// Sets validation rules
		$this->form_validation->set_rules('user_id', $this->lang->line('User ID'), 'required|alpha_numeric');
		$this->form_validation->set_rules('form_name', $this->lang->line('Form name'), 'trim|required|min_length[3]|max_length[200]');
		$this->form_validation->set_rules('form_title', $this->lang->line('Form title'), 'trim|required');
		$this->form_validation->set_rules('page_id', $this->lang->line('Page'), 'required|numeric');
		$this->form_validation->set_rules('assign_label', $this->lang->line('Label ID'), 'regex_match[/^[0-9\,]+$/]');
		$this->form_validation->set_rules('reply_template', $this->lang->line('Template ID'), 'numeric');
		$this->form_validation->set_rules('form_data', $this->lang->line('Form data'), 'required|min_length[230]|max_length[50000]');

		// Checks whether data are valid
		if (false === $this->form_validation->run()) {
			$message = '';
			if ($this->form_validation->error('user_id')) {
				$message = $this->form_validation->error('user_id');
			} elseif ($this->form_validation->error('form_name')) {
				$message = $this->form_validation->error('form_name');
			} elseif ($this->form_validation->error('form_title')) {
				$message = $this->form_validation->error('form_title');
			} elseif ($this->form_validation->error('page_id')) {
				$message = $this->form_validation->error('page_id');
			} elseif ($this->form_validation->error('assign_label')) {
				$message = $this->form_validation->error('assign_label');
			} elseif ($this->form_validation->error('reply_template')) {
				$message = $this->form_validation->error('reply_template');
			} elseif ($this->form_validation->error('form_data')) {
				$message = $this->form_validation->error('form_data');
			}

			return $this->customJsonResponse(strip_tags($message));
		}

		// Extracts vars
		$user_id = $this->input->post('user_id');
		$form_name = strip_tags($this->input->post('form_name'));
		$form_title = strip_tags($this->input->post('form_title'));
		$page_id = (int) $this->input->post('page_id');
		$assign_label = (string) $this->input->post('assign_label');
		$reply_template = (int) $this->input->post('reply_template');
		$form_data = $this->input->post('form_data');

		if ($user_id !== md5($this->user_id)) {
			$message = $this->lang->line('Bad request');
			return $this->customJsonResponse($message);
		}

		// Checks if the JSON data is valid
		$valid_form_data = $this->validate_and_strip_tags_json_form_data($form_data);
		if (false === $valid_form_data) {
			$message = $this->lang->line('Invalid JSON data provided');
			return $this->customJsonResponse($message);
		}

		// Checks if there is a button tag in form data
		if (false === $this->checks_button_tag_in_json_form_data($valid_form_data)) {
			$message = $this->lang->line('You forgot to choose a button field');
			return $this->customJsonResponse($message);
		}

		$uri_canonical_id = $this->input->post('uri_canonical_id');
		$real_canonical_id = $this->input->post('real_canonical_id');
		$data = $this->session->userdata('edit_webview_form_data');

		if (($uri_canonical_id && ! $real_canonical_id) 
			|| ($real_canonical_id && ! $uri_canonical_id)
			|| ('' === $uri_canonical_id && '' === $real_canonical_id)
		) {
			$message = $this->lang->line('Bad request');
			return $this->customJsonResponse($message); 
		}

		// Tries update webview form data
		if ($uri_canonical_id && $real_canonical_id) {
			$session_canonical_id = (is_array($data) && isset($data['canonical_id'])) ? md5($data['canonical_id']) : null;
			if (($uri_canonical_id != $session_canonical_id)
				|| ($real_canonical_id != $session_canonical_id)
			) {
				$message = $this->lang->line('Bad request');
				return $this->customJsonResponse($message); 
			}

			$form_id = (is_array($data) && isset($data['form_id'])) ? $data['form_id'] : null;
			$where = [
				'id' => $form_id,
			];

			$data = [
				'form_name' => $form_name,
				'form_title' => $form_title,
				'page_id' => $page_id,
				'assign_label' => $assign_label,
				'reply_template' => $reply_template,
				'form_data' => json_encode($valid_form_data),
				'updated_at' => date('Y-m-d H:i:s'),
			];

			return $this->update_form_data('webview_builder', $where, $data);
		} 

		if ((null === $uri_canonical_id) && (null === $real_canonical_id)) {
			// Generates canonical id for form if from form title
			$canonical_id = $this->generate_canonical_id($form_title);
			if (empty($canonical_id)) {
				$message = $this->lang->line('Something went wrong! Please try again later');
				return $this->customJsonResponse($message);
			}

			$data = [
				'canonical_id' => $canonical_id,
				'user_id' => $this->user_id,
				'form_name' => $form_name,
				'form_title' => $form_title,
				'page_id' => $page_id,
				'assign_label' => $assign_label,
				'reply_template' => $reply_template,
				'form_data' => json_encode($valid_form_data),
				'inserted_at' => date('Y-m-d H:i:s'),
			];

			return $this->insert_form_data('webview_builder', $data);
		}

		$message = $this->lang->line('Something went wrong. Please try again later!');
		return $this->customJsonResponse($message);
	}

	public function export_form_data() 
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

			$this->form_validation->set_rules('form_id', 'Form ID', 'required');

			if (false === $this->form_validation->run()) {
				if ($this->form_validation->error('form_id')) {
					$message = $this->form_validation->error('form_id');
				} else {
					$message = $this->lang->line('Bad request.');
				}

				echo json_encode(['error' => strip_tags($message)]);
				exit;
			}

			// Holds form ID
			$form_id = filter_var($this->input->post('form_id'), FILTER_SANITIZE_STRING);

			$where = [
				'where' => [
					'web_view_form_canonical_id' => $form_id,
				],
			];
			$select = ['web_view_form_canonical_id'];

			$form = $this->basic->get_data('messenger_bot_user_custom_form_webview_data', $where, $select, [], 1);		

			// Exits displaying error if there is no data to be exported
			if (1 != sizeof($form)) {
				$message = $this->lang->line('No form data to be exported.');
				echo json_encode(['info' => $message]);
				exit;
			}

			// Sets form ID into session
			$this->session->set_userdata('webview_export_form_data_form_id', $form[0]['web_view_form_canonical_id']);
			
			// Sends success response
			echo json_encode(['status' => 'ok']);
			exit;

		} elseif ('get' == strtolower($method)) {
			$form_id = $this->session->userdata('webview_export_form_data_form_id');

			// Exits from here if we've no form ID in session
			if (! $form_id) {
				$message = $this->lang->line('No form data to be exported.');
				echo json_encode(['error' => $message]);
				exit;
			}

			$where = [
				'where' => [
					'messenger_bot_user_custom_form_webview_data.web_view_form_canonical_id' => $form_id,
				],
			];

			$join = [
				'messenger_bot_subscriber' => 'messenger_bot_user_custom_form_webview_data.subscriber_id = messenger_bot_subscriber.subscribe_id, left',
			];

			$select = [
				'messenger_bot_subscriber.first_name',
				'messenger_bot_subscriber.last_name',
				'messenger_bot_user_custom_form_webview_data.subscriber_id',
				'messenger_bot_user_custom_form_webview_data.web_view_form_canonical_id',
				'messenger_bot_user_custom_form_webview_data.data',
			];

			$form_data = $this->basic->get_data('messenger_bot_user_custom_form_webview_data', $where, $select, $join);

			// Exits displaying error if there is no data to be exported
			if (! count($form_data) > 0) {
				$message = $this->lang->line('No form data to be exported.');
				echo json_encode(['error' => $message]);
				exit;
			}

			// Grabs form data
			$data = isset($form_data[0]['data']) ? $form_data[0]['data'] : '';

			// Exits displaying error if there is no data to be exported
			if (! is_array($data = json_decode($data, true))) {
				$message = $this->lang->line('No form data to be exported.');
				echo json_encode(['error' => $message]);
				exit;
			}

			// Sets the csv file name
			$filename = 'webview_' . $data['webview_form_id'] . '.csv';

			// Prepares headers for csv file
			$csv_headers = [
				'PSID',
				'First Name',
				'Last Name',
			];

			// Prepares csv headers
			foreach ($data as $key => $header) {
				if ('subscriber_id' == $key || 'webview_form_id' == $key) {
					continue;
				}

				array_push($csv_headers, $key);
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
				foreach ($form_data as $key => $values) {
					$csv_values = [];
					$csv_values[] = $values['subscriber_id'];
					$csv_values[] = $values['first_name'];
					$csv_values[] = $values['last_name'];

					$tmp_data = json_decode($values['data'], true);
					if (null !== $tmp_data && is_array($tmp_data)) {
						foreach ($tmp_data as $key => $value) {
							if ('subscriber_id' == $key || 'webview_form_id' == $key) {
								continue;
							}

                            $value = is_array($value) ? implode(", ", $value) : $value;

							array_push($csv_values, $value);
						}
					}					
					
					// Puts values into csv file
					fputcsv($fp, $csv_values);
				}
			}

			// Closes the file pointer
			fclose($fp);

			// Unsets form ID from session
            $this->session->unset_userdata('webview_export_form_data_form_id');
            exit;
		}
	}

    public function edit_webview($id = null)
    {
    	if($this->session->userdata('user_type') != 'Admin' && !in_array(261,$this->module_access))  redirect('home/login_page', 'location');
    	// Gets webview form data using id
    	$form = $this->get_single_webview_by_id($id);

		// Shows 404 if webview not found
		if (count($form) < 1) {
			redirect('error_404', 'location');
        	exit();
		}

		$user_id = isset($form[0]['user_id']) ? $form[0]['user_id'] : null;
		if ($user_id != $this->user_id) {
			redirect('error_404', 'location');
        	exit();
		}

		$data['form_id'] = isset($form[0]['form_id']) ? $form[0]['form_id'] : null;
		$data['canonical_id'] = isset($form[0]['canonical_id']) ? $form[0]['canonical_id'] : null;
		$data['form_name'] = isset($form[0]['form_name']) ? $form[0]['form_name'] : '';
		$data['form_title'] = isset($form[0]['form_title']) ? $form[0]['form_title'] : '';
		$data['uri_canonical_id'] = md5($id);
		$data['real_canonical_id'] = $form[0]['canonical_id'] ? md5($form[0]['canonical_id']) : '';

		$data['page_id'] = isset($form[0]['page_id']) ? $form[0]['page_id'] : null;
		$data['label_id'] = isset($form[0]['assign_label']) ? $form[0]['assign_label'] : null;
		$data['template_id'] = isset($form[0]['reply_template']) ? $form[0]['reply_template'] : null;	
    	$data['user_id'] = $this->user_id;
    	$data['pages'] = $this->get_pages();
    	// $data['form_data'] = json_encode($decoded_form_data);
    	$data['form_data'] = isset($form[0]['form_data']) ? $form[0]['form_data'] : '';

    	// Sets canonical ID in session
    	$this->session->set_userdata('edit_webview_form_data', [
    		'form_id' => isset($form[0]['form_id']) ? $form[0]['form_id'] : null,
    		'canonical_id' => isset($form[0]['canonical_id']) ? $form[0]['canonical_id'] : null,
    	]);

    	$data['body'] = 'webview_builder/edit';
    	$data['page_title'] = $this->lang->line('Edit webview form');
    	$this->_viewcontroller($data);
    }	

    public function webview_builder_manager()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(261,$this->module_access))  redirect('home/login_page', 'location');
        $data['body'] = 'webview_builder/manager';
        $data['page_title'] = $this->lang->line('Webview Manager');
        $this->_viewcontroller($data);  
    }

    public function webview_manager_data()
    {           
        if($this->session->userdata('user_type') != 'Admin' && !in_array(261,$this->module_access))  exit();
        $this->ajax_check();

        $search_value = isset($_POST['search']) ? $_POST['search']['value'] : null;
        $display_columns = ['id', 'form_name', 'page_name', 'form_created_time', 'total_form_submit', 'last_form_submitted_at'];
        $search_columns = ['form_name'];

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;

        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 0;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'webview_builder.id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'ASC';
        $order_by = $sort . " " . $order;

        $where = [
        	'where' => [
        		'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id'=>$this->session->userdata('facebook_rx_fb_user_info'),
        		'webview_builder.deleted' => '0',
        		'webview_builder.user_id' => $this->user_id,
        	],
        ];

        if ('' != $search_value) {
            $or_where = [];
            foreach ($search_columns as $key => $value) {
            	$or_where[$value . ' LIKE '] = "%$search_value%";
            	// $where = ['or_where' => $or_where];
            }
            $where['or_where'] = $or_where;
        }
            
        $table = 'webview_builder';

        $select = [
        	'webview_builder.id',
        	'form_name',
        	'webview_builder.inserted_at as form_created_time',
        	'(select page_name from facebook_rx_fb_page_info where id = webview_builder.page_id) as page_name',
        	'max(messenger_bot_user_custom_form_webview_data.inserted_at) as last_form_submitted_at',
        	'updated_at',
        	'count(messenger_bot_user_custom_form_webview_data.id) as total_form_submit',
        	'canonical_id'
        ];

        $join = [
        	'facebook_rx_fb_page_info' => 'facebook_rx_fb_page_info.id=webview_builder.page_id,left',
        	'messenger_bot_user_custom_form_webview_data' => 'webview_builder.canonical_id=messenger_bot_user_custom_form_webview_data.web_view_form_canonical_id,left'
        ];

        $group_by = 'webview_builder.canonical_id';

        $info = $this->basic->get_data($table, $where, $select, $join, $limit, $start, $order_by, $group_by);

        $total_rows_array = $this->basic->count_row($table, $where, $count = $table . '.id', $join, $group_by);
        $total_result = $total_rows_array[0]['total_rows'];

        for ($i = 0; $i < sizeof($info); $i++) {
  
        	if ($info[$i]['form_name']) {
        		$info[$i]['form_name'] = $this->truncate_str($info[$i]['form_name']);
        	}

            if ($info[$i]['form_created_time']) {
                $info[$i]['form_created_time'] = date('jS M Y, H:i', strtotime($info[$i]['form_created_time']));
            }

            if ($info[$i]['last_form_submitted_at']) {
                $info[$i]['last_form_submitted_at'] = date_time_calculator($info[$i]['last_form_submitted_at'],true);
            }

            if (!isset($info[$i]['actions'])) {

            	// Prepares buttons
                $actions = '<a data-toggle="tooltip" title="' . $this->lang->line('View form') . '" href="' . base_url("messenger_bot_connectivity/webview/{$info[$i]['canonical_id']}") . '" class="btn btn-circle btn-outline-info" target="_blank"><i class="fab fa-wpforms"></i></a>';
                $actions .= '<a data-toggle="tooltip" title="' . $this->lang->line('Report') . '" href="#" class="btn btn-circle btn-outline-primary" id="detail-webview-form" data-form-id="'. $info[$i]['canonical_id'] . '"><i class="fas fa-eye"></i></a>';
                $actions .= '<a data-toggle="tooltip" title="' . $this->lang->line('Export form data') . '" href="#" class="btn btn-circle btn-outline-success" id="webview-export-form-data" data-form-id="'. $info[$i]['canonical_id'] . '"><i class="fas fa-file-export"></i></a>';
                $actions .= '<a data-toggle="tooltip" title="' . $this->lang->line('Edit form') . '" href="' . base_url("messenger_bot_connectivity/edit_webview/{$info[$i]['canonical_id']}") . '" class="btn btn-circle btn-outline-warning"><i class="fa fa-edit"></i></a>';
                $actions .= '<a data-toggle="tooltip" title="' . $this->lang->line('Delete form') . '" href="" class="btn btn-circle btn-outline-danger" id="delete-webview-form" data-form-id="'. $info[$i]['canonical_id'] . '"><i class="fas fa-trash-alt"></i></a>';

                // Fixes button group's width
	            $action_width = (5*47)+20;

	            $info[$i]['actions'] ='<div class="dropdown d-inline dropright">';
	            $info[$i]['actions'] .= '<button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
	            $info[$i]['actions'] .= '<i class="fa fa-briefcase"></i>';
	            $info[$i]['actions'] .= '</button>';
	            $info[$i]['actions'] .= '<div class="dropdown-menu mini_dropdown text-center" style="width:' . $action_width . 'px !important">';
	            $info[$i]['actions'] .= $actions;
	            $info[$i]['actions'] .= '</div>';
	            $info[$i]['actions'] .= '</div>';
	            $info[$i]['actions'] .= "<script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";                
            }
        }

        $data['draw'] = isset($_POST['draw']) ? (int) $_POST['draw'] + 1 : 0;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = $info;

        echo json_encode($data);
    }

    public function webview($id = null) 
    {
    	//header('X-Frame-Options: ALLOW-FROM https://www.messenger.com/');
		//header('X-Frame-Options: ALLOW-FROM https://www.facebook.com/');

    	if (is_null($id) || ! is_string($id) || strlen($id) < 10) {
			redirect('error_404', 'location');
        	exit();
    	}

    	// Gets single webview data
    	$form = $this->get_single_webview_by_id($id);

		// Shows 404 if webview not found
		if (count($form) < 1) {
			redirect('error_404', 'location');
        	exit();
		}

		// References form ID and data
		$form_id = isset($form[0]['form_id']) ? $form[0]['form_id'] : null;
		$form_data = isset($form[0]['form_data']) ? $form[0]['form_data'] : [];

		// Decodes form data
		$decoded_form_data = json_decode($form_data, true);

		//Get Subscriber id from url parameter: 
		$data['form_id'] =$id;
		$subscriber_id=$this->input->get('subscriber_id');
		$data['subscriber_id'] =$subscriber_id;

		// Preapares vars for view
		if(isset($form[0])) $data['form']= $form[0]; 
		else $data['form']['form_title'] = "Webview Form Display";

		$data['form_data'] = $this->render_webview_form($decoded_form_data);
        $data['body'] = 'webview_builder/view';
        $data['page_title'] = $this->lang->line('Form Details');

        // Get Facebook app ID 
        $fb_app_id_info=$this->basic->get_data('facebook_rx_config',$where=array('where'=>array('status'=>'1')));
        $data['fb_app_id']=isset($fb_app_id_info[0]['api_id']) ? $fb_app_id_info[0]['api_id']: "";


        $this->load->view('webview_builder/bare-theme', $data);  
    }

    public function get_submitted_subscribers()
    {
    	if($this->session->userdata('user_type') != 'Admin' && !in_array(261,$this->module_access))  exit();

    	$this->ajax_check();
    	$form_id = $this->input->post('form_id',true);
    	$searching = $this->input->post('searching',true);
    	$display_columns = array("#", 'image_path', 'first_name', 'last_name', 'subscribe_id', 'inserted_at', 'actions');

    	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    	$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    	$limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
    	$sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
    	$sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'messenger_bot_user_custom_form_webview_data.inserted_at';
    	$order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'asc';
    	$order_by=$sort." ".$order;

    	$sql = '';
    	if($searching != '')
    	{
    		$sql = "messenger_bot_subscriber.first_name like '%".$searching."%' OR messenger_bot_subscriber.last_name like '%".$searching."%' OR messenger_bot_subscriber.subscribe_id like '%".$searching."%'";
    		$this->db->where($sql);
    	}
    	$where = array(
    		'where' => array('web_view_form_canonical_id'=>$form_id)
    	);
    	$join = array('messenger_bot_subscriber'=>'messenger_bot_subscriber.subscribe_id=messenger_bot_user_custom_form_webview_data.subscriber_id,left');
    	$select = array('messenger_bot_subscriber.id','messenger_bot_subscriber.first_name','messenger_bot_subscriber.last_name','messenger_bot_subscriber.full_name','messenger_bot_subscriber.profile_pic','messenger_bot_subscriber.subscribe_id','messenger_bot_subscriber.image_path','messenger_bot_subscriber.page_table_id','messenger_bot_user_custom_form_webview_data.inserted_at');
    	$info = $this->basic->get_data('messenger_bot_user_custom_form_webview_data',$where,$select,$join,$limit,$start,$order_by,'messenger_bot_subscriber.subscribe_id');

    	if($sql != '') $this->db->where($sql);
    	$total_rows_array=$this->basic->count_row('messenger_bot_user_custom_form_webview_data',$where,"messenger_bot_user_custom_form_webview_data.id",$join,$group_by='messenger_bot_subscriber.subscribe_id');
    	$total_result=$total_rows_array[0]['total_rows'];


    	$base_url=base_url();
    	foreach ($info as $key => $value) 
    	{
    		$profile_pic = ($value['profile_pic']!="") ? "<img class='rounded-circle' style='height:40px;width:40px;' src='".$value["profile_pic"]."'>" :  "<img class='rounded-circle' style='height:40px;width:40px;' src='".base_url('assets/images/50x50.png')."'>";
    		$info[$key]['image_path']=($value["image_path"]!="") ? "<a  target='_BLANK' href='".base_url($value["image_path"])."'><img class='rounded-circle' style='height:40px;width:40px;' src='".base_url($value["image_path"])."'></a>" : $profile_pic;

    		$info[$key]['actions']='<a href="#" class="btn btn-circle btn-outline-info get_subscriber_formdata" data-id="'.$value['id'].'" subscribe_id="'.$value['subscribe_id'].'" page_table_id="'.$value['page_table_id'].'" data-form-id="'.$form_id.'" title="'.$this->lang->line('View Form Data').'" ><i class="fas fa-eye"></i></a>&nbsp;<a target="_BLANK" href="'.base_url('subscriber_manager/bot_subscribers/').$value['subscribe_id'].'" class="btn btn-circle btn-outline-warning" title="'.$this->lang->line('Go to subscriber list').'" ><i class="far fa-hand-point-right"></i></a>';
    		$info[$key]['inserted_at'] = date('Y-m-d H:i:s', strtotime($info[$key]['inserted_at']));
    	}


    	$data['draw'] = (int)$_POST['draw'] + 1;
    	$data['recordsTotal'] = $total_result;
    	$data['recordsFiltered'] = $total_result;
    	$data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

    	echo json_encode($data);

    }

    public function get_subscriber_formdata()
    {
      if($this->session->userdata('user_type') != 'Admin' && !in_array(261,$this->module_access))  exit();
      $this->ajax_check();
      $id = $this->input->post("id",true);
      $page_table_id = $this->input->post("page_id",true);
      $subscribe_id = $this->input->post("subscribe_id",true);
      $form_id = $this->input->post("form_id",true);

      $table_name = "messenger_bot_user_custom_form_webview_data";
      $where = array(
        "where"=>array(
          "messenger_bot_user_custom_form_webview_data.page_id"=>$page_table_id,
          "messenger_bot_user_custom_form_webview_data.subscriber_id"=>$subscribe_id,
          "messenger_bot_user_custom_form_webview_data.web_view_form_canonical_id"=>$form_id
        )
      );
      $join = array('webview_builder'=>"messenger_bot_user_custom_form_webview_data.web_view_form_canonical_id=webview_builder.canonical_id,left");
      $select = array("webview_builder.form_name","messenger_bot_user_custom_form_webview_data.data as form_data","messenger_bot_user_custom_form_webview_data.inserted_at","messenger_bot_user_custom_form_webview_data.web_view_form_canonical_id as form_id");
      $data = $this->basic->get_data($table_name,$where,$select,$join);

      $content = '
        <div class="col-12 col-sm-4">
          <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">';

      $i=1;
      foreach($data as $value)
      {
        $unique_id = 'formdata_tab_'.$i;
        $unique_id2 = 'formdata_tab_content_'.$i;
        if($i == 1) $active = 'active';
        else $active = '';
        $insert_date = date('jS M Y, H:i', strtotime($value['inserted_at']));
        $content .= '
            <li class="nav-item">
              <a class="nav-link '.$active.'" id="'.$unique_id.'" data-toggle="tab" href="#'.$unique_id2.'" role="tab" aria-controls="'.$unique_id.'" aria-selected="true">'
              .$value['form_name'].

              '<br/><p class="form_id">Form ID: '.$value['form_id'].'</p>
               <p class="insert_date">Submit Date: '.$insert_date.'</p>
              </a>

            </li>
        ';
        $i++;
      }
            
      $content .='</ul>
        </div>
        <div class="col-12 col-sm-8">
          <div class="tab-content no-padding" id="myTab2Content">';

      $i=1;
      foreach($data as $value)
      {
        $unique_id = 'formdata_tab_'.$i;
        $unique_id2 = 'formdata_tab_content_'.$i;
        if($i == 1) $active = 'active show';
        else $active = '';
        $content .= '<div class="tab-pane fade '.$active.'" id="'.$unique_id2.'" role="tabpanel" aria-labelledby="'.$unique_id.'">';
        $content .= '
          <div class="table-responsive">
            <table class="table table-bordered table-md">
              <tbody><tr>
                <th>Field</th>
                <th>Value</th>
              </tr>
        ';

        $form_data = json_decode($value['form_data'],true);
        foreach($form_data as $key=>$value)
        {
          $value = is_array($value) ? implode(", ", $value) : $value;
          $content .= '<tr>
                        <td>'.$key.'</td>
                        <td>'. $value .'</td>
                      </tr>';
        }

        $content .= '
            </tbody></table>
          </div>
        ';
        $content .= '</div>';
        $i++;
      }

      $content .='</div>
        </div>
      ';

      if(!empty($data))
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

    public function handle_form_details_data() 
    {
		if($this->session->userdata('user_type') != 'Admin' && !in_array(261,$this->module_access))  exit();

		// Kicks out if not an AJAX request
		if (! $this->input->is_ajax_request()) {
			$message = $this->lang->line('Bad Request');
			return $this->customJsonResponse($message);
		}

		$this->form_validation->set_rules('form_id', 'Form ID', 'required|alpha_numeric|min_length[10]|max_length[45]');

		if (false === $this->form_validation->run()) {
			$message = $this->form_validation->error('form_id');
			return $this->customJsonResponse($message);
		}

		$form_id = (string) $this->input->post('form_id');

        $table = 'webview_builder';

        $select = [
        	'webview_builder.id', 
        	'webview_builder.canonical_id', 
        	'webview_builder.user_id', 
        	'webview_builder.assign_label', 
        	'webview_builder.form_title', 
        	'webview_builder.inserted_at', 
        	'webview_builder.updated_at',
        	'facebook_rx_fb_page_info.page_name',
        	'messenger_bot_postback.postback_id', 
        	// 'messenger_bot_drip_campaign.campaign_name', 
        ];

        $join = [
        	'facebook_rx_fb_page_info' => 'webview_builder.page_id=facebook_rx_fb_page_info.id,left',
        	'messenger_bot_postback' => 'webview_builder.reply_template=messenger_bot_postback.id,left',
        ];

        $where = [
        	'where' => [
        		'webview_builder.canonical_id' => $form_id,
        	]
        ];

        $form_details = $this->basic->get_data($table, $where, $select, $join, 1);

		if (count($form_details) < 1) {
			redirect('error_404', 'location');
        	exit();
		}

		$user_id = isset($form_details[0]['user_id']) ? $form_details[0]['user_id'] : null;
		if ($user_id != $this->user_id) {
			$message = $this->lang->line('You do not have permission to view the form.');
			return $this->customJsonResponse($message);
		}

		$assign_label = isset($form_details[0]['assign_label']) ? explode(',', $form_details[0]['assign_label']) : [-1];

		$select = ['id', 'group_name'];
		$join = [];
		$where = [];

		if (is_array($assign_label)) {
			$where['where_in'] = ['id' => $assign_label];
		}

        $labels = $this->basic->get_data('messenger_bot_broadcast_contact_group', $where, $select, $join);

        $group_names = [];
        if (count($labels) > 0) {
        	foreach ($labels as $key => $label) {
        		$group_names[] = $label['group_name'];
        	}
        }

        // Modifies date format
        if (isset($form_details[0]['inserted_at'])) {
        	$form_details[0]['inserted_at'] = date('jS M y H:i', strtotime($form_details[0]['inserted_at']));
        } 

        // Adds group_name with different formatted values 
        $form_details[0]['group_name'] = $group_names;

        if (isset($form_details[0]['id'])) {
        	unset($form_details[0]['id']); 
        }

        if (isset($form_details[0]['assign_label'])) {
        	unset($form_details[0]['assign_label']);
        }

        if (isset($form_details[0]['user_id'])) {
        	unset($form_details[0]['user_id']);
        }

        echo json_encode($form_details[0]);
    }

    public function form_submit(){

    	//if($this->session->userdata('user_type') != 'Admin' && !in_array(261,$this->module_access))  exit();
    	$form_data=$this->input->post();

    	$insert_data=array();
    	$insert_data['subscriber_id']=$form_data['subscriber_id'];
    	$insert_data['web_view_form_canonical_id']=$form_data['webview_form_id'];
    	$insert_data['data']=json_encode($form_data);
    	$insert_data['inserted_at']=date('Y-m-d H:i:s');

    	//Collect all information of webview form for sending message or add label or assign Drip Messaging. 
    	$where=array('where'=>array('canonical_id'=>$form_data['webview_form_id']));
    	$webview_info=$this->basic->get_data('webview_builder',$where);

    	if(empty($webview_info) || $insert_data['subscriber_id']==""){
    		$response['error']='1';

    		if(empty($webview_info))
       			$response['error_message']=$this->lang->line("Form information not found.");
       		else
       			$response['error_message']=$this->lang->line("Subscriber information not found.");
       		echo json_encode($response);
    		exit; 
    	}

    	$form_title= isset($webview_info[0]['form_title']) ? $webview_info[0]['form_title']:"";

    	$label_ids=isset($webview_info[0]['assign_label']) ? $webview_info[0]['assign_label']:"";
    	$reply_template_id=isset($webview_info[0]['reply_template']) ? $webview_info[0]['reply_template']:"";


    	// Get subscriber information & page information
       $where = array("where"=> array('messenger_bot_subscriber.subscribe_id'=>$form_data['subscriber_id']));

       $select = array("facebook_rx_fb_page_info.facebook_rx_fb_user_info_id","facebook_rx_fb_page_info.page_id","page_access_token","first_name","last_name","messenger_bot_subscriber.status","facebook_rx_fb_page_info.id","messenger_bot_subscriber.user_id");
       $join  = array("facebook_rx_fb_page_info"=>"messenger_bot_subscriber.page_table_id=facebook_rx_fb_page_info.id,left");

       $table="messenger_bot_subscriber";
       $subscriber_info = $this->basic->get_data($table,$where,$select,$join);

       $page_access_token= isset($subscriber_info[0]['page_access_token']) ? $subscriber_info[0]['page_access_token'] : ""; 
       $page_id= isset($subscriber_info[0]['page_id']) ? $subscriber_info[0]['page_id'] : ""; 
       $subscriber_id=$insert_data['subscriber_id'];
       $page_table_id=isset($subscriber_info[0]['id']) ? $subscriber_info[0]['id'] : "";
       $user_id= isset($subscriber_info[0]['user_id']) ? $subscriber_info[0]['user_id'] : "";

       $subscriber_name = $subscriber_info[0]['first_name']; 

       $insert_data['page_id']=$page_table_id;

       if(!empty($subscriber_info)){

	       	// Assaign label to subscriber

	       if($label_ids!=""){

	    		//$this->assign_label_webhook_call($subscriber_id,$page_id,$label_ids);

	    		//DEPRECATED FUNCTION FOR QUICK BROADCAST// 
	   			$post_data_label_assign=array("psid"=>$subscriber_id,"fb_page_id"=>$page_id,"label_auto_ids"=>$label_ids);
				$url=base_url()."home/assign_label_webhook_call";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch,CURLOPT_POST,1);
				curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_label_assign);
				curl_setopt($ch, CURLOPT_TIMEOUT, 5);
				// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
				$reply_response=curl_exec($ch); 
				
	          }

	    	// Assaign Drip Messaging to subscriber 

	    	// Send message to subscriber 

	        if($reply_template_id!=""){

	          	$where=array('where'=>array('id'=>$reply_template_id));
	    		$reply_template_info=$this->basic->get_data('messenger_bot_postback',$where);

	    		$message_str = isset($reply_template_info[0]['template_jsoncode'])? $reply_template_info[0]['template_jsoncode'] : "";
	    		$message_array=array();
	    		if($message_str!='')
	            	$message_array = json_decode($message_str,true);


	            foreach($message_array as $msg)
	                {
	                $template_type_file_track=$msg['message']['template_type'];
	                unset($msg['message']['template_type']);

	                // typing on and typing on delay [alamin]
	                $enable_typing_on = $msg['message']['typing_on_settings'];
	                $enable_typing_on = ($enable_typing_on=='on')  ? 1 : 0;
	                unset($msg['message']['typing_on_settings']);
	                $typing_on_delay_time = $msg['message']['delay_in_reply'];
	                if($typing_on_delay_time=="") $typing_on_delay_time = 0;
	                unset($msg['message']['delay_in_reply']);
	                
	                /** Spintax **/
	                if(isset($msg['message']['text']))
	                    $msg['message']['text']=spintax_process($msg['message']['text']);               
	                
	                $msg['messaging_type'] = "RESPONSE";
	                $reply = json_encode($msg);     

	                $replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
	                $replace_with=array('{"id":"'.$subscriber_id.'"}',$subscriber_id);
	                $reply=str_replace($replace_search, $replace_with, $reply);

	                if(isset($subscriber_info[0]['first_name']))
	                    $reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
	                if(isset($subscriber_info[0]['last_name']))
	                    $reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
	                $access_token = $page_access_token;
	                if(isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1"){
	                
	                    // typing on and typing on delay [alamin]
	                    if($enable_typing_on) $this->sender_action($subscriber_id,"typing_on",$access_token);                                
	                    if($typing_on_delay_time>0) sleep($typing_on_delay_time);

	                    if($template_type_file_track=='video' || $template_type_file_track=='file' || $template_type_file_track=='audio'){
	                        $post_data=array("access_token"=>$access_token,"reply"=>$reply);
	                        $url=base_url()."home/send_reply_curl_call";
	                        $ch = curl_init();
	                        curl_setopt($ch, CURLOPT_URL, $url);
	                        curl_setopt($ch,CURLOPT_POST,1);
	                        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
	                        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	                        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	                        $reply_response=curl_exec($ch);  
	                
	                    }
	                    else
	                         $reply_response=$this->send_reply($access_token,$reply);
	                        /*****Insert into database messenger_bot_reply_error_log if get error****/
	                         if(isset($reply_response['error']['message'])){
	                            $bot_settings_id= $reply_template_info[0]['messenger_bot_table_id'];
	                            $reply_error_message= $reply_response['error']['message'];
	                            $error_time= date("Y-m-d H:i:s");
	                            $error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
	                                                "error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
	                                                "error_time"=>$error_time);
	                            $this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);
	                            
	                        }
	                }
	            }


	          }

	    	$this->basic->insert_data("messenger_bot_user_custom_form_webview_data",$insert_data);

	    	// Send JSON API DATA 

	    	$this->thirdparty_webhook_trigger($page_id,$subscriber_id,"trigger_webview","",$form_data['webview_form_id'],$form_data);


	    	// Send Email To Form Admin 	

	    	$product_short_name = $this->config->item('product_short_name');
	        $from = $this->config->item('institute_email');
	        $mask = $this->config->item('product_name');
	        $where = array();
	        $where['where'] = array('id'=>$user_id);
	        $user_email = $this->basic->get_data('users',$where,$select='');

	        $form_submit_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'webview_form_submit_admin')),array('subject','message'));

	        if(isset($form_submit_email_template[0]) && $form_submit_email_template[0]['subject'] != '' && $form_submit_email_template[0]['message'] != '') {

	            $to = $user_email[0]['email'];
	            $url = base_url();

	            $subject = str_replace(array('#APP_NAME#','#APP_URL#','#SUBSCRIBER_NAME#','#FORM_TITLE#'),array($mask,$url,$subscriber_name,$form_title),$form_submit_email_template[0]['subject']);

	            $form_data_json=json_encode($form_data);
	            
	            $message = str_replace(array('#APP_NAME#','#APP_URL#','#SUBSCRIBER_NAME#','#FORM_TITLE#','#FORM_DATA#'),array($mask,$url,$subscriber_name,$form_title,$form_data_json),$form_submit_email_template[0]['message']);

	            //send mail to user
	            @$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

	        }




	    	// End of Email Send to Form Admin


	    	$response['error']='0';
       		echo json_encode($response);
       }

       else{
       		// Subscriber information not found. 

       	$response['error']='1';
       	$response['error_message']=$this->lang->line("Subscriber information not found.");

       	echo json_encode($response);

       }
    }

    public function delete_form_data() 
	{
		if($this->session->userdata('user_type') != 'Admin' && !in_array(261,$this->module_access))  exit();

		// Kicks out if not an AJAX request
		if (! $this->input->is_ajax_request()) {
			$message = $this->lang->line('Bad Request');
			return $this->customJsonResponse($message);
		}

		// Validates form ID
		$this->form_validation->set_rules('form_id', 'Form ID', 'required|max_length[45]');
		if (false == $this->form_validation->run()) {
			$message = $this->form_validation->error('form_id');
			return $this->customJsonResponse($message);
		}

		// Gets the form 
    	$form_id = (string) $this->input->post('form_id');
    	$form = $this->get_single_webview_by_id($form_id);

		if (count($form) < 1) {
			$message = $this->lang->line('Bad Request');
			return $this->customJsonResponse($message);
		}

		// References user ID
		$user_id = isset($form[0]['user_id']) ? $form[0]['user_id'] : null;

		// Denies if the request doesn't come from owner or admin
		if ($user_id != $this->user_id) {
			$message = $this->lang->line('You do not have permission to delete the form.');
			return $this->customJsonResponse($message);			
		}

		// Attempts to delete the form data
		$form_id = isset($form[0]['form_id']) ? $form[0]['form_id'] : null;
		if ($this->basic->delete_data('webview_builder', ['id' => $form_id])) {
			$message = $this->lang->line('The form has been deleted successfully.');
			return $this->customJsonResponse($message, true);
		} else {
			$message = $this->lang->line('Something went wrong, please try again!');
			return $this->customJsonResponse($message);		
		}
    }

	/**
	 * Produces custom json response
	 *
	 * @param string $message
	 * @param bool $success
	 * @return void
	 */
	protected function customJsonResponse($message, $success = false) 
	{
		echo json_encode([
			'error' => $success ? false : true,
			'success' => $success,
			'message' => $message
		]);
	}

	/**
	 * Fetches single form data by id
	 *
	 * @param int $id
	 * @return array
	 */
	private function get_single_webview_by_id($id) 
	{
		// Prepares sql statements and clauses
		$where = [
			'where' => [
				'webview_builder.canonical_id' => $id,
				'webview_builder.deleted' => '0',
				'users.deleted' => '0'
			]
		];
		$select = ['webview_builder.id as form_id', 'webview_builder.canonical_id', 'webview_builder.form_name', 'webview_builder.form_title', 'webview_builder.form_data', 'webview_builder.page_id', 'webview_builder.assign_label', 'webview_builder.reply_template', 'webview_builder.inserted_at', 'users.id as user_id', 'users.name'];
		$join = ['users' => 'webview_builder.user_id=users.id,left'];

		// Executes query
		return $this->basic->get_data('webview_builder', $where, $select, $join, 1);
	}

	private function render_webview_form($form_data) 
	{
		if (! sizeof($form_data) > 0) {
			return null;
		}

		// Holds dynamically generated dom elements
		$output = '';

		// Holds button index
		$button_index = null;

		// Loop through form_data and build dom elements
		foreach ($form_data as $key => $form) {

			// Determines button index
			if ('button' == $form['type']) {
				$button_index = $key;
				continue;
			}

			$type = isset($form['type']) ? strip_tags($form['type']) : '';
			$subtype = isset($form['subtype']) ? strip_tags($form['subtype']) : '';
			$label = isset($form['label']) ? strip_tags($form['label']) : '';
			$description = isset($form['description']) ? strip_tags($form['description']) : '';
			$name = isset($form['name']) ? strip_tags($form['name']) : '';
			$classname = isset($form['className']) ? strip_tags($form['className']) : '';
			$placeholder = isset($form['placeholder']) ? strip_tags($form['placeholder']) : '';
			$maxlength = isset($form['maxlength']) ? (int) $form['maxlength'] : 200;

			$multiple = (isset($form['multiple']) && 1 == $form['multiple']) ? 'multiple' : null;
			$required = (isset($form['required']) && 1 == $form['required']) ? 'required' : null;

			switch ($form['type']) {
				case 'header':
					$allowed_tags = ['h1', 'h2', 'h3', 'h4'];

					if (in_array($subtype, $allowed_tags)) {
						$header = '<%1$s>%2$s</%3$s>';
						$output .= '<div class="form-group">';
						$output .= sprintf($header, $subtype, $label, $subtype);
						$output .= '</div>';
					}

					break;

				case 'text':
					$allowed_subtypes = ['text', 'password', 'email', 'color', 'tel'];

					if (in_array($subtype, $allowed_subtypes)) {
						$output .= '<div class="form-group">';

						if ($label) {
							$label_str = '<label>%s</label>';
							$output .= sprintf($label_str, $label);
						}

						if ($description) {
							$tooltip_str = '&nbsp;<a href="#" data-toggle="tooltip" title="" data-original-title="%1$s"><i class="fas fa-info"></i></a>';
							$output .= sprintf($tooltip_str, $description);
						}						

						$input = '<input name="%1$s" type="%2$s" class="%3$s" maxlength="%4$d" placeholder="%5$s" %6$s />';
						$output .= sprintf($input, $name, $subtype, $classname, $maxlength, $placeholder, $required);

						$output .= '</div>';
					}

					break;

				case 'textarea':
					$output .= '<div class="form-group">';

					if ($label) {
						$label_str = '<label>%s</label>';
						$output .= sprintf($label_str, $label);
					}

					if ($description) {
						$tooltip_str = '&nbsp;<a href="#" data-toggle="tooltip" title="" data-original-title="%1$s"><i class="fas fa-info"></i></a>';
						$output .= sprintf($tooltip_str, $description);
					}									

					$textarea_str = '<textarea name="%1$s" class="%2$s" %3$s></textarea>';
					$output .= sprintf($textarea_str, $name, $classname, $required);

					$output .= '</div>';

					break;

				case 'select':
					$values = isset($form['values']) ? $form['values'] : [];
					$select_options = '';

					if (sizeof($values) > 0) {
						$option = '<option value="%1$s" %2$s>%3$s</option>';
						foreach ($values as $key => $value) {
							$select_options .= sprintf(
								$option,
								(isset($value['value']) ? strip_tags($value['value']) : ''),
								((isset($value['selected']) && 1 == $value['selected']) ? 'selected' : null),
								(isset($value['label']) ? strip_tags($value['label']) : '')
							);
						}
					}

					$output .= '<div class="form-group">';

					if ($label) {
						$label_str = '<label>%s</label>';
						$output .= sprintf($label_str, $label);
					}

					if ($description) {
						$tooltip_str = '&nbsp;<a href="#" data-toggle="tooltip" title="" data-original-title="%1$s"><i class="fas fa-info"></i></a>';
						$output .= sprintf($tooltip_str, $description);
					}						

                    $trimmed_name = str_replace(['[', ']'], '', $name);
                    $name = $multiple ? $trimmed_name . '[]' : $trimmed_name;

					$select_str = '<select name="%1$s" class="select2 %2$s" %3$s %4$s>%5$s</select>';
					$output .= sprintf($select_str, $name, $classname, $multiple, $required, (strlen($select_options) > 0 ? $select_options : ''));

					$output .= '</div>';

					break;

				case 'radio-group':
					$values = isset($form['values']) ? $form['values'] : [];
					$radio_options = '';

					if (sizeof($values) > 0) {
						$radio_str = '<label class="custom-switch"><input name="%1$s" type="radio" value="%2$s" class="custom-switch-input" %3$s /><span class="custom-switch-indicator"></span><span class="custom-switch-description">%4$s</span></label>';
						foreach ($values as $key => $value) {
							$radio_options .= sprintf(
								$radio_str,
								$name,
								(isset($value['value']) ? strip_tags($value['value']) : null),
								((isset($value['selected']) && 1 == $value['selected']) ? 'checked' : null),
								(isset($value['label']) ? strip_tags($value['label']) : null)
							);
						}
					}

					$output .= '<div class="form-group">';

					if ($label) {
						$label_str = '<div class="control-label">%s</div>';
						$output .= sprintf($label_str, $label);
					}

					if ($description) {
						$tooltip_str = '&nbsp;<a href="#" data-toggle="tooltip" title="" data-original-title="%1$s"><i class="fas fa-info"></i></a>';
						$output .= sprintf($tooltip_str, $description);
					}						

					$output .= '<div class="custom-switches-stacked mt-2">';
					$output .= $radio_options;
					$output .= '</div>';

					$output .= '</div>';

					break;

				case 'checkbox-group':
                    $name = str_replace(['[', ']'], '', $name) . '[]';
					$values = isset($form['values']) ? $form['values'] : [];
					$checkbox_options = '';

					if (sizeof($values) > 0) {
						$radio_str = '<label class="selectgroup-item"><input name="%1$s" type="checkbox" value="%2$s" class="selectgroup-input" %3$s /><span class="selectgroup-button">%4$s</span></label>';
						foreach ($values as $key => $value) {
							$checkbox_options .= sprintf(
								$radio_str,
								$name,
								(isset($value['value']) ? strip_tags($value['value']) : null),
								((isset($value['selected']) && 1 == $value['selected']) ? 'checked' : null),
								(isset($value['label']) ? strip_tags($value['label']) : null)
							);
						}
					}

					$output .= '<div class="form-group">';

					if ($label) {
						$label_str = '<label class="form-label">%s</label>';
						$output .= sprintf($label_str, $label);
					}

					if ($description) {
						$tooltip_str = '&nbsp;<a href="#" data-toggle="tooltip" title="" data-original-title="%1$s"><i class="fas fa-info"></i></a>';
						$output .= sprintf($tooltip_str, $description);
					}				

					$output .= '<div class="selectgroup selectgroup-pills">';
					$output .= $checkbox_options;
					$output .= '</div>';

					$output .= '</div>';

					break;																

				case 'date':
					$output .= '<div class="form-group">';

					if ($label) {
						$label_str = '<label>%s</label>';
						$output .= sprintf($label_str, $label);
					}

					if ($description) {
						$tooltip_str = '&nbsp;<a href="#" data-toggle="tooltip" title="" data-original-title="%1$s"><i class="fas fa-info"></i></a>';
						$output .= sprintf($tooltip_str, $description);
					}						

					$input_str = '<input name="%1$s" type="text" class="datepicker_x %2$s" placeholder="%3$s" %4$s />';
					$output .= sprintf($input_str, $name, $classname, $placeholder, $required);

					$output .= '</div>';

					break;

				case 'time':
					$output .= '<div class="form-group">';

					if ($label) {
						$label_str = '<label>%s</label>';
						$output .= sprintf($label_str, $label);
					}

					if ($description) {
						$tooltip_str = '&nbsp;<a href="#" data-toggle="tooltip" title="" data-original-title="%1$s"><i class="fas fa-info"></i></a>';
						$output .= sprintf($tooltip_str, $description);
					}	

					$input_str = '<input name="%1$s" type="text" class="timepicker_x %2$s" placeholder="%3$s" %4$s />';
					$output .= sprintf($input_str, $name, $classname, $placeholder, $required);

					$output .= '</div>';

					break;
				default:
					break;
			}
		}

		// Adds button element at the very end of the dom elements
		if (null !== $button_index 
			&& (isset($form_data[$button_index]['type']) 
				&& 'button' == $form_data[$button_index]['type'])
		) {
			$alignment = isset($form['alignment']) ? strip_tags($form['alignment']) : '';
			$output .= '<div class="form-group ' . $alignment . '">';
			
			$form = $form_data[$button_index];
			$input_str = '<button id="webview_submit_button" type="submit" class="%1$s">%2$s</button>';
			$output .= sprintf(
				$input_str,
				(isset($form['className']) ? strip_tags($form['className']) : ''), 
				(isset($form['label']) ? strip_tags($form['label']) : '')
			);
			
			$output .= '</div>';
		} else {
			$output .= '<div class="form-group">';
			$output .= '<button id="webview_submit_button" type="submit" class="btn btn-primary">Submit</button>';
			$output .= '</div>';
		}

		return $output;
	}

	/**
	 * Inserts data into database
	 *
	 * @param string $table_name The name of database
	 * @param array $where An array with specified fields for data update 
	 * @param array $data The data to be inserted
	 * @return null|string
	 */
	private function update_form_data($table_name, $where, $data)
	{
		if ($this->basic->update_data($table_name, $where, $data)) {
			$this->session->unset_userdata('edit_webview_form_data');
			echo json_encode([
				'success' => true,
				'data' => $data,
				'message' => $this->lang->line('The form has been updated successfully')
			]);
			return;
		} else {
			$message = $this->lang->line('Something went wrong, please try again!');
			return $this->customJsonResponse($message); 
		}
	}

	/**
	 * Inserts data into database
	 *
	 * @param string $table_name The name of database
	 * @param array $data The data to be inserted
	 * @return null|string
	 */
	private function insert_form_data($table_name, $data) 
	{
		if ($this->basic->insert_data($table_name, $data)) {
			echo json_encode([
				'success' => true,
				'data' => $data,
				'message' => $this->lang->line('The form has been created successfully')
			]);
			return;
		} else {
			$message = $this->lang->line('Something went wrong, please try again!');
			return $this->customJsonResponse($message); 
		}
	}

	/**
	 * Generates hash from title
	 *
	 * @param string The form title
	 * @return string
	 */
	private function generate_canonical_id($title) 
	{
		$canonical_id = '';

		try {
			$canonical_id = $this->generate_hash($title);
		} catch (Exception $e) {
			// Logs error
			log_message('error', 'Could not generate hash while saving webview form in the ' . __METHOD__ . ' method.');
		}

		return $canonical_id;
	}

	/**
	 * Checks whether a button exists in form data
	 *
	 * @param string $json_data JSON form data
	 * @return bool
	 */
	private function checks_button_tag_in_json_form_data($json_data) 
	{
		$button_found = false;
		foreach ($json_data as $key => $value) {
			if (isset($value['type']) && 'button' === $value['type']) {
				$button_found = true;
				break;
			}
		}

		return (bool) $button_found;
	}

	/**
	 * Validates and strip tags from json form data
	 * 
	 * @param string $form_data JSON formatted string data
	 * @return bool|string
	 */
	private function validate_and_strip_tags_json_form_data($json_data) 
	{
		// Strips tags
		$stripped_form_data = (string) strip_tags(html_entity_decode($json_data));

		// Decodes and gets an array of form data
		$decoded_form_data = json_decode($stripped_form_data, true);

		// Checks if the JSON data is valid
		if (null === $decoded_form_data || ! is_array($decoded_form_data)) {
			return false;
		}

		return $decoded_form_data;
	}

	/**
	 * Generates cryptographically secured hash
	 * 
	 * @param int|string $hash_me The string to be hashed
	 * @param int $length The hash length
	 * @param string $algorithm The algorithm to be used for the hash
	 * @return string
	 */
	private function generate_hash($hash_me, $length = 10, $algorithm = 'ripemd256') 
	{
		// Generates random numbers
		$salt = mt_rand(10000000, 999999999);

		// The number of internal iterations to perform for the derivation
		$iterations = 1000;
		
		$hash = hash_pbkdf2($algorithm, $hash_me, $salt, $iterations, $length);

		return $hash;
	}

	public function get_pages() 
	{
		// Gets user info ID
		$facebook_rx_fb_user_info_id  =  $this->session->userdata('facebook_rx_fb_user_info');

		// Prepares sql statements and clauses
		$where = [
			'where' => [
				'facebook_rx_fb_user_info_id' => $facebook_rx_fb_user_info_id,
				'bot_enabled' => '1',
			]
		];

		$select = ['id', 'page_name'];

		// Executes query
		$pages = $this->basic->get_data('facebook_rx_fb_page_info', $where, $select, []);

		return count($pages) > 0 ? $pages : [];
	}

	public function get_labels($page_id)
	{
	   $where = array();
	   $where['where'] = array(
	   		'messenger_bot_broadcast_contact_group.user_id' => $this->user_id,
	   		"messenger_bot_broadcast_contact_group.page_id" => (int) $page_id,
	   		"messenger_bot_broadcast_contact_group.invisible" => "0"
	   	);

	    $select = ['messenger_bot_broadcast_contact_group.id', 'messenger_bot_broadcast_contact_group.group_name', 'facebook_rx_fb_page_info.page_name'];

		$join = [
			'facebook_rx_fb_page_info' => 'messenger_bot_broadcast_contact_group.page_id=facebook_rx_fb_page_info.id,left'
		];

	   	$group_info = $this->basic->get_data('messenger_bot_broadcast_contact_group', $where, $select, $join, $limit = '', $start = '', $order_by = 'group_name', $group_by = '', $num_rows = 0);

	   	$result = [];
	   	if (sizeof($group_info) > 0) {
	   		foreach ($group_info as $key => $value) {
	   			$result[$key]['value'] = $value['id'];
	   			$result[$key]['text'] = $value['group_name'] . ' [' . $value['page_name'] . ']';
	   		}
	   	}

	   return count($result) > 0 ? $result : [];
	}

	public function get_page_template($page_id) 
	{
		if(0 == $page_id) {
			return array();
		}

        $postback_data = $this->basic->get_data("messenger_bot_postback", array("where" => array("page_id" => $page_id, "is_template" => "1","template_for"=>"reply_message")), '', '', '', $start = NULL, $order_by = "template_name ASC");

        $push_postback = array();

        foreach ($postback_data as $key => $value) {

            $push_postback[$key]['value'] = $value['id'];
            $push_postback[$key]['text'] = $value['template_name'].' ['.$value['postback_id'].']';
        }

        return count($push_postback) ? $push_postback : [];
	}

	public function truncate_str ($str, $delimiter = '...', $encoding = 'UTF-8') 
	{
	    $truncated_str = mb_substr($str, 0, 60, $encoding);
	    
	    if (mb_strlen($truncated_str) < 60) {
	    	$delimiter = '';
	    }

		if (" " === mb_substr($truncated_str, -1, null, $encoding)) {
			return mb_substr($str, 0, 59, $encoding) . $delimiter;
		}

	    return $truncated_str . $delimiter;
	}


	/* 
	===============================================
	WEBVIEW BUILDER
	***********************************************
	*/



	/*
	===============================================
	JSON API CONNECTOR
	***********************************************
	*/
	public function json_api_connector()
	{
	    $this->json_api_connector_dashbaord();
	}
	public function json_api_connector_dashbaord()
	{
	 
	  if($this->session->userdata('user_type') != 'Admin' && !in_array(258,$this->module_access))  redirect('home/login_page', 'location');
	  $this->is_input_flow_addon_exists = $this->addon_exist("custom_field_manager");

	  $page_info = array();
	  $join      = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
	  
	  $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array("facebook_rx_fb_page_info.facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name','page_id'),$join);

	  $connector_data = $this->basic->get_data("messenger_bot_thirdparty_webhook",array("where"=>array("user_id"=>$this->user_id)),'','','','');

	  $page_postback_info = array();
	  $join1      = array('facebook_rx_fb_page_info'=>'messenger_bot_postback.page_id=facebook_rx_fb_page_info.id,left',);
	  $page_postback_info = $this->basic->get_data('messenger_bot_postback',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('messenger_bot_postback.*','page_name'),$join1);

	  $data['connector_data'] = $connector_data;
	  $data['page_title']     = $this->lang->line("Json API Connector");
	  $data['page_info']      = $page_info;
	  $data['body']           = "json_api_connector_dashboard";

	  $this->_viewcontroller($data); 
	}

	public function json_api_connector_dashbaord_data()
	{
	  if($this->session->userdata('user_type') != 'Admin' && !in_array(258,$this->module_access))  exit();
	  $this->ajax_check();

	  $searching       = trim($this->input->post("searching",true));
	  $post_date_range = $this->input->post("post_date_range",true);

	  $display_columns = array("#",'id','name','webhook_url','actions','page_name','added_date','last_trigger_time');

	  $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	  $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
	  $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
	  $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
	  $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
	  $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
	  $order_by=$sort." ".$order;

	  $where_simple=array();

	  $where_simple['user_id'] = $this->user_id;

	  if($post_date_range!="")
	  {
	    $exp = explode('|', $post_date_range);
	    $from_date = isset($exp[0])?$exp[0]:"";
	    $to_date   = isset($exp[1])?$exp[1]:"";

	    if($from_date!="Invalid date" && $to_date!="Invalid date")
	    {
	      $from_date = date('Y-m-d', strtotime($from_date));
	      $to_date   = date('Y-m-d', strtotime($to_date));
	      $where_simple["Date_Format(added_date,'%Y-%m-%d') >="] = $from_date;
	      $where_simple["Date_Format(added_date,'%Y-%m-%d') <="] = $to_date;
	    }
	  }

	  $sql = '';
	  if($searching !="") $sql = "(name LIKE  '%".$searching."%' OR page_name LIKE '%".$searching."%')";
	  if($sql != '') $this->db->where($sql);

	  $where  = array('where'=>$where_simple);

	  $table = "messenger_bot_thirdparty_webhook";
	  $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

	  $total_rows_array=$this->basic->count_row($table,$where,$count="id",$join="",$group_by='');
	  $total_result=$total_rows_array[0]['total_rows'];

	  for($i=0;$i<count($info);$i++)
	  {
	    $action_count = 3;

	    $info[$i]['page_name'] = "<div style='min-width:120px !important;'><a target='_BLANK' data-toggle='tooltip' title='".$this->lang->line("Visit Page")."' href='https://facebook.com/".$info[$i]['page_id']."'>".$info[$i]['page_name']."</a></div>";

	    if($info[$i]['added_date'] != "0000-00-00 00:00:00")
	      $info[$i]['added_date'] = "<div style='min-width:120px !important;'>".date("M j, y H:i",strtotime($info[$i]['added_date']))."</div>";
	    else 
	      $info[$i]['added_date'] = "<div style='min-width:120px !important;' class='text-muted'><i class='fas fa-exclamation-circle'></i> ".$this->lang->line('Not Found')."</div>";

	    if($info[$i]['last_trigger_time'] != "0000-00-00 00:00:00")
	      $info[$i]['last_trigger_time'] = "<div style='min-width:120px !important;'>".date("M j, y H:i",strtotime($info[$i]['last_trigger_time']))."</div>";
	    else 
	      $info[$i]['last_trigger_time'] = "<div style='min-width:120px !important;' class='text-muted'><i class='fas fa-exclamation-circle'></i> ".$this->lang->line('Not Triggered')."</div>";

	    $see_report = "<a href='#' data-toggle='tooltip' title='".$this->lang->line("See Report")."' class='btn btn-circle btn-outline-primary view_connector' table_id='".$info[$i]['id']."'><i class='fa fa-eye'></i></a>";

	    $editPost = "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Edit Campaign")."' class='btn btn-circle btn-outline-warning edit_connector' table_id='".$info[$i]['id']."'><i class='fa fa-edit'></i></a>";

	    $deletePost = "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Delete Campaign")."' class='btn btn-circle btn-outline-danger delete_connector' table_id='".$info[$i]['id']."'><i class='fa fa-trash'></i></a>";

	    // Action section started from here
	    $action_width = ($action_count*47)+20;
	    $info[$i]['actions'] = '<div class="dropdown d-inline dropright">
	    <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i></button>
	    <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';
	    $info[$i]['actions'] .= $see_report;
	    $info[$i]['actions'] .= $editPost;
	    $info[$i]['actions'] .= $deletePost;

	    $info[$i]['actions'] .= "</div></div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
	  }

	  $data['draw'] = (int)$_POST['draw'] + 1;
	  $data['recordsTotal'] = $total_result;
	  $data['recordsFiltered'] = $total_result;
	  $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

	  echo json_encode($data);
	}

	public function find_page_postback()
	{
	  if($this->session->userdata('user_type') != 'Admin' && !in_array(258,$this->module_access))  exit();
	  $this->ajax_check();

	  $data = array();
	  $elements = array();

	  $pageID = $this->input->post("page_id");
	  $user_id = $this->user_id;
	  $this->is_input_flow_addon_exists = $this->addon_exist("custom_field_manager");

	  $postback_infos = $this->basic->get_data("messenger_bot_postback",array('where'=>array('page_id'=>$pageID,'user_id'=>$user_id)));
	  $webview_infos = $this->basic->get_data("webview_builder",array('where'=>array('page_id'=>$pageID,'user_id'=>$user_id)));
	  
	  $elements['html1'] = '<div class="form-group">
	              <label>'.$this->lang->line("Choose Postback ID").'</label>
	              <select multiple name="postback[]" id="postback" class="form-control select2" style="width:100%;">';
	              if(!empty($postback_infos))
	              { 
	                foreach($postback_infos as $postback)
	                {
	                    $elements['html1'] .="<option value='trigger_postback_{$postback['postback_id']}'>{$postback['postback_id']}</option>";
	                }
	              } else
	              {
	                $elements['html1'] .='<span class="orange">No Postback ID Record Found for this page.</span>';
	              }

	  $elements['html1'] .= '</select></div><script>$("#postback").select2();</script>';


	  $elements['html2'] = '<div class="form-group">
	              <label>'.$this->lang->line("Choose Page webview").'</label>
	              <select multiple name="webview[]" id="webview" class="form-control select2" style="width:100%;">';
	              if(!empty($webview_infos))
	              { 
	                foreach($webview_infos as $webviews)
	                {
	                    $elements['html2'] .="<option value='trigger_webview_{$webviews['canonical_id']}'>".$webviews['form_name']." [".$webviews['canonical_id']."]"."</option>";
	                }
	              } else
	              {
	                $elements['html2'] .='<span class="orange">No Postback ID Record Found for this page.</span>';
	              }

	  $elements['html2'] .='</select></div><script>$("#webview").select2();</script>';


	   	if($this->is_input_flow_addon_exists) {
	  	   	if($this->basic->is_exist("modules",array("id"=>292))) {
	     		if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access)) {

     			$user_input_flow_campaigns = $this->basic->get_data("user_input_flow_campaign",array('where'=>array('page_table_id'=>$pageID,'user_id'=>$user_id)));
			  	$elements['html3'] = '<div class="form-group">
			              <label>'.$this->lang->line("Choose User Input Flow Campaign").'</label>
			              <select multiple name="input_flow[]" id="input_flow" class="form-control select2" style="width:100%;">';
			              if(!empty($user_input_flow_campaigns))
			              { 
			                foreach($user_input_flow_campaigns as $flow_campaign)
			                {
			                    $elements['html3'] .="<option value='trigger_userinput_{$flow_campaign['id']}'>".$flow_campaign['flow_name']."</option>";
			                }
			              } else
			              {
			                $elements['html3'] .='<span class="orange">'.$this->lang->line('No Input Flow Campaign Record Found for this page.').'</span>';
			              }

			  	$elements['html3'] .='</select></div><script>$("#input_flow").select2();</script>';
			  	}
			}
		}

	  echo json_encode($elements);
	  
	}


	public function ajax_connector_info_saving()
	{
	  if($this->session->userdata('user_type') != 'Admin' && !in_array(258,$this->module_access))  exit();
	  $this->ajax_check();

	  $ext = array();
	  $post = $_POST;

	  foreach($post as $key => $value) 
	  {
	      $$key=$value;
	  }

	  // get the page_name,page_id by page_table_id
	  $page_name = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('user_id'=>$this->user_id,'id'=>$page_table_id,'bot_enabled'=>'1')),array('page_name','page_id'));

	  $alldata = array();

	  $alldata['name']          = trim($connector_name);
	  $alldata['user_id']       = $this->user_id;
	  $alldata['webhook_url']   = trim($webhook_url);
	  $alldata['variable_post'] = implode(',',$variable_post);
	  $alldata['page_id']       = $page_name[0]['page_id'];
	  // $alldata['page_id']       = $page_table_id;
	  $alldata['page_name']     = $page_name[0]['page_name'];
	  $alldata['added_date']    = date("Y-m-d H:i:s");

	  // processing postback data
	  if(in_array("trigger_postbackid",$field))
	  {
	    $find_trigger_postback = array_search('trigger_postbackid',$field);
	    unset($field[$find_trigger_postback]);
	  
	    if(!empty($postback))
	    {
	      foreach ($postback as $single_postback) 
	      {
	        array_push($field,$single_postback);
	      }
	    }

	  }

	  // processing webview data
	  if(in_array("trigger_webview",$field))
	  {
	    $find_trigger_webview = array_search('trigger_webview',$field);
	    unset($field[$find_trigger_webview]);
	  
	    if(!empty($webview))
	    {
	      foreach ($webview as $single_webview) 
	      {
	        array_push($field,$single_webview);
	      }
	    }

	  }

	  // processing webview data
	  if(in_array("trigger_user_input",$field))
	  {
	    $find_trigger_user_input = array_search('trigger_user_input',$field);
	    unset($field[$find_trigger_user_input]);
	  
	    if(!empty($input_flow))
	    {
	      foreach ($input_flow as $single_input_flow) 
	      {
	        array_push($field,$single_input_flow);
	      }
	    }

	  }


	  $table = 'messenger_bot_thirdparty_webhook';

	  if($this->basic->insert_data($table,$alldata))
	  {
	    $inserted_id = $this->db->insert_id();
	    $triggered_table = array();

	    foreach ($field as $value) 
	    {
	      $triggered_table['webhook_id']     = $inserted_id;
	      $triggered_table['trigger_option'] = $value;
	      $this->basic->insert_data('messenger_bot_thirdparty_webhook_trigger',$triggered_table);
	    }

	    $ext['result'] = 1;
	    $ext['msg'] = $this->lang->line("Connection has been Created successfully.");

	  } else 
	  {
	    $ext['result'] = 0;
	    $ext['msg']    = $this->lang->line("Something went wrong,please try again.");
	  }

	  echo json_encode($ext);

	}


	public function ajax_view_connector_info()
	{
	  if($this->session->userdata('user_type') != 'Admin' && !in_array(258,$this->module_access))  exit();
	  $this->ajax_check();

	  $table_id       = $this->input->post('table_id',true);
	  $table          = 'messenger_bot_thirdparty_webhook';
	  $where['where'] = array('id'=>$table_id,'user_id'=>$this->user_id);
	  $result         = $this->basic->get_data($table,$where);
	  $webhook_id     = $result[0]['id'];
	  $name           = $result[0]['name'];
	  $webhook_url    = $result[0]['webhook_url'];
	  $variable_post  = $result[0]['variable_post'];
	  $page_name      = $result[0]['page_name'];
	  $last_trigger   = $result[0]['last_trigger_time'];
	  $trigger_option = $this->basic->get_data('messenger_bot_thirdparty_webhook_trigger',array('where'=>array('webhook_id'=>$webhook_id)),array('trigger_option'));

	  $trigger   = implode(',', array_map(function($el){ return $el['trigger_option']; }, $trigger_option));
	  $triggered = str_replace(array('trigger_','_'),' ',$trigger);

	  $last_activity = $this->basic->get_data('messenger_bot_thirdparty_webhook_activity',array('where'=>array('webhook_id'=>$webhook_id)),$select='',$join='',$limit='',$start=NULL,$order_by='id desc');

	  if($result != '')
	  {
	    $str = '<div class="activities" style="display:block;">
	                <div class="row">
	                  <div class="col-12 col-md-6">
	                    <div class="activity">
	                      <div class="activity-detail">
	                        <div class="mb-2"><h6 class="text-job text-primary">'.$this->lang->line('Name').'</h6></div>
	                        <span>'.$name.'</span>
	                      </div>
	                    </div>

	                    <div class="activity">
	                      <div class="activity-detail">
	                        <div class="mb-2"><h6 class="text-job text-primary">'.$this->lang->line('Webhook URL').'</h6></div>
	                        <span>'.$webhook_url.'</span>
	                      </div>
	                    </div>

	                    <div class="activity">
	                      <div class="activity-detail">
	                        <div class="mb-2"><h6 class="text-job text-primary">'.$this->lang->line('Triggered Webhook').'</h6></div>
	                        <span>'.$triggered.'</span>
	                      </div>
	                    </div>
	                  </div>

	                  <div class="col-12 col-md-6">

	                    <div class="activity">
	                      <div class="activity-detail">
	                        <div class="mb-2"><h6 class="text-job text-primary">'.$this->lang->line('Page Name').'</h6></div>
	                        <span>'.$page_name.'</span>
	                      </div>
	                    </div>

	                    <div class="activity">
	                      <div class="activity-detail" id="last_activity_detail">
	                        <div class="mb-2"><h6 class="text-job text-primary">'.$this->lang->line('Last Triggered Time').'</h6></div>
	                        <span>'.$last_trigger.'</span>
	                      </div>
	                    </div>

	                    <div class="activity">
	                      <div class="activity-detail">
	                        <div class="mb-2"><h6 class="text-job text-primary">'.$this->lang->line('Data to Send').'</h6></div>
	                        <span>'.str_replace('_',' ',$variable_post).'</span>
	                      </div>
	                    </div>

	                  </div>
	                </div>
	              </div>';
	    echo $str;
	  } 
	}

	public function ajax_get_connector_last_activities()
	{
	  if($this->session->userdata('user_type') != 'Admin' && !in_array(258,$this->module_access))  exit();
	  $this->ajax_check();

	  $table_id = $this->input->post('table_id');
	  
	  $display_columns = array("#",'id','http_code','curl_error','post_data','post_time');

	  $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	  $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
	  $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
	  $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
	  $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
	  $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
	  $order_by=$sort." ".$order;

	  $get_webhook_id = $this->basic->get_data('messenger_bot_thirdparty_webhook',array('where'=>array('id'=>$table_id,'user_id'=>$this->user_id)));
	  $webhook_id = $get_webhook_id[0]['id'];

	  $where['where'] = array('webhook_id'=>$webhook_id);
	  $info = $this->basic->get_data('messenger_bot_thirdparty_webhook_activity',$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

	  $total_rows_array=$this->basic->count_row('messenger_bot_thirdparty_webhook_activity',$where,$count="id",$join='',$group_by='');
	  $total_result=$total_rows_array[0]['total_rows'];

	  for($i=0;$i<count($info);$i++)
	  {
	    if($info[$i]['post_time'] != "0000-00-00 00:00:00")
	      $info[$i]['post_time'] = "<div style='min-width:100px !important;'>".date("M j, y H:i",strtotime($info[$i]['post_time']))."</div>";

	    if($info[$i]['post_data'] != "")
	      $info[$i]['post_data'] = "<a href='#' data-toggle='tooltip' data-placement='bottom' title='".$this->lang->line('See Post Data')."' class='btn btn-outline-primary btn-circle view_post_data' post_data='".htmlspecialchars($info[$i]['post_data'],ENT_QUOTES)."'><i class='fas fa-eye'></i></a><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
	  }

	  $data['draw'] = (int)$_POST['draw'] + 1;
	  $data['recordsTotal'] = $total_result;
	  $data['recordsFiltered'] = $total_result;
	  $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

	  echo json_encode($data);
	}

	public function ajax_get_update_connector_info()
	{
	  if($this->session->userdata('user_type') != 'Admin' && !in_array(258,$this->module_access))  exit();
	  $this->ajax_check();

	  $this->is_input_flow_addon_exists = $this->addon_exist("custom_field_manager");

	  $table_id       = $this->input->post("table_id");
	  $table          = 'messenger_bot_thirdparty_webhook';
	  $where['where'] = array('id'=>$table_id);
	  $info           = $this->basic->get_data($table,$where);
	  $connector_name = $info[0]['name'];
	  $webhook_url    = $info[0]['webhook_url'];
	  $webhook_id     = $info[0]['id'];
	  $pageid         = $info[0]['page_id'];

	  // for postback infos
	  $triggered_postback_val = array();
	  $pageInfoPostack = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('page_id'=>$pageid,'bot_enabled'=>'1')));

	  $get_page_postbacks = $this->basic->get_data('messenger_bot_postback',array('where'=>array('page_id'=>$pageInfoPostack[0]['id'])));
	  $get_page_webviews = $this->basic->get_data('webview_builder',array('where'=>array('page_id'=>$pageInfoPostack[0]['id'])));


  	   	if($this->is_input_flow_addon_exists) {
  	   		if($this->basic->is_exist("modules",array("id"=>292))) {
	   			if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access)) {

	 		 	$get_page_user_input_flow_campaigns = $this->basic->get_data('user_input_flow_campaign',array('where'=>array('page_table_id'=>$pageInfoPostack[0]['id'])));
	 		 	}
	 		}
	 	}

	  $triggered_info = $this->basic->get_data('messenger_bot_thirdparty_webhook_trigger',array('where'=>array('webhook_id'=>$webhook_id)),array('trigger_option'));


	  foreach ($triggered_info as $single_triggered_postback) 
	  {
	    foreach ($single_triggered_postback as $value) 
	    {
	      array_push($triggered_postback_val,$value);
	    }
	  }


	  $triggered_val = array();

	  foreach ($triggered_info as $triggered) 
	  {
	    foreach ($triggered as $value) 
	    {
	      array_push($triggered_val,$value);
	    }
	  }


	  // tiggered value section
	  if(in_array("trigger_email",$triggered_val)) $checked_email = "checked";
	  else $checked_email = "";
	  if(in_array("trigger_phone_number",$triggered_val)) $checked_phone = "checked";
	  else $checked_phone = "";
	  if(in_array("trigger_location",$triggered_val)) $checked_location= "checked";
	  else $checked_location= "";
	  if(in_array("trigger_birthdate",$triggered_val)) $checked_birthdate= "checked";
	  else $checked_birthdate= "";

	  foreach($triggered_val as $string1) 
	  {
	      if(strpos($string1, 'trigger_postback') !== FALSE)
	      {
	         $checked_postback= "checked";
	         break;
	      }
	      else
	      {
	        $checked_postback= "";
	      }
	  }

	  foreach($triggered_val as $string2) 
	  {
	      if(strpos($string2, 'trigger_webview') !== FALSE)
	      {
	         $checked_webview = "checked";
	         break;
	      }
	      else
	      {
	        $checked_webview= "";
	      }
	  }

	  foreach($triggered_val as $string3) 
	  {
	      if(strpos($string3, 'trigger_userinput') !== FALSE)
	      {
	         $checked_user_input = "checked";
	         break;
	      }
	      else
	      {
	        $checked_user_input= "";
	      }
	  }

	  // for checking the sending data
	  $updated_variable_post = explode(",",$info[0]['variable_post']);

	  if(in_array("psid",$updated_variable_post)) $psid = "checked";
	  else $psid = "";
	  if(in_array("first_name",$updated_variable_post)) $first_name = "checked";
	  else $first_name = "";
	  if(in_array("last_name",$updated_variable_post)) $last_name = "checked";
	  else $last_name = "";
	  if(in_array("subscribed_at",$updated_variable_post)) $subscribed_at = "checked";
	  else $subscribed_at= "";
	  if(in_array("email",$updated_variable_post)) $email = "checked";
	  else $email = "";
	  if(in_array("labels",$updated_variable_post)) $labels = "checked";
	  else $labels = "";
	  if(in_array("page_id",$updated_variable_post)) $page_id = "checked";
	  else $page_id = "";
	  if(in_array("page_name",$updated_variable_post)) $page_name = "checked";
	  else $page_name = "";
	  if(in_array("phone_number",$updated_variable_post)) $phone_number = "checked";
	  else $phone_number = "";
	  if(in_array("user_location",$updated_variable_post)) $user_location = "checked";
	  else $user_location = "";
	  if(in_array("postbackid",$updated_variable_post)) $postbackid = "checked";
	  else $postbackid = "";
	  if(in_array("birthdate",$updated_variable_post)) $birthdate = "checked";
	  else $birthdate = "";
	  if(in_array("formdata",$updated_variable_post)) $formdata = "checked";
	  else $formdata = "";
	  if(in_array("user_input_flow_campaign",$updated_variable_post)) $user_input_flow_campaign = "checked";
	  else $user_input_flow_campaign = "";

	  // this for page list and selected page section
	  $join     = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
	  $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,"facebook_rx_fb_page_info.facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name','page_id'),$join);

	  $update_form = '<form id="json_api_connector_update_form" action="" method="POST">
	                      <input type="hidden" name="table_id" id="table_id" value="'.$table_id.'">';
	  // name section
	  $update_form .= '<div class="row">
	                    <div class="col-12 col-md-6">
	                      <div class="form-group">
	                          <label>'.$this->lang->line("Connection Name").'</label>
	                          <input type="text" class="form-control" id="connector_name" name="connector_name" value="'.$connector_name.'">
	                      </div>
	                    </div>';

	  // page selection
	  $update_form .= '<div class="col-12 col-md-6"> 
	                    <div class="form-group">
	                      <label>'.$this->lang->line("Please select a page").'</label>
	                      <select name="updated_page_table_id" id="updated_page_table_id" class="form-control select2" style="width:100%">';
	                        foreach ($page_info as $page_list) 
	                        {
	                            if ($page_list['page_id'] == $info[0]['page_id'])
	                              $update_form .='<option value="'.$page_list['id'].'" selected>'.$info[0]['page_name'].'</option>';
	                            else
	                              $update_form .='<option value="'.$page_list['id'].'">'.$page_list['page_name'].'</option>'; 
	                        }
	  $update_form .=     '</select>
	                    </div>
	                  </div>
	                  <script>$("#updated_page_table_id").select2();</script>';

	  // webhook url section
	  $update_form .= '<div class="col-12">
	                    <div class="form-group">
	                      <label>'.$this->lang->line("Webhook URL").'<span style="color:red"> *</span></label>
	                      <input type="text" class="form-control" id="updated_webhook_url" name="updated_webhook_url" value="'.$webhook_url.'">
	                    </div>
	                  </div>';

	  // triggered options
	  $update_form .= '<div class="col-12">
	                    <div class="form-group">
	                      <label>'.$this->lang->line("What Field Change Trigger Webhook").'</label>
	                      <div class="row">
	                        <div class="col-12 col-md-3">
	                          <div class="custom-control custom-checkbox">
	                            <input type="checkbox" value="trigger_email" id="trigger_email_updated" name="updated_field[]" '.$checked_email.' class="custom-control-input">
	                            <label class="custom-control-label" for="trigger_email_updated">'.$this->lang->line("Email").'</label>
	                          </div>

	                          <div class="custom-control custom-checkbox">
	                            <input type="checkbox" value="trigger_phone_number" id="trigger_phone_number_updated" name="updated_field[]" '.$checked_phone.' class="custom-control-input">
	                            <label class="custom-control-label" for="trigger_phone_number_updated">'. $this->lang->line("phone number").'</label>
	                          </div>
	                        </div>

	                        <div class="col-12 col-md-3">
	                          <div class="custom-control custom-checkbox">
	                            <input type="checkbox" value="trigger_location" id="trigger_location_updated" name="updated_field[]" '.$checked_location.' class="custom-control-input">
	                            <label class="custom-control-label" for="trigger_location_updated">'.$this->lang->line("location").'</label>
	                          </div>

	                          <div class="custom-control custom-checkbox">
	                              <input type="checkbox" value="trigger_birthdate" id="trigger_birthdate_updated" name="updated_field[]" '.$checked_birthdate.' class="custom-control-input">
	                              <label class="custom-control-label" for="trigger_birthdate_updated">'.$this->lang->line("Birthdate").'</label>
	                          </div>
	                        </div>

	                        <div class="col-12 col-md-3">
	                          <div class="custom-control custom-checkbox">
	                            <input type="checkbox" value="trigger_postbackid" id="trigger_postbackid_updated" name="updated_field[]" '.$checked_postback.' class="custom-control-input">
	                            <label class="custom-control-label" for="trigger_postbackid_updated">'.$this->lang->line("Postback ID").'</label>
	                          </div>
	                          <div class="custom-control custom-checkbox">
	                              <input type="checkbox" value="trigger_webview" id="trigger_webview_updated" name="updated_field[]" '.$checked_webview.' class="custom-control-input">
	                              <label class="custom-control-label" for="trigger_webview_updated">'.$this->lang->line("Webview Form").'</label>
	                          </div>
	                        </div>';
                    	   	if($this->is_input_flow_addon_exists) {
                    	   	if($this->basic->is_exist("modules",array("id"=>292))) {
                	   		if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access)) {
                    	  		$update_form .= '<div class="col-12 col-md-3">
		                                              <div class="custom-control custom-checkbox">
		                                                <input type="checkbox" value="trigger_user_input" id="trigger_user_input_updated" name="updated_field[]" '.$checked_user_input.' class="custom-control-input">
		                                                <label class="custom-control-label" for="trigger_user_input_updated">'.$this->lang->line("User input flow").'</label>
		                                              </div>
                	                    		</div>';
		                    	    }
		                    	}
		                    }
	   	$update_form .= '</div>
	                    </div>
	                   </div>';

	  	// postback ID div
	  	$update_form .= '<div class="col-12 col-md-6" id="updated_postback_div">
	                      <div class="form-group">
	                        <label>'.$this->lang->line("Choose Postback ID").'</label>
	                        <select multiple class="form-control select2" name="postback[]" id="postback_updated" style="width:100%;">';
	                        if(!empty($get_page_postbacks))
	                        { 
	                          foreach ($get_page_postbacks as $postback) 
	                          { 
	                            if(in_array("trigger_postback_".$postback['postback_id'],$triggered_postback_val)) $postbackchecked="selected";
	                            else $postbackchecked = "";

	                            $update_form .="<option value='trigger_postback_{$postback['postback_id']}' {$postbackchecked}>{$postback['postback_id']}</option>";
	                          }

	                        } else
	                        {
	                          $update_form .='<span class="red">No Postback ID Record Found for this page.</span>';
	                        }

	  $update_form .= '</select></div></div>
	                  <script>$("#postback_updated").select2();</script>';

	  // webview data div
	  $update_form .= '<div class="col-12 col-md-6" id="updated_webview_div">
	                    <div class="form-group">
	                      <label>'.$this->lang->line("Choose Page Webview").'</label>
	                      <select multiple class="form-control select2" name="webview[]" id="webview_updated" style="width:100%;">';
	                      if(!empty($get_page_webviews))
	                      { 
	                        foreach ($get_page_webviews as $webviews) 
	                        { 
	                          if(in_array("trigger_webview_".$webviews['canonical_id'],$triggered_postback_val)) $webviewschecked="selected";
	                          else $webviewschecked = "";

	                          $update_form .="<option value='trigger_webview_{$webviews['canonical_id']}' {$webviewschecked}>".$webviews['form_name']." [".$webviews['canonical_id']."]"."</option>";
	                        }

	                      } else
	                      {
	                        $update_form .='<span class="red">No Webview Record Found for this page.</span>';
	                      }

	    $update_form .= '</select></div></div><script>$("#webview_updated").select2();</script>';

	  	// User Input Flow data div
	   	if($this->is_input_flow_addon_exists) {
	   	if($this->basic->is_exist("modules",array("id"=>292))) {
   		if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access)) {
	  	$update_form .= '<div class="col-12 col-md-6" id="updated_input_flow_div">
	                    <div class="form-group">
	                      <label>'.$this->lang->line("Choose User Input Flow Campaign").'</label>
	                      <select multiple class="form-control select2" name="input_flow[]" id="input_flow_updated" style="width:100%;">';
	                      if(!empty($get_page_user_input_flow_campaigns))
	                      { 
	                        foreach ($get_page_user_input_flow_campaigns as $flow_campaign) 
	                        { 
	                          if(in_array("trigger_userinput_".$flow_campaign['id'],$triggered_postback_val)) $userinput_flow_checked="selected";
	                          else $userinput_flow_checked = "";

	                          $update_form .="<option value='trigger_userinput_{$flow_campaign['id']}' {$userinput_flow_checked}>".$flow_campaign['flow_name']."</option>";
	                        }

	                      } else
	                      {
	                        $update_form .='<span class="red">No User Input Flow Campaign Record Found for this page.</span>';
	                      }

	  	$update_form .= '</select></div></div><script>$("#input_flow_updated").select2();</script>';
			  }
			}
		}

	  	$update_form .= '</div>';

	  	// sending data
	  	$update_form .= '<div class="row">
	                    <div class="col-12">
	                      <div class="form-group">
	                        <label>'.$this->lang->line("Which Data You Want To Send").'</label>
	                        <div class="row">
	                          <div class="col-12 col-md-3">
	                            <div class="custom-control custom-checkbox">
	                              <input type="checkbox" value="psid" id="psid_updated" name="updated_variable_post[]" '.$psid.' class="custom-control-input">
	                              <label class="custom-control-label" for="psid_updated">'.$this->lang->line('PSID').'</label>
	                            </div>
	                            <div class="custom-control custom-checkbox">
	                              <input type="checkbox" value="subscribed_at" id="subscribed_at_updated" name="updated_variable_post[]" '.$subscribed_at.' class="custom-control-input">
	                              <label class="custom-control-label" for="subscribed_at_updated">'.$this->lang->line("Subscribed At").'</label>
	                            </div>
	                            <div class="custom-control custom-checkbox">
	                              <input type="checkbox" value="postbackid" id="postbackid_updated" name="updated_variable_post[]" '.$postbackid.'  class="custom-control-input">
	                              <label class="custom-control-label" for="postbackid_updated">'.$this->lang->line("Postback ID").'</label>
	                            </div>
	                            <div class="custom-control custom-checkbox">
	                                <input type="checkbox" value="formdata" id="formdata_updated" name="updated_variable_post[]" '.$formdata.' class="custom-control-input">
	                                <label class="custom-control-label" for="formdata_updated">'.$this->lang->line('Webview form data').'</label>
	                            </div>
	                          </div>

	                          <div class=" col-12 col-md-3">
	                            <div class="custom-control custom-checkbox">
	                              <input type="checkbox" value="first_name" id="first_name_updated" name="updated_variable_post[]" '.$first_name.' class="custom-control-input">
	                              <label class="custom-control-label" for="first_name_updated">'.$this->lang->line("First Name").'</label>
	                            </div>
	                            <div class="custom-control custom-checkbox">
	                              <input type="checkbox" value="last_name" id="last_name_updated" name="updated_variable_post[]" '.$last_name.' class="custom-control-input">
	                              <label class="custom-control-label" for="last_name_updated">'.$this->lang->line("Last Name").'</label>
	                            </div>
	                            <div class="custom-control custom-checkbox">
	                              <input type="checkbox" value="email" id="email_updated" name="updated_variable_post[]" '.$email.' class="custom-control-input">
	                              <label class="custom-control-label" for="email_updated">'.$this->lang->line("Email").'</label>
	                            </div>';
                        	   	if($this->is_input_flow_addon_exists) {
                        	   	if($this->basic->is_exist("modules",array("id"=>292))) {
                    	   		if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access)) {
	                            $update_form .= '<div class="custom-control custom-checkbox">
	                                <input type="checkbox" value="user_input_flow_campaign" id="user_input_flow_campaign_updated" name="updated_variable_post[]" '.$user_input_flow_campaign.' class="custom-control-input">
	                                <label class="custom-control-label" for="user_input_flow_campaign_updated">'.$this->lang->line("User input data").'</label>
	                            </div>';
				                        }
				                    }
				                }
	                            
		$update_form .= '</div>

                      	<div class=" col-12 col-md-3">
                            <div class="custom-control custom-checkbox">
                              <input type="checkbox" value="page_id" id="page_id_updated" name="updated_variable_post[]" '.$page_id.' class="custom-control-input">
                              <label class="custom-control-label" for="page_id_updated">'.$this->lang->line("Page ID").'</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                              <input type="checkbox" value="page_name" id="page_name_updated" name="updated_variable_post[]" '.$page_name.' class="custom-control-input">
                              <label class="custom-control-label" for="page_name_updated">'.$this->lang->line("Page Name").'</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                              <input type="checkbox" value="phone_number" id="phone_number_updated" name="updated_variable_post[]" '.$phone_number.' class="custom-control-input">
                              <label class="custom-control-label" for="phone_number_updated">'.$this->lang->line("Phone number").'</label>
                            </div>
                      	</div>

                      	<div class="col-12 col-md-3">
                          	<div class="custom-control custom-checkbox">
                          	  <input type="checkbox" value="birthdate" id="birthdate_updated" name="updated_variable_post[]" '.$birthdate.' class="custom-control-input">
                          	  <label class="custom-control-label" for="birthdate_updated">'.$this->lang->line('Birthdate').'</label>
                          	</div>
                          	<div class="custom-control custom-checkbox">
                          	    <input type="checkbox" value="user_location" id="user_location_updated" name="updated_variable_post[]" '.$user_location.' class="custom-control-input">
                          	    <label class="custom-control-label" for="user_location_updated">'.$this->lang->line("Location").'</label>
                          	</div>
                            
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" value="labels" id="labels_updated" name="updated_variable_post[]" '.$labels.' class="custom-control-input">
                                <label class="custom-control-label" for="labels_updated">'.$this->lang->line("Labels").'</label>
                            </div>
                      	</div>
                 		</div>
                      	</div>
	                    </div>
	                  </div>
	                </form><br><br>

	                <div class="row">
	                  <div class="col-12">
	                    <button id="save_updated_connector_infos" class="btn btn-lg btn-primary float-left"><i class="fas fa-edit"></i> '.$this->lang->line("update").'</button>
	                    <a id="cancel" class="btn btn-lg btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> '.$this->lang->line('Cancel').'</a>
	                    </div>
	                </div>';

	  echo $update_form;
	}


	public function find_page_update_postback()
	{
	  if($this->session->userdata('user_type') != 'Admin' && !in_array(258,$this->module_access))  exit();
	  $this->ajax_check();
	  $triggered_postback_webview_val = array();

	  $pageID = $this->input->post("page_id",true);
	  $tableID = $this->input->post('table_id',true);
	  $this->is_input_flow_addon_exists = $this->addon_exist("custom_field_manager");

	  $triggered_postback_webview_info = $this->basic->get_data("messenger_bot_thirdparty_webhook_trigger",array('where'=>array('webhook_id'=>$tableID)),array('trigger_option'));

	  $postback_infos = $this->basic->get_data("messenger_bot_postback",array('where'=>array('page_id'=>$pageID,'user_id'=>$this->user_id)));
	  $webview_infos = $this->basic->get_data("webview_builder",array('where'=>array('page_id'=>$pageID,'user_id'=>$this->user_id)));

	 	if($this->is_input_flow_addon_exists) {
	    	if($this->basic->is_exist("modules",array("id"=>292))) {
	  	  		if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access)) {
	  			
	  			$user_input_flow_campaigns = $this->basic->get_data("user_input_flow_campaign",array('where'=>array('page_table_id'=>$pageID,'user_id'=>$this->user_id)));
	  			}
	  		}
	  	}

	  foreach ($triggered_postback_webview_info as $single_triggered_postback_webview) 
	  {
	    foreach ($single_triggered_postback_webview as $value1) 
	    {
	      array_push($triggered_postback_webview_val,$value1);
	    }
	  }

	  $elements = array();

	  $elements['html1'] = '<div class="form-group">
	              <label>'.$this->lang->line("Choose Postback ID").'</label>
	              <select multiple name="postback[]" id="postback_updated" class="form-control select2" style="width:100%;">';
	              if(!empty($postback_infos))
	              { 
	                foreach ($postback_infos as $postback) 
	                {
	                  if(in_array("trigger_postback_".$postback['postback_id'],$triggered_postback_webview_val)) $postback_checked="selected";
	                  else $postback_checked = "";

	                  $elements['html1'] .="<option value='trigger_postback_{$postback['postback_id']}' {$postback_checked}>{$postback['postback_id']}</option>";
	                }
	              } else
	              {
	                $elements['html1'] .='<span class="red">No Postback ID Record Found for this page.</span>';
	              }

	  $elements['html1'] .='</select></div><script>$("#postback_updated").select2();</script>';


	  $elements['html2'] = '<div class="form-group">
	              <label>'.$this->lang->line("Choose Webview").'</label>
	              <select multiple name="webview[]" id="webview_updated" class="form-control select2" style="width:100%;">';
	              if(!empty($webview_infos))
	              { 
	                foreach ($webview_infos as $webviews) 
	                {
	                  if(in_array("trigger_webview_".$webviews['canonical_id'],$triggered_postback_webview_val)) $webviews_checked="selected";
	                  else $webviews_checked = "";

	                  $elements['html2'] .="<option value='trigger_webview_{$webviews['canonical_id']}' {$webviews_checked}>".$webviews['form_name']." [".$webviews['canonical_id']."]"."</option>";
	                }

	              } else
	              {
	                $elements['html2'] .='<span class="red">No Webview Record Found for this page.</span>';
	              }

	  $elements['html2'] .='</select></div><script>$("#webview_updated").select2();</script>';

  	 	if($this->is_input_flow_addon_exists) {
  		   	if($this->basic->is_exist("modules",array("id"=>292))) {
	  	   		if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access)) {

			  	$elements['html3'] = '<div class="form-group">
			              <label>'.$this->lang->line("Choose User Input Flow Campaign").'</label>
			              <select multiple name="input_flow[]" id="input_flow_updated" class="form-control select2" style="width:100%;">';
			              if(!empty($user_input_flow_campaigns))
			              { 
			                foreach ($user_input_flow_campaigns as $flow_campaign) 
			                {
			                  if(in_array("trigger_userinput_".$flow_campaign['id'],$triggered_postback_webview_val)) $input_flow_checked="selected";
			                  else $input_flow_checked = "";

			                  $elements['html3'] .="<option value='trigger_userinput_{$flow_campaign['id']}' {$input_flow_checked}>".$flow_campaign['flow_name']."</option>";
			                }

			              } else
			              {
			                $elements['html3'] .='<span class="red">'.$this->lang->line('No Input Flow Campaign Record Found for this page.').'</span>';
			              }

			  	$elements['html3'] .='</select></div><script>$("#input_flow_updated").select2();</script>';
			  	}
			}
		}
	            
	  echo json_encode($elements);
	}


	public function ajax_connector_info_updating()
	{
	  if($this->session->userdata('user_type') != 'Admin' && !in_array(258,$this->module_access))  exit();
	  $this->ajax_check();
	  $post_data = $_POST;

	  foreach ($post_data as $key => $value) { $$key = $value; }

	  $update_data = array();
	  $table_id                     = $table_id;
	  $update_data['name']          = $connector_name;
	  $update_data['user_id']       = $this->user_id;

	  $page_name                    = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('user_id'=>$this->user_id,'id'=>$updated_page_table_id,'bot_enabled'=>'1')),array('page_name','page_id'));

	  $update_data['page_name']     = $page_name[0]['page_name'];
	  $update_data['page_id']       = $page_name[0]['page_id'];
	  $update_data['webhook_url']   = $updated_webhook_url;
	  $update_data['variable_post'] = implode(',',$updated_variable_post);

	  if(in_array("trigger_postbackid",$updated_field))
	  {
	    $find_trigger_postback = array_search('trigger_postbackid',$updated_field);
	    unset($updated_field[$find_trigger_postback]);
	  
	    if(!empty($postback))
	    {
	      foreach ($postback as $single_postback) 
	      {
	        array_push($updated_field,$single_postback);
	      }
	    }
	  }


	  if(in_array("trigger_webview",$updated_field))
	  {
	    $find_trigger_webview = array_search('trigger_webview',$updated_field);
	    unset($updated_field[$find_trigger_webview]);
	  
	    if(!empty($webview))
	    {
	      foreach ($webview as $single_webview) 
	      {
	        array_push($updated_field,$single_webview);
	      }
	    }
	  }


	  if(in_array("trigger_user_input",$updated_field))
	  {
	    $find_trigger_user_input = array_search('trigger_user_input',$updated_field);
	    unset($updated_field[$find_trigger_user_input]);
	  
	    if(!empty($input_flow))
	    {
	      foreach ($input_flow as $single_flow) 
	      {
	        array_push($updated_field,$single_flow);
	      }
	    }
	  }


	  $updated_table_name  = 'messenger_bot_thirdparty_webhook';
	  $update_data_where   = array('id'=>$table_id);

	  $success = array();

	  if($this->basic->update_data($updated_table_name,$update_data_where,$update_data))
	  {
	    $update_trigger_data = array();

	    $get_triggered_data = $this->basic->delete_data('messenger_bot_thirdparty_webhook_trigger',array('webhook_id'=>$table_id));

	    foreach ($updated_field as $single_field) 
	    {
	      $trigger_table = 'messenger_bot_thirdparty_webhook_trigger';
	      $update_where  = array('webhook_id' => $table_id);
	      $update_trigger_data['webhook_id']  = $table_id;
	      $update_trigger_data['trigger_option'] = $single_field;
	      $this->basic->insert_data('messenger_bot_thirdparty_webhook_trigger',$update_trigger_data);
	    }

	    $success['result'] = 1;
	    $success['msg']    = $this->lang->line("Connection has been updated successfully.");

	  } else
	  {
	    $success['result'] = 0;
	    $success['msg']    = $this->lang->line("Something went wrong,please try again.");
	  }

	  echo json_encode($success);    
	}


	public function ajax_delete_connector_info()
	{
	  if($this->session->userdata('user_type') != 'Admin' && !in_array(258,$this->module_access))  exit();
	  $this->ajax_check();
	  $table_id = $this->input->post("table_id",true);

	  if($this->basic->delete_data('messenger_bot_thirdparty_webhook',array("id"=>$table_id)) && $this->basic->delete_data('messenger_bot_thirdparty_webhook_trigger',array("webhook_id"=>$table_id)))
	  {
	      echo "1";

	  } else
	  {
	      echo "0";
	  }
	}
	/*===============================================
	JSON API CONNECTOR
	***********************************************
	*/



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
            CREATE TABLE IF NOT EXISTS `messenger_bot_thirdparty_webhook` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(100) NOT NULL,
              `user_id` int(11) NOT NULL,
              `page_id` varchar(50) NOT NULL,
              `page_name` varchar(250) NOT NULL,
              `webhook_url` text NOT NULL,
              `variable_post` text NOT NULL,
              `added_date` datetime NOT NULL,
              `last_trigger_time` datetime NOT NULL,
              PRIMARY KEY (`id`),
              KEY `xuser_id_page_id` (`user_id`,`page_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            2=>"
            CREATE TABLE IF NOT EXISTS `messenger_bot_thirdparty_webhook_activity` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `http_code` varchar(10) NOT NULL,
              `webhook_id` int(11) NOT NULL,
              `curl_error` varchar(250) NOT NULL,
              `post_time` datetime NOT NULL,
              `post_data` text NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            3=>"
            CREATE TABLE IF NOT EXISTS `messenger_bot_thirdparty_webhook_trigger` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `webhook_id` int(11) NOT NULL,
              `trigger_option` varchar(50) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `xwebhook_trigger` (`webhook_id`,`trigger_option`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            4=>"
            CREATE TABLE IF NOT EXISTS `messenger_bot_user_custom_form_webview_data` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `page_id` int(11) NOT NULL COMMENT 'page_table_id',
              `subscriber_id` varchar(25) NOT NULL,
              `web_view_form_canonical_id` varchar(50) NOT NULL,
              `data` longtext NOT NULL,
              `inserted_at` datetime NOT NULL,
              PRIMARY KEY (`id`),
              KEY `FK_mbucfwd_web_view_form_canonical_id` (`web_view_form_canonical_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            5=>"
            CREATE TABLE IF NOT EXISTS `webview_builder` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `canonical_id` varchar(45) NOT NULL,
              `user_id` int(11) NOT NULL,
              `page_id` int(11) DEFAULT NULL,
              `assign_label` varchar(11) DEFAULT NULL,
              `reply_template` int(11) DEFAULT NULL,
              `form_name` varchar(200) NOT NULL COMMENT 'This is actually form name for identify in our system',
              `form_title` varchar(200) NOT NULL COMMENT 'The form title that will be shown on top of your form',
              `form_data` text,
              `deleted` enum('0','1') DEFAULT '0',
              `inserted_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `user_id_key` (`user_id`),
              KEY `canonical_id` (`canonical_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            6=>"
            ALTER TABLE `messenger_bot_user_custom_form_webview_data`
              ADD CONSTRAINT `FK_mbucfwd_web_view_form_canonical_id` FOREIGN KEY (`web_view_form_canonical_id`) REFERENCES `webview_builder` (`canonical_id`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `messenger_bot_user_custom_form_webview_data_ibfk_1` FOREIGN KEY (`web_view_form_canonical_id`) REFERENCES `webview_builder` (`canonical_id`);",

            7=>"
            ALTER TABLE `webview_builder`
              ADD CONSTRAINT `user_id_key` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);",

            8 => "ALTER TABLE `webview_builder` CHANGE `assign_label` `assign_label` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;",
            9 => "ALTER TABLE `messenger_bot_thirdparty_webhook` ADD INDEX `xuser_id_page_id` (`user_id`, `page_id`);",
            10 => "ALTER TABLE `messenger_bot_thirdparty_webhook_trigger` ADD INDEX `xwebhook_trigger` (`webhook_id`, `trigger_option`);",
            11=>"ALTER TABLE `webview_builder` CHANGE `form_data` `form_data` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;"

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
          1=> "DROP TABLE IF EXISTS `messenger_bot_thirdparty_webhook`;",
          2=> "DROP TABLE IF EXISTS `messenger_bot_thirdparty_webhook_activity`;",
          3=> "DROP TABLE IF EXISTS `messenger_bot_thirdparty_webhook_trigger`;",
          4=> "DROP TABLE IF EXISTS `messenger_bot_user_custom_form_webview_data`;",
          5=> "DROP TABLE IF EXISTS `webview_builder`;"
        );  
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }


}
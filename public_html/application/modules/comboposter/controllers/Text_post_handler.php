<?php 

/**
 * 		
 */
class Text_post_handler
{
	private $comboposter;
	
	function __construct($comboposter_handler)
	{
		$this->comboposter = $comboposter_handler;
	}


	public function create()
	{
		/* get each social accounts list */
		$data['facebook_account_list'] = $this->comboposter->getUserAccountsList('facebook', $this->comboposter->user_id);
		$data['twitter_account_list'] = $this->comboposter->getUserAccountsList('twitter', $this->comboposter->user_id);
		$data['linkedin_account_list'] = $this->comboposter->getUserAccountsList('linkedin', $this->comboposter->user_id);
		// $data['tumblr_account_list'] = $this->comboposter->getUserAccountsList('tumblr', $this->comboposter->user_id);

		$data['medium_account_list'] = $this->comboposter->getUserAccountsList('medium', $this->comboposter->user_id);

		$data['reddit_account_list'] = $this->comboposter->getUserAccountsList('reddit', $this->comboposter->user_id);
		$data['subreddits'] = $data['reddit_account_list']['subreddits'];
		unset($data['reddit_account_list']['subreddits']);


		// $data['youtube_account_list'] = $this->comboposter->getUserAccountsList('youtube', $this->comboposter->user_id);
		// $data['pinterest_account_list'] = $this->comboposter->getUserAccountsList('pinterest', $this->comboposter->user_id);
		// $data['blogger_account_list'] = $this->comboposter->getUserAccountsList('blogger', $this->comboposter->user_id);
		// $data['wordpress_account_list'] = $this->comboposter->getUserAccountsList('wordpress', $this->comboposter->user_id);
		$data['wordpress_account_list_self_hosted'] = $this->comboposter->getUserAccountsList('wordpress_self_hosted', $this->comboposter->user_id);

		// echo "<pre>";print_r($data['blogger_account_list']);exit;

		$data['post_type'] = 'text';
		$data['post_action'] = 'add';
		$data["time_zone"] = $this->comboposter->_time_zone_list();
		$data["time_interval"] = $this->comboposter->get_periodic_time();

		$data['page_title'] = $this->comboposter->lang->line('Text post');
		$data['title'] = $this->comboposter->lang->line('Text post');
		$data['body'] = 'posts/text';

		$this->comboposter->_viewcontroller($data);
	}



	public function edit($table_id)
	{

		/* get campaign info */
		$campaign_info = $this->comboposter->basic->get_data('comboposter_campaigns', array('where' => array('user_id' => $this->comboposter->user_id, 'id' => $table_id)));

		if (count($campaign_info) == 0) {
			redirect(base_url('404'),'refresh');
		}

		$data['campaigns_social_media'] = json_decode($campaign_info[0]['posting_medium'], true);
		$data['wpsh_selected_category'] = json_decode($campaign_info[0]['wpsh_selected_category'], true);
		unset($campaign_info[0]['posting_medium']);
		$data['campaign_form_info'] = $campaign_info[0];

		/* get each social accounts list */
		$data['facebook_account_list'] = $this->comboposter->getUserAccountsList('facebook', $this->comboposter->user_id);
		$data['twitter_account_list'] = $this->comboposter->getUserAccountsList('twitter', $this->comboposter->user_id);
		// $data['tumblr_account_list'] = $this->comboposter->getUserAccountsList('tumblr', $this->comboposter->user_id);
		$data['linkedin_account_list'] = $this->comboposter->getUserAccountsList('linkedin', $this->comboposter->user_id);

		$data['medium_account_list'] = $this->comboposter->getUserAccountsList('medium', $this->comboposter->user_id);

		$data['reddit_account_list'] = $this->comboposter->getUserAccountsList('reddit', $this->comboposter->user_id);
		$data['subreddits'] = $data['reddit_account_list']['subreddits'];
		unset($data['reddit_account_list']['subreddits']);


		$data['youtube_account_list'] = $this->comboposter->getUserAccountsList('youtube', $this->comboposter->user_id);
		$data['pinterest_account_list'] = $this->comboposter->getUserAccountsList('pinterest', $this->comboposter->user_id);
		$data['blogger_account_list'] = $this->comboposter->getUserAccountsList('blogger', $this->comboposter->user_id);
		$data['wordpress_account_list'] = $this->comboposter->getUserAccountsList('wordpress', $this->comboposter->user_id);
		$data['wordpress_account_list_self_hosted'] = $this->comboposter->getUserAccountsList('wordpress_self_hosted', $this->comboposter->user_id);

		// echo "<pre>";print_r($data['blogger_account_list']);exit;

		$data['post_type'] = 'text';
		$data['post_action'] = 'edit';
		$data["time_zone"] = $this->comboposter->_time_zone_list();
		$data["time_interval"] = $this->comboposter->get_periodic_time();

		$data['page_title'] = $this->comboposter->lang->line('Text post');
		$data['title'] = $this->comboposter->lang->line('Text post');
		$data['body'] = 'posts/text';

		$this->comboposter->_viewcontroller($data);
	}



	public function clone_campaign($table_id)
	{

		/* get campaign info */
		$campaign_info = $this->comboposter->basic->get_data('comboposter_campaigns', array('where' => array('user_id' => $this->comboposter->user_id, 'id' => $table_id)));

		if (count($campaign_info) == 0) {
			redirect(base_url('404'),'refresh');
		}

		$data['campaigns_social_media'] = json_decode($campaign_info[0]['posting_medium'], true);
		$data['wpsh_selected_category'] = json_decode($campaign_info[0]['wpsh_selected_category'], true);
		unset($campaign_info[0]['posting_medium']);
		$data['campaign_form_info'] = $campaign_info[0];


		


		/* get each social accounts list */
		$data['facebook_account_list'] = $this->comboposter->getUserAccountsList('facebook', $this->comboposter->user_id);
		$data['twitter_account_list'] = $this->comboposter->getUserAccountsList('twitter', $this->comboposter->user_id);
		$data['linkedin_account_list'] = $this->comboposter->getUserAccountsList('linkedin', $this->comboposter->user_id);

		$data['medium_account_list'] = $this->comboposter->getUserAccountsList('medium', $this->comboposter->user_id);

		$data['reddit_account_list'] = $this->comboposter->getUserAccountsList('reddit', $this->comboposter->user_id);
		$data['subreddits'] = $data['reddit_account_list']['subreddits'];
		unset($data['reddit_account_list']['subreddits']);


		$data['youtube_account_list'] = $this->comboposter->getUserAccountsList('youtube', $this->comboposter->user_id);
		$data['pinterest_account_list'] = $this->comboposter->getUserAccountsList('pinterest', $this->comboposter->user_id);
		$data['blogger_account_list'] = $this->comboposter->getUserAccountsList('blogger', $this->comboposter->user_id);
		$data['wordpress_account_list'] = $this->comboposter->getUserAccountsList('wordpress', $this->comboposter->user_id);
		$data['wordpress_account_list_self_hosted'] = $this->comboposter->getUserAccountsList('wordpress_self_hosted', $this->comboposter->user_id);
		

		// echo "<pre>";print_r($data['blogger_account_list']);exit;

		$data['post_type'] = 'text';
		$data['post_action'] = 'clone';
		$data["time_zone"] = $this->comboposter->_time_zone_list();
		$data["time_interval"] = $this->comboposter->get_periodic_time();

		$data['page_title'] = $this->comboposter->lang->line('Text post');
		$data['title'] = $this->comboposter->lang->line('Text post');
		$data['body'] = 'posts/text';

		$this->comboposter->_viewcontroller($data);
	}


	public function add()
	{

		$processed_input_data = $this->prepare_input_data();

		if (!is_array($processed_input_data)) {
			echo $processed_input_data;
		} else {

			$response = array();

			$posting_mediums_count = $processed_input_data['posting_mediums_count'];
			unset($processed_input_data['posting_mediums_count']);

			// ************************************************//
			$status = $this->comboposter->_check_usage($module_id = 110, $request = $posting_mediums_count);
			if ($status == "2") {

			    $response['status'] = 'error';
			    $response['message'] = $this->comboposter->lang->line("Sorry, your posting bulk limit has exceed.");
			    echo json_encode($response);
			    exit();
			} else if ($status == "3") {

			    $response['status'] = 'error';
			    $response['message'] = $this->comboposter->lang->line("Sorry, your monthly posting limit has exceed.");
			    echo json_encode($response);
			    exit();
			}
			// ************************************************//
			
			/* check if has multiple time posting */
			if ($processed_input_data['schedule_type'] == 'later' 
				&& ($processed_input_data['repeat_times'] != '' || $processed_input_data['repeat_times'] >= 0)) {
				
				$repeat_times = $processed_input_data['repeat_times'];
				$time_interval = $processed_input_data['time_interval'];

				/* insert parent campaign */
				if ($processed_input_data['repeat_times'] > 0) {
					$processed_input_data['parent_campaign_id'] = 0;
				}
				$this->comboposter->basic->insert_data('comboposter_campaigns', $processed_input_data);

				/* get parent id for child campaigns */
				$parent_campaign = $this->comboposter->basic->get_data('comboposter_campaigns', array('where' => array('user_id' => $this->comboposter->user_id, 'campaign_name' => $processed_input_data['campaign_name'], 'schedule_time' => $processed_input_data['schedule_time'])), array('id'));
				$parent_id = $parent_campaign[0]['id'];

				/* insert child campaigns */
				$processed_input_data['is_child'] = '1';
				$processed_input_data['parent_campaign_id'] = $parent_id;


				unset($processed_input_data['repeat_times']);
				unset($processed_input_data['time_interval']);

				$temp_time_interval = 0;
				$cash_schedule_time = $processed_input_data['schedule_time'];
				
				for ($i=0; $i < $repeat_times; $i++) { 

					$temp_time_interval += $time_interval;
					$next_posting_time = Date('Y-m-d H:i:s', strtotime($cash_schedule_time. " + {$temp_time_interval} minute"));

					$processed_input_data['schedule_time'] = $next_posting_time;
					$this->comboposter->basic->insert_data('comboposter_campaigns', $processed_input_data);
				}

				$this->comboposter->_insert_usage_log($module_id = 110, $request = $posting_mediums_count * $repeat_times);
			} else {

				$this->comboposter->basic->insert_data('comboposter_campaigns', $processed_input_data);
				$this->comboposter->_insert_usage_log($module_id = 110, $request = $posting_mediums_count);
			}


			if ($this->comboposter->db->affected_rows() > 0) {

				$response['status'] = 'success';

				if ($processed_input_data['schedule_type'] == 'now') {

					$table_id = $this->comboposter->db->insert_id();
					$this->comboposter->single_campaign_post_to_all_media($table_id);
					$response['message'] = $this->comboposter->lang->line("Campaign created & posted successfully.");
				} else {

					$response['message'] = $this->comboposter->lang->line("Campaign created successfully.");
				}


				echo json_encode($response);
			} else {

				$response['status'] = 'error';
				$response['message'] = $this->comboposter->lang->line("Something went wrong.");

				echo json_encode($response);
			}
		}
	}


	public function edit_action()
	{
		$processed_input_data = $this->prepare_input_data();

		$table_id = $this->comboposter->input->post('table_id', true);

		if (!is_array($processed_input_data)) {
			echo $processed_input_data;
		} else {

			$response = array();

			$campaign_info = $this->comboposter->basic->get_data('comboposter_campaigns', array('where' => array('user_id' => $this->comboposter->user_id, 'id' => $table_id, 'posting_status' => 'pending')), array('posting_medium'));

			if (count($campaign_info) > 0) {

				/* remove usage log  */
				$posting_mediums_count = json_decode($campaign_info[0]['posting_medium'], true);
				$posting_mediums_count = count($posting_mediums_count);
				$this->comboposter->_delete_usage_log($module_id = 110, $request = $posting_mediums_count);


				/* add usage log & update */
				$posting_mediums_count = $processed_input_data['posting_mediums_count'];
				unset($processed_input_data['posting_mediums_count']);

				$this->comboposter->basic->update_data('comboposter_campaigns', array('user_id' => $this->comboposter->user_id, 'id' => $table_id), $processed_input_data);
				$this->comboposter->_insert_usage_log($module_id = 110, $request = $posting_mediums_count);


				$response['status'] = 'success';
				$response['message'] = $this->comboposter->lang->line("Campaign edited successfully.");
			} else {

				$response['status'] = 'error';
				$response['message'] = $this->comboposter->lang->line("Something went wrong.");
			}


			echo json_encode($response);
			
		}
	}


	public function prepare_input_data()
	{
		$response = array();

		/* get form inputs */
		$data['user_id'] = $this->comboposter->user_id;

		$data['campaign_type'] = 'text';
		$data['campaign_name'] = strip_tags($this->comboposter->input->post('campaign_name', true));
		$data['message'] = $this->comboposter->input->post('message', true);
		if ($data['campaign_name'] == '' || $data['message'] == '') {

			$response['status'] = 'error';
			$response['message'] = $this->comboposter->lang->line("Campaign name / Message cann't be empty.");
			return json_encode($response);
		}

		$data['title'] = strip_tags($this->comboposter->input->post('title', true));

		$data['schedule_type'] = $this->comboposter->input->post('schedule_type', true);
		if ($data['schedule_type'] == '') {

			$data['schedule_type'] = 'later';

			$data['repeat_times'] = $this->comboposter->input->post('repeat_times', true);
			$data['time_interval'] = $this->comboposter->input->post('time_interval', true);

			if ($data['repeat_times'] > 0 && $data['time_interval'] == '') {

				$response['status'] = 'error';
				$response['message'] = $this->comboposter->lang->line("Repeat times & Time interval cann't be empty at the same time.");
				return json_encode($response);
			}
		} 

		$data['schedule_timezone'] = $this->comboposter->input->post('time_zone', true);
		$data['schedule_time'] = $this->comboposter->input->post('schedule_time', true);
		if ($data['schedule_type'] == 'now') {
			$data['schedule_time'] = date("Y-m-d h:i:s");
		}


		/* get social media info */
		$facebook_pages = $this->comboposter->input->post('facebook_pages', true);
		$twitter_accounts = $this->comboposter->input->post('twitter_accounts', true);
		$medium_accounts = $this->comboposter->input->post('medium_accounts', true);
		// $tumblr_accounts = $this->comboposter->input->post('tumblr_accounts', true);
		$linkedin_accounts = $this->comboposter->input->post('linkedin_accounts', true);
		$reddit_accounts = $this->comboposter->input->post('reddit_accounts', true);
		$data['subreddits'] = $subreddits = $this->comboposter->input->post('subreddits', true);
		$wordpress_accounts_self_hosted = $this->comboposter->input->post('wordpress_accounts_self_hosted', true);
		$wpsh_selected_category = $this->comboposter->input->post('wpsh_selected_category', true);

		/* ensure that they are array */
		if (!is_array($facebook_pages)) {
			$facebook_pages = array();
		}
		if (!is_array($twitter_accounts)) {
			$twitter_accounts = array();
		}
		if (!is_array($linkedin_accounts)) {
			$linkedin_accounts = array();
		}
		if (!is_array($wordpress_accounts_self_hosted)) {
			$wordpress_accounts_self_hosted = array();
			$wpsh_selected_category = array();
		}
		if (!is_array($reddit_accounts)) {
			$reddit_accounts = array();
		}
		if (!is_array($medium_accounts)) {
			$medium_accounts = array();
		}


		/* check if is empty */
		if (count($facebook_pages) == 0 
			&& count($twitter_accounts) == 0 
			&& count($linkedin_accounts) == 0 
			&& count($wordpress_accounts_self_hosted) == 0 
			&& count($reddit_accounts) == 0
			&& count($medium_accounts) == 0) {

			$response['status'] = 'error';
			$response['message'] = $this->comboposter->lang->line("Please make sure that at least one social media is selected.");

			return json_encode($response);
		}

		if (count($reddit_accounts) > 0 && $subreddits == '0') {

			$response['status'] = 'error';
			$response['message'] = $this->comboposter->lang->line("Please make sure that a subreddit is selected.");

			return json_encode($response);
		}

		if (count($wordpress_accounts_self_hosted) > 0 && ! count($wpsh_selected_category) > 0) {
			$response['status'] = 'error';
			$response['message'] = $this->comboposter->lang->line("Please make sure that at least one blog category is selected.");

			return json_encode($response);			
		}		

		/* get all social media in an array and process it */
		$posting_mediums = array();

		array_push($posting_mediums, $facebook_pages);
		array_push($posting_mediums, $twitter_accounts);
		// array_push($posting_mediums, $tumblr_accounts);
		array_push($posting_mediums, $linkedin_accounts);
		array_push($posting_mediums, $reddit_accounts);
		array_push($posting_mediums, $wordpress_accounts_self_hosted);
		array_push($posting_mediums, $medium_accounts);

		$posting_mediums = array_filter($posting_mediums, function ($element) {
			return count($element) > 0 ? true : false;
		});

		$posting_mediums = $this->comboposter->mutiArrToSingleArr($posting_mediums);
		$data['wpsh_selected_category'] = json_encode($wpsh_selected_category);
		$data['posting_mediums_count'] = count($posting_mediums);
		$data['posting_medium'] = json_encode($posting_mediums);

		return $data;
	}
}
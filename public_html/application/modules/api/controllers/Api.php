<?php
/*
Addon Name: XeroChat API Documentation
Unique Name: api_documentation
Modules:
{
   "285":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"0",
      "extra_text":"month",
      "module_name":"API Documentation"
   },
}
Project ID: 46
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 2.0
Description: Describes how the API for this application works
*/

require_once("application/controllers/Home.php"); // loading home controller

class Api extends Home
{
    public function __construct()
    {
        parent::__construct();   
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
        $sql = array(); 
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }


    public function api_key_check($api_key="",$is_admin=0)
    {
        if (! headers_sent()) {
            header('Content-Type: application/json');
        }

        $user_id="";
        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }

        if($api_key=="")
        {        
           $response['status']='error';
           $response['message'] = 'API Key is required';
           return $response;
        }

        $join=array('native_api'=>"native_api.user_id=users.id,left");
        $where['where']=array("users.id"=>$user_id,"api_key"=>$api_key,"users.status"=>"1");
        if($is_admin)
            $where['where']['user_type']="Admin";
        $user_info= $this->basic->get_data("users",$where,"",$join);

        if(count($user_info)==1){
            $response['status']='success';  
            $response['user_id']=$user_id;  
            return $response;
        }
        else{

            $response['status']='error';
            $response['message']='Either API Key Invalid or Member Validity Expired';
            return $response;
        }
    } 

    public function doc() 
    {
        $data['page_title'] = $this->lang->line('API Documentation');
        $data['title'] = $this->lang->line('API Documentation');
        $data['product_name'] = $this->config->item('product_name');
        $data['endpoint'] = base_url('/api');

        $this->load->view('api/doc', $data);
    }

    public function create_system_user(){

    	$api_key=$this->input->post('api_key');
        $api_key_response= $this->api_key_check($api_key,"1");

        if($api_key_response['status']!="success"){
            echo json_encode($api_key_response);
            exit; 
        }

        $name=$this->input->post('name');
        if(!$name) $name="";
        $email=$this->input->post('email');
        if(!$email) $email="";
        $mobile=$this->input->post('mobile');
        if(!$mobile) $mobile="";
        $password=md5($this->input->post('password'));
        if(!$password) $password="";
        $address=$this->input->post('address');
        if(!$address) $address="";

        $user_type="Member";
        $status="1";
        $package_id=$this->input->post('package_id');
        if(!$package_id) $package_id="";

        $expired_date=$this->input->post('expired_date');
        if(!$expired_date) $expired_date="";
                                               
        $data=array
        (
            'name'=>$name,
            'email'=>$email,
            'mobile'=>$mobile,
            'password'=>$password,
            'address'=>$address,
            'user_type'=>$user_type,
            'status'=>$status,
            'add_date' => date("Y-m-d H:i:s")
        );
        $data["package_id"] = $package_id;
        $data["expired_date"] = $expired_date;

        if($name=="" || $email=="" || $password=="" || $package_id=="" || $expired_date==""){

            $response['status']='error';
            $response['message']='All mandatory field is not provided';
            echo json_encode($response);
            exit;

        }

        if(!$this->basic->is_exist("package",array("id"=>$package_id))){
            $response['status']='error';
            $response['message']='Package ID not found';
            echo json_encode($response);
            exit;
        }

        if($this->basic->is_exist("users",array("email"=>$email))){
            $response['status']='error';
            $response['message']='Email already exists';
            echo json_encode($response);
            exit;
        }

        $this->basic->insert_data('users',$data);
        $new_user_id=$this->db->insert_id();
        if($new_user_id){

            $response['status']='success';
            $response['id']=$new_user_id;
            echo json_encode($response);
            exit;
        }

    }


    public function update_user($user_id=""){

        if($user_id==""){
            $response['status']='error';
            $response['message']="Provide User ID with End Point URL";
            echo json_encode($response);
            exit;
        }

        $api_key=$this->input->post('api_key');
        $api_key_response= $this->api_key_check($api_key,"1");

        if($api_key_response['status']!="success"){
            echo json_encode($api_key_response);
            exit; 
        }

        $update_info=array();

        $name=$this->input->post('name');
        if($name) $update_info["name"]=$name;
        
        $mobile=$this->input->post('mobile');
        if($mobile)    $update_info["mobile"]=$mobile;
        

        $address=$this->input->post('address');
        if($address)   $update_info["address"]=$address;
       
        $status=$this->input->post('status');
        if($status)  $update_info["status"]=$status;

        $package_id=$this->input->post('package_id');
        if($package_id)  $update_info["package_id"]=$package_id;

        $expired_date=$this->input->post('expired_date');
        if($expired_date) $update_info["expired_date"]=$expired_date;


       if(!$this->basic->is_exist("users",array("id"=>$user_id))){
            $response['status']='error';
            $response['message']="User not found with the ID: {$user_id}";
            echo json_encode($response);
            exit;
        }

        $this->basic->update_data("users",array("id"=>$user_id),$update_info);

        $response['status']='success';
        echo json_encode($response);
        exit;
    }


    public function get_user_info(){

        $api_key=$this->input->get('api_key');
        $api_key_response= $this->api_key_check($api_key,"1");

        if($api_key_response['status']!="success"){
            echo json_encode($api_key_response);
            exit; 
        }
        
        $user_id=$this->input->get('user_id');
        $email = $this->input->get('email');
        if($email) $email=urldecode($email);

        if(!$user_id && !$email){
            $response['status']='error';
            $response['message']="Provide either user ID or Email to filter";
            echo json_encode($response);
            exit;
        }

        if($user_id)
            $where['where']['id']=$user_id;
        if($email)
            $where['where']['email']=$email;

        $select="id,name,email,mobile,address,user_type,status,add_date,last_login_at,expired_date,package_id,last_login_ip";
        $user_info= $this->basic->get_data("users",$where,$select);

        if(!isset($user_info[0])){

            $response['status']='error';
            $response['message']="No Matching User Found";
            echo json_encode($response);
            exit;
        }

        $response['status']='success';
        $response['user_info']=$user_info[0];
        $response=json_encode($response);
        echo $response;

    }


    public function get_all_packages(){

        $api_key=$this->input->get('api_key');
        $api_key_response= $this->api_key_check($api_key,"1");

        if($api_key_response['status']!="success"){
            echo json_encode($api_key_response);
            exit; 
        }

        $select="id,package_name,module_ids,price,validity,deleted,visible,highlight";
        $packages= $this->basic->get_data("package",'',$select);

        $response['status']='success';
        $response['packages']=$packages;
        $response=json_encode($response);
        echo $response;

    }

    // Member level funciton 

    public function subscriber_information(){


        $api_key=$this->input->get('api_key');
        $api_key_response= $this->api_key_check($api_key);

        if($api_key_response['status']!="success"){
            echo json_encode($api_key_response);
            exit; 
        }

        $user_id=$api_key_response['user_id']; 
        $subscriber_id=$this->input->get('subscriber_id');

        if(!$subscriber_id){
            $response['status']='error';
            $response['message']="Provide subscriber_id field";
            echo json_encode($response);
            exit;
        }

        if($subscriber_id)
            $where['where']['subscribe_id']=$subscriber_id;
            $where['where']['messenger_bot_subscriber.user_id']=$user_id;

        $select="subscribe_id as subscriber_id,contact_group_id as labels,first_name,last_name,full_name,gender,locale,timezone,page_name,messenger_bot_subscriber.page_id,unavailable,last_error_message,refferer_source,subscribed_at,email,phone_number,birthdate,last_subscriber_interaction_time,";
        $join=array("facebook_rx_fb_page_info"=>"facebook_rx_fb_page_info.id=messenger_bot_subscriber.page_table_id,left");

        $subscriber_info= $this->basic->get_data("messenger_bot_subscriber",$where,$select,$join);

        if(!isset($subscriber_info[0])){

            $response['status']='error';
            $response['message']="No Matching Subscriber Found";
            echo json_encode($response);
            exit;
        }

        $response['status']='success';
        $response['subscriber_info']=$subscriber_info[0];
        $response=json_encode($response);
        echo $response;

    }



    public function get_all_labels(){

    	$api_key=$this->input->get('api_key');
        $api_key_response= $this->api_key_check($api_key);

        if($api_key_response['status']!="success"){
            echo json_encode($api_key_response);
            exit; 
        }

        $user_id=$api_key_response['user_id']; 
        $page_id=$this->input->get('page_id');

        if(!$page_id){
            $response['status']='error';
            $response['message']="Provide page_id field";
            echo json_encode($response);
            exit;
        }

     
        $where['where']=array("messenger_bot_broadcast_contact_group.user_id"=>$user_id,"facebook_rx_fb_page_info.page_id"=>$page_id,'bot_enabled'=>"1");
        $select="label_id,group_name as label_name";
        $join=array("facebook_rx_fb_page_info"=>"facebook_rx_fb_page_info.id=messenger_bot_broadcast_contact_group.page_id,left");

        $label_info= $this->basic->get_data("messenger_bot_broadcast_contact_group",$where,$select,$join);

        if(!isset($label_info[0])){

            $response['status']='error';
            $response['message']="No Matching Label Found";
            echo json_encode($response);
            exit;
        }

        $response['status']='success';
        $response['label_info']=$label_info;
        $response=json_encode($response);
        echo $response;

    }



    public function create_label(){

     	$api_key=$this->input->post('api_key',true);
     	$label_name=$this->input->post('label_name',true);
        $page_id=$this->input->post('page_id',true);

        $api_key_response= $this->api_key_check($api_key);

        if($api_key_response['status']!="success"){
            echo json_encode($api_key_response);
            exit; 
        }

        $user_id=$api_key_response['user_id']; 

        if(!$page_id){
            $response['status']='error';
            $response['message']="Provide page_id field";
            echo json_encode($response);
            exit;
        }

        else if(!$label_name){

        	$response['status']='error';
            $response['message']="Provide label_name field";
            echo json_encode($response);
            exit;
        }


      $getdata = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("page_id"=>$page_id,"user_id"=>$user_id,"bot_enabled"=>"1")));
      $page_access_token = isset($getdata[0]['page_access_token'])?$getdata[0]['page_access_token']:"";

      if($page_access_token==""){
      	$response['status']='error';
        $response['message']="No Enabled Bot Found";
        echo json_encode($response);
        exit;
      }

      $this->load->library('fb_rx_login');

      $response = $this->fb_rx_login->create_label($page_access_token,$label_name);
      if(isset($response['id']) && !empty($response['id']))
      { 
        $inserted_data = array(
          'user_id'=> $user_id,
          'group_name'=> $label_name,
          'label_id'=> $response['id'],
          'page_id'=> $page_id
        ); 

        if($this->basic->insert_data("messenger_bot_broadcast_contact_group",$inserted_data))
        {
          $return['status'] = "success";
          $return['label_id'] = $response['id'];
        }
        
      }
      if(isset($response['error']))
      {
        $return['status'] = "error";
        $return['message'] = $response['error'];
      }

      echo json_encode($return);
    }



    public function assign_label(){
    	$api_key=$this->input->post('api_key',true);
    	$psid=$this->input->post('subscriber_id',true);
        $fb_page_id=$this->input->post('page_id',true);
        $label_ids=$this->input->post('label_ids',true);
        $label_ids=explode(",",$label_ids);

        $api_key_response= $this->api_key_check($api_key);

        if($api_key_response['status']!="success"){
            echo json_encode($api_key_response);
            exit; 
        }

        $user_id=$api_key_response['user_id']; 


        $pageinfo=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("page_id"=>$fb_page_id,"user_id"=>$user_id,"bot_enabled"=>"1")));
        $page_auto_id=isset($pageinfo[0]["id"])?$pageinfo[0]["id"]:"";
        $page_access_token=isset($pageinfo[0]["page_access_token"])?$pageinfo[0]["page_access_token"]:"";

       	if($page_access_token==""){
	      	$response['status']='error';
	        $response['message']="No Enabled Bot Found";
	        echo json_encode($response);
	        exit;
        }

      	$label_info=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where_in"=>array("label_id"=>$label_ids)));

      	if(empty($label_info)){

      		$response['status']='error';
	        $response['message']="No Label Found";
	        echo json_encode($response);
	        exit;
      	}
        
        $this->load->library('fb_rx_login');
        
        foreach($label_info as $value){

            $label_auto_id=isset($value['id'])?$value['id']:0;
            $label_id=isset($value['label_id'])?$value['label_id']:"";
          
            $response= $this->fb_rx_login->assign_label($page_access_token,$psid,$label_id);

            if(isset($response['error'])){

			    $return['status'] = "error";
			    $return['message'] = $response['error']['message'];
			    echo json_encode($return);
			    exit; 
			}

            $subscriberdata=$this->basic->get_data("messenger_bot_subscriber",array("where"=>array("subscribe_id"=>$psid,"page_id"=>$fb_page_id)));

            $contact_group_id=isset($subscriberdata[0]["contact_group_id"])?$subscriberdata[0]["contact_group_id"]:"";
            $explode=explode(',', $contact_group_id);
            array_push($explode, $label_auto_id);
            $new=array_unique($explode);
            $contact_group_id=implode(',', $new);
            $contact_group_id=trim($contact_group_id,',');

            $this->basic->update_data("messenger_bot_subscriber",array("subscribe_id"=>$psid,"page_id"=>$fb_page_id),array("contact_group_id"=>$contact_group_id));

        }


    	$response['status']='success';
        $response=json_encode($response);
    	echo $response;

    }

    public function get_contact_group() 
    {
        $api_key = $this->input->get('api_key',true);
        $api_key_response = $this->api_key_check($api_key);

        if('success' != $api_key_response['status']) {
            return $this->response($api_key_response);
        }

        $user_id = $api_key_response['user_id'];
        $contact_type_id = (int) trim($this->input->get('contact_type_id', true));

        if (! $contact_type_id) {
            $data = [
                'status' => 'error',
                'message' => 'The contact type ID is required',
            ];

            return $this->response($data); 
        }

        if (! $this->is_exist('sms_email_contact_group', ['id' => $contact_type_id, 'user_id' => $user_id])) {
            $data = [
                'status' => 'error',
                'message' => 'The contact type ID does not exist',
            ];

            return $this->response($data); 
        }

        $data = $this->basic->get_data('sms_email_contact_group', ['where' => ['id' => $contact_type_id]], '', '', 1);

        if (is_array($data) && 1 == count($data)) {
            return $this->response($data[0]);
        }

        $data  = [
            'status' => 'error',
            'message' => 'Unable to get contact group details', 
        ];

        return $this->response($data);     
    }

    public function create_contact_group() 
    {
        $api_key = $this->input->post('api_key',true);
        $api_key_response = $this->api_key_check($api_key);

        if('success' != $api_key_response['status']) {
            return $this->response($api_key_response);
        }

        $user_id = $api_key_response['user_id'];
        $name = $this->input->post('name', true);

        if (! $name) {
            $data = [
                'status' => 'error',
                'message' => 'The contact type name is required',
            ];

            return $this->response($data); 
        }

        $data = [
            'user_id' => $user_id,
            'type' => $name,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if (false != ($contact_type_id = $this->insert('sms_email_contact_group', $data))) {
            $data = [
                'status' => 'success',
                'contact_type_id' => $contact_type_id,
            ];

            return $this->response($data);
        }

        $data  = [
            'status' => 'error',
            'message' => 'Unable to create contact group name', 
        ];

        return $this->response($data);     
    }

    public function update_contact_group($contact_type_id = '') 
    {
        $api_key = $this->input->post('api_key',true);
        $api_key_response = $this->api_key_check($api_key);

        if('success' != $api_key_response['status']) {
            return $this->response($api_key_response);
        }

        $user_id = $api_key_response['user_id'];
        $name = $this->input->post('name', true);
        $contact_type_id = (int) $contact_type_id;

        if (! $contact_type_id) {
            $data['status'] = 'error';
            $data['message'] = 'Invalid contact type ID provided';

            return $this->response($data); 
        }

        if (! $name) {
            $data['status'] = 'error';
            $data['message'] = 'The name field is required';

            return $this->response($data); 
        }

        $user_has_permission = $this->is_exist(
            'sms_email_contact_group',
            [
                'user_id' => $user_id,
                'id' => $contact_type_id, 
            ]
        ); 

        if (! $user_has_permission) {
            $data = [
                'status' => 'error',
                'message' => 'You don NOT have permission to update contact group',
            ];

            return $this->response($data); 
        }

        $has_updated = $this->update('sms_email_contact_group', 
            [
                'user_id' => $user_id,
                'id' => $contact_type_id,
            ], 
            ['type' => $name]
        );

        if ($has_updated) {
            $data = [
                'status' => 'success',
            ];

            return $this->response($data);
        }

        $data  = [
            'status' => 'info',
            'message' => 'Make sure, you changed the contact group name', 
        ];

        return $this->response($data); 
    }

    public function delete_contact_group() 
    {
        $api_key = $this->input->post('api_key',true);
        $api_key_response = $this->api_key_check($api_key);

        if('success' != $api_key_response['status']) {
            return $this->response($api_key_response);
        }

        $user_id = $api_key_response['user_id'];
        $contact_type_id = $this->input->post('contact_type_id', true);

        if (! $contact_type_id) {
            $data['status'] = 'error';
            $data['message'] = 'The contact_type_id field is required';

            return $this->response($data); 
        }

        $user_has_permission = $this->is_exist(
            'sms_email_contact_group',
            [
                'user_id' => $user_id,
                'id' => $contact_type_id, 
            ]
        ); 

        if (! $user_has_permission) {
            $data = [
                'status' => 'error',
                'message' => 'The contact type ID does not exist',
            ];

            return $this->response($data); 
        }

        if ($this->delete_endpoint('sms_email_contact_group', ['id' => $contact_type_id])) {
            $data = [
                'status' => 'success',
                'message' => "The contact type ID ($contact_type_id) has been deleted",
            ];

            return $this->response($data);
        }

        $data  = [
            'status' => 'info',
            'message' => 'Unable to delete the contact group', 
        ];

        return $this->response($data);  
    }

    public function get_contact() 
    {
        $api_key = $this->input->get('api_key', true);
        $api_key_response = $this->api_key_check($api_key);

        if('success' != $api_key_response['status']) {
            return $this->response($api_key_response);
        }

        $user_id = $api_key_response['user_id'];
        $contact_id = (int) trim($this->input->get('contact_id', true));

        if (! $contact_id) {
            $data = [
                'status' => 'error',
                'message' => 'The contact_id field is required',
            ];

            return $this->response($data);
        }

        if (! $this->is_exist('sms_email_contacts', ['id' => $contact_id, 'user_id' => $user_id])) {
            $data = [
                'status' => 'error',
                'message' => 'The contact ID does not exist',
            ];

            return $this->response($data);
        }

        $data = $this->basic->get_data('sms_email_contacts', ['where' => ['id' => $contact_id]], '', '', 1);

        if (is_array($data) && 1 == count($data)) {
            return $this->response($data[0]);
        }

        $data  = [
            'status' => 'error',
            'message' => $this->lang->line("Unable to get contact data"), 
        ];

        return $this->response($data);
    }

    public function create_contact() 
    {
        $api_key = $this->input->post('api_key', true);
        $api_key_response = $this->api_key_check($api_key);

        if('success' != $api_key_response['status']) {
            return $this->response($api_key_response);
        }

        $user_id = $api_key_response['user_id'];

        $first_name = trim($this->input->post('first_name', true));
        $last_name = trim($this->input->post('last_name', true));
        $email = trim($this->input->post('email', true));
        $phone_number = trim($this->input->post('phone_number', true));
        $contact_type_id = (int) trim($this->input->post('contact_type_id', true));

        if (! $contact_type_id) {
            $data = [
                'status' => 'error',
                'message' => 'The contact_type_id is required',
            ];

            return $this->response($data);
        }

        if (! $this->is_exist('sms_email_contact_group', ['user_id' => $user_id, 'id' => $contact_type_id])) {
            $data = [
                'status' => 'error',
                'message' => 'You can NOT add the contact to the specified contact group',
            ];

            return $this->response($data);
        }

        if (! $email && ! $phone_number) {
            $data = [
                'status' => 'error',
                'message' => 'The email or phone_number is required',
            ];

            return $this->response($data);
        }

        if ($this->is_exist('sms_email_contacts', ['email' => $email])) {
            $data = [
                'status' => 'error',
                'message' => 'Please try with different email address',
            ];

            return $this->response($data);
        }

        if ($this->is_exist('sms_email_contacts', ['phone_number' => $phone_number])) {
            $data = [
                'status' => 'error',
                'message' => 'Please try with different phone_number',
            ];

            return $this->response($data);
        }

        $data = [
            'user_id' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone_number' => $phone_number,
            'contact_type_id' => $contact_type_id,
        ];

        if ($new_contact_id = $this->insert('sms_email_contacts', $data)) {
            $data = [
                'status' => 'success',
                'new_contact_id' => $new_contact_id,
            ];

            return $this->response($data);
        }

        $data  = [
            'status' => 'error',
            'message' => $this->lang->line("Unable to create new contact"), 
        ];

        return $this->response($data);
    }

    public function update_contact($contact_id = '') 
    {
        $api_key = $this->input->post('api_key', true);
        $api_key_response = $this->api_key_check($api_key);

        if('success' != $api_key_response['status']) {
            return $this->response($api_key_response);
        }

        $user_id = $api_key_response['user_id'];

        $first_name = trim($this->input->post('first_name', true));
        $last_name = trim($this->input->post('last_name', true));
        $email = trim($this->input->post('email', true));
        $phone_number = trim($this->input->post('phone_number', true));

        $contact_id = (int) $contact_id;

        if (! $contact_id) {
            $data = [
                'status' => 'error',
                'message' => 'The contact ID is required',
            ];

            return $this->response($data);
        }

        if (! $this->is_exist('sms_email_contacts', ['id' => $contact_id, 'user_id' => $user_id])) {
            $data = [
                'status' => 'error',
                'message' => 'You do NOT have permission to update the contact',
            ];

            return $this->response($data);
        }

        if (! $first_name && ! $last_name && ! $email && !$phone_number) {
            $data = [
                'status' => 'error',
                'message' => 'You should at least provide value for first name or last name or email or phone number',
            ];

            return $this->response($data);
        }        

        $data = [];

        if ($first_name) {
            $data['first_name'] = $first_name;
        }

        if ($last_name) {
            $data['last_name'] = $last_name;
        }

        if ($email) {
            if ($this->is_exist('sms_email_contacts', ['email' => $email])) {
                $data = [
                    'status' => 'error',
                    'message' => 'Please try with different email address',
                ];

                return $this->response($data);
            }

            $data['email'] = $email;
        }

        if ($phone_number) {
            if ($this->is_exist('sms_email_contacts', ['phone_number' => $phone_number])) {
                $data = [
                    'status' => 'error',
                    'message' => 'Please try with different phone_number',
                ];

                return $this->response($data);
            }

            $data['phone_number'] = $phone_number;
        }

        if ($this->update('sms_email_contacts', ['user_id' => $user_id, 'id' => $contact_id], $data)) {
            $data = [
                'status' => 'success',
            ];

            return $this->response($data);
        }

        $data  = [
            'status' => 'info',
            'message' => $this->lang->line("Make sure you changed to update the contact"), 
        ];

        return $this->response($data);
    }

    public function delete_contact() 
    {
        $api_key = $this->input->post('api_key', true);
        $api_key_response = $this->api_key_check($api_key);

        if('success' != $api_key_response['status']) {
            return $this->response($api_key_response);
        }

        $user_id = $api_key_response['user_id'];
        $contact_id = (int) trim($this->input->post('contact_id', true));

        if (! $contact_id) {
            $data = [
                'status' => 'error',
                'message' => 'The contact_id field is required',
            ];

            return $this->response($data);
        }

        if (! $this->is_exist('sms_email_contacts', ['id' => $contact_id, 'user_id' => $user_id])) {
            $data = [
                'status' => 'error',
                'message' => 'The contact ID does not exist',
            ];

            return $this->response($data);
        }

        if ($this->delete_endpoint('sms_email_contacts', ['id' => $contact_id])) {
            $data = [
                'status' => 'success',
                'message' => "The contact ID ($contact_id) has been deleted",
            ];

            return $this->response($data);
        }

        $data  = [
            'status' => 'info',
            'message' => 'Unable to delete the contact', 
        ];

        return $this->response($data); 
    }

    public function get_flow_campaigns() 
    {
        $api_key = $this->input->get('api_key',true);
        $api_key_response = $this->api_key_check($api_key);

        if('success' != $api_key_response['status']) {
            return $this->response($api_key_response);
        }

        $user_id = (int) $api_key_response['user_id'];


        $data = $this->basic->get_data('user_input_flow_campaign', ['where' => ['user_id' => $user_id]], ['id', 'flow_name']);      

        if (is_array($data)) {
            return $this->response($data);
        }

        $data  = [
            'status' => 'error',
            'message' => 'Unable to get flow campaign list', 
        ];

        return $this->response($data);     
    }

    public function get_flow_campaign_info() 
    {
        $api_key = $this->input->get('api_key', true);
        $api_key_response = $this->api_key_check($api_key);

        if('success' != $api_key_response['status']) {
            return $this->response($api_key_response);
        }

        $user_id = (int) $api_key_response['user_id'];
        $flow_campaign_id = (int) trim($this->input->get('flow_campaign_id', true));

        if (! $flow_campaign_id) {
            $data = [
                'status' => 'error',
                'message' => 'The flow_campaign_id field is required',
            ];

            return $this->response($data);
        }

        if (! $this->is_exist('user_input_flow_campaign', ['id' => $flow_campaign_id, 'user_id' => $user_id])) {
            $data = [
                'status' => 'error',
                'message' => 'The flow campaign ID does not exist',
            ];

            return $this->response($data);
        }

        $where = [
            'where' => [
                'user_input_flow_campaign.user_id' => $user_id,
                'user_input_flow_campaign.id' => $flow_campaign_id,
            ]
        ];

        $select = [
            'user_input_flow_campaign.id',
            'user_input_flow_campaign.flow_name',
            'user_input_flow_questions.question',
        ];

        $join = [
            'user_input_flow_questions' => 'user_input_flow_questions.flow_campaign_id=user_input_flow_campaign.id,left',
        ];

        $data = $this->basic->get_data('user_input_flow_campaign', $where, $select, $join);

        if (is_array($data)) {
            $questions = array_map(function($item) {
                return $item['question'];
            }, $data);

            $resultingData = [
                'id' => $data[0]['id'],
                'flow_name' => $data[0]['flow_name'],
                'questions' => $questions,
            ];

            return $this->response($resultingData);
        }

        $data  = [
            'status' => 'error',
            'message' => 'Unable to get flow campaign ', 
        ];

        return $this->response($data); 
    }

    public function get_single_subscriber_flow_info() 
    {
        $api_key = $this->input->get('api_key', true);
        $api_key_response = $this->api_key_check($api_key);

        if('success' != $api_key_response['status']) {
            return $this->response($api_key_response);
        }

        $user_id = (int) $api_key_response['user_id'];
        $subscriber_id = (int) trim($this->input->get('subscriber_id', true));
        $flow_campaign_id = (int) trim($this->input->get('flow_campaign_id', true));

        if (! $subscriber_id) {
            $data = [
                'status' => 'error',
                'message' => 'The subscriber_id field is required',
            ];

            return $this->response($data);
        }        

        if (! $flow_campaign_id) {
            $data = [
                'status' => 'error',
                'message' => 'The flow_campaign_id field is required',
            ];

            return $this->response($data);
        }

        if (! $this->is_exist('user_input_flow_campaign', ['id' => $flow_campaign_id, 'user_id' => $user_id])) {
            $data = [
                'status' => 'error',
                'message' => 'The flow campaign ID does not exist',
            ];

            return $this->response($data);
        }

        $where = [
            'where' => [
                'user_input_flow_questions_answer.subscriber_id' => $subscriber_id,
                'user_input_flow_questions_answer.flow_campaign_id' => $flow_campaign_id,
            ]
        ];

        $select = [
            'user_input_flow_questions_answer.question_id',
            'user_input_flow_questions_answer.user_answer',
            'user_input_flow_questions.question',
            'user_input_flow_campaign.flow_name',
            'user_input_flow_campaign.id as flow_id',
        ];

        $join = [
            'user_input_flow_questions' => 'user_input_flow_questions_answer.question_id=user_input_flow_questions.id,left',
            'user_input_flow_campaign' => 'user_input_flow_questions_answer.flow_campaign_id=user_input_flow_campaign.id,left',
        ];

        $data = $this->basic->get_data('user_input_flow_questions_answer', $where, $select, $join);

        if (is_array($data)) {
            $questions = array_map(function($item) {
                return [
                    'question_id' => $item['question_id'],
                    'question' => $item['question'],
                    'answer' => $item['user_answer'],
                ];
            }, $data);

            $resultingData = [
                'id' => $data[0]['flow_id'],
                'flow_name' => $data[0]['flow_name'],
                'data' => $questions,
            ];

            return $this->response($resultingData);
        }

        $data  = [
            'status' => 'error',
            'message' => 'Unable to get flow campaign ', 
        ];

        return $this->response($data); 
    }

    public function get_all_subscriber_flow_info() 
    {
        $api_key = $this->input->get('api_key', true);
        $api_key_response = $this->api_key_check($api_key);

        if('success' != $api_key_response['status']) {
            return $this->response($api_key_response);
        }

        $user_id = (int) $api_key_response['user_id'];    
        $flow_campaign_id = (int) trim($this->input->get('flow_campaign_id', true));       

        if (! $flow_campaign_id) {
            $data = [
                'status' => 'error',
                'message' => 'The flow_campaign_id field is required',
            ];

            return $this->response($data);
        }

        if (! $this->is_exist('user_input_flow_campaign', ['id' => $flow_campaign_id, 'user_id' => $user_id])) {
            $data = [
                'status' => 'error',
                'message' => 'The flow campaign ID does not exist',
            ];

            return $this->response($data);
        }

        $where = [
            'where' => [
                'user_input_flow_questions.user_id' => $user_id,
                'user_input_flow_questions_answer.flow_campaign_id' => $flow_campaign_id,
            ]
        ];

        $select = [
            'user_input_flow_questions_answer.subscriber_id',
            'user_input_flow_questions_answer.question_id',
            'user_input_flow_questions_answer.user_answer',
            'user_input_flow_questions.question',
            'user_input_flow_campaign.flow_name',
            'user_input_flow_campaign.id as flow_id',
        ];

        $join = [
            'user_input_flow_questions' => 'user_input_flow_questions_answer.question_id=user_input_flow_questions.id,inner',
            'user_input_flow_campaign' => 'user_input_flow_questions_answer.flow_campaign_id=user_input_flow_campaign.id,inner',
        ];

        $data = $this->basic->get_data('user_input_flow_questions_answer', $where, $select, $join);

        if (is_array($data)) {
            $subscriber_ids = array_values(array_column($data, 'subscriber_id', 'subscriber_id'));
            $resultingData = [
                'id' => $data[0]['flow_id'],
                'flow_name' => $data[0]['flow_name'],
                'data' => [],
            ];

            foreach($subscriber_ids as $subscriber_id) {
                $resultingData['data'][$subscriber_id] = [];
                foreach ($data as $item) {
                    if ($subscriber_id == $item['subscriber_id']) {
                        $tmpArray = [
                            'id' => $item['question_id'],
                            'question' => $item['question'],
                            'answer' => $item['user_answer'],
                        ];

                        array_push($resultingData['data'][$subscriber_id], $tmpArray);
                    }
                }
            }

            return $this->response($resultingData);
        }

        $data  = [
            'status' => 'error',
            'message' => 'Unable to get flow campaign ', 
        ];

        return $this->response($data); 
    }    

    private function insert($table, $data) 
    {
        $this->basic->insert_data($table, $data);

        if($this->db->affected_rows()) {
            return $this->db->insert_id();
        }

        return false;  
    }

    private function update($table, $where, $data) 
    {
        $this->basic->update_data($table, $where, $data);

        if($this->db->affected_rows()) {
            return true;
        }

        return false;
    }

    private function delete_endpoint($table, $data) 
    {
        $this->basic->delete_data($table, $data);

        if($this->db->affected_rows()) {
            return true;
        }

        return false;
    }

    private function is_exist($table, $whereData) 
    {
        $result = $this->basic->is_exist($table, $whereData);

        if (false == $result) {
            return false;
        }

        return true;
    }

    private function response($data) 
    {   
        if (! headers_sent()) {
            header('Content-Type: application/json');
        }

        echo json_encode($data);
    }
}


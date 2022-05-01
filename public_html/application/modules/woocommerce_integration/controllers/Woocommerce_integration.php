<?php
/*
Addon Name: WooCommerce Integration
Unique Name: woocommerce_integration

Modules:
{
   "293":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"WooCommerce Integration"
   }
}
Project ID: 50
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 2.0
Description: Import WooCommerce products to sell inside Messenger using webview and export products as Ecommerce product
*/

require_once("application/controllers/Home.php"); // loading home controller
require APPPATH . 'modules/woocommerce_integration/assets/vendor/autoload.php';
use Automattic\WooCommerce\Client;

class Woocommerce_integration extends Home
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

		$function_name=$this->uri->segment(2);
		if($function_name!="store" && $function_name!="product")
		{
		      // all addon must be login protected
		      //------------------------------------------------------------------------------------------
		      if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');
		      $this->member_validity();       
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
		$this->load->helper("ecommerce");
		
	}

	public function index()
	{
	    $data['page_title'] = $this->lang->line('WooCommerce Integration');
	    $data['body'] = 'woocommerce_app_settings';

	    $where_custom = '';
	    $where_custom="user_id = ".$this->user_id;
	    $table="woocommerce_config";
	    $this->db->where($where_custom);
	    $data['info']=$this->basic->get_data($table,$where='',$select='',$join='','','','id desc');
	
	    $this->_viewcontroller($data);
	}


	public function add_woocommerce_settings()
	{
	    $data['table_id'] = 0;
	    $data['woocommerce_settings'] = array();
	    $data['page_title'] = $this->lang->line('Connect WooCommerce API');
	    $data['body'] = 'woocommerce_settings';

	    $this->_viewcontroller($data);
	}


	public function edit_woocommerce_settings($table_id=0)
	{
	    
	    if($table_id==0) exit;
	    $woocommerce_settings = $this->basic->get_data('woocommerce_config',array("where"=>array("id"=>$table_id,"user_id"=>$this->user_id)));
	    if (!isset($woocommerce_settings[0])) $woocommerce_settings = array();
	    else $woocommerce_settings = $woocommerce_settings[0];
	    $data['table_id'] = $table_id;
	    $data['woocommerce_settings'] = $woocommerce_settings;
	    $data['page_title'] = $this->lang->line('Connect WooCommerce API');
	    $data['body'] = 'woocommerce_settings';

	    $this->_viewcontroller($data);
	}


	public function woocommerce_settings_update_action()
	{
	    // if($this->is_demo == '1' && $this->session->userdata('user_type') == "Admin" )
	    // {
	    //     echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>You are not allowed perform this action as admin account!!</h2>"; 
	    //     exit();
	    // }

	    if (!isset($_POST)) exit;

	    $this->form_validation->set_rules('consumer_key', $this->lang->line("Consumer Key"), 'trim|required');
	    $this->form_validation->set_rules('consumer_secret', $this->lang->line("Consumer Secret"), 'trim|required');
	    $this->form_validation->set_rules('home_url', $this->lang->line("Website Home URL"), 'trim|required');
	    $table_id = $this->input->post('table_id',true);

	    if ($this->form_validation->run() == FALSE) 
	    {
	        if($table_id == 0) $this->add_woocommerce_settings();
	        else $this->edit_woocommerce_settings($table_id);
	    }
	    else
	    {
	        $this->csrf_token_check();
	        $consumer_key = strip_tags($this->input->post('consumer_key',true));
        	$consumer_secret = strip_tags($this->input->post('consumer_secret',true));
        	$home_url = strip_tags($this->input->post('home_url',true));
        	$this->sync_data($table_id,$consumer_key,$consumer_secret,$home_url);     
	        redirect(base_url('woocommerce_integration'),'location');	        
	    }
	}


	public function sync_woocommerce_data($table_id=0)
	{
		if($table_id==0) exit();
		$config_data = $this->basic->get_data("woocommerce_config",array("where"=>array("id"=>$table_id,"user_id"=>$this->user_id)));
		if(!isset($config_data[0])) exit();
		$consumer_key = $config_data[0]['consumer_key'];
		$consumer_secret = $config_data[0]['consumer_secret'];
		$home_url = $config_data[0]['home_url'];
		$this->sync_data($table_id,$consumer_key,$consumer_secret,$home_url);
		redirect(base_url('woocommerce_integration'),'location');
	}


	private function sync_data($table_id=0,$consumer_key="",$consumer_secret="",$home_url="")
	{
        $insert_data['consumer_key'] =$consumer_key ;
        $insert_data['consumer_secret'] = $consumer_secret;
        $insert_data['home_url'] = $home_url;
        $insert_data['user_id'] = $this->user_id;
        $insert_data['last_updated_at'] = date("Y-m-d H:i:s");

        try
        {	            
            $per_page = 100;
            $page = 1;
            $woocommerce_api_call = new Client($home_url, $consumer_key, $consumer_secret,['wp_api' => true,'version' => 'wc/v3','query_string_auth' =>true]);	           
            $woocommerce_product_2d = array();
            $found = true;
            while ($found)
            {
            	$temp = $woocommerce_api_call->get('products',['page'=>$page,'per_page'=>$per_page]);
            	if(!empty($temp)) $woocommerce_product_2d[$page] = $temp;
            	$page++;
            	if(empty($temp)) $found = false;
            }

            $woocommerce_product = array();
            foreach ($woocommerce_product_2d as $key => $value)
            {
            	foreach ($value as $key2 => $value2)
            	{
            		$woocommerce_product[] = $value2;
            	}
            }

            if(!empty($woocommerce_product))
            {
            	$this->db->trans_start();
            	// $payment_gateways = $woocommerce_api_call->get('payment_gateways');
            	$woocommerce_settings = $woocommerce_api_call->get('system_status');
            	
            	$insert_data['currency'] = isset($woocommerce_settings->settings->currency) ? $woocommerce_settings->settings->currency : "USD";
            	$insert_data['currency_icon'] = isset($woocommerce_settings->settings->currency_symbol) ? $woocommerce_settings->settings->currency_symbol : "$";
            	$insert_data['currency_position'] = isset($woocommerce_settings->settings->currency_position) ? $woocommerce_settings->settings->currency_position : "left";
            	$insert_data['decimal_point'] = isset($woocommerce_settings->settings->currency) ? $woocommerce_settings->settings->currency : "USD";
            	$insert_data['thousand_comma'] = isset($woocommerce_settings->settings->thousand_separator) && $woocommerce_settings->settings->thousand_separator==',' ? "1" : "0";
                
                $woocommerce_product_attribute = $woocommerce_api_call->get('products/attributes');
            	$woocommerce_product_category = $woocommerce_api_call->get('products/categories');

            	if(!empty($woocommerce_product_attribute)) $insert_data['attributes'] = json_encode($woocommerce_product_attribute);
            	if(!empty($woocommerce_product_category)) $insert_data['categories'] = json_encode($woocommerce_product_category);	            	

            	if ($table_id != 0) 
            	{
            		$this->basic->update_data('woocommerce_config', array('id' => $table_id,"user_id"=>$this->user_id), $insert_data);
            		$woocommerce_config_id = $table_id;
            	}
            	else
            	{
            		$this->basic->insert_data('woocommerce_config', $insert_data);
            		$woocommerce_config_id = $this->db->insert_id();
            	}
            	$user_id = $this->user_id;
            	foreach ($woocommerce_product as $key => $value)
            	{
            		$wc_product_id = isset($value->id) ? $value->id : 0;
            		$product_name = isset($value->name) ? $this->db->escape($value->name) : '""';
            		$slug = isset($value->slug) ? $this->db->escape($value->slug) : '""';
            		$permalink = isset($value->permalink) ? $this->db->escape($value->permalink) : '""';
            		$product_description = isset($value->description) ? $this->db->escape($value->description) : '""';
            		$purchase_note = isset($value->purchase_note) ? $this->db->escape($value->purchase_note) : '""';
            		$original_price = isset($value->regular_price) ? $value->regular_price : 0;
            		$sell_price = isset($value->sale_price) ? $value->sale_price : 0;
            		$price_html = isset($value->price_html) ? $this->db->escape($value->price_html) : '""';
            		$taxable = (isset($value->tax_status) && $value->tax_status=="taxable") ? '1' : '0';
            		$stock_item = isset($value->stock_quantity) ? $value->stock_quantity : 0;	            		
            		$value_images = isset($value->images) ? $value->images : array();
            		$thumbnail = isset($value_images[0]->src) ? $this->db->escape($value_images[0]->src) : '""';
            		$category = isset($value->categories) ? $this->db->escape(json_encode($value->categories)) : '""';
            		$attribute = isset($value->attributes) ? $this->db->escape(json_encode($value->attributes)) : '""';
            		$featured_images_array = array();
            		
            		if(isset($value_images[0]->src)) unset($value_images[0]->src);
            		foreach ($value_images as $key2 => $value2)
            		{
            			$featured_images_array[] = isset($value2->src) ?$value2->src : "";
            		}
            		$featured_images = implode(',', $featured_images_array);
            		$featured_images = ltrim($featured_images,',');
            		$featured_images = $this->db->escape($featured_images);

            		if(empty($featured_images)) $featured_images = '""';
            		$sales_count = isset($value->total_sales) ? $value->total_sales : 0;
            		$updated_at = date("Y-m-d H:i:s");
            		
            		$status = (isset($value->status) && $value->status=="publish") ? '1' : '0';
            		$on_sale = isset($value->on_sale) ? '1' : '0';

            		if($status=="0") continue;

            		$sql="INSERT INTO woocommerce_product
            		(
	            		user_id,
	            		woocommerce_config_id,
	            		wc_product_id,
	            		slug,
	            		permalink,
	            		product_name,
	            		product_description,
	            		purchase_note,
	            		original_price,
	            		sell_price,
	            		price_html,
	            		taxable,
	            		stock_item,
	            		thumbnail,
	            		featured_images,
	            		sales_count,
	            		category,
	            		attribute,
	            		updated_at,
	            		status,
	            		on_sale
            		) 
            		VALUES
            		(
            			'$user_id',
            			'$woocommerce_config_id',
            			'$wc_product_id',
            			 $slug,
            			 $permalink,
            			 $product_name,
            			 $product_description,
            			 $purchase_note,
            			 '$original_price',
            			 '$sell_price',
            			 $price_html,
            			 '$taxable',
            			 '$stock_item',
            			 $thumbnail,
            			 $featured_images,
            			 '$sales_count',
            			 $category,
	            		 $attribute,
            			 '$updated_at',
            			 '$status',
            			 '$on_sale'
            		)
            		ON DUPLICATE KEY UPDATE
	            		user_id='$user_id',
	            		woocommerce_config_id='$woocommerce_config_id',
	            		wc_product_id='$wc_product_id',
	            		slug=$slug,
	            		permalink=$permalink,
	            		product_name=$product_name,
	            		product_description=$product_description,
	            		purchase_note=$purchase_note,
	            		original_price='$original_price',
	            		sell_price='$sell_price',
	            		price_html=$price_html,
	            		taxable='$taxable',
	            		stock_item='$stock_item',
	            		thumbnail=$thumbnail,
	            		featured_images=$featured_images,
	            		sales_count='$sales_count',
	            		category=$category,
	            		attribute=$attribute,
	            		updated_at='$updated_at',
	            		status='$status',
	            		on_sale='$on_sale' ; ";
            		$this->basic->execute_complex_query($sql);

            	}

            	$this->db->trans_complete();
            	if ($this->db->trans_status() === FALSE) $this->session->set_flashdata('error_message_woocommerce', $this->lang->line("Something went wrong. Failed to import WooCommerce data."));
            	else $this->session->set_flashdata('success_message', '1');
            }

        }
        catch (Exception  $e)
        {
            $this->session->set_flashdata('error_message_woocommerce', $e->getMessage());
        }
	}


	public function product_list($id=0)
	{
	  $data['body'] = 'product_list';
	  $data['page_title'] = $this->lang->line('Product');
	  $data["iframe"]="1";	  
	  $data["config_id"]=$id;
	  $data['store_data']=$this->basic->get_data("ecommerce_store",array("where"=>array("ecommerce_store.user_id"=>$this->user_id)),'id,store_unique_id,store_name','','',NULL,"store_name ASC");
	  $this->_viewcontroller($data);
	}


	public function product_list_data()
	{ 
	  $this->ajax_check();
	  $store_data =$this->basic->get_data("ecommerce_store",array("where"=>array("ecommerce_store.user_id"=>$this->user_id)),'id,store_unique_id,store_name','','',NULL,"store_name ASC");
	  $search_value = $this->input->post("search_value");
	  $store_id = $this->input->post("search_store_id");  
	  $display_columns = 
	  array(
	    "#",
	    "CHECKBOX",
	    "thumbnail",
	    'product_name',
	    'price_html',
	    'actions',
	    'updated_at',
	  );
	  $search_columns = array('product_name');

	  $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	  $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
	  $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
	  $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 3;
	  $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'product_name';
	  $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'asc';
	  $order_by=$sort." ".$order;

	  $where_custom="woocommerce_product.user_id = ".$this->user_id;

	  if ($search_value != '') 
	  {
	      foreach ($search_columns as $key => $value) 
	      $temp[] = $value." LIKE "."'%$search_value%'";
	      $imp = implode(" OR ", $temp);
	      $where_custom .=" AND (".$imp.") ";
	  }
	  $this->db->where($where_custom);

	  if($store_id!="") $this->db->where(array("woocommerce_product.woocommerce_config_id"=>$store_id));       
	  
	  $table="woocommerce_product";
	  $select = "woocommerce_product.*,woocommerce_config.home_url,";
	  $join = array('woocommerce_config'=>"woocommerce_config.id=woocommerce_product.woocommerce_config_id,left");
	  $info=$this->basic->get_data($table,$where='',$select,$join,$limit,$start,$order_by,$group_by='');
	  
	  $this->db->where($where_custom);
	  if($store_id!="") $this->db->where(array("woocommerce_product.woocommerce_config_id"=>$store_id)); 
	  $total_rows_array=$this->basic->count_row($table,$where='',$count=$table.".id",$join,$group_by='');

	  $total_result=$total_rows_array[0]['total_rows'];

	  foreach($info as $key => $value) 
	  {
	      $updated_at = date("M j, y H:i",strtotime($info[$key]['updated_at']));
	      $info[$key]['updated_at'] =  "<div style='min-width:110px;'>".$updated_at."</div>";
	      $link = base_url("woocommerce_integration/product/".$info[$key]['id']);
	      $actions = "<a target='_BLANK' href='".$link."' title='".$this->lang->line("Product Page")."' data-toggle='tooltip' class='btn btn-circle btn-outline-info'><i class='fas fa-eye'></i></a>";      

	      $info[$key]['actions'] = $actions;	    

	      if($info[$key]['thumbnail']=='') $url = base_url('assets/img/products/product-1.jpg');
	      else $url = $info[$key]['thumbnail'];
	      $info[$key]['thumbnail'] = "<a  target='_BLANK' href='".$link."'><img class='img-fluid' style='height:80px;width:80px;border-radius:4px;border:1px solid #eee;padding:2px;' src='".$url."'></a>";
	      $info[$key]['product_name'] = "<a  target='_BLANK' href='".$link."'>".$info[$key]['product_name']."</a>";
	  }
	  $data['draw'] = (int)$_POST['draw'] + 1;
	  $data['recordsTotal'] = $total_result;
	  $data['recordsFiltered'] = $total_result;
	  $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
	  echo json_encode($data);
	}


	public function export_product()
	{
		$this->ajax_check();
		$ids = $this->input->post("ids",true);
		$store_id = $this->input->post("store_id",true);
		$woocommerce_config_id = $this->input->post("woocommerce_config_id",true);

		$this->db->trans_start();
		$woocommerce_config = $this->basic->get_data("woocommerce_config",array("where"=>array("id"=>$woocommerce_config_id)));
		if(empty($woocommerce_config))
		{
			echo json_encode(array("status"=>"0","message"=>$this->lang->line("No WooCommerce integration found.")));
			exit();
		}
		$woocommerce_config_data = $woocommerce_config[0];
		$attributes = isset($woocommerce_config_data['attributes']) ? json_decode($woocommerce_config_data['attributes'],true):array();
		$categories = isset($woocommerce_config_data['categories']) ? json_decode($woocommerce_config_data['categories'],true):array();
		$user_id = isset($woocommerce_config_data['user_id']) ? $woocommerce_config_data['user_id']:0;
		$updated_at = date("Y-m-d H:i:s");
		
		$woocommerce_products = $this->basic->get_data("woocommerce_product",array("where_in"=>array("id"=>$ids)));
		$woocommerce_attribute_map = array();
		$attribute_insert_data = array();

		foreach ($woocommerce_products as $key => $value)
		{
    		$tmp_attribute = isset($value['attribute']) ? json_decode($value['attribute'],true):array();
    		foreach($tmp_attribute as $key2 => $value2)
    		{
    			if(!isset($value2['name']) || !isset($value2['id'])) continue;

    			if($value2['id']!=0) $woocommerce_attribute_map[$value['id']][]=$value2['id'];
    			else $woocommerce_attribute_map[$value['id']][]=$value2['name'];

    			$index = $value2['name'];
    			$attribute_insert_data[$index]['id'] = isset($value2['id'])  ? $value2['id'] : 0;
    			$attribute_insert_data[$index]['name'] = $index;
    			
    			if(isset($attribute_insert_data[$index]['values']) && !empty($attribute_insert_data[$index]['values']))
    				 $attribute_insert_data[$index]['values'] = array_merge($attribute_insert_data[$index]['values'], $value2['options']);
    			else $attribute_insert_data[$index]['values'] = isset($value2['options'])  ? $value2['options'] : array();

    			$attribute_insert_data[$index]['values'] = array_unique($attribute_insert_data[$index]['values']);
    		}
		}
		
		$woocommerce_attribute_ids = array();
		foreach ($attributes as $key => $value)
		{
			$woocommerce_attribute_id = isset($value['id']) ? $value['id'] : NULL;
			$woocommerce_attribute_slug = isset($value['slug']) ? $value['slug'] : '';
			$attribute_name = isset($value['name']) ? $value['name'] : '';
			$attribute_values = isset($attribute_insert_data[$attribute_name]['values']) ? $attribute_insert_data[$attribute_name]['values'] : array();
			if(empty($attribute_values)) continue;
			$attribute_values = $this->db->escape(json_encode($attribute_values));
			$attribute_name = $this->db->escape($attribute_name);
			$woocommerce_attribute_ids[] = $woocommerce_attribute_id;		
			$sql="INSERT INTO ecommerce_attribute
			(
	    		user_id,
	    		store_id,
	    		woocommerce_config_id,
	    		woocommerce_attribute_id,
	    		woocommerce_attribute_slug,
	    		attribute_name,
	    		attribute_values,
	    		optional,
	    		multiselect,
	    		status,
	    		updated_at
			) 
			VALUES
			(
				'{$user_id}',
				'{$store_id}',
				'{$woocommerce_config_id}',
				'{$woocommerce_attribute_id}',
				'{$woocommerce_attribute_slug}',
	    		{$attribute_name},
	    		{$attribute_values},
	    		'1',
	    		'0',
	    		'1',
	    		'{$updated_at}'
			)
			ON DUPLICATE KEY UPDATE
	    		user_id='{$user_id}',
	    		store_id='{$store_id}',
	    		woocommerce_config_id='{$woocommerce_config_id}',
	    		woocommerce_attribute_id='{$woocommerce_attribute_id}',
	    		woocommerce_attribute_slug='{$woocommerce_attribute_slug}',
	    		attribute_name=	{$attribute_name},
	    		attribute_values={$attribute_values},
	    		optional='1',
	    		multiselect='0',
	    		status='1',
	    		updated_at='{$updated_at}'
	    		; ";
			$this->basic->execute_complex_query($sql);
		}
		$check_default_attr = $this->basic->get_data("ecommerce_attribute",array("where"=>array("store_id"=>$store_id),"where_in"=>array("attribute_name"=>array("size","color"))),"id,attribute_name");
		$attr_size_id = 0;
		$attr_color_id = 0;
		foreach ($check_default_attr as $key2 => $value2)
		{
			if(strtolower($value2['attribute_name'])=='size' && $attr_size_id<=0) $attr_size_id = $value2['id'];
			if(strtolower($value2['attribute_name'])=='color' && $attr_color_id<=0) $attr_color_id = $value2['id'];
		}
		$default_attr = array("user_id"=>$user_id,"store_id"=>$store_id,"optional"=>"1","multiselect"=>"0","status"=>"1","updated_at"=>$updated_at);
		if($attr_color_id==0 && isset($attribute_insert_data["Color"]['values']))
		{
			$default_attr["attribute_values"] = json_encode($attribute_insert_data["Color"]['values']);
			$default_attr["attribute_name"] = "Color";
			$this->basic->insert_data("ecommerce_attribute",$default_attr);
			$attr_color_id = $this->db->insert_id();
		}
		if($attr_size_id==0 && isset($attribute_insert_data["Size"]['values']))
		{
			$default_attr["attribute_values"] = json_encode($attribute_insert_data["Size"]['values']);
			$default_attr["attribute_name"] = "Size";
			$this->basic->insert_data("ecommerce_attribute",$default_attr);
			$attr_size_id = $this->db->insert_id();
		}
		$woocommece_to_ecommerce_attribute_map = array();
		if(!empty($woocommerce_attribute_ids))
		{
			$get_new_attr = $this->basic->get_data("ecommerce_attribute",array("where_in"=>array("store_id"=>$store_id,"woocommerce_attribute_id"=>$woocommerce_attribute_ids)),"id,woocommerce_attribute_id");
			foreach ($get_new_attr as $key => $value)
			{
				$woocommece_to_ecommerce_attribute_map[$value["woocommerce_attribute_id"]] = $value["id"];
			}
		}



		$woocommerce_category_ids = array();
		foreach ($categories as $key => $value)
		{
			$woocommerce_category_id = isset($value['id']) ? $value['id'] : NULL;
			$woocommerce_category_slug = isset($value['slug']) ? $value['slug'] : '';
			$category_name = isset($value['name']) ? $value['name'] : '';
			$category_name = $this->db->escape($category_name);
			$woocommerce_category_ids[] = $woocommerce_category_id;
			$sql="INSERT INTO ecommerce_category
			(
	    		user_id,
	    		store_id,
	    		woocommerce_config_id,
	    		woocommerce_category_id,
	    		woocommerce_category_slug,
	    		category_name,
	    		status,
	    		updated_at
			) 
			VALUES
			(
				'{$user_id}',
				'{$store_id}',
				'{$woocommerce_config_id}',
				'{$woocommerce_category_id}',
				'{$woocommerce_category_slug}',
	    		{$category_name},
	    		'1',
	    		'{$updated_at}'
			)
			ON DUPLICATE KEY UPDATE
	    		user_id='{$user_id}',
	    		store_id='{$store_id}',
	    		woocommerce_config_id='{$woocommerce_config_id}',
	    		woocommerce_category_id='{$woocommerce_category_id}',
	    		woocommerce_category_slug='{$woocommerce_category_slug}',
	    		category_name=	{$category_name},	    		
	    		status='1',
	    		updated_at='{$updated_at}'
	    		; ";
			$this->basic->execute_complex_query($sql);
		}
		$woocommece_to_ecommerce_category_map = array();
		if(!empty($woocommerce_category_ids))
		{
			$get_new_cat = $this->basic->get_data("ecommerce_category",array("where_in"=>array("woocommerce_category_id"=>$woocommerce_category_ids)),"id,woocommerce_category_id");
			foreach ($get_new_cat as $key => $value)
			{
				$woocommece_to_ecommerce_category_map[$value["woocommerce_category_id"]] = $value["id"];
			}
		}




		foreach ($woocommerce_products as $key => $value)
		{
    		if($value["on_sale"]!="1") $value["sell_price"] = 0;
    		$value["product_name"] = $this->db->escape($value["product_name"]);
    		$value["product_description"] = $this->db->escape($value["product_description"]);
    		$attribute_ids = isset($woocommerce_attribute_map[$value['id']]) ? $woocommerce_attribute_map[$value['id']] : array();
    		if(in_array('Color',$attribute_ids)) array_push($attribute_ids, $attr_color_id);
    		if(in_array('Size',$attribute_ids))  array_push($attribute_ids, $attr_size_id);
    		foreach ($attribute_ids as $key2 => $value2)
    		{	
    			if(isset($woocommece_to_ecommerce_attribute_map[$value2])) $attribute_ids[$key2] = $woocommece_to_ecommerce_attribute_map[$value2];
    		}    		
    		$attribute_ids = array_filter($attribute_ids, 'is_numeric');    			
    		$attribute_ids = implode(',', $attribute_ids);

    		$cat_json = json_decode($value['category'],true);
    		$category_ids = array_column($cat_json, 'id');
    		foreach ($category_ids as $key2 => $value2)
    		{	
    			if(isset($woocommece_to_ecommerce_category_map[$value2])) $category_ids[$key2] = $woocommece_to_ecommerce_category_map[$value2];
    		}
    		$category_ids = array_filter($category_ids, 'is_numeric');
    		$insert_category_id = reset($category_ids);
    		if(!$insert_category_id) $insert_category_id = 0;
    		
    		$sql="INSERT INTO ecommerce_product
    		(
        		user_id,
        		store_id,
        		product_name,
        		product_description,
        		original_price,
        		sell_price,
        		taxable,
        		stock_item,
        		stock_display,
        		attribute_ids,
        		category_id,
        		thumbnail,
        		featured_images,
        		sales_count,
        		updated_at,
        		status,
        		woocommerce_product_id,
        		woocommerce_price_html
    		) 
    		VALUES
    		(
    			'{$value["user_id"]}',
    			'{$store_id}',
        		{$value["product_name"]},
        		{$value["product_description"]},
        		'{$value["original_price"]}',
        		'{$value["sell_price"]}',
        		'{$value["taxable"]}',
        		'{$value["stock_item"]}',
        		'0',
        		'{$attribute_ids}',
        		'{$insert_category_id}',
        		'{$value["thumbnail"]}',
        		'{$value["featured_images"]}',
        		'{$value["sales_count"]}',
        		'{$value["updated_at"]}',
        		'{$value["status"]}',
        		'{$value["id"]}',
        		'{$value["price_html"]}'
    		)
    		ON DUPLICATE KEY UPDATE
        		user_id='{$value["user_id"]}',
        		store_id='{$store_id}',
        		product_name={$value["product_name"]},
        		product_description={$value["product_description"]},
        		original_price='{$value["original_price"]}',
        		sell_price='{$value["sell_price"]}',
        		taxable='{$value["taxable"]}',
        		stock_item='{$value["stock_item"]}',
        		stock_display='0',
        		attribute_ids='{$attribute_ids}',
        		category_id='{$insert_category_id}',
        		thumbnail='{$value["thumbnail"]}',
        		featured_images='{$value["featured_images"]}',
        		sales_count='{$value["sales_count"]}',
        		updated_at='{$value["updated_at"]}',
        		status='{$value["status"]}',
        		woocommerce_product_id='{$value["id"]}',
        		woocommerce_price_html='{$value["price_html"]}'
        		; ";
    		$this->basic->execute_complex_query($sql);
		}

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) echo json_encode(array("status"=>"0","message"=>$this->lang->line("Something went wrong. Failed to export products.")));
		else  echo json_encode(array("status"=>"1","message"=>count($woocommerce_products)." ".$this->lang->line("Products have been exported to ecommerce successfully.")));
	}



	public function delete_action()
    {
     if($this->is_demo == '1')
      {
          if($this->session->userdata('user_type') == "Admin")
          {
              $response['status'] = 0;
              $response['message'] = "You can not delete anything from admin account!!";
              echo json_encode($response);
              exit();
          }
      }

      $this->ajax_check();
      $this->csrf_token_check();
      $app_table_id = $this->input->post('app_table_id',true);
      $app_info = $this->basic->get_data('woocommerce_config',array('where'=>array('id'=>$app_table_id,'user_id'=>$this->user_id)));
      if(empty($app_info))
      {
        $response['status'] = 0;
        $response['message'] = $this->lang->line('We could not find any API.');  
        echo json_encode($response);
        exit;
      }
      
      $this->basic->delete_data('woocommerce_config',array('id'=>$app_table_id,'user_id'=>$this->user_id));
      $this->basic->delete_data('woocommerce_product',array('woocommerce_config_id'=>$app_table_id,'user_id'=>$this->user_id));
      $response['status'] = 1;
      $response['message'] = $this->lang->line("WooCommerce API has been deleted successfully.");  
      echo json_encode($response);
    }


    public function store($id=0)
    {
      if($id==0) exit();
      $where_simple = array("woocommerce_config.id"=>$id);
      $where = array('where'=>$where_simple);
      $store_data = $this->basic->get_data("woocommerce_config",$where);

      if(!isset($store_data[0]))
      {
        echo '<br/><h2 style="border:1px solid red;padding:15px;color:red">'.$this->lang->line("Store not found.").'</h2>';
        exit();
      }
      $user_id = $store_data[0]['user_id'];
      
      $data = array('body'=>"store_single","page_title"=>$store_data[0]['home_url']." | ".$this->lang->line("Products"));

      $order_by = "product_name ASC";
      $default_where = array();

      $data["store_data"] = $store_data[0];
      $data["product_list"] = $this->get_product_list_array($id,$default_where,$order_by);     
      $this->load->view('bare-theme', $data);
    }

    public function product($product_id=0)
    {
      if($product_id==0) exit();      
      $where_simple = array("woocommerce_product.id"=>$product_id,"woocommerce_product.status"=>"1");
      $where = array('where'=>$where_simple);
      $join = array(' woocommerce_config'=>"woocommerce_product.woocommerce_config_id=woocommerce_config.id,left");  
      $select = array("woocommerce_product.*","currency_icon","currency_position","decimal_point","thousand_comma","attributes","categories");   
      $product_data = $this->basic->get_data("woocommerce_product",$where,$select,$join);

      if(!isset($product_data[0]))
      {
        echo '<br/><h1 style="text-align:center">'.$this->lang->line("Product not found.").'</h1>';
        exit();
      }
      
      $update_visit_count_sql = "UPDATE woocommerce_product SET visit_count=visit_count+1 WHERE id=".$product_id;
      $this->basic->execute_complex_query($update_visit_count_sql);

      $user_id = isset($product_data[0]["user_id"]) ? $product_data[0]["user_id"] : 0;
      $data = array('body'=>"product_single","page_title"=>$product_data[0]['product_name']);

      $data["product_data"] = $product_data[0];
      $data['current_product_id'] = isset($product_data[0]['id']) ? $product_data[0]['id'] : 0;
      $data['current_store_id'] = isset($product_data[0]['woocommerce_config_id']) ? $product_data[0]['woocommerce_config_id'] : 0;

      $this->load->view('bare-theme', $data);
    }

    private function get_product_list_array($woocommerce_config_id=0,$default_where="",$order_by="")
    {
      $where_simple = array("woocommerce_config_id"=>$woocommerce_config_id,"status"=>"1");
      if(isset($default_where['product_name'])) {
        $product_name = $default_where['product_name'];
        $this->db->where(" product_name LIKE "."'%".$product_name."%'");
        unset($default_where['product_name']);
      }
      if(is_array($default_where) && !empty($default_where))
      {
        foreach($default_where as $key => $value) 
        {
          $where_simple[$key] = $value;
        }
      }      
      if($order_by=="") $order_by = "product_name ASC";     
      $product_list = $this->basic->get_data("woocommerce_product",array("where"=>$where_simple),$select='',$join='',$limit='',$start=NULL,$order_by);
      
      // echo $this->db->last_query();
      return $product_list;
    }

    public function copy_url($id=0)
    {
      $data['product_list'] = $this->get_product_list_array($id);
      $where_simple = array("woocommerce_config.id"=>$id);
      $where = array('where'=>$where_simple);
      $store_data = $this->basic->get_data("woocommerce_config",$where);
      if(!isset($store_data[0])) exit();
      $data["store_data"] = $store_data[0];
      $data['body'] = "copy_url";
      $data['iframe'] = "1";
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
        $sql=
        array
        (
        	0 => "INSERT INTO `menu` (`name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES('WC Integration', 'fab fa-wordpress', 'woocommerce_integration', (SELECT serial FROM menu as menu2 WHERE url='ecommerce'), '293', '0', '0', '0', (SELECT id FROM add_ons WHERE project_id='50'), '0', '', '0', 0);",
        	1 => "CREATE TABLE IF NOT EXISTS `woocommerce_config` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `user_id` int(11) NOT NULL,
				  `consumer_key` varchar(255) NOT NULL,
				  `consumer_secret` varchar(255) NOT NULL,
				  `home_url` text NOT NULL,
				  `currency` varchar(10) NOT NULL DEFAULT 'USD',
				  `currency_icon` varchar(10) NOT NULL DEFAULT '$',
				  `currency_position` enum('left','right') NOT NULL DEFAULT 'left',
				  `decimal_point` tinyint(4) NOT NULL,
				  `thousand_comma` enum('0','1') NOT NULL DEFAULT '0',
				  `attributes` longtext NOT NULL,
				  `categories` text NOT NULL,
				  `last_updated_at` datetime NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `user_id` (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			2 => "CREATE TABLE IF NOT EXISTS `woocommerce_product` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `user_id` int(11) NOT NULL,
				  `woocommerce_config_id` int(11) NOT NULL,
				  `wc_product_id` int(11) NOT NULL,
				  `slug` text NOT NULL,
				  `permalink` text NOT NULL,
				  `product_name` text NOT NULL,
				  `product_description` text NOT NULL,
				  `purchase_note` text NOT NULL,
				  `original_price` float NOT NULL,
				  `sell_price` float NOT NULL,
				  `price_html` text NOT NULL,
				  `taxable` enum('0','1') NOT NULL DEFAULT '0',
				  `stock_item` int(11) NOT NULL,
				  `thumbnail` text NOT NULL,
				  `featured_images` text NOT NULL,
				  `sales_count` int(11) NOT NULL,
				  `visit_count` int(11) NOT NULL,
				  `category` text NOT NULL,
				  `attribute` text NOT NULL,
				  `updated_at` datetime NOT NULL,
				  `status` enum('0','1') NOT NULL DEFAULT '1',
				  `on_sale` enum('0','1') NOT NULL DEFAULT '0',
				  `is_exported` enum('0','1') NOT NULL DEFAULT '0',
				  `exported_store_id` int(11) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `woocommerce_config_id` (`woocommerce_config_id`,`wc_product_id`)
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
        	0=> "DELETE FROM `menu` WHERE `url` = 'woocommerce_integration'",
        	1=> "DROP TABLE IF EXISTS `woocommerce_config`;",
        	2=> "DROP TABLE IF EXISTS `woocommerce_product`;"
        );  
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }

 


}
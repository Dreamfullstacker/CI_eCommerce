<?php
/*
Addon Name: Hidden Interest Explorer
Unique Name: hidden_interest_explorer
Modules:
{
   "3003":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"0",
      "extra_text":"",
      "module_name":"Hidden Interest Explorer"
   }
}
Project ID: 1013
Addon URI: https://nvxgroup.com
Author: MD
Author URI: https://nvxgroup.com
Version: 1.7
Description: Hidden Interest Explorer
*/


require_once("application/controllers/Home.php"); // loading home controller
class Marketing extends Home
{
    public $key = "3D7A672F0211397F";
	private $product_id = "1";
	private $product_base = "InterestExplorer";
	private $server_host = "https://nvxgroup.com/wp-json/licensor/";
	private $nvx_version = 1.7;
	/* @var self*/
    private static $selfobj=null;

	public $addon_data=array();
    public function __construct()
    {
        parent::__construct();
        //$this->load->config('page_response_config');// config
        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $addondata=$this->get_addon_data($addon_path); 
        $this->load->config('marketing_config');// config
        $this->addon_data=$addondata;
        $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it
        $function_name=$this->uri->segment(2);
        if($function_name!="webhook_callback")
        {
             // all addon must be login protected
              //------------------------------------------------------------------------------------------
              if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');          
              // if you want the addon to be accessed by admin and member who has permission to this addon
              //-------------------------------------------------------------------------------------------


                if($this->session->userdata('user_type') != 'Admin' && !in_array(3003,$this->module_access))
                   {

                        redirect('home/access_forbidden', 'location');
                        exit();
                   }
                   
              }

        $interest_lt = $this->basic->get_data("users",array("where"=>array("id"=>$this->session->userdata('user_id'))));
        
        if($function_name!="activate"){
                if(isset($interest_lt[0]['interest']) AND $interest_lt[0]['interest']==0){
                    $this->member_validity();
                }
        }
        
        $addon_lang = 'marketing';
        if (file_exists(APPPATH.'modules/'.$addon_lang.'/language/'.$this->language.'/'.$addon_lang.'_lang.php')) {
            $this->lang->load($addon_lang,$this->language,FALSE,TRUE,APPPATH.'modules/'.$addon_lang.'/language/'.$this->language); 
        } else {
            $this->lang->load($addon_lang,'english',FALSE,TRUE,APPPATH.'modules/'.$addon_lang.'/language/english'); 
        }
        

        if (file_exists(APPPATH.'modules/'.$addon_lang.'/language/'.$this->language.'/'.$addon_lang.'_custom_lang.php')) {
            $this->lang->load($addon_lang.'_custom',$this->language,FALSE,TRUE,APPPATH.'modules/'.$addon_lang.'/language/'.$this->language); 
        }
        

    }
    
    
    public function run_curl_for_fb($url)
	{
		$headers = array("Content-type: application/json"); 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		$results=curl_exec($ch); 	   
		return  $results;   
	}
    

        
    public function get_interest_search(){
        $iid = 0;        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }
        
        $domain_name = $this->input->post('domain_name', true);
        $language = $this->input->post('language', true);
        $ad_type = $this->input->post('type_int', true);
        $action_button = $this->input->post('get_button', true);
        
        $token = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->session->userdata('user_id'))));
        
if(!empty($token[0]['access_token'])){
 $data['page_info'] = 1;
 
 if(empty($domain_name)){
	echo json_encode(array('status'=>'0','message'=>$this->lang->line('No keyword found')));
    exit();
 }
 $domain_name = str_replace(' ','%20', $domain_name);
 
 $ad_explode = explode(':',$ad_type);
 if(isset($ad_explode[1])){
    $ad_type = 'type='.$ad_explode[0].'&class='.$ad_explode[1];
 }elseif($ad_type=='adinterestsuggestion' OR $ad_type=='adinterestvalid'){
    $ad_type = 'type='.$ad_type.'&interest_list='.json_encode(array($domain_name));
 }elseif($ad_type=='adgeolocation'){
    $ad_type = 'type='.$ad_type.'&location_types=["zip"]';
 }else{
    $ad_type = 'type='.$ad_type;
 }
 $debug = $ad_type;
$url="https://graph.facebook.com/v9.0/search?".$ad_type."&q={$domain_name}&access_token={$token[0]['access_token']}&limit=1000&locale={$language}";
//echo $url;
		$results= $this->run_curl_for_fb($url);

	$data = json_decode($results,TRUE);
		if(!empty($data['error'])){
		    echo json_encode(array('status'=>'0','message'=>$data['error']['message']));
            exit();
		}
		
		//var_dump($data);
		if(!empty($data['data'])){


		
		$ad_type_switch = explode('&', $ad_type);
		
				    if($action_button=="get_csv"){$this->get_csv($data, $ad_type_switch, $ad_type);}
		
		switch($ad_type_switch[0]){
		
		case 'type=adinterest';
		case 'type=adinterestsuggestion';
		    $size_type = $this->lang->line('Audience Size');
		    $keywords = '<div class="row" style="border-bottom:1px solid #000; font-weight:bold;">
		    <div class="col-sm-4 text-left">'.$this->lang->line('Keywords').'</div>
		    <div class="col-sm-2 text-left">'.$size_type.'</div>
		    <div class="col-sm-4 text-left">'.$this->lang->line('Category').'</div>
		    <div class="col-sm-2 text-left">'.$this->lang->line('Topic').'</div>
		    </div>';
		  break;
		        
		        default;
		        $size_type = $this->lang->line('Audience Size');
		        $keywords = '<div class="row" style="border-bottom:1px solid #000; font-weight:bold;"><div class="col-sm-6 text-left">'.$this->lang->line('Keywords').'</div><div class="col-sm-6 text-left">'.$size_type.'</div></div>';
		         
		        break;
		}
		        
		usort($data['data'], function($a, $b) {
            //return $a['audience_size'] - $b['audience_size'];
            if(!isset($a['audience_size'])){
                $a['audience_size']=0;
            }
            if(!isset($b['audience_size'])){
                $b['audience_size']=0;
            }
            if ($a['audience_size'] == $b['audience_size']) {
                return 0;
            }
            return ($a['audience_size'] > $b['audience_size']) ? -1 : 1;
        });	
        
        if($ad_type_switch[0]=='type=adgeolocation'){
            usort($data['data'], function ($item1, $item2) {
                return $item1['key'] <=> $item2['key'];
            });
        }
        
        $counter_list = 0;
		foreach($data['data'] as $k => $v){
		
		switch($ad_type_switch[0]){
		
		case 'type=adinterest';
		case 'type=adinterestsuggestion';
		
		        $size = $v['audience_size'];
		        $name = $v['name'];
		        //.(isset($v['path'][2]) ? ' => '.$v['path'][2] : '')
		        if(isset($v['path'][0])){
		            $cat = $v['path'][0].(isset($v['path'][1]) ? ' => '.$v['path'][1] : '');
		        }else{
		            $cat = "--";
		        }
		        
		        
		        $url_fb = 'https://www.facebook.com/search/top/?q='.str_replace(' ','%20', $name);
		        $url_google = 'https://www.google.com/search?q='.str_replace(' ','%20', $name);
		        
		        $keywords .= '<div class="row">
		        
		        <div class="col-sm-4 text-left">
		            <div style="float:left; padding-right:10px;">
		                  <input 
                              type="checkbox" 
                              class="checksel" 
                              id="'.$v['id'].'" 
                              value="'.$name.'" 
                              name="checksel" 
                              data-fb="'.$url_fb.'"
                              data-gl="'.$url_google.'"
                              data-audience="'.$size.'"
                              data-topic="'.(isset($v['topic']) ? $v['topic'] : '').(isset($v['disambiguation_category']) ? ' ('.$v['disambiguation_category'].')' : '').'"
                              data-cat="'.$cat.'"
		                  />
		            </div>
		            <span id="copy_'.$v['id'].'" class="tooltipcs" onclick="copyToClipboard(\'span#copy_'.$v['id'].'\')" data-toggle="tooltip" title="" data-original-title="'.$this->lang->line('Click to copy').'">'.$name.'</span> 
		            <div style="float:right">
		                <a target="_blank" href="'.$url_fb.'"><i class="fa fa-facebook-square"></i></a>
		                <a target="_blank" href="'.$url_google.'"><i class="fa fa-google"></i></a>
		            </div>
		        </div>
		        <div class="col-sm-2 text-left">'.$size.'</div>
		        <div class="col-sm-4 text-left">'.$cat.'</div>
		        <div class="col-sm-2 text-left">'.(isset($v['topic']) ? $v['topic'] : '').(isset($v['disambiguation_category']) ? ' ('.$v['disambiguation_category'].')' : '').'</div>
		        </div>';
		        
		        
		        break;
		        
		        case 'type=adgeolocation';
		        
		            if(empty($v['id'])){$iid+=1; $v['id']=$iid;}

                    $size = $v['country_code'].(isset($v['primary_city']) ? ' => '.$v['primary_city'] : '');
                 
                    $name = $v['key'];
                    if(isset($v['subtext'])){
                        $name = $v['name'].' ('.$v['subtext'].')';
                    }
                    $keywords .= '<div class="row"><div class="col-sm-6 text-left"><div style="float:left; padding-right:10px;">
                              <input 
                                  type="checkbox" 
                                  class="checksel" 
                                  id="'.$v['id'].'" 
                                  value="'.$name.'" 
                                  name="checksel" 
                                  data-coverage="'.$size.'"
                              />
                        </div><span id="copy_'.$v['id'].'" class="tooltipcs" onclick="copyToClipboard(\'span#copy_'.$v['id'].'\')" data-toggle="tooltip" title="" data-original-title="'.$this->lang->line('Click to copy').'">'.$name.'</span></div><div class="col-sm-6 text-left">'.$size.'</div></div>';
		        
		        break;
		        
		        
		        default;
		        
		        if(empty($v['id'])){$iid+=1; $v['id']=$iid;}
		        
		        if(isset($v['audience_size'])){
		            $size = $v['audience_size'];
		        }elseif(isset($v['coverage'])){
		            $size = $v['coverage'];
		        }else{
		            $size = '--';
		        }
		         
		        $name = $v['name'];
		        if(isset($v['subtext'])){
		            $name = $v['name'].' ('.$v['subtext'].')';
		        }
		        $keywords .= '<div class="row"><div class="col-sm-6 text-left"><div style="float:left; padding-right:10px;">
		                  <input 
                              type="checkbox" 
                              class="checksel" 
                              id="'.$v['id'].'" 
                              value="'.$name.'" 
                              name="checksel" 
                              data-coverage="'.$size.'"
		                  />
		            </div><span id="copy_'.$v['id'].'" class="tooltipcs" onclick="copyToClipboard(\'span#copy_'.$v['id'].'\')" data-toggle="tooltip" title="" data-original-title="'.$this->lang->line('Click to copy').'">'.$name.'</span></div><div class="col-sm-6 text-left">'.$size.'</div></div>';
		        
		       
		        
		        break;
		        
		    
		
		
		}
		
		

	// 	    if($ad_type!='type=adinterest' AND !isset($ad_explode[1])){
// 		        
// 		    }else{
// 		        
// 		    }

$counter_list = $counter_list+1;
// 			
		}
		}else{
		 $keywords = $this->lang->line('Put only one keyword or not found');
		 		    echo json_encode(array('status'=>'0','message'=>$this->lang->line('Put only one keyword or not found')));
            exit();
		}
		
		echo json_encode(array('status'=>'1','message'=>"<i class='fa fa-check'></i> ".$this->lang->line('Keywords downloaded.'),'js_code'=>$keywords, 'count' => $counter_list));
		
}else{
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Account not found.')));
            exit();

}

        } 
        
    public function get_interest(){
		
		$data['body'] = 'Interest_Explorer';
        $data['page_title'] = $this->lang->line('Interest Explorer');
        $data['sdk_locale']=$this->sdk_locale();
        $data['config_sdk_locale'] = $this->config->item('nvx_marketing_sdk_locale_value');

        $token = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->session->userdata('user_id'))));
        
if(!empty($token[0]['access_token'])){
 $data['page_info'] = 1;
 
}
		
		 $this->_viewcontroller($data);   
		
        } 
        
    protected function sdk_locale()
    {
        $config = array(
            'af_ZA' => 'Afrikaans',
            'ar_AR' => 'Arabic',
            'az_AZ' => 'Azerbaijani',
            'be_BY' => 'Belarusian',
            'bg_BG' => 'Bulgarian',
            'bn_IN' => 'Bengali',
            'bs_BA' => 'Bosnian',
            'ca_ES' => 'Catalan',
            'cs_CZ' => 'Czech',
            'cy_GB' => 'Welsh',
            'da_DK' => 'Danish',
            'de_DE' => 'German',
            'el_GR' => 'Greek',
            'en_GB' => 'English (UK)',
            'en_PI' => 'English (Pirate)',
            'en_UD' => 'English (Upside Down)',
            'en_US' => 'English (US)',
            'eo_EO' => 'Esperanto',
            'es_ES' => 'Spanish (Spain)',
            'es_LA' => 'Spanish',
            'et_EE' => 'Estonian',
            'eu_ES' => 'Basque',
            'fa_IR' => 'Persian',
            'fb_LT' => 'Leet Speak',
            'fi_FI' => 'Finnish',
            'fo_FO' => 'Faroese',
            'fr_CA' => 'French (Canada)',
            'fr_FR' => 'French (France)',
            'fy_NL' => 'Frisian',
            'ga_IE' => 'Irish',
            'gl_ES' => 'Galician',
            'he_IL' => 'Hebrew',
            'hi_IN' => 'Hindi',
            'hr_HR' => 'Croatian',
            'hu_HU' => 'Hungarian',
            'hy_AM' => 'Armenian',
            'id_ID' => 'Indonesian',
            'is_IS' => 'Icelandic',
            'it_IT' => 'Italian',
            'ja_JP' => 'Japanese',
            'ka_GE' => 'Georgian',
            'km_KH' => 'Khmer',
            'ko_KR' => 'Korean',
            'ku_TR' => 'Kurdish',
            'la_VA' => 'Latin',
            'lt_LT' => 'Lithuanian',
            'lv_LV' => 'Latvian',
            'mk_MK' => 'Macedonian',
            'ml_IN' => 'Malayalam',
            'ms_MY' => 'Malay',
            'nb_NO' => 'Norwegian (bokmal)',
            'ne_NP' => 'Nepali',
            'nl_NL' => 'Dutch',
            'nn_NO' => 'Norwegian (nynorsk)',
            'pa_IN' => 'Punjabi',
            'pl_PL' => 'Polish',
            'ps_AF' => 'Pashto',
            'pt_BR' => 'Portuguese (Brazil)',
            'pt_PT' => 'Portuguese (Portugal)',
            'ro_RO' => 'Romanian',
            'ru_RU' => 'Russian',
            'sk_SK' => 'Slovak',
            'sl_SI' => 'Slovenian',
            'sq_AL' => 'Albanian',
            'sr_RS' => 'Serbian',
            'sv_SE' => 'Swedish',
            'sw_KE' => 'Swahili',
            'ta_IN' => 'Tamil',
            'te_IN' => 'Telugu',
            'th_TH' => 'Thai',
            'tl_PH' => 'Filipino',
            'tr_TR' => 'Turkish',
            'uk_UA' => 'Ukrainian',
            'vi_VN' => 'Vietnamese',
            'zh_CN' => 'Chinese (China)',
            'zh_HK' => 'Chinese (Hong Kong)',           
            'zh_TW' => 'Chinese (Taiwan)',
        );
        asort($config);
        return $config;
    }
    
    

        
        
        private function download_send_headers($filename) {
            // disable caching
            $now = gmdate("D, d M Y H:i:s");
            header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
            header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
            header("Last-Modified: {$now} GMT");

            // force download  
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Content-type: text/csv');

            // disposition / encoding on response body
            header("Content-Disposition: attachment;filename={$filename}");
            header("Content-Transfer-Encoding: binary");
        }
        

        private function doCSV($data = array()){
            ob_start();
            $fp =  fopen("php://output", 'w');
    
            $i = 0;
            foreach ($data as $fields) {
                if($i == 0){
                    fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
                    fputcsv($fp, array_keys($fields), ';');
                }
                fputcsv($fp, array_values($fields), ';');
                $i++;
            }

            fclose($fp);
            return ob_get_clean();
        }
        
        
        private function get_csv($data, $ad_type_switch, $ad_type){
        
            $ad_type_switch = explode('&', $ad_type);
            $keywords = array();
        
//             switch($ad_type_switch[0]){
//         
//                 case 'type=adinterest';
//                 case 'type=adinterestsuggestion';
//                     $size_type = $this->lang->line('Audience Size');
// 
//                     $keywords['Keywords'] = $this->lang->line('Keywords');
//                     $keywords['Size'] = $size_type;
//                     $keywords['Category'] = $this->lang->line('Category');
//                     $keywords['Topic'] = $this->lang->line('Topic');
//                     $keywords['Facebook'] = 'Facebook';
//                     $keywords['Google'] = 'Google';
//                   break;
//                 
//                     default;
//                     $size_type = $this->lang->line('Audience Size');
//                     
//                     $keywords['Keywords'] = 'Keywords';
//                     $keywords['Size'] = $size_type;
//                  
//                     break;
//             }
                
            usort($data['data'], function($a, $b) {
                //return $a['audience_size'] - $b['audience_size'];
                if(!isset($a['audience_size'])){
                    $a['audience_size']=0;
                }
                if(!isset($b['audience_size'])){
                    $b['audience_size']=0;
                }
                if ($a['audience_size'] == $b['audience_size']) {
                    return 0;
                }
                return ($a['audience_size'] > $b['audience_size']) ? -1 : 1;
            });	
        
            foreach($data['data'] as $k => $v){
        
                switch($ad_type_switch[0]){
        
                    case 'type=adinterest';
                    case 'type=adinterestsuggestion';
                            $size_type = $this->lang->line('Audience Size');
                            $size = $v['audience_size'];
                            $name = $v['name'];
                            //.(isset($v['path'][2]) ? ' => '.$v['path'][2] : '')
                            if(isset($v['path'][0])){
                                $cat = $v['path'][0].(isset($v['path'][1]) ? ' => '.$v['path'][1] : '');
                            }else{
                                $cat = "--";
                            }
                
                
                            $url_fb = 'https://www.facebook.com/search/top/?q='.str_replace(' ','%20', $name);
                            $url_google = 'https://www.google.com/search?q='.str_replace(' ','%20', $name);
                

                            
                        $keyword = array();
                        $keyword['Keywords'] = $name;
                        $keyword[$size_type] = $size;
                        $keyword['Category'] = $cat;
                        $keyword['Topic'] = (isset($v['topic']) ? $v['topic'] : '').(isset($v['disambiguation_category']) ? ' ('.$v['disambiguation_category'].')' : '');
                        $keyword['Facebook'] = $url_fb;
                        $keyword['Google'] = $url_google;
                    
                        $keywords[] = $keyword;
                
                    break;
                
                    default;
                
                        if(isset($v['audience_size'])){
                            $size = $v['audience_size'];
                            $size_type = 'audience_size';
                        }elseif(isset($v['coverage'])){
                            $size = $v['coverage'];
                            $size_type = 'coverage';
                        }else{
                            $size = '--';
                            $size_type = 'size';
                        }
                 
                        $name = $v['name'];
                        if(isset($v['subtext'])){
                            $name = $v['name'].' ('.$v['subtext'].')';
                        }

                        
                    $keyword = array();
                    $keyword['Keywords'] = $name;
                    $keyword[$size_type] = $size;
                    
                    $keywords[] = $keyword;
                
                
                    break;
                
                }
    
            }
            
            //$this->download_send_headers("data_export_" . date("Y-m-d") . ".csv");
            
            echo json_encode(array('status'=>'1','message'=>"<i class='fa fa-check'></i> ".$this->lang->line('Keywords downloaded.'),'js_code'=>$this->doCSV($keywords)));
            //echo $this->doCSV($keywords);
            die();
            
        
        
        }

	public function activate()
    {
        $this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $purchase_code=$this->input->post('purchase_code');
     
          
        //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
         $sidebar=array();
        // mysql raw query needed to run, it's an array, put each query in a seperate index, create table query must should IF NOT EXISTS
        $sql=array
        (
            //1=>"INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`) VALUES (NULL, 'Hidden Interest Explorer', '3003', '', '1', '0', '0')",
        
        
//             1=> "INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Interest Explorer', 'fa fa-layer-group', 'marketing/get_interest', '1013', '3003', '0', '0', '0', '0', '0', '', '0', '0')",
            
            0=>"ALTER TABLE `users` ADD `interest` int(1) NOT NULL default '0';",

        );
        
        
        $menu_exists = $this->db->query(" SELECT id FROM `menu` where url LIKE '%get_interest%' ")->row_array();
        if(!$menu_exists){
            try{
                $sql_cust = "INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Interest Explorer', 'fa fa-layer-group', 'marketing/get_interest', '1013', '3003', '0', '0', '0', '0', '0', '', '0', '0')" ;
                $this->db->query($sql_cust);
            }catch(Exception $e){

            }
        }
        
        //send blank array if you does not need sidebar entry,send a blank array if your addon does not need any sql to run
        $this->register_addon($addon_controller_name,$sidebar,$sql,$purchase_code);
    }
    
    public function updates(){
        if($this->session->userdata('user_type') != 'Admin'){exit;}
        $sql=array
        (
            1 => "DELETE from `modules` where add_ons_id = 3003",
            
            2 => "DELETE from `menu` where serial = 1013",
        
        );
        if(is_array($sql)){       
            foreach ($sql as $key => $query){
                try{
                    $this->db->query($query);
                }catch(Exception $e){
                
                }                    
            }
        }
        $fp = fopen(APPPATH."modules/".strtolower($this->router->fetch_class())."/install.txt","wb");
        fwrite($fp,'');
        echo 'Please re-active add-on with new key from https://nvxgroup.com';
    }
    
    public function changemenu(){
        if($this->session->userdata('user_type') != 'Admin'){exit;}
        $sql=array
        (   
            1 => "DELETE from `menu` where serial = 1013",
        );
        if(is_array($sql)){       
            foreach ($sql as $key => $query){
                try{
                    $this->db->query($query);
                }catch(Exception $e){
                
                }                    
            }
        }
        
        //read the entire string
        $str=file_get_contents(APPPATH."views/utility/menu_block.php");

        //replace something in the file string - this is a VERY simple example
        $str=str_replace('<div class="row">', '
        <div class="row">  
        
        <div class="col-12 col-lg-6">
              <div class="card card-large-icons">
                  <div class="card-icon text-primary"><i class="fas fa-ad"></i></div>
                  <div class="card-body">
                      <h4><?php echo $this->lang->line("Interest Explorer"); ?></h4>
                      <p><?php echo $this->lang->line("Facebook Hidden Interest Explorer for ADS targeting"); ?></p>
                      <div class="dropdown">
                          <a href="<?php echo base_url(\'marketing/get_interest\'); ?>" class="no_hover"><?php echo $this->lang->line("Actions"); ?> <i class="fas fa-chevron-right"></i></a>
                      </div>
                  </div>
              </div>
          </div>

        
        
        ',$str);

        //write the entire string
        file_put_contents(APPPATH."views/utility/menu_block.php", $str);
        echo 'Menu removed, added to Search Tools';
    }
    
    public function deactivate(){        
        echo json_encode(array('status'=>'0','message'=>$this->lang->line('For deactivate addon please use our NVX Addon Manager. Download: https://nvxgroup.com/addon-manager/')));
        exit();   
    }
    
    public function delete(){        
        $this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $add_inf = $this->get_addon_data(APPPATH.'modules/'.$this->router->fetch_class().'/controllers/'.$addon_controller_name.'.php');
        if($add_inf['installed']==1){
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Please first deactivate addon using our NVX Addon Manager. Download: https://nvxgroup.com/addon-manager/')));
            exit();   
        }
        
        
        // mysql raw query needed to run, it is an array, put each query in a seperate index, drop table/column query should have IF EXISTS
        $sql=array
        (
          0 => "DELETE from `menu` where module_access = 3003",
        );  
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }
    
        
    public function fix_menu(){
    
        $sql=array
        (   
            1 => "DELETE from `menu` where module_access = 3003",
        );
        if(is_array($sql)){       
            foreach ($sql as $key => $query){
                try{
                    $this->db->query($query);
                }catch(Exception $e){
                
                }                    
            }
        }
    
        $menu_exists = $this->db->query(" SELECT id FROM `menu` where url LIKE '%marketing%' ")->row_array();
        $parent_id_to_add = $this->db->query(" SELECT serial FROM `menu` where url LIKE '%search_tools%' ")->row_array();
        if(!$menu_exists){
            try{
                $sql = "INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Interest Explorer', 'fa fa-layer-group', 'marketing/get_interest', ".($parent_id_to_add['serial']+1).", '3003', '0', '0', '0', '0', '0', '', '0', '0')" ;
                $this->db->query($sql);
            }catch(Exception $e){

            }
        }
        
        echo 'Done';
    
    }

    
///

    private function nvx_lic($purchase_code){
        $error = '';
        $param    = $this->getParam( $purchase_code );
        $response = $this->_request( 'product/active/'.$this->product_id, $param, $error );

        return $response->status;
    }
    
    private function encrypt($plainText,$password='') {
        if(empty($password)){
            $password=$this->key;
        }
        $plainText=rand(10,99).$plainText.rand(10,99);
        $method = 'aes-256-cbc';
        $key = substr( hash( 'sha256', $password, true ), 0, 32 );
        $iv = substr(strtoupper(md5($password)),0,16);
        return base64_encode( openssl_encrypt( $plainText, $method, $key, OPENSSL_RAW_DATA, $iv ) );
    }
    
    private function decrypt($encrypted,$password='') {
        if(empty($password)){
      		$password=$this->key;
      	}
        $method = 'aes-256-cbc';
        $key = substr( hash( 'sha256', $password, true ), 0, 32 );
        $iv = substr(strtoupper(md5($password)),0,16);
        $plaintext=openssl_decrypt( base64_decode( $encrypted ), $method, $key, OPENSSL_RAW_DATA, $iv );
        return substr($plaintext,2,-2);
    }
    
     private function processs_response($response){
        $resbk="";
          if ( ! empty( $response ) ) {
              if ( ! empty( $this->key ) ) {
                $resbk=$response;
                  $response = $this->decrypt( $response );
              }
              $response = json_decode( $response );

              if ( is_object( $response ) ) {
                  return $response;
              } else {
                $response=new stdClass();
                $response->status = false;
                $bkjson=@json_decode($resbk);
                if(!empty($bkjson->msg)){
                    $response->msg    = $bkjson->msg;
                }else{
                    $response->msg    = "Response Error, contact with the author or update the plugin or theme";
                }
                  $response->data = NULL;
                  return $response;

              }
          }
          $response=new stdClass();
          $response->msg    = "unknown response";
          $response->status = false;
          $response->data = NULL;

          return $response;
    }
    
    private function _request( $relative_url, $data, &$error = '' ) {
        $response         = new stdClass();
        $response->status = false;
        $response->msg    = "Empty Response";
        $curl             = curl_init();
        $finalData        = json_encode( $data );
        if ( ! empty( $this->key ) ) {
            $finalData = $this->encrypt( $finalData );
        }
        $url = rtrim( $this->server_host, '/' ) . "/" . ltrim( $relative_url, '/' );

        //curl when fall back
        curl_setopt_array( $curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $finalData,
            CURLOPT_HTTPHEADER     => array(
                "Content-Type: text/plain",
                "cache-control: no-cache"
            ),
        ) );
        $serverResponse = curl_exec( $curl );
        //echo $response;
        $error = curl_error( $curl );
        curl_close( $curl );
        if ( ! empty( $serverResponse ) ) {
            return $this->processs_response($serverResponse);
        }
        $response->msg    = "unknown response";
        $response->status = false;
        $response->data = NULL;
        return $response;
    }

    private function getParam( $purchase_key ) {
        $req               = new stdClass();
        $req->license_key  = $purchase_key;
        // $req->email        = ! empty( $admin_email ) ? $admin_email : $this->getEmail();
        $req->domain       = $this->getDomain();
        $req->app_version  = $this->nvx_version;
        $req->product_id   = $this->product_id;
        $req->product_base = $this->product_base;

        return $req;
    }
    
     private function getDomain() {
	    $base_url = ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == "on" ) ? "https" : "http" );
	    $base_url .= "://" . $_SERVER['HTTP_HOST'];
	    $base_url .= str_replace( basename( $_SERVER['SCRIPT_NAME'] ), "", $_SERVER['SCRIPT_NAME'] );
	    return $base_url;

    }   
    

    
    public function testing(){

    
    }

///


}
<?php
/*
Addon Name: Ecommerce Product Rating & Comment
Unique Name: ecommerce_review_comment
Modules:
{}
Project ID: 48
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 1.0
Description: 
*/

require_once("application/controllers/Home.php"); // loading home controller

class Ecommerce_review_comment extends Home
{
	public $addon_data=array();
    public $login_to_continue;
    public function __construct()
    {
        parent::__construct();
        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $this->addon_data=$this->get_addon_data($addon_path);
        $this->login_to_continue = $this->lang->line("Please login to continue.");
        $function_name=$this->uri->segment(2);
        if($function_name!="activate" && $function_name!="deactivate" && $function_name!="delete") 
        if(!$this->basic->is_exist("add_ons",array("project_id"=>48))) exit();
    }


    public function hide_comment()
    {
        $this->ajax_check();
        $id = $this->input->post("id",true);
        $product_id = $this->input->post("product_id",true);
        $store_id = $this->input->post("store_id",true);

        $check_admin = $this->basic->count_row("ecommerce_store",array("where"=>array("id"=>$store_id,"user_id"=>$this->user_id)),"id");
        if($check_admin[0]['total_rows']==0)
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Only store admin can hide a comment.")));
            exit();
        }
        $datetime = date("Y-m-d H:i:s");
        $this->basic->update_data("ecommerce_product_comment",array("id"=>$id),array("hidden"=>"1","hidden_by_user_id"=>$this->user_id,"last_updated_at"=>$datetime,"hidden_at"=>$datetime));
        echo json_encode(array("status"=>"1","message"=>""));
    }

    public function hide_review()
    {
        $this->ajax_check();
        $id = $this->input->post("id",true);
        $product_id = $this->input->post("product_id",true);
        $store_id = $this->input->post("store_id",true);

        $check_admin = $this->basic->count_row("ecommerce_store",array("where"=>array("id"=>$store_id,"user_id"=>$this->user_id)),"id");
        if($check_admin[0]['total_rows']==0)
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Only store admin can hide a review.")));
            exit();
        }
        $datetime = date("Y-m-d H:i:s");
        $this->basic->update_data("ecommerce_product_review",array("id"=>$id),array("hidden"=>"1","hidden_by_user_id"=>$this->user_id,"hidden_at"=>$datetime));
        echo json_encode(array("status"=>"1","message"=>$this->lang->line("Review has been hidden successfully.")));
    }


    public function new_comment()
    {
        $this->ajax_check();
        $parent_product_comment_id = $this->input->post("parent_product_comment_id",true);
        $product_id = $this->input->post("product_id",true);
        $store_id = $this->input->post("store_id",true);
        $new_comment = $this->input->post("new_comment",true);
        $new_comment = strip_tags($new_comment);
        $product_name = strip_tags($this->input->post("product_name",true));
        $need_to_login = false;

        if($this->session->userdata("logged_in")=='1')
        {
            $check_admin = $this->basic->count_row("ecommerce_store",array("where"=>array("id"=>$store_id,"user_id"=>$this->user_id)),"id");
            if($check_admin[0]['total_rows']==0) $need_to_login = true;
        }
        else
        {
            $where_subs = array();
            $subscriber_id=$this->session->userdata($store_id."ecom_session_subscriber_id");
            if($subscriber_id!="") $where_subs = array("subscriber_type"=>"system","subscribe_id"=>$subscriber_id,"store_id"=>$store_id);
            else
            {
                if($subscriber_id=="") $subscriber_id = $this->input->post("subscriber_id",true);
                if($subscriber_id!="") $where_subs = array("subscriber_type!="=>"system","subscribe_id"=>$subscriber_id);
            }
            if($subscriber_id=="") $need_to_login = true;
            else
            {
                $subscriber_info = $this->basic->count_row("messenger_bot_subscriber",array("where"=>$where_subs),"id");
                if($subscriber_info[0]['total_rows']==0) $need_to_login = true;
            }
        }

        if($need_to_login)
        {
            echo json_encode(array('status'=>'0','message'=>$this->login_to_continue,"login_popup"=>'1'));
            exit();
        }

        $store_data = $this->basic->get_data("ecommerce_store",array("where"=>array("id"=>$store_id)),array("user_id","store_name","store_favicon"));
        $store_name = isset($store_data[0]["store_name"])?$store_data[0]["store_name"]:"";
        $store_favicon = isset($store_data[0]["store_favicon"])?$store_data[0]["store_favicon"]:"";
        $store_admin_user_id = isset($store_data[0]["user_id"])?$store_data[0]["user_id"]:"0";

        $datetime = date("Y-m-d H:i:s");
        $subscriber_id = $this->input->post("subscriber_id",true);

        $anouncement_id = 0;
        $direct_link = "";       

        $insert_data = array
        (
            "store_id"=>$store_id,
            "product_id"=>$product_id,
            "subscriber_id"=>$subscriber_id,
            "commented_by_user_id"=>$this->user_id,
            "comment_text"=>$new_comment,
            "parent_product_comment_id"=>$parent_product_comment_id,
            "inserted_at"=>$datetime,
            "last_updated_at"=>$datetime
        );
        $new_comment = preg_replace("/(https?:\/\/[a-zA-Z0-9\-._~\:\/\?#\[\]@!$&'\(\)*+,;=]+)/", '<a target="_BLANK" href="$1">$1</a>', $new_comment); // find and replace links with ancor tag
        if($parent_product_comment_id=="") unset($insert_data["parent_product_comment_id"]);
        if($this->user_id!="") unset($insert_data["subscriber_id"]);
        else unset($insert_data["commented_by_user_id"]);
        $html='';
        if($this->basic->insert_data("ecommerce_product_comment",$insert_data))
        {            
            $insert_id = $this->db->insert_id();
            $direct_link = base_url("ecommerce/comment/".$insert_id);           

            if($this->user_id!="")
            {
                $commenter = $store_name." <i class='fas fa-user-circle text-primary'></i>";                   

                if(!empty($store_favicon)) $image_path = "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url('upload/ecommerce/'.$store_favicon)."'>";
                else $image_path = "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url('assets/img/avatar/avatar-1.png')."'>";
            }
            else
            {
                $subscriber_info=$this->basic->get_data("messenger_bot_subscriber",array("where"=>array("subscribe_id"=>$subscriber_id)),array("first_name","last_name","profile_pic","image_path"));
                $first_name = isset($subscriber_info[0]['first_name']) ? $subscriber_info[0]['first_name'] : "";
                $last_name = isset($subscriber_info[0]['last_name']) ? $subscriber_info[0]['last_name'] : "";
                $profile_pic_src = isset($subscriber_info[0]['profile_pic']) ? $subscriber_info[0]['profile_pic'] : "";
                $image_path_src = isset($subscriber_info[0]['image_path']) ? $subscriber_info[0]['image_path'] : "";
                $commenter = $first_name." ".$last_name;

                $profile_pic = ($profile_pic_src!="") ? "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".$profile_pic_src."'>" :  "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url('assets/img/avatar/avatar-1.png')."'>";
                $image_path=($image_path_src!="") ? "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url($image_path_src)."'>" : $profile_pic;
            }

            $description = $this->lang->line("Hello")." ".$store_name." ".$this->lang->line("admin").",<br><br>";
            $description .= "<b>".$commenter."</b> ".$this->lang->line("just commented on ecommerce store")." <b>".$store_name."</b> <i>@".$product_name."</i><br><br><blockquote>".$new_comment."</blockquote>";
            $description .= $this->lang->line("You can reply this comment here")." : ".$direct_link."<br><br>".$this->lang->line("Thanks");             
            
            if($this->user_id != $store_admin_user_id)
            {
                $announcement_insert = array
                (
                    'title'=> $this->lang->line("New comment on ecommerce store")." : ".$store_name,
                    'description'=> $description,
                    'status'=>"published",
                    'created_at'=>$datetime,
                    'user_id' => $store_admin_user_id,
                    'color_class' => 'info',
                    'icon' => 'fas fa-shopping-cart'
                );
                $this->basic->insert_data("announcement",$announcement_insert);
                $anouncement_id = $this->db->insert_id();
            }

            $hide_link = "";
            $hide_lang = empty($parent_product_comment_id) ? " ".$this->lang->line("Hide") : "";
            $hide_class = empty($parent_product_comment_id) ? "pr-3" : "";
            if($this->user_id!='') $hide_link = '<a data-id="'.$insert_id.'" class="d-inline float-right '.$hide_class.' hide-comment text-muted" href="#"><i class="fas fa-eye-slash"></i>'.$hide_lang.'</a>';
            $divId = "collapse".$insert_id;
            if($subscriber_id!="") $direct_link.="?subscriber_id=".$subscriber_id;
                                
            if(empty($parent_product_comment_id))
            {
                $html.='
                <div class="media mb-2 mt-2 w-100 p-0" id="comment-'.$insert_id.'">
                    '.$image_path.'
                    <div class="media-body">
                      <h6 class="mt-1 mb-0">'.$commenter.'</h6>
                      <p class="m-0 small d-inline"><a target="_BLANK" href="'.$direct_link.'" target="_BLANK">'.date("d M,y H:i",strtotime($datetime)).'</a></p>
                      <a class="collpase_link d-inline float-right" data-toggle="collapse" href="#'.$divId.'" role="button" aria-expanded="false" aria-controls="'.$divId.'"><i class="fas fa-comment"></i> '.$this->lang->line("Reply").'</a>
                      '.$hide_link.'
                      <p class="mb-0 text-justify">'.nl2br($new_comment).'</p>                  
                      <div class="input-group collapse pt-2" id="'.$divId.'">
                        <textarea class="form-control comment_reply" name="comment_reply" style="height:50px !important;"></textarea>
                        <button class="btn btn-primary btn-lg leave_comment no_radius" parent-id='.$insert_id.'><i class="fas fa-reply"></i> '.$this->lang->line("Reply").'</button>              
                      </div>
                    </div>
                </div>'; 
            }
            else
            {
                $html .= '
                <div class="media mt-3 w-100">
                    '.$image_path.'
                    <div class="media-body">
                      <h6 class="mt-1 mb-0">'.$commenter.'</h6>
                      <p class="m-0 small text-muted d-inline">'.date("d M,y H:i",strtotime($datetime)).'</p>
                      '.$hide_link.'
                      <p class="mb-0 text-justify">'.nl2br($new_comment).'</p>
                    </div>
                </div>';
            }
        
            echo json_encode(array('status'=>'1','message'=>$html));
        }
        else echo json_encode(array('status'=>'0','message'=>$this->lang->line("Something went wrong.")));
    }
    
    public function new_review()
    {
        $this->ajax_check();
        $this->load->helper("ecommerce");
        $insert_id = $this->input->post("insert_id",true);
        $cart_id = $this->input->post("cart_id",true);
        $product_id = $this->input->post("product_id",true);
        $store_id = $this->input->post("store_id",true);
        $reason = strip_tags($this->input->post("reason",true));
        $rating = $this->input->post("rating",true);
        $review = strip_tags($this->input->post("review",true));
        $product_name = strip_tags($this->input->post("product_name",true));
        $need_to_login =false;
        
        $where_subs = array();
        $subscriber_id=$this->session->userdata($store_id."ecom_session_subscriber_id");
        if($subscriber_id!="") $where_subs = array("subscriber_type"=>"system","subscribe_id"=>$subscriber_id,"store_id"=>$store_id);
        else
        {
            if($subscriber_id=="") $subscriber_id = $this->input->post("subscriber_id",true);
            if($subscriber_id!="") $where_subs = array("subscriber_type!="=>"system","subscribe_id"=>$subscriber_id);
        }
        if($subscriber_id=="") $need_to_login = true;
        else
        {
            $subscriber_info = $this->basic->count_row("messenger_bot_subscriber",array("where"=>$where_subs),"id");
            if($subscriber_info[0]['total_rows']==0) $need_to_login = true;
        }
        

        if($need_to_login)
        {
            echo json_encode(array('status'=>'0','message'=>$this->login_to_continue,"login_popup"=>'1'));
            exit();
        }

        $join_me = array('ecommerce_cart_item'=>"ecommerce_cart_item.cart_id=ecommerce_cart.id,left"); 
        $has_purchase_array = $this->basic->count_row("ecommerce_cart",array("where"=>array("subscriber_id"=>$subscriber_id,"product_id"=>$product_id),"where_not_in"=>array("status"=>array("pending","rejected"))),'count(cart_id) as total_row',$join_me,'cart_id');
        if($has_purchase_array[0]['total_rows']==0)
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("You have not purchased this item.")));
            exit();
        }


        $datetime = date("Y-m-d H:i:s");
        if($cart_id=='') $cart_id = 0;

        $insert_data = array
        (
            "store_id"=>$store_id,
            "cart_id"=>$cart_id,
            "product_id"=>$product_id,
            "subscriber_id"=>$subscriber_id,
            "reason"=>$reason,
            "rating"=>$rating,
            "review"=>$review,
            "review_reply" => "",
            "replied_by_user_id" => 0,
            "inserted_at"=>$datetime
        );

        if($this->basic->is_exist("ecommerce_product_review",array("product_id"=>$product_id,"subscriber_id"=>$subscriber_id),'id'))
        $this->basic->update_data("ecommerce_product_review",array("product_id"=>$product_id,"subscriber_id"=>$subscriber_id),$insert_data);
        else
        {
            $this->basic->insert_data("ecommerce_product_review",$insert_data);
            $insert_id = $this->db->insert_id();
        }


        $store_data = $this->basic->get_data("ecommerce_store",array("where"=>array("id"=>$store_id)),array("user_id","store_name","store_favicon"));
        $store_name = isset($store_data[0]["store_name"])?$store_data[0]["store_name"]:"";
        $store_favicon = isset($store_data[0]["store_favicon"])?$store_data[0]["store_favicon"]:"";
        $store_admin_user_id = isset($store_data[0]["user_id"])?$store_data[0]["user_id"]:"0";

        $subscriber_info=$this->basic->get_data("messenger_bot_subscriber",array("where"=>array("subscribe_id"=>$subscriber_id)),array("first_name","last_name","profile_pic","image_path"));
        $first_name = isset($subscriber_info[0]['first_name']) ? $subscriber_info[0]['first_name'] : "";
        $last_name = isset($subscriber_info[0]['last_name']) ? $subscriber_info[0]['last_name'] : "";
        $commenter = $first_name." ".$last_name;

        $stars = mec_display_rating_starts($rating);

        $direct_link = base_url("ecommerce/review/".$insert_id);
        $invoice_link = base_url("ecommerce/order/".$cart_id);

        $description = $this->lang->line("Hello")." ".$store_name." ".$this->lang->line("admin").",<br><br>";
        $description .= "<b>".$commenter."</b> ".$this->lang->line("just posted a review on ecommerce store")." <b>".$store_name."</b> <i>@".$product_name."</i><br><br><blockquote>".$stars."<b>".$reason."</b> : ".$review."</blockquote>";
        $description .= $this->lang->line("You can reply this review here")." : ".$direct_link."<br><br>";           
        $description .= $this->lang->line("You can see the invoice here")." : ".$invoice_link."<br><br>".$this->lang->line("Thanks");             
        
        if($this->user_id != $store_admin_user_id)
        {
            $announcement_insert = array
            (
                'title'=> $this->lang->line("New review on ")." : ".$product_name,
                'description'=> $description,
                'status'=>"published",
                'created_at'=>$datetime,
                'user_id' => $store_admin_user_id,
                'color_class' => 'dark',
                'icon' => 'fas fa-star orange'
            );
            $this->basic->insert_data("announcement",$announcement_insert);
        }            
        

        echo json_encode(array('status'=>'1','message'=>$this->lang->line("Review has been submitted successfully.")));
    }

    public function new_review_comment()
    {
        $this->ajax_check();
        $parent_product_review_id = $this->input->post("parent_product_review_id",true);
        $product_id = $this->input->post("product_id",true);
        $store_id = $this->input->post("store_id",true);
        $review_reply = $this->input->post("review_reply",true);
        $review_reply = strip_tags($review_reply);
        
        $check_admin = $this->basic->count_row("ecommerce_store",array("where"=>array("id"=>$store_id,"user_id"=>$this->user_id)),"id");
        if($check_admin[0]['total_rows']==0)
        {
             echo json_encode(array('status'=>'0','message'=>$this->lang->line("Only admin can reply a review.")));
             exit();
        }      
      
        $store_data = $this->basic->get_data("ecommerce_store",array("where"=>array("id"=>$store_id)),array("user_id","store_name","store_favicon"));
        $store_name = isset($store_data[0]["store_name"])?$store_data[0]["store_name"]:"";
        $store_favicon = isset($store_data[0]["store_favicon"])?$store_data[0]["store_favicon"]:"";
        $store_admin_user_id = isset($store_data[0]["user_id"])?$store_data[0]["user_id"]:"0";

        $datetime = date("Y-m-d H:i:s");
        $subscriber_id = $this->input->post("subscriber_id",true);

        $anouncement_id = 0;
        $direct_link = "";       

        $insert_data = array
        (            
            "replied_by_user_id"=>$this->user_id,
            "review_reply"=>$review_reply,
            "replied_at"=>$datetime
        );
        $this->basic->update_data("ecommerce_product_review",array("id"=>$parent_product_review_id),$insert_data);
        echo json_encode(array('status'=>'1','message'=>$this->lang->line("Review has been replied successfully.")));
    }

    public function comment_list_data()
    {
        $this->ajax_check();
        $subscriber_id = $this->input->post("subscriber_id",true); // to load a single comment
        $comment_id = $this->input->post("comment_id",true); // to load a single comment
        $product_id = $this->input->post("product_id",true);
        $store_id = $this->input->post("store_id",true);
        $store_favicon = $this->input->post("store_favicon",true);
        $store_name = strip_tags($this->input->post("store_name",true));
        if($store_name=="") $store_name = $this->lang->line("Administrator");
        $start = $this->input->post("start",true);
        $limit = $this->input->post("limit",true);
      
        $select=array("ecommerce_product_comment.*","first_name","last_name","profile_pic","image_path");
        if(empty($comment_id)) $where=array("where"=>array("ecommerce_product_comment.product_id"=>$product_id,"hidden"=>"0","parent_product_comment_id"=>0));
        else $where=array("where"=>array("ecommerce_product_comment.id"=>$comment_id,"hidden"=>"0","parent_product_comment_id"=>0));
        $join=array('messenger_bot_subscriber'=>"messenger_bot_subscriber.subscribe_id=ecommerce_product_comment.subscriber_id,left");    
        $parent_comment_info=$this->basic->get_data("ecommerce_product_comment",$where,$select,$join,$limit,$start,$order_by="id DESC");
        $total_rows_array = $this->basic->count_row("ecommerce_product_comment", $where,$count='ecommerce_product_comment.id',$join);    
        $total_rows =$total_rows_array[0]['total_rows'];
        $parent_ids=array();
        foreach ($parent_comment_info as $key => $value) 
        {
          array_push($parent_ids, $value['id']);
        }
        $parent_ids=array_unique($parent_ids);

        // getting child comments (using same join param and derived parent array)
        $total_comment=count($parent_comment_info);
        $child_comment_info_formatted=array();
        if(!empty($parent_ids))
        {
          $child_comment_info=$this->basic->get_data("ecommerce_product_comment",array("where"=>array("ecommerce_product_comment.product_id"=>$product_id,"hidden"=>"0"),"where_in"=>array("parent_product_comment_id"=>$parent_ids)),$select,$join,$limit='',$start=NULL,$order_by="id ASC");
          foreach ($child_comment_info as $key => $value) 
          {
            $total_comment++;
            $child_comment_info_formatted[$value['parent_product_comment_id']][]=$value;        
          }
        }

        $html='';
        $border_bottom = empty($comment_id) ? "border-bottom" : "";
        foreach ($parent_comment_info as $key => $value)
        {           
            $sub_comments = '';
            $divId = "collapse".$value['id'];
            if(isset($child_comment_info_formatted[$value['id']])) 
            foreach ($child_comment_info_formatted[$value['id']] as $key2 => $value2) 
            {   
                if($value2['subscriber_id']=='') $commenter2 = $store_name." <i class='fas fa-user-circle text-primary'></i>";
                else $commenter2 = $value2["first_name"]." ".$value2["last_name"];
                $profile_pic2 = ($value2['profile_pic']!="") ? "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".$value2["profile_pic"]."'>" :  "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url('assets/img/avatar/avatar-1.png')."'>";
                $image_path2=($value2["image_path"]!="") ? "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url($value2["image_path"])."'>" : $profile_pic2;

                if($value2['subscriber_id']=='' && !empty($store_favicon)) $image_path2 = "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url('upload/ecommerce/'.$store_favicon)."'>";

                $hide_link2 = "";
                if($this->user_id!='') $hide_link2 = '<a data-id="'.$value2['id'].'" class="d-inline float-right hide-comment text-muted" href="#"><i class="fas fa-eye-slash"></i></a>';

                $new_comment_formatted2 = preg_replace("/(https?:\/\/[a-zA-Z0-9\-._~\:\/\?#\[\]@!$&'\(\)*+,;=]+)/", '<a target="_BLANK" href="$1">$1</a>', $value2["comment_text"]); // find and replace links with ancor tag
                $sub_comments .= '
                <div class="media mt-3 w-100">
                    '.$image_path2.'
                    <div class="media-body">
                      <h6 class="mt-1 mb-0">'.$commenter2.'</h6>
                      <p class="m-0 small text-muted d-inline">'.date("d M,y H:i",strtotime($value2['inserted_at'])).'</p>
                      '.$hide_link2.'
                      <p class="mb-0">'. nl2br($new_comment_formatted2).'</p>
                    </div>
                </div>';
            }

            if($value['subscriber_id']=='') $commenter = $store_name." <i class='fas fa-user-circle text-primary'></i>";
            else $commenter = $value["first_name"]." ".$value["last_name"];
            $profile_pic = ($value['profile_pic']!="") ? "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".$value["profile_pic"]."'>" :  "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url('assets/img/avatar/avatar-1.png')."'>";
            $image_path=($value["image_path"]!="") ? "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url($value["image_path"])."'>" : $profile_pic;
            if($value['subscriber_id']=='' && !empty($store_favicon)) $image_path = "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url('upload/ecommerce/'.$store_favicon)."'>";
            $hide_link = "";
            if($this->user_id!='') $hide_link = '<a data-id="'.$value['id'].'" class="d-inline float-right pr-3 hide-comment text-muted" href="#"><i class="fas fa-eye-slash"></i> '.$this->lang->line("Hide").'</a>';
            $new_comment_formatted = preg_replace("/(https?:\/\/[a-zA-Z0-9\-._~\:\/\?#\[\]@!$&'\(\)*+,;=]+)/", '<a target="_BLANK" href="$1">$1</a>', $value["comment_text"]); // find and replace links with ancor tag
            $direct_link = base_url("ecommerce/comment/".$value['id']);
            if($subscriber_id!="") $direct_link.="?subscriber_id=".$subscriber_id;
            $html.='
            <div class="media mb-2 mt-2 w-100 p-0" id="comment-'.$value['id'].'">
                '.$image_path.'
                <div class="media-body">
                  <h6 class="mt-1 mb-0">'.$commenter.'</h6>
                  <p class="m-0 small d-inline"><a target="_BLANK" href="'.$direct_link.'" target="_BLANK">'.date("d M,y H:i",strtotime($value['inserted_at'])).'</a></p>
                  <a class="collpase_link d-inline float-right" data-toggle="collapse" href="#'.$divId.'" role="button" aria-expanded="false" aria-controls="'.$divId.'"><i class="fas fa-comment"></i> '.$this->lang->line("Reply").'</a>
                  '.$hide_link.'
                  <p class="mb-0">'. nl2br($new_comment_formatted).'</p>                  
                  <div class="input-group collapse pt-2" id="'.$divId.'">
                    <textarea class="form-control comment_reply" name="comment_reply" style="height:50px !important;"></textarea>
                    <button class="btn btn-primary btn-lg leave_comment no_radius" parent-id='.$value["id"].'><i class="fas fa-reply"></i> '.$this->lang->line("Reply").'</button>              
                  </div>
                  '.$sub_comments.'
                </div>
            </div>';              
        }
        echo json_encode(array("html"=>$html,"found"=>count($parent_comment_info)));        
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
            0 => "CREATE TABLE IF NOT EXISTS `ecommerce_product_review` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `store_id` int(11) NOT NULL,
                  `cart_id` int(11) NOT NULL,
                  `product_id` int(11) NOT NULL,
                  `subscriber_id` varchar(50) NOT NULL,
                  `reason` varchar(100) NOT NULL,
                  `review` text NOT NULL,
                  `rating` float NOT NULL,
                  `inserted_at` datetime NOT NULL,
                  `featured` enum('0','1') NOT NULL DEFAULT '0',
                  `review_reply` text NOT NULL,
                  `replied_by_user_id` int(11) NOT NULL,
                  `replied_at` datetime NOT NULL,
                  `hidden` enum('0','1') NOT NULL DEFAULT '0',
                  `hidden_by_user_id` int(11) NOT NULL,
                  `hidden_at` datetime NOT NULL,
                  `note` text NOT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `subscriber_id` (`subscriber_id`,`product_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
            1 => "CREATE TABLE IF NOT EXISTS `ecommerce_product_comment` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `store_id` int(11) NOT NULL,
                  `product_id` int(11) NOT NULL,
                  `subscriber_id` varchar(50) NOT NULL,
                  `commented_by_user_id` int(11) NOT NULL COMMENT 'if store admin comment',
                  `comment_text` text NOT NULL,
                  `parent_product_comment_id` int(11) NOT NULL COMMENT 'ecommerce_product_comment.id',
                  `inserted_at` datetime NOT NULL,
                  `note` text NOT NULL,
                  `hidden` enum('0','1') NOT NULL DEFAULT '0',
                  `hidden_by_user_id` int(11) NOT NULL,
                  `hidden_at` datetime NOT NULL,
                  `last_updated_at` datetime NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `product_id` (`product_id`,`store_id`),
                  KEY `parent_product_comment_id` (`parent_product_comment_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );//send blank array if you does not need sidebar entry,send a blank array if your addon does not need any sql to run
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
        $sql = array
        (
            0=>"DROP TABLE IF EXISTS `ecommerce_product_review`;",
            1=>"DROP TABLE IF EXISTS `ecommerce_product_comment`;",
        ); 
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }


}
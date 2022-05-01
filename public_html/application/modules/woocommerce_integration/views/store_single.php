<section class="section mt-2">
  <div class="section-header p-0 no_shadow bg-light mb-0">
      <?php
      $buy_button_title = $this->lang->line("Buy Now"); 
      // $currency_icon = rtrim($store_data["currency_icon"],'&nb');
      // $currency_position = $store_data["currency_position"];
      // $decimal_point = $store_data["decimal_point"];
      // $thousand_comma = $store_data["thousand_comma"];
      $category_list = array();
      $categories = json_decode($store_data["categories"],true);
      foreach ($categories as $key => $value) {
        $category_list[$value['id']] = $value["name"];
      }      
      ?>
      <form class='m-0 mt-2 w-100 search_form'>
        <div class="input-group">
          <?php
          $url_cat =  isset($_GET["category"]) ? $_GET["category"] : "";
          ?>
          <input type="text" onkeyup="search_product(this,'product-container')"  class="form-control" name="search" id="search" value= "<?php echo $this->session->userdata('search_search');?>" 
          placeholder="<?php echo $this->lang->line("Search"); ?>">
        </div>
      </form>
  </div>

  <div class="section-body">
    <div class="category_container">
      <?php 
      $active_class = $url_cat=='' ? 'bg-primary text-white' : '';
      echo '<div class="slide"><a class="pointer cat_nav nav-link '.$active_class.'" href="" data-val="">'.$this->lang->line("Any Category").'</a></div>';
      unset($category_list['']);

      foreach ($category_list as $key => $value)
      {
        $active_class2 = ($key==$url_cat) ? 'bg-primary text-white' : '';
        echo '<div class="slide"><a class="pointer cat_nav nav-link '.$active_class2.'" href="" data-val="'.$key.'">'.$value.'</a></div>';
      } ?>
      
    </div>
    <?php
    if(empty($product_list))
    { ?>
      <div class="card no_shadow mt-2" id="nodata">
        <div class="card-body">
          <div class="empty-state">
            <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-full-stack-man-colour.svg'); ?>" alt="image">
             <h2 class="mt-0"><?php echo $this->lang->line("We could not find any item.");?></h2>
             <?php if($_POST) { ?>
             <a href="<?php echo $_SERVER['QUERY_STRING'] ? current_url().'?'.$_SERVER['QUERY_STRING'] : current_url(); ?>" class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-circle-right"></i> <?php echo $this->lang->line("Search Again");?></a>
             <?php } ?>
          </div>
        </div>
      </div>
    <?php
    }?>
    <div class="row" id="product-container">
      <?php
      foreach ($product_list as $key => $value) 
      {  
        $product_link = $value['permalink'];
        $product_categories = json_decode($value['category'],true);
        $category_id = 0;
        foreach ($product_categories as $key2 => $value2)
        {
          $category_id = $value2['id'];
          break;
        }
        ?>

        <div class="col-12 col-sm-12 col-md-6 col-lg-4 product-single" data-cat="<?php echo $category_id;?>">
          <ul class="list-unstyled list-unstyled-border bg-white mb-1 mt-2 rounded bordered">
              <li class="media align-items-center">
               <?php
                $imgSrc = ($value['thumbnail']!='') ? $value['thumbnail'] : base_url('assets/img/products/product-1.jpg');
                if(isset($value["woocommerce_product_id"]) && !is_null($value["woocommerce_product_id"]) && $value['thumbnail']!='')
                $imgSrc = $value['thumbnail'];
               ?>
               <a href="<?php echo $product_link;?>"><img width="110" height="110" class="mr-2 rounded-left bordered-right" src="<?php echo $imgSrc; ?>"/>
               </a>
                <div class="media-body pl-0 pr-2">
                  <div class="media-title mb-1">
                    <a href="<?php echo $product_link;?>" class="text-dark text-small"><?php echo $value['product_name'];?></a><br>
                    <span class="mt-1 text-small"><?php echo $value['price_html']; ?> </span>                  
                  </div>
                  <p class="text-small text-muted m-0 mb-1" style="line-height: normal !important"><?php echo strlen(strip_tags($value['product_description']))>30?substr(strip_tags($value['product_description']), 0, 30).'...':strip_tags($value['product_description']); ?></p>
                  <p class="d-none"><?php echo strip_tags($value['product_description']); ?></p>
                  
                  <a href="<?php echo $product_link; ?>" class="btn btn-sm btn-primary" data-attributes="" data-product-id="<?php echo $value['id'];?>" data-action=''><i class="fas fa-credit-card"></i> <?php echo $this->lang->line($buy_button_title); ?></a>
                  
                </div>
              </li>                
           </ul>
        </div>
      <?php
      } ?>       
    </div>
    <div class="card no_shadow d-none w-100 mt-2" id="nodata_search">
      <div class="card-body">
        <div class="empty-state">
          <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-full-stack-man-colour.svg'); ?>" alt="image">
           <h2 class="mt-0"><?php echo $this->lang->line("We could not find any item.");?></h2>
           <?php if($_POST) { ?>
           <a href="<?php echo $_SERVER['QUERY_STRING'] ? current_url().'?'.$_SERVER['QUERY_STRING'] : current_url(); ?>" class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-circle-right"></i> <?php echo $this->lang->line("Search Again");?></a>
           <?php } ?>
        </div>
      </div>
    </div> 
  </div>

</section>

<script> 
  var url_cat =  '<?php echo $url_cat;?>';
  $("document").ready(function()  {
    if(url_cat!="" )setTimeout(function(){ $(".cat_nav[data-val="+url_cat+"]").click(); }, 500);
    $(document).on('click','.cat_nav',function(e){
      e.preventDefault();
      $('.cat_nav').removeClass('bg-primary');
      $('.cat_nav').removeClass('text-white');
      $(this).addClass('bg-primary');
      $(this).addClass('text-white');
      var cat = $(this).attr('data-val');
      if(cat=='0' || cat=='') $('.product-single').removeClass('d-none');
      else
      {
        $('.product-single').addClass('d-none');
        $('.product-single[data-cat='+cat+']').removeClass('d-none');
      }
      var count = $('.product-single:visible').length;
      if(count==0) $("#nodata_search").removeClass('d-none');
      else $("#nodata_search").addClass('d-none');
    });
  });

  function search_product(obj,div_id){  // obj = 'this' of jquery, div_id = id of the div 
    var filter=$(obj).val().toUpperCase();
    $('#'+div_id+" .col-12").each(function(){
      var content=$(this).text().trim();
      if (content.toUpperCase().indexOf(filter) > -1) {
        $(this).removeClass('d-none');
      }
      else $(this).addClass('d-none');
    });
    var count = $('.product-single:visible').length;
    if(count==0) $("#nodata_search").removeClass('d-none');
    else $("#nodata_search").addClass('d-none');

  }
</script>

<style type="text/css">
  .nav-link{padding: 5px 10px;margin: 5px 0 5px 0;border:.5px solid #e4e6fc;border-radius: 10px !important;margin-right: 5px;white-space: nowrap;}
  .category_container {
    display: inline-flex;
    width: 100%;
    overflow-y: auto;
  }
  .category_container .slide {float: left;}
  .rounded{border-radius: 10px  !important;}
  .rounded-left{border-radius: 10px 0 0 10px  !important;}
  ins{text-decoration: none;}
</style>


<?php include(APPPATH."views/ecommerce/cart_style.php"); ?>
<?php include(APPPATH."views/ecommerce/common_style.php"); ?>
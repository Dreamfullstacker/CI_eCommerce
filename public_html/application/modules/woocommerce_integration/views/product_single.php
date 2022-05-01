  <?php
  $currency_icon = "$";
  $buy_button_title = $this->lang->line("Buy Now"); 
  $product_link = $product_data['permalink'];
  $carousel = true;
  if($product_data['featured_images']=="" && $product_data['thumbnail']=="") $carousel = false;
  $category_list = array();
  $categories = json_decode($product_data["categories"],true);
  foreach ($categories as $key => $value) {
    $category_list[$value['id']] = $value["name"];
  }
  $product_categories = json_decode($product_data['category'],true);
  $category_id = 0;
  foreach ($product_categories as $key2 => $value2)
  {
    $category_id = $value2['id'];
    break;
  }
  $product_attributes = json_decode($product_data['attribute'],true);
  $have_attributes = !empty($product_attributes) ? true : false;
  ?>

  <div class="row bg-white pb-3 margin_md">   

    <div class="col-12 col-sm-12 <?php if($carousel) echo 'col-md-6 col-lg-8';?>">
      <div class="card no_shadow mt-3 mb-0 remove-margin">
        <div class="card-header p-2" style="border:none">
           <h4 class="full_width pr-0">
            <?php echo $product_data['product_name'];?>
            <span class="float-right" id="calculated_price_basedon_attribute"><?php echo $product_data['price_html'];?></span>          
          </h4>
        </div>
      </div>

      <div class="hero p-2" style="border-radius: 0 0 3px 3px;">
        <div class="hero-inner">
          <ul class="nav nav-tabs" id="myTab2" role="tablist">

            <?php if(!empty($product_data['product_description'])): ?>
            <li class="nav-item">
              <a class="nav-link active show" id="description-tab2" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="false"><?php echo $this->lang->line("Details"); ?></a>
            </li>
            <?php endif; ?>
            <?php if($have_attributes): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo empty($product_data['product_description']) ? 'active show' : '';?>" id="details-tab2" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="false"><?php echo $this->lang->line("Options"); ?></a>
            </li>
            <?php endif; ?> 

          </ul>
          <div class="tab-content tab-bordered mb-2" id="myTab3Content">
            <?php if(!empty($product_data['product_description'])): ?>
            <div class="tab-pane fade p-2 pb-0 active show" id="description" role="tabpanel" aria-labelledby="description-tab2">
              <?php echo $product_data['product_description']; ?>              
            </div>
            <?php endif; ?>
            <?php if($have_attributes): ?>
            <div style="overflow-x: auto" class="tab-pane fade p-2 pb-0 makescroll <?php echo empty($product_data['product_description']) ? 'active show' : '';?>" id="details" role="tabpanel" aria-labelledby="details-tab2">
              <?php
              foreach($product_attributes as $key => $value)
              {
                echo '
                <nav>
                  <ul class="pagination">
                    <li class="page-item active"><a class="page-link" href="'.$product_link.'">'.$value["name"].'</a></li>';
                    $options = $value['options'];
                    foreach ($options as $key2 => $value2)
                    {
                      echo '<li class="page-item"><a class="page-link" href="'.$product_link.'">'.$value2.'</a></li>';
                    }
                echo 
                '</nav>';
              } ?>              
            </div>
            <?php endif; ?>
          </div>
          
        </div>
        <a href="<?php echo $product_link;?>" class="btn btn-outline-primary btn-lg btn-block no_radius" data-attributes="" data-product-id="<?php echo $product_data['id'];?>" data-action=''><i class="fas fa-credit-card"></i> <?php echo $this->lang->line($buy_button_title); ?></a>
      </div>
              
    </div>

    <div class="<?php echo $carousel ? 'col-12 col-sm-12 col-md-6 col-lg-4' : 'col-12';?>">
      <?php if($carousel) : ?>
      <article class="article article-style-c mt-3 mb-0 remove-margin">            
        <?php $featured_images_array = ($product_data['featured_images']!="") ? explode(',', $product_data['featured_images']) : array(); ?>
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-interval="3000">
          <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <?php 
            if($product_data['featured_images']!="")
            {          
              $slide=0;
              foreach ($featured_images_array as $key => $value) 
              {
                $slide++;
                echo '<li data-target="#carouselExampleIndicators" data-slide-to="'.$slide.'"></li>';
              }
            }
            ?>
          </ol>
          <div class="carousel-inner">
            <div class="carousel-item active">
              <?php 
                $imgSrc = ($product_data['thumbnail']!='') ? $product_data['thumbnail'] : base_url('assets/img/products/product-1.jpg');
              ?>
              <img class="d-block w-100" style="height: 345px;" src="<?php echo $imgSrc; ?>">             
              <div class="carousel-caption">
                  <h4><?php echo $product_data['product_name'];?></h4>
                  <!-- <p></p> -->
              </div>
            </div>
            <?php 
            if($product_data['featured_images']!="")
            {
              foreach ($featured_images_array as $key => $value)
              { ?>
              <div class="carousel-item">
                <?php
                $imgSrc = $value;                
                ?>
                <img class="d-block w-100" style="height: 345px;" src="<?php echo $imgSrc; ?>">
                <div class="carousel-caption">
                    <h4><?php echo $product_data['product_name'];?></h4>
                    <!-- <p></p> -->
                </div>
              </div> 
              <?php 
              }?>
            <?php 
            } ?>
          </div>
          <?php if($product_data['featured_images']!="")
          { ?>
          <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only"><?php echo $this->lang->line("Previous");?></span>
          </a>
          <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only"><?php echo $this->lang->line("Next");?></span>
          </a>
        <?php } ?>
        </div>
      </article>
      <?php endif; ?>

      <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center no_radius">
          <?php echo $this->lang->line("Category"); ?>
          <span class="badge badge-primary badge-pill">
            <?php echo isset($category_list[$category_id]) ? $category_list[$category_id] : $this->lang->line("Uncategorised");?>
          </span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center no_radius">
          <?php echo $this->lang->line("Sales"); ?>
          <span class="badge badge-primary badge-pill"><?php echo $product_data['sales_count'];?></span>
        </li>
      </ul>
    </div>
  </div>
   
<div class="sticky-height"></div>
    
              

<?php include(APPPATH."views/ecommerce/cart_style.php"); ?>
<?php include(APPPATH."views/ecommerce/common_style.php"); ?>

<style type="text/css">
  .custom-control.custom-checkbox{margin-bottom:10px;}
  .custom-control-label{line-height: 2rem;padding-left: 20px}
  .custom-control-label::before,.custom-control-label::after{height: 1.5rem;width: 1.5rem;}
  .custom-switch{margin-bottom: 10px;}
  .media-body h6{font-weight: 700;font-size: 17px;}
  @media (max-width: 978px) {
    .sticky-height{height: 35px !important;}
    #cart_actions{position: fixed;border-radius: 0;z-index: 99;bottom:65px;left:0;width: 100%;background:#fff;}
    .col-12:not(.always_padded) {
      padding:0;
    }   
   .remove-margin{margin:0 !important;}    
  }
  @media (min-width: 768px) { 
    .margin_md{margin-top:20px;}
  }
  .hero p{font-size: 14px;line-height: 25px;}
  .text-medium{font-size: 12px !important;}
  ins{text-decoration: none;}
</style>

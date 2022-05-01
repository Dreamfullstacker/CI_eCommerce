<?php
$category_list = array();
$categories = json_decode($store_data["categories"],true);
foreach ($categories as $key => $value) {
  $category_list[$value['id']] = $value["name"];
}

$store_code = array(0=>array("title"=>$this->lang->line("Store Page"),"url"=>base_url("woocommerce_integration/store/".$store_data['id'])));
$category_copy = array();
$product_copy = array();
foreach ($category_list as $key => $value)
{
   $store_code[] = array("title"=>$this->lang->line("Store Page")." - ".$this->lang->line("Category")." : ".$value,"url"=>base_url("woocommerce_integration/store/".$store_data['id']."?category=".$key));
}
$product_list_assoc = array();                         
foreach ($product_list as $key => $value) 
{
  $product_copy[] = array("title"=>$this->lang->line("Product Page")." : ".$value["product_name"],"url"=>base_url("woocommerce_integration/product/".$value['id']));  
}
?>


<section class="section">
  <div class="section-body">
    <div class="modal-body">
      <ul class="nav nav-tabs" id="myTab2" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="home-tab2" data-toggle="tab" href="#home2" role="tab" aria-controls="home" aria-selected="true"><?php echo $this->lang->line("Store URL"); ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="contact-tab2" data-toggle="tab" href="#contact2" role="tab" aria-controls="contact" aria-selected="false"><?php echo $this->lang->line("Product URL"); ?></a>
        </li>
      </ul>
      <div class="tab-content tab-bordered" id="myTab3Content">

        <div class="tab-pane fade show active bg-body" id="home2" role="tabpanel" aria-labelledby="home-tab2">
          <?php 
           foreach ($store_code as $key => $value)
           { ?>
             <div class="card">
              <div class="card-header">
                <h4><i class="fas fa-circle"></i> 
                  <a href="<?php echo $value["url"];?>" target="_BLANK"><?php echo $value['title'];?></a>
                </h4>
              </div>
              <div class="card-body">
                <pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword"><?php echo $value["url"];?></span></code></pre>
              </div>
            </div>
           <?php
          } 
          ?>
        </div>
        <div class="tab-pane fade bg-body" id="contact2" role="tabpanel" aria-labelledby="contact-tab2">
         <?php 
         foreach ($product_copy as $key => $value)
         { ?>
           <div class="card">
            <div class="card-header">
              <h4><i class="fas fa-circle"></i> 
                <a href="<?php echo $value["url"];?>" target="_BLANK"><?php echo $value['title'];?></a>
              </h4>
            </div>
            <div class="card-body">
              <pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword"><?php echo $value["url"];?></span></code></pre>
            </div>
          </div>
         <?php
         } 
         ?>
        </div>
      </div>
    </div>
  </div>
</section>
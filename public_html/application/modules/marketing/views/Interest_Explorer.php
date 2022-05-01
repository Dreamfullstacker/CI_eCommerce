 <style type="text/css">
.alert a{text-decoration: none;}
#copy_code .row:hover{background:#e0e0e0;}
</style>
<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


if(empty($page_info)){ ?>
     
    <section class="section section_custom">
      <div class="section-header">
        <h1><i class="fa fa-search-location"></i> <?php echo $page_title;?></h1>
        <div class="section-header-breadcrumb">
          <div class="breadcrumb-item"><a href="<?php echo base_url('marketing/get_interest'); ?>"><?php echo $this->lang->line('interest_explorer'); ?></a></div>
          <div class="breadcrumb-item"><?php echo $page_title;?></div>
        </div>
      </div>
      </section>
  
      <div class="section-body">
    <div class="row">
    <div class="col-12">
      <div class="card" id="nodata">
      <div class="card-body">
        <div class="empty-state">
          <img class="img-fluid" style="height: 200px" src="https://app.salesmiles.com/assets/img/drawkit/drawkit-nature-man-colour.svg" alt="image">
           <h2 class="mt-0"><?php echo $this->lang->line('We could not find any page.');?></h2>
          <p class="lead"><?php echo $this->lang->line('Please import account if you have not imported yet.'); ?><br />
          <?php echo $this->lang->line('If you have already imported account then enable bot connection for one or more page to continue.');?></p>
          <a href="/social_accounts" class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-circle-right"></i> <?php echo $this->lang->line('Continue');?></a>
        </div>
      </div>
    </div>    </div>
      </div>
    </div>

<?php }else{ ?>

    <section class="section section_custom">
      <div class="section-header">
        <h1><i class="fa fa-search-location"></i> <?php echo $page_title;?></h1>
        <div class="section-header-breadcrumb">
          <div class="breadcrumb-item"><a href="<?php echo base_url('marketing/get_interest'); ?>"><?php echo $this->lang->line('interest_explorer'); ?></a></div>
          <div class="breadcrumb-item"><?php echo $page_title;?></div>
        </div>
      </div>



    <div class="section-body">
    <div class="row">
    <div class="col-12">
    <div class="card">
            <div id="response" style="margin:0 15px;"></div>
            
            <div class="row">
            
                <div class="col-xs-12 col-md-8 col-">
                    <div class="box box-warning">

                        <div class="box-body" >
                            <br>
                            <form action="#" enctype="multipart/form-data" style="padding: 0 20px 20px 20px">
                        
                                <div class="form-group text-center">
                                    <label style="width:100%">
                                        <?php echo $this->lang->line("Keyword Interest, ex. soccer title"); ?> *
                                         <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Keyword Interest, ex. soccer help title") ?>" data-content='<?php echo $this->lang->line("Keyword Interest, ex. soccer help") ?>'><i class='fa fa-info-circle'></i> </a>
                                    </label>
                                    <input type="text" name="domain_name" id="domain_name" class="form-control" placeholder="<?php echo $this->lang->line("Keyword Interest, ex. soccer placeholder"); ?>">						
                                </div>
                                <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="form-group text-center  " style='padding:0;'>
                                    <label style="width:100%">
                                        <?php echo $this->lang->line("Type"); ?> 
                                    </label>
                            
                                    <?php 
                            
                                    $ad_type = array(
                                        'adeducationschool' => $this->lang->line("College targeting"),
                                        'adeducationmajor' => $this->lang->line("College major targeting"),
                                        //'adgeolocation' => 'combined for country, city, state & zip',
                                        //'adcountry' => 'country',
                                        'adgeolocation' => $this->lang->line("Zip code"),
                                        //'adgeolocationmeta' => 'Additional metadata for geolocations',
                                        //'adradiussuggestion' => 'Returns recommended radius around location',
                                        'adinterest' => $this->lang->line("Interest targeting"),
                                        'adinterestsuggestion' => $this->lang->line("Suggestions based on interest targeting"),
                                        //'adinterestvalid' => 'Validates string as valid interest targeting option',
                                        //'adlocale' => 'Locale targeting',
                                        //'adeducationmajor' => 'Education major',
                                        'adworkemployer' => $this->lang->line("Work employer"),
                                        'adworkposition' => $this->lang->line("Job title"),
                                        'adTargetingCategory:interests' => $this->lang->line("Targeting interests (put any string keyword)"),
                                        'adTargetingCategory:behaviors' => $this->lang->line("Targeting behaviors (put any string keyword)"),
                                        'adTargetingCategory:life_events' => $this->lang->line("Life events (put any string keyword)"),
                                        'adTargetingCategory:industries' => $this->lang->line("Industries (put any string keyword)"),
                                        'adTargetingCategory:income' => $this->lang->line("Income (put any string keyword)"),
                                        'adTargetingCategory:family_statuses' => $this->lang->line("Family statuses (put any string keyword)"),
                                        'adTargetingCategory:user_device' => $this->lang->line("User device (put any string keyword)"),
                                        //'adTargetingCategory:user_os' => 'User os (put any string keyword)' $this->lang->line("Type"),
                                        'adTargetingCategory:demographics' => $this->lang->line("Demographics (put any string keyword)"),
                                    );
                            
                                    echo form_dropdown('type_int', $ad_type,'adinterest','class="form-control select2" id="type_int"'); ?>													
                                </div>		
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group text-center " style='padding:0;'>
                                    <label style="width:100%">
                                        <?php echo $this->lang->line("language"); ?> 
                                    </label>
                                    <?php echo form_dropdown('language', $sdk_locale,$config_sdk_locale,'class="form-control select2" id="language"'); ?>													
                                </div>		
                                 </div>
                                 
                                </div>
                                <div class="clearfix"></div>	
                        
                                <div class="box-footer text-center">
                                    <!-- <div class="col-xs-12"> -->
                                        <button style='width:100%; max-width:350px; margin-bottom:10px;' class="btn btn-lg btn-primary center-block get_button" id="get_button" name="get_button" type="button" value="get_records" onclick="get_button_data('get_interest')"><i class="fa fa-tasks" ></i> <?php echo $this->lang->line("Get Interest");?></button>
                                        <button style='width:100%; max-width:350px; margin-bottom:10px;' class="btn btn-lg btn-primary center-block get_button" name="get_button" type="button" value="get_csv" onclick="get_button_data('get_csv')"><i class="fa fa-file-csv"></i> <?php echo $this->lang->line("Get CSV");?></button>
                                    <!-- </div> -->
                                </div>

                            </form>
                        </div>
                
                    </div>
                </div>  <!-- end of col-6 left part -->

                <div class="col-xs-12 col-md-4">
                    <div class="box box-warning">
                        <div class="box-body" >
                            <h3 class="box-title"><?php echo $this->lang->line("Selected keywords"); ?></h3>
                            <form action="#" style="padding-right: 15px">
                                <textarea class="form-control name_list" id="list_keywords" style="height: 146px!important;margin-bottom: 24px;"></textarea>
                                <button style='width:45%; max-width:350px; margin-bottom:10px;' class="btn btn-lg btn-primary center-block" onclick="copyToClipboard('textarea#list_keywords')" name="get_button" type="button" value="get_csv"><i class="fa fa-copy"></i> <?php echo $this->lang->line("Copy to clipboard");?></button>
                                 <button style='width:45%; max-width:350px; margin-bottom:10px;' class="btn btn-lg btn-primary center-block onclick="exportToCSV(keys_id)" name="get_button" type="button" value="get_csv"><i class="fa fa-file-csv"></i> <?php echo $this->lang->line("Export to CSV");?></button>
                             </form>
                        </div>
                    </div>
                </div>
            
            </div>

            <div class="col-xs-12 col-md-12">
                <div class="box box-warning">
                    <div class="box-header ui-sortable-handle blue text-center" style="cursor: move;margin-bottom: 0px;border-bottom: 1px solid #ddd;">
                        <h3 class="box-title"><?php echo $this->lang->line("Interest Explorer Result"); ?>: <span id="counterlist"></span></h3>
                        <!-- tools box -->
                        <div class="pull-left box-tools">
                            <input 
                              type="checkbox" 
                              id="checkAll" 
		                  /> <?php echo $this->lang->line("Select all"); ?>
                        </div><!-- /. tools -->
                    </div>
                    <div class="box-body">
                        <br>
                        <div class="form-group text-center">
                        
                            <div id="preloader" class="text-center"></div>
                            <div id="copy_code" style="padding:15px;"></div>
                        

                            
                        </div>
                    </div>
                
                </div>
            </div>  <!-- end of col-6 left part -->
    </div></div>
        </div>
    </div>
    </section>


    <?php 	
        $pleaseputyourwebsitelink = $this->lang->line("Please put your keyword");
     ?>

    <script>
    
        $("#checkAll").click(function(){
            $('.checksel').not(this).prop('checked', this.checked);
            
            
            $.each($('.checksel'),function(){
                if($(this)[0].checked){
                    set_keyids($(this));
                }else{
                    unset_keyids($(this));
                }
            });

            
            $('#list_keywords').html(keywords);
        });

        function copyToClipboard(element) {
              var $temp = $("<input>");
              $("body").append($temp);
              $temp.val($(element).text()).select();
              document.execCommand("copy");
              $temp.remove();
        }
        
            var keys_id = new Array();
            var keywords = '';
        
            function set_keyids(val){
                keys_id[val.attr("id")] = new Array();
                
                if(val.attr("data-audience")!=undefined){
                    keys_id[val.attr("id")]['Keywords'] = val.attr("value");
                    keys_id[val.attr("id")]['Audience Size'] = val.attr("data-audience");
                    keys_id[val.attr("id")]['Category'] = val.attr("data-cat");
                    keys_id[val.attr("id")]['Topic'] = val.attr("data-topic");
                    keys_id[val.attr("id")]['Facebook'] = val.attr("data-fb");
                    keys_id[val.attr("id")]['Google'] = val.attr("data-gl");
                }else{
                    keys_id[val.attr("id")]['Keywords'] = val.attr("value");
                    keys_id[val.attr("id")]['Coverage'] = val.attr("data-coverage");
                }
                
                keywords = keywords + val.attr("value") + ", ";
                return true;
            }
            
            function unset_keyids(val){
//                 keys_id.splice(val.attr("id"), 1);
                delete keys_id[val.attr("id")];
                keywords = keywords.replace(val.attr("value")+', ', "");
                return true;
            }
            
            var objectToCSVRow = function(dataObject) {
                var dataArray = new Array;
                    for (var o in dataObject) {
                        var innerValue= dataObject[o]===null?'':dataObject[o].toString();
                        var result = innerValue.replace(/"/g, '""');
                        dataArray.push(result);
                    }
                return dataArray.join('; ') + '\r\n';
        
            }
            
            var exportToCSV = function(arrayOfObjects) {


                var csvContent = "data:text/csv;charset=utf-8,";

                // headers
                //csvContent += objectToCSVRow(Object.keys(arrayOfObjects[0]));

                for (var item in arrayOfObjects) {
                    csvContent += objectToCSVRow(arrayOfObjects[item]);
                }

                var encodedUri = encodeURI(csvContent);
                
                var a = document.createElement('a');
                a.href = encodedUri;
                a.download = 'selected_records.csv';
                a.textContent='download';
                document.body.append(a);
                a.click();
                a.remove();
                //window.URL.revokeObjectURL(url);
            }
        

//         $("document").ready(function(){

            var base_url="<?php echo base_url();?>";
            var get_button_var = "get_interest";

        
            $('[data-toggle="popover"]').popover(); 
            $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});
        
           

           var get_button_data = function(button)
            {

                var domain_name = $("#domain_name").val();
                var language = $("#language").val();
                var type_int = $("#type_int").val();
            
                var pleaseputyourwebsitelink = "<?php echo $pleaseputyourwebsitelink; ?>";

                if(domain_name=="")
                {
                    alertify.alert('<?php echo $this->lang->line("Alert");?>',pleaseputyourwebsitelink,function(){});
                    return;
                }

                $("#preloader").html('<img width="30%" class="center-block text-center" src="<?php echo base_url('assets/pre-loader/loading-animations.gif')?>" alt="Processing...">');
                $(".get_button").addClass('disabled');
                $("#response").attr('class','').html('');
                $("#wp_plugin").addClass('hidden');
                get_button_var = button;

                $.ajax({
                    type:'POST' ,
                    url:"<?php echo site_url();?>marketing/get_interest_search",
                    data:{domain_name:domain_name,language:language,type_int:type_int,get_button:get_button_var},
                    dataType:'JSON',
                    success:function(response)
                    {        			
                        $("#preloader").html("");
                        $(".get_button").removeClass('disabled');
                    
                        if(get_button_var=="get_csv" && response.status=='1'){
    //                         var uri = 'data:application/csv;charset=UTF-8,' + encodeURIComponent(response.js_code);
    //                         window.open(uri, 'records.csv');
    //                         get_button_var = "get_interest";

                                var a = document.createElement('a');
                                a.href = 'data:application/csv;charset=UTF-8,' + encodeURIComponent(response.js_code);
                                a.download = 'report_'+domain_name+'.csv';
                                a.textContent='download';
                                document.body.append(a);
                                a.click();
                                a.remove();
                                //window.URL.revokeObjectURL(url);
                            return;
                        }

                        if(response.status=='1') 
                        {
                            $("#response").attr('class','alert alert-success text-center');
                            $("#copy_code").removeAttr('disabled').html(response.js_code);      
                            $("#response").html(response.message);
                            get_button_var = "get_interest";  				
                        }
                        else 
                        {
                            $("#response").attr('class','alert alert-danger text-center');
                            $("#copy_code").text('').attr('disabled','disabled');
                            $("#response").html(response.message);
                            get_button_var = "get_interest";
                            return;
                        }
                        
                        $('#counterlist').html(response.count);

                        
                        $('span.tooltipcs').tooltip({
                            delay: { "show": 0, "hide": 100 },
                            placement: "right"
                      });
                      
                      $(".checksel").click( function(){
                            if($(this)[0].checked){
                                set_keyids($(this));
                            }else{
                                unset_keyids($(this));
                            }
                            $('#list_keywords').html(keywords);
                        });
                        
                        for (var item in keys_id) {
                            $("#"+item).prop('checked', true);
                        }
                      
                        

                    }
                });
            
            

            }
            
            $( "#type_int" ).change(function() {
                keys_id = null;
                keys_id = new Array();
                keywords = '';
                $('#list_keywords').html(keywords);
            });
            
            //$(document.body).on('click','.get_button',get_button_data);
                
        //$('.get_button').click(get_button);


                    document.getElementById("domain_name").addEventListener("keydown", function(e) {
                        if (!e) { var e = window.event; }
                        //e.preventDefault(); // sometimes useful

                        // Enter is pressed
                        if (e.keyCode == 13) {e.preventDefault(); get_button_data(); }
                    }, false);
                    
                    

//         });




    </script>







<?php  } ?>
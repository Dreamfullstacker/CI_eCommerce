<style>
  .bbw{border-bottom-width: thin !important;border-bottom:solid .5px #f9f9f9 !important;padding-bottom:20px;}
  .note-editable{padding-top:40px !important;min-height: 200px !important;max-height:600px !important;border:none !important;padding-right:0 !important;}
  .template_contents { min-height: 200px !important;max-height:400px !important;}
  .button-outline
  {
    background: #fff;
    border: .5px dashed #ccc;
  }
  .button-outline:hover
  {
    border: 1px dashed var(--blue) !important;
    cursor: pointer;
  }
</style>
<input type="hidden" name="sms_email_sequence_csrf_token" id="sms_email_sequence_csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
<div class="hidden" id="add_bot_settings_modal" style="margin-bottom:50px;">
  <div class="modal-dialog" style="max-width:100%;margin:0px;">
    <div class="modal-content <?php if($iframe=='1') echo 'no_shadow';?>">
      <?php if($iframe!='1') : ?>
      <div class="modal-header"  style="padding: 10px;">
        <h4 class="modal-title" style="padding-left: 35px;"><i class='fas fa-plus'></i> <?php echo $this->lang->line("Add Sequence");?></h4>
      </div>
      <?php endif; ?>
      <div class="modal-body  <?php if($iframe=='1') echo 'padding-0';?>">

        <div class="row">
          <div class="col-12">  
            <form action="#" enctype="multipart/form-data" id="sms_email_sequence_form" style="padding-left: 0;">
              <input type="hidden" name="day_counter" id="day_counter" value="<?php echo $default_display;?>">
              <input type="hidden" name="hour_counter" id="hour_counter" value="<?php echo $default_display_hour;?>">
              <input type="hidden" name="page_id" id="page_id" value="<?php echo $page_auto_id;?>">
               
              <div class="row">
                <div class="form-group col-12">             
                  <label><?php echo $this->lang->line("Campaign Name"); ?></label>
                  <input type="text" name="campaign_name" id="campaign_name" class="form-control">  
                </div>
              </div>

              <div class="row">
                  <div class="form-group col-12 col-md-12">
                      <label class="form-label"><?php echo $this->lang->line("Sequence Type"); ?></label>
                      <div class="selectgroup selectgroup-pills">
                        <label class="selectgroup-item">
                          <input type="radio" name="campaign_types" id="campaign_types" value="email" class="selectgroup-input" checked>
                          <span class="selectgroup-button"><?php echo $this->lang->line("Email"); ?></span>
                        </label>
                        <label class="selectgroup-item">
                          <input type="radio" name="campaign_types" id="campaign_types" value="sms" class="selectgroup-input">
                          <span class="selectgroup-button"><?php echo $this->lang->line("SMS"); ?></span>
                        </label>
                        
                      </div>                   
                  </div>
              </div> 

              <div class="card border_me">
                <div class="card-header">
                  <h4>
                    <i class="far fa-clock"></i> <?php echo $this->lang->line("Sequence Time"); ?>                    
                  </h4>
                </div>
                <div class="card-body" id="sequence_body">
                 
                 <ul class="nav nav-tabs" id="sequence_tab" role="tablist">

                   <li class="nav-item">
                     <a class="nav-link active" id="sequence_tab2" data-toggle="tab" href="#hourwise" role="tab" aria-controls="profile" aria-selected="false"><?php echo  $this->lang->line("24 Hour"); ?>  
                     </a>
                   </li>
                   <li class="nav-item">
                     <a class="nav-link" id="sequence_tab1" data-toggle="tab" href="#daywise" role="tab" aria-selected="true"><?php echo  $this->lang->line("Daily"); ?></a>
                   </li>
                 </ul>
                 <div class="tab-content tab-bordered">
                   <div class="tab-pane fade" id="daywise" role="tabpanel" aria-labelledby="sequence_tab1">
                     <div class="row">
                       <?php  
                         $tooplip1='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Starting & Closing Time").'" data-content="'.$this->lang->line('System will start processing email from starting hour & terminate processing at closing hour of the day. The time interval must be minimum one hour. If your subscriber list for this campaign is large, you should select larger time interval in order to send all email properly.').'"><i class="fa fa-info-circle"></i> </a>';
                        ?>
                       <div class="col-6 col-md-4">
                           <div class="form-group">
                           <label><?php echo $this->lang->line("Starting Time")." ".$tooplip1;?></label>
                           <input type="text" class="form-control timepicker_x" value="00:00" id="between_start" name="between_start">
                           </div>
                       </div>
                       <div class="col-6 col-md-4">
                           <div class="form-group">
                               <label><?php echo $this->lang->line("Closing Time")." ".$tooplip1;?></label>
                               <input type="text" class="form-control timepicker_x" value="23:59" id="between_end" name="between_end">
                           </div>
                       </div>
                       <div class="col-12 col-md-4">
                           <div class="form-group">
                           <label><?php echo $this->lang->line("Time Zone");?></label>
                           <?php echo form_dropdown('timezone', $timezones,$this->config->item('time_zone'),"class='form-control select2' id='timezone' style='width:100%;'");?>
                           </div>
                       </div>
                     </div>
                     
                     <?php 
                     for($i=1; $i <=$how_many_days ; $i++) 
                     { 
                       $hideshowclass='';
                       if($i>$default_display) $hideshowclass='hidden';
                       ?>
                       <div class="row <?php echo $hideshowclass;?>" id="day_container<?php echo $i;?>">
                     
                         <div class="form-group col-3">
                           <div class="selectgroup w-100">
                             <label class="selectgroup-item">
                               <input type="checkbox" value="<?php echo $i;?>" id="day<?php echo $i;?>" class="selectgroup-input" checked>
                               <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-calendar"></i> <?php echo $this->lang->line('Day').'-'.$i; ?></span>
                             </label>
                           </div>
                         </div>

                         <div class="form-group col-7">              
                           <div id='sms_email_sequence_templates<?php echo $i;?>'>
                               <?php 
                                 $template_id="template_id".$i;
                                 $sms_email_sequence_templates['']="--- ".$this->lang->line("Do not send message")." ---";
                                 echo form_dropdown($template_id,$sms_email_sequence_templates, '','class="form-control template_id select2" id="'.$template_id.'" style="width:100%;"'); 
                               ?>
                           </div>
                         </div>
                         <div class="form-group col-2">              
                           <a href="" title="<?php echo $this->lang->line("Refresh Template List");?>" data-toggle="tooltip" data-id="<?php echo $i;?>" class="ref_template btn btn-lg"><i class="fas blue fa-sync"></i></a>
                         </div>
                       </div>               
                     <?php
                     }
                     ?>

                     <div class="row button_container">
                       <div class="form-group col-7 offset-3">
                         <a id="add_more_day" href="" class="btn btn-outline-primary btn-sm float-left"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Add More Day');?></a>
                         <a id="remove_last_day" href="" class="btn btn-outline-danger btn-sm float-right"><i class="fas fa-times-circle"></i> <?php echo $this->lang->line('Remove Last Day');?></a>
                       </div>
                       <div class="form-group col-2">
                         <a target="_BLANK" title="<?php echo $this->lang->line('Add New Template');?>" data-toggle="tooltip" class="btn btn-default btn-lg add_template"  href=""><i class="fas fa-plus-circle"></i></a>
                       </div>
                     </div>
                   </div>

                   <div class="tab-pane fade show active" id="hourwise" role="tabpanel" aria-labelledby="sequence_tab2">
                    
                     <?php 
                     for($i=0; $i <=$how_many_hours ; $i++) 
                     { 
                       $hideshowclass='';
                       if($i>$default_display_hour) $hideshowclass='hidden';

                       if($i==0)
                       {
                           $minutes = 1;
                           $displayname = $this->lang->line('1 Mins');
                       }

                       if($i==1)
                       {
                           $minutes = 5;
                           $displayname = $this->lang->line('5 Mins');
                       }

                       if($i==2)
                       {
                           $minutes = 15;
                           $displayname = $this->lang->line('15 Mins');
                       }

                       if($i==3)
                       {
                           $minutes = 30;
                           $displayname = $this->lang->line('30 Mins');
                       } 

                       if($i > 3) {
                           $minutes = ($i-3)*60;
                           $displayname = ($i-3)." ".$this->lang->line('Hour');
                       }

                       ?>
                       <div class="row <?php echo $hideshowclass;?>" id="hour_container<?php echo $i;?>">
                     
                         <div class="form-group col-3">
                           <div class="selectgroup w-100">
                             <label class="selectgroup-item">
                               <input type="checkbox" value="<?php echo $minutes;?>" id="hour<?php echo $i;?>" class="selectgroup-input" checked>
                               <span class="selectgroup-button selectgroup-button-icon"><i class="far fa-clock"></i> <?php echo $displayname; ?></span>
                             </label>
                           </div>
                         </div>

                         <div class="form-group col-7">              
                           <div id='hour_sms_email_sequence_templates<?php echo $i;?>'>
                               <?php 
                                 $template_id="hour_template_id".$i;
                                 $sms_email_sequence_templates['']="--- ".$this->lang->line("Do not send message")." ---";
                                 echo form_dropdown($template_id,$sms_email_sequence_templates, '','class="form-control hour_template_id select2" id="'.$template_id.'" style="width:100%;"'); 
                               ?>
                           </div>
                         </div>
                         <div class="form-group col-2">              
                           <a href="" title="<?php echo $this->lang->line("Refresh Template List");?>" data-toggle="tooltip" data-id="<?php echo $i;?>" class="hour_ref_template btn btn-lg"><i class="fas blue fa-sync"></i></a>
                         </div>
                       </div>               
                     <?php
                     }
                     ?>

                     <div class="row button_container">
                       <div class="form-group col-7 offset-3">
                         <a id="add_more_hour" href="" class="btn btn-outline-primary btn-sm float-left"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Add More Hour');?></a>
                         <a id="remove_last_hour" href="" class="btn btn-outline-danger btn-sm float-right"><i class="fas fa-times-circle"></i> <?php echo $this->lang->line('Remove Last Hour');?></a>
                       </div>
                       <div class="form-group col-2">
                         <a target="_BLANK" title="<?php echo $this->lang->line('Add New Template');?>" data-toggle="tooltip" class="btn btn-default btn-lg add_template" href=""><i class="fas fa-plus-circle"></i></a>
                       </div>
                     </div>

                   </div>

                 </div>

                </div>
              </div>
                  
            </form>
          </div>
        </div>          

      </div>
      <br> <br>
      <div class="modal-footer <?php if($iframe=='1') echo 'padding-0';?>">
         <a id="submit_btn" href="" class="btn btn-lg btn-primary"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line('Create Campaign');?></a>             
         <a href="<?php echo base_url('sms_email_sequence/sms_email_sequence_message_campaign/'.$page_auto_id.'/1'); ?>" class="btn btn-lg btn-secondary float-right"><i class="fas fa-times"></i> <?php echo $this->lang->line('Back');?></a>             
 
      </div>
    </div>
  </div>
</div>



<?php   
  echo '<div id="alert_message" class="text-center" style="padding:20px;margin-bottom:10px;border:.5px solid #dee2e6; color:var(--blue);background: #fff;">'.$this->lang->line("Campaign will be applied to those subscribers have email & phone number.").'</div>';
  echo "<div class='table-responsive data-card' id='setting_list'><table class='table table-bordered table-sm table-striped' id='ses_mytable'>";
    echo "<thead>";
      echo "<tr>";
        echo "<th>".$this->lang->line("SL")."</th>";
        echo "<th>".$this->lang->line("Name")."</th>";
        echo "<th class='text-center'>".$this->lang->line("Last Sent")."</th>";
        echo "<th class='text-center'>".$this->lang->line("Campaign Type")."</th>";
        if($this->is_engagement_exist)             
        {
          echo "<th class='text-center'>".$this->lang->line("Engagement Campaign")."</th>";
        }
        echo "<th class='text-center'>".$this->lang->line("Actions")."</th>";
      echo "</tr>";
    echo "</thead>";

    echo "<tbody>";
      $i=0;
      foreach ($sms_email_sequence_settings as $key => $value) 
      {
        $i++;
        if($value['last_sent_at']!="0000-00-00 00:00:00") $reply_at=date('M j, Y H:i',strtotime($value['last_sent_at']));
        else $reply_at =  "<i class='fa fa-remove'></i>";
        
        $campaign_types=$value['campaign_type'];
        $details='-';
        echo "<tr>";
          echo "<td>".$i."</td>";
          echo "<td>".$value['campaign_name']."</td>";
          echo "<td class='text-center'>".$reply_at."</td>";
          echo "<td class='text-center'>".$campaign_types."</td>";
          if($this->is_engagement_exist) 
          {
            echo "<td class='text-center'>".$details."</td>";
          }

          $editurl = base_url("sms_email_sequence/edit_sequence_campaign/".$value['id'].'/'.$page_auto_id);
          if(isset($iframe) && $iframe=='1') 
          {
            $editurl.='/1';
          }

          $report_link='';
          if($value['message_content_hourly']!='[]')
          $report_link.= '<li><a class="dropdown-item has-icon message_content" href="" data-day="0" data-id="'.$value['id'].'"><i class="far fa-clock"></i> '.$this->lang->line("24H Report").'</a></li>';

          if($value['message_content']!='[]')
          $report_link.= '<li><a class="dropdown-item has-icon message_content" href="" data-day="1" data-id="'.$value['id'].'"><i class="fas fa-calendar"></i> '.$this->lang->line("Daily Report").'</a></li>';

          $report_link.= '<div class="dropdown-divider"></div>';

          echo "<td class='text-center'>";
            echo '<a href="#" data-toggle="dropdown" class="btn btn-outline-primary btn-circle dropdown-toggle bot_actions no_caret"><i class="fas fa-briefcase"></i></a> 
            
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
             <div class="dropdown-title">'.$this->lang->line("Actions").'</div>                         
              '.$report_link.'
              <li><a class="dropdown-item has-icon" href="'.$editurl.'"><i class="fas fa-edit"></i> '.$this->lang->line("Edit Sequence").'</a></li>
              <div class="dropdown-divider"></div>
              <li><a class="dropdown-item has-icon delete_bot red" href="" campaignType="'.$value['campaign_type'].'" id="'.$value['id'].'"><i class="fas fa-trash-alt"></i> '.$this->lang->line("Delete Sequence").'</a></li>
            </ul>';

          echo "</td>";

        echo "</tr>";
      }
    echo "</tbody>";
  echo "</table></div>";    

  $somethingwentwrong = $this->lang->line("Something went wrong.Please try again.");
  $drop_menu = '<a id="add_bot_settings" href="" class="float-right btn btn-primary"><i class="fas fa-plus-circle"></i> '.$this->lang->line("Add Sequence").'</a>';
?>


<script type="text/javascript">
	var day_counter = '<?php echo $default_display;?>';
	var hour_counter = '<?php echo $default_display_hour;?>';
	var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
	var base_url="<?php echo site_url(); ?>";
	var page_auto_id = '<?php echo $page_auto_id; ?>';
  var base_url = '<?php echo base_url();?>';

	var drop_menu = '<?php echo $drop_menu;?>';
	setTimeout(function(){ 
		$("#ses_mytable_filter").append(drop_menu);
	}, 1000);

  $(".timepicker_x").datetimepicker({
    datepicker:false,
    format:"H:i"
  });

  $('[data-toggle=\"tooltip\"]').tooltip();

	var table = $("#ses_mytable").DataTable({
		language: 
		{
			url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
		},
		dom: '<"top"f>rt<"bottom"lip><"clear">'
	});

	function refresh_template(push_id,campaign_types,current_template_id)
	{    
	  var page_id='<?php echo $page_auto_id;?>';
	  $.ajax({
	    type:'POST' ,
	    url: base_url+"sms_email_sequence/sms_email_template_sequence",
	    data: {push_id:push_id,campaign_types:campaign_types,current_template_id:current_template_id},
	    success:function(response){
	      $("#sms_email_sequence_templates"+push_id).html(response);
	    }
	  });
	}

	function hour_refresh_template(push_id,campaign_types,current_template_id)
	{    
	  var page_id='<?php echo $page_auto_id;?>';
	  $.ajax({
	    type:'POST' ,
	    url: base_url+"sms_email_sequence/sms_email_template_sequence/1",
	    data: {push_id:push_id,campaign_types:campaign_types,current_template_id:current_template_id},
	    success:function(response){
	      $("#hour_sms_email_sequence_templates"+push_id).html(response);
	    }
	  });
	}

  $(document).ready(function($) {
      $(document).on('click','.ref_template',function(e){
        e.preventDefault();
        var push_id=$(this).attr('data-id');
        var campaign_types = $("input[name=campaign_types]:checked").val();
        var current_template_id = $("#template_id"+push_id).val();
        refresh_template(push_id,campaign_types,current_template_id);
      });

      $(document).on('click','.hour_ref_template',function(e){
        e.preventDefault();
        var push_id=$(this).attr('data-id');
        var campaign_types = $("input[name=campaign_types]:checked").val();
        var current_template_id = $("#hour_template_id"+push_id).val();
        hour_refresh_template(push_id,campaign_types,current_template_id);
      });

      $(document).on('click','#add_bot_settings',function(e){
        e.preventDefault();
        $("#add_bot_settings_modal").removeClass('hidden');
        $("#setting_list").hide();
        $("#alert_message").hide();
        $(".bot_success").hide();
        $("#error_message").addClass('hidden');
        $('html, body').animate({scrollTop: $("#add_bot_settings_modal").offset().top}, 2000);
      });

      $(document).on('change','input[name=campaign_types]',function(){  
        event.preventDefault();

        var how_many_days = '<?php echo $how_many_days;?>';
        var how_many_hours = '<?php echo $how_many_hours;?>';
        var default_display = '<?php echo $default_display;?>';
        var default_display_hour = '<?php echo $default_display_hour;?>';
        var campaign_types = $("input[name=campaign_types]:checked").val();
        var page_auto_id = '<?php echo $page_auto_id; ?>';

        var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:60px;padding:100px 0;"></i></div>';
        $("#sequence_body").html(loading);

        $.ajax({
          url: "<?php echo base_url('sms_email_sequence/get_selected_sequence_lists')?>",
          type:'POST',
          data: {how_many_days: how_many_days,how_many_hours:how_many_hours,default_display:default_display,default_display_hour:default_display_hour,campaign_types:campaign_types,page_auto_id:page_auto_id},
          success:function(response) {
            $("#sequence_body").html(response);
            $("#day_counter").val(default_display);
            $("#hour_counter").val(default_display_hour);
            hour_counter = default_display_hour;
            day_counter = default_display;
          }
        })
        
      });


      $(document).on('click','#add_more_day',function(e){
         e.preventDefault();
         var how_many_days='<?php echo $how_many_days;?>';
         how_many_days=parseInt(how_many_days);
         if(day_counter>=how_many_days) 
         {
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('You can add more days.')?>", 'error');
          return false;
         }
         day_counter++;
         $("#day_container"+day_counter).removeClass('hidden');
         $('#day_counter').val(day_counter);
      });

      $(document).on('click','#remove_last_day',function(e){
         e.preventDefault();
         if(day_counter<2) 
         {
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('You can not remove the last item.')?>", 'error');
          return false;
         }
         $("#day_container"+day_counter).addClass('hidden');
         day_counter--;
         $('#day_counter').val(day_counter);
      });

      $(document).on('click','#add_more_hour',function(e){
         e.preventDefault();
         var how_many_hours='<?php echo $how_many_hours;?>';
         how_many_hours=parseInt(how_many_hours);
         if(hour_counter>=how_many_hours) 
         {
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('You can not add more hour.')?>", 'error');
          return false;
         }
         hour_counter++;
         $("#hour_container"+hour_counter).removeClass('hidden');
         $('#hour_counter').val(hour_counter);
      });

      $(document).on('click','#remove_last_hour',function(e){
         e.preventDefault();
         if(hour_counter<2) 
         {
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('You can not remove the last item.')?>", 'error');        
          return false;
         }
         $("#hour_container"+hour_counter).addClass('hidden');
         hour_counter--;
         $('#hour_counter').val(hour_counter);
      });


      $(document).on('click','#submit_btn',function(e){
        e.preventDefault();

        var campaign_types=$("input[name=campaign_types]:checked").val();
        if(campaign_types =="")
        {
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Sequence Type is required.')?>", 'error');
          return;
        }

        var message_type = $(".nav-link.active").attr("href");

        var is_day_selected=false;
        $(".template_id").each(function(){
          if($(this).val()!='') is_day_selected=true;          
        });

        var is_hour_selected=false;
        $(".hour_template_id").each(function(){
          if($(this).val()!='') is_hour_selected=true;          
        });

        if(!is_day_selected && !is_hour_selected)
        {
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('You have not selected template for any day or hour.')?>", 'error');
          return;
        }

        if(is_day_selected)
        {
          var between_start=$("#between_start").val();
          var between_end=$("#between_end").val();
          var rep1 = parseFloat(between_start.replace(":", "."));
          var rep2 = parseFloat(between_end.replace(":", "."));
          var rep_diff=rep2-rep1;

          if((between_start== '' &&  between_end!= '') || (between_start!= '' &&  between_end== ''))
          {
            swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('You must select both starting and closing time for daily sequence.')?>", 'error');
            return false;
          }

          if(between_start!="" && between_end!="")
          {
            if(rep1 >= rep2 || rep_diff<1.0)
            {
              swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Daily sequence starting time must be smaller than closing time and need to have minimum one hour time span.')?>", 'error');
              return false;
            }
            if($("#timezone").val()=="")
            {
               swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select time zone for daily sequence.')?>", 'error');
               return false;
            }
          }
        }      

            
        $(this).addClass('btn-progress');

        var queryString = new FormData($("#sms_email_sequence_form")[0]);
        $.ajax({
          context : this,
          type:'POST' ,
          url: base_url+"sms_email_sequence/create_sequence_campaign_action",
          data: queryString,
          dataType : 'JSON',
          cache: false,
          contentType: false,
          processData: false,
          success:function(response){
            $(this).removeClass('btn-progress');

            if(response.status=='1')
            {
              swal('<?php echo $this->lang->line("Campaign Created"); ?>', response.message, 'success').then((value) => {
                window.location.assign("<?php echo base_url('sms_email_sequence/sms_email_sequence_message_campaign/'.$page_auto_id.'/1');?>");
              });
            }

            else swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
          }

        });

      });


      $(document).on('click','.delete_bot',function(e){
        e.preventDefault();
        var id = $(this).attr('id');  
        var cam_type = $(this).attr("campaignType");    
        var somethingwentwrong = "<?php echo $somethingwentwrong; ?>";

        swal({
          title: '<?php echo $this->lang->line("Delete Campaign"); ?>',
          text: '<?php echo $this->lang->line("Do you really want to delete this campaign?"); ?>',
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) 
          {
              $(this).parent().prev().addClass('btn-progress');
              $.ajax({
                context: this,
                type:'POST' ,
                url: "<?php echo base_url('sms_email_sequence/delete_sequecne_campaign')?>",              
                data: {id:id,page_auto_id:page_auto_id,cam_type:cam_type},
                success:function(response){ 
                   $(this).parent().prev().removeClass('btn-progress');
                   if(response == '1')
                   {
                     swal('<?php echo $this->lang->line("Campaign Deleted"); ?>', "<?php echo $this->lang->line('Camapign has been deleted successfully.')?>", 'success');
                     window.location.assign("<?php echo base_url('sms_email_sequence/sms_email_sequence_message_campaign/'.$page_auto_id.'/1');?>");
                   }
                   else
                   {
                     swal('<?php echo $this->lang->line("Error"); ?>', somethingwentwrong, 'error');
                   }
                }
              });
          } 
        });
      });


      $(document).on('click','.message_content',function(e){
        e.preventDefault();
        var campaign_id = $(this).attr('data-id'); // campaign id
        var is_day = $(this).attr('data-day');
        $('#sms_email_message_content_modal_content').html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>');  
        $("#sms_email_message_content_modal").modal(); 
        $.ajax({
          type:'POST' ,
          url:"<?php echo site_url();?>sms_email_sequence/get_campaign_report",
          data:{campaign_id:campaign_id,is_day:is_day},
          success:function(response){
             $('#sms_email_message_content_modal_content').html(response);  
          }
        });
      });

      /* Creating Firstname text button for summernote texteditor */
      var firstName = function (context) {
        var ui = $.summernote.ui;

        // create button
        var button = ui.button({
          contents: '<i class="fas fa-user"/> ',
          container: 'body',
          tooltip: '<?php echo $this->lang->line("You can include #FIRST_NAME# variable inside your message. The variable will be replaced by real name when we will send it.") ?>',
          click: function () {
            context.invoke('editor.insertText', ' #FIRST_NAME# ');
          }
        });

        return button.render(); 
      }

      /* creating Lastname text button for summernote texteditor */
      var lastName = function (context) {
        var ui = $.summernote.ui;

        // create button
        var button = ui.button({
          contents: '<i class="fas fa-user-circle"></i>',
          container: 'body',
          tooltip: '<?php echo $this->lang->line("You can include #LAST_NAME# variable inside your message. The variable will be replaced by real name when we will send it.") ?>',
          click: function () {
            context.invoke('editor.insertText', ' #LAST_NAME# ');
          }
        });

        return button.render();
      }

      /* Creating Unsubscriber text button for summernote texteditor */
      var unsubscriberlink = function (context) {
        var ui = $.summernote.ui;

        // create button
        var button = ui.button({
          contents: '<i class="fas fa-bell-slash"/>',
          container: 'body',
          tooltip: '<?php echo $this->lang->line("You can include #UNSUBSCRIBE_LINK# variable inside your message. The variable will be replaced by real value when we will send it.") ?>',
          click: function () {
            context.invoke('editor.insertText', ' #UNSUBSCRIBE_LINK# ');
          }
        });

        return button.render();
      }

      $(document).on('click','.lead_first_name',function(){
        
        var textAreaTxt = $(this).parent().next("textarea").val();
        
        var lastIndex = textAreaTxt.lastIndexOf("<br>");   
        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
        lastTag = lastTag.trim(lastTag);

        if(lastTag=="<br>")
          textAreaTxt = textAreaTxt.substring(0, lastIndex); 
          
        var txtToAdd = " #FIRST_NAME# ";
        var new_text = textAreaTxt + txtToAdd;
        $(this).parent().next("textarea").val(new_text);
            
      });

      $(document).on('click','.lead_last_name',function(){

        var textAreaTxt = $(this).parent().next().next("textarea").val();
        
        var lastIndex = textAreaTxt.lastIndexOf("<br>");   
        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
        lastTag=lastTag.trim(lastTag);

        if(lastTag=="<br>")
          textAreaTxt = textAreaTxt.substring(0, lastIndex); 
          
        var txtToAdd = " #LAST_NAME# ";
        var new_text = textAreaTxt + txtToAdd;
        $(this).parent().next().next("textarea").val(new_text);
           
      });

      $(document).on('click', '.add_template', function(event) {
        event.preventDefault();
        $("#sms_email_template_modal").modal();
        var campaign_type = $("input[name=campaign_types]:checked").val();
        $("#save_template").attr("button-type",campaign_type);

        if(campaign_type == 'email') {
          $("#name-div").addClass('col-md-6')
          $("#subject-div").css("display","block");
          // $('#template_contents').summernote('reset');
          // $("#template_contents").summernote();

          $('#template_contents').summernote({
            height: 300,  
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture','video']],
                ['view', ['codeview']],
                ['mybutton', ['first_name','last_name','unsubscriberLink']]
            ],

            buttons: {
              first_name: firstName,
              last_name: lastName,
              unsubscriberLink: unsubscriberlink,
            }
          });

          $(".button-outline").hide();

          $('div.note-group-select-from-files').remove();
        } else {
          $(".button-outline").show();
          $("#subject-div").css("display","none");
        }

      });

      $(document).on('click', '#save_template', function(event) {
        event.preventDefault();

        var type = $(this).attr("button-type");
        var temp_name = $("#template_name").val();
        var csrf_token = $("#sms_email_sequence_csrf_token").val();
        var temp_subject = "";
        var temp_contents = $("#template_contents").val();
        
        if(type == 'email') {
          temp_subject = $("#template_subject").val();
        }

        $(this).addClass('btn-progress');

        $.ajax({
          context:this,
          url: base_url+'sms_email_manager/create_template_action',
          type: 'POST',
          dataType: 'JSON',
          data: {template_type:type,temp_name:temp_name,temp_subject:temp_subject,temp_contents:temp_contents,csrf_token:csrf_token},
          success:function(response) {

            $(this).removeClass('btn-progress');
            if (true === response.error) {
              swal({title: 'Error!',text: response.message,icon: 'error'});
            } else if(response.status == "1") {
              $("#sms_email_template_modal").modal('hide');
              iziToast.success({title: '',message: response.message,position: 'bottomRight'});
            } else {
              iziToast.error({title: '',message: response.message,position: 'bottomRight'});

            }
          }
        })
        
      });

      $("#sms_email_template_modal").on('hidden.bs.modal',function(){
        $("#template_name").val("");
        $("#template_subject").val("");
        $("#template_contents").val("");
        $("#template_contents").summernote('destroy');
      });
  });
</script>


<div class="modal fade" id="sms_email_message_content_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-full">
    <div class="modal-content">
      <div class="modal-header smallpadding">
        <h5 class="modal-title"><i class="fa fa-eye"></i> <?php echo $this->lang->line('Campaign Report'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body smallpadding" id="sms_email_message_content_modal_content"></div>
    </div>
  </div>
</div>

<div class="modal fade" id="sms_email_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" style="width: 95% !important;max-width: 95% !important;">
    <div class="modal-content">
      <div class="modal-header bbw">
        <h5 class="modal-title text-primary"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Template'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
          <div class="row">
              <div class="col-12" id="name-div">
                  <div class="form-group">
                      <label><?php echo $this->lang->line("Template Name"); ?></label>
                      <input type="text" class="form-control" name="template_name" id="template_name">
                  </div>
              </div>
              <div class="col-12 col-md-6" id="subject-div">
                  <div class="form-group">
                      <label><?php echo $this->lang->line("Subject"); ?></label>
                      <input type="text" class="form-control" name="template_subject" id="template_subject">
                  </div>
              </div>
              <div class="col-12">
                  <div class="form-group">
                      <label><?php echo $this->lang->line("content"); ?></label>
                      <span class='float-right'> 
                        <a title="<?php echo $this->lang->line("You can include #LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                      </span>
                      <span class='float-right'> 
                        <a title="<?php echo $this->lang->line("You can include #FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                      </span>
                      <textarea name="template_contents" id="template_contents" class="form-control template_contents"></textarea>
                  </div>
              </div>
          </div>
      </div>
      <div class="modal-footer bg-whitesmoke">
          <button class="btn btn-lg btn-primary" button-type="" id="save_template" name="save_template" type="button"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save") ?> </button>
          <a class="btn btn-lg btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
      </div>
    </div>
  </div>
</div>
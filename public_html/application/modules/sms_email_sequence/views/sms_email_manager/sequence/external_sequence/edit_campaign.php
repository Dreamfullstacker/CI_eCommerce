<input type="hidden" name="sms_email_sequence_csrf_token" id="sms_email_sequence_csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>      
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("messenger_bot_broadcast"); ?>"><?php echo $this->lang->line("Broadcasting"); ?></a></div>
            <div class="breadcrumb-item"><a href="<?php echo base_url("sms_email_sequence/external_sequence_lists"); ?>"><?php echo $this->lang->line("Campaign List"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="#" enctype="multipart/form-data" id="sms_email_sequence_form" style="padding-left: 0;">
                            <input type="hidden" name="day_counter" id="day_counter" value="<?php echo $default_display;?>">
                            <input type="hidden" name="hour_counter" id="hour_counter" value="<?php echo $default_display_hour;?>">
                            <input type="hidden" name="page_id" id="page_id" value="0">
                            <input type="hidden" name="campaign_id" id="campaign_id" value="<?php echo $xdata['id'];?>">
                        
                            <div class="row">
                                <div class="form-group col-12">             
                                    <label><?php echo $this->lang->line("Campaign Name"); ?></label>
                                    <input type="text" name="campaign_name" id="campaign_name" class="form-control" value="<?php echo $xdata['campaign_name'];?>">  
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-12 col-md-12">
                                    <label class="form-label">
                                        <?php echo $this->lang->line("Sequence Type"); ?>
                                    </label>
                                    <div class="selectgroup selectgroup-pills">

                                        <label class="selectgroup-item">
                                            <input type="radio" name="campaign_types" id="campaign_types_email" value="email" class="selectgroup-input" checked>
                                            <span class="selectgroup-button"><?php echo $this->lang->line("Email"); ?></span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="campaign_types" id="campaign_types_sms" value="sms" class="selectgroup-input">
                                            <span class="selectgroup-button"><?php echo $this->lang->line("SMS"); ?></span>
                                        </label>
                                    </div>                   
                                </div>

                                <div class="col-12">
                                    <div class="hidden" id="sms_api_lists">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('Select SMS API'); ?></label>
                                            <select class="form-control select2" id="sms_api_id" name="sms_api_id" style="width:100%;">
                                                <option value=''><?php echo $this->lang->line('Select API');?></option>
                                                <?php 
                                                  foreach($sms_option as $id=>$option)
                                                  {
                                                    $smsselected = '';
                                                    if($id == $xdata['external_sequence_sms_api_id']) $smsselected = 'selected';
                                                    echo "<option value='{$id}' {$smsselected}>{$option}</option>";
                                                  }
                                                ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="hidden" id="email_api_lists">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('Select Email API'); ?></label>
                                            <select class="form-control select2" id="email_api_id" name="email_api_id" style="width:100%;">
                                              <option value=''><?php echo $this->lang->line('Select API');?></option>
                                              <?php 
                                                  foreach($email_apis as $id=>$option)
                                                  {
                                                    $emailselected = '';
                                                    if($id == $xdata['external_sequence_email_api_id']) $emailselected = 'selected';
                                                    echo "<option value='{$id}' {$emailselected}>{$option}</option>";
                                                  }
                                              ?>
                                            </select>
                                        </div>
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
                                            <a class="nav-link active" id="sequence_tab2" data-toggle="tab" href="#hourwise" role="tab" aria-controls="profile" aria-selected="false"><?php echo  $this->lang->line("24 Hour"); ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="sequence_tab1" data-toggle="tab" href="#daywise" role="tab" aria-selected="true"><?php echo  $this->lang->line("Daily"); ?></a>
                                        </li>
                                    </ul>
                                    <div class="tab-content tab-bordered">
                                        <div class="tab-pane fade" id="daywise" role="tabpanel" aria-labelledby="sequence_tab1">
                                            <div class="row">
                                                <div class="col-6 col-md-4">
                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line("Starting Time")." ".$tooplip1;?></label>
                                                        <input type="text" class="form-control timepicker_x"value="<?php echo $xdata['between_start'];?>" id="between_start" name="between_start">
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-4">
                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line("Closing Time")." ".$tooplip1;?></label>
                                                        <input type="text" class="form-control timepicker_x" value="<?php echo $xdata['between_end'];?>" id="between_end" name="between_end">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line("Time Zone");?></label>
                                                        <?php $selcted_timezone = ($xdata['timezone']!="") ? $xdata['timezone'] : $this->config->item('time_zone'); ?>
                                                        <?php echo form_dropdown('timezone', $timezones,$selcted_timezone,"class='form-control select2' id='timezone' style='width:100%'");?>
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
                                                            $message_content=json_decode($xdata['message_content'],true);
                                                            $select_template=isset($message_content[$i])?$message_content[$i]:'';
                                                            $template_id="template_id".$i;
                                                            $template_list['']="--- ".$this->lang->line("Do not send message")." ---";
                                                            echo form_dropdown($template_id,$template_list, $select_template,'class="form-control template_id select2" id="'.$template_id.'" style="width:100%;"'); 
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
                                                        <a target="_BLANK" title="<?php echo $this->lang->line('Add New Template');?>" data-toggle="tooltip" class="btn btn-default btn-lg add_template" href=""><i class="fas fa-plus-circle"></i></a>
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
                                                            $message_content_hourly=json_decode($xdata['message_content_hourly'],true);
                                                            $select_template_hourly=isset($message_content_hourly[$minutes])?$message_content_hourly[$minutes]:'';
                                                            $template_id="hour_template_id".$i;
                                                            $template_list['']="--- ".$this->lang->line("Do not send message")." ---";
                                                            echo form_dropdown($template_id,$template_list, $select_template_hourly,'class="form-control hour_template_id select2" id="'.$template_id.'" style="width:100%;"'); 
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

                    <div class="card-footer bg-whitesmoke">
                        <a id="submit_btn" href="" class="btn btn-lg btn-primary"><i class="fas fa-edit"></i> <?php echo $this->lang->line('Edit Campaign');?></a>             
                        <a href="#" class="btn btn-lg btn-secondary float-right" onclick='goBack("sms_email_sequence/external_sequence_lists",0)'><i class="fas fa-times"></i> <?php echo $this->lang->line('Back');?></a> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
    var base_url="<?php echo site_url(); ?>";

    function refresh_template(push_id,campaign_types,current_template_id)
    { 
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
        $.ajax({
            type:'POST' ,
            url: base_url+"sms_email_sequence/sms_email_template_sequence/1",
            data: {push_id:push_id,campaign_types:campaign_types,current_template_id:current_template_id},
            success:function(response){
                $("#hour_sms_email_sequence_templates"+push_id).html(response);
            }
        });
    }

    $("document").ready(function(){

        $(".timepicker_x").datetimepicker({
            datepicker:false,
            format:"H:i"
        });

        var campaign_type='<?php echo $xdata["campaign_type"];?>';
        $('input[type="radio"][name="campaign_types"][value="'+campaign_type+'"]').attr('checked','checked');
        $('input[type="radio"][name="campaign_types"][value="'+campaign_type+'"]').trigger('change');


        if(campaign_type == "email") {
            $("#email_api_lists").removeClass('hidden');
        }

        if(campaign_type == "sms") {
            $("#sms_api_lists").removeClass('hidden');
        }


        $(document).on('click','.ref_template',function(e){
            e.preventDefault();
            var campaign_types = $("input[name=campaign_types]:checked").val();
            var push_id=$(this).attr('data-id');
            var current_template_id = $("#template_id"+push_id).val();
            refresh_template(push_id,campaign_types,current_template_id);
        });

        $(document).on('click','.hour_ref_template',function(e){
            e.preventDefault();
            var campaign_types = $("input[name=campaign_types]:checked").val();
            var push_id=$(this).attr('data-id');
            var current_template_id = $("#hour_template_id"+push_id).val();
            hour_refresh_template(push_id,campaign_types,current_template_id);
        }); 

        $(document).on('click','#add_more_day',function(e){
            e.preventDefault();
            var day_counter = $("#day_counter").val(); 
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
            var day_counter = $("#day_counter").val(); 
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
            var hour_counter = $("#hour_counter").val();
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
            var hour_counter = $("#hour_counter").val();
            if(hour_counter<2) 
            {
                swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('You can not remove the last item.')?>", 'error');        
                return false;
            }
            $("#hour_container"+hour_counter).addClass('hidden');
            hour_counter--;
            $('#hour_counter').val(hour_counter);
        });

        $(document).on('change','input[name=campaign_types]',function(){  
            event.preventDefault();

            if($(this).val() == "email") {
                $("#email_api_lists").removeClass('hidden');
                $("#sms_api_lists").addClass('hidden');
            }

            if($(this).val() == "sms") {
                $("#sms_api_lists").removeClass('hidden');
                $("#email_api_lists").addClass('hidden');
            }

            var how_many_days = '<?php echo $how_many_days;?>';
            var how_many_hours = '<?php echo $how_many_hours;?>';

            var default_display = '<?php echo $default_display;?>';
            var default_display_hour = '<?php echo $default_display_hour;?>';
            var campaign_types = $("input[name=campaign_types]:checked").val();
            var page_auto_id = '0';
            var current_campaign_id = '<?php echo $xdata["id"];?>';
            var current_campaign_type = '<?php echo $xdata["campaign_type"];?>';

            var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:60px;padding:100px 0;"></i></div>';
            $("#sequence_body").html(loading);

            $.ajax({
                url: "<?php echo base_url('sms_email_sequence/edited_get_selected_sequence_lists')?>",
                type:'POST',
                data: {how_many_days: how_many_days,how_many_hours:how_many_hours,default_display:default_display,default_display_hour:default_display_hour,campaign_types:campaign_types,page_auto_id:page_auto_id,current_campaign_id:current_campaign_id,current_campaign_type:current_campaign_type},
                success:function(response) {
                    $("#sequence_body").html(response);
                    if(current_campaign_type != campaign_types) {
                        $("#day_counter").val("3");
                        $("#hour_counter").val("3");
                    } else {
                        $("#day_counter").val(default_display);
                        $("#hour_counter").val(default_display_hour);
                    }
                }
            })

        });

        $(document).on('click','#submit_btn',function(e){
            e.preventDefault();

            var campaign_name = $("#campaign_name").val();
            if(campaign_name =="")
            {
              swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Campaign Name is required.')?>", 'error');
              return;
            }


            var campaign_types=$("input[name=campaign_types]:checked").val();
            if(campaign_types =="")
            {
              swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Sequence Type is required.')?>", 'error');
              return;
            }

            if(campaign_types == "email" && $("#email_api_id").val() == "") {
                swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Email API is required.')?>", 'error');
                return;
            } else if(campaign_types == "sms" && $("#sms_api_id").val() == "") {
                swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('SMS API is required.')?>", 'error');
                return;
            }

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
                url: base_url+"sms_email_sequence/edit_sequence_message_campaign_action",
                data: queryString,
                dataType : 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                success:function(response){
                    $(this).removeClass('btn-progress');
                    if(response.status=='1')
                    {
                        swal('<?php echo $this->lang->line("Campaign Updated"); ?>', response.message, 'success').then((value) => {
                            window.location.assign("<?php echo base_url('sms_email_sequence/external_sequence_lists');?>");
                        });
                    }
                    else swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
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
            var campaign_type = $("input[name=campaign_types]:checked").val();
            $("#save_template").attr("button-type",campaign_type);
            $("#sms_email_template_modal").modal();

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

<div class="modal fade" id="sms_email_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" style="width: 70% !important;max-width: 70% !important;">
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
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
        <meta name="author" content="Coderthemes">

        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">
        <!-- App title -->
        <title><?php echo $this->defaultdata->gradLanguageText(216); ?></title>
        <!-- App css -->
        <?php echo $header_scripts;?>
        <link href="<?php echo DEFAULT_ASSETS_URL;?>plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
        
        
    </head>


    <body class="fixed-left">

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Top Bar Start -->
            <?php echo $header;?>
            <!-- Top Bar End -->


            <!-- ========== Left Sidebar Start ========== -->
            <?php echo $left_sidebar;?>
            <!-- Left Sidebar End -->



            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container">


            <div class="row">
                <div class="col-xs-12">
                    <div class="page-title-box">
                        <h4 class="page-title text-uppercase m-t-5 m-l-15"> <!-- Communicator --><?php echo $this->defaultdata->gradLanguageText(216);?></h4>
                       

                        <div class="clearfix"></div>

                        
                        
                        
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row">
              <div class="col-md-3 col-sm-4 chatleft">
                <?php echo $chat_left_panel; ?>
              </div>

              <div class="col-md-9 col-sm-8 chatright">
              <?php 
             //print_r($user_data);

             //print_r($chat_message); exit;
  

              if(!empty($user_data)) {?>
         
              
                    <!-- <h4 class="header-title m-t-0 m-b-30">Default Tabs</h4> -->
                    <div class="card-box table-responsive">    
                      <div class="row">
                        <div class="col-lg-12 ">
                            
                            
                          <h4 class="m-t-0 m-b-20 header-title"><b><!-- Admin -->
                          <?php echo $user_data->name; ?>
                          <!-- <?php echo $this->defaultdata->gradLanguageText(289);?> -->
                            
                          </b><i class="fa fa-comments-o m-l-10"></i></h4>

                          <div class="chat-conversation">
                            <ul class="conversation-list  slimscroll-alt" style="height: 380px; min-height: 332px;" id="showMessages">
                              <?php echo $singleMessage; ?>
                            </ul>
                            <div class="row">
                              <form name="agentCommunicatior" id="agentCommunicatior" action="post">
                                <div class="col-sm-9 chat-inputbar">
                                  <input type="hidden" name="to_user_id" id="to_user_id" value="<?php echo ($user_data) ? $user_data->id : '';?>">
                                  <input class="form-control chat-input" placeholder="Enter your text" name="message" id="message" />
                                </div>
                                <div class="col-sm-3 chat-send">
                                  <button type="submit" class="btn btn-md btn-info btn-block waves-effect waves-light"><?php echo $this->defaultdata->gradLanguageText(117);?></button>
                                </div>
                              </form>  
                            </div>
                          </div>
                                                            
                        </div>
                      </div>
                    </div>
                        
               
         

            <?php } ?>
            </div>

          </div>

                 

      



        </div> <!-- container -->

    </div> <!-- content -->

    <?php echo $footer; ?>

</div>


            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->


            <!-- Right Sidebar -->
            <?php echo $right_sidebar; ?>
            <!-- /Right-bar -->

        </div>
        <!-- END wrapper -->

<?php echo $footer_scripts; ?>


<script type="text/javascript" src="<?php echo DEFAULT_ASSETS_URL;?>plugins/parsleyjs/parsley.min.js"></script>
<script type="text/javascript" src="<?php echo DEFAULT_ASSETS_URL;?>js/jquery.form.min.js"></script>

<script type="text/javascript">
  $(document).on("click",".chatMmbrList ul li a",function(event){
      //event.preventDefault();
      $(this).parent().parent().children().children().removeClass("sl");
      $(this).addClass("sl");
     
    });

$("#message").keypress(function(event) {
    if (event.which == 13) {
        event.preventDefault();
        $("#agentCommunicatior").submit();
    }
});



$(document).on('submit',"#agentCommunicatior",function(e){ 
  e.preventDefault();
  var to_user_id = $("#to_user_id").val();
  var message = $("#message").val();
  if(to_user_id!='' && message!='')
  {
    //alert('dddd');
    $.ajax({
        type:'POST',
        data:{to_user_id:to_user_id, message:message},
        url : "<?php echo base_url('admin/client/postChat'); ?>",
        dataType: 'json',
        success : function(data){ 
          $('#showMessages').append(data.singleMessage);
          $("#message").val('');
          $('#showMessages').scrollTo('100%', '100%', {
            easing: 'swing'
          });

          $('.chatleft').html(data.chat_left_panel);
          
        }
    });
  }

  
});  
</script>


<script type="text/javascript">
function loadUnreadMessages()
{
    var from_user_id = $("#to_user_id").val();
    if(from_user_id)
    {
    $.ajax({
        type:'POST',
        data:{from_user_id:from_user_id},
        url : "<?php echo base_url('admin/client/loadUnreadMessages'); ?>",
        dataType: 'json',
        success : function(data){ 
            if(data.singleMessage)
            {      
                $('#showMessages').append(data.singleMessage);
                $('#showMessages').scrollTo('100%', '100%', {
                easing: 'swing'
                });


            }
            $('.chatleft').html(data.chat_left_panel);
        }
    });
    }
}    
</script>

<script type="text/javascript">
$(document).ready(function(){
    setInterval(function() {
        loadUnreadMessages();
    }, 5000);
});
</script>




 

        
    </body>
</html>
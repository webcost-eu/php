<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
        <meta name="author" content="Coderthemes">
        <!-- App title -->
        <title><?php echo $this->defaultdata->gradLanguageText(1);?></title>

        <link href="<?php echo DEFAULT_ASSETS_URL;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo DEFAULT_ASSETS_URL;?>plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
        <?php echo $header_scripts;?>
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
                                    <h4 class="page-title"><!-- Dashboard --><?php echo $this->defaultdata->gradLanguageText(1);?></h4>
                                    
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                        <div class="row">
                            <div class="col-sm-12">
                                <?php if($password_change_worning){ ?>
                                <div class="alert alert-icon alert-danger alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    <?php echo $password_change_worning;?>
                                </div>
                                <?php } ?>
                            </div>
                        </div>


                        <?php /*<div class="row">

                           <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <h4 class="m-t-0 header-title"><b>New Client</b></h4>
                                    

                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name Of Client</th>
                                            <th>Type of accident</th>
                                            <th>Agent</th>
                                            <th>Date of adding to System</th>
                                        </tr>
                                        </thead>


                                        <tbody>
                                        <?php 
                                        if(count($allClients) > 0){
                                            foreach($allClients as $key => $user){
                                        ?>
                                        <tr>
                                            <td><?php echo $key+1; ?></td>
                                            <td><a href="<?php echo base_url('agent/client/details/'.$user->user_id); ?>" target="_blank"><?php echo $user->name; ?></a></td>
                                            <td><?php echo $user->type_of_accident; ?></td>
                                            <td><?php echo $user->agent_name; ?></td>        
                                            <td> <?php echo $user->postedtime?date('d/m/Y', $user->postedtime):''; ?></td>
                                        </tr>
                                        <?php } } ?>
                                        
                                        </tbody>
                                    </table>
                                </div>
                            </div> 

                        </div>*/ ?>
                        <!-- end row -->

                        <div class="row">

                           <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <h4 class="m-t-0 header-title"><b><!-- Tasks --><?php echo $this->defaultdata->gradLanguageText(32);?></b></h4>

                                    <form role="form" method="get" class="row" id="search_task">
                                        <div class="col-sm-12">
                                            
                                            <div class="form-group">
                                                <div class="checkbox checkbox-success">
                                                    <input id="my_task" name="my_task" value="Y" type="checkbox" class="taskInput" checked="checked">
                                                    <label for="my_task"> <!-- Show only my tasks --><?php echo $this->defaultdata->gradLanguageText(33);?> </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox checkbox-pink">
                                                    <input id="priority_task" name="priority_task" value="Y" type="checkbox" class="taskInput">
                                                    <label for="priority_task"><!--  Priority tasks --><?php echo $this->defaultdata->gradLanguageText(34);?> </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="today_delayed" name="today_delayed" value="Y" type="checkbox" class="taskInput">
                                                    <label for="today_delayed"> <!-- Today and delayed tasks --><?php echo $this->defaultdata->gradLanguageText(35);?> </label>
                                                </div>
                                            </div>
                                       </div>
                                    </form>

                                    <div id="taskTableData"><?php echo $taskTable; ?></div>
                                </div>
                            </div> 

                        </div>
                       

                        <?php /*<div class="row">

                           <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <h4 class="m-t-0 header-title"><b>Reminder</b></h4>
                                    

                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name Of Client</th>
                                            <th>Taks Subject</th>
                                            <th>Deadline</th>
                                            <th>Task for</th>
                                            <th>Task made by</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>


                                        <tbody>
                                        <?php 
                                        if(count($reminderData) > 0){
                                            foreach($reminderData as $key => $val){
                                        ?>
                                        <tr <?php echo $val->priority=='Y'?'class="danger"':''; ?>>
                                            <td><?php echo $key+1; ?></td>
                                            <td><a href="<?php echo base_url('agent/client/details/'.$val->client_id); ?>" target="_blank"><?php echo $val->client_name; ?></a></td>
                                            <td><?php echo $val->subject; ?></td>
                                            <td> <?php echo $val->deadline?date('d/m/Y', $val->deadline):''; ?></td>
                                            <td><?php echo $val->assigned_user_name; ?></td> <td><?php echo $val->created_user_name; ?></td>
                                            <td>
                                            <?php if($val->status=='P'){ ?>
                                                <button type="button" class="btn btn-success btn-rounded w-xs waves-effect waves-light m-b-5 btn-xs completeMyTask "  data-id="<?php echo $val->id;?>">Done</button>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php } } ?>
                                        
                                        </tbody>
                                    </table>
                                </div>
                            </div> 

                        </div>*/ ?>

                        <div class="row">
                           <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <h4 class="m-t-0 header-title"><b><!-- New comments --><?php echo $this->defaultdata->gradLanguageText(43);?></b></h4>
                                    
                                    <form role="form" method="get" class="row" id="search_comment">
                                        <div class="col-sm-12">
                                            
                                            <div class="form-group">
                                                <div class="checkbox checkbox-success">
                                                    <input id="show_my_clients" name="userType[]" value="me" type="checkbox" class="commentInput" checked="checked">
                                                    <label for="show_my_clients"> <!-- Show only in my cases --><?php echo $this->defaultdata->gradLanguageText(456);?> </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox checkbox-success">
                                                    <input id="show_from_agent" name="userType[]" value="2" type="checkbox" class="commentInput">
                                                    <label for="show_from_agent"> <!-- Show from Agents --><?php echo $this->defaultdata->gradLanguageText(44);?> </label>
                                                </div>
                                            </div>
                                            <!-- <div class="form-group">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="show_from_solicitor" name="userType[]" value="3" type="checkbox" class="commentInput">
                                                    <label for="show_from_solicitor"> Show from Solicitors </label>
                                                </div>
                                            </div> -->
                                            <div class="form-group">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="show_from_consultant" name="userType[]" value="5" type="checkbox" class="commentInput">
                                                    <label for="show_from_consultant"> <!-- Show from Consultants --><?php echo $this->defaultdata->gradLanguageText(46);?> </label>
                                                </div>
                                            </div>

                                       </div>
                                    </form>
                                    
                                    <div id="commentTableData"><?php echo $commentTable; ?></div>
                                </div>
                            </div> 
                        </div>


                        <div class="row">
                           <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <h4 class="m-t-0 header-title"><b><!-- Calendar event --><?php echo $this->defaultdata->gradLanguageText(50);?></b></h4>
                                    
                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                        <tr>
                                            <th><!-- No --><?php echo $this->defaultdata->gradLanguageText(199);?></th>
                                            <th><!-- Date of event --><?php echo $this->defaultdata->gradLanguageText(51);?></th>
                                            <th><!-- Title of event --><?php echo $this->defaultdata->gradLanguageText(52);?></th>
                                            <th><!-- User --><?php echo $this->defaultdata->gradLanguageText(47);?></th>
                                        </tr>
                                        </thead>


                                        <tbody>
                                        <?php 
                                        if(count($calendarEvent) > 0){
                                            foreach($calendarEvent as $key => $val){
                                        ?>
                                        <tr>
                                            <td><?php echo $key+1; ?></td>
                                            <td><?php echo $val->starttime?date('d/m/Y', $val->starttime):''; ?></td>
                                            <td><?php echo $val->title; ?></td>
                                            <td><?php echo $val->user_name; ?></td>
                                        </tr>
                                        <?php } } ?>
                                        
                                        </tbody>
                                    </table>
                                </div>
                            </div> 
                        </div>

                        

                        <?php /*<div class="row">

                           <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <h4 class="m-t-0 header-title"><b>Log in/out</b></h4>
                                    

                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Log in</th>
                                            <th>Log out</th>
                                            <th>Name of user</th>
                                        </tr>
                                        </thead>


                                        <tbody>
                                        <?php 
                                        if(count($allUser) > 0){
                                            foreach($allUser as $key => $val){
                                        ?>
                                        <tr>
                                            <td><?php echo $key+1; ?></td>
                                            <td><?php echo isset($val->userLoginData->lastlogintime) && $val->userLoginData->lastlogintime?date('H:i d/m/Y', $val->userLoginData->lastlogintime):''; ?></td>
                                            <td><?php echo isset($val->userLoginData->logouttime) && $val->userLoginData->logouttime?date('H:i d/m/Y', $val->userLoginData->logouttime):''; ?></td>
                                            <td>
                                                <a href="<?php echo $val->profile_url; ?>" target="_blank"><?php echo $val->name; ?></a>
                                            </td>
                                        </tr>
                                        <?php } } ?>
                                        
                                        </tbody>
                                    </table>
                                </div>
                            </div> 

                        </div>*/ ?>
                        
                        <!-- end row -->




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
        <!-- Counter js  -->
        



<script type="text/javascript">
toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "progressBar": false,
  "positionClass": "toast-top-right",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}    
</script>

<script type="text/javascript">
$(document).on('click','.completeMyTask',function(){ 
    var $this = $(this);
    var id = $(this).attr('data-id');
    if(confirm('<?php echo $this->defaultdata->gradLanguageText(403);?>'))
    {
        $.ajax({
            type:"POST",
            url: "<?php echo base_url('agent/client/completeClientTask'); ?>",
            data:{id:id},
            dataType: 'json',
            success:function(data){
                //Command: toastr["success"]("You Have Successfully Complete This Task.");
                Command: toastr["success"]("<?php echo $this->defaultdata->gradLanguageText(404); ?>");
                $this.hide();
            }
        });
    }
});     
</script>

<script type="text/javascript">
$(document).on('change','.taskInput',function(){
    //$(".taskInput").not(this).attr('checked', false);
    $('#search_task').submit();
});

$(document).on('submit',"#search_task",function(e){ 
    e.preventDefault();
    var me=$(this);
    var formData = new FormData(this);
    //console.log(formData);
    $.ajax({
        type:'POST',
        data:formData,
        url : "<?php echo base_url('agent/dashboard/searchTask'); ?>",
        contentType: false,
        cache: false,
        processData:false,
        dataType: 'json',
        success : function(data){ 
          $('#taskTableData').html(data.taskTable);
          $('.datatable').DataTable({
            'destroy': true,
            "language": {
                "lengthMenu": "<?php echo $this->defaultdata->gradLanguageText(472);?> _MENU_ <?php echo $this->defaultdata->gradLanguageText(473);?>",
                "zeroRecords": "<?php echo $this->defaultdata->gradLanguageText(478);?>",
                "info": "<?php echo $this->defaultdata->gradLanguageText(474);?> _PAGE_ <?php echo $this->defaultdata->gradLanguageText(481);?> _PAGES_ <?php echo $this->defaultdata->gradLanguageText(477);?>",
                "infoEmpty": "<?php echo $this->defaultdata->gradLanguageText(479);?>",
                "infoFiltered": "(<?php echo $this->defaultdata->gradLanguageText(482);?> _MAX_ <?php echo $this->defaultdata->gradLanguageText(483);?>)",
                "sSearch": "<?php echo $this->defaultdata->gradLanguageText(471);?>",
                "paginate": {
                "previous": "<?php echo $this->defaultdata->gradLanguageText(475);?>",
                "next": "<?php echo $this->defaultdata->gradLanguageText(476);?>"
                }
            }
        });
        }
    });
});    
</script>

<script type="text/javascript">
$(document).on('change','.commentInput',function(){ 
    $(".commentInput").not(this).attr('checked', false);
    $('#search_comment').submit();
});

$(document).on('submit',"#search_comment",function(e){ 
    e.preventDefault();
    var me=$(this);
    var formData = new FormData(this);
    //console.log(formData);
    $.ajax({
        type:'POST',
        data:formData,
        url : "<?php echo base_url('agent/dashboard/searchComment'); ?>",
        contentType: false,
        cache: false,
        processData:false,
        dataType: 'json',
        success : function(data){ 
          $('#commentTableData').html(data.commentTable);
          $('.datatable').DataTable({
            'destroy': true,
            "language": {
                "lengthMenu": "<?php echo $this->defaultdata->gradLanguageText(472);?> _MENU_ <?php echo $this->defaultdata->gradLanguageText(473);?>",
                "zeroRecords": "<?php echo $this->defaultdata->gradLanguageText(478);?>",
                "info": "<?php echo $this->defaultdata->gradLanguageText(474);?> _PAGE_ <?php echo $this->defaultdata->gradLanguageText(481);?> _PAGES_ <?php echo $this->defaultdata->gradLanguageText(477);?>",
                "infoEmpty": "<?php echo $this->defaultdata->gradLanguageText(479);?>",
                "infoFiltered": "(<?php echo $this->defaultdata->gradLanguageText(482);?> _MAX_ <?php echo $this->defaultdata->gradLanguageText(483);?>)",
                "sSearch": "<?php echo $this->defaultdata->gradLanguageText(471);?>",
                "paginate": {
                "previous": "<?php echo $this->defaultdata->gradLanguageText(475);?>",
                "next": "<?php echo $this->defaultdata->gradLanguageText(476);?>"
                }
            }
        });
        }
    });
});    
</script>

    </body>
</html>
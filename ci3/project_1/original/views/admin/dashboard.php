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
                                    <h4 class="page-title"><?php echo $this->defaultdata->gradLanguageText(1);?></h4>
                                    
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
                        
                        <div class="row">

                           <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <h4 class="m-t-0 header-title"><b><?php echo $this->defaultdata->gradLanguageText(27);?></b></h4>
                                    

                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                        <tr>
                                            <th><?php echo $this->defaultdata->gradLanguageText(28);?></th>
                                            <th><?php echo $this->defaultdata->gradLanguageText(29);?></th>
                                            <th><?php echo $this->defaultdata->gradLanguageText(30);?></th>
                                            <th><?php echo $this->defaultdata->gradLanguageText(3);?></th>
                                            <th><?php echo $this->defaultdata->gradLanguageText(31);?></th>
                                        </tr>
                                        </thead>


                                        <tbody>
                                        <?php 
                                        if(count($allClients) > 0){
                                            foreach($allClients as $key => $user){
                                        ?>
                                        <tr>
                                            <td><?php echo $key+1; ?></td>
                                            <td><a href="<?php echo base_url('admin/client/details/'.$user->user_id); ?>" target="_blank"><?php echo $user->name; ?></a></td>
                                            <td><?php echo $user->type_of_accident; ?></td>
                                            <td><?php echo $user->agent_name; ?></td>        
                                            <td> <?php echo $user->postedtime?date('d/m/Y', $user->postedtime):''; ?></td>
                                        </tr>
                                        <?php } } ?>
                                        
                                        </tbody>
                                    </table>
                                </div>
                            </div> 

                        </div>
                        <!-- end row -->

                        <div class="row">

                           <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <h4 class="m-t-0 header-title"><b><?php echo $this->defaultdata->gradLanguageText(32);?></b></h4>

                                    <form role="form" method="get" class="row" id="search_task">
                                        <div class="col-sm-12">
                                            
                                            <div class="form-group">
                                                <div class="checkbox checkbox-success">
                                                    <input id="my_task" name="my_task" value="Y" type="checkbox" class="taskInput" checked="checked">
                                                    <label for="my_task"> <?php echo $this->defaultdata->gradLanguageText(33);?> </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox checkbox-pink">
                                                    <input id="priority_task" name="priority_task" value="Y" type="checkbox" class="taskInput">
                                                    <label for="priority_task"> <?php echo $this->defaultdata->gradLanguageText(34);?> </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="today_delayed" name="today_delayed" value="Y" type="checkbox" class="taskInput">
                                                    <label for="today_delayed"> <?php echo $this->defaultdata->gradLanguageText(35);?> </label>
                                                </div>
                                            </div>
                                       </div>
                                    </form>

                                    <div id="taskTableData"><?php echo $taskTable; ?></div>

                                </div>
                            </div> 

                        </div>
                       

                        <div class="row">

                           <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <h4 class="m-t-0 header-title"><b><?php echo $this->defaultdata->gradLanguageText(42);?></b></h4>
                                    
                                    <form role="form" method="get" class="row" id="search_reminder">
                                        <div class="col-sm-12">                
                                            <div class="form-group">
                                                <div class="checkbox checkbox-success">
                                                    <input id="my_reminder" name="my_reminder" value="Y" type="checkbox" class="reminderInput" checked="checked">
                                                    <label for="my_reminder"> <!-- Show only my reminders --><?php echo $this->defaultdata->gradLanguageText(455);?> </label>
                                                </div>
                                            </div>
                                       </div>
                                    </form>

                                    <div id="reminderTableData"><?php echo $reminderTable; ?></div>      
                                </div>
                            </div> 

                        </div>

                        <div class="row">
                           <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <h4 class="m-t-0 header-title"><b><?php echo $this->defaultdata->gradLanguageText(43);?></b></h4>
                                    
                                    <form role="form" method="get" class="row" id="search_comment">
                                        <div class="col-sm-12">
                                            
                                            <div class="form-group">
                                                <div class="checkbox checkbox-success">
                                                    <input id="show_from_agent" name="userType[]" value="2" type="checkbox" class="commentInput">
                                                    <label for="show_from_agent"> <?php echo $this->defaultdata->gradLanguageText(44);?> </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="show_from_solicitor" name="userType[]" value="3" type="checkbox" class="commentInput">
                                                    <label for="show_from_solicitor"> <?php echo $this->defaultdata->gradLanguageText(45);?> </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="show_from_consultant" name="userType[]" value="5" type="checkbox" class="commentInput">
                                                    <label for="show_from_consultant"> <?php echo $this->defaultdata->gradLanguageText(46);?> </label>
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
                                    <h4 class="m-t-0 header-title"><b><?php echo $this->defaultdata->gradLanguageText(50);?></b></h4>
                                    

                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                        <tr>
                                            <th><?php echo $this->defaultdata->gradLanguageText(28);?></th>
                                            <th><?php echo $this->defaultdata->gradLanguageText(51);?></th>
                                            <th><?php echo $this->defaultdata->gradLanguageText(52);?></th>
                                            <th><?php echo $this->defaultdata->gradLanguageText(47);?></th>
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
                        

                        <div class="row">

                           <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <h4 class="m-t-0 header-title"><b><?php echo $this->defaultdata->gradLanguageText(53);?>/ <?php echo $this->defaultdata->gradLanguageText(26);?></b></h4>
                                    
                                    <form role="form" method="get" class="row" id="search_login">
                                        <div class="col-sm-12">
                                            
                                            <div class="form-group">
                                                <div class="checkbox checkbox-success">
                                                    <input id="show_agent" name="userType[]" value="2" type="checkbox" class="loginInput">
                                                    <label for="show_agent"> <?php echo $this->defaultdata->gradLanguageText(54);?> </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox checkbox-pink">
                                                    <input id="show_consultant" name="userType[]" value="5" type="checkbox" class="loginInput">
                                                    <label for="show_consultant"> <?php echo $this->defaultdata->gradLanguageText(55);?> </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="show_solicitor" name="userType[]" value="3" type="checkbox" class="loginInput">
                                                    <label for="show_solicitor"> <?php echo $this->defaultdata->gradLanguageText(56);?> </label>
                                                </div>
                                            </div>
                                       </div>
                                    </form>

                                    <div id="loginTableData"><?php echo $loginTable; ?></div>
                                </div>
                            </div> 

                        </div>
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
/*$(document).ready(function () {
    $('.datatable').dataTable();                
});*/

</script>

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
            url: "<?php echo base_url('admin/client/completeClientTask'); ?>",
            data:{id:id},
            dataType: 'json',
            success:function(data){
                //Command: toastr["success"]("You Have Successfully Complete This Task.");
                Command: toastr["success"]("<?php echo $this->defaultdata->gradLanguageText(404);?>");
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
        url : "<?php echo base_url('admin/dashboard/searchTask'); ?>",
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
$(document).on('change','.reminderInput', function(){
    $('#search_reminder').submit();
});

$(document).on('submit',"#search_reminder", function(e){ 
    e.preventDefault();
    var me=$(this);
    var formData = new FormData(this);
    //console.log(formData);
    $.ajax({
        type:'POST',
        data:formData,
        url : "<?php echo base_url('admin/dashboard/searchReminder'); ?>",
        contentType: false,
        cache: false,
        processData:false,
        dataType: 'json',
        success : function(data){ 
          $('#reminderTableData').html(data.reminderTable);
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
        url : "<?php echo base_url('admin/dashboard/searchComment'); ?>",
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

<script type="text/javascript">
$(document).on('change','.loginInput',function(){ 
    $(".loginInput").not(this).attr('checked', false);
    $('#search_login').submit();
});

$(document).on('submit',"#search_login",function(e){ 
    e.preventDefault();
    var me=$(this);
    var formData = new FormData(this);
    //console.log(formData);
    $.ajax({
        type:'POST',
        data:formData,
        url : "<?php echo base_url('admin/dashboard/searchLogin'); ?>",
        contentType: false,
        cache: false,
        processData:false,
        dataType: 'json',
        success : function(data){ 
          $('#loginTableData').html(data.loginTable);
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
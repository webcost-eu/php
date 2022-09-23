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
        <link href="<?php echo DEFAULT_ASSETS_URL;?>plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
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

                        
                        <div class="row">

                           <div class="col-sm-12">
                                <div class="card-box table-responsive">

                                    <ul class="nav nav-tabs">
                                        <li class="<?php echo($type == 1 ? 'active' : ''); ?>">
                                            <a href="<?php echo base_url('consultant/dashboard?type=1'); ?>">
                                        <span class="visible-xs"><i class="fa fa-user"
                                                                    title="<?php echo $this->defaultdata->gradLanguageText(27); ?>"></i></span>
                                                <span class="hidden-xs"><?php echo $this->defaultdata->gradLanguageText(27); ?></span>
                                            </a>
                                        </li>
                                        <li class="<?php echo($type == 2 ? 'active' : ''); ?>">
                                            <a href="<?php echo base_url('consultant/dashboard?type=2'); ?>">
                                        <span class="visible-xs"><i class="fa fa-comments"
                                                                    title="<?php echo $this->defaultdata->gradLanguageText(32); ?>"></i></span>
                                                <span class="hidden-xs"><?php echo $this->defaultdata->gradLanguageText(32); ?></span>
                                            </a>
                                        </li>
                                        <li class="<?php echo($type == 3 ? 'active' : ''); ?>">
                                            <a href="<?php echo base_url('consultant/dashboard?type=3'); ?>">
                                        <span class="visible-xs"><i class="fa fa-tasks"
                                                                    title="<?php echo $this->defaultdata->gradLanguageText(42); ?>"></i></span>
                                                <span class="hidden-xs"><?php echo $this->defaultdata->gradLanguageText(42); ?></span>
                                            </a>
                                        </li>
                                        <li class="<?php echo($type == 4 ? 'active' : ''); ?>">
                                            <a href="<?php echo base_url('consultant/dashboard?type=4'); ?>">
                                        <span class="visible-xs"><i class="fa fa-file-text"
                                                                    title="<?php echo $this->defaultdata->gradLanguageText(125); ?>"></i></span>
                                                <span class="hidden-xs"><?php echo $this->defaultdata->gradLanguageText(125); ?></span>
                                            </a>
                                        </li>
                                        <!-- <li class="<?php echo($type == 5 ? 'active' : ''); ?>">
                                           < <a href="<?php echo base_url('consultant/dashboard?type=5'); ?>">
                                        <span class="visible-xs"><i class="fa fa-file-text"
                                                                    title="<?php echo $this->defaultdata->gradLanguageText(50);?>"></i></span>
                                                <span class="hidden-xs"><?php echo $this->defaultdata->gradLanguageText(50); ?></span>
                                            </a>
                                        </li> -->
                                        <li class="<?php echo($type == 6 ? 'active' : ''); ?>">
                                            <a href="<?php echo base_url('consultant/dashboard?type=6'); ?>">
                                        <span class="visible-xs"><i class="fa fa-file-text"
                                                                    title="<?php echo $this->defaultdata->gradLanguageText(53).' / '.$this->defaultdata->gradLanguageText(26);?>"></i></span>
                                                <span class="hidden-xs"><?php echo $this->defaultdata->gradLanguageText(53).' / '.$this->defaultdata->gradLanguageText(26); ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <?php if ($type == 1) { ?>
                                            <div class="tab-pane active" id="informations">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="card-box table-responsive">
                                                            <table class="table table-striped table-bordered datatable">
                                                                <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th><!-- Name Of Client --><?php echo $this->defaultdata->gradLanguageText(29);?></th>
                                                                    <th><!-- Type of accident --><?php echo $this->defaultdata->gradLanguageText(30);?></th>
                                                                    <th><!-- Agent --><?php echo $this->defaultdata->gradLanguageText(3);?></th>
                                                                    <th><!-- Date of adding to System --><?php echo $this->defaultdata->gradLanguageText(31);?></th>
                                                                </tr>
                                                                </thead>


                                                                <tbody>
                                                                <?php
                                                                if(count($allClients) > 0){
                                                                    foreach($allClients as $key => $user){
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $key+1; ?></td>
                                                                    <td><a href="<?php echo base_url('consultant/client/details/'.$user->user_id); ?>" target="_blank"><?php echo $user->name; ?></a></td>
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
                                        </div>
                                        <?php } elseif ($type == 2) { ?>
                                            <div class="tab-pane active">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="card-box table-responsive">

                                                            <form role="form" method="get" class="row" id="search_task">
                                                                <div class="col-sm-12">

                                                                    <div class="form-group">
                                                                        <div class="checkbox checkbox-success">
                                                                            <input id="my_task" name="my_task" value="Y" type="checkbox" class="taskInput" checked="checked">
                                                                            <label for="my_task"> <!-- Show only my tasks  --><?php echo $this->defaultdata->gradLanguageText(33);?></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="checkbox checkbox-pink">
                                                                            <input id="priority_task" name="priority_task" value="Y" type="checkbox" class="taskInput">
                                                                            <label for="priority_task"> <!-- Priority tasks --> <?php echo $this->defaultdata->gradLanguageText(34);?></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="checkbox checkbox-primary">
                                                                            <input id="today_delayed" name="today_delayed" value="Y" type="checkbox" class="taskInput">
                                                                            <label for="today_delayed"> <!-- Today and delayed tasks --><?php echo $this->defaultdata->gradLanguageText(35);?> </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="checkbox checkbox-primary">
                                                                            <input id="assigned_user_type" name="assigned_user_type" value="S" type="checkbox" class="taskInput">
                                                                            <label for="assigned_user_type"> <!-- Today and delayed tasks --><?php echo $this->defaultdata->gradLanguageText(577);?> </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group" style="width: 300px;">
                                                                        <span class="m-l-5 font-13 text-muted"><?php echo $this->defaultdata->gradLanguageText(37); ?></span>

                                                                        <div class="input-daterange input-group" id="date-range">
                                                                            <input type="text" placeholder="dd/mm/yyyy" class="form-control datepicker-autoclose taskInput" name="deadline_start" value="<?php echo isset($search_data['date_of_accident_start'])?$search_data['date_of_accident_start']:''; ?>"/>
                                                                            <span class="input-group-addon bg-custom text-white b-0"><?php echo $this->defaultdata->gradLanguageText(578);?></span>
                                                                            <input type="text" placeholder="dd/mm/yyyy" class="form-control datepicker-autoclose taskInput" name="deadline_end" value="<?php echo isset($search_data['date_of_accident_end'])?$search_data['date_of_accident_end']:''; ?>"/>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </form>

                                                            <div id="taskTableData"><?php echo $taskTable; ?></div>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        <?php } elseif ($type == 3) { ?>
                                            <div class="tab-pane active">
                                                <div class="row">

                                                    <div class="col-sm-12">
                                                        <div class="card-box table-responsive">
                                                           <form role="form" method="get" class="row" id="search_reminder">
                                                                <div class="col-sm-12">
                                                                    <div class="form-group">
                                                                        <div class="checkbox checkbox-success">
                                                                            <input id="my_reminder" name="my_reminder" value="Y" type="checkbox" class="reminderInput" checked="checked">
                                                                            <label for="my_reminder"> <!-- Show only my reminders --><?php echo $this->defaultdata->gradLanguageText(455);?> </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="checkbox checkbox-success">
                                                                            <input id="show_aging" name="show_aging" value="Y" type="checkbox" class="reminderInput" checked="checked">
                                                                            <label for="show_aging"> <!-- Show only my reminders --><?php echo $this->defaultdata->gradLanguageText(583);?> </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="checkbox checkbox-success">
                                                                            <input id="no_action" name="no_action" value="Y" type="checkbox" class="reminderInput" checked="checked">
                                                                            <label for="no_action"> <!-- Show only my reminders --><?php echo $this->defaultdata->gradLanguageText(584);?> </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group" style="width: 300px;">
                                                                        <span class="m-l-5 font-13 text-muted"><?php echo $this->defaultdata->gradLanguageText(585); ?></span>

                                                                        <div class="input-daterange input-group" id="date-range">
                                                                            <input type="text" placeholder="dd/mm/yyyy" class="form-control datepicker-autoclose reminderInput" name="deadline_start" value="<?php echo isset($search_data['date_of_accident_start'])?$search_data['date_of_accident_start']:''; ?>"/>
                                                                            <span class="input-group-addon bg-custom text-white b-0"><?php echo $this->defaultdata->gradLanguageText(578);?></span>
                                                                            <input type="text" placeholder="dd/mm/yyyy" class="form-control datepicker-autoclose reminderInput" name="deadline_end" value="<?php echo isset($search_data['date_of_accident_end'])?$search_data['date_of_accident_end']:''; ?>"/>
                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </form>

                                                            <div id="reminderTableData"><?php echo $reminderTable; ?></div>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        <?php } elseif ($type == 4) { ?>
                                            <div class="tab-pane active">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="card-box table-responsive">

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
                                                                    <div class="form-group">
                                                                        <div class="checkbox checkbox-primary">
                                                                            <input id="show_from_solicitor" name="userType[]" value="3" type="checkbox" class="commentInput">
                                                                            <label for="show_from_solicitor"> <!-- Show from Solicitors --><?php echo $this->defaultdata->gradLanguageText(45);?> </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="checkbox checkbox-primary">
                                                                            <input id="show_from_consultant" name="userType[]" value="5" type="checkbox" class="commentInput">
                                                                            <label for="show_from_consultant"><!--  Show from Consultants --> <?php echo $this->defaultdata->gradLanguageText(46);?></label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <div class="checkbox checkbox-primary">
                                                                            <input id="show_from_admin" name="userType[]" value="1" type="checkbox" class="commentInput">
                                                                            <label for="show_from_admin"><!--  Show from Admin --> <?php echo $this->defaultdata->gradLanguageText(580);?></label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group" style="width: 300px;">
                                                                        <span class="m-l-5 font-13 text-muted"><?php echo $this->defaultdata->gradLanguageText(49);?></span>

                                                                        <div class="input-daterange input-group" id="date-range">
                                                                            <input type="text" placeholder="dd/mm/yyyy" class="form-control datepicker-autoclose commentInput" name="postedtime_start" value="<?php echo isset($search_data['date_of_accident_start'])?$search_data['date_of_accident_start']:''; ?>"/>
                                                                            <span class="input-group-addon bg-custom text-white b-0"><?php echo $this->defaultdata->gradLanguageText(578);?></span>
                                                                            <input type="text" placeholder="dd/mm/yyyy" class="form-control datepicker-autoclose commentInput" name="postedtime_end" value="<?php echo isset($search_data['date_of_accident_end'])?$search_data['date_of_accident_end']:''; ?>"/>
                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </form>

                                                            <div id="commentTableData"><?php echo $commentTable; ?></div>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        <?php } elseif ($type == 5) { ?>
                                            <div class="tab-pane active">
                                                <div class="row">

                                                    <div class="col-sm-12">
                                                        <div class="card-box table-responsive">

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
                                            </div>
                                        <?php } elseif ($type == 6) { ?>
                                            <div class="tab-pane active">
                                                <div class="row">

                                                    <div class="col-sm-12">
                                                        <div class="card-box table-responsive">

                                                            <form role="form" method="get" class="row" id="search_login">
                                                                <div class="col-sm-12">

                                                                    <div class="form-group">
                                                                        <div class="checkbox checkbox-success">
                                                                            <input id="show_agent" name="userType[]" value="2" type="checkbox" class="loginInput">
                                                                            <label for="show_agent"> <!-- Show Agents --><?php echo $this->defaultdata->gradLanguageText(54);?> </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="checkbox checkbox-pink">
                                                                            <input id="show_consultant" name="userType[]" value="5" type="checkbox" class="loginInput">
                                                                            <label for="show_consultant"> <!-- Show Consultants --> <?php echo $this->defaultdata->gradLanguageText(55);?></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="checkbox checkbox-primary">
                                                                            <input id="show_solicitor" name="userType[]" value="3" type="checkbox" class="loginInput">
                                                                            <label for="show_solicitor"> <!-- Show Solicitors --><?php echo $this->defaultdata->gradLanguageText(56);?> </label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group" style="width: 300px;">
                                                                        <span class="m-l-5 font-13 text-muted"><?php echo $this->defaultdata->gradLanguageText(582);?></span>

                                                                        <div class="input-daterange input-group" id="date-range">
                                                                            <input type="text" placeholder="dd/mm/yyyy" class="form-control datepicker-autoclose loginInput" name="logintime_start" value="<?php echo isset($search_data['logintime_start'])?$search_data['logintime_start']:''; ?>"/>
                                                                            <span class="input-group-addon bg-custom text-white b-0"><?php echo $this->defaultdata->gradLanguageText(578);?></span>
                                                                            <input type="text" placeholder="dd/mm/yyyy" class="form-control datepicker-autoclose loginInput" name="logintime_end" value="<?php echo isset($search_data['logintime_end'])?$search_data['logintime_end']:''; ?>"/>
                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </form>

                                                            <div id="loginTableData"><?php echo $loginTable; ?></div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        <?php }  ?>
                                    </div>
                                </div>
                            </div> 

                        </div>
                        <!-- end row -->


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

        <script src="<?php echo DEFAULT_ASSETS_URL;?>plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
     $(document).ready(function () {
        $('.datepicker-autoclose').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true
                });
        });
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
            url: "<?php echo base_url('consultant/client/completeClientTask'); ?>",
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
$( document ).ready(function() {
    $('.table-task').DataTable({
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?php echo $this->defaultdata->gradLanguageText(576);?>"]],
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
});
$(document).on('submit',"#search_task",function(e){ 
    e.preventDefault();
    var me=$(this);
    var formData = new FormData(this);
    //console.log(formData);
    $.ajax({
        type:'POST',
        data:formData,
        url : "<?php echo base_url('consultant/dashboard/searchTask'); ?>",
        contentType: false,
        cache: false,
        processData:false,
        dataType: 'json',
        success : function(data){ 
          $('#taskTableData').html(data.taskTable);
          $('.table-task').DataTable({
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?php echo $this->defaultdata->gradLanguageText(576);?>"]],
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
        url : "<?php echo base_url('consultant/dashboard/searchReminder'); ?>",
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
        url : "<?php echo base_url('consultant/dashboard/searchComment'); ?>",
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
        url : "<?php echo base_url('consultant/dashboard/searchLogin'); ?>",
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
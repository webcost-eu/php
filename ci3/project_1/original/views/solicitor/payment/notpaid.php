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
        <title><?php echo $this->defaultdata->gradLanguageText(127);?></title>
        <link rel="stylesheet" href="<?php echo DEFAULT_ASSETS_URL;?>plugins/tooltipster/tooltipster.bundle.min.css">
        <!-- App css -->
        <?php echo $header_scripts;?>
        <link href="<?php echo DEFAULT_ASSETS_URL;?>plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo DEFAULT_ASSETS_URL;?>plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
        <link href="<?php echo DEFAULT_ASSETS_URL;?>plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo DEFAULT_ASSETS_URL;?>plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
        <link href="<?php echo DEFAULT_ASSETS_URL;?>plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
        
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
                        <h4 class="page-title text-uppercase m-t-5 m-l-15"><!-- PAYMENTS --><?php echo $this->defaultdata->gradLanguageText(127);?></h4>

                        <div class="clearfix"></div>

                        
                        
                        
                    </div>
				</div>
			</div>
            <!-- end row -->

            <div class="row">
                <div class="col-md-12">
                  <div class="card-box">
                    <?php echo $inner_header; ?>
                    <div class="tab-content">
                      <div class="tab-pane active" id="payment">      

                        <div class="card-box table-responsive">
                            <?php if($this->session->flashdata('success')){ ?>
                            <div class="alert alert-icon alert-success alert-dismissible fade in" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <?php echo $this->session->flashdata('success');?>
                            </div>
                            <?php } ?>

                            <?php if($agent_data->company=='Y'){ ?>
                            <div class="alert alert-success" role="alert">
                              <p>1.W tym miejscu wygenerujesz fakturę za wypracowane prowizje, zaliczki i premie.</p>
                              <p>2.Fakturę możesz wygenerować w każdej chwili gdy zobaczysz naliczone prowizje.</p>
                              <p>3.Faktura zostanie opłacona w ciągu 5 dni roboczych.</p>
                              <p>4.Sprawdź poprawność danych na fakturze (dane firmy i numer konta).</p>
                              <p>5.Nie wysyłaj faktury do Kancelarii. Pobierz plik PDF i zaksięguj.</p>
                            </div>

                            <?php } else { ?>
                            <div class="alert alert-success" role="alert">
                              <p>1.W tym miejscu wygenerujesz umowę zlecenie i rachunek za wypracowane prowizje, zaliczki i premie.</p>
                              <p>2.Najpierw pobierz, wypełnij i podpisz oświadczenie ZUS (dla celów ubezpieczeniowych i podatkowych).</p>
                              <p>3.Oświadczenie zeskanuj i dodaj do rozliczenia prowizji.</p>
                              <p>4.Za 3 dni otrzymasz do Systemu rachunek. Wydrukuj w 2 egz., wypełnij i podpisz.</p>
                              <p>5.Wyślij pocztą do Kancelarii po 2 egz. Podpisanych oświadczeń ZUS i rachunków.</p>
                              <p>6.Przelew na wskazany numer konta otrzymasz w ciągu 3 dni od wpływu dokumentów do Kancelarii.</p>
                            </div>
                            <?php } ?>



                            <div class="row">
                                <div class="col-lg-12" id="agentNotpaidPayment">
                                    <table class="table table-bordered table-striped m-0 font-13">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><!-- Date --><?php echo $this->defaultdata->gradLanguageText(146);?></th>
                                                <th><!-- Client --><?php echo $this->defaultdata->gradLanguageText(2);?></th>
                                                <th><!-- Type of Payment --><?php echo $this->defaultdata->gradLanguageText(170);?></th>
                                                <th><!-- Provision --><?php echo $this->defaultdata->gradLanguageText(149);?> %</th>
                                                <th><!-- Proper Provision --><?php echo $this->defaultdata->gradLanguageText(161);?> %</th>
                                                <th><!-- Company Provision --><?php echo $this->defaultdata->gradLanguageText(162);?></th>
                                                <th><!-- Source Agent --><?php echo $this->defaultdata->gradLanguageText(128);?></th>
                                                <th><!-- Value --><?php echo $this->defaultdata->gradLanguageText(297);?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          <?php
                                          $total_provision = 0;
                                          $total_bonus = 0;
                                          $total_settle = 0;
                                          $total_target = 0;
                                          $all_total = 0;
                                          $i = 0;
                                          ?>
                                          <?php 
                                          if(count($comment_provision)>0){
                                            foreach($comment_provision as $provision){
                                              $i++;
                                          ?>
                                          <tr>
                                              <td><?php echo $i; ?></td>
                                              <td><?php echo date('d/m/Y H:i', $provision->postedtime)?></td>
                                              <td><?php echo $provision->client_name; ?></td>
                                              <td>
                                              <?php if($provision->provision_type==1){ echo 'Provision General';} ?>
                                              <?php if($provision->provision_type==2){ echo 'Provision in Court';} ?>
                                              <?php if($provision->provision_type==3){ echo 'Central Provision';} ?>
                                              <?php if($provision->provision_type==4){ echo 'Upper Provision';} ?>
                                                  
                                              </td>
                                              <td><?php echo $provision->agent_provision_percent.'%'; ?></td>
                                              <td><?php echo $provision->porper_provision_percent.'%'; ?></td>
                                              <td><?php echo $provision->provision_gross; ?></td>
                                              <td>
                                                  <?php if($provision->agent_id!=$provision->client_agent_id){ ?>
                                                  <a href="<?php echo base_url('agent/subagent/information/'.$provision->client_agent_id); ?>" target="_blank"><?php echo $provision->client_agent_name; ?></a>
                                                  <?php } ?>
                                              </td>
                                              <td><?php echo $provision->porper_provision_value; ?></td>
                                          </tr>
                                          <?php $total_provision += $provision->porper_provision_value; ?>
                                          <?php } } ?>

                                          <?php 
                                          if(count($agent_bonus)>0){
                                            foreach($agent_bonus as $bonus){
                                              $i++;
                                          ?>
                                          <tr>
                                              <td><?php echo $i; ?></td>
                                              <td><?php echo date('d/m/Y H:i', $bonus->postedtime)?></td>
                                              <td><?php echo $bonus->client_name; ?></td>
                                              <td><?php echo $bonus->type==1 ? $this->defaultdata->gradLanguageText(294) : $this->defaultdata->gradLanguageText(295); ?></td>
                                              <td></td>
                                              <td></td>
                                              <td></td>
                                              <td></td>
                                              <td><?php echo $bonus->gross_value; ?></td>
                                          </tr>

                                          <?php $total_bonus += $bonus->gross_value; ?>

                                            <?php 
                                            if(count($bonus->settle_data)>0){
                                            foreach($bonus->settle_data as $settle){
                                              $i++;
                                            ?>
                                            <tr>
                                              <td><?php echo $i; ?></td>
                                              <td><?php echo date('d/m/Y H:i', $settle->postedtime)?></td>
                                              <td><?php echo $bonus->client_name; ?></td>
                                              <td><?php echo $settle->description; ?></td>
                                              <td></td>
                                              <td></td>
                                              <td></td>
                                              <td></td>
                                              <td><?php echo '-'.$settle->settle_value; ?></td>
                                            </tr>
                                            <?php $total_settle += $settle->settle_value; ?>
                                            <?php } } ?>

                                          <?php } } ?>

                                          <?php 
                                          if(count($agent_target)>0){
                                            foreach($agent_target as $target){
                                              $i++;
                                          ?>
                                          <tr>
                                              <td><?php echo $i; ?></td>
                                              <td><?php echo date('d/m/Y H:i', $target->postedTime)?></td>
                                              <td></td>
                                              <td>Premia za osiągnięty cel sprzedażowy</td>
                                              <td></td>
                                              <td></td>
                                              <td></td>
                                              <td>
                                                  <a href="<?php echo base_url('agent/subagent/information/'.$target->agent_from_structure); ?>" target="_blank"><?php echo $target->agent_from_structure_name; ?></a>
                                              </td>
                                              <td><?php echo number_format($target->reward, 2, '.', ''); ?></td>
                                          </tr>
                                          <?php $total_target += $target->reward; ?>
                                          <?php } } ?>


                                          <tr>
                                              <td colspan="8"><!-- Sum --><?php echo $this->defaultdata->gradLanguageText(167);?>: </td>
                                              <td>
                                              <?php 
                                              $all_total = $total_provision+$total_bonus-$total_settle+$total_target;
                                              echo number_format($all_total, 2, '.', '');
                                              ?>
                                                  
                                              </td>
                                          </tr>
                                          <?php 
                                          if($all_total>0){
                                            if($agent_data->company=='Y'){ 
                                          ?>
                                          <tr>
                                              <td colspan="9" align="right">
                                                <button type="button" class="btn btn-success w-md waves-effect waves-light m-b-5 btn-md generateAgentInvoice" data-id="<?php echo $agent_data->user_id; ?>" data-value="<?php echo $all_total; ?>"><!-- Generate Invoice --><?php echo $this->defaultdata->gradLanguageText(442);?></button>
                                              </td>
                                          </tr>
                                          <?php } else { ?>
                                          <tr>
                                              <td colspan="9" align="right">
                                                
                                                <a href="<?php echo DEFAULT_ASSETS_URL; ?>upload/non_company_invoice/OświadczenieZUS.pdf" class="btn btn-success w-md waves-effect waves-light m-b-5 btn-md" download><!-- Download --><?php echo $this->defaultdata->gradLanguageText(142);?></a>

                                                <button type="button" class="btn btn-success w-md waves-effect waves-light m-b-5 btn-md" data-toggle="modal" data-target="#uploadInvoiceModal" ><!-- Upload Invoice --><?php echo $this->defaultdata->gradLanguageText(299);?></button>
                                              </td>
                                          </tr>
                                          <?php } } ?>
                                        </tbody>
                                    </table>
                                                                    
                                </div>
                            </div>
                        </div>

                       </div>
                    </div>                    
                  </div>          
                    
                </div>
            </div>

            

            <div id="agentInvoiceModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            </div><!-- /.modal -->       

            <div id="uploadInvoiceModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="folderModelHeading"><!-- Add Invoice --><?php echo $this->defaultdata->gradLanguageText(298);?></h4>
                        </div>
                        <form role="form" name="ajax-form" method="post" class="contact-form" id="uploadAgentFile" action="<?php echo base_url('agent/payment/uploadNonCompanyAgentFile'); ?>" enctype="multipart/form-data">
                            <input type="hidden" name="agent_id" id="agent_id" value="<?php echo $agent_data->user_id; ?>">
                            <div class="modal-body">
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="folder_name"><!-- Upload Invoice --><?php echo $this->defaultdata->gradLanguageText(299);?></label>
                                            <input type="file" name="invoice_file" id="invoice_file" class="filestyle" data-buttonname="btn-default" required>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><!-- Close --><?php echo $this->defaultdata->gradLanguageText(253);?></button>
                                <button type="submit" id="submitComment" class="btn btn-info waves-effect waves-light" ><!-- Submit --><?php echo $this->defaultdata->gradLanguageText(117);?></button>
                            </div>
                        </form>    
                    </div>
                </div>
            </div><!-- /.modal -->



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


<script src="<?php echo DEFAULT_ASSETS_URL;?>plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script src="<?php echo DEFAULT_ASSETS_URL;?>plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo DEFAULT_ASSETS_URL;?>plugins/parsleyjs/parsley.min.js"></script>
<script type="text/javascript" src="<?php echo DEFAULT_ASSETS_URL;?>js/jquery.form.min.js"></script>
<script src="<?php echo DEFAULT_ASSETS_URL;?>plugins/toastr/toastr.min.js"></script>
<script src="<?php echo DEFAULT_ASSETS_URL;?>pages/jquery.toastr.js"></script>
<script src="<?php echo DEFAULT_ASSETS_URL;?>plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo DEFAULT_ASSETS_URL;?>plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>


<script type="text/javascript">
    $(document).ready(function() {
        $(".select2").select2();
        $('.selectpicker').selectpicker();
        
        $(":file").filestyle({input: false});
        
        jQuery('.datepicker-autoclose').datepicker({
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
var serial_no = 1;

$(document).on('click','.generateAgentInvoice',function(){
    serial_no = "<?php echo $i; ?>";
    var agent_id = $(this).attr('data-id');
    var total_value = $(this).attr('data-value');
    $.ajax({
        type:"POST",
        url: "<?php echo base_url('agent/payment/generateAgentInvoice'); ?>",
        data:{agent_id:agent_id, total_value:total_value},
        dataType: 'json',
        success:function(data){
            $('#agentInvoiceModal').html(data.generateAgentInvoiceHtml);
            $('#agentInvoiceModal').modal('show');
        }
    });
});

$(document).on('submit',"#agentInvoice",function(e){ 
    e.preventDefault();
    var me=$(this);
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        data:formData,
        mimeType:"multipart/form-data",
        url : "<?php echo base_url('agent/payment/postAgentInvoice'); ?>",
        contentType: false,
        cache: false,
        processData:false,
        dataType: 'json',
        success : function(data){ 
            //Command: toastr["success"]("Invoice Added Successfully");
            Command: toastr["success"]("<?php echo $this->defaultdata->gradLanguageText(401);?>");
            if(data.agentNotpaidPayment!='undefined')
            {
                $('#agentNotpaidPayment').html(data.agentNotpaidPayment);
            } 
            
            $('#agentInvoiceModal').modal('hide');
        }
    });
}); 


$(document).on('click', '.addNewPayment', function() {
    serial_no++;
    newPaymentRow = '<tr><input type="hidden" name="source_table[]" value="0"><input type="hidden" name="source_id[]" value="0" ><td>'+serial_no+'</td><td><input type="text" class="form-control"name="service_name[]" id="service_name'+serial_no+'" required></td><td>1</td><td><input type="text" class="form-control netPrice" name="net_price[]"  id="net_price'+serial_no+'" data-id="'+serial_no+'" required></td><td><input type="text" class="form-control" name="vat[]" value="" id="vat'+serial_no+'" readonly></td><td><input type="text" class="form-control" name="gross_price[]" id="gross_price'+serial_no+'" readonly required></td><td><a href="javascript:void(0)" class="on-default remove-row "><i class="fa fa-trash-o removeInvoicePayRow" style="color:red;"></i></a></td></tr>';
    $('#pamentTable').append(newPaymentRow);
});

$(document).on('click', '.removeInvoicePayRow', function() { 
    //alert();
    $(this).parent().closest('tr').remove();
}); 
</script>

<script type="text/javascript">

<?php if($agent_data->company=='Y' && $agent_data->vat_payer=='Y'){ ?>

$(document).on('keyup', '.netPrice', function() { 
    var slno = $(this).attr('data-id');
    var net_price = parseFloat($(this).val());
    var vat = net_price*0.23;
    $('#vat'+slno).val(vat);
    var gross_price = net_price + vat;
    $('#gross_price'+slno).val(gross_price);
}); 

<?php } else { ?>

$(document).on('keyup', '.netPrice', function() { 
    var slno = $(this).attr('data-id');
    var net_price = parseFloat($(this).val());
    var vat = 0;
    $('#vat'+slno).val(vat);
    var gross_price = net_price + vat;
    $('#gross_price'+slno).val(gross_price);
});

<?php } ?>

</script> 

 

 

        
    </body>
</html>
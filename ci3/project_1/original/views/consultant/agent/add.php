<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
        <meta name="author" content="Coderthemes">
        <!-- App title -->
        <title><?php echo $this->defaultdata->gradLanguageText(184);?></title>
         
        
        <?php echo $header_scripts;?>
        <link href="<?php echo DEFAULT_ASSETS_URL;?>plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo DEFAULT_ASSETS_URL;?>plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet"> 
        <link href="<?php echo DEFAULT_ASSETS_URL;?>plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
        <!-- <link href="<?php echo DEFAULT_ASSETS_URL;?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet"> -->

       
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
                                    <h4 class="page-title"><!-- Add Agent  --><?php echo $this->defaultdata->gradLanguageText(184);?></h4>
                                    
                                    <div class="clearfix"></div>
                                </div>
							</div>
						</div>
                        <!-- end row -->


                        <div class="row">
                            <div class="col-xs-12">
                                <div class="card-box">

                                    <div class="row">
                                        <div class="col-sm-12 col-xs-12 col-md-9">

                                            <h4 class="header-title m-t-0"><!-- Add Agent --><?php echo $this->defaultdata->gradLanguageText(184);?></h4>

                                            <div class="p-20">
                                                <form method="post" action="<?php echo base_url('consultant/agent/addProcess'); ?>" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                    <?php if($this->session->flashdata('error')){ ?>
                                                    <div class="alert alert-icon alert-danger alert-dismissible fade in" role="alert">
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                        <?php echo $this->session->flashdata('error');?>
                                                    </div>
                                                    <?php } ?>

                                                    <?php if($this->session->flashdata('success')){ ?>
                                                    <div class="alert alert-icon alert-success alert-dismissible fade in" role="alert">
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                        <?php echo $this->session->flashdata('success');?>
                                                    </div>
                                                    <?php } ?>
                                                    <div class="form-group">
                                                        <label for="firstName"><!-- First Name --><?php echo $this->defaultdata->gradLanguageText(77);?> <span class="text-danger">*</span></label>
                                                        <input type="text" name="firstName" parsley-trigger="change" required  class="form-control" id="firstName" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="lastName"><!-- Last Name --> <?php echo $this->defaultdata->gradLanguageText(78);?><span class="text-danger">*</span></label>
                                                        <input type="text" name="lastName" parsley-trigger="change" required  class="form-control" id="lastName">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="phone"><!-- Phone --> <?php echo $this->defaultdata->gradLanguageText(79);?><span class="text-danger">*</span></label>
                                                        <input parsley-trigger="change" type="text" class="form-control" required  name="phone" id="phone"/>
                                                        
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="userName"><!-- User Name --><?php echo $this->defaultdata->gradLanguageText(185);?> <span class="text-danger">*</span></label>
                                                        <input type="text" name="userName" parsley-trigger="change" required  class="form-control" id="userName">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="emailAddress"><!-- Email address --> <?php echo $this->defaultdata->gradLanguageText(80);?><span class="text-danger">*</span></label>
                                                        <input type="email" name="emailAddress" parsley-trigger="change" required class="form-control" id="emailAddress">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="userPassword"><!-- Password --><?php echo $this->defaultdata->gradLanguageText(186);?> <span class="text-danger">*</span></label>
                                                        <input id="userPassword" name="userPassword" type="password" required class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="cPassword"><!-- Confirm Password --><?php echo $this->defaultdata->gradLanguageText(187);?> <span class="text-danger">*</span></label>
                                                        <input data-parsley-equalto="#userPassword" type="password" required  class="form-control" name="cPassword" id="cPassword">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="parent_id"><!-- Manager --> <?php echo $this->defaultdata->gradLanguageText(180);?><span class="text-danger">*</span></label>
                                                        <select class="form-control select2" name="parent_id" id="parent_id" required>
                                                            <option value="0">Select</option>
                                                        <?php 
                                                        if(count($allAgents) > 0){
                                                            foreach($allAgents as $user){
                                                        ?>    
                                                            <option value="<?php echo $user->id; ?>"><?php echo $user->name; ?></option>
                                                        <?php } } ?>    
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="country_id"><!-- Country --> <?php echo $this->defaultdata->gradLanguageText(66);?></label>
                                                        <select class="form-control select2" name="country_id" id="country_id">
                                                            <option value="">Select</option>
                                                        <?php 
                                                        if(count($allCountries) > 0){
                                                            foreach($allCountries as $country){
                                                        ?>    
                                                            <option value="<?php echo $country->idCountry; ?>"><?php echo $country->countryName; ?></option>
                                                        <?php } } ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="town"><!-- Town --><?php echo $this->defaultdata->gradLanguageText(188);?></label>
                                                        <input id="town" name="town" type="text" class="form-control">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="street"><!-- Street and number --><?php echo $this->defaultdata->gradLanguageText(189);?></label>
                                                        <input id="street" name="street" type="text" class="form-control">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="zip"><!-- Post code --><?php echo $this->defaultdata->gradLanguageText(190);?></label>
                                                        <input id="zip" name="zip" type="text" class="form-control">
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="checkbox">
                                                            <input id="company" name="company" type="checkbox" value="Y">
                                                            <label for="company"> <!-- Company --><?php echo $this->defaultdata->gradLanguageText(191);?> </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="company_name"><!-- Name of company --><?php echo $this->defaultdata->gradLanguageText(192);?><span class="text-danger">*</span></label>
                                                        <input id="company_name" name="company_name" type="text" class="form-control">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="NIP"><!-- NIP --><?php echo $this->defaultdata->gradLanguageText(193);?><span class="text-danger">*</span></label>
                                                        <input id="NIP" name="NIP" type="text"  required class="form-control">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="KRS"><!-- KRS --><?php echo $this->defaultdata->gradLanguageText(194);?></label>
                                                        <input id="KRS" name="KRS" type="text" class="form-control">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="account_number"><!-- Account Number --> <?php echo $this->defaultdata->gradLanguageText(195);?><span class="text-danger">*</span></label>
                                                        <input id="account_number" name="account_number" type="text"  required class="form-control">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="bank_name"><!-- Bank Name --><?php echo $this->defaultdata->gradLanguageText(196);?> <span class="text-danger">*</span></label>
                                                        <input id="bank_name" name="bank_name" type="text"  required class="form-control">
                                                    </div>

                                                    <div class="form-group">
                                                        <label ><!-- VAT payer --><?php echo $this->defaultdata->gradLanguageText(197);?></label>
                                                        <div class="radio">
                                                            <input type="radio" name="vat_payer" id="vat_payerY" value="Y" checked onclick="chooseVatPayer(this.value)">
                                                            <label for="vat_payerY"> Yes </label>
                                                        </div>
                                                        <div class="radio">
                                                            <input type="radio" name="vat_payer" id="vat_payerN" value="N" onclick="chooseVatPayer(this.value)">
                                                            <label for="vat_payerN"> <!-- No  --><?php echo $this->defaultdata->gradLanguageText(199);?></label>
                                                        </div>
                                                        


                                                        <textarea style="display:none;" parsley-trigger="change" id="vat_payer_txt" name="vat_payer_txt" class="form-control" >Sprzedawca zwolniony podmiotowo z podatku od towarów i usług [dostawa towarów lub świadczenie usług zwolniono na podstawie art. 113 ust.1 (albo ust. 9) ustawy z dnia 11.03.2004 r. o podatku od towarów i usług (Dz.U. z 2011 r., Nr 177, poz. 1054, z późm.zm.)]</textarea>
                                                    </div>
                                                    

                                                    <div class="form-group">
                                                        <label for="agreement_date"><!-- Date of signing agreement --><?php echo $this->defaultdata->gradLanguageText(200);?> <span class="text-danger">*</span></label>
                                                        <div><div class="input-group">
                                                            <input type="text" class="form-control datepicker-autoclose" placeholder="dd/mm/yyyy"  name="agreement_date" id="agreement_date" required >
                                                            <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                                                        </div></div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label for=""><!-- Provision general  --><?php echo $this->defaultdata->gradLanguageText(201);?><span class="text-danger">*</span></label>
                                                        <div id="provisionGeneral1" class="provisionGeneral row" >
                                                            <div class="col-md-11">    
                                                                <input name="provision_general_percent[]" type="text" required class="form-control">
                                                            </div>    
                                                            <div class="m-t-10 col-md-11">
                                                                <div class="input-daterange input-group" id="date-range">
                                                                    <input type="text" class="form-control datepicker-autoclose" name="provision_general_start[]" />
                                                                    <span class="input-group-addon bg-custom text-white b-0">to</span>
                                                                    <input type="text" class="form-control datepicker-autoclose" name="provision_general_end[]" />
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="m-t-10">
                                                            <button class="btn btn-success waves-effect waves-light m-b-5" type="button" id="moreProvisionGeneral"> <i class="fa fa-plus m-r-5"></i> <span><!-- Add More --><?php echo $this->defaultdata->gradLanguageText(206);?></span> </button>
                                                        </div>
   
                                                    </div>

                                                    <div class="form-group">
                                                        <label for=""><!-- Provision in court --><?php echo $this->defaultdata->gradLanguageText(202);?> <span class="text-danger">*</span></label>
                                                        <div id="provisionCourt1" class="provisionCourt row" >    
                                                            <div class="col-md-11">     
                                                                <input  name="provision_court_percent[]" type="text"  required class="form-control">
                                                            </div>
                                                            <div class="m-t-10 col-md-11">
                                                                <div class="input-daterange input-group" id="date-range">
                                                                    <input type="text" class="form-control datepicker-autoclose" name="provision_court_start[]" />
                                                                        <span class="input-group-addon bg-custom text-white b-0">to</span>
                                                                        <input type="text" class="form-control datepicker-autoclose" name="provision_court_end[]" />
                                                                </div>
                                                            </div>
                                                        </div> 
                                                        <div class="m-t-10">
                                                            <button class="btn btn-success waves-effect waves-light m-b-5" type="button" id="moreProvisionCourt"> <i class="fa fa-plus m-r-5"></i> <span><!-- Add More --><?php echo $this->defaultdata->gradLanguageText(206);?></span> </button>
                                                        </div>   
                                                    </div>

                                                    <div class="form-group">
                                                        <label ><!-- Central provision --> <?php echo $this->defaultdata->gradLanguageText(203);?><span class="text-danger">*</span></label>

                                                        <div id="provisionCentral1" class="provisionCentral row" >    
                                                            <div class="col-md-11">     
                                                                <input  name="provision_central_percent[]" type="text"  required class="form-control">
                                                            </div>
                                                            <div class="m-t-10 col-md-11">
                                                                <div class="input-daterange input-group" id="date-range">
                                                                    <input type="text" class="form-control datepicker-autoclose" name="provision_central_start[]" />
                                                                        <span class="input-group-addon bg-custom text-white b-0">to</span>
                                                                        <input type="text" class="form-control datepicker-autoclose" name="provision_central_end[]" />
                                                                </div>
                                                            </div>
                                                        </div> 
                                                        <div class="m-t-10">
                                                            <button class="btn btn-success waves-effect waves-light m-b-5" type="button" id="moreProvisionCentral"> <i class="fa fa-plus m-r-5"></i> <span><!-- Add More --><?php echo $this->defaultdata->gradLanguageText(206);?></span> </button>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label ><!-- Upper provision --><?php echo $this->defaultdata->gradLanguageText(204);?> <span class="text-danger">*</span></label>  
                                                        <div id="provisionUpper1" class="provisionUpper row" >    
                                                            <div class="col-md-11">     
                                                                <input  name="provision_upper_percent[]" type="text"  required class="form-control">
                                                            </div>
                                                            <div class="m-t-10 col-md-11">
                                                                <div class="input-daterange input-group" id="date-range">
                                                                    <input type="text" class="form-control datepicker-autoclose" name="provision_upper_start[]" />
                                                                        <span class="input-group-addon bg-custom text-white b-0">to</span>
                                                                        <input type="text" class="form-control datepicker-autoclose" name="provision_upper_end[]" />
                                                                </div>
                                                            </div>
                                                        </div> 
                                                        <div class="m-t-10">
                                                            <button class="btn btn-success waves-effect waves-light m-b-5" type="button" id="moreProvisionUpper"> <i class="fa fa-plus m-r-5"></i> <span><!-- Add More --><?php echo $this->defaultdata->gradLanguageText(206);?></span> </button>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="id_of_partner"><!-- ID of Partner  --><?php echo $this->defaultdata->gradLanguageText(205);?><span class="text-danger">*</span></label>
                                                        <input id="id_of_partner" name="id_of_partner" type="text" required class="form-control">
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-success">
                                                            <input id="see_instruction" name="see_instruction" value="Y" type="hidden">
                                                            <!--<label for="see_instruction"><?php echo $this->defaultdata->gradLanguageText(207);?></label>-->
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label><!-- Formulars --><?php echo $this->defaultdata->gradLanguageText(8);?></label>
                                                         <div>
                                                            <input type="checkbox" id="formulars" name="formulars" value="Y" switch="bool"/>
                                                            <label for="formulars" data-on-label="Yes"
                                                           data-off-label="No"></label>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label><!-- Training scripts --><?php echo $this->defaultdata->gradLanguageText(9);?></label>
                                                         <div>
                                                            <input type="checkbox" id="training_script" name="training_script" value="Y" switch="bool"/>
                                                            <label for="training_script" data-on-label="Yes"
                                                           data-off-label="No"></label>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="provision"><!-- Add Document --><?php echo $this->defaultdata->gradLanguageText(116);?></label>
                                                        <input type="file" class="filestyle"  name="document[]" id="document" multiple="multiple"  data-input="false">
                                                    </div>

                                                    <div class="form-group text-right m-b-0">
                                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                                            <?php echo $this->defaultdata->gradLanguageText(117);?>
                                                        </button>
                                                        <button type="reset" class="btn btn-default waves-effect m-l-5">
                                                            <?php echo $this->defaultdata->gradLanguageText(118);?>
                                                        </button>
                                                    </div>

                                                </form>
                                            </div>

                                        </div>

                                        
                                    </div>
                                    <!-- end row -->

                                   
                                    <!-- end row -->


                                  
                        		</div> <!-- end ard-box -->
                            </div><!-- end col-->

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

<script type="text/javascript" src="<?php echo DEFAULT_ASSETS_URL;?>plugins/parsleyjs/parsley.min.js"></script>
<script src="<?php echo DEFAULT_ASSETS_URL;?>plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo DEFAULT_ASSETS_URL;?>plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script src="<?php echo DEFAULT_ASSETS_URL;?>plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script src="<?php echo DEFAULT_ASSETS_URL;?>plugins/toastr/toastr.min.js"></script>
<script src="<?php echo DEFAULT_ASSETS_URL;?>pages/jquery.toastr.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.buttonText').text('<?php echo $this->defaultdata->gradLanguageText(480);?>');
})
    $(document).ready(function() {
        $(".select2").select2();
        $(":file").filestyle({input: false});
    });
</script>    
<script type="text/javascript">
$(document).ready(function() {
    jQuery('.datepicker-autoclose').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true
    });
});
</script>

<script type="text/javascript">

function chooseVatPayer(val){
    if(val=='N') {
        $('#vat_payer_txt').show();
    } else {
        $('#vat_payer_txt').hide();
    }
}

</script>
<script type="text/javascript">
$(document).ready(function(){
    /* ======== Provision General Start ========== */
    var i = 1; 
    var lastdatecount = i; 
    $('#moreProvisionGeneral').click(function(){
        i++;
        var addclassdate = '<div id="provisionGeneral'+i+'" class="provisionGeneral m-t-10 row"><div class="col-md-11"><input id="" name="provision_general_percent[]" type="text" required class="form-control"></div><div class="m-t-10 col-md-11"><div class="input-daterange input-group" id="date-range"><input type="text" class="form-control datepicker-autoclose" name="provision_general_start[]" /><span class="input-group-addon bg-custom text-white b-0">to</span><input type="text" class="form-control datepicker-autoclose" name="provision_general_end[]" /></div></div><button class="btn btn-icon waves-effect waves-light btn-danger m-b-5 removePG" type="button" id="removePG'+i+'"> <i class="fa fa-remove m-r-5"></i> </button></div>';
        $("#provisionGeneral"+lastdatecount).after( addclassdate );
        lastdatecount = i;

        jQuery('.datepicker-autoclose').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
    });

    $(document).on('click','.removePG',function(){
        $(this).parent('div').remove();
        var lastdate_id =  $('.provisionGeneral').last().attr('id');
        lastdatecount = parseInt(lastdate_id.substr(16,lastdate_id.length)); 
    });
    /* ======== Provision General End ========== */
    
    /* ======== Provision Court Start ========== */
    var i1 = 1; 
    var lastdatecount1 = i1; 
    $('#moreProvisionCourt').click(function(){
        i1++;
        var addclassdate1 = '<div id="provisionCourt'+i1+'" class="provisionCourt m-t-10 row"><div class="col-md-11"><input id="" name="provision_court_percent[]" type="text" required class="form-control"></div><div class="m-t-10 col-md-11"><div class="input-daterange input-group" id="date-range"><input type="text" class="form-control datepicker-autoclose" name="provision_court_start[]" /><span class="input-group-addon bg-custom text-white b-0">to</span><input type="text" class="form-control datepicker-autoclose" name="provision_court_end[]" /></div></div><button class="btn btn-icon waves-effect waves-light btn-danger m-b-5 removePC" type="button" id="removePC'+i1+'"> <i class="fa fa-remove m-r-5"></i> </button></div>';
        $("#provisionCourt"+lastdatecount1).after( addclassdate1 );
        lastdatecount1 = i1;

        jQuery('.datepicker-autoclose').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
    });

    $(document).on('click','.removePC',function(){
        $(this).parent('div').remove();
        var lastdate_id1 =  $('.provisionCourt').last().attr('id');
        lastdatecount1 = parseInt(lastdate_id1.substr(14,lastdate_id1.length));
    });

    /* ======== Provision Court End ========== */

    /* ======== Central Provision Start ========== */
    var i2 = 1; 
    var lastdatecount2 = i2; 
    $('#moreProvisionCentral').click(function(){
        i2++;
        var addclassdate2 = '<div id="provisionCentral'+i2+'" class="provisionCentral m-t-10 row"><div class="col-md-11"><input id="" name="provision_central_percent[]" type="text" required class="form-control"></div><div class="m-t-10 col-md-11"><div class="input-daterange input-group" id="date-range"><input type="text" class="form-control datepicker-autoclose" name="provision_central_start[]" /><span class="input-group-addon bg-custom text-white b-0">to</span><input type="text" class="form-control datepicker-autoclose" name="provision_central_end[]" /></div></div><button class="btn btn-icon waves-effect waves-light btn-danger m-b-5 removePCe" type="button" id="removePCe'+i2+'"> <i class="fa fa-remove m-r-5"></i> </button></div>';
        $("#provisionCentral"+lastdatecount2).after( addclassdate2 );
        lastdatecount2 = i2;

        jQuery('.datepicker-autoclose').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
    });

    $(document).on('click','.removePCe',function(){
        $(this).parent('div').remove();
        var lastdate_id2 =  $('.provisionCentral').last().attr('id');
        lastdatecount2 = parseInt(lastdate_id2.substr(16,lastdate_id2.length));
    });

    /* ======== Central Provision End ========== */

    /* ======== Upper Provision Start ========== */
    var i3 = 1; 
    var lastdatecount3 = i3; 
    $('#moreProvisionUpper').click(function(){
        i3++;
        var addclassdate3 = '<div id="provisionUpper'+i3+'" class="provisionUpper m-t-10 row"><div class="col-md-11"><input id="" name="provision_upper_percent[]" type="text" required class="form-control"></div><div class="m-t-10 col-md-11"><div class="input-daterange input-group" id="date-range"><input type="text" class="form-control datepicker-autoclose" name="provision_upper_start[]" /><span class="input-group-addon bg-custom text-white b-0">to</span><input type="text" class="form-control datepicker-autoclose" name="provision_upper_end[]" /></div></div><button class="btn btn-icon waves-effect waves-light btn-danger m-b-5 removePU" type="button" id="removePU'+i3+'"> <i class="fa fa-remove m-r-5"></i> </button></div>';
        $("#provisionUpper"+lastdatecount3).after( addclassdate3 );
        lastdatecount3 = i3;

        jQuery('.datepicker-autoclose').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
    });

    $(document).on('click','.removePU',function(){
        $(this).parent('div').remove();
        var lastdate_id3 =  $('.provisionUpper').last().attr('id');
        lastdatecount3 = parseInt(lastdate_id3.substr(14,lastdate_id3.length));
    });

    /* ======== Upper Provision End ========== */

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
$(document).on('keyup','#emailAddress',function(){ 
    var emailAddress = $('#emailAddress').val();
    $.ajax({
        type:"POST",
        url: "<?php echo base_url('consultant/agent/checkemailID'); ?>",
        data:{emailAddress:emailAddress},
        dataType: 'json',
        success:function(data){ 
           if(data==1){
            $('#emailAddress').val('');
            Command: toastr["error"]('This Email ID Already Exist.');
             return false;
           } 
        }
    });
});
$(document).on('keyup','#userName',function(){ 
    var userName = $('#userName').val();
    $.ajax({
        type:"POST",
        url: "<?php echo base_url('consultant/agent/checkusername'); ?>",
        data:{userName:userName},
        dataType: 'json',
        success:function(data){
           if(data==1){
            $('#userName').val('');
             Command: toastr["error"]('This Username Already Exist.');
             return false;
           } 
        }
    });
});
</script>


    </body>
</html>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
        <meta name="author" content="Coderthemes">
        <!-- App title -->
        <title><?php echo $this->defaultdata->gradLanguageText(164);?></title>
         

         
        <?php echo $header_scripts;?>
        
       
    </head>


    <body class="fixed-left" >

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
                                    <h4 class="page-title" ><!-- Invoice --> <?php echo $this->defaultdata->gradLanguageText(164);?></h4>
                                    
                                    <div class="clearfix"></div>
                                </div>
							</div>
						</div>
                        <!-- end row -->


                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    
                                    <div class="panel-body">
                                        <div class="clearfix">
                                            <div class="pull-left">
                                                <h3><?php echo $user_data->name; ?></h3>
                                            </div>
                                            <div class="pull-right">
                                                <h4><!-- Invoice --><?php echo $this->defaultdata->gradLanguageText(164);?> # <br>
                                                    <strong><?php echo $invoice_data->invoice_no; ?></strong>
                                                </h4>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">

                                                <div class="pull-left m-t-30" style="width:50%;">
                                                    <label for="field-1" class="control-label"><!-- Seller --><?php echo $this->defaultdata->gradLanguageText(305);?></label>
                                                    <p class="text font-13 m-0">
                                                        <span class="m-l-5"><?php echo $user_data->name; ?></span>
                                                    </p>
                                                    <p class="text font-13 m-0">
                                                        <strong><!-- Address --><?php echo $this->defaultdata->gradLanguageText(306);?>:</strong>
                                                        <span class="m-l-5">
                                                            <?php echo $agent_data->street?$agent_data->street.', ':''?>
                                                            <?php echo $agent_data->zip?$agent_data->zip.', ':''?>
                                                            <?php echo $agent_data->town?$agent_data->town:''?>
                                                        </span>
                                                    </p>
                                                    <p class="text font-13 m-0">
                                                        <strong><!-- NIP --><?php echo $this->defaultdata->gradLanguageText(193);?>:</strong>
                                                        <span class="m-l-5"><?php echo $agent_data->NIP; ?> </span>
                                                    </p>

                                                    <?php if($agent_data->KRS){ ?>
                                                    <p class="text font-13 m-0">
                                                        <strong><!-- KRS --><?php echo $this->defaultdata->gradLanguageText(194);?>:</strong>
                                                        <span class="m-l-5"><?php echo $agent_data->KRS; ?> </span>
                                                    </p>
                                                    <?php } ?>
                                                    
                                                    <?php if($agent_data->account_number){ ?>
                                                    <p class="text font-13 m-0">
                                                        <strong><!-- Account Number --><?php echo $this->defaultdata->gradLanguageText(195);?>:</strong> 
                                                        <span class="m-l-5"><?php echo $agent_data->account_number; ?> </span>
                                                    </p>
                                                    <?php } ?>
                                                    

                                                </div>
                                                <div class="pull-right m-t-30">
                                                    <p><strong><!-- Date of invoice --><?php echo $this->defaultdata->gradLanguageText(302);?>: </strong> <?php echo date('d/m/Y', $invoice_data->date_of_invoice)?></p>
                                                    <p><strong><!-- Date of selling --><?php echo $this->defaultdata->gradLanguageText(303);?>: </strong> <?php echo date('d/m/Y', $invoice_data->date_of_selling)?></p>
                                                    <p><strong><!-- Payment Type --><?php echo $this->defaultdata->gradLanguageText(315);?>: </strong> <?php echo $invoice_data->pament_type; ?></p>
                                                    <p><strong><!-- Term of Payment --><?php echo $this->defaultdata->gradLanguageText(316);?>: </strong> <?php echo date('d/m/Y', $invoice_data->term_of_payment)?></p>
                                                    
                                                    <p><strong><!-- Place of invoice --><?php echo $this->defaultdata->gradLanguageText(304);?>: </strong> 
                                                    <?php echo $invoice_data->place_of_invoice; ?>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-md-12">
                                                <div class="pull-left m-t-30">
                                                    <label for="field-1" class="control-label"><!-- Buyer --><?php echo $this->defaultdata->gradLanguageText(307);?></label>
                                                    <p class="text font-13 m-0">
                                                        <strong><?php echo $this->defaultdata->gradLanguageText(191) ?>:</strong>
                                                        <span class="m-l-0"><?php echo $invoice_details->company ?></span>
                                                    </p>
                                                    <p class="text font-13 m-0">
                                                        <strong><?php echo $this->defaultdata->gradLanguageText(306) ?>:</strong>
                                                        <span class="m-l-5"><?php echo $invoice_details->address ?></span>
                                                    </p>
                                                    <p class="text font-13 m-0">
                                                        <strong><?php echo $this->defaultdata->gradLanguageText(190) ?>:</strong>
                                                        <span class="m-l-0"><?php echo $invoice_details->post_code ?></span>
                                                    </p>
                                                    <p class="text font-13 m-0">
                                                        <strong><?php echo $this->defaultdata->gradLanguageText(193) ?>:</strong>
                                                        <span class="m-l-5"><?php echo $invoice_details->nip ?></span>
                                                    </p>
                                                    <p class="text font-13 m-0">
                                                        <strong><?php echo $this->defaultdata->gradLanguageText(194) ?>:</strong>
                                                        <span class="m-l-5"><?php echo $invoice_details->krs ?></span>
                                                    </p>
                                                    <p class="text font-13 m-0">
                                                        <strong><?php echo $this->defaultdata->gradLanguageText(195) ?>:</strong>
                                                        <span class="m-l-5"><?php echo $invoice_details->account_number ?></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end row -->

                                        <div class="m-h-50"></div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table m-t-30">
                                                        <thead>
                                                            <tr>
                                                            <th>#</th>
                                                            <th><!-- Name of service --><?php echo $this->defaultdata->gradLanguageText(309);?></th>
                                                            <th><!-- Quantity --><?php echo $this->defaultdata->gradLanguageText(310);?></th>
                                                            <th><!-- Net Price --><?php echo $this->defaultdata->gradLanguageText(311);?></th>
                                                            <th><!-- VAT --><?php echo $this->defaultdata->gradLanguageText(312);?></th>
                                                            <th><!-- Gross Price --><?php echo $this->defaultdata->gradLanguageText(313);?></th>
                                                        </tr></thead>
                                                        <tbody>
                                                            <?php
                                                            $total_net_price = 0; 
                                                            $total_vat = 0; 
                                                            $total_gross_price = 0; 
                                                            if(count($payment_data)>0){ 
                                                            foreach ($payment_data as $key => $value) {
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $key+1;?></td>
                                                                <td><?php echo $value->service_name;?></td>
                                                                <td>1</td>
                                                                <td><?php echo $value->net_price.' '.$invoice_data->currency;?></td>
                                                                <td><?php echo ($value->vat!=0)?$value->vat.' '.$invoice_data->currency:'ZW';?></td>
                                                                <td><?php echo $value->gross_price.' '.$invoice_data->currency;?></td>
                                                            </tr>
                                                            <?php 
                                                            $total_net_price += $value->net_price; 
                                                            $total_vat += $value->vat; 
                                                            $total_gross_price += $value->gross_price;
                                                            ?>
                                                            <?php } } ?>

                                                            <tr>
                                                                <td></td>
                                                                <td>SUM</td>
                                                                <td></td>
                                                                <td><?php echo number_format($total_net_price,2,'.','').' '.$invoice_data->currency;?></td>
                                                                <td><?php echo ($total_vat!=0)?number_format($total_vat,2,'.','').' '.$invoice_data->currency:'ZW';?></td>
                                                                <td><?php echo number_format($total_gross_price,2,'.','').' '.$invoice_data->currency;?></td>
                                                            </tr>
                                                            
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <?php /*<div class="row">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5 btn-md" style="left: 40%;">Sum to pay: <?php echo $total_gross_price.' '.$invoice_data->currency; ?></button>
                                            </div>
                                        </div>*/ ?>

                                         
                                                
                                                                         
                                            
                                        <div class="row">    
                                            <div class=" col-md-offset-3">
                                                <?php /*<p class="text-right"><b>Total Net Price:</b> <?php echo $total_net_price; ?></p>
                                                <p class="text-right">VAT: <?php echo $total_vat; ?></p>*/ ?>
                                                <hr>
                                                <h3 class="text-right"><!-- Sum to pay --><?php echo $this->defaultdata->gradLanguageText(328);?>: <?php echo number_format($total_gross_price,2,'.','').' '.$invoice_data->currency; ?></h3>
                                            </div>
                                        </div>
                                        <hr>
                                        <?php if($agent_data->vat_payer=='N'){ ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p><?php echo $agent_data->vat_payer_txt; ?></p>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <hr>
                                        <div class="hidden-print">
                                            <div class="pull-right">
                                                <a href="javascript:window.print()" class="btn btn-inverse waves-effect waves-light"><i class="fa fa-print"></i></a>
                                                <!-- <a href="#" class="btn btn-primary waves-effect waves-light">Submit</a> -->
                                            </div>
                                        </div>
                                    </div>
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


        

    </body>
</html>
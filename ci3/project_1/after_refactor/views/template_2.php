<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#ffffff">

    <!-- App title -->
    <title><?= isset($page_title) ? $page_title : ''; ?></title>

    <!-- App css -->
	<?= $header_scripts; ?>

</head>


<body class="bg-transparent">

<!-- HOME -->
<section>
    <div class="container-alt">
        <div class="row">
            <div class="col-sm-12">

                <div class="wrapper-page">

                    <div class="m-t-40 account-pages">
                        <div class="text-center account-logo-box">
                            <h2 class="text-uppercase">
                                <a href="javascript:void(0)" class="text-success">
											<span><img src="<?php echo '/uploads/logos/' . $general_settings->logo; ?>"
                                                       alt="" height="36"> </span>
                                </a>
                            </h2>

                        </div>
                        <div class="account-content">
		                    <?php $this->load->view('partials/backend_info_container'); ?>
		
		                    <?= $content ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <!-- end card-box-->


                    <div class="row m-t-50">
                        <div class="col-sm-12 text-center">
                            <p class="text-muted">Nie pracujesz z nami?
                                <a href="<?= base_url() ?>" class="text-primary m-l-5">
                                    <b>Dołącz do Zespołu!</b>
                                </a>
                            </p>
                        </div>
                    </div>

                </div>
                <!-- end wrapper -->

            </div>
        </div>
    </div>
</section>
<!-- END HOME -->

<script>
	var resizefunc = [];
</script>

<!-- jQuery  -->
<script src="<?php echo base_url(DEFAULT_ASSETS_URL); ?>js/jquery.min.js"></script>
<script src="<?php echo base_url(DEFAULT_ASSETS_URL); ?>js/bootstrap.min.js"></script>
<script src="<?php echo base_url(DEFAULT_ASSETS_URL); ?>js/detect.js"></script>
<script src="<?php echo base_url(DEFAULT_ASSETS_URL); ?>js/fastclick.js"></script>
<script src="<?php echo base_url(DEFAULT_ASSETS_URL); ?>js/jquery.blockUI.js"></script>
<script src="<?php echo base_url(DEFAULT_ASSETS_URL); ?>js/waves.js"></script>
<script src="<?php echo base_url(DEFAULT_ASSETS_URL); ?>js/jquery.slimscroll.js"></script>
<script src="<?php echo base_url(DEFAULT_ASSETS_URL); ?>js/jquery.scrollTo.min.js"></script>

<!-- App js -->
<script src="<?php echo base_url(DEFAULT_ASSETS_URL); ?>js/jquery.core.js"></script>
<script src="<?php echo base_url(DEFAULT_ASSETS_URL); ?>js/jquery.app.js"></script>

</body>
</html>

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
<body class="fixed-left">

<!-- Begin page -->
<div id="wrapper">

	<!-- ========== Left Sidebar Start ========== -->
	<?= isset($header) ? $header : ''; ?>
	
	<?= isset($left_sidebar) ? $left_sidebar : ''; ?>
    <!-- Left Sidebar End -->


    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
			<?php if ($breadcrumbs) { ?>
                <div class="text pull-right">
					<?= $breadcrumbs; ?>
                </div>
                <div class="clearfix"></div>
			<?php } ?>

            <div class="container">
				<? if (isset($page_title) && $show_content_title) { ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="page-title-box p-t-0">
                                <h4 class="page-title m-t-5 m-r-15">
									<?= isset($page_title) ? $page_title : ''; ?>
                                </h4>
								
								<?= isset($upper_header) ? $upper_header : ''; ?>

                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
				<? } ?>
	
	            <?php $this->load->view('partials/backend_info_container'); ?>
	
	            <?= isset($content) ? $content : '' ?>
            </div> <!-- container -->
	        <?= isset($content_footer) ? $content_footer : ''; ?>
        </div> <!-- content -->
    </div>


    <!-- Right Sidebar -->
	<?= isset($right_sidebar) ? $right_sidebar : ''; ?>
    <!-- /Right-bar -->
	
	<?= isset($footer) ? $footer : ''; ?>
</div>


<?= isset($footer_scripts) ? $footer_scripts : ''; ?>

<?= isset($common_script) ? $common_script : ''; ?>


</body>
</html>

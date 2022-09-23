<?php if (isset($error) || isset($success) || $this->session->flashdata('error') || $this->session->flashdata('success')) { ?>
    <div>
		<?php if ($this->session->flashdata('error') || isset($error)) { ?>
            <div class="alert alert-icon alert-danger alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
				<?= isset($error) ? $error : $this->session->flashdata('error'); ?>
            </div>
		<?php } ?>
		
		<?php if ($this->session->flashdata('success') || isset($success)) { ?>
            <div class="alert alert-icon alert-success alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
				<?= isset($success) ? $success : $this->session->flashdata('success'); ?>
            </div>
		<?php } ?>
    </div>
<?php } ?>

<form method="post" class="form-horizontal"
      action="<?= base_url('reset-password-process'); ?>">
	<?= form_hidden($csrf); ?>

    <div class="form-group ">
        <div class="col-xs-12">
            <input class="form-control" type="password" name="password" required
                   placeholder="Nowe hasło" value="">
        </div>
    </div>

    <div class="form-group">
        <div class="col-xs-12">
            <input class="form-control" type="password" name="confirmPassword" required placeholder="Potwierdź hasło">
        </div>
    </div>

    <input type="hidden" name="strCode" value="<?= $str; ?>">

    <div class="form-group account-btn text-center m-t-10">
        <div class="col-xs-12">
            <button class="btn w-md btn-bordered btn-danger waves-effect waves-light"
                    type="submit"><?= lang('label_save') ?></button>
        </div>
    </div>

</form>


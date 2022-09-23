<li>
    <h5><?= lang('label_notifications'); ?></h5>
</li>
<?php
foreach ($notificationData as $notification) {
	?>
    <li>
        <a href="<?= $notification->redirect_url; ?>" data-id="<?= $notification->id; ?>"
           class="user-list-item readNotification">
            <div class="icon <?= $notification->background; ?>">
                <i class="<?= $notification->icon; ?>"></i>
            </div>
            <div class="user-desc">
                <span class="name"><?= $notification->title; ?></span>
            </div>
        </a>
    </li>
<?php } ?>

<li class="all-msgs text-center">
    <p class="m-0"><a href="<?= base_url('user/notification'); ?>"><?= lang('label_see_all_notification'); ?></a>
    </p>
    <p class="m-0"><a
            href="<?= base_url('user/notificationallread'); ?>"><?= lang('label_mark_all_as_read'); ?></a>
    </p>
</li>


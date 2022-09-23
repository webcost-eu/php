<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul>
                <!-- <li class="menu-title">Navigation</li> -->

                <li class="has_sub">
                    <a href="<?= base_url('dashboard'); ?>" class="waves-effect"><i
                            class="mdi mdi-view-dashboard"></i><span> <?= lang('label_dashboard'); ?> </span>
                    </a>
                </li>
	
	            <? if (hasAccess('role_access')) { ?>
                    <li class="has_sub">
                        <a href="<?= base_url('role_access/agent'); ?>" class="waves-effect">
                            <i class="mdi mdi-account-network"></i>
                            <span><?= lang('label_menu_role_access'); ?> </span>
                        </a>
                    </li>
	            <?php } ?>
	
	            <? if (hasAccess('language')) { ?>
                    <li class="has_sub">
                        <a href="<?= base_url('language'); ?>" class="waves-effect"><i
                                class="fa fa-language"></i><span> <?= lang('label_language'); ?> </span>
                        </a>
                    </li>
	            <?php } ?>
            </ul>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>


    </div>
    <!-- Sidebar -left -->

</div>

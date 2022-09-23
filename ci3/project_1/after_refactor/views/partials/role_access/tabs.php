<ul class="nav nav-tabs">
	<? foreach (getRoles() as $roleId => $role) { ?>
		<? if ($roleId != USER_ROLE_ADMIN && $roleId != USER_ROLE_CLIENT) { ?>
            <li class="<?php echo ($selected_role == $role) ? 'active' : ''; ?>">
                <a href="<?php echo base_url('role_access/' . $role); ?>">
					<span class="visible-xs">
						<i class="fa fa-user"
                           title="<?= $roles_translating[$roleId] ?>"></i>
					</span>
                    <span class="hidden-xs">
                        <?= $roles_translating[$roleId] ?>
					</span>
                </a>
            </li>
		<? } ?>
	<? } ?>
</ul>


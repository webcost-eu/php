<?php
declare(strict_types=1);

final class User_enum
{
    const TYPES = [
        'admin' => USER_ROLE_ADMIN,
        'subadmin' => USER_ROLE_ADMIN,
        'agent' => USER_ROLE_AGENT,
        'solicitor' => USER_ROLE_SOLICITOR,
        'client' => USER_ROLE_CLIENT,
        'consultant' => USER_ROLE_CONSULTANT,
        'accountant' => USER_ROLE_ACCOUNTANT,
        'secretary' => USER_ROLE_SECRETARY,
    ];
}

<?php
declare(strict_types=1);

final class Activity_enum
{
    const TYPE_LIST = 'list';
    const TYPE_VIEW = 'view';
    const TYPE_EDIT = 'edit';
    const TYPE_ADD = 'add';
    const TYPE_DELETE = 'delete';

    const TYPES = [
        self::TYPE_LIST, self::TYPE_VIEW, self::TYPE_EDIT, self::TYPE_ADD, self::TYPE_DELETE
    ];
}

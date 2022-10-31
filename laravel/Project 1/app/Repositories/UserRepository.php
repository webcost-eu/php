<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Base\BaseModelActionRepository;

class UserRepository extends BaseModelActionRepository
{
    protected function getModel(): string
    {
        return User::class;
    }

}
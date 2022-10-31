<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Statement;
use App\Repositories\Base\BaseModelIndexRepository;
use App\Repositories\Base\BaseModelRepository;

class StatementRepository extends BaseModelIndexRepository
{
    protected array $joinRelationships = [
        'employer' => [
            'key' => 'employer_id',
            'table' => 'employers'
        ],
        'status' => [
            'key' => 'status_id',
            'table' => 'statement_statuses',
        ],
        'responsible_person' => [
            'key' => 'responsible_person_id',
            'table' => 'users',
        ],
    ];

    protected function getModel(): string
    {
        return Statement::class;
    }
}
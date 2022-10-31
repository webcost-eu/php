<?php

namespace App\Http\Resources\Collection;

use App\Http\Resources\StatementResource;

class StatementCollectionResource extends BaseCollectionResource
{
    public $collects = StatementResource::class;
}

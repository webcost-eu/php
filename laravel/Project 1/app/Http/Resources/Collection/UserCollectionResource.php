<?php

namespace App\Http\Resources\Collection;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollectionResource extends BaseCollectionResource
{

    public $collects = UserResource::class;
    
}

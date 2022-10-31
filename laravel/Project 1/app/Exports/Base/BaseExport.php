<?php

namespace App\Exports\Base;

use App\DTOs\IndexDTO;
use App\Repositories\Base\BaseModelIndexRepository;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

abstract class BaseExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $with = [];
    protected IndexDTO $dataDTO;
    protected BaseModelIndexRepository $repository;

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->repository->setWith($this->with)->modelSearchBuilder($this->dataDTO)->get();
    }
}
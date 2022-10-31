<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Http\DTOs\IndexRequestDTO;
use App\Http\DTOs\Statement\StatementStoreRequestDTO;
use App\Http\DTOs\Statement\StatementUpdateRequestDTO;
use App\Models\Base\AuthModel;
use App\Models\Statement;
use App\Repositories\StatementRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StatementService
{

    private array $with = [
        'employer',
        'client',
        'leadAuthority',
        'status',
        'responsiblePerson',
        'files',
        'history.user:id,first_name,last_name',
    ];

    public function __construct(
        private readonly StatementRepository $repository,
    )
    {
        
    }

    public function index(AuthModel $authModel, IndexRequestDTO $dataDTO): LengthAwarePaginator
    {
        $this->repository->setWith($this->with);

        return is_null($dataDTO->s) ? $this->repository->modelPaginate($dataDTO)
                                    : $this->repository->modelSearchPaginate($dataDTO);
    }

    public function store(AuthModel $authModel, StatementStoreRequestDTO $dataDTO): Statement
    {
        return $this->repository->setWith($this->with)->create($dataDTO->toArray());
    }

    public function show(AuthModel $authModel, int $id): Statement
    {
        return $this->repository->setWith($this->with)->findOrFailBy($id);
    }

    public function update(AuthModel $authModel, int $id, StatementUpdateRequestDTO $dataDTO): Statement
    {
        return $this->repository->setWith($this->with)->update($id, $dataDTO->toArray());
    }

    public function destroy(AuthModel $authModel, int $id): void
    {
        $foundStatement = $this->repository->findOrFailBy($id);
        $foundStatement->delete();
    }
}
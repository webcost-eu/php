<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Http\DTOs\User\UserStoreRequestDTO;
use App\Http\DTOs\User\UserUpdateRequestDTO;
use App\Models\Base\AuthModel;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Arr;

class UserService
{
    public function __construct(
        private UserRepository $repository
    )
    {
        
    }

    public function index(AuthModel $authModel, ?string $search, ?int $perPage): Paginator
    {
        if (! is_null($search)) {
            return $this->repository->setWith(['role.permissions'])->searchPaginate(['first_name', 'last_name'], $search, $perPage);
        } else {
            return $this->repository->setColumns()->setWith(['role.permissions'])->paginate(perPage: $perPage);
        }
    }

    public function store(UserStoreRequestDTO $dataDTO): User
    {
        return $this->repository->setWith(['role.permissions'])->create($dataDTO->toArray());
    }

    public function show(AuthModel $authModel, int $id): User
    {
        return $this->repository->setWith(['role.permissions'])->findOrFailBy($id);
    }

    public function update(AuthModel $authModel, UserUpdateRequestDTO $dataDTO): User
    {
        $this->repository->setWith(['role.permissions']);

        return $this->repository->update($dataDTO->id, $dataDTO->onlyValidated()->toArray());;
    }

    public function destroy(int $id): void
    {
        /** @var User $foundUser */
        $foundUser = $this->repository->findOrFailBy($id);
        $foundUser->delete();
    }
}
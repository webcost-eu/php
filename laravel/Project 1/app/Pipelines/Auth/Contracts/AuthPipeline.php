<?php

declare(strict_types=1);

namespace App\Pipelines\Auth\Contracts;

use App\DTOs\Auth\AuthTokenDTO;
use App\Http\DTOs\Auth\LoginRequestDTO;
use App\Models\Base\AuthModel;
use App\Models\Enums\AppGuard;
use App\Repositories\Base\BaseModelRepository;

abstract class AuthPipeline extends BaseModelRepository
{
    protected const GUARD = AppGuard::API;

    /**
     * @param array $data
     * @param \Closure $next
     * @return AuthTokenDTO|array
     */
    public function handle(array $data, \Closure $next): AuthTokenDTO|array
    {
        /** @var LoginRequestDTO $dataDTO */
        ['dataDTO' => $dataDTO, 'field' => $field] = $data;

        /** @var string|bool $token */
        if ($token = auth()->guard(static::GUARD->value)->attempt($dataDTO->only($field, 'password')->toArray())) {
            return AuthTokenDTO::make($dataDTO->device_id, $token, static::GUARD);
        }

        return $next($data);
    }

    /**
     * @param array $data
     * @param \Closure $next
     * @return AuthModel|array
     */
    public function getSendRestoreLinkModel(array $data, \Closure $next): AuthModel|array
    {
        $foundModel = $this->findOrNullBy($data['value'], $data['field']);

        return $foundModel instanceof AuthModel ? $foundModel : $next($data);
    }
}
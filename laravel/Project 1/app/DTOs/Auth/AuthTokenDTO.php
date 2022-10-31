<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Models\Enums\AppGuard;
use Spatie\DataTransferObject\DataTransferObject;

class AuthTokenDTO extends DataTransferObject
{
    public readonly string $device_id;
    public readonly string $token;
    public readonly ?AppGuard $guard;

    public static function make(string $deviceId, string $token, AppGuard $guard = null): static
    {
        return new static(
            device_id: $deviceId,
            token: $token,
            guard: $guard
        );
    }

}
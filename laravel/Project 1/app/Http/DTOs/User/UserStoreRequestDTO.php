<?php

declare(strict_types=1);

namespace App\Http\DTOs\User;

use App\DTOs\Contracts\IFromRequestDTO;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\DataTransferObject;

final class UserStoreRequestDTO extends DataTransferObject implements IFromRequestDTO
{
    public readonly string $first_name;
    public readonly string $last_name;
    public readonly string $username;
    public readonly string $email;
    public readonly string $phone;
    public readonly string $password;
    public readonly int $role_id;
    public readonly ?bool $is_active;

    public static function makeFromRequest(Request|FormRequest $request): static
    {
        return new static(
            first_name: $request->get('first_name'),
            last_name: $request->get('last_name'),
            username: $request->get('username'),
            email: $request->get('email'),
            phone: $request->get('phone'),
            password: $request->get('password'),
            role_id: $request->get('role_id'),
            is_active: $request->get('is_active', true),
        );
    }
}
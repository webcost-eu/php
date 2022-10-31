<?php

declare(strict_types=1);

namespace App\Http\DTOs\User;

use App\DTOs\Contracts\IFromRequestDTO;
use App\DTOs\Traits\ValidatedPropertiesDTOTrait;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\DataTransferObject;

final class UserUpdateRequestDTO extends DataTransferObject implements IFromRequestDTO
{

    use ValidatedPropertiesDTOTrait;

    public readonly int $id;
    public readonly ?string $first_name;
    public readonly ?string $last_name;
    public readonly ?string $username;
    public readonly ?string $email;
    public readonly ?string $phone;
    public readonly ?string $password;
    public readonly ?int $role_id;
    public readonly ?bool $is_active;

    public static function makeFromRequest(Request|FormRequest $request): static
    {

        self::setValidatedProperties($request);

        return new static(
            id: $request->route()->parameter('user'),
            first_name: $request->get('first_name'),
            last_name: $request->get('last_name'),
            username: $request->get('username'),
            email: $request->get('email'),
            phone: $request->get('phone'),
            password: $request->get('password'),
            role_id: $request->get('role_id'),
            is_active: $request->get('is_active'),
        );
    }
}
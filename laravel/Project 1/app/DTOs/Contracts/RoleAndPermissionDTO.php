<?php

namespace App\DTOs\Contracts;

use App\DTOs\Traits\ValidatedPropertiesDTOTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\DataTransferObject;

abstract class RoleAndPermissionDTO extends DataTransferObject
{
    use ValidatedPropertiesDTOTrait;
    
    /**
     * @var string|null $name
     */
    public ?string $name;

    /**
     * @var string|null $guard_name
     */
    public ?string $prefix;

    /**
     * @var array|null $permissions
     */
    public ?array $permissions;

    /**
     * @var boolean
     */
    public bool $is_active;

    /**
     * @var array|null $roles
     */
    public ?array $roles;

    /**
     * @param Request|FormRequest $request
     * @return static
     */
    public static function makeFromRequest(Request|FormRequest $request): static
    {
        self::setValidatedProperties($request);

        return new static(
            name: $request->get('name'),
            prefix: $request->get('prefix'),
            permissions: $request->get('permissions'),
            is_active: $request->get('is_active', true),
            roles: $request->get('roles'),
        );
    }
}
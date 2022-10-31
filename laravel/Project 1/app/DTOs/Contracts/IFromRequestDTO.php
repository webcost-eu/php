<?php

declare(strict_types=1);

namespace App\DTOs\Contracts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

interface IFromRequestDTO
{
    public static function makeFromRequest(Request|FormRequest $request): self;
}
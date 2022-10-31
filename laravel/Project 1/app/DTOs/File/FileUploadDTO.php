<?php

declare(strict_types=1);

namespace App\DTOs\File;

use App\DTOs\Contracts\IFromRequestDTO;
use Illuminate\Http\UploadedFile;
use Spatie\DataTransferObject\DataTransferObject;

abstract class FileUploadDTO extends DataTransferObject implements IFromRequestDTO
{
    public ?string $fileable_type;
    public ?string $fileable_id;

    public ?string $file_name;
    public UploadedFile $file;
    public bool $is_public;
}
<?php

declare(strict_types=1);

namespace App\Http\DTOs\Statement;

use App\DTOs\Contracts\IFromRequestDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\DataTransferObject;

class StatementUpdateRequestDTO extends DataTransferObject implements IFromRequestDTO
{
    public readonly int $employer_id;
    public readonly int $client_id;
    public readonly string $client_first_name;
    public readonly string $client_last_name;
    public readonly ?string $ordered_date;
    public readonly ?string $submission_date;
    public readonly ?string $receipt_date;
    public readonly ?string $valid_from_date;
    public readonly ?string $valid_until_date;
    public readonly int $lead_authority_id;
    public readonly ?string $comment;
    public readonly int $status_id;
    public readonly int $responsible_person_id;

    public static function makeFromRequest(Request|FormRequest $request): static
    {
        return new static(
            employer_id: $request->get('employer_id'),
            client_id: $request->get('client_id'),
            client_first_name: $request->get('client_first_name'),
            client_last_name: $request->get('client_last_name'),
            ordered_date: $request->get('ordered_date'),
            submission_date: $request->get('submission_date'),
            receipt_date: $request->get('receipt_date'),
            valid_from_date: $request->get('valid_from_date'),
            valid_until_date: $request->get('valid_until_date'),
            lead_authority_id: $request->get('lead_authority_id'),
            comment: $request->get('comment'),
            status_id: $request->get('status_id'),
            responsible_person_id: $request->get('responsible_person_id'),
        );
    }
}
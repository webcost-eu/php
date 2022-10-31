<?php

namespace App\Http\Requests\Api;

use App\Models\Base\AppAuth;
use Illuminate\Foundation\Http\FormRequest;

class StatementUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return AppAuth::authCheck();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'employer_id' => 'required|integer|exists:employers,id',
            'client_id' => 'required|integer|exists:clients,id',
            'client_first_name' => 'required|string',
            'client_last_name' => 'required|string',
            'ordered_date' => 'nullable|date',
            'submission_date' => 'nullable|date',
            'receipt_date' => 'nullable|date',
            'valid_from_date' => 'nullable|date',
            'valid_until_date' => 'nullable|date',
            'lead_authority_id' => 'required|integer|exists:statement_lead_authorities,id',
            'comment' => 'nullable|string',
            'status_id' => 'required|integer|exists:statement_statuses,id',
            'responsible_person_id' => 'required|integer|exists:users,id',
        ];
    }
}

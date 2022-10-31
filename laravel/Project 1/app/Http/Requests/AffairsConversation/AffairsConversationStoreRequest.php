<?php

namespace App\Http\Requests\AffairsConversation;

use App\Models\Base\AppAuth;
use Illuminate\Foundation\Http\FormRequest;

class AffairsConversationStoreRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return AppAuth::authCheck();
    }

    protected function prepareForValidation()
    {
        $this->merge($this->route()->parameters);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:affairs,id',
            'type' => 'required|string|in:sms,email',
            'title' => 'required_if:type,email|string',
            'send_to' => ['required', 'array', function (string $prop, array $value, \Closure $closure): bool {
                if ($this->type === 'email') {
                    foreach ($value as $key => $email) {
                        if (! filter_var($email, FILTER_VALIDATE_EMAIL))
                            $closure("Is not Email : {$email}");
                    }
                } else if ($this->type === 'sms') {
                    foreach ($value as $key => $phone) {
                        if (is_null($phone))
                            $closure("Is not phone : {$phone}");
                    }
                }

                return true;
            }],
            'text' => 'required|string',
            'files' => 'array',
            'files.*.id' => 'integer|exists:files,id',
        ];
    }
}

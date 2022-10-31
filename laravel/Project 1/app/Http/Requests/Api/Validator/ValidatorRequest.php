<?php

namespace App\Http\Requests\Api\Validator;

use App\Models\Base\AppAuth;
use App\Models\Base\AuthModel;
use App\Repositories\Base\BaseModelRepository;
use App\Repositories\ClientRepository;
use App\Repositories\EmployerRepository;
use App\Repositories\UserRepository;
use App\Rules\ServerUniqueColumnRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidatorRequest extends FormRequest
{
    /**
     * @var AuthModel|null
     */
    private ?AuthModel $authModel;

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return AppAuth::authCheck();
    }

    /**
     * @return void
     */
    // protected function prepareForValidation()
    // {
    //     $this->authModel = AppAuth::authModel();
    // }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'string',
                'email',
                new ServerUniqueColumnRule($this)
            ],
            'username' => [
                'string',
                new ServerUniqueColumnRule($this)
            ],
            'phone' => [
                'string',
                new ServerUniqueColumnRule($this)
            ],
        ];
    }
}

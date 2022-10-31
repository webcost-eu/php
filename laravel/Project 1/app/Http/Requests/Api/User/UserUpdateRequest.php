<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->guard('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {

        $id = $this->route()->parameter('user');

        return [
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'username' => "string|alpha_dash|max:50|unique:users,username,$id,id",
            'email' => "string|email|unique:users,email,$id,id",
            'phone' => "string|unique:users,phone,$id,id",
            'password' => 'string|min:6|max:50|confirmed',
            'role_id' => 'integer|exists:roles,id',
            'is_active' => 'boolean',
        ];
    }
}

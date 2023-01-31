<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        if (request()->method() == 'POST') {
            return [
                'first_name' => ['required', 'string', 'max:200'],
                'last_name' => ['required', 'string', 'max:200'],
                'email' => ['required', 'email', 'max:200', 'unique:users,email'],
                'phone_number' => ['required', 'string', 'max:11', 'unique:users,phone_number'],
                'password' => ['nullable', 'string'],
                'confirm_password' => ['nullable', 'same:password'],
                'user_type' => ['required', 'string', Rule::in(User::ALL_USER_TYPES)],

            ];
        }
        if (request()->method() == 'PATCH') {
            return [
                'first_name' => ['sometimes', 'string', 'max:200'],
                'last_name' => ['sometimes', 'string', 'max:200'],
                'email' => ['sometimes', 'email', 'max:200', Rule::unique('users', 'email')->ignore($this->user)],
                'phone_number' => ['sometimes', 'string', 'max:11', Rule::unique('users', 'phone_number')->ignore($this->user)],
                'user_type' => ['sometimes', 'string', Rule::in(User::ALL_USER_TYPES)],
            ];
        }
        return [];
    }
}

<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read string first_name
 * @property-read string last_name
 * @property-read string email
 * @property-read string phone_number
 * @property-read string password
 * @property-read string user_type
 */
class RegisterRequest extends FormRequest
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
    public function rules(): array
    {
        if (request()->method() == 'POST') {
            return [
                'first_name' => ['required', 'string', 'max:200'],
                'last_name' => ['required', 'string', 'max:200'],
                'email' => ['required', 'email', 'max:200', 'unique:users,email'],
                'phone_number' => ['required', 'string', 'max:11', 'unique:users,phone_number'],
                'password' => ['required', 'string'],
                'confirm_password' => ['required', 'same:password'],
                'user_type' => ['required', 'string', Rule::in(User::REGULAR_USER_TYPES)],

            ];
        }
        if (request()->method() == 'PATCH') {
            return [
                'first_name' => ['sometimes', 'string', 'max:200'],
                'last_name' => ['sometimes', 'string', 'max:200'],
                'email' => ['sometimes', 'email', 'max:200', 'unique:users,email'],
                'phone_number' => ['sometimes', 'string', 'max:11', 'unique:users,phone_number'],
                'user_type' => ['sometimes', 'string', Rule::in(User::REGULAR_USER_TYPES)],
            ];
        }
        return [];
    }
}

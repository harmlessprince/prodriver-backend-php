<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\RequiredIf;

class BankRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
        return [
            'user_id' => [new RequiredIf(request()->user()->user_type === User::USER_TYPE_ADMIN), 'integer', 'exists:users,id'],
            'bank_id' => ['required', 'integer', 'exists:banks,id'],
            'account_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'min:10']
        ];
    }
}

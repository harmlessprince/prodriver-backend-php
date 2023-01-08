<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'bank_id' => ['required', 'integer', 'exists:banks,id'],
            'account_name' => ['required', 'string', '200'],
            'account_number' => ['required', 'string', 'min:10']
        ];
    }
}

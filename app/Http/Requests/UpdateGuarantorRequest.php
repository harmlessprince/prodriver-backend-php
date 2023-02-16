<?php

namespace App\Http\Requests;

use App\Models\File;
use App\Models\User;
use App\Rules\FileExists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

/**
 * @property string name
 * @property string email
 * @property string home_address
 * @property string work_address
 * @property string phone_number
 * @property string gender
 * @property string relationship
 * @property string occupation
 * @property int id_card_id
*/
class UpdateGuarantorRequest extends FormRequest
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
        $user = request()->user();
        $fileExists = Rule::exists(File::class, 'id')
            ->where('type', File::TYPE_IMAGE)
            ->where('creator_id', $user->id);
        return [
            'user_id' => [new RequiredIf(request()->user()->user_type === User::USER_TYPE_ADMIN), 'integer', 'exists:users,id'],
            'first_name' => ['required', 'string', 'max:200'],
            'last_name' => ['required', 'string', 'max:200'],
            'email' => ['sometimes', 'email', 'max:200'],
            'home_address' => ['sometimes', 'string', 'max:200'],
            'work_address' => ['sometimes', 'string', 'max:200'],
            'phone_number' => ['required', 'string', 'max:14'],
            'gender' => ['required', 'string', Rule::in(['male', 'female'])],
            'relationship' => ['sometimes', 'string'],
            'occupation' => ['sometimes', 'string'],
            'id_card_id' => ['sometimes', 'integer',  $fileExists]
        ];
    }
}

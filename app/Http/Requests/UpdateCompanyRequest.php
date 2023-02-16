<?php

namespace App\Http\Requests;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

/**
 * @property string $name
 * @property string email
 * @property string phone_number
 * @property string rc_number
 * @property string description
 * @property int goods_in_transit_insurance_id
 * @property int cac_document_id
 * @property int fidelity_insurance_id
*/
class UpdateCompanyRequest extends FormRequest
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
    public function rules()
    {
        $user = request()->user();
        $fileExists = Rule::exists(File::class, 'id')
        ->where('type', File::TYPE_IMAGE)
        ->where('creator_id', $user->id);
        return [
            'user_id' => [new RequiredIf(request()->user()->user_type === User::USER_TYPE_ADMIN), 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email'],
            'phone_number' => ['required', 'string', 'min:11'],
            'rc_number' => ['sometimes', 'string', 'max:200'],
            'cac_document_id' => ['sometimes', 'integer', $fileExists],
            'goods_in_transit_insurance_id' => ['sometimes', 'integer', $fileExists],
            'fidelity_insurance_id' => ['sometimes', 'integer', $fileExists],
        ];
    }
}

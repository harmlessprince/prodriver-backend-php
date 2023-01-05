<?php

namespace App\Rules;

use App\Models\File;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Validation\Rule;

class FileExists implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail): void
    {
        $user = request()->user();
        $fileExists = Rule::exists(File::class, 'id')
            ->where('type', File::TYPE_IMAGE)
            ->where('creator_id', $user->id);
        dd($fileExists);
        if (!$fileExists){
            $fail('The supplied :attribute does not exists');
        }
    }
}

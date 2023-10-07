<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OnlyOneIsCorrect implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $count = collect($value)->filter(fn ($option) => $option['is_correct'])->count();
        if ($count !== 1) {
            $fail('Only one option can be correct.');
        }
        
    }
}

<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BeforeEndTimeIfExists implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value) {
            $end_time = request()->end_time;
            if ($end_time && $value >= $end_time) {
                    $fail('The start time must be before the end time.');
            }
        }
    }
}

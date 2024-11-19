<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MobileRule implements ValidationRule
{
    public const Pattern = "/^[9]{1}\d{9}$/";

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match(self::Pattern, $value)) {
            $fail('فرمت شماره موبایل صحیح نمی باشد');
        }
    }
}

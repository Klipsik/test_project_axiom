<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Lang;

class PriceRange implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $priceFrom = request()->input('price_from');

        if ($priceFrom !== null && $value !== null && $value < $priceFrom) {
            $fail(Lang::get('validation.price_range'));
        }
    }
}

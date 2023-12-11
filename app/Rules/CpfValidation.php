<?php

namespace App\Rules;

use App\Utils\Cpf;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Cpf::validate($value)) {
            $fail('O valor do :attribute informado é inválido.');
        }
    }
}

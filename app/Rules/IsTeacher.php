<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsTeacher implements ValidationRule
{
    protected $roles;

    public function __construct(array $roles = ['Guru SD', 'Guru KB'])
    {
        $this->roles = $roles;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value->roles()->whereIn('name', $this->roles)->exists()) {
            $fail('Only teachers (Guru SD or Guru KB) can perform this action.');
        }
    }
}

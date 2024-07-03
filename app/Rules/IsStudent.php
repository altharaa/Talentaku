<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsStudent implements ValidationRule
{
    protected $roles;

    public function __construct(array $roles = ['Murid SD', 'Murid KB'])
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
            $fail('Only students (Murid SD or Murid KB) can perform this action.');
        }
    }
}

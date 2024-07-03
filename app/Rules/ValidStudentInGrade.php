<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Grade;

class ValidStudentInGrade implements ValidationRule
{
    protected $gradeId;

    /**
     * Create a new rule instance.
     *
     * @param  int  $gradeId
     * @return void
     */
    public function __construct($gradeId)
    {
        $this->gradeId = $gradeId;
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
        $exists = Grade::find($this->gradeId)->members()->where('user_id', $value)->exists();

        if (! $exists) {
            $fail('The selected user is not a valid student in this grade.');
        }
    }
}

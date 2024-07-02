<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Grade;

class ValidTeacherInGrade implements ValidationRule
{
    protected $teacher;

    /**
     * Create a new rule instance.
     *
     * @param  mixed  $teacher
     * @return void
     */
    public function __construct($teacher)
    {
        $this->teacher = $teacher;
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
        $grade = Grade::findOrFail($value);

        if ($grade->teacher_id !== $this->teacher->id) {
            $fail('You are not authorized to perform this action for the specified grade.');
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'student_id', 'score'];

    public function media()
    {
        return $this->hasMany(TaskSubmissionMedia::class, 'submission_id');
    }
}

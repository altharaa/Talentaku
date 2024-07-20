<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmissionMedia extends Model
{
    use HasFactory;

    protected $fillable = ['file_path', 'submission_id'];

    public function submission()
    {
        return $this->belongsTo(TaskSubmission::class, 'submission_id');
    }
}

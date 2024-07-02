<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentReportMedia extends Model
{
    use HasFactory;

    protected $table = 'student_report_media';

    protected $fillable = ['student_report_id', 'file_path', 'file_type'];

    public function studentReport()
    {
        return $this->belongsTo(StudentReport::class);
    }
}

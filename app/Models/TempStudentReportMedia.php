<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempStudentReportMedia extends Model
{
    use HasFactory;

    protected $table = 'temp_student_report_media';

    protected $fillable = [
        'path_name',
    ];
}

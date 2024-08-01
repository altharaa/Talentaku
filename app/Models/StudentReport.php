<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'created', 'semester_id', 'kegiatan_awal', 'awal_point', 
        'kegiatan_inti', 'inti_point', 'snack', 'snack_point', 
        'inklusi', 'inklusi_point', 'catatan', 'teacher_id', 
        'student_id', 'grade_id'
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class)->whereHas('roles', function($query) {
            $query->whereIn('name', ['Guru KB', 'Guru SD']);
        });
    }

    public function student()
    {
        return $this->belongsTo(User::class)->whereHas('roles', function($query) {
            $query->whereIn('name', ['Murid KB', 'Murid SD']);
        });
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    public function media()
    {
        return $this->hasMany(StudentReportMedia::class);
    }

    public function semester()
    {
        return $this->belongsTo(StudentReportSemester::class, 'semester_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'created', 'semester_id',
        'kegiatan_awal_dihalaman', 'dihalaman_hasil',
        'kegiatan_awal_berdoa', 'berdoa_hasil',
        'kegiatan_inti_satu', 'inti_satu_hasil',
        'kegiatan_inti_dua', 'inti_dua_hasil',
        'kegiatan_inti_tiga', 'inti_tiga_hasil',
        'snack',
        'inklusi', 'inklusi_hasil',
        'inklusi_penutup', 'inklusi_penutup_hasil',
        'inklusi_doa', 'inklusi_doa_hasil',
        'catatan',
        'teacher_id', 'student_id', 'grade_id'
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

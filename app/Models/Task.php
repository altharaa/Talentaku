<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_id',
        'teacher_id',
        'title',
        'start_date',
        'end_date',
        'desc',
    ];

    public function media()
    {
        return $this->hasMany(TaskMedia::class);
    }

    public function links()
    {
        return $this->hasMany(TaskLink::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
        'unique_code',
        'isactive',
        'teacher_id',
        'level_id'
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'grade_members', 'grade_id', 'student_id');
    }

    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    public function level()
    {
        return $this->belongsTo(GradeLevel::class, 'level_id');
    }

    public function userHasAccess($userId)
    {
        return $this->teacher_id === $userId || $this->members()->where('id', $userId)->exists();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}

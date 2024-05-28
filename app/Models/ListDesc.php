<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListDesc extends Model
{
    use HasFactory;

    public function information() {
        return $this->belongsToMany(Information::class, 'information_list_desc')->withTimestamps();
    }

    protected $fillable = ['grade_id', 'student_id'];

    // Relationship to Grade
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    // Relationship to Student
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}

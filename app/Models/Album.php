<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $fillable = ['desc', 'date', 'grade_id', 'teacher_id'];

    public function media()
    {
        return $this->hasMany(AlbumMedia::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

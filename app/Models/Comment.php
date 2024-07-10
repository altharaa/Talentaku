<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['comments', 'grade_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function media()
    {
        return $this->hasMany(CommentMedia::class);
    }

    public function reply()
    {
        return $this->hasMany(CommentReply::class);
    }
}

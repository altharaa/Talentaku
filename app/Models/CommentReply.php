<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReply extends Model
{
    use HasFactory;

    protected $fillable = ['comment_id', 'user_id', 'reply'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

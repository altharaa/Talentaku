<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementReply extends Model
{
    use HasFactory;

    protected $fillable = ['announce_id', 'user_id', 'replies'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function announcement()
    {
        return $this->belongsTo(Announcement::class, 'announce_id');
    }
}

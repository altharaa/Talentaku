<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = ['announcements', 'grade_id', 'user_id'];

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
        return $this->hasMany(AnnouncementMedia::class, 'announce_id');
    }

    public function reply()
    {
        return $this->hasMany(AnnouncementReply::class, 'announce_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementMedia extends Model
{
    protected $fillable = ['announce_id', 'original_file_name', 'file_name'];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class, 'announce_id');
    }
}

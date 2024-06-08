<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    // Jika tabel album_photos menggunakan konvensi nama lain, bisa diatur disini.
    protected $table = 'album_photos';

    public function photos()
    {
        return $this->hasMany(AlbumPhoto::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'album_id');
    }
    

    public function getGrade()
    {
        return $this->grades()->get();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    // Jika tabel album_photos menggunakan konvensi nama lain, bisa diatur disini.
    protected $table = 'album_photos';

    // Definisikan relasi ke grades
    public function grades()
    {
        return $this->hasMany(Grade::class, 'album_id', 'album_id');
    }

    // Fungsi untuk mengambil data grade
    public function getGrade()
    {
        return $this->grades()->get();
    }
}

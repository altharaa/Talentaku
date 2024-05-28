<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    use HasFactory;

    protected $table = 'information';

    public function list_desc() {
        return $this->belongsToMany(ListDesc::class, 'information_list_desc')->withTimeStamps();
    }
}
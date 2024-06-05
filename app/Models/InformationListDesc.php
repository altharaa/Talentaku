<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformationListDesc extends Model
{
    use HasFactory;

    public function information() {
        return $this->belongsToMany(Information::class, 'information')->withTimestamps();
    }

    public function information_list() {
        return $this->belongsToMany(ListDesc::class, 'list_desc')->withTimestamps();
    }
}

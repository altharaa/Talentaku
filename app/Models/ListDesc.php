<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListDesc extends Model
{
    use HasFactory;

    protected $table = 'list_desc';
    public function informations() {
        return $this->belongsToMany(InformationListDesc::class, 'information_list_desc');
    }
}

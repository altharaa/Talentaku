<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoList extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function pivotLists()
    {
        return $this->hasMany(PivotList::class, 'information_id');
    }
}

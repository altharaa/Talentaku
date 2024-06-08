<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PivotList extends Model
{
    use HasFactory;

    protected $fillable = ['information_id', 'list_desc_id'];

    public function information()
    {
        return $this->belongsTo(Information::class, 'information_id');
    }

    public function listDesc()
    {
        return $this->belongsTo(ListDesc::class, 'list_desc_id');
    }
}

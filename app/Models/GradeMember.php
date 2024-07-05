<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeMember extends Model
{
    use HasFactory;

    public function grade() {
        return $this->belongsTo(Grade::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'original_file_name',
        'file_name',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'identification_number',
        'address',
        'place_of_birth',
        'birth_date',
        'joining_year',
        'password',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'joining_year' => 'date',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('student', function (Builder $query) {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', ['Murid SD', 'Murid KB']);
            });
        });
    }
}

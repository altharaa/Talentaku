<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'identification_number',
        'address',
        'birth_date',
        'photo',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles() {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimeStamps();
    }

    public function members(): HasMany
    {
        return $this->hasMany(GradeMember::class, 'student_id')->withTimeStamps();
    }

    public function grades()
    {
        return $this->belongsToMany(Grade::class, 'grade_members', 'student_id', 'grade_id');
    }

    public function getGrade()
    {
        return $this->grades()->get();
    }

    public function albums()
    {
        return $this->hasMany(Album::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
    // or
    protected $visible = ['id', 'name', 'roles', 'grades', 'identification_number', 'photo'];
    protected $hidden = [
        'password',
        'remember_token',
        'email',
        'address',
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

    public function members()
    {
        return $this->hasMany(GradeMember::class, 'student_id');
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

    public function isTeacher()
    {
        return $this->roles()->where('name', 'Guru SD')->exists() || $this->roles()->where('name', 'Guru KB')->exists();
    }

    public function isStudent()
    {
        return $this->roles()->where('name', 'Murid SD')->exists() || $this->roles()->where('name', 'Murid KB')->exists();
    }

    public function isMember($gradeId)
    {
        return $this->grades()->where('grade_id', $gradeId)->exists();
    }


}

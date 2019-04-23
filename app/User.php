<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    const ROLE_USER = 0;
    const ROLE_USER_MANAGER = 1;
    const ROLE_ADMIN = 2;

    const ROLES = [
        self::ROLE_USER,
        self::ROLE_USER_MANAGER,
        self::ROLE_ADMIN,
    ];

    protected $fillable = [ 'name', 'email', 'password', 'role', 'expected_calories' ];
    protected $hidden = [ 'password', ];

    protected $casts = [
        'role' => 'int',
        'expected_calories' => 'int',
    ];

    // RELATIONS
    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    // ATTRIBUTES
    public function getAccessTokenAttribute()
    {
        // Use an attribute accessor to ensure consistent token name
        return $this->createToken('TopTalCalories')->accessToken;
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->role == self::ROLE_ADMIN;
    }

    public function getIsUserManagerAttribute(): bool
    {
        return $this->role == self::ROLE_USER_MANAGER;
    }

    public function setPasswordAttribute($password)
    {
        // We have this here so admin & user managers can create new users via the REST api
        $this->attributes['password'] = bcrypt($password);
    }

    public static function boot() {
        parent::boot();

        static::deleting(function(User $user) {
            // When a user deletion is requested, we need to delete their meals first!
            $user->meals()->delete();
        });
    }
}

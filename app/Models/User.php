<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'email',
        'password',
        'mobile_number',
        'role_id',
        'IsActive',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'IsActive' => 'boolean',
        ];
    }

    /** Relationships **/

    public function role(): BelongsTo {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function client(): HasOne {
        return $this->hasOne(Client::class, 'user_id');
    }

    public function manager(): HasOne {
        return $this->hasOne(Manager::class, 'user_id');
    }
}
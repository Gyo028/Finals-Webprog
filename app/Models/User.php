<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; 
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'google_id',
        'role_id',
        'IsActive',
        'mobile_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
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
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function client(): HasOne {
        return $this->hasOne(Client::class, 'user_id', 'user_id');
    }

    public function manager(): HasOne {
        return $this->hasOne(Manager::class, 'user_id', 'user_id');
    }
}

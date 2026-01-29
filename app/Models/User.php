<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The primary key associated with the table.
     * Since your spreadsheet uses 'user_id' instead of 'id'.
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     * Updated to match your spreadsheet columns.
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'mobile_number',
        'role_id',
        'IsActive',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'IsActive' => 'boolean',
        ];
    }
}
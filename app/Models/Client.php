<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'client_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'bday',
        'IsActive',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'bday' => 'date',
        'IsActive' => 'boolean',
    ];

    /**
     * Get the user that owns the client profile.
     * Relationship: A Client profile belongs to one User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the bookings for the client.
     * Relationship: A Client can have many Bookings.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'client_id', 'client_id');
    }
}
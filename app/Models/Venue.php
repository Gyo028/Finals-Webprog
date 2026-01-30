<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venue extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'venue_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'venue_name',
        'venue_address',
        'isActive',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'isActive' => 'boolean',
    ];

    /**
     * Get the bookings for the venue.
     * Relationship: One Venue can host many Bookings.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'venue_id', 'venue_id');
    }
}
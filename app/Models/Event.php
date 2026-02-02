<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'event_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'event_name',
        'event_base_price',
        'IsActive',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'event_base_price' => 'decimal:2',
        'IsActive' => 'boolean',
    ];

    /**
     * Get the bookings for this event type.
     * Relationship: One Event type can have many Bookings.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'event_id', 'event_id');
    }
}
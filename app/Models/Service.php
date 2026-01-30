<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'service_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'service_name',
        'service_price',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'service_price' => 'decimal:2',
    ];

    /**
     * The bookings that belong to the service.
     * Relationship: Many-to-Many via the booking_services pivot table.
     */
    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(
            Booking::class, 
            'booking_services', 
            'service_id', 
            'booking_id'
        )->withTimestamps();
    }
}
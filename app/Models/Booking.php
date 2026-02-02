<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'booking_id';

    /**
     * Status Constants
     * Use these in your code: Booking::STATUS_PENDING
     */
    const STATUS_DRAFT    = 'draft';
    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_DENIED   = 'denied';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'client_id',
        'pax_id',
        'venue_id',
        'event_id',
        'total_price',
        'booking_date',
        'booking_start_time',
        'booking_end_time',
        'verified_by_manager_id',
        'verification_remarks',
        'is_payment_verified',
        'is_details_verified',
        'verified_at',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'booking_date'        => 'date',
        'is_payment_verified' => 'boolean',
        'is_details_verified' => 'boolean',
        'verified_at'         => 'datetime',
    ];

    /** * Relationships 
     */

    // The client who owns this booking
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    // The manager who verified this booking
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'verified_by_manager_id', 'manager_id');
    }

    // The venue selected for this booking
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'venue_id', 'venue_id');
    }

    // The type of event
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    // The pax/capacity details
    public function pax(): BelongsTo
    {
        return $this->belongsTo(Pax::class, 'pax_id', 'pax_id');
    }

    // Payments associated with this booking
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id', 'booking_id');
    }

    // Many-to-Many relationship for additional services
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'booking_services', 'booking_id', 'service_id');
    }
}
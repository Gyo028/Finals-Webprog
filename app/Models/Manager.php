<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Manager extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'manager_id';

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
     * Get the user that owns the manager profile.
     * Relationship: A Manager profile belongs to one User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the bookings verified by this manager.
     * Relationship: A Manager can verify many Bookings.
     */
    public function verifiedBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'verified_by_manager_id', 'manager_id');
    }
}
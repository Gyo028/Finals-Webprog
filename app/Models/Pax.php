<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pax extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * Laravel usually expects "paxes", but we specify it to be safe.
     */
    protected $table = 'paxes';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'pax_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pax_price',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'pax_price' => 'decimal:2',
    ];

    /**
     * Get the bookings associated with this pax tier.
     * Relationship: One Pax tier can be used by many Bookings.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'pax_id', 'pax_id');
    }
}
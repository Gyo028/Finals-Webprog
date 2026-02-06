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
        'pax_count',
        'pax_price',
        'IsActive', // ✅ added
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'pax_count' => 'integer',
        'pax_price' => 'decimal:2',
        'IsActive' => 'boolean', // ✅ added
    ];

    /**
     * Get the bookings associated with this pax tier.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'pax_id', 'pax_id');
    }
}

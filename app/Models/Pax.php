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
     * Added 'pax_count' to the list.
     */
    protected $fillable = [
        'pax_count', // New field added
        'pax_price',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'pax_count' => 'integer',   // Cast to integer for clean math
        'pax_price' => 'decimal:2',
    ];

    /**
     * Get the bookings associated with this pax tier.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'pax_id', 'pax_id');
    }
}
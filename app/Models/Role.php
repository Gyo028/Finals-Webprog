<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     * Overriding default 'id' to match your migration.
     */
    protected $primaryKey = 'role_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'role_name',
        'role_description',
        'IsActive',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'IsActive' => 'boolean',
    ];

    /**
     * Get the users associated with this role.
     * Relationship: A Role has many Users.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
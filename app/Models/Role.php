<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    // Allows you to use Role::create(['name' => 'Admin'])
    protected $fillable = ['name'];

    /**
     * Relationship: A role has many users.
     * This allows you to do: $role->users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
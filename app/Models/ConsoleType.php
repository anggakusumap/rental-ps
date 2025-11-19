<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsoleType extends Model
{
    protected $fillable = ['name', 'hourly_rate', 'description', 'is_active'];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function consoles(): HasMany
    {
        return $this->hasMany(Console::class);
    }
}

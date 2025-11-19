<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Console extends Model
{
    protected $fillable = ['console_type_id', 'console_number', 'status', 'notes'];

    public function consoleType(): BelongsTo
    {
        return $this->belongsTo(ConsoleType::class);
    }

    public function rentalSessions(): HasMany
    {
        return $this->hasMany(RentalSession::class);
    }

    public function currentSession(): ?RentalSession
    {
        return $this->rentalSessions()
            ->whereIn('status', ['active', 'paused'])
            ->latest()
            ->first();
    }
}

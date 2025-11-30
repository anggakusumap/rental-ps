<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RentalSession extends Model
{
    protected $fillable = [
        'console_id', 'user_id', 'package_id', 'customer_id', 'customer_name',
        'start_time', 'end_time', 'paused_at', 'total_paused_minutes',
        'status', 'total_cost', 'payment_status', 'payment_method', 'paid_at', 'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'paused_at' => 'datetime',
        'paid_at' => 'datetime',
        'total_paused_minutes' => 'integer',
        'total_cost' => 'decimal:2',
    ];

    public function console(): BelongsTo
    {
        return $this->belongsTo(Console::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}

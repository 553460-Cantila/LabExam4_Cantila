<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'menu_id',
        'user_id',
        'quantity',
        'unit_price',
        'total_price',
        'paid_amount',
        'order_status',
        'payment_status'
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, $this->total_price - $this->paid_amount);
    }

    public function updatePaymentStatus(): void
    {
        if ($this->paid_amount >= $this->total_price) {
            $this->payment_status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }
        $this->save();
    }
}
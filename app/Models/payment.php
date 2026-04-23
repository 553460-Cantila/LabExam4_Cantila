<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class payment extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'amount_paid', 'change_given', 'notes'];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
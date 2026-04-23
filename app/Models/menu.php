<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class menu extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'price_per_kilo', 'stock'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reduceStock(int $quantity): bool
    {
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    public function increaseStock(int $quantity): void
    {
        $this->stock += $quantity;
        $this->save();
    }
}
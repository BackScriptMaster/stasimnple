<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coin extends Model
{
    /** @use HasFactory<\Database\Factories\CoinFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'buy_price',
        'sell_price',
        'local_currency',
        'min_amount',
        'max_amount',
        'status',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function logs()
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'coin_id',
        'usdt_amount',
        'local_amount',
        'local_currency',
        'type',
        'status',
    ];

    // Relaciones
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function coin()
    {
        return $this->belongsTo(Coin::class);
    }

    public function logs()
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}

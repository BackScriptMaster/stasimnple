<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    /** @use HasFactory<\Database\Factories\BalanceFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'usdt_balance',
        'local_balance',
        'local_currency',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}

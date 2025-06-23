<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    /** @use HasFactory<\Database\Factories\LogFactory> */
    use HasFactory;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loggable_id',
        'loggable_type',
        'action',
        'description',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loggable()
    {
        return $this->morphTo();
    }
}
